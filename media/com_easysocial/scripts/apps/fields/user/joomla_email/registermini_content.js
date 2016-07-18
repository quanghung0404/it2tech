EasySocial.module('apps/fields/user/joomla_email/registermini_content', function($) {
    var module = this;

    EasySocial.require()
    .language(
        'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_REQUIRED',
        'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_RECONFIRM_REQUIRED',
        'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_NOT_MATCHING',
        'PLG_FIELDS_JOOMLA_EMAIL_CHECKING',
        'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_INVALID_FORMAT')
    .done(function() {
        EasySocial.Controller('Field.Joomla_email.Mini', {
            defaultOptions: {
                require: true,
                id: null,

                '{input}': '#email',

                '{confirm}' : '[data-field-email-reconfirm-input]'

            }
        }, function(self) {
            return {
                init: function() {
                    self.origEmail = self.input().val();
                },

                '{input} keyup': function(el) {
                    if(el.val().length > 0) {
                        self.delayedCheck();
                    }
                },

                '{confirm} blur': function(el, ev) {
                    self.checkEmail();
                },

                state: false,

                delayedCheck: $.debounce(function() {
                    self.checkEmail();
                }, 250),

                checkEmail: function() {

                    self.clearError();

                    var email = self.input().val();

                    if(self.options.required && email.length == 0) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_REQUIRED'));
                        return false;
                    }

                    if(!$.isEmpty(email) && self.options.regex) {
                        var regex = new RegExp(self.options.regexFormat, self.options.regexModifier);

                        if(!regex.test(email)) {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_INVALID_FORMAT'));
                            return false;
                        }
                    }


                    if(self.options.reconfirm)
                    {
                        var reconfirm = self.confirm().val();

                        if(email !== self.origEmail && $.isEmpty(reconfirm))
                        {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_RECONFIRM_REQUIRED'));
                            return false;
                        }

                        if(!$.isEmpty(reconfirm) && email !== reconfirm)
                        {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_NOT_MATCHING'));
                            return false;
                        }
                    }


                    if(email.length > 0) {
                        var state = $.Deferred();

                        self.setLoading($.language('PLG_FIELDS_JOOMLA_EMAIL_CHECKING'));

                        var email = self.input().val();

                        EasySocial.ajax('fields/user/joomla_email/isValid', {
                            id: self.options.id,
                            userid: 0,
                            email: email
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
                        register.push(self.checkEmail());
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
