EasySocial.module('site/toolbar/notifications' , function($){

	var module 				= this;

	EasySocial.require()
	.script( 
		'site/toolbar/friends', 
		'site/toolbar/story',
		'site/toolbar/system',
		'site/toolbar/profile',
		'site/toolbar/login',
		'site/toolbar/conversations'
	)
	.done(function($){

		EasySocial.Controller('Notifications', {
			defaultOptions: {
				friendsInterval: 30,
				systemInterval: 30,
				conversationsInterval: 30,

				"{friendNotifications}": "[data-notifications-friends]",
				"{conversationNotifications}": "[data-notifications-conversations]",
				"{systemNotifications}": "[data-notifications-system]",
				"{profileItem}": "[data-toolbar-profile]",
				"{storyForm}": "[data-toolbar-story]",
				"{login}": "[data-toolbar-login]",
				"{item}": "[data-toolbar-item]"
			}
		}, function(self, opts){ return { 

			init: function() {

				// Implement toolbar login controller
				self.login().addController(EasySocial.Controller.Toolbar.Login);

				// Initialize profile controller for toolbar.
				if (!opts.guest) {

					self.profileItem().addController(EasySocial.Controller.Toolbar.Profile, {
						interval: self.options.systemInterval
					});

					// Initialize system notifications controller.
					self.systemNotifications().addController(EasySocial.Controller.Notifications.System, {
						interval: opts.systemInterval
					});

					// Initialize friends controller.
					self.friendNotifications().addController( EasySocial.Controller.Notifications.Friends, {
						interval: self.options.friendsInterval
					});

					// Initialize conversations controller.
					self.conversationNotifications().addController(EasySocial.Controller.Notifications.Conversations, {
						interval: self.options.conversationsInterval
					});
					
					// Initialize story form controller.
					self.storyForm().addController(EasySocial.Controller.Notifications.Story);
				}

				// Initialize responsive layout for the notification bar.
				self.setLayout();

				// Monitor clicks on the body. So that all dropdowns should be hidden whenever clicks are made on the body.
				$('body').on('click.out-of-dropdown', function() {
					self.item().removeClass('open');
				});
			},

			"{window} resize": $.debounce(function(){
				self.setLayout();
			}, 250),

			setLayout: function() {

				var elem = self.element,
					toolbarWidth = elem.outerWidth(true) - 80,
					allItemWidth = 0;

					// Calculate how much width toolbar items are taking
					self.item().each(function(){
						allItemWidth += $(this).outerWidth(true);
					});

				var exceeded = (allItemWidth > toolbarWidth);

				elem.toggleClass("narrow", exceeded).toggleClass("wide", !exceeded);
			}
		}});

		module.resolve();
	});

});
