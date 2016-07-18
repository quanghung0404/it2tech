EasySocial.module('apps/fields/user/avatar/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Avatar.Sample', {
        defaultOptions: {
            '{upload}': '[data-avatar-upload]',

            '{gallery}': '[data-avatar-gallery]',

            '{galleryTitle}': '[data-avatar-gallery-title]',

            '{galleryButton}': '[data-avatar-gallery-button]',

            '{gallerySelection}': '[data-avatar-gallery-selection]'
        }
    }, function(self) {
        return {
            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'upload':
                        self.upload().toggle(value);
                    break;

                    case 'gallery':
                        self.gallery().toggle(value);
                    break;

                    case 'use_gallery_button':
                        self.galleryButton().toggle(value);

                        self.gallerySelection().toggle(!value);

                        self.galleryTitle().toggle(!value);
                    break;
                }
            }
        }
    });

    module.resolve();
});
