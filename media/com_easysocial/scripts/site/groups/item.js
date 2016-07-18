EasySocial.module( 'site/groups/item' , function($)
{
	var module	= this;

	EasySocial.template('info/item', '<li data-es-group-filter><a class="ml-20" href="[%= url %]" title="[%= title %]" data-info-item data-info-index="[%= index %]"><i class="fa fa-info-circle mr-5"></i> [%= title %]</a></li>');

	EasySocial.require()
	.script('site/friends/suggest')
	.library('history')
	.done(function($) {
		EasySocial.Controller('Groups.Item', {
			defaultOptions: {

				"{content}": "[data-es-group-item-content]",
				"{apps}": "[data-es-group-item-app]",
				"{filters}": "[data-es-group-filter]",
				"{filterStream}" : "[data-es-group-stream]",
				"{appFilter}"	: "[data-es-group-app-filter]",
				"{appFilterShowAll}" : "[data-app-filters-showall]",
				"{filterBtn}"	 : "[data-stream-filter-button]",
				"{filterEditBtn}": "[data-dashboardFeeds-Filter-edit]",

				"{filterModeration}": "[data-filter-moderation]",

				// hashtag filter save
				"{saveHashTag}"		: "[data-hashtag-filter-save]",

				"{filterUL}": "[data-es-group-ul]",

				"{joinGroup}": "[data-es-group-join]",
				"{leaveGroup}": "[data-es-group-leave]",
				"{invite}": "[data-es-group-invite]",
				"{respond}": "[data-es-group-respond]",
				"{withdraw}": "[data-es-group-withdraw]",

				'{info}': '[data-info]',
				'{infoItem}': '[data-info-item]',

				view: {
					infoItem: 'info/item'
				}
			}
		}, function(self, options, base) {
				return {
					init : function(){

						options.type = base.data('type');
						options.id = base.data('id');


						// Implement app controller
						self.apps().implement(EasySocial.Controller.Groups.Item.App, {
							"{parent}": self,
							"groupId": options.id
						});
					},

					setActive: function(el) {
						// Remove all active filters
						self.filters().removeClass('active');

						// Add active filter of the element
						el.parent().addClass('active');
					},

					setLoading: function(el) {
						// Empty the contents
						self.content().html('');

						// Add loading class
						self.element.addClass('loading');
					},

					updateContents: function(html) {
						// Once the content is updated, remove the loading class
						self.element.removeClass('loading');

						self.content().html(html);
					},

					"{appFilterShowAll} click" : function(el,event) {
						// Hide itself
						el.hide();

						self.filters().removeClass('hide');
					},

					"{filterEditBtn} click" : function(el, event) {
						event.preventDefault();

						// Update the url
						el.route();

						// Notify the dashboard that it's starting to fetch the contents.
						self.setLoading();

						self.setActive(el);

						EasySocial.ajax( 'site/controllers/groups/getFilter' ,
						{
							"id"			: el.data( 'id' ),
							"clusterId" 	: self.options.id
						})
						.done(function( contents )
						{
							// self.dashboard.updateHeading( title , desc );

							self.updateContents( contents );
						})
						.fail( function( messageObj ){

							return messageObj;
						})
						.always(function(){

						});

					},

					"{filterBtn} click" : function(el, event) {

						// Prevent bubbling up
						event.preventDefault();

						// Update the url
						el.route();

						self.setActive(el);

						// Notify the dashboard that it's starting to fetch the contents.
						self.setLoading();

						EasySocial.ajax( 'site/controllers/groups/getFilter' , {
							"id": 0,
							"clusterId": self.options.id
						}).done(function(contents) {
							self.updateContents(contents);
						}).fail(function(messageObj) {
							return messageObj;
						}).always(function(){
						});

					},

					"{appFilter} click" : function(el, event) {
						event.preventDefault();

						// Get the url and the title
						var url 	= el.data('url'),
							title 	= el.data('title');

						// Set the browser's url
						History.pushState( {state:1} , title , url);

						// Set active class
						self.setActive( el );

						// Set the loading screen
						self.setLoading();

						// Set active menu
						self.setActive(el);

						// Get the id of the app
						var id 	= el.data('id');

						// Perform an ajax to get the group's stream data
						EasySocial.ajax( 'site/controllers/groups/getStream',
						{
							"id" 	: self.options.id,
							"app"	: id,
							"view" 	: "groups"
						})
						.done(function( contents )
						{
							self.updateContents( contents );
						});
					},

					"{filterModeration} click": function(filterModeration, event) {
						event.preventDefault();

						// Update the browser's url
						filterModeration.route();

						// Set the active class to the current filter
						self.setActive(filterModeration);

						// Perform an ajax to get the group's stream data
						EasySocial.ajax('site/controllers/groups/getStream', {
							"id": options.id,
							"view": 'groups',
							"moderation": 1
						}).done(function(contents) {
							self.updateContents(contents);
						});
					},

					"{filterStream} click" : function(el, event) {
						event.preventDefault();

						// Set active class
						self.setActive( el );

						// Set the browser's url
						el.route();

						// Notify the dashboard that it's starting to fetch the contents.
						self.setLoading();

						var currentSidebarMenu = $("[data-dashboardSidebar-menu].active");
						var fid = currentSidebarMenu.data( 'fid' );

						// Perform an ajax to get the group's stream data
						EasySocial.ajax( 'site/controllers/groups/getStream', {
							"id": self.options.id,
							"filterId": fid,
							"view": 'groups',
							"layout": 'item'
						}).done(function(contents) {
							self.updateContents(contents);
						});
					},

					"{saveHashTag} click": function( el )
					{
						var hashtag = el.data('tag');
						var id 		= el.data('id');

						EasySocial.dialog({
							content		: EasySocial.ajax( 'site/views/stream/confirmSaveFilter', { "tag": hashtag } ),
							bindings	:
							{
								"{saveButton} click" : function()
								{
									this.inputWarning().hide();

									filterName = this.inputTitle().val();

									if( filterName == '' )
									{
										this.inputWarning().show();
										return;
									}

									EasySocial.ajax( 'site/controllers/groups/addFilter',
									{
										"title"		: filterName,
										"tag"		: hashtag,
										"id"		: id,
									})
									.done(function( html, msg )
									{
										// self.feeds().append( html );

										var item = $.buildHTML( html );

										self.filterUL().append( item );

										// show message
										EasySocial.dialog( msg );

										// auto close the dialog
										setTimeout(function() {
											EasySocial.dialog().close();
										}, 2000);

									});
								}
							}
						});
					},

					"{withdraw} click" : function( el , event )
					{
						EasySocial.dialog(
						{
							content	: EasySocial.ajax( 'site/views/groups/confirmWithdraw' , { "id" : self.options.id } )
						});
					},

					"{invite} click" : function( el , event )
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/groups/inviteFriends' , { "id" : self.options.id } )
						})
					},

					"{respond} click" : function( el , event )
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'site/views/groups/confirmRespondInvitation' , { "id" : self.options.id } ),
							bindings: {
								"{rejectButton} click" : function() {
									this.responseValue().val('reject');

									this.form().submit();
								},

								"{acceptButton} click" : function() {
									this.responseValue().val('accept');

									this.form().submit();
								}
							}
						})
					},

					"{joinGroup} click" : function(el, event) {

						// // If this is an open group, hide the join button since the user is already a member of the group
						// if (options.type == 'open') {

						// 	// Add loading
						// 	base.switchClass('is-loading');

						// 	// Join the group and hide the footer
						// 	EasySocial.ajax('site/controllers/groups/joingroup', {
						// 		"id": options.id
						// 	}).done(function() {
						// 		base.switchClass('is-member');
						// 	});

						// 	return;
						// }

						// // If this is a private group, display the standard popup.
						// EasySocial.dialog({
						// 	content: EasySocial.ajax('site/controllers/groups/joinGroup', { "id" : options.id})
						// });

						EasySocial.dialog({
							content: EasySocial.ajax('site/controllers/groups/joinGroup' , { "id" : self.options.id})
						});
					},

					"{leaveGroup} click" : function( el , event )
					{
						EasySocial.dialog({
							content: EasySocial.ajax( 'site/views/groups/confirmLeaveGroup' , { "id" : self.options.id } ),
							bindings: {
								"{leaveButton} click" : function() {
									this.leaveForm().submit();
								}
							}
						})
					},

					'{info} click': function(el, ev) {
						ev.preventDefault();

						el.route();

						self.setActive(el);

						self.setLoading();

						var loaded = el.data('loaded');

						if (loaded) {
							self.infoItem().eq(0).trigger('click');
							return;
						}

						if (el.enabled()) {
							el.disabled(true);

							EasySocial.ajax('site/controllers/groups/initInfo', {
								groupId: self.options.id
							}).done(function(steps) {
								el.data('loaded', 1);

								var parent = el.parent('[data-es-group-filter]');

								// Append all the steps
								$.each(steps.reverse(), function(index, step) {
									if (!step.hide) {
										parent.after(self.view.infoItem({
											url: step.url,
											title: step.title,
											index: step.index
										}));
									}

									if (step.html) {
										self.updateContents(step.html);
										self.content().find('[data-field]').trigger('onShow');
									}
								});

								var item = self.infoItem().eq(0);

								self.setActive(item);

								// Have to set the title
								$(document).prop('title', item.attr('title'));

								el.enabled(true);
							}).fail(function(error) {
								el.enabled(true);
								self.updateContents(error.message);
							});
						}
					},

					'{infoItem} click': function(el, ev) {
						ev.preventDefault();

						el.route();

						self.setActive(el);

						self.setLoading();

						var index = el.data('info-index');

						EasySocial.ajax('site/controllers/groups/getInfo', {
							groupId: self.options.id,
							index: index
						}).done(function(contents) {
							self.updateContents(contents);

							self.content().find('[data-field]').trigger('onShow');
						}).fail(function(error) {
							self.updateContents(error.message);
						});
					}
				}
			}
		);

		EasySocial.Controller(
			'Groups.Item.App',
			{
				defaultOptions:
				{

				}
			},
			function( self )
			{
				return {
					init : function()
					{
						self.options.id 	= self.element.data( 'app-id' );
					},

					"{self} click" : function( el , event )
					{
						// Prevent event from bubbling up
						event.preventDefault();

						// Update the url in the browser
						el.route();

						// Set active element
						self.parent.setActive( el );

						// Set loading class
						self.parent.setLoading();

						// Make an ajax call to get the app contents
						EasySocial.ajax( 'site/controllers/groups/getAppContents' ,
						{
							"appId"		: self.options.id,
							"groupId"	: self.options.groupId
						})
						.done( function( contents )
						{
							self.parent.updateContents( contents );
						})
						.fail(function( messageObj )
						{
							return messageObj;
						});
					}
				}
			}
		);

		EasySocial.Controller('Groups.Item.Members', {
			defaultOptions: {
				"{items}" 	: "[data-group-members-item]",
				"{filters}"	: "[data-group-members-filter]",
				"{content}"	: "[data-group-members-content]"
			}
		}, function(self) {
			return {

				init : function() {
					self.options.id = self.element.data('id');
					self.items().implement(EasySocial.Controller.Groups.Item.Members.Record);
				},

				"{filters} click" : function(el, event) {
					// Remove active
					self.filters().removeClass( 'active' );

					// Set current to active
					el.addClass( 'active' );

					// Get the filter
					var filter 	= el.data( 'filter' );

					// Set the loading class
					self.content().html( '&nbsp;' );
					self.content().addClass( 'is-loading' );

					EasySocial.ajax( 'apps/group/members/controllers/groups/filterMembers' ,
					{
						"id"		: self.options.id,
						"filter" 	: filter
					})
					.done(function( contents )
					{
						// Remove is-loading
						self.content().removeClass( 'is-loading' );

						self.content().html( contents );

						// Re-implement the members record
						self.items().implement(EasySocial.Controller.Groups.Item.Members.Record);
					});
				}
			}
		});

		EasySocial.Controller('Groups.Item.Members.Record', {
			defaultOptions: {
				"{makeAdmin}" : "[data-members-make-admin]",
				"{revokeAdmin}": "[data-members-revoke-admin]",
				"{approve}": "[data-members-approve]",
				"{reject}": "[data-members-reject]",
				"{removeMember}": "[data-members-remove]",
				"{cancelInvitation}": "[data-members-cancel-invitation]"
			}
		}, function(self) { return {
			
			init : function() {
				self.options.id = self.element.data('id');
				self.options.groupId = self.element.data('groupid');
				self.options.redirect = self.element.data('redirect');
			},

			// Approve a member
			"{approve} click" : function(el) {

				EasySocial.dialog({
					"content": EasySocial.ajax('site/views/groups/confirmApprove', { "id" : self.options.groupId , "userId" : self.options.id, "return": self.options.redirect })
				});
			},

			"{reject} click" : function()
			{
				EasySocial.dialog(
				{
					content 	: EasySocial.ajax( 'site/views/groups/confirmReject' , { "id" : self.options.groupId , "userId" : self.options.id } )
				});
			},

			"{cancelInvitation} click" : function()
			{
				EasySocial.dialog(
				{
					content 	: EasySocial.ajax( 'site/views/groups/confirmCancelInvitation' , { "id" : self.options.groupId , "userId" : self.options.id } )
				});
			},

			"{revokeAdmin} click" : function()
			{
				EasySocial.dialog(
				{
					content 	: EasySocial.ajax( 'site/views/groups/confirmRevokeAdmin' , { "id" : self.options.groupId , "userId" : self.options.id } ),
					bindings:
					{
						"{revokeButton} click" : function()
						{
							EasySocial.ajax( 'site/controllers/groups/revokeAdmin' ,
							{
								"id"		: self.options.groupId,
								"userId"	: self.options.id
							})
							.done(function()
							{
								// Close the dialog once done.
								EasySocial.dialog().close();

								self.element.removeClass( 'is-admin' ).addClass( 'is-member' );
							});

						}
					}
				});
			},

			"{removeMember} click" : function()
			{
				EasySocial.dialog(
				{
					content 	: EasySocial.ajax( 'site/views/groups/confirmRemoveMember' , { "id" : self.options.groupId , "userId" : self.options.id } )
				});
			},

			"{makeAdmin} click" : function()
			{
				EasySocial.dialog(
				{
					content	: EasySocial.ajax( 'site/views/groups/confirmMakeAdmin' , { "id" : self.options.id }),
					bindings:
					{
						"{makeAdminButton} click" : function()
						{
							EasySocial.ajax( 'site/controllers/groups/makeAdmin' ,
							{
								"id"		: self.options.groupId,
								"userId"	: self.options.id
							})
							.done(function()
							{
								// Close the dialog once done.
								EasySocial.dialog().close();

								self.element.removeClass( 'is-member' ).addClass( 'is-admin' );
							});

						}
					}
				});
			}
		}});

		EasySocial.Controller(
			'Groups.Item.Discussions',
			{
				defaultOptions:
				{
					"{filter}" 	: "[data-group-discussions-filter]",
					"{contents}": "[data-group-discussion-contents]"
				}
			},
			function(self)
			{
				return {
					init: function()
					{
						self.options.id 	= self.element.data( 'id' );
					},

					setContent: function( html )
					{
						// Remove loading class since we already have the content.
						self.contents().removeClass( 'is-loading' );

						self.contents().html( html );
					},

					setActiveFilter: function( el )
					{
						// Remove active class.
						self.filter().removeClass( 'active' );

						// Add active class to the current element
						el.addClass( 'active' );
					},

					"{filter} click" : function( el , event )
					{
						var filter = el.data( 'filter' );

						// Add loader for the contents area
						self.contents().html( '&nbsp;' ).addClass( 'is-loading' );

						// Set active filter
						self.setActiveFilter( el );

						// Run the ajax call now
						EasySocial.ajax( 'apps/group/discussions/controllers/discussion/getDiscussions' ,
						{
							"id"		: self.options.id,
							"filter" 	: filter
						})
						.done(function( contents , empty )
						{
							if( empty )
							{
								self.contents().addClass( 'is-empty' );
							}
							else
							{
								self.contents().removeClass( 'is-empty' );
							}
							// Set the contents
							self.setContent( contents );
						});
					}
				}
			}
		);
		EasySocial.Controller(
			'Groups.Item.Discussion',
			{
				defaultOptions:
				{
					"{form}"		: "[data-reply-form]",
					"{list}"		: "[data-reply-list]",
					"{replies}"		: "[data-reply-item]",
					"{repliesWrap}"	: "[data-replies-wrapper]",

					"{replyCounter}": "[data-reply-count]",

					"{lock}"		: "[data-discussion-lock]",
					"{unlock}"		: "[data-discussion-unlock]",
					"{delete}"		: "[data-discussion-delete]"
				}
			},
			function( self )
			{
				return {
					init: function()
					{
						self.options.id 		= self.element.data( 'id' );
						self.options.groupId	= self.element.data( 'groupid' );

						self.implementReply( self.replies() );

						self.form().implement( EasySocial.Controller.Groups.Item.Discussion.Form ,
						{
							"{parent}"	: self
						});
					},

					implementReply: function()
					{
						self.replies().implement( EasySocial.Controller.Groups.Item.Discussion.Reply ,
						{
							"{parent}"	: self
						});
					},

					insertReply: function( html )
					{
						// Since we know that we need to append the reply item, we need to remove is-unanswered
						self.element.removeClass( 'is-unanswered' );

						// Since an item is added, we want to remove the empty class.
						self.repliesWrap().removeClass( 'is-empty' );

						// Append the new item
						self.list().append( html );

						// Implement the controller again
						self.implementReply();
					},

					updateReplyCounter: function( total )
					{
						if( total == 0 )
						{
							self.repliesWrap().addClass( 'is-empty' );
						}
						self.replyCounter().html( total );
					},

					setResolved: function()
					{
						self.element.addClass( 'is-resolved' );
					},

					"{unlock} click" : function( el , event )
					{
						EasySocial.ajax( 'apps/group/discussions/controllers/discussion/unlock' ,
						{
							"id" : self.options.id
						})
						.done(function()
						{
							// Add lock element
							self.element.removeClass( 'is-locked' );
						});
					},

					"{delete} click" : function( el , event )
					{
						EasySocial.dialog(
						{
							content : EasySocial.ajax( 'apps/group/discussions/controllers/discussion/confirmDelete' , { "id" : self.options.id , "groupId" : self.options.groupId })
						});
					},

					"{lock} click" : function( el , event )
					{
						EasySocial.dialog(
						{
							content : EasySocial.ajax( 'apps/group/discussions/controllers/discussion/confirmLock' ),
							bindings:
							{
								"{lockButton} click" : function()
								{
									EasySocial.ajax( 'apps/group/discussions/controllers/discussion/lock' ,
									{
										"id" : self.options.id
									})
									.done(function()
									{
										// Hide the dialog
										EasySocial.dialog().close();

										// Add lock element
										self.element.addClass( 'is-locked' );
									});
								}
							}
						});
					}
				}
			}
		);

		EasySocial.Controller(
			'Groups.Item.Discussion.Reply',
			{
				defaultOptions:
				{
					"{acceptAnswer}"	: "[data-reply-accept-answer]",
					"{delete}"			: "[data-reply-delete]",
					"{edit}"			: "[data-reply-edit]",
					"{cancelEdit}"		: "[data-reply-edit-cancel]",
					"{update}"			: "[data-reply-edit-update]",
					"{textarea}"		: "[data-reply-content]",
					"{content}"			: "[data-reply-display-content]",
					"{alertDiv}" 		: "div.alert-error"
				}
			},
			function( self )
			{
				return {
					init: function()
					{
						self.options.id 	= self.element.data( 'id' );
					},
					"{acceptAnswer} click" : function()
					{
						EasySocial.ajax( 'apps/group/discussions/controllers/reply/accept' ,
						{
							"id" : self.options.id
						})
						.done(function()
						{
							self.parent.setResolved();
						});
					},

					cancelEditing : function()
					{
						self.element.removeClass( 'is-editing' );
					},

					"{cancelEdit} click" : function()
					{
						self.cancelEditing();
					},

					"{edit} click" : function()
					{
						self.element.addClass( 'is-editing' );
					},

					"{update} click" : function()
					{
						var content 	= self.textarea().val();

						// console.log( self.element);

						// If content is empty, throw some errors
						if( content == '' )
						{
							self.element.addClass( 'is-empty' );
							self.alertDiv().show();
							return false;
						}

						EasySocial.ajax( 'apps/group/discussions/controllers/reply/update' ,
						{
							"id"		: self.options.id,
							"content"	: content
						})
						.done(function( content )
						{
							// Update the content
							self.content().html( content );

							self.element.removeClass( 'is-empty' );
							self.alertDiv().hide();


							// Hide the textarea
							self.cancelEditing();
						});
					},

					"{delete} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'apps/group/discussions/controllers/reply/confirmDelete' , { "id"	: self.options.id } ),
							bindings	:
							{
								"{deleteButton} click" : function()
								{
									EasySocial.ajax( 'apps/group/discussions/controllers/reply/delete',
									{
										"id"	: self.options.id
									})
									.done(function( discussion )
									{
										// Update the counter
										self.parent.updateReplyCounter( discussion.total_replies );

										// Hide the dialog
										EasySocial.dialog().close();

										// Remove the element
										self.element.remove();
									});
								}
							}
						});
					}
				}
			}
		);

		EasySocial.Controller(
			'Groups.Item.Discussion.Form',
			{
				defaultOptions:
				{
					"{textarea}"	: "[data-reply-content]",
					"{submitReply}" : "[data-reply-submit]"
				}
			},
			function( self )
			{
				return {
					init: function()
					{
					},
					"{submitReply} click" : function( el , event )
					{
						var content 	= self.textarea().val();

						// If content is empty, throw some errors
						if( content == '' )
						{
							self.element.addClass( 'is-empty' );
							return false;
						}

						EasySocial.ajax( 'apps/group/discussions/controllers/reply/submit' ,
						{
							"id"		: self.parent.options.id,
							"groupId"	: self.parent.options.groupId,
							"content"	: content
						})
						.done(function( html )
						{
							// Inser the new node back.
							self.parent.insertReply( html );

							// Update the textarea
							self.textarea().val( '' );
						});

					}
				}
			}
		);

		EasySocial.Controller('Groups.Item.News', {
				defaultOptions: {
					"{delete}" 			: "[data-news-delete]",
					"{likes}"			: "[data-likes-action]",
					"{counter}"			: "[data-news-counter]",
					"{likeContent}" 	: "[data-likes-content]",
				}
			}, function(self) {
				return {

					init : function()
					{
						self.options.id 	= self.element.data( 'id' );
						self.options.groupId = self.element.data( 'group-id' );
					},

					//need to make the data-stream-counter visible
					"{likes} onLiked": function(el, event, data) {
						self.counter().removeClass('hide');
					},

					"{likes} onUnliked": function(el, event, data) {
						var hideCounter 	= self.likeContent().hasClass( 'hide' );

						if( hideCounter )
						{
							self.counter().addClass( 'hide' );
						}
					},
					"{delete} click" : function( el , event )
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax( 'apps/group/news/controllers/news/confirmDelete' , { "id" : self.options.id , "groupId" : self.options.groupId })
						});
					}
				}
			}
		);

		module.resolve();
	});
});

