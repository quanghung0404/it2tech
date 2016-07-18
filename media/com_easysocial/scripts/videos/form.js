EasySocial.module('videos/form', function($) {

	var module = this;

	EasySocial.require()
	.script('site/friends/suggest')
	.library('mentions')
	.done(function($) {

	EasySocial.Controller('Videos.Form', {
		defaultOptions: {
			"{videoSource}": "[data-video-source]",

			// Forms for video source
			"{forms}": "[data-form-source]",
			"{linkForm}": "[data-form-link]",
			"{uploadForm}": "[data-form-upload]",

			// Mentions
			"{mentions}": "[data-mentions]"
		}
	}, function(self, opts, base) { return {

		init: function() {
			self.initMentions();
		},

		initMentions: function() {

			self.mentions()
				.addController("EasySocial.Controller.Friends.Suggest", {
					"showNonFriend": false,
					"includeSelf": true,
					"name": "tags[]",
					"exclusion": opts.tagsExclusion
				});
		},

		"{videoSource} change": function(videoSource, event) {

			var source = $(videoSource).val();
			var form = self[source + "Form"]();

			// Hide all source forms
			self.forms().addClass('hide');

			// Remove hidden class for the active form
			form.removeClass('hide');
		}

	}});

	module.resolve();


	});

});
