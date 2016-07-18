<?php
/**
 * @version    $Id: data.php 16077 2012-09-17 02:30:25Z giangnd $
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
 * Data model of JSN Framework Sample component
 */

class ImageShowModelData extends JSNDataModel
{

	/**
	 * Create data backup then force user to download the backup file.
	 *
	 * @return  void
	 */

	public function backup($options = array())
	{
		$tables = array();
		$showlist = false;
		$showcase = false;

		foreach ($options['tables'] as $table)
		{
			if (strpos($table, '#__imageshow_showlist') !== false)
			{
				$showlist = true;
			}

			if (strpos($table, '#__imageshow_showcase') !== false)
			{
				$showcase = true;
			}
		}

		// Get Joomla config
		$config = JFactory::getConfig();

		$objJSNISData  = JSNISFactory::getObj('classes.jsn_is_data');

		// Initialize variables

		$com  = preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option'));

		$name	= (isset($options['name']) AND ! empty($options['name']))
		? ($options['name'] . (@$options['timestamp'] ? '_' . date('YmdHis') : ''))
		: date('YmdHis');

		$name = array(
				'zip' => "{$name}.zip",
				'xml' => "jsn_{$com}_backup_db.xml"
		);

		// Do any preparation needed before doing real data backup
		try
		{
			$this->beforeBackup($options, $name);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Backup data from selected tables
		//$this->backupTables($options['tables']);
		$this->data = $objJSNISData->executeBackup($showlist, $showcase);

		// Backup files from selected folders
		$this->backupFiles($options['files']);

		// Do any extra work needed after doing real data backup
		try
		{
			$this->afterBackup($options, $name);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Force client to download backup file
		if (isset($this->zippedBackup))
		{
			if ( ! isset($options['no-download']) OR ! $options['no-download'])
			{
				JSNUtilsFile::forceDownload($name['zip'], $this->zippedBackup, 'application/zip', true);
			}
			else
			{
				// Store zipped backup to file system
				JFile::write($config->get('tmp_path') . '/' . $name['zip'], $this->zippedBackup);

				return $config->get('tmp_path') . '/' . $name['zip'];
			}
		}

		throw new Exception(JText::_('JSN_EXTFW_DATA_BACKUP_FAIL'));
	}

	/**
	 * Restore database table data and/or files from backup.
	 *
	 * @param   mixed  $backup  Either path to an existing file or a variable of $_FILES.
	 *
	 * @return  void
	 */

	public function restore($backup)
	{
		global $objectLog;

		$session 		= JFactory::getSession();
		$objJSNRestore 	= JSNISFactory::getObj('classes.jsn_is_restore');

		// Get Joomla config
		$config = JFactory::getConfig();

		// Initialize backup file

		$config 				= array();
		$config['file'] 		= $backup;
		$config['compress'] 	= 1;
		$config['path'] 		= JPATH_ROOT . DS . 'tmp' . DS;
		$config['file_upload'] 	= JPATH_ROOT . DS . 'tmp' . DS . $backup['name'];

		$result = $objJSNRestore->restore($config);

		$requiredInstallData = $objJSNRestore->getListRequiredInstallData();
		$requiredInstallData['backup_file'] = $backup;
		$requiredInstallData['processText'] = JText::_('JSN_IMAGESHOW_PROCESS_TEXT', true);
		$requiredInstallData['waitText'] = JText::_('JSN_IMAGESHOW_WAIT_TEXT', true);

		if ($result)
		{
			$objectLog->addLog(JFactory::getUser()->get('id'), JRequest::getURI(), $backup['name'], 'maintenance', 'restore');
			$session->set('JSNISRestore',
			array(
							'error' => false,
							'extractFile'=> $objJSNRestore->_extractFile,
							'message' => JText::_('MAINTENANCE_BACKUP_RESTORE_SUCCESSFULL'),
							'requiredSourcesNeedInstall' => $objJSNRestore->_requiredSourcesNeedInstall,
							'requiredThemesNeedInstall' => $objJSNRestore->_requiredThemesNeedInstall,
							'requiredInstallData' => $requiredInstallData
			));
		}
		else
		{
			$session->set('JSNISRestore',
			array(
							'error' => true,
							'extractFile'=> $objJSNRestore->_extractFile,
							'message' => $objJSNRestore->_msgError,
							'requiredSourcesNeedInstall' => $objJSNRestore->_requiredSourcesNeedInstall,
							'requiredThemesNeedInstall' => $objJSNRestore->_requiredThemesNeedInstall,
							'requiredInstallData' => $requiredInstallData
			));
		}
	}
}
