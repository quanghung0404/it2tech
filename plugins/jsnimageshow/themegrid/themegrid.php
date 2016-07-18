<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: themegrid.php 16892 2012-10-11 04:07:40Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_factory.php');
class plgJSNImageshowThemeGrid extends JPlugin
{
	var $_showcaseThemeName = 'themegrid';
	var $_showcaseThemeType = 'jsnimageshow';
	var $_pathAssets 		= 'plugins/jsnimageshow/themegrid/assets/';
	var $_tableName			= 'theme_grid';

	function onLoadJSNShowcaseTheme($name, $themeID = 0)
	{
		if($name != $this->_showcaseThemeName){
			return false;
		}

		JPlugin::loadLanguage('plg_'.$this->_showcaseThemeType.'_'.$this->_showcaseThemeName);

		ob_start();

		JHTML::stylesheet($this->_pathAssets.'css/' . 'admin_style.css');
		JHTML::script($this->_pathAssets.'js/' . 'jsn_is_admin_conflict.js');
		JHTML::script($this->_pathAssets.'js/jquery/' . 'jquery.kinetic.js');
		JHTML::script($this->_pathAssets.'js/jquery/' . 'jquery.masonry.min.js');
		JHTML::script($this->_pathAssets.'js/' . 'jsn_is_gridtheme.js');

		include(dirname(__FILE__).DS.'helper'.DS.'helper.php');
		include(dirname(__FILE__).DS.'views'.DS.'default.php');

		return ob_get_clean();
	}

	function onExtensionBeforeUninstall($eid)
	{
		$query 	= 'DROP TABLE IF EXISTS `#__imageshow_'.$this->_tableName.'`';
		$db 	= JFactory::getDbo();
		$db->setQuery($query);
		$db->query();
	}

	function getLanguageJSNPlugin()
	{
		$language = array();
		$language['admin']['files'] = array('plg_'.$this->_showcaseThemeType.'_'.$this->_showcaseThemeName.'.ini');
		$language['admin']['path'] 	= array(dirname(__FILE__).DS.'languages');

		return $language;
	}

	function onDisplayJSNShowcaseTheme($args)
	{
		if ($args->theme_name != $this->_showcaseThemeName) {
			return false;
		}

		JHTML::stylesheet($this->_pathAssets.'css/' . 'style.css');
		JPlugin::loadLanguage('plg_'.$this->_showcaseThemeType.'_'.$this->_showcaseThemeName);
		$basePath 		 = JPATH_PLUGINS.DS.$this->_showcaseThemeType.DS.$this->_showcaseThemeName;
		$objThemeDisplay = JSNISFactory::getObj('classes.jsn_is_griddisplay', null ,null, $basePath);
		$result			 = $objThemeDisplay->display($args);
		return $result;
	}

	function listThemegridTable()
	{
		return array('#__imageshow_theme_grid');
	}
}