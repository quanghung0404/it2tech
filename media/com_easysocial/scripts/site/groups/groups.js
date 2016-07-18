EasySocial.module('site/groups/groups', function($)
{
	var module	= this;

	EasySocial.require()
	.view('site/loading/small')
	.script('validate', 'field')
	.done(function($) {
		EasySocial.Controller(
			'Groups.Browser', {
				
				defaultOptions: {
					"{filters}": "[data-es-groups-filters] > li",
					"{sort}": "[data-groups-sort]",
					"{categories}": "[data-es-groups-categories] >li",
					"{content}": "[data-es-groups-content]",
					"{items}": "[data-groups-item]",
					"{featured}": "[data-groups-featured-item]",
					"{listContents}": "[data-es-groups-list]",

					view: {
						loader: "site/loading/small"
					}
				}
			}, function(self) {
				return {

					init: function() {
						// Implement user item controller
						self.initGroupController();
					},

					initGroupController : function() {

						// Implement the filters
						self.filters().implement(EasySocial.Controller.Groups.Filter,{
							"{parent}" : self
						});

						// Implement the sorting
						self.sort().implement(EasySocial.Controller.Groups.Sort,{
							"{parent}" : self
						});

						// Implement category filters
						self.categories().implement(EasySocial.Controller.Groups.Filter.Category, {
							"{parent}": self
						});

						self.featured().implement(EasySocial.Controller.Groups.Browser.Item, {
							"{parent}": self
						});

						self.items().implement(EasySocial.Controller.Groups.Browser.Item, {
							"{parent}": self
						});
					},

					// Allows child items to set an active filter
					setActive: function(element) {
						// Remove all category filters
						self.categories().removeClass('active');

						// Remove all active items
						self.filters().removeClass('active');

						// Add active on the element
						$(element).addClass('active');

						// Add loading class
						self.content().html('&nbsp;');
						self.element.addClass('loading');
					},

					setListContent: function(contents) {

						self.listContents().html(contents);

						self.items().implement(EasySocial.Controller.Groups.Browser.Item, {
							"{parent}": self
						});
					},

					setContent: function(contents, replaceWrapper) {

						// When content is ready, we should remove the loading state.
						self.element.removeClass('loading');

						//Update the content
						if (replaceWrapper) {
							self.content().html(contents);	
						} else {
							self.listContents().html(contents);
						}
						
						self.items().implement(EasySocial.Controller.Groups.Browser.Item, {
							"{parent}": self
						});
					}
				}
			}
		);


		EasySocial.Controller('Groups.Browser.Item', {
				defaultOptions: {
					"{joinGroup}": "[data-groups-item-join]",
					"{leaveGroup}": "[data-groups-leave]",
					"{respond}": "[data-groups-item-respond]",
					"{setFeatured}": "[data-groups-item-set-featured]",
					"{removeFeatured}": "[data-groups-item-remove-featured]",
					"{deleteGroup}": "[data-groups-item-delete]",
					"{unpublishGroup}": "[data-groups-item-unpublish]",
					"{footer}": "[data-groups-item-footer]"
				}
			}, function(self, options, base) {

				return {
					
					init : function() {
						options.id = self.element.data('id');
						options.type = self.element.data('type');
					},

					"{deleteGroup} click" : function(el, event) {
						EasySocial.dialog({
							content 	: EasySocial.ajax('site/views/groups/confirmDelete', { "id" : self.options.id })
						});
					},

					"{unpublishGroup} click" : function(el, event) {
						EasySocial.dialog({
							content: EasySocial.ajax('site/views/groups/confirmUnpublishGroup', { "id" : self.options.id })
						});
					},

					"{setFeatured} click" : function(el, event) {
						EasySocial.dialog({
							content	: EasySocial.ajax('site/views/groups/setFeatured', {"id" : self.options.id})
						});
					},

					"{removeFeatured} click" : function(el, event) {
						EasySocial.dialog({
							content : EasySocial.ajax('site/views/groups/removeFeatured', { "id" : self.options.id })
						})
					},

					"{respond} click" : function(el, event) {
						EasySocial.dialog({
							content: EasySocial.ajax('site/views/groups/confirmRespondInvitation', { "id" : self.options.id }),
							bindings: {
								"{rejectButton} click" : function() {

									// Add loading
									base.switchClass('is-loading');

									EasySocial.ajax('site/controllers/groups/respondInvitation', {
										"id": options.id,
										"action": "reject"
									}).done(function() {

										// Show join button
										base.switchClass('is-guest');

										EasySocial.dialog().close();
									});
								},

								"{acceptButton} click" : function() {

									// Add loading
									base.switchClass('is-loading');

									EasySocial.ajax('site/controllers/groups/respondInvitation', {
										"id": options.id,
										"action": "accept"
									}).done(function() {

										// Show leave button
										base.switchClass('is-member');

										EasySocial.dialog().close();
									});
								}
							}
						});
					},

					"{leaveGroup} click": function(leaveButton, event) {

						EasySocial.dialog({
							content: EasySocial.ajax('site/views/groups/confirmLeaveGroup', {"id": options.id}),
							bindings: {
								"{leaveButton} click" : function() {

									// Add loading
									base.switchClass('is-loading');

									// Perform an ajax call to really leave the group
									EasySocial.ajax('site/controllers/groups/leaveGroup', {
										"id": options.id
									}).done(function() {
										// Show the join group again
										base.switchClass('is-guest');

										// Hide the dialog
										EasySocial.dialog().close();
									});
								}
							}
						});
					},

					"{joinGroup} click" : function(joinButton, event) {

						// If this is an open group, hide the join button since the user is already a member of the group
						if (options.type == 'open') {

							// Add loading
							base.switchClass('is-loading');

							// Join the group and hide the footer
							EasySocial.ajax('site/controllers/groups/joingroup', {
								"id": options.id
							}).done(function() {
								base.switchClass('is-member');
							});

							return;
						}

						// If this is a private group, display the standard popup.
						EasySocial.dialog({
							content: EasySocial.ajax('site/controllers/groups/joinGroup', { "id" : options.id})
						});
					}
				}
			}
		);

		EasySocial.Controller(
			'Groups.Edit', {
				defaultOptions: {
					id: null,

					"{stepContent}": "[data-group-edit-fields-content]",
					"{stepItem}": "[data-group-edit-fields-step]",

					// Forms.
					"{profileForm}": "[data-group-fields-form]",

					// Content for profile editing
					"{profileContent}": "[data-group-edit-fields]",

					"{fieldItem}": "[data-group-edit-fields-item]",

					// Submit buttons
					"{save}"			: "[data-group-fields-save]"
				}
			}, function(self) {
				return {

					init: function()
					{
						self.fieldItem().addController('EasySocial.Controller.Field.Base', {
							mode: 'edit'
						});
					},

					errorFields: [],

					// Support field throwing error internally
					'{fieldItem} error': function(el, ev)
					{
						self.triggerStepError(el);
					},

					// Support for field resolving error internally
					'{fieldItem} clear': function(el, ev)
					{
						self.clearStepError(el);
					},

					// Support validate.js throwing error externally
					'{fieldItem} onError': function(el, ev)
					{
						self.triggerStepError(el);
					},

					triggerStepError: function(el)
					{
						var fieldid = el.data('id'),
							stepid = el.parents(self.stepContent.selector).data('id');

						if($.inArray(fieldid, self.errorFields) < 0)
						{
							self.errorFields.push(fieldid);
						}

						self.stepItem().filterBy('for', stepid).trigger('error');
					},

					clearStepError: function(el)
					{
						var fieldid = el.data('id'),
							stepid = el.parents(self.stepContent.selector).data('id');

						self.errorFields = $.without(self.errorFields, fieldid);

						self.stepItem().filterBy('for', stepid).trigger('clear');
					},

					"{stepItem} click" : function(el, event)
					{
						var id 	= $(el).data('for');

						// Profile form should be hidden
						self.profileContent().show();

						// Hide all profile steps.
						self.stepContent().hide();

						// Remove active class on step item
						self.stepItem().removeClass('active');

						// Add active class on the selected item.
						el.addClass('active');

						// Get the step content element
						var stepContent = self.stepContent('.step-' + id);

						// Show active profile step.
						stepContent.show();

						// Trigger onShow on the field item in the content
						stepContent.find(self.fieldItem.selector).trigger('show');
					},

					"{stepItem} error": function(el)
					{
						el.addClass('error');
					},

					"{stepItem} clear": function(el)
					{
						if(self.errorFields.length < 1)
						{
							el.removeClass('error');
						}
					},

					"{save} click" : function(el, event)
					{
						// Run some error checks here.
						event.preventDefault();

						el.addClass('btn-loading');

						self.profileForm()
							.validate()
							.fail(function()
							{
								el.removeClass('btn-loading');
								EasySocial.dialog(
								{
									content 	: EasySocial.ajax('site/views/profile/showFormError')
								});
							})
							.done(function()
							{
								self.profileForm().submit();
							});

						return false;
					}
				}
			}
		);

		EasySocial.Controller('Groups.Filter', {
				defaultOptions: {
				}
			}, function(self) {
				return {
					init : function() {
					},

					"{self} click" : function(el, event) {

						// Prevent default.
						event.preventDefault();

						// Add active class to itself.
						self.parent.setActive(el);

						// Update the URL on the browser
						$(el).find('a').route();

						EasySocial.ajax('site/controllers/groups/getGroups', {
							filter: self.element.data('es-groups-filters-type')
						})
						.done(function(contents) {
							self.parent.setContent(contents, true);

							// Re-apply controller
							self.parent.initGroupController();
						});
					}
				}
			}
		);

		EasySocial.Controller('Groups.Sort', {
				defaultOptions: {
				}
			}, function(self) {
				return {

					init : function() {
					},

					"{self} click" : function(sortButton, event) {

						event.preventDefault();

						// Get the sort type
						var type = sortButton.data('type');
						var filter = sortButton.data('filter');
						var category = sortButton.data('categoryid');

						// Create an anchor link and route it?
						$('<a>').attr({
							"title": document.title,
							"href": sortButton.attr('href')
						}).route();

						// Add the active state on the current element.
						self.parent.sort().removeClass('active');
						sortButton.addClass('active');

						// Add the loader on the list content
						self.parent.listContents().html(self.parent.view.loader());


						// Render the ajax to load the contents.
						EasySocial.ajax('site/controllers/groups/getGroups', {
							ordering: type,
							filter: filter,
							categoryId: category
						})
						.done(function(contents) {

							// Update the contents
							self.parent.setContent(contents, false);

							// Re-apply controller
							self.parent.initGroupController();
						});
					}
				}
			}
		);

		EasySocial.Controller('Groups.Filter.Category', {
				defaultOptions: {
					id 	: null
				}
			}, function(self) {
				
				return {
					init : function() {
						self.options.id = self.element.data('es-groups-category-id');
					},

					"{self} click" : function(el, event) {
						// Prevent default.
						event.preventDefault();

						// Set active item
						self.parent.setActive(el);

						// Update the url
						$(el).find('a').route();

						// Perform ajax calls to update the content
						EasySocial.ajax('site/controllers/groups/getGroups', {
							categoryId 	: self.options.id
						})
						.done(function(contents) {
							self.parent.setContent(contents, true);

							// Re-apply controller
							self.parent.initGroupController();
						});
					}
				}
			}
		);

		EasySocial.Controller('Groups.Create', {
			defaultOptions: {
				'previousLink': null,

				'{fieldItem}': '[data-groups-create-fields-item]',

				'{previousButton}': '[data-groups-create-previous]',

				'{nextButton}': '[data-groups-create-submit]'
			}
		}, function(self) {
			return {
				init: function() {
					self.fieldItem().addController('EasySocial.Controller.Field.Base');
				},

				'{previousButton} click': function() {
					window.location = self.options.previousLink;
				},

				'{nextButton} click': function(el) {
					if (el.enabled()) {
						el.disabled(true);

						el.addClass('btn-loading');

						self.element.validate()
							.done(function() {
								el.removeClass('btn-loading');
								el.enabled(true);

								self.element.submit();
							})
							.fail(function() {
								el.removeClass('btn-loading');
								el.enabled(true);

								EasySocial.dialog({
									content 	: EasySocial.ajax('site/views/profile/showFormError')
								});
							});
					}
				}
			}
		});

		module.resolve();
	});
});

