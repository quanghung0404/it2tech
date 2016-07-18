EasySocial.module('site/badges/badge', function($) {
	var module = this;

	EasySocial.Controller('Badges.Badge', {
		defaultOptions: {
			id					: 0,
			total				: 0,

			'{achieversList}'	: '[data-badge-achievers-list]',

			'{achiever}'		: '[data-badge-achievers-achiever]',

			'{loadIndicator}'	: '[data-badge-achievers-loading]',

			'{loadButton}'		: '[data-badge-achievers-load]',
		}
	}, function(self) {
		return {
			init: function() {
				self.options.id = self.element.data('id');
				self.options.total = self.element.data('total-achievers');
			},

			'{loadButton} click': function(el) {
				var current = self.achiever().length;

				if(el.enabled() && current < self.options.total) {
					el.disabled(true);

					el.hide();

					self.loadIndicator().show();

					EasySocial.ajax('site/controllers/badges/loadAchievers', {
						id: self.options.id,
						start: current
					}).done(function(html) {

						self.achieversList().append(html);

						el.enabled(true);

						self.loadIndicator().hide();

						if(self.achiever().length < self.options.total) {
							el.show();
						}

					}).fail(function(msg) {

					});
				}
			},

			loadAchievers: function() {

			}
		}
	});

	module.resolve();
});
