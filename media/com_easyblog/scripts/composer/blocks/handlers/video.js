EasyBlog.module("composer/blocks/handlers/video", function($){

var module = this;

var selectRatioView = "view-selectratio";
var customRatioView = "view-customratio";

var videoSizeProps = [
    "video-width",
    "video-height"
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

var getCssProp = function(prop) {
    return prop.replace(/video-/,"");
}

var parseUnit = function(val) {
    return val.toString().match("%") ? "%" : "px";
};

var roundToDecimalPoint = function(value, n) {
    var p = Math.pow(10, n);
    return Math.round(value * p) / p;
};

var getRatioInDecimal = ratioDecimal = function(ratio) {

    // If decimal was given, just return the ratio.
    if ($.isNumeric(ratio)) return ratio;
    var parts = ratio.split(":");

    return parts[0] / parts[1];
};

var getRatioInPercent = ratioPercent = function(ratio, unit) {
    return roundToDecimalPoint(getRatioInDecimal(ratio) * 100, 2) + (unit ? "%" : 0);
};

var getRatioInPadding = ratioPadding = function(ratio) {
    return roundToDecimalPoint(1 / getRatioInDecimal(ratio) * 100, 2) + "%";
};

var sanitizeRatio = function(ratio) {
    ratio = $.trim(ratio);
    if (/\:/.test(ratio)) {
        var parts = ratio.split(":");
        return parseInt(parts[0]) + ":" + parseInt(parts[1]);
    } else {
        return parseFloat(ratio) || 0;
    }
};

EasyBlog.require()
.library(
    'plupload2',
    'videojs'
).done(function(){

EasyBlog.Controller("Composer.Blocks.Handlers.Video", {
    elements: [
        "[data-eb-{file-error}]",

        "[data-eb-video-size-field] [data-eb-{" + numsliderElements.join("|") + "}]",
        "^videoSizeField .eb-composer-field[data-name={" + videoSizeProps.join("|") + "}]",
        "[data-eb-video-{ratio-button|ratio-label|ratio-customize-button|ratio-cancel-button|ratio-use-custom-button|ratio-cancel-custom-button|alignment-selection|size-field|ratio-selection|ratio-input}]"
    ],
    defaultOptions: {

        // Browse button in placeholder
        "{browseButton}": ".eb-composer-placeholder-video [data-eb-mm-browse-button]",

        "{placeholder}": "[data-eb-composer-video-placeholder]",
        "{player}": "[data-video-player]",
        "{dropElement}": "[data-plupload-drop-element]",

        // Fieldset options
        "{autoplay}": "[data-video-fieldset-autoplay]",
        "{loop}": "[data-video-fieldset-loop]",
        "{muted}": "[data-video-fieldset-muted]",

        "{controls}": "[data-video-controls]",

        "{videoContainer}": ".eb-video",
        "{videoViewport}": ".eb-video-viewport",
        "{videoPlayer}": "video",
        "{videoSource}": "source"
    }
}, function(self, opts, base, composer, blocks, meta, currentBlock, getVideoContainer, dimensions, mediaManager) { return {

    init: function() {

        // Globals
        blocks = self.blocks;
        composer = blocks.composer;
        meta = opts.meta;
        currentBlock = $();
        dimensions = blocks.dimensions;
        mediaManager = EasyBlog.MediaManager;

        // INTERNAL HACK
        // Duckpunch .of() to accept prop
        $.each(numsliderElements, function(i, element){
            var method = $.camelize(element);
            self[method].of = $.memoize(function(prop) {
                // Get numslider field of this prop and return
                // numslider element under this numslider field
                var numsliderField = self.getVideoSizeField(prop);
                return self[method].under(numsliderField);
            });
        });

        // Speed up retrieval of get video container
        var videoContainers = {};
        getVideoContainer = function(block){
            return $(
                videoContainers[block.data("uid")] ||
                (videoContainers[block.data("uid")] = self.videoContainer.inside(block)[0])
            );
        }

        // Speed up retrieval of get video viewport
        getVideoViewport = $.memoize(function(block){
            return self.videoViewport.inside(block);
        }, function(block){
            return block.data("uid");
        });
    },

    normalize: function(data) {
        return $.extend({}, meta.data, data);
    },

    activate: function(block) {

        // Set as current block
        currentBlock = block

        // Populate fieldset
        self.populate(block);
    },

    deactivate: function(block) {
    },

    construct: function(data) {

        var block = blocks.createBlockContainer('video');
        var blockData = blocks.data(block);

        $.extend(blockData, data);

        return block;
    },

    constructFromMediaFile: function(mediaFile) {

        var key = mediaFile.data("key");
        var uri = mediaManager.getUri(key);

        // Create block container first
        var block = blocks.createBlockContainer("video");
        var blockContent = blocks.getBlockContent(block);
        var data = blocks.data(block);

        // Add loading indicator
        block.addClass("is-loading");

        // Get media meta
        mediaManager.getMedia(uri)
            .done(function(media){

                // Give it some time for block to drop & release
                // before creating the video player.
                setTimeout(function(){
                    var mediaMeta = media.meta;
                    var url = mediaMeta.url;
                    self.createPlayer(block, url);
                }, 250);
            })
            .fail(function(){
            })
            .always(function(){
                block.removeClass("is-loading");
            });

        return block;
    },

    reconstruct: function(block) {

        // Disable content editable
        block.editable(false);

        var data = blocks.data(block);

        // Has Video
        if (self.hasVideo(block)) {
            var data = blocks.data(block);
            self.createPlayer(block, data.url);
        } else {

            // Has Placeholder
            var placeholder = self.placeholder.inside(block);

            if (placeholder.length > 0) {
                EasyBlog.MediaManager.uploader.register(placeholder);
            }
        }
    },

    deconstruct: function(block) {
    },

    refocus: function(block) {
    },

    reset: function(block) {
    },

    populate: function(block) {

        var hasVideoContainer = self.hasVideoContainer(block);

        // Hide fieldgroup if there is no video
        var fieldgroup = blocks.panel.fieldgroup.get("video");
        fieldgroup.toggleClass("is-new", !hasVideoContainer);

        // Populate fieldset if there is video
        if (hasVideoContainer) {
            self.populateVideoSize(block);
            self.populateVideoControls(block);
        }
    },

    toData: function(block) {

        var data = blocks.data(block);
        var videoContainer = getVideoContainer(block);

        if (videoContainer.length > 0) {
            var videoContainerStyle = videoContainer[0].style;

            data.width  = videoContainerStyle.width;
            data.height = videoContainerStyle.height;
        }

        return data;
    },

    toText: function(block) {
        return;
    },

    toHTML: function(block) {
        return;
    },

    toLegacyShortcode: function(meta, block) {
        var str = '[embed=video][/embed]';
        var width = self.getVideoSize(block, 'width');
        var height = self.getVideoSize(block, 'height');
        var data = blocks.data(block);

        var obj = {
            "width": width,
            "height": height,
            "uri": meta.uri,
            "autoplay": data.autoplay ? "1" : "0",
            "muted": data.muted ? "1" : "0",
            "loop": data.loop ? "1" : "0"
        };

        var str = '[embed=video]' + JSON.stringify(obj) + '[/embed]';

        return str;
    },

    toEditableHTML: function(block) {
        return;
    },

    //
    // Video Uploads
    //
    "{placeholder} mediaUploaderFilesAdded": function(placeholder, event, uploader, files) {
        EasyBlog.MediaManager.uploader.addItem(files[0], placeholder);
    },

    "{placeholder} mediaUploaderFileUploaded": function(placeholder, event, uploader, file, data) {

        var response = data.response;
        var mediaItem = response.media;
        var mediaMeta = mediaItem.meta;

        var block = blocks.block.of(placeholder);

        setTimeout(function() {

            self.createPlayer(block, mediaMeta.url);

            // Populate block again
            if (block.hasClass("active")) {
                self.populate(block);
            }

        }, 600);
    },

    "{placeholder} mediaUploaderFileError": function(placeholder, event, uploader, error) {

        if (error.code == $.plupload2.FILE_EXTENSION_ERROR) {
            self.fileError.inside(currentBlock).removeClass('hide');
        }
    },

    //
    // Video Player
    //
    createPlayer: function(block, url) {

        var data = blocks.data(block);

        var uid = data.uid || (data.uid = $.uid("video-"));
        var videoContainer = $(meta.player).clone();
        var videoPlayer = self.videoPlayer.inside(videoContainer);
        var videoSource = self.videoSource.inside(videoContainer);

        // Set id, width, height, url
        videoPlayer.attr("id", uid);
        videoSource.attr("src", url);

        // Set the url of the video on the data
        data.url = url;

        data.width  && videoContainer.css("width", data.width);
        data.height && videoContainer.css("height", data.height);

        // Remove any assigned width/height.
        dimensions.toFluidWidth(block);
        dimensions.toFluidHeight(block);

        content = blocks.getBlockContent(block);

        // Insert video container onto block content
        blocks.getBlockContent(block)
            .empty()
            .append(videoContainer);

        // Initialize videojs
        videojs(uid, {
            controls: true,
            autoplay: false
        }, function() {
            // Determines if the player should be muted.
            this.muted(data.muted);
        });
    },

    //
    // Video Controls
    //
    populateVideoControls: function(block) {

        // Get block data
        var data = blocks.data(block);

        // Update the fieldsets
        self.autoplay()
            .val(data.autoplay ? 1 : 0)
            .trigger("change");

        self.loop()
            .val(data.loop ? 1 : 0)
            .trigger("change");

        self.muted()
            .val(data.muted ? 1 : 0)
            .trigger("change");
    },

    "{muted} change": function(el, event) {

        var data = blocks.data(currentBlock);
        data.muted = el.val() == 1 ? true : false;
    },

    "{autoplay} change": function(el, event) {

        var data = blocks.data(currentBlock);
        data.autoplay = el.val() == 1 ? true : false;
    },

    "{loop} change": function(el, event) {

        var data = blocks.data(currentBlock);
        data.loop = el.val() == 1 ? true : false;
    },

    //
    // Video Size
    //
    populateVideoSize: function(block, props) {

        // Populate slider, input & unit for video width & height
        var props = props || ["width", "height"];
        var prop;

        while (prop = props.shift()) {

            var value  = prop=="width" ? self.getVideoWidth(block) : self.getVideoHeight(block); // 1280
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

        // Also populate video ratio and alignment
        self.populateVideoRatio(block);
        self.populateVideoAlignment(block);
    },

    getVideoWidth: function(block) {

        var videoContainer = getVideoContainer(block);
        var videoContainerStyle = videoContainer[0].style;

        // Nested block (%) - assigned block width
        if (blocks.isNestedBlock(block)) {

            // Get assigned block width
            var assignedBlockWidth = block[0].style.width;

            // If assigned block width has a % on it, use it.
            if (/%/.test(assignedBlockWidth)) {
                return assignedBlockWidth;
            }
        }

        // Root block (%/px) or nested block (px)
        // Get assigned width, else get computed width.
        return videoContainerStyle.width || videoContainer.css("width");
    },

    getVideoHeight: function(block) {

        var videoContainer = getVideoContainer(block);
        var videoContainerStyle = videoContainer[0].style;

        // Root/nested (%)  - computed container height
        // Root/nested (px) - assigned container height
        return videoContainerStyle.height || videoContainer.css("height");
    },

    getVideoSize: function(block, prop) {

        if (prop=="width") return self.getVideoWidth(block);
        if (prop=="height") return self.getVideoHeight(block);
    },

    getVideoComputedWidth: function(block) {

        return getVideoContainer(block).width();
    },

    getVideoComputedHeight: function(block) {

        return getVideoContainer(block).outerHeight();
    },

    setVideoWidth: function(block, width) {

        var videoContainer = getVideoContainer(block);
        var videoViewport = getVideoViewport(block);
        var ratio = self.getVideoRatio(block);
        var unit = parseUnit(width);

        // Get computed height before a new width assigned
        var computedHeight = self.getVideoComputedHeight(block);

        // Add is-responsive class if unit is %
        videoContainer.toggleClass("is-responsive", unit=="%");

        // Nested block
        if (blocks.isNestedBlock(block)) {

            if (unit=="%") {
                // Assign new width
                block.css("width", width);
                videoContainer.css("width", "");
            }

            if (unit=="px") {
                block.css("width", "auto");
                videoContainer.css("width", width);
            }

        // Root block
        } else {
            videoContainer.css("width", width);
        }

        // Fluid video will need a ratio even if its unlocked.
        // Passing in null value to adjustVideoRatio creates a new
        // ratio based on current computed width & height.
        if (ratio==0 && unit=="%") {
            ratio = null;
        }

        // Adjust video ratio
        self.adjustVideoRatio(block, ratio);
    },

    setVideoFluidWidth: function(block, width) {

        var videoContainer = getVideoContainer(block);

        if (blocks.isNestedBlock(block)) {

            // Convert back to fixed width then assign a width
            dimensions.toAutoWidth(block);
            videoContainer.css("width", width);

            // Then from the new fixed width, convert it back to fluid.
            dimensions.toFluidWidth(block);
            videoContainer.css("width", "");

        } else {

            // Calculate width percentage
            var blockContent = blocks.getBlockContent(block);
            var width = ((width / blockContent.width()) * 100) + "%";

            // Assign width percentage
            videoContainer.css("width", width);
        }
    },

    setVideoSize: function(block, prop, val) {

        if (prop=="width") {
            self.setVideoWidth(block, val);
        }

        if (prop=="height") {
            self.setVideoHeight(block, val);
        }
    },

    updateVideoSize: function(block, prop, val) {

        self.setVideoSize(block, prop, val);

        self.populateVideoSize(block);
    },

    setVideoHeight: function(block, height) {

        var videoContainer = getVideoContainer(block);
        var videoViewport = getVideoViewport(block);
        var ratio = self.getVideoRatio(block);

        // Fluid video
        if (self.isFluidVideo(block)) {

            // If ratio is unlocked, adjust padding ratio.
            if (ratio==0) {

                var width = self.getVideoComputedWidth(block);
                var height = parseFloat(height);
                ratio = width / height;

            // If ratio is locked, adjust width.
            } else {

                // Calculate video width
                var width = parseFloat(height) * ratio;
                self.setVideoFluidWidth(block, width);
            }

            videoContainer.css("height", "");
            videoViewport.css("padding-top", ratioPadding(ratio));

        // Fixed height
        } else {

            // Adjust height
            videoContainer.css("height", height);

            // If ratio is locked, adjust width
            if (ratio!==0) {

                var width = parseFloat(height) * ratioDecimal(ratio);

                videoContainer.css("width", width);
                videoViewport.css("padding-top", "");
            }
        }
    },

    setVideoUnit: function(block, prop, unit) {

        // Only applies to width
        if (prop!=="width") return;

        var videoContainer = getVideoContainer(block);
        var width = self.getVideoWidth(block);
        var computedWidth = self.getVideoComputedWidth(block);
        var computedHeight = self.getVideoComputedHeight(block);

        // % to px
        if (unit=="px" && /%/.test(width)) {
            self.setVideoWidth(block, computedWidth);
        }

        // px to %
        if (unit=="%" && /px/.test(width)) {
            self.setVideoFluidWidth(block, computedWidth);
        }
    },

    getVideoUnit: function(block, prop) {

        var val = self.getVideoSize(block, prop);

        return parseUnit(val)
    },

    updateVideoUnit: function(block, prop, unit) {

        self.setVideoUnit(block, prop, unit);

        self.populateVideoSize(block);
    },

    handleNumsliderWidget: function(numsliderWidget, val) {

        // Get prop & val to update
        var prop = self.getVideoSizeProp(numsliderWidget);
        var unit = self.getVideoSizeUnit(prop);
        var val  = Math.round(val) + unit;

        // Declare that we are resizing the slider of this property
        self.resizingFromSlider = prop;

        self.updateVideoSize(currentBlock, prop, val);

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

        var prop = self.getVideoSizeProp(numsliderInput);

        var oldVal = self.getVideoSize(currentBlock, prop);
        var oldNum = parseFloat(oldVal);

        var num  = numsliderInput.val();
        var unit = parseUnit(oldVal);
        var val  = num + unit;

        // If value is invalid, don't do anything.
        if (!$.isNumeric(num)) {
            // Revert to original value when input is blurred.
            return revertOnBlur(oldNum);
        }

        // Update video size
        self.updateVideoSize(currentBlock, prop, val);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var prop = self.getVideoSizeProp(numsliderUnit);
        var unit = numsliderUnit.data("unit");

        self.updateVideoUnit(currentBlock, prop, unit);
    },


    "{self} composerBlockResizeStart": function(base, event, block, ui) {

        // Only handle video block
        if (blocks.getBlockType(block)!=="video") return;

        // Remember initial width & height
        var initialWidth  = self.getVideoComputedWidth(block);
        var initialHeight = self.getVideoComputedHeight(block);
        block.data("initialWidth" , initialWidth);
        block.data("initialHeight", initialHeight);
    },

    "{self} composerBlockBeforeResize": function(base, event, block, ui) {

        // Only handle video block
        if (blocks.getBlockType(block)!=="video" || !self.hasVideoContainer(block)) return;

        // Stop resizable from resizing block because
        // we want to resize the block ourselves.
        event.preventDefault();

        // Get image size, original block size and current block size.
        var imageSize = block.data("initialImageSize");
        var originalSize = ui.originalSize;
        var currentSize = ui.size;

        // Calculate width/height difference
        var dx = currentSize.width  - originalSize.width;
        var dy = currentSize.height - originalSize.height;
        var initialWidth  = block.data("initialWidth");
        var initialHeight = block.data("initialHeight");
        var newWidth  = initialWidth  + dx;
        var newHeight = initialHeight + dy;
        var ratio = self.getVideoRatio(block);

        function updateWidth() {
            self.isFluidVideo(block) ?
                self.setVideoFluidWidth(block, newWidth) :
                self.setVideoWidth(block, newWidth);
        };

        function updateHeight() {
            self.setVideoHeight(block, newHeight);
        };

        // If ratio is unlocked, update both width & height.
        if (ratio==0) {
            dx!==0 && updateWidth();
            dy!==0 && updateHeight();

        // If ratio is locked,
        // update width if there's change in width,
        // update height if there's a change in height.
        } else {
            dx==0 ? (dy!==0 && updateHeight()) : updateWidth();
        }

        // Populdate video size
        self.populateVideoSize(block);
    },

    //
    // Video Ratio
    //
    setVideoRatio: function(block, ratio) {

        // Set new ratio onto block data
        var data = blocks.data(block);
        data.ratio = ratio;

        // Sync video ratio
        self.adjustVideoRatio(block, ratio);
    },

    adjustVideoRatio: function(block, ratio) {

        var videoContainer = getVideoContainer(block);
        var videoViewport = getVideoViewport(block);
        var computedWidth = self.getVideoComputedWidth(block);
        var computedHeight = self.getVideoComputedHeight(block);
        var height = "";
        var paddingTop = "";

        // If no ratio given, get ratio from current computed width & height.
        if (ratio==null) {
            ratio = computedWidth / computedHeight;
        }

        if (self.isFluidVideo(block)) {
            // Note: Fluid video will need a ratio even if its unlocked
            paddingTop = ratioPadding(ratio);
        } else {
            // If ratio is unlocked, use computed height.
            // If ratio is locked, calculate new height.
            height = ratio==0 ? computedHeight : computedWidth / ratioDecimal(ratio);
        }

        videoContainer.css("height", height);
        videoViewport.css("padding-top", paddingTop);
    },

    populateVideoRatio: function(block) {

        // Get ratio from data
        var data = blocks.data(block);
        var ratio = data.ratio;

        // Toggle ratio-unlocked class
        self.ratioButton()
            .toggleClass("ratio-unlocked", ratio==0);

        // Update ratio label
        self.ratioLabel()
            .html(ratio);
    },

    updateVideoRatio: function(block, ratio) {

        self.setVideoRatio(block, ratio);

        self.populateVideoRatio(block);
    },

    getVideoRatio: function(block) {

        var data = blocks.data(block);
        var videoContainer = getVideoContainer(block);

        if (data.ratio===undefined) {
            var width  = self.getVideoComputedWidth(block);
            var height = self.getVideoComputedHeight(block);
            data.ratio = width / height;
        }

        return getRatioInDecimal(data.ratio);
    },

    "{browseButton} mediaSelect": function(browseButton, event, media) {

        var block = blocks.block.of(browseButton);

        if (media.meta.type != "video") {
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
            self.createPlayer(block, mediaMeta.url);
        }
    },

    // Triggers when the insert button of mediamanager info is clicked
    "{self} mediaInsert": function(el, event, media, block) {

        // Make sure we only process video blocks
        if (media.meta.type!="video") {
            return;
        }

        var composerDocument = composer.document;
        var isLegacy = composerDocument.isLegacy();

        if (isLegacy) {
            content = self.toLegacyShortcode(media.meta, block);
            composerDocument.insertContent(content);
        } else {

            var data = blocks.data(block);
            var block = blocks.constructBlock("video", {
                "url": media.meta.url
            });

            blocks.addBlock(block);
            blocks.activateBlock(block);

            // // Construct a new post block and insert into the document
            // var block = blocks.constructBlock('video', {
            //     "url": file.url
            // });

            // blocks.addBlock(block);
        }
    },

    "{self} mediaInfoDestroy": function(el, event, info, media) {

        if (media && media.meta.type!="video") {
            return;
        }

        // Get video player
        var videoPlayer = self.videoPlayer.inside(info);
        var videoId = videoPlayer.attr("id");

        // Destroy video player
        videojs(videoId).dispose();
    },

    "{ratioButton} click": function(ratioButton) {

        // Show ratio selection field
        self.sizeField()
            .switchClass(selectRatioView);
    },

    "{ratioCustomizeButton} click": function(ratioCustomizeButton) {

        // Show custom ratio field
        self.sizeField()
            .switchClass(customRatioView);
    },

    "{ratioCancelButton} click": function(ratioCancelButton) {

        // Hide ratio selection field
        self.sizeField()
            .removeClass(selectRatioView);
    },

    "{ratioCancelCustomButton} click": function(ratioCancelCustomButton) {

        // Show ratio selection field
        self.sizeField()
            .switchClass(selectRatioView);
    },

    "{ratioOkCustomButton} click": function(ratioOkCustomButton) {

        // Hide custom ratio field
        self.sizeField()
            .removeClass(customRatioView);
    },

    "{ratioUseCustomButton} click": function(ratioUseCustomButton) {

        var ratioInput = self.ratioInput();
        var ratio = sanitizeRatio(ratioInput.val());

        // If ratio is invalid, do nothing.
        if (ratio==0) return;

        // Update video ratio
        self.updateVideoRatio(currentBlock, ratio);

        // Deactivate all ratio selection
        self.ratioSelection()
            .removeClass("active");

        // Hide custom ratio field
        self.sizeField()
            .removeClass(customRatioView);
    },

    "{ratioSelection} click": function(ratioSelection) {

        self.ratioSelection()
            .removeClass("active");

        ratioSelection.addClass("active");

        self.sizeField()
            .removeClass(selectRatioView);

        var ratio = ratioSelection.data("value");

        self.updateVideoRatio(currentBlock, ratio);
    },

    //
    // Video Alignment
    //
    "{alignmentSelection} change": function(alignmentSelection) {

        var alignment = alignmentSelection.val();
        blocks.font.setFontFormatting(currentBlock, "align" + alignment);
    },

    populateVideoAlignment: function(block) {

        if (blocks.isNestedBlock(block)) {
            self.sizeField()
                .addClass("no-alignment");
            return;
        }

        var blockContent = blocks.getBlockContent(block);
        var width = self.getVideoComputedWidth(block);
        var hasAlignment = width < blockContent.width();

        // Toggle alignment field
        self.sizeField()
            .toggleClass("no-alignment", !hasAlignment);

        // Set alignment
        var alignment = block.css("text-align");
        self.alignmentSelection()
            .val(alignment);
    },

    //
    // Video Helpers
    //
    getVideoSizeField: function(prop) {

        var field = self["videoSizeFieldVideo" + $.capitalize(prop)]();
        return field;
    },

    getVideoSizeProp: function(elem) {

        var numslider = elem.closest(self.numslider.selector);
        var prop = getCssProp(numslider.data("name"));

        return prop;
    },

    getVideoSizeUnit: function(prop) {

        // Get field of this prop
        var field = self.getVideoSizeField(prop);
        return $.trim(self.numsliderCurrentUnit.under(field).text());
    },

    isFluidVideo: function(block) {
        var videoContainer = getVideoContainer(block);
        return videoContainer.hasClass("is-responsive");
    },

    hasVideo: function(block) {
        var data = blocks.data(block);

        return !!data.url;
    },

    hasVideoContainer: function(block) {
        return self.videoContainer.inside(block).length > 0;
    }

}});

module.resolve();

});

});
