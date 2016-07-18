EasySocial.module('apps/fields/user/checkbox/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language('PLG_FIELDS_CHECKBOX_CHECK_AT_LEAST_ONE_ITEM')
        .done(function() {
            EasySocial.Controller(
                'Field.Checkbox',
                {
                    defaultOptions:
                    {
                        required        : false,
                        "{item}"        : "[data-field-checkbox-item]"
                    }
                },
                function( self )
                {
                    return {
                        init : function() {
                        },

                        validateInput : function() {
                            self.clearError();

                            if(self.options.required && self.item(':checked').length == 0) {
                                self.raiseError();
                                return false;
                            }

                            return true;
                        },

                        raiseError: function() {
                            self.trigger('error', [$.language('PLG_FIELDS_CHECKBOX_CHECK_AT_LEAST_ONE_ITEM')]);
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
