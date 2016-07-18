EasyBlog.module("layout/image/popup", function($){

var module = this;

// Image templates
var imagePopupHtml = EasyBlog.template("site/layout/image/popup");
var imagePopupThumbHtml = EasyBlog.template("site/layout/image/popup/thumb");
var imageContainerHtml = EasyBlog.template("site/layout/image/container");

// Image popup selectors
var imagePopup_ = ".eb-image-popup";
var imagePopupButton_ = ".eb-image-popup-button";
var imagePopupCloseButton_ = ".eb-image-popup-close-button";
var imagePopupContainer_ = ".eb-image-popup-container";
var imagePopupFooter_ = ".eb-image-popup-footer";
var imagePopupThumbs_ = ".eb-image-popup-thumbs";
var imagePopupThumb_ = ".eb-image-popup-thumb";

// Image container selectors
var imageContainer_ = ".eb-image";
var imageViewport_ = ".eb-image-viewport";
var imageCaptionText_ = ".eb-image-caption > span";

// Thumbnail selectors
var thumbContainer_ = ".eb-thumbs";

var escapeToCloseEvent = "keyup.eb.imagepopup";
var clickToCloseEvent = "click.eb.imagepopup";
var windowResizeEvent = "resize.eb.imagepopup";
var keyNavigationEvent = "keydown.eb.imagepopup";

var self = EasyBlog.ImagePopup = {

	open: function() {

		// Destroy existing instance
		self.close();
		$("body").noscroll(true);

		$(window)
			.off(escapeToCloseEvent)
			.on(escapeToCloseEvent, function(event){

				// Escape
				if (event.which==27) {
					self.close();
				}
			})
			// Close when clicking outside of the image popup
			.off(clickToCloseEvent)
			.on(clickToCloseEvent, function(event){

				var imageContainer =
					$(event.target)
						.parentsUntil(imagePopup_)
						.andSelf()
						.filter(imageContainer_);

				if (!imageContainer.length) {
					self.close();
				}
			})
			.off(windowResizeEvent)
			.on(windowResizeEvent, function(event){
				self.refresh();
			})
			.off(keyNavigationEvent)
			.on(keyNavigationEvent, function(event){

				// If there are no popup thumbnails, stop.
				if (!$(imagePopupThumb_).length) return;

				var keyCode = event.which;

				// Don't do anything if it's not up down left right
				if (!/37|38|39|40/.test(keyCode)) return;

				var activeImagePopupThumb = $(imagePopupThumb_ + ".active");
				var nextImagePopupThumb;

				// up, left
				if (/37|38/.test(keyCode)) {
					nextImagePopupThumb = activeImagePopupThumb.prev(imagePopupThumb_);
				}

				// down, right
				if (/39|40/.test(keyCode)) {
					nextImagePopupThumb = activeImagePopupThumb.next(imagePopupThumb_);
				}

				if (nextImagePopupThumb.length) {
					self.openPopupThumb(nextImagePopupThumb);
				}

				event.preventDefault();
			});

		// Create image popup
		var imagePopup = $(imagePopupHtml);
		imagePopup.appendTo("body");
	},

	close: function() {

		$(window)
			.off(escapeToCloseEvent)
			.off(clickToCloseEvent)
			.off(windowResizeEvent)
			.off(keyNavigationEvent);

		$(imagePopup_)
			.data("destoyed", true)
			.remove();

		$("body").noscroll(false);
	},

	openThumbnails: function(thumbContainer, startingImageContainer) {

		// Open popup
		self.open();

		// Get image popup thumbs
		var imagePopupFooter = $(imagePopupFooter_);
		var imagePopupThumbs = $(imagePopupThumbs_);
		var imagePopupThumbsWidth = 0;
		var startingImagePopupThumb;

		// Show footer
		imagePopupFooter.show();

		// Generate thumbnails
		thumbContainer.find(imageContainer_)
			.each(function(){

				var imageContainer = $(this);
				var imageElement = imageContainer.find("img");
				var imagePopupButton = imageContainer.find(imagePopupButton_);
				var imageUrl = imageElement.attr("src");
				var imagePopupUrl = imagePopupButton.attr("href");

				var imagePopupThumb =
					$(imagePopupThumbHtml)
						.attr("data-url", imagePopupUrl)
						.find("img")
						.attr("src", imageUrl)
						.end()
						.appendTo(imagePopupThumbs);

				// Sum up thumb width
				imagePopupThumbsWidth += imagePopupThumb.outerWidth(true);

				// If this image is the starting image, remember it.
				if (imageContainer[0]==startingImageContainer[0]) {
					startingImagePopupThumb = imagePopupThumb;
				}
			});

		// Set thumbs width
		imagePopupThumbs.css("width", imagePopupThumbsWidth);

		// Open thumbnail
		self.openPopupThumb(startingImagePopupThumb);
	},

	openImage: function(imageContainer) {

		// Open popup
		self.open();

		var imageCaptionText = imageContainer.find(imageCaptionText_);
		var imagePopupButton = imageContainer.find(imagePopupButton_);

		// Get image url & caption
		var url = imagePopupButton.attr("href");
		var captionText = imageCaptionText.text();

		// If there is no text, get the title of the button
		if (!captionText) {
			captionText = imagePopupButton.attr('title');
		}

		// Show image
		self.showImage(url, captionText);
	},

	openPopupThumb: function(imagePopupThumb) {

		var url = imagePopupThumb.attr("data-url");

		// Toggle active class
		$(imagePopupThumb_).removeClass("active");
		imagePopupThumb.addClass("active");

		self.showImage(url, "");

		// Reposition thumbnails
		self._reposition();
	},

	showImage: function(url, captionText) {

		var imagePopup = $(imagePopup_);
		var imagePopupContainer = imagePopup.find(imagePopupContainer_);

		// Show loading indicator
		imagePopup.addClass("is-loading");

		// Remove existing image container
		imagePopup.find(imageContainer_).remove();

		// Create image container
		var imageContainer = $(imageContainerHtml);
		var imageViewport = imageContainer.find(imageViewport_);

		// Append image caption
		var imageCaptionText = imageContainer.find(imageCaptionText_);
		imageCaptionText
			.text(captionText)
			.css("display", !!captionText ? "block" : "none");

		// Append image container to image popup container
		imageContainer
			.addClass("style-popup")
			.appendTo(imagePopupContainer);

		// Create image element
		var imageElement = $("<img>");

		imageElement
			.on("load", function(){

				if (imagePopup.data("destroyed")) return;

				imagePopup
					.removeClass("is-loading")
					.addClass("is-preparing");

				self.resize();

				imagePopup.removeClassAfter("is-preparing");
			})
			.on("error", function(){

				imagePopup
					.removeClass("is-loading")
					.addClass("is-failed");
			})
			.appendTo(imageViewport)
			.attr("src", url);
	},

	refresh: function() {

		self.resize();
		self.reposition();
	},

	resize: function() {

		// Get image popup & container
		var imagePopup = $(imagePopup_);
		var imagePopupFooter = $(imagePopupFooter_);
		var imageContainer = imagePopup.find(imageContainer_);
		var imageElement = imageContainer.find("img");

		// Get dimensions
		var footerHeight = imagePopupFooter.height();
		var sourceWidth = imageElement.width();
		var sourceHeight = imageElement.height();
		var popupWidth = imagePopup.width();
		var popupHeight = imagePopup.height();
		var maxWidth = popupWidth * 0.75;
		var maxHeight = (popupHeight * 0.75) - footerHeight;

		// Resize the width first
		var ratio        = maxWidth / sourceWidth;
			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;

		// inner resize (default)
		var condition = targetHeight > maxHeight;

		if (condition) {
			ratio        = maxHeight / sourceHeight;
			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;
		}

		imageElement
			.css({
				width : targetWidth,
				height: targetHeight
			});

		var containerWidth = imageContainer.width();
		var containerHeight = imageContainer.height() - footerHeight;

		imageContainer
			.css({
				top: ((popupHeight - containerHeight) / 2) - footerHeight,
				left: (popupWidth - containerWidth) / 2
			});
	},

	reposition: function() {

		var imagePopupFooter = $(imagePopupFooter_);
		var imagePopupThumbs = $(imagePopupThumbs_)
		var activeImagePopupThumb = $(imagePopupThumb_ + ".active");

		var midPoint = imagePopupFooter.width() / 2;
		var thumbMidPoint = activeImagePopupThumb.position().left + (activeImagePopupThumb.width() / 2);
		var thumbsLeft = midPoint - thumbMidPoint;

		imagePopupThumbs.css("left", thumbsLeft);
	},

	_reposition: $.debounce(function() {
		self.reposition();
	}, 350)
};

$(document)
	.on("click", imageViewport_, function(event){

		var imageViewport = $(this);
		var url = imageViewport.attr("href");

		if (url!=="javascript:void(0)") return;

		// If there is no link but there is a popup button,
		// simulate clicking on the sibling popup button.
		imageViewport.siblings(imagePopupButton_).click();
	})
	.on("click", imagePopupButton_, function(event){

		// If user holds shift/ctrl/cmd key when clicking on the button,
		// open image in a new page.
		if (event.shiftKey || event.ctrlKey || event.metaKey) {
			return;
		}

		// Get image popup button, image container and image caption.
		var imagePopupButton = $(this);
		var imageContainer = imagePopupButton.closest(imageContainer_);

		// Thumbnails
		var thumbContainer = imageContainer.closest(thumbContainer_);
		if (thumbContainer.length) {
			self.openThumbnails(thumbContainer, imageContainer);

		// Single image
		} else {
			self.openImage(imageContainer);
		};

		event.stopPropagation();
		event.preventDefault();
	})
	.on("click", imageViewport_, function(event){

		var imageViewport = $(this);

		// If image viewport has no link,
		// but there is an image popup,
		// then show popup.
		if (!imageViewport.attr("href")) {

			var imageContainer = imageViewport.closest(imageContainer_);
			var imagePopupButton = imageContainer.find(imagePopupButton_);

			if (imagePopupButton.length) {
				imagePopupButton.click();
				event.stopPropagation();
				event.preventDefault();
			}
		}
	})
	.on("click", imagePopupThumb_, function(event){

		var imagePopupThumb = $(this);

		self.openPopupThumb(imagePopupThumb);

		event.stopPropagation();
		event.preventDefault();
	})
	.on("click", imagePopupCloseButton_, function(){

		// Close popup
		self.close();
	});

module.resolve();

});