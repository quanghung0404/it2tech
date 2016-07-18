EasySocial.module( 'site/friends/item' , function($){

	var module 	= this;

	EasySocial.require()
	.script( 'site/conversations/composer' )
	.language( 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT_PENDING_APPROVAL', 'COM_EASYSOCIAL_FRIENDS_REQUEST_DIALOG_TITLE', 'COM_EASYSOCIAL_FRIENDS_CANCEL_REQUEST_DIALOG_CANCELLED', 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT' )
	.done(function($){

		EasySocial.Controller(
			'Friends.Item',
			{
				defaultOptions:
				{
					id 					: null,
					name 				: null,
					friendId 			: null,

					"{removeFromList}"	: "[data-lists-removeFriend]",
					"{unfriend}"		: "[data-friends-unfriend]",
					"{addfriend}"		: "[data-friends-addfriend]",
					"{block}"			: "[data-friends-block]",
					"{message}"			: "[data-friendItem-message]",
					"{reject}"			: "[data-friendItem-reject]",
					"{approve}"			: "[data-friendItem-approve]",

					"{addContainer}" : "[data-friendItem-addbutton]",
					"{addButton}" : "[data-friendItem-add]",

					"{cancelRequest}"	: "[data-friendItem-cancel-request]"
				}
			},
			function( self ){
				return {

					init: function()
					{
						self.options.id 		= self.element.data( 'id' );
						self.options.name 		= self.element.data( 'name' );
						self.options.friendId	= self.element.data( 'friendid' );
						self.options.avatar 	= self.element.data( 'avatar' );

						// Initialize conversation links
						self.initConversation();
					},

					"{addButton} click" : function( el )
					{

						// Implement controller on add friend.
						EasySocial.ajax( 'site/controllers/friends/request' ,
						{
							"id"	: self.options.id
						}).done(function(friendId) {
							// replace the button with done message.
							self.addContainer().html( $.language('COM_EASYSOCIAL_FRIENDS_REQUEST_SENT') );
							self.addContainer().addClass('btn btn-es-success btn-sm mt-20');

						}).fail(function(obj) {


							EasySocial.dialog({
								width: 450,
								height: 180,
								content: obj.message
							});
						});
					},

					initConversation : function()
					{
						// Implement conversation controller on the message link.
						self.message().implement( EasySocial.Controller.Conversations.Composer.Dialog ,
						{
							"recipient"	:
							{
								"name"		: self.options.name,
								"id"		: self.options.id,
								"avatar"	: self.options.avatar
							}
						});

					},

					"{removeFromList} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/friends/confirmRemoveFromList' , { "id" : self.options.id }),
							bindings 	:
							{
								"{removeButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/friends/removeFromList' ,
									{
										"listId"	: self.parent.options.activeList,
										"userId"	: self.options.id
									})
									.done( function(){

										// Remove the item from the list.
										self.parent.removeItem( self.options.id );

										// Update the counter.

										// Update the dialog to notify the user that the user has been removed from the list.
										EasySocial.dialog(
										{
											"title"		: "User removed from list",
											"content"	: "The user has been removed from the list.",
											"buttons"	:
											[
												{
													"name"			: "Done",
													"classNames"	: "btn btn-es",
													"click"			: function()
													{
														EasySocial.dialog().close();
													}
												}
											]
										})
									})
									.fail( function(message){
										console.log( message );
									});
								}
							}
						});

					},

					"{reject} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/friends/confirmReject' , { "id" : self.options.id } ),
							bindings	:
							{
								// "{rejectButton} click" : function()
								// {
								// 	$( '[data-friends-reject-form]' ).submit();
								// }


								"{rejectButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/friends/reject' ,
									{
										"id"	: self.options.friendId
									})
									.done(function()
									{
										// Remove itself from the list.
										self.parent.removeItem( self.options.id );

										// Update the counter
										self.parent.updateFriendsCounter();

										EasySocial.dialog(
										{
											content 	: EasySocial.ajax( 'site/views/friends/friendRejected' )
										});
									});
								}



							}
						});
					},

					"{unfriend} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/friends/confirmUnfriend' , { "id" : self.options.id }),
							bindings 	:
							{
								"{unfriendButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/friends/unfriend' ,
									{
										"id"	: self.options.friendId
									})
									.done(function()
									{
										// Remove itself from the list.
										self.parent.removeItem( self.options.id );

										// Update the counter
										self.parent.updateFriendsCounter();

										EasySocial.dialog(
										{
											content 	: EasySocial.ajax( 'site/views/friends/friendRemoved' , { "id" : self.options.id } )
										});
									});
								}
							}
						});

					},

					"{cancelRequest} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/friends/confirmCancelRequest' , { "id" : self.options.id }),
							bindings 	:
							{
								"{cancelRequestButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/friends/cancelRequest' ,
									{
										"id"	: self.options.friendId
									})
									.done(function()
									{
										// Remove itself from the list.
										self.parent.removeItem( self.options.id );

										// update count.
										self.parent.updateFriendRequestCount( -1 );

										EasySocial.dialog(
										{
											content 	: EasySocial.ajax( 'site/views/friends/requestCancelled' , { "id" : self.options.id } )
										});
									});
								}
							}
						});

					},

					"{approve} click" : function( el )
					{
						EasySocial.ajax( 'site/controllers/friends/approve',
						{
							"id" : self.options.friendId
						})
						.done(function()
						{
							// Update the counter
							self.parent.updateFriendsCounter();

							// Remove this item from the pending list.
							self.element.remove();
						});
					},

					"{addfriend} click" : function( el )
					{

						// Implement controller on add friend.
						EasySocial.ajax( 'site/controllers/friends/request' ,
						{
							"id"	: self.options.id
						})
						.done( function( friendId )
						{
							// update count
							self.parent.updateFriendRequestCount( 1 );

							EasySocial.dialog({
								title: $.language('COM_EASYSOCIAL_FRIENDS_REQUEST_DIALOG_TITLE'),
								content: $.language('COM_EASYSOCIAL_FRIENDS_REQUEST_SENT_PENDING_APPROVAL', self.options.name )
							});

							// Remove itself from the list.
							self.parent.removeItem( self.options.id );
						})
						.fail(function( message )
						{
							EasySocial.dialog(
							{
								content 	: EasySocial.ajax( 'site/views/friends/exceeded' )
							});
						});
					},

					"{block} click" : function()
					{
						console.log( 'block' );
					}
				}
			}
		);

		module.resolve();
	});
});
