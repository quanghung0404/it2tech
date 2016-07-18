<?php
/**
 * @version    $Id: plugin.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$showlistID = JRequest::getInt('showlist_id');
$showcaseID = JRequest::getInt('showcase_id');
$url 		= JURI::root() . '/administrator/components/com_imageshow/assets/swf';
?>
<script type="text/javascript">
var clipboard = null;
(function($){
	$(document).ready(function () {
		ZeroClipboard.moviePath = "<?php echo $url; ?>/ZeroClipboard.swf";
		clipboard 			= new ZeroClipboard.Client();
		clipboard.addEventListener('complete', function (client, text)
		{
			if ($('#syntax-plugin').val() != '')
			{
				var checkIcon = $('.jsn-clipboard-checkicon')[0];
				checkIcon.addClass('jsn-clipboard-coppied');
				(function() { checkIcon.removeClass('jsn-clipboard-coppied');  } ).delay(3000);
			}
		});
		clipboard.glue('jsn-clipboard-button', 'jsn-clipboard-container');
		clipboard.setText($('#syntax-plugin').val());
		$('#syntax-plugin').change(function()
		{
			clipboard.setText($('#syntax-plugin').val());
		});
	});
})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
</script>
<div class="jsn-plugin-details">
	<div class="jsn-bootstrap">
		<div class="form-search">
		<?php
		echo JText::_('CPANEL_PLEASE_INSERT_FOLLOWING_TEXT_TO_YOUR_ARTICLE_AT_THE_POSITION_WHERE_YOU_WANT_TO_SHOW_GALLERY');
		?>
			<div id="jsn-clipboard">
				<span class="jsn-clipboard-input"> <input type="text"
					id="syntax-plugin" name="plugin"
					value="{imageshow sl=<?php echo $showlistID; ?> sc=<?php echo $showcaseID; ?> /}" />
					<span class="jsn-clipboard-checkicon jsn-icon-ok"></span> </span> <span
					id="jsn-clipboard-container">
					<button id="jsn-clipboard-button" class="btn">
					<?php echo JText::_('CPANEL_COPY_TO_CLIPBOARD')?>
					</button> </span>
			</div>
		</div>
		<?php
		echo JText::_('CPANEL_MORE_DETAILS_ABOUT_PLUGIN_SYNTAX');
		?>
	</div>
</div>
