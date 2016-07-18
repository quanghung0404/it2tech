<?php
/**
 * @version    $Id: maintenance.php 16506 2012-09-27 10:00:41Z giangnd $
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
 * Controller Maintenance Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

jimport('joomla.application.component.controller');

/**
 * Item controllers of JControllerForm
 *
 * @package  Controllers
 *
 * @since    1.6
 */

class ImageShowControllerMaintenance extends JSNConfigController
{
	/**
	 * Contructor
	 *
	 * @return void
	 *
	 */

	public function __construct()
	{
		parent::__construct();
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
	 * @return  JController  A JController object to support chaining.
	 *
	 */

	public function display($cachable = false, $urlparams = false)
	{
		switch($this->getTask())
		{
			default:
				JRequest::setVar('layout', 'default');
				JRequest::setVar('view', 'maintenance');
				JRequest::setVar('model', 'maintenance');
		}
		parent::display();
	}

	/**
	 * Install languages
	 *
	 * @return void
	 */

	public function installLang()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$objJSNLang 	= JSNISFactory::getObj('classes.jsn_is_language');
		$lang			= JRequest::getVar('languagemanager', array(), 'post', 'array');

		$adminLanguages = (isset($lang['a'])) ? $lang['a'] : array();
		$siteLanguages =  (isset($lang['s'])) ? $lang['s'] : array();

		if (count($adminLanguages) || count($siteLanguages))
		{
			$adminLanguages[] = JComponentHelper::getParams('com_languages')->get('administrator', 'en-GB');
			$objJSNLang->install($adminLanguages, 'administrator');

			$siteLanguages[] = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
			$objJSNLang->install($siteLanguages, 'site');
		}

		echo JText::_('SUCCESSFULLY_SAVED_CHANGES');
		exit();
	}

	/**
	 * Save message
	 *
	 * @return void
	 */

	public function saveMessage()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$status		= JRequest::getVar('messages', array(), 'post', 'array');
		$screen		= JRequest::getString('msg_screen');
		$objJSNMsg 	= JSNISFactory::getObj('classes.jsn_is_message');
		$objJSNMsg->setMessagesStatus($status, $screen);
		echo JText::_('SUCCESSFULLY_SAVED_CHANGES');
		exit();
	}

	/**
	 * refresh message
	 *
	 * @return void
	 */

	public function refreshMessage()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$objJSNMsg = JSNISFactory::getObj('classes.jsn_is_message');
		$objJSNMsg->refreshMessage();
		echo JText::_('SUCCESSFULLY_SAVED_CHANGES');
		exit();
	}

	/**
	 * Remove profile
	 *
	 * @return void
	 */

	public function removeProfile()
	{
		global $objectLog;
		JRequest::checkToken('get') or jexit('Invalid Token');
		$user			= JFactory::getUser();
		$userID			= $user->get ('id');
		$sourceID 		= JRequest::getInt('external_source_id');
		$sourceName 	= JRequest::getString('image_source_name');
		$objJSNProfile 	= JSNISFactory::getObj('classes.jsn_is_profile');
		$objJSNProfile->deleteProfile($sourceID, $sourceName);
		$objectLog->addLog($userID, JRequest::getURI(), '1', 'profile', 'delete');
		exit();
	}

	/**
	 * Save parameter
	 *
	 * @return void
	 */

	public function saveParam()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$post 		   		= JRequest::get('post');
		$objJSNParameter 	= JSNISFactory::getObj('classes.jsn_is_parameter');
		$objJSNUtils 		= JSNISFactory::getObj('classes.jsn_is_utils');

		$objJSNParameter->saveParameters(@$post['jsnconfig']);
		$objJSNUtils->approveModule('mod_imageshow_quickicon', (int) $post['jsnconfig']['show_quick_icons']);

		echo JText::_('SUCCESSFULLY_SAVED_CHANGES');
		exit();
	}

	/**
	 * Save profile
	 *
	 * @return void
	 */

	public function saveProfile()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$post = JRequest::get('post');
		$imageSource = JSNISFactory::getSource($post['source'], 'external');
		$imageSource->_source['sourceTable']->load($post['external_source_id']);
		$imageSource->_source['sourceTable']->bind($post);
		$imageSource->_source['sourceTable']->store();
		exit();
	}

	/**
	 * Delete theme
	 *
	 * @return void
	 */

	public function deleteTheme()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');

		$themeID 	= array();
		$id 	 	= JRequest::getInt('theme_id', 0);
		$themeName 	= JRequest::getString('theme_name', '');
		if ($id && $themeName != '')
		{
			$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
			$objJSNShowcaseTheme->deleteThemeProfileByThemeName($themeName);

			$themeID [] = $id;
			$model		= $this->getModel('installer');
			$model->uninstall($themeID);
		}
		exit();
	}

	/*public function enableDisablePlugin()
	 {
		global $mainframe;
		$arrayPluginID = JRequest::getVar('pluginID');
		$publishStatus = JRequest::getInt('publish');

		if (count($arrayPluginID) > 0)
		{
		$pluginTable = JTable::getInstance('extension', 'JTable');
		$pluginTable->publish($arrayPluginID, $publishStatus);
		}

		$link = 'index.php?option=com_imageshow&controller=maintenance&type=themes';
		$mainframe->redirect($link);
		}*/

	/**
	 * Check whether the profile exist or not on editing
	 *
	 * @return void
	 */

	public function checkEditProfileExist()
	{
		$get 				= JRequest::get('get');
		$objJSNProfile 		= JSNISFactory::getObj('classes.jsn_is_profile');
		$result 			= $objJSNProfile->checkExternalProfileExist(trim($get['external_source_profile_title']), $get['source'], $get['external_source_id']);
		$data['success'] = $result;

		if ($result)
		{
			$data['msg'] = JText::_('MAINTENANCE_SOURCE_REQUIRED_FIELD_PROFILE_TITLE_EXIST');
		}

		echo json_encode($data);

		exit();
	}

	/**
	 * Validate profile
	 *
	 * @return void
	 */

	public function validateProfile()
	{
		$get = JRequest::get('get');
		$data['success'] 	= true;
		$imageSource 		= JSNISFactory::getSource($get['source'], 'external');
		$data['success'] 	= $imageSource->getValidation($get);
		$data['msg'] 		= ($data['success'] == false) ? $imageSource->getErrorMsg() : '';

		echo json_encode($data);
		exit();
	}

	/**
	 * Delete Obsolete Thumbnails
	 */

	public function deleteObsoleteThumbnails()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$objJSNImage 			= JSNISFactory::getObj('classes.jsn_is_images');
		$objJSNImageThumb 		= JSNISFactory::getObj('classes.jsn_is_imagethumbnail');
		$data 					= array();
		$obsoleteImages			= array();
		$thumbnailPath			= JPATH_ROOT . DS . 'images' . DS . 'jsn_is_thumbs' . DS;
		$imagePath				= JPATH_ROOT . DS . 'images' . DS;
		$showlistImages			= array();

		if (JFolder::exists($imagePath) && is_writable($imagePath))
		{
			$dbImages 		= $objJSNImage->getImagesBySourceName('folder');
			$folderImages   = $objJSNImageThumb->getThumnails();

			if (count($dbImages))
			{
				for ($i = 0, $counti = count($dbImages); $i < $counti; $i++)
				{
					$dbImage = $dbImages[$i];
					$objJSNImageThumb->checkImageFolderStatus($dbImage);
					$showlistImages [] = JFile::getName(str_replace('/', DS, $dbImage->image_small));
				}
			}

			if (count($folderImages))
			{
				if (count($showlistImages))
				{
					foreach ($folderImages as $folderImage)
					{
						$isExsited = false;
						foreach ($showlistImages as $showlistImage)
						{
							if ($folderImage == $showlistImage)
							{
								$isExsited = true;
								break;
							}
						}

						if(!$isExsited)
						{
							$obsoleteImages[] = $folderImage;
						}
					}
				}
				else
				{
					$obsoleteImages = $folderImages;
				}
			}

			if (count($obsoleteImages))
			{
				foreach ($obsoleteImages as $obsoleteImage)
				{
					$path = $thumbnailPath.$obsoleteImage;
					if (@is_file($path))
					{
						@JFile::delete($path);
					}
				}
			}
			$data['existed_folder'] = true;
			$data['delete'] 		= true;
			$data['status'] 		= true;
			$data['message'] 		= '';
		}
		else
		{
			$data['existed_folder'] = false;
			$data['delete'] 		= false;
			$data['status'] 		= false;
			$data['message'] 		= JText::sprintf('MAINTENANCE_FOLDER_IS_UNWRITABLE_OR_DOES_NOT_EXIST', 'images');
		}

		echo json_encode($data);
		exit();
	}

	/**
	 * Save Theme parameter
	 *
	 *  @return void
	 */

	public function saveThemeParameter()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$post = JRequest::get('post');
		$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$objJSNShowcaseTheme->importTableByThemeName($post['theme_name']);
		$table = JTable::getInstance($post['theme_name'] . $post['theme_table'], 'Table');
		$table->bind($post);
		$table->store();
		exit();
	}

	/**
	 * Save Profile parameter
	 *
	 *  @return void
	 */

	public function saveProfileParameter()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$post = JRequest::get('post');
		$objJSNProfile = JSNISFactory::getObj('classes.jsn_is_profile');
		$objJSNProfile->saveProfileParameter($post);
		exit();
	}

	/**
	 * Delete Image source
	 *
	 * @return void
	 */

	public function deleteImageSource()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');
		$sourceIDs = array();
		$sourceID  = JRequest::getInt('source_id');

		if ($sourceID)
		{
			$objJSNSource	= JSNISFactory::getObj('classes.jsn_is_source');
			$sourceInfo		= $objJSNSource->getSourceInfoByPluginID($sourceID);

			// remove images, profile record if have
			if ($sourceInfo)
			{
				$objJSNSource->uninstallImageSource($sourceInfo);
			}

			$sourceIDs[] 	= $sourceID;
			$model		  	= $this->getModel('installer');
			$model->uninstall(array($sourceID));
		}
		exit();
	}

	/**
	 * Down load the sample data package
	 *
	 * @return string
	 */

	public function downloadSampleDataPackage()
	{
		ini_set('max_execution_time', 300);
		$sampleDownloadUrl 	= JFactory::getApplication()->input->getVar('sample_download_url');
		$perm				= true;
		$foldername 		= 'tmp';
		$folderpath 		= JPATH_ROOT . DS . $foldername;
		$link 				= 'index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data#data-sample-installation';

		if (is_writable($folderpath))
		{
			try
			{
				$objJSNDownloadPackage = JSNUtilsHttp::get($sampleDownloadUrl, $folderpath . DS. basename($sampleDownloadUrl));
	
				if ($objJSNDownloadPackage)
				{
					echo json_encode(array('download' => true, 'file_name'=> (string) basename($sampleDownloadUrl)));
				}
				else
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_CANNOT_DOWNLOAD_INSTALLATION_FILE', true);
					echo json_encode(array('download' => false, 'message'=>$msg, 'redirect_link'=>$link));
				}
			}
			catch(Exception $e)
			{
				$msg = JText::_('MAINTENANCE_SAMPLE_DATA_CANNOT_DOWNLOAD_INSTALLATION_FILE', true);
				echo json_encode(array('download' => false, 'message'=>$msg, 'redirect_link'=>$link));
			}				
		}
		else
		{
			$msg = JText::sprintf('MAINTENANCE_SAMPLE_DATA_FOLDER_MUST_HAVE_WRITABLE_PERMISSION', DS.'tmp');
			echo json_encode(array('download' => false, 'message'=>$msg, 'redirect_link'=>$link));
		}
		exit();
	}

	/**
	 * Install sample data
	 *
	 * @return string
	 */

	public function installSampledata()
	{
		$objJSNSource	   					= JSNISFactory::getObj('classes.jsn_is_source');
		$objJSNTheme	   					= JSNISFactory::getObj('classes.jsn_is_themes');
		$sampleData 						= JSNISFactory::getObj('classes.jsn_is_sampledata');
		$objReadXmlDetail					= JSNISFactory::getObj('classes.jsn_is_readxmldetails');
		$objJSNUtils      					= JSNISFactory::getObj('classes.jsn_is_utils');
		$infoCore 							= $objJSNUtils->getComponentInfo();
		$infoCore							= json_decode($infoCore->manifest_cache, true);
		$task 								= JRequest::getWord('task');
		$fileName							= JRequest::getVar('file_name');
		$link 								= 'index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data#data-sample-installation';
		$perm								= false;
		$foldertmp	 						= JPATH_ROOT.DS.'tmp';
		if (is_writable($foldertmp))
		{
			$perm = true;
		}
		$imageSources			= $objJSNSource->compareSources();
		$uninstalledSources  	= array();
		$sampleSources 	 		= array();
		$errors					= array();
		$themeElements			= array();
		$sourceElements			= array();
		$themes					= $objJSNTheme->compareSources();
		$uninstalledThemes  	= array();
		$sampleThemes 	 		= array();
		$elements				= array();
		$commercial				= false;

		if ($task == 'installSampledata')
		{
			if ($perm)
			{
				$currentVersion = JSN_IMAGESHOW_VERSION;
				$sampleData->getPackageVersion(trim(strtolower($infoCore['name'])));
				$path 		= $foldertmp . DS . $fileName;

				if (!JFile::exists($path))
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_INSTALLATION_FILE_NOT_FOUND', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}

				$unpackage = $sampleData->unpackPackage($fileName);
				if ($unpackage)
				{
					$dataInstall = $objReadXmlDetail->parserExtXmlDetailsSampleData($unpackage, $unpackage . DS . FILE_XML);
					$sampleData->deleteTempFolderISD($unpackage);
					if ($dataInstall && is_array($dataInstall))
					{
						if (trim(strtolower($currentVersion)) != trim(strtolower($dataInstall['imageshow']->version)))
						{
							$msg = stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_ERROR_IMAGESHOW_VERSION', true));
							echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
							exit();
						}
						/*Check sources*/
						$sampleSources = explode(',', $dataInstall['imageshow']->sources);
						if (count($imageSources))
						{
							for ($z = 0, $countz=count($imageSources); $z < $countz; $z ++ )
							{
								$zrows 		= $imageSources[$z];

								if ($zrows->needInstall)
								{
									$uninstalledSources['source'.$zrows->identified_name]['name'] = $zrows->name;
									if (isset($zrows->authentication) && $zrows->authentication == true)
									{
										$uninstalledSources['source' . $zrows->identified_name]['commercial'] = true;
									}
									else
									{
										$uninstalledSources['source' . $zrows->identified_name]['commercial'] = false;
									}
								}
							}
						}

						if (count($uninstalledSources) && count($sampleSources))
						{
							foreach ($uninstalledSources as $key=>$uninstalledSource)
							{
								if (in_array($key, $sampleSources))
								{
									$elementID							= 'jsn-download-id-'.$key;
									$errors [] 							= '<div id="'.$elementID.'" class="jsn-sampledata-installation-processing jsn-sampledata-installation-wait"><span class="jsn-sampledata-installation-requried-elements">'.$uninstalledSource['name'].'.</span><img src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif"><span class="jsn-sampledata-install-status jsn-icon16 jsn-icon-ok">&nbsp;</span><p class="jsn-sampledata-install-status-text"></p>';
									$elements[]							= $key;
									$objInfoUpdate 						= new stdClass();
									$objInfoUpdate->identify_name 		= str_replace('source', '', $key);
									$objInfoUpdate->edition 			= '';
									$objInfoUpdate->wait_text 			= JText::_('MAINTENANCE_SAMPLE_DATA_PROCESS_TEXT', true);
									$objInfoUpdate->process_text 		= JText::_('MAINTENANCE_SAMPLE_DATA_WAIT_TEXT', true);
									$objInfoUpdate->download_element_id	= $elementID;
									$objInfoUpdate 						= json_encode($objInfoUpdate);
									$sourceElements []					= $objInfoUpdate;
									if (!$commercial && $uninstalledSource['commercial'])
									{
										$commercial = true;
									}
								}
							}
						}

						/*Check sources*/
						/*Check themes*/
						$sampleThemes = explode(',', $dataInstall['imageshow']->themes);

						if (count($themes))
						{
							for ($q = 0, $countq=count($themes); $q < $countq; $q ++ )
							{
								$qrows 		= $themes[$q];
								if ($qrows->needInstall)
								{
									$uninstalledThemes[$qrows->identified_name]['name'] = $qrows->name;
									if (isset($qrows->authentication) && $qrows->authentication == true)
									{
										$uninstalledThemes[$qrows->identified_name]['commercial'] = true;
									}
									else
									{
										$uninstalledThemes[$qrows->identified_name]['commercial'] = false;
									}
								}
							}
						}

						if (count($uninstalledThemes) && count($sampleThemes))
						{
							foreach ($uninstalledThemes as $key=>$uninstalledTheme)
							{
								if (in_array($key, $sampleThemes))
								{
									$elementID							= 'jsn-download-id-'.$key;
									$errors [] 							= '<div id="'.$elementID.'" class="jsn-sampledata-installation-processing jsn-sampledata-installation-wait"><span class="jsn-sampledata-installation-requried-elements">'.$uninstalledTheme['name'].'.</span><img src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif"><span class="jsn-sampledata-install-status jsn-icon16 jsn-icon-ok">&nbsp;</span><p class="jsn-sampledata-install-status-text"></p>';
									$objInfoUpdate 						= new stdClass();
									$elements[]							= $key;
									$objInfoUpdate->identify_name 		= $key;
									$objInfoUpdate->edition 			= '';
									$objInfoUpdate->wait_text 			= JText::_('MAINTENANCE_SAMPLE_DATA_PROCESS_TEXT', true);
									$objInfoUpdate->process_text 		= JText::_('MAINTENANCE_SAMPLE_DATA_WAIT_TEXT', true);
									$objInfoUpdate->download_element_id	= $elementID;
									$objInfoUpdate 						= json_encode($objInfoUpdate);
									$themeElements []					= $objInfoUpdate;
									if (!$commercial && $uninstalledTheme['commercial'])
									{
										$commercial = true;
									}
								}
							}
						}
						/*Check themes*/
						if (count($errors))
						{
							$allElements 		= implode(',', $elements);
							$msg 				= stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_FOLLOWING_ELEMENTS_ARE_NOT_INSTALLED', true));
							$objJSNLightCart 	= JSNISFactory::getObj('classes.jsn_is_lightcart');
							$lightCartErrorCode = $objJSNLightCart->getErrorCode('DEFAULT', false);
							echo json_encode(array('light_cart_error_code' => $lightCartErrorCode, 'install' => false, 'message' => $msg, 'redirect_link'=>$link, 'warnings'=>$errors, 'sources'=>$sourceElements, 'themes'=>$themeElements, 'elements'=>$allElements, 'total_elements'=>count($elements), 'commercial'=>$commercial));
							exit();
						}
						/*Check version theme*/
						if (count($objReadXmlDetail->_themeVersion))
						{
							$objJSNTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
							foreach ($objReadXmlDetail->_themeVersion as $key=>$value)
							{
								$themeInfo 		= $objJSNTheme->getThemeInfo($key);
								if ($themeInfo->version != $value)
								{
									$msg = stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_THEME_VERSION_ERROR', true));
									echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
									exit();
								}
							}
						}
						/*Check version theme*/
						/*Check version source*/
						if (count($objReadXmlDetail->_sourceVersion))
						{
							$objJSNShowlistSource 	= JSNISFactory::getObj('classes.jsn_is_showlistsource');
							foreach ($objReadXmlDetail->_sourceVersion as $key=>$value)
							{
								$sourceInfo 		= $objJSNShowlistSource->getSourceInfo($key);
								if ($sourceInfo->version != $value)
								{
									$msg = stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_SOURCE_VERSION_ERROR', true));
									echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
									exit();
								}
							}
						}
						/*Check version source*/
						if ($fileName != '')
						{
							$sampleData->deleteISDFile($fileName);
						}
						$sampleData->executeInstallSampleData($dataInstall);
						$msg = JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SAMPLE_DATA_SUCCESSFULLY', true);
						echo json_encode(array('install' => true, 'message'=>$msg));
						exit();
					}
					else
					{
						$sampleData->returnError('false','');
					}
				}
				else
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_UNPACK_SAMPLEDATA_FALSE', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}
			}
			else
			{
				$msg = JText::sprintf('MAINTENANCE_SAMPLE_DATA_FOLDER_MUST_HAVE_WRITABLE_PERMISSION', DS.'tmp');
				echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
				exit();
			}
		}
	}

	public function reRestoreDatabase()
	{
		$get 		  = JRequest::get('get');
		$compressType = 1;
		$filepath 	  = JPATH_ROOT.DS.'tmp';

		$config['path'] 		= $filepath;
		$config['file'] 		= array('name' => $get['backup_file']);
		$config['compress'] 	= $compressType;
		$config['file_upload'] 	= $filepath . DS . $get['backup_file'];

		$objJSNRestore 	= JSNISFactory::getObj('classes.jsn_is_restore');
		$result 		= $objJSNRestore->restoreBackupForMigrate($config);

		echo json_encode(array('success' => ($result) ? true : false , 'message' => ''));
		exit();
	}

	function installSampledataManually()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$sampleData 				= JSNISFactory::getObj('classes.jsn_is_sampledata');
		$objReadXmlDetail			= JSNISFactory::getObj('classes.jsn_is_readxmldetails');
		$objJSNUtils      			= JSNISFactory::getObj('classes.jsn_is_utils');
		$infoCore 					= $objJSNUtils->getComponentInfo();
		$infoCore					= json_decode($infoCore->manifest_cache, true);
		$task 						= JRequest::getWord('task', '', 'POST');
		$post 						= JRequest::get('post');
		$uploadIdentifier 			= md5('upload_sampledata_package');
		$packagenameIdentifier 		= md5('sampledata_package_name');
		$session 					= JFactory::getSession();
		$session->set($uploadIdentifier, false, 'jsnimageshow');
		$session->set($packagenameIdentifier, '', 'jsnimageshow');


		//if ($task == 'installSampledataManually')
		//{
		if (!$post['agree_install_sample'])
		{
			$sampleData->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_PLEASE_CHECK_I_AGREE_INSTALL_SAMPLE_DATA'));
		}

		$perm = $sampleData->checkFolderPermission();

		if ($perm)
		{
			//$inforPackage 	= $objReadXmlDetail->parserXMLDetails();
			$sampleData->getPackageVersion(trim(strtolower($infoCore['name'])));

			$package 		= $sampleData->getPackageFromUpload();
			$unpackage 		= $sampleData->unpackPackage($package);

			if ($unpackage)
			{
				$dataInstall = $objReadXmlDetail->parserExtXmlDetailsSampleDataManually($unpackage . DS . FILE_XML);
				$sampleData->deleteTempFolderISD($unpackage);
				if (!$dataInstall && !is_array($dataInstall))
				{
					$sampleData->deleteISDFile($package);
					$sampleData->returnError('false','');
				}
				else
				{
					$session->set($uploadIdentifier, true, 'jsnimageshow');
					$session->set($packagenameIdentifier, $package, 'jsnimageshow');
					$sampleData->returnError('false','');
				}
			}
			else
			{
				$sampleData->deleteISDFile($package);
				$sampleData->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_UNPACK_SAMPLEDATA_FALSE'));
			}
		}
		else
		{
			$sampleData->returnError('false', JText::sprintf('MAINTENANCE_SAMPLE_DATA_FOLDER_MUST_HAVE_WRITABLE_PERMISSION', DS.'tmp'));
		}
		//}
	}

	public function executeInstallSampledataManually()
	{
		$objJSNSource	   					= JSNISFactory::getObj('classes.jsn_is_source');
		$objJSNTheme	   					= JSNISFactory::getObj('classes.jsn_is_themes');
		$sampleData 						= JSNISFactory::getObj('classes.jsn_is_sampledata');
		$objReadXmlDetail					= JSNISFactory::getObj('classes.jsn_is_readxmldetails');
		$objJSNUtils      					= JSNISFactory::getObj('classes.jsn_is_utils');

		//$infoCore 							= $objJSNUtils->getComponentInfo();
		//$infoCore							= json_decode($infoCore->manifest_cache, true);

		$uploadIdentifier 					= md5('upload_sampledata_package');
		$packagenameIdentifier 				= md5('sampledata_package_name');
		$task 								= JRequest::getWord('task');
		$fileName							= JRequest::getVar('file_name');
		$link 								= 'index.php?option=com_imageshow&controller=maintenance&g=data#data-sample-installation';
		$perm								= false;
		$foldertmp	 						= JPATH_ROOT  .DS . 'tmp';
		$session 							= JFactory::getSession();
		if (is_writable($foldertmp))
		{
			$perm = true;
		}
		$imageSources			= $objJSNSource->compareLocalSources();
		$installedSources	  	= array();
		$sampleSources 	 		= array();
		$errors					= array();
		$themeElements			= array();
		$sourceElements			= array();
		$themes					= $objJSNTheme->compareLocalSources();
		$installedThemes	  	= array();
		$sampleThemes 	 		= array();
		$elements				= array();
		$commercial				= false;
		$requiredElements		= array();
		if ($task == 'executeInstallSampledataManually')
		{
			if ($perm)
			{
				//$inforPackage 	= $objReadXmlDetail->parserXMLDetails();
				$componentInfo 	= $objJSNUtils->getComponentInfo();
				$componentData 	= null;

				//if (!is_null($componentInfo) && isset($componentInfo->manifest_cache) && $componentInfo->manifest_cache != '')
				//{
				$componentData  = json_decode($componentInfo->manifest_cache);
				$currentVersion = JSN_IMAGESHOW_VERSION;
				//}
				//else
				//{
				//$currentVersion = trim(@$inforPackage['version']);
				//}
				$sampleData->getPackageVersion(trim(strtolower($componentData->name)));
				$path 		= $foldertmp . DS . $fileName;

				if (!JFile::exists($path))
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_INSTALLATION_FILE_NOT_FOUND', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}

				$unpackage = $sampleData->unpackPackage($fileName);

				if ($unpackage)
				{
					$dataInstall = $objReadXmlDetail->parserExtXmlDetailsSampleData($unpackage, $unpackage.DS.FILE_XML);
					$sampleData->deleteTempFolderISD($unpackage);
					if ($dataInstall && is_array($dataInstall))
					{
						if (trim(strtolower($currentVersion)) != trim(strtolower($dataInstall['imageshow']->version)))
						{
							$msg = stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_ERROR_IMAGESHOW_VERSION', true));
							echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
							exit();
						}
						/*Check sources*/
						$sampleSources = explode(',', $dataInstall['imageshow']->sources);


						if (count($imageSources))
						{
							for ($z = 0, $countz=count($imageSources); $z < $countz; $z ++ )
							{
								$zrows 		= $imageSources[$z];
								$installedSources['source'.$zrows->identified_name]['name'] = $zrows->name;
							}
						}
						if (count($installedSources) && count($sampleSources))
						{
							foreach ($sampleSources as $sampleSource)
							{

								if (!isset($installedSources[$sampleSource]))
								{
									$elements[]							= $sampleSource;
									$objInfoUpdate 						= new stdClass();
									$objInfoUpdate->identify_name 		= $sampleSource;
									$objInfoUpdate->name 				= ucwords(str_replace('source', 'source ', $sampleSource));
									$objInfoUpdate->text 				= JText::sprintf('MAINTENANCE_SAMPLE_DATA_YOU_CAN_DOWNLOAD_VIA_CUSTOMER_AREA_PAGE', $objInfoUpdate->name, array('jsSafe'=>true, 'interpretBackSlashes'=>true, 'script'=>false));
									$objInfoUpdate->type 				= 'imagesource';
									$requiredElements []				= $objInfoUpdate;
								}
							}
						}
						else
						{
							foreach ($sampleSources as $sampleSource)
							{
								$elements[]							= $sampleSource;
								$objInfoUpdate 						= new stdClass();
								$objInfoUpdate->identify_name 		= $sampleTheme;
								$objInfoUpdate->name 				= ucwords(str_replace('source', 'source ', $sampleSource));
								$objInfoUpdate->text 				= JText::sprintf('MAINTENANCE_SAMPLE_DATA_YOU_CAN_DOWNLOAD_VIA_CUSTOMER_AREA_PAGE', $objInfoUpdate->name, array('jsSafe'=>true, 'interpretBackSlashes'=>true, 'script'=>false));
								$objInfoUpdate->type 				= 'imagesource';
								$requiredElements []				= $objInfoUpdate;
							}
						}
						/*Check sources*/
						/*Check themes*/
						$sampleThemes = explode(',', $dataInstall['imageshow']->themes);

						if (count($themes))
						{
							for ($q = 0, $countq=count($themes); $q < $countq; $q ++ )
							{
								$qrows 		= $themes[$q];
								$installedThemes[$qrows->identified_name]['name'] = $qrows->name;
							}
						}

						if (count($installedThemes) && count($sampleThemes))
						{
							foreach ($sampleThemes as $sampleTheme)
							{
								if (!isset($installedThemes[$sampleTheme]))
								{
									$elements[]							= $sampleTheme;
									$objInfoUpdate 						= new stdClass();
									$objInfoUpdate->identify_name 		= $sampleTheme;
									$objInfoUpdate->name 				= ucwords(str_replace('theme', 'theme ', $sampleTheme));
									$objInfoUpdate->text 				= JText::sprintf('MAINTENANCE_SAMPLE_DATA_YOU_CAN_DOWNLOAD_VIA_CUSTOMER_AREA_PAGE', $objInfoUpdate->name, array('jsSafe'=>true, 'interpretBackSlashes'=>true, 'script'=>false));
									$objInfoUpdate->type 				= 'theme';
									$requiredElements []				= $objInfoUpdate;
								}
							}
						}
						else
						{
							foreach ($sampleThemes as $sampleTheme)
							{
								$elements[]							= $sampleTheme;
								$objInfoUpdate 						= new stdClass();
								$objInfoUpdate->identify_name 		= $sampleTheme;
								$objInfoUpdate->name 				= ucwords(str_replace('theme', 'theme ', $sampleTheme));
								$objInfoUpdate->text 				= JText::sprintf('MAINTENANCE_SAMPLE_DATA_YOU_CAN_DOWNLOAD_VIA_CUSTOMER_AREA_PAGE', $objInfoUpdate->name, array('jsSafe'=>true, 'interpretBackSlashes'=>true, 'script'=>false));
								$objInfoUpdate->type 				= 'theme';
								$requiredElements []				= $objInfoUpdate;
							}
						}


						/*Check themes*/
						if (count($requiredElements))
						{
							$allElements 		= implode(',', $elements);
							$msg 				= stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_FOLLOWING_ELEMENTS_ARE_NOT_INSTALLED', true));
							$objJSNLightCart 	= JSNISFactory::getObj('classes.jsn_is_lightcart');
							$lightCartErrorCode = $objJSNLightCart->getErrorCode('DEFAULT', false);
							echo json_encode(array('light_cart_error_code' => $lightCartErrorCode, 'install' => false, 'message' => $msg, 'redirect_link'=>$link, 'required_elements'=>$requiredElements, 'elements'=>$allElements, 'total_elements'=>count($elements), 'commercial'=>$commercial));
							exit();
						}
						/*Check version theme*/
						if (count($objReadXmlDetail->_themeVersion))
						{
							$objJSNTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
							foreach ($objReadXmlDetail->_themeVersion as $key=>$value)
							{
								$themeInfo 		= $objJSNTheme->getThemeInfo($key);
								if ($themeInfo->version != $value)
								{
									$msg = stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_THEME_VERSION_ERROR', true));
									echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
									exit();
								}
							}
						}
						/*Check version source*/
						if (count($objReadXmlDetail->_sourceVersion))
						{
							$objJSNShowlistSource 	= JSNISFactory::getObj('classes.jsn_is_showlistsource');
							foreach ($objReadXmlDetail->_sourceVersion as $key=>$value)
							{
								$sourceInfo 		= $objJSNShowlistSource->getSourceInfo($key);
								if ($sourceInfo->version != $value)
								{
									$msg = stripslashes(JText::_('MAINTENANCE_SAMPLE_DATA_SOURCE_VERSION_ERROR', true));
									echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
									exit();
								}
							}
						}
						/*Check version source*/
						/*Check version theme*/
						if ($fileName != '')
						{
							$sampleData->deleteISDFile($fileName);
						}
						$session->set($uploadIdentifier, false, 'jsnimageshow');
						$session->set($packagenameIdentifier, '', 'jsnimageshow');
						$sampleData->executeInstallSampleData($dataInstall);
						$msg = JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_SAMPLE_DATA_SUCCESSFULLY', true);
						echo json_encode(array('install' => true, 'message'=>$msg));
						exit();
					}
					else
					{
						$sampleData->returnError('false','');
					}
				}
				else
				{
					$msg = JText::_('MAINTENANCE_SAMPLE_DATA_UNPACK_SAMPLEDATA_FALSE', true);
					echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
					exit();
				}
			}
			else
			{
				$msg = JText::sprintf('MAINTENANCE_SAMPLE_DATA_FOLDER_MUST_HAVE_WRITABLE_PERMISSION', DS.'tmp');
				echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
				exit();
			}
		}
	}

	public function installRequiredPlugin()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$elementType = JRequest::getVar('element_type');
		$file = JRequest::getVar('pluign_file', null, 'files', 'array');

		if ($elementType == 'imagesource')
		{
			$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installimagesource');
		}
		else
		{
			$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installshowcasetheme');
		}
		$objInstallSource->installManual($file);
		$this->setRedirect('index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data#data-sample-installation');
	}

	/**
	 * Install required plugin when restore database, then run restore database
	 */
	/*public function installJSNPluginForRestore()
	 {
		JRequest::checkToken() or jexit('Invalid Token');
		$elementType = JRequest::getVar('element_type');
		$file = JRequest::getVar('pluign_file', null, 'files', 'array');

		if ($elementType == 'imagesource')
		{
		$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installimagesource');
		}
		else
		{
		$objInstallSource = JSNISFactory::getObj('classes.jsn_is_installshowcasetheme');
		}

		$objInstallSource->installManual($file);

		// run restore
		$session 	   = JFactory::getSession();
		$restoreResult = $session->get('JSNISRestore');
		$backupFile = @$restoreResult['requiredInstallData']['backup_file'];

		JRequest::setVar( 'filedata', $backupFile, 'files', 'array' );
		$this->restore(true);
		}*/
}