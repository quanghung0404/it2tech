/**
 * =============================================================
 * RAXO All-mode K2 J3.x - Interface JS
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2014 RAXO Group
 * @license		RAXO Commercial License
 * 				This file is forbidden for redistribution
 * @link		http://www.raxo.org
 * =============================================================
 */


(function($){
	$(document).ready(function(){ 

		// Source Selection
		var	source_selection	= $('#jform_params_source_selection'),
			source_cat			= $('#jform_params_source_cat').closest('.control-group'),
			source_itm			= $('#jform_params_source_itm').closest('.control-group');
		var date_filtering		= $('#jform_params_date_filtering'),
			date_range_start	= $('#jform_params_date_range_start').closest('.control-group'),
			date_range_end		= $('#jform_params_date_range_end').closest('.control-group'),
			date_range_from		= $('#jform_params_date_range_from').closest('.control-group'),
			date_range_to		= $('#jform_params_date_range_to').closest('.control-group');

		// Default Settings
		if (source_selection.find('input:checked').val() == 'cat') {
			source_itm.toggleClass('hide-field');
		} else {
			source_cat.toggleClass('hide-field');
		}
		if (date_filtering.val() !== 'range') {
			date_range_start.css('display', 'none');
			date_range_end.css('display', 'none');
		}
		if (date_filtering.val() !== 'relative') {
			date_range_from.css('display', 'none');
			date_range_to.css('display', 'none');
		}

		// Changed Settings
		source_selection.find('label:not(.active)').click(function(){
			source_cat.toggleClass('hide-field');
			source_itm.toggleClass('hide-field');
		});
		date_filtering.change(function(){
			var sel = $(this).val();
			if (sel == "range") {
				date_range_start.css('display','block');
				date_range_end.css('display','block');
			} else {
				date_range_start.css('display','none');
				date_range_end.css('display','none');
			}
			if (sel == "relative") {
				date_range_from.css('display','block');
				date_range_to.css('display','block');
			} else {
				date_range_from.css('display','none');
				date_range_to.css('display','none');
			}
		});

	})
})(jQuery);