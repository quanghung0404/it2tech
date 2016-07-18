<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_showcasetheme.php 16304 2012-09-24 02:28:08Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );

class JSNISShowcaseTheme
{
	var $_db 			= null;
	var $_pluginType 	= 'jsnimageshow';
	var $_pluginPrefix 	= 'theme';
	var $_installFolder	= 'install';
	var $_installFile	= 'install.mysql.sql';

	function JSNISShowcaseTheme()
	{
		if ($this->_db == null)
		{
			$this->_db = JFactory::getDBO();
		}
	}

	public static function getInstance()
	{
		static $instanceShowcaseTheme;
		if ($instanceShowcaseTheme == null)
		{
			$instanceShowcaseTheme = new JSNISShowcaseTheme();
		}
		return $instanceShowcaseTheme;
	}

	function installPluginTableByThemeName($themeName)
	{
		jimport('joomla.filesystem.file');
		$sqlFile = JPATH_PLUGINS.DS.$this->_pluginType.DS.$themeName.DS.$this->_installFile;

		if (JFile::exists($sqlFile))
		{
			$objJNSUtils = JSNISFactory::getObj('classes.jsn_is_utils');
			$buffer = $objJNSUtils->readFileToString($sqlFile);

			if ($buffer === false) {
				return false;
			}

			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			if (count($queries) == 0)
			{
				JError::raiseWarning(100, $sqlFile . JText::_(' not exits'));
				return 0;
			}

			foreach ($queries as $query)
			{
				$query = trim($query);
				if ($query != '' && $query{0} != '#')
				{
					$this->_db->setQuery($query);

					if (!$this->_db->query())
					{
						JError::raiseWarning(100, 'JInstaller::install: '.JText::_('SQL Error')." ".$this->_db->stderr(true));
						return false;
					}
				}
			}

			return true;
		}
		else
		{
			JError::raiseWarning(100, $sqlFile . JText::_(' not exits'));
			return false;
		}
	}

	function checkThemeTableInstallByThemeName($themeName)
	{
		/*if (!empty($themeName))
		 {
			$themeName = str_replace($this->_pluginPrefix, '', trim(strtolower($themeName)));
			}

			$query 	= 'SHOW TABLES LIKE \''.$this->_db->getPrefix().'imageshow_theme_'.$themeName.'\'';
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();

			if (!empty($result))
			{
			return true;
			}

			return false;*/
		return true;
	}

	function checkThemePluginInstallByThemeName($themeName)
	{
		$query 	= 'SELECT extension_id as id, name, element
				   FROM #__extensions
				   WHERE folder = \''.$this->_pluginType.'\'
				   AND element='.$this->_db->quote($themeName);

		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		if (!empty($result))
		{
			return true;
		}
		return false;
	}

	function listThemes($enabled = true)
	{
		$published	= '';

		if ($enabled)
		{
			$published = 'AND enabled = 1 ';
		}

		$query 	= 'SELECT extension_id as id, name, element, manifest_cache
				   FROM #__extensions
				   WHERE element LIKE \'theme%\' '.$published.'
				   AND folder = \''.$this->_pluginType.'\'';

		$this->_db->setQuery($query);
		return (array) $this->_db->loadAssocList();
	}

	function enableThemeByThemeName($themeName, $themeType = 'jsnimageshow')
	{
		$query 	= 'UPDATE #__extensions
				   SET enabled = 1
				   WHERE folder ='.$this->_db->quote(trim(strtolower($themeType))).'
				   AND element = '.$this->_db->quote(trim(strtolower($themeName)));

		$this->_db->setQuery($query);

		if ($this->_db->query())
		{
			return true;
		}

		return false;
	}

	function getThemeByID($tblName, $themeID)
	{
		$query 	= 'SELECT * FROM #__imageshow_'.$tblName.' WHERE theme_id = '. (int) $themeID;
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

	function getShowcaseThemeByShowcaseID($showcaseID, $URL)
	{
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$themeProfile = $this->getThemeProfile($showcaseID);

		if (!$themeProfile) {
			return false;
		}

		if ($this->importModelByThemeName($themeProfile->theme_name) == false) {
			return false;
		}

		$modelShowcaseTheme = JModelLegacy::getInstance($themeProfile->theme_name);

		if ($modelShowcaseTheme == false) {
			return false;
		}

		$data = $modelShowcaseTheme->_prepareDataJSON($themeProfile->theme_id, $URL);

		return (object) $data;
	}

	function importTableByThemeName($themeName)
	{
		if (!empty($themeName))
		{
			$pathTableShowcaseTheme = JPATH_PLUGINS.DS.$this->_pluginType.DS.trim(strtolower($themeName)).DS.'tables';
			JTable::addIncludePath($pathTableShowcaseTheme);
			return true;
		}
		return false;
	}

	function importModelByThemeName($themeName)
	{
		if (!empty($themeName))
		{
			$pathModelShowcaseTheme = JPATH_PLUGINS.DS.$this->_pluginType.DS.trim(strtolower($themeName)).DS.'models';
			JModelLegacy::addIncludePath($pathModelShowcaseTheme);
			return true;
		}
		return false;
	}

	function getDefaultThemeByThemeName($themeName, $URL)
	{
		$showcaseID = 0; // default
		$this->importModelByThemeName($themeName);

		$modelShowcaseTheme = JModelLegacy::getInstance($themeName);

		if ($modelShowcaseTheme == false)
		{
			return false;
		}

		$data = $modelShowcaseTheme->_prepareDataJSON($showcaseID, $URL);

		return (object) $data;
	}

	function loadThemeByName($themeName, $themeID = 0)
	{
		$resultCheckThemeTable = $this->checkThemeTableInstallByThemeName($themeName);

		if (!$resultCheckThemeTable)
		{
			$resultInstallThemeTable = $this->installPluginTableByThemeName($themeName);
			if ($resultInstallThemeTable == false) return false;
		}

		JPluginHelper::importPlugin($this->_pluginType, $themeName);
		$dispatcher = JDispatcher::getInstance();
		$arg 		= array($themeName, $themeID);
		$plugins 	= $dispatcher->trigger('onLoadJSNShowcaseTheme', $arg);

		foreach ($plugins as $plugin)
		{
			if (gettype($plugin) == 'string')
			{
				echo $plugin;
			}
		}
	}

	function checkThemeExist($themeName)
	{
		$themes 	= $this->listThemes(false);
		$countTheme	= count($themes);
		if ($countTheme)
		{
			foreach ($themes as $theme)
			{
				if($theme['element'] == $themeName)
				{
					return true;
				}
			}
		}
		return false;
	}

	function enableAllTheme()
	{
		$query 	= 'UPDATE #__extensions
				   SET enabled = 1
				   WHERE folder ='.$this->_db->quote($this->_pluginType).' AND element LIKE \'theme%\'';
		$this->_db->setQuery($query);

		if ($this->_db->query())
		{
			return true;
		}

		return false;
	}

	function getThemeInfo($name = null)
	{
		if ($name)
		{
			$query 	= 'SELECT *
			   FROM #__extensions
			   WHERE folder = \''.$this->_pluginType.'\'
			   AND element='.$this->_db->quote($name);

			$this->_db->setQuery($query);
			$result = $this->_db->loadObject();
			if (count($result))
			{
				if ($result->manifest_cache)
				{
					return json_decode($result->manifest_cache);
				}
			}
		}
		return false;
	}

	function listThemesExist()
	{
		/*$config   = new JConfig();
		 $dbprefix = $config->dbprefix;

		 $query = "SHOW TABLES LIKE '".$dbprefix."imageshow_theme%'";

		 $this->_db->setQuery($query);

		 return $this->_db->loadColumn();*/

		return null;
	}

	function loadTheme($themeName)
	{
		$existedThemeTable = $this->checkThemeTableInstallByThemeName($themeName);
		if (!$existedThemeTable)
		{
			$installingResult = $this->installPluginTableByThemeName($themeName);
			if (!$installingResult) return false;
		}
		JPluginHelper::importPlugin($this->_pluginType, $themeName);
	}

	function triggerThemeEvent($eventName, $arg = array())
	{
		$dispatcher 	= JDispatcher::getInstance();
		$plugins 		= $dispatcher->trigger($eventName, $arg);
		return $plugins;
	}

	function getThemeProfile($showcaseID)
	{
		$query = 'SELECT * FROM #__imageshow_theme_profile WHERE showcase_id ='.(int)$showcaseID;
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

	function insertThemeProfile($themeID, $showcaseID, $themeName, $themeStyleName)
	{
		$query = 'DELETE FROM #__imageshow_theme_profile WHERE theme_id = '.(int)$themeID . ' AND showcase_id =' .(int)$showcaseID;
		$this->_db->setQuery($query);
		$this->_db->query();

		$query = 'INSERT INTO #__imageshow_theme_profile (theme_id, showcase_id, theme_name,theme_style_name) VALUES ('.(int)$themeID.', '.(int)$showcaseID.', '. $this->_db->quote((string)$themeName) .', '. $this->_db->quote((string)$themeStyleName) .')';
		$this->_db->setQuery($query);

		return ($this->_db->query()) ? true : false;
	}

	function deleteThemeProfileShowcaseID($showcaseID)
	{
		$query = 'DELETE FROM #__imageshow_theme_profile WHERE showcase_id =' .(int)$showcaseID;
		$this->_db->setQuery($query);

		return ($this->_db->query()) ? true : false;
	}

	function deleteThemeProfileByThemeName($themeName)
	{
		$query = 'DELETE FROM #__imageshow_theme_profile WHERE theme_name =' .$this->_db->quote((string)$themeName);
		$this->_db->setQuery($query);

		return ($this->_db->query()) ? true : false;
	}

	function getListThemeDefineToInstall()
	{
		$pluginDefine = json_decode(PluginInstalledList);
		return (is_array($pluginDefine->theme)) ? $pluginDefine->theme : array();
	}

	/**
	 * call display function in theme plugin and return the result
	 * @agr theme information
	 */
	function displayTheme($args)
	{
		if (isset($args->theme_name))
		{
			$this->loadTheme($args->theme_name);

			$results = $this->triggerThemeEvent('onDisplayJSNShowcaseTheme', array($args));

			foreach ($results as $result)
			{
				if ($result !== false) {
					return $result;
				}
			}
		}

		return false;
	}
}