<?php
/**
 * @version    $Id: helper_flash.php 17048 2012-10-15 11:28:27Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die( 'Restricted access');

$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
// Fix backward compatible with Core 3.1.3
$objJSNUtils      		= JSNISFactory::getObj('classes.jsn_is_utils');
$coreInfo 	  			= $objJSNUtils->getComponentInfo();
$coreInfo 	  			= json_decode($coreInfo->manifest_cache);
$coreVersion			= $coreInfo->version;
$themeInfo 				= $objJSNShowcaseTheme->getThemeInfo($this->_showcaseThemeName);
$themeVersion 			= $themeInfo->version;
if (version_compare($coreVersion, '3.1.3') <= 0 && version_compare($themeVersion, '1.1.1') == 0)
{
	JTable::addIncludePath(JPATH_PLUGINS.DS.'jsnimageshow'.DS.trim(strtolower($this->_showcaseThemeName)).DS.'tables');
	JModelLegacy::addIncludePath(JPATH_PLUGINS.DS.'jsnimageshow'.DS.trim(strtolower($this->_showcaseThemeName)).DS.'models');
}

$objJSNShowcaseTheme->importTableByThemeName($this->_showcaseThemeName);
$objJSNShowcaseTheme->importModelByThemeName($this->_showcaseThemeName);
$modelShowcaseTheme = JModelLegacy::getInstance($this->_showcaseThemeName);

if (!$showcaseID || $skin != 'flash')
{
	$flashThemeID = 0;
}
else
{
	$flashThemeID = $themeID;
}

$items_flash 		= $modelShowcaseTheme->getTable($flashThemeID, 'flash');
$imgpanel_bg_value	= explode(',', $items_flash->imgpanel_bg_value);
JSNISFactory::importFile('classes.jsn_is_htmlselect');

/**
 * /////////////////////////////////////////////////////////Image Panel Begin////////////////////////////////////////////////////////////////////////////
 */
//Image Presentation Begin
//Fit Begin
$classImagePanel = 'imagePanel';
$imgPanelPresentationMode = array(
		'0' => array('value' => 'fit-in',
		'text' => JText::_('FIT_IN')),
		'1' => array('value' => 'expand-out',
		'text' => JText::_('EXPAND_OUT'))
);
$lists_flash['imgPanelPresentationMode'] = JHTML::_('select.genericList', $imgPanelPresentationMode, 'imgpanel_presentation_mode', 'class="inputbox '.$classImagePanel.'"', 'value', 'text', $items_flash->imgpanel_presentation_mode );

$imgPanelImgTransitionTypeFit = array(
		'0' => array('value' => 'random',
		'text' => JText::_('RANDOM')),
		'1' => array('value' => 'fade',
		'text' => JText::_('FADE')),
		'2' => array('value' => 'push',
		'text' => JText::_('PUSH')),
		'3' => array('value' => 'zoom',
		'text' => JText::_('ZOOM')),
		'4' => array('value' => 'flip3d',
		'text' => JText::_('THREE_D_FLIP')),
		'5' => array('value' => 'page-curl',
		'text' => JText::_('PAGE_CURL')),
		'6' => array('value' => 'page-flip',
		'text' => JText::_('PAGE_FLIP')),
		'7' => array('value' => 'none',
		'text' => JText::_('NO_TRANSITION'))
);
$lists_flash['imgPanelImgTransitionTypeFit'] = JHTML::_('select.genericList', $imgPanelImgTransitionTypeFit, 'imgpanel_img_transition_type_fit', 'class="inputbox '.$classImagePanel.'" '. '', 'value', 'text', ($items_flash->imgpanel_img_transition_type_fit!='')?$items_flash->imgpanel_img_transition_type_fit:'random');

$imgPanelImgClickActionFit = array(
		'0' => array('value' => 'no-action',
		'text' => JText::_('NO_ACTION')),
		'1' => array('value' => 'image-zooming',
		'text' => JText::_('IMAGE_ZOOMING')),
		'2' => array('value' => 'open-image-link',
		'text' => JText::_('OPEN_IMAGE_LINK'))
);
$lists_flash['imgPanelImgClickActionFit'] = JHTML::_('select.genericList', $imgPanelImgClickActionFit, 'imgpanel_img_click_action_fit', 'class="inputbox '.$classImagePanel.'" '. '', 'value', 'text', ($items_flash->imgpanel_img_click_action_fit!='')?$items_flash->imgpanel_img_click_action_fit:'image-zooming');
//Fit End

//Expand Begin
$imgPanelImgZoomingTypeExpand = array(
		'0' => array('value' => 'center',
		'text' => JText::_('CENTRIC')),
		'1' => array('value' => 'edge',
		'text' => JText::_('EDGE_TO_EDGE'))
);
$lists_flash['imgPanelImgZoomingTypeExpand'] = JHTML::_('select.genericList', $imgPanelImgZoomingTypeExpand, 'imgpanel_img_zooming_type_expand', 'class="inputbox"', 'value', 'text', ($items_flash->imgpanel_img_zooming_type_expand!='')?$items_flash->imgpanel_img_zooming_type_expand:'center' );

$imgPanelImgTransitionTypeExpand = array(
		'0' => array('value' => 'random',
		'text' => JText::_('RANDOM')),
		'1' => array('value' => 'cross-fade',
		'text' => JText::_('CROSS_FADE')),
		'2' => array('value' => 'linear-fade',
		'text' => JText::_('LINEAR_FADE')),
		'3' => array('value' => 'radial-fade',
		'text' => JText::_('RADIAL_FADE')),
		'4' => array('value' => 'black-dim',
		'text' => JText::_('BLACK_DIM')),
		'5' => array('value' => 'white-burn',
		'text' => JText::_('WHITE_BURN')),
		'6' => array('value' => 'none',
		'text' => JText::_('NO_TRANSITION'))
);
$lists_flash['imgPanelImgTransitionTypeExpand'] = JHTML::_('select.genericList', $imgPanelImgTransitionTypeExpand, 'imgpanel_img_transition_type_expand', 'class="inputbox '.$classImagePanel.'" '. '', 'value', 'text', ($items_flash->imgpanel_img_transition_type_expand!='')?$items_flash->imgpanel_img_transition_type_expand:'random' );
$imgPanelImgMotionTypeExpand = array(
		'0' => array('value' => 'no-motion',
		'text' => JText::_('NO_MOTION')),
		'1' => array('value' => 'zoom-in',
		'text' => JText::_('ZOOM_IN')),
		'2' => array('value' => 'zoom-out',
		'text' => JText::_('ZOOM_OUT')),
		'3' => array('value' => 'random',
		'text' => JText::_('RANDOM'))
);
$lists_flash['imgPanelImgMotionTypeExpand'] = JHTML::_('select.genericList', $imgPanelImgMotionTypeExpand, 'imgpanel_img_motion_type_expand', 'class="inputbox '.$classImagePanel.'" onchange="JSNISClassicTheme.showcaseChangeImageMotionType(this); " ', 'value', 'text', ($items_flash->imgpanel_img_motion_type_expand!='')?$items_flash->imgpanel_img_motion_type_expand:'center-random' );
$imgPanelImgClickActionExpand = array(
		'0' => array('value' => 'no-action',
		'text' => JText::_('NO_ACTION')),
		'1' => array('value' => 'image-zooming',
		'text' => JText::_('IMAGE_ZOOMING')),
		'2' => array('value' => 'open-image-link',
		'text' => JText::_('OPEN_IMAGE_LINK'))
);
$lists_flash['imgPanelImgClickActionExpand'] = JHTML::_('select.genericList', $imgPanelImgClickActionExpand, 'imgpanel_img_click_action_expand', 'class="inputbox '.$classImagePanel.'" '. '', 'value', 'text', ($items_flash->imgpanel_img_click_action_expand!='')?$items_flash->imgpanel_img_click_action_expand:'open-image-link' );
//Expand End

$openLinkIn = array(
		'0' => array('value' => 'current-browser',
		'text' => JText::_('CURRENT_BROWSER')),
		'1' => array('value' => 'new-browser',
		'text' => JText::_('NEW_BROWSER'))
);
$lists_flash['imgPanelImgOpenLinkInExpand']	= JHTML::_('select.genericList', $openLinkIn, 'imgpanel_img_open_link_in_expand', 'class="inputbox" ', 'value', 'text', ($items_flash->imgpanel_img_open_link_in_expand != '') ? $items_flash->imgpanel_img_open_link_in_expand : 'current-browser' );
$lists_flash['imgPanelImgOpenLinkInFit']		= JHTML::_('select.genericList', $openLinkIn, 'imgpanel_img_open_link_in_fit', 'class="inputbox" ', 'value', 'text', ($items_flash->imgpanel_img_open_link_in_fit != '') ? $items_flash->imgpanel_img_open_link_in_fit : 'current-browser' );
//	$lists_flash['imgPanelImgShowImageShadowFit'] = JSNISHTMLSelect::booleanlist('imgpanel_img_show_image_shadow_fit','class="inputbox '.$classImagePanel.'"', $items_flash->imgpanel_img_show_image_shadow_fit);
$lists_flash['imgPanelImgShowImageShadowFit'] = JHTML::_('jsnselect.booleanlist', 'imgpanel_img_show_image_shadow_fit', 'class="inputbox '.$classImagePanel.'"', $items_flash->imgpanel_img_show_image_shadow_fit, 'JYES', 'JNO', false, 'yes', 'no');

//Image Presentation End

//Background Begin
$imgPanelBgType = array(
	'0' => array('value' => 'solid-color',
	'text' => JText::_('SOLID_COLOR')),
	'1' => array('value' => 'linear-gradient',
	'text' => JText::_('LINEAR_GRADIENT')),
	'2' => array('value' => 'radial-gradient',
	'text' => JText::_('RADIAL_GRADIENT')),
	'3' => array('value' => 'pattern',
	'text' => JText::_('PATTERN')),
	'4' => array('value' => 'image',
	'text' => JText::_('IMAGE'))
);
$lists_flash['imgPanelBgType'] = JHTML::_('select.genericList', $imgPanelBgType, 'imgpanel_bg_type', 'class="inputbox '.$classImagePanel.'" '. '', 'value', 'text', ($items_flash->imgpanel_bg_type != '') ? $items_flash->imgpanel_bg_type : 'linear-gradient'  );
//Background End

//Watermark Presentation Begin
//$lists_flash['imgPanelShowWatermark'] = JSNISHTMLSelect::booleanlist('imgpanel_show_watermark','class="inputbox '.$classImagePanel.'"', $items_flash->imgpanel_show_watermark);
$lists_flash['imgPanelShowWatermark'] = JHTML::_('jsnselect.booleanlist', 'imgpanel_show_watermark', 'class="inputbox '.$classImagePanel.'"', $items_flash->imgpanel_show_watermark, 'JYES', 'JNO', false, 'yes', 'no');

$imgPanelWatermarkPosition = array(
	'0' => array('value' => 'center',
	'text' => JText::_('CENTER')),
	'1' => array('value' => 'top-left',
	'text' => JText::_('TOP_LEFT')),
	'2' => array('value' => 'top-right',
	'text' => JText::_('TOP_RIGHT')),
	'3' => array('value' => 'bottom-left',
	'text' => JText::_('BOTTOM_LEFT')),
	'4' => array('value' => 'bottom-right',
	'text' => JText::_('BOTTOM_RIGHT'))
);
$lists_flash['imgPanelWatermarkPosition'] = JHTML::_('select.genericList', $imgPanelWatermarkPosition, 'imgpanel_watermark_position', 'class="inputbox '.$classImagePanel.'" onChange="JSNISClassicTheme.ChangeWatermark();"'. '', 'value', 'text', ($items_flash->imgpanel_watermark_position!='')?$items_flash->imgpanel_watermark_position:'top-right' );
//Watermark Presentation End

//Overlay Effect Begin
$imgPanelOverlayEffectType = array(
	'0' => array('value' => 'vertical-floating-bar',
	'text' => JText::_('HORIZONTAL_FLOATING_BAR')),
	'1' => array('value' => 'horizontal-floating-bar',
	'text' => JText::_('VERTICAL_FLOATING_BAR')),
	'2' => array('value' => 'winter-snow',
	'text' => JText::_('WINTER_SNOW')),
	'3' => array('value' => 'old-movie',
	'text' => JText::_('OLD_MOVIE')),
	'4' => array('value' => 'water-bubbles',
	'text' => JText::_('WATER_BUBBLES')),
	'5' => array('value' => 'sparkle',
	'text' => JText::_('SPARKLE'))
);

$lists_flash['imgPanelOverlayEffectType'] = JHTML::_('select.genericList', $imgPanelOverlayEffectType, 'imgpanel_overlay_effect_type', 'class="inputbox '.$classImagePanel.'" '. '', 'value', 'text', $items_flash->imgpanel_overlay_effect_type );

$imgPanelShowOverlayEffect = array(
	'1' => array('value' => 'during',
	'text' => JText::_('OVERLAY_ON_DURING_SLIDESHOW')),
	'0' => array('value' => 'yes',
	'text' => JText::_('OVERLAY_ALWAYS_ON')),
	'2' => array('value' => 'no',
	'text' => JText::_('OVERLAY_OFF'))
);

$lists_flash['imgPanelShowOverlayEffect'] = JHTML::_('select.genericList', $imgPanelShowOverlayEffect, 'imgpanel_show_overlay_effect', 'class="inputbox '.$classImagePanel.'" '. '', 'value', 'text', $items_flash->imgpanel_show_overlay_effect );

//Overlay Effect End

//Inner Shawdow Begin
//$lists_flash['imgPanelShowInnerShawdow'] = JSNISHTMLSelect::booleanlist('imgpanel_show_inner_shawdow','class="inputbox '.$classImagePanel.'"', $items_flash->imgpanel_show_inner_shawdow);
$lists_flash['imgPanelShowInnerShawdow'] = JHTML::_('jsnselect.booleanlist', 'imgpanel_show_inner_shawdow', 'class="inputbox '.$classImagePanel.'"', $items_flash->imgpanel_show_inner_shawdow, 'JYES', 'JNO', false, 'yes', 'no');
//Inner Shawdow End

/**
 * /////////////////////////////////////////////////////////Image Panel End////////////////////////////////////////////////////////////////////////////
 */

/**
 * /////////////////////////////////////////////////////////////////////////////////Thumbnail Panel Begin////////////////////////////////////////////////////////////
 */
//General Begin
$classThumbPanel = 'thumbnailPanel';
$thumbPanelStatus = array(
		'0' => array('value' => 'auto',
		'text' => JText::_('AUTO_SHOW_HIDE')),
		'1' => array('value' => 'on',
		'text' => JText::_('ALWAYS_ON')),
		'2' => array('value' => 'off',
		'text' => JText::_('GLOBAL_OFF'))
);

$lists_flash['thumbPanelShowPanel'] = JHTML::_('select.genericList', $thumbPanelStatus, 'thumbpanel_show_panel', 'class="inputbox '.$classThumbPanel.'" '. '', 'value', 'text', $items_flash->thumbpanel_show_panel);
$thumbPanelPanelPosition = array(
		'0' => array('value' => 'top',
		'text' => JText::_('TOP')),
		'1' => array('value' => 'bottom',
		'text' => JText::_('BOTTOM'))
);
$lists_flash['thumbPanelPanelPosition']		= JHTML::_('select.genericList', $thumbPanelPanelPosition, 'thumbpanel_panel_position', 'class="inputbox '.$classThumbPanel.'" '. '', 'value', 'text', (!empty($items_flash->thumbpanel_panel_position))?$items_flash->thumbpanel_panel_position:'bottom' );
//	$lists_flash['thumbPanelCollapsiblePosition']	= JSNISHTMLSelect::booleanlist('thumbpanel_collapsible_position','class="inputbox '.$classThumbPanel.'"', $items_flash->thumbpanel_collapsible_position);
$lists_flash['thumbPanelCollapsiblePosition'] = JHTML::_('jsnselect.booleanlist', 'thumbpanel_collapsible_position', 'class="inputbox '.$classThumbPanel.'"', $items_flash->thumbpanel_collapsible_position, 'JYES', 'JNO', false, 'yes', 'no');
$thumbPanelThumbBrowsingMode = array(
		'0' => array('value' => 'pagination',
		'text' => JText::_('PAGINATION')),
		'1' => array('value' => 'sliding',
		'text' => JText::_('SLIDING'))
);
$lists_flash['thumbPanelThumbBrowsingMode']	= JHTML::_('select.genericList', $thumbPanelThumbBrowsingMode, 'thumbpanel_thumb_browsing_mode', 'class="inputbox '.$classThumbPanel.'" onchange="JSNISClassicTheme.ShowcaseSwitchBrowsingMode();"'. '', 'value', 'text', $items_flash->thumbpanel_thumb_browsing_mode );
//	$lists_flash['thumbPanelShowThumbStatus']		= JSNISHTMLSelect::booleanlist('thumbpanel_show_thumb_status','class="inputbox '.$classThumbPanel.'"', $items_flash->thumbpanel_show_thumb_status);
$lists_flash['thumbPanelShowThumbStatus'] = JHTML::_('jsnselect.booleanlist', 'thumbpanel_show_thumb_status', 'class="inputbox '.$classThumbPanel.'"', $items_flash->thumbpanel_show_thumb_status, 'JYES', 'JNO', false, 'yes', 'no');
//General End

//Thumbnail Begin
$thumbPanelPresentationMode = array(
		'0' => array('value' => 'image',
		'text' => JText::_('IMAGE')),
		'1' => array('value' => 'number',
		'text' => JText::_('NUMBER'))
);
$lists_flash['thumbPanelPresentationMode']	= JHTML::_('select.genericList', $thumbPanelPresentationMode, 'thumbpanel_presentation_mode', 'class="inputbox '.$classThumbPanel.'" '. '', 'value', 'text', $items_flash->thumbpanel_presentation_mode );
//	$lists_flash['thumbPanelEnableBigThumb']		= JSNISHTMLSelect::booleanlist('thumbpanel_enable_big_thumb','class="inputbox '.$classThumbPanel.'"', $items_flash->thumbpanel_enable_big_thumb);
$lists_flash['thumbPanelEnableBigThumb'] = JHTML::_('jsnselect.booleanlist', 'thumbpanel_enable_big_thumb', 'class="inputbox '.$classThumbPanel.'"', $items_flash->thumbpanel_enable_big_thumb, 'JYES', 'JNO', false, 'yes', 'no');

//Thumbnail End
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Thumbnail Panel End//////////////////////////////////////////////////////////////////////////////////
 */
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Information Panel Begin//////////////////////////////////////////////////////////////////////////////////
 */
$classInfoPanel = 'informationPanel';
//General Begin
$infoPanelPanelPosition = array(
		'0' => array('value' => 'top',
		'text' => JText::_('TOP')),
		'1' => array('value' => 'bottom',
		'text' => JText::_('BOTTOM'))
);
$lists_flash['infoPanelPanelPosition'] = JHTML::_('select.genericList', $infoPanelPanelPosition, 'infopanel_panel_position', 'class="inputbox '.$classInfoPanel.'" '. '', 'value', 'text', $items_flash->infopanel_panel_position );

$infoPanelPresentation = array(
		'0' => array('value' => 'auto',
		'text' => JText::_('AUTO_SHOW_HIDE')),
		'1' => array('value' => 'on',
		'text' => JText::_('ALWAYS_ON')),
		'2' => array('value' => 'off',
		'text' => JText::_('GLOBAL_OFF'))
);
$lists_flash['infoPanelPresentation'] = JHTML::_('select.genericList', $infoPanelPresentation, 'infopanel_presentation', 'class="inputbox '.$classInfoPanel.'" '. '', 'value', 'text', $items_flash->infopanel_presentation );
//General End

//Image Title Begin

// 	$lists_flash['infoPanelShowTitle'] = JSNISHTMLSelect::booleanlist('infopanel_show_title','class="inputbox '.$classInfoPanel.'"', $items_flash->infopanel_show_title);
$lists_flash['infoPanelShowTitle'] = JHTML::_('jsnselect.booleanlist', 'infopanel_show_title', 'class="inputbox '.$classInfoPanel.'"', $items_flash->infopanel_show_title, 'JYES', 'JNO', false, 'yes', 'no');

$infoPanelPanelClickAction = array(
		'0' => array('value' => 'no-action',
		'text' => JText::_('NO_ACTION')),
		'1' => array('value' => 'open-image-link',
		'text' => JText::_('OPEN_IMAGE_LINK'))
);
$lists_flash['infoPanelPanelClickAction'] = JHTML::_('select.genericList', $infoPanelPanelClickAction, 'infopanel_panel_click_action', 'class="inputbox '.$classInfoPanel.'" '. '', 'value', 'text', $items_flash->infopanel_panel_click_action );
//Image Title End

//Image Description Begin
//	$lists_flash['infoPanelShowDes'] = JSNISHTMLSelect::booleanlist('infopanel_show_des','class="inputbox '.$classInfoPanel.'"', $items_flash->infopanel_show_des);
$lists_flash['infoPanelShowDes'] = JHTML::_('jsnselect.booleanlist', 'infopanel_show_des', 'class="inputbox '.$classInfoPanel.'"', $items_flash->infopanel_show_des, 'JYES', 'JNO', false, 'yes', 'no');
//Image Description End

//Link Begin
//		$lists_flash['infoPanelShowLink'] = JSNISHTMLSelect::booleanlist('infopanel_show_link','class="inputbox '.$classInfoPanel.'"', $items_flash->infopanel_show_link);
$lists_flash['infoPanelShowLink'] = JHTML::_('jsnselect.booleanlist', 'infopanel_show_link', 'class="inputbox '.$classInfoPanel.'"', $items_flash->infopanel_show_link, 'JYES', 'JNO', false, 'yes', 'no');
//Link End

//Open link in begin
$lists_flash['infoPanelOpenLinkIn'] = JHTML::_('select.genericList', $openLinkIn, 'infopanel_open_link_in', 'class="inputbox" ', 'value', 'text', ($items_flash->infopanel_open_link_in != '') ? $items_flash->infopanel_open_link_in : 'current-browser' );
//Open link in end
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Information Panel End//////////////////////////////////////////////////////////////////////////////////
 */

/**
 * ///////////////////////////////////////////////////////////////////////////////////////Toolbar Panel Begin//////////////////////////////////////////////////////////////////////////////////
 */
$classToolBarPanel = 'toolbarPanel';
//General Begin
$toolBarPanelPanelPosition = array(
		'0' => array('value' => 'top',
		'text' => JText::_('TOP')),
		'1' => array('value' => 'bottom',
		'text' => JText::_('BOTTOM'))
);
$lists_flash['toolBarPanelPanelPosition'] = JHTML::_('select.genericList', $toolBarPanelPanelPosition, 'toolbarpanel_panel_position', 'class="inputbox '.$classToolBarPanel.'" '. '', 'value', 'text', ($items_flash->toolbarpanel_panel_position!='')?$items_flash->toolbarpanel_panel_position:'bottom' );

$toolBarPanelPresentation = array(
		'0' => array('value' => 'auto',
		'text' => JText::_('AUTO_SHOW_HIDE')),
		'1' => array('value' => 'on',
		'text' => JText::_('ALWAYS_ON')),
		'2' => array('value' => 'off',
		'text' => JText::_('GLOBAL_OFF'))
);
$lists_flash['toolBarPanelPresentation'] = JHTML::_('select.genericList', $toolBarPanelPresentation, 'toolbarpanel_presentation', 'class="inputbox '.$classToolBarPanel.'" '. '', 'value', 'text', ($items_flash->toolbarpanel_presentation!=''?$items_flash->toolbarpanel_presentation:'auto') );
//General End

//Functions Begin
// 	$lists_flash['toolBarPanelShowImageNavigation'] 		= JSNISHTMLSelect::booleanlist('toolbarpanel_show_image_navigation','class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_show_image_navigation);
$lists_flash['toolBarPanelShowImageNavigation'] = JHTML::_('jsnselect.booleanlist', 'toolbarpanel_show_image_navigation', 'class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_show_image_navigation, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_flash['toolBarPanelSlideShowPlayer'] 			= JSNISHTMLSelect::booleanlist('toolbarpanel_slideshow_player','class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_slideshow_player);
$lists_flash['toolBarPanelSlideShowPlayer'] = JHTML::_('jsnselect.booleanlist', 'toolbarpanel_slideshow_player', 'class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_slideshow_player, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_flash['toolBarPanelShowFullscreenSwitcher'] 	= JSNISHTMLSelect::booleanlist('toolbarpanel_show_fullscreen_switcher','class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_show_fullscreen_switcher);
$lists_flash['toolBarPanelShowFullscreenSwitcher'] = JHTML::_('jsnselect.booleanlist', 'toolbarpanel_show_fullscreen_switcher', 'class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_show_fullscreen_switcher, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_flash['toolBarPanelShowTooltip'] 				= JSNISHTMLSelect::booleanlist('toolbarpanel_show_tooltip','class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_show_tooltip);
$lists_flash['toolBarPanelShowTooltip'] = JHTML::_('jsnselect.booleanlist', 'toolbarpanel_show_tooltip', 'class="inputbox '.$classToolBarPanel.'"', $items_flash->toolbarpanel_show_tooltip, 'JYES', 'JNO', false, 'yes', 'no');
//Functions End
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Toolbar Panel End//////////////////////////////////////////////////////////////////////////////////
 */

/**
 * ///////////////////////////////////////////////////////////////////////////////////////SlideShow Begin//////////////////////////////////////////////////////////////////////////////////
 */
$classSlideShowPanel = 'slideshowPanel';
//Action on Slideshow Start Begin

//	$lists_flash['slideShowEnableKenBurnEffect']	= JSNISHTMLSelect::booleanlist('slideshow_enable_ken_burn_effect','class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_enable_ken_burn_effect);
$lists_flash['slideShowEnableKenBurnEffect'] = JHTML::_('jsnselect.booleanlist', 'slideshow_enable_ken_burn_effect', 'class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_enable_ken_burn_effect, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_flash['slideShowHideThumbPanel']		= JSNISHTMLSelect::booleanlist('slideshow_hide_thumb_panel','class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_hide_thumb_panel);
$lists_flash['slideShowHideThumbPanel'] = JHTML::_('jsnselect.booleanlist', 'slideshow_hide_thumb_panel', 'class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_hide_thumb_panel, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_flash['slideShowHideImageNavigation']	= JSNISHTMLSelect::booleanlist('slideshow_hide_image_navigation','class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_hide_image_navigation);
$lists_flash['slideShowHideImageNavigation'] = JHTML::_('jsnselect.booleanlist', 'slideshow_hide_image_navigation', 'class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_hide_image_navigation, 'JYES', 'JNO', false, 'yes', 'no');

//Action on Slideshow Start End

//Slideshow Process Begin

//	$lists_flash['slideShowProcess'] 	 = JSNISHTMLSelect::booleanlist('slideshow_auto_play','class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_auto_play);
$lists_flash['slideShowProcess'] = JHTML::_('jsnselect.booleanlist', 'slideshow_auto_play', 'class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_auto_play, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_flash['slideShowShowStatus']= JSNISHTMLSelect::booleanlist('slideshow_show_status','class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_show_status);
$lists_flash['slideShowShowStatus'] = JHTML::_('jsnselect.booleanlist', 'slideshow_show_status', 'class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_show_status, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_flash['slideShowLooping'] 	 = JSNISHTMLSelect::booleanlist('slideshow_looping','class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_looping);
$lists_flash['slideShowLooping'] = JHTML::_('jsnselect.booleanlist', 'slideshow_looping', 'class="inputbox '.$classSlideShowPanel.'"', $items_flash->slideshow_looping, 'JYES', 'JNO', false, 'yes', 'no');
//Slideshow Process End

/**
 * ///////////////////////////////////////////////////////////////////////////////////////SlideShow End//////////////////////////////////////////////////////////////////////////////////
 */

$objJSNShowlist		= JSNISFactory::getObj('classes.jsn_is_showlist');
$lists_flash['showlist'] 	= $objJSNShowlist->renderShowlistComboBox(null, 'Select showlist to see live view with', 'showlist_id', 'onchange="JSNISClassicTheme.EnableShowCasePreview();"');
