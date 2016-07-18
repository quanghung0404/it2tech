EasySocial.module('admin/profiles/form', function($) {
	var module = this;

	EasySocial.require()
	.script('utilities/alias')
	.done(function($) {

		EasySocial.Controller('Profiles.Profile', {
			defaultOptions: {
				id: null,

				// Profile avatar
				hasAvatar: false,
				defaultAvatar: null,

				'{profileAvatar}': '[data-profile-avatar]',
				'{profileAvatarImage}': '[data-profile-avatar-image]',
				'{profileAvatarRemoveWrap}': '[data-profile-avatar-remove-wrap]',
				'{profileAvatarRemoveButton}': '[data-profile-avatar-remove-button]',
				'{profileAvatarUpload}': '[data-profile-avatar-upload]'
			}
		}, function(self) {
			
			return {
				init: function() {

					self.element.addController('EasySocial.Controller.Utilities.Alias', {
						"{source}"	: "#title",
						"{target}"	: "#alias"
					});

					self.options.hasAvatar = self.profileAvatar().data('hasavatar');
					self.options.defaultAvatar = self.profileAvatar().data('defaultavatar');
				},

				'{profileAvatarUpload} change': function(el) {
					var value = el.val();

					if(!$.isEmpty(value)) {
						self.profileAvatarRemoveWrap().show();
					} else {
						if(!self.options.hasAvatar) {
							self.profileAvatarRemoveWrap().hide();
						}
					}
				},

				'{profileAvatarRemoveButton} click': function(el) {

					if (!self.options.hasAvatar) {
						self.profileAvatarUpload()
							.val('')
							.trigger('change');

						return;
					}

					EasySocial.dialog({
						content: EasySocial.ajax('admin/views/profiles/confirmRemoveProfileAvatar'),
						bindings: {
							'{deleteButton} click': function() {

								EasySocial.ajax('admin/controllers/profiles/deleteProfileAvatar', {
									id: self.options.id
								}).done(function() {

									self.profileAvatarImage().attr('src', self.options.defaultAvatar);

									self.profileAvatarRemoveWrap().hide();

									self.options.hasAvatar = false;

									EasySocial.dialog().close();
								});
							}
						}
					});
				}
			}
		});

		module.resolve();
	});
});
