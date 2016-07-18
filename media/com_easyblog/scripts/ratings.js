EasyBlog.module('ratings', function($){

	var module = this;

	EasyBlog
		.require()
		.library('ui/stars')
		.done(function(){

			EasyBlog.Controller('Ratings', {
				defaultOptions: {
					"{stars}": ".ui-stars-star",
					"{ratingValue}": "[data-rating-value]",
					"{ratingText}": "[data-rating-text]",
					"{showRating}": "[data-rating-voters]",
					"{totalRating}": "[data-rating-total]",
					"{starContainer}": ".star-location"
				}
			}, function(self) {
				return {

					init: function() {
						self.type = self.element.data('type');
						self.uid = self.element.data('id');
						self.locked = self.element.data('locked');

						var options = {
							'split': 2,
							'disabled': self.locked,
							'oneVoteOnly': true,
							'cancelShow': false,
							callback: self.onUserVote
						};

						// Implement star ratings
						self.starContainer().stars(options);
					},

					onUserVote: function(el) {
						var value = el.value();

						EasyBlog.ajax('site/views/ratings/vote', {
							"value": value,
							"type": self.type,
							"id": self.uid
						})
						.done(function(total, message, rating) {

							// Disable the selected stars
							self.stars().removeClass('ui-stars-star-on');

							// Hide the rate this text
							self.ratingText().html(message);

							// Add voted class
							self.element.addClass('voted');

							self.totalRating().text(total);

							// Enable specific stars
							self.stars().each(function(index) {
								if (index < rating) {
									$(this).addClass('ui-stars-star-on');
								} else {
									$(this).removeClass('ui-stars-star-on');
								}
							});
						});
					},

					"{showRating} click": function() {
						var total = parseInt(self.totalRating().text(), 10);
						if (total <= 0) {
							return;
						}

						EasyBlog.dialog({
							content: EasyBlog.ajax('site/views/ratings/voters', {"uid" : self.uid, "type" : self.type})
						});
					}
				}
			});

			module.resolve();
		});

});
