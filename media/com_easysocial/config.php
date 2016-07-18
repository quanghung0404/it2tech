<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
/*
(function($window){

var _space = " ",
    _width = "width",
    _height = "height",
    _replace = "replace",
    _classList = "classList",
    _className = "className",
    _parentNode = "parentNode",
    _fitWidth = "fit-width",
    _fitHeight = "fit-height",
    _fitBoth = "fit-both",
    _fitSmall = "fit-small",
    _fitClasses = _fitWidth + _space + _fitHeight + _space + _fitBoth + _space + _fitSmall,

    getData = function(node, key){
        return node.getAttribute("data-" + key);
    },

    getNaturalSize = function(node, key) {
        return node["natural" + key[0].toUpperCase() + key.slice(1)];
    },

    getSize = function(node, key) {
        return parseInt(getData(node, key) || getNaturalSize(node, key) || node[key]);
    },

    addClass = function(node, className) {
        node[_classList] ? node[_classList].add(className) : node[_className] += _space + className;
    },

    removeClass = function(node, className) {
        node[_className] = node[_className][_replace](new RegExp("\\b(" + className[_replace](/\s+/g, "|") + ")\\b", "g"), _space)[_replace](/\s+/g, _space)[_replace](/^\s+|\s+$/g, "");
    },

    setStyle = function(node, key, val) {
        node.style[key] = val + "px";
    },

    retry = {},

    setLayout = function(image, viewport, gutter, container, mode, threshold, width, height, viewportWidth, viewportHeight) {

        // If we're not taking dimensions from data attributes,
        // and we're not ready to take dimensions from natural attributes,
        // try again after 200ms  or give up after 5 seconds.
        if (!getData(image, _width) && getNaturalSize(image, _width)===0 && (image._retry || (image._retry = 0)) <= 25) {
            return setTimeout(function(){
                image._retry++; setLayout(image);
            }, 200);
        }

        // Get image viewport (b), gutter (u) and container (a).
        viewport  = image[_parentNode];
        gutter    = viewport[_parentNode];
        container = gutter[_parentNode];
        mode      = getData(viewport, "mode");
        threshold = getData(viewport, "threshold");
        width     = getSize(image, _width);
        height    = getSize(image, _height);
        viewportWidth  = viewport.offsetWidth;
        viewportHeight = viewport.offsetHeight;

        // Remove class
        removeClass(container, _fitClasses);

        // Add the correct classname to the image container
        addClass(container,

            // If image is smaller than threshold, show the
            // image exactly as it is inside the container.
            (width < threshold && height < threshold) ?
            (function(){
                // Enforce width & height in case the actual
                // image loaded is larger than dimension specified
                // from the data-width & data-height tag
                setStyle(image, _width, width);
                setStyle(image, _height, height);
                return _fitSmall;
            })()

            // Else determine if we should fit or fill the image within the viewport
            : mode=="cover" ?

            // If we should fill the image within the viewport,
            // determine strategy to fill the image within the viewport
            // by assessing the orientation of the image
            // against the orientation of the viewport.
            // https://gist.github.com/jstonne/2ea7077236a1245c397a
            (function(ratio, wRatio, hRatio){

                // When viewport's width & height is 0,
                // put it on the watchlist first. When resize
                // event is triggered, the actual layout will be set again.
                if (viewportWidth < 1 || viewportHeight < 1) {
                    watchList.push(image);
                    return _fitBoth;
                }

                ratio  = viewportWidth / viewportHeight;
                wRatio = viewportWidth / width;
                hRatio = viewportHeight / height;

                // Tall viewport
                if (ratio < 1) return (height * wRatio < viewportHeight) ? _fitHeight : _fitWidth;

                // Wide viewport
                if (ratio > 1) return (width * hRatio < viewportWidth) ? _fitWidth : _fitHeight;

                // Square viewport
                if (ratio==1) return (width/height <= 1) ? _fitWidth : _fitHeight;
            })()

            // If we should fit the image within the container
            // add the image to the watchlist because tall
            // images needs a fixed maxHeight. (Chrome/Webkit issue)
            :(function(){
                watchList.push(image);
                image.style.maxHeight = "none";
                setStyle(image, "maxHeight", viewport.offsetHeight);
                return _fitBoth;
            })()
        );

        // Remove onload attribute
        image.removeAttribute("onload");
    },

    updateLayout = function(image, imageList) {
        imageList = watchList;
        watchList = [];
        while (image = imageList.shift()) {
            image[_parentNode] && setLayout(image);
        }
    },

    watchList = [],

    watchTimer,

    watchLayout = function(){
        clearTimeout(watchTimer);
        watchTimer = setTimeout(updateLayout, 500);
    },

    imageList = $window.ESImageList || [];

    $window.ESImage = setLayout;
    $window.ESImageRefresh = updateLayout;

    if ($window.addEventListener) {
        $window.addEventListener("resize", watchLayout, false);
    } else {
        $window.attachEvent("resize", watchLayout);
    }

    while (imageList.length) {
        setLayout(imageList.shift());
    }

})(window);
*/
?>
<?php echo SOCIAL_FOUNDRY_BOOTCODE ?>.component("EasySocial", <?php echo $this->toJSON(); ?>);
!function(a){var x,b=" ",c="width",d="height",e="replace",f="classList",g="className",h="parentNode",i="fit-width",j="fit-height",k="fit-both",l="fit-small",m=i+b+j+b+k+b+l,n=function(a,b){return a.getAttribute("data-"+b)},o=function(a,b){return a["natural"+b[0].toUpperCase()+b.slice(1)]},p=function(a,b){return parseInt(n(a,b)||o(a,b)||a[b])},q=function(a,c){a[f]?a[f].add(c):a[g]+=b+c},r=function(a,c){a[g]=a[g][e](new RegExp("\\b("+c[e](/\s+/g,"|")+")\\b","g"),b)[e](/\s+/g,b)[e](/^\s+|\s+$/g,"")},s=function(a,b,c){a.style[b]=c+"px"},u=function(a,b,e,f,g,t,v,x,y,z){return!n(a,c)&&0===o(a,c)&&(a._retry||(a._retry=0))<=25?setTimeout(function(){a._retry++,u(a)},200):(b=a[h],e=b[h],f=e[h],g=n(b,"mode"),t=n(b,"threshold"),v=p(a,c),x=p(a,d),y=b.offsetWidth,z=b.offsetHeight,r(f,m),q(f,t>v&&t>x?function(){return s(a,c,v),s(a,d,x),l}():"cover"==g?function(b,c,d){return 1>y||1>z?(w.push(a),k):(b=y/z,c=y/v,d=z/x,1>b?z>x*c?j:i:b>1?y>v*d?i:j:1==b?1>=v/x?i:j:void 0)}():function(){return w.push(a),a.style.maxHeight="none",s(a,"maxHeight",b.offsetHeight),k}()),a.removeAttribute("onload"),void 0)},v=function(a,b){for(b=w,w=[];a=b.shift();)a[h]&&u(a)},w=[],y=function(){clearTimeout(x),x=setTimeout(v,500)},z=a.ESImageList||[];for(a.ESImage=u,a.ESImageRefresh=v,a.addEventListener?a.addEventListener("resize",y,!1):a.attachEvent("resize",y);z.length;)u(z.shift())}(window);
