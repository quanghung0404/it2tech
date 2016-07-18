EasyBlog.module('comments/captcha', function($) {

    var module = this;

    EasyBlog.require()
    .done(function($) {

        EasyBlog.Controller('Comments.Form.Captcha', {

            defaultOptions: {
                "{input}": "[data-captcha-input]",
                "{reload}": "[data-captcha-reload]",
                "{captchaId}": "[data-captcha-id]",
                "{image}": "[data-captcha-image]"
            }
        }, function(self, opts, base) {

            return {

                init: function() {
                },

                "{self} submitComment": function(el, event, data) {
                    data['captcha-response'] = self.input().val();
                    data['captcha-id'] = self.captchaId().val();
                },

                "{self} resetForm": function() {
                    self.input().val('');
                    
                    self.reload().click();
                },

                "{self} reloadCaptcha": function() {

                    EasyBlog.ajax('site/views/comments/reloadCaptcha', {
                        "previousId": self.captchaId().val()
                    })
                    .done(function(newImage, newCaptchaId) {
                        self.image().attr('src', newImage);

                        self.captchaId().val(newCaptchaId);
                    });
                },

                "{reload} click": function() {
                    self.trigger('reloadCaptcha');
                }
            }
        });

        module.resolve();
    });
});