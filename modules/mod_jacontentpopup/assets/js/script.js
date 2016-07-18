/**
 * ------------------------------------------------------------------------
 * JA Content Popup Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

;(function($){
	var blank = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

	$.fn.jaload = function(option){
		var opts = $.extend({onload: false}, $.isFunction(option) ? {onload: option} : option),
			jimgs = this.find('img').add(this.filter('img')),
			total = jimgs.length,
			loaded = [],
			onload = function(){
				if(this.src === blank || $.inArray(this, loaded) !== -1){
					return;
				}

				loaded.push(this);

				$.data(this, 'JAImgLoad', {src: this.src});
				if (total === loaded.length){
					$.isFunction(opts.onload) && setTimeout(opts.onload);
					jimgs.unbind('.JAImgLoad');
				}
			};

		if (!total){
			$.isFunction(opts.onload) && opts.onload();
		} else {
			jimgs.on('load.JAImgLoad error.JAImgLoad', onload).each(function(i, el){
				var src = el.src,
					cached = $.data(el, 'JAImgLoad');

				if(cached && cached.src === src){
					onload.call(el);
					return;
				}

				if(el.complete && el.naturalWidth !== undefined){
					onload.call(el);
					return;
				}

				if(el.readyState || el.complete){
					el.src = blank;
					el.src = src;
				}
			});
		}

		return this;
	};
})(jQuery);
 
;(function($){
	
	var defaults = {
		effects: [
			'slice-down-right',			//animate height and opacity
			'slice-down-left',
			'slice-up-right',
			'slice-up-left',

			'slice-updown-right',				//slide up alternate column
			'slice-updown-left',
			
			'slice-down-right-inv',				//look like above, slide from an offset, but use the current image instead of the new image
			'slice-down-left-inv',
			'slice-up-right-inv',
			'slice-up-left-inv',
		
			'slice-left-right',
			'slice-left-left',

			'slice-right-right',
			'slice-right-left',

			'slice-zoomin-right',			//slide and offset fade
			'slice-zoomin-left',

			'slice-zoomrotate-right',
			'slice-zoomrotate-left'
		],
		
		animation: 'fade', 							//[fade, vrtslide, hrzslide, fullslide, slice], slide and fade for old compactible, fullslide is horizontal only
		direction: 'horizontal', 					//depend on [animation=slide] - [horizontal, vertical] - slide direction of main item for move animation
		
		duration: 1000,								//duration - time for animation
		
		//private:
		repeat: true,								//animation repeat or not
		autoPlay: false,							//auto play
		interval: 5000,								//interval - time for between animation	
		
		rtl: null,									//rtl - for future
		
		navButtons: false,							//[next, prev] buttons of other position, control by css, even overwrite html structure, everywhere but not in main block
		
		urls: false, 								// [] array of url of main items
		targets: false 								// [] same as urls, an array of target value such as, '_blank', 'parent', '' - default
	};
	
	function jacp(elm, options){
		this.element = $(elm);
		this.options = $.extend({}, defaults, options);
		this.initialize();
	};
	
	jacp.prototype = {
	
		initialize: function () {
			var slider = this.element,
				options = this.options,
				mainWrap = slider.find('.ja-cp-main-wrap'),
				mainFrame = slider.find('.ja-cp-main'),
				pageList = slider.find('.ja-cp-pagelist'),
				pages = pageList.children();
				
			if(options.animation == 'slidevrt'){
				options.direction = 'vertical';
			} else if (options.animation == 'slidehrz'){
				options.direction = 'horizontal';
			}

			//filter effects
			if (options.animation == 'slicevrt'){
				options.effects = options.effects.slice(0, 10);
			} else if(options.animation == 'slicehrz'){
				options.effects = options.effects.slice(10, 14);
			} else if (options.animation == 'slicezoom'){
				options.effects = options.effects.slice(14, 18);
			}

			options.animation = options.animation.substr(0, 5);

			//first load
			var selpage = pages.filter('.active'),
				sgroup = mainFrame.find('.ja-cp-group').addClass('active');

			selpage.data('loaded', 1).data('page', sgroup[0]);

			sgroup.find('.ja-cp-item').each(function(){
				$(this).data('orgclass', $(this).attr('class'));	
			});

			var	vars = {
				slider: slider,
				mainWrap: mainWrap,
				mainFrame: mainFrame,
				
				pageList: pageList,
				pages: pages,
				total: pages.length,
				curidx: 0,
				nextidx: -1,
				curpage: sgroup,
				
				running: 0,
				stop: 0,
				timer: 0,

				visible: {float: 'left', position: 'relative', zIndex: 2, display: 'block'},
        		hidden: {float: 'none', position: 'absolute', zIndex: 1, display: 'block'},

        		sliceTime: 150,
				
				modes: (options.direction == 'horizontal' ? (options.rtl == 'rtl' ? ['right', 'width'] : ['left', 'width']) : ['top', 'height']),
				
				finished: $.proxy(this.animFinished, this),
				hidefinish: this.endAnim
			};
			
			this.vars = vars;

			this.initLoader();
			this.initPaging();
			this.initNavAction();
			
			vars.direct = 'next';
			slider.css('visibility', 'visible');
			
			this.prepare(false, vars.curidx);
			this.animFinished();
		},
		
		prev: function(force){
			var vars = this.vars;
			if(vars.running && !force){
				return false;
			}
			vars.direct = 'prev';
			this.prepare(force, vars.curidx -1);
			
			return false;
		},
		
		next: function(force){
			var vars = this.vars;
			if(vars.running && !force){
				return false;
			}
			vars.direct = 'next';
			this.prepare(force, vars.curidx +1);
			
			return false;
		},

		stop: function(){
			clearInterval(this.vars.timer);
			this.vars.stop = 1;
			
			return false;
		},
		
		start: function(){
			var vars = this.vars;
			
			clearTimeout(vars.timer);
			vars.timer = setTimeout($.proxy(this[this.vars.direct], this), this.options.interval)
		},
		
		loadpage: function(idx){
			var self = this,
				vars = this.vars,
				page = vars.pages.eq(idx),
				pagelink = page.find('span'),
				params = pagelink.attr('data-ref');

			if(params){
				params = $.parseJSON(params);
			}

			if(params){

				$.ajax({
					url: this.options.baseurl + 'modules/mod_jacontentpopup/admin/loadcontent.php',
					data : params,
					success: function(data) {
						if(data) {
							var group = $(data).appendTo(vars.mainFrame).css(vars.hidden);
							page.data('loaded', 1).data('page', group[0]);

							self.initgroup(group);

							group.jaload(function(){
								if(vars.nextidx == idx){
									if(vars.loader){
										vars.loader.stop().fadeTo(500, 0, function(){
											$(this).hide();
										});
									}
									
									self.run(false, idx);
								} else if(vars.nextidx == -1 && vars.loader){
									vars.loader.stop().fadeTo(500, 0, function(){
										$(this).hide();
									});
								}
								if(typeof jaloadyoxview != "undefined" )
									jaloadyoxview(params.modulesid);
							});
							
						}
					}
				});
			}
		},

		initgroup: function(group){
			if(this.options.animation == 'slice'){
				group.find('.ja-cp-item').each(function(){
					$(this).data('orgclass', $(this).attr('class')).addClass('top'); //hide
				});
			} else if(this.options.animation == 'fade'){
				group.css('opacity', 0);
			}
		},
		
		prepare: function(force, idx){
			var vars = this.vars,
				options = this.options;
				
			if(options.animation === 'slice' && vars.running){
				return false;
			}
			
			if(idx >= vars.total){ 
				idx = 0;
			}
			
			if(idx < 0){
				idx = vars.total - 1;
			}
			
			var	curpage = vars.pages.eq(idx);
			
			vars.nextidx = idx;
			
			clearTimeout(vars.timer);
			
			if(curpage.data('loaded')){
				if(idx == vars.curidx){
					return false;
				}
			
				this.run(force, idx);
			} else{
				
				if(vars.loader){
					vars.loader.show().stop().animate({opacity: 0.8});
				}
				
				if(!curpage.data('loading')){
					curpage.data('loading', 1);

					this.loadpage(idx);
				}
			}
			
			return false;
		},
		
		run: function(force, idx){
			var vars = this.vars,
				options = this.options;
				
			if(vars.curidx == idx){
				return false;
			}			
			
			vars.pages.eq(vars.curidx).removeClass('active');
			vars.pages.eq(idx).addClass('active');

			$(vars.pages.eq(vars.curidx).data('page')).removeClass('active'),
			$(vars.pages.eq(idx).data('page')).addClass('active');

			if(this[options.animation]){
				this[options.animation](force, idx);
			}else{
				this.fade(force, idx);
			}
		},
		
		slide: function(force, idx){
			var options = this.options,
				vars = this.vars;
				
			if(idx != vars.curidx){
				var jprev = $(vars.pages.eq(vars.curidx).data('page')).stop().css(vars.visible),
					jnext = $(vars.pages.eq(idx).data('page')).stop().css(vars.visible),
					prevanim = {}, nextcss = {}, nextanim = {};

				vars.mainFrame.stop(true).css({
					width: jprev.outerWidth(true),
					height: jprev.outerHeight(true)
				}).animate({
					width: jnext.outerWidth(true),
					height: jnext.outerHeight(true)
				}, 500, function(){
					vars.mainFrame.css({width: '', height: ''});
				});

				jprev.css(vars.hidden);

				if(vars.direct == 'next' || (vars.direct == 'none' && idx > vars.curidx)){
					prevanim[vars.modes[0]] = '-100%';
					nextcss[vars.modes[0]] = '100%';
				} else if (vars.direct == 'prev' || (vars.direct == 'none' && idx < vars.curidx)){
					prevanim[vars.modes[0]] = '100%';
					nextcss[vars.modes[0]] = '-100%';
				}

				nextanim[vars.modes[0]] = '0%';

				jprev.animate(prevanim, options.duration, vars.hidefinish);
				jnext.css(nextcss).animate(nextanim, options.duration, vars.finished);
			}

			vars.curidx = idx;
		},
		
		fade: function(force, idx){
			var options = this.options,
				vars = this.vars;
				
			if(idx != vars.curidx){
				var jprev = $(vars.pages.eq(vars.curidx).data('page')).stop().css(vars.visible),
					jnext = $(vars.pages.eq(idx).data('page')).stop().css(vars.visible);

				vars.mainFrame.stop(true).css({
					width: jprev.outerWidth(true),
					height: jprev.outerHeight(true)
				}).animate({
					width: jnext.outerWidth(true),
					height: jnext.outerHeight(true)
				}, 500, function(){
					vars.mainFrame.css({width: '', height: ''});
				});

				jprev.css(vars.hidden).fadeTo(options.duration, 0, vars.hidefinish);
				jnext.fadeTo(options.duration + 200, 1, vars.finished);
			}
			
			vars.curidx = idx;
		},
		
		slice: function(force, idx){
		
			var options = this.options,
				vars = this.vars,
				container = vars.mainFrame,
				oldpage = vars.curpage;
			
			//Set vars.curpage
			vars.curidx = idx;
			vars.curpage = $(vars.pages.eq(vars.curidx).data('page'));
			
			//Generate random effect
			var	effect = options.effects[Math.floor(Math.random() * (options.effects.length))];
			if(effect == undefined){
				effect = 'fading';
			}
			
			//Run effects
			var effects = effect.split('-'),
				callfun = 'anim' + effects[0];
			
			if(this[callfun]){
			
				vars.running = true;
				this[callfun](effects, oldpage, vars.curpage);
			}
		},
		
		animFinished: function(hide){ 
			var options = this.options,
				vars = this.vars;
				
			vars.running = false;
			
			
			if (!vars.stop && (options.autoPlay && (vars.curidx < vars.total -1 || options.repeat))) {
				this.start();
			}
		},

		endAnim: function(){
			$(this).closest('.ja-cp-group').css('display', 'none');
		},
		
		animslice: function(effects, oldpage, curpage){
			var options = this.options,
				vars = this.vars;

			oldpage.css(vars.visible);
			curpage.css(vars.visible);

			vars.mainFrame.stop(true).css({
				width: oldpage.outerWidth(true),
				height: oldpage.outerHeight(true)
			}).animate({
				width: curpage.outerWidth(true),
				height: curpage.outerHeight(true)
			}, 500, function(){
				vars.mainFrame.css({width: '', height: ''});
			});

			oldpage.css(vars.hidden);
			
			var oldslices = $.makeArray(oldpage.children()),
				slices = $.makeArray(curpage.children()),
				classOff = '',
				oldlast = oldslices.length - 1,
				last = slices.length -1,
				timeBuff = 100;
			
			// by default, animate is sequence from left to right
			if(effects[2] == 'left'){		// reverse the direction, so animation is sequence from right to left
				oldslices = oldslices.reverse();
				slices = slices.reverse();
			}
			
			if(effects[1] == 'updown'){
				
				$.each(oldslices, function(i, slice){
					setTimeout(function(){
						$(slice)
							.removeClass('no-anim').addClass((i & 1) == 0 ? 'top' : 'bottom'); //animate to hidden area
					
						if(i == oldlast){
							setTimeout($.proxy(vars.hidefinish, slice), options.duration);
						}

					}, timeBuff);
					
					timeBuff += vars.sliceTime;
				});

				
				$.each(slices, function(i, slice){

					setTimeout(function(){
						$(slice)
							.addClass('no-anim').addClass((i & 1) == 0 ? 'top' : 'bottom') //set to some hidden position
							.attr('class', $(slice).data('orgclass'));	// animate to stable state

						if(i == last){
							setTimeout(vars.finished, options.duration);
						}

					}, timeBuff);
					
					timeBuff += vars.sliceTime;
				});			

				return true;
			}
			
			else if(effects[1] == 'down'){
				$(slices).addClass('no-anim top');
				classOff = 'bottom';
			}
			else if(effects[1] == 'up'){
				$(slices).addClass('no-anim bottom');
				classOff = 'top';
			}
			else if(effects[1] == 'left'){
				$(slices).addClass('no-anim left');
				classOff = 'right';
			}
			else if(effects[1] == 'right'){
				$(slices).addClass('no-anim right');
				classOff = 'left';
			}

			else if(effects[1] == 'zoomin'){
				classOff = 'zoomin';
			}

			else if(effects[1] == 'zoomrotate'){
				classOff = 'zoomrotate';
			}
			
			$.each(oldslices, function(i, slice){
				
				setTimeout(function(){
					$(slice)
						.removeClass('no-anim').addClass(classOff); //animate to hidden area

					if(i == oldlast){
						setTimeout($.proxy(vars.hidefinish, slice), options.duration);
					}

				}, timeBuff);
				
				timeBuff += vars.sliceTime;
			});

			$.each(slices, function(i, slice){

				setTimeout(function(){
					$(slice)
						.addClass('no-anim').addClass(classOff) //set to some hidden position
						.attr('class', $(slice).data('orgclass'));	// animate to stable state

					if(i == last){
						setTimeout(vars.finished, options.duration);
					}

				}, timeBuff);
				
				timeBuff += vars.sliceTime;
			});
		},
		
		initPaging: function () {
			var vars = this.vars,
				self = this;

			vars.pageList.on('click', 'li', function(){
				vars.direct = 'none';

				self.prepare(true, $(this).index());

				return false;
			});
		},

		initNavAction: function () {
			var options = this.options,
				vars = this.vars,
				slider = this.vars.slider,
				controls = ['prev', 'next'],
				btnarr;
				
			for (var j = 0, jl = controls.length; j < jl; j++) {
				if(this[controls[j]]){
					btnarr = slider.find('.ja-cp-' + controls[j]);
					
					for (var i = 0, il = btnarr.length; i < il; i++) {
						btnarr.eq(i).bind('click', $.proxy(this[controls[j]], this, true));
					}
				}
			}
			
			var jcontrols = $('.ja-cp-controls');
			if(!options.navButtons){
				jcontrols.css('display', 'none');
			}
		},
		
		initLoader: function(){
			var vars = this.vars,
				loader = vars.slider.find('.ja-cp-loader');
				
			if(!loader){
				return false;
			}
			
			$.extend(vars, {
				loader: loader
			});
		}
	};
	
	$.fn.jacp = function(options){
		return this.each(function(){
			new jacp(this, options);
		});
	};
})(jQuery);