/**
 * @version     $Id: launchpad.js 17049 2012-10-15 11:43:54Z giangnd $
 * @package     JSN.ImageShow
 * @subpackage  Html
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function($){
	
	$(document).ready(function (){
		
		var showlist;
		if ($('#jform_request_showlist_id').length > 0)
		{
			showlist = $('#jform_request_showlist_id');
		}
		
		if ($('#jform_params_showlist_id').length > 0)
		{
			showlist = $('#jform_params_showlist_id');
		}
		
		var showcase;
		if ($('#jform_request_showcase_id').length > 0)
		{
			showcase = $('#jform_request_showcase_id');
		}
		
		if ($('#jform_params_showcase_id').length > 0)
		{
			showcase = $('#jform_params_showcase_id');
		}
		
		if ($('option:selected', showlist).val() == 0)
		{
			$('#jsn-showlist-icon-warning').addClass('show-icon-warning');
			$('#showlist-icon-warning').css('display', '');	
			$('#showlist-icon-edit').css('display', 'none');
			$('#jsn-link-edit-showlist').attr('href', 'javascript: void(0);');
		}
		else
		{
			$('#showlist-icon-warning').css('display', 'none');
			$('#showlist-icon-edit').css('display', '');
			$('#jsn-link-edit-showlist').attr('href', "index.php?option=com_imageshow&controller=showlist&task=edit&cid[]=" + $('option:selected', showlist).val());	
		}

		if ($('option:selected', showcase).val() == 0)
		{
			$('#jsn-showcase-icon-warning').addClass('show-icon-warning');
			$('#showcase-icon-warning').css('display', '');	
			$('#showcase-icon-edit').css('display', 'none');
			$('#jsn-link-edit-showcase').attr('href', 'javascript: void(0);');
		}
		else
		{
			$('#showcase-icon-warning').css('display', 'none');
			$('#showcase-icon-edit').css('display', '');
			$('#jsn-link-edit-showcase').attr('href', "index.php?option=com_imageshow&controller=showcase&task=edit&cid[]=" + $('option:selected', showcase).val());	
		}
		
		showlist.change(function(el) {
			if($('option:selected', showlist).val() == 0)
			{		
				showlist.css('background', '#CC0000').css('color', '#fff');
				$('#jsn-showlist-icon-warning').addClass('show-icon-warning');
				$('#showlist-icon-warning').css('display', '');	
				$('#showlist-icon-edit').css('display', 'none');
				$('#jsn-link-edit-showlist').attr('href', 'javascript: void(0);');
			}
			else
			{
				showlist.css('background', 'none');
				showlist.css('color', '#000');
				$('#showlist-icon-warning').css('display', 'none');	
				$('#showlist-icon-edit').css('display', '');
				$('#jsn-link-edit-showlist').attr('href', "index.php?option=com_imageshow&controller=showlist&task=edit&cid[]=" + $('option:selected', showlist).val());
			}			
		});
		
		showcase.change(function(el) {
			if($('option:selected', showcase).val() == 0)
			{
				showcase.css('background', '#CC0000').css('style', '#fff');
				$('#showcase-icon-warning').css('display', '');
				$('#showcase-icon-edit').css('display', 'none');
				$('#jsn-link-edit-showcase').attr('href', "javascript: void(0);");					
			}
			else
			{
				showcase.css('background', 'none');
				showcase.css('color', '#000');
				$('#showcase-icon-warning').css('display', 'none');
				$('#showcase-icon-edit').css('display', '');
				$('#jsn-link-edit-showcase').attr('href', "index.php?option=com_imageshow&controller=showcase&task=edit&cid[]=" + $('option:selected', showcase).val());					
			}
		});			
	});
})(jQuery);