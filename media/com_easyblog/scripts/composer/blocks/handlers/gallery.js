EasyBlog.module("composer/blocks/handlers/gallery", function($){

var module = this;
var isEmpty = "is-empty";

var selectRatioView = "view-selectratio";
var customRatioView = "view-customratio";

var sanitizeRatio = function(ratio) {
    ratio = $.trim(ratio);
    if (/\:/.test(ratio)) {
        var parts = ratio.split(":");
        return parseInt(parts[0]) + ":" + parseInt(parts[1]);
    } else {
        return parseFloat(ratio) || 0;
    }
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


EasyBlog.Controller("Composer.Blocks.Handlers.Gallery", {
    elements: [
        "[data-eb-gallery-{items-fieldset|list|list-item-group|list-item|list-item-icon|list-item-title|list-item-primary-label|list-item-delete-button|list-item-primary-button}]",

        "[data-eb-gallery-{size-fieldset|strategy-field|strategy-menu-item}]",
        "[data-eb-gallery-{ratio-button|ratio-label|ratio-customize-button|ratio-cancel-button|ratio-use-custom-button|ratio-cancel-custom-button|ratio-selection|ratio-input}]",
    ],
    defaultOptions: {
        "{galleryUploadPlaceholder}": ".eb-gallery-upload-placeholder",
        "{galleryContainer}": ".eb-gallery",
        "{galleryViewport}": ".eb-gallery-viewport",
        "{galleryItem}": ".eb-gallery-item:not(.is-placeholder)",
        "{galleryStage}": ".eb-gallery-stage",
        "{galleryNextButton}": ".eb-gallery-next-button",
        "{galleryPrevButton}": ".eb-gallery-prev-button",
        "{galleryMenu}": ".eb-gallery-menu",
        "{galleryMenuItem}": ".eb-gallery-menu-item",
        "{galleryImage}": ".ebd-block[data-type=image]",

        "{galleryItemPlaceholder}": ".eb-gallery-item.is-placeholder",
        "{galleryMenuItemPlaceholder}": ".eb-gallery-menu-item.is-placeholder"

    }
}, function(self, opts, base, composer, blocks, meta, currentBlock, dimensions, imageBlockHandler) { return {

    init: function() {

        // Globals
        blocks = self.blocks;
        composer = blocks.composer;
        meta = opts.meta;
        currentBlock = $();
        dimensions = blocks.dimensions;
        mediaManager = EasyBlog.MediaManager;
        mediaUploader = mediaManager.uploader;
        imageBlockHandler = blocks.getBlockHandler("image");
    },

    normalize: function(data) {
        return $.extend({}, meta.data, data);
    },

    activate: function(block) {

        // Set as current block
        currentBlock = block

        // Populate fieldset
        self.populate(block);

        // Get list item group
        var listItemGroup = self.listItemGroup();

        // If ui sortable is implemented, stop.
        if (listItemGroup.hasClass("ui-sortable")) {
            return;
        }

        // drag and drop here
        listItemGroup.sortable({

            // Items
            items: self.listItem.selector,

            // Behaviour
            tolerance: "pointer",
            refreshPositions: true,

            update: function(event, ui) {

                var galleryItems = [];
                var menuGalleryItems = [];

                activeId = 0;

                self.listItem()
                    .each(function(){

                        var listItem = $(this);
                        var itemId = listItem.attr("data-id");

                        // check the class active is it exist from the Gallery items listing right panel
                        if (listItem.hasClass('active')) {
                            activeId = itemId;
                        }

                        var galleryItem = self.galleryItem().where("id", itemId);
                        galleryItems.push(galleryItem[0]);
                        menuGalleryItems.push(galleryItem[0]);
                    });

                var galleryViewport = self.galleryViewport.inside(currentBlock);
                var galleryContainer = self.galleryContainer.inside(currentBlock);

                var galleryItemPlaceholder = self.galleryItemPlaceholder.inside(currentBlock);
                var galleryMenuItemPlaceholder = self.galleryMenuItemPlaceholder.inside(currentBlock);

                $.each(galleryItems.reverse(), function(i, galleryItem){
                    galleryViewport.prepend(galleryItem);
                });

                $.each(menuGalleryItems, function(i, galleryItem){

                    var listItem = $(galleryItem);
                    var itemId = listItem.attr("data-id");

                    self.galleryMenuItem.inside(currentBlock)
                        .where('id', itemId)
                        .remove();

                    // Create gallery menu item and insert it before gallery item menu.
                    var galleryMenuItem = self.createGalleryMenuItem(itemId);

                    galleryMenuItem
                        .attr("data-id", itemId)
                        .insertBefore(galleryMenuItemPlaceholder);
                    
                    if (activeId && itemId == activeId) {
                        galleryMenuItem.addClass('active');
                    }
                });


                EasyBlog.ImageGallery.setLayout(galleryContainer);
            }
        });
    },

    deactivate: function(block) {
    },

    construct: function(data) {
    },

    reconstruct: function(block) {
        // Disable content editable
        block.editable(false);

        // Register upload placeholder
        var galleryUploadPlaceholder = self.galleryUploadPlaceholder.inside(block);
        var uploader = mediaUploader.register(galleryUploadPlaceholder);

        self.populateDataItems(block);
    },

    deconstruct: function(block) {

        var data = blocks.data(block);

        var blockContent = blocks.getBlockContent(block);

        var galleryContainer = self.galleryContainer.inside(block);

        // Remove placeholder
        self.galleryItemPlaceholder
            .inside(galleryContainer)
            .remove();

        // Remove placeholder
        self.galleryMenuItemPlaceholder
            .inside(galleryContainer)
            .remove();

        // Remove any active class on the gallery item
        var galleryItems = self.galleryItem.inside(galleryContainer);
        var primaryGalleryItem = galleryItems.where('id', data.primary);

        // Remove all active class for gallery items
        galleryItems.removeClass('active');

        // Add active class on primary gallery item
        primaryGalleryItem.addClass('active');

        self.galleryMenuItem.inside(galleryContainer)
            .removeClass("active")
            .where("id", data.primary)
            .addClass("active");

        // Set the layout
        EasyBlog.ImageGallery.setLayout(galleryContainer);

        // Get index
        var galleryItemIndex = self.getGalleryItemIndex(block, primaryGalleryItem);

        if (galleryItemIndex < 0) {
            galleryItemIndex = 0;
        }

        // Set viewport position
        var galleryViewport = self.galleryViewport.inside(galleryContainer);

        // Default to be 0 because it's always the first item
        var left = 0;

        if (galleryItemIndex != 0) {
            left = -1 * (100 * galleryItemIndex);
        }

        galleryViewport.css("left", left + "%");

        blockContent.html(galleryContainer);

        return block;
    },

    refocus: function(block) {
    },

    reset: function(block) {
    },

    populate: function(block) {
        self.populateDataItems(block);
        self.populateRatio(block);
        self.populateResizeStrategy(block);
        self.populateList(block);
    },

    // We need to repopulate the data items because when we store it, we store it on separate arrays
    populateDataItems: function(block) {

        var data = blocks.data(block);

        $.each(data.itemsKeyArray, function(i, key) {
            data.items[key] = data.itemsArray[i];
        });

        // Set the length for uploaded items to avoid script setting the first uploaded image as primary
        self.uploadedItems = data.itemsArray.length;
    },

    toData: function(block) {
        // Get the list of items
        var data = blocks.data(block);

        // When storing the data we need to store it in an array
        data.itemsArray = [];
        data.itemsKeyArray = [];

        for (var key in data.items) {
            if (data.items.hasOwnProperty(key)) {
                data.itemsArray.push(data.items[key]);
                data.itemsKeyArray.push(key);
            }
        }

        return data;
    },

    toText: function(block) {
        return;
    },

    toHTML: function(block) {

        var cloned = block.clone();
        var deconstructedBlock = self.deconstruct(cloned);
        var blockContent = blocks.getBlockContent(deconstructedBlock);

        return blockContent.html();
    },

    createGalleryItem: function() {
        return $(meta.galleryItem);
    },

    createGalleryMenuItem: function(id) {
        var menuItem = $(meta.galleryMenuItem);
        menuItem.data('id', id);

        return menuItem;
    },

    createGalleryListItem: function() {
        return $(meta.galleryListItem);
    },

    createGalleryPlaceholder: function() {
        return $(meta.galleryPlaceholder);
    },

    createGalleryImage: function(block, mediaMeta) {

        var data = blocks.data(block);

        // Get medium variation
        var uri = mediaMeta.uri;
        var galleryVariation = mediaManager.getVariation(uri, "medium");

        var naturalWidth = galleryVariation.width;
        var naturalHeight = galleryVariation.height;
        var naturalRatio = naturalWidth / naturalHeight;

        var galleryImageData = {

            uri: uri,
            url: galleryVariation.url,
            key: galleryVariation.key,

            // Size
            mode: "advanced",
            mode_lock: true,
            fluid: true,

            // Viewport width/height
            width: "100%",
            width_lock: true,
            height: "100%",
            height_lock: true,
            ratio: data.ratio,

            // Element width/height
            strategy: data.strategy,
            element_ratio: naturalRatio,

            // Natural width/height
            natural_width: naturalWidth,
            natural_height: naturalHeight,
            natural_ratio: naturalRatio,

            // Disable caption, link & style
            caption_enabled: false,
            link_enabled: false,
            style_enabled: false,
            popup_enabled: false
        };

        var galleryImage =
            blocks.constructNestedBlock("image", galleryImageData)
                .addClass("is-isolated");

        return galleryImage;
    },

    "{galleryUploadPlaceholder} mediaUploaderFilesAdded": function(galleryUploadPlaceholder, event, uploader, files) {

        var block = blocks.block.of(galleryUploadPlaceholder);

        var galleryContainer  = self.galleryContainer.inside(block);
        var galleryItemPlaceholder = self.galleryItemPlaceholder.inside(block);
        var galleryMenuItemPlaceholder = self.galleryMenuItemPlaceholder.inside(block);

        $.each(files, function(i, file) {

            // Get fileId
            var fileId = file.id;

            // Create item id
            var itemId = $.uid("g");

            // Create gallery item and assign file id
            // so we can track back this item.
            var galleryItem = self.createGalleryItem();
            galleryItem
                .attr("data-file-id", fileId)
                .attr("data-id", itemId);

            // Create gallery placeholder and append it to
            // gallery item so that we can show upload progress.
            var galleryPlaceholder = self.createGalleryPlaceholder();
            galleryItem.html(galleryPlaceholder);

            // Insert gallery item before gallery item placeholder.
            galleryItem.insertBefore(galleryItemPlaceholder);

            // Make sure gallery menu items doesn't have an active state
            self.galleryMenuItem
                .inside(block)
                .removeClass('active');

            // Create gallery menu item and insert it before gallery item menu.
            var galleryMenuItem = self.createGalleryMenuItem(itemId);

            galleryMenuItem
                .attr("data-id", itemId)
                .insertBefore(galleryMenuItemPlaceholder)
                .addClass('active');


            // Register gallery placeholder to uploader to the file's upload progress
            // will be automatically reflected on this gallery placeholder.
            mediaUploader.addItem(file, galleryPlaceholder);

            // Remove is-empty class
            galleryContainer.removeClass(isEmpty);
        });

        EasyBlog.ImageGallery.setLayout(galleryContainer);
    },

    uploadedItems: 0,

    "{galleryUploadPlaceholder} mediaUploaderFileUploaded": function(galleryUploadPlaceholder, event, uploader, file, data) {

        // Get fileId, mediaItem and mediaMeta.
        var fileId = file.id;
        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        // Get block
        var block = blocks.block.of(galleryUploadPlaceholder);
        var data = blocks.data(block);

        // Give some time for upload progress transition to complete
        // before replacing the thumb placeholder with the thumb image
        setTimeout(function(){

            // Get gallery item
            var galleryItem =
                self.galleryItem()
                    .where("fileId", fileId)
                    .removeAttr("data-file-id");

            // Get item id
            var itemId = galleryItem.attr("data-id");

            // Unregister item from uploader
            mediaUploader.removeItem(file, galleryItem);

            // Create image
            var galleryImage = self.createGalleryImage(block, mediaMeta);

            // Insert thumb image on thumb item
            galleryItem.html(galleryImage);

            // Get icon variation
            var uri = mediaMeta.uri;
            var iconVariation = mediaManager.getVariation(uri, "icon");

            // Item
            data.items[itemId] = {
                title: mediaMeta.title,
                iconUrl: iconVariation.url
            };

            // Normalize image size
            imageBlockHandler.normalizeImageSize(galleryImage);

            // If this block is current block, populate list.
            if (block.is(currentBlock)) {
                self.populateList(block);
            }

            if (self.uploadedItems == 0) {
                self.setPrimary(itemId);
            }

            self.uploadedItems += 1;

        }, 600);
    },

    //
    // Primary image for gallery
    //
    setPrimary: function(id) {

        // Get gallery item
        var galleryItem = self.galleryItem().where("id", id);
        var galleryMenuItem = self.galleryMenuItem().where("id", id);

        var block = blocks.block.of(galleryItem);

        // Get block data
        var data = blocks.data(block);

        // Set primary block
        data.primary = id;

        // Populate gallery list
        self.populateList(block);

        // Set the active menu item
        self.galleryMenuItem
            .inside(block)
            .removeClass('active');

        galleryMenuItem.addClass('active');
    },

    //
    // Ratio
    //

    setRatio: function(block, ratio) {

        var data = blocks.data(block);
        data.ratio = ratio;

        // Set gallery stage ratio
        var galleryStage = self.galleryStage.inside(block);
        galleryStage.css("padding-top", ratioPadding(ratio));

        // Set aspect ratio on gallery images
        var galleryImages = self.galleryImage.inside(block);
        galleryImages.each(function(){
            var galleryImage = $(this);
            imageBlockHandler.setImageRatio(galleryImage, ratioDecimal(ratio));
        });
    },

    updateRatio: function(block, ratio) {

        self.setRatio(block, ratio);
        self.populateRatio(block);
    },

    populateRatio: function(block) {

        // Get ratio from data
        var data = blocks.data(block);
        var ratio = data.ratio;

        // Update ratio label
        self.ratioLabel()
            .html(ratio);
    },

    "{ratioButton} click": function(ratioButton) {

        // Show ratio selection field
        self.sizeFieldset()
            .switchClass(selectRatioView);
    },

    "{ratioCustomizeButton} click": function(ratioCustomizeButton) {

        // Show custom ratio field
        self.sizeFieldset()
            .switchClass(customRatioView);
    },

    "{ratioCancelButton} click": function(ratioCancelButton) {

        // Hide ratio selection field
        self.sizeFieldset()
            .removeClass(selectRatioView);
    },

    "{ratioCancelCustomButton} click": function(ratioCancelCustomButton) {

        // Show ratio selection field
        self.sizeFieldset()
            .switchClass(selectRatioView);
    },

    "{ratioOkCustomButton} click": function(ratioOkCustomButton) {

        // Hide custom ratio field
        self.sizeFieldset()
            .removeClass(customRatioView);
    },

    "{ratioUseCustomButton} click": function(ratioUseCustomButton) {

        var ratioInput = self.ratioInput();
        var ratio = sanitizeRatio(ratioInput.val());

        // If ratio is invalid, do nothing.
        if (ratio==0) return;

        // Update video ratio
        self.updateRatio(currentBlock, ratio);

        // Deactivate all ratio selection
        self.ratioSelection()
            .removeClass("active");

        // Hide custom ratio field
        self.sizeFieldset()
            .removeClass(customRatioView);
    },

    "{ratioSelection} click": function(ratioSelection) {

        self.ratioSelection()
            .removeClass("active");

        ratioSelection.addClass("active");

        self.sizeFieldset()
            .removeClass(selectRatioView);

        var ratio = ratioSelection.data("value");

        self.updateRatio(currentBlock, ratio);
    },


    //
    // Resize Strategy
    //

    setResizeStrategy: function(block, strategy) {

        var data = blocks.data(block);
        data.strategy = strategy;

        var galleryImages = self.galleryImage.inside(block);
        galleryImages.each(function(i){
            var galleryImage = $(this);
            imageBlockHandler.resizeToViewport(galleryImage, strategy);
        });
    },

    updateResizeStrategy: function(block, strategy) {

        self.setResizeStrategy(block, strategy);
        self.populateResizeStrategy(block);
    },

    populateResizeStrategy: function(block) {

        var data = blocks.data(block);
        var strategy = data.strategy;

        self.strategyMenuItem()
            .removeClass("active")
            .where("strategy", strategy)
            .addClass("active");
    },

    "{strategyMenuItem} click": function(strategyMenuItem) {

        var strategy = strategyMenuItem.data("strategy");
        self.updateResizeStrategy(currentBlock, strategy);
    },

    //
    // List
    //

    listItems: {},

    populateList: function(block) {

        var data = blocks.data(block);
        var galleryItems = self.galleryItem.inside(block);
        var galleryListItems = [];

        galleryItems.each(function(){

            var galleryItem = $(this);
            var itemId = galleryItem.attr("data-id");
            var itemData = data.items[itemId];

            // If this is not ready, skip.
            if (!itemData) return;

            var galleryImage = self.galleryImage.inside(galleryItem);

            // If this gallery item is not ready, skip.
            if (!galleryImage.length) return;

            // Get gallery list item
            var galleryListItem = self.listItems[itemId];

            // If list item does not exist
            if (!galleryListItem) {

                // Create list item now
                galleryListItem = self.createGalleryListItem();

                // Set item id
                galleryListItem
                    .attr("data-id", itemId);

                // Set item title
                self.listItemTitle.inside(galleryListItem)
                    .text(itemData.title);

                // Set item icon
                self.listItemIcon.inside(galleryListItem)
                    .attr("src", itemData.iconUrl);

                // Cache list item
                self.listItems[itemId] = galleryListItem;
            }

            // Determines if this item is primary item
            var isPrimary = itemId == data.primary;
            
            // Toggle is-primary class is item is primary
            galleryListItem
                .toggleClass("is-primary", isPrimary)
                .toggleClass("active", galleryItem.hasClass("active"));

            // Push to array of list items
            galleryListItems.push(galleryListItem);
        });

        // Toggle empty state
        self.itemsFieldset()
            .toggleClass("is-empty", !galleryListItems.length);

        // Append list items to list item group
        self.listItemGroup()
            .empty()
            .append(galleryListItems);
    },

    "{listItem} click": function(listItem) {

        // Get item id from listi tem
        var itemId = listItem.data("id");

        // Remove active class
        self.listItem()
            .removeClass("active");

        // Add active class to list item
        listItem.addClass("active");

        // Get gallery item
        var galleryItem = self.galleryItem().where("id", itemId);

        // Get index from gallery item
        var galleryItemIndex = self.getGalleryItemIndex(currentBlock, galleryItem);

        // Get gallery container
        var galleryContainer = self.galleryContainer.of(galleryItem);

        // Go to gallery
        EasyBlog.ImageGallery.go(galleryContainer, galleryItemIndex);
    },

    getGalleryItemIndex: function(block, galleryItem) {

        var viewport = self.galleryViewport.of(galleryItem);
        var galleryItems = viewport.find(self.galleryItem.selector);
        var index = galleryItems.index(galleryItem);

        return index;
    },

    "{listItemDeleteButton} click": function(listItemDeleteButton) {

        var activeListItem = self.listItem(".active");

        // If there are no active list item, stop.
        if (!activeListItem.length) return;

        // Get item id
        var itemId = activeListItem.data("id");

        // Get gallery item
        var galleryItem = self.galleryItem().where("id", itemId);

        // Get next gallery item
        var nextGalleryItem = galleryItem.prev();

        if (nextGalleryItem.length) {
            nextGalleryItem = nextGalleryItem.next();
        }

        // Get block of gallery item
        var block = blocks.block.of(galleryItem);

        // Remove gallery item
        galleryItem.remove();

        // Get gallery menu itme
        var galleryMenuItem = self.galleryMenuItem().where("id", itemId);

        // Remove gallery menu item
        galleryMenuItem.remove();

        // Get gallery container
        var galleryContainer = self.galleryContainer.inside(block);
        var galleryItemIndex = self.getGalleryItemIndex(nextGalleryItem);

        // Set layout
        EasyBlog.ImageGallery.setLayout(galleryContainer);

        // Go to gallery
        EasyBlog.ImageGallery.go(galleryContainer, galleryItemIndex);

        // Populate gallery list
        self.populateList(block);
    },

    "{listItemPrimaryButton} click": function(listItemPrimaryButton) {

        var activeListItem = self.listItem(".active");

        // If there are no active list item, stop.
        if (!activeListItem.length) {
            return;
        }

        // Get item id
        var itemId = activeListItem.data("id");

        // Set the primary item
        self.setPrimary(itemId);
    },

    hasThumbnails: function(block) {

        return false;
    }

}});

module.resolve();

});