(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

<<<<<<< mine
// ONE COLOR STARTS
(function(){

/*jshint evil:true, onevar:false*/
/*global define*/
var installedColorSpaces = [],
    namedColors = {},
    undef = function (obj) {
        return typeof obj === 'undefined';
    },
    channelRegExp = /\s*(\.\d+|\d+(?:\.\d+)?)(%)?\s*/,
    alphaChannelRegExp = /\s*(\.\d+|\d+(?:\.\d+)?)\s*/,
    cssColorRegExp = new RegExp(
                         "^(rgb|hsl|hsv)a?" +
                         "\\(" +
                             channelRegExp.source + "," +
                             channelRegExp.source + "," +
                             channelRegExp.source +
                             "(?:," + alphaChannelRegExp.source + ")?" +
                         "\\)$", "i");

function ONECOLOR(obj) {
    if (Object.prototype.toString.apply(obj) === '[object Array]') {
        if (typeof obj[0] === 'string' && typeof ONECOLOR[obj[0]] === 'function') {
            // Assumed array from .toJSON()
            return new ONECOLOR[obj[0]](obj.slice(1, obj.length));
        } else if (obj.length === 4) {
            // Assumed 4 element int RGB array from canvas with all channels [0;255]
            return new ONECOLOR.RGB(obj[0] / 255, obj[1] / 255, obj[2] / 255, obj[3] / 255);
        }
    } else if (typeof obj === 'string') {
        var lowerCased = obj.toLowerCase();
        if (namedColors[lowerCased]) {
            obj = '#' + namedColors[lowerCased];
        }
        if (lowerCased === 'transparent') {
            obj = 'rgba(0,0,0,0)';
        }
        // Test for CSS rgb(....) string
        var matchCssSyntax = obj.match(cssColorRegExp);
        if (matchCssSyntax) {
            var colorSpaceName = matchCssSyntax[1].toUpperCase(),
                alpha = undef(matchCssSyntax[8]) ? matchCssSyntax[8] : parseFloat(matchCssSyntax[8]),
                hasHue = colorSpaceName[0] === 'H',
                firstChannelDivisor = matchCssSyntax[3] ? 100 : (hasHue ? 360 : 255),
                secondChannelDivisor = (matchCssSyntax[5] || hasHue) ? 100 : 255,
                thirdChannelDivisor = (matchCssSyntax[7] || hasHue) ? 100 : 255;
            if (undef(ONECOLOR[colorSpaceName])) {
                throw new Error("one.color." + colorSpaceName + " is not installed.");
            }
            return new ONECOLOR[colorSpaceName](
                parseFloat(matchCssSyntax[2]) / firstChannelDivisor,
                parseFloat(matchCssSyntax[4]) / secondChannelDivisor,
                parseFloat(matchCssSyntax[6]) / thirdChannelDivisor,
                alpha
            );
        }
        // Assume hex syntax
        if (obj.length < 6) {
            // Allow CSS shorthand
            obj = obj.replace(/^#?([0-9a-f])([0-9a-f])([0-9a-f])$/i, '$1$1$2$2$3$3');
        }
        // Split obj into red, green, and blue components
        var hexMatch = obj.match(/^#?([0-9a-f][0-9a-f])([0-9a-f][0-9a-f])([0-9a-f][0-9a-f])$/i);
        if (hexMatch) {
            return new ONECOLOR.RGB(
                parseInt(hexMatch[1], 16) / 255,
                parseInt(hexMatch[2], 16) / 255,
                parseInt(hexMatch[3], 16) / 255
            );
        }
    } else if (typeof obj === 'object' && obj.isColor) {
        return obj;
    }
    return false;
}

function installColorSpace(colorSpaceName, propertyNames, config) {
    ONECOLOR[colorSpaceName] = new Function(propertyNames.join(","),
        // Allow passing an array to the constructor:
        "if (Object.prototype.toString.apply(" + propertyNames[0] + ") === '[object Array]') {" +
            propertyNames.map(function (propertyName, i) {
                return propertyName + "=" + propertyNames[0] + "[" + i + "];";
            }).reverse().join("") +
        "}" +
        "if (" + propertyNames.filter(function (propertyName) {
            return propertyName !== 'alpha';
        }).map(function (propertyName) {
            return "isNaN(" + propertyName + ")";
        }).join("||") + "){" + "throw new Error(\"[" + colorSpaceName + "]: Invalid color: (\"+" + propertyNames.join("+\",\"+") + "+\")\");}" +
        propertyNames.map(function (propertyName) {
            if (propertyName === 'hue') {
                return "this._hue=hue<0?hue-Math.floor(hue):hue%1"; // Wrap
            } else if (propertyName === 'alpha') {
                return "this._alpha=(isNaN(alpha)||alpha>1)?1:(alpha<0?0:alpha);";
            } else {
                return "this._" + propertyName + "=" + propertyName + "<0?0:(" + propertyName + ">1?1:" + propertyName + ")";
            }
        }).join(";") + ";"
    );
    ONECOLOR[colorSpaceName].propertyNames = propertyNames;

    var prototype = ONECOLOR[colorSpaceName].prototype;

    ['valueOf', 'hex', 'hexa', 'css', 'cssa'].forEach(function (methodName) {
        prototype[methodName] = prototype[methodName] || (colorSpaceName === 'RGB' ? prototype.hex : new Function("return this.rgb()." + methodName + "();"));
    });

    prototype.isColor = true;

    prototype.equals = function (otherColor, epsilon) {
        if (undef(epsilon)) {
            epsilon = 1e-10;
        }

        otherColor = otherColor[colorSpaceName.toLowerCase()]();
=======
(function(root, factory) {
    if(typeof exports === 'object') {
        module.exports = factory(require('onecolor'));
    }
    else if(typeof define === 'function' && define.amd) {
        define(['onecolor'], factory);
    }
    else {
        root.colorjoe = factory(root.one.color);
    }
}(this, function(onecolor) {
/*! colorjoe - v0.9.7 - Juho Vepsalainen <bebraw@gmail.com> - MIT
https://bebraw.github.com/colorjoe - 2014-12-07 */
/*! dragjs - v0.4.0 - Juho Vepsalainen <bebraw@gmail.com> - MIT
https://bebraw.github.com/dragjs - 2013-07-17 */
var drag = (function() {
    function drag(elem, cbs) {
        if(!elem) {
            console.warn('drag is missing elem!');
            return;
        }
>>>>>>> theirs

<<<<<<< mine
        for (var i = 0; i < propertyNames.length; i = i + 1) {
            if (Math.abs(this['_' + propertyNames[i]] - otherColor['_' + propertyNames[i]]) > epsilon) {
                return false;
            }
        }

        return true;
    };
=======
        if(isTouch()) dragTemplate(elem, cbs, 'touchstart', 'touchmove', 'touchend');
        else dragTemplate(elem, cbs, 'mousedown', 'mousemove', 'mouseup');
    }
>>>>>>> theirs

<<<<<<< mine
    prototype.toJSON = new Function(
        "return ['" + colorSpaceName + "', " +
            propertyNames.map(function (propertyName) {
                return "this._" + propertyName;
            }, this).join(", ") +
        "];"
    );

    for (var propertyName in config) {
        if (config.hasOwnProperty(propertyName)) {
            var matchFromColorSpace = propertyName.match(/^from(.*)$/);
            if (matchFromColorSpace) {
                ONECOLOR[matchFromColorSpace[1].toUpperCase()].prototype[colorSpaceName.toLowerCase()] = config[propertyName];
            } else {
                prototype[propertyName] = config[propertyName];
            }
        }
=======
    function xyslider(o) {
        var twod = div(o['class'] || '', o.parent);
        var pointer = div('pointer', twod);
        div('shape shape1', pointer);
        div('shape shape2', pointer);
        div('bg bg1', twod);
        div('bg bg2', twod);

        drag(twod, attachPointer(o.cbs, pointer));

        return {
            background: twod,
            pointer: pointer
        };
>>>>>>> theirs
    }

<<<<<<< mine
    // It is pretty easy to implement the conversion to the same color space:
    prototype[colorSpaceName.toLowerCase()] = function () {
        return this;
    };
    prototype.toString = new Function("return \"[one.color." + colorSpaceName + ":\"+" + propertyNames.map(function (propertyName, i) {
        return "\" " + propertyNames[i] + "=\"+this._" + propertyName;
    }).join("+") + "+\"]\";");

    // Generate getters and setters
    propertyNames.forEach(function (propertyName, i) {
        prototype[propertyName] = prototype[propertyName === 'black' ? 'k' : propertyName[0]] = new Function("value", "isDelta",
            // Simple getter mode: color.red()
            "if (typeof value === 'undefined') {" +
                "return this._" + propertyName + ";" +
            "}" +
            // Adjuster: color.red(+.2, true)
            "if (isDelta) {" +
                "return new this.constructor(" + propertyNames.map(function (otherPropertyName, i) {
                    return "this._" + otherPropertyName + (propertyName === otherPropertyName ? "+value" : "");
                }).join(", ") + ");" +
            "}" +
            // Setter: color.red(.2);
            "return new this.constructor(" + propertyNames.map(function (otherPropertyName, i) {
                return propertyName === otherPropertyName ? "value" : "this._" + otherPropertyName;
            }).join(", ") + ");");
    });

    function installForeignMethods(targetColorSpaceName, sourceColorSpaceName) {
        var obj = {};
        obj[sourceColorSpaceName.toLowerCase()] = new Function("return this.rgb()." + sourceColorSpaceName.toLowerCase() + "();"); // Fallback
        ONECOLOR[sourceColorSpaceName].propertyNames.forEach(function (propertyName, i) {
            obj[propertyName] = obj[propertyName === 'black' ? 'k' : propertyName[0]] = new Function("value", "isDelta", "return this." + sourceColorSpaceName.toLowerCase() + "()." + propertyName + "(value, isDelta);");
        });
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop) && ONECOLOR[targetColorSpaceName].prototype[prop] === undefined) {
                ONECOLOR[targetColorSpaceName].prototype[prop] = obj[prop];
            }
        }
=======
    function slider(o) {
        var oned = div(o['class'], o.parent);
        var pointer = div('pointer', oned);
        div('shape', pointer);
        div('bg', oned);

        drag(oned, attachPointer(o.cbs, pointer));

        return {
            background: oned,
            pointer: pointer
        };
>>>>>>> theirs
    }

<<<<<<< mine
    installedColorSpaces.forEach(function (otherColorSpaceName) {
        installForeignMethods(colorSpaceName, otherColorSpaceName);
        installForeignMethods(otherColorSpaceName, colorSpaceName);
    });

    installedColorSpaces.push(colorSpaceName);
}
=======
    drag.xyslider = xyslider;
    drag.slider = slider;
>>>>>>> theirs

<<<<<<< mine
ONECOLOR.installMethod = function (name, fn) {
    installedColorSpaces.forEach(function (colorSpace) {
        ONECOLOR[colorSpace].prototype[name] = fn;
    });
};

installColorSpace('RGB', ['red', 'green', 'blue', 'alpha'], {
    hex: function () {
        var hexString = (Math.round(255 * this._red) * 0x10000 + Math.round(255 * this._green) * 0x100 + Math.round(255 * this._blue)).toString(16);
        return '#' + ('00000'.substr(0, 6 - hexString.length)) + hexString;
    },

    hexa: function () {
        var alphaString = Math.round(this._alpha * 255).toString(16);
        return '#' + '00'.substr(0, 2 - alphaString.length) + alphaString + this.hex().substr(1, 6);
    },

    css: function () {
        return "rgb(" + Math.round(255 * this._red) + "," + Math.round(255 * this._green) + "," + Math.round(255 * this._blue) + ")";
    },
=======
    return drag;
>>>>>>> theirs

<<<<<<< mine
    cssa: function () {
        return "rgba(" + Math.round(255 * this._red) + "," + Math.round(255 * this._green) + "," + Math.round(255 * this._blue) + "," + this._alpha + ")";
    }
});
=======
    function attachPointer(cbs, pointer) {
        var ret = {};
>>>>>>> theirs

<<<<<<< mine
// if (typeof define === 'function' && !undef(define.amd)) {
//     define(function () {
//         return ONECOLOR;
//     });
// } else if (typeof exports === 'object') {
//     // Node module export
//     module.exports = ONECOLOR;
// } else {
//     one = window.one || {};
//     one.color = ONECOLOR;
// }

$.color = ONECOLOR;

/*global one*/

installColorSpace('HSV', ['hue', 'saturation', 'value', 'alpha'], {
    rgb: function () {
        var hue = this._hue,
            saturation = this._saturation,
            value = this._value,
            i = Math.min(5, Math.floor(hue * 6)),
            f = hue * 6 - i,
            p = value * (1 - saturation),
            q = value * (1 - f * saturation),
            t = value * (1 - (1 - f) * saturation),
            red,
            green,
            blue;
        switch (i) {
        case 0:
            red = value;
            green = t;
            blue = p;
            break;
        case 1:
            red = q;
            green = value;
            blue = p;
            break;
        case 2:
            red = p;
            green = value;
            blue = t;
            break;
        case 3:
            red = p;
            green = q;
            blue = value;
            break;
        case 4:
            red = t;
            green = p;
            blue = value;
            break;
        case 5:
            red = value;
            green = p;
            blue = q;
            break;
        }
        return new ONECOLOR.RGB(red, green, blue, this._alpha);
    },
=======
        for(var n in cbs) ret[n] = wrap(cbs[n]);
>>>>>>> theirs

<<<<<<< mine
    hsl: function () {
        var l = (2 - this._saturation) * this._value,
            sv = this._saturation * this._value,
            svDivisor = l <= 1 ? l : (2 - l),
            saturation;

        // Avoid division by zero when lightness approaches zero:
        if (svDivisor < 1e-9) {
            saturation = 0;
        } else {
            saturation = sv / svDivisor;
        }
        return new ONECOLOR.HSL(this._hue, saturation, l / 2, this._alpha);
    },
=======
        function wrap(fn) {
            return function(p) {
                p.pointer = pointer;
                fn(p);
            };
        }
>>>>>>> theirs

<<<<<<< mine
    fromRgb: function () { // Becomes one.color.RGB.prototype.hsv
        var red = this._red,
            green = this._green,
            blue = this._blue,
            max = Math.max(red, green, blue),
            min = Math.min(red, green, blue),
            delta = max - min,
            hue,
            saturation = (max === 0) ? 0 : (delta / max),
            value = max;
        if (delta === 0) {
            hue = 0;
        } else {
            switch (max) {
            case red:
                hue = (green - blue) / delta / 6 + (green < blue ? 1 : 0);
                break;
            case green:
                hue = (blue - red) / delta / 6 + 1 / 3;
                break;
            case blue:
                hue = (red - green) / delta / 6 + 2 / 3;
                break;
            }
        }
        return new ONECOLOR.HSV(hue, saturation, value, this._alpha);
    }
});

/*global one*/
=======
        return ret;
    }
>>>>>>> theirs

<<<<<<< mine
=======
    // move to elemutils lib?
    function div(klass, p) {
        return e('div', klass, p);
    }
>>>>>>> theirs

<<<<<<< mine
installColorSpace('HSL', ['hue', 'saturation', 'lightness', 'alpha'], {
    hsv: function () {
        // Algorithm adapted from http://wiki.secondlife.com/wiki/Color_conversion_scripts
        var l = this._lightness * 2,
            s = this._saturation * ((l <= 1) ? l : 2 - l),
            saturation;

        // Avoid division by zero when l + s is very small (approaching black):
        if (l + s < 1e-9) {
            saturation = 0;
        } else {
            saturation = (2 * s) / (l + s);
        }
=======
    function e(type, klass, p) {
        var elem = document.createElement(type);
        if(klass) elem.className = klass;
        p.appendChild(elem);
>>>>>>> theirs

<<<<<<< mine
        return new ONECOLOR.HSV(this._hue, saturation, (l + s) / 2, this._alpha);
    },
=======
        return elem;
    }
>>>>>>> theirs

<<<<<<< mine
    rgb: function () {
        return this.hsv().rgb();
    },
=======
    // http://stackoverflow.com/questions/4817029/whats-the-best-way-to-detect-a-touch-screen-device-using-javascript
    function isTouch() {
        return typeof(window.ontouchstart) != 'undefined';
    }
>>>>>>> theirs

<<<<<<< mine
    fromRgb: function () { // Becomes one.color.RGB.prototype.hsv
        return this.hsv().hsl();
    }
});


})();
/*global one*/

// This file is purely for the build system
// ONE COLOR ENDS
=======
    function dragTemplate(elem, cbs, down, move, up) {
        var dragging = false;
>>>>>>> theirs

<<<<<<< mine
(function(root, factory) {
    // if(typeof exports === 'object') {
    //     module.exports = factory(require('onecolor'));
    // }
    // else if(typeof define === 'function' && define.amd) {
    //     define(['onecolor'], factory);
    // }
    // else {
    //     root.colorjoe = factory(root.one.color);
    // }
    root.colorjoe = factory($.color);

}($, function(onecolor) {

/*! colorjoe - v0.9.7 - Juho Vepsalainen <bebraw@gmail.com> - MIT
https://bebraw.github.com/colorjoe - 2014-12-07 */
/*! dragjs - v0.4.0 - Juho Vepsalainen <bebraw@gmail.com> - MIT
https://bebraw.github.com/dragjs - 2013-07-17 */
var drag = (function() {
    function drag(elem, cbs) {
        if(!elem) {
            console.warn('drag is missing elem!');
            return;
        }

        if(isTouch()) dragTemplate(elem, cbs, 'touchstart', 'touchmove', 'touchend');
        else dragTemplate(elem, cbs, 'mousedown', 'mousemove', 'mouseup');
    }
=======
        cbs = getCbs(cbs);
>>>>>>> theirs

<<<<<<< mine
    function xyslider(o) {
        var twod = div(o['class'] || '', o.parent);
        var pointer = div('pointer', twod);
        div('shape shape1', pointer);
        div('shape shape2', pointer);
        div('bg bg1', twod);
        div('bg bg2', twod);

        drag(twod, attachPointer(o.cbs, pointer));

        return {
            background: twod,
            pointer: pointer
        };
    }

    function slider(o) {
        var oned = div(o['class'], o.parent);
        var pointer = div('pointer', oned);
        div('shape', pointer);
        div('bg', oned);

        drag(oned, attachPointer(o.cbs, pointer));

        return {
            background: oned,
            pointer: pointer
        };
    }
=======
        var beginCb = cbs.begin;
        var changeCb = cbs.change;
        var endCb = cbs.end;
>>>>>>> theirs

<<<<<<< mine
    drag.xyslider = xyslider;
    drag.slider = slider;
=======
        on(elem, down, function(e) {
            dragging = true;
>>>>>>> theirs

<<<<<<< mine
    return drag;
=======
            var moveHandler = partial(callCb, changeCb, elem);
            function upHandler() {
                dragging = false;
>>>>>>> theirs

<<<<<<< mine
    function attachPointer(cbs, pointer) {
        var ret = {};
=======
                off(document, move, moveHandler);
                off(document, up, upHandler);
>>>>>>> theirs

<<<<<<< mine
        for(var n in cbs) ret[n] = wrap(cbs[n]);
=======
                callCb(endCb, elem, e);
            }
>>>>>>> theirs

<<<<<<< mine
        function wrap(fn) {
            return function(p) {
                p.pointer = pointer;
                fn(p);
            };
        }
=======
            on(document, move, moveHandler);
            on(document, up, upHandler);
>>>>>>> theirs

<<<<<<< mine
        return ret;
    }
=======
            callCb(beginCb, elem, e);
        });
    }
>>>>>>> theirs

<<<<<<< mine
    // move to elemutils lib?
    function div(klass, p) {
        return e('div', klass, p);
    }
=======
    function on(elem, evt, handler) {
        if(elem.addEventListener)
            elem.addEventListener(evt, handler, false);
        else if(elem.attachEvent)
            elem.attachEvent('on' + evt, handler);
    }
>>>>>>> theirs

<<<<<<< mine
    function e(type, klass, p) {
        var elem = document.createElement(type);
        if(klass) elem.className = klass;
        p.appendChild(elem);
=======
    function off(elem, evt, handler) {
        if(elem.removeEventListener)
            elem.removeEventListener(evt, handler, false);
        else if(elem.detachEvent)
            elem.detachEvent('on' + evt, handler);
    }
>>>>>>> theirs

<<<<<<< mine
        return elem;
    }
=======
    function getCbs(cbs) {
        if(!cbs) {
            var initialOffset;
            var initialPos;

            return {
                begin: function(c) {
                    initialOffset = {x: c.elem.offsetLeft, y: c.elem.offsetTop};
                    initialPos = c.cursor;
                },
                change: function(c) {
                    style(c.elem, 'left', (initialOffset.x + c.cursor.x - initialPos.x) + 'px');
                    style(c.elem, 'top', (initialOffset.y + c.cursor.y - initialPos.y) + 'px');
                },
                end: empty
            };
        }
        else {
            return {
                begin: cbs.begin || empty,
                change: cbs.change || empty,
                end: cbs.end || empty
            };
        }
    }
>>>>>>> theirs

<<<<<<< mine
    // http://stackoverflow.com/questions/4817029/whats-the-best-way-to-detect-a-touch-screen-device-using-javascript
    function isTouch() {
        return typeof(window.ontouchstart) != 'undefined';
    }
=======
    // TODO: set draggable class (handy for fx)
>>>>>>> theirs

<<<<<<< mine
    function dragTemplate(elem, cbs, down, move, up) {
        var dragging = false;
=======
    function style(e, prop, value) {
        e.style[prop] = value;
    }
>>>>>>> theirs

<<<<<<< mine
        cbs = getCbs(cbs);
=======
    function empty() {}
>>>>>>> theirs

<<<<<<< mine
        var beginCb = cbs.begin;
        var changeCb = cbs.change;
        var endCb = cbs.end;
=======
    function callCb(cb, elem, e) {
        e.preventDefault();
>>>>>>> theirs

<<<<<<< mine
        on(elem, down, function(e) {
            dragging = true;
=======
        var offset = findPos(elem);
        var width = elem.clientWidth;
        var height = elem.clientHeight;
        var cursor = {
            x: cursorX(elem, e),
            y: cursorY(elem, e)
        };
        var x = (cursor.x - offset.x) / width;
        var y = (cursor.y - offset.y) / height;

        cb({
            x: isNaN(x)? 0: x,
            y: isNaN(y)? 0: y,
            cursor: cursor,
            elem: elem,
            e: e
        });
    }
>>>>>>> theirs

<<<<<<< mine
            var moveHandler = partial(callCb, changeCb, elem);
            function upHandler() {
                dragging = false;
=======
    // http://stackoverflow.com/questions/4394747/javascript-curry-function
    function partial(fn) {
        var slice = Array.prototype.slice;
        var args = slice.apply(arguments, [1]);

        return function() {
            return fn.apply(null, args.concat(slice.apply(arguments)));
        };
    }
>>>>>>> theirs

<<<<<<< mine
                off(document, move, moveHandler);
                off(document, up, upHandler);
=======
    // http://www.quirksmode.org/js/findpos.html
    function findPos(e) {
        var x = 0;
        var y = 0;

        if(e.offsetParent) {
            do {
                x += e.offsetLeft;
                y += e.offsetTop;
            } while (e = e.offsetParent);
        }
>>>>>>> theirs

<<<<<<< mine
                callCb(endCb, elem, e);
            }

            on(document, move, moveHandler);
            on(document, up, upHandler);

            callCb(beginCb, elem, e);
        });
    }

    function on(elem, evt, handler) {
        if(elem.addEventListener)
            elem.addEventListener(evt, handler, false);
        else if(elem.attachEvent)
            elem.attachEvent('on' + evt, handler);
    }

    function off(elem, evt, handler) {
        if(elem.removeEventListener)
            elem.removeEventListener(evt, handler, false);
        else if(elem.detachEvent)
            elem.detachEvent('on' + evt, handler);
    }

    function getCbs(cbs) {
        if(!cbs) {
            var initialOffset;
            var initialPos;

            return {
                begin: function(c) {
                    initialOffset = {x: c.elem.offsetLeft, y: c.elem.offsetTop};
                    initialPos = c.cursor;
                },
                change: function(c) {
                    style(c.elem, 'left', (initialOffset.x + c.cursor.x - initialPos.x) + 'px');
                    style(c.elem, 'top', (initialOffset.y + c.cursor.y - initialPos.y) + 'px');
                },
                end: empty
            };
        }
        else {
            return {
                begin: cbs.begin || empty,
                change: cbs.change || empty,
                end: cbs.end || empty
            };
        }
    }
=======
        return {x: x, y: y};
    }
>>>>>>> theirs

<<<<<<< mine
    // TODO: set draggable class (handy for fx)
=======
    // http://javascript.about.com/library/blmousepos.htm
    function cursorX(elem, evt) {
        if(isFixed(elem)) {
            var bodyLeft = parseInt(getStyle(document.body, 'marginLeft'), 10) -
                calc(elem, 'scrollLeft') + window.pageXOffset +
                elem.style.marginLeft;
>>>>>>> theirs

<<<<<<< mine
    function style(e, prop, value) {
        e.style[prop] = value;
    }
=======
            return evt.clientX - bodyLeft;
        }
        if(evt.pageX) return evt.pageX;
        else if(evt.clientX)
            return evt.clientX + document.body.scrollLeft;
    }
    function cursorY(elem, evt) {
        if(isFixed(elem)) {
            var bodyTop = parseInt(getStyle(document.body, 'marginTop'), 10) -
                calc(elem, 'scrollTop') + window.pageYOffset +
                elem.style.marginTop;
>>>>>>> theirs

<<<<<<< mine
    function empty() {}
=======
            return evt.clientY - bodyTop;
        }
        if(evt.pageY) return evt.pageY;
        else if(evt.clientY)
            return evt.clientY + document.body.scrollTop;
    }
>>>>>>> theirs

<<<<<<< mine
    function callCb(cb, elem, e) {
        e.preventDefault();
=======
    function calc(element, prop) {
        var ret = 0;
>>>>>>> theirs

<<<<<<< mine
        var offset = findPos(elem);
        var width = elem.clientWidth;
        var height = elem.clientHeight;
        var cursor = {
            x: cursorX(elem, e),
            y: cursorY(elem, e)
        };
        var x = (cursor.x - offset.x) / width;
        var y = (cursor.y - offset.y) / height;

        cb({
            x: isNaN(x)? 0: x,
            y: isNaN(y)? 0: y,
            cursor: cursor,
            elem: elem,
            e: e
        });
    }
=======
        while (element.nodeName != "HTML") {
            ret += element[prop];
            element = element.parentNode;
        }
>>>>>>> theirs

<<<<<<< mine
    // http://stackoverflow.com/questions/4394747/javascript-curry-function
    function partial(fn) {
        var slice = Array.prototype.slice;
        var args = slice.apply(arguments, [1]);

        return function() {
            return fn.apply(null, args.concat(slice.apply(arguments)));
        };
    }
=======
        return ret;
    }
>>>>>>> theirs

<<<<<<< mine
    // http://www.quirksmode.org/js/findpos.html
    function findPos(e) {
        var x = 0;
        var y = 0;

        if(e.offsetParent) {
            do {
                x += e.offsetLeft;
                y += e.offsetTop;
            } while (e = e.offsetParent);
        }

        return {x: x, y: y};
    }

    // http://javascript.about.com/library/blmousepos.htm
    function cursorX(elem, evt) {
        if(isFixed(elem)) {
            var bodyLeft = parseInt(getStyle(document.body, 'marginLeft'), 10) -
                calc(elem, 'scrollLeft') + window.pageXOffset +
                elem.style.marginLeft;

            return evt.clientX - bodyLeft;
        }
        if(evt.pageX) return evt.pageX;
        else if(evt.clientX)
            return evt.clientX + document.body.scrollLeft;
    }
    function cursorY(elem, evt) {
        if(isFixed(elem)) {
            var bodyTop = parseInt(getStyle(document.body, 'marginTop'), 10) -
                calc(elem, 'scrollTop') + window.pageYOffset +
                elem.style.marginTop;
=======
    // http://www.velocityreviews.com/forums/t942580-mouse-position-in-both-fixed-and-relative-positioning.html
    function isFixed(element) {
        // While not at the top of the document tree, or not fixed, keep
        // searching upwards.
        while (element.nodeName != "HTML" && usedStyle(element,
                "position") != "fixed")
            element = element.parentNode;
            if(element.nodeName == "HTML") return false;
            else return true;
    }
>>>>>>> theirs

<<<<<<< mine
            return evt.clientY - bodyTop;
        }
        if(evt.pageY) return evt.pageY;
        else if(evt.clientY)
            return evt.clientY + document.body.scrollTop;
    }
=======
    // http://www.javascriptkit.com/dhtmltutors/dhtmlcascade4.shtml
    function getStyle(el, cssprop){
        if (el.currentStyle) // IE
            return el.currentStyle[cssprop];
>>>>>>> theirs

<<<<<<< mine
    function calc(element, prop) {
        var ret = 0;
=======
        if(document.defaultView && document.defaultView.getComputedStyle)
            return document.defaultView.getComputedStyle(el, "")[cssprop];
>>>>>>> theirs

<<<<<<< mine
        while (element.nodeName != "HTML") {
            ret += element[prop];
            element = element.parentNode;
        }
=======
        //try and get inline style
        return el.style[cssprop];
    }
>>>>>>> theirs

<<<<<<< mine
        return ret;
    }

    // http://www.velocityreviews.com/forums/t942580-mouse-position-in-both-fixed-and-relative-positioning.html
    function isFixed(element) {
        // While not at the top of the document tree, or not fixed, keep
        // searching upwards.
        while (element.nodeName != "HTML" && usedStyle(element,
                "position") != "fixed")
            element = element.parentNode;
            if(element.nodeName == "HTML") return false;
            else return true;
    }
=======
    // Used style is to get around browsers' different methods of getting
    // the currently used (e.g. inline, class, etc) style for an element
    function usedStyle(element, property) {
        var s;

        // getComputedStyle is the standard way but some ie versions don't
        // support it
        if(window.getComputedStyle)
            s = window.getComputedStyle(element, null);
        else s = element.currentStyle;
>>>>>>> theirs

<<<<<<< mine
    // http://www.javascriptkit.com/dhtmltutors/dhtmlcascade4.shtml
    function getStyle(el, cssprop){
        if (el.currentStyle) // IE
            return el.currentStyle[cssprop];
=======
        return s[property];
    }
})();
>>>>>>> theirs

<<<<<<< mine
        if(document.defaultView && document.defaultView.getComputedStyle)
            return document.defaultView.getComputedStyle(el, "")[cssprop];
=======
var div = partial(e, 'div');
>>>>>>> theirs

<<<<<<< mine
        //try and get inline style
        return el.style[cssprop];
    }
=======
function e(type, klass, p) {
    var elem = document.createElement(type);
    elem.className = klass;
    p.appendChild(elem);
>>>>>>> theirs

<<<<<<< mine
    // Used style is to get around browsers' different methods of getting
    // the currently used (e.g. inline, class, etc) style for an element
    function usedStyle(element, property) {
        var s;

        // getComputedStyle is the standard way but some ie versions don't
        // support it
        if(window.getComputedStyle)
            s = window.getComputedStyle(element, null);
        else s = element.currentStyle;
=======
    return elem;
}
>>>>>>> theirs

<<<<<<< mine
        return s[property];
    }
})();
=======
// http://stackoverflow.com/questions/4394747/javascript-curry-function
function partial(fn) {
    var slice = Array.prototype.slice;
    var args = slice.apply(arguments, [1]);
>>>>>>> theirs

<<<<<<< mine
var div = partial(e, 'div');
=======
    return function() {
        return fn.apply(null, args.concat(slice.apply(arguments)));
    };
}
>>>>>>> theirs

<<<<<<< mine
function e(type, klass, p) {
    var elem = document.createElement(type);
    elem.className = klass;
    p.appendChild(elem);
=======
function labelInput(klass, n, p, maxLen) {
    var d = div(klass, p);
    var l = label(n, d);
    var i = input('text', d, maxLen);
>>>>>>> theirs

<<<<<<< mine
    return elem;
}
=======
    return {label: l, input: i};
}
>>>>>>> theirs

<<<<<<< mine
// http://stackoverflow.com/questions/4394747/javascript-curry-function
function partial(fn) {
    var slice = Array.prototype.slice;
    var args = slice.apply(arguments, [1]);
=======
function label(c, p) {
    var elem = e('label', '', p);
    elem.innerHTML = c;
>>>>>>> theirs

<<<<<<< mine
    return function() {
        return fn.apply(null, args.concat(slice.apply(arguments)));
    };
}
=======
    return elem;
}
>>>>>>> theirs

<<<<<<< mine
function labelInput(klass, n, p, maxLen) {
    var d = div(klass, p);
    var l = label(n, d);
    var i = input('text', d, maxLen);
=======
function input(t, p, maxLen) {
    var elem = e('input', '', p);
    elem.type = t;
    if(maxLen) elem.maxLength = maxLen;
>>>>>>> theirs

<<<<<<< mine
    return {label: l, input: i};
}
=======
    return elem;
}
>>>>>>> theirs

<<<<<<< mine
function label(c, p) {
    var elem = e('label', '', p);
    elem.innerHTML = c;
=======
function X(p, a) {p.style.left = clamp(a * 100, 0, 100) + '%';}
function Y(p, a) {p.style.top = clamp(a * 100, 0, 100) + '%';}
function BG(e, c) {e.style.background = c;}
>>>>>>> theirs

<<<<<<< mine
    return elem;
}
=======
function clamp(a, minValue, maxValue) {
    return Math.min(Math.max(a, minValue), maxValue);
}
>>>>>>> theirs

<<<<<<< mine
function input(t, p, maxLen) {
    var elem = e('input', '', p);
    elem.type = t;
    if(maxLen) elem.maxLength = maxLen;
=======
var utils = {
    clamp: clamp,
    e: e,
    div: div,
    partial: partial,
    labelInput: labelInput,
    X: X,
    Y: Y,
    BG: BG
};
>>>>>>> theirs

<<<<<<< mine
    return elem;
}
=======
function currentColor(p) {
  var e1 = utils.div('currentColorContainer', p);
  var e = utils.div('currentColor', e1);

  return {
    change: function(col) {
      utils.BG(e, col.cssa());
    }
  };
}
>>>>>>> theirs

<<<<<<< mine
function X(p, a) {p.style.left = clamp(a * 100, 0, 100) + '%';}
function Y(p, a) {p.style.top = clamp(a * 100, 0, 100) + '%';}
function BG(e, c) {e.style.background = c;}
=======
function fields(p, joe, o) {
  var cs = o.space;
  var fac = o.limit || 255;
  var fix = o.fix >= 0? o.fix: 0;
  var inputLen = ('' + fac).length + fix;
  inputLen = fix? inputLen + 1: inputLen;

  var initials = cs.split('');
  var useAlpha = cs[cs.length - 1] == 'A';
  cs = useAlpha? cs.slice(0, -1): cs;

  if(['RGB', 'HSL', 'HSV', 'CMYK'].indexOf(cs) < 0)
    return console.warn('Invalid field names', cs);

  var c = utils.div('colorFields', p);
  var elems = initials.map(function(n, i) {
    n = n.toLowerCase();

    var e = utils.labelInput('color ' + n, n, c, inputLen);
    e.input.onblur = done;
    e.input.onkeydown = validate;
    e.input.onkeyup = update;

    return {name: n, e: e};
  });

  function done() {
    joe.done();
  }

  function validate(e) {
    if (!(e.ctrlKey || e.altKey) && /^[a-zA-Z]$/.test(e.key)) {
      e.preventDefault();
    }
  }
>>>>>>> theirs

<<<<<<< mine
function clamp(a, minValue, maxValue) {
    return Math.min(Math.max(a, minValue), maxValue);
}
=======
  function update() {
    var col = [cs];
>>>>>>> theirs

<<<<<<< mine
var utils = {
    clamp: clamp,
    e: e,
    div: div,
    partial: partial,
    labelInput: labelInput,
    X: X,
    Y: Y,
    BG: BG
};
=======
    elems.forEach(function(o) {col.push(o.e.input.value / fac);});
>>>>>>> theirs

<<<<<<< mine
function currentColor(p) {
  var e1 = utils.div('currentColorContainer', p);
  var e = utils.div('currentColor', e1);

  return {
    change: function(col) {
      utils.BG(e, col.cssa());
    }
  };
}
=======
    if(!useAlpha) col.push(joe.getAlpha());
>>>>>>> theirs

<<<<<<< mine
function fields(p, joe, o) {
  var cs = o.space;
  var fac = o.limit || 255;
  var fix = o.fix >= 0? o.fix: 0;
  var inputLen = ('' + fac).length + fix;
  inputLen = fix? inputLen + 1: inputLen;

  var initials = cs.split('');
  var useAlpha = cs[cs.length - 1] == 'A';
  cs = useAlpha? cs.slice(0, -1): cs;

  if(['RGB', 'HSL', 'HSV', 'CMYK'].indexOf(cs) < 0)
    return console.warn('Invalid field names', cs);

  var c = utils.div('colorFields', p);
  var elems = initials.map(function(n, i) {
    n = n.toLowerCase();

    var e = utils.labelInput('color ' + n, n, c, inputLen);
    e.input.onblur = done;
    e.input.onkeydown = validate;
    e.input.onkeyup = update;

    return {name: n, e: e};
  });

  function done() {
    joe.done();
  }

  function validate(e) {
    if (!(e.ctrlKey || e.altKey) && /^[a-zA-Z]$/.test(e.key)) {
      e.preventDefault();
    }
  }
=======
    joe.set(col);
  }
>>>>>>> theirs

<<<<<<< mine
  function update() {
    var col = [cs];
=======
  return {
    change: function(col) {
      elems.forEach(function(o) {
        o.e.input.value = (col[o.name]() * fac).toFixed(fix);
      });
    }
  };
}
>>>>>>> theirs

<<<<<<< mine
    elems.forEach(function(o) {col.push(o.e.input.value / fac);});
=======
function alpha(p, joe) {
  var e = drag.slider({
    parent: p,
    'class': 'oned alpha',
    cbs: {
      begin: change,
      change: change,
      end: done
    }
  });
>>>>>>> theirs

<<<<<<< mine
    if(!useAlpha) col.push(joe.getAlpha());
=======
  function done() {
    joe.done();
  }

  function change(p) {
    var val = utils.clamp(p.y, 0, 1);

    utils.Y(p.pointer, val);
    joe.setAlpha(1 - val);
  }

  return {
    change: function(col) {
      utils.Y(e.pointer, 1 - col.alpha());
    }
  };
}
>>>>>>> theirs

<<<<<<< mine
    joe.set(col);
  }
=======
function hex(p, joe, o) {
  var e = utils.labelInput('hex', o.label || '', p, 7);
  e.input.value = '#';

  e.input.onkeyup = function(elem) {
    var key = elem.keyCode || elem.which;
    var val = elem.target.value;
    val = val[0] == '#'? val: '#' + val;
    val = pad(val, 7, '0');

    if(key == 13) joe.set(val);
  };

  e.input.onblur = function(elem) {
    joe.set(elem.target.value);
    joe.done();
  };

  return {
    change: function(col) {
      e.input.value = e.input.value[0] == '#'? '#': '';
      e.input.value += col.hex().slice(1);
    }
  };
}
>>>>>>> theirs

<<<<<<< mine
  return {
    change: function(col) {
      elems.forEach(function(o) {
        o.e.input.value = (col[o.name]() * fac).toFixed(fix);
      });
    }
  };
}
=======
function close(p, joe, o) {
  var elem = utils.e('a', o['class'] || 'close', p);
  elem.href = '#';
  elem.innerHTML = o.label || 'Close';
>>>>>>> theirs

<<<<<<< mine
function alpha(p, joe) {
  var e = drag.slider({
    parent: p,
    'class': 'oned alpha',
    cbs: {
      begin: change,
      change: change,
      end: done
    }
  });
=======
  elem.onclick = function(e) {
    e.preventDefault();
>>>>>>> theirs

<<<<<<< mine
  function done() {
    joe.done();
  }

  function change(p) {
    var val = utils.clamp(p.y, 0, 1);

    utils.Y(p.pointer, val);
    joe.setAlpha(1 - val);
  }

  return {
    change: function(col) {
      utils.Y(e.pointer, 1 - col.alpha());
    }
  };
}
=======
    joe.hide();
  };
}
>>>>>>> theirs

<<<<<<< mine
function hex(p, joe, o) {
  var e = utils.labelInput('hex', o.label || '', p, 7);
  e.input.value = '#';

  e.input.onkeyup = function(elem) {
    var key = elem.keyCode || elem.which;
    var val = elem.target.value;
    val = val[0] == '#'? val: '#' + val;
    val = pad(val, 7, '0');

    if(key == 13) joe.set(val);
  };

  e.input.onblur = function(elem) {
    joe.set(elem.target.value);
    joe.done();
  };

  return {
    change: function(col) {
      e.input.value = e.input.value[0] == '#'? '#': '';
      e.input.value += col.hex().slice(1);
    }
  };
}
=======
function pad(a, n, c) {
  var ret = a;
>>>>>>> theirs

<<<<<<< mine
function close(p, joe, o) {
  var elem = utils.e('a', o['class'] || 'close', p);
  elem.href = '#';
  elem.innerHTML = o.label || 'Close';
=======
  for(var i = a.length, len = n; i < n; i++) ret += c;
>>>>>>> theirs

<<<<<<< mine
  elem.onclick = function(e) {
    e.preventDefault();
=======
  return ret;
}
>>>>>>> theirs

<<<<<<< mine
    joe.hide();
  };
}
=======
var extras = {
  currentColor: currentColor,
  fields: fields,
  hex: hex,
  alpha: alpha,
  close: close
};
>>>>>>> theirs

<<<<<<< mine
function pad(a, n, c) {
  var ret = a;

  for(var i = a.length, len = n; i < n; i++) ret += c;

  return ret;
}

var extras = {
  currentColor: currentColor,
  fields: fields,
  hex: hex,
  alpha: alpha,
  close: close
};
=======
var colorjoe = function(cbs) {
  if(!all(isFunction, [cbs.init, cbs.xy, cbs.z]))
    return console.warn('colorjoe: missing cb');

  return function(element, initialColor, extras) {
    return setup({
      e: element,
      color: initialColor,
      cbs: cbs,
      extras: extras
    });
  };
};
>>>>>>> theirs

<<<<<<< mine
var colorjoe = function(cbs) {
  if(!all(isFunction, [cbs.init, cbs.xy, cbs.z]))
    return console.warn('colorjoe: missing cb');

  return function(element, initialColor, extras) {
    return setup({
      e: element,
      color: initialColor,
      cbs: cbs,
      extras: extras
    });
  };
};
=======
/* pickers */
colorjoe.rgb = colorjoe({
  init: function(col, xy, z) {
    var ret = onecolor(col).hsv();

    this.xy(ret, {x: ret.saturation(), y: 1 - ret.value()}, xy, z);
    this.z(ret, ret.hue(), xy, z);

    return ret;
  },
  xy: function(col, p, xy, z) {
    utils.X(xy.pointer, p.x);
    utils.Y(xy.pointer, p.y);

    return col.saturation(p.x).value(1 - p.y);
  },
  z: function(col, v, xy, z) {
    utils.Y(z.pointer, v);
    RGB_BG(xy.background, v);
>>>>>>> theirs

<<<<<<< mine
/* pickers */
colorjoe.rgb = colorjoe({
  init: function(col, xy, z) {
    var ret = onecolor(col).hsv();

    this.xy(ret, {x: ret.saturation(), y: 1 - ret.value()}, xy, z);
    this.z(ret, ret.hue(), xy, z);

    return ret;
  },
  xy: function(col, p, xy, z) {
    utils.X(xy.pointer, p.x);
    utils.Y(xy.pointer, p.y);

    return col.saturation(p.x).value(1 - p.y);
  },
  z: function(col, v, xy, z) {
    utils.Y(z.pointer, v);
    RGB_BG(xy.background, v);
=======
    return col.hue(v);
  }
});
>>>>>>> theirs

<<<<<<< mine
    return col.hue(v);
  }
});
=======
colorjoe.hsl = colorjoe({
  init: function(col, xy, z) {
    var ret = onecolor(col).hsl();

    this.xy(ret, {x: ret.hue(), y: 1 - ret.saturation()}, xy, z);
    this.z(ret, 1 - ret.lightness(), xy, z);

    return ret;
  },
  xy: function(col, p, xy, z) {
    utils.X(xy.pointer, p.x);
    utils.Y(xy.pointer, p.y);
    RGB_BG(z.background, p.x);

    return col.hue(p.x).saturation(1 - p.y);
  },
  z: function(col, v, xy, z) {
    utils.Y(z.pointer, v);
>>>>>>> theirs

<<<<<<< mine
colorjoe.hsl = colorjoe({
  init: function(col, xy, z) {
    var ret = onecolor(col).hsl();

    this.xy(ret, {x: ret.hue(), y: 1 - ret.saturation()}, xy, z);
    this.z(ret, 1 - ret.lightness(), xy, z);

    return ret;
  },
  xy: function(col, p, xy, z) {
    utils.X(xy.pointer, p.x);
    utils.Y(xy.pointer, p.y);
    RGB_BG(z.background, p.x);

    return col.hue(p.x).saturation(1 - p.y);
  },
  z: function(col, v, xy, z) {
    utils.Y(z.pointer, v);
=======
    return col.lightness(1 - v);
  }
});
>>>>>>> theirs

<<<<<<< mine
    return col.lightness(1 - v);
  }
});
=======
colorjoe._extras = {};
>>>>>>> theirs

<<<<<<< mine
colorjoe._extras = {};
=======
colorjoe.registerExtra = function(name, fn) {
  if(name in colorjoe._extras)
    console.warn('Extra "' + name + '"has been registered already!');
>>>>>>> theirs

<<<<<<< mine
colorjoe.registerExtra = function(name, fn) {
  if(name in colorjoe._extras)
    console.warn('Extra "' + name + '"has been registered already!');
=======
  colorjoe._extras[name] = fn;
};
>>>>>>> theirs

<<<<<<< mine
  colorjoe._extras[name] = fn;
};
=======
for(var k in extras) {
  colorjoe.registerExtra(k, extras[k]);
}
>>>>>>> theirs

<<<<<<< mine
for(var k in extras) {
  colorjoe.registerExtra(k, extras[k]);
}
=======
function RGB_BG(e, h) {
  utils.BG(e, new onecolor.HSV(h, 1, 1).cssa());
}
>>>>>>> theirs

<<<<<<< mine
function RGB_BG(e, h) {
  utils.BG(e, new onecolor.HSV(h, 1, 1).cssa());
}
=======
function setup(o) {
  if(!o.e) return console.warn('colorjoe: missing element');
>>>>>>> theirs

<<<<<<< mine
function setup(o) {
  if(!o.e) return console.warn('colorjoe: missing element');
=======
  var e = isString(o.e)? document.getElementById(o.e): o.e;
  e.className = 'colorPicker';
>>>>>>> theirs

<<<<<<< mine
  var e = isString(o.e)? document.getElementById(o.e): o.e;
  e.className = 'colorPicker';
=======
  var cbs = o.cbs;
>>>>>>> theirs

<<<<<<< mine
  var cbs = o.cbs;
=======
  var xy = drag.xyslider({
    parent: e,
    'class': 'twod',
    cbs: {
      begin: changeXY,
      change: changeXY,
      end: done
    }
  });
>>>>>>> theirs

<<<<<<< mine
  var xy = drag.xyslider({
    parent: e,
    'class': 'twod',
    cbs: {
      begin: changeXY,
      change: changeXY,
      end: done
    }
  });
=======
  function changeXY(p) {
    col = cbs.xy(col, {
      x: utils.clamp(p.x, 0, 1),
      y: utils.clamp(p.y, 0, 1)
    }, xy, z);
    changed();
  }

  var z = drag.slider({
    parent: e,
    'class': 'oned',
    cbs: {
      begin: changeZ,
      change: changeZ,
      end: done
    }
  });
>>>>>>> theirs

<<<<<<< mine
  function changeXY(p) {
    col = cbs.xy(col, {
      x: utils.clamp(p.x, 0, 1),
      y: utils.clamp(p.y, 0, 1)
    }, xy, z);
    changed();
  }

  var z = drag.slider({
    parent: e,
    'class': 'oned',
    cbs: {
      begin: changeZ,
      change: changeZ,
      end: done
    }
  });
=======
  function changeZ(p) {
    col = cbs.z(col, utils.clamp(p.y, 0, 1), xy, z);
    changed();
  }

  // Initial color
  var previous = getColor(o.color);
  var col = cbs.init(previous, xy, z);
  var listeners = {change: [], done: []};

  function changed(skip) {
    skip = isArray(skip)? skip: [];

    var li = listeners.change;
    var v;

    for(var i = 0, len = li.length; i < len; i++) {
      v = li[i];
      if(skip.indexOf(v.name) == -1) v.fn(col);
    }
  }
>>>>>>> theirs

<<<<<<< mine
  function changeZ(p) {
    col = cbs.z(col, utils.clamp(p.y, 0, 1), xy, z);
    changed();
  }

  // Initial color
  var previous = getColor(o.color);
  var col = cbs.init(previous, xy, z);
  var listeners = {change: [], done: []};

  function changed(skip) {
    skip = isArray(skip)? skip: [];

    var li = listeners.change;
    var v;

    for(var i = 0, len = li.length; i < len; i++) {
      v = li[i];
      if(skip.indexOf(v.name) == -1) v.fn(col);
    }
  }
=======
  function done() {
    // Do not call done callback if the color did not change
    if (previous.equals(col)) return;
    for(var i = 0, len = listeners.done.length; i < len; i++) {
      listeners.done[i].fn(col);
    }
    previous = col;
  }
>>>>>>> theirs

<<<<<<< mine
  function done() {
    // Do not call done callback if the color did not change
    if (previous.equals(col)) return;
    for(var i = 0, len = listeners.done.length; i < len; i++) {
      listeners.done[i].fn(col);
    }
    previous = col;
  }
=======
  var ob = {
    e: e,
    done: function() {
      done();
>>>>>>> theirs

<<<<<<< mine
  var ob = {
    e: e,
    done: function() {
      done();
=======
      return this;
    },
    update: function(skip) {
      changed(skip);
>>>>>>> theirs

      return this;
    },
<<<<<<< mine
    update: function(skip) {
      changed(skip);
=======
    hide: function() {
      e.style.display = 'none';
>>>>>>> theirs

      return this;
    },
<<<<<<< mine
    hide: function() {
      e.style.display = 'none';
=======
    show: function() {
      e.style.display = '';
>>>>>>> theirs

<<<<<<< mine
      return this;
=======
      return this;
    },
    get: function() {
      return col;
>>>>>>> theirs
    },
<<<<<<< mine
    show: function() {
      e.style.display = '';
=======
    set: function(c) {
      var oldCol = this.get();
      col = cbs.init(getColor(c), xy, z);
>>>>>>> theirs

<<<<<<< mine
      return this;
=======
      if(!oldCol.equals(col)) this.update();

      return this;
    },
    getAlpha: function() {
      return col.alpha();
>>>>>>> theirs
    },
<<<<<<< mine
    get: function() {
      return col;
    },
    set: function(c) {
      var oldCol = this.get();
      col = cbs.init(getColor(c), xy, z);

      if(!oldCol.equals(col)) this.update();
=======
    setAlpha: function(v) {
      col = col.alpha(v);
>>>>>>> theirs

<<<<<<< mine
      return this;
    },
    getAlpha: function() {
      return col.alpha();
=======
      this.update();

      return this;
>>>>>>> theirs
    },
<<<<<<< mine
    setAlpha: function(v) {
      col = col.alpha(v);
=======
    on: function(evt, cb, name) {
      if(evt == 'change' || evt == 'done') {
        listeners[evt].push({name: name, fn: cb});
      }
      else console.warn('Passed invalid evt name "' + evt + '" to colorjoe.on');
>>>>>>> theirs

<<<<<<< mine
      this.update();

      return this;
=======
      return this;
>>>>>>> theirs
    },
<<<<<<< mine
    on: function(evt, cb, name) {
      if(evt == 'change' || evt == 'done') {
        listeners[evt].push({name: name, fn: cb});
      }
      else console.warn('Passed invalid evt name "' + evt + '" to colorjoe.on');
=======
    removeAllListeners: function(evt) {
      if (evt) {
        delete listeners[evt];
      }
      else {
        for(var key in listeners) {
          delete listeners[key];
        }
      }

      return this;
    }
  };

  setupExtras(e, ob, o.extras);
  changed();

  return ob;
}

function getColor(c) {
  if(!isDefined(c)) return onecolor('#000');
  if(c.isColor) return c;

  var ret = onecolor(c);

  if(ret) return ret;
>>>>>>> theirs

<<<<<<< mine
      return this;
    },
    removeAllListeners: function(evt) {
      if (evt) {
        delete listeners[evt];
      }
      else {
        for(var key in listeners) {
          delete listeners[key];
        }
      }

      return this;
    }
  };

  setupExtras(e, ob, o.extras);
  changed();
=======
  if(isDefined(c)) console.warn('Passed invalid color to colorjoe, using black instead');

  return onecolor('#000');
}

function setupExtras(p, joe, extras) {
  if(!extras) return;
>>>>>>> theirs

<<<<<<< mine
  return ob;
}

function getColor(c) {
  if(!isDefined(c)) return onecolor('#000');
  if(c.isColor) return c;

  var ret = onecolor(c);

  if(ret) return ret;

  if(isDefined(c)) console.warn('Passed invalid color to colorjoe, using black instead');

  return onecolor('#000');
}

function setupExtras(p, joe, extras) {
  if(!extras) return;
=======
  var c = utils.div('extras', p);
  var cbs;
  var name;
  var params;

  extras.forEach(function(e, i) {
    if(isArray(e)) {
      name = e[0];
      params = e.length > 1? e[1]: {};
    }
    else {
      name = e;
      params = {};
    }
    extra = name in colorjoe._extras? colorjoe._extras[name]: null;
>>>>>>> theirs

<<<<<<< mine
  var c = utils.div('extras', p);
  var cbs;
  var name;
  var params;

  extras.forEach(function(e, i) {
    if(isArray(e)) {
      name = e[0];
      params = e.length > 1? e[1]: {};
    }
    else {
      name = e;
      params = {};
=======
    if(extra) {
      cbs = extra(c, extraProxy(joe, name + i), params);
      for(var k in cbs) joe.on(k, cbs[k], name);
>>>>>>> theirs
    }
<<<<<<< mine
    extra = name in colorjoe._extras? colorjoe._extras[name]: null;
=======
  });
}

function extraProxy(joe, name) {
  var ret = copy(joe);

  ret.update = function() {
    joe.update([name]);
  };

  return ret;
}
>>>>>>> theirs

<<<<<<< mine
    if(extra) {
      cbs = extra(c, extraProxy(joe, name + i), params);
      for(var k in cbs) joe.on(k, cbs[k], name);
    }
  });
}

function extraProxy(joe, name) {
  var ret = copy(joe);

  ret.update = function() {
    joe.update([name]);
  };

  return ret;
}

function copy(o) {
  // returns a shallow copy
  var ret = {};

  for(var k in o) {
    ret[k] = o[k];
  }

  return ret;
}

function all(cb, a) {return a.map(cb).filter(id).length == a.length;}

function isArray(o) {
  return Object.prototype.toString.call(o) === "[object Array]";
}
function isString(o) {return typeof(o) === 'string';}
function isDefined(input) {return typeof input !== "undefined";}
function isFunction(input) {return typeof input === "function";}
function id(a) {return a;}
=======
function copy(o) {
  // returns a shallow copy
  var ret = {};

  for(var k in o) {
    ret[k] = o[k];
  }

  return ret;
}

function all(cb, a) {return a.map(cb).filter(id).length == a.length;}

function isArray(o) {
  return Object.prototype.toString.call(o) === "[object Array]";
}
function isString(o) {return typeof(o) === 'string';}
function isDefined(input) {return typeof input !== "undefined";}
function isFunction(input) {return typeof input === "function";}
function id(a) {return a;}
>>>>>>> theirs

    return colorjoe;
}));

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD40.module("colorjoe", moduleFactory);

}());