/**
 * =============================================================
 * RAXO All-mode K2 J3.x - Template JS
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @subpackage	All-mode Bricks Template
 * @copyright	Copyright (C) 2014 RAXO Group
 * @license		RAXO Commercial License
 * 				This file is forbidden for redistribution
 * @link		http://www.raxo.org
 * =============================================================
 */


/* Debounced Resize Events (Copyright 2012 @louis_remi) */
(function(b){var a=b.event,c,d;c=a.special.debouncedresize={setup:function(){b(this).on("resize",c.handler)},teardown:function(){b(this).off("resize",c.handler)},handler:function(b,f){var e=this,h=arguments,g=function(){b.type="debouncedresize";a.dispatch.apply(e,h)};d&&clearTimeout(d);f?g():d=setTimeout(g,c.threshold)},threshold:150}})(jQuery); 

/* jQuery Grid-A-Licious(tm) v3.01 (Copyright 2012 Andreas Pihlström) */
(function(b){b.Gal=function(a,c){this.box=b(c);this._init(a)};b.Gal.settings={selector:".allmode-item",width:220,gutter:20,animate:!1,animationOptions:{speed:200,duration:300,effect:"fadeInOnAppear",queue:!0,complete:function(){}}};b.Gal.prototype={_init:function(a){var c=this;this.name=this._setName(5);this.gridArr=[];this.gridArrAppend=[];this.gridArrPrepend=[];this.setGrid=this.setArr=!1;this.itemCount=this.cols=0;this.isPrepending=!1;this.appendCount=0;this.ifCallback=this.resetCount=!0;this.options= b.extend(!0,{},b.Gal.settings,a);this.gridArr=b.makeArray(this.box.find(this.options.selector));this.isResizing=!1;this.w=0;this.boxArr=[];this._setCols();this._renderGrid("append");b(this.box).addClass("gridalicious").css("margin","0 -"+this.options.gutter+"px");b(window).on("debouncedresize",function(a){c.resize()})},_setName:function(a,b){b=b?b:"";return a?this._setName(--a,"0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz".charAt(Math.floor(60*Math.random()))+b):b},_setCols:function(){var a= this.box.width();this.cols=Math.max(Math.floor(a/this.options.width),1);this.w=(this.options.width+(a-this.cols*this.options.width-this.options.gutter)/this.cols)/a*100;for(a=0;a<this.cols;a++){var c=b("<div></div>").addClass("allmode-bricks-column").attr("id","item"+a+this.name).css({float:"left",width:this.w+"%",paddingLeft:this.options.gutter});this.box.append(c)}},_renderGrid:function(a,c,d){var k=[],f=[],e=0,h=this.appendCount,g=this.cols,l=this.name;c?(f=c,"append"==a&&(h+=d,e=this.appendCount),"prepend"== a&&(this.isPrepending=!0,e=Math.round(d%g),0>=e&&(e=g)),"renderAfterPrepend"==a&&(h+=d,e=d)):(f=this.gridArr,h=b(this.gridArr).length);b.each(f,function(c,d){var f=b(d);f.css({filter:"alpha(opacity=0)",opacity:"0"});"prepend"==a?(e--,b("#item"+e+l).prepend(f),k.push(f),0===e&&(e=g)):(b("#item"+e+l).append(f),k.push(f),e++,e>=g&&(e=0),h>=g&&(h-=g))});this.appendCount=h;this.itemCount=e;"append"==a||"prepend"==a?("prepend"==a&&this._updateAfterPrepend(this.gridArr,f),this._renderItem(k),this.isPrepending= !1):this._renderItem(this.gridArr)},_collectItems:function(){var a=[];b(this.box).find(this.options.selector).each(function(c){a.push(b(this))});return a},_renderItem:function(a){var c=this.options.animationOptions.speed,d=this.options.animationOptions.effect,k=this.options.animationOptions.duration,f=this.options.animationOptions.queue,e=this.options.animationOptions.complete,h=0,g=0;!0!==this.options.animate||this.isResizing?(b.each(a,function(a,c){b(c).css({opacity:"1",filter:"alpha(opacity=1)"})}), this.ifCallback&&e.call(a)):(!0===f&&"fadeInOnAppear"==d?(this.isPrepending&&a.reverse(),b.each(a,function(d,f){setTimeout(function(){b(f).animate({opacity:"1.0"},k);g++;g==a.length&&e.call(void 0,a)},h*c);h++})):!1===f&&"fadeInOnAppear"==d&&(this.isPrepending&&a.reverse(),b.each(a,function(c,d){b(d).animate({opacity:"1.0"},k);g++;g==a.length&&this.ifCallback&&e.call(void 0,a)})),!0!==f||d||b.each(a,function(c,d){b(d).css({opacity:"1",filter:"alpha(opacity=1)"});g++;g==a.length&&this.ifCallback&& e.call(void 0,a)}))},_updateAfterPrepend:function(a,c){var d=this.gridArr;b.each(c,function(a,b){d.unshift(b)});this.gridArr=d},resize:function(){this.box.find(this.options.selector).unwrap();this._setCols();this.ifCallback=!1;this.isResizing=!0;this._renderGrid("append");this.ifCallback=!0;this.isResizing=!1},append:function(a){var c=this.gridArr,d=this.gridArrPrepend;b.each(a,function(a,b){c.push(b);d.push(b)});this._renderGrid("append",a,b(a).length)},prepend:function(a){this.ifCallback=!1;this._renderGrid("prepend", a,b(a).length);this.ifCallback=!0}};b.fn.gridalicious=function(a,c){"string"===typeof a?this.each(function(){var d=b.data(this,"gridalicious");d[a].apply(d,[c])}):this.each(function(){b.data(this,"gridalicious",new b.Gal(a,this))});return this}})(jQuery);