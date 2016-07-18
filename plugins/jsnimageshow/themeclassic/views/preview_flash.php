<?php
/**
 * @version    $Id: preview_flash.php 17070 2012-10-16 04:34:57Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die( 'Restricted access' );
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$baseURL 		= $objJSNUtils->overrideURL();
$URLLocalFile	= dirname($baseURL).'/plugins/'.$this->_showcaseThemeType.'/'.$this->_showcaseThemeName.'/assets/swf/';
$baseURL		= $baseURL;
$format 		= JRequest::getVar('view_format', 'temporary');
$showcaseID 	= $array = JRequest::getVar('cid', array(0), '', 'array');
?>
<script type="text/javascript">
	var flashvars = {
			showcase: '<?php echo $baseURL; ?>index.php?option=com_imageshow%26controller=showcase%26format=showcase%26view_format=<?php echo $format;?>%26showcase_id=<?php echo $showcaseID[0];?>%26theme=<?php echo $this->_showcaseThemeName; ?>',
			baseurl:'<?php echo $URLLocalFile; ?>'
		};
	var params = {wmode:'opaque', bgcolor:'#FFFFFF', menu:'false', allowFullScreen:'true', quality:'best'};
	swfobject.embedSWF("<?php echo $URLLocalFile; ?>VisualPreview.swf", "jsn-flash-visual-object", "550", "350", "9.0.0", "<?php echo $URLLocalFile; ?>assets/js/expressInstall.swf", flashvars, params);

	(function ($){
		$(document).ready(function($) {
			JSNISClassicTheme.showPreviewHintText();
		});
	})(jQuery);
</script>
<input
	type="hidden" name="showcase_base_url"
	value="<?php echo dirname($baseURL).'/'; ?>" />
<div class="jsn-preview-showcase">
<?php echo JText::_('LIVE_VIEW'); ?>
	:
	<?php echo $lists_flash['showlist']; ?>
	<button disabled="disabled" id="preview-showcase-link"
		onclick="javascript:void(0);return false;">
		<?php echo JText::_('GO'); ?>
	</button>
	<div id="jsn-preview-hint-text">
		<span id="jsn-preview-hint-text-img" class="hint-text-deactive"></span>
		<div id="jsn-preview-hint-text-content">
			<ul>
				<li class="jsn-preview-hint-caption"><?php echo JText::_('LIVE_VIEW'); ?>
				</li>
				<li><?php echo JText::_('LIVE_VIEW_HINT_TEXT_1');?></li>
				<li><?php echo JText::_('LIVE_VIEW_HINT_TEXT_2');?></li>
			</ul>
		</div>
	</div>

</div>
<div class="jsn-preview">
	<div id="jsn-flash-visual-object">
		<p>
			<a href="http://www.adobe.com/go/getflashplayer"><img
				src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif"
				alt="Get Adobe Flash player" /> </a>
		</p>
	</div>
</div>

<script type="text/javascript">
	var flashvars = {
			baseurl:"<?php echo $URLLocalFile; ?>",
			edition: '<?php echo $objJSNUtils->getShortEdition();?>'
		};
	var params = {wmode:'opaque', bgcolor:'#333333', menu:'false', allowFullScreen:'true', quality:'best'};
	swfobject.embedSWF("<?php echo $URLLocalFile; ?>Gallery.swf", "jsn-flash-preview-object", "640", "480", "9.0.0", "<?php echo $URLLocalFile; ?>assets/js/expressInstall.swf", flashvars, params);
</script>
<div id="jsn-windowbox-content">
	<div id="jsn-flash-preview-object">
		<p>
			<a href="http://www.adobe.com/go/getflashplayer"><img
				src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif"
				alt="Get Adobe Flash player" /> </a>
		</p>
	</div>
	<div id="jsn-info-bar">
	<?php echo JText::_('PREVIEW_MODAL_DESCRIPTION'); ?>
	</div>
</div>
