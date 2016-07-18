EasySocial.module("site/privacy/privacy", function($){

	var module	= this;

	EasySocial.require()
	.library("textboxlist")
	.language(
		'COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_PUBLIC',
		'COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_MEMBER',
		'COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_FRIENDS_OF_FRIEND',
		'COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_FRIEND',
		'COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_ONLY_ME',
		'COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_CUSTOM'
	)
	.done(function($){

		EasySocial.Controller("Privacy", {
			defaultOptions: {
				"{menu}": "[data-privacy-menu]",
				"{privacyItem}": "[data-privacy-item]",
				"{icon}": "[data-privacy-icon]",
				"{button}": "[data-privacy-toggle]",
				"{tooltip}": "[data-original-title]",
				"{key}": "[data-privacy-hidden]"
			}
		}, function(self) { return {

			init: function() {
				self.instanceId = $.uid();

				self.addPlugin("custom");
			},

			getData: function(item) {
				return $._.pick(item.data(), "uid", "utype", "value", "pid", "privacyicon", "streamid", "pitemid", "userid");
			},

			"{self} click" : function(el, event) {

				var target = $(event.target);
				var button = self.button();

				// If the area being clicked is the toggle button,
				if (target.parents().andSelf().filter(button).length > 0) {

					// then we toggle privacy menu.
					self.toggle();
				}
			},

			"{privacyItem} click" : function(item) {

				// Retrieve data from this privacy item
				var data = self.getData(item);

				// Trigger privacy changed event
				self.trigger("privacyChange", [data]);

				if (!data.preventSave) {

					// Save new privacy settings
					self.save(data);

					// Deactivate menu
					self.deactivate();
				}
			},

			"{self} privacyChange": function(el, event, data) {

				// Deactivate other privacy item
				self.privacyItem()
					.removeClass("active")

					// and activate current privacy item.
					.filter("[data-value=" + data.value + "]")
					.addClass("active");
			},

			toggle: function() {

				var isActive = self.element.hasClass("active");
				self[(isActive) ? "deactivate" : "activate"]();
			},

			activate: function() {

				self.element.addClass("active");

				self.trigger("activate", [self]);
				$(window).trigger("activatePrivacy", [self]);

				var windowClick = "click.privacy." + self.instanceId;

				$(document).on(windowClick, function(event){

					var clickedTarget = $(event.target);

					// Don't do anything if we're clicking ourself
					if (clickedTarget.parents().andSelf().filter(self.element).length > 0
						|| clickedTarget.parents('[data-textboxlist-autocomplete]').length > 0
						|| clickedTarget.parents('[data-textboxlist-item]').length > 0 )
					{
						return;
					}

					$(document).off(windowClick);
					self.deactivate();
				});
			},

			deactivate: function() {

				self.element.removeClass("active");

				self.trigger("deactivateAllPrivacy", [self]);
				$(window).trigger("deactivatePrivacy", [self]);
			},

			"{window} activatePrivacy": function(el, event, instance) {
				if (instance!==self) {
					self.deactivate();
				}
			},

			save: function(data) {

				// Set privacy value
				self.key().val(data.value);

				// Set privacy icon
				self.icon().attr("class", data.privacyicon);

				// Trigger save event
				self.trigger("privacySave", [data]);

				// update tooltips
				self.element.attr('data-original-title', $.language( 'COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_' + data.value.toUpperCase() ) );

				// If saving is done via ajax, save now.
				if (self.element.data("privacyMode")=="ajax") {

					EasySocial.ajax("site/controllers/privacy/update",
						{
							uid 	: data.uid,
							utype	: data.utype,
							value 	: data.value,
							pid 	: data.pid,
							custom 	: data.custom,
							streamid: data.streamid,
							userid	: data.userid,
							pitemid	: data.pitemid
						})
						.done(function(){

						})
						.fail(function(){
							// Unable to set privacy settings
						});
				}
			}
		}});


		EasySocial.Controller("Privacy.Custom", {
			defaultOptions: {
				"{textField}"   : "[data-textfield]",
				"{saveButton}" 	: "[data-save-button]",
				"{cancelButton}": "[data-cancel-button]",
				"{customItem}"  : "[data-privacy-item][data-value=custom]",
				"{customKey}"   : "[data-privacy-custom-hidden]"
			}
		}, function(self) { return {

				init: function() {

					self.textField()
						.textboxlist({
							component: 'es',
							unique: true,
							plugin: {
								autocomplete: {
									exclusive: true,
									minLength: 1,
									cache: false,
									query: function(keyword) {

										var users = self.getIds();

										var ajax = EasySocial.ajax("site/views/privacy/getfriends", {
												q: keyword,
												exclude: users
											});
										return ajax;
									}
								}
							}
						});

					self.textboxlist = self.textField().controller("TextboxList");
				},

				getIds: function() {

					var items =
						self.textField()
							.textboxlist("controller")
							.getAddedItems();

					return $.map(items, function(item, idx) {
						return item.id;
					});
				},

				updateIds: function() {

					var ids = self.getIds();
					self.customKey().val(ids.join(","));
				},

				"{parent} privacyChange": function(el, event, data) {

					var isCustomPrivacy = (data.value=="custom");

					self.element.toggleClass("custom-privacy", isCustomPrivacy);

					// If user no longer selects custom privacy
					if (!isCustomPrivacy) {

						// Clear any existing custom privacy
						self.textField()
							.textboxlist("controller")
							.clearItems();
					} else {

						// Prevent privacy from saving
						data.preventSave = true;
					}
				},

				"{parent} privacySave": function(el, event, data) {
					// for now do nothing.
				},

				"{parent} deactivateAllPrivacy": function(el, event) {

					self.textboxlist.autocomplete.hide();
				},

				"{cancelButton} click" : function(){
					self.element.removeClass("custom-privacy");
					self.textboxlist.autocomplete.hide();
				},

				"{saveButton} click" : function(){

					var parent = self.parent,
						customItem = self.customItem();

					var data = parent.getData(customItem);
					data.custom = self.customKey().val();

					self.parent.save(data );
					self.parent.deactivate();
				},

				// event listener for adding new name
				"{textField} addItem": function() {
					self.updateIds();
				},

				// event listener for removing name
				"{textField} removeItem": function() {
					self.updateIds();
				}
		}});

		// Implement privacy button upon clicking on the button
		$(document).on('click.es.privacy',  '[data-es-privacy-container]', function() {

			var privacyButton = $(this);
			var privacyController = "EasySocial.Controller.Privacy";

			// If controller is already implemented on the button, just skip implementation
			if (privacyButton.hasController(privacyController)) {
				return;
			}

			// Run the toggle.
			privacyButton.addController(privacyController)
				.toggle();
		});

		module.resolve();
	});

});
