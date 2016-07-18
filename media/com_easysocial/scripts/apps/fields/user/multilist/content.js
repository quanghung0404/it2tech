EasySocial.module('apps/fields/user/multilist/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language('PLG_FIELDS_MULTILIST_VALIDATION_PLEASE_SELECT_A_VALUE')
        .done(function($) {
            EasySocial.Controller(
                'Field.Multilist',
                {
                    defaultOptions:
                    {
                        required        : null,
                        multiple        : null,

                        "{field}"       : "[data-field-multilist]",

                        "{item}"        : "[data-field-multilist-item]",

                        "{option}"      : "[data-field-multilist-item] option"
                    }
                },
                function( self )
                {
                    return {
                        init : function()
                        {
                        },

                        validateInput : function()
                        {
                            self.clearError();

                            if(self.options.multiple && self.item().children(':selected').length <= 0) {
                                self.raiseError();
                                return false;
                            }

                            // The only way to test for an empty value is when the value is empty and it's required.
                            if(self.item().children(':selected' ).val() == '') {
                                self.raiseError();
                                return false;
                            }

                            return true;
                        },

                        raiseError: function() {
                            self.trigger('error', [$.language('PLG_FIELDS_MULTILIST_VALIDATION_PLEASE_SELECT_A_VALUE')]);
                        },

                        clearError: function() {
                            self.trigger('clear');
                        },

                        "{self} onSubmit": function(el, event, register) {
                            // If field is not required, skip the checks.

                            if(!self.options.required)
                            {
                                register.push(true);
                                return;
                            }

                            register.push(self.validateInput());

                            return;
                        },

                        '{self} onSample': function() {
                            if(self.option().length < 1) {
                                self.item().append($('<option></option>'));
                            }
                        },

                        '{self} onChoiceAdded': function(el, event, index) {
                            if(self.option().eq(index).length > 0) {
                                self.option().eq(index).before($('<option></option>'));
                            } else {
                                self.item().append($('<option></option>'));
                            }
                        },

                        '{self} onChoiceValueChanged': function(el, event, index, value) {
                            self.option().eq(index).val(value);
                        },

                        '{self} onChoiceTitleChanged': function(el, event, index, value) {
                            self.option().eq(index).text(value);
                        },

                        '{self} onChoiceRemoved': function(el, event, index) {
                            self.option().eq(index).remove();
                        },

                        '{self} onChoiceToggleDefault': function(el, event, index, value) {
                            if(value) {
                                self.option().eq(index).attr('selected', 'selected');
                            } else {
                                self.option().eq(index).removeAttr('selected');
                            }
                        }
                    }
                });

            module.resolve();
        });
});
