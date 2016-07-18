EasySocial.module("story", function($){

var module = this;

// This speeds up story initialization during development mode.
// Do not add this to the manifest file.
EasySocial.require()
	.language(
		"COM_EASYSOCIAL_LOCATION_PERMISSION_ERROR",
		"COM_EASYSOCIAL_STREAM_META_JOINER"
	)
	.view(
		"apps/user/links/story/attachment.item",
		"site/location/story.suggestion",
		"site/albums/upload.item",
		"site/friends/suggest.item",
		"site/friends/suggest.hint.search",
		"site/friends/suggest.hint.empty",
		"site/hashtags/suggest.item",
		"site/hashtags/suggest.hint.search",
		"site/hashtags/suggest.hint.empty"
	)
	.done();

// Non-essential dependencies
EasySocial.require()
	.script(
		"story/locations",
		"story/friends",
		"story/mood"
	);

EasySocial.require()
	.library("mentions", "placeholder")
	.script('site/stream/item')
	.language(
		"COM_EASYSOCIAL_STORY_SUBMIT_ERROR",
		"COM_EASYSOCIAL_STORY_CONTENT_EMPTY",
		"COM_EASYSOCIAL_STORY_NOT_ON_STREAM_FILTER"
	).done(function(){

		EasySocial.Controller("Story", {
			
			defaultOptions: {
				view: {
					suggestItem: "site/friends/suggest.item",
					tagSuggestItem: "site/hashtags/suggest.item"
				},

				plugin: {
					text: {
						id: "text",
						name: "text",
						selector: "[data-story-plugin-name=photos]",
						type: "panel"
					}
				},

				sourceView: null,

				attachment: {
					limit: 1,
					lifo: true
				},

				enterToSubmit: false,

				"{header}"		: "[data-story-header]",
				"{body}"  		: "[data-story-body]",
				"{footer}"		: "[data-story-footer]",

				"{form}"        : "[data-story-form]",
				"{textbox}"     : "[data-story-textbox]",
				"{textField}"   : "[data-story-textField]",
				"{target}"      : "[data-story-target]",

				"{cluster}"      : "[data-story-cluster]",
				"{clusterType}"  : "[data-story-clustertype]",

				"{submitButton}": "[data-story-submit]",
				"{resetButton}" : "[data-story-reset]",
				"{privacyButton}": "[data-story-privacy]",

				"{panelContents}": "[data-story-panel-contents]",
				"{panelContent}": "[data-story-panel-content]",
				"{panelButton}": "[data-story-panel-button]",
				// "{defaultPanel}"	: "[data-story-panel-button-default]",

				//stream listing
				"{streamContainer}": "[data-streams]",
				"{streamItem}"     : "[data-streamItem]",

				"{friends}": "[data-story-friends]",
				"{location}": "[data-story-location]",
				"{mood}": "[data-story-mood]",

				// Mentions
				"{mentionsOverlay}": "[data-mentions-overlay]",

				// Meta
				"{meta}"        : "[data-story-meta]",
				"{metaContents}": "[data-story-meta-contents]",
				"{metaContent}" : "[data-story-meta-content]",
				"{metaButtons}" : "[data-story-meta-buttons]",
				"{metaButton}"  : "[data-story-meta-button]"
			},

			hostname: "story"
		}, function(self, opts, base) { return {

			init: function() {

				// Find out what's my story id
				self.id = base.data("story");

				// Create plugin repository
				$.each(self.options.plugin, function(pluginName, pluginOptions) {

					var plugin = self.plugins[pluginName] = pluginOptions;

					// Pre-count the number of available attachment type
					if (plugin.type=="attachment") self.attachments.max++;

					// Add selector property
					plugin.selector = self.getPluginSelector(pluginName);
				});

				self.setMentionsLayout();

				if (self.friends().length > 0) {
					EasySocial.module("story/friends")
						.done(function(){
							self.addPlugin("friends");
						});
				}

				if (self.location().length > 0) {
					EasySocial.module("story/locations")
						.done(function(){
							self.addPlugin("locations");
						});
				}

				if (self.mood().length > 0) {

					EasySocial.module("story/mood")
						.done(function(){
							self.addPlugin("mood");
						});
				}

				// Remember placeholder value (used by meta)
				self.placeholder = self.textField().attr("placeholder");

				// Duckpunch setMessage
				self._setMessage = self.setMessage;

				self.setMessage = function() {

					// Do not set any messages when story is collapsed or is resizing.
					if (base.hasClass("is-collapsed") || base.hasClass("is-resizing")) {
						return;
					}

					// Remove any previous message group first to avoid stacking error messages.
					this.element
						.find('[data-message-group]')
						.remove();

					self._setMessage.apply(this, arguments);
				};

				// Show placeholder shim for ie9
				if (navigator.userAgent.match(/MSIE 9.0/i)) {
					base.addClass("is-ie");
				}

				self.checkResetButton();

				// Resolve story instance
				$.module("story-" + self.id).resolve(self);
			},

			checkResetButton: function() {

				var textField = self.textField(),
					defaultText = textField.attr("data-default");

				if (defaultText) {
					self.resetButton()
						.toggle(textField.val()!==defaultText);
				}
			},

			"{textField} keyup": $.debounce(function(textField){

				self.checkResetButton();

			}, 250, {leading: true}),

			"{self} click": function(element, event) {

				if ($(event.target).parents().andSelf().filter(self.resetButton()[0]).length > 0) return;
				self.expand();
			},

			"{textField} touchstart": function() {
				self.expand();
			},

			"{textField} keydown": function(textField, event) {
				self.expand();
			},

			"{textField} click": function() {
				self.expand();
			},

			"{textField} mousedown": function(textField, event) {
				self.expand();
			},

			"{textField} keypress": function(textField, event) {

				if (
					// If pressing enter submits form
					opts.enterToSubmit &&
					// And enter key was pressed
					event.keyCode==13 &&
					// Without any meta keys involved
					!(event.shiftKey || event.altKey || event.ctrlKey || event.metaKey)
				) {
					self.save();
					event.preventDefault();
				}
			},

			expand: $.debounce(function() {

				if (base.hasClass("is-expanded") || base.hasClass("is-resizing")) {
					return;
				}

				var transitionEnd   = $.support && $.support.transition && $.support.transition.end,
					transitionEvent = (transitionEnd || "transitionend") + ".es.story",
					finalize = $.debounce(function(){

						base.off(transitionEvent)
							.addClass("is-expanded")
							.removeClass("is-resizing")

						// Executes only once
						self.setMentionsLayout();
						self.submitButton().removeAttr("data-disabled");
						self.textField().focus();
					}, 1);

				if (transitionEnd) {
					base.on(transitionEvent, finalize);
				} else {
					setTimeout(finalize, 600);
				}

				// The CSS transition in this class expands the textarea
				base.removeClass("is-collapsed")
					.addClass("is-resizing");
			}, 1),

			collapse: function() {

				if (base.hasClass("is-collapsed") || base.hasClass("is-resizing")) {
					return;
				}

				base.addClass("is-resizing")
					.removeClass("is-expanded");

				setTimeout(function(){
					base.addClass("is-collapsed")
						.removeClass("is-resizing");
				}, 1);
			},

			reset: function(collapse) {

				self.clear();

				// If there are default values in the textarea, don't collapse
				if (self.textField().val()!=="") {
					return;
				}

				if (collapse) {
					self.collapse();
				}
			},

			setMentionsLayout: function() {

				var textbox = self.textbox(),
					mentions = textbox.controller("mentions");

				if (mentions) {
					mentions.cloneLayout();
					return;
				}

				var body = self.body();

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

												if (!$.isArray(items)) {
													task.reject();
													return;
												}

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

												if (!$.isArray(items)) { 
													task.reject(); 
													return; 
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
								modifier: "es-story-mentions-autocomplete",
								sticky: true,
								shadow: true,
								position: {
									my: 'left top',
									at: 'left bottom',
									of: textbox.parent(),
									collision: 'none'
								},
								size: {
									width: function() {
										return body.width();
									}
								}
							}
						}
					});
			},

			//
			// PLUGINS
			//
			plugins: {},

			getPluginName: function(element) {
				return $(element).data("story-plugin-name");
			},

			getPluginSelector: function(pluginName) {
				return "[data-story-plugin-name=" + pluginName + "]";
			},

			hasPlugin: function(pluginName, pluginType) {

				var plugin = self.plugins[pluginName];

				if (!plugin) return false;

				// Also check for pluginType
				if (pluginType) return (plugin.type===pluginType);

				return true;
			},

			buildPluginSelectors: function(selectorNames, plugin, pluginControllerType) {

				var selectors = {};

				$.each(selectorNames, function(i, selectorName) {

					var selector = self[selectorName].selector + plugin.selector;

					if (pluginControllerType=="function") {
						selectors[selectorName] = function() {
							return self.find(selector);
						};
					} else {
						selectors["{"+selectorName+"}"] = selector;
					}
				});

				return selectors;
			},

			"{self} addPlugin": function(element, event, pluginName, pluginController, pluginOptions, pluginControllerType) {

				// Prevent unregistered plugin from extending onto story
				if (!self.hasPlugin(pluginName))
				{
					return;
				}

				var plugin = self.plugins[pluginName],
					extendedOptions = {};

				// See plugin type and build the necessary options for them
				switch (plugin.type)
				{
					case "panel":
						var panelSelectors = [
							"panelButton",
							"panelContent"
						];
						extendedOptions = self.buildPluginSelectors(panelSelectors, plugin, pluginControllerType);
						break;
				}

				$.extend(pluginOptions, extendedOptions);
			},

			"{self} registerPlugin": function(element, event, pluginName, pluginInstance) {

				// Prevent unregistered plugin from extending onto story
				if (!self.hasPlugin(pluginName)) return;

				var plugin = self.plugins[pluginName];

				plugin.instance = pluginInstance;
			},

			//
			// PANELS
			//

			panels: {},

			currentPanel: "text",

			getPanel: function(pluginName) {

				// If plugin is not a panel, stop.
				if (!self.hasPlugin(pluginName, 'panel')) return;

				var plugin = self.plugins[pluginName];

				// Return existing panel entry if it has been created,
				return self.panels[plugin.name] ||

						// or create panel entry and return it.
						(self.panels[plugin.name] = {
							plugin: plugin,
							button: self.panelButton(plugin.selector),
							content: self.panelContent(plugin.selector)
						});
			},

			activatePanel: function(pluginName) {

				// Get panel
				var panel = self.getPanel(pluginName);

				// If panel does not exist, stop.
				if (!panel) return;

				// Deactivate current panel
				self.deactivatePanel(self.currentPanel);

				// Set plugin as current panel
				self.currentPanel = pluginName;

				var panelContents = self.panelContents();

				// Activate panel container
				panelContents.addClass("active");

				// Activate panel
				panel.button.addClass("active");
				panel.content
					.appendTo(panelContents)
					.addClass("active");

                base.addClass("plugin-" + pluginName);

				// Invoke plugin's activate method if exists
				self.invokePlugin(pluginName, "activatePanel", [panel]);

				// Trigger panel activate event
				self.trigger("activatePanel", [pluginName]);

				// Refocus story form
				self.textField().focus();
			},

			deactivatePanel: function(pluginName) {

				// Get panel
				var panel = self.getPanel(pluginName);

				// If panel does not exist, stop.
				if (!panel) return;

				// Deactivate panel
				panel.button.removeClass("active");
				panel.content.removeClass("active");

                base.removeClass("plugin-" + pluginName);

				// Deactivate panel container
				self.panelContents().removeClass("active");

				// Invoke plugin's deactivate method if exists
				self.invokePlugin(pluginName, "deactivatePanel", [panel]);

				// Trigger panel deactivate event
				self.trigger("deactivatePanel", [pluginName]);
			},

			addPanelCaption: function(pluginName, panelCaption) {

				// Get panel
				var panel = self.getPanel(pluginName);

				// If panel does not exist, stop.
				if (!panel) return;

				panel.button
					.addClass("has-data")
					.find(".with-data").html(panelCaption);
			},

			removePanelCaption: function(pluginName) {

				// Get panel
				var panel = self.getPanel(pluginName);

				// If panel does not exist, stop.
				if (!panel) return;

				panel.button
					.removeClass("has-data")
					.find(".with-data").empty();
			},

			// Triggered when the panel buttons beneath the story footer is clicked
			"{panelButton} click": function(panelButton, event) {

				var pluginName = self.getPluginName(panelButton);

				self.activatePanel(pluginName);
			},

			//
			// SAVING
			//
			saving: false,

			save: function() {

				if (self.saving) {
					return;
				}

				self.saving = true;

				// Create save object
				var save = $.Deferred();
				
				save.data = {};
				save.tasks = [];

				save.addData = function(plugin, props) {

					var pluginName = plugin.options.name,
						pluginType = plugin.options.type;

					if (pluginName !== self.currentPanel) {
						return;
					}


					save.data.attachment = self.currentPanel;

					if ($.isPlainObject(props))
					{
						$.each(props, function(key, val)
						{
							save.data[pluginName + "_" + key] = val;
						});
					}
					else
					{
						save.data[pluginName] = props;
					}
				};

				save.addTask = function(name) {
					var task = $.Deferred();
					task.name = name;
					task.save = save;
					save.tasks.push(task);
					return task;
				};

				save.process = function() {
					if (save.state()==="pending") {
						$.when.apply($, save.tasks)
							.done(function() {

								// If content & attachment is empty, reject.
								if (!$.trim(save.data.content) && !save.data.attachment) {
									save.reject($.language("COM_EASYSOCIAL_STORY_CONTENT_EMPTY"), "warning");
									return;
								}

								save.resolve();
							})
							.fail(save.reject);
					}

					return save;
				};

				// Set the current panel so that the plugins know whether they should intercept
				save.currentPanel = self.currentPanel;

				// Trigger the save event
				self.trigger("save", [save]);

				self.element.addClass("saving");

				save.process()
					.done(function(){
						var mentions = self.textbox().mentions("controller").toArray(),
							hashtags = self.element.data("storyHashtags"),
							hashtags = (hashtags) ? hashtags.split(",") : [],
							nohashtags = false;

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

						self.trigger("beforeSubmit", [save]);


						// then the ajax call to save story.
						EasySocial.ajax("site/controllers/story/create", save.data)
							.done(function(html, id) {

								if (nohashtags) {
									html = self.setMessage($.language("COM_EASYSOCIAL_STORY_NOT_ON_STREAM_FILTER"));
								}
								
								self.trigger("create", [html, id]);
								self.clear();
								self.reset();
							})
							.fail(function(message){
								self.trigger("fail", arguments);
								if (!message) return;
								self.setMessage(message.message, message.type);
							})
							.always(function(){

								self.trigger("afterSubmit", [save]);

								self.element.removeClass("saving");
								self.saving = false;
							});
					})
					.fail(function(message, messageType){

						if (!message) {
							message = $.language("COM_EASYSOCIAL_STORY_SUBMIT_ERROR");
							messageType = "error";
						}

						self.setMessage(message, messageType);
						self.element.removeClass("saving");
						self.saving = false;
					});
			},

			clear: function() {

				// Clear textfield
				self.textField().val("");

				// Clear status messages
				self.clearMessage();

				// Reactivate text panel
				self.activatePanel("text");

				// Deactivate meta
				self.deactivateMeta(self.currentMeta);

				// Trigger clear event
				self.trigger("clear");

				// Reset mentions
				var mentions = self.textbox().mentions("controller");
				mentions.reset();

				self.checkResetButton();

				setTimeout(function(){
					mentions.cloneLayout();
				}, 500);

				// Focus textfield
				self.textField().focus();
			},

			"{self} save": function(element, event, save) {

				var content = self.textField().val(),
					data = save.data;

				data.view 	 = self.options.sourceView;
				data.content = content;
				data.target  = self.target().val();
				data.cluster = self.cluster().val();
				data.clusterType = self.clusterType().val();
				data.privacy = self.find("[data-privacy-hidden]").val();
				data.privacyCustom = self.find("[data-privacy-custom-hidden]").val();

				var mentions = self.textbox().mentions("controller").toArray();

				data.mentions = $.map(mentions, function(mention){
					if (mention.type==="hashtag" && $.isPlainObject(mention.value)) {
						mention.value = mention.value.title.slice(1);
					}
					return JSON.stringify(mention);
				});
			},

			"{submitButton} click": function(submitButton, event) {
				self.save();
			},

			"{resetButton} click": function() {
				self.reset(true);
			},

			//
			// Privacy
			//
			"{privacyButton} click": function(el) {
				setTimeout(function(){
					var isActive = el.find("[data-es-privacy-container]").hasClass("active");
					// self.footer().toggleClass("allow-overflow", isActive);
				}, 1);
			},

			//
			// Meta
			//
			metas: {
				friends: "",
				location: "",
				mood: ""
			},

			currentMeta: null,

			getMeta: function(metaName) {

				var meta = {
					name: metaName,
					button: self.metaButton().filterBy("storyMetaButton", metaName),
					content: self.metaContent().filterBy("storyMetaContent", metaName)
				};

				if (meta.button.length < 1 || meta.content.length < 1) return null;

				return meta;
			},

			activateMeta: function(metaName) {

				var meta = self.getMeta(metaName);

				if (!meta) return;

				// Deactivate current meta
				self.deactivateMeta(self.currentMeta);

				meta.button.addClass("active");

				// Always push meta content to the beginning
				meta.content
					.appendTo(self.metaContents())
					.addClass("active");

				self.currentMeta = metaName;

				self.trigger("activateMeta", [meta]);

				base.addClass("has-meta");
			},

			deactivateMeta: function(metaName) {

				var meta = self.getMeta(metaName);

				if (!meta) return;

				meta.button.removeClass("active");

				meta.content.removeClass("active");

				self.currentMeta = null;

				self.trigger("deactivateMeta", [meta]);

				base.removeClass("has-meta");
			},

			toggleMeta: function(metaName) {

				if (self.currentMeta == metaName) {
					self.deactivateMeta(metaName);
				} else {
					self.activateMeta(metaName);
				}
			},

			getMetaText: function() {

				var metas = self.metas,
					parts = [],
					joiner = $.language("COM_EASYSOCIAL_STREAM_META_JOINER");

				$.each(["friends", "location", "mood"], function(i, type){
					var meta = metas[type];
					if (meta) parts.push(meta);
				});

				return parts.join(joiner);
			},

			setMeta: function(metaName, content) {

				self.metas[metaName] = content;
				self.updateMeta();
			},

			updateMeta: $.debounce(function() {

				// This is debounced so we only have to update
				// the html once after multiple setMeta calls.
				var metaText = self.getMetaText(),
					meta = self.meta(),
					textField = self.textField();

				// Highlight meta button icon if meta has content
				$.each(self.metas, function(key, val){
					var meta = self.getMeta(key);
					meta && meta.button.toggleClass("has-content", !!val);
				});

				// If there is no meta string, don't show anything.
				if (!metaText) {
					meta.remove();
					textField.attr("placeholder", self.placeholder);
					return;
				}

				// Create meta element if it does not exist;
				var mentionsOverlay = self.mentionsOverlay();
				if (meta.length < 1) {
					meta = $('<u class="es-story-meta" data-story-meta data-ignore></u>').appendTo(mentionsOverlay);
				}

				// Add rtl mark if necessary
				var rtlMark =  mentionsOverlay.css("direction")=="rtl" ? "&#8207;" : "";

				// Update meta string
				meta.html(rtlMark + " &mdash; " + metaText);

				// Don't show placeholder text if we have meta text
				textField.attr("placeholder", "");

				self.setMentionsLayout();

			}, 1),

			refreshMeta: function() {

				// Trigger refresh meta so plugins
				// can update the meta the database
				self.trigger("refreshMeta");

				self.updateMeta();
			},

			"{textbox} triggerClear": function() {

				self.refreshMeta();
			},

			"{meta} click": function(meta, event) {

				// Do not focus textfield if a link was clicked
				if ($(event.target).is("a")) return;

				self.textField().focus();
			},

			"{metaButton} click": function(metaButton) {

				var metaName = metaButton.attr("data-story-meta-button");
				self.toggleMeta(metaName);
			}
		}});

		module.resolve();
	});

});
