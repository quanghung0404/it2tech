EasyBlog.module("composer/blocks/font", function($){

var module = this;

EasyBlog.require()
.library(
    "colorpicker",
    "nouislider"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Font",
{
    elements: [
        ".eb-composer-fieldset[data-name=font] [data-eb-{font-color-menu|font-family-menu|font-size-menu|font-color-content|font-color-picker|font-family-content|font-size-content|font-color-caption|font-family-caption|font-size-caption|font-family-option|font-format-option}]",

        ".eb-composer-fieldset[data-name=font] [data-eb-{numslider-toggle|numslider-widget|numslider-value|numslider-input|numslider-units|numslider-unit|numslider-current-unit}]",

        ".eb-composer-fieldset[data-name=font] [data-eb-{colorpicker|colorpicker-toggle}]",
    ],

    defaultOptions: $.extend({

        fontSizeUnits: {

            "px": {
                start: 12,
                step: 2,
                range: {
                    min: 8,
                    max: 72
                },
                pips: {
                    mode: "values",
                    density: 4,
                    values: [8, 12, 18, 24, 48, 72]
                }
            },

            "%": {
                start: 100,
                step: 10,
                range: {
                    min: 0,
                    max: 200
                },
                pips: {
                    mode: "positions",
                    values: [0,50,100],
                    density: 10
                }
            }
        }

    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
        currentBlock = $();

        self.initFontFormatting();
        self.initFontColor();
    },

    "{self} composerBlockActivate": function(base, event, block) {

        currentBlock = block;

        self.populate(block);
    },

    populate: function(block) {

        // Determine if we should show font fieldset
        var blockMeta = blocks.getBlockMeta(block),
            showFontFieldset = blockMeta.properties.fonts;

        // Show or hide font fieldset
        composer.panels.fieldset.get("font")
            .toggle(showFontFieldset);

        if (!showFontFieldset) {
            return;
        }

        self.populateFontColor(block);
        self.populateFontFamily(block);
        self.populateFontSize(block);
        self.populateFontFormatting(block);
    },

    //
    // Font Color API
    //

    initFontColor: function() {

        // Init colorpicker
        self.fontColorPicker()
            .colorpicker();
    },

    populateFontColor: function(block) {

        var fontColor = block.css('color');

        self.updateFontColorUI(fontColor);
    },

    setFontColor: function(block, fontColor) {

        // Update block font color
        block.css("color", fontColor);

        // Update font color UI
        self.updateFontColorUI(fontColor);
    },

    updateFontColorUI: function(fontColor) {

        self.updatingFontColorUI = true;

        // Defaults to black
        if (!fontColor) fontColor = currentBlock.css("color") || "#000";

        // Update color preview
        self.fontColorCaption()
            .css("backgroundColor", fontColor);

        self.fontColorPicker()
            .colorpicker("setColor", fontColor);

        self.updatingFontColorUI = false;
    },

    removeFontColor: function(block) {

        self.setFontColor(block, "");
    },

    //
    // Font Family API
    //

    populateFontFamily: function(block) {

        var fontFamily = block[0].style.fontFamily;

        // Update the font family
        self.updateFontFamilyUI(fontFamily);
    },

    setFontFamily: function(block, fontFamily) {

        // Remove any font preview
        self.unpreviewFontFamily(block);

        // Create change event
        var changeEvent = $.Event("composertBlockFontFamilyChange");
        changeEvent.fontFamily = fontFamily;

        // Trigger change event
        base.trigger(changeEvent, fontFamily);

        // Update font family UI
        self.updateFontFamilyUI(changeEvent.fontFamily);

        // If change event is not prevented, set font.
        if (!changeEvent.isDefaultPrevented()) {
            block.css("fontFamily", changeEvent.fontFamily);
        }
    },

    updateFontFamilyUI: function(fontFamily) {

        var fontFamilyOption =
            self.fontFamilyOption()
                .removeClass("active")
                .where("value", '"' + fontFamily + '"')
                .addClass("active");

        // Determine font family captiomn
        var fontFamilyCaption =
                fontFamilyOption.length > 0 ?
                    fontFamilyOption.html() :
                    fontFamily.split(",")[0];

        // Set font family caption
        self.fontFamilyCaption()
            .html(fontFamilyCaption);
    },

    previewFontFamily: function(block, fontFamily) {

        // Remember original font value
        var originalFontFamily =
                block.data("originalFontFamily") ||
                currentBlock[0].style.fontFamily;

        // Create preview event
        var previewEvent = $.Event("composerBlockFontFamilyPreview");
        previewEvent.originalFontFamily = originalFontFamily;
        previewEvent.fontFamily = fontFamily;

        // Trigger preview event
        base.trigger(previewEvent, fontFamily, originalFontFamily);

        // Store original font family
        block.data("originalFontFamily", previewEvent.originalFontFamily);

        // If event is not prevented, set font family from block.
        if (!previewEvent.isDefaultPrevented()) {
            block.css("fontFamily", previewEvent.fontFamily);
        }
    },

    unpreviewFontFamily: function(block) {

        // Get original font family
        var originalFontFamily = block.data("originalFontFamily");

        // Create unpreview event
        var unpreviewEvent = $.Event("composerBlockFontFamilyUnpreview");
        unpreviewEvent.originalFontFamily = originalFontFamily;

        // Trigger unpreview evetn
        base.trigger(unpreviewEvent, originalFontFamily);

        // Forget original font family
        block.removeData("originalFontFamily");

        // If event is not prevented, remove font family from block.
        if (!unpreviewEvent.isDefaultPrevented()) {
            block.css("fontFamily", unpreviewEvent.originalFontFamily);
            return;
        }
    },

    //
    // Font Size API
    //

    populateFontSize: function(block) {

        var fontSize = block.css("fontSize");

        // Update the fontsize
        self.updateFontSizeUI(fontSize);
    },

    setFontSize: function(block, fontSize) {

        // If number is given, add a unit.
        if ($.isNumeric(fontSize)) {
            var unit = self.getFontSizeUnit();
            fontSize = fontSize + unit;
        }

        // Update block font size
        block.css("fontSize", fontSize);

        // Update font size UI
        self.updateFontSizeUI(fontSize || block.css("fontSize"));

        // Automatically set line height whenever
        // font size is set.
        if (fontSize) {

            self.setLineHeight(block, "120%");

            self.numsliderToggle()
                .prop("checked", true);

        } else {

            self.removeLineHeight(block);

            self.numsliderToggle()
                .prop("checked", false);
        }
    },

    updateFontSizeUI: function(fontSize) {

        self.updatingFontSizeUI = true;

        // Get value & unit
        var value = Math.abs(fontSize.replace(/\%|px/gi, ""))
            unit = fontSize.match("%") ? "%" : "px";

        if (self.getFontSizeUnit()!==unit) {
            self.setFontSizeUnit(unit);
        }

        // Set caption
        self.fontSizeCaption()
            .html(fontSize);

        // Set dropdown toggle
        self.numsliderCurrentUnit()
            .html(unit);

        // Set dropdown
        self.numsliderUnit()
            .removeClass("active")
            .where("unit", '"' + unit + '"')
            .addClass("active");

        // Set slider value
        self.numsliderWidget()
            .val(value);

        // Set input value
        self.numsliderInput().val(value);

        self.updatingFontSizeUI = false;
    },

    removeFontSize: function(block) {

        self.setFontSize(block, "");
    },

    getFontSizeUnit: function() {

        return self.fontSizeContent().data("unit") || "%";
    },

    setFontSizeUnit: function(unit) {

        self.fontSizeContent().data("unit", unit);

        // Use percentage by default
        var unitOptions = opts.fontSizeUnits[unit];

        // Set up slider
        self.numsliderWidget()
            .find(".noUi-pips")
            .remove()
            .end()
            .noUiSlider(unitOptions, true)
            .noUiSlider_pips(unitOptions.pips);
    },

    setLineHeight: function(block, lineHeight) {

        block.css("lineHeight", lineHeight);
    },

    removeLineHeight: function(block) {

        self.setLineHeight(block, "");
    },

    //
    // Font Formatting API
    //

    fontFormatting: {

        bold: {
            key: "fontWeight",
            val: "bold"
        },

        italic: {
            key: "fontStyle",
            val: "italic"
        },

        underline: {
            key: "textDecoration",
            val: "underline"
        },

        strikethrough: {
            key: "textDecoration",
            val: "line-through"
        },

        alignleft: {
            key: "textAlign",
            val: "left"
        },

        alignright: {
            key: "textAlign",
            val: "right"
        },

        aligncenter: {
            key: "textAlign",
            val: "center"
        },

        justify: {
            key: "textAlign",
            val: "justify"
        }
    },

    initFontFormatting: function() {

        self.fontFormatOption()
            .on("touchstart click mousedown mouseup", function(event){

                // Prevent caret from losing focus
                event.preventDefault();
            })
            .on("touchstart click", function(event){
                var fontFormatOption = $(this);
                var format = fontFormatOption.data("format");
                self.setFontFormatting(currentBlock, format, fontFormatOption.hasClass("active"));
            });
    },

    populateFontFormatting: function(block) {

        var node = block[0];
        var style = node.style;

        var fontFormatOption =
            self.fontFormatOption().each(function(){

                var fontFormatOption = $(this);
                var format = fontFormatOption.data("format");

                if (/orderedlist|unorderedlist|indent|outdent/.test(format)) {
                    fontFormatOption.removeClass("active");
                    return;
                }

                var fontFormatting = self.fontFormatting[format];
                var hasFormatting = style[fontFormatting.key]==fontFormatting.val;

                fontFormatOption.toggleClass("active", hasFormatting);
            });

        var isTextBlock = block.data("type")=="text";

        if (isTextBlock) {

            // Quick hack to activate the same button on global font fieldset
            var current = composer.editor.selection.getCurrent();
            var list = $(current).parentsUntil(block).filter("ul, ol").eq(0);

            $.each({orderedlist: "ol", unorderedlist: "ul"}, function(format, formatTag) {

                var hasFormatting = list.is(formatTag);

                fontFormatOption.where("format", format)
                    .toggleClass("active", hasFormatting);
            });
        }

        // Limit list formating to only text block
        composer.panels.fieldset.get("font")
            .find(".eb-font-formatting.section-list")
            .toggle(isTextBlock);
    },

    setFontFormatting: function(block, format, removeFormatting) {

        var editor = composer.editor;

        switch (format) {

            case "orderedlist":
            case "unorderedlist":
                editor.list.toggle(format);
                break;

            case "indent":
                editor.indent.increase();
                break;

            case "outdent":
                editor.indent.decrease();
                break;

            default:
                var fontFormatting = self.fontFormatting[format];
                block.css(fontFormatting.key, removeFormatting ? "" : fontFormatting.val);
                break;
        }

        self.populateFontFormatting(block);
    },

    removeFontFormatting: function(block, format) {

        self.setFontFormatting(block, format, true);
    },

    //
    // Font Color UI
    //

    "{colorpicker} colorpickerChange": function(colorpicker, event, hex) {

        if (self.updatingFontColorUI) return;

        self.colorpickerToggle().prop("checked", true);

        self.setFontColor(currentBlock, hex);
    },

    "{colorpickerToggle} change": function(colorpickerToggle) {

        // If we're disable font color, remove font color.
        if (!colorpickerToggle.checked()) {
            self.removeFontColor(currentBlock);
        }
    },

    //
    // Font Family UI
    //

    "{fontFamilyOption} mouseover": function(fontFamilyOption) {

        var fontFamily = fontFamilyOption.data("value");

        self.previewFontFamily(currentBlock, fontFamily);
    },

    "{fontFamilyOption} mouseout": function() {

        self.unpreviewFontFamily(currentBlock);
    },

    "{fontFamilyOption} click": function(fontFamilyOption) {

        var fontFamily = fontFamilyOption.data("value");

        self.setFontFamily(currentBlock, fontFamily);
    },

    //
    // Font Size UI
    //

    "{numsliderWidget} nouislide": function(numsliderWidget, event, value) {

        // Sliding only updates input
        self.numsliderInput()
            .val(Math.abs(value));
    },

    "{numsliderWidget} set": function(numsliderWidget, event, value) {

        if (self.updatingFontSizeUI) return;

        self.setFontSize(currentBlock, Math.abs(value));
    },

    "{numsliderInput} input": function(numsliderInput) {

        if (self.updatingFontSizeUI) return;

        var fontSize = Math.abs($.trim(numsliderInput.val()));

        self.numsliderToggle().checked(true);

        self.setFontSize(currentBlock, fontSize);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var unit = numsliderUnit.data("unit");

        self.setFontSizeUnit(unit);
    },

    "{numsliderToggle} change": function(numsliderToggle) {

        // If we're disable font size, remove font size.
        if (!numsliderToggle.checked()) {
            self.removeFontSize(currentBlock);
        }
    }

}});


module.resolve();

});

})