EasySocial.module('apps/fields/user/country/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Country.Sample', {
        defaultOptions: {
            '{inputGeneral}'        : '[data-country-select]',

            '{inputTextboxlist}'    : '[data-country-select-textboxlist]',

            '{inputMultilist}'      : '[data-country-select-multilist]',

            '{inputCheckbox}'       : '[data-country-select-checkbox]',

            '{inputDropdown}'       : '[data-country-select-dropdown]',

            '{maxMessage}'          : '[data-country-max-message]',

            '{maxCount}'            : '[data-country-max-count]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, ev, name, value) {
                switch(name) {
                    case 'select_type':
                        self.inputGeneral().hide();

                        if(value === 'textboxlist') {
                            self.inputTextboxlist().show();
                        }

                        if(value === 'multilist') {
                            self.inputMultilist().show();
                        }

                        if(value === 'checkbox') {
                            self.inputCheckbox().show();
                        }

                        if(value === 'dropdown') {
                            self.inputDropdown().show();
                        }
                        break;

                    case 'multilist_size':
                        self.inputMultilist().attr('size', value);
                        break;

                    case 'show_max_message':
                        self.maxMessage().toggle(!!value);
                        break;

                    case 'max':
                        self.maxCount().text(value);
                        break;
                }
            }
        }
    });

    module.resolve();
});
