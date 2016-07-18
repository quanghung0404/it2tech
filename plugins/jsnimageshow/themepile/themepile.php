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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_factory.php';
class plgJSNImageshowThemePile extends JPlugin
{
	var $_showcaseThemeName = 'themepile';
	var $_showcaseThemeType = 'jsnimageshow';
	var $_pathAssets 		= 'plugins/jsnimageshow/themepile/assets/';
	var $_tableName			= 'theme_pile';

	public function onLoadJSNShowcaseTheme($name, $themeID = 0)
	{
		if ($name != $this->_showcaseThemeName)
		{
			return false;
		}

		JPlugin::loadLanguage('plg_' . $this->_showcaseThemeType . '_' . $this->_showcaseThemeName);

		ob_start();
        $document = JFactory::getDocument();
        $document->addStyleSheet(JUri::root() . $this->_pathAssets . 'css/photopile/photopile.css');
        $document->addStyleSheet(JUri::root() . $this->_pathAssets . 'css/admin/style.css');
        $document->addScript(JUri::root() . $this->_pathAssets . 'js/admin/jsn_is_admin_conflict.js');
        $document->addScript(JUri::root() . $this->_pathAssets . 'js/photopile/photopile.js');
        $document->addScript(JUri::root() . $this->_pathAssets . 'js/admin/jsn_js_piletheme.js');
		include_once dirname(__FILE__) . DS . 'helper' . DS . 'helper.php';
		include_once dirname(__FILE__) . DS . 'views' . DS . 'default.php';

		return ob_get_clean();
	}

	public function onExtensionBeforeUninstall($eid)
	{
		$query 	= 'DROP TABLE IF EXISTS `#__imageshow_' . $this->_tableName . '`';
		$db 	= JFactory::getDbo();
		$db->setQuery($query);
		$db->query();
	}

	public function getLanguageJSNPlugin()
	{
		$language = array();
		$language['admin']['files'] = array('plg_' . $this->_showcaseThemeType . '_' . $this->_showcaseThemeName . '.ini');
		$language['admin']['path'] 	= array(dirname(__FILE__).DS.'languages');

		return $language;
	}

	public function onDisplayJSNShowcaseTheme($args)
	{
		if ($args->theme_name != $this->_showcaseThemeName)
		{
			return false;
		}

		JPlugin::loadLanguage('plg_' . $this->_showcaseThemeType . '_' . $this->_showcaseThemeName);
		$basePath 		 = JPATH_PLUGINS.DS.$this->_showcaseThemeType.DS.$this->_showcaseThemeName;
		$objThemeDisplay = JSNISFactory::getObj('classes.jsn_is_piledisplay', null ,null, $basePath);
		$result			 = $objThemeDisplay->display($args);
		return $result;
	}

	public function listThemesliderTable()
	{
		return array('#__imageshow_theme_pile');
	}
}