<?php
/**
 * @version     $Id: installer.php 17909 2012-11-02 08:07:36Z giangnd $
 * @package     JSN_Sample
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import JSN Installer library
require_once JPATH_COMPONENT_ADMINISTRATOR . '/libraries/joomlashine/installer/model.php';

jimport('joomla.application.component.model');

jimport('joomla.filesystem.file');
jimport('joomla.installer.helper');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_installer.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_component_installer.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_upgradedbutil.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_upgradethemedb.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_upgradeimagesourcedb.php');

class ImageShowModelInstaller extends JSNInstallerModel
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}

	function executeQuery($queies)
	{
		if (count($queies))
		{
			foreach ($queies as $value)
			{
				$this->_db->setQuery($value);
				if (!$this->_db->query())
				{
					return false;
				}
			}
		}
		return true;
	}

	function installPlugin($maintain = false, $filename = '')
	{
		if (! $filename) return false;

		$package = $this->_unPackageFromTempFolder($filename);

		if (!$package)
		{
			$this->setState('message', 'Unable to find install package');
			return false;
		}

		$objJSNPlugins 		= JSNISFactory::getObj('classes.jsn_is_plugins');
		$pluginModel 		= JModel::getInstance('plugins', 'imageshowmodel');
		$listJSNPlugins		= $pluginModel->getFullData();
		$countPlugin		= count($listJSNPlugins);

		global $mainframe;
		$plugins = array();

		if ($countPlugin)
		{
			for ($i = 0; $i < $countPlugin; $i++)
			{
				$item = $listJSNPlugins[$i];
				$plugins [$item->element] = $objJSNPlugins->getXmlFile($item);
			}
		}

		$installer = JSNISInstaller::getInstance();

		if (!$installer->install($package['dir']))
		{
			$result = false;
		}
		else
		{
			$msg = JText::_('INSTALLER_THEME_PACKAGE_SUCCESSFULLY_INSTALLED');
			$result = true;
		}

		if ($result && count($plugins) && !$maintain)
		{
			$this->upgradeThemeDB($plugins, $installer);
		}

		$mainframe->enqueueMessage(@$msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));

		if (!is_file($package['packagefile']))
		{
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->get('tmp_path') . DS . $package['packagefile'];
		}
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
		return $result;
	}

	/*function installComponent()
	{
		global $mainframe;
		jimport('joomla.client.helper');

		switch(JRequest::getWord('installtype'))
		{
			case 'upload':
				$package = $this->_getPackageFromUpload();
				break;
			default:
				$this->setState('message', 'No Install Type Found');
				return false;
				break;
		}

		if (!$package)
		{
			$this->setState('message', 'Unable to find install package');
			return false;
		}

		$installer = JSNISComponentInstaller::getInstance();

		if (!$installer->checkPackage($package['dir']))
		{
			$result = false;
		}
		else
		{
			$jinstaller = JInstaller::getInstance();
			if($jinstaller->install($package['dir']))
			{
				$msg = JText::_('INSTALLER_PACKAGE_SUCCESSFULLY_INSTALLED');
				$result = true;
			}
			else
			{
				$msg = JText::_('INSTALLER_PACKAGE_UNSUCCESSFULLY_INSTALLED');
				$result = false;
			}
		}

		$mainframe->enqueueMessage(@$msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));

		if (!is_file($package['packagefile']))
		{
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->getValue('config.tmp_path') . DS . $package['packagefile'];
		}
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
		return $result;
	}*/

	/*function _getPackageFromUpload()
	{
		$userfile = JRequest::getVar('install_package', null, 'files', 'array');
		if (!(bool) ini_get('file_uploads'))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLFILE'));
			return false;
		}
		if (!extension_loaded('zlib'))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLZLIB'));
			return false;
		}
		if (!is_array($userfile))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('INSTALLER_NO_FILE_SELECTED'));
			return false;
		}

		if ($userfile['error'] || $userfile['size'] < 1)
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLUPLOADERROR'));
			return false;
		}

		$config  = JFactory::getConfig();
		$tmpDEST = $config->getValue('config.tmp_path') . DS . $userfile['name'];
		$tmpSRC	 = $userfile['tmp_name'];

		jimport('joomla.filesystem.file');
		$uploaded 	= JFile::upload($tmpSRC, $tmpDEST);
		$package 	= JInstallerHelper::unpack($tmpDEST);

		return $package;
	}*/

	/*function _unPackageFromTempFolder($fileName)
	{
		$config 	= JFactory::getConfig();
		$tmpDEST 	= $config->getValue('config.tmp_path') . DS . $fileName;

		jimport('joomla.filesystem.file');

		$package = JInstallerHelper::unpack($tmpDEST);

		return $package;
	}*/

	/*function checkThemePlugin()
	{
		$query = 'SELECT COUNT(extension_id) FROM #__extensions WHERE folder=\'jsnimageshow\' AND name LIKE \'Theme%\'';
    	$this->_db->setQuery($query);
		$result    =  $this->_db->loadRow();
		if(count($result))
		{
		    if($result[0] != 0)
    		{
    		    return true;
    		}
        }
        return false;
	}*/

	/*function checkBackupFile($fileName)
	{
		if (JFile::exists(JPATH_ROOT . DS . 'tmp' . DS . $fileName))
		{
			return true;
		}
		return false;
	}*/

	/*function checkTableExist($tableName)
	{
		$objUpgradeDBAction = new JSNJSUpgradeDBAction();
		return $objUpgradeDBAction->isExistTable($tableName);
	}*/

	/*function checkTableColumExist($table, $column)
	{
		$objUpgradeDBAction = new JSNJSUpgradeDBAction();
		return $objUpgradeDBAction->isExistTableColumn($table, $column);
	}*/

	/*function installShowcaseThemeClassic()
	{
		$pathInstallFile = (JPATH_PLUGINS . DS . 'jsnimageshow' . DS . 'themeclassic');

		if(JFolder::exists($pathInstallFile))
		{
			if (JFile::exists($pathInstallFile . DS . 'install.mysqls.sql'))
			{
				$objJNSUtils = JSNISFactory::getObj('classes.jsn_is_utils');
				$buffer = $objJNSUtils->readFileToString($pathInstallFile . DS . 'install.mysql.sql');

				if ($buffer !== false)
				{
					jimport('joomla.installer.helper');
					$queries = JInstallerHelper::splitSql($buffer);
					if (count($queries))
					{
						foreach ($queries as $query)
						{
							$query = trim($query);
							if ($query != '' && $query{0} != '#')
							{
								$this->_db->setQuery($query);
								if (!$this->_db->query())
								{
									return false;
								}
							}
						}
						return true;
					}
				}
			}
		}
		return false;
	}*/

	/*function executeRestoreShowcaseThemeClassicData($version)
	{
		$version 		= (float) str_replace('.', '', $version);
		$compareVersion400 = (float) str_replace('.', '', '4.0.0');
		$database		= JFactory::getDBO();
		$filePath 		= JPATH_ROOT . DS . 'tmp' . DS . 'jsn_is_showcase_backup.xml';
		$parser 		= JFactory::getXMLParser('Simple');
		$loadFile 		= $parser->loadFile($filePath);
		$document 		= $parser->document;
		$showcases		= array();
		$inserts		= array();
		$updates		= array();
		$index			= 1;
		$arrayMapping 	= array();

		if (!$loadFile)
		{
			return false;
		}

		$checkShowCase = $document->getElementByPath('showcases');
		if ($checkShowCase != false)
		{
			$showcaseRoot = $document->showcases;
			if ($showcaseRoot != null)
			{
				$showcase = @$showcaseRoot[0]->showcase;
				if (count($showcase))
				{
					for ($i = 0; $i < count($showcase); $i++)
					{
						$rows 			= $showcase[$i];
						$attributes 	= $rows->attributes();
						$showcases [] 	= $attributes;

					}
					if (count($showcases))
					{
						foreach ($showcases as $showcase)
						{
							$fieldsShowCase 		= '';
							$fieldsShowCaseValue 	= '';

							foreach ($showcase as $key => $value)
							{
								if ($key != 'theme_id')
								{
									if($key == 'showcase_id')
									{
										$arrayMapping [$value] 	= $index;
										$key 					= 'theme_id';
										$value 					= $index;
									}

									if ($this->checkTableColumExist('#__imageshow_theme_classic', $key))
									{
										$fieldsShowCase 	 .= $key.',';
										$fieldsShowCaseValue .= $database->quote($value).',';
									}
								}
							}

							$inserts [] = 'INSERT #__imageshow_theme_classic ('.substr($fieldsShowCase, 0, -1).') VALUES ('.substr($fieldsShowCaseValue, 0, -1).')';
							$index++;
						}
					}
				}
			}
		}

		if (count($arrayMapping))
		{
			foreach($arrayMapping as $key => $value)
			{
				if ($version < $compareVersion400) {
					$inserts [] = 'INSERT #__imageshow_theme_profile (theme_id, showcase_id, theme_name) VALUES ('.$database->quote($value).','.$database->quote($key).' , "themeclassic")';
				}
			}
		}

		$queries = array_merge($inserts, $updates);

		if (count($queries))
		{
			$result = $this->executeQuery($queries);
			return $result;
		}
		return false;
	}*/


	/*function removeFile($path)
	{
		if (JFile::exists($path))
		{
			JFile::delete($path);
		}
	}*/

	function uninstall($eid = array())
	{
		// Initialise variables.
		$failed = array();


		if (!is_array($eid))
		{
			$eid = array($eid => 0);
		}

		// Get a database connector
		$db = JFactory::getDBO();

		// Get an installer object for the extension type
		jimport('joomla.installer.installer');
		$installer 	= JSNISInstaller::getInstance();
		$row 		= JTable::getInstance('extension');

		// Uninstall the chosen extensions
		foreach ($eid as $id)
		{
			$id = trim($id);
			$row->load($id);
			if ($row->type)
			{
				$result = $installer->uninstall($row->type, $id);
				if ($result === false)
				{
					$failed[] = $id;
				}
			}
			else
			{
				$failed[] = $id;
			}
		}

		$langstring = 'COM_INSTALLER_TYPE_TYPE_'. strtoupper($row->type);
		$rowtype 	= JText::_($langstring);
		if (strpos($rowtype, $langstring) !== false)
		{
			$rowtype = $row->type;
		}

		if (count($failed))
		{
			$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_ERROR', $rowtype);
			$result = false;
		}
		else
		{
			$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_SUCCESS', $rowtype);
			$result = true;
		}
		$app = JFactory::getApplication();
		$app->enqueueMessage($msg);
		//$this->setState('action', 'remove');
		//$this->setState('name', $installer->get('name'));
		//$app->setUserState('com_installer.message', $installer->message);
		//$app->setUserState('com_installer.extension_message', $installer->get('extension_message'));
		return $result;
	}

	/*function upgradeThemeDB($plugins, $objInstaller)
	{
		$manifest   = $objInstaller->getManifest();
		$name   	= trim($manifest->name);
		$name		= strtolower($name);
		$name		= str_replace(' ', '', $name);
		$folder		= $name;

		if (isset($plugins[$folder]))
		{
			$objUpgradeThemeDB 	= new JSNISUpgradeThemeDB($manifest, $plugins[$folder]);
			$objUpgradeThemeDB->executeUpgradeDB();
		}
	}*/

	/*function installImageSource($maintain = false)
	{
		global $mainframe;
		$imageSources = array();
		switch(JRequest::getWord('installtype'))
		{
			case 'upload':
				$package = $this->_getPackageFromUpload();
				break;
			default:
				$this->setState('message', 'No Install Type Found');
				return false;
				break;
		}


		if (!$package)
		{
			$this->setState('message', 'Unable to find install package');
			return false;
		}

		$objJSNPlugins = JSNISFactory::getObj('classes.jsn_is_plugins');
		$query = 'SELECT * FROM #__extensions WHERE folder = "jsnimageshow" AND LOWER(element) LIKE "source%"';

		$this->_db->setQuery($query);
		$listSource  = $this->_db->loadObjecList();
		$countSource = count($listSource);

		if ($countSource)
		{
			for ($i = 0; $i < $countSource; $i++)
			{
				$item = $listSource[$i];
				$imageSources [$item->element] = $objJSNPlugins->getXmlFile($item);
			}
		}

		$installer = JSNISInstaller::getInstance();

		if (!$installer->install($package['dir'])) {
			$result = false;
		} else {
			$msg = JText::_('INSTALLER_IMAGE_SOURCE_PACKAGE_SUCCESSFULLY_INSTALLED');
			$result = true;
		}

		if ($result && count($imageSources) && !$maintain) {
			$this->upgradeImageSourceDB($imageSources, $installer);
		}

		$mainframe->enqueueMessage(@$msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));

		if (!is_file($package['packagefile']))
		{
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->getValue('config.tmp_path') . DS . $package['packagefile'];
		}
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
		return $result;
	}*/

	/*function upgradeImageSourceDB($plugins, $objInstaller)
	{
		$manifest   = $objInstaller->getManifest();
		$objFiles   = $manifest->files;
		$folder		= $objFiles->folder;
		$folder		= $folder->data();
		if (isset($plugins[$folder]))
		{
			$objUpgradeImageSourceDB 	= new JSNISUpgradeImageSourceDB($manifest, $plugins[$folder]);
			$objUpgradeImageSourceDB->executeUpgradeDB();
		}
	}*/

	function checkRequiredElementsIsInstalled($elements)
	{
		$db 			= JFactory::getDBO();
		$whereElements 	= array();
		$where 			= array();
		$elements = explode(',', $elements);

		if (count($elements))
		{
			$where[] = 'folder = \'jsnimageshow\'';
			foreach ($elements as $element)
			{
				$whereElements[] = 'LOWER(element) = '.$db->Quote($element);
			}
			$whereElements 	=  (count($whereElements) ? '('.implode(' OR ', $whereElements ).')' : '');
			$where[]	= $whereElements;
			$where 		=  (count($where) ? ' WHERE '. implode(' AND ', $where ) : '');
			$query 		= 'SELECT * FROM #__extensions '.$where;
			$db->setQuery($query);

			return count($db->loadObjectList());
		}
		return false;
	}
}