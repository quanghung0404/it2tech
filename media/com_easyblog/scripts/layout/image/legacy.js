EasyBlog.module("layout/image/legacy", function($){

var module = this;

function getImageAlignment(image) {

	var imageLink = image.parent();

	// Try image link float value
	var alignment = imageLink.css("float");

	// Try image float value
	if (alignment=="none") {
		alignment = image.css("float");
	}

	// Try image center value
	if (alignment=="none") {
		var imageStyle = image[0].style;
		if (imageStyle.marginLeft=="auto" && imageStyle.marginRight=="auto") {
			alignment = "center";
		}
	}

	// Try image align attribute
	if (alignment=="none") {

		// Try image align attribute from image link
		alignment = imageLink.attr("align");

		// Try image align attribute from image
		if (alignment===undefined || alignment=="none") {
			alignment  = image.attr("align");
		}
	}

	// If by now we could not get the alignment, use center alignment
	if (/none|middle/.test(alignment)) {
		alignment = "center";
	}

	return alignment;
}

// <a class="easyblog-thumb-preview" title="link_title_here" href="url_to_large_image"><img class="easyblog-image-caption" title="image_caption_text" alt="image_alt_text" src="url_to_thumb_image"></a>

var legacyImages = ".easyblog-thumb-preview img, img.easyblog-image-caption, img[data-popup], img[data-style]";

$(document).ready(function(){

	// Convert legacy image with popup to .eb-image
	$(legacyImages).each(function(){

		var image = $(this);
		var imageLink = image.parent();
		var imageUrl = image.attr("src");

		// Image Container
		var imageContainer = $('<div class="eb-image">');

		// Image Figure
		var imageFigure = $('<div class="eb-image-figure">');
		imageFigure.appendTo(imageContainer);

		// Image Link
		var imageViewport = $('<a class="eb-image-viewport"><img /></a>');
		imageViewport.appendTo(imageFigure);

		// Image Popup
		var hasOldPopup = imageLink.is(".easyblog-thumb-preview");
		var hasNewPopup = !!image.attr("data-popup");
		var hasPopup = hasOldPopup || hasNewPopup;
		var hasLink = imageLink.is("a:not(.easyblog-thumb-preview)");
		
		if (hasPopup) {

			// var imagePopup = $('<a class="eb-image-popup-button" target="_blank"><i class="fa fa-search"></i></a>');
			var popupUrl = hasOldPopup ? imageLink.attr("href") : image.attr("data-popup");
			imageViewport
				.addClass("eb-image-popup-button")
				.attr({
					href: popupUrl,
					title: imageLink.attr("title")
				});

		} else {
			
			imageViewport
				.attr({
					href: imageLink.attr("href"),
					title: imageLink.attr("title"),
					target: imageLink.attr("target")
				});
		}

		// Image Element
		var imageElement = imageContainer.find("img");
		imageElement
			.attr({
				src: imageUrl,
				width: image.attr("width"),
				height: image.attr("height"),
				alt: image.attr("alt")
			});

		// Image Caption
		var hasCaption = image.is(".easyblog-image-caption");

		if (hasCaption) {

			var imageCaption = $('<div class="eb-image-caption"><span></span></div>');
			var captionText = image.attr("title");

			imageCaption
				.appendTo(imageContainer)
				.find("span")
				.append(captionText);

			// If image width is readily available
			var imageWidth = image.attr("width");

			if (imageWidth) {

				// Set image width directly
				imageCaption.css("width", imageWidth);

			// If image width is not available
			} else {

				// Hide image caption first
				imageCaption.hide();

				// When image is loaded
				imageElement.on("load", function(){

					// Get image element width,
					// apply on image caption,
					// then show image caption.
					imageCaption
						.css("width", imageElement.width())
						.show();
				});
			}
		}

		// Image Style
		var imageStyle = image.attr("data-style") || (hasCaption ? "gray" : "");
		if (imageStyle) {
			imageContainer.addClass("style-" + imageStyle);
		}

		// Image alignment
		var imageAlignment = getImageAlignment(image);
		var blockContainer = $('<div class="ebd-block" data-type="image">');

		if (/left|right/.test(imageAlignment)) {
			blockContainer
				.addClass("is-nested nest-" + imageAlignment)
				.css("width", "auto");
		}

		if (/center/.test(imageAlignment)) {
			blockContainer
				.css("text-align", "center");
		}
		
		// Set image source
		blockContainer.append(imageContainer);
		
		// Replace old image with new image html
		if (hasPopup && !hasNewPopup) {
			imageLink.replaceWith(blockContainer);
		} else {
			image.replaceWith(blockContainer);
		}


	});

});


module.resolve();

});