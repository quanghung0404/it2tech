EasySocial.module('site/dashboard/dashboard.guest.login', function($) {
	var module = this;

	EasySocial.require().script('field', 'validate').done(function() {

		EasySocial.Controller('Dashboard.Guest.Login', {
			defaultOptions: {
				'{fieldItem}': '[data-registermini-fields-item]',

				'{submit}': '[data-registermini-submit]',

				'{form}': '[data-registermini-form]'
			}
		}, function(self) {
			return {
				init: function() {
					self.fieldItem().addController('EasySocial.Controller.Field.Base', {
						userid: 0,
						mode: 'registermini'
					});
				},

				'{submit} click': function(el) {

					if(el.enabled()) {
						el.disabled(true);

						self.form()
							.validate({mode: 'onRegisterMini'})
							.done(function() {
								el.enabled(true);
								self.form().submit();
							})
							.fail(function() {
								el.enabled(true);
							});
					}

				}
			}
		});

		module.resolve();
	});
});
