EasySocial.module('apps/fields/user/file/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.File', {
        defaultOptions: {
            '{sizeText}': '[data-field-file-size-text]',

            '{size}': '[data-field-file-size]',

            '{add}': '[data-field-file-add]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, ev, name, value) {
                switch(name) {
                    case 'size_limit':
                        self.size().text(value);
                        break;

                    case 'show_size_limit':
                        self.sizeText().toggle(!!value);
                        break;

                    case 'file_limit':
                        self.add().toggle((value < 1 || value > 1));
                        break;
                }
            }
        }
    });

    module.resolve();
});
