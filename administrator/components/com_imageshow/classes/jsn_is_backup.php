<?php
/**
 * @version    $Id: jsn_is_backup.php 16077 2012-09-17 02:30:25Z giangnd $
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
 * JSNISBackup Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

jimport('joomla.filesystem.file');
jimport('joomla.utilities.simplexml');

class JSNISBackup
{
	public static function getInstance()
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (empty($instances['JSNISBackup']))
		{
			$instance	= new JSNISBackup;
			$instances['JSNISBackup'] = &$instance;
		}

		return $instances['JSNISBackup'];

	}

	public function JSNISBackup()
	{
	}

	public function createBackUpFileForMigrate()
	{
		$session 	= JFactory::getSession();
		$preVersion = (float) str_replace('.', '', $session->get('preversion', null, 'jsnimageshow'));
		$version400 = (float) str_replace('.', '', '4.0.0');
		//$preVersion = 313;
		if (!$preVersion) return;

		if ($preVersion < $version400) // old backup flow
		{
			$objJSNISMaintenance 	= JSNISFactory::getObj('classes.jsn_is_maintenance313', null, 'database');
			$xmlString  			= $objJSNISMaintenance->renderXMLData(true, true);
		}
		else if ($preVersion >= $version400) // new backup flow
		{
			$objJSNISData  	= JSNISFactory::getObj('classes.jsn_is_data');
			$xmlString  	= $objJSNISData->executeBackup(true, true)->asXML();
		}

		$fileBackupName 		= 'jsn_imageshow_backup_db.xml';
		$fileZipName 			= 'jsn_is_backup_for_migrate_' . $preVersion . '_' . date('YmdHis') . '.zip';

		if (JFile::write(JPATH_ROOT . DS . 'tmp' . DS . $fileBackupName, $xmlString))
		{
			$config = JPATH_ROOT . DS . 'tmp' . DS . $fileZipName;
			$zip 	= JSNISFactory::getObj('classes.jsn_is_archive', 'JSNISZIPFile', $config);
			$zip->setOptions(array('inmemory' => 1, 'recurse' => 0, 'storepaths' => 0));
			$zip->addFiles(JPATH_ROOT . DS . 'tmp' . DS . $fileBackupName);
			$zip->createArchive();
			$zip->writeArchiveFile();
			$FileDelte = JPATH_ROOT . DS . 'tmp' . DS . $fileBackupName;
			$session->set('jsn_is_backup_for_migrate', $fileZipName, 'jsnimageshow');
			return true;
		}
		return false;
	}

	public function setSourceFromVersion3xx()
	{
		$session 	= JFactory::getSession();
		$preVersion = (float) str_replace('.', '', $session->get('preversion', null, 'jsnimageshow'));
		$version400 = (float) str_replace('.', '', '4.0.0');

		if ($preVersion && $preVersion < $version400)
		{
			$db 	= JFactory::getDBO();
			$query 	= 'SELECT * FROM #__imageshow_showlist';
			$db->setQuery($query);
			$results = $db->loadObjectList();

			$sources = array('picasa' => false, 'flickr' => false, 'phoca' => false, 'joomgallery' => false);

			foreach ($results as $result)
			{
				switch ($result->showlist_source)
				{
					case '1': //folder
						//
						break;
					case '2': //flickr
						$sources['flickr'] = true;
						break;
					case '3': //picasa
						$sources['picasa'] = true;
						break;
					case '4': //phoca
						$sources['phoca'] = true;
						break;
					case '5': //joomgallery
						$sources['joomgallery'] = true;
						break;
				}
			}

			$requiredSource = array();

			foreach ($sources as $key => $value)
			{
				if ($value == true)
				{
					$requiredSource[] = $key;
				}
			}
			$session = JFactory::getSession();
			$session->set('JSNISImageSourceRequired3xxVersion', $requiredSource);
			return $requiredSource;
		}
	}
}