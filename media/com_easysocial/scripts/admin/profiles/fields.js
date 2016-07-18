EasySocial.module('admin/profiles/fields', function($) {
	var module = this;

	EasySocial.require()
	.library(
		'ui/draggable',
		'ui/sortable',
		'ui/droppable'
	)
	.script(
		'field'
	)
	.view(
		'admin/profiles/fields/editor.item',
		'admin/profiles/fields/step.item',
		'admin/profiles/fields/editor.page',
		'admin/profiles/fields/config',
		'admin/profiles/fields/dialog.move'
	)
	.language(
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_ITEM_CONFIG_LOADING',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_TITLE',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_CONFIRMATION',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_CONFIRM',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_CANCEL',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_DELETING',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_TITLE',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_CONFIRMATION',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_CONFIRM',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_CANCEL',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_DELETING',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CORE_UNIQUE_KEY_SAVE_FIRST',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_CONFIGURE_PAGE',
		'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_CONFIGURE_FIELD',
		'COM_EASYSOCIAL_FIELDS_REQUIRE_MANDATORY_FIELDS',
		'COM_EASYSOCIAL_FIELDS_UNSAVED_CHANGES',
		'COM_EASYSOCIAL_FIELDS_INVALID_VALUES'
	)
	.done(function() {

		// Controller instance
		var $Parent, $Browser, $Editor, $Steps, $Config;

		// Data registry
		var $Apps = {}, $Core = {}, $Check = {}, $Fields = {}, $Pages = {};

		// Delete registry
		var $Deleted = {
			pages: [],
			fields: []
		}

		EasySocial.Controller('Fields', {
			defaultOptions: {
				id: 0,

				group: null,

				'{wrap}'		: '[data-fields-wrap]',

				'{browser}'		: '[data-fields-browser]',
				'{editor}'		: '[data-fields-editor]',
				'{steps}'		: '[data-fields-steps]',
				'{config}'		: '[data-fields-config]',
				'{saveForm}'	: '[data-fields-save]',

				view: {
					config: 'admin/profiles/fields/config'
				}
			}
		}, function(self) {
			return {

				init: function()
				{
					$Parent = self;

					// The id's are bound in data-id
					self.options.id = self.element.data('id');

					// Get the controller for field browser.
					$Browser	= self.addPlugin('browser');

					// Get the controller for field editor.
					$Editor		= self.addPlugin('editor');

					// Get the controller for steps.
					$Steps		= self.addPlugin('steps');

					var controllers = [$Browser.state, $Editor.state, $Steps.state];

					// Only trigger when all of the states is resolved
					$.when.apply(null, controllers).done(function() {
						$Parent.trigger('controllersReady');
					});

					// Listen to save event on profileForm to perform the save
					$('.profileForm').on('save', function(ev, task, result) {
						var data = self.save(task);
						result.push(data);
					});
				},

				changed: false,

				customFieldChanged: function() {
					self.changed = true;
				},

				'{window} beforeunload': function(el, ev) {
					if(self.changed) {
						return $.language('COM_EASYSOCIAL_FIELDS_UNSAVED_CHANGES');
					}
				},

				/**
				 * When save form is called, call each page's export function to get the data
				 */
				'{saveForm} click': function()
				{
					self.save();
				},

				/**
				 * Send the data to the controller to process the fields.
				 */
				save: function(task)
				{
					var dfd = $.Deferred();

					// Disable all input and select within this form to prevent them from getting through POST
					self.element.find('input,select').not(self.saveForm()).prop('disabled', true);

					// Reset saveform value first
					self.saveForm().val('');

					if( task === 'savecopy' )
					{
						self.changed = true;
					}

					// If no changes, then skip this saving
					if( !self.changed )
					{
						dfd.resolve();

						return dfd;
					}

					// Trigger saving event
					$Parent.trigger('saving');

					// If config is open, we run a internal populate first on the config
					if($Config && $Config.state) {
						if(!$Config.checkConfig()) {

							EasySocial.dialog({
								content: $.language('COM_EASYSOCIAL_FIELDS_INVALID_VALUES')
							});

							dfd.reject();

							return dfd;
						}
					}

					// Clone a non-referenced $Core object into $Check
					$Check = $.extend(true, {}, $Core);

					var data = [];

					// Loop through each step
					$.each($Steps.step(), function(i, step) {
						step = $(step);

						// Get the step's page controller
						var page = $Editor.getPage(step.data('id'));

						// Call the page's export function to get the data of the page
						data.push(page._export());
					});

					// Check if all core apps has been used
					if($._.keys($Check).length > 0) {
						// Trigger saved event and pass in false to indicate error
						$Parent.trigger('saved', [false]);

						EasySocial.dialog({
							content: $.language('COM_EASYSOCIAL_FIELDS_REQUIRE_MANDATORY_FIELDS')
						});

						dfd.reject();

						return dfd;
					}

					$Parent.changed = false;

					var saveData = {
						data: data,
						deleted: $Deleted
					};

					self.injectSaveData(saveData);

					dfd.resolve();

					return dfd;
				},

				/**
				 * Responsible to inject the data object into the hidden input for POST processing
				 */
				injectSaveData: function(data) {
					data = JSON.stringify(data);

					self.saveForm().val(data);
				},

				/**
				 * Update the form based on the returned data
				 */
				updateResult: function(data)
				{
					// It has the same format as the data
					$.each(data, function(i, dataStep) {
						// Get the step based on index (sequence)
						var step = $Steps.step().eq(i);

						// Assign step id first
						var stepid = step.data('id');

						// Get the page
						var page = $Editor.getPage(stepid);

						// Update the step id
						$Steps.updateResult(i, dataStep.id);

						// Update the page id
						page.updateResult(stepid, dataStep);
					});
				},

				'{self} doneConfiguring': function() {
					self.element.removeClass('editting');
				},

				loadConfiguration: function(item, type) {
					self.element.addClass('editting');

					// var config = self.config().clone();
					var config = $(self.view.config());

					$Config = config.addController('EasySocial.Controller.Fields.Config', {
						controller: {
							item: item
						}
					});

					if(type === 'page')
					{
						item.pageHeader().append(config);
					}
					else
					{
						item.element.append(config);
					}

					// $('body').append(config);

					self.element.trigger('loadingConfig', [type]);
				}
			}
		});

		/* Browser Controller */
		EasySocial.Controller('Fields.Browser', {
			defaultOptions: {
				'{browser}'		: '[data-fields-browser]',

				'{mandatory}'	: '[data-fields-browser-group-mandatory]',
				'{unique}'		: '[data-fields-browser-group-unique]',
				'{standard}'	: '[data-fields-browser-group-standard]',

				'{list}'		: '[data-fields-browser-list]',
				'{item}'		: '[data-fields-browser-item]',

				'affixClass'	: 'es-browser-affix'
			}
		}, function(self) {
			return {
				state: $.Deferred(),

				init: function() {
					// Things to do before resolving self
					self.registerApps();

					self.ready();

					self.affixHandler();

					self.initAffix();
				},

				ready: function() {
					self.state.resolve();
				},

				'{parent} controllersReady': function() {
					var id = $Steps.getCurrentStep().data('id');

					self.initDraggable(id);
				},

				'{parent} pageChanged': function(el, ev, page, uid) {
					self.item().draggable('destroy');

					self.initDraggable(uid);
				},

				'{parent} pageAdded': function(el, ev, page, uid) {
					self.item().draggable('destroy');

					self.initDraggable(uid);
				},

				initDraggable: function(id) {
					self.item().draggable({
						revert: 'invalid',
						helper: 'clone',
						connectToSortable: '[data-fields-editor-page-items-' + id + ']'
					});
				},

				affixHandler: function() {
					var parent = $(window),
						wrap = self.parent.wrap(),
						height = wrap.offset().top,
						scroll = parent.scrollTop();

					if(scroll > height && !self.browser().hasClass(self.options.affixClass)) {
						self.browser().addClass(self.options.affixClass);
					}

					if(scroll <= height && self.browser().hasClass(self.options.affixClass)) {
						self.browser().removeClass(self.options.affixClass);
					}
				},

				initAffix: function() {
					$(window).scroll(self.affixHandler);
				},

				registerApps: function() {
					// Register all available apps into an object
					$.each(self.item(), function(index, item) {
						item = $(item);

						var id = item.data('id');

						$Apps[id] = {
							id: id,
							element: item.data('element'),
							title: item.data('title'),
							params: item.data('params'),
							core: item.data('core'),
							unique: item.data('unique'),
							item: item
						};

						// Keep a list of core apps id in $Core
						if(item.data('core')) {
							$Core[id] = $Apps[id];
						}
					});
				},

				/**
				 * Used to check if core apps has been used in saving. Core apps have to be completely used to saved.
				 */
				checkout: function(id) {
					if($Check[id] !== undefined) {
						delete $Check[id];
					}
				},

				/**
				 * This is the event handler for the field items selection.
				 */
				'{item} click': function(el) {
					// Get the current page.
					var currentPage = $Editor.currentPage();

					// Get the app id of the item clicked
					var appid = el.data('id');

					// Add new item to the page
					currentPage.addNewField(appid);
				},

				/**
				 * Carry out any necessary actions when app is added as a field
				 */
				'{parent} fieldAdded': function(el, event, appid) {
					var app = $Apps[appid];

					if(app && app.core) {
						app.item.hide();

						// If core app is added, check if there are any remaining core app left to hide the core group
						var items = self.mandatory().find(self.item.selector).filter(':visible');

						self.mandatory().toggle((items.length > 0));
					}

					if(app && app.unique) {
						app.item.hide();

						// If unique app is added, check if there are any remaining unique app left to hide the unique group
						var items = self.unique().find(self.item.selector).filter(':visible');

						self.unique().toggle((items.length > 0));
					}
				},

				/**
				 * Carry out any necessary actions when field is removed
				 */
				'{parent} fieldDeleted': function(el, event, appid, fieldid) {
					var app = $Apps[appid];

					if(app && app.core) {
						app.item.show();

						// If core app is deleted, then the browser group for core fields have to definitely show
						self.mandatory().show();

						return;
					}

					if(app && app.unique) {
						app.item.show();

						// If unique app is deleted, then the browser group for unique fields have to definitely show
						self.unique().show();

						return;
					}
				}
			}
		});

		/* Config Controller */
		EasySocial.Controller('Fields.Config', {
			defaultOptions: {
				'{config}'		: '[data-fields-config]',

				'{header}'		: '[data-fields-config-header]',

				'{close}'		: '[data-fields-config-close]',

				'{form}'		: '[data-fields-config-form]',

				'{param}'		: '[data-fields-config-param]',

				'{tabnav}'		: '[data-fields-config-tab-nav]',
				'{tabcontent}'	: '[data-fields-config-tab-content]',

				'{done}'		: '[data-fields-config-done]'
			}
		}, function(self) {
			return {
				init: function() {
				},

				state: false,

				load: function(config) {
					// Set state to true to indicate editting mode
					self.state = true;

					// Apply multi choices
					config.find('[data-fields-config-param-choices]').addController('EasySocial.Controller.Config.Choices', {
						controller: {
							item: self.item
						}
					});

					// Hide the field title
					config.find( 'h4' ).hide();

					// Update the header
					self.header().html( config.find( 'h4' ).html() );

					// Inject the html into the form
					self.form().html(config);

					// Carry out necessary actions after config has been loaded if this is a new field
					if(self.item.options.newfield) {

						// Disable the unique key field if it is a new field
						self.param('[data-fields-config-param-field-unique_key]')
							.attr('disabled', true)
							.val($.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_PARAMS_CORE_UNIQUE_KEY_SAVE_FIRST'));
					}

					// Load the first tab as active
					if(self.tabnav().length > 0) {
						var coreTab = self.tabnav().find('a[data-tabname="core"]');

						if(coreTab.length > 0) {
							coreTab.tab('show');
						} else {
							self.tabnav().find('a:first').tab('show');
						}
					}

					self.populateConfig();

					// Get the config height for css fix
					var configHeight = self.element.height();

					$Parent.wrap().css('padding-bottom', configHeight + 'px');

					$Parent.trigger('configLoaded');
				},

				'{close} click': function(el, ev) {
					self.closeConfig();
				},

				'{done} click': function(el, ev) {
					self.closeConfig();
				},

				closeConfig: function() {
					var values = self.populateConfig();

					// Check through the values
					var state = self.checkConfig(values);

					if(state) {
						self.item.updateHtml(self.form().html());

						self.item.content().trigger('onConfigSave', [values]);

						self.element.remove();

						$Config = null;

						$Parent.trigger('doneConfiguring');
					} else {
						EasySocial.dialog({
							content: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_VALUES'),
							width: 400,
							height: 100
						});
					}
				},

				'{parent} loadingConfig': function(el, ev, header) {

					// Set the config header
					if(header !== undefined && header != 'field' )
					{
						var headerText = $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_CONFIGURE_' + header.toUpperCase());
						self.header().html(headerText);
					}

					// Show the config panel
					self.config().show();

					// Hide the close button first
					self.close().hide();

					// Set the loading state
					self.form().html($.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_ITEM_CONFIG_LOADING'));
				},

				'{parent} configLoaded': function(el, ev) {
					self.close().show();
				},

				'{param} change': function(el) {
					self.paramChanged(el);
				},

				'{param} keyup': function(el) {
					self.paramChanged(el);
				},

				paramChanged: function(el) {
					var name = el.data('name'),
						value = self.getConfigValue(name);

					var field = self.item.appParams[name];

					// Manually convert boolean field into boolean value for toggle to work properly
					if(field.type === 'boolean') {
						value = !!value;
					}

					self.item.fieldItem().trigger('onConfigChange', [name, value]);

					$Parent.customFieldChanged();
				},

				getConfigValue: function(name) {
					var field = self.item.appParams[name],
						// element = self.param('[data-fields-config-param-field-' + name +']');
						element = self.param().filterBy('name', name);

					if(element.length === 0) {
						return undefined;
					}

					var values = '';

					switch(field.type) {
						case 'choices':
							values = [];

							$.each(element.find('li'), function(i, choice) {
								choice = $(choice);

								var titleField = choice.find('[data-fields-config-param-choice-title]'),
									valueField = choice.find('[data-fields-config-param-choice-value]'),
									defaultField = choice.find('[data-fields-config-param-choice-default]');

								values.push({
									'id': choice.data('id'),
									'title': titleField.val(),
									'value': valueField.val(),
									'default': defaultField.val()
								});

								titleField.attr('value', titleField.val());
								valueField.attr('value', valueField.val());
								defaultField.attr('value', defaultField.val());
							});
						break;

						case 'boolean':
							var tmp = element.val();

							values = (tmp === 'true' || tmp === '1' || tmp === 1) ? 1 : 0;

							element.attr('value', values);
						break;

						case 'checkbox':
							values = [];
							$.each(field.option, function(k, option) {
								var checkbox = element.filter('[data-fields-config-param-option-' + option.value + ']');

								if(checkbox.length > 0 && checkbox.is(':checked')) {
									values.push(option.value);

									checkbox.attr('checked', 'checked');
								} else {
									checkbox.removeAttr('checked');
								}
							});
						break;

						case 'list':
						case 'select':
						case 'dropdown':
							values = element.length > 0 ? element.val() : field["default"] || '';

							element.find('option').prop('selected', false);

							element.find('option[value="' + values + '"]').prop('selected', true);
						break;

						case 'input':
					case 'text':
						default:
							values = element.length > 0 ? element.val() : field["default"] || '';

							element.attr('value', values);
						break;
					}

					return values;
				},

				populateConfig: function() {
					var data = {};

					$.each(self.item.appParams, function(name, field) {
						var value = self.getConfigValue(name);

						if(value === undefined) {
							// If getConfigValue returns undefined, means this field is not found, then skip to the next field
							return false;
						}

						data[name] = value;
					});

					self.item.trigger('onPopulateConfig', [data]);

					return data;
				},

				checkConfig: function(values) {
					if(values === undefined) {
						values = self.populateConfig();
					}

					// Perform custom checks here
					var state = true;

					$.each(values, function(name, value) {
						var field = self.item.appParams[name];

						switch(field.type) {
							// custom check for choices
							case 'choices':
								// Get all the values first
								var choiceValues = [];

								$.each(value, function(i, choice) {
									if($.isEmpty(choice.value) && !$.isEmpty(choice.title)) {
										choice.value = choice.title.toLowerCase().replace(' ', '');
									}

									if(!$.isEmpty(choice.value) && $.inArray(choice.value, choiceValues) > -1) {
										state = false;
										return false;
									}

									choiceValues.push(choice.value);

									// if((!$.isEmpty(choice.title) && $.isEmpty(choice.value)) || ($.isEmpty(choice.title) && !$.isEmpty(choice.value))) {
									// 	state = false;
									// 	return false;
									// }
								});
							break;
						}

						if(state === false) {
							return false;
						}
					});

					return state;
				},

				'{parent} fieldDeleted': function() {
					if(self.state) {
						$Parent.trigger('doneConfiguring');
					}
				},

				'{parent} pageDeleted': function() {
					if(self.state) {
						$Parent.trigger('doneConfiguring');
					}
				},

				'{parent} pageAdded': function() {
					if(self.state) {
						$Parent.trigger('doneConfiguring');
					}
				},

				'{parent} pageChanged': function() {
					if(self.state) {
						$Parent.trigger('doneConfiguring');
					}
				}
			}
		});

		/* Steps Controller */
		EasySocial.Controller('Fields.Steps', {
			defaultOptions: {
				'{steps}'	: '[data-fields-step]',

				// The step item.
				'{step}'	: '[data-fields-step-item]',

				// The link of each step.
				'{stepLink}': '[data-fields-step-item-link]',

				// The add step button
				'{add}'		: '[data-fields-step-add]',

				view: {
					stepItem: 'admin/profiles/fields/step.item'
				}
			}
		}, function(self) {

			return {
				state: $.Deferred(),

				init: function() {
					self.ready();
				},

				ready: function() {
					self.state.resolve();
				},

				// Delayed init
				'{parent} controllersReady': function() {
					self.initSort();
				},

				initSort: function() {
					self.steps().sortable({
						items: self.step.selector,
						placeholder: 'ui-state-highlight',
						cursor: 'move',
						helper: 'clone',
						forceHelperSize: true,
						stop: function() {
							// Manually remove all the freezing tooltip due to conflict between bootstrap tooltip and jquery sortable
							$('.tooltip-es').remove();

							// Mark as changed
							$Parent.customFieldChanged();
						}
					});
				},

				'{parent} pageDeleted': function(el, event, uid) {
					self.deleteStep(uid);

					// Load the first step as the active page
					if($Steps.step().length > 0) {
						$Steps.stepLink(':first').tab('show');
					}
				},

				'{step} click': function(el, ev) {
					if(!el.hasClass('active')) {
						var id = el.data('id');
						$Parent.trigger('pageChanged', [$Editor.getPage(id), id]);
					}
				},

				/**
				 * Creates a new step.
				 */
				'{add} click': function() {
					// Generate an unique id to link between step and page
					var stepuid = $.uid('step');

					// Add a new step progress at the progress list.
					self.addStep(stepuid);

					// Add a new page form.
					$Editor.addPage(stepuid);

					// Go to the last page automatically since the last page would be the item that is created.
					self.stepLink(':last').tab('show');
				},

				addStep: function(uid) {
					// Always add new step before before the add button
					self.add().before(self.view.stepItem({
						uid: uid
					}));

				},

				getStep: function(uid) {
					return self.step().filterBy('id', uid);
				},

				getStepLink: function(uid) {
					return self.stepLink().filterBy('id', uid);
				},

				deleteStep: function(uid) {
					self.getStep(uid).remove();
				},

				getCurrentStep: function() {
					return self.step('.active');
				},

				currentStepIndex: function() {
					return self.step().index(self.step('.active')) + 1;
				},

				updateResult: function(sequence, newid) {
					var step = self.step(':eq(' + sequence + ')');

					if(step.data('id') != newid) {
						var oldid = step.data('id');

						step.removeAttr('data-fields-step-item-' + oldid);

						step.attr('data-fields-step-item-' + newid, true);

						step.data('id', newid);

						step.attr('data-id', newid);

						var stepLink = self.stepLink().eq(sequence);

						stepLink.removeAttr('data-fields-step-item-link-' + oldid);

						stepLink.attr('data-fields-step-item-link-' + newid, true);

						stepLink.attr('href', '#formStep_' + newid);
					}
				},

				toObject: function() {
					var data = [];

					$.each(self.stepLink(), function(i, step) {
						step = $(step);

						data.push({
							uid: step.data('id'),
							title: step.text(),
							description: step.attr('data-original-title')
						});
					});

					return data;
				}
			}
		});

		/* Editor Controller */
		EasySocial.Controller('Fields.Editor', {
			defaultOptions: {
				'{editor}'	: '[data-fields-editor]',

				'{page}'	: '[data-fields-editor-page]',

				'{items}'	: '[data-fields-editor-page-items]',
				'{item}'	: '[data-fields-editor-page-item]',

				view: {
					editorPage: 'admin/profiles/fields/editor.page'
				}
			}
		}, function(self) {
			return {
				state: $.Deferred(),

				init: function() {
					self.ready();
				},

				ready: function() {
					self.state.resolve();
				},

				'{parent} controllersReady': function() {
					// Implements page controller to all pages
					self.page().addController('EasySocial.Controller.Fields.Editor.Page');
				},

				/**
				 * Returns the current page's controller
				 */
				currentPage: function() {
					return self.page('.active').controller();
				},

				/**
				 * Creates a new page container.
				 */
				addPage: function(uid) {
					// Create a new page item
					var newPage = self.view.editorPage({
						uid: uid
					});

					// Initialize the page controller
					newPage.addController('EasySocial.Controller.Fields.Editor.Page', {
						uid: uid,
						newpage: true,
					});

					// Append the new page
					// self.pages().append(newPage.element);
					self.editor().append(newPage);

					// Trigger pageAdded event on all the pages
					self.page().trigger('pageAdded', [newPage, uid]);

					$Parent.customFieldChanged();
				},

				/**
				 * Returns a page controller container based on uid
				 */
				getPage: function(uid) {
					return self.page().filterBy('id', uid).controller();
				},

				/**
				 * Carry out the necessary action when form is saving
				 */
				'{parent} saving': function(el, event) {
					self.element.addClass('saving');
				},

				/**
				 * Carry out the necessary action when form is saved
				 */
				'{parent} saved': function(el, event, state) {
					// If state is false, this means error during saving
					if(state === false) {
						// TODO: Dialog box needed
					}

					self.element.removeClass('saving');
				}
			}
		});

		/* Editor Page Controller */
		EasySocial.Controller('Fields.Editor.Page', {
			defaultOptions: {
				// This is the stepid stored in the db
				pageid						: 0,

				// This is the unique id generated if the page is a new page
				uid							: 0,

				newpage						: false,

				'{items}'					: '[data-fields-editor-page-items]',
				'{item}'					: '[data-fields-editor-page-item]',

				'{pageHeader}'				: '[data-fields-editor-page-header]',

				// $Config compatibility
				'{content}'					: '[data-fields-editor-page-header]',
				'{fieldItem}'					: '[data-fields-editor-page-header]',

				'{pageTitle}'				: '[data-fields-editor-page-title]',
				'{pageDescription}'			: '[data-fields-editor-page-description]',

				'{inputTitle}'				: '[data-fields-editor-page-title-input]',
				'{inputDescription}'		: '[data-fields-editor-page-description-input]',

				'{pageVisibleRegistration}'	: '[data-fields-editor-page-visible-registration]',
				'{pageVisibleEdit}'			: '[data-fields-editor-page-visible-edit]',
				'{pageVisibleView}'			: '[data-fields-editor-page-visible-view]',
				'{pageDelete}'				: '[data-fields-editor-page-delete]',
				'{pageEdit}'				: '[data-fields-editor-page-edit]',
				'{pageInfo}'				: '[data-fields-editor-page-info]',
				'{pageInfoDone}'			: '[data-fields-editor-page-done]',

				view: {
					editorItem: 'admin/profiles/fields/editor.item'
				}
			}
		}, function(self) {

			return {
				init: function() {

					// Assign uid as pageid if this is not a new page
					if(!self.options.newpage)
					{
						self.options.uid = self.options.pageid = self.element.data('id');
					}

					// Register self into Pages registry
					self.registerPage();

					self.item().addController('EasySocial.Controller.Fields.Editor.Item', {
						pageid: self.options.uid
					});

					// Check for delete button state
					self.checkPageDeleteButton();

					// Init the sorting
					self.initSort();
				},

				// Keep a registry of current page's fields
				fields: {},

				getStep: function() {
					return $Steps.getStep(self.options.uid);
				},

				registerPage: function() {
					$Pages[self.options.uid] = self;
				},

				initSort: function() {
					self.items().sortable({
						items: self.item.selector,
						handle: '[data-fields-editor-page-item-handle]',
						placeholder: 'ui-state-highlight',
						cursor: 'move',
						helper: 'clone',
						forceHelperSize: true,
						stop: function(event, ui) {
							if(ui.item.is($Browser.item.selector)) {
								var appid = ui.item.data('id');

								// Create a placeholder first
								var placeholder = self.createPlaceholder();
								ui.item.replaceWith(placeholder);

								// Create new field and let it replace the placeholder
								self.createNewField(appid, placeholder);
							}

							// Mark change
							$Parent.customFieldChanged();
						}
					});
				},

				addNewField: function(appid) {
					// Append a placeholder first
					var placeholder = self.createPlaceholder();
					self.items().append(placeholder);

					$.scrollTo(placeholder, 200);

					// Create new field and let new field replace the placeholder
					self.createNewField(appid, placeholder);

					$Parent.customFieldChanged();
				},

				createPlaceholder: function() {
					// Generate a uid first
					var uid = $.uid('newfield');

					// Generate a placeholder
					var placeholder = self.view.editorItem({
						uid: uid
					});

					return placeholder;
				},

				createNewField: function(appid, placeholder) {
					// Trigger fieldAdded event
					$Parent.trigger('fieldAdded', [appid]);

					// get the html asyncly
					self.getFieldHtml(appid)
						.done(function(html) {
							// Third parameter set to true to preserve script tags
							html = $.parseHTML(html, document, true);

							// Wrap the whole parsed html as jquery object
							html = $(html);

							// Replace the original loading placeholder with the html object
							placeholder.replaceWith(html);

							// Retrieve the main div to implement item controller
							var div = html.filter('[data-appid="' + appid + '"]');

							// Implement the item controller
							div.addController('EasySocial.Controller.Fields.Editor.Item', {
								controller: {
									page: self
								},

								appid: appid,
								pageid: self.options.uid,
								newfield: true,
							});
						}).fail(function(msg) {
							placeholder.html(msg);
						});
				},

				getFieldHtml: function(appid) {
					var state = $.Deferred();

					if($Apps[appid].html === undefined) {
						EasySocial.ajax('admin/controllers/fields/renderSample', {
							appid: appid,
							profileid: $Parent.options.id,
							group: $Parent.options.group
						}).done(function(html) {
							$Apps[appid].html = html;

							state.resolve(html);
						}).fail(function(msg) {
							state.reject(msg);
						});
					} else {
						state.resolve($Apps[appid].html);
					}

					return state;
				},

				'{pageHeader} click': function(el, event) {
					var clickedTarget = $(event.target);

					if(clickedTarget.not('[data-fields-editor-page-delete]') && !el.hasClass('editting')) {

						if($Config && $Config.state) {

							var state = $Config.checkConfig();

							// Remove itself from other field
							if(state) {
								$Config.closeConfig();
							} else {
								EasySocial.dialog({
									content: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_VALUES'),
									width: 400,
									height: 100
								});

								return;
							}
						}

						self.loadConfiguration();
					}
				},

				loadConfiguration: function() {
					$Parent.loadConfiguration(self, 'page');

					self.pageHeader().addClass('editting');

					self.getPageConfig()
						.done(function() {
							var pageConfig = $(self.html);

							$Config.load(pageConfig);
						})
						.fail(function(msg) {
							$Config.load(msg);
						});
				},

				updateHtml: function(html) {
					self.html = html;
				},

				getPageConfig: function() {
					var state = $.Deferred();

					if(!$.isEmptyObject(self.params)) {
						state.resolve();
					} else {
						EasySocial.ajax('admin/controllers/fields/renderPageConfig', {
							pageid: self.options.pageid
						})
						.done(function(params, values, html) {
							self.params = params;
							self.values = values;
							self.html = html;

							// Compatibility with $Config
							self.appParams = params;

							state.resolve();
						})
						.fail(function(msg) {
							state.reject(msg);
						});
					}

					return state;
				},

				getConfigValues: function() {
					return self.values;
				},

				'{fieldItem} onConfigChange': function(el, ev, name, value) {

					self.values[name] = value;

					var step = $Steps.getStepLink(self.options.uid);


					if(name === 'title') {

						step.text(value);

						self.pageTitle().html(value);
					}

					if(name === 'description') {
						// Used attr('data-original-title') instead of data('original-title') because the tooltip reads the attribute directly while data() adds the value back as a jQuery data on to the element
						step.attr('data-original-title', value);

						self.pageDescription().html(value);
					}

					$Parent.customFieldChanged();
				},

				'{pageDelete} click': function(el) {
					if(el.enabled()) {
						el.disabled(true);

						// If it is the last page, then it shouldn't delete.
						if($Editor.page().length == 1) {
							el.enabled(true);

							// @TODO: error box needed

							return false;
						}

						EasySocial.dialog({
							width: 400,
							height: 150,
							title: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_TITLE'),
							content: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_CONFIRMATION'),
							showOverlay: false,
							buttons: [
								{
									// CANCEL button
									name: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_CANCEL'),
									classNames: 'btn btn-es',
									click: function() {
										el.enabled(true);
										EasySocial.dialog().close();
									}
								},
								{
									// DELETE button
									name: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_CONFIRM'),
									classNames: 'btn btn-es-danger',
									click: function() {
										// Update the dialog content first
										EasySocial.dialog().update({
											content: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_DELETING')
										});

										// Start deleting the page
										self.deletePage();

										// Close the dialog
										EasySocial.dialog().close();
									}
								}
							]
						});
					}
				},

				deletePage: function() {
					// Trigger pageDeleted event
					self.item().trigger('pageDeleted');
					$Parent.trigger('pageDeleted', [self.options.uid]);

					// Remove self from $Pages registry
					delete $Pages[self.options.uid];

					// Add self into $DeletedPages registry
					if(!self.options.newpage) {
						$Deleted.pages.push(self.options.uid);
					}

					// Removed current page
					self.element.remove();

					// Check for delete button
					$.each($Editor.page(), function(i, page) {
						$(page).controller().checkPageDeleteButton();
					});

					$Parent.customFieldChanged();
				},

				_export: function() {
					var fields = [];

					$.each(self.item(), function(j, item) {
						item = $(item).controller();

						if(item !== undefined)
						{
							fields.push(item._export());
						}
					});

					var data = {
						fields: fields,
						newpage: self.options.newpage,
						id: self.options.uid
					}

					if(self.values !== undefined) {
						var data = $.extend(data, self.values);
					}

					return data;
				},

				updateResult: function(oldid, data) {
					if(self.options.newpage) {

						// Update the page element id attribute (to correspond with the step tab structure)
						self.element.attr('id', 'formStep_' + data.id);

						// Remove the old selector and add in the new selector
						self.element.removeAttr('data-fields-editor-page-' + oldid);
						self.element.attr('data-fields-editor-page-' + data.id, true);

						// Assign pageid to self.options
						self.options.pageid = self.options.uid = data.id;

						// Update the $Pages registry
						$Pages[data.id] = $.extend(true, {}, $Pages[oldid]);
						delete $Pages[oldid];

						// Since the page has been saved, then it should not be a new page anymore
						self.options.newpage = false;
					}

					if(data.fields !== undefined) {
						$.each(data.fields, function(i, field) {
							// Go by sequence
							var item = self.item().eq(i).controller();

							item.updateResult(field);
						});
					}
				},

				/**
				 * Carry out necessary action when a new page is added
				 */
				'{self} pageAdded': function(el, event, page) {
					self.checkPageDeleteButton();

					$Parent.customFieldChanged();
				},

				checkPageDeleteButton: function() {
					if($Editor.page().length > 1) {
						self.pageDelete().show();
					} else {
						self.pageDelete().hide();
					}
				},

				'{parent} loadingConfig': function() {
					self.pageHeader().removeClass('editting');
					self.item().removeClass('editting');
				},

				'{parent} doneConfiguring': function() {
					self.pageHeader().removeClass('editting');
					self.item().removeClass('editting');
				}
			}
		});

		/* Editor Item Controller */
		EasySocial.Controller('Fields.Editor.Item', {
			defaultOptions: {
				appid			: 0,
				fieldid			: 0,
				pageid			: 0,

				newfield		: false,

				'{edit}'		: '[data-fields-editor-page-item-edit]',
				'{deleteButton}': '[data-fields-editor-page-item-delete]',
				'{moveButton}'	: '[data-fields-editor-page-item-move]',
				'{content}'		: '[data-fields-editor-page-item-content]',
				'{fieldItem}'	: '[data-field]',

				'{config}'		: '[data-fields-config]',

				'{closeConfig}'	: '[data-fields-config-close]',

				view: {
					moveDialog: 'admin/profiles/fields/dialog.move'
				}
			}
		}, function(self) {

			return {
				app: {},

				field: {
					id: 0,
					appid: 0,
					params: {}
				},

				state: $.Deferred(),

				appParams: {},

				init: function() {

					// Check if it has a valid appid or not
					if(self.options.appid == 0 && self.element.data('appid') !== undefined) {
						self.options.appid = self.element.data('appid');
					}

					// Check if this field's app is a valid app or not
					if($Apps[self.options.appid] !== undefined) {

						// Link the reference copy to self.app from $Apps registry
						self.app = $Apps[self.options.appid];
					}

					// Check if it has fieldid or not
					if(self.options.fieldid == 0 && self.element.data('id') !== undefined) {
						self.options.fieldid = self.element.data('id');
					}

					// Register $Fields
					self.registerFields();

					// Generate a unique id to identify configuration tabs
					self.uniqueid = $.uid(self.app.id + '_');

					self.loadedInit();
				},

				registerFields: function() {
					if(self.options.fieldid != 0) {
						$Fields[self.options.fieldid] = {
							id: self.options.fieldid,
							appid: self.options.appid,
							params: self.field.params || {}
						}

						// Link the reference copy to self.field if this is an existing field
						self.field = $Fields[self.options.fieldid];
					}
				},

				loadedInit: function() {

					// Implement field base controller
					self.element.addController('EasySocial.Controller.Field.Base', {
						mode: 'sample',
						element: self.app.element
					});

					// Implement a common config controller on the item
					self.content().addController('EasySocial.Controller.Fields.Editor.Item.Config');
				},

				// export data during save
				_export: function() {
					// Call checkout function from browser to check if all core apps has been used
					$Browser.checkout(self.options.appid);

					// Initialise export data with appid and fieldid
					// If fieldid == 0, means it is a new field
					// If appid == 0, means it is a non valid application
					var exportData 	= {
						"fieldid"	: self.options.fieldid,
						"appid"		: self.options.appid,
						"newfield"	: self.options.newfield
					};

					// Add in parameter values into export data
					exportData = $.extend(exportData, self.expandConfig(self.field.params));

					return exportData;
				},

				'{self} click': function(el, event) {
					var clickedElement = $(event.target);

					// Click on anywhere of the element except the delete button to load the configuration panel
					if(!clickedElement.is(self.deleteButton.selector) && !clickedElement.is(self.moveButton.selector) && !clickedElement.is(self.config.selector) && !clickedElement.is(self.closeConfig.selector) && !el.hasClass('editting')) {

						// If config state is true, means it is editting other field
						if($Config && $Config.state) {

							var state = $Config.checkConfig();

							// Remove itself from other field
							if(state) {
								$Config.closeConfig();
							} else {
								EasySocial.dialog({
									content: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_VALUES'),
									width: 400,
									height: 100
								});

								return;
							}
						}

						self.loadConfiguration();
					}
				},

				loadConfiguration: function() {
					// $Parent.trigger('loadingConfig', ['field']);
					$Parent.loadConfiguration(self, 'field');

					self.element.addClass('editting');

					self.getAppParams()
						.done(function() {

							var html = $(self.field.html);

							// Pass objects to config panel
							$Config.load(html);
						})
						.fail(function() {

						});
				},

				updateHtml: function(html) {
					self.field.html = html;
				},

				/**
				 * Get field parameters from the server.
				 */
				getAppParams: function() {
					var state = $.Deferred();

					if(self.field.html) {
						state.resolve();
					} else {
						EasySocial.ajax('admin/controllers/fields/renderConfiguration', {
							// Send the application id
							appid		: self.options.appid,

							// Send the field id.
							fieldid		: self.options.fieldid
						})
						.done(function(params, values, html) {

							self.app.params = params;

							self.field.params = values;

							self.field.html = html;

							// This will keep a flat list of the available parameters
							self.populateAppParams();

							state.resolve();
						})
						.fail(function(msg) {
							state.reject(msg);
						});
					}

					return state;
				},

				/**
				 * Populate parameters data
				 */
				populateAppParams: function() {
					$.each(self.app.params, function(i, paramProperties) {
						$.each(paramProperties.fields, function(name, field) {

							if(field.subfields) {
								$.each(field.subfields, function(subname, subfield) {
									self.appParams[name + '_' + subname] = subfield;
								});
							} else {
								self.appParams[name] = field;
							}
						});
					});
				},

				/**
				 * To return the field parameters value
				 */
				getConfigValues: function() {
					return self.field.params;
				},

				/**
				 * Converts flatten config data to expanded data for saving purposes
				 */
				expandConfig: function() {
					var newData = {
						params: {},
						choices: {}
					};

					$.each(self.field.params, function(name, value) {

						var field = self.appParams[name];

						if(!field) {
							return false;
						}

						var type = field.type == 'choices' ? 'choices' : 'params';

						newData[type][name] = value;
					});

					if(self.options.newfield) {
						newData.params.unique_key = '';
					}

					return newData;
				},

				'{moveButton} click': function(el) {
					var pages = $Steps.toObject(),
						currentPageId = el.parents($Editor.page.selector).data('id'),
						newPages = [];

					$.each(pages, function(i, page) {
						if(page.uid != currentPageId) {
							newPages.push(page);
						}
					});

					EasySocial.dialog({
						content: self.view.moveDialog(true, {
							pages: newPages
						}),
						selectors: {
							"{selection}"		: "[data-move-selection]",
							"{confirmButton}"	: "[data-move-confirm]",
							"{cancelButton}"	: "[data-move-cancel]"
						},
						bindings: {
							"{cancelButton} click" : function()
							{
								// Close the dialog
								EasySocial.dialog().close();
							},

							"{confirmButton} click": function() {
								var id = this.selection().val(),
									page = $Editor.getPage(id);

								page.items().append(self.element);

								$Parent.customFieldChanged();

								EasySocial.dialog().close();
							}
						}
					});
				},

				'{deleteButton} click': function(el) {

					if(el.enabled()) {
						el.disabled(true);

						EasySocial.dialog(
						{
							width: 400,
							height: 150,
							title: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_TITLE'),
							content: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_CONFIRMATION'),
							showOverlay: false,
							buttons: [
								{
									// CANCEL button
									name: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_CANCEL'),
									classNames: 'btn btn-es btn-sm',
									click: function() {
										el.enabled(true);
										EasySocial.dialog().close();
									}
								},
								{
									// DELETE button
									name: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_PAGE_DIALOG_CONFIRM'),
									classNames: 'btn btn-es-danger btn-sm',
									click: function() {

										// Update the dialog content first
										EasySocial.dialog().update({
											content: $.language('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_DELETE_ITEM_DIALOG_DELETING')
										});

										// Start deleting the field
										self.deleteField();

										// Close the dialog
										EasySocial.dialog().close();
									}
								}
							]
						});
					}
				},

				deleteField: function() {
					// Trigger fieldDeleted event
					$Parent.trigger('fieldDeleted', [self.options.appid, self.options.fieldid]);

					if(!self.options.newfield) {

						// Delete fields in registry
						delete $Fields[self.options.fieldid];

						// Add this field into the deleted registry
						$Deleted.fields.push(self.options.fieldid);
					}

					// Remove field element
					self.element.remove();

					$Parent.customFieldChanged();
				},

				'{self} pageDeleted': function() {
					self.deleteField();
				},

				'{content} onConfigChange': function(el, event, name, value) {
					self.field.params[name] = value;
				},

				'{self} onPopulateConfig': function(el, event, values) {
					self.field.params = values;
				},

				// Unused
				updateResult: function(data) {
					// Update the unique key
					self.field.params.unique_key = data.unique_key;
					self.itemParam('[data-fields-config-param-field-unique_key]').val(data.unique_key);

					// If this is a new field, the some things need to be updated
					if(self.options.newfield) {
						// Set newfield to false because post-save, this will no longer be a new field
						self.options.newfield = false;

						// Set the fieldid
						self.options.fieldid = data.fieldid;
						self.element.data('id', data.fieldid);

						// Enable the unique key field
						self.itemParam('[data-fields-editor-page-item-param-field-unique_key]').removeAttr('disabled');

						// Register into $Fields registry
						self.registerFields();
					}

					if(data.choices !== undefined) {
						$.each(data.choices, function(name, choices) {
							var element = self.itemParam('[data-fields-config-param-field-' + name + ']');

							$.each(choices, function(i, choice) {
								// Go by sequence
								var item = element.find('li').eq(i);

								if(!item.data('id')) {
									item.attr('data-id', choice.id);
									item.data('id', choice.id);
								}
							});
						});
					}
				}
			}
		});

		/* Config Choices Controller */
		EasySocial.Controller( 'Config.Choices', {
			defaultOptions: {
				'{choiceItems}'	: '[data-fields-config-param-choice]',

				unique			: 1
			}
		}, function(self) {

			return {
				init: function() {
					self.options.unique = self.element.data('unique') !== undefined ? self.element.data('unique') : 1;

					self.choiceItems().implement( EasySocial.Controller.Config.Choices.Choice, {
						controller: {
							'item': self.item,
							'choices': self
						}
					});

					self.initSortable();
				},

				initSortable: function() {
					self.element.sortable({
						items: self.choiceItems.selector,
						placeholder: 'ui-state-highlight',
						cursor: 'move',
						forceHelperSize: true,
						handle: '[data-fields-config-param-choice-drag]',
						stop: function() {
							// Manually remove all the freezing tooltip due to conflict between bootstrap tooltip and jquery sortable
							$('.tooltip-es').remove();

							// Mark change
							$Parent.customFieldChanged();
						}
					});
				}
			}
		});

		/* Config Choices Choice Controller */
		EasySocial.Controller( 'Config.Choices.Choice', {
			defaultOptions: {
				'{choiceValue}'		: '[data-fields-config-param-choice-value]',
				'{choiceTitle}'		: '[data-fields-config-param-choice-title]',
				'{choiceDefault}'	: '[data-fields-config-param-choice-default]',
				'{addChoice}'		: '[data-fields-config-param-choice-add]',
				'{removeChoice}'	: '[data-fields-config-param-choice-remove]',
				'{setDefault}'		: '[data-fields-config-param-choice-setdefault]',

				'{defaultIcon}'		: '[data-fields-config-param-choice-defaulticon]'
			}
		}, function(self) {

			return {

				init: function() {
				},

				'{choiceTitle} keyup': $._.debounce(function(el, event) {
					var index = self.element.index();

					self.item.fieldItem().trigger('onChoiceTitleChanged', [index, el.val()]);

					$Parent.customFieldChanged();
				}, 500),

				'{choiceValue} keyup': $._.debounce(function(el, event) {
					var index = self.element.index();

					self.item.fieldItem().trigger('onChoiceValueChanged', [index, el.val()]);

					$Parent.customFieldChanged();
				}, 500),

				'{addChoice} click' : function() {
					// Clone a new item from current clicked element
					var newItem = self.element.clone();

					// Let's leave the value blank by default.
					var inputElement = newItem.find('input[type="text"]');

					inputElement.attr('value', '');

					inputElement.val('');

					// Set the default as 0 and the icon to unfeatured
					var inputDefault = newItem.find('input[type="hidden"]');

					inputDefault.attr('value', 0);

					inputDefault.val(0);

					var defaultLabel = newItem.find('[data-fields-config-param-choice-defaulticon]');

					defaultLabel.removeClass('es-state-featured').addClass('es-state-default');

					// set id = 0
					newItem.attr('data-id', 0);
					newItem.data('id', 0);

					// Implement the controller for this choice
					newItem.implement(EasySocial.Controller.Config.Choices.Choice, {
						controller: {
							'item': self.item,
							'choices': self.choices
						}
					});

					// Append this item
					self.element.after(newItem);

					// Get the index of the new item
					var index = newItem.index();

					self.item.fieldItem().trigger('onChoiceAdded', [index]);

					$Parent.customFieldChanged();
				},

				'{removeChoice} click' : function() {
					// We need to minus one because we're trying to remove ourself also.
					var remaining = self.choices.choiceItems().length - 1;

					// If this is the last item, we wouldn't want to allow the last item to be removed.
					if( remaining >= 1 ) {
						// Get the index of the new item
						var index = self.element.index();

						self.item.fieldItem().trigger('onChoiceRemoved', [index]);

						self.element.remove();

						// Manually remove the tooltip generated on the remove button
						$('.tooltip-es').remove();
					}

					$Parent.customFieldChanged();
				},

				'{setDefault} click': function() {
					var index = self.element.index(),
						title = self.choiceTitle().val(),
						value = self.choiceValue().val();

					self.choices.choiceItems().trigger( 'toggleDefault', [index] );

					self.item.fieldItem().trigger('onChoiceToggleDefault', [index, parseInt(self.choiceDefault().val())]);

					$Parent.customFieldChanged();
				},

				'{self} toggleDefault': function(el, ev, i) {
					var index = self.element.index(),
						value = parseInt(self.choiceDefault().val());

					if(index === i) {
						if(value) {
							self.defaultIcon()
								.removeClass('es-state-featured')
								.addClass('es-state-default');

							self.choiceDefault().val(0);
						} else {
							self.defaultIcon()
								.removeClass('es-state-default')
								.addClass('es-state-featured');

							self.choiceDefault().val(1);
						}
					} else {
						if(self.choices.options.unique) {
							self.defaultIcon()
								.removeClass('es-state-featured')
								.addClass('es-state-default');

							self.choiceDefault().val(0);
						}
					}
				}
			}
		});

		/* Editor Item Common Controller */
		// This is the common item config controller to implement on item
		EasySocial.Controller('Fields.Editor.Item.Config', {
			defaultOptions: {
				'{required}'			: '[data-required]',

				'{title}'				: '[data-title]',
				'{description}'			: '[data-description]',

				'{displayTitle}'		: '[data-display-title]',
				'{displayDescription}'	: '[data-display-description]'
			}
		}, function(self) {
			return {
				init: function() {

				},

				'{self} onConfigChange': function(el, event, name, value) {
					switch(name) {
						case 'display_title':
							self.displayTitle().toggle(!!value);
						break;

						case 'title':
							self.title().text(value);
						break;

						case 'display_description':
							self.displayDescription().toggle(!!value);
						break;

						case 'description':
							self.description().text(value);
						break;

						case 'required':
							self.required().toggle(!!value);
						break;
					}
				}
			}
		});

		module.resolve();
	}); // require end

}); // module end
