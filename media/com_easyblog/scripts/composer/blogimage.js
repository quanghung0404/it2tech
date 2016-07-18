EasyBlog.module("composer/blogimage", function($){

var module = this;

EasyBlog.require()
.library(
    "image",
    "plupload2",
    "ui/droppable"
)
.done(function() {

EasyBlog.Controller("Composer.Blogimage", {
    elements: [
        "[data-eb-composer-blogimage-{browse-button|remove-button|image|workarea}]",
        ".eb-composer-{field}-blogimage"
    ],

    defaultOptions: {
        "{browseButton}": "[data-eb-composer-blogimage-placeholder] [data-eb-mm-browse-button]",
        "{placeholder}": "[data-eb-composer-blogimage-placeholder]",
        "{data}": "[data-eb-composer-blogimage-value]",
        "{progress}": "[data-eb-mm-upload-progress]",
        "{addCoverButton}": ".eb-document-add-cover-button"
    }
}, function(self, opts, base) { return {

    defaultUri: null,

    init: function() {

        // Get the placeholder so that we can register this with mediamanager
        var placeholder = self.placeholder();

        if (placeholder.length > 0) {
            EasyBlog.MediaManager.uploader.register(placeholder);
        }

        // Get the current image url
        var uri = self.data().val();

        // Store the current image uri in case we need to add "Revert" functionality in the future
        self.defaultUri = uri;

        // Implement droppable
        self.workarea()
            .droppable({
                accept: ".eb-mm-file",
                tolerance: "pointer"
            });
    },

    "{browseButton} mediaSelect": function(browseButton, event, media) {

        // Set the data
        self.data().val(media.meta.uri);

        // Set the image
        self.setImage(media.meta.url);
    },

    "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
        EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
    },

    "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        // Set the data with the appropriate json string
        self.data().val(mediaMeta.uri);

        // Set the image now
        self.setImage(mediaMeta.url);
    },

    "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {
    },

    "{placeholder} mediaUploaderError": function(placeholder, event, uploader, error) {
    },

    setImage: function(url) {

        self.image()
            .css("backgroundImage", $.cssUrl(url));

        setTimeout(function(){
            self.placeholder()
                .addClass("has-image has-art state-done");

            self.addCoverButton().addClass("has-cover");
        }, 250);
    },

    show: function() {
        self.composer.document.artboard.show("cover");
    },

    hide: function() {
        self.composer.document.artboard.hide("cover");
    },

    toggle: function(show) {
    },

    "{removeButton} click": function() {

        self.placeholder()
            .removeClass("has-image is-uploading has-art");

        self.addCoverButton().removeClass("has-cover");

        setTimeout(function(){

            self.image()
                .css("backgroundImage", "");

            // Remove the data
            self.data().val('');
        }, 750);
    }

}});

module.resolve();

});

});
