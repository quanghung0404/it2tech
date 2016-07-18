EasyBlog.module("teamblogs", function($) {

var module = this;

// require: start
EasyBlog.require()
	.done(function($){

	// controller: start

	EasyBlog.Controller('TeamBlogs.Item', {
		defaultOptions: {
			"{feature}" : "[data-team-feature]",
			"{unfeature}" : "[data-team-unfeature]",
			"{featuredTag}": "[data-featured-tag]",
			"{viewMemberBtn}": "[data-view-member]"
		}
	}, function(self) {
		return {
			init: function()
			{
				self.options.id = self.element.data('id');
			},
			featureItem: function() {

				EasyBlog.ajax('site/views/featured/makeFeatured', {
					"type" : "teamblog",
					"id": self.options.id
				}).done(function(){
					// Switch the button
					self.feature().addClass('hide');
					self.unfeature().removeClass('hide');

					// Display the star icon
					self.featuredTag().removeClass('hide');
				});
			},
			"{feature} click" : function(el, event) {
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/confirm', {
						"type": "teamblog",
						"id": self.options.id
					}),
					bindings: {
						"{submitButton} click" : function()
						{
							self.featureItem();

							// Hide dialog now
							EasyBlog.dialog().close();
						}
					}
				});
			},
			"{unfeature} click" : function(el, event) {
				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/featured/removeFeatured', {
						"type": "teamblog",
						"id": self.options.id
					}),
					bindings: {
						"{closeButton} click" : function()
						{
							self.unfeature().addClass('hide');
							self.feature().removeClass('hide');

							self.featuredTag().addClass('hide');

							EasyBlog.dialog().close();
						}
					}
				});
			},

			"{viewMemberBtn} click": function(el, event) {


				console.log(self.options.id);

				EasyBlog.dialog(
				{
					content: EasyBlog.ajax('site/views/teamblog/viewMembers', {
						"id": self.options.id
					}),
					bindings: {
						"{closeButton} click" : function()
						{
							EasyBlog.dialog().close();
						}
					}
				});
			}
		}
	});

	module.resolve();

	// controller: end

	});

	// require: end
});
