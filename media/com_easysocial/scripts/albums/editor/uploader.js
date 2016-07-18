EasySocial.module("albums/editor/uploader", function($){

	var module = this;

	EasySocial.require()
		.script(
			"albums/uploader"
		)
		.view(
			"site/albums/upload.item"
		)		
		.done(function(){

			var Controller = 

			EasySocial.Controller("Albums.Editor.Uploader",
			{
				defaultOptions: {

				}
			},
			function(self) { return {

				init: function() {

					// Shortcuts
					self.album = self.editor.album;

					// Get upload settings
					var settings = self.album.options.uploader;

					// Implement uploader
					self.uploader =
						self.addPlugin(
							"uploader",
							EasySocial.Controller.Albums.Uploader,
							{
								settings: settings,
								"{uploadButton}"   : self.editor.uploadButton.selector,
								"{uploadItemGroup}": self.album.photoItemGroup.selector,
								"{uploadDropsite}" : self.album.content.selector
							}
						);
				},

				setLayout: function() {

					self.album.setLayout_();
				},

				"{self} beforeAlbumSave": function() {

					// Stop existing upload process.
					self.uploader.stop();
				},

				"{self} albumSave": function(el, event, task) {

					task.done(function(album){

						var url = 
							$.uri(self.uploader.settings("url"))
								.replaceQueryParam("albumId", album.id)
								.toString();

						self.uploader.settings("url", url);							
					});
				},

				"{self} layoutChange": function(el, event, layoutName) {

					// Stop any running upload process
					// and clear upload items.
					self.uploader.stop();
					self.uploader.clear();

					var url = 
						$.uri(self.uploader.settings("url"))
							.replaceQueryParam("createStream", layoutName=="form" ? 0 : 1)
							.replaceQueryParam("layout", layoutName)
							.toString();

					self.uploader.settings("url", url);
				},

				"{self} QueueCreated": function(el, event, uploadItem) {
					
					// Give upload item a layout when we're under editor
					if (self.album.currentLayout()=="form") {
						uploadItem.element.addClass("layout-form");
					}

					self.setLayout();
				},
				
				startUpload: $.Enqueue(),

				"{uploader} FilesAdded": function(el, event, uploader, files) {

					// If this is a new album
					if (!self.id) {

						// Create the album first
						self.editor.createAlbum()
							.done(
								// Before we start uploading
								self.startUpload(function(){
									self.uploader.start();
								})
							);

					// Else start uploading straightaway
					} else {
						self.uploader.start();
					}

					self.setLayout();
				},

				"{uploader} FilesRemoved": function() {

					self.setLayout();
				},

				"{uploader} FileUploaded": function(el, event, uploader, file, response) {

					var uploadItem = self.uploader.getItem(file),

						photoItem = $.buildHTML(response.html),

						photoData = response.data;

						// Initialize photo item
						photoItem
							.addClass("new-item")
							.insertAfter(uploadItem.element);

						setTimeout(function(){
							photoItem.removeClass("new-item");
						}, 1);

						self.uploader.removeItem(file.id);

						self.trigger("photoAdd", [photoItem, photoData, self.album]);

						self.setLayout();
				}

			}});

			module.resolve(Controller);

		});
});
