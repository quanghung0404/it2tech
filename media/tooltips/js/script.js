/**
 * @package         Tooltips
 * @version         4.1.5PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var tooltips_timeout = tooltips_timeout || 0;
var tooltips_delay_hide = tooltips_delay_hide || 0;
var delay_hide_touchscreen = delay_hide_touchscreen || 0;
var tooltips_use_auto_positioning = tooltips_use_auto_positioning || 0;
var tooltips_fallback_position = tooltips_fallback_position || 'bottom';

(function($) {
	"use strict";

	$(document).ready(function() {
		var tt_timeout = null;
		var tt_timeoutOff = 0;

		// hover mode
		$('.nn_tooltips-link.hover').popover({
			trigger  : 'hover',
			container: 'body',
			delay    : {show: 5, hide: tooltips_delay_hide}
		});

		// click mode
		$('.nn_tooltips-link.click').popover({trigger: 'manual', container: 'body'})
			.click(function(evt) {
				tooltipsShow($(this), evt, 'click');
			})
			.mouseout(function(evt) {
				tooltipsSetTimer($(this), tooltips_timeout);
			});

		// sticky mode
		$('.nn_tooltips-link.sticky').popover({trigger: 'manual', container: 'body'})
			.mouseover(function(evt) {
				tooltipsShow($(this), evt, 'sticky');
			})
			.mouseout(function(evt) {
				tooltipsSetTimer($(this), tooltips_timeout);
			});

		// close all popovers on click ouside
		$('html').click(function() {
			$('.nn_tooltips-link').popover('hide');
		});

		// do stuff differently for touchscreens
		$('html').one('touchstart', function() {
			// add click mode for hovers
			$('.nn_tooltips-link.hover').popover({
				trigger  : 'manual',
				container: 'body'
			}).click(function(evt) {
				tooltipsShow($(this), evt, 'click');
				tooltipsSetTimer($(this), delay_hide_touchscreen);
			});
		});

		// close all popovers on click outside
		$('html').on('touchstart', function(e) {
			if ($(e.target).closest('.nn_tooltips').length) {
				return;
			}

			$('.nn_tooltips-link').popover('hide');
		});

		$('.nn_tooltips-link').on('touchstart', function(evt) {
			// prevent click close event
			evt.stopPropagation();
		});

		function tooltipsShow(el, evt, cls) {
			// prevent other click events
			evt.stopPropagation();

			clearTimeout(tt_timeout);

			// close all other popovers
			$('.nn_tooltips-link.' + cls).each(function() {
				if ($(this).data('popover') != el.data('popover')) {
					$(this).popover('hide');
				}
			});

			// open current
			if (!el.data('popover').tip().hasClass('in')) {
				el.popover('show');
			}

			$('.nn_tooltips')
				.click(function(evt) {
					// prevent click close event on popover
					evt.stopPropagation();

					// switch timeout off for this tooltip
					tt_timeoutOff = 1;
					clearTimeout(tt_timeout);
				})
				.mouseover(function(evt) {
					clearTimeout(tt_timeout);
				})
				.mouseout(function(evt) {
					tooltipsSetTimer(el, tooltips_timeout);
				})
			;
		}

		function tooltipsSetTimer(el, timeout) {
			// check if imeout should be set
			if (!tt_timeoutOff && timeout) {
				// set the timeout
				tt_timeout = setTimeout(function(el) {
					el.popover('hide');
				}, timeout, el);
			}
		}

		// Adds delay hide functionality to hover popup
		var parentHide = $.fn.popover.Constructor.prototype.hide;
		$.fn.popover.Constructor.prototype.hide = function() {
			if (this.options.trigger === "hover" && this.tip().hasClass('hover')) {
				var that = this;
				// try again after what would have been the delay
				setTimeout(function() {
					return that.hide.call(that, arguments);
				}, that.options.delay.hide);
				return;
			}
			parentHide.call(this, arguments);
		};

		// Improved placement of tooltip if there is no space for it in area
		if (tooltips_use_auto_positioning) {
			$.fn.popover.Constructor.prototype.show = function() {
				var e = $.Event('show.bs.' + this.type);
				if (this.hasContent() && this.enabled) {

					if (e.isDefaultPrevented()) {
						return;
					}

					var $tip = this.tip();

					$tip
						.mouseover(function(evt) {
							$tip.addClass('hover');
						})
						.mouseout(function(evt) {
							$tip.removeClass('hover');
						});

					this.setContent();

					if (this.options.animation) {
						$tip.addClass('fade');
					}

					var placement = (typeof this.options.placement == 'function') ?
						this.options.placement.call(this, $tip[0], this.$element[0]) :
						this.options.placement;

					$tip.detach().css({top: 0, left: 0, display: 'block'});

					this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element);

					var tp;
					var pos = this.getPosition();
					var actualWidth = $tip[0].offsetWidth;
					var actualHeight = $tip[0].offsetHeight;

					// Get positions
					var tpt = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2};
					var tpb = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2};
					var tpl = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth};
					var tpr = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width};

					// Get position room
					var hast = (tpt.top > $(window).scrollTop());
					var hasb = ((tpb.top + actualHeight) < ($(window).scrollTop() + $(window).height()));
					var hasl = (tpl.left > $(window).scrollLeft());
					var hasr = ((tpr.left + actualWidth) < ($(window).scrollLeft() + $(window).width()));

					switch (placement) {
						case 'top':
							if (!hast) {
								placement = hasb ? 'bottom' : (hasr ? 'right' : (hasl ? 'left' : tooltips_fallback_position));
							}
							break;
						case 'bottom':
							if (!hasb) {
								placement = hast ? 'top' : (hasr ? 'right' : (hasl ? 'left' : tooltips_fallback_position));
							}
							break;
						case 'left':
							if (!hasl) {
								placement = hasr ? 'right' : (hast ? 'top' : (hasb ? 'bottom' : tooltips_fallback_position));
							}
							break;
						case 'right':
							if (!hasr) {
								placement = hasl ? 'left' : (hast ? 'top' : (hasb ? 'bottom' : tooltips_fallback_position));
							}
							break;
					}

					switch (placement) {
						case 'top':
							tp = tpt;
							break;
						case 'bottom':
							tp = tpb;
							break;
						case 'left':
							tp = tpl;
							break;
						case 'right':
							tp = tpr;
							break;
					}

					this.applyPlacement(tp, placement);
					this.$element.trigger('shown.bs.' + this.type);
				}
			}
		}
	});
})(jQuery);
