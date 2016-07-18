(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
 * jquery.nearest
 * Get the visually nearest element from set of specified elements.
 *
 * Copyright (c) 2014 Jensen Tonne
 * http://jstonne.me
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Credits:
 * Euclidean distance algorithm from Google TV Web UI Library (Apache 2.0).
 * https://code.google.com/p/gtv-ui-lib/source/browse/trunk/jquery/source/js/keycontrol.js
 */

var DIRECTIONS = {
    left : [-1,  0],
    up   : [ 0, -1],
    right: [ 1,  0],
    down : [ 0,  1]
};

$.distance = function(fromItem, toItem, direction) {

    direction = DIRECTIONS[direction];

    function calcDistance(dx, dy) {
        return Math.floor(Math.sqrt((dx * dx) + (dy * dy)));
    }

    var fromItemOffset  = fromItem.offset(),
        fromOuterWidth  = fromItem.outerWidth(),
        fromOuterHeight = fromItem.outerHeight(),
        fromItemLeft    = fromItemOffset.left,
        fromItemTop     = fromItemOffset.top,
        fromItemRight   = fromItemLeft + fromOuterWidth,
        fromItemBottom  = fromItemTop  + fromOuterHeight,
        fromItemCenterX = fromItemLeft + (fromOuterWidth / 2),
        fromItemCenterY = fromItemTop  + (fromOuterHeight / 2),

        toItemOffset    = toItem.offset(),
        toOuterWidth    = toItem.outerWidth(),
        toOuterHeight   = toItem.outerHeight(),
        toItemLeft      = toItemOffset.left,
        toItemTop       = toItemOffset.top,
        toItemRight     = toItemLeft + toOuterWidth,
        toItemBottom    = toItemTop  + toOuterHeight,
        toItemCenterX   = toItemLeft + (toOuterWidth / 2),
        toItemCenterY   = toItemTop  + (toOuterHeight / 2),

        toItemDistance,
        distanceX,
        distanceY;

    if (direction[1] == 0) {

        if (direction[0] < 0) {

            if (toItemRight <= fromItemLeft) {
                distanceX = fromItemLeft - toItemRight;
            }

            if (toItemCenterX <= fromItemLeft) {
                if (distanceX != undefined) {
                    distanceX = Math.min(distanceX, fromItemLeft - toItemCenterX);
                } else {
                    distanceX = fromItemLeft - toItemCenterX;
                }
            }

            if (toItemRight <= fromItemLeft) {
                if (distanceX != undefined) {
                    distanceX = Math.min(distanceX, fromItemLeft - toItemRight);
                } else {
                    distanceX = fromItemLeft - toItemRight;
                }
            }

        } else {

            if (fromItemRight <= toItemLeft) {
                distanceX = toItemLeft - fromItemRight;
            }

            if (fromItemRight <= toItemCenterX) {
                if (distanceX != undefined) {
                    distanceX = Math.min(distanceX, toItemCenterX - fromItemRight);
                } else {
                    distanceX = toItemCenterX - fromItemRight;
                }
            }

            if (fromItemLeft < toItemLeft) {
                if (distanceX != undefined) {
                    distanceX = Math.min(distanceX, toItemLeft - fromItemLeft);
                } else {
                    distanceX = toItemLeft - fromItemLeft;
                }
            }
        }

        distanceY = Math.min(
            Math.abs(fromItemCenterY - toItemTop),
            Math.abs(fromItemCenterY - toItemCenterY),
            Math.abs(fromItemCenterY - toItemBottom)
        ) * 2;

    } else if (direction[0] == 0) {

        if (direction[1] < 0) {

            if (toItemBottom <= fromItemTop) {
                distanceY = fromItemTop - toItemBottom;
            }

            if (toItemCenterY <= fromItemTop) {
                if (distanceY != undefined) {
                    distanceY = Math.min(distanceY, fromItemTop - toItemCenterY);
                } else {
                    distanceY = fromItemTop - toItemCenterY;
                }
            }

            if (toItemBottom <= fromItemTop) {
                if (distanceY != undefined) {
                    distanceY = Math.min(distanceY, fromItemTop - toItemBottom);
                } else {
                    distanceY = fromItemTop - toItemBottom;
                }
            }

        } else {

            if (fromItemBottom <= toItemTop) {
                distanceY = toItemTop - fromItemBottom;
            }

            if (fromItemBottom <= toItemCenterY) {
                if (distanceY != undefined) {
                    distanceY = Math.min(distanceY, toItemCenterY - fromItemBottom);
                } else {
                    distanceY = toItemCenterY - fromItemBottom;
                }
            }

            if (fromItemTop < toItemTop) {
                if (distanceY != undefined) {
                    distanceY = Math.min(distanceY, toItemTop - fromItemTop);
                } else {
                    distanceY = toItemTop - fromItemTop;
                }
            }
        }

        distanceX = Math.min(
            Math.abs(fromItemCenterX - toItemLeft),
            Math.abs(fromItemCenterX - toItemCenterX),
            Math.abs(fromItemCenterX - toItemRight)
        ) * 2;
    }

    if (distanceX == undefined || distanceY == undefined) {
        toItemDistance = -1;
    } else {
        toItemDistance = calcDistance(distanceX, distanceY);
    }

    return toItemDistance;
};

$.fn.nearest = function(items, direction) {

    var fromItem = this,
        toItem,
        nearestItem = $(),
        itemDistance,
        minItemDistance = null,
        i = -1,
        max = items.length;

    while (++i < max) {

        toItem = $(items[i]);

        // If the item we're comparing against is itself, skip.
        if (fromItem[0]==toItem[0]) continue;

        itemDistance = $.distance(fromItem, toItem, direction);

        // If this item is in the direction that we're looking at
        if (itemDistance >= 0 &&
            // And this distance is shorter than the distance of the previously picked item
            (minItemDistance===null || itemDistance < minItemDistance)) {

            // Mark this item as the current nearest item
            // until something closer comes along
            minItemDistance = itemDistance;
            nearestItem = toItem;
        }
    }

    return nearestItem;
};
}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD40.module("nearest", moduleFactory);

}());