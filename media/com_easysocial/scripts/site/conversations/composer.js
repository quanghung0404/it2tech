EasySocial.module( 'site/conversations/composer' , function($){

	var module 	= this;

	EasySocial.require()
	.library('mentions')
	.script('site/friends/suggest', 'uploader/uploader')
	.view(
		"site/friends/suggest.item",
		"site/friends/suggest.hint.search",
		"site/friends/suggest.hint.empty",
		"site/hashtags/suggest.item",
		"site/hashtags/suggest.hint.search",
		"site/hashtags/suggest.hint.empty"
	)
	.language(
		'COM_EASYSOCIAL_CONVERSATIONS_ERROR_EMPTY_RECIPIENTS',
		'COM_EASYSOCIAL_CONVERSATIONS_ERROR_EMPTY_MESSAGE'
	)
	.done(function($){

		EasySocial.Controller('Conversations.Composer', {
				defaultOptions: {

					// Determines if these features should be enabled.
					attachments 		: true,
					location 			: true,
					showNonFriend		: false,

					// Uploader properties.
					extensionsAllowed	: "",

					// File uploads
					"{uploader}"		: "[data-composer-attachment]",

					// Location service.
					"{location}"		: "[data-composer-location]",

					// The text editor.
					"{editorHeader}"	: "[data-composer-editor-header]",
					"{editorArea}"		: "[data-composer-editor-area]",
					"{editor}"			: "[data-composer-editor]",

					// Wrapper for suggest to work.
					"{friendSuggest}"		: "[data-friends-suggest]",

					"{recipients}"		: "input[name=uid\\[\\]],input[name=list_id\\[\\]]",

					"{recipientRow}"	: "[data-composer-recipients]",
					"{messageRow}"		: "[data-composer-message]",

					// Submit button
					"{submit}"			: "[data-composer-submit]",

					view:
					{
						suggestItem: "site/friends/suggest.item",
						tagSuggestItem: "site/hashtags/suggest.item"
					}
				}
			}, function(self) { return {

					init: function() {

						// Initialize the participants textbox.
						self.initSuggest();

						// Initialize uploader
						if (self.options.attachments) {
							self.initUploader();
						}

						self.setMentionsLayout();
					},

					resetForm: function() {
						self.editor().val('');
					},

					setMentionsLayout: function() {

						var editor = self.editorArea();
						var mentions = editor.controller("mentions");

						if (mentions) {
							mentions.cloneLayout();
							return;
						}

						var header = self.editorHeader();

						editor
							.mentions({
								
								triggers: {
								    
								    "@": {
										type			: "entity",
										wrap			: false,
										stop			: "",
										allowSpace		: true,
										finalize		: true,
										query:
										{
											loadingHint	: true,
											searchHint	: $.View("easysocial/site/friends/suggest.hint.search"),
											emptyHint	: $.View("easysocial/site/friends/suggest.hint.empty"),

											data: function( keyword )
											{

												var task = $.Deferred();

												EasySocial.ajax( "site/controllers/friends/suggest" , { search: keyword })
												.done(function(items)
												{
													if (!$.isArray(items)) task.reject();

													var items = $.map(items, function(item)
													{
														item.title	= item.screenName;
														item.type	= "user";

														item.menuHtml = self.view.suggestItem(true, {
															item: item,
															name: "uid[]"
														});

														return item;
													});

													task.resolve(items);
												})
												.fail(task.reject);

												return task;
											},
											use: function(item) {
												return item.type + ":" + item.id;
											}
									    }
									},
									"#":
									{
									    type		: "hashtag",
									    wrap		: true,
									    stop		: " #",
									    allowSpace	: false,
										query:
										{
											loadingHint	: false,
											searchHint	: $.View("easysocial/site/hashtags/suggest.hint.search"),
											emptyHint	: $.View("easysocial/site/hashtags/suggest.hint.empty"),
											data: function(keyword)
											{

												var task = $.Deferred();

												EasySocial.ajax("site/controllers/hashtags/suggest", {search: keyword})
													.done(function(items)
													{
														if (!$.isArray(items)) task.reject();

														var items = $.map(items, function(item){
															item.title = "#" + item.title;
															item.type = "hashtag";
															item.menuHtml = self.view.tagSuggestItem(true, {
																item: item,
																name: "uid[]"
															});
															return item;
														});

														task.resolve(items);
													})
													.fail(task.reject);

												return task;
											}
									    }
									}
								},
								plugin:
								{
									autocomplete:
									{
										id			: "fd",
										component	: "es",
										position	:
										{
											my: 'left top',
											at: 'left bottom',
											of: header,
											collision: 'none'
										},
										size:
										{
											width: function()
											{
												return header.width();
											}
										}
									}
								}
							});
					},

					initUploader: function() {
						// Implement uploader controller.
						self.uploader().implement( EasySocial.Controller.Uploader , {
							// We want the uploader to upload automatically.
							temporaryUpload	: true,
							query 			: "type=conversations",
							type 				: 'conversations',
							extensionsAllowed : self.options.extensionsAllowed
						});
					},

					initSuggest: function() {
						self.friendSuggest()
							.addController(EasySocial.Controller.Friends.Suggest,
								{
									friendList		: true,
									friendListName	: "list_id[]",
									showNonFriend : self.options.showNonFriend
								});
					},

					initEditor : function() {
						self.editor().expandingTextarea();
					},

					/**
					 * Check for errors on the conversation form.
					 */
					checkErrors: function()
					{
						if( self.recipients().length <= 0 )
						{
							self.recipientRow().addClass( 'error' );
							self.clearMessage();
							self.setMessage( $.language( 'COM_EASYSOCIAL_CONVERSATIONS_ERROR_EMPTY_RECIPIENTS' ) , 'error' );

							return true;
						}
						else
						{
							self.recipientRow().removeClass( 'error' );
						}

						if( self.editor().val() == '' )
						{
							self.messageRow().addClass( 'error' );
							self.clearMessage();
							self.setMessage( $.language( 'COM_EASYSOCIAL_CONVERSATIONS_ERROR_EMPTY_MESSAGE' ) , 'error' );

							return true;
						}
						else
						{
							self.messageRow().removeClass( 'error' );
						}

						return false;
					},

					/**
					 * Submit button.
					 */
					"{submit} click" : function( el , event )
					{
						// Prevent form submission since this is a submit button.
						event.preventDefault();

						// Check for errors on this page.
						if( self.checkErrors() )
						{
							return false;
						}

						if( self.options.attachments )
						{
							var uploaderController 	= self.uploader().controller();

							// Do not allow user to submit this when the items are still being uploaded.
							if( uploaderController.options.uploading && uploaderController.hasFiles() )
							{
								return false;
							}
						}

						var mentions = self.editorArea().mentions("controller").toArray();

						// Reconstruct the inputs
						$( mentions ).each(function( i , item )
						{
							$( '<input>' )
								.attr( 'type' , 'hidden')
								.attr( 'name' , 'tags[]' )
								.attr( 'value' , JSON.stringify( item ) )
								.appendTo( self.element );
						});

						// Submit the form when we're ready.
						self.element.submit();
					}
				}
			}
		);

		EasySocial.Controller('Conversations.Composer.Dialog', {
			defaultOptions: {
				recipient: {},
			}
		}, function(self) { return {

			"{self} click" : function() {
				EasySocial.dialog({
					"content"	: EasySocial.ajax( 'site/views/conversations/composer' , { "id" : self.options.recipient.id } ),
					"bindings"	: {
						"{sendButton} click" : function(el) {

							var dialog = this.parent;
							var recipient = $('[data-composer-recipient]').val();
							var message = $('[data-composer-message]').val();

							// disable the send button so that user cannot click again.
							el.disabled(true);

							EasySocial.ajax( 'site/controllers/conversations/store', {
								"uid"		: recipient,
								"message"	: message
							}).done(function(link) {

								EasySocial.dialog({
									"content": EasySocial.ajax( 'site/views/conversations/sent' , { "id" : self.options.recipient.id }),
									"bindings": {
										"{viewButton} click" : function() {
											document.location 	= link;
										}
									}
								});
							}).fail(function(message) {
								dialog.setMessage(message);
								el.disabled(false);
							});
						}
					}
				});
			}
		}});

		module.resolve();
	});

});

