EasySocial.module( 'admin/users/form' , function($) {

	var module = this;

	EasySocial.require()
	.script('field')
	.done(function($) {
		
		EasySocial.Controller('Users.Form', {
			defaultOptions: {
				userid: null,
				mode: 'adminedit',

				"{selectProfile}": "[data-user-select-profile]",
				"{content}": "[data-user-new-content]",
				"{profileTitle}": "[data-profile-title]",

				"{fieldItem}": "[data-profile-adminedit-fields-item]",

				"{tabnav}": "[data-tabnav]",
				"{tabcontent}": "[data-tabcontent]",

				"{stepnav}": "[data-stepnav]",
				"{stepcontent}": "[data-stepcontent]",

				view: {
					loading : "site/loading/large"
				}
			}
		}, function(self) {
			return {

				init : function() {
					window.selectedProfile 	= self.selectedProfile;

					self.fieldItem().addController('EasySocial.Controller.Field.Base', {
						userid: self.options.userid,
						mode: self.options.mode
					});
				},

				selectedProfile : function(profileId) {
					EasySocial.dialog().close();

					window.location.href	= 'index.php?option=com_easysocial&view=users&layout=form&profileId=' + profileId;
				},

				"{selectProfile} click" : function() {
					EasySocial.dialog(
					{
						content 	: EasySocial.ajax( 'admin/views/profiles/browse' )
					});
				},

				errorFields: [],

				'{fieldItem} error': function(el, ev) {
					var id = el.data('id');

					if($.inArray(id, self.errorFields) < 0) {
						self.errorFields.push(id);
					}

					var stepid = el.parents(self.stepcontent.selector).data('for');

					self.stepnav().filterBy('for', stepid).trigger('error');

					var tabid = el.parents(self.tabcontent.selector).data('for');

					self.tabnav().filterBy('for', tabid).trigger('error');
				},

				'{fieldItem} clear': function(el, ev) {
					var fieldid = el.data('id');

					self.errorFields = $.without(self.errorFields, fieldid);

					var stepid = el.parents(self.stepcontent.selector).data('for');

					self.stepnav().filterBy('for', stepid).trigger('clear');

					var tabid = el.parents(self.tabcontent.selector).data('for');

					self.tabnav().filterBy('for', tabid).trigger('clear');
				},

				'{stepnav} error': function(el) {
					el.addClass('error');
				},

				'{tabnav} error': function(el) {
					el.addClass('error');
				},

				'{stepnav} clear': function(el) {
					if(self.errorFields.length < 1) {
						el.removeClass('error');
					}
				},

				'{tabnav} clear': function(el) {
					if(self.errorFields.length < 1) {
						el.removeClass('error');
					}
				},

				'{stepnav} click': function(el) {
					var id = el.data('for');

					self.stepcontent().filterBy('for', id).find(self.fieldItem.selector).trigger('show');
				}
			}
		});

		module.resolve();
	});

});
