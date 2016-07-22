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
	
    $.fn.JSNTplMMShortcodeModuleSettingsDelayKeyup = function (callback, ms) 
    {
        var timer = 0;
        var md = $(this);
        $(this).keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback(md);
            }, ms);
        });
        return $(this);
    };
    
	$.JSNTplMMShortcodeModuleSettings = function(params) {
		// Initialize parameters
		this.params = $.extend({}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.init(), this));
	};

	$.JSNTplMMShortcodeModuleSettings.prototype = {
		init : function() {
			
			this.moduleTotal = $('#jsn-tpl-mm-element-module-list-container').attr('data-module-total');
			//this.renderModuleList();
			this.getTotalModule();
			this.container = $('#jsn-tpl-mm-element-module-list-container');
			var self = this;
			$('#jsn-tpl-mm-element-module-load-more-btn').on('click', function(e) {
				
				e.preventDefault();
				self.getTotalModule();
			});
			
			$('#jsn-tpl-mm-element-module-btn-reset').on('click', function(e) {
				e.preventDefault();
				$('#jsn-tpl-mm-element-module-load-more-btn').removeClass('hidden');
				$('#jsn-tpl-mm-element-module-content').html('');
				$('#jsn-tpl-mm-element-module-input-search', this.container).val('');
				self.getTotalModule();
			});
			
            $('#jsn-tpl-mm-element-module-input-search', this.container).JSNTplMMShortcodeModuleSettingsDelayKeyup(function (md) {
            	//self.filterModule($(md).val());
            	$('#jsn-tpl-mm-element-module-load-more-btn').removeClass('hidden');
            	$('#jsn-tpl-mm-element-module-content').html('');
            	$('#jsn-tpl-mm-element-module-list-container').attr('data-start', 0);
            	self.getTotalModule();
            }, 500);
			
		},
		
		filterModule: function (value) {
			var self = this; 
            var resultsFilter = $('#jsn-tpl-mm-element-module-content', self.container);
            if (value != "all") {
                $(resultsFilter).find(".jsn-item-type").hide();
                $(resultsFilter).find(".jsn-item-type").each(function () {
                    var findDiv = $(this).find("div");
                    var textField = textField ? findDiv.attr("data-module-title").toLowerCase() : findDiv.attr("title").toLowerCase();
                    if (textField.search(value.toLowerCase()) === -1) {
                        $(this).hide();
                    } else {
                        $(this).fadeIn(500);
                    }
                });
            }
            else
            {	
            	$(resultsFilter).find(".jsn-item-type").show();
            }
        },
        
		renderModuleList: function(){
			var self = this;
			var start = $('#jsn-tpl-mm-element-module-list-container').attr('data-start');
			$('#jsn-tpl-mm-element-module-icon-loading').removeClass('hidden');
			$.ajax({
				type: 'GET',
				dataType: 'html',
				url: 'index.php?widget=megamenu&action=get-module-list&shortcode=jsn_tpl_mm_module&start=' + start + '&rformat=raw&template=' + $('#jsn-tpl-name').val() + '&kword=' + encodeURIComponent($('#jsn-tpl-mm-element-module-input-search').val()) + '&' + self.params.token + '=1',
				success: function (response) {
					$('#jsn-tpl-mm-element-module-load-more-btn').removeClass('hidden');
					$('#jsn-tpl-mm-element-module-icon-loading').addClass('hidden');
					$('#jsn-tpl-mm-element-module-content').html($('#jsn-tpl-mm-element-module-content').html() + response);
					var length = $('#jsn-tpl-mm-element-module-content').find('.jsn-tpl-mm-element-module-item').length;
					$('#jsn-tpl-mm-element-module-list-container').attr('data-start', length);

					var id = $('#param-module_id').val();

					$('#jsn-tpl-mm-element-module-content').find('.jsn-tpl-mm-element-module-item').each(function(){
						
						if($(this).attr('id') == id)
						{
							$(this).find('div.jsn-tpl-mm-element-module-item-btn:first').addClass('active');
							var i = $("<i/>", {"class": "icon-checkmark"}).css('float', 'right');
							if (!$(this).find('div.jsn-tpl-mm-element-module-item-btn:first').find('span:first').find('i.icon-checkmark').length)
							{	
								$(this).find('div.jsn-tpl-mm-element-module-item-btn:first').find('span:first').append(i);
							}
							//$('#jsn-tpl-mm-element-module-name').val($(this).find('div.jsn-tpl-mm-element-module-item-btn:first').attr('data-module-title'));
							return false;
						}
					});
					
					$('#jsn-tpl-mm-element-module-content').find('.jsn-tpl-mm-element-module-item').unbind('click').bind('click', function(){
						var id = $(this).attr('id');
						
						$('#param-module_id').val(id);
						$('#param-module_id').trigger('change');
						
						$('.jsn-tpl-mm-element-module-item-btn').removeClass('active');
						$(this).find('div.jsn-tpl-mm-element-module-item-btn:first').addClass('active');
						$('#jsn-tpl-mm-element-module-name').val($(this).find('div.jsn-tpl-mm-element-module-item-btn:first').attr('data-module-title'));
						
						/*if ($('#param-el_title').val() == '')
						{
							$('#param-el_title').val($(this).find('div.jsn-tpl-mm-element-module-item-btn:first').attr('data-module-title'));
						}*/
					});

					if (parseInt($('#jsn-tpl-mm-element-module-list-container').attr('data-start')) >= parseInt($('#jsn-tpl-mm-element-module-list-container').attr('data-module-total')))
					{
						$('#jsn-tpl-mm-element-module-load-more-btn').addClass('hidden');
					}
				}
			});
		},

		getTotalModule: function(){
			var self = this;
			var start = $('#jsn-tpl-mm-element-module-list-container').attr('data-start');
			$('#jsn-tpl-mm-element-module-icon-loading').removeClass('hidden');
			$.ajax({
				type: 'GET',
				dataType: 'html',
				url: 'index.php?widget=megamenu&action=get-total-module&shortcode=jsn_tpl_mm_module&start=' + start + '&rformat=json&template=' + $('#jsn-tpl-name').val() + '&kword=' + encodeURIComponent($('#jsn-tpl-mm-element-module-input-search').val()) + '&' + self.params.token + '=1',
				success: function (response) {
					var obj = jQuery.parseJSON(response);
					//if (parseInt(obj.data['total']) > 0)
					//{
						$('#jsn-tpl-mm-element-module-list-container').attr('data-module-total', obj.data['total']);
						self.renderModuleList();
					//}	
				}
			});
		}
	};
	
	/*$(document).ready(function() {
		new $.JSNTplMMShortcodeModuleSettings();
	});*/
	
})(jQuery);
