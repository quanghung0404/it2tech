EasySocial.module("story/videos", function($){

	var module = this;

	EasySocial.require()
	.library('image', 'plupload')
	.language(
		"COM_EASYSOCIAL_VIDEOS_STORY_SELECT_CATEGORY",
		"COM_EASYSOCIAL_VIDEOS_STORY_ENTER_VIDEO",
		"COM_EASYSOCIAL_VIDEOS_STORY_PROCESSING_VIDEO",
		"COM_EASYSOCIAL_VIDEOS_STORY_CLICK_INSERT_VIDEO",
		"COM_EASYSOCIAL_VIDEOS_STORY_NO_VIDEO_DETECTED"
	)
	.done(function($){

	EasySocial.Controller("Story.Videos", {
		defaultOptions: {

			// This is the main wrapper for the form
			"{form}": "[data-video-form]",

			// Video links
			"{insertVideo}": "[data-insert-video]",
			"{videoLink}": "[data-video-link]",
			"{videoCategory}": "[data-video-category]",

			// Video uploads
			"{uploaderForm}": "[data-video-uploader]",
			"{uploaderButton}": "[data-video-uploader-button]",
			"{uploaderDropsite}": "[data-video-uploader-dropsite]",
			"{uploaderProgressBar}": "[data-video-uploader-progress-bar]",
			"{uploaderProgressText}": "[data-video-uploader-progress-text]",

			// Video preview
			"{removeButton}": "[data-remove-video]",
			"{previewImageWrapper}": "[data-video-preview-image]",
			"{previewTitle}": "[data-video-preview-title]",
			"{title}": "[data-video-title]",
			"{previewDescription}": "[data-video-preview-description]",
			"{description}": "[data-video-description]"
		}
	}, function(self, opts, base) { return {

			init: function() {


				// If video uploader form doesn't exist, perhaps the admin already disabled this
				if (self.uploaderForm().length == 0) {
					return;
				}

				// Implement plupload
				self.uploader = self.uploaderForm().addController("plupload", $.extend({
						"{uploadButton}": self.uploaderButton.selector,
						"{uploadDropsite}": self.uploaderDropsite.selector
					}, opts.uploader)
				);

				self.plupload = self.uploader.plupload;
			},

			isProcessed: function() {
				self.form().switchClass('is-processed');

				self.processing = false;
			},

			isUploading: function() {
				self.form().switchClass('is-uploading');
			},

			isProcessing: function() {
				self.form().switchClass('is-processing');

				self.processing = true;
			},

			isInitial: function() {
				self.form().switchClass('is-waiting');
			},

			currentCategory: null,
			processing: false,
			video: null,
			videoType: null,

			updatePreview: function(type, data, imageUrl) {

				self.video = {
					"type": type,
					"title": data.title,
					"description": data.description,
					"link": data.link,
					"id": data.id ? data.id : '',
					"isEncoding": false
				};

				// Update the title
				if (data.title) {
					self.previewTitle().html(data.title);
				}

				// Update the description
				if (data.description) {
					self.previewDescription().html(data.description);
				}

				// Load the image
				$.Image.get(imageUrl).done(function(image){
					image.appendTo(self.previewImageWrapper());
				});

			},

			resetProgress: function() {

				// Reset the progress bar
				self.uploaderProgressBar().css('width', '0%');
				self.uploaderProgressText().html('0%');
			},

			clearForm: function(resetVideo) {

				if (resetVideo) {
					self.video = null;
				}

				// Set to initial position
				self.isInitial();

				// Reset all the form values
				self.videoLink().val('');

				self.previewImageWrapper().html('');

				self.previewTitle().html('');
				self.title().val('');

				self.previewDescription().html('');
				self.description().val('');
			},

			editTitleEvent: "click.es.story.video.editLinkTitle",
			editDescriptionEvent: "click.es.story.video.editLinkDescription",

			editTitle: function() {

				// Apply the class to the form wrapper
				self.form().addClass('editing-title');

				setTimeout(function(){

					self.title()
						.val(self.previewTitle().text())
						.focus()[0]
						.select();

					$(document).on(self.editTitleEvent, function(event) {
						if (event.target !== self.title()[0]) {
							self.saveTitle("save");
						}
					});

				}, 1);
			},

			saveTitle: function(operation) {

				if (!operation) {
					operation = 'save';
				}

				var value = self.title().val();

				if (operation == 'save') {
					self.previewTitle().html(value);
				}

				// Remove the editing title class
				self.form().removeClass('editing-title');

				self.video.title = value;

				$(document).off(self.editTitleEvent);
			},

			checkVideoStatus: function(videoId) {
				EasySocial.ajax('site/controllers/videos/status', {
					"id": videoId,
					"uid": opts.video.uid,
					"type": opts.video.type,
					"createStream": 0
				}).done(function(permalink, progress, data, thumbnail) {

					if (progress === 'done') {

						self.processing = false;

						// Set the progress bar to 100%
						self.uploaderProgressBar().css('width', '100%');
						self.uploaderProgressText().html('100%');

						// Update the state
						self.isProcessed();

						// Update the preview
						self.updatePreview('upload', data, thumbnail);

						// Reset the progress bar
						self.resetProgress();

						return;
					}

					// Set the progress bar width
					var progress = progress + '%';
					self.uploaderProgressBar().css('width', progress);
					self.uploaderProgressText().html(progress);

					// This should run in a loop
					self.checkVideoStatus(videoId);
				});
			},

			editDescription: function() {

				self.form().addClass('editing-description');

				setTimeout(function(){

					var descriptionClone = self.previewDescription().clone();
					var noDescription = descriptionClone.hasClass("no-description");

					descriptionClone.wrapInner(self.description());

					if (noDescription) {
						self.description().val("");
					}

					self.description()
						.val(self.previewDescription().text())
						.focus()[0].select();

					$(document).on(self.editDescriptionEvent, function(event) {

						if (event.target!==self.description()[0]) {
							self.saveDescription("save");
						}
					});
				}, 1);
			},

			saveDescription: function(operation) {

				if (!operation) {
					operation = 'save';
				}

				var value = self.description().val().replace(/\n/g, "<br//>");

				switch (operation) {

					case "save":

						var noValue = (value==="");

						self.previewDescription()
							.toggleClass("no-description", noValue);

						if (noValue) {
							value = self.description().attr("placeholder");
						}

						self.previewDescription()
							.html(value);

						self.video.description = value;
						break;

					case "revert":
						break;
				}

				self.form().find(".textareaClone").remove();

				self.form().removeClass("editing-description");

				$(document).off(self.editDescriptionEvent);
			},

			"{uploaderForm} FilesAdded": function() {

				// Set the state to uploading
				self.isUploading();

				// Start the upload
				self.plupload.start();
			},

			"{uploaderForm} FileUploaded": function(uploaderForm, event, uploader, file, response) {

				// Server thrown an error
				if (response.error) {

					// Set the message
					self.clearMessage();
					self.setMessage(response.error);

					// Display the video upload form again
					self.clearForm(true);

					return false;
				}

				// If the server isn't encoding on the fly, we should display some message
				if (!response.isEncoding) {

					self.processing = false;

					// Set the progress bar to 100%
					self.uploaderProgressBar().css('width', '100%');
					self.uploaderProgressText().html('100%');

					// Update the state
					self.isProcessed();

					// Update the preview
					self.updatePreview('upload', response.data, response.thumbnail);

					self.video.isEncoding = true;

					// Reset the progress bar
					self.resetProgress();

					return;
				}

				self.processing = true;

				// Update the progress since the video needs to be converted.
				self.checkVideoStatus(response.data.id);
			},

			"{uploaderForm} Error": function(el, event, uploader, error) {

				// Get the error message
				var message = opts.errors[error.code];

				self.story.setMessage(message, "error");
			},

			"{previewTitle} click": function() {

				var editing = self.form().hasClass('editing-title');

				self.form().toggleClass('editing-title', !editing);

				if (!editing) {
					self.editTitle();
				}
			},

			"{previewDescription} click": function() {
				var editing = self.form().hasClass('editing-description');

				self.form().toggleClass('editing-description', !editing);

				if (!editing) {
					self.editDescription();
				}
			},

			"{videoCategory} change": function(videoCategory) {
				self.currentCategory = videoCategory.val();
			},

			"{insertVideo} click": function() {

				var url = self.videoLink().val();

				if (!url || self.processing) {
					return;
				}

				// Hide the form
				self.isProcessing();

				EasySocial.ajax('ajax:/apps/user/videos/controllers/process/process', {
					"type": "link",
					"link": url
				}).done(function(data, image, embed) {
					self.isProcessed();

					data.link = url;

					self.updatePreview('link', data, image);
				});
			},

			"{removeButton} click": function(removeButton) {
				self.clearForm(true);
			},

			//
			// Saving
			//

			"{story} save": function(element, event, save) {

                if (save.currentPanel != 'videos') {
                    return;
                }

				var url = self.videoLink().val();
				if (url && !self.video) {
					save.reject($.language('COM_EASYSOCIAL_VIDEOS_STORY_CLICK_INSERT_VIDEO'));
					return;
				}

				if (!url && !self.video) {
					save.reject($.language('COM_EASYSOCIAL_VIDEOS_STORY_NO_VIDEO_DETECTED'));
					return;
				}

				// Add the task for uploading video
				self.uploadingVideo = save.addTask("uploadingVideo");

				self.save(save);
			},

			"{story} afterSubmit": function() {

				var uploadingVideo = self.uploadingVideo;

				if (!uploadingVideo) {
					return;
				}

				// Reset the form upon submission
				self.clearForm(true);

				delete self.uploadingVideo;

				if (self.video && self.video.isEncoding) {

					EasySocial.dialog({
						content: EasySocial.ajax('site/views/videos/showEncodingMessage')
					});

					delete self.video;
					return;
				}

				delete self.video;
			},

			save: function(save) {

				var uploadingVideo = self.uploadingVideo;

				if (!uploadingVideo) {
					return;
				}

				if (self.processing) {
					save.reject($.language('COM_EASYSOCIAL_VIDEOS_STORY_PROCESSING_VIDEO'));
					return;
				}

				// Attach the category to the video data
				self.video.category = self.videoCategory().val();

				if (!self.video.category || self.video.category == 0) {
					save.reject($.language('COM_EASYSOCIAL_VIDEOS_STORY_SELECT_CATEGORY'));
					return;
				}

				save.addData(self, self.video);

				uploadingVideo.resolve();

				self.videoType = self.video.type;
			},

			"{story} clear": function() {
				self.clearForm(false);
			}
	}});

	// Resolve module
	module.resolve();

	});
});
