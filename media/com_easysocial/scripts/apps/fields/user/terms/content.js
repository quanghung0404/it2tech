EasySocial.module('apps/fields/user/terms/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language('PLG_FIELDS_TERMS_VALIDATION_REQUIRED')
        .done(function($) {
            EasySocial.Controller('Field.Terms',
            {
                defaultOptions:
                {
                    event           : null,

                    required        : false,

                    '{textbox}'     : '[data-field-terms-textbox]',
                    '{checkbox}'    : '[data-field-terms-checkbox]'
                }
            },
            function(self)
            {
                return {
                    init : function() {
                    },

                    validateInput: function() {
                        self.clearError();

                        if(self.options.event == 'onAdminEdit') {
                            return true;
                        }

                        if(self.options.required && !self.checkbox().is(':checked'))
                        {
                            self.raiseError();
                            return false;
                        }

                        return true;
                    },

                    '{checkbox} change': function() {
                        self.validateInput();
                    },

                    raiseError: function() {
                        self.trigger('error', [$.language('PLG_FIELDS_TERMS_VALIDATION_REQUIRED')]);
                    },

                    clearError: function() {
                        self.trigger('clear');
                    },

                    '{self} onSubmit': function(el, event, register) {
                        register.push(self.validateInput());
                    },

                    '{self} onConfigChange': function(el, event, name, value) {
                        switch(name) {
                            case 'message':
                                self.textbox().val(value);
                                break;
                        }
                    }
                }
            });

            module.resolve();
        });
});
