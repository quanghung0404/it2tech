EasyBlog.module("composer/blocks/dimensions", function($){

var module = this;

var parseUnit = function(val) {
    return val.toString().match("%") ? "%" : "px";
};

EasyBlog.require()
.library(
    "nouislider"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Dimensions",
{
    elements: [
        "[data-eb-block-dimensions-{field}]",
        "[data-eb-block-dimensions-field-container] [data-eb-{numslider-toggle|numslider-widget|numslider-value|numslider-input|numslider-units|numslider-unit|numslider-current-unit}]",
    ],

    defaultOptions: $.extend({}, EBD.selectors)
},
function(self, opts, base, composer, blocks, currentBlock) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;
        currentBlock = $();
    },

    "{self} composerBlockActivate": function(base, event, block) {
        currentBlock = block;
        self.populate(block);
    },

    isResizableNestedBlock: function(block) {
        if (!blocks.isNestedBlock(block)) return false;
        return blocks.getBlockNestType(block)=="content";
    },

    allowDimensionsFieldset: function(block) {
        return self.isResizableNestedBlock(block) &&
            self.getDimensionsSettings(block).enabled;
    },

    getDimensionsSettings: function(block) {
        return blocks.getBlockMeta(block).dimensions;
    },

    getDimensionsFieldset: function() {
        return composer.panels.fieldset.get("dimensions");
    },

    shouldRespectMinContentSize: function(block) {
        return self.isResizableNestedBlock(block) &&
            self.getDimensionsSettings(block).respectMinContentSize;
    },

    isInherited: function(value) {
        return !value || /auto|inherit/.test(value);
    },

    hasInheritedSize: function(block, prop) {
        var value = block[0].style[prop];
        return self.isInherited(value);
    },

    hasInheritedWidth: function(block) {
        return self.hasInheritedSize(block, "width");
    },

    hasInheritedHeight: function(block) {
        return self.hasInheritedSize(block, "height");
    },

    getSize: function(block, prop) {
        var value = block[0].style[prop];
        return self.isInherited(value) ? block.css(prop) : value;
    },

    getWidth: function(block) {
        return self.getSize(block, "width");
    },

    getHeight: function(block) {
        return self.getSize(block, "height");
    },

    getComputedWidth: function(block) {
        return block.width();
    },

    getComputedHeight: function(block) {

        // var blockViewport = blocks.getBlockViewport(block);
        // value =
        //     (block.height() -
        //     parseInt(blockViewport.css("padding-top")) -
        //     parseInt(blockViewport.css("padding-bottom"))) + "px";

        return block.height();
    },

    setWidth: function(block, width) {
        block.css("width", width);
    },

    setHeight: function(block, height) {
        block.css("height", height);
    },

    updateWidth: function(block, width) {
        self.setWidth(block, width);
        self.populateWidth(block);
    },

    updateHeight: function(block, height) {
        self.setHeight(block, height);
        self.populateHeight(block);
    },

    getFluidWidth: function(block) {
        // Secondary fallback is for isolated blocks (experimental)
        var nest = $(blocks.getBlockNest(block)[0] || block.parent()[0]);
        var width = Math.round(block.width() / nest.width() * 100) + "%";
        return width;
    },

    toFluidWidth: function(block) {

        // Only for nested block
        if (blocks.isRootBlock(block)) return;

        var width = self.getFluidWidth(block);
        self.updateWidth(block, width);
    },

    toFluidHeight: function(block) {
        self.updateHeight(block, "");
    },

    toFixedWidth: function(block, width) {
        self.updateWidth(block, block.width());
    },

    toFixedHeight: function(block, height) {
        self.updateHeight(block, block.height());
    },

    toAutoWidth: function(block) {
        self.updateWidth(block, "auto");
    },

    toAutoHeight: function(block) {
        self.updateHeight(block, "");
    },

    setUnit: function(block, prop, toUnit) {

        // Only applies to width
        if (prop!=="width") return;

        var fromUnit = self.getUnit(block, prop);

        // % to px
        if (fromUnit=="%" && toUnit=="px") {
            self.toFixedWidth(block);
        }

        // px to %
        if (fromUnit=="px" && toUnit=="%") {
            self.toFluidWidth(block);
        }
    },

    getUnit: function(block, prop) {
        return parseUnit(self.getSize(block, prop));
    },

    updateUnit: function(block, prop, unit) {

        self.setUnit(block, prop, unit);

        prop=="width"  && self.populateWidth(block);
        prop=="height" && self.populateHeight(block);
    },

    populate: function(block) {

        // Dimensions fieldset
        var dimensionsFieldset = self.getDimensionsFieldset();

        // Determine dimensions fieldset if allowed or not
        var allowDimensionsFieldset = self.allowDimensionsFieldset(block);

        // Show or hide dimensions fieldset
        dimensionsFieldset.toggle(allowDimensionsFieldset);

        // If dimensions fieldset is allowed, populate width & height.
        if (allowDimensionsFieldset) {
            self.populateWidth(block);
            self.populateHeight(block);
        }
    },

    populateWidth: function(block) {
        var width = self.getWidth(block);
        var checked = !self.hasInheritedWidth(block);
        self.populateField("width", width, checked);
    },

    populateHeight: function(block) {
        var height = self.getHeight(block);
        var checked = !self.hasInheritedHeight(block);
        self.populateField("height", height, checked);
    },

    populateField: function(prop, value, checked) {

        // Get field, number, unit
        var field = self.field().where("name", prop);
        var number = parseFloat(value);
        var unit = parseUnit(value);

        // Field toggle
        self.numsliderToggle.inside(field)
            .prop("checked", checked);

        // Numslider input
        self.numsliderInput.inside(field)
            .data("number", number)
            .val(number);

        // Numslider current unit
        self.numsliderCurrentUnit.inside(field)
            .html(unit);

        // Numslider unit dropdown
        self.numsliderUnit.inside(field)
            .where("unit", '"' + unit + '"')
            .activateClass("active");

        // Store unit data in field
        field.data("unit", unit);

        // Numslider widget
        if (self.resizingFromSlider!==prop) {

            // Pixel unit
            if (unit=="px") {
                var unitOptions = {
                    start: number,
                    step: 1,
                    range: {
                        min: 0,
                        max: 800
                    },
                    pips: {
                        mode: "values",
                        density: 4,
                        values: [0, 200, 400, 600, 800, 1000]
                    }
                }
            }

            // Percent unit
            if (unit=="%") {
                var unitOptions = {
                    start: number,
                    step: 1,
                    range: {
                        min: 0,
                        max: 100
                    },
                    pips: {
                        mode: "positions",
                        values: [0, 20, 40, 60, 80, 100],
                        density: 5
                    }
                }
            }

            self.numsliderWidget.inside(field)
                .find(".noUi-pips")
                    .remove()
                    .end()
                .noUiSlider(unitOptions, true)
                .noUiSlider_pips(unitOptions.pips);
        }
    },

    handleNumsliderWidget: function(numsliderWidget, number) {

        var workarea = self.workarea();

        var field = self.field.of(numsliderWidget);
        var prop  = field.data("name");
        var unit  = field.data("unit");
        var value = Math.round(number) + unit;

        // Declare that we are resizing the slider of this property
        self.resizingFromSlider = prop;

        // Disable transition so width/height changes is instantaneous
        clearTimeout(self.resizingTimer);
        workarea.addClass("is-resizing");

        prop=="width"  && self.updateWidth(currentBlock, value);
        prop=="height" && self.updateHeight(currentBlock, value);

        self.resizingTimer = setTimeout(function(){
            workarea.removeClass("is-resizing");
        }, 15);

        self.resizingFromSlider = null;
    },

    "{numsliderWidget} nouislide": function(numsliderWidget, event, number) {

        self.handleNumsliderWidget(numsliderWidget, number);
    },

    "{numsliderWidget} set": function(numsliderWidget, event, number) {

        self.handleNumsliderWidget(numsliderWidget, number);
    },

    "{numsliderInput} input": function(numsliderInput) {

        // Destroy any blur event handler
        numsliderInput.off("blur.numslider");

        function revertOnBlur(lastValidNumber) {
            numsliderInput
                .on("blur.numslider", function(){
                    numsliderInput.val(lastValidNumber);
                });
        }

        // Get prop
        var field = self.field.of(numsliderInput);
        var prop  = field.data("name");

        // Get number
        var number = $.trim(numsliderInput.val());

        // Get unit
        var numsliderUnit = self.numsliderUnit.inside(field);
        var unit = numsliderUnit.data("unit");

        // Get value
        var value = number + unit;

        if (number==0 || !$.isNumeric(number)) {
            var lastValidNumber = numsliderInput.data("number");
            return revertOnBlur(lastValidNumber);
        }

        prop=="width"  && self.updateWidth(currentBlock, value);
        prop=="height" && self.updateHeight(currentBlock, value);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var field = self.field.of(numsliderUnit);
        var prop  = field.data("name");
        var unit  = numsliderUnit.data("unit");

        self.setUnit(currentBlock, prop, unit);
    },

    "{numsliderToggle} change": function(numsliderToggle) {

        var field = self.field.of(numsliderToggle);
        var prop  = field.data("name");

        // If we're disable font size, remove font size.
        if (!numsliderToggle.is(":checked")) {
            prop=="width"  && self.toAutoWidth(currentBlock);
            prop=="height" && self.toAutoHeight(currentBlock);
        }
    },

    // When a block is resized using resizable
    "{self} composerBlockResize": function(base, event, block) {

        self.populate(block);
    },

    // When a nested block is converted into a root block
    "{self} composerBlockNestOut": function(base, event, block) {

        // Remove width, height from block
        block.css({
            width: "",
            height: ""
        });
    }

}});

module.resolve();

});

})