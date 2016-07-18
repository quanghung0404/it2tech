EasySocial.module('apps/fields/user/checkbox/sample_content', function($) {
    var module  = this;

    EasySocial.require()
    .view('fields/user/checkbox/item')
    .done(function($){

        EasySocial.Controller('Field.Checkbox.Sample', {
            defaultOptions: {
                '{checkboxes}'      : '[data-checkboxes]',
                '{checkbox}'        : '[data-checkbox]',
                '{checkboxInput}'   : '[data-checkbox-input]',
                '{checkboxTitle}'   : '[data-checkbox-title]',

                view: {
                    sample  : 'fields/user/checkbox/item'
                }
            }
        }, function(self) {
            return {
                init: function() {
                },

                '{self} onChoiceTitleChanged': function(el, event, index, data) {
                    self.checkboxTitle().eq(index).text(data);
                },

                '{self} onChoiceValueChanged': function(el, event, index, data) {
                    self.checkboxInput().eq(index).val(data);
                },

                '{self} onChoiceAdded': function(el, event, index, data) {
                    if(self.checkbox().eq(index).length > 0) {
                        self.checkbox().eq(index).before(self.view.sample());
                    } else {
                        self.checkboxes().append(self.view.sample());
                    }
                },

                '{self} onChoiceRemoved': function(el, event, index) {
                    self.checkbox().eq(index).remove();
                },

                '{self} onChoiceToggleDefault': function(el, event, index, value) {
                    var element = self.checkboxInput().eq(index);

                    if(value) {
                        element.prop('checked', true);
                    } else {
                        element.prop('checked', false);
                    }
                }
            }
        });

        module.resolve();
    });
});
