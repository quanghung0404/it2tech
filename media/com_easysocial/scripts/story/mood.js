EasySocial.module("story/mood", function($) {

var module = this;

EasySocial.require()
	.language(
		"COM_EASYSOCIAL_MOOD_FEELING_CUSTOM"
	)
	.done(function() {

		// Constants
		var KEYCODE = {
			BACKSPACE: 8,
			COMMA: 188,
			DELETE: 46,
			DOWN: 40,
			ENTER: 13,
			ESCAPE: 27,
			LEFT: 37,
			RIGHT: 39,
			SPACE: 32,
			TAB: 9,
			UP: 38
		};

		EasySocial.Controller("Story.Mood", {

			defaultOptions: {

				"{base}": "[data-story-mood]",

				"{presets}": "[data-story-mood-presets]",
				"{preset}": "[data-story-mood-preset]",

				"{verb}": "[data-story-mood-verb]",
				"{verbType}": "[data-story-mood-verb-type]",
				"{textField}": "[data-story-mood-textfield]",

				"{removeButton}": "[data-story-mood-remove-button]"
			}
		},
		function(self, opts) { return {

			init: function() {

				self.reset();

				// Add placeholder support for IE9
				self.textField().placeholder();
			},

			setLayout: function() {

				var base = self.base(),
					textField = self.textField();

				base.toggleClass("is-empty", textField.val()==="");
			},

			currentMood: {},

			resetMood: function() {

				self.currentMood = {
					icon: '',
					verb: '',
					subject: '',
					text: '',
					custom: false
				};

				self.preset().removeClass("is-disabled");

				self.story.setMeta("mood", "");
			},

			setMood: function(type, content) {

				var currentMood = self.currentMood;

				if ($.isPlainObject(type)) {
					$.extend(currentMood, type);
				} else {
					currentMood[type] = content;
				}

				// This toggles the preset selection
				self.base().toggleClass("using-preset", !currentMood.custom);

				self.updateMeta();

				self.setLayout();
			},

			getVerb: function() {
				return self.verbType(".active").data("storyMoodVerbType") || "feeling";
			},

			reset: function() {

				self.selecting = true;

				self.resetMood();

				self.preset()
					.removeClass("is-disabled");

				self.textField()
					.val("")
					.focus();

				self.setLayout();

				self.base()
					.removeClass("using-preset hide-preset");

				self.selecting = false;
			},

			updateMeta: function() {

				var currentMood = self.currentMood,
					icon = currentMood.icon ? '<i class="es-emoticon ' + currentMood.icon + '"></i> ' : '';

				self.verbType()
					.removeClass("active")
					.filterBy("storyMoodVerbType", currentMood.verb)
					.addClass("active");

				self.story.setMeta("mood", icon + currentMood.text);
			},

			"{preset} click": function(preset) {

				// Create mood object
				var mood = {
					icon       : preset.data("storyMoodIcon"),
					verb       : preset.data("storyMoodVerb"),
					subject    : preset.data("storyMoodSubject"),
					text       : preset.data("storyMoodText"),
					subjectText: preset.data("storyMoodSubjectText"),
					custom     : false,
				};

				self.selecting = true;

				// Update textfield and refocus
				self.textField()
					.val(mood.subjectText)
					.focus();

				// Update meta
				self.setMood(mood);

				self.selecting = false;
			},

			"{textField} keydown": function(textField, event) {

				self.update();
			},

			"{textField} keyup": function(textField, event) {

				self.update();
			},

			"{textField} input": function(textField, event) {

				// If user is selecting from preset,
				// don't do anything.
				if (self.selecting) return;

				// Get text
				var text = textField.val();

				if (!text) {
					self.reset();
					return;
				}

				// If user modifies a preset, set mood to custom.
				// This *might* cause the preset selection to show if there are matching keywords.
				var currentMood = self.currentMood,
					presetModified = !currentMood.custom && (text !== currentMood.subjectText);

					if (presetModified) {
						self.setMood("custom", true);
					}

				// Highlight preset candidates
				var candidates =
					self.preset()
						.filter(function(){
							var preset = $(this),
								isDisabled = preset.data("storyMoodSubjectText").indexOf(text) !== 0;
							preset.toggleClass("is-disabled", isDisabled);
							return !isDisabled;
						});

				// Hide preset seleection if there is no candidate
				self.base().toggleClass("hide-preset", candidates.length < 1);

				// Create mood object
				var verb = self.getVerb(),
					mood = {
						verb   : verb,
						subject: '',
						custom : true,
						text   : $.language("COM_EASYSOCIAL_MOOD_" + verb.toUpperCase() + "_CUSTOM", text),
					};

				// Set custom mood
				self.setMood(mood);
			},

			"{removeButton} click": function() {

				self.reset();
			},

			"{story} activateMeta": function(el, event, meta) {

				if (meta.name==="mood") {
					setTimeout(function(){
						self.textField().focus();
					}, 1);
				}
			},

			"{story} save": function(event, element, save) {

				var currentMood = self.currentMood;

				if (!currentMood.verb) return;

				save.data.mood_icon    = currentMood.icon;
				save.data.mood_verb    = currentMood.verb;
				save.data.mood_subject = currentMood.subject;
				save.data.mood_custom  = currentMood.custom;
				save.data.mood_text    = currentMood.text;
			},

			"{story} clear": function() {
				self.reset();
			}
		}});

		// Resolve module
		module.resolve();

	});

});