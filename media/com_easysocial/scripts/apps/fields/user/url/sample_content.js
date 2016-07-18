EasySocial.module('apps/fields/user/url/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Url.Sample', {
        defaultOptions: {
            '{urlInput}': '[data-url-input]'
        }
    }, function(self) {
        return {
            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'placeholder':
                        self.urlInput().attr('placeholder', value);
                    break;

                    case 'default':
                        self.urlInput().val(value);
                    break;
                }
            }
        }
    });

    module.resolve();
});
