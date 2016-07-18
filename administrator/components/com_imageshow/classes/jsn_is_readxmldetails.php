<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_readxmldetails.php 16077 2012-09-17 02:30:25Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.filesystem.file');
class JSNISReadXmlDetails
{
	var $_arrayTruncate 	= array();
	var $_arrayInstall 		= array();
	var $_indentityInsertOn	= array();
	var $_indentityInsertOff= array();
	var $_limitEdition 		= true;
	var $_themeTablesExist  = array();
	var $_error				= false;
	var $_themeVersion		= array();
	var $_sourceVersion		= array();

	public static function getInstance()
	{
		static $instanceReadXML;
		if ($instanceReadXML == null)
		{
			$instanceReadXML = new JSNISReadXmlDetails();
		}
		return $instanceReadXML;
	}

	function parserXMLDetails()
	{
		$arrayResult 			= array();
		$arraylang 				= array();
		$temp 					= null;

		$pathOldManifestFile		= JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'com_imageshow.xml';
		$pathNewManifestFile 		= JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'imageshow.xml';

		if (JFile::exists($pathNewManifestFile))
		{
			$fileDescription = $pathNewManifestFile;
		}
		else
		{
			$fileDescription = $pathOldManifestFile;
		}

		$xml 						= JFactory::getXML($fileDescription);
		$nodeRealName				= $xml->name;
		$nodVersion 			    = $xml->version;
		$nodAuthor 			   		= $xml->author;
		$nodDate 			        = $xml->creationDate;
		$nodLicense			        = $xml->license;
		$nodCopyright			    = $xml->copyright;
		$nodWebsite 			    = $xml->authorUrl;
		$languages 			    	= $xml->languages;
		$administration				= $xml->administration;
		$nodEdition 				= $xml->edition;

		if ($administration != false)
		{
			$submenu = $administration->submenu;
			if ($submenu != false)
			{
				$child = $submenu->children();

				if (count($child) > 0)
				{
					$arrayKey = array();
					foreach ($child as $value)
					{
						$value    = (string) $value;
						$keyValue = JString::strtoupper($value);
						$arrayKey [] = $keyValue;
					}
					$arrayResult['menu'] = $arrayKey;
				}
			}
		}
		if ($nodAuthor != false && $nodVersion != false && $nodDate != false && $nodLicense != false && $nodCopyright != false && $nodWebsite != false && $nodeRealName != false)
		{
			$arrayResult['realName'] 	= (string) $nodeRealName;
			$arrayResult['version'] 	= (string) $nodVersion;
			$arrayResult['author'] 		= (string) $nodAuthor;
			$arrayResult['date'] 		= (string) $nodDate;
			$arrayResult['license'] 	= (string) $nodLicense;
			$arrayResult['copyright'] 	= (string) $nodCopyright;
			$arrayResult['website'] 	= (string) $nodWebsite;
			$arrayResult['edition'] 	= (($nodEdition!= false) ? (string) $nodEdition : '');
			if (count($languages->children()))
			{
				$langchild = $languages->children();
				foreach ($langchild as $value)
				{
					if ($temp != (string) $value->attributes()->tag)
					{
						$tag 				= (string) $value->attributes()->tag;
						$arraylang [$tag] 	= $tag;
						$temp 				= $tag;
					}
				}
				$arrayResult['langs'] = $arraylang;
			}
		}
		return $arrayResult;
	}

	function raiseError($error)
	{
		$this->_error = true;
		JError::raiseWarning(100,JText::_($error));
	}

	/*
	 * Paser xml file in package was downloaded
	 * $path path to xml file
	 *
	 */

	function parserExtXmlDetailsSampleData($folderPath, $path)
	{
		$sampleData 		  	= JSNISFactory::getObj('classes.jsn_is_sampledata');
		$objJSNUtils 	      	= JSNISFactory::getObj('classes.jsn_is_utils');
		$this->_limitEdition  	= $objJSNUtils->checkLimit();
		$arrayObj 				= array();
		$obj		 			= new stdClass();
		$xml 					= JFactory::getXML($path);
		$link 				  	= 'index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data#data-sample-installation';
		if (!$xml)
		{
			$sampleData->deleteTempFolderISD($folderPath);
			$msg = JText::_('MAINTENANCE_SAMPLE_DATA_NOT_FOUND_INSTALLATION_FILE_IN_SAMPLE_DATA_PACKAGE', true);
			echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
			exit();
		}
		$sampleData->deleteTempFolderISD($folderPath);

		if ($xml->attributes())
		{
			if((string) $xml->attributes()->name != 'imageshow' || (string) $xml->attributes()->author != 'joomlashine' || (string) $xml->attributes()->description != 'JSN ImageShow' )
			{
				$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
				echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
				exit();
			}

			$obj->name 			= trim(strtolower((string) $xml->attributes()->name));
			$obj->version 		= (isset($xml->attributes()->version) ? (string) $xml->attributes()->version : '');
			$obj->author 		= (isset($xml->attributes()->author) ? (string) $xml->attributes()->author : '');
			$obj->description 	= (isset($xml->attributes()->description) ? (string) $xml->attributes()->description : '');
			$obj->sources 		= (isset($xml->attributes()->sources) ? (string) $xml->attributes()->sources : '');
			$obj->themes 		= (isset($xml->attributes()->themes) ? (string) $xml->attributes()->themes : '');
		}
		else
		{
			$msg = JText::_('MAINTENANCE_SAMPLE_DATA_INCORRECT_FILE_XML_SAMPLE_DATA_PACKAGE', true);
			echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
			exit();
		}

		$this->_arrayTruncate 	= array();
		$this->_arrayInstall  	= array();
		$this->_indentityInsertOff = array();
		$this->_indentityInsertOn = array();
		$arrayMethod 			= get_class_methods('JSNISReadXmlDetails');
		$arrayMethodAvailable   = array();

		if (is_array($arrayMethod))
		{
			foreach ($arrayMethod as $method)
			{
				$arrayMethodAvailable[] = trim(strtolower($method));
			}
		}

		foreach ($xml->children() as $children)
		{

			$methodName = "_parse".ucfirst($children->name())."SampleData";
			if (in_array(trim(strtolower($methodName)), $arrayMethodAvailable))
			{
				$this->$methodName($children);
			}
		}

		$obj->truncate = $this->_arrayTruncate;
		$obj->install  = $this->_arrayInstall;
		$obj->indentityInsertOn = $this->_indentityInsertOn;
		$obj->indentityInsertOff = $this->_indentityInsertOff;
		$arrayObj [(string) $xml->attributes()->name] = $obj;
		return $arrayObj;
	}

	function _setIdentityInsertOn($objSimpleXML)
	{
		$tmp = array();
		foreach ($objSimpleXML->children() as $objSubSimleXML)
		{
			foreach ($objSubSimleXML->children() as $tables)
			{
				foreach ($tables->children() as $table)
				{
					if ($table->_attributes['name'] != '' && $table->_attributes['name'] != '#__imageshow_theme_profile')
					{
						$tmp [] = 'SET IDENTITY_INSERT '.$table->_attributes['name'].' ON';
					}
				}
			}
		}
		if (count($tmp))
		{
			$this->_indentityInsertOn = array_unique($tmp);
		}
	}

	function _setIdentityInsertOff($objSimpleXML)
	{
		$tmp = array();
		foreach ($objSimpleXML->children() as $objSubSimleXML)
		{
			foreach ($objSubSimleXML->children() as $tables)
			{
				foreach ($tables->children() as $table)
				{
					if ($table->_attributes['name'] != ''  && $table->_attributes['name'] != '#__imageshow_theme_profile')
					{
						$tmp [] = 'SET IDENTITY_INSERT '.$table->_attributes['name'].' OFF';
					}
				}
			}
		}
		if (count($tmp))
		{
			$this->_indentityInsertOff = array_unique($tmp);
		}
	}
	function _parseCoreSampleData($objSimpleXML)
	{
		$link 	= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';
		foreach ($objSimpleXML->children() as $task)
		{
			if ($task->name() != 'task')
			{
				$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
				echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
				exit();
			}

			$arrayTruncate = $this->_parseTablesElementSampleData($task, 'dbtruncate');
			$arrayInstall  = $this->_parseTablesElementSampleData($task, 'dbinstall');

			if ($arrayInstall === false || $arrayTruncate === false)
			{
				return false;
			}

			foreach ($arrayTruncate as $value)
			{
				$this->_arrayTruncate[] =  $value;
			}

			foreach ($arrayInstall as $value)
			{
				$this->_arrayInstall[] = $value;
			}
		}

		return true;
	}

	function _parseThemesSampleData($objSimpleXML)
	{
		$objJSNTheme 	 		 = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$themeTableExist 		 = $objJSNTheme->listThemesExist();
		$this->_themeTablesExist = is_array($themeTableExist) ? $themeTableExist : array();
		$link 					= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';
		foreach ($objSimpleXML->children() as $theme)
		{
			$objJSNTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');

			$this->_themeVersion [(string) $theme->attributes()->name] = (string) $theme->attributes()->version;

			foreach ($theme->children() as $task)
			{
				if ($task->name() != 'task')
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}
				$arrayTruncate = $this->_parseThemeTablesElementSampleData($task, 'dbtruncate');
				$arrayInstall  = $this->_parseThemeTablesElementSampleData($task, 'dbinstall');

				if ($arrayInstall === false || $arrayTruncate === false)
				{
					return false;
				}

				foreach ($arrayTruncate as $value)
				{
					$this->_arrayTruncate[] =  $value;
				}

				foreach ($arrayInstall as $value)
				{
					$this->_arrayInstall[] = $value;
				}
			}
		}
		return true;
	}

	function _parseSourcesSampleData($objSimpleXML)
	{
		$link 					= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';
		foreach ($objSimpleXML->children() as $source)
		{
			$this->_sourceVersion [(string) $source->attributes()->name] = (string) $source->attributes()->version;

			foreach ($source->children() as $task)
			{
				if ($task->name() != 'task')
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}
				$arrayTruncate = $this->_parseSourceTablesElementSampleData($task, 'dbtruncate');
				$arrayInstall  = $this->_parseSourceTablesElementSampleData($task, 'dbinstall');

				if ($arrayInstall === false || $arrayTruncate === false)
				{
					return false;
				}

				foreach ($arrayTruncate as $value)
				{
					$this->_arrayTruncate[] =  $value;
				}

				foreach ($arrayInstall as $value)
				{
					$this->_arrayInstall[] = $value;
				}
			}
		}
		return true;
	}

	function _parseTablesElementSampleData($objSimleXML, $tableType)
	{
		$queries 		= array();
		$attributesTask = $objSimleXML->attributes();
		$link 			= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';

		if (isset($attributesTask->name) && (string) $attributesTask->name == $tableType)
		{
			foreach ($objSimleXML->children() as $tables)
			{
				if ($tables->name() != 'tables')
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}

				foreach ($tables->children() as $table)
				{

					if ($table->name() != 'table')
					{
						$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
						echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
						exit();
					}

					foreach ($table->children() as $parameters)
					{
						if ($parameters->name() != 'parameters')
						{
							$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
							echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
							exit();
						}

						$countRecord = 0;
						foreach($parameters->children() as $parameter)
						{
							if($parameter->name() != 'parameter')
							{
								$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
								echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
								exit();
							}

							if($countRecord == 2 && $this->_limitEdition == true  && $tableType = 'dbinstall')// allow only two record
							{
								break;
							}
							$tmpStrParameter = (string) $parameter;
							$queries[] = trim($tmpStrParameter);
							$countRecord++;
						}
					}
				}
			}
		}
		return $queries;
	}

	function _parseThemeTablesElementSampleData($objSimleXML, $tableType)
	{
		$config   = new JConfig();
		$dbprefix = $config->dbprefix;
		$queries 		= array();
		$attributesTask = $objSimleXML->attributes();
		$link 			= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';

		if (isset($attributesTask->name) && (string) $attributesTask->name == $tableType)
		{
			foreach ($objSimleXML->children() as $tables)
			{
				if ($tables->name() != 'tables')
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}

				foreach ($tables->children() as $table)
				{
					if ($table->name() != 'table')
					{
						$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
						echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
						exit();
					}

					$tableName = str_replace("#__", $dbprefix, (string) $table->attributes()->name);

					foreach ($table->children() as $parameters)
					{
						if ($parameters->name() != 'parameters')
						{
							$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
							echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
							exit();
						}

						$countRecord = 0;
						foreach($parameters->children() as $parameter)
						{
							if($parameter->name() != 'parameter')
							{
								$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
								echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
								exit();
							}
							if($countRecord == 2 && $this->_limitEdition == true  && $tableType = 'dbinstall')// allow only two record
							{
								break;
							}
							$tmpStrParameter = (string) $parameter;
							$queries[] = trim($tmpStrParameter);
							$countRecord++;
						}
					}
				}
			}
		}

		return $queries;
	}

	function _parseSourceTablesElementSampleData($objSimleXML, $tableType)
	{
		$config   = new JConfig();
		$dbprefix = $config->dbprefix;
		$queries 		= array();
		$attributesTask = $objSimleXML->attributes();
		$link 			= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';

		if (isset($attributesTask->name) && (string) $attributesTask->name == $tableType)
		{
			foreach ($objSimleXML->children() as $tables)
			{
				if ($tables->name() != 'tables')
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}

				foreach ($tables->children() as $table)
				{
					if ($table->name() != 'table')
					{
						$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
						echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
						exit();
					}

					$tableName = str_replace("#__", $dbprefix, (string) $table->attributes()->name);

					foreach ($table->children() as $parameters)
					{
						if ($parameters->name() != 'parameters')
						{
							$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
							echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
							exit();
						}

						foreach($parameters->children() as $parameter)
						{
							if($parameter->name() != 'parameter')
							{
								$msg = JText::_('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED', true);
								echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
								exit();
							}
							$tmpStrParameter = (string) $parameter;
							$queries[] = trim($tmpStrParameter);
						}
					}
				}
			}
		}

		return $queries;
	}

	function parserExtXmlDetailsSampleDataManually($path)
	{
		$objJSNUtils 	      = JSNISFactory::getObj('classes.jsn_is_utils');
		$this->_limitEdition  = $objJSNUtils->checkLimit();
		$xml 				  = JFactory::getXML($path);
		$path 				  = JPath::clean($path);

		if (!$xml)
		{
			$this->raiseError('MAINTENANCE_SAMPLE_DATA_NOT_FOUND_INSTALLATION_FILE_IN_SAMPLE_DATA_PACKAGE');
			return false;
		}

		$arrayObj = array();
		$obj = new stdClass();

		if ($xml->attributes())
		{
			if ((string) $xml->attributes()->name != 'imageshow' || (string) $xml->attributes()->author != 'joomlashine' || (string) $xml->attributes()->description != 'JSN ImageShow')
			{
				$this->raiseError('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED');
				return false;
			}

			$obj->name 			= trim(strtolower((string) $xml->attributes()->name));
			$obj->version 		= (isset($xml->attributes()->version) ? (string) $xml->attributes()->version : '');
			$obj->author 		= (isset($xml->attributes()->author) ? (string) $xml->attributes()->author : '');
			$obj->description 	= (isset($xml->attributes()->description) ? (string) $xml->attributes()->description : '');
			$obj->sources 		= (isset($xml->attributes()->sources) ? (string) $xml->attributes()->sources : '');
			$obj->themes 		= (isset($xml->attributes()->themes) ? (string) $xml->attributes()->themes : '');
		}
		else
		{
			$this->raiseError('MAINTENANCE_SAMPLE_DATA_INCORRECT_FILE_XML_SAMPLE_DATA_PACKAGE');
			return false;
		}

		$this->_arrayTruncate = array();
		$this->_arrayInstall  = array();

		$arrayMethod 			= get_class_methods('JSNISReadXmlDetails');
		$arrayMethodAvailable   = array();

		if (is_array($arrayMethod))
		{
			foreach ($arrayMethod as $method)
			{
				$arrayMethodAvailable[] = trim(strtolower($method));
			}
		}

		foreach ($xml->children() as $children)
		{
			$methodName = "_parse".ucfirst($children->name())."SampleDataManually";

			if (in_array(trim(strtolower($methodName)), $arrayMethodAvailable))
			{
				$this->$methodName($children);
			}
		}

		if ($this->_error){
			return false;
		}

		$obj->truncate = $this->_arrayTruncate;
		$obj->install  = $this->_arrayInstall;

		$arrayObj [(string) $xml->attributes()->name] = $obj;

		return $arrayObj;
	}

	function _parseThemesSampleDataManually($objSimpleXML)
	{
		$objJSNTheme 	 		 = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$themeTableExist 		 = $objJSNTheme->listThemesExist();
		$this->_themeTablesExist = is_array($themeTableExist) ? $themeTableExist : array();

		foreach ($objSimpleXML->children() as $theme)
		{
			$objJSNTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');

			foreach ($theme->children() as $task)
			{
				if ($task->name() != 'task')
				{
					$this->raiseError('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED');
					return false;
				}

				$arrayTruncate = $this->_parseThemeTablesElementSampleData($task, 'dbtruncate');
				$arrayInstall  = $this->_parseThemeTablesElementSampleData($task, 'dbinstall');

				if ($arrayInstall === false || $arrayTruncate === false)
				{
					return false;
				}

				foreach ($arrayTruncate as $value)
				{
					$this->_arrayTruncate[] =  $value;
				}

				foreach ($arrayInstall as $value)
				{
					$this->_arrayInstall[] = $value;
				}
			}
		}
		return true;
	}

	function _parseCoreSampleDataManually($objSimpleXML)
	{
		$link 	= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';
		foreach ($objSimpleXML->children() as $task)
		{
			if ($task->name() != 'task')
			{
				$this->raiseError('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED');
				return false;
			}

			$arrayTruncate = $this->_parseTablesElementSampleData($task, 'dbtruncate');
			$arrayInstall  = $this->_parseTablesElementSampleData($task, 'dbinstall');

			if ($arrayInstall === false || $arrayTruncate === false)
			{
				return false;
			}

			foreach ($arrayTruncate as $value)
			{
				$this->_arrayTruncate[] =  $value;
			}

			foreach ($arrayInstall as $value)
			{
				$this->_arrayInstall[] = $value;
			}
		}

		return true;
	}

	function _parseSourcesSampleDataManually($objSimpleXML)
	{
		foreach ($objSimpleXML->children() as $source)
		{
			foreach ($source->children() as $task)
			{
				if ($task->name() != 'task')
				{
					$this->raiseError('MAINTENANCE_SAMPLE_DATA_XML_STRUCTURE_WAS_EDITED');
					return false;
				}

				$arrayTruncate = $this->_parseSourceTablesElementSampleData($task, 'dbtruncate');
				$arrayInstall  = $this->_parseSourceTablesElementSampleData($task, 'dbinstall');

				if ($arrayInstall === false || $arrayTruncate === false)
				{
					return false;
				}

				foreach ($arrayTruncate as $value)
				{
					$this->_arrayTruncate[] =  $value;
				}

				foreach ($arrayInstall as $value)
				{
					$this->_arrayInstall[] = $value;
				}
			}
		}
		return true;
	}
}
?>