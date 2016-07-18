(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
 * jquery.colorpicker
 * Simple colorpicker plugin.
 *
 * Copyright (c) 2015 Jensen Tonne
 * http://www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

var hsbPanel_ = ".colorpicker-hsb-panel",
    hPanel_  = ".colorpicker-h-panel",
    sbPanel_ = ".colorpicker-sb-panel",
    hHandle_ = ".colorpicker-h-handle",
    sbHandle_ = ".colorpicker-sb-handle",
    colorPreview_ = ".colorpicker-preview",
    hexInput_ = ".colorpicker-hex-input",
    defaultOptions = {
        color: "#000000",
        document: document
    };

$.fn.colorpicker = function(options) {

    var colorpicker = this.data("colorpicker");

    if (!colorpicker) {
        colorpicker = new Colorpicker(this, options);
    }

    if ($.isString(options)) {
        var method = options;
        return colorpicker[method].apply(colorpicker, $.makeArray(arguments).slice(1));
    }

    return this;
}

var Colorpicker = function(element, options) {

    // Normalize options
    this.options = $.extend({}, defaultOptions, options);

    this.element = element;

    this.init();

    element.data("colorpicker", this);
}

$.extend(Colorpicker.prototype, {

    init: function () {

        this.initHPanel();
        this.initSBPanel();
        this.initHexInput();

        this.silent = true;
        this.setHex(this.options.color);
        this.silent = false;
    },

    initHPanel: function() {

        var self = this;

        // Generate hue colors
        self.generateHueColors();

        // Hue Panel UI
        self.element.on($.ns("mousedown", ".colorpicker.h"), hPanel_, function(event){

            event.preventDefault();

            // Get hue panel & handle
            var hsbPanel = self.hsbPanel(),
                hPanel   = self.hPanel(),
                hHandle  = self.hHandle(),

                // Get hue panel's height & offset top
                offset  = hPanel.offset(),
                offsetY = offset.top,
                height  = hPanel.height(),

                updateH = function(event) {

                    var position = $.getPointerPosition(event),
                        y  = position.y - offsetY,
                        py = y / height;

                    // Always stay within 0 to 1
                    if (py < 0) py = 0; if (py > 1) py = 1;

                    // Update selector indicator position
                    hHandle.css({
                        top: (py * 100) + "%",
                    });

                    var hsb = self.getHsb();
                    self.setHsb(hsb);
                };

            hsbPanel.addClass("adjusting-h");
            updateH(event);

            // When cursor moves, determine new hue handle position
            var $document = $(self.options.document);

            $document
                .on($.ns("mousemove touchmove", ".colorpicker.h"), function(event){

                    event.preventDefault();

                    updateH(event);
                })
                .on($.ns("mouseup touchend", ".colorpicker.h"), function(event){

                    event.preventDefault();

                    hsbPanel.removeClass("adjusting-h");

                    $document.off($.ns("mousemove mouseup touchend", ".colorpicker.h"));
                });
        });
    },

    destroyHPanel: function() {

        this.element.off($.ns("mousedown", ".colorpicker.h"));
    },

    generateHueColors: function() {

        var hPanel = this.hPanel(),
            stops = ['#ff0000','#ff0080','#ff00ff','#8000ff','#0000ff','#0080ff','#00ffff','#00ff80','#00ff00','#80ff00','#ffff00','#ff8000','#ff0000'];

        if ($.IE <= 9) {

            var i, div;

            for (i=0; i<=11; i++) {
                div = $('<div class="colorpicker-hue-stop"></div>').attr('style','height:8.333333%; filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='+stops[i]+', endColorstr='+stops[i+1]+'); -ms-filter: "progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='+stops[i]+', endColorstr='+stops[i+1]+')";');
                hPanel.append(div);
            }

        } else {

            stopList = stops.join(',');

            hPanel.attr('style','background:-webkit-linear-gradient(top,'+stopList+'); background: -o-linear-gradient(top,'+stopList+'); background: -ms-linear-gradient(top,'+stopList+'); background:-moz-linear-gradient(top,'+stopList+'); -webkit-linear-gradient(top,'+stopList+'); background:linear-gradient(to bottom,'+stopList+'); ');
        }
    },

    initSBPanel: function() {

        var self = this;

        // Saturation & Brightness Panel UI
        self.element.on($.ns("mousedown", ".colorpicker.sb"), sbPanel_, function(event){

            // Get saturation & brightness panel & handle
            var hsbPanel = self.hsbPanel(),
                sbPanel  = self.sbPanel(),
                sbHandle = self.sbHandle(),

                // Get saturation & brightness panel's dimension & offset
                offset  = sbPanel.offset(),
                offsetX = offset.left,
                offsetY = offset.top,
                width   = sbPanel.width(),
                height  = sbPanel.height(),

                updateSB = function(event) {

                    var position = $.getPointerPosition(event),
                        x  = position.x - offsetX,
                        y  = position.y - offsetY,
                        px = x / width,
                        py = y / height;

                    // Always stay within 0 to 1
                    if (px < 0) px = 0; if (px > 1) px = 1;
                    if (py < 0) py = 0; if (py > 1) py = 1;

                    // Update saturation & brightness' handle
                    sbHandle.css({
                        top : py * 100 + "%",
                        left: px * 100 + "%"
                    });

                    var hsb = self.getHsb();
                    self.setHsb(hsb);
                };

            hsbPanel.addClass("adjusting-sb");
            updateSB(event);

            var $document = $(self.options.document);

            $document
                .on($.ns("mousemove touchmove", ".colorpicker.sb"), function(event){

                    event.preventDefault();

                    updateSB(event);
                })
                .on($.ns("mouseup touchend", ".colorpicker.sb"), function(){

                    event.preventDefault();

                    hsbPanel.removeClass("adjusting-sb");

                    $document.off($.ns("mousemove mouseup touchend", ".colorpicker.sb"));
                });
        });
    },

    destroySBPanel: function() {

        this.element.off($.ns("mousedown", ".colorpicker.sb"));
    },

    initHexInput: function() {

        var self = this;

        self.element
            .on("input", hexInput_, function(event) {

                var hex = $.fixHex($.trim(self.hexInput().val().replace(/\#/g, "")));
                self.setHex(hex);
            })
            .on("focusin", hexInput_, function(event) {

                self.hexInput().addClass("is-focused");
            })
            .on("focusout", hexInput_, function(event) {

                var hexInput = self.hexInput(),
                    normalizedHex = hexInput.data("value");

                hexInput
                    .removeClass("is-focused")
                    .val(normalizedHex);
            });
    },

    setHsb: function(hsb) {

        var hsbPanel = this.hsbPanel();

        if (!hsbPanel.hasClass("adjusting-h") &&
            !hsbPanel.hasClass("adjusting-sb")) {

            // Hue
            this.hHandle().css({
                top: ((1 - (hsb.h / 360)) * 100) + "%"
            });
        }

        if (!hsbPanel.hasClass("adjusting-sb")) {

            this.sbPanel().css({
                background: '#' + $.hsbToHex({h: hsb.h, s: 100, b: 100})
            });
        }

        // Saturation & Brightness
        this.sbHandle()
            .css({
                left: hsb.s + "%",
                top: (100 - hsb.b) + "%"
            });

        var hex = "#" + $.hsbToHex(hsb),
            hexInput = this.hexInput();

        // Update hex input
        hexInput.data("value", hex);
        !hexInput.hasClass("is-focused") &&
            hexInput.val(hex);

        // Update preview
        this.colorPreview()
            .css("background", hex);

        !this.silent && this.element.trigger("colorpickerChange", hex, hsb);
    },

    getHsb: function() {

        var hHandle = this.hHandle();
            sbHandle = this.sbHandle();

        var hsb = {
            h: 360 * (1 - (parseFloat(hHandle[0].style.top) / 100)),
            s: parseFloat(sbHandle[0].style.left),
            b: 100 - parseFloat(sbHandle[0].style.top)
        }

        return $.fixHsb(hsb);
    },

    setHex: function(hex) {

        var hex = hex.replace(/\#/g, "");
        this.setHsb($.hexToHsb(hex));
    },

    getHex: function() {

        return "#" + $.hsbToHex(this.getHsb());
    },

    setRgb: function(rgb) {

        if ($.isString(rgb)) {

            var parts = rgb.match(/\d+/g);

            rgb = {
                r: parts[0] || 0,
                g: parts[1] || 0,
                b: parts[2] || 0
            };
        }

        this.setHsb($.rgbToHsb(rgb));
    },

    getRgb: function() {

        return $.hsbToRgb(this.getHsb());
    },

    setColor: function(color) {

        if (color.match(/^rgb/i)) {
            this.setRgb(color);
        }

        if (color.match(/^\#/)) {
            this.setHex(color);
        }
    },

    hsbPanel: function() {
        return this.element.find(hsbPanel_);
    },

    hPanel: function() {
        return this.element.find(hPanel_);
    },

    sbPanel: function() {
        return this.element.find(sbPanel_);
    },

    hHandle: function() {
        return this.element.find(hHandle_);
    },

    sbHandle: function() {
        return this.element.find(sbHandle_);
    },

    colorPreview: function() {
        return this.element.find(colorPreview_);
    },

    hexInput: function() {
        return this.element.find(hexInput_);
    },

    widget: function() {
        return this;
    }

});

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD50.module("colorpicker", moduleFactory);

}());