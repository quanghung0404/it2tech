EasySocial.module( 'site/search/item' , function($){

	var module	= this;

	EasySocial.require()
	.view(
		'site/loading/small'
	)
	.language(
		'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT_NOTICE'
	)
	.done(function($){

		EasySocial.Controller(
			'Search.Item',
			{
				defaultOptions:
				{
					// Elements
					"{toggle}"		: "[data-activity-toggle]",
					"{deleteBtn}"	: "[data-activity-delete]",

					"{addFriendButton}" : "[data-search-friend-button]",
					"{pendingFriendButton}" : "[data-search-friend-pending-button]",
					// Dropdown
					"{dropdown}"		: "[data-profileFriends-dropdown]",

					view :
					{
						loader 			: "site/loading/small"
					}
				}
			},
			function( self ){
				return {

					init : function()
					{
						// Implement sidebar controller.
						friendid 		: null
					},

					"{pendingFriendButton} click" : function( el )
					{
						self.dropdown().html(
							$.language( 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT_NOTICE' )
						);
					},

					/**
					 * Triggered when the add friend button is clicked
					 */
					"{addFriendButton} click" : function( el )
					{
						var id = self.element.data('friend-uid');

						$( el ).addClass( 'btn-loading' );

						// Implement controller on add friend.
						EasySocial.ajax( 'site/controllers/friends/request' ,
						{
							"viewCallback"	: "usersRequest",
							"id"	: id
						})
						.done( function( button )
						{
							// After the request is complete, set the correct friend id.
							// self.options.friendid 	= friendId;

							$( el ).replaceWith( button );

							self.dropdown().remove();

							// Remove the loading state from the button
							$( el ).removeClass( 'btn-loading' );

						})
						.fail(function( message )
						{
							EasySocial.dialog(
							{
								content 	: EasySocial.ajax( 'site/views/friends/exceeded' )
							});
							
							self.dropdown().html( message );
						});

					}

				}
			});

		module.resolve();
	});

});
