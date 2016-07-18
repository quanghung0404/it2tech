EasySocial.module('apps/fields/user/joomla_password/registermini_content', function($) {
    var module = this;

    EasySocial.require()
    .language(
        'PLG_FIELDS_JOOMLA_PASSWORD_TOO_SHORT',
        'PLG_FIELDS_JOOMLA_PASSWORD_TOO_LONG',
        'PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_INTEGER',
        'PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_SYMBOLS',
        'PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_UPPERCASE',
        'PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD',
        'PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_RECONFIRM_PASSWORD',
        'PLG_FIELDS_JOOMLA_PASSWORD_NOT_MATCHING')
    .done(function() {
        EasySocial.Controller('Field.Joomla_password.Mini', {
            defaultOptions: {
                required: false,
                reconfirmPassword: false,
                min: 4,
                max: 0,

                '{input}': '[data-password]',
                '{reconfirm}'   : '[data-field-password-confirm]',

                '{reconfirmNotice}' : '[data-reconfirmPassword-failed]'
            }
        }, function(self) {
            return {
                init: function() {

                },

                '{input} keyup': function() {
                    self.checkPassword();
                },

                '{reconfirm} keyup': function() {
                    self.validatePassword();
                },

                '{reconfirm} blur': function() {
                    self.validatePassword();
                },

                checkPassword: function() {
                    self.clearError();

                    var value = self.input().val();

                    if(self.options.required && value.length == 0) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD'));
                        return false;
                    }

                    if(self.options.min > 0 && value.length < self.options.min) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_TOO_SHORT'));
                        return false;
                    }

                    if(self.options.max > 0 && value.length > self.options.max) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_TOO_LONG'));
                        return false;
                    }

                    if(self.options.minInteger > 0) {
                        var test = value.match(/[0-9]/g);
                        if (!test || test.length < self.options.minInteger) {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_INTEGER', self.options.minInteger));
                            return false;
                        }
                    }

                    if(self.options.minSymbol > 0) {
                        var test = value.match(/[\W]/g);
                        if (!test || test.length < self.options.minSymbol) {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_SYMBOLS', self.options.minSymbol));
                            return false;
                        }
                    }

                    if(self.options.minUpperCase > 0) {
                        var test = value.match(/[A-Z]/g);
                        if (!test || test.length < self.options.minUpperCase) {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_UPPERCASE', self.options.minUpperCase));
                            return false;
                        }
                    }

                    return true;
                },

                validatePassword: function()
                {
                    self.clearError();

                    var input = self.input().val(),
                        reconfirm = self.reconfirm().val();

                    if(self.options.reconfirmPassword && !self.validatePasswordConfirm()) {
                        return false;
                    }

                    return true;
                },

                validatePasswordConfirm: function() {
                    var input = self.input().val(),
                        reconfirm = self.reconfirm().val();

                    // Check if either input or reconfirm is not empty
                    if(!$.isEmpty(input) || !$.isEmpty(reconfirm)) {
                        if($.isEmpty(input)) {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD'));
                            return false;
                        }

                        if($.isEmpty(reconfirm)) {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_RECONFIRM_PASSWORD'));
                            return false;
                        }

                        if(input !== reconfirm) {
                            self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_NOT_MATCHING'));
                            return false;
                        }
                    }

                    return true;
                },

                '{self} onSubmit': function(el, event, register, mode) {
                    if (mode !== 'onRegisterMini') {
                        return;
                    }

                    register.push(self.checkPassword());
                },

                clearError: function() {
                    self.trigger('clear');
                },

                raiseError: function(msg) {
                    self.trigger('error', [msg]);
                }
            }
        });

        module.resolve();
    });
})
