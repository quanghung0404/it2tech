/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function($) {
	"use strict";

	$.JSNMMHandleSetting 	= $.JSNMMHandleSetting || {};
	$.JSNTplModal			= $.JSNTplModal || {};

    // Gradient picker for row
//	$.JSNMMHandleSetting.gradientPicker = function() {
//		var gradientPicker = function() {
//			var val = $('#param-background').val();
//
//			if (val == 'gradient') {
//				$("input.jsn-grad-ex").each(function(i, e) {
//					$(e).next('.classy-gradient-box').first().ClassyGradient({
//						gradient: $(e).val(),
//						width : 218,
//						orientation : $('#param-gradient_direction').val(),
//						onChange: function(stringGradient, cssGradient, arrayGradient) {
//							$(e).val() == stringGradient || $(e).val(stringGradient);
//
//							$('#param-gradient_color_css').val(cssGradient);
//
//							$.JSNMMHandleSetting.shortcodePreview();
//						}
//					});
//				});
//			}
//		};
//
//		$(document).ready(function() {
//			setTimeout(function() {
//				gradientPicker();
//			}, 500);
//		});
//
//		$('#param-background').change(function() {
//			gradientPicker();
//		});
//
//		// Control orientation
//		$('#param-gradient_direction').on('change', function() {
//			var orientation = $(this).val();
//
//			$('.classy-gradient-box').data('ClassyGradient').setOrientation(orientation);
//
//			// Update gradient background
//			if (orientation == 'horizontal') {
//				$('#param-gradient_color_css').val($('#param-gradient_color_css').val().replace('left top, left bottom', 'left top, right top').replace(/\(top/g,'(left'));
//			} else {
//				$('#param-gradient_color_css').val($('#param-gradient_color_css').val().replace('left top, right top', 'left top, left bottom').replace(/\(left/g,'(top'));
//			}
//		});
//
//	};

	// Check radio button when click button in btn-group
	$.JSNMMHandleSetting.buttonGroup = function() {
		var data_value;

		$('.jsn-mm-btn-group .btn').click(function(i) {
			data_value = $(this).attr('data-value');

			$(this).parent().next('.jsn-mm-btn-radio').find('input:radio[value="'+data_value+'"]').prop('checked', true);

			$.JSNMMHandleSetting.shortcodePreview();
		});
	};

	// Validate input of text field with regular expression
	$.JSNMMHandleSetting.regexTestInput = function(event, regex_str) {
		var regex = new RegExp(regex_str), key = String.fromCharCode(!event.charCode ? event.which : event.charCode);

		if (!regex.test(key) && key != ' ') {
			event.preventDefault();

			return false;
		}

		return true;
	};

	// Validator input field
	$.JSNMMHandleSetting.inputValidator = function() {
		var input_action = 'change paste';

		// Disable special characters, limit length of title
		$("#param-el_title, input:text[name$='[title]'], [data-role='title'], .jsn-mm-limit-length", '#modalOptions').each(function() {
			$(this).prop('maxlength', 50);
		});

		// Number field: allow typing number only
		$("#modalOptions input[type='number']").bind(input_action, function(event) {
			$.JSNMMHandleSetting.regexTestInput(event, "^[0-9\b\-]+$");
		});

		// Doesn't allow 0 in items_per_ input field
		$("#modalOptions input[id*='items_per']").bind(input_action, function(event) {
			var regex = /^[1-9\b]+$/g, val = regex.test($(this).val());

			if (!val) {
				$(this).val('1');
			}
		});

		// Positive value
		$('.positive-val').bind(input_action, function(event) {
			var this_val = $(this).val();

			if (parseInt(this_val) <= 0) {
				$(this).val(1);
			}
		});
	};

	/*$.JSNMMHandleSetting.selectImage = function() {
		var _custom_media = true;

		$('#modalOptions .select-media-remove').on('click', function() {
			var _input	= $(this).closest('div').find('input[type="text"]');

			_input.attr('value', '');
			_input.trigger('change');
		});

        $('body').on( 'click', '#modalOptions .select-media', function(e) {
			var	button = $(this),
				id = button.attr('id').replace('_button', ''),
				jqueryParent = window.parent.jQuery.noConflict(),
				filter_type = $(this).attr('filter_type'),
				object = {};

			if (typeof(filter_type) != undefined) {
				object.type = filter_type;
			} else {
				object.type = '';
			}

			jqueryParent('#wr-select-media').val(JSON.stringify(object));
			jqueryParent('#wr-select-media').trigger('change');

			var timer = setInterval(function() {
				var currentValue = jqueryParent('#wr-select-media').val();

				if (currentValue) {
					var jsonObject = JSON.parse( currentValue );

					switch ( jsonObject.type ) {
						case 'media_selected':
							if (typeof($.WR_ImageElement.setImageSize) == 'function') {
								$.WR_ImageElement.setImageSize(jsonObject.select_prop);
							}

							$("#"+id).val(jsonObject.select_url);
							$("#"+id).trigger('change');

							clearInterval(timer);
						break;
					}
				}
			}, 500);
		});

		$('#modalOptions .add_media').on('click', function() {
			_custom_media = false;
		});
	};*/

	$.JSNMMHandleSetting.updateState = function(state) {
		if (state != null) {
			$.JSNMMHandleSetting.doing = state;
		} else {
			if ($.JSNMMHandleSetting.doing == null || $.JSNMMHandleSetting.doing) {
				$.JSNMMHandleSetting.doing = 0;
			} else {
				$.JSNMMHandleSetting.doing = 1;
			}
		}
	};

	$.JSNMMHandleSetting.renderModal = function() {
		if ($( "#modalOptions" ).length == 0) {
			return false;
		}

		$("#modalOptions").modal('toggle');

		// Sortable sub-elements
		var group_elements = $("#group_elements");

		if (group_elements.length) {
			group_elements.sortable({
				stop: function( event, ui ) {
					$.JSNMMHandleSetting.shortcodePreview();

					$('body').trigger('on_after_reorder_element');
				}
			});

			group_elements.disableSelection();

			$('body').trigger('on_remove_handle_reorder');
		}

		$.JSNMMHandleSetting.setColorPicker('#modalOptions .color-selector');
		$.JSNMMHandleElement.initTooltip('#modalOptions [data-toggle="tooltip"]');

		// Toggle dependency params
//		var jsn_mm_HasDepend = $( '#modalOptions .jsn_mm_has_depend' );
//
//		jsn_mm_HasDepend.each(function() {
//			if (($(this).is(":radio") || $(this).is(":checkbox")) && !$(this).is(":checked")) {
//				return;
//			}
//
//			var this_id  = $(this).attr('id'), this_val  = $(this).val();
//
//			$.JSNMMHandleSetting.toggleDependency(this_id, this_val);
//		});
	};

	$.JSNMMHandleSetting.setColorPicker = function(selector) {
		if (!selector) {
			return false;
		}

		$(selector).each(function() {
			var	self = $(this),
				colorInput = self.siblings('input').last(),
				inputId = colorInput.attr('id'),
				inputValue = inputId.replace(/_color/i, '') + '_value';

			if ($('#' + inputValue).length) {
				$('#' + inputValue).val($(colorInput).val());
			}

			self.ColorPicker({
				color: $(colorInput).val(),
				onShow: function(colpkr) {
					$(colpkr).fadeIn(500);

					return false;
				},
				onHide: function(colpkr) {
					$(colpkr).fadeOut(500);

					$.JSNMMHandleSetting.shortcodePreview();

					return false;
				},
				onChange: function(hsb, hex, rgb) {
					$(colorInput).val('#' + hex);

					if ($('#' + inputValue).length) {
						$('#' + inputValue).val('#' + hex);
					}

					self.children().css('background-color', '#' + hex);
				}
			});
		});
	};

	$.JSNMMHandleSetting.selector = function(curr_iframe, element) {
		var $selector = (curr_iframe != null &&  curr_iframe.contents() != null) ? curr_iframe.contents().find(element) : $(element);

		return $selector;
	};

	$.JSNMMHandleSetting.shortcodePreview = function(params, shortcode, curr_iframe, callback, stop_reload_iframe) {
		if (($.JSNMMHandleSetting.selector(curr_iframe,"#modalOptions").length == 0 || $.JSNMMHandleSetting.selector(curr_iframe,"#modalOptions").hasClass('submodal_frame')) && curr_iframe == null) {
			return true;
		}

		// Change state to ACTIVE
		$.JSNMMHandleSetting.updateState(1);

		var tmp_content = '', shortcode_name, shortcode_type;

		if (params == null) {
			shortcode_name = $.JSNMMHandleSetting.selector(curr_iframe, '#modalOptions #shortcode_name' ).val();
			shortcode_type = $.JSNMMHandleSetting.selector(curr_iframe, '.jsn-mm-form-container #shortcode_type' ).val();

			/*if (shortcode_type == 'widget') {
				// Widget
				var form_serialize = $.JSNMMHandleSetting.selector(curr_iframe, '#modalOptions #wr-widget-form' ).serialize();

				$('input[type=checkbox]').each(function() {
					if (!this.checked) {
						form_serialize += '&'+this.name+'=0';
					}
				});

				tmp_content = '[wr_megamenu_widget widget_id="'+shortcode_name+'"]' + form_serialize + '[/wr_megamenu_widget]';
			*/	
			//} else {
				var data = $.JSNMMHandleSetting.traverseParam( $.JSNMMHandleSetting.selector(curr_iframe, '#modalOptions .control-group' ) );
                var sc_content = data.sc_content;
                var params_arr = data.params_arr;

                // Get tinymce content
//                tinyMCE.triggerSave();
               /* $('.wr_mm_editor').each(function(){
                    if( ! $(this).closest('.form-group').parent().hasClass('sub-element-settings') ) {
                        var tinymce_content = $(this).val();
                        sc_content += ( tinymce_content != null ) ? tinymce_content : '';

                        return false;
                    }
                });*/


                // Update TinyMCE content to #wr_share_data
				window.parent.jQuery.noConflict()('#jsn_view_modal').contents().find('#jsn_mm_share_data').text(sc_content);

				// For shortcode which has sub-shortcode
				if ($.JSNMMHandleSetting.selector(curr_iframe,"#modalOptions").find('.has_submodal').length > 0) {
					var sub_items_content = [];

					$.JSNMMHandleSetting.selector(curr_iframe, "#modalOptions [name^='shortcode_content']").each(function() {
						sub_items_content.push($(this).text());
					});
					sc_content += sub_items_content.join('');
				}

				tmp_content = $.JSNMMHandleSetting.generateShortcodeContent(shortcode_name, params_arr, sc_content);
			//}
		} else {
			shortcode_name = shortcode;
			tmp_content = params;
		}

		// Update shortcode content
		$.JSNMMHandleSetting.selector(curr_iframe, '#shortcode_content').text(tmp_content);

		if (callback) {
			callback();
		}

		// Change state to inactive
		$.JSNMMHandleSetting.updateState(0);

		// Dont reload preview iframe
		if (stop_reload_iframe) {
			return false;
		}

		/*var url = Wr_Megamenu_Ajax.wr_modal_url + '&wr_shortcode_preview=1' + '&wr_shortcode_name=' + shortcode_name + '&wr_nonce_check=' + Wr_Megamenu_Ajax._nonce;

		if ($('#shortcode_preview_iframe').length > 0) {
			// Asign value to a variable (for show/hide preview)
			$.JSNMMHandleSetting.previewData = {
				curr_iframe: curr_iframe,
				url : url,
				tmp_content: tmp_content
			};

			// Load preview iframe
			$.JSNMMHandleSetting.loadIframe(curr_iframe, url, tmp_content);
		}*/

		return false;
	};

    /**
     * Traverse parameters, get theirs values
     */
    $.JSNMMHandleSetting.traverseParam = function( $selector, get_data_tag ){
        var sc_content = '';
        var params_arr = {};

        $selector.each( function ()
        {
            if ( ! $(this).hasClass( 'wr_hidden_depend' ) )
            {
                $(this).find( "[id^='param-']" ).each(function()
                {
                    // Bypass the Copy style group
                    if ( $(this).attr('id') == 'param-copy_style_from' ) {
                        return;
                    }

                    if(
                            $(this).parents(".tmce-active").length == 0 && !$(this).hasClass('tmce-active')
                            && $(this).parents(".html-active").length == 0 && !$(this).hasClass('html-active')
                            && !$(this).parents("[id^='parent-param']").hasClass( 'wr_hidden_depend' )
                            && $(this).attr('id').indexOf('parent-') == -1
                    )
                    {
                        var id = $(this).attr('id');
                        if($(this).attr('data-role') == 'content'){
                            sc_content =  $(this).val().replace(/\[/g,"&#91;").replace(/\]/g,"&#93;");
                        }else{
                            if(($(this).is(":radio") || $(this).is(":checkbox")) && !$(this).is(":checked"));
                            else{
                                if(!params_arr[id.replace('param-','')] || id.replace('param-', '') == 'title_font_face_type' || id.replace('param-', '') == 'title_font_face_value' || id.replace('param-','') == 'font_face_type' || id.replace('param-','') == 'font_face_value' || id.replace('param-', '') == 'image_type_post' || id.replace('param-', '') == 'image_type_page' || id.replace('param-', '') == 'image_type_category' ) {
                                    params_arr[id.replace('param-','')] = $(this).val();
                                } else {
                                    params_arr[id.replace('param-','')] += '__#__' + $(this).val();
                                }

                            }
                        }

                        if( get_data_tag == null || get_data_tag ) {
                            // data-share
                            if($(this).attr('data-share')){
                                var share_element = $('#' + $(this).attr('data-share'));
                                var share_data = share_element.text();
                                if(share_data == "" || share_data == null)
                                    share_element.text($(this).val());
                                else{
                                    share_element.text(share_data + ',' + $(this).val());
                                    var arr = share_element.text().split(',');
                                    $.unique( arr );
                                    share_element.text(arr.join(','));
                                }

                            }

//                            // data-merge
//                            if($(this).parent().hasClass('merge-data')){
//                                var wr_merge_data = window.parent.jQuery.noConflict()( '#jsn_view_modal').contents().find('#wr_merge_data');
//                                wr_merge_data.text(wr_merge_data.text() + $(this).val());
//                            }
//
//                            // table
//                            if($(this).attr("data-role") == "extract"){
//                                var extract_holder = window.parent.jQuery.noConflict()( '#jsn_view_modal').contents().find('#wr_extract_data');
//                                extract_holder.text(extract_holder.text()+$(this).attr("id")+':'+$(this).val()+'#');
//                            }
                        }
                    }

                });
            }
        });

        return { sc_content : sc_content, params_arr : params_arr };
    }

    /**
     * Generate shortcode content
     */
    $.JSNMMHandleSetting.generateShortcodeContent = function(shortcode_name, params_arr, sc_content){
        var tmp_content = [];

        tmp_content.push('['+ shortcode_name);
        // wrap key, value of params to this format: key = "value"
        $.each(params_arr, function(key, value){
            if ( value ) {
                if ( value instanceof Array ) {
                    value = value.toString();
                }
                tmp_content.push(key + '="' + value.replace(/\"/g,"&quot;").replace(/\[/g,"").replace(/\]/g,"") + '"');

            }
        });

        tmp_content.push(']' + sc_content + '[/' + shortcode_name + ']');
        tmp_content	= tmp_content.join( ' ' );

        return tmp_content;
    }

	// Load preview iframe
//	$.JSNMMHandleSetting.loadIframe = function(curr_iframe, url, tmp_content) {
//		$('#wr_preview_data').remove();
//
//		var tmp_form = $(
//			'<form action="' + url + '" id="wr_preview_data" name="wr_preview_data" method="post" target="shortcode_preview_iframe">' +
//				'<input type="hidden" id="wr_preview_params" name="params" value="' + encodeURIComponent(tmp_content) + '">' +
//			'</form>'
//		).appendTo($('body'));
//
//		$.JSNMMHandleSetting.selector(curr_iframe, '#modalOptions #wr_overlay_loading').fadeIn('fast');
//
//		$('#wr_preview_data').submit();
//
//		$('#modalOptions #shortcode_preview_iframe').bind('load', function() {
//			$('#modalOptions #shortcode_preview_iframe').fadeIn('fast');
//
//			$.JSNMMHandleSetting.selector(curr_iframe, '#modalOptions #wr_overlay_loading').fadeOut('fast');
//
//			$('#wr_previewing').val('0');
//		});
//
//		tmp_form.remove();
//	};

	// Hide/show preview
//	$.JSNMMHandleSetting.togglePreview = function() {
//		$('#previewToggle *').click(function() {
//			if ($(this).attr('id') == 'hide_preview') {
//				$(this).addClass('hidden');
//				$('#show_preview').removeClass('hidden');
//
//				// Remove iframe
//				$('#preview_container iframe').remove();
//			} else {
//				$(this).addClass('hidden');
//				$('#hide_preview').removeClass('hidden');
//
//				$('#preview_container').append(
//					"<iframe scrolling='no' id='shortcode_preview_iframe' name='shortcode_preview_iframe' class='shortcode_preview_iframe'></iframe>"
//				);
//
//				if ($.JSNMMHandleSetting.previewData != null) {
//					var data = $.JSNMMHandleSetting.previewData;
//
//					$.JSNMMHandleSetting.loadIframe(data.curr_iframe, data.url, data.tmp_content);
//				}
//			}
//		});
//	};

	// Show/hide dependency params
//	$.JSNMMHandleSetting.toggleDependency = function(this_id, this_val) {
//		if (!this_id || !this_val) {
//			return;
//		}
//
//		$('.wr_depend_other[data-depend-element="' + this_id + '"]').each(function() {
//			var operator = $(this).attr('data-depend-operator'), compare_value = $(this).attr('data-depend-value');
//
//			switch(operator) {
//				case "=":
//					var check_ = 0;
//
//					if (compare_value.indexOf('__#__') > 0) {
//						var values_ = compare_value.split('__#__');
//
//						check_ = ($.inArray(this_val, values_) >= 0);
//					} else {
//						check_  = (this_val == compare_value);
//					}
//
//					if (check_) {
//						$(this).removeClass('wr_hidden_depend');
//					} else {
//						$(this).addClass( 'wr_hidden_depend' );
//					}
//				break;
//
//				case ">":
//					if (this_val > compare_value) {
//						$(this).removeClass('wr_hidden_depend');
//					} else {
//						$(this).addClass('wr_hidden_depend');
//					}
//				break;
//
//				case "<":
//					if (this_val < compare_value) {
//						$(this).removeClass('wr_hidden_depend');
//					} else {
//						$(this).addClass('wr_hidden_depend');
//					}
//				break;
//
//				case "!=":
//					if (this_val != compare_value) {
//						$(this).removeClass('wr_hidden_depend');
//					} else {
//						$(this).addClass('wr_hidden_depend');
//					}
//				break;
//			}
//
//			$.JSNMMHandleSetting.secondDependency($(this).attr('id'), $(this).hasClass('wr_hidden_depend'), $(this).find('select').hasClass('no_plus_depend'));
//		});
//	};

	// Show/hide 2rd level dependency elements
//	$.JSNMMHandleSetting.secondDependency = function(this_id, hidden, allow) {
//		if (!this_id) {
//			return;
//		}
//
//		this_id = this_id.replace('parent-','');
//
//		$('.wr_depend_other[data-depend-element="'+this_id+'"]').each(function() {
//			if (hidden) {
//				$(this).addClass('wr_hidden_depend2');
//			} else {
//				$(this).removeClass('wr_hidden_depend2');
//			}
//		});
//
//		if (!allow) {
//			$('.wr_depend_other[data-depend-element="'+this_id+'"]').each(function() {
//				$(this).removeClass('wr_hidden_depend2');
//			});
//		}
//
//		// Hide label if all options in .controls div have 'wr_hidden_depend' class
//		$('#modalOptions .controls').each(function() {
//			var hidden_div = 0;
//
//			$(this).children().each(function() {
//				if ($(this).hasClass('wr_hidden_depend')) {
//					hidden_div++;
//				}
//			});
//
//			if (hidden_div > 0 && hidden_div == $(this).children().length) {
//				$(this).parent('.control-group').addClass('margin0');
//				$(this).prev('.control-label').hide();
//			} else {
//				$(this).parent('.control-group').removeClass('margin0');
//				$(this).prev('.control-label').show();
//			}
//		});
//	};

	// Set change event of dependency elements
//	$.JSNMMHandleSetting.changeDependency = function(dp_selector) {
//		if (!dp_selector) {
//			return false;
//		}
//
//		$('#modalOptions').delegate(dp_selector, 'change', function() {
//			var this_id = $(this).attr('id'), this_val	= $(this).val();
//
//			$.JSNMMHandleSetting.toggleDependency(this_id, this_val);
//		});
//	};

	// Show tab in Modal Options
	$.JSNMMHandleSetting.tab = function() {
//		$('#wr_option_tab a[href="#appearance"]').on('click', function() {
//			if ($('#wr_previewing').val() == '1') {
//				return;
//			}
//
//			$('#wr_previewing').val('1');
//
//			$.JSNMMHandleSetting.shortcodePreview();
//		});

		if ($('.jsn-tabs').length && !$('.jsn-tabs').find("#Notab").length) {
			$('.jsn-tabs').tabs();
		}

		return true;
	};

//	$.JSNMMHandleSetting.select2 = function() {
//		$(".select2").each(function() {
//			var share_element = window.parent.jQuery.noConflict()( '#jsn_view_modal').contents().find('#' + $(this).attr('data-share')), share_data = [];
//
//			if (share_element && share_element.text() != "") {
//				share_data = share_element.text().split(',');
//				share_data = $.unique(share_data);
//			}
//
//			$(this).css('width','300px');
//
//			$(this).select2({
//				tags: share_data,
//				maximumInputLength: 10
//			});
//		});
//
//		$('.select2-select').each(function() {
//			if ( $(this).is( 'select' ) ) {
//				var id = $(this).attr('id');
//
//				if ($('#' + id + '_select_multi').val()) {
//					var arr_select_multi = $('#' + id + '_select_multi').val().split('__#__');
//
//					$(this).val(arr_select_multi).select2({
//						minimumResultsForSearch: -1
//					});
//				} else {
//					var parent_selector = '';
//					parent_selector = $(this).parent('div[id^="parent-param-"]');
//					// Only show select2 search textbox for post and page listing
//					if ( $(parent_selector).attr('data-depend-value') == 'post' || $(parent_selector).attr('data-depend-value') == 'page' ) {
//						$(this).select2();
//					} else {
//						$(this).select2({
//							minimumResultsForSearch: -1
//						});
//					}
//				}
//			}
//		});
//
//		$.JSNMMHandleSetting.select2_color();
//	};

//	$.JSNMMHandleSetting.select2_color = function() {
//		function format(state) {
//			if (!state.id) {
//				// optgroup
//				return state.text;
//			}
//
//			var type = state.id.toLowerCase().split('-');
//
//			type = type[type.length - 1];
//
//			return "<img class='color_select2_item' src='" + Wr_Megamenu_Translate.asset_url + "images/icons-16/btn-color/" + type + ".png'/>" + state.text;
//		}
//
//		$('.color_select2').not('.hidden').each(function() {
//			$(this).find('select').each(function() {
//				$(this).select2({
//					minimumResultsForSearch: -1,
//					formatResult: format,
//					formatSelection: format,
//					escapeMarkup: function(m) {
//						return m;
//					}
//				});
//			});
//		});
//	};

	// Handle icon change action in Modal box
//	$.JSNMMHandleSetting.icons = function() {
//		// Icon type: handle icon click
//		$(".jsn-mm-form-container").delegate("[data-type='jsn-mm-icon-item']", "click", function() {
//			$(".controls .icon-selected").each(function() {
//				$(this).removeClass('icon-selected');
//			});
//
//			// Update selected icon
//			var	$selected_icon = $(this).attr('class').replace('icon-selected', '').replace(' ',''),
//				icon = $(this).parent('li').parent('ul').next("input[id^='param']");
//
//			icon.val($selected_icon);
//			icon.trigger('change');
//
//			$(this).addClass('icon-selected');
//		});
//	};

	// Handle click action on Button in Modal: Convert action/ Add Row, Column / ...
	$.JSNMMHandleSetting.actionHandle = function() {
		// Handle Convert To ... button
		$(".wr_action_btn").delegate("a", "click", function(e) {
			e.preventDefault();

			var action_type = $(this).attr('data-action-type'), action = $(this).attr('data-action');

			if (action_type && action) {
				var action_data = {};

				action_data[action_type] = action;

				$.HandleElement.updateBeforeClose(action_data);
			}
		});
	};

	$.JSNMMHandleSetting.closeAllSelect2WhenClickModal = function () {
		$('.jsn-dialog').on("click", function(e) {
    		var el = $(e.target);
    		//if (el.parents(".select2-container").length == 0) {
    			//$(".select2-dropdown-open").select2("close");
    		//}
		});
	};

	// Handle Copy to Clipboard action
//	$.JSNMMHandleSetting.copyToClipboard = function() {
//		var textarea = $("#shortcode_content"), button = $("#copy_to_clipboard"), text_change = button.data('textchange');
//
//		if (textarea.length && button.length) {
//			ZeroClipboard.config({ moviePath: Wr_Megamenu_Ajax.assets_url + '/assets/3rd-party/zeroclipboard/ZeroClipboard.swf' });
//
//			var client = new ZeroClipboard(button);
//
//			client.on("datarequested", function() {
//				// Copy shortcode content to clipboard
//				client.setText(textarea.val());
//
//				// Show message
//				button.addClass("disabled").attr("disabled", "disabled");
//
//				// get current text
//				var cache_text = button.text();
//
//				// Change text button
//				button.text(text_change);
//
//				// Schedule hiding the message
//				setTimeout(function() {
//					button.removeClass("disabled").removeAttr("disabled");
//					$("i", button).animate({"opacity": "0"}, 500, function () { $(this).hide(); });
//					button.text(cache_text);
//				}, 1000);
//			});
//		}
//	};


    // Function to handling resize modal setting
//    $.JSNMMHandleSetting.handleResizeModal = function () {
//
//        if ( typeof ( $('.jsn-tpl-mm-setting-resize').resizable) == 'function' ) {
//            $('.jsn-tpl-mm-setting-resize').resizable({
//                handles: 'e',
//                minWidth: 400,
//
//                start: $.proxy(function (event, ui) {
//                	//$('.wr-preview-resize #wr_overlay_loading').show();
//                }, this),
//                resize: $.proxy(function (event, ui) {
//
////                    var resize_handle_width =  ui.element.find('.ui-resizable-e').first().width();
////                    var thisWidth           = ui.element.width() + resize_handle_width;
////                    var remain_width        = ui.element.parent().width() - thisWidth - 15 ;
//                   // ui.element.next('.wr-preview-resize').css('width', remain_width + 'px');
//
//                }, this),
//                stop: $.proxy(function (event, ui) {
//                	//$('.wr-preview-resize #wr_overlay_loading').fadeOut();
//                })
//            });
//
//        }
//
//    }

	$.JSNMMHandleSetting.init = function() {
		//$.JSNMMHandleSetting.togglePreview();

		$.JSNMMHandleSetting.updateState();

		$.JSNMMHandleSetting.tab();

		// Trigger action of element which has dependency elements
	//	$.JSNMMHandleSetting.changeDependency('.wr_has_depend');

		// Update preview when change param in Modal Box/Styling
		$('#modalOptions').delegate('[id^="param"]', 'change', function() {
			if ($(this).attr('data-role') == 'no_preview') {
				return false;
			}
			if ($(this).attr('id') == 'param-copy_style_from' ) {
				return false;
			}

            var stop_reload_iframe = 0;

			$.JSNMMHandleSetting.shortcodePreview(null, null, null, null, stop_reload_iframe);
		});

        // Trigger preview for parameters of Widget
        /*$('#modalOptions #wr-widget-form').delegate('input, select, textarea', 'change', function() {
            $.JSNMMHandleSetting.shortcodePreview();
        });*/

       /* if ($('#shortcode_name').val() != 'wr_megamenu_image') {
            $('select option[value="large_image"]').hide();
        }*/

        // Send Ajax request for loading shortcode html at first time
		$.JSNMMHandleSetting.renderModal();

		//$.JSNMMHandleSetting.select2();

		//$.JSNMMHandleSetting.icons();

		$.JSNMMHandleSetting.actionHandle();

		//$.JSNMMHandleSetting.selectImage();

		//$.JSNMMHandleSetting.gradientPicker();

		$.JSNMMHandleSetting.buttonGroup();

		$.JSNMMHandleSetting.inputValidator();


		// Init Copy to Clipboard action
		//$.JSNMMHandleSetting.copyToClipboard();

		$.JSNMMHandleSetting.closeAllSelect2WhenClickModal();
       // $.JSNMMHandleSetting.handleResizeModal();
	};
})(jQuery);
