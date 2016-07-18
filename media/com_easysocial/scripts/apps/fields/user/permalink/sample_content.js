EasySocial.module('apps/fields/user/permalink/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Permalink.Sample', {
        defaultOptions: {
            '{checkPermalink}'      : '[data-check-permalink]'
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'check_permalink':
                        self.checkPermalink().toggle(!!value);
                    break;
                }
            }
        }
    });

    module.resolve();
});
