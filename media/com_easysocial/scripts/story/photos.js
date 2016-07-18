EasySocial.module("story/photos", function($){

	var module = this;

	EasySocial.require()
		.script("albums/uploader")
		.done(function() {

			EasySocial.Controller("Story.Photos", {
					defaultOptions: {

						"{albumView}"     : "[data-album-view]",
						"{albumContent}"  : "[data-album-content]",
						"{uploadButton}"  : "[data-album-upload-button]",

						"{photoItemGroup}": "[data-photo-item-group]",
						"{photoItem}"     : "[data-photo-item]",
						"{photoImage}"    : "[data-photo-image]",
						"{photoRemoveButton}": "[data-photo-remove-button]",
						"{uploadItem}"    : "[data-photo-upload-item]",

						"{uploadRemoveButton}": ".upload-remove-button",
					}
				},
				function(self) { return {

					init: function() {

						// I have access to:
						// self.story
						// self.attachmentButton()
						// self.attachmentItem()
						// self.attachmentContent()
						// self.attachmentToolbar()
						// self.attachmentDragHandle()
						// self.attachmentRemoveButton()

						self.uploader =
							self.albumView()
								.addController(
									EasySocial.Controller.Albums.Uploader,
									$.extend({
										"{uploadButton}"   : self.uploadButton.selector,
										"{uploadItemGroup}": self.photoItemGroup.selector,
										"{uploadDropsite}" : self.albumContent.selector
									},
									{settings: self.options.uploader})
								);

						// Difference from album viewer
						self.photoItemGroup()
							.css("opacity", 1);

						self.addPlugin("uploader", self.uploader);

						self.setLayout();
					},

					hasItems: function() {

						var hasPhotoItem  = self.photoItem().length > 0,
							hasUploadItem = self.uploadItem().length > 0;

						return hasPhotoItem || hasUploadItem;
					},

					setLayout: function() {

						// Show upload hint when content is empty
						self.albumView()
							.toggleClass("has-photos", self.hasItems());
					},

					activateAttachment: function() {

						// self.initialize();

						// if (self.attachedPhotos.length < 1) {
						// 	self.showPhotosForm();
						// }
					},

					removePhoto: function(id) {

						// Remove photo item
						self.photoItem()
							.filterBy('photoId', id)
							.remove();

						self.setLayout();
					},

					clearPhoto: function(){

						self.photoItem().remove();
						self.uploadItem().remove();

						self.setLayout();
					},

					"{uploader} FilesAdded": function() {
						self.setLayout();
						self.uploader.start();
					},

					"{uploader} FileUploaded": function(el, event, uploader, file, response) {

						var uploadItem = self.uploader.getItem(file),
							photoItem = $($.parseHTML($.trim(response.html)));

							photoData = response.data;

							// Initialize photo item
							photoItem
								.data("photo", photoData)
								.addClass("new-item")
								.insertAfter(uploadItem.element);

							self.uploader.removeItem(file.id);

							self.setLayout();

							setTimeout(function(){
								photoItem.removeClass("new-item");
							}, 1);

							self.save();
					},

					"{uploader} FileError": function(el, event, uploader, file, response) {

						self.story.setMessage(response.message, "error");

						var uploadingPhoto = self.uploadingPhoto;

						if (uploadingPhoto) {
							uploadingPhoto.reject();
							delete self.uploadingPhoto;
						}

						self.uploader.removeItem(file.id);

						self.setLayout();
					},

					"{uploader} Error": function(el, event, uploader, error) {

						self.story.setMessage(error.message, "error");

						var uploadingPhoto = self.uploadingPhoto;

						if (uploadingPhoto) {
							uploadingPhoto.reject();
							delete self.uploadingPhoto;
						}

						// Temporary workaround. Delegated event don't work
						// because the element has been removed.
						self.uploadRemoveButton()
							.click(function(){
								setTimeout(function(){
									self.setLayout();
								}, 1);
							});

						self.setLayout();
					},

					"{photoRemoveButton} click": function(photoRemoveButton) {

						var photoId =
							photoRemoveButton
								.parent(self.photoItem.selector)
								.data("photoId");

						self.removePhoto(photoId);
					},

					//
					// Saving
					//

					"{story} save": function(element, event, save) {

						if (!self.hasItems()) {
							return;
						}

						self.uploadingPhoto = save.addTask("uploadingPhoto");
						self.save();
					},

					save: function() {

						var uploadingPhoto = self.uploadingPhoto;

						if (!uploadingPhoto) return;

						var uploadItems = self.uploadItem();

						if (uploadItems.length < 1) {

							var photos = [],
								save = uploadingPhoto.save;

							self.photoItem().each(function(){
								photos.push($(this).data("photoId"));
							});

							save.addData(self, photos);

							uploadingPhoto.resolve();

							delete self.uploadingPhoto;
						}
					},

					"{story} clear": function() {

						self.clearPhoto();
					}
				}}
			);

			// Resolve module
			module.resolve();

		});
});
