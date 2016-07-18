EasySocial.module( 'site/conversations/conversations' , function($){

	var module 	= this;


	EasySocial.require()
	.script( 'site/conversations/mailbox' , 'site/conversations/item' , 'site/conversations/filter' )
	.language( 'COM_EASYSOCIAL_NO_BUTTON' )
	.done( function($){

		EasySocial.Controller(
			'Conversations',
			{
				defaultOptions:
				{
					"{mailbox}"	: "[data-conversations-mailbox]",
					"{list}"	: "[data-conversations-list]",
					"{content}"	: "[data-conversations-content]",

					"{item}"		: "[data-conversations-item]",

					// Conversation actions
					"{actions}"		: "[data-conversations-actions]",

					// Conversations filter
					"{filterItem}"	: "[data-conversations-filter]",

					// Check All
					"{checkAll}"	: "[data-conversations-checkAll]",
					"{checkbox}"	: "[data-conversationItem-checkbox]",

					// Actions that can be performed on the conversations
					"{delete}"		: "[data-conversations-delete]",
					"{archive}"		: "[data-conversations-archive]",
					"{unarchive}"	: "[data-conversations-unarchive]",
					"{unread}"		: "[data-conversations-unread]",
					"{read}"		: "[data-conversations-read]"
				}
			},
			function( self ){

				return {

					init: function()
					{
						// Implement mailbox controller.
						self.mailbox().implement( EasySocial.Controller.Conversations.Mailbox ,
						{
							"{parent}"	: self
						});

						self.item().implement( EasySocial.Controller.Conversations.Item , {
							"{parent}"	: self
						});

						self.filterItem().implement( EasySocial.Controller.Conversations.Filter ,
						{
							"{parent}"	: self
						});
					},

					"{filterItem} click" : function( el )
					{
						// Remove all active classes on filter link.
						self.filterItem().removeClass( 'active' );

						// Add active class on active element.
						$( el ).addClass( 'active' );
					},

					"{checkbox} change" : function( el )
					{
						// See if there's any more checked items.
						if( self.checkbox( ':checked' ).length <= 0 && !el.is( ':checked' ) )
						{
							return self.actions().removeClass( 'is-checked' );
						}

						self.actions().addClass( 'is-checked' );
					},

					/**
					 * Checks all checkbox on the page.
					 */
					"{checkAll} click" : function( el )
					{
						// If there's nothing to check, we do not let them to check anything.
						if( self.checkbox().length <= 0 )
						{
							// Uncheck this.
							$( el ).prop( 'checked' , false );
							
							return false;
						}

						if( el.is( ':checked' ) )
						{
							// We don't want to trigger the checked items since they are already checked.
							self.checkbox( ':not(:checked)' ).click();
						}
						else
						{
							self.checkbox( ':checked' ).click();
						}
					},

					/**
					 * Allows caller to add an is-empty to the list.
					 */
					showEmpty: function()
					{
						self.content().addClass( 'is-empty' );
					},

					/**
					 * Allows caller to add an is-empty to the list.
					 */
					hideEmpty: function()
					{
						self.content().removeClass( 'is-empty' );
					},

					/**
					 * Toggles the loading class on the content.
					 */
					toggleLoading: function()
					{
						self.content().removeClass( 'is-empty' )
							.toggleClass( 'is-loading' );
					},

					/**
					 * Allows caller to trigger this method to update the conversations content.
					 */
					updateContent : function( content , mailbox )
					{
						if( mailbox != undefined )
						{
							self.content().addClass( 'layout-' + mailbox );
						}
						else
						{
							self.content().removeClass( 'layout-archives' );
						}
						// Whenever updateContent is called, we need to hide the actions
						self.actions().removeClass('is-checked');
						self.checkAll().removeAttr( 'checked' );
						
						self.list().html( content );
					},

					getSelectedConversations : function()
					{
						// Let's see if there's any checked items.
						if( self.checkbox(':checked').length <= 0 )
						{
							return false;
						}

						var selected	= new Array;
						self.checkbox(':checked').each( function( i , checkedItem ){
							selected.push( $( checkedItem ).val() );
						});

						return selected;
					},

					"{archive} click" : function()
					{
						var selected	= self.getSelectedConversations();

						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/conversations/confirmArchive' , { "ids" : selected } ),
							bindings 	:
							{
								"{confirmButton} click" : function()
								{
									$( '[data-conversation-archive-form]' ).submit();
								}
							}
						});
					},

					"{unarchive} click" : function()
					{
						var selected	= self.getSelectedConversations();

						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/conversations/confirmUnarchive' , { "ids" : selected } ),
							bindings 	:
							{
								"{confirmButton} click" : function()
								{
									$( '[data-conversation-archive-form]' ).submit();
								}
							}
						})
					},

					"{delete} click" : function()
					{
						var selected	= self.getSelectedConversations();

						EasySocial.dialog(
						{
							content		: EasySocial.ajax( 'site/views/conversations/confirmDelete' , { "ids" : selected }),
							bindings	:
							{
								"{deleteButton} click" : function()
								{
									$( '[data-conversation-delete-form]' ).submit();
								}
							}
						});

					},

					"{read} click" : function()
					{
						// If there's nothing to mark as unread, just ignore.
						if( self.checkbox( ':checked' ).length <= 0 )
						{
							return false;
						}
						
						var ids = new Array();

						// Loop through each checked items.
						self.checkbox( ':checked' ).each( function( i , checkedItem ){
							ids.push( $( checkedItem ).val() );
						});

						EasySocial.ajax( 'site/controllers/conversations/markRead' , 
						{
							"ids"	: ids
						})
						.done( function(){
							
							// Add unread class on the items.
							self.checkbox()
								.parents( '[data-conversations-item]' )
								.removeClass( 'unread' )
								.addClass( 'read' );

							// We need to tell the mailbox controller to update the count.
							self.mailbox().controller().updateCounters();
						})
						.fail(function( message )
						{
							self.setMessage( message );
						});

					},

					"{unread} click" : function()
					{
						// If there's nothing to mark as unread, just ignore.
						if( self.checkbox( ':checked' ).length <= 0 )
						{
							return false;
						}
						
						var ids = new Array();

						// Loop through each checked items.
						self.checkbox( ':checked' ).each( function( i , checkedItem ){
							ids.push( $( checkedItem ).val() );
						});

						EasySocial.ajax( 'site/controllers/conversations/markUnread' , 
						{
							"ids"	: ids
						})
						.done( function(){
							
							// Add unread class on the items.
							self.checkbox()
								.parents( '[data-conversations-item]' )
								.removeClass( 'read' )
								.addClass( 'unread' );

							// We need to tell the mailbox controller to update the count.
							self.mailbox().controller().updateCounters();

						})
						.fail(function( message ){
							console.log( message );
						});

					}
				}
			}
		);

		module.resolve();
	});

});

