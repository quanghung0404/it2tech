EasySocial.module('apps/fields/user/joomla_fullname/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Joomla_fullname.Sample', {
        defaultOptions: {
            '{fullnameFormat}'      : '[data-fullname-format]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'format':
                        self.switchFormat(value);
                    break;
                }
            },

            switchFormat: function(value) {
                self.fullnameFormat().hide();

                self.fullnameFormat().eq(value - 1).show();
            }
        }
    });

    module.resolve();
});
