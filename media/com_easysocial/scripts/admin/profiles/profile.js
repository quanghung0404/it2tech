EasySocial.module('admin/profiles/profile', function($) {
	var module = this;

	EasySocial.require()
	.script('utilities/alias')
	.view('admin/profiles/dialog.delete.profileavatar')
	.language('COM_EASYSOCIAL_PROFILES_FORM_CLEAR_AVATAR')
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
				'{profileAvatarUpload}': '[data-profile-avatar-upload]',

				view: {
					deleteProfileAvatar: 'admin/profiles/dialog.delete.profileavatar'
				}
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
					if(self.options.hasAvatar) {
						EasySocial.dialog({
							content: self.view.deleteProfileAvatar(true),
							bindings: {
								'{deleteButton} click': function() {
									var dialog = this.parent;

									dialog.loading(true);

									EasySocial.ajax('admin/controllers/profiles/deleteProfileAvatar', {
										id: self.options.id
									}).done(function() {
										dialog.loading(false);

										dialog.close();

										self.profileAvatarImage().attr('src', self.options.defaultAvatar);

										self.profileAvatarRemoveWrap().hide();

										self.profileAvatarRemoveButton().text($.language('COM_EASYSOCIAL_PROFILES_FORM_CLEAR_AVATAR'));

										self.options.hasAvatar = false;
									});
								}
							}
						});
					} else {
						self.profileAvatarUpload().val('').trigger('change');
					}
				}
			}
		});

		module.resolve();
	});
});
