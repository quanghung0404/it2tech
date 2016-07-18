EasySocial.module( 'site/profile/header' , function($){

	var module 				= this;

	EasySocial.require()
	.script(
		'site/profile/friends' ,
		'site/profile/subscriptions' ,
		'site/conversations/composer'
	).done(function($) {

		EasySocial.Controller(
			'Profile.Header', {
				defaultOptions: {
					// Properties
					id: null,

					// Elements
					"{friendRequest}"	: "[data-profile-friends]",
					"{subscribe}"		: "[data-profile-followers]",
					"{conversation}"	: "[data-profile-conversation]"
				}
			}, function(self) {
				return {

					init: function() {

						// Get the id of the current user.
						self.options.id = self.element.data('id');
						self.options.name = self.element.data('name');
						self.options.avatar = self.element.data('avatar');

						// Implement friends controller on the friend request button.
						self.friendRequest().implement(EasySocial.Controller.Profile.Friends.Request, {
							"{parent}": self
						});

						// Implement subscription controller on the subscribe button.
						self.subscribe().implement(EasySocial.Controller.Profile.Subscriptions, {
							"{parent}"	: self
						});

						self.conversation().implement(EasySocial.Controller.Conversations.Composer.Dialog, {
							"recipient"	: {
								"id": self.options.id,
								"name"	: self.options.name,
								"avatar": self.options.avatar
							}
						});
					}
				}
			}
		);


		module.resolve();
	});


});
