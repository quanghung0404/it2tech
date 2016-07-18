<?php
/**
 * @version    $Id: default.php 17228 2012-10-18 10:19:53Z haonv $
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
jimport('joomla.html.pane');
$user 			= JFactory::getUser();
?>
<script type="text/javascript">
	(function ($){
		$(document).ready(function($) {
			$(".jsn-showcase-tabs").tabs();
			$("#js-acc-image-presentation-tabs").tabs();
			$("#acc-image-presentation-tabs").tabs();
			/*$('.jsn-preview-wrapper').stickyfloat({
				   duration: 0
		    });*/
			JSNISClassicTheme.Skin();
			JSNISClassicTheme.ShowcaseChangeBg();
			JSNISClassicTheme.visualFlash();
			JSNISClassicTheme.visualJS();

			$(".jsn-accordion").togglepanels('jsn-showcase-accordion-status-<?php echo $user->id.'-'.$cid;?>');

			JSNISClassicTheme.openLinkIn('imgpanel_img_click_action_fit', 'jsn-img-open-link-in-fit', 'acc-image-presentation');
			JSNISClassicTheme.openLinkIn('imgpanel_img_click_action_expand', 'jsn-img-open-link-in-expand', 'acc-image-presentation');
			JSNISClassicTheme.openLinkIn('infopanel_panel_click_action' , 'jsn-info-open-link-in', 'acc-caption-general');

			JSNISClassicTheme.openLinkIn('js_imgpanel_img_click_action_fit', 'js-jsn-img-open-link-in-fit', 'js-acc-image-presentation');
			JSNISClassicTheme.openLinkIn('js_imgpanel_img_click_action_expand', 'js-jsn-img-open-link-in-expand', 'js-acc-image-presentation');
			JSNISClassicTheme.openLinkIn('js_infopanel_panel_click_action' , 'js-jsn-info-open-link-in', 'js-acc-caption-general');

			JSNISClassicTheme.loadAccordionSettingCookie('jsn-showcase-accordion-status-<?php echo $user->id.'-'.$cid;?>');
			JSNISClassicTheme.fixDisplayNoMotion();
		});
	})(jQuery);

</script>

<!--  important -->
<input
	type="hidden" name="theme_name"
	value="<?php echo strtolower($this->_showcaseThemeName); ?>" />
<input
	type="hidden" name="theme_id"
	value="<?php echo (int) $items->theme_id; ?>" />
<!--  important -->
<div class="jsn-showcase-theme-settings-wrapper">
<?php include dirname(__FILE__).DS.'default_flash.php'; ?>
<?php include dirname(__FILE__).DS.'default_javascript.php'; ?>
	<div class="jsn-select-skin">
	<?php echo $lists['skin']; ?>
		<input type="hidden" name="theme_style_name"
			id="theme_style_name_value" value="<?php echo $skin;?>">
	</div>
	<div style="clear: both;"></div>
</div>
