EasySocial.module('apps/fields/user/joomla_email/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Joomla_email.Sample', {
        defaultOptions: {
            '{confirmEmail}'        : '[data-field-email-reconfirm-frame]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'reconfirm_email':
                        self.confirmEmail().toggle(value);
                    break;
                }
            }
        }
    });

    module.resolve();
});
