/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	"use strict";

	$(document).ready(function() {
		// remove all empty control groups
		$('div.nn_simplecategory').each(function(i, el) {
			var $el = $(el);

			var func = function() {
				var new_value = $(this).val();

				if (new_value == '-1') {
					$el.find('.nn_simplecategory_value').val($el.find('.nn_simplecategory_new input').val());
					return;
				}

				$el.find('.nn_simplecategory_value').val(new_value);
			};

			$el.find('.nn_simplecategory_select select').bind('change', func).bind('keyup', func);
			$el.find('.nn_simplecategory_new input').bind('change', func).bind('keyup', func);
		});
	});
})(jQuery);
