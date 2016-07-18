/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function ( $ ) {
	$.JSNESSliders = function ( options ) {
		this.options = $.extend({}, options);
		this.initialize = function () {
			var self = this;
			$('#slider_id').change(
				function ( event ) {
					event.preventDefault();
					var sliderID = parseInt($('option:selected', $('#slider_id')).val());
					if ( sliderID ) {
						$('#jsn-link-edit-slider').removeClass("disabled").attr({
							href: 'index.php?option=com_easyslider&view=slider&layout=design&slider_id=' + sliderID,
							target: '_blank'
						});
						$('#btn_insert_button').prop('disabled', false);
					}
					else {
						$('#jsn-link-edit-slider').addClass("disabled").attr({ href: 'javascript:void(0)' }).removeAttr('target');
						$('#btn_insert_button').prop('disabled', true);
					}
				}
			);
		};
	};

	$(window).load(function () {
		$(".jsn-modal-overlay,.jsn-modal-indicator").remove();

		$("body").append($("<div/>", {
			"class": "jsn-modal-overlay",
			"style": "z-index: 1000; display: inline;"
		})).append($("<div/>", {
			"class": "jsn-modal-indicator",
			"style": "display:block"
		})).addClass("jsn-loading-page");

		if ( typeof JSNES_SlidersData !== 'undefined')
			window.JSNES_SlidersData = JSNES_SlidersData;
		var convertedSliders = {};
		if ( window.JSNES_SlidersData ) {
			_.each(window.JSNES_SlidersData, function ( olddata, key ) {
				var data;
				try {
					data = JSON.parse(olddata);
				}
				catch ( e ) {
					data = { version: 2 };
				}
				console.log('convert data')
				var converted = function validate( data ) {
					switch ( parseInt(data.version) ) {
						case 1:
							return ES_DataMigration({}, data, data_migration_map_1_2, { version: 2 });
						case undefined:
							return ES_DataMigration({}, data, data_migration_map_0_1, { version: 1 });
						default:
							return data;
					}
				}(data);

				if ( JSON.stringify(converted) !== olddata ) {
					convertedSliders[ key ] = converted;
					//console.log(converted);
				}
			});

			if ( _.keys(convertedSliders).length ) {

				console.log("Upgrading data...");

				var item = {};
				var dataForm = [];
				item.name = 'converted_data';
				item.value = JSON.stringify(convertedSliders);
				dataForm.push(item);
				var token = $('#jsn-page-list').attr('token');
				$.ajax({
					url: 'index.php?option=com_easyslider&task=sliders.convertSliderData&' + token + '=1',
					type: 'POST',
					dataType: 'json',
					data: dataForm,
					error: function () {
						//console.log('error');
					},
					complete: slidersReady
				});
			}
			else slidersReady();
		}
		else slidersReady();


		//slidersReady();

		function slidersReady() {
			$(".jsn-modal-overlay,.jsn-modal-indicator").delay(1200).queue(function () {
				$(this).remove();
				$("#jsn-page-list").removeClass("jsn-easyslider-hide");
			});
		}

	});

})(jQuery);
