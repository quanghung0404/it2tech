<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: helper.php 14559 2012-07-28 11:50:34Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
$objJSNShowcaseTheme->importTableByThemeName($this->_showcaseThemeName);
$objJSNShowcaseTheme->importModelByThemeName($this->_showcaseThemeName);
$modelShowcaseTheme = JModelLegacy::getInstance($this->_showcaseThemeName);
$items = $modelShowcaseTheme->getTable($themeID);

JSNISFactory::importFile('classes.jsn_is_htmlselect');

/**
 * /////////////////////////////////////////////////////////Image Panel Begin////////////////////////////////////////////////////////////////////////////
 */
$imgLayout = array(
	'0' => array('value' => 'fixed', 'text' => JText::_('THEME_GRID_LAYOUT_FIXED')),
	'1' => array('value' => 'fluid', 'text' => JText::_('THEME_GRID_LAYOUT_FLUID'))
);
$lists['imgLayout'] = JHTML::_('select.genericList', $imgLayout, 'img_layout', 'class="inputbox imagePanel"', 'value', 'text', $items->img_layout );
$thumbnailShadow = array(
	'0' => array('value' => '0', 'text' => JText::_('THEME_GRID_THUMBNAIL_NO_SHADOW')),
	'1' => array('value' => '1', 'text' => JText::_('THEME_GRID_THUMBNAIL_LIGHT_SHADOW')),
	'2' => array('value' => '2', 'text' => JText::_('THEME_GRID_THUMBNAIL_BOLD_SHADOW'))
);
$lists['thumbnailShadow'] = JHTML::_('select.genericList', $thumbnailShadow, 'thumbnail_shadow', 'class="inputbox imagePanel"', 'value', 'text', $items->thumbnail_shadow );

$imageSource = array(
		'0' => array('value' => 'thumbnail', 'text' => JText::_('THEME_GRID_IMAGE_SOURCE_THUMBNAIL')),
		'1' => array('value' => 'original_image', 'text' => JText::_('THEME_GRID_IMAGE_SOURCE_ORIGINAL_IMAGE'))
);
$lists['imageSource'] 	= JHTML::_('select.genericList', $imageSource, 'image_source', 'class="inputbox"', 'value', 'text', ($items->image_source == '') ? 'thumbnail' : $items->image_source);
$lists['showCaption'] 	= JHTML::_('jsnselect.booleanlist', 'show_caption', 'class="inputbox"', $items->show_caption, 'JYES', 'JNO', false, 'yes', 'no');
$lists['captionShowDescription'] = JHTML::_('jsnselect.booleanlist', 'caption_show_description', 'class="inputbox"', $items->caption_show_description, 'JYES', 'JNO', false, 'yes', 'no');
$lists['showThumbs']	= JHTML::_('jsnselect.booleanlist', 'show_thumbs', 'class="inputbox"', $items->show_thumbs, 'JYES', 'JNO', false, 'yes', 'no');
$lists['showClose']		= JHTML::_('jsnselect.booleanlist', 'show_close', 'class="inputbox"', $items->show_close, 'JYES', 'JNO', false, 'yes', 'no');
$clickAction = array(
	'0' => array('value' => 'no_action', 'text' => JText::_('THEME_GRID_CLICK_ACTION_NO_ACTION')),
	'1' => array('value' => 'show_original_image', 'text' => JText::_('THEME_GRID_CLICK_ACTION_SHOW_ORIGINAL_IMAGE')),
	'2' => array('value' => 'open_image_link', 'text' => JText::_('THEME_GRID_CLICK_ACTION_OPEN_IMAGE_LINK'))
);
$lists['clickAction'] = JHTML::_('select.genericList', $clickAction, 'click_action', 'class="inputbox"', 'value', 'text', ($items->click_action == '')?'show_original_image':$items->click_action);

$openLinkIn = array(
	'0' => array('value' => 'current_browser', 'text' => JText::_('THEME_GRID_OPEN_LINK_IN_CURRENT_BROWSER')),
	'1' => array('value' => 'new_browser', 'text' => JText::_('THEME_GRID_OPEN_LINK_IN_NEW_BROWSER'))
);
$lists['openLinkIn'] = JHTML::_('select.genericList', $openLinkIn, 'open_link_in', 'class="inputbox"', 'value', 'text', ($items->open_link_in == '')?'current_browser':$items->open_link_in);


$containerHeightType = array(
		'0' => array('value' => 'inherited', 'text' => JText::_('THEME_GRID_CONTAINER_HEIGHT_TYPE_INHERITED')),
		'1' => array('value' => 'auto', 'text' => JText::_('THEME_GRID_CONTAINER_HEIGHT_TYPE_AUTO'))
);

$lists['containerHeightType'] = JHTML::_('select.genericList', $containerHeightType, 'container_height_type', 'class="inputbox imagePanel"', 'value', 'text', $items->container_height_type);

$lists['containerTransparentBackground']	= JHTML::_('jsnselect.booleanlist', 'container_transparent_background', 'class="inputbox imagePanel"', $items->container_transparent_background, 'JYES', 'JNO', false, 'yes', 'no');

$lists['autoPlay'] = JHTML::_('jsnselect.booleanlist', 'auto_play', 'class="inputbox effect-panel"', $items->auto_play, 'JYES', 'JNO', false, 'yes', 'no');

$navigationType = array(
		'0' => array('value' => 'show_all', 'text' => JText::_('THEME_GRID_NAVIGATION_TYPE_SHOW_ALL')),
		'1' => array('value' => 'load_more', 'text' => JText::_('THEME_GRID_NAVIGATION_TYPE_LOAD_MORE'))
);

$lists['navigationType'] = JHTML::_('select.genericList', $navigationType, 'navigation_type', 'class="inputbox imagePanel"', 'value', 'text', $items->navigation_type);