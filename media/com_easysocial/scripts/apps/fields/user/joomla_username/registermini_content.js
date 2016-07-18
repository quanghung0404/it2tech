EasySocial.module('apps/fields/user/joomla_username/registermini_content', function($) {
    var module = this;

    EasySocial.require()
    .language(
        'PLG_FIELDS_JOOMLA_USERNAME_CHECKING',
        'PLG_FIELDS_JOOMLA_USERNAME_EMPTY_USERNAME')
    .done(function() {

        EasySocial.Controller('Field.Joomla_username.Mini', {
            defaultOptions: {
                id: null,

                required: false,

                '{input}': '#joomla_username'
            }
        }, function(self) {
            return {
                init: function() {

                },

                '{input} keyup': function(el) {
                    if(el.val().length > 0) {
                        self.delayedCheck();
                    }
                },

                state: false,

                delayedCheck: $.debounce(function() {
                    self.checkUsername();
                }, 250),

                checkUsername: function() {

                    var username = self.input().val();

                    if(self.options.required && username.length == 0) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_USERNAME_EMPTY_USERNAME'));
                        return false;
                    }

                    if(username.length > 0) {
                        var state = $.Deferred();

                        self.clearError();

                        self.setLoading($.language('PLG_FIELDS_JOOMLA_USERNAME_CHECKING'));

                        EasySocial.ajax('fields/user/joomla_username/isValid', {
                            id: self.options.id,
                            userid: 0,
                            username: username
                        }).done(function(msg) {

                            self.setLoaded();

                            state.resolve();

                        }).fail(function(msg) {

                            self.setLoaded();

                            self.raiseError(msg);

                            state.reject();

                        });

                        return state;
                    }

                    return true;
                },

                raiseError: function(msg) {
                    self.trigger('error', [msg]);
                },

                clearError: function() {
                    self.trigger('clear');
                },

                '{self} onSubmit': function(el, ev, register, mode) {
                    if (mode !== 'onRegisterMini') {
                        return;
                    }

                    if(self.options.required || self.input().val().length > 0) {
                        register.push(self.checkUsername());
                    }
                },

                setLoading: function(msg) {
                    self.trigger('loading', [msg]);
                },

                setLoaded: function() {
                    self.trigger('loaded');
                }
            }
        });

        module.resolve();
    });
});
