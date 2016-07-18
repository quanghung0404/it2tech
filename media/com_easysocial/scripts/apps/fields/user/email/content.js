EasySocial.module('apps/fields/user/email/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language('PLG_FIELDS_EMAIL_VALIDATION_REQUIRED', 'PLG_FIELDS_EMAIL_VALIDATION_INVALID_FORMAT')
        .done(function($) {
            EasySocial.Controller(
                'Field.Email',
                {
                    defaultOptions:
                    {
                        required        : false,

                        regex           : 0,

                        regexFormat     : '',

                        regexModifier   : '',

                        "{field}"       : "[data-field-email]",

                        "{input}"       : "[data-field-email-input]"
                    }
                },
                function( self )
                {
                    return {
                        init: function() {
                        },

                        validateInput: function() {
                            var value   = self.input().val();

                            if(self.options.required && $.isEmpty(value)) {
                                self.raiseError($.language('PLG_FIELDS_EMAIL_VALIDATION_REQUIRED'));
                                return false;
                            }

                            if(!$.isEmpty(value) && self.options.regex) {
                                var regex = new RegExp(self.options.regexFormat, self.options.regexModifier);

                                if(!regex.test(value)) {
                                    self.raiseError($.language('PLG_FIELDS_EMAIL_VALIDATION_INVALID_FORMAT'));
                                    return false;
                                }
                            }

                            return true;
                        },

                        raiseError: function(msg) {
                            self.trigger('error', [msg]);
                        },

                        clearError: function() {
                            self.trigger('clear');
                        },

                        "{self} onSubmit": function(el, event, register) {

                            register.push(self.validateInput());

                            return;
                        }
                    }
                });

            module.resolve();
        });
});
