EasySocial.module("site/photos/popup", function($){

var module = this;

// Album playlist
//
// <div data-es-photo-group="album:4">
//     <a data-es-photo="499">
// </div>

// Element-based playlist
//
// <div data-es-photo-group>
//     <a data-es-photo="1">
//     <a data-es-photo="2">
//     <a data-es-photo="3">
// </div>

// Custom playlist
// Ideal for large playlist where not all items are shown.
//
// <div data-es-photo-group="photos:400,401,402,403,405,406,407,408">
//     <a data-es-photo="400">
//     <a data-es-photo="401">
//     <a data-es-photo="402">
//     <a data-es-photo="403">
//     <a data-es-photo="404">
//     <!-- The rest of the thumbnails not shown, but the popup will have it. -->
// </div>

// TODO: Move this away
$.fn.at = function(key) {
	return this.find("[data-" + key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase() + "]");
}


EasySocial.require()
.view("site/photos/popup")
.done(function(){

	var controller =
	EasySocial.Controller("Photos.Popup",
	{
		defaultOptions: {

			view: {
				popup:  "site/photos/popup"
			},

			"{popup}"   : "[data-photo-popup]",
			"{viewport}": "[data-popup-viewport]",
			"{handle}"  : "[data-popup-handle]",

			"{photoGroup}": "[data-es-photo-group]",
			"{photo}"     : "[data-es-photo]",

			"{photoHeader}": "[data-photo-popup] [data-photo-header]",
			"{photoContent}": "[data-photo-popup] [data-photo-content]",

			"{navButton}" : ".es-photo-nav-button",
			"{closeButton}": "[data-popup-close-button]",

			"{photoItem}": "[data-photo-popup] [data-photo-item]",

			'{desTagItem}': '[data-photo-des-tag-item]'
		}
	},
	function(self, opts, base, body) { return {

		init: function() {

			body = $("body");

			self.bodyOverflow = {
				overflow: body.css("overflow"),
				overflowX: body.css("overflowX"),
				overflowY: body.css("overflowY")
			};
		},

		setLayout: function() {

			// Disable body scrollbar on narrow layout
			var popup = self.popup();

			if (popup.is(":visible")) {

				// Set image size again
				var image = self.viewport().find("img[data-photo-image]")[0];
				if (image) ESImage(image);

				body.css({
					overflow: "hidden",
					overflowX: "hidden",
					overflowY: "hidden"
				});

				var photoItem = self.photoItem();

				// Update tag position
				var tags = photoItem.controller("EasySocial.Controller.Photos.Tags");
				tags && tags.setLayout();

				// Disable navigation controller if there's only one photo or tagging is active
				var navigation = photoItem.controller("EasySocial.Controller.Photos.Navigation"),
					tagger = photoItem.controller("EasySocial.Controller.Photos.Tagger");

				navigation && navigation[
					(tagger && !tagger.disabled) || (self.playlist.length < 2) ? "disable" : "enable"]();

			} else {

				body.css(self.bodyOverflow);
			}
		},

		'{desTagItem} mouseover': function(el, ev) {
			var tags = self.photoItem().controller("EasySocial.Controller.Photos.Tags");

			tags && tags.getTagItem(el.data('photoDesTagId'))
				.addClass('focus');
		},

		'{desTagItem} mouseout': function(el, ev) {
			var tags = self.photoItem().controller("EasySocial.Controller.Photos.Tags");

			tags && tags.getTagItem(el.data('photoDesTagId'))
				.removeClass('focus');
		},

		"{window} resize": $.debounce(function() {

			self.setLayout();

		}, 250),

		"{self} responsive": function() {

			self.setLayout();
		},

		playlist: [],

		show: function() {

			var popup,
				node = self.popup.node;

			// Create node if not exists
			if (!node) {
				popup = self.view.popup();
				node  = self.popup.node = popup[0];
			}

			// Append node if detached
			if (!$.contains(base, node)) {
				popup = $(node).addClass("is-loading").appendTo(base);
			}

			if (!popup.is(":visible")) {

				// Show popup
				popup.show();

				// Trigger responsive event
				$(window).trigger("resize.responsive");

				// Trigger show event
				popup.trigger("show");
			}
		},

		hide: function() {

			self.popup()
				.hide()
				.trigger("hide")
				.detach();

			// Restore body layout
			body.css(self.bodyOverflow);
		},

		// open(photoId)
		// open(albumId, photoId)
		// open(type, id)
		// open([photoId, photoId, photoId], photoId)
		open: function() {

			// Normalize arguments
			var args = arguments, albumId, photoId, playlist = [];

			// open(photoId)
			if (args.length===1) {
				photoId = args[0];
				playlist = [photoId];
			}

			// open([photoId, photoId, photoId], photoId)
			// open([photoId, photoId, photoId])
			if ($.isArray(args[0])) {
				playlist = args[0];
				photoId = args[1] || playlist[0];

			// open(albumId, photoId)
			// open(type, id)
			} else if (args.length===2) {

				var type = args[0],
					autoplay = true,
					albumId;

				// open(albumId, photoId)
				if ($.isNumeric(type)) {
					type = "album";
					autoplay = false;
					albumId = args[0];
					photoId = args[1];
					playlist = [photoId];
				}

				// open("photo", 32)
				if (type=="photo") {
					photoId = args[1];
					playlist = [photoId];
				}

				// open("album", 64)
				if (type=="album") {

					// Get the playlist
					EasySocial.ajax("site/controllers/albums/playlist", {albumId: albumId || args[1]})
						.done(function(photos){

							// Update the playlist afterwards
							self.playlist = $.map(photos, function(photo){
								return photo.id;
							});

							// Play the first playlist
							if (autoplay) {
								self.display(playlist[0]);
							}

							// This ensure navigation is reenabled
							self.setLayout();
						})
						.fail(function(){
							// TODO: Make this nicer.
							alert("Error. Could not get album playlist.");
							self.hide();
						});
				}
			}

			self.playlist = playlist;

			// Show popup if hidden
			self.show();

			self.display(photoId);
		},

		handles: {},

		display: function(photoId) {

			if (!photoId) return;

			var previousId = self.currentPhotoId;

			self.currentPhotoId = photoId;

			var handle = self.handles[photoId];

			// Detach any existing views
			self.handle().detach();

			if (!handle) {

				var popup = self.popup();

				popup.addClass("is-loading");

				EasySocial.ajax(
					"site/views/photos/item",
					{
						id: photoId,
						browser: 0,
						popup: 1,
					})
					.done(function(html){

						var handle = $.buildHTML('<div class="es-popup-handle" data-popup-handle>' + html + "</div>");

						self.handles[photoId] = handle;

						self.display(photoId);
					})
					.fail(function(){
						alert("There was a problem loading this photo.");
						self.display(previousId);
					})
					.always(function(){
						popup.removeClass("is-loading");
					});

				return;
			} else {
				self.popup().removeClass("is-loading");
			}

			// Show current handle
			self.viewport().empty().append(handle);

			// Only store the node of the photo handle, discarding scripts.
			if (handle instanceof $) {
				self.handles[photoId] = handle.filter("[data-popup-handle]")[0];
			}

			// Set layout
			self.setLayout();
		},

		current: function() {

			var id = self.currentPhotoId,
				playlist = self.playlist,
				i = $.indexOf(playlist, id);

			// No matching item, revert to 0.
			if (i < 0) i = 0;

			return i;
		},

		next: function() {

			var playlist = self.playlist;

			if (playlist.length < 2) return;

			var i = self.current() + 1;

			// Cycle to the beginning
			if (i > playlist.length - 1) i = 0;

			self.display(playlist[i]);
		},

		prev: function() {

			var playlist = self.playlist;

			if (playlist.length < 2) return;

			var i = self.current() - 1;

			// Cycle to the end
			if (i < 0) i = playlist.length - 1;

			self.display(playlist[i]);
		},

		"{self} click": function(el, event) {

			if (event.target===self.popup()[0]) {
				self.hide();
			}
		},

		"{photo} click": function(photo, event) {
			// If this photo is disabled, stop.
			if (photo.attr("data-es-photo-disabled")=="1") return;

			var target = $(event.target),
				targetTree = target.parents().andSelf();

			// Quick fix
			if (target.filter("[data-es-photo]").length < 1 &&
				targetTree.filter("[data-photo-menu], [data-cover-menu]").length > 0) return;

			// Retrieve photo id and photo group
			var photoId = photo.attr("data-es-photo"),
				photoGroup = self.photoGroup.of(photo);

			// If this is not part of any photo group, don't show popup.
			if (photoGroup.length < 0) return;

			// Stop browser from loading full page photo
			event.preventDefault();

			// Retrieve playlist from photo group
			var data = (photoGroup.attr("data-es-photo-group") || "element").split(":"),
				type = data[0];

			switch (type) {

				// Album playlist
				case "album":
					var albumId = data[1];
					self.open(albumId, photoId);
					break;

				// Custom playlist
				case "photos":
					var list = data[1].split(",");
					self.open(list, photoId);
					break;

				// Element-based playlist
				case "element":
					var list = [];

					photoGroup.at("esPhoto").each(function(){
						list.push($(this).attr("data-es-photo"));
					});

					self.open(list, photoId);
					break;
			}
		},


		"{photoItem} directionmove": function(photoItem, event, offset, direction) {

			// Don't show navigation buttons when playlist still loading,
			// or when there's only a single photo in this playlist.
			if (self.playlist.length < 2) {
				self.navButton().removeClass("active");
			}
		},

		"{photoItem} directionstop": function() {

			// self.photoHeader().removeClass("active");
		},

		"{photoItem} photoNext": function() {

			self.next();
		},

		"{photoItem} photoPrev": function() {

			self.prev();
		},

		"{photoItem} photoDelete": function(el, event, photoItem) {

			if (self.playlist.length < 2) {
				self.hide();
			} else {
				self.next();

			}

			self.playlist = $.without(self.playlist, photoItem.id);
		},

		"{closeButton} click": function() {

			self.hide();
		}

	}});

	module.resolve(controller);
});

});
