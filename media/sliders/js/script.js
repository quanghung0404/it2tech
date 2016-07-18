/**
 * @package         Sliders
 * @version         5.1.11PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var nn_sliders_mode = nn_sliders_mode || 'click';
var nn_sliders_use_cookies = nn_sliders_use_cookies || 0;
var nn_sliders_set_cookies = nn_sliders_set_cookies || 0;
var nn_sliders_cookie_name = nn_sliders_cookie_name || '';
var nn_sliders_scroll = nn_sliders_scroll || 0;
var nn_sliders_linkscroll = nn_sliders_linkscroll || 0;
var nn_sliders_urlscroll = nn_sliders_urlscroll || 0;
var nn_sliders_scrolloffset = nn_sliders_scrolloffset || 0;
var nn_sliders_use_hash = nn_sliders_use_hash || 0;
var nn_sliders_reload_iframes = nn_sliders_reload_iframes || 0;
var nn_sliders_init_timeout = nn_sliders_init_timeout || 0;

var nnSliders = null;

(function($) {
	"use strict";

	$(document).ready(function() {
		if (typeof( window['nn_sliders_use_hash'] ) != "undefined") {
			setTimeout(function() {
				nnSliders.init();
			}, nn_sliders_init_timeout);
		}
	});

	nnSliders = {
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
			$('.nn_sliders').removeClass('has_effects');

			var timeout = $('.nn_tabs').length ? 250 : 0;
			setTimeout((function() {
				self.initActiveClasses();

				if (nn_sliders_use_cookies) {
					self.showByCookies();
				}

				self.showByURL();

				self.showByHash();
			}), timeout);

			setTimeout((function() {
				self.initClickMode();

				if (nn_sliders_mode == 'hover') {
					self.initHoverMode();
				}

				if (nn_sliders_use_cookies || nn_sliders_set_cookies) {
					self.initCookieHandling();
				}

				if (nn_sliders_use_hash) {
					self.initHashHandling();
				}

				self.initHashLinkList();

				if (nn_sliders_reload_iframes) {
					self.initIframeReloading();
				}

				// Add the transition durations
				// But not for Bootstrap 3!
				if (typeof $().emulateTransitionEnd != 'function') {
					$('.nn_sliders').addClass('has_effects');
				}
			}), 1000);

		},

		show: function(id, scroll, openparents) {
			if (openparents) {
				this.openParents(id, scroll);
				return;
			}

			var self = this;
			var $el = this.getElement(id);

			if (!$el.length) {
				return;
			}

			var previous_id = ($el.closest('.nn_sliders').find('.nn_sliders-group.active').find('.nn_sliders-body').attr('id'));

			if (!$el.hasClass('in')) {
				if (scroll) {
					$el.one('shown shown.bs.collapse', function(e) {
						self.scroll(id, previous_id);
					});
				}

				$el.collapse({
					toggle: true,
					parent: $el.parent().parent()
				});
				$el.collapse('show');
			}

			if (scroll) {
				self.scroll(id, previous_id);
			}

			this.updateActiveClassesOnSliderLinks($el);

			$el.focus();
		},

		scroll: function(id, previous_id) {
			var $el = this.getElement(id);

			if (!$el.length) {
				return;
			}

			if ($el.hasClass('nn_sliders-scrolling')) {
				return;
			}

			var offset = 0;

			var scrolling_type = $el.closest('.nn_sliders-group').find('.nn_sliders-toggle').hasClass('nn_sliders-item-scroll')
				? 2
				: nn_sliders_scroll;

			switch (scrolling_type) {
				case 2:
					// Scroll to slider
					var $scrollto = $el.closest('.nn_sliders-group').find('.nn_sliders-scroll').first();

					$el.closest('.nn_sliders-group').prevAll('.nn_sliders-group').each(function($i, el) {
						if ($(el).find('#' + previous_id).length > 0) {
							offset = $(el).find('.nn_sliders-body').outerHeight();
						}
					});

					break;

				case 1:
				default:
					// Scroll to first slider
					var $scrollto = $el.closest('.nn_sliders').find('.nn_sliders-scroll').first();
					break;
			}

			if (!$scrollto.length) {
				return;
			}

			this.scrollElement($el, $scrollto, offset);
		},

		scrollElement: function($el, $scrollto, offset) {
			offset = offset ? offset : 0;
			$el.addClass('nn_sliders-scrolling');

			$('html,body').animate(
				{scrollTop: $scrollto.offset().top - offset},
				{
					complete: function() {
						$el.removeClass('nn_sliders-scrolling');
					}
				}
			);
		},

		getElement: function(id) {
			return this.getSliderElement(id);
		},

		getTabElement: function(id) {
			return $('a.nn_tabs-toggle[data-id="' + id + '"]');
		},

		getSliderElement: function(id) {
			return $('#' + id + '.nn_sliders-body');
		},

		showByCookies: function() {
			var cookies = $.cookie(nn_sliders_cookie_name);
			if (!cookies) {
				return false;
			}

			cookies = cookies.split('___');
			for (var i = 0; i < cookies.length; i++) {
				var keyval = cookies[i].split('=');
				if (keyval.length < 2) {
					continue;
				}

				var key = keyval.shift();
				if (key.substr(0, 14) != 'set-nn_sliders') {
					continue;
				}

				this.openParents(decodeURIComponent(keyval.join('=')), 0);
			}

			return true;
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
			if ($('a#nn_sliders-scrollto_' + id).length == 0) {
				this.showByHashAnchor(id);

				return;
			}

			// hash is a slider
			if (!nn_sliders_use_hash) {
				return;
			}

			if (!nn_sliders_urlscroll) {
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

			// Check if anchor has a parent slider
			if ($anchor.closest('.nn_sliders').length == 0) {
				return;
			}

			var $slider = $anchor.closest('.nn_sliders-body').first();

			// Check if slider has tabs. If so, let Tabs handle it.
			if ($slider.find('.nn_tabs').length > 0) {
				return;
			}

			this.openParents($slider.attr('id'), 0);

			setTimeout(function() {
				$('html,body').animate({scrollTop: $anchor.offset().top});
			}, 250);
		},

		showByID: function(id) {
			var $el = $('a#nn_sliders-scrollto_' + id);

			if ($el.length == 0) {
				return;
			}

			this.openParents(id, nn_sliders_urlscroll);
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
				setTimeout(function() {
					if (scroll) {
						if (typeof(scroll) == 'object') {
							$('html,body').animate({scrollTop: $(scroll).offset().top});
						} else {
							self.scroll(parent.id);
						}
					}
				}, 500);

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

		initActiveClasses: function() {
			$('.nn_sliders-body').on('show show.bs.collapse', function(e) {
				$(this).parent().addClass('active');
				e.stopPropagation();
			});
			$('.nn_sliders-body').on('hidden hidden.bs.collapse', function(e) {
				$(this).parent().removeClass('active');
				e.stopPropagation();
			});
		},

		updateActiveClassesOnSliderLinks: function(active_el) {
			active_el.parent().parent().find('.nn_sliders-toggle').each(function($i, el) {
				$('a.nn_sliders-link[data-id="' + $(el).attr('data-id') + '"]').each(function($i, el) {
					var $link = $(el);

					if ($link.attr('data-toggle') || $link.hasClass('nn_tabs-toggle-sm') || $link.hasClass('nn_sliders-toggle-sm')) {
						return;
					}

					if ($link.attr('data-id') !== active_el.attr('id')) {
						$link.removeClass('active');
						return;
					}

					$link.addClass('active');
				});
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
			if ($link.attr('data-toggle') || $link.hasClass('nn_tabs-link') || $link.hasClass('nn_tabs-toggle-sm') || $link.hasClass('nn_sliders-toggle-sm')) {
				return;
			}

			var id = $link.attr('href').substr($link.attr('href').indexOf('#') + 1);

			// No id found
			if (id == '') {
				return;
			}

			var $anchor = $('a[data-toggle="collapse"][data-id="' + id + '"]');

			// No accompanying link found
			if ($anchor.length == 0) {
				return;
			}

			// Check if anchor has a parent slider
			if ($anchor.closest('.nn_sliders').length == 0) {
				return;
			}

			var $slider = $anchor.closest('.nn_sliders-group').find('.nn_sliders-body').first();
			var slider_id = $slider.attr('id');

			// Check if link is inside the same slider
			if ($link.closest('.nn_sliders').length > 0) {
				if ($link.closest('.nn_sliders-body').first().attr('id') == slider_id) {
					return;
				}
			}

			$link.click(function(e) {
				// Open parent slider and parents
				self.openParents(slider_id, nn_sliders_linkscroll);
				e.stopPropagation();
			});
		},

		initHashHandling: function(el) {
			if (window.history.replaceState) {
				$('.nn_sliders-body').on('shown shown.bs.collapse', function(e) {
					history.replaceState({}, '', '#' + this.id);
					e.stopPropagation();
				});
			}
		},

		initClickMode: function(el) {
			var self = this;
			$('body').on('click.collapse.data-api', 'a.nn_sliders-toggle', function(e) {
				e.preventDefault();

				var id = $(this).attr('data-id');
				var $el = self.getElement(id);

				if (!$el.hasClass('in')) {
					nnSliders.show(id, $(this).hasClass('nn_sliders-item-scroll'));
				} else {
					$el.collapse('hide');
				}

				e.stopPropagation();
			});
		},

		initHoverMode: function(el) {
			$('body').on('hover.collapse.data-api', 'a.nn_sliders-toggle', function(e) {
				e.preventDefault();
				nnSliders.show($(this).attr('data-id'));
			});
		},

		initCookieHandling: function(el) {
			var self = this;

			$('.nn_sliders-body').on('show show.bs.collapse', function(e) {
				var id = $(this).attr('data-id');
				var $el = self.getElement(id);

				var set = 0;
				$el.closest('.nn_sliders').each(function($i, el) {
					set = el.id;
				});

				var obj = {};
				var cookies = $.cookie(nn_sliders_cookie_name);
				if (cookies) {
					cookies = cookies.split('___');
					for (var i = 0; i < cookies.length; i++) {
						var keyval = cookies[i].split('=');
						if (keyval.length > 1 && keyval[0] != set) {
							var key = keyval.shift();
							if (key.substr(0, 14) == 'set-nn_sliders') {
								obj[key] = keyval.join('=');
							}
						}
					}
				}
				obj['set-nn_sliders-' + set] = id;

				var arr = [];
				for (var i in obj) {
					if (i && obj[i]) {
						arr[arr.length] = i + '=' + obj[i];
					}
				}

				$.cookie(nn_sliders_cookie_name, arr.join('___'));
			});
		},

		initIframeReloading: function() {
			$('.nn_sliders-body.in iframe').each(function() {
				$(this).attr('reloaded', true);
			});

			$('.nn_sliders-body').on('show show.bs.collapse', function(e) {
				// Re-inintialize Google Maps on tabs show
				if (typeof initialize == 'function') {
					initialize();
				}

				var $el = $(this);

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

				$('.nn_sliders-body iframe').each(function() {
					$(this).attr('reloaded', false);
				});

				$('.nn_sliders-body.in iframe').each(function() {
					if (this.src) {
						this.src += '';
						$(this).attr('reloaded', true);
					}
				});
			});
		},

		getUrlVar: function() {
			var search = 'slider';
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
})(jQuery);

/* For custom use */
function openAllSliders(id) {
	var parent = findSliderSetBy(id);

	parent.find('.nn_sliders-body:not(.in)').collapse('show');
}

function closeAllSliders(id) {
	var parent = findSliderSetBy(id);

	parent.find('.nn_sliders-body.in').collapse('hide');
}

function findSliderSetBy(id) {
	// Try to find a slider with this id and return the children sliders of its parent
	var el = jQuery('#' + id + '.nn_sliders-body');

	if (el.length) {
		return el.closest('.nn_sliders');
	}

	// Try to find another element with this id and close its children sliders
	el = jQuery('#' + id);

	if (el.length) {
		return el;
	}

	return jQuery('body');
}
