EasySocial.module( 'site/toolbar/friends' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'tinyscrollbar' )
	.language( 'COM_EASYSOCIAL_FRIENDS_REQUEST_REJECTED' )
	.done(function($){

		EasySocial.Controller(
			'Notifications.Friends',
			{
				defaultOptions:
				{

					// Check every 10 seconds by default.
					interval: 3,

					// The return url when the friend approval is approved.
					returnURL 	: "",

					// Elements within this container.
					"{counter}"		: "[data-notificationFriends-counter]",
					"{loadRequestsButton}" : ".loadRequestsButton"
				}
			},
			function(self){ return{

				init: function()
				{
					// Start the automatic checking of new notifications.
					self.startMonitoring();
				},

				/**
				 * Start running checks.
				 */
				startMonitoring: function()
				{
					var interval 	= self.options.interval * 1000;

					// Debug
					if( EasySocial.debug )
					{
						// console.info( 'Start monitoring friend requests at interval of ' + self.options.interval + ' seconds.' );
					}

					self.options.state	= setTimeout( self.check , interval );
				},

				/**
				 * Stop running any checks.
				 */
				stopMonitoring: function()
				{
					// Debug
					if( EasySocial.debug )
					{
						// console.info( 'Stop monitoring friend requests.' );
					}

					clearTimeout( self.options.state );
				},

				/**
				 * Check for new updates
				 */
				check: function(){

					// Stop monitoring so that there wont be double calls at once.
					self.stopMonitoring();

					var interval 	= self.options.interval * 1000;

					// Needs to run in a loop since we need to keep checking for new notification items.
					setTimeout( function(){

						EasySocial.ajax('site/controllers/notifications/friendsCounter')
						.done( function( total )
						{

							if( total > 0 )
							{
								// Update element
								self.element.addClass( 'has-notice' );

								// Update the counter's count.
								self.counter().html( total );
							}
							else
							{
								self.element.removeClass( 'has-notice' );
							}

							// Continue monitoring.
							self.startMonitoring();
						});

					}, interval );
				}
			}}
		);

		EasySocial.Controller(
			'Notifications.Friends.Item',
			{
				defaultOptions:
				{
					"{actionsWrapper}" 	: "[data-friend-item-actions]",
					"{acceptFriend}"	: "[data-friend-item-accept]",
					"{rejectFriend}"	: "[data-friend-item-reject]",
					"{actions}"			: "[data-friend-item-action]",
					"{title}"			: "[data-friend-item-title]",
					"{mutual}" 			: "[data-friend-item-mutual]",

					// Views
					view	:
					{
						loader 		: 'site/loading/small'
					},
				}
			},
			function( self ){
				return {

					init: function()
					{

					},

					"{acceptFriend} click" : function( el , event )
					{
						// Stop other events from being triggered.
						event.stopPropagation();

						var toolbar	= $('[data-notifications-friends]').controller(),
							counter	= false;

						self.actionsWrapper().addClass( 'friend-adding' );

						// Send an ajax request to approve the friend.
						EasySocial.ajax( 'site/controllers/friends/approve' ,
						{
							viewCallback	: "notificationsApprove",
							id				: $( el ).data( 'id' )
						})
						.done(function( title , mutualFriendsContent )
						{
							// Update the current state
							self.actionsWrapper().removeClass( 'friend-adding' ).addClass( 'added-friends' );

							self.title().html( title );

							self.mutual().html( mutualFriendsContent );


							if (toolbar) {
								var counter 	= toolbar.counter().html(),
									counter 	= parseInt(counter),
									counter 	= counter - 1
									counter		= counter.toString();

								toolbar.counter().html(counter);

								if (counter == 0) {
									toolbar.element.removeClass('has-notice');
								}
							}
						})
						.fail( function( message )
						{
							// Append error message.
							self.element.html( message.message );
						});

					},


					"{rejectFriend} click" : function( el , event )
					{
						event.stopPropagation();

						EasySocial.ajax( 'site/controllers/friends/reject' ,
						{
							"id"	: $( el ).data( 'id' )
						})
						.done( function( button )
						{
							self.actionsWrapper().html( $.language( 'COM_EASYSOCIAL_FRIENDS_REQUEST_REJECTED' ) );
						})
						.fail( function( message )
						{
							// Append error message.
							self.element.html( message.message );
						});

					}
				}
			}
		);

		module.resolve();
	});

});
