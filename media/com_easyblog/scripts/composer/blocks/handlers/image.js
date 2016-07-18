EasyBlog.module("composer/blocks/handlers/image", function($){

var module = this;

var selectRatioView = "view-selectratio";
var customRatioView = "view-customratio";

var fitOrFill = /fit|fill/;
var isFluid = "is-fluid";
var isLoading = "is-loading";
var isFailed = "is-failed";
var isDifferent = "is-different";
var invalidVariationError = "Variation could not be retrieved because media meta does not exist in library!";

var imageSizeProps = [
    "image-width",
    "image-height"
];

var numsliderElements = [
    "numslider",
    "numslider-toggle",
    "numslider-widget",
    "numslider-value",
    "numslider-input",
    "numslider-units",
    "numslider-unit",
    "numslider-current-unit"
];

// Helpers
function getCssProp(prop) {
    return prop.replace(/image-/,"");
}

function parseUnit(val) {
    return val.toString().match("%") ? "%" : "px";
};

function roundToDecimalPoint(value, decimalPlace) {
    var p = Math.pow(10, decimalPlace);
    return Math.round(value * p) / p;
};

function ratioDecimal(ratio) {
    // If decimal was given, just return the ratio.
    if ($.isNumeric(ratio)) return ratio;
    var parts = ratio.split(":");
    return parts[0] / parts[1];
};

function ratioPercent(ratio, unit, decimalPlace) {
    return roundToDecimalPoint(ratioDecimal(ratio) * 100, decimalPlace || 3) + (unit ? "%" : 0);
};

function ratioPadding(ratio, decimalPlace) {
    return roundToDecimalPoint(1 / ratioDecimal(ratio) * 100, decimalPlace || 3) + "%";
};

function decimalToPercent(val, decimalPlace) {
    return roundToDecimalPoint((val * 100), decimalPlace || 3) + "%";
};

function sanitizeRatio(ratio) {
    ratio = $.trim(ratio);
    if (/\:/.test(ratio)) {
        var parts = ratio.split(":");
        return parseInt(parts[0]) + ":" + parseInt(parts[1]);
    } else {
        return parseFloat(ratio) || 0;
    }
};

function setCSSWidth(el, val) {
    return el.css("width", val);
}

function setCSSTop(el, val) {
    return el.css("top", val);
}

function setCSSLeft(el, val) {
    return el.css("left", val);
}

function setCSSPaddingTop(el, val) {
    return el.css("padding-top", val);
}

function setCSSHeight(el, val) {
    return el.css("height", val);
}

var resizeToFit  = $.Image.resizeWithin;
var resizeToFill = $.Image.resizeToFill;

EasyBlog.require()
.library(
    "plupload2",
    "imgareaselect"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Handlers.Image", {

    elements: [

        // Placeholder
        "[data-eb-composer-image-{placeholder|browse-button}]",

        // URL
        "[data-eb-image-{url-fieldset|url-field|url-field-text|url-field-update-button}]",

        // Source
        "[data-eb-image-{source-fieldset|source-field|source-thumbnail|source-title|source-size|source-url|source-change-button}]",

        // Source > Variation
        "[data-eb-image-{variation-field|variation-list-container|variation-new-button|variation-create-button|variation-rebuild-button|variation-delete-button|variation-cancel-button|variation-cancel-failed-button}]",
        "[data-eb-image-source-fieldset] [data-eb-mm-{variation-list|variation-list-item-group|variation-item}]",
        "[data-eb-image-{variation-name|variation-width|variation-height}]",

        // Size
        "[data-eb-{image-size-fieldset|image-size-simple-field|image-size-advanced-field}]",
        "^imageSize [data-eb-image-size-{preset-toggle|current-preset|preset|retry-button}]",

        // Size > Dimensions
        "[data-eb-image-size-fieldset] [data-eb-{" + numsliderElements.join("|") + "}]",
        "^imageSizeField .eb-composer-field[data-name={" + imageSizeProps.join("|") + "}]",

        // Size > Alignment
        "[data-eb-image-{alignment-selection}]",

        // Size > Ratio
        "[data-eb-image-{ratio-lock|ratio-button|ratio-label|ratio-customize-button|ratio-cancel-button|ratio-use-custom-button|ratio-cancel-custom-button|ratio-selection|ratio-preview|ratio-input}]",

        // Size > Advanced
        "[data-eb-image-{map|map-container|map-figure|map-viewport|map-preview|map-picker}]",
        "[data-eb-image-{strategy-menu-item|strategy-menu-content}]",
        "[data-eb-image-{resize-input-field|resize-ratio-lock|resize-reset-button}]",

        // Style
        "[data-eb-image-{style-toggle|style-selection}]",

        // Caption
        "[data-eb-image-{caption-toggle|caption-text-field}]",

        // Link
        "[data-eb-image-{link-toggle}]",
        "[data-eb-image-link-fieldset] [data-eb-{link-url-field|link-title-field|link-blank-option}]",

        // Popup
        "[data-eb-image-{popup-toggle|popup-fieldset|popup-field|popup-thumbnail|popup-title|popup-size|popup-url|popup-change-button}]",
        "[data-eb-image-{popup-variation-field|popup-variation-list-container}]",
        "^popup [data-eb-image-popup-fieldset] [data-eb-mm-{variation-list|variation-list-item-group|variation-item}]",
    ],

    defaultOptions: $.extend({

        uploader: {
            runtimes: "html5,flash",
            url: "/echo/json",
            max_file_size: '10mb',
            filters: [
                {
                    title: "Image files",
                    extensions: "jpg,gif,png"
                }
            ]
        },

        "{browseButton}": ".eb-composer-placeholder-image [data-eb-mm-browse-button]",
        "{imagePlaceholder}": "[data-eb-composer-image-placeholder]",
        "{imageContainer}": ".eb-image",
        "{imageFigure}": ".eb-image-figure",
        "{imageViewport}": ".eb-image-viewport",
        "{imageElement}": ".eb-image-figure img",
        "{imagePopupButton}": ".eb-image-popup-button",
        "{imageCaption}": ".eb-image-caption",
        "{imageCaptionText}": ".eb-image-caption > span",
        "{imageHint}": ".eb-image-hint",

        // via url
        "{imageUrlForm}" : "[data-eb-image-url-form]",
        "{imageUrlTextbox}" : "[data-eb-image-url-textbox]",
        "{imageUrlAdd}" : "[data-eb-image-url-add]",
        "{imageUrlCancel}" : "[data-eb-image-url-cancel]"

    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, meta, currentBlock, mediaManager, panelsFieldset) {

    function isImageBlock(block) {
        return blocks.getBlockType(block)=="image";
    }

    return {

    init: function() {

        // Globals
        blocks       = self.blocks;
        composer     = blocks.composer;
        meta         = opts.meta;
        currentBlock = $();
        mediaManager = EasyBlog.MediaManager;
        panelsFieldset = composer.panels.fieldset;

        // INTERNAL HACK
        // Duckpunch .of() to accept prop
        $.each(numsliderElements, function(i, element){

            var method = $.camelize(element);
            var cache = {};

            self[method].of = function(prop){

                var numsliderElement = cache[prop];

                if (!numsliderElement) {
                    // Get numslider field of this prop and return
                    // numslider element under this numslider field
                    var numsliderField = self.getImageSizeField(prop);
                    numsliderElement = self[method].under(numsliderField);

                    if (numsliderElement.length) {
                        cache[prop] = numsliderElement;
                    }
                }

                return numsliderElement;
            }
        });
    },

    deactivate: function(block) {

    },

    activate: function(block) {

        // Set as current block
        currentBlock = block;

        // Always center align block
        if (block.hasClass("is-new")) {
            block.css("text-align", "center");
        }

        // Populate fieldset
        self.populate(block);
    },

    construct: function(data) {

        var block = blocks.createBlockContainer("image");
        var data = $.extend(blocks.data(block), data);

        var blockContent = blocks.getBlockContent(block).empty();
        var imageContainer = self.constructImage(data);

        // Append image container to block content
        blockContent.append(imageContainer);

        // Always center align block
        block.css("text-align", "center");

        return block;
    },

    constructFromMediaFile: function(mediaFile) {

        var key = mediaFile.data("key");
        var uri = mediaManager.getUri(key);

        // Create block container first
        var block = blocks.createBlockContainer("image");
        var blockContent = blocks.getBlockContent(block);
        var data = blocks.data(block);

        // Always center align block
        block.css("text-align", "center");

        // Add loading indicator
        block.addClass("is-loading");

        // Get media meta
        mediaManager.getMedia(uri)
            .done(function(media){

                // Get meta and variation
                var mediaMeta = media.meta;
                var variation = mediaManager.getVariation(mediaMeta.uri, "thumbnail");

                data.uri = mediaMeta.uri;
                data.url = variation.url;
                data.simple = 'simple';
                data.ratio_lock = true;
                data.variation = variation.key;

                var imageContainer = self.constructImage(data);

                // Append image container to block content
                blockContent.html(imageContainer);

                // If this block is still active, populate image block
                if (block.hasClass("active")) {
                    self.populate(block);
                }
            })
            .fail(function(){

                // If unable to get media meta, revert block to placeholder.
                var imagePlaceholder = $(meta.html);
                imagePlaceholder.addClass("state-failed"); // TODO: Might need another failed state for failed media file
                blockContent.html(imagePlaceholder);
            })
            .always(function(){
                block.removeClass("is-loading");
            });

        return block;
    },

    constructImage: function(data) {

        var imageContainer = $(meta.imageContainer);
        var imageFigure = self.imageFigure.inside(imageContainer);

        // Set image src
        var imageElement = self.imageElement.inside(imageContainer);
        imageElement.attr("src", data.url);

        // Add is-fluid class if necessary
        if (data.fluid) {
            imageContainer.addClass(isFluid);
        }

        // Set image style
        imageContainer
            .addClass("style-" + data.style);

        // Set image caption
        if (data.caption_text) {

            imageContainer
                .append(meta.imageCaption);

            self.imageCaptionText.inside(imageContainer)
                .html(data.caption_text);
        }

        // Set image popup
        if (data.popup_url) {
            var imagePopupButton = $(meta.imagePopupButton);

            imagePopupButton
                .attr("href", data.popup_url);

            imageFigure.append(imagePopupButton);
        }

        return imageContainer;
    },

    toData: function(block) {

        var data = blocks.data(block);

        return data;
    },

    toHTML: function(block) {

        if (!self.hasImage(block)) {
            return "";
        }

        var clone = block.clone();
        var deconstructedBlock = self.deconstruct(clone);
        var content = blocks.getBlockContent(deconstructedBlock);

        return content.html();
    },

    toLegacyHTML: function(block) {

        var data = self.toData(block);

        var image =
            $("<img>")
                .attr({
                    src: data.url,
                    width: data.width,
                    height: data.height
                });

        // If this image has caption, add caption text
        if (data.caption_text) {
            image
                .addClass("easyblog-image-caption")
                .attr("title", data.caption_text);
        }

        // If this image has popup, add data-popup attribute
        if (data.popup_url) {
            image.attr("data-popup", data.popup_url);
        }

        if (data.style) {
            image.attr("data-style", data.style);
        }

        // If this image has link, wrap in link.
        if (data.link_url) {

            var imageLink =
                $("<a>")
                    .attr({
                        href: data.link_url,
                        title: data.link_title,
                        target: data.link_target
                    })
                    .append(image);

            return imageLink.prop("outerHTML");
        }

        return image.prop("outerHTML");
    },

    toText: function(block) {

        var captionText = self.imageCaptionText.inside(block).text();
        var altText = self.imageElement.inside(block).attr("alt");

        return captionText + "\n" + altText;
    },

    reconstruct: function(block) {

        var imagePlaceholder = self.imagePlaceholder.inside(block);

        if (imagePlaceholder.length > 0) {
            EasyBlog.MediaManager.uploader.register(imagePlaceholder);
        }
    },

    hasImage: function(block) {

        return self.imageContainer.inside(block).length > 0;
    },

    "{imagePlaceholder} mediaUploaderFilesAdded": function(imagePlaceholder, event, uploader, files) {

        EasyBlog.MediaManager.uploader.addItem(files[0], imagePlaceholder);
    },

    "{imagePlaceholder} mediaUploaderFileUploaded": function(imagePlaceholder, event, uploader, file, data) {

        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        var block = blocks.block.of(imagePlaceholder);

        setTimeout(function(){
            self.updateImageSource(block, mediaMeta);
        }, 600);
    },

    "{browseButton} mediaSelect": function(browseButton, event, media) {

        var block = blocks.block.of(browseButton);

        if (media.meta.type!="image") {
            return;
        }

        var mediaMeta = media.meta;

        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        // Legacy
        if (isLegacy) {
            content = self.toLegacyHTML(block);
            composerDocument.insertContent(content);

        // EBD
        } else {

            self.setImageSource(block, mediaMeta);

            // Always center align block
            // block.css("text-align", "center");
        }
    },

    "{popupChangeButton} mediaSelectStart": function(popupChangeButton, event, media) {
        var uid = currentBlock.data("uid");

        // Set the block's uid so that we can retrieve it later when the media is selected
        popupChangeButton.data("uid", uid);
    },

    "{popupChangeButton} mediaSelect": function(popupChangeButton, event, media) {

        if (media.meta.type!="image") {
            return;
        }

        var currentUid = currentBlock.data('uid');
        var targetUid = popupChangeButton.data('uid');
        var mediaMeta = media.meta;
        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        if (currentUid != targetUid) {
            var block = blocks.getBlock(targetUid);

            self.setImagePopup(block, mediaMeta.uri);
        } else {
            self.updateImagePopup(currentBlock, mediaMeta.uri);
        }
    },

    "{sourceChangeButton} mediaSelectStart": function(sourceChangeButton) {
        var uid = currentBlock.data("uid");

        // Set the block's uid so that we can retrieve it later when the media is selected
        sourceChangeButton.data("uid", uid);
    },

    "{sourceChangeButton} mediaSelect": function(sourceChangeButton, event, media) {

        if (media.meta.type!="image") {
            return;
        }

        var currentUid = currentBlock.data('uid');
        var targetUid = sourceChangeButton.data('uid');
        var mediaMeta = media.meta;
        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        // Legacy
        if (isLegacy) {
            content = self.toLegacyHTML(block);
            composerDocument.insertContent(content);

        // EBD
        } else {

            if (currentUid != targetUid) {
                var block = blocks.getBlock(targetUid);

                self.setImageSource(block, mediaMeta);
            } else {
                self.updateImageSource(currentBlock, mediaMeta);
            }
        }
    },

    "{self} mediaInsert": function(el, event, media, block) {

        if (media.meta.type!="image") {
            return;
        }

        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        // Normalize image size first
        self.normalizeImageSize(block);

        // Legacy
        if (isLegacy) {
            content = self.toLegacyHTML(block);
            composerDocument.insertContent(content);

        // EBD
        } else {
            var data = blocks.data(block);
            var block = blocks.constructBlock("image", data);
            blocks.addBlock(block);
            blocks.activateBlock(block);
        }
    },

    deconstruct: function(block) {

        // Nothing to deconstruct
        return block;
    },

    refocus: function(block) {
    },

    reset: function(block) {
    },

    populate: function(block) {

        var hasImage = self.hasImage(block);

        // Hide fieldgroup if there is no video
        var fieldgroup = blocks.panel.fieldgroup.get("image");
        fieldgroup.toggleClass("is-new", !hasImage);

        if (hasImage) {
            self.populateImageUrl(block);
            self.populateImageSource(block);
            self.populateImageSize(block);
            self.populateImageCaption(block);
            self.populateImageLink(block);
            self.populateImagePopup(block);
            self.populateImageStyle(block);
        }
    },

    //
    // Image Hint
    //
    showImageHint: function(block, content) {

        // Remove existing image hint
        self.imageHint.inside(block).remove()

        // Create image hint
        var imageHint = $(meta.imageHint);

        // Set image hint content
        imageHint.find(".eb-hint-text").html(content);

        // Get image viewport
        var imageViewport = self.imageViewport.inside(block);

        // Append image hint to image viewport
        imageViewport.append(imageHint);

        // This will initiate the slow-fading effect
        imageHint
            .removeClassAfter("is-new", 1000);

        // This will remove the image hint after 1.5s
        setTimeout(function(){
            imageHint.remove();
        }, 2500);
    },

    //
    // Image Source
    //
    setImageSource: function(block, mediaMeta) {

        //solo33/images/easyblog_images/605/b2ap3_icon_15_20150303-094940_1.jpg
        var data = blocks.data(block);

        var variation = mediaManager.getVariation(mediaMeta.uri, "thumbnail");

        // Set data from variation
        data.variation = variation.key;
        data.url = variation.url;
        data.uri = mediaMeta.uri;
        data.natural_width = variation.width;
        data.natural_height = variation.height;
        data.natural_ratio = variation.width / variation.height;

        // Construct image container
        var imageContainer = self.constructImage(data);

        // Append image container to block content
        blocks.getBlockContent(block)
            .empty()
            .append(imageContainer);
    },

    updateImageSource: function(block, mediaMeta) {

        self.setImageSource(block, mediaMeta);
        self.populate(block);
    },

    getSourceThumbnailImage: function(url) {

        return $("<img>").attr("src", url)[0];
    },

    populateImageUrl: function(block) {
        var data = blocks.data(block);

        var urlFieldset = self.urlFieldset();
        urlFieldset.removeClass('hidden');

        if (! data.isurl) {
            urlFieldset.addClass('hidden');
            return;
        }

        self.urlFieldText().val(data.url);
    },

    populateImageSource: function(block) {

        var data = blocks.data(block);

        var sourceFieldset = self.sourceFieldset();
        sourceFieldset.removeClass('hidden');

        if (data.isurl) {
            sourceFieldset.addClass('hidden');
            return;
        }

        var uri = data.uri;

        var sourceFieldset = self.sourceFieldset();

        sourceFieldset.addClass(isLoading);


        mediaManager.getMedia(uri)
            .done(function(media){

                // Get media meta
                var mediaMeta = media.meta;

                // Source thumbnail image
                var sourceThumbnailImage = self.getSourceThumbnailImage(mediaMeta.thumbnail);
                self.sourceThumbnail()
                    .empty()
                    .append(sourceThumbnailImage);

                // Source title
                self.sourceTitle()
                    .text(mediaMeta.title);

                // Get variation list
                var variationList = $(media.variations);

                // Append variation list to container
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                self.populateImageVariation(block);
            })
            .fail(function(){
                sourceFieldset.addClass(isFailed);
            })
            .always(function(){
                sourceFieldset.removeClass(isLoading);
            });


    },

    populateImageVariation: function(block) {

        var data = blocks.data(block);
        var uri = data.uri;
        var variationKey = data.variation;
        var variation = mediaManager.getVariation(uri, variationKey);

        // If meta could not be retrieved, stop and throw error.
        if (!variation) {
            console.error(invalidVariationError);
            return;
        };

        // Activate the correct variation item
        var variationItem =
            self.variationItem()
                .where("key", '"' + variationKey + '"')
                .activateClass("active");

        // Variation Size
        self.sourceSize()
            .text($.plupload2.formatSize(variation.size || ""));

        // Variation Url
        self.sourceUrl()
            .text(variation.url || "");

        // Toggle variation delete button
        var isSystem = variationItem.hasClass("is-system");
        var isMissing = variationItem.hasClass("is-missing");

        self.variationField()
            .toggleClass("can-delete", !isSystem)
            .toggleClass("is-missing", isMissing);
    },

    setImageVariation: function(block, variationKey) {

        // Get block data
        var data = blocks.data(block);
        var uri = data.uri;
        var variation = mediaManager.getVariation(uri, variationKey);

        // If meta could not be retrieved, stop and throw error.
        if (!variation) return (EasyBlog.debug && console.error(invalidVariationError));

        // Set url to variation on image element
        self.imageElement.inside(block)
            .attr("src", variation.url);

        // Show hint
        self.showImageHint(block, $.String.capitalize(variation.name) + '<br/><span style="font-weight: normal;">' + variation.width + "x" + variation.height + '</span>');

        // Store variation key in block data
        data.variation = variationKey;

        // we need to set the data.url as well
        data.url = variation.url;
        // data.uri = variation.uri;
    },

    updateImageVariation: function(block, variationKey) {

        self.setImageVariation(block, variationKey);
        self.populateImageVariation(block);
    },

    "{variationItem} click": function(variationItem) {

        var variationKey = variationItem.data("key");

        self.updateImageVariation(currentBlock, variationKey);
    },

    "{variationNewButton} click": function(variationNewButton) {

        self.variationField()
            .addClass("show-create-form");

        var data = blocks.data(currentBlock);
        var uri = data.uri;

        // Get original variation
        var variation = mediaManager.getVariation(uri, "system/original");

        // Set default input values based on selected variation
        var width = variation.width;
        var height = variation.height;
        var ratio = width / height;

        self.variationWidth()
            .val(width)
            .data("value", width)
            .data("ratio", ratio);

        self.variationHeight()
            .val(height)
            .data("value", height)
            .data("ratio", ratio);
    },

    "{variationCancelButton} click": function(variationCancelButton) {

        self.variationField()
            .removeClass("show-create-form");
    },

    "{variationCreateButton} click": function(variationCreateButton) {

        // Get variation field
        var variationField = self.variationField();

        // Get variation name, width & height
        var name = $.trim(self.variationName().val());
        var width = self.variationWidth().val();
        var height = self.variationHeight().val();

        // Do not create when name is blank
        if ($.trim(name)=="") {
            return;
        }

        // Show loading indicator
        variationField
            .addClass("is-creating");

        var block = currentBlock;
        var data = blocks.data(block);
        var uri = data.uri;

        // Make an ajax call to create variation
        mediaManager.createVariation(uri, name, width, height)
            .done(function(media){

                // Get variation list
                var variationList = $(media.variations);

                // Append variation list to container
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                // Populate image variation
                self.populateImageVariation(block);

                variationField
                    .removeClass("show-create-form");
            })
            .fail(function(){

                variationField
                    .removeClass("is-creating")
                    .addClass("is-failed");
            })
            .always(function(){

                variationField
                    .removeClass("is-creating");
            });
    },

    "{variationCancelFailedButton} click": function() {

        self.variationField()
            .removeClass("is-failed");
    },

    "{variationRebuildButton} click": function(variationRebuildButton) {

        var data = blocks.data(currentBlock);
        var uri = data.uri;

        var activeVariation = self.variationItem(".active");
        var variationKey = activeVariation.data("key");

        var variation = mediaManager.getVariation(uri, variationKey);
        var name = variation.name;
        var key = mediaManager.getKey(uri);

        // Make an ajax call to rebuild selected variation
        EasyBlog.ajax('site/views/mediamanager/rebuildVariation', {
            "name": name,
            "key": key
        }).done(function(media) {
                // Get variation list
                var variationList = $(media.variations);

                // Append variation list to container
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                // Populate image variation
                self.populateImageVariation(currentBlock);

                // Update cache with update media object
                mediaManager.setMedia(uri, media);
        })
    },

    "{variationDeleteButton} click": function(variationDeleteButton) {

        var data = blocks.data(currentBlock);
        var uri = data.uri;
        var activeVariation = self.variationItem(".active");
        var variationKey = activeVariation.data("key");

        var variation = mediaManager.getVariation(uri, variationKey);
        var variationName = variation.name;

        mediaManager.removeVariation(uri, variationName)
            .done(function(media){

                // Append variation list to container
                var variationList = $(media.variations);
                self.variationListContainer()
                    .empty()
                    .append(variationList);

                // Use image variation
                var variation = mediaManager.getVariation(uri, "thumbnail");
                self.updateImageVariation(currentBlock, variation.key);
            });
    },

    "{variationWidth} input": function(variationWidth) {

        var ratio = variationWidth.data("ratio");
        var width = Math.round(parseFloat(variationWidth.val()));

        if ($.isNumeric(width)) {
            var height = Math.round(width / ratio);
            self.variationHeight().val(height);
        }
    },

    "{variationHeight} input": function(variationHeight) {

        var ratio = variationHeight.data("ratio");
        var height = Math.round(parseFloat(variationHeight.val()));

        if ($.isNumeric(height)) {
            var width = Math.round(height * ratio);
            self.variationWidth().val(width);
        }
    },

    "{variationWidth} focus": function(variationWidth) {

        variationWidth.select();
    },

    "{variationHeight} focus": function(variationWidth) {

        variationWidth.select();
    },

    "{variationWidth} blur": function(variationWidth) {

        var ratio = variationWidth.data("ratio");
        var width = Math.round(parseFloat(variationWidth.val()));
        variationWidth.val(width);

        if (!$.isNumeric(width)) {

            var variationHeight = self.variationHeight();
            var height = Math.round(parseFloat(variationHeight.val()));

            if ($.isNumeric(height)) {
                width = Math.round(height * ratio);
                variationWidth.val(width);
                variationHeight.val(height);
            } else {
                self.resetVariationFields();
            }
        }
    },

    "{variationHeight} blur": function(variationHeight) {

        var ratio = variationHeight.data("ratio");
        var height = Math.round(parseFloat(variationHeight.val()));
        variationHeight.val(height);

        if (!$.isNumeric(height)) {

            var variationWidth = self.variationWidth();
            var width = Math.round(parseFloat(variationWidth.val()));

            if ($.isNumeric(width)) {
                height = Math.round(width / ratio);
                variationWidth.val(width);
                variationHeight.val(height);
            } else {
                self.resetVariationFields();
            }
        }
    },

    resetVariationFields: function() {

        var variationWidth = self.variationWidth();
        var variationHeight = self.variationHeight();

        variationWidth.val(variationWidth.data("width"));
        variationHeight.val(variationHeight.data("height"));
    },

    //
    // Image Mode
    //
    populateImageMode: function(block) {

        // Get image mode
        var data = blocks.data(block);
        var mode = data.mode;
        var modeLock = data.mode_lock;

        // When on root block, lock to simple mode.
        // TODO: Fix issues related to advanced mode on root block and remove this restriction.
        if (blocks.isRootBlock(block)) {
            mode = "simple";
            modeLock = true;
        }

        // Show/hide mode dropdown
        self.imageSizeFieldset()
            .toggleClass("mode-lock", modeLock);

        // Set active dropdown item to preset to be selected
        var imageSizePreset =
            self.imageSizePreset()
                .removeClass("active")
                .where("type", mode)
                .addClass("active");

        // Update dropdown label to match preset name
        var imageSizePresetLabel = $.trim(imageSizePreset.text());
        self.imageSizeCurrentPreset()
            .html(imageSizePresetLabel);

        // Show image size fields relevant to this preset
        var presetClassname = "preset-" + mode;
        self.imageSizeFieldset()
            .switchClass(presetClassname);
    },

    setImageMode: function(block, mode) {

        if (mode=="simple") {
            self.toSimpleImageMode(block);
        }

        if (mode=="advanced") {
            self.toAdvancedImageMode(block);
        }
    },

    toSimpleImageMode: function(block) {

        var data = blocks.data(block);
        data.mode = "simple";

        // Reset data values
        data.ratio = data.natural_ratio;
        data.ratio_lock = true;
        data.element_width = "";
        data.element_height = "";
        data.element_top = "";
        data.element_left = "";

        // Clear image element css properties
        var imageElement = self.imageElement.inside(block);
        imageElement.css({
            width: "",
            height: "",
            top: "",
            left: ""
        });

        // Normalize image
        self.normalizeImageSize(block);
    },

    toAdvancedImageMode: function(block) {

        var data = blocks.data(block);
        data.mode = "advanced";

        // If ratio hasn't been assigned, use natural ratio.
        if (!data.ratio) {
            data.ratio = data.natural_ratio;
        }

        // If element ratio hasn't been assigned, use ratio.
        if (!data.element_ratio) {
            data.element_ratio = data.ratio;
        }

        // Normalize image
        self.normalizeImageSize(block);

        // Resize to fit viewport
        self.resizeToFitViewport(block);
    },

    updateImageMode: function(block, mode) {

        // Show fields relevant to image type
        self.setImageMode(block, mode);

        // Populate image size
        self.populateImageSize(block);
    },

    "{imageSizePreset} click": function(imageSizePreset) {

        var mode = imageSizePreset.data("type");

        self.updateImageMode(currentBlock, mode);
    },

    //
    // Image Size
    //
    populateImageSize: (function() {

        var populateImageSize = function(block) {

            // Do not populate if block is not current block
            // TODO: Not sure if this should be here because implementor
            // should be able to programmatically populate a block.
            if (!block.is(currentBlock)) return;

            // Populate slider, input & unit for image width & height
            var props = props || ["width", "height"];
            var prop;

            while (prop = props.shift()) {

                var value  = prop=="width" ? self.getImageWidth(block) : self.getImageHeight(block); // 1280
                var number = parseFloat(value); // 1280, 100
                var unit   = parseUnit(value); // px, %

                // Update numslider widget
                // only if user is not resizing from slider
                if (self.resizingFromSlider!==prop) {

                    // Pixel unit
                    if (unit=="px") {
                        var sliderOptions = {
                            start: number,
                            step: 1,
                            range: {
                                min: 1,
                                max: 1600
                            },
                            pips: {
                                mode: "values",
                                values: [64, 320, 640, 960, 1280, 1600],
                                density: 4
                            }
                        };
                    }

                    // Percent unit
                    if (unit=="%") {
                        var sliderOptions = {
                            start: number,
                            step: 1,
                            range: {
                                min: 1,
                                max: 100
                            },
                            pips: {
                                mode: "values",
                                values: [0, 20, 40, 60, 80, 100],
                                density: 5
                            }
                        }
                    }

                    // Set up slider
                    self.numsliderWidget.of(prop)
                        .find(".noUi-pips")
                        .remove()
                        .end()
                        .noUiSlider(sliderOptions, true)
                        .noUiSlider_pips(sliderOptions.pips);
                }

                // Update numslider input
                self.numsliderInput.of(prop)
                    .val(Math.round(number));

                // Update numslider current unit
                self.numsliderCurrentUnit.of(prop)
                    .html(unit);

                // Update numslider unit dropdown
                self.numsliderUnit.of(prop)
                    .removeClass("active")
                    .where("unit", '"' + unit + '"')
                    .addClass("active");
            }

            // Determine if simple field should be hidden
            // This is used for Thumbnails, Gallery & Comparison block.
            var data = blocks.data(block);

            var hideSimpleField = data.width_lock && data.height_lock;

            self.imageSizeFieldset()
                .toggleClass("hide-simple-field", hideSimpleField);

            // Also populate image mode, ratio, alignment & advanced fields.
            self.populateImageMode(block);
            self.populateImageRatio(block);
            self.populateImageAlignment(block);
            self.populateImageAdvancedFields(block);
        }

        return function(block) {

            var data = blocks.data(block);
            var imageSizeFieldset = self.imageSizeFieldset();
            var imageElement = self.imageElement.inside(block);

            // Show or hide image size fieldset
            var sizeEnabled = data.size_enabled;
            panelsFieldset.toggle("image-size", sizeEnabled);

            // If image has loaded, populate image size.
            if (imageElement[0].complete) {

                imageSizeFieldset.removeClass("is-loading is-failed");
                populateImageSize(block);

            // If image is not loaded, wait until it is loaded, then populate image size.
            } else {

                var imageUrl = imageElement.attr("src");
                imageSizeFieldset.addClass("is-loading");

                $.Image.get(imageUrl)
                    .done(function(){
                        populateImageSize(block);
                    })
                    .fail(function(){
                        imageSizeFieldset.switchClass("is-failed");
                    })
                    .always(function(){
                        imageSizeFieldset.removeClass("is-loading is-failed");
                    });
            }
        }
    })(),

    "{imageSizeRetryButton} click": function() {

        self.populateImageSize(currentBlock);
    },

    setImageSize: function(block, prop, val) {

        if (prop=="width") {
            self.setImageWidth(block, val);
        }

        if (prop=="height") {
            self.setImageHeight(block, val);
        }
    },

    updateImageSize: function(block, prop, val) {

        self.setImageSize(block, prop, val);
        self.populateImageSize(block);
    },

    normalizeImageSize: function(block) {

        var data = blocks.data(block);
        var isFluidImage = self.isFluidImage(block);

        // If this is a root block, get width from image figure.
        if (blocks.isRootBlock(block)) {
            var width = self.imageFigure.inside(block).width();
        }

        // If this is a nested block, get width from block.
        if (blocks.isNestedBlock(block)) {
            // var width = blocks.dimensions.getFluidWidth(block);
            // Gallery block have to 100% full width no matter is root or nested. #2793
            // var width = "100%";

            // Fixed locked image ratio not propogate the height correctly. #2827
            var width = self.imageFigure.inside(block).width();
        }

        // Set image width again.
        // This will automatically set image height.
        self.setImageWidth(block, width);

        // Convert to fluid image
        // There's only fluid image in advanced mode for now.
        if (isFluidImage || data.mode=="advanced") {
            self.toFluidImage(block);
        }
    },

    resetImageSize: function(block) {

        var data = blocks.data(block);
        data.mode = "simple";

        var imageContainer = self.imageContainer.inside(block);
        var imageFigure = self.imageFigure.inside(block);
        var imageElement = self.imageElement.inside(block);

        var removeCSSProperties = {
            width: "",
            height: "",
            top: "",
            left: "",
            paddingTop: "",
            float: ""
        };

        imageContainer.css(removeCSSProperties);
        imageFigure.css(removeCSSProperties);
        imageElement.css(removeCSSProperties);

        if (blocks.isRootBlock(block)) {
            self.setImageWidth(block, "100%");
        }

        if (blocks.isNestedBlock(block)) {
            self.setImageWidth(block, "30%");
        }
    },

    getImageWidth: function(block) {

        var imageFigure = self.imageFigure.inside(block);
        var imageFigureStyle = imageFigure[0].style;

        // Root block (%) - assigned image container width
        if (blocks.isRootBlock(block)) {
            var imageContainer = self.imageContainer.inside(block);
            var assignedImageContainerWidth = imageContainer[0].style.width;

            // If assigned image container width has a % on it, use it.
            if (/%/.test(assignedImageContainerWidth)) {
                return assignedImageContainerWidth;
            }
        }

        // Nested block (%) - assigned block width
        if (blocks.isNestedBlock(block)) {

            // Get assigned block width
            var assignedBlockWidth = block[0].style.width;

            // If assigned block width has a % on it, use it.
            if (/%/.test(assignedBlockWidth)) {
                return assignedBlockWidth;
            }
        }

        // Root block (px) or nested block (px)
        // Get assigned width, else get computed width.
        return imageFigureStyle.width || imageFigure.css("width");
    },

    setImageWidth: function(block, width) {

        // Get data & unit
        var data = blocks.getData(block);
        var unit = parseUnit(width);
        var num = parseFloat(width);
        var isPercentUnit = unit=="%";
        var isPixelUnit = unit=="px";
        var isSimpleImage = data.mode=="simple";
        var isAdvancedImage = data.mode=="advanced";

        //
        // Width
        //
        var imageContainer = self.imageContainer.inside(block);
        var imageElement   = self.imageElement.inside(block);
        var imageViewport  = self.imageViewport.inside(block);
        var imageFigure    = self.imageFigure.inside(block);

        // Get original computed width & height
        var originalComputedWidth = imageElement.width();
        var originalComputedHeight = imageElement.height();

        // Fluidity
        if (isPercentUnit) {
            data.fluid = true;
            imageContainer.addClass(isFluid);
        }

        if (isPixelUnit) {
            data.fluid = false;
            imageContainer.removeClass(isFluid);
        }

        // If on advanced image, convert to fixed element first.
        if (isAdvancedImage) {
            self.toFixedElement(block);
        }

        // Root block
        if (blocks.isRootBlock(block)) {

            block.css("width", "");

            if (isPercentUnit) {
                setCSSWidth(imageContainer, width);
                setCSSWidth(imageFigure, "100%");

                if (isSimpleImage) {
                    setCSSWidth(imageElement, "100%");
                    data.element_width = "100%";
                }
            }

            if (isPixelUnit) {
                setCSSWidth(imageContainer, "");
                setCSSWidth(imageFigure, width);

                if (isSimpleImage) {
                    setCSSWidth(imageElement, width);
                    data.element_width = parseFloat(width);
                }
            }
        }

        // Nested block
        if (blocks.isNestedBlock(block)) {

            // Percent
            if (isPercentUnit) {
                setCSSWidth(block, width);
                setCSSWidth(imageFigure, "");

                if (isSimpleImage) {
                    setCSSWidth(imageElement, "100%");
                    data.element_width = "100%";
                }
            }

            // Pixels
            if (isPixelUnit) {
                setCSSWidth(block, "auto");
                setCSSWidth(imageFigure, width);

                if (isSimpleImage) {
                    setCSSWidth(imageElement, width);
                    data.element_width = parseFloat(width);
                }
            }
        }

        // Set width to block data
        data.width = isPercentUnit ? width : num;

        //
        // Height & Ratio
        //
        var ratio = data.ratio;
        var ratioLock = data.ratio_lock;
        var naturalRatio = data.natural_ratio;

        if (ratioLock) {

            var ratioToUse = isSimpleImage ? naturalRatio : ratio;

            if (isPercentUnit) {

                setCSSHeight(imageFigure, "");
                setCSSPaddingTop(imageFigure, ratioPadding(ratioToUse));

                if (isSimpleImage) {
                    setCSSHeight(imageElement, "100%");
                    data.element_height = "100%";
                }

                data.height = "100%";
            }

            if (isPixelUnit) {

                var height = num / ratioDecimal(ratioToUse);

                setCSSHeight(imageFigure, height);
                setCSSPaddingTop(imageFigure, "");

                if (isSimpleImage) {
                    setCSSHeight(imageElement, height);
                    data.element_height = height;
                }

                data.height = height;
            }

            data.ratio = ratioToUse;

            // Also assign to element ratio if this is a simple image
            if (isSimpleImage) {
                data.element_ratio = ratioToUse;
            }

        } else {

            var computedWidth = imageElement.width();
            var ratio = computedWidth / originalComputedHeight;

            if (isPercentUnit) {
                setCSSPaddingTop(imageFigure, ratioPadding(ratio));
            }

            if (isPixelUnit) {
                setCSSPaddingTop(imageFigure, "");
            }

            data.ratio = ratio;

            // Also assign to element ratio if this is a simple image
            if (isSimpleImage) {
                data.element_ratio = ratio;
            }
        }

        // Convert back to fluid element
        if (isAdvancedImage) {
            self.toFluidElement(block);

            var strategy = data.strategy;
            if (/fit|fill/.test(strategy)) {
                self.resizeToViewport(block, strategy);
            }
        }

        //
        // Caption
        //
        var imageCaption = self.imageCaption.inside(block);

        if (isPercentUnit) {
            setCSSWidth(imageCaption, "100%");
        }

        if (isPixelUnit) {
            setCSSWidth(imageCaption, width);
        }
    },

    updateImageWidth: function(block, width) {

        self.setImageWidth(block, width);
        self.populateImageSize(block);
    },

    getImageHeight: function(block) {

        var imageFigure = self.imageFigure.inside(block);
        var imageFigureStyle = imageFigure[0].style;

        // Root/nested (%)  - computed figure height
        // Root/nested (px) - assigned figure height
        return imageFigureStyle.height || imageFigure.css("height");
    },

    setImageHeight: function(block, height) {

        var data = blocks.data(block);
        var height = parseFloat(height);

        var ratio = data.ratio;
        var ratioLock = data.ratio_lock;
        var naturalRatio = data.natural_ratio;
        var isSimpleImage = data.mode=="simple";
        var isAdvancedImage = data.mode=="advanced";

        //
        // Height
        //
        var imageElement = self.imageElement.inside(block);
        var imageViewport = self.imageViewport.inside(block);
        var imageFigure = self.imageFigure.inside(block);

        if (self.isFluidImage(block)) {

            // If ratio is locked, calculate width from height,
            // then convert fixed image to fluid image.
            if (ratioLock) {
                var ratioToUse = isSimpleImage ? naturalRatio : ratio;
                var width = height * ratioDecimal(ratioToUse);
                self.setImageWidth(block, width);
                self.toFluidImage(block);
                data.ratio = ratioToUse;

                if (isSimpleImage) {
                    data.element_ratio = ratioToUse;
                }

            // If ratio is unlocked, calculate ratio from height.
            } else {

                // If on advanced image, convert to fixed element first.
                if (isAdvancedImage) {
                    self.toFixedElement(block);
                }

                var computedWidth = imageElement.width();
                var ratio = computedWidth / height;
                setCSSPaddingTop(imageFigure, ratioPadding(ratio));
                data.ratio = ratio;

                if (isSimpleImage) {
                    data.element_ratio = ratio;
                }

                // Convert back to fluid element
                if (isAdvancedImage) {
                    self.toFluidElement(block);

                    var strategy = data.strategy;
                    if (/fit|fill/.test(strategy)) {
                        self.resizeToViewport(block, strategy);
                    }
                }
            }

            data.height = "100%";
        }

        if (self.isFixedImage(block)) {

            // If ratio is locked, calculate width from height
            // add let setImageWidth handle the rest.
            if (ratioLock) {
                var ratioToUse = isSimpleImage ? naturalRatio : ratio;
                var width = height * ratioDecimal(ratioToUse);
                self.setImageWidth(block, width);

            // If ratio is unlocked, set height directly.
            } else {
                setCSSHeight(imageFigure, height);
                setCSSHeight(imageElement, height);
                data.height = height;
                data.element_width = parseFloat(height);
            }
        }
    },

    updateImageHeight: function(block, height) {

        self.setImageHeight(block, height);
        self.populateImageSize(block);
    },

    isFluidImage: function(block) {

        return self.imageContainer.inside(block).hasClass(isFluid);
    },

    isFixedImage: function(block) {

        return !self.isFluidImage(block);
    },

    toFixedImage: function(block) {

        var data = blocks.data(block);
        var imageElement = self.imageElement.inside(block);

        // Get width & height
        var width = imageElement.width();
        var height = imageElement.height();

        // If ratio is locked, setting image width will
        // automatically set the correct image height.
        self.setImageWidth(block, width);

        // If ratio is unlocked, explicitly set image height.
        var ratioLock = data.ratio_lock;
        if (!ratioLock) {
            self.setImageHeight(block, height);
        }
    },

    toFluidImage: function(block) {

        var data = blocks.data(block);
        var imageContainer = self.imageContainer.inside(block);
        var imageElement = self.imageElement.inside(block);

        // Get width & height
        if (blocks.isRootBlock(block)) {
            var containerWidth = imageContainer.width();
            var blockWidth = block.width();
            var width = Math.round(containerWidth / blockWidth * 100) + "%";
        }

        if (blocks.isNestedBlock(block)) {
            // var width = blocks.dimensions.getFluidWidth(block);
            // Gallery block have to 100% full width no matter is root or nested. #2793
            var width = "100%";
        }

        var height = imageElement.height();

        // If ratio is locked, setting image width will
        // automatically set the correct image height.
        self.setImageWidth(block, width);

        // If ratio is unlocked, explicitly set image height.
        var ratioLock = data.ratio_lock;
        if (!ratioLock) {
            self.setImageHeight(block, height);
        }
    },

    getImageSizeField: function(prop) {

        var field = self["imageSizeFieldImage" + $.capitalize($.camelize(prop))]();
        return field;
    },

    getImageSizeProp: function(elem) {

        var numslider = elem.closest(self.numslider.selector);
        var prop = getCssProp(numslider.data("name"));
        return prop;
    },

    getImageSizeUnit: function(prop) {

        var field = self.getImageSizeField(prop);
        return $.trim(self.numsliderCurrentUnit.under(field).text());
    },

    handleNumsliderWidget: function(numsliderWidget, val) {

        // Get prop & val to update
        var prop = self.getImageSizeProp(numsliderWidget);
        var unit = self.getImageSizeUnit(prop);
        var val = Math.round(val) + unit;

        // Declare that we are resizing the slider of this property
        self.resizingFromSlider = prop;

        self.updateImageSize(currentBlock, prop, val);

        self.resizingFromSlider = null;
    },

    "{numsliderWidget} nouislide": function(numsliderWidget, event, val) {

        self.handleNumsliderWidget(numsliderWidget, val);
    },

    "{numsliderWidget} set": function(numsliderWidget, event, val) {

        self.handleNumsliderWidget(numsliderWidget, val);
    },

    "{numsliderInput} input": function(numsliderInput) {

        // Destroy any blur event handler
        numsliderInput.off("blur.numslider");

        function revertOnBlur(originalValue) {
            numsliderInput
                .on("blur.numslider", function(){
                    numsliderInput.val(originalValue);
                });
        }

        // Get image size, prop, val
        var imageSize = self.getImageSize(currentBlock);
        var prop = self.getImageSizeProperty(numsliderInput);
        var val = numsliderInput.val();

        // If value is invalid, don't do anything.
        if (!$.isNumeric(val)) {
            // Revert to original value when input is blurred.
            return revertOnBlur(imageSize[$.camelize(prop)]);
        }

        // Round value
        val = Math.round(val);

        // Update image size
        self.updateImageSize(currentBlock, prop, val);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var unit = numsliderUnit.data("unit");

        if (unit=="px") {
            self.toFixedImage(currentBlock);
        }

        if (unit=="%") {
            self.toFluidImage(currentBlock);
        }

        self.populateImageSize(currentBlock);
    },

    "{self} composerBlockResizeStart": function(base, event, block, ui) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        var imageFigure = self.imageFigure.inside(block);

        var initialImageSize = {
            width: imageFigure.width(),
            height: imageFigure.outerHeight(),
            fluid: self.isFluidImage(block)
        };

        block.data("initialImageSize", initialImageSize);
    },

    "{self} composerBlockBeforeResize": function(base, event, block, ui) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        // Stop resizable from resizing block because
        // we want to resize the block ourselves.
        event.preventDefault();

        // Get image size, original block size and current block size.
        var imageSize = block.data("initialImageSize");
        var originalBlockSize = ui.originalSize;
        var currentBlockSize = ui.size;

        // Calculate width/height difference
        var dx = currentBlockSize.width  - originalBlockSize.width;
        var dy = currentBlockSize.height - originalBlockSize.height;

        function resizeImageWidth() {
            var newImageWidth = imageSize.width + dx;
            self.setImageWidth(block, newImageWidth);
        }

        function resizeImageHeight() {
            var newImageHeight = imageSize.height + dy;
            self.setImageHeight(block, newImageHeight);
        }

        // If image ratio is locked, resize either image width or height.
        var data = blocks.data(block);
        var ratioLock = data.ratio_lock;

        if (ratioLock) {
            dx==0 ? resizeImageHeight() : resizeImageWidth();

        // If image ratio is unlocked, resize both.
        } else {
            dx!==0 && resizeImageWidth();
            dy!==0 && resizeImageHeight();
        }

        // If this is a fluid image
        if (imageSize.fluid) {
            self.toFluidImage(block);
        }

        // Populate image size
        self.populateImageSize(block);
    },

    "{self} composerBlockResizeStop": function(base, event, block, ui) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        block.removeData("initialImageSize");
    },

    "{self} composerBlockNestIn": function(base, event, block) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        self.resetImageSize(block);
        self.populateImageSize(block);
    },

    "{self} composerBlockNestOut": function(base, event, block) {

        if (!isImageBlock(block) || !self.hasImage(block)) return;

        self.resetImageSize(block);
        self.populateImageSize(block);
    },

    //
    // Image Size > Ratio
    //

    populateImageRatio: function(block) {

        // Get ratio from data
        var data = blocks.data(block);
        var ratio = data.ratio;
        var ratioLock = data.ratio_lock;

        // Update ratio label
        self.ratioLabel()
            .html(ratio);

        // Set lock state on ratio button
        self.ratioButton()
            .toggleClass("ratio-unlocked", !ratioLock);

        // Get original ratio
        var originalRatio = ".ar-original";
        var naturalRatio = data.natural_ratio;
        var naturalRatioPadding = ratioPadding(naturalRatio);

        // Update original ratio selection
        self.ratioSelection(originalRatio)
            .attr("data-value", naturalRatio);

        self.ratioPreview(originalRatio)
            .find("> div")
            .css("padding-top", naturalRatioPadding)
            .find("span")
            .text(naturalRatio);

        // Hide select/custom ratio view when repopulating image size fields.
        self.imageSizeFieldset()
            .removeClass(customRatioView)
            .removeClass(selectRatioView);
    },

    lockImageRatio: function(block) {

        var data = blocks.data(block);
        data.ratio_lock = true;

        // Readjust image dimension
        self.normalizeImageSize(block);
    },

    unlockImageRatio: function(block) {

        var data = blocks.data(block);
        data.ratio_lock = false;
    },

    setImageRatio: function(block, ratio) {

        // Set new ratio onto block data
        var data = blocks.data(block);
        data.ratio = ratio;

        // Set ratio lock state
        var lockRatio = data.ratio_lock = ratio!==0;

        // Don't do anything when ratio is unlocked
        if (!lockRatio) return;

        var isSimpleImage = data.mode=="simple";
        var isAdvancedImage = data.mode=="advanced";

        if (isSimpleImage) {
            data.element_ratio = ratio;

            // Readjust image viewport & element
            self.normalizeImageSize(block);
        }

        if (isAdvancedImage) {

            var strategy = data.strategy;

            if (fitOrFill.test(strategy)) {

                // Readjust image viewport
                self.normalizeImageSize(block);

                // Readjust image element
                self.resizeToViewport(block, strategy);

            } else {

                // TODO: This will be different when we allow px unit in advanced mode.

                // Convert to fixed element
                self.toFixedElement(block);

                // Resize image dimension
                self.normalizeImageSize(block);

                // Convert to fluid element
                self.toFluidElement(block);
            }
        }
    },

    updateImageRatio: function(block, ratio) {

        self.setImageRatio(block, ratio);
        self.populateImageSize(block);
    },

    "{ratioLock} change": function(ratioLock) {

        if (ratioLock.is(":checked")) {
            self.lockImageRatio(currentBlock);
        } else {
            self.unlockImageRatio(currentBlock);
        }
    },

    "{ratioButton} click": function(ratioButton) {

        // Show ratio selection field
        self.imageSizeFieldset()
            .switchClass(selectRatioView);
    },

    "{ratioCustomizeButton} click": function(ratioCustomizeButton) {

        // Show custom ratio field
        self.imageSizeFieldset()
            .switchClass(customRatioView);
    },

    "{ratioCancelButton} click": function(ratioCancelButton) {

        // Hide ratio selection field
        self.imageSizeFieldset()
            .removeClass(selectRatioView);
    },

    "{ratioCancelCustomButton} click": function(ratioCancelCustomButton) {

        // Show ratio selection field
        self.imageSizeFieldset()
            .switchClass(selectRatioView);
    },

    "{ratioOkCustomButton} click": function(ratioOkCustomButton) {

        // Hide custom ratio field
        self.imageSizeFieldset()
            .removeClass(customRatioView);
    },

    "{ratioUseCustomButton} click": function(ratioUseCustomButton) {

        var ratioInput = self.ratioInput();
        var ratio = sanitizeRatio(ratioInput.val());

        // If ratio is invalid, do nothing.
        if (ratio==0) return;

        // Update video ratio
        self.updateImageRatio(currentBlock, ratio);

        // Deactivate all ratio selection
        self.ratioSelection()
            .removeClass("active");

        // Hide custom ratio field
        self.imageSizeFieldset()
            .removeClass(customRatioView);
    },

    "{ratioSelection} click": function(ratioSelection) {

        self.ratioSelection()
            .removeClass("active");

        ratioSelection.addClass("active");

        self.imageSizeFieldset()
            .removeClass(selectRatioView);

        var ratio = ratioSelection.data("value");

        self.updateImageRatio(currentBlock, ratio);
    },

    //
    // Image Size > Alignment
    //
    setImageAlignment: function(block, alignment) {

        var imageContainer = self.imageContainer.inside(block);

        if (/left|right/.test(alignment)) {
            imageContainer.css("float", alignment);
            block.css("text-align", "");
        }

        if (/center/.test(alignment)) {
            imageContainer.css("float", "");
            block.css("text-align", "center");
        }

        var data = blocks.data(block);
        data.alignment = alignment;
    },

    updateImageAlignment: function(block, alignment) {

        self.setImageAlignment(block, alignment);
        self.populateImageAlignment(block);
    },

    populateImageAlignment: function(block) {

        if (blocks.isNestedBlock(block)) {
            self.imageSizeFieldset()
                .addClass("no-alignment");
            return;
        }

        // Show alignment field
        self.imageSizeFieldset()
            .removeClass("no-alignment");

        // Set alignment
        var data = blocks.data(block);
        var alignment = data.alignment;
        self.alignmentSelection()
            .val(alignment);
    },

    "{alignmentSelection} change": function(alignmentSelection) {

        var alignment = alignmentSelection.val();
        self.updateImageAlignment(currentBlock, alignment);
    },

    //
    // Image Size > Advanced
    //

    populateImageAdvancedFields: function(block) {

        var data = blocks.data(block);
        if (data.mode!=="advanced") return;

        self.populateImageResizeStrategy(block);
        self.populateImageResizeMap(block);
        self.populateImageResizeFields(block);
    },

    //
    // Image Size > Advanced > Image Strategy
    //

    populateImageResizeStrategy: function(block) {

        var data = blocks.data(block);
        var strategy = data.strategy;

        self.strategyMenuItem()
            .where("strategy", strategy)
            .activateClass("active");

        self.strategyMenuContent()
            .where("strategy", strategy)
            .activateClass("active");
    },

    setImageResizeStrategy: function(block, strategy) {

        var data = blocks.data(block);
        data.strategy = strategy;

        if (fitOrFill.test(strategy)) {
            self.resizeToViewport(block, strategy);
        }
    },

    updateImageResizeStrategy: function(block, strategy) {

        self.setImageResizeStrategy(block, strategy);
        self.populateImageAdvancedFields(block);
    },

    resizeToViewport: function(block, strategy) {

        // Get image natural width & height
        var data = blocks.data(block);
        var imageNaturalWidth = data.natural_width;
        var imageNaturalHeight = data.natural_height;

        // Get image viewport width & height
        var imageViewport = self.imageViewport.inside(block);
        var imageViewportWidth = imageViewport.width();
        var imageViewportHeight = imageViewport.height();

        // Calculate final image size
        var imageElementSize =
            (strategy=="fit" ? resizeToFit : resizeToFill)(
                imageNaturalWidth,
                imageNaturalHeight,
                imageViewportWidth,
                imageViewportHeight
            );

        var elementWidth = data.element_width =
            decimalToPercent(imageElementSize.width / imageViewportWidth);

        var elementHeight = data.element_height =
            decimalToPercent(imageElementSize.height / imageViewportHeight);

        var elementTop = data.element_top =
            decimalToPercent(imageElementSize.top / imageViewportHeight);

        var elementLeft = data.element_left =
            decimalToPercent(imageElementSize.left / imageViewportWidth);

        // Convert image size to percentage values and set it on image element
        var imageElement = self.imageElement.inside(block);
        imageElement
            .css({
                width: elementWidth,
                height: elementHeight,
                top: elementTop,
                left: elementLeft
            });
    },

    resizeToFillViewport: function(block) {
        self.setImageResizeStrategy(block, "fill");
    },

    resizeToFitViewport: function(block) {
        self.setImageResizeStrategy(block, "fit");
    },

    "{strategyMenuItem} click": function(strategyMenuItem) {

        var strategy = strategyMenuItem.data("strategy");
        self.updateImageResizeStrategy(currentBlock, strategy);
    },

    //
    // Image Size > Advanced > Image Map
    //

    populateImageResizeMap: function(block) {

        var data = blocks.data(block);
        if (data.strategy!=="custom") return;

        // Get map figure width & height
        var mapFigure = self.mapFigure();
        var mapFigureWidth = mapFigure.width();
        var mapFigureHeight = mapFigure.height();

        // Resize map viewport to fit map figure
        // following the ratio of image viewport
        var imageViewport = self.imageViewport();
        var imageViewportWidth = imageViewport.width();
        var imageViewportHeight = imageViewport.height();

        var mapViewport = self.mapViewport();
        var mapViewportSize = resizeToFit(
                imageViewportWidth,
                imageViewportHeight,
                mapFigureWidth,
                mapFigureHeight
            );

        mapViewport.css(mapViewportSize);

        // Resize map preview according to image element
        var mapPreview = self.mapPreview();
        var resizeDirections = ["n", "s", "w", "e", "sw", "se", "nw", "ne"];
        var resizeHandleElements = self.createResizeHandleElements(resizeDirections);
        var resizeHandleSelectors = self.createResizeHandleSelectors(resizeDirections);

        // Destroy resizable
        if (mapPreview.hasClass("ui-resizable")) {
            mapPreview.resizable("destroy");
        }

        // Destroy draggable
        if (mapPreview.hasClass("ui-draggable")) {
            mapPreview.draggable("destroy");
        }

        mapPreview
            .css({
                backgroundImage: $.cssUrl(data.url), // TODO: Use smaller variation to reduce memory usage
                width: data.element_width,
                height: data.element_height,
                top: data.element_top,
                left: data.element_left
            })
            .empty()
            .append(resizeHandleElements)
            .resizable({
                handles: resizeHandleSelectors,
                aspectRatio: data.element_ratio_lock
            })
            .draggable();
    },

    createResizeHandleSelectors: function(directions) {

        var selectors = [];

        $.each(directions, function(i, direction){
            selectors[direction] = "> .ui-resizable-" + direction;
        });

        return selectors;
    },

    createResizeHandleElements: function(directions) {

        var elements = [];

        $.each(directions, function(i, direction){
            var element = $('<div class="ui-resizable-handle ui-resizable-' + direction + '"><div></div></div>')[0];
            elements.push(element);
        });

        return elements;
    },

    getElementSizeFromMap: function() {

        var mapViewport = self.mapViewport();
        var mapViewportWidth = mapViewport.width();
        var mapViewportHeight = mapViewport.height();

        var mapPreview = self.mapPreview();
        var mapPreviewPosition = mapPreview.position();
        var mapPreviewTop = mapPreviewPosition.top;
        var mapPreviewLeft = mapPreviewPosition.left;
        var mapPreviewWidth = mapPreview.width();
        var mapPreviewHeight = mapPreview.height();

        var elementTop = decimalToPercent(mapPreviewTop / mapViewportHeight);
        var elementLeft = decimalToPercent(mapPreviewLeft / mapViewportWidth);
        var elementWidth = decimalToPercent(mapPreviewWidth / mapViewportWidth);
        var elementHeight = decimalToPercent(mapPreviewHeight / mapViewportHeight);

        return {
            top: elementTop,
            left: elementLeft,
            width: elementWidth,
            height: elementHeight
        }
    },

    setElementSizeFromMap: function(block) {

        var data = blocks.data(block);

        // Get element size from map
        var elementSize = self.getElementSizeFromMap();

        // Set new size on image element
        var imageElement = self.imageElement.inside(block);
        imageElement.css(elementSize);

        // Set new size on block data
        self.setElementSizeOnBlockData(elementSize, data);
    },

    updateElementSizeFromMap: function(block) {

        // Set element size from maps
        self.setElementSizeFromMap(block);

        // Populate image resize fields
        self.populateImageResizeFields(block);
    },

    setElementSizeOnBlockData: function(elementSize, data) {

        var props = ["top", "left", "width", "height"];
        var prop;

        while (prop = props.shift()) {
            data["element_" + prop] = elementSize[prop];
        }
    },

    toFixedElement: function(block) {

        var imageElement = self.imageElement.inside(block);
        var imagePosition = imageElement.position();

        // TODO: Set to block data when we are allowed to switch to use px
        // Right now this is just an internal method.
        var elementTop = imagePosition.top;
        var elementLeft = imagePosition.left;
        var elementWidth = imageElement.width();
        var elementHeight = imageElement.height();

        imageElement.css({
            width: elementWidth,
            height: elementHeight,
            top: elementTop,
            left: elementLeft
        });
    },

    toFluidElement: function(block) {

        var data = blocks.data(block);

        // Image element
        var imageElement = self.imageElement.inside(block);
        var imagePosition = imageElement.position();

        var computedTop = imagePosition.top;
        var computedLeft = imagePosition.left;
        var computedWidth = imageElement.width();
        var computedHeight = imageElement.height();

        // Image viewport
        var imageViewport = self.imageViewport.inside(block);
        var viewportWidth = imageViewport.width();
        var viewportHeight = imageViewport.height();

        var elementTop = data.element_top = decimalToPercent(computedTop / viewportHeight);
        var elementLeft = data.element_left = decimalToPercent(computedLeft / viewportWidth);
        var elementWidth = data.element_width = decimalToPercent(computedWidth / viewportWidth);
        var elementHeight = data.element_height = decimalToPercent(computedHeight / viewportHeight);

        imageElement.css({
            top: elementTop,
            left: elementLeft,
            width: elementWidth,
            height: elementHeight
        });
    },

    "{mapPreview} resize": function() {
        self.updateElementSizeFromMap(currentBlock);
    },

    "{mapPreview} drag": function() {
        self.updateElementSizeFromMap(currentBlock);
    },

    //
    // Image Size > Advanced > Image Resize Fields
    //

    populateImageResizeFields: function(block) {

        var data = blocks.data(block);

        var resizeInputFields = self.resizeInputField();
        var props = ["width", "height", "top", "left"];
        var prop;

        while (prop = props.shift()) {

            // If we're resizing from input, skip this.
            if (self.resizingFromInput==prop) return;

            resizeInputFields.where("prop", prop)
                .val(data["element_" + prop]);
        }

        self.resizeRatioLock()
            .prop("checked", data.element_ratio_lock);
    },

    setElementSize: function(block, prop, val) {

        return self["setElement" + $.capitalize(prop)](block, val);
    },

    updateElementSize: function(block, prop, val) {

        self.setElementSize(block, prop, val);
        self.populateImageAdvancedFields(block);
    },

    setElementWidth: function(block, width) {

        var data = blocks.data(block);

        // Get num & unit
        var num = parseFloat(width);
        var unit = parseUnit(width);

        // Get ratio & ratioLock
        var ratio = data.element_ratio;
        var ratioLock = data.element_ratio_lock;

        // Get image viewport & element
        var imageViewport = self.imageViewport.inside(block);
        var imageElement = self.imageElement.inside(block);

        if (unit=="%") {

            // If ratio is locked,
            // set element width and
            // adjust and set element height.
            if (ratioLock) {

                // Get original element width in both px & %
                var originalElementWidth = data.element_width;
                var originalComputedElementWidth = imageElement.width();

                // Get new width in both px & %
                var elementWidth = width;
                var computedElementWidth = originalComputedElementWidth * (parseFloat(elementWidth) / parseFloat(originalElementWidth));

                // Calculate new height in px
                var computedElementHeight = computedElementWidth / ratio;

                // Calculate new height in %
                var imageViewportHeight = imageViewport.height();
                var elementHeight = decimalToPercent(computedElementHeight / imageViewportHeight);

                // Assign element width & height to data
                data.element_width = elementWidth;
                data.element_height = elementHeight;

                // Set to image element
                setCSSWidth(imageElement, elementWidth);
                setCSSHeight(imageElement, elementHeight);

            // If ratio is unlocked,
            // set element width only.
            } else {

                var elementWidth = width;
                data.element_width = parseFloat(elementWidth);

                // Set to image element
                setCSSWidth(imageElement, elementWidth);
            }
        }

        // TODO: Pixel unit
    },

    setElementHeight: function(block, height) {

        var data = blocks.data(block);

        // Get num & unit
        var num = parseFloat(height);
        var unit = parseUnit(height);

        // Get ratio & ratioLock
        var ratio = data.element_ratio;
        var ratioLock = data.element_ratio_lock;

        // Get image viewport & element
        var imageViewport = self.imageViewport.inside(block);
        var imageElement = self.imageElement.inside(block);

        if (unit=="%") {

            // If ratio is locked,
            // set element height and
            // adjust and set element height.
            if (ratioLock) {

                // Get original element height in both px & %
                var originalElementHeight = data.element_height;
                var originalComputedElementHeight = imageElement.height();

                // Get new height in both px & %
                var elementHeight = height;
                var computedElementHeight = originalComputedElementHeight * (parseFloat(elementHeight) / parseFloat(originalElementHeight));

                // Calculate new width in px
                var computedElementWidth = computedElementHeight * ratio;

                // Calculate new width in %
                var imageViewportWidth = imageViewport.width()
                var elementWidth = decimalToPercent(computedElementWidth / imageViewportWidth);

                // Assign element width & height to data
                data.element_width = elementWidth;
                data.element_height = elementHeight;

                // Set to image element
                setCSSWidth(imageElement, elementWidth);
                setCSSHeight(imageElement, elementHeight);

            // If ratio is unlocked,
            // set element width only.
            } else {

                var elementHeight = height;
                data.element_height = elementHeight;

                // Set to image element
                setCSSHeight(imageElement, elementHeight);
            }
        }
    },

    setElementTop: function(block, top) {

        var data = blocks.data(block);
        data.element_top = top;

        var imageElement = self.imageElement.inside(block);
        setCSSTop(imageElement, top);
    },

    setElementLeft: function(block, left) {

        var data = blocks.data(block);
        data.element_left = left;

        var imageElement = self.imageElement.inside(block);
        setCSSLeft(imageElement, left);
    },

    setElementRatio: function(block, ratio) {

        var data = blocks.data(block);
        data.element_ratio = ratio;
        self.setElementWidth(block, data.element_width);
    },

    updateElementRatio: function(block, ratio) {

        self.setElementRatio(block, ratio);
        self.populateImageAdvancedFields(block);
    },

    resetElementSize: function(block) {

        self.setImageResizeStrategy(block, "fit");
        self.setImageResizeStrategy(block, "custom");

        self.populateImageAdvancedFields(block);
    },

    lockElementRatio: function(block) {

        var data = blocks.data(block);
        data.element_ratio_lock = true;

        var naturalRatio = data.natural_ratio;
        self.updateElementRatio(block, naturalRatio);
    },

    unlockElementRatio: function(block) {

        var data = blocks.data(block);
        data.element_ratio_lock = false;

        self.populateImageAdvancedFields(block);
    },

    "{resizeInputField} input": function(resizeInputField, event) {

        var prop = resizeInputField.data("prop");
        var val = resizeInputField.val();
        var num = $.trim(val.replace(/\%/gi, ""));

        // If value is invalid, don't do anything
        if (!$.isNumeric(num)) return;

        // Restore % on num
        val = parseFloat(num) + "%";

        // Update element size
        self.resizingFromInput = prop;
        self.updateElementSize(currentBlock, prop, val);
        self.resizingFromInput = null;
    },

    "{resizeInputField} focus": function(resizeInputField) {

        // Select all when focus on input field
        resizeInputField.select();
    },

    "{resizeInputField} blur": function(resizeInputField) {

        // Restore to original or finalized value when input field is blurred
        var data = blocks.data(currentBlock);
        var prop = resizeInputField.data("prop");
        var val = data["element_" + prop];

        resizeInputField.val(val);
    },

    "{resizeResetButton} click": function() {

        self.resetElementSize(currentBlock);
    },

    "{resizeRatioLock} click": function(resizeRatioLock) {

        var ratioLock = resizeRatioLock.is(":checked");

        if (ratioLock) {
            self.lockElementRatio(currentBlock);
        } else {
            self.unlockElementRatio(currentBlock);
        }
    },

    //
    // Image Caption
    //
    populateImageCaption: function(block) {

        var data = blocks.data(block);

        // Show or hide image caption fieldset
        var captionEnabled = data.caption_enabled;
        panelsFieldset.toggle("image-caption", captionEnabled)

        // Check or uncheck image caption fieldset
        var hasImageCaption = self.imageCaption.inside(block).length;
        panelsFieldset.enable("image-caption", hasImageCaption);

        // Set image caption text on text field only if this block has a caption text.
        var captionText = data.caption_text;

        if (captionText) {
            self.captionTextField()
                .val(captionText);
        }
    },

    getImageCaption: function(block) {

        var data = blocks.data(block);
        return data.caption_text;
    },

    setImageCaption: function(block, captionText) {

        var imageCaption = self.imageCaption.inside(block);

        // If image caption does not exist
        if (!imageCaption.length) {

            // Create caption
            var imageCaption = $(meta.imageCaption);

            // Get image container
            var imageContainer = self.imageContainer.inside(block);

            // Append caption to contianer
            imageContainer
                .append(imageCaption);
        }

        var imageCaptionText = self.imageCaptionText.inside(block);
        imageCaptionText.text(captionText);

        // Store to block data
        var data = blocks.data(block);
        data.caption_text = captionText;
    },

    removeImageCaption: function(block) {

        var imageCaption = self.imageCaption.inside(block);

        // Remove image caption
        imageCaption.remove();
    },

    enableImageCaption: function(block) {

        // Set caption text
        var data = blocks.data(block);

        // If there's no caption text, get it from the fieldset
        if (!data.caption_text) {
            data.caption_text = self.captionTextField().val();
        }

        self.setImageCaption(block, data.caption_text);

        // Populate caption field
        self.populateImageCaption(block);
    },

    disableImageCaption: function(block) {

        // Remove image caption
        self.removeImageCaption(block);

        // Populate caption field
        self.populateImageCaption(block);
    },

    "{captionToggle} change": function(captionToggle, event) {

        if (captionToggle.is(":checked")) {
            self.enableImageCaption(currentBlock);
        } else {
            self.disableImageCaption(currentBlock);
        }
    },

    "{captionTextField} input": function(captionTextField, event) {

        var captionText = captionTextField.val();

        self.setImageCaption(currentBlock, captionText);
    },

    //
    // Image Style
    //
    populateImageStyle: function(block) {

        var data = blocks.data(block);

        // Show or hide image style fieldset
        var styleEnabled = data.style_enabled;
        panelsFieldset.toggle("image-style", styleEnabled);

        // Active caption selection
        self.styleSelection()
            .where("value", data.style)
            .activateClass("active");
    },

    setImageStyle: function(block, style) {

        var imageContainer = self.imageContainer.inside(block);
        imageContainer.switchClass("style-" + style);

        var data = blocks.data(block);
        data.style = style;
    },

    updateImageStyle: function(block, style) {

        self.setImageStyle(block, style);
        self.populateImageStyle(block);
    },

    "{styleSelection} click": function(styleSelection, event) {

        var style = styleSelection.data("value");
        self.updateImageStyle(currentBlock, style);
    },

    //
    // Image Link
    //
    populateImageLink: function(block) {

        var data = blocks.data(block);

        // Show or hide image link fieldset
        var linkEnabled = data.link_enabled;
        panelsFieldset.toggle("image-link", linkEnabled);

        // Set link field values
        self.linkUrlField()
            .val(data.link_url);

        self.linkTitleField()
            .val(data.link_title);

        self.linkBlankOption()
            .prop("checked", data.link_target=="_blank");

        // Chekc or uncheck image link fieldset
        var hasLink = data.link_url!=='';
        panelsFieldset.enable("image-link", hasLink);
    },

    setImageLinkUrl: function(block, url) {

        var data = blocks.data(block);
        data.link_url = url = $.trim(url);

        var imageViewport = self.imageViewport.inside(block);

        if (url) {
            imageViewport.attr("href", url);
        } else {
            imageViewport.removeAttr("href");
        }
    },

    setImageLinkTitle: function(block, title) {

        var data = blocks.data(block);
        data.link_title = title;

        self.imageViewport.inside(block)
            .attr("title", data.link_title);
    },

    setImageLinkTarget: function(block, target) {

        var data = blocks.data(block);
        data.link_target = target;

        var imageViewport = self.imageViewport.inside(block);
        if (target) {
            imageViewport.attr("target", target);
        } else {
            imageViewport.removeAttr("target");
        }
    },

    removeImageLink: function(block) {

        var data = blocks.data(block);
        data.link_url = '';
        data.link_title = '';
        data.link_target = '';

        var imageViewport = self.imageViewport.inside(block);
        imageViewport
            .removeAttr("href")
            .removeAttr("title")
            .removeAttr("target");

        self.populateImageLink(block);
    },

    "{linkUrlField} input": function(linkUrlField) {

        var url = linkUrlField.val();
        self.setImageLinkUrl(currentBlock, url);
    },

    "{linkTitleField} input": function(linkTitleField) {

        var title = linkTitleField.val();
        self.setImageLinkTitle(currentBlock, title);
    },

    "{linkBlankOption} change": function(linkBlankOption) {

        var checked = linkBlankOption.is(":checked");
        var target = checked ? "_blank" : "";
        self.setImageLinkTarget(currentBlock, target);
    },

    "{linkToggle} change": function(linkToggle) {

        if (!linkToggle.is(":checked")) {
            self.removeImageLink(currentBlock);
        }
    },

    "{imageViewport} click": function(imageViewport, event) {

        event.preventDefault();
    },

    //
    // Image Popup
    //
    setImagePopup: function(block, popupUri, popupVariationKey) {

        var data = blocks.data(block);

        // Remove existing popup button before adding new one
        self.imagePopupButton
            .inside(block)
            .remove();

        // Add image popup button
        // var imageFigure = self.imageFigure.inside(block);
        // var imagePopupButton = $(meta.imagePopupButton);
        // imageFigure.append(imagePopupButton);

        // If popup image source is not set yet, use original image source.
        var popupUri = data.popup_uri =
            popupUri || data.popup_uri || data.uri;

        var popupVariationKey = data.popup_variation =
            popupVariationKey || data.popup_variation;

        // Get media
        var task =
            mediaManager.getMedia(popupUri)
                .done(function(media){

                    // Get media meta
                    var mediaMeta = media.meta;

                    // Set variation and url
                    var variation = mediaManager.getVariation(popupUri, [popupVariationKey, "system/large", "system/original"]);

                    // Just in case it was fallback variation, set variation values again.
                    data.popup_url = variation.url;
                    data.popup_variation = variation.key;

                    // Set url to variation on image element
                    self.imagePopupButton.inside(block)
                        .attr("href", data.popup_url);
                });

        return task;
    },

    unsetImagePopup: function(block) {

        var data = blocks.data(block);
        data.popup_url = "";
        data.popup_uri = "";
        data.popup_variation = "";

        self.imagePopupButton.inside(block)
            .remove();
    },

    updateImagePopup: function(block, popupUri, popupVariationKey) {

        self.setImagePopup(block, popupUri, popupVariationKey)
            .always(function(){
                self.populateImagePopup(block);
            });
    },

    removeImagePopup: function(block) {
        self.unsetImagePopup(block);
        self.populateImagePopup(block);
    },

    populateImagePopup: function(block) {

        var data = blocks.data(block);

        self.popupFieldset().removeClass('hidden');

        if (data.isurl) {
            self.popupFieldset().addClass('hidden');
            return;
        }


        var popupUri = data.popup_uri;
        var popupVariationKey = data.popup_variation;

        // Show or hide image popup fieldset
        var popupEnabled = data.popup_enabled;
        panelsFieldset.toggle("image-popup", popupEnabled);

        // Check or uncheck image popup fieldset
        var hasPopup = !!popupUri;
        panelsFieldset.enable("image-popup", hasPopup);

        // If image uri & popup uri are two different source,
        // show different hint.
        var sourceUri = data.uri;
        var popupFieldset = self.popupFieldset();
        popupFieldset.toggleClass(isDifferent, hasPopup && (popupUri !== sourceUri));

        // If there is a popup, populate image popup fieldset.
        if (!hasPopup) return;

        // Get popup fieldset
        popupFieldset.addClass(isLoading);

        // Get media
        mediaManager.getMedia(popupUri)
            .done(function(media){

                // Get media meta
                var mediaMeta = media.meta;

                // Popup title
                self.popupTitle()
                    .text(mediaMeta.title);

                // Popup thumbnail image
                var popupThumbnailImage = self.getSourceThumbnailImage(mediaMeta.thumbnail);
                self.popupThumbnail()
                    .empty()
                    .append(popupThumbnailImage);

                // Popup variation list
                var popupVariationList = $(media.variations);
                self.popupVariationListContainer()
                    .empty()
                    .append(popupVariationList);

                // Popup variation
                var popupVariation = mediaManager.getVariation(popupUri, [popupVariationKey, "system/large", "system/original"]);

                // Popup variatian item
                var popupVariationItem =
                    self.popupVariationItem()
                        .where("key", '"' + popupVariation.key + '"')
                        .activateClass("active");

                // Popup size
                self.popupSize()
                    .text($.plupload2.formatSize(popupVariation.size));

                // Popup url
                self.popupUrl()
                    .text(popupVariation.url);
            })
            .fail(function(){
                popupFieldset.addClass(isFailed);
            })
            .always(function(){
                popupFieldset.removeClass(isLoading);
            });
    },

    "{popupToggle} change": function(popupToggle) {
        var checked = popupToggle.is(":checked");

        if (checked) {
            self.updateImagePopup(currentBlock);
        } else {
            self.removeImagePopup(currentBlock);
        }
    },

    "{popupVariationItem} click": function(popupVariationItem) {

        // Prevent user from selecting missing variation
        if (popupVariationItem.hasClass("is-missing")) return;

        // Set variation key
        var popupVariationKey = popupVariationItem.data("key");
        self.updateImagePopup(currentBlock, null, popupVariationKey);
    },


    "{imageUrlAdd} click" : function(el, event) {
        // "[data-eb-image-{url-form|url-textbox|url-add|url-cancel}]",
        //
        var imageUrl = self.imageUrlTextbox().val();

        if (!imageUrl) {
            return;
        }


        var blockContent = blocks.getBlockContent(currentBlock);
        var data = blocks.data(currentBlock);

        // Always center align block
        currentBlock.css("text-align", "center");

        // Add loading indicator
        // block.addClass("is-loading");

        data.uri = imageUrl;
        data.url = imageUrl;
        data.simple = 'simple';
        data.ratio_lock = true;
        data.isurl = true;

        var imageContainer = self.constructImage(data);

        // Append image container to block content
        // blockContent.html(imageContainer);

        blocks.getBlockContent(currentBlock)
            .empty()
            .append(imageContainer);

        // If this block is still active, populate image block
        if (currentBlock.hasClass("active")) {
            self.populate(currentBlock);
        }
    },

    "{urlFieldUpdateButton} click": function(el, event) {
        var newUrl = self.urlFieldText().val();

        if (newUrl) {
            var data = blocks.data(currentBlock);

            // update data url
            data.uri = newUrl;
            data.url = newUrl;

            // Construct image container
            var imageContainer = self.constructImage(data);

            // Append image container to block content
            blocks.getBlockContent(currentBlock)
                .empty()
                .append(imageContainer);
        }
    }



}});

module.resolve();

});

});
