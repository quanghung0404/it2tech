
EasySocial.module( 'site/profile/friends' , function($){

	var module 				= this;

	EasySocial.require()
	.view( 'site/loading/small')
	.language(
		'COM_EASYSOCIAL_FRIENDS_DIALOG_CANCEL_REQUEST',
		'COM_EASYSOCIAL_CANCEL_BUTTON',
		'COM_EASYSOCIAL_YES_CANCEL_MY_REQUEST_BUTTON'
	)
	.done(function($){

		EasySocial.Controller(
			'Profile.Friends.Request',
			{
				defaultOptions:
				{
					id 		: null,
					callback		: null,

					// Elements
					"{addButton}"		: "[data-profileFriends-add]",
					"{manageButton}"	: "[data-profileFriends-manage]",
					"{pendingButton}"	: "[data-profileFriends-pending]",
					"{respondButton}"	: "[data-profileFriends-respond]",
					"{cancelRequest}"	: "[data-profileFriends-cancelRequest]",

					"{unfriend}"		: "[data-profile-friends-unfriend]",
					"{approve}"			: "[data-friends-response-approve]",
					"{reject}"			: "[data-friends-response-reject]",

					// The current add friend / cancel friend btuton.
					"{button}"			: "[data-profileFriends-button]",

					// Dropdown
					"{dropdown}"		: "[data-profileFriends-dropdown]",

					view :
					{
						loader 			: "site/loading/small",
					}
				}
			},
			function(self)
			{
				return{

					init: function()
					{
						// Set the friend id.
						self.options.id 		= self.element.data( 'friend' );

						// Set the target id
						self.options.target 	= self.element.data( 'id' );

						// Set the callback url
						self.options.callback 	= self.element.data( 'callback' );
					},

					showDropDown : function()
					{
						self.element.addClass( 'open' );
					},

					hideDropDown : function()
					{
						self.element.removeClass( 'open' );
					},

					"{addButton} click" : function( el ) {

						var button = self.button();

						button.addClass("loading");

						EasySocial.ajax("site/controllers/friends/request", {
								id: self.options.target
							})
							.done(function(friendId, button) {

								// Remove any previous dropdown
								self.dropdown().remove();

								// After the request is complete, set the correct friend id.
								self.options.id = friendId;

								// Replace the button
								self.button().replaceWith(button);
							}).fail(function(obj){

								EasySocial.dialog({
									content: obj.message
								});

								button.removeClass("loading");
							});
					},

					"{cancelRequest} click" : function(el , event) {
						// If user can click on the cancel request, they should have a valid friend id by now.
						var friendId 	= self.options.id;

						// Hide any dropdown that's open
						self.hideDropDown();

						// Show confirmation dialog
						EasySocial.dialog({
							content: EasySocial.ajax( 'site/views/friends/confirmCancel' ),
							bindings:
							{
								"{confirmButton} click": function()
								{
									EasySocial.ajax( 'site/controllers/friends/cancelRequest' ,
									{
										"id"	: self.options.id
									})
									.done( function( button )
									{
										// Remove any previous dropdowns.
										self.dropdown().remove();

										// Update the button
										self.button().replaceWith( button );

										// Hide the dialog once the request has been cancelled.
										EasySocial.dialog().close();
									});
								}
							}
						});

					},

					"{unfriend} click" : function( el , event )
					{
						var userId 	= $( el ).data( 'id' );
						// Implement controller on add friend.
						EasySocial.dialog(
						{
							content		: EasySocial.ajax( 'site/views/friends/confirmUnfriend' , { "id"	: userId } ),
							bindings 	:
							{
								"{unfriendButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/friends/unfriend' ,
									{
										"id"	: self.options.id
									})
									.done(function( button )
									{
										// Remove any previous dropdowns.
										self.dropdown().remove();

										// Update the button
										self.button().replaceWith( button );

										// Close the dialog
										EasySocial.dialog().close();
									});
								}
							}
						});
					},

					"{approve} click" : function( el , event )
					{
						var friendId 	= self.options.id;

						// Hide dropdown
						self.hideDropDown();

						EasySocial.ajax( 'site/controllers/friends/approve' ,
						{
							"id"	: friendId
						})
						.done( function( button )
						{
							// Replace the button.
							self.button().replaceWith( button );
						});
					},

					"{reject} click" : function( el , event )
					{
						var friendId 	= self.options.id;

						// Hide dropdown
						self.hideDropDown();

						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/friends/confirmReject' ),
							bindings :
							{
								"{rejectButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/friends/reject' ,
									{
										"id"	: friendId
									})
									.done( function( button )
									{
										// Update the button.
										self.button().replaceWith( button );

										EasySocial.dialog(
										{
											content 	: EasySocial.ajax( 'site/views/friends/friendRejected' ),
										});

									});


								}
							}
						});
					},

					"{dropdown} click" : function( el , event )
					{
						// Disallow clicking of events to trigger parent items.
						event.stopPropagation();
					},

					"{approveRequest} click" : function()
					{
						// Update the task
						self.respondForm().find( 'input[name=task]' ).val( 'approve' );

						// Update the friend id
						self.respondForm().find( 'input[name=id]' ).val( self.options.friendId );

						// Update the return url.
						self.respondForm().find( 'input[name=return]' ).val( self.options.callback );

						// Submit the form.
						self.respondForm().submit();
					}
				}
		});


		module.resolve();
	});

})
