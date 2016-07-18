EasySocial.module("photos/editor", function($){

	var module = this;

	EasySocial.require()
		.done(function(){

			var Controller =
			EasySocial.Controller("Photos.Editor",
			{
				defaultOptions: {

					view: {
						uploadItem: "upload.item",
						photoForm : "site/albums/photo.form"
					},

					"{titleField}"  : "[data-photo-title-field]",
					"{captionField}": "[data-photo-caption-field]",

					"{location}"          : "[data-photo-location]",
					"{locationCaption}"   : "[data-photo-location-caption]",
					"{addLocationButton}" : "[data-photo-addLocation-button]",
					"{date}"              : "[data-photo-date]",
					"{dateCaption}"       : "[data-photo-date-caption]",
					"{addDateCaption}"    : "[data-photo-adddate-button]",

					"{locationWidget}"  : ".es-photo-location-form .es-locations",
					"{latitude}"        : "[data-location-lat]",
					"{longitude}"       : "[data-location-lng]",

					"{dateDay}"  : "[name=date-day]",
					"{dateMonth}": "[name=date-month]",
					"{dateYear}" : "[name=date-year]",

					"{actionsMenu}"  : "[data-item-actions-menu]",
					"{featureButton}": "[data-photo-feature-button]",
					"{coverButton}"  : "[data-photo-cover-button]",

					"{editButton}"    : "[data-photo-edit-button]",
					"{editButtonLink}": "[data-photo-edit-button] > a",

					"{cancelButton}"   : "[data-photo-cancel-button]",

					"{doneButton}"     : "[data-photo-done-button]",
					"{doneButtonLink}" : "[data-photo-done-button] > a",

					"{moveButton}"  : "[data-photo-move-button]",
					"{deleteButton}": "[data-photo-delete-button]",

					"{rotateLeftButton}": "[data-photo-rotateLeft-button]",
					"{rotateRightButton}": "[data-photo-rotateRight-button]",

					"{profileAvatarButton}": "[data-photo-profileAvatar-button]",
					"{profileCoverButton}": "[data-photo-profileCover-button]"
				}
			},
			function(self, opts, base) { return {

				init: function() {
				},

				data: function() {

					return {
						id        : self.photo.id,
						title     : self.titleField().val(),
						caption   : self.captionField().val(),
						date      : self.formatDate(),
						address   : self.locationCaption().html(),
						latitude  : self.latitude().val(),
						longitude : self.longitude().val()
					}
				},

				save: function() {

					var data = self.data();

					self.clearMessage();

					var task =
						EasySocial.ajax(
							"site/controllers/photos/update",
							data
						)
						.done(function(photo){

							self.photo.setLayout("item");
						})
						.fail(function(){

							self.setMessage(message, "error");
						})
						.progress(function(message, type){

							if (type=="success") {
								self.setMessage(message);
							}
						});

					self.trigger("photoSave", [task, self]);

					return task;
				},

				enable: function() {

					self.photo.setLayout("form");

					// If we are running under an album frame
					var album = self.photo.album;

					if (album) {
						base.addClass("active");
					}

					self.trigger("enabled", [self]);
				},

				disable: function() {

					self.photo.setLayout("item");

					// If we are running under an album frame
					var album = self.photo.album;

					if (album) {
						base.removeClass("active");
					}

					self.trigger("disabled", [self]);
				},

				imageLoader: {},

				setImage: function(type) {

					var image = self.photo.image(),
						imageCss = self.photo.imageCss(),
						imageSource = image.data(type + "Src"),
						imageLoader = self.imageLoader[imageSource];

					// If this image hasn't been loaded before
					if (!imageLoader) {

						// Create an image loader
						imageLoader = $.Image.get(imageSource);

						// Store a reference of the loader within the element
						self.imageLoader[imageSource] = imageLoader;
					}

					imageLoader
						.done(function(){
							image.attr("src", imageSource);
							imageCss.css({
								backgroundImage: $.cssUrl(imageSource)
							});
						});

					return imageLoader;
				},

				"{featureButton} click": function(featureButton, event) {

					event.stopPropagation();

					var isPopup =
						self.photo.element.parents("[data-photo-popup]").length > 0 ||
						self.photo.element.parents("[data-photo-browser-content]").length > 0;

					var isFeatured = base.hasClass("featured");

					base.toggleClass("featured", !isFeatured);

					!isPopup && self.setImage((isFeatured) ? "thumbnail" : "featured");

					// Perform an ajax call to mark the photo as featured
					var task =
						EasySocial.ajax(
							"site/controllers/photos/feature", {
								id: self.photo.id
							}
						)
						.done(function( message , isFeatured ) {

							// If this is not under album, show a message
							// if (!self.photo.album) {
							// 	self.clearMessage();
							// 	self.setMessage( message );
							// }

							featureButton.toggleClass('btn-es-primary', isFeatured);
						})
						.fail(function() {

							base.removeClass("featured");
							!isPopup && self.setImage((!isFeatured) ? "thumbnail" : "featured");
						});

					self.trigger("photoFeature", [task, self.photo, !isFeatured]);
				},

				"{coverButton} click": function() {

					var album = self.photo.album;

					// When viewing photos invidually,
					// there is no reference to album,
					// the button itself should't be visible anyway.
					if (!album) return;

					// If the editor is available, set cover.
					album.editor && album.editor.setCover(self.photo.id);
				},

				"{dateDay} keyup": function() {
					self.updateDate();
				},

				"{dateMonth} change": function() {
					self.updateDate();
				},

				"{dateYear} keyup": function() {
					self.updateDate();
				},

				updateDate: function() {

					setTimeout(function(){
						self.date().addClass("has-data");
						var dateCaption = self.dateDay().val() + ' ' + $.trim(self.dateMonth().find(":selected").text() + ' ' + self.dateYear().val());
						self.dateCaption().html(dateCaption);
					}, 1);
				},

				formatDate: function() {
					var day = self.dateDay().val() || self.dateDay().data('date-default'),
						month = self.dateMonth().val() || self.dateMonth().data('date-default'),
						year = self.dateYear().val() || self.dateYear().data('date-default');

					return year + '-' + month + '-' + day;
 				},

				"{locationWidget} locationChange": function(el, event, location) {

					var address = location.address || location.fulladdress || location.formatted_address;
					self.locationCaption().html(address);
					self.location().addClass("has-data");
				},

				rotate: function(angle) {

					var photo = self.photo;

					self.rotateLeftButton().disabled(true);
					self.rotateRightButton().disabled(true);

					// Show loading indicator
					photo.content().addClass("loading");

					var task =
						EasySocial.ajax(
							"site/controllers/photos/rotate",
							{
								id: photo.id,
								angle: angle
							}
						)
						.done(function(photoObj) {

							var url;

							if (self.photo.album) {
								url = photoObj.sizes.thumbnail.url;
							} else {
								url = photoObj.sizes.large.url;
							}

							// So that it actual loads a new one
							url += "?" + $.uid();

							// Replace image url
							photo.image()
								.attr("src", url);

							photo.imageCss()
								.css({
									backgroundImage: $.cssUrl(url)
								});

							base
								.addTransitionClass("rotating-ready", 150)
								.removeClass("rotating-right rotating-left");
						})
						.fail(function(message, type) {

							self.setMessage(message, type);
						})
						.always(function(){

							photo.content().removeClass("loading");
							self.rotateLeftButton().disabled(false);
							self.rotateRightButton().disabled(false);
						});

					self.trigger("photoRotate", [task, angle, photo])
				},

				"{rotateRightButton} click": function() {

					base.addClass("rotating-right");
					self.rotate(90);
				},

				"{rotateLeftButton} click": function() {

					base.addClass("rotating-left");
					self.rotate(-90);
				},

				"{moveButton} click": function() {

					var photo = self.photo;

					var dialog =
						EasySocial.dialog({
							content: EasySocial.ajax(
								"site/views/photos/moveToAnotherAlbum",
								{
									id: photo.id
								}
							),
							bindings: {
								"{moveButton} click": function() {

									var targetAlbumId = this.albumSelection().val();

									var task =
										EasySocial.ajax(
											"site/controllers/photos/move",
											{
												id: photo.id,
												albumId: targetAlbumId
											}
										)
										.always(function(){
											dialog.close();
										});

									self.trigger("photoMove", [task, photo, targetAlbumId]);
								}
							}
						});
				},

				"{deleteButton} click": function() {

					var photo = self.photo;

					EasySocial.dialog({
						content: EasySocial.ajax(
							"site/views/photos/confirmDelete",
							{
								id: photo.id
							}
						),
						bindings: {
							"{deleteButton} click": function(deleteButton) {

								var dialog = this.parent;

								deleteButton.disabled(true);

								var task =
									EasySocial.ajax(
										"site/controllers/photos/delete",
										{
											id: photo.id
										}
									)
									.always(function(){
										dialog.close();
									});

								self.trigger("photoDelete", [task, photo]);
							}
						}
					});
				},

				"{editButton} click": function() {

					// Change viewer layout
					self.photo.setLayout("form");

					// Change address bar url
					self.editButtonLink().route();
				},

				"{editButtonLink} click": function(editButtonLink, event) {

					event.preventDefault();
				},

				"{cancelButton} click": function()
				{
					// Change album layout
					self.photo.setLayout("item");

					// Change address bar url
					self.doneButtonLink().route();
				},

				"{doneButton} click": function() {

					self.save()
						.done(function(){

							// Change album layout
							self.photo.setLayout("item");

							// Change address bar url
							self.doneButtonLink().route();
						})
						.fail(function(){

						});
				},

				"{doneButtonLink} click": function(doneButtonLink, event) {
					event.preventDefault();
				},

				"{profileAvatarButton} click": function() {
					EasySocial.photos.createAvatar(self.photo.id);
				}
			}});

			module.resolve(Controller);

		});
});
