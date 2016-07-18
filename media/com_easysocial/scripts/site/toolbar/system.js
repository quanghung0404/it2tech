EasySocial.module( 'site/toolbar/system' , function($){

	var module = this;

	EasySocial.require()
	.view('site/notifications/system.empty')
	.library('tinyscrollbar')
	.done(function($) {

		EasySocial.Controller('Notifications.System', {
			
			defaultOptions: {
				pageTitle: null,
				interval: 30,

				// Elements within this container.
				"{counter}": "[data-notificationSystem-counter]"
			}
		}, function(self, opts){ return{

			init: function() {
				// Initialize the default page title
				opts.pageTitle = $(document).attr('title');

				// Start the automatic checking of new notifications.
				self.startMonitoring();
			},

			startMonitoring: function() {
				var interval = self.options.interval * 1000;

				self.options.state = setTimeout(self.check, interval);
			},

			stopMonitoring: function() {
				clearTimeout(self.options.state);
			},

			check: function(){

				// Stop monitoring so that there wont be double calls at once.
				self.stopMonitoring();

				var interval = self.options.interval * 1000;

				// Needs to run in a loop since we need to keep checking for new notification items.
				setTimeout( function(){

					EasySocial.ajax('site/controllers/notifications/getSystemCounter')
					.done(function( total ){

						if (total > 0) {
							// When there is new notification items, we want to update the page title.
							$(document).attr('title', self.options.pageTitle + ' (' + total + ')');

							// Update toolbar item element
							self.element.addClass( 'has-notice' );

							// Update the counter's count.
							self.counter().html( total );
						} else {

							self.element.removeClass( 'has-notice' );

							// When the new notification button is clicked, we want to reset to the original title.
							$(document).attr('title', self.options.pageTitle);
						}

						// Continue monitoring.
						self.startMonitoring();
					});

				}, interval );

			},

			'{window} easysocial.clearSystemNotification': function() {
				self.element.removeClass('has-notice');
				self.counter().html(0);
			}

		}});

		EasySocial.Controller('Notifications.System.Popbox', {
			defaultOptions: {
				"{readall}"	: "[data-notificationsystem-readall]",
				"{items}"	: "[data-notificationsystem-items]",

				view: {
					empty	: "site/notifications/system.empty"
				}
			}
		}, function(self) {
			return {
				init: function() {

				},

				"{readall} click": function() {

					// Bad way of implementing this
					$('[data-notificationSystem-counter]').parents('li').removeClass('has-notice');
					$('[data-notificationSystem-counter]').html(0);

					EasySocial.ajax( 'site/controllers/notifications/setAllState' ,
					{
						"state"	: "read"
					})
					.done(function()
					{
						self.items().html('');

						self.items().append(self.view.empty());

						self.items().addClass('is-empty');

						$(window).trigger('easysocial.clearSystemNotification');
					});
				}
			}
		})

		module.resolve();
	});

});
