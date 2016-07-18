<?php
/**
 * @version    $Id: jsn_is_restore.php 17522 2012-10-26 09:12:25Z giangnd $
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

jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.file');
require_once(JPATH_COMPONENT.DS.'classes'.DS.'jsn_is_upgradedbutil.php');
class JSNISRestoreDBUtil
{
	var $_fileName 		  = 'db_schema_upgrade.xml';
	var $_mainfest		  = null;
	var $_previousVersion = null;
	var $_currentVersion  = null;
	var $_versionIndexes  = array();
	var $_currentTables   = array();
	var $_upgradeDBAction = null;
	var $_items			  = array();

	function JSNISRestoreDBUtil()
	{
		$this->setObjUpgradeDBAction();
		$this->setCurrentVersion();
		$this->parserXMLContent();
	}

	function setObjUpgradeDBAction()
	{
		$this->_upgradeDBAction = new JSNJSUpgradeDBAction();
	}

	function setPreviousVersion($value)
	{
		$this->_previousVersion	= $value;
	}

	function setCurrentVersion()
	{
		$objectReadxmlDetail    = new JSNISReadXmlDetails();
		$infoXmlDetail 		    = $objectReadxmlDetail->parserXMLDetails();
		$this->_currentVersion	= @$infoXmlDetail['version'];
	}

	function setVersionIndex($key, $value)
	{
		$this->_versionIndexes[$key] = $value;
	}

	function getEndVersionIndex()
	{
		return end($this->_versionIndexes);
	}

	function getStartVersionIndex()
	{
		$previousVersion = $this->_previousVersion;
		if (isset($this->_versionIndexes[$previousVersion]))
		{
			return $this->_versionIndexes[$previousVersion];
		}
		else
		{
			$previousVersion = (float) str_replace('.', '', $previousVersion);
			foreach ($this->_versionIndexes as $key => $value)
			{
				$tmpPreviousVersion = (float) str_replace('.', '', $key);
				if ($tmpPreviousVersion > $previousVersion)
				{
					return $value;
				}
			}
		}
	}

	function setCurrentTable($key, $value)
	{
		$this->_currentTables[$key] = $value;
	}

	function extractArray($data, $begin, $end)
	{
		$newData = array();
		for($i = $begin; $i <= $end; $i++)
		{
			$newData [] = $data[$i];
		}
		return $newData;
	}

	function extractVersionRange()
	{
		$items 		   = $this->_items;
		$startIndex    = $this->getStartVersionIndex();
		$endIndex      = $this->getEndVersionIndex();
		$items 		   = $this->extractArray($items, $startIndex, $endIndex);
		return $items;
	}

	function parserXMLContent()
	{
		$versionsArray			= array();
		$filePath 				= JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.$this->_fileName;
		$xml 				 	= JFactory::getXML($filePath);
		if (!$xml)
		{
			return false;
		}

		$document	= $xml;

		if(isset($document->version))
		{
			$versions = $document->version;

			if (count($versions))
			{
				for($i = 0, $count = count($versions); $i < $count; $i++)
				{
					$tablesArray		= array();
					$version    		= $versions[$i];
					$versionAttributes  = $version->attributes();
					$objVersion			= new stdClass();
					$objVersion->number = (string) $versionAttributes->number;
					$this->setVersionIndex((string) $versionAttributes->number, $i);

					if(isset($version->tables))
					{
						$tables 	 = $version->tables;
						$tableParent = $tables[0]->table;
						if(count($tableParent))
						{
							for ($j = 0, $count1 = count($tableParent); $j < $count1; $j++)
							{
								$fieldsArray 		= array();
								$table 				= $tableParent[$j];
								$tableAttributes 	= $table->attributes();

								$objTable 			= new stdClass();
								$objTable->id	    = (isset($tableAttributes->id)?(string) $tableAttributes->id:'');
								$objTable->status   = (isset($tableAttributes->status)?(string) $tableAttributes->status:'');
								$objTable->name	    = (isset($tableAttributes->name)?(string) $tableAttributes->name:'');

								$fields				= $table->field;
								if(count($fields))
								{
									for ($z = 0, $count2 = count($fields); $z < $count2; $z++)
									{
										$field						= $fields[$z];
										$fieldAttributes 			= $field->attributes();

										$objField					= new stdClass();
										$objField->id				= (string) $fieldAttributes->id;
										$objField->status			= (string) $fieldAttributes->status;
										$objField->name				= (isset($fieldAttributes->name)?(string) $fieldAttributes->name:'');
										$objField->type				= (isset($fieldAttributes->type)?(string) $fieldAttributes->type:'');
										$objField->primary_key		= (isset($fieldAttribute->primary_key)?(string) $fieldAttributes->primary_key:'');
										$objField->default_value	= (isset($fieldAttributes->default_value)?(string) $fieldAttributes->default_value:'');
										$objField->not_null			= (isset($fieldAttributes->not_null)?(string) $fieldAttributes->not_null:'yes');

										$fieldsArray []				= $objField;
									}
								}
								$objTable->fields	= $fieldsArray;
								$tablesArray []		= $objTable;
							}

						}

					}
					$objVersion->tables 	= $tablesArray;
					$versionsArray []		= $objVersion;
				}
			}
		}
		$this->_items = $versionsArray;
	}

	function searchField($tableID, $fieldID)
	{
		$result	= array();
		$items  = $this->_items;

		for ($i = 0, $count = count($items); $i < $count; $i++)
		{
			$data     = $items[$i];
			$tables   = $data->tables;
			$version  = $data->number;

			if (count($tables))
			{
				for ($j = 0, $count1 = count($tables); $j < $count1; $j++)
				{
					$table = $tables[$j];
					if ($table->id == $tableID)
					{
						$fields = $table->fields;
						if (count($fields))
						{
							for ($z = 0, $count2 = count($fields); $z < $count2; $z++)
							{
								$obj = new stdClass();
								$field = $fields[$z];

								if($field->id == $fieldID && JString::strtolower($field->status) != 'removed')
								{
									$obj->index   = $i;
									$obj->id      = $field->id;
									$obj->version = $version;
									$obj->status  = $field->status;
									$obj->name    = $field->name;
									$obj->type    = $field->type;
									$obj->not_null= $field->not_null;
									$result []    = $obj;
									break;
								}
							}
						}
						break;
					}
				}
			}
		}
		return $result;
	}

	function getFieldChanged($items)
	{
		$tableResult   = array();
		$fieldIDs	   = array();
		for ($i = 1, $count = count($items); $i < $count; $i++)
		{
			$data     	   = $items[$i];
			$version       = $data->number;
			$tables        = $data->tables;

			if (count($tables))
			{
				for ($j = 0, $count1 = count($tables); $j < $count1; $j++)
				{

					$table         = $tables[$j];
					$tableName     = $table->name;
					$tableID       = $table->id;
					$tableStatus   = $table->status;

					if (JString::strtolower($tableStatus) == 'changed')
					{
						$fields      = $table->fields;

						if (count($fields))
						{
							for ($z = 0, $count2 = count($fields); $z < $count2; $z++)
							{
								$field   	 = $fields[$z];
								$fieldID 	 = $field->id;
								$fieldStatus = JString::strtolower($field->status);

								if ($fieldStatus == 'changed' && !in_array($fieldID, $fieldIDs))
								{
									$tableResult [$tableName][] = $this->searchField($tableID, $fieldID);
									$fieldIDs []    			= $fieldID;
								}
							}
						}
					}
				}
				$fieldIDs = array_unique($fieldIDs);
			}
		}

		return $tableResult;
	}

	function getDataChanged()
	{
		$result	= array();
		if($this->_previousVersion == $this->_currentVersion) return $result;
		$items   			= $this->extractVersionRange();
		$preDataChange 		= $this->getFieldChanged($items);
		$dataChange			= $this->processDataChanged($preDataChange);
		return $dataChange;
	}

	function processDataChanged($data)
	{
		$tableResult = array();
		if(count($data))
		{
			foreach ($data as $key => $tables)
			{
				$tableName = $key;
				if(count($tables))
				{
					foreach ($tables as $fields)
					{
						$tmpArray = array();
						if(count($fields))
						{
							for ($i = 0, $count = count($fields); $i < $count; $i++)
							{
								$field = $fields[$i];
								if($field->version == $this->_previousVersion)
								{
									$originalFieldName = $field->name;
									break;
								}
								elseif ($this->_upgradeDBAction->isExistTableColumn($tableName, $field->name))
								{
									$originalFieldName = $field->name;
									break;
								}

							}

							$lastElement = end($fields);
							$tmpArray [$originalFieldName] = array('type' => $lastElement->type, 'change' => $lastElement->name, 'id' => $lastElement->id, 'not_null' => $lastElement->not_null);
							$tableResult [$tableName][]   	   = $tmpArray;
						}
					}
				}
			}
		}

		return $tableResult;
	}
}

class JSNISRestore
{
	var $_error 	= false;
	var $_msgError 	= '';
	var $path;
	var $file;
	var $compress;
	var $_document;
	var $_manifestInfo;
	var $_db;
	var $_fileRestore;
	var $_fieldChanged;
	var $_arrayFileDelte = array();
	var $_extractFile = true;
	var $_requiredSourcesNeedInstall = array();
	var $_requiredThemesNeedInstall = array();
	var $_requiredInstallJSON = '{}';
	var $_requiredInstallDataToJSON = array();
	var $_deleteFilesAfterRestore = true;
	var $_canAutoDownload;

	function JSNISRestore($config = array('path'=> '', 'file'=>'', 'compress'=>''))
	{
		if (count($config) > 0)
		{
			$this->path 	= $config['path'];
			$this->file 	= $config['file'];
			$this->compress = $config['compress'];
		}

		$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
		$this->_canAutoDownload = $objJSNUtils->checkEnvironmentDownload();


		$this->_db	= JFactory::getDBO();
		$this->_setManifestInfo();

	}

	function &getInstance()
	{
		static $instanceRestore;
		if ($instanceRestore == null)
		{
			$instanceRestore = new JSNISRestore();
		}
		return $instanceRestore;
	}

	function _loadRestoreFile()
	{
		$fileStoreName	 	 = "jsn_".JString::strtolower(@$this->_manifestInfo['realName']).'_backup_db.xml';
		$filepath	 		 = JPATH_ROOT.DS.'tmp'.DS.$fileStoreName;
		$this->_fileRestore  = $filepath;
		$xml 				 = JFactory::getXML($filepath);
		if (!$xml)
		{
			$fileStoreName	 	 = "jsn_".JString::strtolower(@$this->_manifestInfo['realName']).'_pro_backup_db.xml';
			$filepath	 		 = JPATH_ROOT.DS.'tmp'.DS.$fileStoreName;
			$this->_fileRestore  = $filepath;
			$xml 				 = JFactory::getXML($filepath);
			if ($xml)
			{
				return $xml;
			}
			return false;
		}

		return $xml;
	}

	function _setDocument()
	{

		$loadFile = $this->_loadRestoreFile();

		if (!$loadFile) {
			$this->_document = null;
		} else {
			$this->_document = $loadFile;
		}
	}

	function _setManifestInfo()
	{
		$objJSNXML 				= JSNISFactory::getObj('classes.jsn_is_readxmldetails');
		$this->_manifestInfo 	= $objJSNXML->parserXMLDetails();
	}

	function importFile()
	{
		$this->_setDocument();
		if (is_null($this->_document)) {
			$this->_error = true;
			return false;
		}

		$attributeDocument  	= $this->_document->attributes();
		$tmpStrVersion			= (string) $attributeDocument->version;
		$versionRestore 		= (float) str_replace('.', '', $tmpStrVersion);
		$versionCheck			= (float) str_replace('.', '', '3.0.0');

		if($versionRestore < $versionCheck)
		{
			$this->_error 	 = true;
			$this->_msgError = JText::_('MAINTENANCE_BACKUP_ERROR_IMAGESHOW_VERSION_RESTORE');
			return false;
		}
		$this->_fieldChanged = $this->_getFieldChanged();

		if($versionRestore < (float) str_replace('.', '', '4.0.0'))
		{
			$requiredSourcesNeedInstall = $this->_getSourceFromVersion3();
			$requiredThemesNeedInstall 	= $this->_getThemeFromVersion3();

			if (count($requiredSourcesNeedInstall) || count($requiredThemesNeedInstall)) {
				$this->_error = true;
				$this->_requiredSourcesNeedInstall = $requiredSourcesNeedInstall;
				$this->_requiredThemesNeedInstall  = $requiredThemesNeedInstall;
				$this->_msgError = JText::_('MAINTENANCE_RESTORE_MISSING_REQUIRED_SOURCE_AND_THEMES');
				return false;
			}

			if (isset($this->_document->showlists))
			{
				$firstQueries [] = array('TRUNCATE #__imageshow_showlist','ALTER TABLE #__imageshow_showlist AUTO_INCREMENT = 1');
				$firstQueries [] = array('TRUNCATE #__imageshow_source_profile', 'ALTER TABLE #__imageshow_source_profile AUTO_INCREMENT = 1');
				$firstQueries [] = $this->_migrateFlickrSourceFromVersion3();
				$firstQueries [] = $this->_migratePicasaSourceFromVersion3();
				$this->executeQuery($firstQueries);
			}
			$lastQueries []	= $this->_restoreShowlistFromVersion3();
			$lastQueries []	= $this->_restoreShowlistFlickrFromVersion3();
			$lastQueries []	= $this->_restoreShowlistPicasaFromVersion3();

			$lastQueries []	= $this->_restoreShowcaseFromVersion3();
			$lastQueries []	= $this->_restoreShowcaseThemeFromVersion3();
			$lastQueries [] = $this->_migrateShowcaseToThemeVersion3();
			$lastQueries [] = $this->_migrateParameterToThemeClassicVersion3();
			///$lastQueries [] = $this->_restoreParameter();
			$this->executeQuery($lastQueries);
		}
		else
		{
			$requiredSourcesAndThemes = $this->_getSourceFromVersion4();
			if (count($requiredSourcesAndThemes)) {
				$this->_error = true;
				$this->_msgError = JText::_('RESTORE_MISSING_REQUIRED_SOURCE_AND_THEMES');
				return false;
			}

			$queries []	= $this->_restoreShowcase();
			$queries []	= $this->_restoreShowcaseTheme();
			//$queries [] = $this->_restoreParameter();
			$queries []	= $this->_restoreShowlist();
			$queries []	= $this->_restoreSourceProfile();
			$queries []	= $this->_restoreThemeProfile();
			$queries []	= $this->_restoreSource();
			$this->executeQuery($queries);
		}
		$this->_upgradeShowcaseThemeData();
		$this->_arrayFileDelte[] = $this->_fileRestore;

		return true;
	}

	function executeQuery($datas)
	{
		if (count($datas))
		{
			foreach ($datas as $data)
			{
				if (count($data))
				{
					foreach ($data as $value)
					{
						$this->_db->setQuery($value);
						$this->_db->query();
					}
				}
			}
		}
	}

	function upload()
	{
		global $mainframe;
		$file = $this->file;

		$err  = null;
		if (isset($file['name']))
		{
			$filepath = JPath::clean($this->path.DS.$file['name']);
			if (!JFile::upload($file['tmp_name'], $filepath))
			{
				header('HTTP/1.0 400 Bad Request');
				die('Error. Unable to upload file');
				return false;
			}
			else
			{
				return $filepath;
			}
		}
	}

	function restore($config)
	{
		$this->JSNISRestore($config);
		$this->_getFileUpload = $this->upload();
		$result = $this->extractArchive();

		if ($result)
		{
			$restore = $this->importFile();

			if ($restore)
			{
				return $restore;
			}
			else
			{
				$this->_error = true;
				$this->_msgError = JText::_('RESTORE_RESTORE_FAILURE');
				return false;
			}
		}
		else
		{
			$this->_error 	 = true;
			$this->_msgError = JText::_('RESTORE_EXTRACT_PACKAGE_FAILURE');
		}

		if ($this->_deleteFilesAfterRestore) {
			JFile::delete($this->_arrayFileDelte);
		}

		return false;
	}

	/**
	 * restore data from file in tmp directory
	 */
	function restoreFromFileHasUploaded($config)
	{
		$this->JSNISRestore($config);
		$this->_getFileUpload = $config['file_upload'];
		$result = $this->extractArchive();

		if ($result)
		{
			$restore = $this->importFile();

			if ($restore)
			{
				return $restore;
			}
			else
			{
				$this->_error = true;
				$this->_msgError = JText::_('RESTORE_RESTORE_FAILURE');
				return false;
			}
		}
		else
		{
			$this->_error 	 = true;
			$this->_msgError = JText::_('RESTORE_EXTRACT_PACKAGE_FAILURE');
		}

		if ($this->_deleteFilesAfterRestore) {
			JFile::delete($this->_arrayFileDelte);
		}

		return false;
	}

	function restoreBackupForMigrate($config, $deleteFilesAfterRestore = false)
	{
		$this->JSNISRestore($config);
		$this->_deleteFilesAfterRestore = $deleteFilesAfterRestore;

		if ($config['file_upload'] == '' || !JFile::exists($config['file_upload'])) {
			return false;
		}

		$this->_getFileUpload = $config['file_upload'];

		$result = $this->extractArchive();

		if ($result)
		{
			$result = $this->importFile();

			if ($this->_deleteFilesAfterRestore) {
				JFile::delete($this->_arrayFileDelte);
			}

			return $result;
		}

		return false;
	}

	function extractArchive()
	{
		$result = false;

		switch ($this->compress)
		{
			case 1:
			case 2:
				$extractdir  = JPath::clean(dirname($this->_getFileUpload));
				$archivename = JPath::clean($this->_getFileUpload);
				$result 	 = JArchive::extract($archivename, $extractdir);

				if (!$result) {
					$this->_extractFile = false;
				}

				$this->_arrayFileDelte[] = $archivename;
				break;
			case 0:
				break;
		}

		return $result;
	}

	function _restoreShowcase()
	{
		$queries 		= array();

		if(!isset($this->_document->showcases)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showcase']) && count($this->_fieldChanged['#__imageshow_showcase']))
		{
			foreach ($this->_fieldChanged['#__imageshow_showcase'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$showcaseRoot = $this->_document->showcases;
		if ($showcaseRoot != null)
		{
			$showcase = @$showcaseRoot[0]->showcase;
			if (count($showcase))
			{
				for ($i = 0, $counti=count($showcase); $i < $counti; $i++)
				{
					$rows 			= $showcase[$i];
					$attributes [] 	= $rows->attributes();
				}
				if (count($attributes))
				{
					$queries [] = 'TRUNCATE #__imageshow_showcase';
					$queries [] = 'ALTER TABLE #__imageshow_showcase AUTO_INCREMENT = 1';
					foreach ($attributes as $attribute)
					{
						$fields 		= '';
						$fieldsValue 	= '';
						foreach ($attribute as $key => $value)
						{
							if (count($fieldChanged) && isset($fieldChanged[$key]))
							{
								$key = $fieldChanged[$key];
							}
							if ($this->_checkTableColumExist('#__imageshow_showcase', $key))
							{
								$fields 	 .= $key.',';
								$fieldsValue .= $this->_db->quote($value).',';
							}
						}
						$queries [] = 'INSERT #__imageshow_showcase ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
					}
				}
			}
		}

		return $queries;
	}

	function _restoreParameter()
	{
		$queries 		= array();
		if(!isset($this->_document->parameter)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__jsn_imageshow_config']) && count($this->_fieldChanged['#__jsn_imageshow_config']))
		{
			foreach ($this->_fieldChanged['#__jsn_imageshow_config'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}
		$parameter = $this->_document->parameter;
		if ($parameter != null)
		{
			$attributes [] = $parameter[0]->attributes();
			if(count($attributes))
			{
				$queries [] = 'TRUNCATE #__jsn_imageshow_config';
				//$queries [] = 'ALTER TABLE #__jsn_imageshow_config AUTO_INCREMENT = 1';
				foreach ($attributes as $attribute)
				{
					$fields			= '';
					$fieldsValue 	= '';

					foreach($attribute as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}

						if ($this->_checkTableColumExist('#__jsn_imageshow_config', $key))
						{
							$fieldsValue 	.= $this->_db->quote((string) $value).',';
							$fields 		.= $key.',';
						}
					}

					$queries [] = 'INSERT #__jsn_imageshow_config ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
				}
			}
		}
		return $queries;
	}

	function _migrateParameterToThemeClassicVersion3()
	{
		if ($this->_checkTableExist('#__imageshow_theme_classic_parameters') == false) return;
		$queries 		= array();

		if(!isset($this->_document->parameter)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__jsn_imageshow_config']) && count($this->_fieldChanged['#__jsn_imageshow_config']))
		{
			foreach ($this->_fieldChanged['#__jsn_imageshow_config'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}
		$parameter = $this->_document->parameter;
		if ($parameter != null)
		{
			$attributes [] = $parameter[0]->attributes();
			if(count($attributes))
			{
				foreach ($attributes as $attribute)
				{
					$fields			= '';
					$fieldsValue 	= '';

					foreach($attribute as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}

						if ($this->_checkTableColumExist('#__imageshow_theme_classic_parameters', $key))
						{
							$fieldsValue 	.= $this->_db->quote((string) $value).',';
							$fields 		.= $key.',';
						}
					}

					$queries [] = 'INSERT #__imageshow_theme_classic_parameters ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
				}
			}
		}
		return $queries;
	}

	function _restoreShowlist()
	{
		$objJSNUtil  	  = JSNISFactory::getObj('classes.jsn_is_utils');
		$joomlaGroupLevel = $objJSNUtil->getJoomlaLevelName();

		$queries 	 	= array();

		if(!isset($this->_document->showlists)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showlist']) && count($this->_fieldChanged['#__imageshow_showlist']))
		{
			foreach ($this->_fieldChanged['#__imageshow_showlist'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$showlists = $this->_document->showlists;
		$showlist  = @$showlists[0]->showlist;
		if (count($showlist))
		{
			for ($i = 0, $counti=count($showlist); $i < $counti; $i ++ )
			{
				$rows 		= $showlist[$i];
				$attributes [] = $rows->attributes();
				$images =& $rows->image;
				if(count($images) > 0)
				{
					for ($y = 0, $county=count($images); $y < $county; $y++)
					{
						$image 				= $images[$y];
						$attributesImage [] = $image->attributes();
					}
				}
			}

			if(count($attributes))
			{
				$queries [] = 'TRUNCATE #__imageshow_showlist';
				$queries [] = 'ALTER TABLE #__imageshow_showlist AUTO_INCREMENT = 1';
				foreach ($attributes as $attribute)
				{
					$fields 		= '';
					$fieldsValue 	= '';
					foreach($attribute as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}

						if ($key == 'access')
						{
							$value = $objJSNUtil->convertJoomlaLevelFromNameToID($joomlaGroupLevel, $value);
						}
						if ($this->_checkTableColumExist('#__imageshow_showlist', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote((string) $value).',';
						}
					}

					$queries [] = 'INSERT #__imageshow_showlist ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
				}
			}

			if(count($attributesImage))
			{
				$fieldChanged 	= array();
				if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_images']) && count($this->_fieldChanged['#__imageshow_images']))
				{
					foreach ($this->_fieldChanged['#__imageshow_images'] as $items)
					{
						foreach ($items as $key => $item)
						{
							$fieldChanged [$key] = $item['change'];
						}
					}
				}

				$queries [] = 'TRUNCATE #__imageshow_images';
				$queries [] = 'ALTER TABLE #__imageshow_images AUTO_INCREMENT = 1';
				foreach ($attributesImage as $attributeImage)
				{
					$fields 		= '';
					$fieldsValue 	= '';
					foreach($attributeImage as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}
						if ($this->_checkTableColumExist('#__imageshow_images', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote((string) $value).',';
						}
					}

					$queries [] = 'INSERT #__imageshow_images ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
				}
			}
		}

		return $queries;
	}

	function _getShowcaseTheme()
	{
		$objJSNISShowcaseTheme  = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$themes 				= $objJSNISShowcaseTheme->listThemes(false);
		$results		 		= array();
		if (count($themes))
		{
			foreach ($themes as $theme)
			{
				$results [] = $theme['element'];
			}
		}

		return $results;
	}

	function _restoreShowcaseThemeFromVersion3()
	{
		require_once(JPATH_COMPONENT.DS.'classes'.DS.'jsn_is_upgradethemedb.php');
		$objJSNPlugins 	= JSNISFactory::getObj('classes.jsn_is_plugins');

		$queries		= array();

		if(!isset($this->_document->themes)) return $queries;

		$objTmpTheme    = new stdClass();
		$objTmpTheme->version = '1.0.0';

		$objTheme    	  = new stdClass();
		$objTheme->folder = 'jsnimageshow';

		$themesRoot	   =& $this->_document->themes;
		$themes 		= $this->_getShowcaseTheme();

		if (count($themes))
		{
			foreach ($themes as $theme)
			{
				$fieldChanged = array();
				$objTheme->element = $theme;
				$themeName	     = JString::str_ireplace('theme', 'theme_', $theme);
				$checkTableTheme = $this->_checkTableExist('#__imageshow_'.$themeName.'_flash');
				if(!$checkTableTheme) continue;

				$manifest =  $objJSNPlugins->getXmlFile($objTheme, false);
				$objUpgradeThemeDB 				= new JSNISUpgradeThemeDB($manifest, $objTmpTheme);
				$items   						= $objUpgradeThemeDB->extractVersionRange();
				$preDataChange 					= $objUpgradeThemeDB->getFieldChanged($items);
				$dataChange						= $objUpgradeThemeDB->processDataChanged($preDataChange);

				if (isset($dataChange['#__imageshow_'.$themeName]))
				{
					foreach ($dataChange['#__imageshow_'.$themeName] as $items)
					{
						foreach ($items as $key => $item)
						{
							$fieldChanged [$key] = $item['change'];
						}
					}
				}

				$queries [] = 'TRUNCATE #__imageshow_'.$themeName.'_flash';
				$queries [] = 'ALTER TABLE #__imageshow_'.$themeName.'_flash'.' AUTO_INCREMENT = 1';

				$attributes = array();

				if (isset($themesRoot[0]->{$theme.'s'}))
				{
					$root = $themesRoot[0]->{$theme.'s'};
					$subRoot = @$root[0]->{$theme};

					if (count($subRoot))
					{
						for ($i = 0; $i < count($subRoot); $i++)
						{
							$rows 				= $subRoot[$i];
							$attributes [] 		= $rows->attributes();
						}
						if (count($attributes))
						{
							foreach ($attributes as $attribute)
							{
								$fields			= '';
								$fieldsValue 	= '';
								foreach ($attribute as $key => $value)
								{
									if (count($fieldChanged) && isset($fieldChanged[$key]))
									{
										$key = $fieldChanged[$key];
									}
									if ($this->_checkTableColumExist('#__imageshow_'.$themeName.'_flash', $key))
									{
										$fields 	 .= $key.',';
										$fieldsValue .= $this->_db->quote((string) $value).',';
									}
								}
								$queries [] = 'INSERT #__imageshow_'.$themeName.'_flash'.' ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
							}
						}
					}
				}

			}
		}
		return $queries;
	}

	function _getTableFields($table)
	{
		$fields			= array();
		$tableInfo 		= $this->_db->getTableFields($table, true);
		$countFields 	= count($tableInfo[$table]);
		if($countFields > 0)
		{
			foreach ($tableInfo[$table] as $key =>$value)
			{
				$fields [] = $key;
			}
		}
		return $fields;
	}

	function _migrateShowcaseToVersion3()
	{
		$index				= 1;
		$fieldsComparer	    = $this->_getTableFields('#__imageshow_showcase');
		$queries 			= array();
		$checkShowCase 		=& $this->_document->getElementByPath('showcases');
		$checkTableTheme 	= $this->_checkTableExist('#__imageshow_theme_classic');
		if ($checkShowCase != false)
		{
			$fieldChanged 	= array();
			if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showcase']) && count($this->_fieldChanged['#__imageshow_showcase']))
			{
				foreach ($this->_fieldChanged['#__imageshow_showcase'] as $items)
				{
					foreach ($items as $key => $item)
					{
						$fieldChanged [$key] = $item['change'];
					}
				}
			}

			$showcaseRoot =& $this->_document->showcases;
			if ($showcaseRoot != null)
			{
				$showcase = @$showcaseRoot[0]->showcase;
				if (count($showcase))
				{
					for ($i = 0, $counti=count($showcase); $i < $counti; $i++)
					{
						$rows 			= $showcase[$i];
						$attributes [] 	= $rows->attributes();
					}
					if (count($attributes))
					{
						$queries [] = 'TRUNCATE #__imageshow_showcase';
						$queries [] = 'ALTER TABLE #__imageshow_showcase AUTO_INCREMENT = 1';
						$queries [] = 'TRUNCATE #__imageshow_theme_profile';
						$queries [] = 'ALTER TABLE #__imageshow_theme_profile AUTO_INCREMENT = 1';
						if ($checkTableTheme)
						{
							$queries [] = 'TRUNCATE #__imageshow_theme_classic';
							$queries [] = 'ALTER TABLE #__imageshow_theme_classic AUTO_INCREMENT = 1';
						}
						foreach ($attributes as $attribute)
						{
							$fields 			= 'theme_id,theme_name,';
							$fieldsValue 		= "'".$index."','themeclassic',";
							$fieldsShowcase		= '';
							$fieldsShowcaseValue= '';
							$fieldsTheme		= '';
							$fieldsThemeValue	= '';
							foreach ($attribute as $key => $value)
							{

								if (in_array($key, $fieldsComparer))
								{
									$fieldsShowcase 	 .= $key.',';
									$fieldsShowcaseValue .= $this->_db->quote($value).',';
								}
								else
								{
									if (count($fieldChanged) && isset($fieldChanged[$key]))
									{
										$key = $fieldChanged[$key];
									}

									if ($this->_checkTableColumExist('#__imageshow_theme_classic', $key))
									{
										$fieldsTheme	 	.= $key.',';
										$fieldsThemeValue 	.= $this->_db->quote($value).',';
									}
								}
							}

							$queries [] = 'INSERT #__imageshow_showcase ('.substr($fieldsShowcase, 0, -1).') VALUES ('.substr($fieldsShowcaseValue, 0, -1).')';
							$queries [] = 'INSERT #__imageshow_theme_profile ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';

							if ($checkTableTheme)
							{
								$queries [] = 'INSERT #__imageshow_theme_classic ('.substr($fieldsTheme, 0, -1).') VALUES ('.substr($fieldsThemeValue, 0, -1).')';
							}

							$index++;
						}
					}
				}
			}
		}
		return $queries;
	}

	function _checkTableExist($table)
	{
		$objUpgradeDBAction = new JSNJSUpgradeDBAction();
		return $objUpgradeDBAction->isExistTable($table);
	}

	function _checkTableColumExist($table, $column)
	{
		$objUpgradeDBAction = new JSNJSUpgradeDBAction();
		return $objUpgradeDBAction->isExistTableColumn($table, $column);
	}

	function _getFieldChanged()
	{
		$objJSNISRestoreDBUtil = new JSNISRestoreDBUtil();
		$attributeDocument  	= $this->_document->attributes();
		$versionRestore 		= (string) $attributeDocument->version;
		$objJSNISRestoreDBUtil->setPreviousVersion($versionRestore);
		return $objJSNISRestoreDBUtil->getDataChanged();
	}

	function _upgradeShowcaseThemeData()
	{
		require_once(JPATH_COMPONENT.DS.'classes'.DS.'jsn_is_upgradethemedb.php');
		$objJSNPlugins 		= JSNISFactory::getObj('classes.jsn_is_plugins');
		$objPluginModel		= JModelLegacy::getInstance('plugins', 'imageshowmodel');

		$existedPlugins     		= $objPluginModel->getFullData();
		$countExistedPlugins		= count($existedPlugins);

		$objTmpTheme    = new stdClass();
		$objTmpTheme->version = '1.0.0';

		if ($countExistedPlugins)
		{
			for ($i = 0; $i < $countExistedPlugins; $i++)
			{
				$item = $existedPlugins[$i];
				$data =  $objJSNPlugins->getXmlFile($item, false);
				if(!is_null($data))
				{
					$objUpgradeThemeDB 				= new JSNISUpgradeThemeDB($data, $objTmpTheme);
					$items   						= $objUpgradeThemeDB->extractVersionRange();
					$preDataChange 					= $objUpgradeThemeDB->getFieldChanged($items);
					$dataChange						= $objUpgradeThemeDB->processDataChanged($preDataChange);
					$queriesFieldDataChange 		= array($objUpgradeThemeDB->buildQueriesFieldDataChange($dataChange, true));
					$this->executeQuery($queriesFieldDataChange);
				}
			}
		}
	}

	function _restoreShowcaseTheme()
	{
		require_once(JPATH_COMPONENT.DS.'classes'.DS.'jsn_is_upgradethemedb.php');

		$objJSNPlugins 	= JSNISFactory::getObj('classes.jsn_is_plugins');
		$queries		= array();

		if(!isset($this->_document->themes)) return $queries;

		$objTmpTheme    = new stdClass();
		$objTmpTheme->version = '1.0.0';

		$objTheme    	  = new stdClass();
		$objTheme->folder = 'jsnimageshow';

		$themesRoot	    = $this->_document->themes;
		$themes 		= $this->_getShowcaseTheme();

		if (count($themes))
		{
			foreach ($themes as $theme)
			{
				$fieldChanged = array();
				$themeName	= JString::str_ireplace('theme', 'theme_', $theme);
				//$checkTableTheme = $this->_checkTableExist('#__imageshow_'.$themeName);

				//if(!$checkTableTheme) continue;

				$objTheme->element = $theme;
				$manifest =  $objJSNPlugins->getXmlFile($objTheme, false);
				$objUpgradeThemeDB 				= new JSNISUpgradeThemeDB($manifest, $objTmpTheme);
				$items   						= $objUpgradeThemeDB->extractVersionRange();
				$preDataChange 					= $objUpgradeThemeDB->getFieldChanged($items);
				$dataChange						= $objUpgradeThemeDB->processDataChanged($preDataChange);

				if (isset($dataChange['#__imageshow_'.$themeName]))
				{
					foreach ($dataChange['#__imageshow_'.$themeName] as $items)
					{
						foreach ($items as $key => $item)
						{
							$fieldChanged [$key] = $item['change'];
						}
					}
				}

				if (isset($themesRoot[0]->{$theme}))
				{
					$root = $themesRoot[0]->{$theme};

					$tables 	= @$root[0]->tables;
					$subtables  = @$tables[0]->table;
						
					if (count($subtables))
					{
						for ($i = 0, $counti = count($subtables); $i < $counti; $i++)
						{
							$attributes		 	= array();
							$tableAttributes 	= $subtables[$i]->attributes();
							$tableName  	 	= (string) $tableAttributes->name;
							$records 		 	= $subtables[$i]->records;
							$subrecords		 	= $records[0]->record;
							$queries[] 			= 'TRUNCATE '.$tableName;
							$queries[]  		= 'ALTER TABLE '.$tableName.' AUTO_INCREMENT = 1';
							for ($k = 0, $countk = count($subrecords); $k < $countk; $k++)
							{
								$rows 				= $subrecords[$k];
								$attributes [] 		= $rows->attributes();
							}

							if (count($attributes))
							{
								foreach ($attributes as $attribute)
								{
									$fields			= '';
									$fieldsValue 	= '';

									foreach ($attribute as $key => $value)
									{
										if (count($fieldChanged) && isset($fieldChanged[$key]))
										{
											$key = $fieldChanged[$key];
										}

										if ($this->_checkTableColumExist($tableName, $key))
										{
											$fields 	 .= $key.',';
											$fieldsValue .= $this->_db->quote((string) $value).',';
										}
									}

									$queries [] = 'INSERT '.$tableName.' ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
								}
							}
						}
					}
				}
			}
		}

		return $queries;
	}

	/*function _migrateSourceFromVersion3()
	 {
		$queries 				= array();
		$checkConfiguration 	=& $this->_document->getElementByPath('configurations');

		if(!$checkConfiguration) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_configuration']) && count($this->_fieldChanged['#__imageshow_configuration']))
		{
		foreach ($this->_fieldChanged['#__imageshow_configuration'] as $items)
		{
		foreach ($items as $key => $item)
		{
		$fieldChanged [$key] = $item['change'];
		}
		}
		}
		$configurationRoot =& $this->_document->configurations;
		if ($configurationRoot != null)
		{
		$configuration = @$configurationRoot[0]->configuration;

		if (count($configuration))
		{
		for ($i = 0; $i < count($configuration); $i++)
		{
		$rows 				= $configuration[$i];
		$attributes [] 		= $rows->attributes();
		}
		if (count($attributes))
		{
		$queries [] = 'TRUNCATE #__imageshow_configuration';
		$queries [] = 'ALTER TABLE #__imageshow_configuration AUTO_INCREMENT = 1';
		foreach ($attributes as $attribute)
		{
		$fields			= '';
		$fieldsValue 	= '';
		foreach ($attribute as $key => $value)
		{
		if (count($fieldChanged) && isset($fieldChanged[$key]))
		{
		$key = $fieldChanged[$key];
		}

		if ($this->_checkTableColumExist('#__imageshow_configuration', $key))
		{
		$fields 	 .= $key.',';
		$fieldsValue .= $this->_db->quote($value).',';
		}
		}
		$queries [] = 'INSERT #__imageshow_configuration ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
		}
		}
		}
		}

		return $queries;
		}
		*/
	function _migratePicasaSourceFromVersion3()
	{
		if ($this->_checkTableExist('#__imageshow_external_source_picasa') == false) return;
		$queries 				= array();

		if(!isset($this->_document->configurations)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_configuration']) && count($this->_fieldChanged['#__imageshow_configuration']))
		{
			foreach ($this->_fieldChanged['#__imageshow_configuration'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}
		$configurationRoot = $this->_document->configurations;
		if ($configurationRoot != null)
		{
			$configuration = @$configurationRoot[0]->configuration;

			if (count($configuration))
			{
				for ($i = 0, $counti=count($configuration); $i < $counti; $i++)
				{
					$rows 				= $configuration[$i];
					$attributes [] 		= $rows->attributes();
				}
				if (count($attributes))
				{
					$queries [] = 'TRUNCATE #__imageshow_external_source_picasa';
					$queries [] = 'ALTER TABLE #__imageshow_external_source_picasa AUTO_INCREMENT = 1';
					foreach ($attributes as $attribute)
					{
						$fields			= '';
						$fieldsValue 	= '';
						$picasa			= true;
						//$externalSourceID 	= '';
						foreach ($attribute as $key => $value)
						{
							$value = (string) $value;
							if (count($fieldChanged) && isset($fieldChanged[$key]))
							{
								$key = $fieldChanged[$key];
							}
							if($key == 'configuration_id')
							{
								$key = 'external_source_id';
							}
							/*if ($key == 'external_source_id')
							 {
								$externalSourceID = $value;
								}*/

							if($key == 'picasa_user_name')
							{
								$key = 'picasa_username';
							}

							if($key == 'configuration_title')
							{
								$key = 'external_source_profile_title';
							}

							if($key == 'source_type')
							{
								if ($value != 3)
								{
									$picasa = false;
								}
							}
							if ($this->_checkTableColumExist('#__imageshow_external_source_picasa', $key))
							{
								$fields 	 .= $key.',';
								$fieldsValue .= $this->_db->quote((string) $value).',';
							}
						}
						if($picasa)
						{
							$queries [] = 'INSERT #__imageshow_external_source_picasa ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
							//$queries [] = 'INSERT #__imageshow_source_profile (external_source_id) VALUES ('.$externalSourceID.')';
						}
					}
				}
			}
		}
		return $queries;
	}

	function _migrateFlickrSourceFromVersion3()
	{
		if ($this->_checkTableExist('#__imageshow_external_source_flickr') == false) return;

		$queries 				= array();

		if(!isset($this->_document->configurations)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_configuration']) && count($this->_fieldChanged['#__imageshow_configuration']))
		{
			foreach ($this->_fieldChanged['#__imageshow_configuration'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}
		$configurationRoot = $this->_document->configurations;
		if ($configurationRoot != null)
		{
			$configuration = @$configurationRoot[0]->configuration;

			if (count($configuration))
			{
				for ($i = 0, $counti=count($configuration); $i < $counti; $i++)
				{
					$rows 				= $configuration[$i];
					$attributes [] 		= $rows->attributes();
				}
				if (count($attributes))
				{
					$queries [] = 'TRUNCATE #__imageshow_external_source_flickr';
					$queries [] = 'ALTER TABLE #__imageshow_external_source_flickr AUTO_INCREMENT = 1';
					foreach ($attributes as $attribute)
					{
						$fields				= '';
						$fieldsValue 		= '';
						$flickr				= true;
						//$externalSourceID 	= '';
						foreach ($attribute as $key => $value)
						{
							$value = (string) $value;
							if (count($fieldChanged) && isset($fieldChanged[$key]))
							{
								$key = $fieldChanged[$key];
							}
							if($key == 'configuration_id')
							{
								$key = 'external_source_id';
							}

							/*if ($key == 'external_source_id')
							 {
								$externalSourceID = $value;
								}*/
							if($key == 'configuration_title')
							{
								$key = 'external_source_profile_title';
							}

							if($key == 'source_type')
							{
								if ($value != 2)
								{
									$flickr = false;
								}
							}
							if ($this->_checkTableColumExist('#__imageshow_external_source_flickr', $key))
							{
								$fields 	 .= $key.',';
								$fieldsValue .= $this->_db->quote($value).',';
							}
						}

						if($flickr)
						{
							$queries [] = 'INSERT #__imageshow_external_source_flickr ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
							//$queries [] = 'INSERT #__imageshow_source_profile (external_source_id) VALUES ('.$externalSourceID.')';
						}
					}
				}
			}
		}
		return $queries;
	}

	function _restoreShowlistPicasaFromVersion3()
	{
		if ($this->_checkTableExist('#__imageshow_external_source_picasa') == false) return;
		$objJSNUtil  	  	= JSNISFactory::getObj('classes.jsn_is_utils');
		$joomlaGroupLevel	= $objJSNUtil->getJoomlaLevelName();
		$sourceID			= (int) $this->getMaxIDSourceProfiles();
		$queries 	 		= array();
		$showlistPicasaIds 	= array();
		if(!isset($this->_document->showlists)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showlist']) && count($this->_fieldChanged['#__imageshow_showlist']))
		{
			foreach ($this->_fieldChanged['#__imageshow_showlist'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$showlists = $this->_document->showlists;
		$showlist  = @$showlists[0]->showlist;
		$attributesImage =  array();
		if (count($showlist))
		{
			for ($i = 0, $counti=count($showlist); $i < $counti; $i ++ )
			{
				$rows 		= $showlist[$i];
				$attributes [] = $rows->attributes();
				$images =& $rows->image;
				if(count($images) > 0)
				{
					for ($y = 0 ; $y < count($images); $y++)
					{
						$image 				= $images[$y];
						$attributesImage [] = $image->attributes();
					}
				}
			}

			if(count($attributes))
			{
				foreach ($attributes as $attribute)
				{
					$fields 		= '';
					$fieldsValue 	= '';
					$picasa			= true;
					$imageShowlistID = 0;
					$externalSourceID 	= '';
					foreach($attribute as $key => $value)
					{
						$value = (string) $value;
						if (count($fieldChanged) && isset($fieldChanged[$key])) {
							$key = $fieldChanged[$key];
						}

						if ($key == 'showlist_id') {
							$imageShowlistID = $value;
						}

						if ($key == 'showlist_source')
						{
							if ($value != 3) {
								$picasa = false;
							}
						}

						if($key == 'configuration_id')
						{
							$key 	= 'image_source_profile_id';
							$externalSourceID = $value;
							$value  = $sourceID;
							$externalSourceProfileID = $sourceID;
						}

						if ($key == 'access') {
							$value = $objJSNUtil->convertJoomlaLevelFromNameToID($joomlaGroupLevel, $value);
						}

						if ($this->_checkTableColumExist('#__imageshow_showlist', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote($value).',';
						}
					}
					if($picasa)
					{
						$showlistPicasaIds[] = $imageShowlistID;
						$queries [] = 'INSERT #__imageshow_showlist ('.substr($fields, 0, -1).', image_source_name, image_source_type) VALUES ('.substr($fieldsValue, 0, -1).', '.$this->_db->quote('picasa').', '.$this->_db->quote('external').')';
						$queries [] = 'INSERT #__imageshow_source_profile (external_source_profile_id, external_source_id) VALUES ('.$externalSourceProfileID.','.$externalSourceID.')';
					}
					$sourceID++;
				}
			}

			if(count($attributesImage))
			{
				$fieldChanged 	= array();
				if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_images']) && count($this->_fieldChanged['#__imageshow_images']))
				{
					foreach ($this->_fieldChanged['#__imageshow_images'] as $items)
					{
						foreach ($items as $key => $item)
						{
							$fieldChanged [$key] = $item['change'];
						}
					}
				}

				foreach ($attributesImage as $attributeImage)
				{
					$imageBig = '';
					$imageID  = '';
					$imagePicasa = false;
					foreach($attributeImage as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}
						if ($this->_checkTableColumExist('#__imageshow_images', $key))
						{
							if ($key == 'showlist_id')
							{
								if (in_array($value, $showlistPicasaIds)) {
									$imagePicasa = true;
								}
							}

							if ($key == 'image_big')
							{
								$patt = '/\/s(\d)*\//';

								if (!preg_match($patt, $value))
								{
									$imageParts = explode('/', $value);
									$count = count($imageParts);

									if ($count > 0) {
										$imageParts[] = $imageParts[$count - 1];
										$imageParts[$count - 1] = 's1024';
										$imageBig = implode('/', $imageParts);
									} else {
										$imageBig = $value;
									}
								}
							}

							if ($key == 'image_id') {
								$imageID = $value;
							}
						}
					}

					if ($imageID && $imageBig && $imagePicasa){
						$queries [] = 'UPDATE #__imageshow_images SET image_big = '.$this->_db->quote($imageBig).' WHERE image_id ='.(int)$imageID;
					}
				}
			}
		}

		return $queries;
	}

	function _restoreShowlistFlickrFromVersion3()
	{
		if ($this->_checkTableExist('#__imageshow_external_source_flickr') == false) return;
		$objJSNUtil  	  	= JSNISFactory::getObj('classes.jsn_is_utils');
		$joomlaGroupLevel	= $objJSNUtil->getJoomlaLevelName();
		//$sourceID			= $this->getDataSourceProfiles();
		$sourceID			= (int) $this->getMaxIDSourceProfiles();
		$queries 	 		= array();

		if(!isset($this->_document->showlists)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showlist']) && count($this->_fieldChanged['#__imageshow_showlist']))
		{
			foreach ($this->_fieldChanged['#__imageshow_showlist'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$showlists = $this->_document->showlists;
		$showlist  = @$showlists[0]->showlist;
		if (count($showlist))
		{
			for ($i = 0, $counti=count($showlist); $i < $counti; $i ++ )
			{
				$rows 		= $showlist[$i];
				$attributes [] = $rows->attributes();

			}

			if(count($attributes))
			{
				foreach ($attributes as $attribute)
				{
					$fields 		= '';
					$fieldsValue 	= '';
					$flickr			= true;
					$externalSourceID 	= '';
					foreach($attribute as $key => $value)
					{
						$value = (string) $value;
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}
						if ($key == 'showlist_source')
						{
							if ($value != 2)
							{
								$flickr = false;
							}
						}
						if($key == 'configuration_id')
						{
							$key 	= 'image_source_profile_id';
							$externalSourceID = $value;
							$value  = $sourceID;
							$externalSourceProfileID = $sourceID;
						}
						if ($key == 'access')
						{
							$value = $objJSNUtil->convertJoomlaLevelFromNameToID($joomlaGroupLevel, $value);
						}
						if ($this->_checkTableColumExist('#__imageshow_showlist', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote($value).',';
						}
					}
					if($flickr)
					{
						$queries [] = 'INSERT #__imageshow_showlist ('.substr($fields, 0, -1).', image_source_name, image_source_type) VALUES ('.substr($fieldsValue, 0, -1).', '.$this->_db->quote('flickr').', '.$this->_db->quote('external').')';
						$queries [] = 'INSERT #__imageshow_source_profile (external_source_profile_id, external_source_id) VALUES ('.$externalSourceProfileID.','.$externalSourceID.')';
					}
					$sourceID++;
				}
			}
		}

		return $queries;
	}

	function _restoreShowlistFromVersion3()
	{
		$objJSNUtil  	  = JSNISFactory::getObj('classes.jsn_is_utils');
		$joomlaGroupLevel = $objJSNUtil->getJoomlaLevelName();

		$queries 	 	= array();

		if(!isset($this->_document->showlists)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showlist']) && count($this->_fieldChanged['#__imageshow_showlist']))
		{
			foreach ($this->_fieldChanged['#__imageshow_showlist'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$showlists = $this->_document->showlists;
		$showlist  = @$showlists[0]->showlist;
		if (count($showlist))
		{
			for ($i = 0, $counti=count($showlist); $i < $counti; $i ++ )
			{
				$rows 		= $showlist[$i];
				$attributes [] = $rows->attributes();
				$images =& $rows->image;
				if(count($images) > 0)
				{
					for ($y = 0 ; $y < count($images); $y++)
					{
						$image 				= $images[$y];
						$attributesImage [] = $image->attributes();
					}
				}
			}

			if(count($attributes))
			{
				foreach ($attributes as $attribute)
				{
					$fields 			= 'image_source_name, image_source_profile_id, image_source_type,';
					$fieldsValue 		= '';
					$tmpfieldsValue		= '';
					$tmpInternalValue 	= '';
					$internal			= false;
					foreach($attribute as $key => $value)
					{
						$value = (string) $value;
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}

						if ($key == 'access')
						{
							$value = $objJSNUtil->convertJoomlaLevelFromNameToID($joomlaGroupLevel, $value);
						}
						if ($key == 'showlist_source')
						{


							if ($value == '1' || $value == '4' || $value == '5')
							{
								$internal = true;
							}
						}
						if($key == 'showlist_source')
						{
							switch ($value)
							{
								case '1': // folder
									$tmpfieldsValue 	.= $this->_db->quote('folder').',';
									$tmpInternalValue   .= $this->_db->quote('folder').',';
									break;

								case '4': // phoca
									$tmpfieldsValue 	.= $this->_db->quote('phoca').',';
									$tmpInternalValue   .= $this->_db->quote('internal').',';
									break;

								case '5': // joomga
									$tmpfieldsValue 	.= $this->_db->quote('joomgallery').',';
									$tmpInternalValue   .= $this->_db->quote('internal').',';
									break;
							}
						}

						if ($this->_checkTableColumExist('#__imageshow_showlist', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote((string) $value).',';
						}
					}

					if($internal)
					{
						$queries [] = 'INSERT #__imageshow_showlist ('.substr($fields, 0, -1).') VALUES ('.$tmpfieldsValue.'0,'.$tmpInternalValue.substr($fieldsValue, 0, -1).')';
					}
				}
			}

			if(count($attributesImage))
			{
				$fieldChanged 	= array();
				if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_images']) && count($this->_fieldChanged['#__imageshow_images']))
				{
					foreach ($this->_fieldChanged['#__imageshow_images'] as $items)
					{
						foreach ($items as $key => $item)
						{
							$fieldChanged [$key] = $item['change'];
						}
					}
				}

				$queries [] = 'TRUNCATE #__imageshow_images';
				$queries [] = 'ALTER TABLE #__imageshow_images AUTO_INCREMENT = 1';
				foreach ($attributesImage as $attributeImage)
				{
					$fields 		= '';
					$fieldsValue 	= '';
					foreach($attributeImage as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}
						if ($this->_checkTableColumExist('#__imageshow_images', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote($value).',';
						}
					}

					$queries [] = 'INSERT #__imageshow_images ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
				}
			}
		}

		return $queries;
	}

	function _restoreShowcaseFromVersion3()
	{
		$queries 		= array();

		if(!isset($this->_document->showcases)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showcase']) && count($this->_fieldChanged['#__imageshow_showcase']))
		{
			foreach ($this->_fieldChanged['#__imageshow_showcase'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$showcaseRoot =& $this->_document->showcases;
		if ($showcaseRoot != null)
		{
			$showcase = @$showcaseRoot[0]->showcase;
			if (count($showcase))
			{
				for ($i = 0, $counti = count($showcase); $i < $counti; $i++)
				{
					$rows 			= $showcase[$i];
					$attributes [] 	= $rows->attributes();
				}
				if (count($attributes))
				{
					$queries [] = 'TRUNCATE #__imageshow_showcase';
					$queries [] = 'ALTER TABLE #__imageshow_showcase AUTO_INCREMENT = 1';
					$queries [] = 'TRUNCATE #__imageshow_theme_profile';
					$queries [] = 'ALTER TABLE #__imageshow_theme_profile AUTO_INCREMENT = 1';
					foreach ($attributes as $attribute)
					{
						$fields 					= '';
						$fieldsValue 				= '';
						$themeProfileFields 		= '';
						$themeProfileFieldsValue 	= '';
						foreach ($attribute as $key => $value)
						{
							$value = (string) $value;
							if (count($fieldChanged) && isset($fieldChanged[$key]))
							{
								$key = $fieldChanged[$key];
							}
							if ($this->_checkTableColumExist('#__imageshow_showcase', $key))
							{
								$fields 	 .= $key.',';
								$fieldsValue .= $this->_db->quote($value).',';
							}
							if ($this->_checkTableColumExist('#__imageshow_theme_profile', $key))
							{
								$themeProfileFields 	 .= $key.',';
								$themeProfileFieldsValue .= $this->_db->quote($value).',';
							}
						}
						$themeProfileFields 	 .= 'theme_style_name,';
						$themeProfileFieldsValue 	 .= '"flash",';
						$queries [] = 'INSERT #__imageshow_showcase ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
						$queries [] = 'INSERT #__imageshow_theme_profile ('.substr($themeProfileFields, 0, -1).') VALUES ('.substr($themeProfileFieldsValue, 0, -1).')';
					}
				}
			}
		}

		return $queries;
	}

	/**
	 * move field in showcase table to themeclassic table from 3.x.x version to 4.x.x
	 * general_round_corner_radius, general_border_color, background_color, general_border_stroke
	 */
	function _migrateShowcaseToThemeVersion3()
	{
		$queries 		= array();

		if(!isset($this->_document->showcases)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_showcase']) && count($this->_fieldChanged['#__imageshow_showcase']))
		{
			foreach ($this->_fieldChanged['#__imageshow_showcase'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$showcaseRoot = $this->_document->showcases;
		if ($showcaseRoot != null)
		{
			$showcase = @$showcaseRoot[0]->showcase;
			if (count($showcase))
			{
				for ($i = 0, $counti = count($showcase); $i < $counti; $i++)
				{
					$rows 			= $showcase[$i];
					$attributes [] 	= $rows->attributes();
				}
				if (count($attributes))
				{
					foreach ($attributes as $attribute)
					{
						$fields  = array();
						$themeID = 0;

						foreach ($attribute as $key => $value)
						{
							$value = (string) $value;
							if (count($fieldChanged) && isset($fieldChanged[$key]))
							{
								$key = $fieldChanged[$key];
							}

							if ($key == 'background_color') {
								$key = 'general_background_color';
							}

							if (($key == ('general_round_corner_radius' || 'general_border_color'
							|| 'general_background_color' || 'general_border_stroke') )
							&& $this->_checkTableColumExist('#__imageshow_theme_classic_flash', $key))
							{
								$fields[] = $key.'='.$this->_db->quote($value);
							}

							if ($key == 'theme_id' && $this->_checkTableColumExist('#__imageshow_theme_classic_flash', $key)) {
								$themeID = $value;
							}
						}

						$queries [] = 'UPDATE #__imageshow_theme_classic_flash SET '.implode(',', $fields).' WHERE theme_id = '. $themeID ;
					}
				}
			}
		}

		return $queries;
	}

	function getDataSourceProfiles()
	{
		$array = array();
		$query = 'SELECT * FROM #__imageshow_source_profile';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if(count($result))
		{
			for ($i = 0, $counti = count($result); $i < $counti; $i++)
			{
				$row = $result[$i];
				$array [$row->external_source_id] = $row->external_source_profile_id;
			}
		}
		return $array;
	}

	function _restoreSourceProfile()
	{
		$objJSNUtil  	  = JSNISFactory::getObj('classes.jsn_is_utils');
		$joomlaGroupLevel = $objJSNUtil->getJoomlaLevelName();

		$queries 	 	= array();

		if(!isset($this->_document->source_profiles)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_source_profile']) && count($this->_fieldChanged['#__imageshow_source_profile']))
		{
			foreach ($this->_fieldChanged['#__imageshow_source_profile'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$profiles = $this->_document->source_profiles;
		$profile  = @$profiles[0]->source_profile;
		if (count($profile))
		{
			for ($i = 0, $counti = count($profile); $i < $counti; $i ++ )
			{
				$rows 		= $profile[$i];
				$attributes [] = $rows->attributes();
			}

			if(count($attributes))
			{
				$queries [] = 'TRUNCATE #__imageshow_source_profile';
				$queries [] = 'ALTER TABLE #__imageshow_source_profile AUTO_INCREMENT = 1';
				foreach ($attributes as $attribute)
				{
					$fields 		= '';
					$fieldsValue 	= '';
					foreach($attribute as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}

						if ($key == 'access')
						{
							$value = $objJSNUtil->convertJoomlaLevelFromNameToID($joomlaGroupLevel, $value);
						}
						if ($this->_checkTableColumExist('#__imageshow_source_profile', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote((string) $value).',';
						}
					}

					$queries [] = 'INSERT #__imageshow_source_profile ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
				}
			}
		}
		return $queries;
	}

	function _restoreThemeProfile()
	{
		$objJSNUtil  	  = JSNISFactory::getObj('classes.jsn_is_utils');
		$joomlaGroupLevel = $objJSNUtil->getJoomlaLevelName();

		$queries 	 		= array();

		if(!isset($this->_document->theme_profiles)) return $queries;

		$fieldChanged 	= array();
		if (count($this->_fieldChanged) && isset($this->_fieldChanged['#__imageshow_theme_profile']) && count($this->_fieldChanged['#__imageshow_theme_profile']))
		{
			foreach ($this->_fieldChanged['#__imageshow_theme_profile'] as $items)
			{
				foreach ($items as $key => $item)
				{
					$fieldChanged [$key] = $item['change'];
				}
			}
		}

		$profiles = $this->_document->theme_profiles;
		$profile  = @$profiles[0]->theme_profile;

		if (count($profile))
		{
			for ($i = 0, $counti = count($profile); $i < $counti; $i ++ )
			{
				$rows 		= $profile[$i];
				$attributes [] = $rows->attributes();
			}

			if(count($attributes))
			{
				$queries [] = 'TRUNCATE #__imageshow_theme_profile';
				$queries [] = 'ALTER TABLE #__imageshow_theme_profile AUTO_INCREMENT = 1';
				foreach ($attributes as $attribute)
				{
					$fields 		= '';
					$fieldsValue 	= '';
					foreach($attribute as $key => $value)
					{
						if (count($fieldChanged) && isset($fieldChanged[$key]))
						{
							$key = $fieldChanged[$key];
						}

						if ($key == 'access')
						{
							$value = $objJSNUtil->convertJoomlaLevelFromNameToID($joomlaGroupLevel, $value);
						}
						if ($this->_checkTableColumExist('#__imageshow_theme_profile', $key))
						{
							$fields 		.= $key.',';
							$fieldsValue 	.= $this->_db->quote((string) $value).',';
						}
					}

					$queries [] = 'INSERT #__imageshow_theme_profile ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
				}
			}
		}

		return $queries;
	}

	function _restoreSource()
	{
		$queries		= array();

		if(!isset($this->_document->sources)) return $queries;

		$sourcesRoot			=& $this->_document->sources;
		//$model 					= JModelLegacy::getInstance('sources', 'imageshowmodel');
		$objJSNSource     		= JSNISFactory::getObj('classes.jsn_is_source');
		//$sources 				= $model->getFullData();
		$sources 				= $objJSNSource->_listSource;
		if (count($sources))
		{
			foreach ($sources as $source)
			{
				if(isset($source->pluginInfo))
				{
					if ($source->type == 'external')
					{
						$sourceName	     = JString::str_ireplace('source', 'external_source_', $source->pluginInfo->element);
						$checkTableSource = $this->_checkTableExist('#__imageshow_'.$sourceName);
						if(!$checkTableSource) continue;
						$check 	= $sourcesRoot[0]->{$source->pluginInfo->element};

						if ($check)
						{
							$root = $sourcesRoot[0]->{$source->pluginInfo->element};
							if ($root != null)
							{
								$tables 	= @$root[0]->tables;
								$subtables  = @$tables[0]->table;
								if (count($subtables))
								{
									for ($i = 0, $counti = count($subtables); $i < $counti; $i++)
									{
										$attributes		 	= array();
										$tableAttributes 	= $subtables[$i]->attributes();
										$tableName  	 	= (string) $tableAttributes->name;
										$records 		 	= $subtables[$i]->records;
										$subrecords		 	= $records[0]->record;
										$queries[] 			= 'TRUNCATE '.$tableName;
										$queries[]  		= 'ALTER TABLE '.$tableName.' AUTO_INCREMENT = 1';
										for ($k = 0, $countk = count($subrecords); $k < $countk; $k++)
										{
											$rows 				= $subrecords[$k];
											$attributes [] 		= $rows->attributes();
										}
										if (count($attributes))
										{
											foreach ($attributes as $attribute)
											{
												$fields			= '';
												$fieldsValue 	= '';
												foreach ($attribute as $key => $value)
												{

													if ($this->_checkTableColumExist($tableName, $key))
													{
														$fields 	 .= $key.',';
														$fieldsValue .= $this->_db->quote((string) $value).',';
													}
												}
												$queries [] = 'INSERT '.$tableName.' ('.substr($fields, 0, -1).') VALUES ('.substr($fieldsValue, 0, -1).')';
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $queries;
	}

	function getMaxIDSourceProfiles()
	{
		$array = array();
		$query = 'SELECT max(external_source_profile_id) as max_id FROM #__imageshow_source_profile';
		$this->_db->setQuery($query);
		$result = $this->_db->loadRow();
		if($result[0] != null)
		{
			return $result[0];
		}
		return 1;
	}

	/**
	 * check the imagesources need to install depend on backup file from 3.x.x version
	 * @return array image sources
	 */
	function _getSourceFromVersion3()
	{
		$objJSNSource = JSNISFactory::getObj('classes.jsn_is_source');

		if ($this->_canAutoDownload == false) {
			$this->_imageSources = $objJSNSource->compareLocalSources();
		} else {
			$this->_imageSources = $objJSNSource->compareSources();
		}

		$uninstalledSources  	= array();
		$restoredSources 	 	= array();
		$tmpRestoredSources		= array();

		if(!isset($this->_document->showlists)) return array();

		$showlists = $this->_document->showlists;
		$showlist  = @$showlists[0]->showlist;

		if (count($showlist))
		{
			$imageSources = $this->_imageSources;
			$countz 	  = count($imageSources);

			if ($countz)
			{
				for ($z = 0; $z < $countz; $z ++ )
				{
					$zrows = $imageSources[$z];
					if ($this->_canAutoDownload)
					{
						if ($zrows->needInstall) {
							$uninstalledSources['source'.$zrows->identified_name] = $zrows;
						}
					}
					else
					{
						$uninstalledSources['source'.$zrows->identified_name] = $zrows;
					}
				}
			}

			for ($i = 0, $counti=count($showlist); $i < $counti; $i ++ )
			{
				$rows 		   = $showlist[$i];
				$attributes [] = $rows->attributes();
			}

			if(count($attributes))
			{
				foreach ($attributes as $attribute)
				{
					foreach($attribute as $key => $value)
					{
						if($key == 'showlist_source')
						{
							switch ($value)
							{
								case '2': // flickr
									$tmpRestoredSources [] = 'sourceflickr';
									break;
								case '3': // picasa
									$tmpRestoredSources [] = 'sourcepicasa';
									break;
								case '4': // phoca
									$tmpRestoredSources [] = 'sourcephoca';
									break;
								case '5': // joomga
									$tmpRestoredSources [] = 'sourcejoomgallery';
									break;
							}
						}
					}
				}
			}
			$restoredSources = array_unique($tmpRestoredSources);
		}

		if (count($restoredSources))
		{
			if ($this->_canAutoDownload == false)
			{
				foreach ($restoredSources as $restoredSource)
				{
					if (!isset($uninstalledSources[$restoredSource]))
					{
						$tmpSource = new stdClass();
						$tmpSource->identified_name = str_replace('source', '', $restoredSource);
						$tmpSource->name = ucwords(str_replace('source', 'source ', $restoredSource));
						$tmpSource->authentication = false;
						$this->_requiredSourcesNeedInstall [] = $tmpSource;
					}
				}
			}
			else
			{
				foreach ($uninstalledSources as $key => $uninstalledSource)
				{
					if (in_array($key, $restoredSources))
					{
						$this->_requiredSourcesNeedInstall [] = $uninstalledSource;
					}
				}
			}
		}

		return $this->_requiredSourcesNeedInstall;
	}

	/**
	 * check the themes need to install depend on backup file from 3.x.x version
	 * @return array image sources
	 */
	function _getThemeFromVersion3()
	{
		$objJSNTheme	   		= JSNISFactory::getObj('classes.jsn_is_themes');

		if ($this->_canAutoDownload == false) {
			$this->_themes 	= $objJSNTheme->compareLocalSources();
		} else {
			$this->_themes 	= $objJSNTheme->compareSources();
		}

		$uninstalledSources  	= array();
		$restoredSources 	 	= array();
		$tmpRestoredSources		= array();

		if(!isset($this->_document->showcases)) return array();

		$showcases = $this->_document->showcases;
		$showcase  = @$showcases[0]->showcase;
		if (count($showcase))
		{
			$themes = $this->_themes;
			if (count($themes))
			{
				for ($z = 0, $countz=count($themes); $z < $countz; $z ++ )
				{
					$zrows = $themes[$z];

					if ($this->_canAutoDownload)
					{
						if ($zrows->needInstall) {
							$uninstalledSources[$zrows->identified_name] = $zrows;
						}
					}
					else
					{
						$uninstalledSources[$zrows->identified_name] = $zrows;
					}
				}
			}

			for ($i = 0, $counti=count($showcase); $i < $counti; $i ++ )
			{
				$rows 			= $showcase[$i];
				$attributes [] = $rows->attributes();
			}

			if(count($attributes))
			{
				foreach ($attributes as $attribute)
				{
					foreach($attribute as $key => $value)
					{
						if($key == 'theme_name')
						{
							$tmpRestoredSources [] = (string) $value;
						}
					}
				}
			}
			$restoredSources = array_unique($tmpRestoredSources);
		}

		if (count($restoredSources))
		{
			if ($this->_canAutoDownload == false)
			{
				foreach ($restoredSources as $restoredSource)
				{
					if (!isset($uninstalledSources[$restoredSource]))
					{
						$tmpSource = new stdClass();
						$tmpSource->identified_name = $restoredSource;
						$tmpSource->name = ucwords(str_replace('theme', 'theme ', $restoredSource));
						$tmpSource->authentication = false;
						$this->_requiredThemesNeedInstall [] = $tmpSource;
					}
				}
			}
			else
			{
				foreach ($uninstalledSources as $key=>$uninstalledSource)
				{
					if (in_array($key, $restoredSources))
					{
						$this->_requiredThemesNeedInstall [] = $uninstalledSource;
					}
				}
			}
		}

		return $this->_requiredThemesNeedInstall;
	}

	/**
	 * check the imagesources need to install depend on backup file from 4.x.x version
	 * @return array image sources
	 */
	function _getSourceFromVersion4()
	{
		$objJSNSource	   	= JSNISFactory::getObj('classes.jsn_is_source');
		$objJSNTheme		= JSNISFactory::getObj('classes.jsn_is_themes');
		$unInstallSources 	= array();
		$unInstallThemes 	= array();

		if ($this->_canAutoDownload)
		{
			$this->_imageSources 	= $objJSNSource->compareSources();
			$this->_themes			= $objJSNTheme->compareSources();
				
			foreach ($this->_imageSources as $source)
			{
				if ($source->needInstall) {
					$unInstallSources[$source->identified_name] = $source;
				}
			}

			foreach ($this->_themes as $theme)
			{
				if ($theme->needInstall) {
					$unInstallThemes[$theme->identified_name] = $theme;
				}
			}
		}
		else
		{
			$this->_imageSources 	= $objJSNSource->compareLocalSources();
			$this->_themes			= $objJSNTheme->compareLocalSources();

			foreach ($this->_themes as $theme) {
				$unInstallThemes[$theme->identified_name] = $theme;
			}

			foreach ($this->_imageSources as $source) {
				$unInstallSources['source'.$source->identified_name] = $source;
			}
		}

		//$checkDatabase 	= $this->_document->getElementByPath('database');
		$attributes 	= $this->_document->attributes();

		if (!count($attributes)) return array();

		if (isset($attributes->sources))
		{
			$tmpStrSource = (string) $attributes->sources;
			$sources = explode(',', $tmpStrSource);
			if ($this->_canAutoDownload)
			{
				foreach ($unInstallSources as $source)
				{
					if ($source->needInstall && in_array('source'.$source->identified_name, $sources)){
						$this->_requiredSourcesNeedInstall[] = $source;
					}
				}
			}
			else
			{
				foreach ($sources as $source)
				{
					if (!isset($unInstallSources[$source]))
					{
						$tmpSource = new stdClass();
						$tmpSource->identified_name = $source;
						$tmpSource->name = ucwords(str_replace('source', 'source ', $source));
						$tmpSource->authentication = false;
						$this->_requiredSourcesNeedInstall[] = $tmpSource;
					}
				}
			}
		}

		if (isset($attributes->themes))
		{
			$tmpStrThemes = (string) $attributes->themes;
			$themes = explode(',', $tmpStrThemes);
			if ($this->_canAutoDownload)
			{
				foreach ($unInstallThemes as $theme)
				{
					if ($theme->needInstall && in_array($theme->identified_name, $themes)){
						$this->_requiredThemesNeedInstall[] = $theme;
					}
				}
			}
			else
			{
				foreach ($themes as $theme)
				{
					if (!isset($unInstallThemes[$theme]))
					{
						$tmpSource = new stdClass();
						$tmpSource->identified_name = $theme;
						$tmpSource->name = ucwords(str_replace('theme', 'theme ', $theme));
						$tmpSource->authentication = false;
						$this->_requiredThemesNeedInstall[] = $tmpSource;
					}
				}
			}
		}

		return array_merge($this->_requiredSourcesNeedInstall, $this->_requiredThemesNeedInstall);
	}

	/**
	 * prepare data list sources and themes need to install when restore data
	 */
	function getListRequiredInstallData()
	{
		$joomlaVersion 	= new JVersion();
		$commercial 	= false;
		$sources 		= array();
		$themes 		= array();

		foreach ($this->_requiredSourcesNeedInstall as $source)
		{
			$sourceInfo = new stdClass();
			$sourceInfo->identify_name = $source->identified_name;
			$sourceInfo->edition = '';
			$sourceInfo->joomla_version = $joomlaVersion->RELEASE;
			$sourceInfo->commercial = $source->authentication;
			$sources[] = $sourceInfo;

			if ($source->authentication == true) {
				$commercial = true;
			}
		}

		foreach ($this->_requiredThemesNeedInstall as $theme)
		{
			$themeInfo = new stdClass();
			$themeInfo->identify_name = $theme->identified_name;
			$themeInfo->edition = '';
			$themeInfo->joomla_version = $joomlaVersion->RELEASE;
			$themeInfo->commercial = $theme->authentication;
			$themes[] = $themeInfo;

			if ($theme->authentication == true) {
				$commercial = true;
			}
		}

		$objJSNLightcart = JSNISFactory::getObj('classes.jsn_is_lightcart');
		$jsnLightCartError = $objJSNLightcart->getErrorCode('DEFAULT', false);

		return $this->_requiredInstallData = array('lightCartErrorCode' => $jsnLightCartError, 'commercial' => $commercial,'imagesources' => $sources, 'themes' => $themes);
	}
}