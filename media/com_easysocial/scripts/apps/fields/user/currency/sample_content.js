EasySocial.module('apps/fields/user/currency/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Currency.Sample', {
        defaultOptions: {

        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'format':
                    break;
                }
            }
        }
    });

    module.resolve();
});
