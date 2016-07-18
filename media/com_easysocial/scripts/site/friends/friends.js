EasySocial.module( 'site/friends/friends' , function($){

	var module 	= this;

	EasySocial.require()
	.script(
		'site/friends/list' ,
		'site/friends/item' ,
		'site/friends/suggest',
		'site/conversations/composer'
	)
	.view(
		'site/loading/small' ,
		'site/friends/default.empty' ,
		'site/friends/list.assign'
	)
	.done(function($){

		EasySocial.Controller(
			'Friends.Birthday',
			{
				defaultOptions:
				{
					"{messageButton}"	: "[data-upcoming-birthday-message-button]"
				}
			},
			function( self ){
				return {
					init: function()
					{
						// Get the id of the current user.
						self.options.id 	= self.element.data( 'id' ),
						self.options.name 	= self.element.data( 'name' ),
						self.options.avatar	= self.element.data( 'avatar' );

						self.messageButton().implement( EasySocial.Controller.Conversations.Composer.Dialog,
						{
							"recipient"	:
							{
								"id"	: self.options.id,
								"name"	: self.options.name,
								"avatar": self.options.avatar
							}
						});
					}

				}
			}

		);


		EasySocial.Controller(
			'Friends',
			{
				defaultOptions:
				{
					// Get the default active list if there is any.
					activeList 		: null,

					// Left side friend's list.
					"{friendList}"	: "[data-friends-list]",

					// Content area.
					"{content}"		: "[data-friends-content]",

					// Result
					"{friendItems}"	: "[data-friends-items]",
					"{friendItem}"	: "[data-friends-item]",
					"{emptyList}"	: "[data-friends-emptyItems]",
					"{activeTitle}"	: "[data-friends-activeTitle]",

					// Friends filter
					"{filterItem}"	: "[data-friends-filter]",

					// Friend list actions
					"{friendListActions}"	: "[data-friendList-actions]",

					// Button to add a friend to the list.
					"{addFriendToList}"	: "[data-friends-add]",

					// Counters
					"{friendsCounter}"	: "[data-total-friends]",
					"{pendingCounter}"	: "[data-total-friends-pending]",
					"{suggestionCounter}": "[data-total-friends-suggestion]",
					"{requestCount}"	: "[data-frields-request-sent-count]",

					view :
					{
						loader 				: "site/loading/small",
						emptyFriendItems 	: "site/friends/default.empty",
						addUserForm			: "site/friends/list.assign"
					}

				}
			},
			function( self ){
				return {

					init: function()
					{
						// Implement friend list controller.
						self.friendList().implement( EasySocial.Controller.Friends.List ,
						{
							// parent : self,
							"{parent}" : self
						});

						//Initialize friend item controllers
						self.initFriendItems();
					},

					initFriendItems: function()
					{
						// Apply the friend list actions
						self.friendListActions().implement( EasySocial.Controller.Friends.List.Actions ,
						{
							"{parent}"	: self
						})

						// Implement friend item controller.
						self.friendItem().implement( EasySocial.Controller.Friends.Item ,
						{
							"{parent}"	: self
						});
					},

					updateFriendsCounter: function()
					{
						EasySocial.ajax( 'site/controllers/friends/getCounters' )
						.done(function( totalFriends , totalPending , totalRequests , totalSuggestion )
						{
							self.friendsCounter().html( totalFriends );

							self.pendingCounter().html( totalPending );

							self.requestCount().html( totalRequests );

							self.suggestionCounter().html( totalSuggestion );
						});
					},

					updateListCounters: function()
					{
						EasySocial.ajax( 'site/controllers/friends/getListCounts' ,
						{
						})
						.done( function( lists ){

							$( lists ).each( function( i , list){
								var listController = $( '[data-list-' + list.id + ']').controller();

								listController.updateCounter( list.count );
							});

						});
					},

					insertItem: function( item )
					{
						// Hide any empty notices.
						self.emptyList().hide();

						// Update the counter for the list items.
						self.updateListCounters();

						$( item ).implement( EasySocial.Controller.Friends.Item ,
						{
							"{parent}"	: self
						})
						.prependTo( self.friendItems() );

					},

					removeItem: function( id )
					{
						// Remove item from the list.
						self.friendItem( '[data-friendItem-' + id + ']' ).remove();

						if( self.friendItem().length <= 0 )
						{
							self.emptyList().show();
						}

						// Update the counter for the list items.
						self.updateListCounters();

					},

					updateFriendRequestCount: function( value )
					{
						curCount = parseInt( self.requestCount().text(), 10 );
						if( curCount != NaN )
						{
							curCount = curCount + value;
							self.requestCount().text( curCount );
						}
					},

					updateContent: function( html )
					{
						// Update the content on the friends list.
						self.content().replaceWith(html);

						self.initFriendItems();
					},

					removeActiveFilter: function()
					{
						self.filterItem().removeClass( 'active' );
					},

					"{filterItem} click" : function(filterItem, event )
					{
						var filterType 	= filterItem.data( 'filter' ),
							title 		= filterItem.data( 'title' ),
							userid 		= filterItem.data( 'userid' ),
							url 		= filterItem.data( 'url' );


						// Removes all active state from the friend lists
						if( self.friendList().length > 0)
						{
							self.friendList().controller().removeActiveList();
						}

						// Remove all active state on the filter links.
						self.filterItem().removeClass("active");

						// Add active class to this filter.
						filterItem.addClass( 'active' );

						History.pushState( {state:1} , title , url );

						filterItem.addClass( 'loading' );

						EasySocial.ajax(
							"site/controllers/friends/filter",
							{
								"filter"	: filterType,
								"userid"	: userid

							})
							.done(function(html){

								self.updateContent( html );
							})
							.always(function(){

								// Remove loading on the element.
								filterItem.removeClass("loading");
							});
					}
				}
			}
		);

		module.resolve();
	});
});
