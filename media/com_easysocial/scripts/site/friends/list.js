EasySocial.module( 'site/friends/list' , function($){

	var module 	= this;

	EasySocial.require()
	.view( 
		'site/loading/small'
	)
	.library( 'history' )
	.script( 'site/friends/suggest' )
	.done(function($){

		EasySocial.Controller(
			'Friends.List',
			{
				defaultOptions:
				{
					parent 		: null,

					"{item}"	: "[data-friends-listItem]",
					"{items}"	: "[data-friends-listItems]",

					"{loadMoreButton}"	: ".loadMoreButton",
					"{loadMore}"		: ".loadMore",

					view :
					{
						loader 		: "site/loading/small",
						items 		: "site/friends/default.lists"
					}
				}
			},
			function( self ){
				return {

					init: function()
					{
						self.item().implement( EasySocial.Controller.Friends.List.Item ,
						{
							"{parent}"	: self
						});
					},

					removeActiveList: function()
					{
						self.item().removeClass( 'active' );
					},

					setDefault : function( id )
					{
						// Remove all items with default class
						self.item().removeClass( 'default' );

						// Add default class on the item
						self.item( '.item-' + id ).addClass( 'default' );
					},

					"{item} click" : function( el )
					{
						var title 	= $(el).data( 'title' ),
							url 	= $(el).data( 'url' );

						History.pushState( {state:1} , title , url );

						// Remove all active class from filters.
						self.parent.removeActiveFilter();

						// Remove all active class from list
						self.item().removeClass( 'active' );

						// Add active class to this element.
						self.item( el ).addClass( 'active' );

						var id 	= $( el ).data( 'id' );

						// Set the active list.
						self.parent.options.activeList	= id;
						
						// Get list of friends.
						EasySocial.ajax( 'site/controllers/friends/getListFriends',
						{
							"id"	: id
						},
						{
							beforeSend: function()
							{
								$( el ).addClass( 'loading' );
							}
						})
						.done(function( html ){

							// Hide loading.
							$( el ).removeClass( 'loading' );

							// Trigger friends list to update with appropriate content.
							self.parent.updateContent( html );

						});
					},

					"{loadMoreButton} click" : function() {
						
						// Get current limit start.
						var limitstart	= self.loadMoreButton().data( 'limitstart' );

						self.loadMore().html( self.view.loader() );

						// Get list of friends.
						EasySocial.ajax(
							"site/controllers/friends/getLists",
							{
								limitstart: limitstart
							})
							.done(function( items ){

								// Hide load more button since nothing to load anymore.
								self.loadMore().hide();

								self.view.items({
									"items"	: items
								}).appendTo( self.items() );
							});
					}
				}
			}
		);

		EasySocial.Controller(
			'Friends.List.Item',
			{
				defaultOptions: 
				{
					id 			: null,

					"{counter}"	: "[data-list-counter]"
				}
			},
			function( self )
			{
				return {

					init: function()
					{
						self.options.id 	= self.element.data( 'id' );
					},

					updateCounter: function( total )
					{
						self.counter().html( total );
					}
				}
			});

		EasySocial.Controller(
			'Friends.List.Actions',
			{
				defaultOptions:
				{
					"{delete}"	: "[data-friendListActions-delete]",
					"{add}"		: "[data-friendListActions-add]",
					"{default}"	: "[data-friendListActions-default]"
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						self.options.id 	= self.element.data( 'id' );
						self.options.title 	= self.element.data( 'title' );
						self.options.userId	= self.element.data( 'userid' );
					},

					"{default} click" : function()
					{
						EasySocial.ajax( 'site/controllers/friends/setDefault' ,
						{
							"id"	: self.options.id
						})
						.done(function()
						{
							// Set the default class on the list item
							self.parent.friendList().controller().setDefault( self.options.id );
						});
					},

					"{add} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/friends/assignList' , { "id" : self.options.id } ),
							bindings 	:
							{
								"{insertButton} click" : function()
								{
									var items = this.suggest().textboxlist("controller").getAddedItems();

									EasySocial.ajax( 'site/controllers/friends/assign' ,
									{
										"uid"		: $.pluck(items, "id"),
										"userId"	: self.options.userId,
										"listId"	: self.options.id
									})
									.done(function( contents ){

										// Hide any notice messages.
										$( '[data-assignFriends-notice]' ).hide();
										

										$( contents ).each(function( i , item ){

											// Pass the item to the parent so it gets inserted into the friends list.
											self.parent.insertItem( item );

											$('[data-friends-items]').removeClass('is-empty');
											
											// Close the dialog
											EasySocial.dialog().close();
										});
									})
									.fail( function( message ){
										$( '[data-assignFriends-notice]' ).addClass( 'alert alert-error' )
											.html( message.message );
									});
								}
							}
						});
					},

					"{delete} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( "site/views/friends/confirmDeleteList" , { "id" : self.options.id } ),
							bindings	:
							{
								"{deleteButton} click" : function()
								{
									$( '[data-friends-list-delete-form]' ).submit();
								}
							}
						});
					}
				}
			}
		);

		module.resolve();
	});
});
