<?php
/**
 * @version    $Id: helper.php 16978 2012-10-12 12:21:31Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
$objJSNShowcaseTheme->importModelByThemeName($this->_showcaseThemeName);
$modelShowcaseTheme = JModelLegacy::getInstance($this->_showcaseThemeName);
$cid				= JRequest::getVar('cid', array(0), '', 'array');
$showcaseID			= (int) $cid[0];
$skin				= $modelShowcaseTheme->getSkin($themeID, $showcaseID);
$items				= $modelShowcaseTheme->getTable($themeID);
$lists['skin'] = JHTML::_('select.genericList', $JSNThemeClassicSkin, 'theme_style_name', 'class="inputbox" '. '', 'value', 'text', $skin, 'theme_style_name');