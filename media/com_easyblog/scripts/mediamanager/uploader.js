EasyBlog.module("mediamanager/uploader", function($){

var module = this;

EasyBlog.require()
.library(
    "plupload2"
)
.done(function(){

EasyBlog.Controller("MediaManager.Uploader", {
    elements: [
        "[data-eb-mm-{upload-progress|upload-name|upload-size|upload-progress-value|upload-progress-bar}]",

        "[data-eb-mm-upload-{error-text|error-retry}]"
    ],
    defaultOptions: {
    }
}, function(self, opts, base, mediaManager) { return {

    init: function() {

        // Globals
        mediaManager = self.mediaManager;

        var defaultUploadOptions = {
            runtimes: "html5, html4",
            autostart: true
        };

        var inlineDefaultUploadOptions = mediaManager.frame().htmlData("mm-uploader");

        self.defaultUploadOptions = $.extend(
            defaultUploadOptions,
            inlineDefaultUploadOptions
        );
    },

    instances: {},

    register: function(container, options) {

        // Normalize options
        var options = $.extend({}, self.defaultUploadOptions, options);

        // Set key in url
        var key = container.data("key");

        // We need to know if the uri is "post" so that we can translate it
        var uri = EasyBlog.MediaManager.getUri(key);

        if (uri == 'post') {
            key = EasyBlog.MediaManager.getKey(EasyBlog.MediaManager.getCurrentPostUri());
        }

        // Get the list of default allowed extensions
        var allowedExtensions = options.extensions.split(',');

        $.each(EasyBlog.MediaManager.options.types, function(i, extensions) {

            // Go through each of the allowed extension
            $(extensions).filter(function(index, extension){
                return $.inArray(extension, allowedExtensions);
            });
        });

        // Since there is no "file" extensions, add it into the types
        EasyBlog.MediaManager.options.types['file'] = allowedExtensions;

        // Get the type of service provided so that we can set which extensions are allowed
        var type = container.data('type') || 'file';

        // Set the default allowed extensions
        options.extensions = EasyBlog.MediaManager.options.types[type].join(',');

        // Get the upload url
        options.url = $.uri(options.url).replaceQueryParam("key", key).toString();

        // Create uploader instance but don't initialize it yet
        // because we need to bind event handlers first.
        var uploader = container.plupload2(options, false);
        var id = uploader.id;

        // Assign destination uri to uploader
        uploader.uri = mediaManager.getUri(key);

        // Keep a reference to the container
        uploader.container = container;

        // Keep a reference to our uploaders
        self.instances[id] = uploader;

        // Bind event handlers
        $.each(self.plupload, function(name, handler) {
            uploader.bind(name, function(){

                // EasyBlog.debug && console.info("mediaUploader" + name, arguments);

                // First, we handle plupload events.
                handler.apply(this, arguments);

                // Then, we forward them as mediaUploader events.
                self.trigger("mediaUploader" + name, arguments);
                container.trigger("mediaUploader" + name, arguments);

                // Always trigger mediaUploaderChange
                self.trigger("mediaUploaderChange", arguments);
                container.trigger("mediaUploaderChange", arguments);
            });
        });

        // Initialize uploader now
        uploader.init();

        // EasyBlog.debug && console.log("Register Uploader", container, uploader);

        return uploader;
    },

    //
    // Event Handlers
    //

    plupload: {

        FileFiltered: function(uploader, file) {

            // Extend file object with items
            file.items = [];
            file.addedDate = new Date().getTime();
        },

        FilesAdded: function(uploader, files) {

            // Start uploading if autostart is true
            // For blog image and image block, this is false
            // because we don't want to upload until user confirms it.
            if (uploader.settings.autostart) {
                uploader.start();
            }
        },

        BeforeUpload: function(uploader, file) {
            self.updateAllItems(file);
        },

        UploadFile: function(uploader, file) {
            self.updateAllItems(file);
        },

        UploadProgress: function(uploader, file) {
            self.updateAllItems(file);
        },

        ChunkUploaded: function(uploader, file) {
            self.updateAllItems(file);
        },

        FileUploaded: function(uploader, file, data) {

            try {
                // Convert response json into object
                var response = $.parseJSON(data.response);
                data.response = response;
            } catch (e) {};

            self.updateAllItems(file);
        },

        Error: function(uploader, error) {

            // If this is a file level error
            var file = error.file;

            if (file) {

                if (error.code == '-600') {
                    file.status = 4;
                    file.error = error.message;

                    EasyBlog.dialog({
                        content: EasyBlog.ajax('site/views/composer/cancelFileSizeWarning')
                    });

                    self.updateAllItems(file);

                    // Trigger specific FileError event
                    self.trigger("mediaUploaderFileError", arguments);
                    uploader.container.trigger("mediaUploaderFileError", arguments);

                    return;
                }

                // If we hit an error, display the error message
                file.status = 4;
                file.error = $.parseJSON(error.response);

                self.updateAllItems(file);

                // Trigger specific FileError event
                self.trigger("mediaUploaderFileError", arguments);
                uploader.container.trigger("mediaUploaderFileError", arguments);
            }
        },

        FilesRemoved: function(uploader, file) {

            // We will not remove file items. It is up to the
            // implementor to decide if they want to remove it.
            file.status = 6;
            self.updateAllItems(file);
        },

        Destroy: function(uploader) {
            delete self.instances[uploader.id];
        },

        // Unused events
        Init: $.noop,
        PostInit: $.noop,
        OptionChanged: $.noop,
        StateChanged: $.noop,
        QueueChanged: $.noop,
        Refresh: $.noop,
        UploadComplete: $.noop
    },

    //
    // Aggregated Uploader API
    //
    getInstances: function(uri) {

        return $.map(self.instances, function(uploader){
            return uploader.uri==uri ? uploader : null;
        });
    },

    getFiles: function(uri) {

        var instances = self.getInstances(uri),
            files =
                $.chain(instances)
                    .pluck("files")
                    .flatten(true)
                    .sortBy("addedDate")
                    .value();

        return files;
    },

    //
    // Item API
    //

    // The following status matches values of file.status.
    // Access using self.status[file.status]
    status: {
        "0": "idle",      // 0 - Non-standard
        "1": "queued",    // 1
        "2": "uploading", // 2
        "3": "unused",    // 3 - Unused
        "4": "failed",    // 4
        "5": "done",      // 5
        "6": "removed"    // 6 - Non-standard
    },

    addItem: function(file, item) {
        file.items.push(item);
        self.updateItem(file, item);
    },

    updateItem: function(file, item) {

        var item = $(item);

        // Update the state on the uploader element
        var state = self.status[file.status];
        item.switchClass("state-" + state);

        // If there's an error, display the error
        if (file.error) {

            item.find(self.errorText)
                .html(file.error.message);

            item.find(self.errorRetry)
                .off('click.mmupload.retry')
                .on('click.mmupload.retry', function(){
                    item.switchClass('state-idle');
                });

            return;
        }

        // Update title
        item.find(self.uploadName)
            .html(file.name);

        // Update size
        item.find(self.uploadSize)
            .html($.plupload2.formatSize(file.size));

        // Display the percentage value
        item.find(self.uploadProgressValue)
            .html(file.percent + '%');

        // Update progress bar
        item.find(self.uploadProgressBar)
            .width(file.percent + "%");
    },

    updateAllItems: function(file) {

        if (!file.items) {
            return;
        }

        $.each(file.items, function(i, item){

            self.updateItem(file, item);
        });
    },

    removeItem: function(file, item) {

        $.pull(file.items, item);
    }
}});

module.resolve();

});

});

