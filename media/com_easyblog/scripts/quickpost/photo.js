EasyBlog.module("quickpost/photo", function($){

	var module = this;

	EasyBlog.require()
	.library('webcam','plupload2')
	.done(function($) {

		EasyBlog.Controller('Quickpost.Form.Photo', {
			defaultOptions: {

				// Webcam Form
				"{capture}": "[data-photo-camera-capture]",
				"{canvas}": "[data-photo-camera-canvas]",
				"{preview}": "[data-photo-camera-preview]",
				"{recapture}": "[data-photo-camera-recapture]",

				dataType: "upload",

				"{container}": "[data-photo-upload-container]",
				"{uploadPreview}": "[data-photo-upload-preview]",
				"{reupload}": "[data-photo-upload-reupload]",

				// Tabs
				"{tabs}": "[data-quickpost-photo-tab]",
				"{tabUpload}": "[data-quickpost-photo-tab-upload]",
				"{tabWebcam}": "[data-quickpost-photo-tab-webcam]",

				"{fileName}": "[data-photo-filename]",

				//
				"{form}": "[data-microblog-form]",
				"{link}": "[data-quickpost-link]",

				//
				"{title}" : "[data-quickpost-title]",
				"{content}": "[data-quickpost-content]",
				"{tags}": "[data-quickpost-tags]",
				"{privacy}": "[data-quickpost-privacy]",
				"{category}": "[data-quickpost-category]"
			}
		}, function(self) {
			return {
				init: function()
				{
					// Implement plupload on the upload form
					self.implementUpload();

					// if the web browser dont support flash,
					// lets hide tabs so that only photo upload feature
					// is visible to user.
					if (! self.hasFlash()) {
						$('ul.eb-quick-photo-tab').hide();
					}
				},

				hasFlash: function() {

					// method to check if web browser installed with flash plugin or not.
					var hasFlash = false;

					try {
					  var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
					  if (fo) {
					    hasFlash = true;
					  }
					} catch (e) {
					  if (navigator.mimeTypes
					        && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
					        && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
					    hasFlash = true;
					  }
					}

					return hasFlash;
				},

				implementUpload: function(el, event) {

					var uploader = self.container().plupload2();

					uploader.bind('FilesAdded', function(up, files) {
						uploader.start();
					});

					uploader.bind('FileUploaded', function(up, file, result){
						var response = JSON.parse(result.response),
							image = new Image();

						$(image).attr('src', response.url);

						self.fileName().val(response.file);

						// Hide the container
						self.container().addClass('hidden');

						// Display the preview
						self.uploadPreview()
							.removeClass('hidden')
							.append(image);

						// Display re-upload
						self.reupload().removeClass('hidden');
					});
				},

				"{reupload} click": function(el, event)
				{
					self.reupload().addClass('hidden');

					// Remove hidden from the upload container
					self.container().removeClass('hidden');

					// Add hidden to the preview
					self.uploadPreview().addClass('hidden').html('');
				},

				"{tabs} click": function(el, event)
				{
					self.options.dataType = $(el).data('type');
				},

				webcamStarted : false,

				"{capture} click": function(el, event)
				{
					webcam.capture();
				},

				"{recapture} click": function(el, event)
				{
					self.capture().removeClass('hidden');
					self.recapture().addClass('hidden');
					self.canvas().removeClass('hidden');
					self.preview().find('img').remove();
					self.preview().addClass('hidden')
				},

				"{tabWebcam} click": function(el, event)
				{
					var pos = 0, ctx = null, saveCB, image = [];

					var canvas = document.createElement("canvas");
					canvas.setAttribute('width', 320);
					canvas.setAttribute('height', 240);

					if (canvas.toDataURL) {

						ctx = canvas.getContext("2d");

						image = ctx.getImageData(0, 0, 320, 240);

						saveCB = function(data) {

							var col = data.split(";");
							var img = image;

							for(var i = 0; i < 320; i++) {
								var tmp = parseInt(col[i]);
								img.data[pos + 0] = (tmp >> 16) & 0xff;
								img.data[pos + 1] = (tmp >> 8) & 0xff;
								img.data[pos + 2] = tmp & 0xff;
								img.data[pos + 3] = 0xff;
								pos+= 4;
							}

							if (pos >= 4 * 320 * 240) {
								ctx.putImageData(img, 0, 0);

								EasyBlog.ajax('site/controllers/quickpost/saveWebcam', {
									type: "data",
									image: canvas.toDataURL('image/png')
								}).done(function(result){
									var source = result.url,
										image = new Image();

									self.preview().removeClass('hidden');

									$(image).attr('src', source)
										.appendTo(self.preview());

									// Hide the canvas
									self.canvas().addClass('hidden');

									// Hide the capture picture button now
									self.capture().addClass('hidden');
									self.recapture().removeClass('hidden');

									self.fileName().val(result.file)
								});

								pos = 0;
							}
						};

					} else {

						saveCB = function(data) {
							image.push(data);

							pos+= 4 * 320;

							if (pos >= 4 * 320 * 240) {
								$.post("/upload.php", {type: "pixel", image: image.join('|')});
								pos = 0;
							}
						};
					}


					if (!self.webcamStarted) {

						self.canvas().webcam({

							width: 320,
							height: 240,
							mode: "callback",
							swffile: $.basePath + "/media/foundry/4.0/scripts/webcam/jscam.swf",

							onSave: saveCB,
							onCapture: function () {
								console.log('finishing');
								webcam.save();
							},

							debug: function (type, string) {
								console.log(type + ": " + string);
							}
						});

						self.webcamStarted = true;
					}
				},

				"{self} onPublishQuickPost": function(el, event, save, type, form)
				{
					if (type != 'photo') {
						return;
					}

					// Perform saving for standard posts
					save.data = {
									"dataType": self.options.dataType,
									"title": $(form).find(self.title.selector).val(),
									"type": "photo",
									"content": $(form).find(self.content.selector).val(),
									"link": $(form).find(self.link.selector).val(),
									"tags": $(form).find(self.tags.selector).val(),
									"privacy": $(form).find(self.privacy.selector).val(),
									"category": $(form).find(self.category.selector).val(),
									"fileName": self.fileName().val()
								};
				},

				"{self} onClearForm": function(el, event, save, type, form)
				{
					if (type != 'photo') {
						return;
					}

					$(form).find(self.title.selector).val('');
					$(form).find(self.content.selector).val('');
					$(form).find(self.link.selector).val('');
					$(form).find(self.tags.selector).val('');
					self.fileName().val('');

					self.reupload().addClass('hidden');
					self.container().removeClass('hidden');
					self.uploadPreview().addClass('hidden').html('');

				}


			}
		});

		module.resolve();
	});

});
