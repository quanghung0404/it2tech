<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: internal_source_folder.php 16077 2012-09-17 02:30:25Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
class JSNInternalSourceFolder extends JSNImagesSourcesInternal
{
	private $_folder 				= 'images/';
	private $_thumbFolder 			= 'images/jsn_is_thumbs';
	private $_hideImageSpecialName  = false;
	private $_validFolderName 		= '/^[A-Za-z0-9_-\s]+[A-Za-z0-9_\.-\s]*([\\\\\/\s][A-Za-z0-9_-\s]+[A-Za-z0-9_\.-\s]*)*$/';
	private $_validImageName 		= '/^[A-Za-z0-9_-\s()]+[A-Za-z0-9_\.-\s()]*([\\\\\/\s][A-Za-z0-9_-\s()]+[A-Za-z0-9_\.-\s()]*)*$/';

	public function addOriginalInfo($config = array())
	{
		$data = array();

		if (is_array($this->_data['images']))
		{
			foreach ($this->_data['images'] as $img)
			{
				if ($img->custom_data == 1)
				{
					$img->original_title 	   = substr($img->image_big, strrpos($img->image_big,'/') + 1);
					$img->original_description = '';
					$img->original_link		   = JURI::root().$img->image_big;
				}
				else
				{
					$img->original_title 		= $img->image_title;
					$img->original_description 	= $img->image_description;
					$img->original_link			= $img->image_link;
				}

				$data[] = $img;
			}
		}

		$this->_data['images'] = $data;
	}

	public function getCategories($config = array('showlist_id' => 0))
	{
		$objJSNImages   = JSNISFactory::getObj('classes.jsn_is_images');
		$syncAlbum      = $objJSNImages->getSyncAlbumsByShowlistID($config['showlist_id']);
		$syncAlbum 	   	= (count($syncAlbum) > 0) ? $syncAlbum : array();
		$path       	= JPath::clean(JPATH_ROOT.DS.$this->_folder);

		if (!is_dir($path)) {
			return false;
		}

		$xmlObj = new JXMLElement('<node></node>');
		$xmlObj->addAttribute('label', 'images');
		$xmlObj->addAttribute('data', 'images');
		$xmlObj->addAttribute('type', 'root');

		$this->drawTree($xmlObj, $path, $syncAlbum);
		return $xmlObj->asFormattedXML();
	}

	function drawTree($xmlObj, $path, $syncAlbum)
	{
		$dir = @opendir($path);
		$folders = array();

		while (false !== ($file = @readdir($dir)))
		{
			if (is_dir($path.DS.$file) && $file != '.' && $file != '..')
			{
				if (JPATH_ROOT == '') {
					$folderLevel = substr_replace($path, '', 0, 1);
				} else {
					$folderLevel = str_replace(JPATH_ROOT.DS, '', $path);
				}

				$folderLevel = JPath::clean($folderLevel.DS.$file);
				$folderLevel = str_replace(DS, '/', $folderLevel);
				$syncStatus  = (in_array($folderLevel, $syncAlbum)) ? 'checked' : 'unchecked';

				$object 			= new stdClass();
				$object->label 		= $file;
				$object->data 		= $folderLevel;
				$object->state 		= $syncStatus;
				$object->childPath  = $path.DS.$file;

				$folders[$file] = $object;
			}
		}

		ksort($folders);

		foreach ($folders as $folder)
		{
			if($folder->label != 'jsn_is_thumbs')
			{
				$child = $xmlObj->addChild('node');
				$child->addAttribute('label', $folder->label);
				$child->addAttribute('data', $folder->data);
				$child->addAttribute('state', $folder->state);
				JSNInternalSourceFolder::drawTree($child, $folder->childPath, $syncAlbum);
			}
		}
	}

	public function removeImages($config =array())
	{
		$imgExtID 	= $config['imgExtID'];
		$showListID = $config['showlistID'];

		$db 				= JFactory::getDBO();
		$objJSNThumbnail 	= JSNISFactory::getObj('classes.jsn_is_imagethumbnail');

		if (is_array($imgExtID) && count($imgExtID))
		{
			$arrayImageThumb 	= array();
			$imageThumb 		= array();

			for ($i = 0 ; $i < count($imgExtID); $i++)
			{
				$imageThumb[] 	= $objJSNThumbnail->getOnceThumbImage(@$imgExtID[$i], $showListID);
				$query 			= 'DELETE FROM #__imageshow_images WHERE image_extid=\''.@$imgExtID[$i].'\' AND showlist_id='.$showListID;

				$db->setQuery($query);
				$result = $db->query();
			}

			if ($result)
			{
				if (is_array($imageThumb) && count($imageThumb) && $imageThumb != null)
				{
					foreach ($imageThumb as $value) {
						$arrayImageThumb [] = $value['image_small'];
					}

					$objJSNThumbnail->deleteThumbImage($arrayImageThumb);
				}

				return true;
			}

			return false;
		}

		return false;
	}

	public function removeAllImages($config = array())
	{
		if ($config['showlist_id'])
		{
			$objJSNThumb = JSNISFactory::getObj('classes.jsn_is_imagethumbnail');
			$objJSNThumb->deleteAllThumbnailByShowlistID($config['showlist_id']);
		}

		parent::removeAllImages($config);
	}

	public function loadImages($config = array())
	{
		$objJSNParameter 	= JSNISFactory::getObj('classes.jsn_is_parameter');
		$parameters    		= $objJSNParameter->getParameters();
		$config 			= array_merge(array('hideImageSpecialName' => false), $config);
		if(isset($config['syncOfInternal']))
		{
			$num = 1000;
		}
		else if (is_null(@$parameters->enable_update_checking) || (int) $parameters->number_of_images_on_loading == 0)
		{
			$num = 30;
		}
		else
		{
			$num = (int) $parameters->number_of_images_on_loading;
		}
		$offset 	= @$config['offset'];

		if (!isset($config['album'])) {
			return false;
		}

		$data	 		= new stdClass();
		$arrayImageLoad = array();
		$data->images   = $arrayImageLoad;

		$folderPath = str_replace(DS, '/', $config['album']);
		$path 		= str_replace(DS, '/', JPATH_ROOT.DS.$folderPath);

		$matchesInvalidFolder = array();

		preg_match($this->_validFolderName, (string) $folderPath, $matchesInvalidFolder);

		if (count($matchesInvalidFolder) == 0) {
			$this->_error 	 = true;
			$this->_errorMsg = JText::_('SHOWLIST_FOLDER_SOURCE_INVALID_FOLDER_NAME');
			return false;
		}

		if (!JFolder::exists($path)) {
			$this->_error 	 = true;
			$this->_errorMsg = JText::_('FOlDER_SOURCE_FOLDER_NOT_EXISTS');
			return false;
		}

		$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');

		$dataFolder	= $objJSNUtils->getImageInPath($path);
		if (!count($dataFolder)) return $data;
		$imageData = array_slice($dataFolder->images, $offset, $num);
		foreach ($imageData as $image)
		{
			$matchesInvalidImage = array();
			$imageInfo 			 = pathinfo($image);
			preg_match($this->_validImageName, (string)$imageInfo['basename'], $matchesInvalidImage);
			$ImageBaseName  				= str_replace(DS, '/', $folderPath.DS.$imageInfo['basename']);
			$realPath 						= str_replace('/', DS,  $folderPath.DS.$imageInfo['basename']);
			$objImage 						= new stdClass();
			$objImage->image_title 			= $imageInfo['basename'];
			$objImage->image_extid 			= $ImageBaseName;
			$objImage->album_extid 			= $folderPath;
			$objImage->image_link 			= str_replace(DS, '/', JURI::root().$folderPath.DS.$imageInfo['basename']);
			$objImage->image_description 	= '';
			$objImage->image_small 			= $ImageBaseName;
			$objImage->image_medium 		= $ImageBaseName;
			$objImage->image_big			= $ImageBaseName;
			$objImage->exif_data 			= '';
			if (count($matchesInvalidImage) == 0)
			{
				$objImage->invalid_file_name = true;
			}

			if ($config['hideImageSpecialName'] == true  && count($matchesInvalidImage) == 0){

			}else{
				$arrayImageLoad[] = $objImage;
			}
		}

		$data->images = $arrayImageLoad;

		return $data;
	}

	public function saveImages($config = array())
	{
		parent::saveImages($config);

		$config 			= $this->_data['saveImages'];
		$imgExtIDs 			= $config['imgExtID'];
		$objJSNThumbnail 	= JSNISFactory::getObj('classes.jsn_is_imagethumbnail');
		$objJSNExif			= JSNISFactory::getObj('classes.jsn_is_exifinternalsource');
		$imagesTable 		= JTable::getInstance('images', 'Table');
		$memory 			= (int) ini_get('memory_limit');

		if ($memory == 0) {
			$memory = 8;
		}

		if (count($imgExtIDs))
		{
			$objJSNImages 	= JSNISFactory::getObj('classes.jsn_is_images');
			$ordering 		= $objJSNImages->getMaxOrderingByShowlistID($config['showlistID']);

			if (count($ordering) < 0 || is_null($ordering)) {
				$ordering = 1;
			} else {
				$ordering = $ordering[0] + 1;
			}

			$result = false;

			foreach ($imgExtIDs as $imgExtID)
			{
				$realPath 		= str_replace('/', DS, @$config['imgBig'][$imgExtID]);
				$copiedRealPath = $realPath;
				$realPath 		= JPATH_ROOT.DS.$realPath;
				$imageSize 		= @filesize($realPath);
				/*if ($objJSNThumbnail->checkGraphicLibrary())
				 {
					$imageThumbPath	= $this->_thumbFolder;
					$imageName 		= explode('/', @$imgExtID);
					$imageName 		= end($imageName);
					$imageName 		= uniqid('').rand(1, 99).'_'.$imageName;

					if	(!$objJSNThumbnail->createThumbnail($realPath, $imageName)) {
					$imageThumbPath = @$config['imgBig'][$imgExtID];
					} else {
					$imageThumbPath = $imageThumbPath.'/'.$imageName;
					}

					$imagesTable->showlist_id 	= $config['showlistID'];
					$imagesTable->image_extid 	= $imgExtID;
					$imagesTable->album_extid 	= $config['albumID'][$imgExtID];
					$imagesTable->image_small 	= $imageThumbPath;
					$imagesTable->image_medium 	= $imageThumbPath;
					$imagesTable->image_big		= $config['imgBig'][$imgExtID];
					$imagesTable->image_title   = $config['imgTitle'][$imgExtID];
					$imagesTable->image_link 	= $config['imgLink'][$imgExtID];
					$imagesTable->image_description = $config['imgDescription'][$imgExtID];
					$imagesTable->ordering		= $ordering;
					$imagesTable->custom_data 	= $config['customData'][$imgExtID];
					$imagesTable->image_size 	= @$imageSize;
					}
					else
					{*/
				$imagesTable->showlist_id 	= $config['showlistID'];
				$imagesTable->image_extid 	= $imgExtID;
				$imagesTable->album_extid 	= $config['albumID'][$imgExtID];
				$imagesTable->image_small 	= $config['imgSmall'][$imgExtID];
				$imagesTable->image_medium 	= $config['imgMedium'][$imgExtID];
				$imagesTable->image_big		= $config['imgBig'][$imgExtID];
				$imagesTable->image_title   = $config['imgTitle'][$imgExtID];
				$imagesTable->image_description = $config['imgDescription'][$imgExtID];
				$imagesTable->image_link 	= $config['imgLink'][$imgExtID];
				$imagesTable->ordering		= $ordering;
				$imagesTable->custom_data 	= $config['customData'][$imgExtID];
				$imagesTable->image_size 	= @$imageSize;
				$imagesTable->exif_data 	= $objJSNExif->renderData($copiedRealPath);

				/*}*/

				$result = $imagesTable->store(array('replaceSpace' => true));
				$imagesTable->image_id = null;
				$ordering ++;
			}

			$memoryString = $memory.'M';
			@ini_set('memory_limit', $memoryString);

			if($result) {
				return true;
			}

			return false;
		}
		return false;
	}

	public function getSyncImages($config = array())
	{
		$config 	 = array_merge(array('limitEdition' => true), $config);
		$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
		$db 		 = JFactory::getDBO();

		$query = 'SELECT i.album_extid
				  FROM #__imageshow_images as i
				  INNER JOIN #__imageshow_showlist as sl ON sl.showlist_id = i.showlist_id
				  WHERE i.sync = 1
				  AND sl.published = 1
				  AND i.showlist_id = '.(int)$config['showlist_id']. '
				  GROUP BY i.album_extid
				  ORDER BY i.image_id';

		$db->setQuery($query);

		$albums 	 = $db->loadObjectList();
		$images		 = array();
		$limitStatus = $objJSNUtils->checkLimit();

		if (count($albums) > 0)
		{
			$albumLimit = 0;
			foreach ($albums as $album)
			{

				$data 		  = $this->loadSyncImages(array('album' => $album->album_extid, 'hideImageSpecialName' => true));
				$imagesFolder = $data->images;

				if (is_array($imagesFolder)) {
					$images = array_merge($images , $imagesFolder);
				}

				$albumLimit++;
				if ($limitStatus == true && $albumLimit >= 3 && $config['limitEdition'] == true) {
					break;
				}
			}

			if (count($images) > 0 && $limitStatus == true && $config['limitEdition'] == true) {
				$images = array_splice($images, 0, 10);
			}
		}

		$this->_data['images'] = $images;
	}

	public function getImages($config = array())
	{
		parent::getImages($config);

		return $this->_data['images'];
	}

	public function getImages2JSON($config = array())
	{
		parent::getImages2JSON($config);

		$arrayImage = array();

		if (count($this->_data['images']))
		{
			foreach ($this->_data['images'] as $image)
			{
				$imageDetailObj 				= new stdClass();
				$imageDetailObj->thumbnail		= $config['URL'].$image->image_small;
				$imageDetailObj->image			= $config['URL'].$image->image_big;
				$imageDetailObj->title 			= $image->image_title;
				$imageDetailObj->description 	= (!is_null($image->image_description)) ? $image->image_description: '';
				$imageDetailObj->link 			= $image->image_link;
				$imageDetailObj->exif_data		= $image->exif_data;
				$arrayImage[] 					= $imageDetailObj;
			}
		}

		return $arrayImage;
	}

	public function createThumb($config = array())
	{
		$objJSNThumbnail = JSNISFactory::getObj('classes.jsn_is_imagethumbnail');
		$data = new stdClass();

		if (isset($config['image_extid']))
		{
			$realPath 	= str_replace('/', DS, @$config['image_big']);
			$realPath 	= JPATH_ROOT.DS.$realPath;
			$data->image_extid = $config['image_extid'];
			$data->album_extid = $config['album_extid'];

			if ($objJSNThumbnail->checkGraphicLibrary())
			{
				$imageName = explode('/', @$config['image_extid']);
				$imageName = end($imageName);
				$imageName = uniqid('').rand(1, 99).'_'.$imageName;

				if	(!$objJSNThumbnail->createThumbnail($realPath, $imageName))
				{

					$data->image_small = @$config['image_big'];
				}
				else
				{
					$data->image_small = $this->_thumbFolder.'/'.$imageName;
				}
			}
			else
			{
				$data->image_small = @$config['image_big'];
			}
		}

		return $data;
	}

	// render ajax script to check thumb
	public function renderScriptcheckThumb()
	{
		$html = '';

		$this->getImages(array('showlist_id' => $this->_source['showlistID']));

		$limitCheck = count($this->_data['images']);

		if ($this->_syncmode == false && $limitCheck) // only check thumb when sync image feature is off
		{
			$objJSNThumb = JSNISFactory::getObj('classes.jsn_is_imagethumbnail');
			$thumbIDs = array();

			foreach ($this->_data['images'] as $image)
			{
				$thumbID = $objJSNThumb->getImagesNeedCreateThumb($image);
				$canCreateThumb = $this->canCreatThumb($image->image_small);
				if ($thumbID && $canCreateThumb) {
					$thumbIDs[] = $thumbID;
				}
			}

			$limitCheck = count($thumbIDs);
			$label 	    = JText::_('SHOWLIST_FOLDER_SOURCE_RECREATING_THUMBNAILS');

			if ($limitCheck)
			{
				$html .= 'JSNISInternalFolder.listAjaxCheckThumb = {};JSNISInternalFolder.disableImageManager();
						JSNISInternalFolder.createCheckThumbBox("'.$label.'"); ';

				$count  = 0;

				foreach ($thumbIDs as $thumbID) {
					$count++;
					$html .= 'JSNISInternalFolder.ajaxCheckThumb('.(int)$limitCheck.', '. (int)$count.', '. (int)$thumbID.');';
				}

				$html .= 'JSNISInternalFolder.triggerAjaxCheckThumb(1);';

			} else {
				$html .= ' JSNISImageShow.checkThumbCallBack(); ';
			}
		}
		else
		{
			$html .= ' JSNISImageShow.checkThumbCallBack(); ';
		}

		return $html;
	}

	public function loadScript()
	{
		$objJSNMedia = JSNISFactory::getObj('classes.jsn_is_media');
		$objJSNMedia->addScript(JUri::root(true) . '/administrator/components/com_imageshow/imagesources/source_folder/assets/js/internal_source_folder.js');
	}

	public function loadSyncImages($config = array())
	{
		$config 	= array_merge(array('hideImageSpecialName' => false), $config);

		if (!isset($config['album'])) {
			return false;
		}

		$data	 		= new stdClass();
		$arrayImageLoad = array();
		$data->images   = $arrayImageLoad;

		$folderPath = str_replace(DS, '/', $config['album']);
		$path 		= str_replace(DS, '/', JPATH_ROOT.DS.$folderPath);
		$thumbnailPath		 = str_replace(DS, '/', 'images'.DS.'jsn_is_thumbs'.DS.$folderPath);
		$thumbnailFolderPath =  str_replace(DS, '/', JPATH_ROOT.DS.$thumbnailPath);
		$matchesInvalidFolder = array();

		preg_match($this->_validFolderName, (string) $folderPath, $matchesInvalidFolder);

		if (count($matchesInvalidFolder) == 0) {
			$this->_error 	 = true;
			$this->_errorMsg = JText::_('SHOWLIST_FOLDER_SOURCE_INVALID_FOLDER_NAME');
			return false;
		}

		if (!JFolder::exists($path)) {
			$this->_error 	 = true;
			$this->_errorMsg = JText::_('FOlDER_SOURCE_FOLDER_NOT_EXISTS');
			return false;
		}

		$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');

		$dataFolder	= $objJSNUtils->getImageInPath($path);
		$dataThumbnail = $objJSNUtils->getImageInPath($thumbnailFolderPath);

		foreach ($dataFolder->images as $image)
		{
			$matchesInvalidImage = array();
			$imageInfo 			 = pathinfo($image);

			preg_match($this->_validImageName, (string)$imageInfo['basename'], $matchesInvalidImage);

			$ImageBaseName  				= str_replace(DS, '/', $folderPath.DS.$imageInfo['basename']);
			$realPath 						= str_replace('/', DS,  $folderPath.DS.$imageInfo['basename']);
			$objImage 						= new stdClass();
			$objImage->image_title 			= $imageInfo['basename'];
			$objImage->image_extid 			= $ImageBaseName;
			$objImage->album_extid 			= $folderPath;
			$objImage->image_link 			= str_replace(DS, '/', JURI::root().$folderPath.DS.$imageInfo['basename']);
			$objImage->image_description 	= '';
			$thumbnailImage					= $ImageBaseName;
			if(count(@$dataThumbnail->images))
			{
				foreach(@$dataThumbnail->images as $thumbnail)
				{
					$thumbnailInfo 			 = pathinfo($thumbnail);
					if($thumbnailInfo['basename'] == $imageInfo['basename'])
					{
						$thumbnailImage			= str_replace(DS, '/', $thumbnailPath.DS.$imageInfo['basename']);
						break;
					}
				}
			}
			$objImage->image_small 			= $thumbnailImage;
			$objImage->image_medium 		= $ImageBaseName;
			$objImage->image_big			= $ImageBaseName;
			$objImage->exif_data 			= '';
			if (count($matchesInvalidImage) == 0)
			{
				$objImage->invalid_file_name = true;
			}

			if ($config['hideImageSpecialName'] == true  && count($matchesInvalidImage) == 0){

			}else{
				$arrayImageLoad[] = $objImage;
			}
		}

		$data->images = $arrayImageLoad;

		return $data;
	}

	function canCreatThumb($string)
	{
		if ($string == '') return false;
		if (preg_match('#jsn_is_thumbs#', $string)) return true;
		return false;
	}

	function countImages($albumId){
		$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
		$path 			= str_replace(DS, '/', JPATH_ROOT.DS.$albumId);
		$dataFolder		= $objJSNUtils->getImageInPath($path);
		return count($dataFolder->images);
	}
}
