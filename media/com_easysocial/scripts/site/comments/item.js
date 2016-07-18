EasySocial.module('site/comments/item', function($) {
	var module = this;

	EasySocial.require()
		.library('mentions')
		.view(
			"site/friends/suggest.item",
			"site/friends/suggest.hint.search",
			"site/friends/suggest.hint.empty",
			"site/hashtags/suggest.item",
			"site/hashtags/suggest.hint.search",
			"site/hashtags/suggest.hint.empty"
		)
		.language(
			'COM_EASYSOCIAL_COMMENTS_STATUS_SAVE_ERROR',
			'COM_EASYSOCIAL_COMMENTS_STATUS_LOADING',
			'COM_EASYSOCIAL_COMMENTS_STATUS_LOAD_ERROR',
			'COM_EASYSOCIAL_COMMENTS_STATUS_DELETING',
			'COM_EASYSOCIAL_COMMENTS_STATUS_DELETE_ERROR',
			'COM_EASYSOCIAL_LIKES_LIKE',
			'COM_EASYSOCIAL_LIKES_UNLIKE'
		)
		.done(function() {
			/**
			 *	Item controller
			 */
			EasySocial.Controller('Comments.Item', {
				defaultOptions: {
					'id'			: 0,

					'child'			: 0,

					'loadedChild'	: 0,

					'limit'			: 10,

					'isNew'			: false,

					'{frame}'		: '[data-comments-item-frame]',

					'{avatar}'		: '[data-comments-item-avatar]',

					'{commentFrame}': '[data-comments-item-commentFrame]',

					'{author}'		: '[data-comments-item-author]',

					'{action}'		: '[data-comments-item-actions]',
					'{edit}'		: '[data-comments-item-actions-edit]',
					'{delete}'		: '[data-comments-item-actions-delete]',
					'{spam}'		: '[data-comments-item-actions-spam]',

					'{comment}'		: '[data-comments-item-comment]',

					'{meta}'		: '[data-comments-item-meta]',

					'{date}'		: '[data-comments-item-date] a',

					'{like}'		: '[data-comments-item-like]',
					'{likeCount}'	: '[data-comments-item-likeCount]',

					'{editFrame}'	: '[data-comments-item-editFrame]',
					'{editInput}'	: '[data-comments-item-edit-input]',
					'{editCancel}'	: '[data-comments-item-edit-cancel]',
					'{editSubmit}'	: '[data-comments-item-edit-submit]',
					'{editStatus}'	: '[data-comments-item-edit-status]',

					'{statusFrame}'	: '[data-comments-item-statusFrame]',

					'{loadReplies}'	: '[data-comments-item-loadReplies]',

					'{readMore}'	: '[data-es-comment-readmore]',

					'{fullContent}'	: '[data-es-comment-full]',

					view: {
						suggestItem: "site/friends/suggest.item",
						tagSuggestItem: "site/hashtags/suggest.item"
					}
				}
			}, function(self) { return {
				init: function() {
					// Initialise comment id
					self.options.id = self.element.data('id');

					// Initialise child count
					self.options.child = self.element.data('child');

					// Register self into the registry of comments
					self.parent.registerComment(self);

					// Add the status plugin
					// self.status = self.addPlugin('status');

					// Using add Controller instead of addPlugin because the parent should reference the item's parent, not the item itself
					self.status = self.element.addController('EasySocial.Controller.Comments.Item.Status', {
						controller: {
							parent: self.parent,
							item: self
						}
					})
				},

				'{like} click': function(el) {
					if(el.enabled()) {
						// Disable the like button
						el.disabled(true);

						// Send the like to the server
						self.likeComment()
							.done(function(liked, count, string) {

								// Enable the button
								el.enabled(true);

								// Set the likes count
								self.likeCount().text(count);

								// Strip off tags from the like text
								string = $('<div></div>').html(string).text();

								// Set the like text
								self.likeCount().attr('data-original-title', string);

								// Set the like button text
								self.like().find('a').text($.language(liked ? 'COM_EASYSOCIAL_LIKES_UNLIKE' : 'COM_EASYSOCIAL_LIKES_LIKE'));
							})
							.fail(function() {

							});
					}
				},

				likeComment: function() {
					return EasySocial.ajax('site/controllers/comments/like', {
						id: self.options.id
					});
				},

				'{likeCount} click': function() {
					EasySocial.dialog({
						content: self.getLikedUsers()
					});
				},

				getLikedUsers: function() {
					return EasySocial.ajax('site/controllers/comments/likedUsers', {
						id: self.options.id
					});
				},

				'{edit} click': function(el) {
					if(el.enabled()) {

						var editor = self.editFrame(),
							mentions = editor.controller("mentions");

						// Manually clear out the html and destroy the mentions controller to prevent conflict of loading the editFrame again.
						editor.html('');
						if (mentions) {
							mentions.destroy();
						}

						// Disable the edit button
						el.disabled(true);

						// Trigger commentEditLoading event
						self.trigger('commentEditLoading', [self.options.id]);

						self.getEditComment()
							.done(function(html) {
								self.trigger('commentEditLoaded', [self.options.id, html]);

								self.editFrame().html(html).show();

								self.setMentionsLayout();

								// Focus on the editor
								self.editInput().focus();
							})
							.fail(function(msg) {

								// Trigger commentEditLoadError event
								self.trigger('commentEditLoadError', [self.options.id, msg]);
							});
					}
				},

				setMentionsLayout: function() {
					var editor = self.editFrame();
					var mentions = editor.controller("mentions");


					if (mentions) {
						mentions.cloneLayout();
						return;
					}

					var header = self.editFrame();

					editor
						.mentions(
						{
							triggers:
							{
								"@":
								{
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

				getRawComment: function() {
					return EasySocial.ajax('site/controllers/comments/getRawComment', {
						id: self.options.id
					});
				},

				getEditComment: function() {
					return EasySocial.ajax('site/controllers/comments/getEditComment', {
						id: self.options.id
					});
				},

				'{editCancel} click': function() {
					self.trigger('commentEditCancel', [self.options.id]);

					self.edit().enabled(true);
				},

				'{editSubmit} click': function() {
					self.submitEdit();
				},

				submitEdit: function() {
					// Get and trim the edit value
					var input = self.editInput().val();

					// Do not proceed if value is empty
					if(input == '') {
						return false;
					}

					// Trigger commentEditSaving event
					self.trigger('commentEditSaving', [self.options.id, input]);

					// Send the edit to the server
					self.saveEdit()
						.done(function(comment) {

							// Trigger commentEdited event
							self.trigger('commentEditSaved', [self.options.id, comment]);

							// Update the comment content
							self.comment().html(comment);

							self.edit().enabled(true);
						})
						.fail(function(msg) {

							// Trigger commentEditError event
							self.trigger('commentEditSaveError', [self.options.id, msg]);
						});
				},

				saveEdit: function() {
					var mentions = self.editFrame().mentions("controller");

					return EasySocial.ajax('site/controllers/comments/update', {
						id: self.options.id,
						input: self.editInput().val(),
						mentions: mentions ? mentions.toArray() : []
					});
				},

				'{delete} click': function(el) {
					// Prepare the item properly first
					self.frame().hide();
					self.commentFrame().show();

					// Clone the whole item to place in the dialog
					// var comment = self.element.clone();

					EasySocial.dialog({
						content: EasySocial.ajax('site/views/comments/confirmDelete', {
							id: self.options.id
						}),
						selectors: {
							"{deleteButton}"  : "[data-delete-button]",
							"{cancelButton}"  : "[data-cancel-button]"
						},
						bindings: {
							"{deleteButton} click": function() {

								// Close the dialog
								EasySocial.dialog().close();

								// Trigger commentDeleting event on parent to announce to sibling frames
								self.parent.trigger('commentDeleting', [self.options.id]);

								// Trigger commentDeleting event on self to announce to child frames
								self.trigger('commentDeleting');

								// Send delete command to server
								self.deleteComment()
									.done(function() {

										// Trigger commentDeleted event on parent, since this element will be remove, no point triggering on self
										self.parent.trigger('commentDeleted', [self.options.id]);
									})
									.fail(function(msg) {

										// Trigger commentDeleteError event on parent to announce to sibling frames
										self.parent.trigger('commentDeleteError', [self.options.id, msg]);

										// Trigger commentDeleteError event on self to announce to child frames
										self.trigger('commentDeleteError', [self.options.id, msg]);
									});
							},

							"{cancelButton} click": function() {

								// Close the dialog
								EasySocial.dialog().close();
							}
						}
					});
				},

				deleteComment: function() {
					return EasySocial.ajax('site/controllers/comments/delete', {
						id: self.options.id
					});
				},

				'{loadReplies} click': function(el) {
					// Hide the loadReplies button
					el.hide();

					// Add a loader after this comment first

					// Calculate the start
					var start = Math.max(self.options.child - self.options.loadedChild - self.options.limit, 0);

					// Get the child comments
					EasySocial.ajax()
						.done(function(comments) {

							// Append the comments below the current comment item
							$.each(comments, function(index, comment) {
								self.parent.$List.addToList(comment, 'child', false);
							});

							// Trigger oldCommentsLoaded event
							self.parent.trigger('oldCommentsLoaded', [comments]);

							// Check if we need to show the load more replies button in the current item
							start > 0 && self.loadMoreReplies().show();
						});
				},

				'{readMore} click': function(el, ev) {
					self.comment().html(self.fullContent().html());
				}
			} });

			/**
			 *	Status frame controller
			 */
			EasySocial.Controller('Comments.Item.Status', {
				defaultOptions: {
					'{frame}'		: '[data-comments-item-frame]',

					'{statusFrame}'	: '[data-comments-item-statusFrame]',

					'{statusContent}': '[data-comments-item-statusFrame] div',

					'{commentFrame}': '[data-comments-item-commentFrame]',

					'{editFrame}'	: '[data-comments-item-editFrame]'
				}
			}, function(self) { return {

				// commentEditLoading(id)
				// commentEditLoaded(id, rawcomment)
				// commentEditLoadError(id, errormsg)
				// commentEditCancel(id)
				// commentEditSaving(id, newcomment)
				// commentEditSaved(id, newcomment)
				// commentEditSaveError(id, errormsg)
				// commentDeleting(id)
				// commentDeleted(id)
				// commentDeleteError(id, errormsg)

				init: function() {

				},

				setStatus: function(html) {
					self.frame().hide();

					if ($.isPlainObject(html) && html.message !== undefined) {
						html = html.message;
					}

					self.statusContent().html(html);

					self.statusFrame().show();
				},

				'{self} commentEditLoading': function() {
					self.setStatus($.language('COM_EASYSOCIAL_COMMENTS_STATUS_LOADING'));
				},

				'{self} commentEditLoaded': function() {
					self.frame().hide();

					self.editFrame().show();
				},

				'{self} commentEditLoadError': function() {
					self.setStatus($.language('COM_EASYSOCIAL_COMMENTS_STATUS_LOAD_ERROR'));
				},

				'{self} commentEditCancel': function() {
					self.frame().hide();

					self.commentFrame().show();
				},

				'{self} commentEditSaving': function() {
					self.setStatus($.language('COM_EASYSOCIAL_COMMENTS_STATUS_SAVING'));
				},

				'{self} commentEditSaved': function() {
					self.frame().hide();

					self.commentFrame().show();
				},

				'{self} commentEditSaveError': function() {
					self.setStatus($.language('COM_EASYSOCIAL_COMMENTS_STATUS_SAVE_ERROR'));
				},

				'{self} commentDeleting': function() {
					self.setStatus($.language('COM_EASYSOCIAL_COMMENTS_STATUS_DELETING'));
				},

				'{self} commentDeleteError': function(el, event, id, msg) {
					msg = msg || $.language('COM_EASYSOCIAL_COMMENTS_STATUS_DELETE_ERROR');
					self.setStatus(msg);
				}
			} });

			module.resolve();
		});
});
