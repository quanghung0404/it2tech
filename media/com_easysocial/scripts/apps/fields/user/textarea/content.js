EasySocial.module('apps/fields/user/textarea/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language(
            'PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_REQUIRED',
            'PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_TOO_SHORT',
            'PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_TOO_LONG')
        .done(function($) {
            EasySocial.Controller('Field.Textarea', {
                defaultOptions: {
                    required        : false,

                    min: 0,
                    max: 0,

                    '{field}'       : '[data-field-textarea]',

                    '{input}'       : '[data-field-textarea-input]'
                }
            }, function( self ) {
                return {
                    init : function() {
                        self.options.min = self.field().data('min');
                        self.options.max = self.field().data('max');
                    },

                    validateInput : function()
                    {
                        self.clearError();

                        var val     = self.input().val();

                        if(self.options.required && $.isEmpty(val)) {
                            self.raiseError($.language('PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_REQUIRED'));
                            return false;
                        }

                        if(!$.isEmpty(val) && self.options.min > 0 && val.length < self.options.min) {
                            self.raiseError($.language('PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_TOO_SHORT'));
                            return false;
                        }

                        if(self.options.max > 0 && val.length > self.options.max) {
                            self.raiseError($.language('PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_TOO_LONG'));
                            return false;
                        }

                        return true;
                    },

                    raiseError: function(msg) {
                        self.trigger('error', [msg]);
                    },

                    clearError: function() {
                        self.trigger('clear');
                    },

                    '{self} onSubmit': function(el, event, register) {
                        register.push(self.validateInput());
                    },

                    '{self} onError': function(el, ev, type) {
                        if(type === 'required') {
                            self.raiseError($.language('PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_REQUIRED'));
                        }
                    },

                    '{input} keyup': function() {
                        self.validateInput();
                    },

                    '{self} onConfigChange': function(el, event, name, value) {
                        switch(name) {
                            case 'default':
                                self.input().val(value);
                                break;

                            case 'placeholder':
                                self.input().attr('placeholder', value);
                                break;

                            case 'readonly':
                                if(value) {
                                    self.input().attr('readonly', 'readonly');
                                } else {
                                    self.input().removeAttr('readonly');
                                }
                                break;
                        }
                    }
                }
            });

            module.resolve();
        });
});
