EasySocial.module('site/profile/feeds', function($){

	var module 				= this;

	EasySocial.require()
	.library('history')
	.done(function($){

		EasySocial.Controller(
			'Profile.Feeds',
			{
				defaultOptions:
				{
					"{menuItem}"	: "[data-profileSidebar-menu]",
					"{item}"	: "[data-profileFeeds-item]",
					"{filter}"	: "[data-profileFeeds-Filter-edit]"
				}
			},
			function(self){

				return{

					init : function()
					{
						// Implement each feed links.
						self.item().implement(EasySocial.Controller.Profile.Feeds.Item, {
							"{parent}"	: self,
							"{profile}"	: self.parent
						});
					},

					"{menuItem} click" : function( el , event )
					{
						// Remove all active class.
						self.menuItem().removeClass( 'active' );

						// Add active class on this item.
						$( el ).addClass( 'active' );
					},

					addFilterItem: function(feed)
					{
						feed.find('[data-profileFeeds-item]').implement(EasySocial.Controller.Profile.Feeds.Item, {
							"{parent}"	: self,
							"{profile}"	: self.parent
						});

						feed.appendTo(self.element);
					}
				}
			});

		EasySocial.Controller('Profile.Feeds.Item', {
			defaultOptions:
			{
			}
		}, function(self) {
			return{

				clicked: false,

				init : function()
				{
				},

				"{self} click" : function()
				{
					//remove no-stream class if any
					$('.es-streams').removeClass( 'no-stream' );

					var type = self.element.data( 'type' ),
						filterId = self.element.data( 'filterid' ),
						userId = self.element.data( 'user' ),
						url = self.element.data( 'url' ),
						title = self.element.data( 'title' ),
						desc = self.element.data( 'description' );

					if (self.clicked) {
						return;
					}

					self.clicked = true;

					// clear the new feed notification counter.
					var key = '[data-stream-counter-';

					if (type == 'list') {
						key = key + type + '-' + id;
					} else {
						key = key + type;
					}

					key = key + ']';

					$(key).html( '0' );

					// clear new feed counter
					self.element.removeClass('has-notice');


			        var appendTitle = $.joomla.appendTitle;

			        if (appendTitle==="before") {
			            title = $.joomla.sitename + ((title) ? " - " + title : "");
			        }

			        if (appendTitle==="after") {
			            title = ((title) ? title + " - " : "") + $.joomla.sitename;
			        }

					// If this is an embedded layout, we need to play around with the push state.
					History.pushState( {state:1} , title , url );

					// Notify the profile that it's starting to fetch the contents.
					self.profile.contents().html("");
					self.profile.updatingContents();

					self.element.addClass('loading');

					EasySocial.ajax( 'site/controllers/profile/getStream', {
						"type"	: type,
						"filterId"	: filterId,
						"id"	: userId,
						"view"  : 'profile',
					})
					.done(function(contents, count) {

						if (count == 0) {
							$('.es-streams').addClass( 'no-stream' );
						}

						// Trigger change for the stream
						self.trigger('onStreamUpdate', [type]);

						window.streamFilter = type;

						self.profile.updateContent(contents);

						// add support to kunena [tex] replacement.
						try { MathJax && MathJax.Hub.Queue(["Typeset",MathJax.Hub]); } catch( err ) {};

					}).fail(function(messageObj) {
						return messageObj;
					}).always(function() {
						self.clicked	= false;
						self.element.removeClass('loading');
					});


				}
			}
		});
		module.resolve();
	});

});
