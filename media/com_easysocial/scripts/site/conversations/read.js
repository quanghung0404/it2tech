EasySocial.module( 'site/conversations/read' , function($){

	var module 	= this;

	EasySocial.require()
	.library( 'dialog' )
	.script( 'site/conversations/composer' , 'site/friends/suggest' )
	.language(
		'COM_EASYSOCIAL_CONVERSATION_REPLY_POSTED_SUCCESSFULLY',
		'COM_EASYSOCIAL_CONVERSATION_REPLY_FORM_EMPTY'
	)
	.done(function($){

		EasySocial.Controller(
			'Conversations.Read',
			{
				defaultOptions:
				{
					// Conversation id.
					id 	: "",

					// Determines if these features should be enabled.
					attachments 		: true,
					location 			: false,
					maxSize 			: "3mb",

					extensionsAllowed	 : "",
					attachmentController : null,
					composerController	 : null,

					// Conversation items.
					"{item}"		: "[data-readConversation-item]",
					"{items}"		: "[data-readConversation-items]",

					// Form composer
					"{composer}"	: "[data-readConversation-composer]",

					// Buttons
					"{replyButton}"	: "[data-readConversation-replyButton]",

					// Add participant to a conversation.
					"{addParticipant}"	: "[data-readConversation-addParticipant]",

					// Leave conversation.
					"{leaveConversation}"	: "[data-readConversation-leaveConversation]",

					// Delete conversation.
					"{delete}"			: "[data-readConversation-delete]",

					// Attachments
					"{attachments}"		: "[data-uploaderQueue-id]",

					// Notice message on reply form.
					"{replyNotice}"		: "[data-readConversation-replyNotice]",

					// Load previous message button.
					"{readLoadMore}"		: "[data-readconversation-load-more]",


					// Views
					view	:
					{
						messageItem		: 'site/conversations/read.message'
					}
				}
			},
			function( self ){
				return {

					init: function()
					{
						// Implement the composer on the reply form
						self.composer().implement( EasySocial.Controller.Conversations.Composer ,
						{
							"{uploader}"		: "[data-readConversation-attachment]",
							"{location}"		: "[data-readConversation-location]",
							maxSize 			: self.options.maxSize,
							extensionsAllowed	: self.options.extensionsAllowed
						});

						// Get the composer controller.
						self.options.composerController 	= self.composer().controller();

						if( self.options.attachments )
						{
							// Get the uploader controller.
							self.options.attachmentController = self.options.composerController.uploader().controller();
						}

						if( self.options.location )
						{
							// Get the location controller.
							self.options.locationController = self.options.composerController.location().controller();
						}

						// Initialize message item.
						self.item().implement( EasySocial.Controller.Conversations.Read.Item );

						// Set the conversation id.
						self.options.id 	= self.element.data( 'id' );
					},

					resetForm: function()
					{
						// Reset the editor form.
						self.options.composerController.resetForm();

						var mentions = self.composer().controller().editorArea().mentions( 'controller' );

						mentions.reset();

						if( self.options.location )
						{
							// Reset the location.
							self.options.locationController.removeLocation();
						}

						if( self.options.attachments )
						{
							// Reset the uploader.
							self.options.attachmentController.reset();
						}
					},

					"{readLoadMore} click" : function( el )
					{
						var id = $( el ).data( 'id' ),
							limitstart = $(el).data( 'limitstart' );

						self.readLoadMore().hide();
						$( '.loading-indicator' ).show();

						var options 	=	{
												"id"			: id,
												"limitstart"	: limitstart
											};

						// Do an ajax call to submit the reply.
						EasySocial.ajax( 'site/controllers/conversations/loadPrevious' , options )
						.done(function( html, nextlimit )
						{
							$.buildHTML(html)
								.prependTo(self.items())
								.addController("EasySocial.Controller.Conversations.Read.Item");

							if( nextlimit == 0 )
							{
								self.readLoadMore().hide();
							}
							else
							{
								self.readLoadMore().show();
								$(el).data( 'limitstart', nextlimit );
							}

						})
						.always(function()
						{
							$( '.loading-indicator' ).hide();
						});


					},


					"{replyButton} click" : function( el , event )
					{
						// Stop bubbling up.
						event.preventDefault();

						var content 	= self.options.composerController.editor().val(),
							files 		= new Array;


						if( content.length <= 0 )
						{
							self.replyNotice().html( $.language( 'COM_EASYSOCIAL_CONVERSATION_REPLY_FORM_EMPTY' ) ).addClass( 'alert alert-error' ).removeClass( 'alert-success' );
							return false;
						}

						if( self.options.attachments )
						{
							// Get through each attachments.
							self.attachments().each( function( i , attachment ){
								files.push( $( attachment ).val() );
							});
						}

						var options 	=	{
												"id"		: self.options.id,
												"message"	: content
											};

						if( self.options.attachments )
						{
							options[ 'upload-id' ]	= files;
						}

						if( self.options.location )
						{
							options.address 	= self.options.locationController.locationInput().val();
							options.latitude	= self.options.locationController.locationLatitude().val();
							options.longitude	= self.options.locationController.locationLongitude().val();
						}

						options[ 'tags' ]	= self.composer().controller().editorArea().mentions( 'controller' ).toArray();

						// Disable submit button.
						self.replyButton().attr( 'disabled' , true );

						// Do an ajax call to submit the reply.
						EasySocial.ajax( 'site/controllers/conversations/reply' , options )
						.done(function( html )
						{
							self.replyNotice().html( $.language( 'COM_EASYSOCIAL_CONVERSATION_REPLY_POSTED_SUCCESSFULLY' ) ).addClass( 'alert alert-success' ).removeClass( 'alert-error' );

							// Apply controller on the appended item.
							var item 	= $( html );

							item.implement( EasySocial.Controller.Conversations.Read.Item );

							// Append the data back to the list.
							self.items().append( item );

							// Reset the composer form.
							self.resetForm();
						})
						.always(function()
						{
							// Re-activate button.
							self.replyButton().attr( 'disabled' , false );
						});


						return false;
					},

					"{leaveConversation} click" : function()
					{
						EasySocial.dialog({
							content : EasySocial.ajax( 'site/views/conversations/confirmLeave' , { id : self.options.id } )
						});
					},

					"{addParticipant} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/conversations/addParticipantsForm' , { "id" : self.options.id })
						});
					},

					"{delete} click" : function()
					{
						EasySocial.dialog(
						{
							content : EasySocial.ajax( 'site/views/conversations/confirmDelete' , { "ids" : [ self.options.id ] } ),
							bindings:
							{
								"{deleteButton} click" : function()
								{
									$( '[data-conversation-delete-form]' ).submit();
								}
							}
						});
					},

					/**
					 * Adds a new item into the reading list.
					 */
					addItem: function( obj ){
						// Append the message item into the list.
						self.messageList().append(
							self.view.messageItem({
								item: obj
							})
						);

						// Now we need to empty the message.
						self.textMessage().val( '' ).focus();
					}
				}
		});

		EasySocial.Controller(
			'Conversations.Read.Item',
			{
				defaultOptions :
				{
					id 	: null,

					"{attachmentsWrapper}" : "[data-conversation-attachment-wrapper]",
					"{attachments}"		: "[data-conversation-attachment]"
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						// Get the message id.
						self.options.id 	= self.element.data( 'id' );

						// Implement attachment items.
						self.attachments().implement( EasySocial.Controller.Conversations.Read.Item.Attachment ,
							{
								"{parent}" : self
							});
					},

					removeAttachment : function( el , event )
					{
						// Remove the attachment item
						$( el ).remove();

						// Check to see if there are any more attachments.
						if( self.attachments().length == 0 )
						{
							self.attachmentsWrapper().hide();
						}
					}
				}
			});

		EasySocial.Controller(
			'Conversations.Read.Item.Attachment',
			{
				defaultOptions :
				{
					"{deleteAttachment}" 	: "[data-attachment-delete]"
				}
			},
			function( self )
			{
				return {
					init : function()
					{

					},

					"{deleteAttachment} click" : function( el , event )
					{
						var attachmentId 	= $( el ).data( 'id' );

						EasySocial.dialog(
						{
							content : EasySocial.ajax( 'site/views/conversations/confirmDeleteAttachment', { "id" : attachmentId } ),
							bindings :
							{
								"{deleteButton} click" : function()
								{
									EasySocial.ajax( 'site/controllers/conversations/deleteAttachment',
									{
										id 	: attachmentId
									})
									.done( function()
									{
										// Remove the attachment element.
										self.parent.removeAttachment( self.element );

										EasySocial.dialog(
										{
											content 	: EasySocial.ajax( 'site/views/conversations/attachmentDeleted' , {} )
										});
									})
									.fail( function( message )
									{
										self.setMessage( message );
									})
								}
							}
						});
					}
				}
			})
		module.resolve();
	});
});
