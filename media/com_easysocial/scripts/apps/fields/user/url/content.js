EasySocial.module('apps/fields/user/url/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language('PLG_FIELDS_URL_VALIDATION_EMPTY_URL')
        .done(function($) {
            EasySocial.Controller('Field.Url', {
                defaultOptions: {
                    required        : false,

                    '{input}'       : '[data-field-url-input]'
                }
            }, function( self ) {
                return {
                    init: function() {
                    },

                    '{input} blur': function() {
                        self.validateInput();
                    },

                    validateInput: function() {
                        self.clearError();

                        var value = self.input().val();

                        if(self.options.required && $.isEmpty(value)) {
                            self.raiseError($.language('PLG_FIELDS_URL_VALIDATION_EMPTY_URL'));
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

                    '{self} onError': function(el, event, type, field) {
                        self.raiseError($.language('PLG_FIELDS_URL_VALIDATION_EMPTY_URL'));
                    }
                }
            });

            module.resolve();
        });
});
