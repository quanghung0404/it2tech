EasySocial.module('site/comments/frame', function($) {
	var module = this;

	EasySocial
		.require()
		.library('mentions')
		.script('site/comments/item', 'uploader/uploader')
		.view(
			"site/friends/suggest.item",
			"site/friends/suggest.hint.search",
			"site/friends/suggest.hint.empty",
			"site/hashtags/suggest.item",
			"site/hashtags/suggest.hint.search",
			"site/hashtags/suggest.hint.empty"
		)
		.language(
			'COM_EASYSOCIAL_COMMENTS_LOADED_OF_TOTAL',
			'COM_EASYSOCIAL_COMMENTS_STATUS_SAVING',
			'COM_EASYSOCIAL_COMMENTS_STATUS_SAVED'
		)
		.done(function() {

			EasySocial.Controller('Comments', {
				defaultOptions: {

					'group': 'user',
					'element': 'stream',
					'verb': 'null',
					'uid': 0,
					'enterkey': 'submit',
					'url': '',
					'streamid': '',

					'{actionContent}': '[data-action-contents-comments]',
					'{actionLink}': '[data-stream-action-comments]',
					'{stat}': '[data-comments-stat]',
					'{load}': '[data-comments-load]',
					'{list}': '[data-comments-list]',
					'{item}': '[data-comments-item]',
					'{form}': '[data-comments-form]'
				}
			}, function(self) { return {

				// List all the triggers here made to parent
				// newCommentSaving
				// newCommentSaved(comment)
				// newCommentSaveError(errormsg)
				// oldCommentsLoaded(comments)
				// oldCommentsLoadError(errormsg)
				// commentDeleted(id)

				// Item triggers
				// commentEditLoading(id)
				// commentEditLoaded(id, rawcomment)
				// commentEditLoadError(id, errormsg)
				// commentEditSaving(id, newcomment)
				// commentEditSaved(id, newcomment)
				// commentEditSaveError(id, errormsg)
				// commentDeleting(id)
				// commentDeleteError(id, errormsg)

				init: function() {
					// Initialise uid
					self.options.uid = self.element.data('uid') || self.options.uid;

					// Initialise element
					self.options.element = self.element.data('element') || self.options.element;

					// Initialise group
					self.options.group = self.element.data('group') || self.options.group;

					// Initialise verb
					self.options.verb = self.element.data('verb') || self.options.verb;

					// Initialise url
					self.options.url = self.element.data('url') || self.options.url;

					// Initialise streamid
					self.options.streamid = self.element.data('streamid') || self.options.streamid;

					self.$Stat = self.addPlugin('stat');
					self.$Load = self.addPlugin('load');
					self.$List = self.addPlugin('list');
					self.$Form = self.addPlugin('form');

					// Comment Control needs to be required once when there is a frame on the page
					EasySocial.require().script('site/comments/control').done(function() {

						// This block needs to be registered
						EasySocial.Comments.register(self);
					});

					// Trigger commentInit on self
					self.trigger('commentInit', [self]);
				},

				// Create a registry of items
				$Comments: {},

				registerComment: function(instance) {
					var id = instance.options.id;

					self.$Comments[id] = instance;
				},

				'{actionLink} click' : function(){
					self.actionContent().toggle();
				},

				_export: function() {
					var data = {
						total: self.$Stat.total(),
						count: self.$Stat.count(),
						ids: $._.keys(self.$Comments)
					};

					return data;
				},

				updateComment: function(comments) {
					var newComments = [];

					$.each(comments['ids'], function(commentid, state) {
						if(state !== true) {
							if(state === false) {

								// Trigger commentDeleted event on self (as parent)
								self.trigger('commentDeleted', [commentid]);

							} else {
								var appended = false;

								// Search for the next larger id as the node to insert before
								$.each(self.$Comments, function(id, comment) {
									if(id > commentid) {
										self.$List.addToList(state, id, false);

										appended = true;
										return false;
									}
								});

								// If no node found, then just append it to the list
								if(!appended) {
									self.$List.addToList(state, 'append', false);
								}

								// Add this comment into the list of new comments
								newComments.push(state);
							}
						}
					});

					// Update the new total count
					self.$Stat.total(comments['total']);

					// Trigger oldCommentsLoaded event
					self.trigger('oldCommentsLoaded', [newComments]);
				},

				'{self} show': function() {
					self.element.show();

					self.$Form.input().focus();
				}
			} });
			/**
			 *	List controller
			 */
			EasySocial.Controller('Comments.List', {
				defaultOptions: {
					'{list}': '[data-comments-list]',

					'{item}': '[data-comments-item]'
				}
			}, function(self) { return {
				init: function() {
					// Multiple instances of items
					self.initItemController(self.item(), false);
				},

				initItemController: function(item, isNew) {
					item.addController('EasySocial.Controller.Comments.Item', {
						controller: {
							parent: self.parent
						},

						isNew: isNew
					});

					return item;
				},

				'{parent} newCommentSaved': function(el, event, comment) {
					// Add the comment to the list
					self.addToList(comment);
				},

				addToList: function(comment, type, isNew) {
					// Set type to append by default
					type = type === undefined ? 'append' : type;

					// Set isNew to true by default
					isNew = isNew === undefined ? true : isNew;

					// Wrap comment in jQuery
					comment = $(comment);

					// Implement item controller on comment
					self.initItemController(comment, isNew);

					// Check if type is append/prepend
					if(type == 'append' || type == 'prepend') {

						// Prepare function values based on type (append/prepend)
						var filter = type == 'append' ? ':last' : ':first',
							action = type == 'append' ? 'after' : 'before';

						// Add the comment item into list
						if(self.item().length === 0) {
							// If no comments yet then add the html into the list
							self.list().html(comment);
						} else {
							// If there are existing comments, then append/prepend comment into the list
							self.item(filter)[action](comment);
						}
					} else {

						// If type is neither append or prepend, then type could be the comment id
						var item = self.parent.$Comments[type];

						// Check if type is a valid comment, if it is then by this means prepend on top
						if(item !== undefined) {
							item.element.before(comment);
						}
					}

					// Show the whole comment block because the block could be hidden
					self.parent.actionContent().show();
				},

				'{parent} commentDeleted': function(el, event, id) {
					// Remove this comment from comment registry
					if(self.parent.$Comments[id] !== undefined) {

						// Remove the element
						self.parent.$Comments[id].element.remove();

						// Remove the controller reference in the registry
						delete self.parent.$Comments[id];
					}
				}
			} });

			/**
			 *	Statistic controller
			 */
			EasySocial.Controller('Comments.Stat', {
				defaultOptions: {
					'{stats}'	: '[data-comments-stats]',

					count	: 0,
					total	: 0,

					limit	: 10
				}
			}, function(self) { return {
				init: function() {
					self.options.count = self.element.data('count');
					self.options.total = self.element.data('total');
				},

				// Get / set total comments
				total: function(count) {
					if(count !== undefined) {
						self.options.total = parseInt(count);
						self.stats().text($.language('COM_EASYSOCIAL_COMMENTS_LOADED_OF_TOTAL', self.count(), self.total()));
					}

					return self.options.total;
				},

				// Get / set current comments
				count: function(count) {
					if(count !== undefined) {
						self.options.count = parseInt(count);
						self.stats().text($.language('COM_EASYSOCIAL_COMMENTS_LOADED_OF_TOTAL', self.count(), self.total()));
					}

					return self.options.count;
				},

				getNextCycle: function() {
					var start = Math.max(self.total() - self.count() - self.options.limit, 0);

					var limit = self.total() - self.count() - start;

					return {
						start: start,
						limit: limit
					}
				},

				'{parent} oldCommentsLoaded': function(el, event, comments) {
					var count = comments.length;

					self.count(self.count() + count);
				},

				'{parent} newCommentSaved': function() {
					self.total(self.total() + 1);

					self.count(self.count() + 1);
				},

				'{parent} commentDeleted': function() {
					self.total(self.total() - 1);

					self.count(self.count() - 1);
				}
			} });

			EasySocial.Controller('Comments.Load', {
				defaultOptions: {
					'{load}'		: '[data-comments-load]',
					'{loadMore}'	: '[data-comments-load-loadMore]'
				}
			}, function(self) { return {
				init: function() {

				},

				'{loadMore} click': function(el, event) {
					if(el.enabled()) {

						// Disable the button
						el.disabled(true);

						// Get boundary details
						var cycle = self.parent.$Stat.getNextCycle();

						// If limit is 0, means no comment to load
						if(cycle.limit == 0) {
							return false;
						}

						// Send load comments command to the server
						self.loadComments(cycle.start, cycle.limit)
							.done(function(comments) {
								// Comments come in with chronological order array
								// Hence need to reverse comment and prepend from bottom

								// Create a copy of reverse comments to not affect the original array
								// Slice is to create a non reference copy of the array
								var reversedComments = comments.slice().reverse();

								$.each(reversedComments, function(index, comment) {
									self.parent.$List.addToList(comment, 'prepend', false);
								});

								// Trigger oldCommentsLoaded event
								self.parent.trigger('oldCommentsLoaded', [comments]);

								// Enable the button
								el.enabled(true);

								// If start is 0, means this is the last round of comments to load
								cycle.start == 0 && self.load().hide();
							})
							.fail(function(msg) {

								// Trigger oldCommentsLoadError event
								self.parent.trigger('oldCommentsLoadError', [msg]);
							});
					}
				},

				loadComments: function(start, limit) {
					limit = limit || 10;
					return EasySocial.ajax('site/controllers/comments/load', {
						uid: self.parent.options.uid,
						element: self.parent.options.element,
						group: self.parent.options.group,
						verb: self.parent.options.verb,
						start: start,
						length: limit
					});
				}
			} });

			/**
			 *	Form controller
			 */
			EasySocial.Controller('Comments.Form', {
				
				defaultOptions: {
					'{editorHeader}': '[data-comment-form-header]',
					'{editorArea}': '[data-comment-form-editor-area]',
					'{input}': '[data-comments-form-input]',
					'{submit}': '[data-comments-form-submit]',
					'{status}': '[data-comments-form-status]',
					
					// Smileys
					"{smileyLink}": "[data-comment-smileys]",
					"{smileyItem}": "[data-comment-smiley-item]",

					// Attachments
					"{attachmentQueue}": "[data-comment-attachment-queue]",
					"{attachmentProgress}": "[data-comment-attachment-progress-bar]",
					"{attachmentBackground}": "[data-comment-attachment-background]",
					"{attachmentRemove}": "[data-comment-attachment-remove]",
					"{attachmentItem}": "[data-comment-attachment-item]",

					"{attachmentDelete}": "[data-comment-attachment-delete]",

					"{uploaderForm}": "[data-uploader-form]",
					"{itemTemplate}": "[data-comment-attachment-template]",

					attachmentIds:[],

					view: {
						suggestItem: "site/friends/suggest.item",
						tagSuggestItem: "site/hashtags/suggest.item"
					}
				}
			}, function(self, opts, base, parent) { return {

				init: function() {

					// Assign the parent
					parent = self.parent;

					// Apply the mentions on the comment form
					self.setMentionsLayout();

					// Implement attachments on the comment form.
					if (parent.options.attachments) {
						self.implementAttachments();
					}

				},

				attachmentTemplate: null,

				getAttachmentTemplate: function() {

					if (!self.attachmentTemplate) {
						self.attachmentTemplate = self.itemTemplate().detach();
					}

					var tpl = $(self.attachmentTemplate).clone().html();

					return $(tpl);
				},
				
				implementAttachments: function() {

					// Implement uploader controller
					self.editorArea().implement(EasySocial.Controller.Uploader, {
						'temporaryUpload': true,
						'query': 'type=comments',
						'type': 'comments',
						extensionsAllowed: 'jpg,png,gif'
					});

				},

				"{smileyItem} click": function(smileyItem, event) {

					var value = smileyItem.data('comment-smiley-value');
					var isEditing = smileyItem.parents('[data-comments-item-editframe]').length > 0;
					var currentInput = self.input();

					if (isEditing) {
						currentInput = smileyItem.parents('[data-comments-item-editframe]').find('[data-comments-item-edit-input]');
					}
						
					var currentValue = currentInput.val();
					currentValue += " " + value;

					// Update the comment form with the smiley
					currentInput.val(currentValue);
				},

				"{smileyLink} click": function(smileyLink, event) {

					if (smileyLink.hasClass('active')) {
						smileyLink.removeClass('active');

						return;
					}

					smileyLink.addClass('active');
				},

				"{attachmentDelete} click": function(deleteLink, event) {

					var attachmentId = deleteLink.data('id');
					
					EasySocial.dialog({
						content: EasySocial.ajax('site/views/comments/confirmDeleteCommentAttachment', {
										"id": attachmentId
									}),
						bindings: {
							"{deleteButton} click": function() {

								// Perform an ajax call to the server
								EasySocial.ajax('site/controllers/comments/deleteAttachment', {
									"id": attachmentId
								})
								.done(function() {
									// Remove the dom from the page
									var item = deleteLink.parents(self.attachmentItem.selector);
									item.remove();

									EasySocial.dialog().close();
								});
							}
						}
					});

				},

				"{attachmentRemove} click": function(removeLink, event) {
					var item = removeLink.parents(self.attachmentItem.selector);

					// Remove the item from the attachment ids
					opts.attachmentIds = $.without(opts.attachmentIds, item.data('id'));

					// Remove the item
					item.remove();

					if (self.attachmentItem().length < 1) {
						self.attachmentQueue().removeClass('has-attachments');
					}
				},

				// When a new item is added, we want to display
				"{uploaderForm} FilesAdded": function(el, event, uploader, files) {

					$.each(files, function(index, file) {
						// Get the attachment template
						var item = self.getAttachmentTemplate();

						// Set the queue to use has-attachments class
						self.attachmentQueue()
							.addClass('has-attachments');

						// Insert the item into the queue
						item.attr('id', file.id)
							.addClass('is-uploading')
							.prependTo(self.attachmentQueue());
					});
				},

				// When the file is uploaded, we need to remove the uploading state
				"{uploaderForm} FileUploaded": function(el, event, uploader, file, response) {

					var item = $('#' + file.id);

					// Add preview
					self.attachmentBackground.inside(item)
						.css('background-image', 'url(' + response.preview + ')');

					// Remove the is-uploading state on the upload item
					item.removeClass('is-uploading');

					// Push the id
					item.data('id', response.id);

					opts.attachmentIds.push(response.id);
				},

				// When item is being uploaded
				"{uploaderForm} UploadProgress" : function(el, event, uploader, file) {

					var item = $('#' + file.id);
					var progress = self.attachmentProgress.inside(item);

					progress.css('width', file.percent + '%');
				},

				'{input} keypress': function(el, event) {

					if (event.keyCode == 13) {
						if(self.parent.options.enterkey === 'submit' && !(event.shiftKey || event.altKey || event.ctrlKey || event.metaKey)) {
							self.submitComment();
						}

						if(self.parent.options.enterkey === 'newline' && (event.shiftKey || event.altKey || event.ctrlKey || event.metaKey)) {
							self.submitComment();
						}
					}
				},

				'{submit} click': function(el, event) {
					if (el.enabled()) {
						self.submitComment();
					}
				},

				setMentionsLayout: function() {
					var loader = $.Deferred();

					var editor		= self.editorArea(),
						mentions	= editor.controller("mentions");


					if (mentions) {
						mentions.cloneLayout();
						return;
					}

					var header = self.editorHeader();

					editor
						.mentions({
							triggers: {
							    
							    "@": {
									type: "entity",
									wrap: false,
									stop: "",
									allowSpace: true,
									finalize: true,
									query: {
										loadingHint	: true,
										searchHint	: $.View("easysocial/site/friends/suggest.hint.search"),
										emptyHint	: $.View("easysocial/site/friends/suggest.hint.empty"),

										data: function(keyword) {

											var task = $.Deferred();

											EasySocial.ajax( "site/controllers/friends/suggest" , { search: keyword })
											.done(function(items) {
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
								"#": {
								    type: "hashtag",
								    wrap: true,
								    stop: " #",
								    allowSpace: false,
									query: {
										loadingHint: true,
										searchHint: $.View("easysocial/site/hashtags/suggest.hint.search"),
										emptyHint: $.View("easysocial/site/hashtags/suggest.hint.empty"),
										data: function(keyword) {

											var task = $.Deferred();

											EasySocial.ajax("site/controllers/hashtags/suggest", {search: keyword})
												.done(function(items) {

													if (!$.isArray(items)) {
														task.reject();
													}

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
							plugin: {
								autocomplete: {
									id: "fd",
									component: "es",
									position: {
										my: 'left top',
										at: 'left bottom',
										of: header,
										collision: 'none'
									},
									size: {
										width: function() {
											return header.width();
										}
									}
								}
							}
						});
				},

				submitComment: function() {
					var comment = self.input().val();

					// If comment value is empty, then don't proceed
					if ($.trim(comment) == '') {
						return false;
					}

					// Trigger newCommentSaving event
					self.parent.trigger('newCommentSaving');

					// Execute save
					self.save()
						.done(function(comment) {
							// Rather than using commentItem ejs, let PHP return a full block of HTML codes
							// This is to unify 1 single theme file to use loading via static or ajax

							// trigger parent's commentSaved event
							self.parent.trigger('newCommentSaved', [comment]);

							// Enable the submit button
							self.submit().enabled(true);

							var editor = self.editorArea();
							var mentions = editor.controller("mentions");

							// Reset the mentions upon saving.
							mentions && mentions.reset();

							// Update the stream exclude id if applicable
							if (self.parent.options.streamid != '') {
								self.updateStreamExcludeIds(self.parent.options.streamid);
							}

						}).fail(function(msg) {
							self.parent.trigger('newCommentSaveError', [msg.message]);
						});
				},

				save: function() {
					var mentions = self.editorArea().controller("mentions");

					var data = {
						url: self.parent.options.url,
						mentions: mentions ? mentions.toArray() : []
					};

					data.mentions = $.map(data.mentions, function(mention){

						if (mention.type==="hashtag" && $.isPlainObject(mention.value)) {
							mention.value = mention.value.title.slice(1);
						}
						return JSON.stringify(mention);
					});

					return EasySocial.ajax('site/controllers/comments/save', {
						uid: self.parent.options.uid,
						element: self.parent.options.element,
						group: self.parent.options.group,
						verb: self.parent.options.verb,
						streamid: self.parent.options.streamid,
						input: self.input().val(),
						attachmentIds: opts.attachmentIds,
						data: data
					});
				},

				updateStreamExcludeIds: function(id) {
					// ids = self.element.data('excludeids' );
					ids = $('[data-streams-wrapper]').data( 'excludeids' );

					newIds = '';

					if (ids != '' && ids != undefined) {
						newIds = ids + ',' + id;
					} else {
						newIds = id;
					}

					$('[data-streams-wrapper]').data('excludeids', newIds);
				},

				disableForm: function() {
					// Disable input
					self.input().attr('disabled', true);

					// Disable submit button
					self.submit().disabled(true);
				},

				enableForm: function() {
					// Enable and reset input
					self.input().removeAttr('disabled');

					// Enable submit button
					self.submit().enabled(true);
				},

				'{parent} newCommentSaving': function() {
					// Show the status as it could be hidden by other actions
					self.status().show();

					// Set the status as success
					self.status().removeClass('label-important label-success label-info');
					self.status().addClass('label-info');

					// Set the status
					self.status().text($.language('COM_EASYSOCIAL_COMMENTS_STATUS_SAVING'));

					// Disable comment form
					self.disableForm();
				},

				'{parent} newCommentSaved': function() {
					// Show the status bar of the form
					self.status().show();

					// Set the text of the status bar
					self.status().text($.language('COM_EASYSOCIAL_COMMENTS_STATUS_SAVED'));

					// Set the status as success
					self.status().removeClass('label-important label-success label-info');
					self.status().addClass('label-success');

					// Fade out the status bar after 2 second
					setTimeout(function() {
						self.status().fadeOut('fast');
					}, 2000);

					// Enable comment form
					self.enableForm();

					// Reset the attachments
					opts.attachmentIds = [];

					// Get all the attachment items in the queue
					var attachmentItems = self.attachmentItem.inside(self.attachmentQueue.selector);
					attachmentItems.remove();
					
					self.attachmentQueue().removeClass('has-attachments');

					// Reset comment input
					self.input().val('');
				},

				'{parent} newCommentSaveError': function(el, event, msg) {
					// Show the status bar of the form
					self.status().show();

					// Set the status as error
					self.status().removeClass('label-important label-success label-info');
					self.status().addClass('label-important');

					// Add the error message
					self.status().text(msg);

					// Enable comment form
					self.enableForm();
				}
			} });

			module.resolve();
		});
})
