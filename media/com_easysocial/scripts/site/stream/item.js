EasySocial.module('site/stream/item', function() {

	var module	= this;

	EasySocial.require()
	.library("mentions", "placeholder", "dialog")
	.view(
		"site/friends/suggest.item",
		"site/friends/suggest.hint.search",
		"site/friends/suggest.hint.empty",
		"site/hashtags/suggest.item",
		"site/hashtags/suggest.hint.search",
		"site/hashtags/suggest.hint.empty"
	)
	.done(function($){

		EasySocial.Controller('Stream.Item', {
			defaultOptions: {
				view: {
					suggestItem: "site/friends/suggest.item",
					tagSuggestItem: "site/hashtags/suggest.item"
				},

				// Properties
				id: "",
				context: "",

				// Elements
				"{deleteFeed}"	: "[data-stream-delete]",
				"{editStream}"	: "[data-stream-edit]",
				"{updateStream}"	: "[data-stream-edit-update]",
				"{cancelEditStream}" : "[data-stream-edit-cancel]",

				"{editPoll}"	: "[data-stream-polls-edit]",
				"{cancelEditPoll}"	: "[data-stream-polls-edit-cancel]",
				"{updatePoll}"	: "[data-stream-polls-edit-update]",


				"{publishItem}": "[data-stream-publish]",
				"{addBookmark}": "[data-stream-bookmark-add]",
				"{removeBookmark}": "[data-stream-bookmark-remove]",

				"{addSticky}": "[data-stream-sticky-add]",
				"{removeSticky}": "[data-stream-sticky-remove]",

				"{hideLink}"	: "[data-stream-hide]",
				"{unHideLink}"	: "[data-stream-show]",

				"{hideAppLink}"	: "[data-stream-hide-app]",
				"{unHideAppLink}"	: "[data-stream-show-app]",

				"{hideActorLink}" 	: "[data-stream-hide-actor]",
				"{unHideActorLink}" : "[data-stream-show-actor]",

				"{hideNotice}"	: "[data-stream-hide-notice]",

				"{actions}"		: "[data-streamItem-actions]",
				"{contents}"	: "[data-streamItem-contents]",


				"{streamContent}"	: "[data-stream-content]",
				"{streamEditor}"	: "[data-stream-editor]",

				"{streamData}"		: "[data-stream-item]",

				"{likes}"			: "[data-likes-action]",
				"{counterBar}"		: "[data-stream-counter]",
				"{likeContent}" 	: "[data-likes-content]",
				"{repostContent}" 	: "[data-repost-content]",

				"{share}"			: "[data-repost-action]",

				// Translations
				"{translateLink}": "[data-stream-translate]",
				"{translateLoader}": "[data-stream-translate-loader]",

				// for stream comment
				"{streamCommentLink}" 	: "[data-stream-action-comments]",
				"{streamCommentBlock}" 	: "[data-comments]"
			}
		}, function(self, opts, base) {

			return {

				init: function() {
					// Set the stream's unique id.
					opts.id = base.data('id');
					opts.context = base.data('context');
					opts.ishidden = base.data('ishidden');
					opts.actor = base.data('actor');
				},

				plugins: {},

				"{translateLink} click": function(translateLink, event) {

					// Get the stream content
					var contents = self.streamContent().html();

					// Add a loading indicator on the translation link
					self.element.addClass('is-translating');

					EasySocial.ajax('site/controllers/stream/translate', {
						"contents": contents
					})
					.done(function(translated) {						
						self.streamContent().html(translated);
					})
					.always(function() {
						self.element.removeClass('is-translating');
						self.element.addClass('is-translated');
					});
				},

				"{addBookmark} click": function(el, event) {
					// Add the bookmark class
					self.element.addClass('is-bookmarked');

					EasySocial.ajax('site/controllers/stream/bookmark', {
						"id" : self.options.id
					})
					.done(function() {
						// Do nothing once the item is already bookmarked
					})
					.fail(function(message) {
						// If this is failed, we need to display the message object
						self.element.removeClass('is-bookmarked');

						self.setMessage(message);
					});
				},

				"{removeBookmark} click": function(el, event) {
					var filterType = window.streamFilter || false;

					// Remove the bookmarked class
					if (filterType != 'bookmarks') {
						self.element.removeClass('is-bookmarked');
					}

					EasySocial.ajax('site/controllers/stream/removeBookmark', {
						"id": self.options.id
					}).done(function(html) {
						if (filterType == 'bookmarks') {
							self.element.html(html);
						}
					});
				},


				"{addSticky} click": function(el, event) {
					EasySocial.ajax('site/controllers/stream/addSticky', {
						"id" : self.options.id
					})
					.done(function() {
						// add sticky icon
						self.element.addClass('is-sticky');
					})
					.fail(function(obj) {

						// If this is failed, we need to display the message object
						EasySocial.dialog({
							content: obj.message
						});

					});
				},

				"{removeSticky} click": function(el, event) {
					var filterType = window.streamFilter || false;

					if (filterType != 'sticky') {
						// Remove the bookmarked class
						self.element.removeClass('is-sticky');
					}

					EasySocial.ajax('site/controllers/stream/removeSticky', {
						"id": self.options.id
					})
					.done(function(html){

						if (filterType == 'sticky') {
							self.element.html(html);
						}
					});
				},

				"{publishItem} click": function(el, event) {
					var id = opts.id;

					EasySocial.ajax('site/controllers/stream/publish', {
						"id": id
					}).done(function() {
						// When the stream is published, we want to hide the item
						base.switchClass('is-published');
					});
				},

				"{likes} onLiked": function(el, event, data) {

					//need to make the data-stream-counter visible
					self.counterBar().removeClass('hide');

				},

				"{likes} onUnliked": function(el, event, data) {

					var isLikeHide 		= self.likeContent().hasClass('hide');
					var isRepostHide 	= self.repostContent().hasClass('hide');

					if( isLikeHide && isRepostHide )
					{
						self.counterBar().addClass( 'hide' );
					}
				},

				"{share} create": function(el, event, itemHTML) {

					//need to make the data-stream-counter visible
					self.counterBar().removeClass( 'hide' );

				},


				"{streamCommentLink} click" : function()
				{
					// self.streamCommentBlock().toggle();
					self.streamCommentBlock().trigger('show');
				},

				/**
				 * Executes when a stream action is clicked.
				 */
				"{actions} click" : function( el , event )
				{
					// Remove active class on all action links
					self.actions().removeClass( 'active' );

					// Add active class on itself.
					$( el ).addClass( 'active' );
				},

				/**
				 * Delete a stream item
				 */
				 "{deleteFeed} click" : function() {
					var uid = self.options.id;

					EasySocial.dialog({
						content: EasySocial.ajax('site/views/stream/confirmDelete'),
						bindings: {

							"{deleteButton} click" : function() {
								EasySocial.ajax( 'site/controllers/stream/delete',
								{
									"id"		: uid,
								})
								.done(function( html )
								{

									EasySocial.dialog({
										content: html
									});

									self.element.fadeOut();

									// Close dialog box after 2 seconds
									setTimeout(function() {
										EasySocial.dialog().close();
									}, 2000);
								})
								.fail(function( message ){

									EasySocial.dialog({
										content: message
									});


								});

							}
						}
					});

				 },

				"{editPoll} click" : function()
				{
					var uid = self.options.id,
						element = 'stream';

					EasySocial.ajax('site/views/polls/edit',
					{
						"uid": uid,
						"element": element,
						"source": 'stream'
					})
					.done(function(html)
					{
						// Add editing state
						self.element.addClass('is-editing');

						self.streamContent().hide();

						self.streamEditor().html(html);

					});
				},

				"{cancelEditPoll} click" : function()
				{
					self.element.removeClass('is-editing');

					// Remove the contents
					self.streamEditor().html('');

					// Show the contents
					self.streamContent().show();
				},

				"{updatePoll} click": function()
				{
					var pollController = self.element.find('[data-polls-edit]').controller('EasySocial.Controller.Polls');

					var valid = pollController.validateForm();

                    if (! valid) {
                        return task.reject('Error validating polls inputs. Please make sure all the required fields are filled in.');
                    }

					var data = pollController.toData();

					EasySocial.ajax('site/controllers/polls/update', data)
					.done(function(html, id)
					{
						self.streamContent().html(html);

						self.cancelEditPoll().click();
					});
				},


				"{cancelEditStream} click" : function()
				{
					self.element.removeClass('is-editing');

					// Remove the contents
					self.streamEditor().html('');

					// Show the contents
					self.streamContent().show();
				},

				"{editStream} click" : function()
				{
					var id = self.options.id;

					EasySocial.ajax('site/views/stream/edit',
					{
						"id"	: id
					})
					.done(function(html)
					{
						// Add editing state
						self.element.addClass('is-editing');

						self.streamContent().hide();

						self.streamEditor().html(html);

						var textbox = self.streamEditor().find('[data-story-textbox]');
						var mentions = textbox.controller("mentions");

						if (mentions) {
							mentions.cloneLayout();
							return;
						}

						textbox
							.mentions({
								triggers: {
								    "@": {
										type: "entity",
										wrap: false,
										stop: "",
										allowSpace: true,
										finalize: true,
										query: {
											loadingHint: true,
											searchHint: $.View("easysocial/site/friends/suggest.hint.search"),
											emptyHint: $.View("easysocial/site/friends/suggest.hint.empty"),
											data: function(keyword) {

												var task = $.Deferred();

												EasySocial.ajax("site/controllers/friends/suggest", {search: keyword})
													.done(function(items){

														if (!$.isArray(items)) task.reject();

														var items = $.map(items, function(item){
															item.title = item.screenName;
															item.type = "user";
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
													.done(function(items){

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
								plugin: {
									autocomplete: {
										id: "fd",
										component: "es",
										modifier: "es-story-mentions-autocomplete",
										sticky: true,
										shadow: true,
										position: {
											my: 'left top',
											at: 'left bottom',
											of: self.streamEditor().find('.es-story-text'),
											collision: 'none'
										}
									}
								}
							});
					});
				},

				"{updateStream} click" : function()
				{
					var textbox	 	= self.streamEditor().find('[data-story-textbox]'),
						textField	= self.streamEditor().find('[data-story-textfield]'),
						mentions 	= textbox.mentions("controller").toArray(),
						hashtags 	= self.element.data("storyHashtags"),
						hashtags 	= (hashtags) ? hashtags.split(",") : [],
						nohashtags	= false,
						data 		= {};

					if (hashtags.length > 0) {
						var tags =
							$.map(mentions, function(mention)
							{
								if (mention.type==="hashtag" && $.inArray(mention.value, hashtags) > -1)
								{
									return mention;
								}
							});

						nohashtags = tags.length < 1;
					}

					data.mentions = $.map(mentions, function(mention){
						if (mention.type==="hashtag" && $.isPlainObject(mention.value)) {
							mention.value = mention.value.title.slice(1);
						}
						return JSON.stringify(mention);
					});

					data.content 	= textField.val();
					data.id 		= self.options.id;

					EasySocial.ajax('site/controllers/story/update', data)
					.done(function(html, id)
					{
						self.streamContent().html(html);

						self.cancelEditStream().click();
					});

				},

				/**
				 * Hide's a stream item.
				 */
				"{hideLink} click" : function()
				{
					// Add hide class
					self.streamData().addClass( 'es-feed-loading' );

					EasySocial.ajax( 'site/controllers/stream/hide',
					{
						"id"		: self.options.id
					})
					.done(function( html )
					{
						self.streamData().removeClass( 'es-feed-loading' );

						self.streamData().hide();
						self.element.append( html );
					})
					.fail(function( message ){

					});
				},

				"{hideActorLink} click": function()
				{
					EasySocial.ajax( 'site/controllers/stream/hideactor',
					{
						"actor"		: self.options.actor
					})
					.done(function( html )
					{
						// hide itself.
						self.streamData().hide();

						// hide all feeds that belong to this actor.
						$( '.stream-actor-' + self.options.actor ).addClass('hide-stream');

						self.element.append( html );

					})
					.fail(function( message ){
						console.log( message );
					});
				},

				/**
				 * unHide's a stream item.
				 */
				"{unHideActorLink} click" : function()
				{

					EasySocial.ajax( 'site/controllers/stream/unhideactor',
					{
						"actor"		: self.options.actor
					})
					.done(function()
					{
						self.hideNotice().remove();

						//show itself.
						self.streamData().show();

						// show all the items with same context
						$( '.stream-actor-' + self.options.actor ).removeClass('hide-stream');

					})
					.fail(function( message ){
						console.log( message );
					});
				},

				/**
				 * Hide's a stream item.
				 */
				"{hideAppLink} click" : function()
				{
					// self.actions().trigger( "onHideStream" , self.options.id );
					EasySocial.ajax( 'site/controllers/stream/hideapp',
					{
						"context"		: self.options.context
					})
					.done(function( html )
					{
						// self.streamData().hide();
						// self.element.append( self.view.hiddenItem() );

						// hide itself.
						self.streamData().hide();

						// hide all feeds that belong to this context.
						$( '.stream-context-' + self.options.context ).addClass('hide-stream');

						self.element.append( html );

					})
					.fail(function( message ){
						console.log( message );
					});
				},

				/**
				 * unHide's a stream item.
				 */
				"{unHideLink} click" : function()
				{

					EasySocial.ajax( 'site/controllers/stream/unhide',
					{
						"id"		: self.options.id
					})
					.done(function()
					{
						self.hideNotice().remove();
						self.streamData().show();

					})
					.fail(function( message ){
						console.log( message );
					});
				},

				/**
				 * unHide's a stream item.
				 */
				"{unHideAppLink} click" : function()
				{

					EasySocial.ajax( 'site/controllers/stream/unhideapp',
					{
						"context"		: self.options.context
					})
					.done(function()
					{
						self.hideNotice().remove();

						//show itself.
						self.streamData().show();

						// show all the items with same context
						$( '.stream-context-' + self.options.context ).removeClass('hide-stream');

					})
					.fail(function( message ){
						console.log( message );
					});
				}

			}
		});

		module.resolve();
	});
});
