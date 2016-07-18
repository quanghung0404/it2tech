EasySocial.module('apps/fields/user/joomla_password/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Joomla_password.Sample', {
        defaultOptions: {
            '{confirmPassword}'     : '[data-password-confirm]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'reconfirm_password':
                        self.confirmPassword().toggle(value);
                    break;
                }
            }
        }
    });

    module.resolve();
});
