/**
 * @package         Tabs
 * @version         5.1.10PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var nn_tabs_mode = nn_tabs_mode || 'click';
var nn_tabs_use_cookies = nn_tabs_use_cookies || 0;
var nn_tabs_set_cookies = nn_tabs_set_cookies || 0;
var nn_tabs_cookie_name = nn_tabs_cookie_name || '';
var nn_tabs_scroll = nn_tabs_scroll || 0;
var nn_tabs_linkscroll = nn_tabs_linkscroll || 0;
var nn_tabs_urlscroll = nn_tabs_urlscroll || 0;
var nn_tabs_slideshow_timeout = nn_tabs_slideshow_timeout || 0;
var nn_tabs_stop_slideshow_on_click = nn_tabs_stop_slideshow_on_click || 0;
var nn_tabs_use_hash = nn_tabs_use_hash || 0;
var nn_tabs_reload_iframes = nn_tabs_reload_iframes || 0;
var nn_tabs_init_timeout = nn_tabs_init_timeout || 0;

var nnTabs = null;

(function($) {
	"use strict";

	$(document).ready(function() {
		if (typeof( window['nn_tabs_use_hash'] ) != "undefined") {
			setTimeout(function() {
				nnTabs.init();
			}, nn_tabs_init_timeout);
		}
	});

	nnTabs = {
		timers: [],

		init: function() {
			var self = this;

			try {
				this.hash_id = decodeURIComponent(window.location.hash.replace('#', ''));
			} catch (err) {
				this.hash_id = '';
			}

			this.current_url = window.location.href;
			if (this.current_url.indexOf('#') !== -1) {
				this.current_url = this.current_url.substr(0, this.current_url.indexOf('#'));
			}

			// Remove the transition durations off to make initial setting of active tabs as fast as possible
			$('.nn_tabs').removeClass('has_effects');

			if (nn_tabs_use_cookies) {
				self.showByCookies();
			}

			this.showByURL();

			this.showByHash();

			this.initEqualHeights();

			setTimeout((function() {
				self.initActiveClasses();

				self.initResponsiveScrolling();

				self.initClickMode();

				self.initHoverMode();

				if (nn_tabs_use_cookies || nn_tabs_set_cookies) {
					self.initCookieHandling();
				}

				if (nn_tabs_use_hash) {
					self.initHashHandling();
				}

				self.initHashLinkList();

				if (nn_tabs_reload_iframes) {
					self.initIframeReloading();
				}

				self.initSlideshow();

				// Add the transition durations
				$('.nn_tabs').addClass('has_effects');
			}), 1000);

		},

		show: function(id, scroll, openparents, slideshow) {
			if (openparents) {
				this.openParents(id, scroll);
				return;
			}

			var self = this;
			var $el = this.getElement(id);

			if (!$el.length) {
				return;
			}

			if (scroll) {
				if (!$el.hasClass('in')) {
					$el.one('shown shown.bs.tab', function(e) {
						self.scroll(id);
					});
				}

				setTimeout(function() {
					self.scroll(id);
				}, 500);
			}

			$el.tab('show');

			$el.closest('ul.nav-tabs').find('.nn_tabs-toggle').attr('aria-selected', false);
			$el.attr('aria-selected', true);

			$el.closest('div.nn_tabs').find('.tab-content').first().children().attr('aria-hidden', true);
			$('div#' + id).attr('aria-hidden', false);

			this.updateActiveClassesOnTabLinks($el);

			if (!slideshow) {
				$el.focus();
			}
		},

		scroll: function(id) {
			var $el = this.getElement(id);

			if (!$el.length) {
				return;
			}

			if ($el.hasClass('nn_tabs-scrolling')) {
				return;
			}

			// Scroll to tab
			var $scrollto = $el.closest('ul:not(.dropdown-menu)').parent().find('.nn_tabs-scroll').first();

			if (!$scrollto.length) {
				return;
			}

			$el.addClass('nn_tabs-scrolling');

			$('html,body').animate(
				{scrollTop: $scrollto.offset().top},
				{
					complete: function() {
						$el.removeClass('nn_tabs-scrolling');
					}
				}
			);
		},

		fixEqualContentHeights: function() {
			$('.nn_tabs.bottom').each(function() {
				$(this).find('.tab-content').first().after($(this).find('.nav-tabs').first());
			});

			$('.nn_tabs.left').each(function() {
				if ($(window).width() <= 767 && $(this).hasClass('nn_tabs-responsive')) {
					$(this).find('.tab-content').first()
						.css('margin-left', 0)
						.css('min-height', 0);

					return;
				}

				$(this).find('.tab-content').first()
					.css('margin-left', $(this).find('.nav-tabs').first().width())
					.css('min-height', $(this).find('.nav-tabs').first().height());
			});

			$('.nn_tabs.right').each(function() {
				if ($(window).width() <= 767 && $(this).hasClass('nn_tabs-responsive')) {
					$(this).find('.tab-content').first()
						.css('margin-right', 0)
						.css('min-height', 0);

					return;
				}

				$(this).find('.tab-content').first()
					.css('margin-right', $(this).find('.nav-tabs').first().width())
					.css('min-height', $(this).find('.nav-tabs').first().height());
			});
		},

		getElement: function(id) {
			return this.getTabElement(id);
		},

		getTabElement: function(id) {
			return $('a.nn_tabs-toggle[data-id="' + id + '"]');
		},

		getSliderElement: function(id) {
			return $('#' + id + '.nn_sliders-body');
		},

		showByCookies: function() {
			var cookies = $.cookie(nn_tabs_cookie_name);
			if (!cookies) {
				return;
			}

			cookies = cookies.split('___');
			for (var i = 0; i < cookies.length; i++) {
				var keyval = cookies[i].split('=');
				if (keyval.length < 2) {
					continue;
				}

				var key = keyval.shift();
				if (key.substr(0, 11) != 'set-nn_tabs') {
					continue;
				}

				this.openParents(decodeURIComponent(keyval.join('=')), 0);
			}
		},

		showByURL: function() {
			var id = this.getUrlVar();

			if (id == '') {
				return;
			}

			this.showByID(id);
		},

		showByHash: function() {
			if (this.hash_id == '') {
				return;
			}

			var id = this.hash_id;

			if (id == '' || id.indexOf("&") != -1 || id.indexOf("=") != -1) {
				return;
			}

			// hash is a text anchor
			if ($('a.nn_tabs-toggle[data-id="' + id + '"]').length == 0) {
				this.showByHashAnchor(id);

				return;
			}

			// hash is a tab
			if (!nn_tabs_use_hash) {
				return;
			}

			if (!nn_tabs_urlscroll) {
				// Prevent scrolling to anchor
				$('html,body').animate({scrollTop: 0});
			}

			this.showByID(id);
		},

		showByHashAnchor: function(id) {
			if (id == '') {
				return;
			}

			var $anchor = $('a#anchor-' + id);

			if ($anchor.length == 0) {
				$anchor = $('a#' + id);
			}

			if ($anchor.length == 0) {
				return;
			}

			// Check if anchor has a parent tab
			if ($anchor.closest('.nn_tabs').length == 0) {
				return;
			}

			var $tab = $anchor.closest('.tab-pane').first();

			// Check if tab has sliders. If so, let Sliders handle it.
			if ($tab.find('.nn_sliders').length > 0) {
				return;
			}

			this.openParents($tab.attr('id'), 0);

			setTimeout(function() {
				$('html,body').animate({scrollTop: $anchor.offset().top});
			}, 250);
		},

		showByID: function(id) {
			var $el = $('a.nn_tabs-toggle[data-id="' + id + '"]');

			if ($el.length == 0) {
				return;
			}

			this.openParents(id, nn_tabs_urlscroll);
		},

		openParents: function(id, scroll) {
			var $el = this.getElement(id);

			if (!$el.length) {
				return;
			}

			var parents = new Array;

			var parent = this.getElementArray($el);
			while (parent) {
				parents[parents.length] = parent;
				parent = this.getParent(parent.el);
			}

			if (!parents.length) {
				return false;
			}

			this.stepThroughParents(parents, null, scroll);
		},

		stepThroughParents: function(parents, parent, scroll) {
			var self = this;

			if (!parents.length && parent) {
				if (scroll) {
					if (typeof(scroll) == 'object') {
						$('html,body').animate({scrollTop: $(scroll).offset().top});
					} else {
						self.scroll(parent.id);
					}
				}

				parent.el.focus();
				return;
			}

			parent = parents.pop();

			if (parent.el.hasClass('in') || parent.el.parent().hasClass('active')) {
				self.stepThroughParents(parents, parent, scroll);
				return;
			}

			switch (parent.type) {
				case 'tab':
					if (typeof( window['nnTabs'] ) == "undefined") {
						self.stepThroughParents(parents, parent, scroll);
						break;
					}

					parent.el.one('shown shown.bs.tab', function(e) {
						self.stepThroughParents(parents, parent, scroll);
					});

					nnTabs.show(parent.id);
					break;

				case 'slider':
					if (typeof( window['nnSliders'] ) == "undefined") {
						self.stepThroughParents(parents, parent, scroll);
						break;
					}

					parent.el.one('shown shown.bs.collapse', function(e) {
						self.stepThroughParents(parents, parent, scroll);
					});

					nnSliders.show(parent.id);
					break;
			}
		},

		getParent: function($el) {
			if (!$el) {
				return false;
			}

			var $parent = $el.parent().closest('.nn_tabs-pane, .nn_sliders-body');

			if (!$parent.length) {
				return false;
			}

			var parent = this.getElementArray($parent);

			return parent;
		},

		getElementArray: function($el) {
			var id = $el.attr('data-toggle') ? $el.attr('data-id') : $el.attr('id');
			var type = ($el.hasClass('nn_tabs-pane') || $el.hasClass('nn_tabs-toggle')) ? 'tab' : 'slider'

			return {
				'type': type,
				'id'  : id,
				'el'  : type == 'tab' ? this.getTabElement(id) : this.getSliderElement(id)
			};
		},

		fixEqualHeights: function(parent) {
			var self = this;
			setTimeout((function() {
				self.fixEqualTabHeights(parent);
			}), 250);

			setTimeout((function() {
				self.fixEqualContentHeights(parent);
			}), 500);
		},

		fixEqualTabHeights: function(parent) {
			var self = this;
			parent = parent ? 'div.nn_tabs-pane#' + parent.attr('data-id') : 'div.nn_tabs';

			$(parent + ' ul.nav-tabs').each(function() {
				var height = 0;
				$(this).children().each(function() {
					var link = $(this).find('a').first();
					link.height('auto');
					height = Math.max(height, link.height());
				});
				$(this).children().each(function() {
					$(this).find('a').first().height(height);
				});
			});
		},

		initActiveClasses: function() {
			$('li.nn_tabs-tab-sm').removeClass('active');
		},

		updateActiveClassesOnTabLinks: function(active_el) {
			active_el.parent().parent().find('.nn_tabs-toggle').each(function($i, el) {
				$('a.nn_tabs-link[data-id="' + $(el).attr('data-id') + '"]').each(function($i, el) {
					var $link = $(el);

					if ($link.attr('data-toggle') || $link.hasClass('nn_tabs-toggle-sm') || $link.hasClass('nn_sliders-toggle-sm')) {
						return;
					}

					if ($link.attr('data-id') !== active_el.attr('data-id')) {
						$link.removeClass('active');
						return;
					}

					$link.addClass('active');
				});
			});
		},

		initEqualHeights: function() {
			var self = this;

			self.fixEqualHeights();

			$('a.nn_tabs-toggle').on('shown shown.bs.tab', function(e) {
				self.fixEqualHeights($(this));
			});

			$(window).resize(function() {
				self.fixEqualHeights();
			});
		},

		initHashLinkList: function() {
			var self = this;

			$('a[href^="#"],a[href^="' + this.current_url + '#"]').each(function($i, el) {
				self.initHashLink(el);
			});
		},

		initHashLink: function(el) {
			var self = this;
			var $link = $(el);

			// link is a tab or slider or list link, so ignore
			if ($link.attr('data-toggle') || $link.hasClass('nn_aliders-link') || $link.hasClass('nn_tabs-toggle-sm') || $link.hasClass('nn_sliders-toggle-sm')) {
				return;
			}

			var id = $link.attr('href').substr($link.attr('href').indexOf('#') + 1);

			// No id found
			if (id == '') {
				return;
			}

			var $anchor = $('a#anchor-' + id);

			// No accompanying link found
			if ($anchor.length == 0) {
				return;
			}

			// Check if anchor has a parent tab
			if ($anchor.closest('.nn_tabs').length == 0) {
				return;
			}

			var $tab = $anchor.closest('.tab-pane').first();
			var tab_id = $tab.attr('id');

			// Check if link is inside the same tab
			if ($link.closest('.nn_tabs').length > 0) {
				if ($link.closest('.tab-pane').first().attr('id') == tab_id) {
					return;
				}
			}

			$link.click(function(e) {
				// Open parent tab and parents
				self.openParents(tab_id, nn_tabs_linkscroll);
				e.stopPropagation();
			});
		},

		initHashHandling: function(el) {
			if (window.history.replaceState) {
				$('a.nn_tabs-toggle').on('shown shown.bs.tab', function(e) {
					if ($(this).closest('div.nn_tabs').hasClass('slideshow')) {
						return;
					}

					var id = $(this).attr('data-id');
					history.replaceState({}, '', '#' + id);
					e.stopPropagation();
				});
			}
		},

		initClickMode: function() {
			var self = this;

			$('body').on('click.tab.data-api', 'a.nn_tabs-toggle', function(e) {
				var $el = $(this);

				e.preventDefault();

				nnTabs.show($el.attr('data-id'), $el.hasClass('nn_tabs-doscroll'));

				if (self.timers[$el.closest('ul.nav-tabs').attr('id')]) {
					clearTimeout(self.timers[$el.closest('ul.nav-tabs').attr('id')]);

					if (!nn_tabs_stop_slideshow_on_click) {
						self.startSlideshow($el);
					}
				}

				e.stopPropagation();
			});
		},

		initHoverMode: function() {
			$('li.hover > a.nn_tabs-toggle').hover(function(e) {
				var $el = $(this);

				if (nn_tabs_mode != 'hover' && !$el.parent().hasClass('hover')) {
					return;
				}

				if (nn_tabs_mode == 'hover' && $el.parent().hasClass('click')) {
					return;
				}

				e.preventDefault();
				nnTabs.show($(this).attr('data-id'));
			});
		},

		initCookieHandling: function() {
			var self = this;

			$('a.nn_tabs-toggle').on('show show.bs.tab', function(e) {
				var id = $(this).attr('data-id');
				var $el = self.getElement(id);

				var set = 0;
				$el.closest('ul:not(.dropdown-menu)').each(function($i, el) {
					set = el.id;
				});

				var obj = {};
				var cookies = $.cookie(nn_tabs_cookie_name);
				if (cookies) {
					cookies = cookies.split('___');
					for (var i = 0; i < cookies.length; i++) {
						var keyval = cookies[i].split('=');
						if (keyval.length > 1 && keyval[0] != set) {
							var key = keyval.shift();
							if (key.substr(0, 11) == 'set-nn_tabs') {
								obj[key] = keyval.join('=');
							}
						}
					}
				}
				obj['set-nn_tabs-' + set] = id;

				var arr = [];
				for (var i in obj) {
					if (i && obj[i]) {
						arr[arr.length] = i + '=' + obj[i];
					}
				}

				$.cookie(nn_tabs_cookie_name, arr.join('___'));
			});
		},

		initResponsiveScrolling: function() {
			var self = this;

			$('.nav-tabs-sm a.nn_tabs-link').click(function() {
				var $el = self.getElement($(this).attr('data-id'));
				$('html,body').animate({scrollTop: $el.offset().top});
			});
		},

		initIframeReloading: function() {
			var self = this;

			$('.tab-pane.active iframe').each(function() {
				$(this).attr('reloaded', true);
			});

			$('a.nn_tabs-toggle').on('show show.bs.tab', function(e) {
				// Re-inintialize Google Maps on tabs show
				if (typeof initialize == 'function') {
					initialize();
				}

				var $el = $('#' + $(this).attr('data-id'));

				$el.find('iframe').each(function() {
					if (this.src && !$(this).attr('reloaded')) {
						this.src += '';
						$(this).attr('reloaded', true);
					}
				});
			});

			$(window).resize(function() {
				if (typeof initialize == 'function') {
					initialize();
				}

				$('.tab-pane iframe').each(function() {
					$(this).attr('reloaded', false);
				});

				$('.tab-pane.active iframe').each(function() {
					if (this.src) {
						this.src += '';
						$(this).attr('reloaded', true);
					}
				});
			});
		},

		initSlideshow: function() {
			var self = this;

			$('div.nn_tabs.slideshow ul.nav-tabs > .nn_tabs-tab.active').each(function() {
				self.startSlideshow($(this).find('.nn_tabs-toggle').first());
			});
		},

		startSlideshow: function($el) {
			var self = this;

			var $ul = $el.closest('ul.nav-tabs');

			var timeout = $ul.attr('data-slideshow-timeout');
			timeout = timeout > 1 ? timeout : nn_tabs_slideshow_timeout;

			this.timers[$ul.attr('id')] = setTimeout((function() {
				var $active_el = $ul.find('.nn_tabs-tab.active > .nn_tabs-toggle').first();
				self.openNext($active_el, true);
			}), timeout);
		},

		openNext: function($el, slideshow) {
			var $next = this.getNextTab($el);

			if ($el.attr('data-id') == $next.attr('data-id')) {
				return;
			}

			this.show($next.attr('data-id'), false, false, slideshow);

			if (slideshow) {
				this.startSlideshow($next);
			}
		},

		openPrevious: function($el) {
			var $previous = this.getPreviousTab($el);

			if ($el.attr('data-id') == $previous.attr('data-id')) {
				return;
			}

			this.show($previous.attr('data-id'));
		},

		getNextTab: function($el) {
			if ($el.parent().next().length) {
				return $el.closest('.nn_tabs-tab').next().find('.nn_tabs-toggle').first();
			}

			return $el.closest('ul.nav-tabs').find('.nn_tabs-tab > .nn_tabs-toggle').first();
		},

		getPreviousTab: function($el) {
			if ($el.parent().previous().length) {
				return $el.closest('.nn_tabs-tab').previous().find('.nn_tabs-toggle').first();
			}

			return $el.closest('ul.nav-tabs').find('.nn_tabs-tab > .nn_tabs-toggle').last();
		},

		getUrlVar: function() {
			var search = 'tab';
			var query = window.location.search.substring(1);

			if (query.indexOf(search + '=') == -1) {
				return '';
			}

			var vars = query.split('&');
			for (var i = 0; i < vars.length; i++) {
				var keyval = vars[i].split('=');

				if (keyval[0] != search) {
					continue;
				}

				return keyval[1];
			}

			return '';
		}
	};
})
(jQuery);
