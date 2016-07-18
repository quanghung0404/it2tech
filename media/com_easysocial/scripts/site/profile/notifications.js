EasySocial.module('site/profile/notifications', function($) {

	var module = this;

	EasySocial.require()
		.script('site/profile/header')
		.done(function() {
			EasySocial.Controller('Profile.Notifications', {
				defaultOptions: {
					// App item
					"{sidebarItem}"	: "[data-notification-item]",
					"{contentItem}"	: "[data-notification-content]",

					//input form
					"{notificationForm}" : "[data-notifications-form]"
				},
			}, function(self) {
				return {

					init : function() {
					},

					"{sidebarItem} click": function(el, event) {
						self.sidebarItem().removeClass('active');

						el.addClass('active');

						self.contentItem().hide();

						var element = el.data('alert-element');

						self.contentItem('[data-alert-element="' + element + '"]').show();
					}
				}
			});

			module.resolve();
		});
});
