EasySocial.module('site/dashboard/feeds', function($){

	var module 				= this;

	EasySocial.require()
	.library( 'history' )
	.done(function($){

		EasySocial.Controller(
			'Dashboard.Feeds',
			{
				defaultOptions:
				{
					"{item}"	: "[data-dashboardFeeds-item]",
					"{filter}"	: "[data-dashboardFeeds-Filter-edit]"
				}
			},
			function(self){

				return{

					init : function()
					{
						// Implement each feed links.
						self.item().implement(EasySocial.Controller.Dashboard.Feeds.Item, {
							"{parent}"		: self,
							"{dashboard}"	: self.parent
						});
					},

					addFilterItem: function(feed)
					{
						feed.find('[data-dashboardFeeds-item]').implement(EasySocial.Controller.Dashboard.Feeds.Item, {
							"{parent}"		: self,
							"{dashboard}"	: self.parent
						});

						// feed.appendTo(self.element);
						if ($(self.element).find('.widget-filter-group').length > 0) {
							feed.insertBefore($(self.element).find('.widget-filter-group'));
						} else {
							feed.appendTo(self.element);
						}
					}
				}
			});

		EasySocial.Controller('Dashboard.Feeds.Item', {
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
						id = self.element.data( 'id' ),
						url = self.element.data( 'url' ),
						title = self.element.data( 'title' ),
						desc = self.element.data( 'description' );

					if (self.clicked) {
						return;
					}

					self.clicked	= true;

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

					// Notify the dashboard that it's starting to fetch the contents.
					self.dashboard.content().html("");
					self.dashboard.updatingContents();

					self.element.addClass('loading');

					EasySocial.ajax( 'site/controllers/dashboard/getStream', {
						"type"	: type,
						"id"	: id,
						"view"  : 'dashboard',
					})
					.done(function(contents, count) {

						self.dashboard.updateHeading(title, desc);

						if (count == 0) {
							$('.es-streams').addClass( 'no-stream' );
						}

						// Trigger change for the stream
						self.trigger('onStreamUpdate', [type]);

						window.streamFilter = type;

						self.dashboard.updateContents(contents);

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
