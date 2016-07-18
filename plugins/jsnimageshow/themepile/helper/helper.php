<?php
/**
 * @version     $Id$
 * @package     JSN ImageShow
 * @subpackage  ThemePile
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
$objJSNShowcaseTheme->importTableByThemeName($this->_showcaseThemeName);
$objJSNShowcaseTheme->importModelByThemeName($this->_showcaseThemeName);
$modelShowcaseTheme = JModelLegacy::getInstance($this->_showcaseThemeName);
$items = $modelShowcaseTheme->getTable($themeID);

JSNISFactory::importFile('classes.jsn_is_htmlselect');

/**
 * ///////////////////////////////////////////////////////// Thumbnail ////////////////////////////////////////////////////////////////////////////
 */
$imageSource = array(
    '0' => array('value' => 'thumbnail', 'text' => JText::_('THEME_PILE_IMAGE_SOURCE_THUMBNAIL')),
    '1' => array('value' => 'original_image', 'text' => JText::_('THEME_PILE_IMAGE_SOURCE_ORIGINAL_IMAGE'))
);
$lists['imageSource'] 	= JHTML::_('select.genericList', $imageSource, 'image_source', 'class="inputbox"', 'value', 'text', ($items->image_source == '') ? 'thumbnail' : $items->image_source);
$imageClickAction = array(
    '0' => array('value' => 'no_action', 'text' => JText::_('THEME_PILE_IMAGE_CLICK_ACTION_NO_ACTION')),
    '1' => array('value' => 'show_original_image', 'text' => JText::_('THEME_PILE_IMAGE_CLICK_ACTION_SHOW_ORIGINAL_IMAGE')),
    '2' => array('value' => 'open_image_link', 'text' => JText::_('THEME_PILE_IMAGE_CLICK_ACTION_OPEN_IMAGE_LINK'))
);
$lists['imageClickAction'] = JHTML::_('select.genericList', $imageClickAction, 'image_click_action', 'class="inputbox"', 'value', 'text', ($items->image_click_action == '') ? 'no-action' : $items->image_click_action);

$openLinkIn = array(
    '0' => array('value' => 'current_browser', 'text' => JText::_('THEME_PILE_OPEN_LINK_IN_CURRENT_BROWSER')),
    '1' => array('value' => 'new_browser', 'text' => JText::_('THEME_PILE_OPEN_LINK_IN_NEW_BROWSER'))
);
$lists['openLinkIn'] = JHTML::_('select.genericList', $openLinkIn, 'open_link_in', 'class="inputbox"', 'value', 'text', ($items->open_link_in == '')?'current_browser':$items->open_link_in);
$lists['showTitle'] = JHTML::_('jsnselect.booleanlist', 'show_title', 'class="inputbox"', $items->show_title, 'JYES', 'JNO', 0, 1, 0);
$lists['showDescription'] = JHTML::_('jsnselect.booleanlist', 'show_description', 'class="inputbox"', $items->show_description, 'JYES', 'JNO', 0, 1, 0);
$lists['showShadow'] = JHTML::_('jsnselect.booleanlist', 'show_shadow', 'class="inputbox"', $items->show_shadow, 'JYES', 'JNO', 0, 1, 0);