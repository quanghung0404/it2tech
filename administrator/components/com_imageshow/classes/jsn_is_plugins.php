<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_plugins.php 11375 2012-02-24 10:19:14Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_database.php');
class JSNISPlugins extends JSNISDatabase
{
	public static function getInstance()
	{
		static $instanceJSNPlugins;

		if ($instanceJSNPlugins == null) {
			$instanceJSNPlugins = new JSNISPlugins();
		}

		return $instanceJSNPlugins;
	}

	function getXmlFile($row, $convertToObj = true)
	{
		$baseDir = JPATH_ROOT.DS.'plugins';
		$xmlfile = $baseDir.DS.$row->folder.DS.$row->element.DS.$row->element.".xml";
		$result = new stdClass();
		if(file_exists($xmlfile))
		{
			if($convertToObj)
			{
				if($data = JApplicationHelper::parseXMLInstallFile($xmlfile))
				{
					foreach($data as $key => $value)
					{
						$result->$key = $value;
					}
				}
				return $result;
			}
			else
			{
				return JFactory::getXML($xmlfile);
			}
		}
		return null;
	}

	function getJSNPluginList()
	{
		$query 	= 'SELECT *
				   FROM #__extensions
				   WHERE folder = \'jsnimageshow\'';

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getFullData($plugin)
	{
		$query = 'SELECT * FROM #__extensions WHERE LOWER(element) LIKE '.$this->_db->quote($plugin.'%').' AND folder = \'jsnimageshow\'';
		$this->_db->setQuery($query);
		$result =  $this->_db->loadObjectList();
		$this->translate($result);
		return $result;
	}

	function translate(&$items)
	{
		foreach($items as &$item)
		{
			if (strlen($item->manifest_cache))
			{
				$data = json_decode($item->manifest_cache);
				if ($data)
				{
					foreach($data as $key => $value)
					{
						if ($key == 'type')
						{
							continue;
						}
						$item->$key = $value;
					}
				}
			}
			$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;
			$item->client = $item->client_id ? JText::_('JADMINISTRATOR') : JText::_('JSITE');
			$item->name = JText::_($item->name);
			$item->description = JText::_(@$item->description);
		}
	}

	function getListPluginElement($pluginElement = 'theme')
	{
		$query 	= 'SELECT element
				   FROM #__extensions
				   WHERE folder = \'jsnimageshow\'
				   AND LOWER(element) LIKE '.$this->_db->quote($pluginElement.'%');

		$this->_db->setQuery($query);
		return $this->_db->loadColumn ();
	}

	/**
	 * get list jsn needful jsn plugin when install core
	 * @return array
	 */
	function getListJSNPluginNeedInstall()
	{
		$objJSNTheme 	  = JSNISFactory::getObj('classes.jsn_is_themes');
		$objJSNSource 	  = JSNISFactory::getObj('classes.jsn_is_source');
		$listImageSources = $objJSNSource->getListSourcesForInstall();
		$listThemes 	  = $objJSNTheme->getListThemeForInstall();

		return array('imageSources' => $listImageSources, 'themes' => $listThemes);
	}
}