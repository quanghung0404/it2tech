EasySocial.module("albums/item", function($){

	var module = this;

	// Non-essential dependencies
	EasySocial.require()
		.script("albums/editor")
		.done();

	// Essential dependencies
	EasySocial.require()
		.library(
			"masonry"
		)
		.done(function(){

			EasySocial.Controller("Albums.Item",
			{
				hostname: "album",

				defaultOptions: {

					tilesPerRow: 4,
					editable: false,
					multipleSelection: false,

					"{header}": "[data-album-header]",
					"{content}": "[data-album-content]",
					"{footer}": "[data-album-footer]",

					"{info}": "[data-album-info]",

					"{title}": "[data-album-title]",
					"{caption}"       : "[data-album-caption]",
					"{location}"      : "[data-album-location]",
					"{date}"          : "[data-album-date]",
					"{cover}"         : "[data-album-cover]",
					"{favouriteButton}"	  : "[data-album-favourite-button]",

					"{photoItemGroup}": "[data-photo-item-group]",
					"{photoItem}"     : "[data-photo-item]",
					"{photoImage}"    : "[data-photo-image]",
					"{photoImageCss}" : "[data-photo-image-css]",
					"{featuredItem}"  : "[data-photo-item].featured",
					"{featuredImage}" : "[data-photo-item].featured [data-photo-image]",
					"{featuredImageCss}" : "[data-photo-item].featured [data-photo-image-css]",
					"{uploadItem}"    : "[data-photo-upload-item]",

					"{moreButton}"    : "[data-album-more-button]",
					"{viewButton}"    : "[data-album-view-button]",

					"{share}"			  : "[data-repost-action]",
					"{likes}"			  : "[data-likes-action]",
					"{likeContent}" 	  : "[data-likes-content]",
					"{repostContent}" 	  : "[data-repost-content]",
					"{counterBar}"	  	  : "[data-stream-counter]"
				}
			},
			function(self, opts, base) { return {

				init: function()
				{
					self.id = base.data("album-id");

					self.nextStart = base.data("album-nextstart") || -1;

					// If this viewer is editable, load & implement editor.
					if (self.options.editable) {
						EasySocial.module("albums/editor")
							.done(function(EditorController)
							{
								self.editor = self.addPlugin("editor", EditorController);
							});
					}

					// Set layout when window is resized
					self.setLayout();

					// Show load more button
					// Quick monkey fix for load more button showing
					// on the right corner before layout is set.
					self.moreButton().show();

					// Attach existing photo items as subscribers
					self.addSubscriber(
						self.photoItem()
							.controllers("EasySocial.Controller.Photos.Item")
					);
				},

				"{window} resize": $.debounce(function(){
					self.setLayout();
				}, 250),

				currentLayout: function() {

					return base.data("albumLayout");
				},

				setLayout_: $.debounce(function(){

					self.setLayout();
				}, 100),

				setLayout: function(layoutName) {

					var photoItemGroup = self.photoItemGroup(),

						// Build layout state
						currentLayout = self.currentLayout(),
						layoutName    = layoutName || currentLayout,
						seed          = self.setLayout.seed,
						intact        = (seed == photoItemGroup.width() && currentLayout==layoutName)
						hasPhotoItem  = self.photoItem().length > 0,
						hasUploadItem = self.uploadItem().length > 0,
						hasItem       = hasPhotoItem || hasUploadItem,
						masonry       = $.data(photoItemGroup[0], "masonry"),

						// Put them in an object
						layout = {
							currentLayout: currentLayout,
							seed         : seed,
							intact       : intact,
							hasPhotoItem : hasPhotoItem,
							hasUploadItem: hasUploadItem,
							hasItem      : hasItem,
							masonry      : masonry
						};

					// Determine if we need to switch layout
					if (!intact) {

						// Switch layout
						base
							.data("albumLayout", layoutName)
							.switchClass("layout-" + layoutName);

						// Switch all photo item's layout
						self.photoItem()
							.switchClass("layout-" + layoutName);

						// Reset viewport width to force layout redraw
						self.setLayout.seed = layout.seed = null;

						// Only trigger layout change when layout has really changed.
						if (currentLayout !== layoutName) {
							// Trigger layout change event
							self.trigger("layoutChange", [layoutName, layout]);
						}
					}

					// Show upload hint when content is empty
					base.toggleClass("has-photos", hasItem);

					// If there's no item from the list
					if (!hasItem) {

						// If this is coming from deleting the last item
						// from the list, we need to keep the container
						// on zero height.
						photoItemGroup.css("opacity", 1);
					}

					// Execute layout handler
					var layoutHandler = "set" + $.String.capitalize(layoutName) + "Layout";
					self[layoutHandler](layout);

					// Save current layout
					self.setLayout.seed = photoItemGroup.width();
				},

				setItemLayout: function(layout) {

					self.photoItem().attr("data-es-photo-disabled", 0);

					// Get photoItemGroup
					var tilesPerRow = 4,
						photoItemGroup = self.photoItemGroup(),
						viewportWidth  = base.width(),
						containerWidth = Math.floor(viewportWidth / tilesPerRow) * tilesPerRow;

					self.photoItemGroup()
						.width(containerWidth);

					if (layout.masonry) {

						photoItemGroup.masonry("reloadItems").masonry("layout");
					} else {

						photoItemGroup
							.masonry({
								columnWidth: ".es-photo-item.grid-sizer",
								itemSelector: self.photoItem.selector + ", " + self.uploadItem.selector,
								isOriginLeft: !self.options.rtl
							});
					}
				},

				setFormLayout: function(layout) {

					self.photoItem().attr("data-es-photo-disabled", 1);

					// Destroy masonry if we are on form layout
					layout.masonry && layout.masonry.destroy();

					// Reset layout
					self.clearLayout();
				},

				setDialogLayout: function() {

					self.photoItem().attr("data-es-photo-disabled", 1);

					// Destroy masonry if we are on form layout
					layout.masonry && layout.masonry.destroy();

					// Reset layout
					self.clearLayout();
				},

				setThumbnailLayout: function() {

				},

				setRowLayout: function() {

					self.photoItem().attr("data-es-photo-disabled", 0);

					self.clearLayout();
				},

				clearLayout: function() {

					self.photoItemGroup()
						.addClass("no-transition");

					self.photoItem
						.css().remove();

					self.photoImage
						.css().remove();

					self.photoImageCss
						.css().remove();

					self.featuredItem
						.css().remove();

					self.featuredImage
						.css().remove();

					self.featuredImageCss
						.css().remove();

					self.uploadItem
						.css().remove();

					self.setLayout.seed = null;
				},

				getSelectedItems: function() {

					var selectedPhotos = self.photoItem(".selected");

					var data = [];

					selectedPhotos.each(function(i, photo){
						data.push($(photo).controller("EasySocial.Controller.Photos.Item").data());
					});

					return data;
				},

				"{photoItem} init.photos.item": function(el, event, photoItem) {

					self.addSubscriber(photoItem);
				},

				"{photoItem} destroyed": function() {

					self.setLayout();
				},

				"{photoItem} activate": function(photoItem, event, photo) {

					// Activate is a non-standard IE event,
					// if photo is undefined then it is coming
					// from the browser not photo item controller.
					if (!photo) return;

					var currentLayout = self.currentLayout();

					switch (currentLayout) {

						case "item":
						case "row":

							// Show loading indicator
							photoItem.addClass("loading");

							// If browser is available, ask browser
							// to load photo view via ajax.
							if (self.browser) {

								// View photo
								self.browser
									.open("photo", photo.id)
									.always(function(){

										// Remove loading indicator
										photoItem.removeClass("loading");
									});

								// Change address bar url
								photo.imageLink().route();

							// If browser is not available,
							// just load the photo view normally.
							} else {
								window.location = photo.imageLink().attr("href");
							}
							break;

						case "form":
							// photo.editor && photo.editor.enable();
							break;

						case "dialog":

							var selectedPhotos = self.photoItem(".selected");

							if (!self.options.multipleSelection) {

								var selected = photoItem.hasClass("selected");

								// In case it came from multiple selection
								selectedPhotos.removeClass("selected");

								photoItem.toggleClass("selected", !selected);

							} else {

								photoItem.toggleClass("selected");
							}
							break;
					}
				},

				"{photoItem} photoFeature": function(el, event, task, photo, featured) {

					// Set layout to accomodate double size photo item
					self.setLayout();

					// When a photo fail to be featured, it shrinks
					task
						.fail(function(){

							// So we're resetting layout again
							self.setLayout();
						});
				},

				"{photoItem} photoMove": function(el, event, task, photo, targetAlbumId) {

					self.clearMessage();

					task
						.done(function(){

							// Remove photo
							photo.element.remove();

							// Set layout
							self.setLayout();

							// If there are no more photos, remove cover
							if (self.photoItem().length < 1) {
								self.trigger("coverRemove", [self]);
							}
						})
						.fail(function(message, type){
							self.setMessage(message, type);
						});
				},

				"{photoItem} photoDelete": function(el, event, task, photo) {

					self.clearMessage();

					task
						.done(function(){

							// Remove photo
							photo.element.remove();

							// Set layout
							self.setLayout();

							// If there are no more photos, remove cover
							if (self.photoItem().length < 1) {
								self.trigger("coverRemove", [self]);
							}
						})
						.fail(function(message, type){
							self.setMessage(message, type);
						});
				},

				// These are coming from album editor
				"{self} albumSave": function(el, event, task) {

					task.done(function(album){
						self.id = album.id;
					});
				},

				"{self} coverChange": function(el, event, photo, album) {

					self.cover()
						.css("backgroundImage", $.cssUrl(photo.sizes.thumbnail.url));
				},

				"{self} coverRemove": function() {

					self.cover()
						.css("backgroundImage", "");
				},

				"{viewButton} click": function(viewButton, event) {
					if (self.browser)
					{
						event.preventDefault();
						base.addClass("loading");
						self.browser.open("Album", self.id);
					}
				},

				"{moreButton} click": function(moreButton) {

					// If nextStart is -1, means no more photos
					if (self.nextStart == -1) {
						return;
					}

					if (moreButton.disabled()) {
						return;
					}

					// Disable this button
					moreButton.toggleClass('loading');
					moreButton.disabled(true);

					// Set the button into loading state
					// moreButton.addClass('loading');

					// Get the new photos content
					EasySocial.ajax(
						"site/controllers/albums/loadMore",
						{
							albumId: self.id,
							start: self.nextStart,
							layout: self.currentLayout()
						})
						.done(function(htmls, nextStart) {

							self.nextStart = nextStart;

							var photoItemGroup = self.photoItemGroup();

							$.each(htmls, function(i, html){
								$.buildHTML(html).appendTo(photoItemGroup);
							});

							moreButton.toggleClass('loading');

							// If there is no more photos to load, hide the button
							if (nextStart < 0) {
								moreButton.hide();
							}

							self.setLayout();
						})
						.always(function(){

							moreButton.disabled(false);
						});
				},

                "{share} create": function(el, event, itemHTML) {
                	self.counterBar().removeClass('hide');
                },

 				"{likes} onLiked": function(el, event, data) {

					//need to make the data-stream-counter visible
					self.counterBar().removeClass( 'hide' );
				},

				"{likes} onUnliked": function(el, event, data) {

					var isLikeHide 		= self.likeContent().hasClass('hide');
					var isRepostHide 	= self.repostContent().hasClass('hide');

					if( isLikeHide && isRepostHide )
					{
						self.counterBar().addClass( 'hide' );
					}
				},

				"{favouriteButton} click": function()
				{
					EasySocial.ajax(
						"site/controllers/albums/favourite",
						{
							id: self.id
						}
					)
					.done(function(favourite) {
					
						self.favouriteButton().toggleClass( "is-fav btn-es-primary" );	
					});
				}

			}});

			module.resolve();
		});
});
