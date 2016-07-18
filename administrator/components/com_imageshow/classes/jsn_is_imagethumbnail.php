<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_imagethumbnail.php 15501 2012-08-25 10:24:20Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
include_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_imageutils.php');
class JSNISImageThumbNail
{
	public static function getInstance()
	{
		static $instanceThumbnail;

		if ($instanceThumbnail == null)
		{
			$instanceThumbnail = new JSNISImageThumbNail();
		}
		return $instanceThumbnail;
	}

	function createThumbnailFolder($folderOriginal, $folderThumbnail)
	{
		$folderPermissions = 0755;
		if (JFolder::exists($folderOriginal))
		{
			if (strlen($folderThumbnail) > 0)
			{
				$folderThumbnail = JPath::clean($folderThumbnail);
				if (!JFolder::exists($folderThumbnail) && !JFile::exists($folderThumbnail))
				{
					switch((int)$folderPermissions)
					{
						case 777:
							@JFolder::create($folderThumbnail, 0777 );
							break;
						case 705:
							@JFolder::create($folderThumbnail, 0705 );
							break;
						case 666:
							@JFolder::create($folderThumbnail, 0666 );
							break;
						case 644:
							@JFolder::create($folderThumbnail, 0644 );
							break;
						case 755:
						default:
							@JFolder::create($folderThumbnail, 0755 );
							break;
					}
					if (isset($folderThumbnail))
					{
						$content = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
						@JFile::write($folderThumbnail.DS."index.html", $content);
					}
					if (!JFolder::exists($folderThumbnail))
					{
						return false;
					}
				}
			}
		}
		return true;
	}
	function createFileThumbnail($fileOriginal, $fileThumbnail)
	{
		$objJSNISGD2 = new JSNISGD2();
		$crop 		= 0;
		$fileResize	= array("width"=>150, "height"=>150);
		if (JFile::exists($fileOriginal))
		{
			if (!JFile::exists($fileThumbnail))
			{
				list($width, $height) = getimagesize($fileOriginal);
				if ($width > $fileResize['width'] || $height > $fileResize['height'])
				{
					$imageMagic = $objJSNISGD2->resizeImage($fileOriginal, $fileThumbnail, $fileResize['width'] , $fileResize['height'], $crop);
				}
				else
				{
					$imageMagic = $objJSNISGD2->resizeImage($fileOriginal, $fileThumbnail, $width , $height, $crop);
				}
				if ($imageMagic)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
		return true;
	}

	function createThumbnail($fileOriginal, $fileNameThumb)
	{
		$fileOriginal = str_replace('/', DS, $fileOriginal);
		$tmpPath	  = str_replace(JPATH_ROOT.DS, '', dirname($fileOriginal));
		if (JFile::exists($fileOriginal))
		{
			$imageThumbPath	= JPATH_ROOT . DS . 'images' . DS . 'jsn_is_thumbs' . DS. $tmpPath. DS;
			if(!JSNISImageThumbNail::createThumbnailFolder(JPATH_ROOT . DS . 'images' . DS, $imageThumbPath)) return false;
			if(!JSNISImageThumbNail::createFileThumbnail($fileOriginal, $imageThumbPath.$fileNameThumb)) return false;
			return true;
		}
		else
		{
			return false;
		}
	}

	function checkGraphicLibrary()
	{
		$objJSNISGD2 = new JSNISGD2();
		return $objJSNISGD2->detect();
	}

	function deleteThumbImage($arrayThumbPath)
	{
		if (count($arrayThumbPath) < 1)
		{
			return false;
		}

		$folderThumbnail = JPATH_ROOT . DS . 'images' . DS . 'jsn_is_thumbs' . DS;

		if (!JFolder::exists($folderThumbnail))
		{
			return false;
		}

		if (is_writable($folderThumbnail) == false)
		{
			return false;
		}

		foreach ($arrayThumbPath as $thumb)
		{
			//    		$thumb 			= str_replace('/', DS, $thumb);
			//    		$thumbName 		= explode(DS, $thumb);
			//    		$thumbName 		= end($thumbName);
			//    		$thumbPath 		= $folderThumbnail.$thumbName;

			if (JFile::exists($thumb))
			{
				@JFile::delete($thumb);
			}
		}
		return true;
	}

	function checkImageFolderStatus($objImage)
	{
		$objImage->image_big = urldecode($objImage->image_big);
		$imagesTable 	= JTable::getInstance('images','Table');
		$imagesTable->load((int) $objImage->image_id);
		$realPath 		= JPATH_ROOT.DS.str_replace('/', DS,  urldecode($imagesTable->image_big));
		$thumbPath 		= JPATH_ROOT.DS.str_replace('/', DS,  urldecode($imagesTable->image_small));
		$arrayThumb[] 	= $thumbPath;

		// check real image exists
		if (JFile::exists($realPath))
		{
			$imageSize 		= @filesize($realPath);
			// check real image have changed
			if ($imageSize > 0 && $imageSize != $imagesTable->image_size)
			{
				// create thumb for real image which have changed
				$imageThumbPath 			= $this->createThumbImageFolder($objImage);

				// save new information
				$imagesTable->image_medium 	= $imageThumbPath;
				$imagesTable->image_small   = $imageThumbPath;
				$imagesTable->image_size	= $imageSize;
				$imagesTable->store(array('replaceSpace' => true));

			} // if real image have not changed , check thumb status
			else if ($imageSize > 0 && $imageSize == $imagesTable->image_size)
			{
				if (!JFile::exists($thumbPath))
				{
					$imageThumbPath 			= $this->createThumbImageFolder($objImage);
					$imagesTable->image_medium 	= $imageThumbPath;
					$imagesTable->image_small   = $imageThumbPath;
					$imagesTable->store(array('replaceSpace' => true));
				}
			}
		}
		else // real image not exist , check thumb
		{
			if (JFile::exists($thumbPath))
			{
				JSNISImageThumbNail::deleteThumbImage($arrayThumb);
			}
		}
	}

	function getImagesNeedCreateThumb($objImage)
	{
		$objImage->image_big 	= urldecode($objImage->image_big);
		$imagesTable 			= JTable::getInstance('images','Table');
		$imagesTable->load((int) $objImage->image_id);
		$realPath 				= JPATH_ROOT.DS.str_replace('/', DS,  urldecode($imagesTable->image_big));
		$thumbPath				= JPATH_ROOT.DS.str_replace('/', DS,  urldecode($imagesTable->image_small));
		$arrayThumb[] 			= $thumbPath;
		// check real image exists
		if (JFile::exists($realPath))
		{
			$imageSize = @filesize($realPath);
			// check real image have changed
			if ($imageSize > 0 && $imageSize != $imagesTable->image_size)
			{
				//if real image have changed , check thumb exists and remove
				if (JFile::exists($thumbPath))
				{
					return $imagesTable->image_id;
				}

			} // if real image have not changed , check thumb status
			else if ($imageSize > 0 && $imageSize == $imagesTable->image_size)
			{
				if (!JFile::exists($thumbPath)) {
					return $imagesTable->image_id;
				}
			}
		}
		else // real image not exist , check thumb
		{
			if (JFile::exists($thumbPath))
			{
				JSNISImageThumbNail::deleteThumbImage($arrayThumb);
			}
		}
	}

	function createThumbImageFolder($objImage)
	{
		$objImage->image_big = urldecode($objImage->image_big);
		if ($this->checkGraphicLibrary())
		{
			$imageThumbPath	= 'images/jsn_is_thumbs';
			$imageName 		= explode('/', $objImage->image_big);
			$imageName 		= end($imageName);
			$realPath  		= JPATH_ROOT.DS.$objImage->image_big;

			if (!$this->createThumbnail($realPath, $imageName))
			{
				$imageThumbPath = $objImage->image_big;
			}
			else
			{
				$imageThumbPath = $imageThumbPath.'/'.$objImage->image_big;
			}

			return $imageThumbPath;
		}
		else
		{
			return $objImage->image_big;
		}
	}

	function getOnceThumbImage($imageExtID, $showListID)
	{
		$db 	= JFactory::getDBO();
		$query 	= 'SELECT *
				   FROM #__imageshow_images WHERE image_extid="'.$imageExtID.'" AND showlist_id='.$showListID;
		$db->setQuery($query);
		return $db->loadAssoc();
	}

	function getArrayThumbImage($cids)
	{
		$db 	= JFactory::getDBO();
		$query 	= 'SELECT image_small FROM #__imageshow_images'.' WHERE image_id IN ('.$cids.')';
		$db->setQuery($query);
		return $db->loadColumn();
	}

	function getAllThumbnailByShowlistID($showlistID)
	{
		$db 	= JFactory::getDBO();
		$query 	= 'SELECT image_small FROM #__imageshow_images'.' WHERE showlist_id ='.(int)$showlistID;
		$db->setQuery($query);
		return $db->loadColumn();
	}

	function deleteThumbnailByImageID($arrayImageID)
	{
		if (count($arrayImageID) > 0)
		{
			$stringImageID 	= implode(',', $arrayImageID);
			$thumbs 		= $this->getArrayThumbImage($stringImageID);

			if (count($thumbs) > 0)
			{
				return $this->deleteThumbImage($thumbs);
			}
		}
	}

	function deleteAllThumbnailByShowlistID($showlistID)
	{
		if ($showlistID)
		{
			$thumbnails = $this->getAllThumbnailByShowlistID($showlistID);

			if (count($thumbnails) > 0)
			{
				return $this->deleteThumbImage($thumbnails);
			}
		}
	}

	function getThumnails()
	{
		$thumbnailPath	= JPATH_ROOT . DS . 'images' . DS . 'jsn_is_thumbs' . DS;
		$result			= array();
		if (@JFolder::exists($thumbnailPath))
		{
			$fileList 	= JFolder::files($thumbnailPath);
			if (count($fileList))
			{
				foreach ($fileList as $file)
				{
					if (@is_file($thumbnailPath.DS.$file))
					{
						$ext = strtolower(JFile::getExt($file));
						switch ($ext)
						{
							case 'jpg':
							case 'png':
							case 'gif':
							case 'xcf':
							case 'odg':
							case 'bmp':
							case 'jpeg':
								$result [] = $file;
								break;
							default:
								break;
						}
					}
				}
			}
		}
		return $result;
	}
}
?>