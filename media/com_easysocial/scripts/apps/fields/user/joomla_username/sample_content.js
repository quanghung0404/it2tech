EasySocial.module('apps/fields/user/joomla_username/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Joomla_username.Sample', {
        defaultOptions: {
            '{checkUsername}'       : '[data-check-username]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'check_username':
                        self.checkUsername().toggle(!!value);
                    break;
                }
            }
        }
    });

    module.resolve();
});
