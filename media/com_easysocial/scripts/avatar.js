EasySocial.module( 'avatar' , function($){

var module = this;

EasySocial.require()
.script('utilities/webcam')
.done(function($) {

	EasySocial.Controller('Avatar', {
		defaultOptions: {
			uid: null,
			type: null,
			redirectUrl: null,
			"{menu}": "[data-avatar-menu]",

			"{takePictureButton}": "[data-avatar-webcam]",
			"{uploadButton}": "[data-avatar-upload-button]",
			"{selectButton}": "[data-avatar-select-button]",
			"{removeButton}": "[data-avatar-remove-button]"
		}
	}, function(self) {
		return {

			init: function() {

				// Implement the webcam js on the take picture button
				var hasFlash = self.hasFlash();
			},

			"{uploadButton} click": function() {
				EasySocial.dialog({
					content: EasySocial.ajax('site/views/avatar/upload', { 
						'uid' : self.options.uid, 'type': self.options.type 
					})
				});
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

			hideWebcamCanvas: function() {
				// Instead of hiding the canvas, we reposition the webcam canvas
				$('[data-canvas-webcam]')
					.css('position', 'absolute')
					.css('left', '-9999px');

				// $('[data-canvas-webcam]').addClass('hidden');
			},

			showWebcamCanvas: function() {
				// Instead of hiding the canvas, we reposition the webcam canvas
				$('[data-canvas-webcam]')
					.css('position', 'relative')
					.css('left', '0');
			},

			webcamStarted : false,
			"{takePictureButton} click": function() {

				var pos = 0, ctx = null, saveCB, image = [];

				var canvas = document.createElement("canvas");
				canvas.setAttribute('width', 320);
				canvas.setAttribute('height', 240);
				
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

						EasySocial.ajax('site/views/profile/saveCamPicture', {
							type: "data",
							image: canvas.toDataURL('image/png')
						}).done(function(result){
							var source = result.url;
							var image = new Image();

							preview = $('[data-photo-camera-preview]');

							preview.removeClass('hidden');

							$(image).attr('src', source)
								.appendTo(preview);

							$('[data-save-button]').removeClass('hidden');
							
							// Instead of hiding the canvas, we reposition the webcam canvas
							self.hideWebcamCanvas();

							// Hide the capture picture button now
							$('[data-capture-button]').addClass('hidden');
							$('[data-recapture-button]').removeClass('hidden');

							$('[data-photo-filename]').val(result.file);
						});

						pos = 0;
					}
				};


				if (!self.webcamStarted) {

					EasySocial.dialog({
						content: EasySocial.ajax('site/views/profile/takePicture', {}),
						
						afterHide: function() {
							// After the webcam dialog is hidden, we need to reset the state
							self.webcamStarted = false;
						},

						bindings: {
							init: function() {

								$('[data-canvas-webcam]').webcam({
									onSave: saveCB,
									onCapture: function () {
										webcam.save();
									},
									debug: function(type, message) {

										// User denied access to the camera
										if (type == 'notify' && message == 'Camera stopped') {
											self.webcamStarted = false;
											EasySocial.dialog().close();
											return;
										}
									}
								});

								self.webcamStarted = true;
							},

							"{captureButton} click": function() {
								webcam.capture();
							},
							
							"{recaptureButton} click": function(el, event) {
								$('[data-capture-button]').removeClass('hidden');
								$('[data-recapture-button]').addClass('hidden');
								$('[data-save-button]').addClass('hidden');
									
								// Display the webcam canvas again.
								self.showWebcamCanvas();

								preview.find('img').remove();
								preview.addClass('hidden');
							},

							"{saveButton} click": function(el, event) {
								file = $('[data-photo-filename]').val();
								uid = $('[data-photo-uid]').val();

								EasySocial.ajax('site/controllers/photos/createAvatarFromWebcam', {
									uid 	: uid,
									type	: "user",
									file	: file
								}).done(function(result){
									EasySocial.dialog().close();
									location.reload();
								});
								
							}
						}
					})
				}
			},

			"{selectButton} click": function() {
				
				EasySocial.photos.selectPhoto({
					uid : self.options.uid,
					type : self.options.type,
					bindings: {
						"{self} photoSelected": function(el, event, photos) {

							// Photo selection dialog returns an array,
							// so just pick the first one.
							var photo = photos[0];

							// If no photo selected, stop.
							if (!photo) {
								return;
							}

							EasySocial.photos.createAvatar(photo.id , { "uid" : self.options.uid , "type" : self.options.type , "redirectUrl" : self.options.redirectUrl } );
						},

						"{cancelButton} click": function() {
							this.parent.close();
						}
					}
				});
			},

            "{menu} shown.bs.dropdown": function() {
                 self.element.addClass("show-all");
            },

            "{menu} hidden.bs.dropdown": function() {
                 self.element.removeClass("show-all");
            },

			"{removeButton} click": function() {
				EasySocial.dialog({
					"content": EasySocial.ajax('site/views/profile/confirmRemoveAvatar')
				});
			}

		}
	});

	module.resolve();
});


});