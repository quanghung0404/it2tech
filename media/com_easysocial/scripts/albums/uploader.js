// module: start
EasySocial.module("albums/uploader", function($) {

    var module = this;

    // require: start
    EasySocial.require()
    .library("plupload")
    .script("albums/uploader.item")
    .view("site/albums/upload.item")
    .done(function(){

        // controller: start
        EasySocial.Controller("Albums.Uploader", {
    		defaultOptions: {

                view: {
                    uploadItem: "site/albums/upload.item"
                },

                direction: 'prepend',

                "{uploadButton}"   : "[data-upload-button]",
                "{uploadItemGroup}": "[data-upload-item-group]",
                "{uploadItem}"     : "[data-upload-item]",
                "{uploadDropsite}" : "[data-upload-dropsite]"
    		}
    	}, function(self, opts, base) { return {

                init: function() {

                    var uploader = self.element;

                    // Plupload controller
                    self.pluploadController =
                        self.element
                            .addController(
                                "plupload",
                                $.extend({
                                    "{uploadButton}" : self.uploadButton.selector,
                                    "{uploadDropsite}": self.uploadDropsite.selector
                                },self.options.settings)
                            );

                    // Plupload
                    self.plupload = self.pluploadController.plupload;

                    // Indicate uploader supports drag & drop
                    if (!$.IE && self.plupload.runtime=="html5") {

                        uploader.addClass("can-drop-file");
                    }

                    // Indicate uploader is ready
                    uploader.addClass("can-upload");
        		},

        		setLayout: function() {

                    self.uploadItemGroup().toggleClass("no-upload-items", self.uploadItem().length < 1);
        		},

                items: {},

                getItem: function(file) {

                    var id;

                    // By id
                    if ($.isString(file)) id = file;

                    // By file object
                    if (file && file.id) id = file.id;

                    return self.items[id];
                },

                createItem: function(file) {

                    // Create item controller
                    var item =
                        self.view.uploadItem({file: file})
                            .switchClass("layout-" + (base.data("albumLayout") || "form"))
                            .addController(
                                "EasySocial.Controller.Albums.Uploader.Item",
                                {
                                    "{uploader}": self,
                                    file: file
                                }
                            );

                    // Add to item group
                    item.element[opts.direction=='append' ? 'appendTo' : 'prependTo'](self.uploadItemGroup());

                    // Keep a copy of the item in our registry
                    self.items[file.id] = item;

                    self.setLayout();

                    self.trigger("QueueCreated", [item]);

                    return item;
                },

                settings: function(key, val) {

                    var settings = self.plupload.settings;

                    // Setter
                    if (val!==undefined) {
                        settings[key] = val;
                    }

                    // Getter
                    return (key) ? settings[key] : settings;
                },

                start: function() {

                    return self.plupload.start();
                },

                stop: function() {

                    return self.plupload.stop();
                },

                "{self} FilesAdded": function(el, event, uploader, files) {

                    // Wrap the entire body in a try...catch scope to prevent
                    // browser from trying to redirect and load the file if anything goes wrong here.
                    try {

                        // Reverse upload ordering as we are prepending.
                        files.reverse();

                        $.each(files, function(i, file) {

                            // The item may have been created before, e.g.
                            // when plupload error event gets triggered first.
                            if (self.getItem(file)) return;

                            self.createItem(file);
                        });

                    } catch (e) {

                        console.error(e);
                    };
                },

                "{self} BeforeUpload": function(el, event, uploader, file) {

                    var item = self.getItem(file);
                    if (!item) return;

                    item.setState("preparing");
                },

                "{self} UploadFile": function(el, event, uploader, file) {

                    var item = self.getItem(file);
                    if (!item) return;

                    item.setState("uploading");
                },

                "{self} UploadProgress": function(el, event, uploader, file) {

                    var item = self.getItem(file);
                    if (!item) return;

                    item.setState("uploading");
                    item.setProgress();
                },

                "{self} FileUploaded": function(el, event, uploader, file, response) {

                    var item = self.getItem(file);
                    if (!item) return;

                    // If the response is not a valid object
                    if (!$.isPlainObject(response)) {

                        // Set upload item state to failed.
                        item.setState("failed");
                        return;
                    }

                    item.setState("done");
                },

                "{self} FileError": function(el, event, uploader, file, response) {

                    var item = self.getItem(file);

                    // If the item hasn't been created, create first.
                    if (!item) item = self.createItem(file);

                    item.setState("failed");
                    item.setMessage(response.message);
                },

                "{self} Error": function(el, event, uploader, error) {

                    // If the returned error object also returns a file object
                    if (error.file) {

                        // Check if the upload item has been created
                        var file = error.file,
                            item = self.getItem(file);

                        // If the upload item doesn't exist
                        if (!item) item = self.createItem(file);

                        item.setState("failed");
                        item.setMessage(error.message);
                    }
                },

                removeItem: function(id) {

                    var item = self.getItem(id);
                    if (!item) return;

                    // Remove item
                    self.plupload.removeFile(item.file());
                    item.element.remove();
                    delete self.items[id];

                    self.setLayout();
                },

                clear: function(id) {

                    $.each(self.items, function(id, item){

                        // Remove item
                        self.plupload.removeFile(item.file());
                        item.element.remove();
                        delete self.items[id];
                    });

                    self.items = {};
                }

        	}}

        );
        // controller: end

    module.resolve();

    });
    // require: end

});
// module: end
