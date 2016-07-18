EasySocial.module( 'site/dashboard/dashboard' , function($){

	var module = this;

	EasySocial.require()
	.script('site/dashboard/apps', 'site/dashboard/feeds', 'site/dashboard/sidebar', 'site/stream/filter', 'site/dashboard/groups', 'site/dashboard/events')
	.done(function($){

		EasySocial.Controller('Dashboard', {
			defaultOptions:
			{
				currentTitle 	: null,
				pageTitle 		: null,

				"{heading}"			: "[data-dashboard-heading]",
				"{sidebar}"			: "[data-dashboard-sidebar]",
				"{content}"			: "[data-dashboard-real-content]",

				// Feeds.
				"{feeds}"			: "[data-dashboard-feeds]",
				"{groups}"			: "[data-dashboard-groups]",
				"{events}": "[data-dashboard-events]",
				"{groupItems}"		: "[data-dashboard-group-item]",
				"{eventItems}"		: "[data-dashboard-event-item]",
				"{showAllFilters}"	: "[data-app-filters-showall]",
				"{showAllGroups}"	: "[data-groups-filters-showall]",
				"{showAllEvents}"	: "[data-events-filters-showall]",
				"{appFilters}"		: "[data-sidebar-app-filter]",

				// Applications.
				"{apps}"			: "[data-dashboard-apps]",

				// hashtag filter save
				"{saveHashTag}"		: "[data-hashtag-filter-save]"
			}
		}, function(self) {
			return{

				init: function()
				{
					// Implement sidebar controller.
					self.sidebar().implement(EasySocial.Controller.Dashboard.Sidebar, {
						"{parent}"	: self
					});

					// Implement app controller on all app items.
					self.feedsController = self.feeds().addController(EasySocial.Controller.Dashboard.Feeds, {
						"{parent}"	: self
					});

					// Implement app controller on all app items.
					self.apps().implement(EasySocial.Controller.Dashboard.Apps, {
						"{parent}"	: self,
						pageTitle	: self.options.pageTitle
					});

					// Implement groups navigation on dashboard
					self.groups().implement( EasySocial.Controller.Dashboard.Groups, {
						"{parent}"	: self
					});

					// Implement groups navigation on dashboard
					self.events().implement(EasySocial.Controller.Dashboard.Events, {
						"{parent}"	: self
					});
				},

				"{showAllGroups} click": function(el, event) {
					$(el).hide();

					self.groupItems().removeClass('hide');
				},

				"{showAllEvents} click": function(el, event) {
					$(el).hide();

					self.eventItems().removeClass('hide');
				},

				"{showAllFilters} click" : function( el , event )
				{
					$(el).hide();

					self.appFilters().removeClass( 'hide' );
				},

				/**
				 * Responsible to update the heading area in the dashboard.
				 */
				updateHeading: function( title , description )
				{
					self.heading().find( '[data-heading-title]' ).html( title );
					self.heading().find( '[data-heading-desc]' ).html( description );
				},

				/**
				 * Add a loading icon on the content layer.
				 */
				updatingContents: function()
				{
					self.element.addClass("loading");
				},

				/**
				 * Responsible to update the content area in the dashboard.
				 */
				updateContents : function( contents )
				{
					self.element.removeClass("loading");

					// Hide the content first.
					$.buildHTML( contents ).appendTo( self.content() );
				},



				"{saveHashTag} click": function( el )
				{
					var hashtag = el.data('tag');

					EasySocial.dialog({
						content		: EasySocial.ajax( 'site/views/stream/confirmSaveFilter', { "tag": hashtag } ),
						bindings	:
						{
							"{saveButton} click" : function()
							{
								this.inputWarning().hide();

								filterName = this.inputTitle().val();

								if( filterName == '' )
								{
									this.inputWarning().show();
									return;
								}

								EasySocial.ajax( 'site/controllers/stream/addFilter',
								{
									"title"		: filterName,
									"tag"		: hashtag,
								})
								.done(function( html, msg )
								{
									// self.feeds().append( html );

									var item = $.buildHTML( html );
									self.feedsController.addFilterItem( item );

									// show message
									EasySocial.dialog( msg );

									// auto close the dialog
									setTimeout(function() {
										EasySocial.dialog().close();
									}, 2000);

								});
							}
						}
					});
				}


			}
		});

		module.resolve();
	});

});
