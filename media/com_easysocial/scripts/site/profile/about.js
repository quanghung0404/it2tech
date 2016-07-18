EasySocial.module( 'site/profile/about', function($){
	var module = this;

	EasySocial.require().script('field').library('history').done(function($) {
		EasySocial.Controller('Profile.About', {
			defaultOptions: {
				'{stepItem}'	: '[data-profile-about-step-item]',
				'{stepContent}'	: '[data-profile-about-step-content]',

				'{fieldItem}'	: '[data-field]'
			}
		}, function(self) {
			return {
				init: function() {
					self.fieldItem().addController('EasySocial.Controller.Field.Base', {
						mode: 'display'
					});
				},

				'{stepItem} click': function(el, ev) {
					ev.preventDefault();

					el.find('a').route();

					var target = el.data('for');

					self.stepItem().removeClass('active');

					el.addClass('active');

					self.stepContent().filterBy('id', target).trigger('activateTab');
				},

				'{stepContent} activateTab': function(el, ev) {
					self.stepContent().removeClass('active');

					el.addClass('active');

					el.find(self.fieldItem.selector).trigger('onShow');
				}
			}
		});

		module.resolve();
	});
});
