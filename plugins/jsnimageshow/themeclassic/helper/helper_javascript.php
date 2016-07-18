<?php
/**
 * @version    $Id: helper_javascript.php 16940 2012-10-12 04:40:52Z haonv $
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

$objJSNShowcaseTheme->importTableByThemeName($this->_showcaseThemeName);
$objJSNShowcaseTheme->importModelByThemeName($this->_showcaseThemeName);
$modelShowcaseTheme = JModelLegacy::getInstance($this->_showcaseThemeName);
if (!$showcaseID || $skin != 'javascript')
{
	$javascriptThemeID = 0;
}
else
{
	$javascriptThemeID = $themeID;
}

$items_javascript	= $modelShowcaseTheme->getTable($javascriptThemeID, 'javascript');

JSNISFactory::importFile('classes.jsn_is_htmlselect');

$objJSNShowlist		= JSNISFactory::getObj('classes.jsn_is_showlist');
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Javascript Classic//////////////////////////////////////////////////////////////////////////////////
 */
/**
 * /////////////////////////////////////////////////////////Image Panel Begin////////////////////////////////////////////////////////////////////////////
 */
//Image Presentation Begin
//Fit Begin
$classJSImagePanel = 'jsImagePanel';
$jsImgPanelPresentationMode = array(
		'0' => array('value' => 'fit-in',
		'text' => JText::_('FIT_IN')),
		'1' => array('value' => 'expand-out',
		'text' => JText::_('EXPAND_OUT'))
);

$lists_javascript['jsImgPanelPresentationMode'] = JHTML::_('select.genericList', $jsImgPanelPresentationMode, 'imgpanel_presentation_mode', 'class="inputbox '.$classJSImagePanel.'"', 'value', 'text', $items_javascript->imgpanel_presentation_mode, 'js_imgpanel_presentation_mode' );

$jsImgPanelImgClickActionFit = array(
		'0' => array('value' => 'no-action',
		'text' => JText::_('NO_ACTION')),
		'1' => array('value' => 'open-image-link',
		'text' => JText::_('OPEN_IMAGE_LINK'))
);
$lists_javascript['jsImgPanelImgClickActionFit'] = JHTML::_('select.genericList', $jsImgPanelImgClickActionFit, 'imgpanel_img_click_action_fit', 'class="inputbox '.$classJSImagePanel.'" '. '', 'value', 'text', ($items_javascript->imgpanel_img_click_action_fit!='')?$items_javascript->imgpanel_img_click_action_fit:'no-action', 'js_imgpanel_img_click_action_fit');
//Fit End
$jsImgPanelImgClickActionExpand = array(
		'0' => array('value' => 'no-action',
		'text' => JText::_('NO_ACTION')),
		'2' => array('value' => 'open-image-link',
		'text' => JText::_('OPEN_IMAGE_LINK'))
);
$lists_javascript['jsImgPanelImgClickActionExpand'] = JHTML::_('select.genericList', $jsImgPanelImgClickActionExpand, 'imgpanel_img_click_action_expand', 'class="inputbox '.$classJSImagePanel.'" '. '', 'value', 'text', ($items_javascript->imgpanel_img_click_action_expand!='')?$items_javascript->imgpanel_img_click_action_expand:'open-image-link', 'js_imgpanel_img_click_action_expand' );
//Expand End

$jsOpenLinkIn = array(
		'0' => array('value' => 'current-browser',
		'text' => JText::_('CURRENT_BROWSER')),
		'1' => array('value' => 'new-browser',
		'text' => JText::_('NEW_BROWSER'))
);
$lists_javascript['jsImgPanelImgOpenLinkInExpand']	= JHTML::_('select.genericList', $jsOpenLinkIn, 'imgpanel_img_open_link_in_expand', 'class="inputbox" ', 'value', 'text', ($items_javascript->imgpanel_img_open_link_in_expand != '') ? $items_javascript->imgpanel_img_open_link_in_expand : 'current-browser' );
$lists_javascript['jsImgPanelImgOpenLinkInFit']	= JHTML::_('select.genericList', $jsOpenLinkIn, 'imgpanel_img_open_link_in_fit', 'class="inputbox" ', 'value', 'text', ($items_javascript->imgpanel_img_open_link_in_fit != '') ? $items_javascript->imgpanel_img_open_link_in_fit : 'current-browser' );

//Image Presentation End

//Background Begin
$jsImgPanelBgType = array(
	'0' => array('value' => 'solid-color',
	'text' => JText::_('SOLID_COLOR')),
	'1' => array('value' => 'pattern',
	'text' => JText::_('PATTERN')),
	'2' => array('value' => 'image',
	'text' => JText::_('IMAGE'))
);
$lists_javascript['jsImgPanelBgType'] = JHTML::_('select.genericList', $jsImgPanelBgType, 'imgpanel_bg_type', 'class="inputbox '.$classJSImagePanel.'" '. '', 'value', 'text', ($items_javascript->imgpanel_bg_type != '') ? $items_javascript->imgpanel_bg_type : 'solid-color' , 'js_imgpanel_bg_type' );
//Background End

/**
 * /////////////////////////////////////////////////////////Image Panel End////////////////////////////////////////////////////////////////////////////
 */

/**
 * /////////////////////////////////////////////////////////////////////////////////Thumbnail Panel Begin////////////////////////////////////////////////////////////
 */
//General Begin
$classJSThumbPanel = 'jsThumbnailPanel';
$jsThumbPanelStatus = array(
		'0' => array('value' => 'on',
		'text' => JText::_('ALWAYS_ON')),
		'1' => array('value' => 'off',
		'text' => JText::_('GLOBAL_OFF'))
);
$lists_javascript['jsThumbPanelShowPanel'] = JHTML::_('select.genericList', $jsThumbPanelStatus, 'thumbpanel_show_panel', 'class="inputbox '.$classJSThumbPanel.'" '. '', 'value', 'text', $items_javascript->thumbpanel_show_panel, 'js_thumbpanel_show_panel');
$jsThumbPanelPanelPosition = array(
		'0' => array('value' => 'top',
		'text' => JText::_('TOP')),
		'1' => array('value' => 'bottom',
		'text' => JText::_('BOTTOM'))
);
$lists_javascript['jsThumbPanelPanelPosition']		= JHTML::_('select.genericList', $jsThumbPanelPanelPosition, 'thumbpanel_panel_position', 'class="inputbox '.$classJSThumbPanel.'" '. '', 'value', 'text', (!empty($items_javascript->thumbpanel_panel_position))?$items_javascript->thumbpanel_panel_position:'bottom', 'js_thumbpanel_panel_position' );
//General End
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Thumbnail Panel End//////////////////////////////////////////////////////////////////////////////////
 */
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Information Panel Begin//////////////////////////////////////////////////////////////////////////////////
 */
$classJSInfoPanel = 'jsInformationPanel';
//General Begin
$jsInfoPanelPanelPosition = array(
		'0' => array('value' => 'top',
		'text' => JText::_('TOP')),
		'1' => array('value' => 'bottom',
		'text' => JText::_('BOTTOM'))
);
$lists_javascript['jsInfoPanelPanelPosition'] = JHTML::_('select.genericList', $jsInfoPanelPanelPosition, 'infopanel_panel_position', 'class="inputbox '.$classJSInfoPanel.'" '. '', 'value', 'text', $items_javascript->infopanel_panel_position,'js_infopanel_panel_position' );

$jsInfoPanelPresentation = array(
		'0' => array('value' => 'on',
		'text' => JText::_('ALWAYS_ON')),
		'1' => array('value' => 'off',
		'text' => JText::_('GLOBAL_OFF'))
);
$lists_javascript['jsInfoPanelPresentation'] = JHTML::_('select.genericList', $jsInfoPanelPresentation, 'infopanel_presentation', 'class="inputbox '.$classJSInfoPanel.'" '. '', 'value', 'text', $items_javascript->infopanel_presentation );
//General End

//Image Title Begin
//	$lists_javascript['jsInfoPanelShowTitle'] = JSNISHTMLSelect::booleanlist('js_infopanel_show_title','class="inputbox '.$classJSInfoPanel.'"', $items_javascript->infopanel_show_title);
$lists_javascript['jsInfoPanelShowTitle'] = JHTML::_('jsnselect.booleanlist', 'js_infopanel_show_title', 'class="inputbox '.$classJSInfoPanel.'"', $items_javascript->infopanel_show_title, 'JYES', 'JNO', false, 'yes', 'no');
$jsInfoPanelPanelClickAction = array(
		'0' => array('value' => 'no-action',
		'text' => JText::_('NO_ACTION')),
		'1' => array('value' => 'open-image-link',
		'text' => JText::_('OPEN_IMAGE_LINK'))
);
$lists_javascript['jsInfoPanelPanelClickAction'] = JHTML::_('select.genericList', $jsInfoPanelPanelClickAction, 'infopanel_panel_click_action', 'class="inputbox '.$classJSInfoPanel.'" '. '', 'value', 'text', $items_javascript->infopanel_panel_click_action, 'js_infopanel_panel_click_action');
//Image Title End

//Image Description Begin
//	$lists_javascript['jsInfoPanelShowDes'] = JSNISHTMLSelect::booleanlist('js_infopanel_show_des','class="inputbox '.$classJSInfoPanel.'"', $items_javascript->infopanel_show_des);
$lists_javascript['jsInfoPanelShowDes'] = JHTML::_('jsnselect.booleanlist', 'js_infopanel_show_des', 'class="inputbox '.$classJSInfoPanel.'"', $items_javascript->infopanel_show_des, 'JYES', 'JNO', false, 'yes', 'no');
//Image Description End

//Link Begin
//		$lists_javascript['jsInfoPanelShowLink'] = JSNISHTMLSelect::booleanlist('js_infopanel_show_link','class="inputbox '.$classJSInfoPanel.'"', $items_javascript->infopanel_show_link);
$lists_javascript['jsInfoPanelShowLink'] = JHTML::_('jsnselect.booleanlist', 'js_infopanel_show_link', 'class="inputbox '.$classJSInfoPanel.'"', $items_javascript->infopanel_show_link, 'JYES', 'JNO', false, 'yes', 'no');
//Link End

//Open link in begin
$lists_javascript['jsInfoPanelOpenLinkIn'] = JHTML::_('select.genericList', $jsOpenLinkIn, 'infopanel_open_link_in', 'class="inputbox" ', 'value', 'text', ($items_javascript->infopanel_open_link_in != '') ? $items_javascript->infopanel_open_link_in : 'current-browser' );
//Open link in end
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Information Panel End//////////////////////////////////////////////////////////////////////////////////
 */

/**
 * ///////////////////////////////////////////////////////////////////////////////////////Toolbar Panel Begin//////////////////////////////////////////////////////////////////////////////////
 */
$classJSToolBarPanel = 'jsToolbarPanel';
//General Begin

$jsToolBarPanelPresentation = array(
		'0' => array('value' => 'auto',
		'text' => JText::_('AUTO_SHOW_HIDE')),
		'1' => array('value' => 'on',
        'text' => JText::_('ALWAYS_ON')),
		'2' => array('value' => 'off',
        'text' => JText::_('GLOBAL_OFF'))
);
$lists_javascript['jsToolBarPanelPresentation'] = JHTML::_('select.genericList', $jsToolBarPanelPresentation, 'toolbarpanel_presentation', 'class="inputbox '.$classJSToolBarPanel.'" '. '', 'value', 'text', ($items_javascript->toolbarpanel_presentation!=''?$items_javascript->toolbarpanel_presentation:'auto') );
$lists_javascript['jsToolBarPanelCounter'] = JHTML::_('jsnselect.booleanlist', 'toolbarpanel_show_counter', 'class="inputbox '.$classJSToolBarPanel.'"', $items_javascript->toolbarpanel_show_counter, 'JYES', 'JNO', false, 'yes', 'no');
//General End
/**
 * ///////////////////////////////////////////////////////////////////////////////////////Toolbar Panel End//////////////////////////////////////////////////////////////////////////////////
 */

/**
 * ///////////////////////////////////////////////////////////////////////////////////////SlideShow Begin//////////////////////////////////////////////////////////////////////////////////
 */
$classJSSlideShowPanel = 'jsslideshowPanel';
//	$lists_javascript['jsslideShowProcess'] 	 = JSNISHTMLSelect::booleanlist('js_slideshow_auto_play','class="inputbox '.$classJSSlideShowPanel.'"', $items_javascript->slideshow_auto_play);
$lists_javascript['jsslideShowProcess'] = JHTML::_('jsnselect.booleanlist', 'js_slideshow_auto_play', 'class="inputbox '.$classJSSlideShowPanel.'"', $items_javascript->slideshow_auto_play, 'JYES', 'JNO', false, 'yes', 'no');
//	$lists_javascript['jsslideShowLooping'] 	 = JSNISHTMLSelect::booleanlist('js_slideshow_looping','class="inputbox '.$classJSSlideShowPanel.'"', $items_javascript->slideshow_looping);
$lists_javascript['jsslideShowLooping'] = JHTML::_('jsnselect.booleanlist', 'js_slideshow_looping', 'class="inputbox '.$classJSSlideShowPanel.'"', $items_javascript->slideshow_looping, 'JYES', 'JNO', false, 'yes', 'no');
/**
 * ///////////////////////////////////////////////////////////////////////////////////////SlideShow End//////////////////////////////////////////////////////////////////////////////////
 */