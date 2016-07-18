<?php
/**
 * @version    $Id: installer.php 17322 2012-10-24 03:41:14Z giangnd $
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
jimport('joomla.application.component.controller');

jimport('joomla.filesystem.file');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/libraries/joomlashine/installer/controller.php';
require_once JPATH_COMPONENT . DS . 'classes' . DS . 'jsn_is_installermessage.php';

class ImageShowControllerInstaller extends JSNInstallerController
{
	/**
	 * Contructor
	 *
	 * @param   array  $config  a array of config items
	 */

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('installcore', 'display');
		$this->registerTask('installtheme', 'display');
		$this->registerTask('installsuccessfully', 'display');
	}

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  A JController object to support chaining
	 */

	public function display($cachable = false, $urlparams = false)
	{
		//JRequest::setVar('hidemainmenu', 1);
		switch ($this->getTask())
		{
			case 'installcore':
				JRequest::setVar('layout', 'installcore');
				break;
			case 'login':
				JRequest::setVar('layout', 'form_login');
				break;
			case 'manualInstall':
				JRequest::setVar('layout', 'form_manual_install');
				break;
			default:
				JRequest::setVar('layout', 'default');
				break;
		}

		JRequest::setVar('view', 'installer');
		JRequest::setVar('model', 'installer');
		parent::display();
	}

	/**
	 * Forward to a specified URL
	 *
	 * @return void
	 */

	public function forward()
	{
		$this->setRedirect('index.php?option=com_imageshow&controller=installer&task=install');
	}

	/**
	 * Finish process of installation and creat a copy of imageshow.xml with new name is com_imageshow.xml
	 * This action is to maintain for old versions.
	 *
	 * @return void
	 */

	public function finish()
	{
		// clear install session
		$session = JFactory::getSession();
		$session->set('preversion', null, 'jsnimageshow');
		$session->set('jsn-list-required-install', array(), 'jsn-install-manual');
		$link 	= 'index.php?option=com_imageshow';

		$pathSrcManifestFile 		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'imageshow.xml';
		$pathDestManifestFile     	= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'com_imageshow.xml';
		JFile::copy($pathSrcManifestFile, $pathDestManifestFile);

		$objJSNInstMessage 	= new JSNISInstallerMessage();
		$objJSNInstMessage->installMessage();
		$this->setRedirect($link);
	}

	/**
	 * Download image source from the server
	 *
	 * @return string
	 */

	public function downloadImageSource()
	{
		$session 		= JFactory::getSession();
		$identifier		= md5('jsn_imageshow_downloasource_identify_name');
		$session->set($identifier, '', 'jsnimageshowsession');
		$foldername 	= 'tmp';
		$folderpath 	= JPATH_ROOT . DS . $foldername;
		$ftp 			= JRequest::getInt('ftp', 0);
		if (is_writable($folderpath) || $ftp)
		{
			$download 	= JSNISFactory::getObj('classes.jsn_is_downloadimagesource');
			$post 		= JRequest::get('post');
			$objVersion = new JVersion();

			$options 				= new stdClass();
			$options->identifyName 	= $post['identify_name'];
			$options->edition 		= $post['edition'];
			$options->joomlaVersion = $objVersion->RELEASE;

			if (isset($post['username']) && $post['username'] != '')
			{
				$options->username = $post['username'];
			}

			if (isset($post['password']) && $post['password'] != '')
			{
				$options->password = $post['password'];
			}

			$result = $download->download($options);

			if ($result)
			{
				$session->set($identifier, $post['identify_name'], 'jsnimageshowsession');
				echo json_encode(array('success' => true, 'package_path'=> (string) $download->_tmpPackagePath));
			}
			else
			{
				echo json_encode(array('success' => false, 'message'=> $download->_msgError));
			}
		}
		else
		{
			$msg = JText::sprintf('INSTALLER_IMAGE_SOURCE_FOLDER_PERMISSION', DS . 'tmp');
			echo json_encode(array('success' => false, 'message'=>$msg));
		}
		exit();
	}

	/**
	 * Download image core from the server
	 *
	 * @return string
	 */

	public function downloadImageShowCore()
	{
		$foldername 	= 'tmp';
		$folderpath 	= JPATH_ROOT . DS . $foldername;

		if (is_writable($folderpath))
		{
			$download 	= JSNISFactory::getObj('classes.jsn_is_downloadcore');
			$post 		= JRequest::get('post');
			$objVersion = new JVersion();

			$options 				= new stdClass();
			$options->identifyName 	= $post['identify_name'];
			$options->edition 		= $post['edition'];
			$options->joomlaVersion = $objVersion->RELEASE;

			if (isset($post['username']) && $post['username'] != '')
			{
				$options->username = $post['username'];
			}

			if (isset($post['password']) && $post['password'] != '')
			{
				$options->password = $post['password'];
			}

			if (isset($post['based_identified_name']) && $post['based_identified_name'] != '')
			{
				$options->basedIdentifiedName = $post['based_identified_name'];
			}

			if (isset($post['upgrade']) && $post['upgrade'] != '')
			{
				$options->upgrade = $post['upgrade'];
			}

			$result = $download->download($options);

			if ($result)
			{
				echo json_encode(array('success' => true, 'package_path'=> (string) $download->_tmpPackagePath));
			}
			else
			{
				echo json_encode(array('success' => false, 'message'=> $download->_msgError));
			}
		}
		else
		{
			$msg = JText::sprintf('INSTALLER_IMAGE_SOURCE_FOLDER_PERMISSION', DS . 'tmp');
			echo json_encode(array('success' => false, 'message'=>$msg));
		}
		exit();
	}

	/*function installDefaultTheme()
	 {
		$fileName = JRequest::getVar('file_name', '');

		if (!$fileName) return false;

		$result = false;
		$msg = '';

		$model = $this->getModel('installer');

		$result = $model->install(true, $fileName);

		if ($result) {
		$this->_migrateShowcaseToThemeProfile();
		$result = $this->get('success', true);
		$msg 	= $this->get('message', '');
		} else {
		$msg = JText::_('INSTALLER_DEFAULT_THEME_CAN_NOT_INSTALL');
		}

		echo json_encode(array('success' => $result, 'message' => $msg));
		exit();
		}*/

	/**
	 * Install image source
	 *
	 * @return string
	 */

	public function installImageSource()
	{
		$session 		= JFactory::getSession();
		$identifier		= md5('jsn_imageshow_downloasource_identify_name');
		$result 		= false;
		$msg 			= '';

		$filePath = JRequest::getVar('package_path', '');

		if (JFile::exists($filePath))
		{
			$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installimagesource');
			$objInstallSource->install($filePath);

			if ($objInstallSource->_error)
			{
				$msg = $objInstallSource->_msgError;
			}
			else
			{
				$result = true;
			}
		}
		else
		{
			$msg = JText::_('INSTALLER_IMAGE_SOURCE_PACKAGE_NOT_EXISTS');
		}

		if (!$result)
		{
			$session->set($identifier, '', 'jsnimageshowsession');
		}

		echo json_encode(array('success' => $result, 'message' => $msg));
		exit();
	}

	/**
	 * check backup file , core version and do migrate
	 *
	 * @return void
	 */

	public function getListSourcesMigrate()
	{
		$session 	= JFactory::getSession();
		$preVersion = (float) str_replace('.', '', $session->get('preversion', null, 'jsnimageshow'));
		$version400 = (float) str_replace('.', '', '4.0.0');
		$result 	= false;
		$msg 		= '';
		$preVersion = 300;

		if (!$preVersion || ($preVersion >= $version400)) return false; // new install , don't need to migrate

		$objJSNMigrateSource = JSNISFactory::getObj('classes.jsn_is_migrateimagesource');
		$showlist 	 	 = $objJSNMigrateSource->checkBackupFile('jsn_is_showlist_backup.xml');
		$configuration 	 = $objJSNMigrateSource->checkBackupFile('jsn_is_configuration_backup.xml');

		if ($showlist = false || $configuration == false)
		{
			$this->set('success', false);
			$this->set('message', JText::_('INSTALLER_IMAGE_SOURCE_NOT_FOUND_BACKUP_FILES'));
			//return false;
		}

		$objJSNSource = JSNISFactory::getObj('classes.jsn_is_source');
		$sources 	  = $objJSNSource->getListSources();
	}

	/**
	 * List all plugin required to install.
	 *
	 * @param bool $json. If it is true, then return type of data is JSON, otherwise a object
	 *
	 * @return string
	 */

	public function getListPluginInstall($json = true)
	{
		$objJSNPlugin 	= JSNISFactory::getObj('classes.jsn_is_plugins');
		$data 			= $objJSNPlugin->getListJSNPluginNeedInstall();

		if ($json)
		{
			echo json_encode($data);
			exit();
		}
		else
		{
			return $data;
		}
	}

	/**
	 * Download Theme from the server
	 *
	 * @return string
	 */

	public function downloadShowcaseTheme()
	{
		$foldername 	= 'tmp';
		$folderpath 	= JPATH_ROOT . DS . $foldername;
		$ftp 			= JRequest::getInt('ftp', 0);
		if (is_writable($folderpath) || $ftp)
		{
			$download 	= JSNISFactory::getObj('classes.jsn_is_downloadshowcasetheme');
			$post 		= JRequest::get('post');
			$objVersion = new JVersion();

			$options 				= new stdClass();
			$options->identifyName 	= $post['identify_name'];
			$options->edition 		= $post['edition'];
			$options->joomlaVersion = $objVersion->RELEASE;

			if (isset($post['username']) && $post['username'] != '')
			{
				$options->username = $post['username'];
			}

			if (isset($post['password']) && $post['password'] != '')
			{
				$options->password = $post['password'];
			}

			$result = $download->download($options);

			if ($result)
			{
				echo json_encode(array('success' => true, 'package_path'=> (string) $download->_tmpPackagePath));
			}
			else
			{
				echo json_encode(array('success' => false, 'message'=> $download->_msgError));
			}
		}
		else
		{
			$msg = JText::sprintf('SHOWCASE_INSTALL_THEME_FOLDER_PERMISSION', DS . 'tmp');
			echo json_encode(array('success' => false, 'message'=>$msg));
		}
		exit();
	}

	/**
	 * Install a image source
	 *
	 * @return string
	 */

	public function installShowcaseTheme()
	{
		$result 	= false;
		$msg 		= '';
		$filePath 	= JRequest::getVar('package_path', '');
		if (JFile::exists($filePath))
		{
			$objInstallShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_installshowcasetheme');
			$objInstallShowcaseTheme->install($filePath);
			if ($objInstallShowcaseTheme->_error)
			{

				$msg = $objInstallShowcaseTheme->_msgError;
			}
			else
			{
				$result = true;
			}
		}
		else
		{
			$msg = JText::_('SHOWCASE_INSTALL_THEME_PACKAGE_NOT_EXISTS');
		}
		echo json_encode(array('success' => $result, 'message' => $msg));
		exit();
	}

	/**
	 * Restore data, which is backup from version < 4.0.0
	 *
	 * @return string
	 */

	public function restoreDatabase()
	{
		$session 	= JFactory::getSession();
		$preVersion = (float) str_replace('.', '', $session->get('preversion', null, 'jsnimageshow'));
		$version400 = (float) str_replace('.', '', '4.0.0');

		if ($preVersion && ($preVersion < $version400))
		{
			$get = JRequest::get('get');
			$compressType = 1;
			$filepath 	  = JPATH_ROOT . DS . 'tmp';

			$config['path'] 		= $filepath;
			$config['file'] 		= array('name' => $get['backup_file']);
			$config['compress'] 	= $compressType;
			$config['file_upload'] 	= $filepath.DS.$get['backup_file'];

			if (JFile::exists($config['file_upload']))
			{
				$objJSNRestore 	= JSNISFactory::getObj('classes.jsn_is_restore');
				$result 		= $objJSNRestore->restoreBackupForMigrate($config);

				if ($result)
				{
					$session->set('jsn-list-required-install', array(), 'jsn-install-manual');
				}

				echo json_encode(array('success' => ($result) ? true : false , 'message' => ''));
			}
			else
			{
				echo json_encode(array('success' => false , 'message' => JText::_('INSTALLER_MIGRATE_FILE_NOT_EXISTS', true)));
			}

			exit();
		}

		echo json_encode(array('success' => false , 'message' => ''));
		exit();
	}

	/**
	 * Download file backup
	 *
	 * @return void
	 */

	public function downloadBackupFile()
	{
		$get = JRequest::get('get');
		$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNUtils->downloadArchiveFile($get['file_name']);
		exit();
	}

	/**
	 * Check all elements, which are necessary for installation
	 *
	 * @return string
	 */

	public function checkRequiredElementsIsInstalled()
	{
		$get 	= JRequest::get('get');
		$model	= $this->getModel('installer');
		$result = ($model->checkRequiredElementsIsInstalled(trim($get['elements'])));

		if ($result)
		{
			$totalElements = (int) $get['total_elements'];
			if ($totalElements == $result)
			{
				echo json_encode(array('check' => true));
			}
			else
			{
				echo json_encode(array('check' => false));
			}
		}
		else
		{
			echo json_encode(array('check' => false));
		}
		exit();
	}

	/**
	 *  Get all sources form version 3.x.x
	 *
	 *  @return array
	 */

	public function _getSourceFromVersion3xx()
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

		return $requiredSource;
	}

	/**
	 * Install ImageShow core
	 *
	 * @return string
	 */

	public function installImageShowCore()
	{
		$result = false;
		$msg 	= '';

		$filePath = JRequest::getVar('package_path', '');

		if (JFile::exists($filePath))
		{
			$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installimageshowcore');
			$objInstallSource->onInstall($filePath);

			if ($objInstallSource->_error)
			{
				$msg = $objInstallSource->_msgError;
			}
			else
			{
				$result = true;
			}
		} else
		{
			$msg = JText::_('INSTALLER_IMAGESHOW_CORE_PACKAGE_NOT_EXISTS');
		}

		echo json_encode(array('success' => $result, 'message' => $msg));
		exit();
	}

	public function installImageShowCoreByUpgrade()
	{
		$filePath = JRequest::getVar('package_path', '');
		$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installimageshowcore');
		$objInstallSource->onInstall($filePath);
		$this->setRedirect('index.php?option=com_installer&view=install');
	}

	public function installThemeManual()
	{
		$file = JRequest::getVar('file', null, 'files', 'array');
		$identifiedName = JRequest::getVar('identified_name', null);
		$redirectLink = JRequest::getVar('redirect_link', 'index.php');
		$objInstallShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_installshowcasetheme');
		$result = $objInstallShowcaseTheme->installManual($file);

		if ($identifiedName)
		{

			$session = JFactory::getSession();
			$required = $session->get('jsn-list-required-install', array(), 'jsn-install-manual');

			if (is_array($required))
			{
				$required[$identifiedName] = $result;
				$session->set('jsn-list-required-install', $required, 'jsn-install-manual');
			}
		}

		$this->setRedirect($redirectLink);
	}

	public function installImageSourceManual()
	{
		$file = JRequest::getVar('file', null, 'files', 'array');
		$identifiedName = JRequest::getVar('identified_name', null);
		$redirectLink = JRequest::getVar('redirect_link', 'index.php');
		$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installimagesource');
		$result = $objInstallSource->installManual($file);

		if ($identifiedName)
		{
			$session = JFactory::getSession();
			$required = $session->get('jsn-list-required-install', array(), 'jsn-install-manual');

			if (is_array($required))
			{
				$required[$identifiedName] = $result;
				$session->set('jsn-list-required-install', $required, 'jsn-install-manual');
			}
		}

		$this->setRedirect($redirectLink);
	}

	public function installImageShowCoreManual()
	{
		$file = JRequest::getVar('file', null, 'files', 'array');
		$redirectLink = JRequest::getVar('redirect_link', 'index.php');
		$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installimageshowcore');
		$objInstallSource->installManual($file);
		$this->setRedirect($redirectLink);
	}
}
