EasySocial.module('apps/fields/user/datetime/sample_content', function($) {
    var module = this;

    EasySocial.require().library('ui/datepicker').done(function() {
        EasySocial.Controller('Field.Datetime.Sample', {
            defaultOptions: {
                '{yearPrivacy}'     : '[data-yearprivacy]',

                '{input}'           : '[data-field-datetime-select]',

                '{timezone}'        : '[data-field-datetime-timezone]'
            }
        }, function(self) {
            return {
                init: function() {
                },

                '{self} onConfigChange': function(el, event, name, value) {
                    switch(name) {
                        case 'year_privacy':
                            self.yearPrivacy().toggle(value);
                        break;

                        case 'allow_timezone':
                            self.timezone().toggle(value);
                        break;

                        case 'placeholder':
                            self.input().attr('placeholder', value);
                        break;
                    }
                }
            }
        });

        module.resolve();
    });
});
