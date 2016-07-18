<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: themegrid.php 13426 2012-06-21 07:25:29Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
JTable::addIncludePath(JPATH_ROOT.DS.'plugins'.DS.'jsnimageshow'.DS.'themegrid'.DS.'tables');
class ThemeGrid
{
	var $_pluginName 	= 'themegrid';
	var $_pluginType 	= 'jsnimageshow';
	var $_table = 'theme_grid';

	function &getInstance()
	{
		static $themeGrid;
		if ($themeGrid == null){
			$themeGrid = new ThemeGrid();
		}
		return $themeGrid;
	}

	function __construct()
	{
		$pathModelShowcaseTheme = JPATH_PLUGINS.DS.$this->_pluginType.DS.$this->_pluginName.DS.'models';
		$pathTableShowcaseTheme = JPATH_PLUGINS.DS.$this->_pluginType.DS.$this->_pluginName.DS.'tables';
		JModelLegacy::addIncludePath($pathModelShowcaseTheme);
		JTable::addIncludePath($pathTableShowcaseTheme);
	}

	function _prepareSaveData($data)
	{
		if(!empty($data))
		{
			return $data;
		}
		return false;
	}

	function getTable($themeID = 0)
	{
		$showcaseThemeTable = JTable::getInstance($this->_pluginName, 'Table');

		if(!$showcaseThemeTable->load((int) $themeID))
		{
			$showcaseThemeTable = JTable::getInstance($this->_pluginName, 'Table');// need to load default value when theme record has been deleted
			$showcaseThemeTable->load(0);
		}

		return $showcaseThemeTable;
	}

	function _prepareDataJSON($themeID, $URL)
	{
		return true;
	}
}