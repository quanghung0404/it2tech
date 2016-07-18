<?php
/**
 * @version    $Id: linkpopup.php 16104 2012-09-17 10:28:08Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
(function($){
	$(document).ready(function () {
		var tabSelectLinkCookieName = "jsn-is-tabs-select-link-ck-name";
		$("#jsn-showlist-tabs-link").tabs({
			selected : ($.cookie(tabSelectLinkCookieName) || 0),
			cache: false,
		    select : function(e, ui) {
		    	$.cookie(tabSelectLinkCookieName, ui.index);
		    },
			ajaxOptions: {
				error: function(xhr, status, index, anchor) {
					$(anchor.hash).html();
				},
				beforeSend: function (e, ui) {
					$('#loading-wrapper', window.parent.document).show();
					$('.ui-tabs-panel').html('');
				},
				success: function () {
					$('#loading-wrapper', window.parent.document).hide();
				}
			}
		});
	})
})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
</script>
<div id="jsn-showlist-tabs-link" class="jsn-tabs">
	<ul>
		<li><a
			href="index.php?option=com_imageshow&controller=articles&tmpl=component&function=jsnGetArticle"><?php echo JText::_('SHOWLIST_POPUP_IMAGE_ARTICLE');?>
		</a></li>
		<li><a
			href="index.php?option=com_imageshow&controller=menus&tmpl=component&function=jsnGetMenuItems"><?php echo JText::_('SHOWLIST_POPUP_IMAGE_MENU');?>
		</a></li>
	</ul>
</div>
