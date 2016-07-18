EasyBlog.module("composer/blocks/text", function($){

var module = this;

EasyBlog.require()
.library(
    "colorpicker",
    "nouislider"
)
.script(
    "layout/elements"
)
.done(function(){

EasyBlog.Controller("Composer.Blocks.Text",
{
    elements: [
        ".eb-composer-fieldset[data-name=text] [data-eb-{font-color-menu|font-family-menu|font-size-menu|font-color-content|font-color-picker|font-family-content|font-size-content|font-color-caption|font-family-caption|font-size-caption|font-family-option|font-format-option}]",
        ".eb-composer-fieldset[data-name=text] [data-eb-{numslider-toggle|numslider-widget|numslider-value|numslider-input|numslider-units|numslider-unit|numslider-current-unit|numslider-unit-toggle}]",
        ".eb-composer-fieldset[data-name=text] [data-eb-{colorpicker|colorpicker-toggle}]",
        "[data-eb-{links|link-item-group|link|link-item|link-preview|link-preview-caption|link-url-field|link-title-field|link-blank-option|link-remove-button}]"
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
        },

        "{textFieldset}": ".eb-composer-fieldset[data-name=text]",
        "{linksFieldset}": ".eb-composer-fieldset[data-name=links]",

        "{fontSizeCheckbox}": "[data-eb-font-size-content] .eb-numslider-toggle input",
        "{fontSizeToggle}": "[data-eb-font-size-content] .eb-numslider-toggle label",

        "{linkBlankOptionField}": ".eb-link-blank-option",

    }, EBD.selectors)
},
function(self, opts, base, composer, blocks, editor, iframe, iframeDocument, iframeWindow, isEditingSelection) { return {

    init: function() {

        blocks = self.blocks;
        composer = blocks.composer;

        self.textFieldset()
            .on("touchstart click mousedown mouseup", function(event){
                // Prevent caret from losing focus
                event.preventDefault();
            });

        self.linksFieldset()
            .on("touchstart click mousedown mouseup", function(event){
                // Prevent caret from losing focus
                event.preventDefault();
            });
    },

    "{self} composerDocumentReady": function() {

        editor = composer.editor;

        self.initFontColor();
        self.initLinks();
        self.initHandlers();
    },

    initHandlers: function() {

        // $.each(self.handlers, function(handlerName, handlerFunc){

        //     var parts = handlerName.match(/^\{(.+)\} (.+)/),
        //         eventTarget = parts[1],
        //         eventName   = parts[2],
        //         selector = self[eventTarget].selector;

        //     // Bind to iframe document
        //     $(iframeDocument)
        //         .on(eventName, selector, function(event){
        //             handlerFunc.apply(self, [$(this)].concat($.makeArray(arguments)));
        //         });
        // });
    },

    "{self} composerBlockActivate": function(base, event, block) {

        var meta = blocks.getBlockMeta(block);

        if (!meta.properties.fonts) {
            self.textFieldset().hide();
            self.linksFieldset().hide();
            return;
        }
    },

    "{self} composerTextSelect": function(base, event, selection, block, editor) {

        // Get the block meta and see if it should display the text panel
        var meta = blocks.getBlockMeta(block);

        if (!meta.properties.fonts) {
            self.textFieldset().hide();
            self.linksFieldset().hide();
            return;
        }

        if (isEditingSelection) return;

        // Show text fieldset
        blocks.panel.blockSubpanel().addClass("has-text-selection");
        self.textFieldset().show();
        self.linksFieldset().show();

        // Toggle list formatting
        self.textFieldset()
            .find(".eb-font-formatting.section-list")
            .toggle(block.data("type")=="text");

        // Populate fieldsets
        self.populateFont();
        self.populateLinks();
    },

    "{self} composerTextDeselect": function(base, event, editor) {

        if (isEditingSelection) return;

        // If user is sorting, don't do anything.
        if (self.workarea().hasClass("is-sorting")) return;

        // Show text fieldset
        blocks.panel.blockSubpanel().removeClass("has-text-selection");
        self.textFieldset().hide();
        self.linksFieldset().hide();

        // Remove text markers
        self.removeTextMarkers();

        // Clear out link items
        self.linkItemGroup().empty();
    },

    populateFont: function() {

        self.populateFontColor();
        self.populateFontFamily();
        self.populateFontSize();
        self.populateFontFormatting();
    },

    //
    // Font Color API
    //

    initFontColor: function() {

        self.fontColorPicker()
            .colorpicker();
    },

    populateFontColor: function() {

        if (self.lastUpdatedViaFontColorUI) {
            self.lastUpdatedViaFontColorUI = false;
            return;
        }

        var parent = editor.selection.getParent(),
            fontColor = $(parent).css("color");

        // Determine if this has color override by determining
        // if the style's color attribute has an actual value.
        self.colorpickerToggle().prop("checked", parent.style.color);

        self.updateFontColorUI(fontColor);
    },

    setFontColor: function(fontColor) {

        // Remove font color
        // self.removeFontColor();

        self.lastUpdatedViaFontColorUI = true;

        // Update text font color
        editor.inline.toggleStyle("color: " + fontColor);

        // Update font color UI
        self.updateFontColorUI(fontColor);

    },

    updateFontColorUI: function(fontColor) {

        self.updatingFontColorUI = true;

        // Fallback to black if no font color given
        !fontColor && (fontColor = "#000");

        // Update color preview
        self.fontColorCaption()
            .css("backgroundColor", fontColor);

        self.fontColorPicker()
            .colorpicker("setColor", fontColor);

        self.updatingFontColorUI = false;
    },

    removeFontColor: function() {

        editor.inline.removeStyleRule("color");
    },

    //
    // Font Family API
    //

    populateFontFamily: function() {

        if (self.lastUpdatedViaFontFamilyUI) {
            self.lastUpdatedViaFontFamilyUI = false;
            return;
        }

        var parent = editor.selection.getParent(),
            fontFamily = parent.style.fontFamily.replace(/\'\"/g, "");

        // Update the font family
        self.updateFontFamilyUI(fontFamily);
    },

    setFontFamily: function(fontFamily) {

        // Set font family on text selection
        self.lastUpdatedViaFontFamilyUI = true;
        editor.inline.format("span", "style", "font-family: " + fontFamily);

        // Update font family UI
        self.updateFontFamilyUI(fontFamily);
    },

    updateFontFamilyUI: function(fontFamily) {

        // If no font family given, use empty string.
        !fontFamily && (fontFamily = "");

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

    removeFontFamily: function() {

        self.lastUpdatedViaFontFamilyUI = true;
        editor.inline.removeStyleRule("font-family");
    },

    //
    // Font Size API
    //

    populateFontSize: function() {

        if (self.lastUpdatedViaFontSizeUI) {
            self.lastUpdatedViaFontSizeUI = false;
            return;
        }

        var parent = editor.selection.getParent(),
            fontSize = $(parent).css("fontSize"),
            hasFontSize = !!parent.style.fontSize;

        // Update the fontsize
        self.updateFontSizeUI(fontSize);

        self.numsliderToggle().prop("checked", !!hasFontSize);
    },

    setFontSize: function(fontSize) {

        // If number is given, add a unit.
        if ($.isNumeric(fontSize)) {
            var unit = self.getFontSizeUnit();
            fontSize = fontSize + unit;
        }

        // Update block font size
        self.lastUpdatedViaFontSizeUI = true;
        editor.inline.format("span", "style", "font-size: " + fontSize);

        // Get fallback fontsize
        var parent = editor.selection.getParent(),
            fallbackFontSize = $(parent).css("fontSize");

        self.updateFontSizeUI(fontSize || fallbackFontSize);

        self.numsliderToggle().prop("checked", !!fontSize);
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

    removeFontSize: function() {

        editor.inline.removeStyleRule("font-size");
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
            .noUiSlider($.extend({document: iframeDocument}, unitOptions), true)
            .noUiSlider_pips(unitOptions.pips);
    },

    //
    // Font Formatting API
    //

    formattingTags: {
        bold: "strong",
        italic: "em",
        underline: "u",
        strikethrough: "del",
        code: "code",
        superscript: "sup",
        subscript: "sub",
        orderedlist: "ol",
        unorderedlist: "ul"
    },

    populateFontFormatting: function() {

        if (self.lastUpdatedViaFontFormattingUI) {
            self.lastUpdatedViaFontFormattingUI = false;
        } else {
            self.removeTextMarkers();
        }

        var current = editor.selection.getCurrent();
        var list = $(current).parentsUntil(EBD.block).filter("ul, ol").eq(0);

        self.fontFormatOption().each(function(){

            var fontFormatOption = $(this),
                format = fontFormatOption.data("format"),
                formatTag = self.formattingTags[format];

            if (!/unorderedlist|orderedlist/.test(format)) {
                hasFormatting = $(current).closest(formatTag).length !== 0;
            } else {
                hasFormatting = list.is(formatTag);
            }

            fontFormatOption.toggleClass("active", hasFormatting);
        });
    },

    toggleFontFormatting: function(format) {

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

            case "clear":
                self.selectWithinTextMarkers();
                editor.inline.removeFormat();
                self.removeTextMarkers();
                break;

            default:
                // Create text markers
                !self.hasTextMarkers() && self.createTextMarkers();
                editor.inline.format(format);
                self.lastUpdatedViaFontFormattingUI = true;
                break;
        }
    },

    //
    // Text Marker API
    //

    hasTextMarkers: function() {

        return self.workarea().find(".composer-text-marker").length > 0;
    },

    createTextMarkers: function() {

        // Remove existing markers
        editor.selection.removeMarkers();

        // Create new markers
        editor.selection.createMarkers();

        // Create text marker
        var markers =
            $.makeArray(
                self.workarea()
                    .find(".redactor-selection-marker")
                    .each(function(){
                        $(this)
                            .removeClass("redactor-selection-marker")
                            .addClass("composer-text-marker")
                            .attr("id", this.id.replace("selection-", "text-"));
                    })
                );

        // Select within text markers
        self.selectWithinTextMarkers(markers);

        return markers;
    },

    getTextMarkers: function() {

        var markers =
            $.makeArray(
                    self.workarea()
                        .find("span#text-marker-1, span#text-marker-2")
                );

        if (markers[0] && markers[1]) return markers;
    },

    selectWithinTextMarkers: function(markers) {

        // Get markers
        markers || (markers = self.getTextMarkers());

        // Adjust selection to cover only contents within the marker
        markers && editor.caret.set(markers[0].nextSibling, 0, markers[1], 0);
    },

    removeTextMarkers: function() {

        self.workarea()
            .find(".composer-text-marker")
            .remove();
    },

    startEditingSelection: function() {

        isEditingSelection = true;

        // Remove text markers
        self.removeTextMarkers();

        // When user clicks anywhere in the workarea
        self.workarea()
            .off("mousedown.stopEditingSelection")
            .one("mousedown.stopEditingSelection", function(){

                // Stop editing selection
                self.stopEditingSelection();
            });
    },

    stopEditingSelection: function() {

        isEditingSelection = false;
    },

    //
    // Links API
    //

    initLinks: function() {

        // Keep a copy of the template
        self.linkItem.template =
             self.linkItem(".is-blank")
                .detach()
                .removeClass("is-blank")[0];
    },

    populateLinks: function() {

        // Get all nodes in text selection
        var nodes = editor.selection.getNodesInRange();

        // Find all anchor nodes within it
        var anchorNodes = $(nodes).filter("a");

        // Generate link item for existing anchor nodes
        var linkItemGroup = self.linkItemGroup().empty();

        $.each(anchorNodes, function(i, anchorNode){

            // Generate link item
            var linkItem = self.createLinkItem()
                                .appendTo(linkItemGroup);

            // Process link item
            self.processLinkItem(linkItem);

            // Update link item
            self.updateLinkItem(linkItem, anchorNode);
        });

        // Add .has-existing-links class on link item group if necessary
        self.linkItemGroup()
            .toggleClass("has-existing-links", anchorNodes.length > 0);

        // Create blank link item
        var linkItem =
                self.createLinkItem()
                    .addClass("is-new")
                    .appendTo(linkItemGroup);

        // Process link item
        self.processLinkItem(linkItem);

        // Get link caption from text selection
        var linkCaption = editor.selection.getText();

        // Set link caption on link preview
        self.linkPreviewCaption.inside(linkItem)
            .html(linkCaption);
    },

    createLinkItem: function() {

        var linkItem = $(self.linkItem.template).clone();

        return linkItem;
    },

    processLinkItem: function(linkItem) {

        // Get link url & title
        var linkUrlField = self.linkUrlField.inside(linkItem);
        var linkTitleField = self.linkTitleField.inside(linkItem);

        // Keep a reference to this element
        linkItem.data("linkUrlField", linkUrlField[0]);
        linkItem.data("linkTitleField", linkTitleField[0]);

        linkUrlField
            .data("linkItem", linkItem)
            .on("input", self.linkUrlFieldInputHandler)
            .on("mousedown", function(){
                if ($.IE) {
                    var anchorNode = linkItem.data("anchorNode");
                    if (!anchorNode) {
                       editor.selection.save();
                    }
                }
            });

        linkTitleField
            .data("linkItem", linkItem)
            .on("input", self.linkTitleFieldInputHandler)
            .on("mousedown", function(){
                if ($.IE) {
                    var anchorNode = linkItem.data("anchorNode");
                    if (!anchorNode) {
                       editor.selection.save();
                    }
                }
            });

        // Link input
        var linkInput = linkItem.find(".eb-link-input");

        linkUrlField.css({
            padding: "6px 12px",
            display: "block",
            fontSize: "13px",
            lineHeight: "24px",
            fontWeight: "bold",
            paddingBottom: "0px",
            color: "#555555",
            width: "100%",
            border: "none",
            outline: "none",
            position: "relative",
            zIndex: 2
        });

        if ($.IE) {
            linkUrlField.css({height: "28px"});
        }

        linkTitleField.css({
            border: "none",
            resize: "none",
            fontSize: "12px",
            padding: "12px",
            paddingTop: "32px",
            overflow: "hidden",
            outline: "none",
            position: "absolute",
            width: "100%",
            height: "100%",
            color: "#555555",
            fontFamily: "Arial, Helvetica, sans-serif",
            top: 0,
            left: 0,
            zIndex: 1
        });

        try {

            // Link Iframe
            var linkIframe = $("<iframe>");

            linkIframe.on("load", function(){

                $(linkIframe[0].contentWindow.document.body)
                    .css({
                        margin: 0,
                        overflow: "hidden"
                    })
                    .append(linkUrlField)
                    .append(linkTitleField);
            });

            linkInput.append(linkIframe);

        } catch(e) {
            console.error("There may a cross-iframe security issue. Unable to create text link item properly", e);
        }
    },

    updateLinkItem: function(linkItem, anchorNode)  {

        // Update link preview caption
        self.linkPreviewCaption.inside(linkItem)
            .html($(anchorNode).text());

        // Update link url field
        $(linkItem.data("linkUrlField"))
            .val(anchorNode.getAttribute("href"));

        // Update link title field
        $(linkItem.data("linkTitleField"))
            .val(anchorNode.title);

        // Update link blank option
        self.linkBlankOption.inside(linkItem)
            .prop("checked", anchorNode.target=="_blank");

        // Keep a reference to the <a> tag
        linkItem.data("anchorNode", anchorNode);
    },

    removeLinkItem: function(linkItem) {

        // Remove <a> associated to this link
        self.removeAnchorNode(linkItem);

        // If link item is new, just clear link fields.
        if (linkItem.hasClass("is-new")) {
            self.resetLinkItem(linkItem);
            return;
        }

        // Remove link item
        linkItem.remove();

        // If there are no more existing link item,
        // remove .has-existing-links
        if (self.linkItem(":not(.is-new)").length < 1) {
            self.linkItemGroup().removeClass("has-existing-links");
        }
    },

    resetLinkItem: function(linkItem) {

        // Clear link url field
        self.linkUrlField.inside(linkItem)
            .val("");

        // Clear link title field
        self.linkTitleField.inside(linkItem)
            .val("");

        // Uncheck link blank option
        self.linkBlankOption.inside(linkItem)
            .prop("checked", false);
    },

    createAnchorNode: function(linkItem) {

        // Get html of text selection
        var html = editor.selection.getHtml(),

            // Generate a temporary id for this <a> tag
            id = $.uid("link-"),

            // Create <a> tag with html of the text selection
            anchorNode = $("<a />").attr("id", id).html(html)[0];

        // Insert <a> tag into the editor.
        // Note: This <a> tag will also replace the existing text selection.
        editor.insert.node(anchorNode);

        // After inserting it seems we have lost reference to the <a> tag,
        // so we'll find it back again.
        anchorNode = $("#" + id).removeAttr("id")[0];

        // Update editor's text selection to select the <a> tag.
        editor.selection.selectElement(anchorNode);

        // Remove .is-new class from link item,
        // and store a reference to the <a> tag.
        linkItem
            .data("anchorNode", anchorNode);

        return anchorNode;
    },

    updateAnchorNode: function(linkItem) {

        // Get <a> tag associated to this link item.
        var anchorNode = linkItem.data("anchorNode");

        // Skip if no <a> tag associated to this link item.
        if (!anchorNode) return;

        var urlField    = $(linkItem.data("linkUrlField")),
            titleField  = $(linkItem.data("linkTitleField")),
            blankOption = self.linkBlankOption.inside(linkItem);

        // Set href & title attribute
        anchorNode.href  = $.trim(urlField.val());
        anchorNode.title = titleField.val();

        // Set target attribute.
        if (blankOption.is(":checked")) {
            anchorNode.target = "_blank";
        } else {
            anchorNode.removeAttribute("target");
        }
    },

    removeAnchorNode: function(linkItem) {

        // Get <a> tag associated to this link item.
        var anchorNode = linkItem.data("anchorNode");

        // Skip if no <a> associated to this link item.
        if (!anchorNode) return;

        // Save selection
        editor.selection.save();

        $anchorNode = $(anchorNode);

        // Take contents of <a> tag out and insert after it
        $anchorNode.contents()
            .insertAfter($anchorNode);

        // Remove <a> tag
        $anchorNode.remove();

        // Restore selection
        editor.selection.restore();

        // Remove association from link item
        linkItem.removeData("anchorNode");
    },

    //
    // Font Formatting UI
    "{fontFormatOption} click": function(fontFormatOption) {

        var format = fontFormatOption.data("format");

        if (format != 'clear') {
            fontFormatOption.toggleClass('active');
        }

        self.toggleFontFormatting(format);
    },

    //
    // Font Family UI
    //
    "{fontFamilyOption} click": function(fontFamilyOption) {

        var fontFamily = fontFamilyOption.data("value");

        self.setFontFamily(fontFamily);
    },

    //
    // Font Color UI
    //

    "{colorpicker} colorpickerChange": function(colorpicker, event, fontColor) {

        if (self.updatingFontColorUI) return;

        self.colorpickerToggle().prop("checked", true);

        self.setFontColor(fontColor);
    },

    "{colorpickerToggle} change": function(colorpickerToggle) {

        // If we're disable font color, remove font color.
        if (!colorpickerToggle.checked()) {
            self.removeFontColor();
        }
    },

    //
    // Font Size UI
    //
    "{fontSizeToggle} click": function(fontSizeToggle) {

        var fontSizeCheckbox = self.fontSizeCheckbox();

        if (fontSizeCheckbox.is(":checked")) {

            fontSizeCheckbox.prop("checked", false);

            // If we're disabling font size, remove font size.
            self.removeFontSize();
        } else {

            fontSizeCheckbox.prop("checked", true);
        }
    },

    "{numsliderWidget} nouislide": function(numsliderWidget, event, value) {

        // Sliding only updates input
        self.numsliderInput()
            .val(Math.abs(value));
    },

    "{numsliderWidget} set": function(numsliderWidget, event, value) {

        if (self.updatingFontSizeUI) return;

        self.setFontSize(Math.abs(value));
    },

    "{numsliderInput} input": function(numsliderInput) {

        if (self.updatingFontSizeUI) return;

        var fontSize = Math.abs($.trim(numsliderInput.val()));

        self.setFontSize(fontSize);
    },

    "{numsliderUnit} click": function(numsliderUnit) {

        var unit = numsliderUnit.data("unit");

        self.setFontSizeUnit(unit);

        self.numsliderUnits().removeClass("open");
    },

    "{numsliderUnitToggle} click": function(numsliderUnitToggle) {

        self.numsliderUnits().toggleClass("open");
    },

    //
    // Link UI
    //

    "{linkItem} mouseover": function(linkItem, event) {

        var anchorNode = linkItem.data("anchorNode");

        if (!anchorNode) return;

        $(anchorNode).addClass("is-highlighting");
    },

    "{linkItem} mouseout": function(linkItem, event) {

        var anchorNode = linkItem.data("anchorNode");

        if (!anchorNode) return;

        $(anchorNode).removeClass("is-highlighting");
    },

    linkUrlFieldInputHandler: function(event) {

        var linkUrlField = $(this);

        // Start editing selection
        self.startEditingSelection();

        // Get link item
        var linkItem = $(linkUrlField.data("linkItem")),

            // Get link href
            href = $.trim(linkUrlField.val()),

            // Get <a> tag
            anchorNode = linkItem.data("anchorNode");

        // If this is the first time we are creating this link,
        if (href!=="" && !anchorNode) {

            if ($.IE) {
                // Restore selection
                editor.selection.restore();
            }

            // wrap text selection in <a> tag.
            anchorNode = self.createAnchorNode(linkItem);

            if ($.IE) {
                // Focus back on link url field
                linkUrlField.focus().val(linkUrlField.val());
            }
        }

        // If we are removing this link,
        if (href=="" && anchorNode) {
            // unwrap <a> tag from text selection.
            self.removeAnchorNode(linkItem);
            return;
        }

        // Update <a> tag with the appropriate values.
        self.updateAnchorNode(linkItem);
    },

    linkTitleFieldInputHandler: function(event) {

        var linkTitleField = $(this);

        // Start editing selection
        self.startEditingSelection();

        // Get link item
        var linkItem = $(linkTitleField).data("linkItem");

        // Update <a> tag with the appropriate values.
        self.updateAnchorNode(linkItem);
    },

    "{linkBlankOptionField} click": function(linkBlankOptionField, event) {

        // We'll have to do this because prevent default is in place.
        var linkBlockOption = linkBlankOptionField.find("input");
        if (linkBlockOption.is(":checked")) {
            linkBlockOption.prop("checked", false);
        } else {
            linkBlockOption.prop("checked", true);
        }
        // Start editing selection
        self.startEditingSelection();

        // Get link item
        var linkItem = self.linkItem.of(linkBlankOptionField);

        // Update <a> tag with the appropriate values.
        self.updateAnchorNode(linkItem);
    },

    "{linkRemoveButton} click": function(linkRemoveButton, event) {

        // Start editing selection
        self.startEditingSelection();

        var linkItem = self.linkItem.of(linkRemoveButton);

        self.removeLinkItem(linkItem);
    }

}});


module.resolve();

});

});
