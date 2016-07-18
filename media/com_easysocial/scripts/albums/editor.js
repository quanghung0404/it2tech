EasySocial.module("albums/editor", function($){

	var module = this;

	// Constants
	var photoEditorController = "EasySocial.Controller.Photos.Editor"

	// Non-essential dependencies
	EasySocial.require()
		.script(
			"albums/editor/sortable",
			"albums/editor/uploader"
		)
		.done();

	// Essential dependencies
	var Controller =

	EasySocial.Controller("Albums.Editor",
	{
		hostname: "editor",

		defaultOptions: {

			view: {
		        uploadItem: "site/albums/upload.item"
			},

			canReorder: false,
			canUpload: true,

			"{titleField}"        : "[data-album-title-field]",
			"{captionField}"      : "[data-album-caption-field]",
			"{coverField}"        : "[data-album-cover-field]",

			"{type}"			  : "[data-album-type]",
			"{uid}"				  : "[data-album-uid]",

			"{location}"          : "[data-album-location]",
			"{locationCaption}"   : "[data-album-location-caption]",
			"{addLocationButton}" : "[data-album-addLocation-button]",
			"{date}"              : "[data-album-date]",
			"{dateCaption}"       : "[data-album-date-caption]",
			"{addDateCaption}"    : "[data-album-addDate-button]",
			"{privacy}"           : "[data-album-privacy]",

			"{uploadButton}"      : "[data-album-upload-button]",
			"{deleteButton}"      : "[data-album-delete-button]",
			"{moreButton}"        : "[data-album-more-button]",

			"{privacy}"			  : "[data-privacy-hidden]",
			"{privacycustom}"	  : "[data-privacy-custom-hidden]",

			"{uploadItem}"        : "[data-photo-upload-item]",

			"{dateDay}"		    : "[data-date-day]",
			"{dateMonth}"		: "[data-date-month]",
			"{dateYear}"		: "[data-date-year]",

			"{editButton}"     : "[data-album-edit-button]",
			"{editButtonLink}" : "[data-album-edit-button] > a",
			"{doneButton}"     : "[data-album-done-button]",
			"{doneButtonLink}" : "[data-album-done-button] > a",
			"{cancelButton}"   : "[data-album-cancel-button]",
			"{cancelButtonLink}"   : "[data-album-cancel-button] > a",

			"{locationWidget}"  : ".es-album-location-form .es-locations",
			"{latitude}"        : "[data-location-lat]",
			"{longitude}"       : "[data-location-lng]"
		}
	},
	function(self) { return {

		init: function() {

			self.id = self.element.data("album-id");

			var options = self.options;

			// If we can sort photos, load & implement sortable.
			if (options.canReorder) {
				EasySocial.module("albums/sortable")
					.done(function(SortableController){
						self.addPlugin("sortable", SortableController);
					});
			}

			// If we can upload photos, load & implement uploader.
			if (options.canUpload) {

				EasySocial.module("albums/editor/uploader")
					.done(function(UploaderController){
						self.uploader = self.addPlugin("uploader", UploaderController);
					});
			}

			// If this is an existing album, there's no need to create album
			if (self.id) {
				self.createAlbum.task = $.Deferred().resolve();
				self.createStream = 0;
			} else {
				self.createStream = 1;
			}
		},

		data: function() {

			var title         	= self.titleField().val(),
				caption       	= self.captionField().val(),
				date          	= self.formatDate(),
				address       	= self.locationCaption().html(),
				latitude      	= self.latitude().val(),
				longitude     	= self.longitude().val(),
				privacy       	= self.privacy().val(),
				privacycustom 	= self.privacycustom().val();
				uid 			= self.element.data( 'album-uid' );
				type 			= self.element.data( 'album-type' );

			return {
				id           : self.id,
				uid 		 : uid,
				type 		 : type,
				title        : title,
				caption      : caption,
				date         : date,
				address      : address,
				latitude     : latitude,
				longitude    : longitude,
				privacy      : privacy,
				privacycustom: privacycustom,
				createStream : self.createStream
			}
		},

		createAlbum: function() {

			var task = self.createAlbum.task;

			if (!task) {

				task = self.createAlbum.task =

					self.save({
							createStream: 0
						})
						.done(function(album){
							self.deleteButton().disabled(false);
							self.element.attr("data-album-id", self.id = album.id);
						})
						.fail(function(message, type){
							self.setMessage(message, type);
						});
			}

			return task;
		},

		save: function(options) {

			self.trigger("beforeAlbumSave", [self]);

			// Build save data
			var data = $.extend(self.data(), options);

				data.photos =
					$.map(
						self.album.photoItem(),
						function(photoItem, i){
							var editor = $(photoItem).controller("EasySocial.Controller.Photos.Editor");
							return (editor) ? editor.data() : null;
						});

				// TODO: Get photo ordering
				// data.ordering = self.getPhotoOrdering();

			// Clear any messages
			self.clearMessage();

			// Save album
			var task = EasySocial.ajax( "site/controllers/albums/store" , data );

			// Trigger albumSave event
			self.trigger("albumSave", [task, self]);

			// Return task
			return task;
		},

		"{self} photoAdd": function(el, event, photoItem, photoData) {

			// Set cover if this is the first photo
			if (self.album.photoItem().length <= 1) {
				self.changeCover(photoData);
			}
		},

		setCover: function(photoId) {

			var task =
				EasySocial.ajax(
					"site/controllers/albums/setCover",
					{
						albumId: self.id,
						coverId: photoId
					}
				)
				.done(function(photo){
					self.changeCover(photo);
				})
				.fail(function(){

				});

			return task;
		},

		removeCover: function() {

			self.trigger("coverRemove", [self.album]);
		},

		changeCover: function(photo) {

			self.trigger("coverChange", [photo, self]);
		},

		"{self} coverChange": function(el, event, photo) {

			self.coverField()
				.removeClass("no-cover")
				.css("backgroundImage", $.cssUrl(photo.sizes.thumbnail.url));
		},

		"{self} coverRemove": function() {

			self.coverField()
				.addClass("no-cover")
				.css("backgroundImage", "");
		},

		"{editButton} click": function() {

			// Change viewer layout
			self.album.setLayout("form");

			// Change address bar url
			self.editButtonLink().route();
		},

		"{editButtonLink} click": function(editButtonLink, event) {

			event.preventDefault();
		},

		"{cancelButton} click": function() {

			// Change viewer layout
			self.album.setLayout("item");

			// Change address bar url
			self.cancelButtonLink().route();
		},

		"{cancelButtonLink} click": function(editButtonLink, event) {

			event.preventDefault();
		},

		"{doneButton} click": function(el, event) {

			// Add a loading indicator here
			self.doneButtonLink().addClass('btn-loading');

			self.save()
				.done(function(album, html){

					// Replace the done link again
					self.doneButtonLink().removeClass('btn-loading');

					$.buildHTML(html).replaceAll(self.element);
				})
				.progress(function(message, type){
					self.setMessage(message, type);
				});
		},

		"{doneButtonLink} click": function(doneButtonLink, event) {
			event.preventDefault();
		},

		"{deleteButton} click": function(deleteButton) {
			
			if (deleteButton.disabled()) return;

			EasySocial.dialog({
				content: EasySocial.ajax("site/views/albums/confirmDelete", {id: self.id})
			});
		},

		formatDate: function() {
			var day = self.dateDay().val() || self.dateDay().data('date-default'),
				month = self.dateMonth().val() || self.dateMonth().data('date-default'),
				year = self.dateYear().val() || self.dateYear().data('date-default');

			return year + '-' + month + '-' + day;
			},

		updateDate: function() {

			self.date().addClass("has-data");
			var dateCaption = self.dateDay().val() + ' ' + $.trim(self.dateMonth().find(":selected").html()) + ' ' + self.dateYear().val();
			self.dateCaption().html(dateCaption);
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

		"{titleField} keyup": function(titleField) {

			self.trigger("titleChange", [titleField.val(), self]);
		},

		"{locationWidget} locationChange": function(el, event, location) {
			var address = location.address || location.fulladdress || location.formatted_address;

			// Set the address in the caption
			self.locationCaption().html(address);
			self.location().addClass("has-data");
		}

	}});

	module.resolve(Controller);
});
