EasyBlog.module("composer/document/toolbar", function($){

    var module = this;

    EasyBlog.Controller("Composer.Document.Toolbar", {
        elements: [
            "[data-eb-composer-{add-block-button|add-media-button|add-post-button|show-drawer-button}]",
            "[data-eb-composer-{embed-video-button}]",
            "[data-eb-composer-{mobile-blip}]"
        ],
        defaultOptions: {
        }
    }, function(self, opts, base, composer) { return {

        init: function() {
            composer = self.document.composer;
        },

        activate: function() {
            self.mobileBlip().addClass('show-menu');
        },

        deactivate: function() {
            self.mobileBlip().removeClass('show-menu');
        },

        isActive: function() {
            return self.mobileBlip().hasClass('show-menu');
        },

        "{mobileBlip} click": function(mobileBlip, event) {
            
            if (self.isActive()) {
                self.deactivate();
                return;
            }

            self.activate();
        },

        "{addBlockButton} click": function() {
            self.deactivate();

            composer.views.show("blocks");
        },

        "{addMediaButton} click": function() {
            self.deactivate();

            composer.views.show("media");
        },

        "{embedVideoButton} click": function() {
            EasyBlog.dialog({
                content: EasyBlog.ajax('site/views/composer/embedVideoDialog'),
                bindings: {
                    "{insertButton} click": function() {
                        var url = this.videoUrl().val();
                        var width = this.videoWidth().val();
                        var height = this.videoHeight().val();

                        var data = '[embed=videolink]'
                                    + '{"video":"' + url + '","width":"' + width + '","height":"' + height + '"}'
                                    + '[/embed]';

                        EasyBlog.LegacyEditor.insert(data);

                        // After inserting the video, close the dialog
                        EasyBlog.dialog().close();

                        // Reset the input
                        this.videoUrl().val('');
                    }
                }
            });
        },

        "{addPostButton} click": function() {
            self.deactivate();

            composer.views.show("posts");
        },

        "{showDrawerButton} click": function() {
           $('[data-eb-composer-frame]').toggleClass('show-drawer');
        }

    }});

    module.resolve();

});
