<?php
/**
 * @version    $Id: jsn_is_data.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JSNISData Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

jimport('joomla.utilities.simplexml');
class JSNISData
{
	private $_xml 			= null;
	private $_db  			= null;
	private $_rootTag  		= '';

	/**
	 * Contructor
	 *
	 */

	public function JSNISData()
	{
		$this->_setRootTag('database');
		$this->_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><'.$this->_rootTag.'></'.$this->_rootTag.'>');
		$this->_setAttributteRootTag();
		$this->_db  = JFactory::getDBO();
	}

	/**
	 * Signleton pattern
	 *
	 * @return a instance
	 */

	public static function getInstance()
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (empty($instances['JSNISData']))
		{
			$instance	= new JSNISData;
			$instances['JSNISData'] = &$instance;
		}

		return $instances['JSNISData'];
	}

	/**
	 * Set name to root tag
	 *
	 * @param string $tag the name of root tag
	 * @return void
	 */

	private function _setRootTag($tag)
	{
		$this->_rootTag = $tag;
	}

	/**
	 * Set attributes to root tag
	 *
	 * @return void
	 */

	private function _setAttributteRootTag()
	{
		// Get Joomla config
		$config 			= JFactory::getConfig();
		$com  				= preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option'));
		$info 				= simplexml_load_file(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . "imageshow.xml");

		$database	   		= $config->get('db');

		$this->_xml->addAttribute('name', $database);
		$this->_xml->addAttribute('version', (string) $info->version);
		$this->_xml->addAttribute('joomla_version', JVERSION);

		$objJSNplugin = JSNISFactory::getObj('classes.jsn_is_plugins');
		$themes 	  = (array) $objJSNplugin->getListPluginElement('theme');
		$sources 	  = (array) $objJSNplugin->getListPluginElement('source');

		$this->_xml->addAttribute('sources', implode(',', $sources));
		$this->_xml->addAttribute('themes', implode(',', $themes));
	}

	/**
	 * Render data of specified table
	 *
	 * @param string  $tables The table name
	 * @param boolean $root   It is root tag
	 * @return boolean
	 */

	private function _renderTableData($tables, $root = true)
	{
		foreach ($tables as $tagName => $table)
		{
			$tableInfo 	 = $this->_db->getTableColumns($table, false);
			$countField  = count($tableInfo);
			$fields		 = array();
			if(count($countField))
			{
				foreach ($tableInfo as $value)
				{
					$fields [] = $value->Field;
				}

				$query  = 'SELECT ' . implode(',', $fields) . ' FROM ' .$table;
				$this->_db->setQuery($query);
				$data  = $this->_db->loadAssocList();

				if (count($data))
				{
					if ($root)
					{
						$root = $this->_xml->addChild($tagName . 's');
					}
					else
					{
						$root = $this->_xml;
					}

					foreach ($data as $value)
					{
						$subroot = $root->addChild($tagName);
						reset($fields);

						foreach ($fields as $fieldValue)
						{
							$subroot->addAttribute($fieldValue, $value[$fieldValue]);
						}
					}
				}
				else
				{
					return false;
				}

			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Render object XML Data
	 * @param boolean $showlist The flag to know whether back up showlist or not
	 * @param boolean $showcase The flag to know whether back up showcase or not
	 *
	 * @return resource
	 */

	public function executeBackup($showlist, $showcase)
	{
		if ($showlist)
		{
			$this->_renderShowListData();
			$this->_renderSourceProfileData();
			$this->_renderSourceData();
		}

		if ($showcase)
		{
			$this->_renderShowcaseData();
			$this->_renderThemeData();
			$this->_renderThemeProfileData();
		}

		//$this->_renderParameterData();
		return $this->_xml;
	}

	private function _renderTableThemeData($data)
	{
		$rootTheme		=  $this->_xml->addChild('themes');
		foreach ($data as $tagName => $tables)
		{
			if (count($tables['tables']))
			{
				$root 		= $rootTheme->addChild($tagName);
				$root->addAttribute('version', $tables['version']);
				$rootTables	= $root->addChild('tables');
				foreach ($tables['tables'] as $table)
				{
					$tableInfo 	 = $this->_db->getTableColumns($table, false);
					$countField  = count($tableInfo);
					$fields		 = array();
					if (count($countField))
					{
						foreach ($tableInfo as $value)
						{
							$fields [] = $value->Field;
						}

						$query  = 'SELECT ' . implode(',', $fields) . ' FROM ' .$table;
						$this->_db->setQuery($query);
						$data  = $this->_db->loadAssocList();

						if (count($data))
						{
							$rootTable 		= $rootTables->addChild('table');
							$rootTable->addAttribute('name', $table);
							$rootRecords 	= $rootTable->addChild('records');
							foreach ($data as $value)
							{
								$subroot = $rootRecords->addChild('record');
								reset($fields);

								foreach ($fields as $fieldValue)
								{
									$subroot->addAttribute($fieldValue, $value[$fieldValue]);
								}
							}
						}
					}
				}
			}
		}
	}

	private function _renderThemeData()
	{
		$objJSNPlugins			= JSNISFactory::getObj('classes.jsn_is_plugins');
		$objJSNISShowcaseTheme  = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$themes 				= $objJSNPlugins->getFullData('theme');
		$themeTables 			= array();
		if (count($themes))
		{
			foreach ($themes as $theme)
			{
				$objJSNISShowcaseTheme->loadTheme($theme->element);
				$result	= $objJSNISShowcaseTheme->triggerThemeEvent('list' . ucfirst($theme->element) . 'Table');
				if (count($result))
				{
					$themeTables [$theme->element]['tables'] = $result[0];
					$themeTables [$theme->element]['version'] = $theme->version;
				}
			}
		}

		if (count($themeTables))
		{
			$this->_renderTableThemeData($themeTables);
		}
	}

	private function _renderSourceData()
	{
		$objJSNSource     		= JSNISFactory::getObj('classes.jsn_is_source');
		$sources 				= $objJSNSource->_listSource;
		$sourceTables 			= array();
		$result					= array();
		if (count($sources))
		{
			foreach ($sources as $source)
			{
				if (isset($source->pluginInfo))
				{
					if ($source->type == 'external')
					{
						$objJSNSource->loadSource($source->pluginInfo->element);
						$result		  = $objJSNSource->triggerSourceEvent('list' . ucfirst($source->pluginInfo->element).'Tables');

						if (count($result))
						{
							$sourceTables [$source->pluginInfo->element]['tables']= $result[0];
							$manifestCache = json_decode($source->pluginInfo->manifest_cache);
							$sourceTables [$source->pluginInfo->element]['version']= $manifestCache->version;
						}
					}
				}
			}
		}

		if (count($sourceTables))
		{
			$this->_renderTableSourceData($sourceTables);
		}
	}

	private function _renderTableSourceData($data)
	{
		$rootSource		=  $this->_xml->addChild('sources');
		foreach ($data as $tagName => $tables)
		{
			if (count($tables['tables']))
			{
				$root 		= $rootSource->addChild($tagName);
				$root->addAttribute('version', $tables['version']);
				$rootTables	= $root->addChild('tables');
				foreach ($tables['tables'] as $table)
				{
					$tableInfo 	 = $this->_db->getTableColumns($table, false);
					$countField  = count($tableInfo);
					$fields		 = array();
					if(count($countField))
					{
						foreach ($tableInfo as $value)
						{
							$fields [] = $value->Field;
						}

						$query  = 'SELECT ' . implode(',', $fields) . ' FROM ' .$table;
						$this->_db->setQuery($query);
						$data  = $this->_db->loadAssocList();

						if(count($data))
						{
							$rootTable 		= $rootTables->addChild('table');
							$rootTable->addAttribute('name', $table);
							$rootRecords 	= $rootTable->addChild('records');
							foreach ($data as $value)
							{
								$subroot = $rootRecords->addChild('record');
								reset($fields);

								foreach ($fields as $fieldValue)
								{
									$subroot->addAttribute($fieldValue, $value[$fieldValue]);
								}
							}
						}
					}
				}
			}
		}
	}

	private function _renderShowcaseData()
	{
		$table = array('showcase'=> '#__imageshow_showcase');
		$this->_renderTableData($table);
	}

	private function _renderSourceProfileData()
	{
		$table = array('source_profile'=> '#__imageshow_source_profile');
		$this->_renderTableData($table);
	}

	private function _renderThemeProfileData()
	{
		$table = array('theme_profile'=> '#__imageshow_theme_profile');
		$this->_renderTableData($table);
	}

	private function _renderParameterData()
	{
		$table = array('parameter'=> '#__jsn_imageshow_config');
		$this->_renderTableData($table, false);
	}

	private function _renderShowListData()
	{
		$objJSNUtil  	  = JSNISFactory::getObj('classes.jsn_is_utils');
		$joomlaGroupLevel = $objJSNUtil->getJoomlaLevelName();

		$tableInfo 	 = $this->_db->getTableColumns('#__imageshow_showlist', false);
		$countField  = count($tableInfo);
		$fields		 = array();
		$showListID	 = 0;
		if (count($countField))
		{
			foreach ($tableInfo as $value)
			{
				$fields [] = $value->Field;
			}

			$query  = 'SELECT ' . implode(',', $fields) . ' FROM #__imageshow_showlist';

			$this->_db->setQuery($query);
			$data  = $this->_db->loadAssocList();

			if (count($data))
			{
				$root = $this->_xml->addChild('showlists');
				foreach ($data as $value)
				{
					$subroot = $root->addChild('showlist');
					reset($fields);
					foreach ($fields as $fieldValue)
					{
						if ($fieldValue == 'access')
						{
							$value[$fieldValue] = $objJSNUtil->convertJoomlaLevelFromIDToName($joomlaGroupLevel, $value[$fieldValue]);
						}

						$subroot->addAttribute($fieldValue, $value[$fieldValue]);
						if ($fieldValue = 'showlist_id')
						{
							$showListID = $value[$fieldValue];
						}
					}
					$this->_renderImageData($showListID, $subroot);
				}
			}
			else
			{
				return false;
			}

		}
		else
		{
			return false;
		}
	}

	private function _renderImageData($showlistID, $root)
	{
		$tableInfo 	 = $this->_db->getTableColumns('#__imageshow_images', false);
		$countField  = count($tableInfo);
		$fields		 = array();
		if (count($countField))
		{
			foreach ($tableInfo as $value)
			{
				$fields [] = $value->Field;
			}
			$query  = 'SELECT ' . implode(',', $fields) . ' FROM #__imageshow_images WHERE showlist_id = '. (int) $showlistID;
			$this->_db->setQuery($query);
			$data  = $this->_db->loadAssocList();

			if (count($data))
			{
				foreach ($data as $value)
				{
					$subroot = $root->addChild('image');
					reset($fields);
					foreach ($fields as $fieldValue)
					{
						$subroot->addAttribute($fieldValue, $value[$fieldValue]);
					}
				}
			}
			else
			{
				return false;
			}

		}
		else
		{
			return false;
		}

	}
}