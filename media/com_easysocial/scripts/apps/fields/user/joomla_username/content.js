EasySocial.module('apps/fields/user/joomla_username/content', function($) {
    var module = this;

    EasySocial.Controller('Field.Joomla_username', {
        defaultOptions: {
            event: null,

            id: null,

            userid: null,

            '{checkUsernameButton}': '[data-username-check]',

            '{input}': '[data-username-input]',

            '{available}': '[data-username-available]'
        }
    }, function(self) {
        return {
            state: false,

            init: function() {
            },

            '{checkUsernameButton} click': function() {
                self.delayedCheck();
            },

            '{input} keyup': function() {
                self.delayedCheck();
            },

            delayedCheck: $.debounce(function() {
                self.checkUsername();
            }, 250),

            checkUsername: function() {
                self.clearError();

                var state = $.Deferred();

                self.checkUsernameButton().addClass('btn-loading');

                var username = self.input().val();

                EasySocial.ajax('fields/user/joomla_username/isValid', {
                    id: self.options.id,
                    userid: self.options.userid,
                    username: username,
                    event: self.options.event
                }).done(function(msg) {

                    self.checkUsernameButton().removeClass('btn-loading');

                    self.available().show();

                    state.resolve();
                }).fail(function(msg) {

                    self.raiseError(msg);

                    self.checkUsernameButton().removeClass('btn-loading');

                    self.available().hide();

                    state.reject();
                });

                return state;
            },

            raiseError: function(msg) {
                self.trigger('error', [msg]);
            },

            clearError: function() {
                self.trigger('clear');
            },

            '{self} onSubmit': function(el, ev, register, mode) {
                if (mode === 'onRegisterMini') {
                    return;
                }

                register.push(self.checkUsername());
            }
        }
    });

    module.resolve();
});
