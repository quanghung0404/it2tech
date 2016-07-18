EasySocial.module('site/likes/likes', function($){

	var module = this;

	$(document)
		.on("click.es.likes.action", "[data-likes-action]", function(){

			var button = $(this),
				data = {
					id   : button.data("id"),
					type : button.data("type"),
					group: button.data("group"),
					verb: button.data("verb"),
					streamid : button.data("streamid")
				},
				key = data.type + "-" + data.group + "-" + data.id;

			EasySocial.ajax("site/controllers/likes/toggle", data)
				.done(function(content, label, showOrHide, actionVerb, count) {

					// Update like label
					button.text(label);

					//streamid
					id = button.data("streamid");

					// Update like content
					$("[data-likes-" + key + "]")
						.html(content)
						.toggleClass("hide", showOrHide)
						.toggle(!showOrHide);

					// Furnish data with like count
					data.uid   = data.id; // inconsistency
					data.count = count;

					// verb = like/unlike
					button.trigger((actionVerb=="like") ? "onLiked" : "onUnliked", [data]);

					if( actionVerb == 'like' && id != "" )
					{
						//update excludeids

						ids = $('[data-streams-wrapper]').data( 'excludeids' );

						newIds = '';
						if( ids != '' && ids != undefined )
						{
							newIds = ids + ',' + id;
						}
						else
						{
							newIds = id;
						}

						$('[data-streams-wrapper]').data( 'excludeids', newIds );
					}
				})
				.fail(function(message) {

					console.log(message);
				});
		})
		.on("click.es.likes.others", "[data-likes-others]", function(){

			var button = $(this),
				content = button.parents("[data-likes-content]"),
				data = {
					uid    : content.data("id"),
					type   : content.data("type"),
					verb   : content.data('verb'),
					group  : content.data('group'),
					exclude: button.data("authors")
				};

			EasySocial.dialog({
				content: EasySocial.ajax("site/controllers/likes/showOthers", data)
			});
		});

	module.resolve();
});
