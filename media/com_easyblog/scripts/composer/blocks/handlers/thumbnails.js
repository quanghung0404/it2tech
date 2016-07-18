EasyBlog.module("composer/blocks/handlers/thumbnails", function($){

var module = this;

var selectRatioView = "view-selectratio";
var customRatioView = "view-customratio";

var numsliderElements = [
    "numslider",
    "numslider-toggle",
    "numslider-widget",
    "numslider-value",
    "numslider-input"
];

var sanitizeRatio = function(ratio) {
    ratio = $.trim(ratio);
    if (/\:/.test(ratio)) {
        var parts = ratio.split(":");
        return parseInt(parts[0]) + ":" + parseInt(parts[1]);
    } else {
        return parseFloat(ratio) || 0;
    }
};

EasyBlog.Controller("Composer.Blocks.Handlers.Thumbnails", {
    elements: [
        "[data-eb-thumbnails-{layout-fieldset|layout-selection|size-fieldset|strategy-field|strategy-menu-item}]",
        "[data-eb-thumbnails-columns-field] [data-eb-{" + numsliderElements.join("|") + "}]",
        "[data-eb-thumbnails-{ratio-button|ratio-label|ratio-customize-button|ratio-cancel-button|ratio-use-custom-button|ratio-cancel-custom-button|alignment-selection|size-field|ratio-selection|ratio-input}]",

    ],
    defaultOptions: {
        "{thumbUploadPlaceholder}": ".eb-thumbs-upload-placeholder",
        "{thumbContainer}": ".eb-thumbs",
        "{thumbColumn}": ".eb-thumbs-col",
        "{thumbItem}": ".eb-thumb",
        "{thumbViewport}": "> div",
        "{thumbPlaceholder}": ".eb-thumb-placeholder",
        "{thumbImage}": ".ebd-block[data-type=image]"
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
        currentBlock = block;

        // Populate fieldset
        self.populate(block);

        // Activate draggable
        self.initSortable(block);
    },

    deactivate: function(block) {

        // Deactivate draggable
        self.destroySortable(block);
    },

    initSortable: function(block) {

        // Initialize sortable on nest
        self.thumbColumn.inside(block)
            .each(function(){

                var thumbColumn = $(this);

                // If ui sortable is implemented, stop.
                if (thumbColumn.hasClass("ui-sortable")) {
                    return;
                }

                thumbColumn.sortable({

                    // Items
                    items: self.thumbItem.selector,
                    connectWith: self.thumbColumn.selector,

                    // Behaviour
                    tolerance: "pointer",
                    refreshPositions: true,
                });
            });
    },

    destroySortable: function(block) {

        // Initialize sortable on nest
        self.thumbColumn.inside(block)
            .each(function(){

                var thumbColumn = $(this);

                // If ui sortable is not implemented, skip.
                if (!thumbColumn.hasClass("ui-sortable")) {
                    return;
                }

                // Cloned block will fail at this part
                try {
                    thumbColumn.sortable("destroy");
                } catch(e) {}
            });
    },

    construct: function(data) {
    },

    reconstruct: function(block) {

        // Disable content editable
        block.editable(false);

        // if (self.hasThumbnails(block)) {

        // } else {

        //     // Register upload placeholder
        //     var thumbUploadPlaceholder = self.thumbUploadPlaceholder.inside(block);
        //     EasyBlog.MediaManager.uploader.register(thumbUploadPlaceholder);
        // }

        var thumbUploadPlaceholder = self.thumbUploadPlaceholder.inside(block);
        EasyBlog.MediaManager.uploader.register(thumbUploadPlaceholder);
    },

    deconstruct: function(block) {

        // Destroy sortable
        self.destroySortable(block);

        // Remove upload placeholder
        self.thumbUploadPlaceholder.inside(block).remove();

        return block;
    },

    refocus: function(block) {
    },

    reset: function(block) {
    },

    toData: function(block) {
        var data = blocks.data(block);
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

    createThumbItem: function() {
        return $(meta.thumbItem);
    },

    addThumbItem: function(block) {

        // Create thumb item
        var thumbItem = self.createThumbItem();

        // Append thumb item to the correct block
        var column = self.getNextColumn(block);
        column.append(thumbItem);

        return thumbItem
    },

    getNextColumnIndex: function(block) {

        var data = blocks.data(block);

        // Determine of index of thumb item
        var total = self.thumbItem.inside(block).length;
        var thumbIndex = total;

        // From the index of thumb item, determine column index
        var columnCount = data.column_count;
        var columnIndex = self.getColumnIndex(thumbIndex, columnCount);

        return columnIndex;
    },

    getColumnIndex: function(thumbIndex, columnCount) {

        return thumbIndex - (Math.floor(thumbIndex / columnCount) * columnCount);
    },

    getNextColumn: function(block) {

        var columnIndex = self.getNextColumnIndex(block);
        var column = self.thumbColumn.inside(block).eq(columnIndex);

        return column;
    },

    createThumbPlaceholder: function() {

        return $(meta.thumbPlaceholder);
    },

    createThumbImage: function(block, mediaMeta) {

        var data = blocks.data(block);

        var uri = mediaMeta.uri;
        var thumbVariation = mediaManager.getVariation(uri, "small");
        var popupVariation = mediaManager.getVariation(uri, "large");

        var naturalWidth = thumbVariation.width;
        var naturalHeight = thumbVariation.height;
        var naturalRatio = naturalWidth / naturalHeight;

        var thumbImageData = {

            // Source
            uri: uri,
            url: thumbVariation.url,
            variation: thumbVariation.key,

            // Size
            mode_lock: true,
            width_lock: true,
            height_lock: true,
            element_ratio: naturalRatio,

            // Natural width/height
            natural_width: naturalWidth,
            natural_height: naturalHeight,
            natural_ratio: naturalRatio,

            // Diable caption, link & style
            caption_enabled: false,
            link_enabled: false,
            style_enabled: false,
            size_enabled: false,

            // Popup
            popup_uri: uri,
            popup_url: popupVariation.url,
            popup_variation: popupVariation.key
        };

        var layout = data.layout;

        if (layout=="stack") {
            $.extend(thumbImageData, {
                mode: "simple",
                fluid: false,
                width: "100%"
            });
        }

        if (layout=="grid") {
            $.extend(thumbImageData, {
                mode: "advanced",
                fluid: true,
                width: "100%",
                height: "100%",
                ratio: data.ratio,
                strategy: data.strategy
            });
        }

        // Create thumbnail image block
        var thumbImage =
            blocks.constructNestedBlock("image", thumbImageData)
                .addClass("is-isolated");

        return thumbImage;
    },

    "{thumbUploadPlaceholder} mediaUploaderFilesAdded": function(thumbUploadPlaceholder, event, uploader, files) {

        var block = blocks.block.of(thumbUploadPlaceholder);

        $.each(files, function(i, file) {

            // Get fileId
            var fileId = file.id;

            // Add thumb item
            var thumbItem = self.addThumbItem(block);

            // Assign file id so we can track back this item
            thumbItem
                .addClass("is-uploading")
                .attr("data-file-id", fileId);

            // Get thumb viewport
            var thumbViewport = self.thumbViewport.inside(thumbItem);

            // Add thumb placeholder to thumb viewport
            // so that we can show upload progress.
            var thumbPlaceholder = self.createThumbPlaceholder();
            thumbItem.append(thumbViewport);

            // Register thumb placeholder to uploader to the file's upload progress
            // will be automatically reflected on this thumb placeholder.
            mediaUploader.addItem(file, thumbPlaceholder);

            // Remove is-empty class.
            var thumbContainer = self.thumbContainer.inside(block);
            thumbContainer.removeClass("is-empty");
        });
    },

    "{thumbUploadPlaceholder} mediaUploaderFileUploaded": function(thumbUploadPlaceholder, event, uploader, file, data) {

        // Get fileId, mediaItem and mediaMeta.
        var fileId = file.id;
        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        // Give some time for upload progress transition to complete
        // before replacing the thumb placeholder with the thumb image
        setTimeout(function(){

            var block = blocks.block.of(thumbUploadPlaceholder);

            // Get thumb item
            var thumbItem = self.thumbItem().where("fileId", fileId);

            // Remove is-uploading class
            thumbItem
                .removeClass("is-uploading")
                .removeAttr("data-file-id")
                .find(".eb-loader-o")
                .remove();

            // Unregister item from uploader
            mediaUploader.removeItem(file, thumbItem);

            // Get thumb viewport
            var thumbViewport = self.thumbViewport.inside(thumbItem);

            // Create thumb image and insert it inside thumb viewport
            var thumbImage = self.createThumbImage(block, mediaMeta);
            thumbViewport.html(thumbImage);

        }, 600);
    },

    populate: function(block) {

        var hasThumbnails = self.hasThumbnails(block);

        // Hide fieldgroup if there is no video
        var fieldgroup = blocks.panel.fieldgroup.get("thumbnails");
        fieldgroup.toggleClass("is-new", !hasThumbnails);

        if (hasThumbnails) {
            self.populateLayout(block);
            self.populateColumnCount(block);
            self.populateResizeStrategy(block);
            self.populateRatio(block);
        }
    },

    //
    // Layout
    //

    setLayout: function(block, layout) {

        var data = blocks.data(block);
        data.layout = layout;

        var thumbImages = self.thumbImage.inside(block);
        thumbImages.each(function(i){

            var thumbImage = $(this);

            // Stack
            if (layout=="stack") {
                imageBlockHandler.toSimpleImageMode(thumbImage);
            }

            // Grid
            if (layout=="grid") {

                var strategy = data.strategy;
                var ratio = data.ratio;

                thumbImages.each(function(i){
                    imageBlockHandler.toAdvancedImageMode(thumbImage);
                    imageBlockHandler.setImageRatio(thumbImage, ratio);
                    imageBlockHandler.resizeToViewport(thumbImage, strategy);
                });
            }
        });
    },

    updateLayout: function(block, layout) {

        self.setLayout(block, layout);
        self.populateLayout(block);
    },

    populateLayout: function(block) {

        var data = blocks.data(block);
        var layout = data.layout;

        self.layoutSelection()
            .removeClass("active")
            .where("value", layout)
            .addClass("active");

        // Switch size fieldset preset
        self.sizeFieldset()
            .switchClass("preset-" + layout);
    },

    "{layoutSelection} click": function(layoutSelection) {

        var layout = layoutSelection.data("value");
        self.updateLayout(currentBlock, layout);
    },

    //
    // Column Count
    //

    setColumnCount: function(block, columnCount) {

        var data = blocks.data(block);
        data.column_count = columnCount;

        // Detach all thumb items
        var thumbItems = self.thumbItem.inside(block);
        thumbItems.detach();

        // Create an array of columns
        var columns = [];
        var i = 0;

        while (i++ < columnCount) {
            columns.push($(meta.thumbColumn));
        }

        // Append thumb items using round-robin method
        thumbItems.each(function(thumbIndex){

            // Get column
            var columnIndex = self.getColumnIndex(thumbIndex, columnCount);
            var column = columns[columnIndex];

            // Append thumb item to colu,mn
            var thumbItem = $(this);
            column.append(thumbItem);
        });

        // Remove existing columns
        var thumbColumns = self.thumbColumn.inside(block);
        thumbColumns.remove();

        // Append new columns
        var thumbContainer = self.thumbContainer.inside(block);
        thumbContainer
            .switchClass("col-" + columnCount)
            .append(columns);

        // Reactivate sortable if block is active
        if (block.hasClass("active")) {
            self.activateSortable(block);
        }
    },

    updateColumnCount: function(block, columnCount) {

        self.setColumnCount(block, columnCount);
        self.populateColumnCount(block);
    },

    populateColumnCount: function(block) {

        var data = blocks.data(block);
        var columnCount = data.column_count;

        // Update numslider widget
        // only if user is not resizing from slider
        if (!self.resizingFromSlider) {

            // Pixel unit
            var sliderOptions = {
                start: columnCount,
                step: 1,
                range: {
                    min: 1,
                    max: 8
                },
                pips: {
                    mode: "values",
                    values: [1, 2, 3, 4, 5, 6, 7, 8],
                    density: 16
                }
            };

            // Set up slider
            self.numsliderWidget()
                .find(".noUi-pips")
                .remove()
                .end()
                .noUiSlider(sliderOptions, true)
                .noUiSlider_pips(sliderOptions.pips);
        }
    },

    "{numsliderWidget} set": function(numsliderWidget, event, val) {

        self.resizingFromSlider = true;

        var columnCount = Math.round(val);
        self.updateColumnCount(currentBlock, columnCount);

        self.resizingFromSlider = null;
    },

    //
    // Ratio
    //

    setRatio: function(block, ratio) {

        var data = blocks.data(block);
        data.ratio = ratio;

        var thumbImages = self.thumbImage.inside(block);
        thumbImages.each(function(i){
            var thumbImage = $(this);
            imageBlockHandler.setImageRatio(thumbImage, ratio);
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

        var thumbImages = self.thumbImage.inside(block);
        thumbImages.each(function(i){
            var thumbImage = $(this);
            imageBlockHandler.resizeToViewport(thumbImage, strategy);
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
    // Helpers
    //
    hasThumbnails: function(block) {

        return !!self.thumbItem.inside(block).length;
    },

    "{self} composerBlockBeforeRemove": function(base, event, block) {

        var blockType = blocks.getBlockType(block);

        if (blockType=="image") {

            var thumbItem = self.thumbItem.of(block);

            setTimeout(function(){

                // Get block of thumbItem
                var block = blocks.block.of(thumbItem);

                // Remove thumbnail
                thumbItem.remove();

                // Toggle is-empty class if all has been removed
                var thumbContainer = self.thumbContainer.inside(block);
                var hasThumbnails = self.hasThumbnails(block);
                thumbContainer.toggleClass("is-empty", !hasThumbnails);

            }, 1);
        }
    }

}});

module.resolve();

});
