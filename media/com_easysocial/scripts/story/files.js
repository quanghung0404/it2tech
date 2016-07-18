EasySocial.module("story/files", function($){

	var module = this;

	EasySocial.require()
		.library('plupload')
		.view('apps/user/files/story/attachment.item', 'apps/user/files/story/progress')
		.done(function(){

			EasySocial.Controller("Story.Files",
				{
					defaultOptions: {

						"{canvas}": "[data-files-canvas]",
						"{dropsite}": "[data-files-dropsite]",
						"{upload}": "[data-files-upload]",

						"{uploadGroup}": "[data-files-items]",
						"{fileItem}": "[data-files-item]",
						"{removeItem}": "[data-files-item-remove]",
						"{uploadItem}": "[data-files-upload-item]",


						settings: {
							url: null,
							max_file_size: null,
							filters: []
						},

						view: {
							attachment: "apps/user/files/story/attachment.item",
							progress: "apps/user/files/story/progress"
						}
					}
				},
				function(self) { return {

					items: {},

					init: function() {

						// Initialize plupload's settings
						var options = $.extend({
													"{uploadButton}": self.upload.selector,
													"{uploadDropsite}": self.dropsite.selector
												}, {"settings": self.options.settings});


						// Implement plupload controller on the canvas
						self.uploader = self.canvas().addController('plupload', options);

	                    // Plupload
	                    self.plupload = self.uploader.plupload;

	                    // Add the uploader
	                    self.addPlugin("uploader", self.uploader);

	                    // Indicate uploader supports drag & drop
	                    if (!$.IE && self.plupload.runtime=="html5") {
	                        self.canvas().addClass("can-drop-file");
	                    }

	                    // Indicate uploader is ready
	                    self.canvas().addClass("can-upload");

	                    self.setLayout();
					},

					hasItems: function() {
						var hasItem = self.fileItem().length > 0,
							hasUploadItem = self.uploadItem().length > 0;

						return hasItem || hasUploadItem;
					},

					setLayout: function() {

						// Show upload hint when content is empty
						self.canvas()
							.toggleClass("has-items", self.hasItems());
					},

					removeFile: function(id) {

						// Remove photo item
						self.fileItem()
							.where('id', id)
							.remove();

						self.setLayout();
					},

					clearFiles: function(){

						self.fileItem().remove();
						self.uploadItem().remove();

						self.setLayout();
					},

	                removeFileItem: function(id) {

	                    var item = self.getItem(id);
	                    
	                    if (!item) {
	                    	return;
	                    }


	                    // Remove item
	                    self.plupload.removeFile(item.file());
	                    item.element.remove();
	                    delete self.items[id];

	                    self.setLayout();
	                },

	                getItem: function(file) {

	                    var id;

	                    // By id
	                    if ($.isString(file)) {
	                    	id = file;
	                    }

	                    // By file object
	                    if (file && file.id) {
	                    	id = file.id;
	                    }

	                    return self.items[id];
	                },

	                createItem: function(file) {

						// Get the view item
	                    var item = self.view.progress({file: file});

	                    // Add to item group
	                    self.uploadGroup().append(item);

	                    // Keep a copy of the item in our registry
	                    self.items[file.id] = item;

	                    self.setLayout();

	                    self.trigger("QueueCreated", [item]);

	                    return item;
	                },

					"{uploader} FilesAdded": function(el, event, uploader, files) {

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

	                    self.setLayout();

						// Begin the upload process
						self.uploader.plupload.start();
					},

					"{uploader} FileUploaded": function(el, event, uploader, file, response) {

						var progress = self.getItem(file),
							attachmentItem = self.view.attachment({"file" : file, "id" : response.id});

						// Insert the preview after the progress
						attachmentItem
							.data('file-id', response.id)
							.addClass('new-item')
							.insertAfter(progress);

						// Remove the progress
						progress.remove();

						self.setLayout();

						// Remove the new-item class since we want it to be displayed on the screen once it is added
						setTimeout(function(){
							attachmentItem.removeClass("new-item");
						}, 1);
					},

	                "{uploader} UploadProgress": function(el, event, uploader, file) {

	                    var item = self.getItem(file);

	                    if (!item) {
	                    	return;
	                    }

		            	var noFilesize = (file.size===undefined || file.size=="N/A");
		            	file.percentage = file.percent + "%";
		                file.filesize   = (noFilesize) ? "" : $.plupload.formatSize(file.size);
		                file.remaining  = (noFilesize) ? "" : $.plupload.formatSize(file.size - (file.loaded || 0));

	                    var percentage = file.percentage;

						// Never use 100% because users might think
						// the photo is completely uploaded when it might
						// still be working.
						if (percentage=="100%") {
							percentage = "99%";
						}

						if (percentage=="0%") {
							percentage = "1%";
						}

						item.find('.upload-progress-bar')
							.width(percentage);

						// Set the percentage
						item.find('.upload-percentage')
							.html(percentage);
	                },


					"{uploader} FileError": function(el, event, uploader, file, response) {

						self.story.setMessage(response.message, "error");

						var uploadingFile = self.uploadingFile;

						if (uploadingFile) {
							uploadingFile.reject();

							delete self.uploadingFile;
						}

						self.setLayout();
					},

					"{uploader} Error": function(el, event, uploader, error) {

						self.story.setMessage(error.message, "error");

						var uploadingFile = self.uploadingFile;

						if (uploadingFile) {
							uploadingFile.reject();

							delete self.uploadingFile;
						}

						// Temporary workaround. Delegated event don't work
						// because the element has been removed.
						self.removeItem()
							.click(function(){
								setTimeout(function(){
									self.setLayout();
								}, 1);
							});

						self.setLayout();
					},

					"{removeItem} click": function(el) {

						var id = el.parent(self.fileItem.selector).data('id');

						// Remove item
						self.removeFile(id);
					},

					//
					// Saving
					//
					"{story} save": function(element, event, save) {

						if (!self.hasItems()) {
							return;
						}

						self.uploadingFile = save.addTask('uploadingFile');
						self.save();
					},

					save: function() {

						var uploadingFile = self.uploadingFile;

						if (!uploadingFile) {
							return;
						}

						var items = self.fileItem();

						if (items.length) {

							var files = []
								save = uploadingFile.save;

							items.each(function(){
								files.push($(this).data('id'));
							});

							save.addData(self, files);

							uploadingFile.resolve();

							delete self.uploadingFile;
						}
					},

					"{story} clear": function() {

						self.clearFiles();
					}
				}}
			);

			// Resolve module
			module.resolve();

		});
});
