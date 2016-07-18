EasySocial.module('apps/fields/event/description/content', function($) {
    var module = this;

    EasySocial
        .require()
        .language('PLG_FIELDS_EVENT_DESCRIPTION_VALIDATION_INPUT_REQUIRED')
        .done(function($) {

            EasySocial.Controller('Field.Event.Description', {
                defaultOptions: {
                    "required": false,
                    "editor": null,
                    "{input}": '[data-field-description]'
                }
            }, function(self) { return {

                init: function() {
                    self.editor = self.options.editor;
                },

                '{input} keyup': function() {
                    self.validateInput();
                },

                '{input} blur': function() {
                    self.validateInput();
                },

                validateInput: function() {
                    self.clearError();

                    var value = self.editor.getContent();

                    if (self.options.required && $.isEmpty(value)) {
                        self.raiseError($.language('PLG_FIELDS_EVENT_DESCRIPTION_VALIDATION_INPUT_REQUIRED'));
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
                
                '{self} onError': function(el, ev, type) {
                    if(type === 'required') {
                        self.raiseError($.language('PLG_FIELDS_EVENT_DESCRIPTION_VALIDATION_INPUT_REQUIRED'));
                    }
                },

                '{self} onSubmit': function(el, ev, register) {
                    register.push(self.validateInput());
                }
            }});

            module.resolve();
        });
});
