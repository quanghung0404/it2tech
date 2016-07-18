<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: images.php 17017 2012-10-15 04:12:15Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
class ImageShowControllerImages extends JControllerLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}

	function display($cachable = false, $urlparams = false)
	{
		$document 		= &JFactory::getDocument();
		$viewType		= $document->getType();
		$viewName 		= JRequest::getCmd('view', 'images');
		$view 			= &$this->getView( $viewName, $viewType);

		$view->setLayout('default');
		$view->display();
	}

	function close()
	{
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$link = 'index.php?option=com_imageshow';
		$mainframe->redirect($link);
	}

	function loadSourceImages()
	{
		$cateName		= JRequest::getVar('cateName', '');
		$showListID		= JRequest::getVar('showListID', '');
		$sourceType		= JRequest::getVar('sourceType', '');
		$sourceName		= JRequest::getVar('sourceName', '');
		$selectMode		= JRequest::getVar('selectMode', '');
		$pagination		= JRequest::getVar('pagination', '');
		$offset			= JRequest::getVar('offset', 0);
		$progressBarRandomNumber			= JRequest::getVar('progressBarRandomNumber', 0);
		$newProgressBarContainerId = 'progress_bar_conatainer_'.$progressBarRandomNumber;
		$newProgressBarId = 'progress_bar_'.$progressBarRandomNumber;
		$imageSource	= JSNISFactory::getSource($sourceName, $sourceType, $showListID);
		$config			= array('album'=>$cateName,'showlistid'=>$showListID,'offset'=>$offset);
		if($selectMode == "sync" && $sourceType != "external")
		{
			$config		= array_merge(array('syncOfInternal' => true), $config);
		}
		$images = $imageSource->loadImages($config);
		if($offset==0 && $pagination)
		$countImages = $imageSource->countImages($cateName);

		if ($selectMode == 'sync')
		{
			$syncIsSelected = $imageSource->checkSync($cateName);
		}
		else
		{
			$syncIsSelected = '';
		}
		include(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'views'.DS.'images'.DS.'tmpl'.DS.'images.php');
		jexit();
	}

	function saveshowlist()
	{
		global $objectLog;
		$session 		= JFactory::getSession();
		$identifier		= md5('jsn_imageshow_inserted_images_identify_name');
		$session->set($identifier, array(), 'jsnimageshowsession');
		$showListID  = JRequest::getVar('showListID', '');
		$sourceName  = JRequest::getVar('sourceName', '');
		$sourceType  = JRequest::getVar('sourceType', '');
		$images      = JRequest::getVar('images', '', 'post', 'string', JREQUEST_ALLOWHTML);
		if (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)
		{
			$images = stripslashes($images);
		}
		
		$images		 = json_decode($images);
		$imageSource = JSNISFactory::getSource($sourceName, $sourceType, $showListID);
		$syncMode	 = JRequest::getVar('syncMode','');
		if ($syncMode == 'sync')
		{
			$imageSource->resetShowListImages();
			$sync = 1;
		}
		else
		{
			$sync = 0;
		}
		$infoInsert 				= array();
		$infoInsert['showlistID']	= $showListID;

		foreach ($images as $key => $img)
		{
			$ImgDetail = $img->img_detail;
			$img->imgid									= urldecode($img->imgid);
			$infoInsert['imgID'][]						= null;
			$infoInsert['imgExtID'][]					= $img->imgid;
			$infoInsert['order'][$img->imgid]			= $img->order + 1;
			$infoInsert['albumID'][$img->imgid]			= urldecode($img->albumid);
			if ($sourceName == 'folder')
			{
				$infoInsert['imgSmall'][$img->imgid]		= urldecode($img->img_thumb);
			}
			else
			{
				$infoInsert['imgSmall'][$img->imgid]		= $ImgDetail->image_small;
			}
			$infoInsert['imgMedium'][$img->imgid]		= $ImgDetail->image_medium;
			$infoInsert['imgBig'][$img->imgid]			= $ImgDetail->image_big;
			$infoInsert['imgTitle'][$img->imgid]		= $ImgDetail->image_title;
			$infoInsert['imgLink'][$img->imgid]			= $ImgDetail->image_link;
			$infoInsert['imgDescription'][$img->imgid]	= empty($ImgDetail->image_description)?' ':$ImgDetail->image_description;
			$infoInsert['customData'][$img->imgid]		= 0;

		}
		$session->set($identifier, $images, 'jsnimageshowsession');
		$objJSNImages	= JSNISFactory::getObj('classes.jsn_is_images');
		$objJSNImages->addImages($infoInsert);
		jexit();
	}
	/**
	 * process delete image from showlist
	 */
	function deleteimageshowlist()
	{
		$showListID  = JRequest::getVar('showListID', '');
		$sourceName  = JRequest::getVar('sourceName', '');
		$sourceType  = JRequest::getVar('sourceType', '');
		$imageIDs	 = urldecode(JRequest::getVar('imageIDs',''));
		if (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)
		{
			$imageIDs = stripslashes($imageIDs);
		}
		$imageIDs	 = json_decode($imageIDs, true);
		$imageSource = JSNISFactory::getSource($sourceName, $sourceType, $showListID);
		$config['imgExtID']   = $imageIDs; //array('0'=>$imageID);
		$config['showlistID'] = $showListID;
		$imageSource->removeImages($config);
		jexit();
	}
	function savesync()
	{
		$objJSNImages		= JSNISFactory::getObj('classes.jsn_is_images');
		$showlistID 		= JRequest::getVar('showlist_id');
		$AlbumID 			= array(JRequest::getVar('album_extid'));
		$objJSNImages->saveSyncAlbum($showlistID,$AlbumID);
		jexit();
	}
	function removesync(){
		$syncCate    = JRequest::getVar('album_extid', '');
		$showListID  = JRequest::getVar('showlist_id', '');
		$sourceType  = JRequest::getVar('sourceType', '');
		$sourceName  = JRequest::getVar('sourceName', '');
		$imageSource = JSNISFactory::getSource( $sourceName, $sourceType, $showListID );
		$imageSource->removeSync($syncCate);
		jexit();
	}

	/**
	 * Ajax function remove all sync (click button Sync to disable sync)
	 */
	function removeallSync()
	{
		$showListID  = JRequest::getVar('showListID', '');
		$sourceName  = JRequest::getVar('sourceName', '');
		$sourceType  = JRequest::getVar('sourceType', '');
		$imageSource = JSNISFactory::getSource($sourceName, $sourceType, $showListID);
		$syncMode	 = JRequest::getVar('syncMode','');
		$imageSource->resetShowListImages();
		jexit();
	}

	/**
	 *
	 * Check sync
	 */
	public function checksyncalbum()
	{
		$syncCate    = JRequest::getVar('syncCate', '');
		//$syncCate	 = str_replace("cat_","",$syncCate);
		$showListID  = JRequest::getVar('showListID', '');
		$sourceType  = JRequest::getVar('sourceType', '');
		$sourceName  = JRequest::getVar('sourceName', '');

		$imageSource = JSNISFactory::getSource( $sourceName, $sourceType, $showListID );

		if ($imageSource->getShowlistMode() =='sync' && $imageSource->checkSync($syncCate) ){
			$status = 'is_selected';
		}else{
			$status  = 'none';
		}

		jexit($status);
	}

	/**
	 * Create image thumb for local folder source when preview image
	 *
	 * @return JSON-type result
	 */
	function createThumbForPreview()
	{
		$objJSNThumbnail = JSNISFactory::getObj('classes.jsn_is_imagethumbnail');
		$post = JRequest::get('post');
		$JSNISThumbsPath	= JPATH_ROOT.DS.'images'.DS.'jsn_is_thumbs'.DS;
		$imagePath			= JPATH_ROOT.DS.str_replace('/', DS, urldecode($post['imagePath']));

		$tmpReturnedPath = str_replace('/', DS, urldecode($post['imagePath']));
		$returnedPath = JURI::root().str_replace(DS, "/", $tmpReturnedPath);
		// check jsn_is_thumbs folder, if not existed->created
		if (!is_writable(JPATH_ROOT.DS.'images'.DS))
		{
			$data = array("status"=>false, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
			echo json_encode($data);
			exit();
		}

		if (!JFolder::exists($JSNISThumbsPath))
		{
			if(!$objJSNThumbnail->createThumbnailFolder(JPATH_ROOT.DS.'images'.DS, $JSNISThumbsPath))
			{
				$data = array("status"=>false, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
				echo json_encode($data);
				exit();
			}
		}

		if (JFolder::exists($JSNISThumbsPath))
		{
			if (is_writable($JSNISThumbsPath))
			{
				//create a folder that contains all images previewed
				$imageFolderPath = $JSNISThumbsPath.str_replace('/', DS, urldecode($post['folderName']));
				$uriPath   		 = 'images'.DS.'jsn_is_thumbs'.DS.urldecode($post['folderName']).DS.urldecode($post['imageName']);

				if (!JFolder::exists($imageFolderPath))
				{
					if(!$objJSNThumbnail->createThumbnailFolder($JSNISThumbsPath, $imageFolderPath))
					{
						$data = array("status"=>false, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
						echo json_encode($data);
						exit();
					}
				}

				if (JFolder::exists($imageFolderPath))
				{
					if (!JFile::exists($imageFolderPath.DS.urldecode($post['imageName'])))
					{
						if(!$objJSNThumbnail->createFileThumbnail($imagePath, $imageFolderPath.DS.urldecode($post['imageName'])))
						{
							$data = array("status"=>false, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
							echo json_encode($data);
							exit();
						}
						else
						{
							$tmpReturnedPath = $uriPath;
							$returnedPath = JURI::root().str_replace(DS, "/", $tmpReturnedPath);
							$data = array("status"=>true, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
							echo json_encode($data);
							exit();
						}
					}
					elseif (JFile::exists($imageFolderPath.DS.urldecode($post['imageName'])) && @filemtime($imagePath) > @filemtime($imageFolderPath.DS.urldecode($post['imageName'])))
					{
						JFile::delete($imageFolderPath.DS.urldecode($post['imageName']));
						if(!$objJSNThumbnail->createFileThumbnail($imagePath, $imageFolderPath.DS.urldecode($post['imageName'])))
						{
							$data = array("status"=>false, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
							echo json_encode($data);
							exit();
						}
						else
						{
							$tmpReturnedPath = $uriPath;
							$returnedPath = JURI::root().str_replace(DS, "/", $tmpReturnedPath);
							$data = array("status"=>true, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
							echo json_encode($data);
							exit();
						}
					}
					else
					{
						$tmpReturnedPath = $uriPath;
						$returnedPath = JURI::root().str_replace(DS, "/", $tmpReturnedPath);
						$data = array("status"=>true, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
						echo json_encode($data);
						exit();
					}
				}
			}
			else
			{
				$data = array("status"=>true, "image_path"=>$returnedPath, "encode_image_path"=>urlencode(str_replace(DS, "/", $tmpReturnedPath)));
				echo json_encode($data);
				exit();
			}
		}
		exit();
	}

	/**
	 * Render the javascript function to recreate image thumb
	 * @return a javascript functions serial
	 */
	function getScriptCheckThumb()
	{
		$objImage	= JSNISFactory::getObj('classes.jsn_is_images');
		$task 		= JRequest::getVar('task');
		echo $objImage->$task();
		jexit();
	}

	/**
	 * Check whether image thumb exist or not
	 *
	 * @return JSON-type result
	 */
	function checkThumb()
	{
		$objImage	= JSNISFactory::getObj('classes.jsn_is_images');
		$task 		= JRequest::getVar('task');
		echo $objImage->$task();
		jexit();
	}
}
?>