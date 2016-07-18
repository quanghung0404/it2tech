EasySocial.module("site/profile/popbox", function($) {

	var module = this;

	EasySocial.require()
		.library("popbox")
		.done(function(){

			// We should check if popbox should be initialized or not.
			var initPopbox = (EasySocial.options.lockdown && !EasySocial.options.guest) || !EasySocial.options.lockdown

			if (initPopbox) {
				EasySocial.module("profile/popbox", function($) {

					this.resolve(function(popbox){

						var id = popbox.button.data("userId");
						var position = popbox.button.attr('data-popbox-position') || 'top-left';

						return {
							content: EasySocial.ajax("site/views/profile/popbox", {id: id}),
							id: "fd",
							component: "es",
							type: "profile",
							position: position
						}
					})
				});
			}

		});

	// Non-essential dependency
	EasySocial.require()
		.script("site/conversations/composer")
		.done();

	EasySocial.Controller("Profile.Popbox", {
		defaultOptions: {
			// The current user being viewed
			id: null,

			"{addButton}"	    : "[data-popbox-friends-add]",
			"{friendsButton}"	: "[data-popbox-friends-friends]",
			"{respondButton}"	: "[data-popbox-friends-respond]",
			"{requestedButton}"	: "[data-popbox-friends-requested]",
			"{messageButton}"	: "[data-popbox-message]",
			"{friendsSubmenu}"	: "[data-friends-submenu]",

			"{cancelFriend}"	: "[data-popbox-friends-friends-cancel]",
			"{cancelFriendRequest}" : "[data-popbox-friends-requested-cancel]",
			"{approveFriend}"	: "[data-popbox-friends-respond-approve]",
			"{rejectFriend}"	: "[data-popbox-friends-respond-reject]"
		}
	},
	function(self) { return {

		init: function() {

			self.options.id = self.element.find("[data-user-id]").data("userId");

			EasySocial.module("site/conversations/composer")
				.done(function(){
					self.messageButton()
						.implement( EasySocial.Controller.Conversations.Composer.Dialog, { "recipient" : { "id" : self.options.id } } );
				});
		},

		"{self} popboxActivate": function() {

			self.friendsSubmenu().parent().removeClass("open");
		},

		"{addButton} click": function() {

			EasySocial.ajax("site/controllers/friends/request",
			{
				"id"	: self.options.id
			})
			.done(function()
			{
				// Add a loader
				// self.addButton().html( self.view.loader() );

				// Replace the button
				EasySocial.ajax( 'site/views/profile/getButton' , { "button" : "button.requested" , "id" : self.options.id } )
				.done(function( button )
				{
					// We know that the existing button is a request button
					self.addButton().replaceWith( button );
				});
				
			}).fail(function()
			{
				EasySocial.dialog(
				{
					content 	: EasySocial.ajax( 'site/views/friends/exceeded' )
				});
			});
		},

		"{cancelFriend} click": function(el, event) {

			var friendId = $( el ).data( 'friendid' );

			EasySocial.dialog(
			{
				content		: EasySocial.ajax( 'site/views/profile/confirmRemoveFriend' , { "id" : self.options.id } ),
				bindings	:
				{
					"{confirmButton} click" : function()
					{
						EasySocial.ajax( 'site/controllers/friends/unfriend' , { "id" : friendId } )
						.done(function()
						{
							// Display tot he user that they are no longer friends now.
							EasySocial.dialog(
							{
								content 	: EasySocial.ajax( 'site/views/profile/friendRemoved' , { "id" : self.options.id } )
							});

							// Replace the button
							EasySocial.ajax( 'site/views/profile/getButton' , { "button" : "button.add" } )
							.done(function( button )
							{
								self.friendsSubmenu().remove();

								self.friendsButton().replaceWith( button );
							});
						});
					}
				}
			});

		},

		"{cancelFriendRequest} click": function(el, event) {

			var friendId = $( el ).data( 'friendid' );

			EasySocial.dialog(
			{
				content: EasySocial.ajax( 'site/views/profile/confirmCancelRequest' ,
							{
								"id"	: friendId
							}),
				bindings:
				{
					"{confirmButton} click" : function()
					{
						// Close the dialog
						EasySocial.dialog().close();

						EasySocial.ajax( 'site/controllers/friends/cancelRequest' , { "id" : friendId } )
						.done(function()
						{
							// Replace the button
							EasySocial.ajax( 'site/views/profile/getButton' , { "button" : "button.add" } )
							.done(function( button )
							{
								// Hide the submenu
								self.friendsSubmenu().remove();

								// We know that the existing button is a request button
								self.requestedButton().replaceWith( button );
							});
						});
					}
				}
			});

		},

		"{approveFriend} click": function( el , event ) {

			var friendId = $( el ).data( 'friendid' );

			EasySocial.ajax( 'site/controllers/friends/approve' , { "id" : friendId } )
			.done(function()
			{
				EasySocial.dialog(
				{
					content: EasySocial.ajax( 'site/views/profile/confirmFriends' , { "id" : self.options.id } )
				});

				// Replace the button
				EasySocial.ajax( 'site/views/profile/getButton' , { "button" : "button.friends" } )
				.done(function( button )
				{
					// Hide the submenu
					self.friendsSubmenu().remove();

					// We know that the existing button is a request button
					self.respondButton().replaceWith( button );
				});
			});
		},

		"{rejectFriend} click" : function(el, event) {

			var friendId = $( el ).data( 'friendid' );

			EasySocial.ajax("site/controllers/friends/reject",
			{
				id: friendId
			})
			.done(function(){

				EasySocial.dialog({
					content: EasySocial.ajax("site/views/profile/rejected", { "id" : self.options.id } )
				});

				// Replace the button
				EasySocial.ajax("site/views/profile/getButton",
				{
					"button" : "button.add"
				})
				.done(function(button){

					// Hide the submenu
					self.friendsSubmenu().remove();

					// We know that the existing button is a request button
					self.respondButton().replaceWith( button );
				});
			});
		}

	}});

	// Popovers can implement themselves
	$(document).on("mouseover.es.profile.popbox", "[data-popbox-tooltip=profile]", function(){
		$(this).addController("EasySocial.Controller.Profile.Popbox");
	});

	module.resolve();

});
