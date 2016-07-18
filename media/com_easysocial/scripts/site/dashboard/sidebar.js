EasySocial.module('site/dashboard/sidebar', function($){

	var module = this;

	EasySocial
	.require()
	.library('history')
	.done(function($) {

		EasySocial.Controller('Dashboard.Sidebar', {
			defaultOptions: {
				"{menuItem}"	: "[data-dashboardSidebar-menu]",
				"{filterBtn}"	: "[data-stream-filter-button]",
				"{editIcon}" 	: "[data-dashboardFeeds-Filter-edit]"
			}
		}, function(self) { return {

			init: function() {
			},
			
			"{menuItem} click": function(menuItem, event) {
				
				// Remove all active class.
				self.menuItem().removeClass('active');

				// Add active class on this item.
				menuItem.addClass('active');
			},

			"{editIcon} click" : function(editIcon, event) {
				event.preventDefault();

				// Update the browser's url
				editIcon.route();
					
				var id = editIcon.data('id');

				// Notify the dashboard that it's starting to fetch the contents.
				self.parent.content().html("");
				self.parent.updatingContents();

				EasySocial.ajax('site/controllers/stream/getFilter', {
					"id": id
				}).done(function(contents) {
					self.parent.updateContents(contents);
				}).fail(function(messageObj) {
					return messageObj;
				});
			},

			"{filterBtn} click" : function(filterButton, event) {
				event.preventDefault();

				// Update the url
				filterButton.route();

				// Notify the dashboard that it's starting to fetch the contents.
				self.parent.content().html("");
				self.parent.updatingContents();

				EasySocial.ajax( 'site/controllers/stream/getFilter', {
					"id": 0
				})
				.done(function(contents) {
					self.parent.updateContents(contents);
				})
				.fail(function(messageObj) {
					return messageObj;
				});
			}
		}});

		module.resolve();
	});

});
