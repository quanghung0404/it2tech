EasySocial.module('apps/fields/user/joomla_email/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language(
            'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_REQUIRED',
            'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_RECONFIRM_REQUIRED',
            'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_NOT_MATCHING')
        .done(function($) {
            EasySocial.Controller('Field.Joomla_email', {
                defaultOptions: {
                    required    : true,

                    id          : null,

                    userid      : null,

                    reconfirm   : false,

                    event       : null,

                    '{input}'   : '[data-field-email-input]',

                    '{confirm}' : '[data-field-email-reconfirm-input]',

                    '{confirmFrame}'    : '[data-field-email-reconfirm-frame]'
                }
            }, function(self) {
                return {
                    init: function() {
                        self.origEmail = self.input().val();
                    },

                    '{input} blur': function(el, ev) {
                        var value = self.input().val();

                        if(self.options.reconfirm && value !== self.origEmail)
                        {
                            self.confirmFrame().show();
                        }

                        if(self.options.reconfirm && value === self.origEmail && (self.options.event === 'onEdit' || self.options.event === 'onAdminEdit'))
                        {
                            self.confirmFrame().hide();
                        }

                        self.validateInput();
                    },

                    '{confirm} blur': function(el, ev) {
                        self.validateInput();
                    },

                    validateInput: function() {
                        self.clearError();

                        var value = self.input().val();

                        if($.isEmpty(value)) {
                            if(!self.options.required) {
                                return true;
                            }

                            self.raiseError($.language('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_REQUIRED'));
                            return false;
                        }

                        if(self.options.reconfirm)
                        {
                            var reconfirm = self.confirm().val();

                            if(value !== self.origEmail && $.isEmpty(reconfirm))
                            {
                                self.raiseError($.language('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_RECONFIRM_REQUIRED'));
                                return false;
                            }

                            if(!$.isEmpty(reconfirm) && value !== reconfirm)
                            {
                                self.raiseError($.language('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_NOT_MATCHING'));
                                return false;
                            }
                        }

                        return self.checkInput()
                            .done(function() {
                                self.clearError();
                            })
                            .fail(function(msg) {
                                self.raiseError(msg);
                            });
                    },

                    checkInput: function() {
                        return EasySocial.ajax('fields/user/joomla_email/isValid', {
                            id: self.options.id,
                            userid: self.options.userid,
                            email: self.input().val()
                        });
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

                        register.push(self.validateInput());
                    }
                }
            });

            module.resolve();
        });
});
