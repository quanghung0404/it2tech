<?php
/**
 * @version    $Id: imageshow.php 16609 2012-10-02 09:23:05Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// Set the directory separator define if necessary.
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
@ini_set('max_execution_time', 300);
@ini_set('allow_url_fopen', 1);
include_once(JPATH_PLUGINS . DS . 'jsnimageshow' . DS . 'sourcepicasa' . DS . 'sources' . DS . 'external_source_picasa_exifinternalsource.php');
class JSNExternalSourcePicasa extends JSNImagesSourcesExternal
{
	public function getValidation($config = array())
	{
		$config = array_merge(array('validate_screen' => '_SHOWLIST_FLEX'), $config);
		if (!isset($config['picasa_username'])) {
			$this->_errorMsg = JText::_('PICASA_PICASA_USERNAME_EMPTY');
			return false;
		}

		$prefixString = '';

		if (($config['validate_screen']) != '') {
			$prefixString = strtoupper($config['validate_screen']);
		}

		$rss = 'https://picasaweb.google.com/data/feed/api/user/'.$config['picasa_username'].'/contacts?kind=user';

		if ((stristr($this->feedContentFile($rss), 'Unable to find user') !== false)
		|| (stristr($this->feedContentFile($rss), 'Unknown user') !== false)
		|| (stristr($this->feedContentFile($rss), 'Content has been removed for violation') !== false)
		|| (stristr($this->feedContentFile($rss), 'Invalid request') !== false)
		)
		{
			$this->_errorMsg = JText::_('PICASA_INVALID_PICASA_USERNAME'.$prefixString);

			return false;
		}

		return true;
	}

	protected function feedContentFile($url)
	{
		$objJSNHTTPRequest = JSNISFactory::getObj('classes.jsn_is_httprequest', null, $url);
		return $objJSNHTTPRequest->DownloadToString();
	}

	public function getCategories($config = array())
	{
		$rss 	= 'https://picasaweb.google.com/data/feed/api/user/'.$this->_source['sourceTable']->picasa_username.'/?kind=album&access=public&alt=rss';
		$albums = array();
		$file 	= $this->feedContentFile($rss);
		$start 	= @strpos($file, "<item>");
		$end 	= @strrpos($file, "</item>");
		$substr = substr($file, $start, $end-$start+1);
		$items 	= explode("<item>", $substr);

		if ($start != false or $end != false)
		{
			if (is_array($items) && count($items)>0)
			{
				$xml = "<node label='Web Album(s)' data=''>\n";

				foreach ($items as $tmp)
				{
					if (trim($tmp) != "")
					{
						$title 		= htmlspecialchars ($this->getTagContent($tmp, "title"), ENT_QUOTES);
						$albumId 	= htmlspecialchars ($this->getTagContent($tmp, "gphoto:id"), ENT_QUOTES);;

						$xml .= "<node label='{$title}' data='{$albumId}'></node>\n";
					}
				}

				$xml .= "</node>\n";
			}
			return $xml;
		}
		return false;
	}

	protected function getTagContent($src, $tag)
	{
		$start = @strpos ($src, "<".$tag.">");// + strlen($tag)+2;

		if ($start === false)
		{
			$start 		= @strpos ($src, "<".$tag) + strlen($tag)+1;
			$end 		= @strpos ($src, "/>", $start)-1;
			$content 	= substr($src, $start, $end-$start+1);
			$return 	= array();

			$tmp = explode(' ', $content);

			if (is_array($tmp) && count($tmp)>0)
			{
				foreach ($tmp as $line)
				{
					if (trim($line)!="")
					{
						$a 				= explode("=", $line);
						$return[$a[0]] 	= @str_replace("'", "", trim($a[1]));
					}
				}
			}
		}
		else
		{
			$start	+= strlen($tag)+2;
			$end 	= @strpos ($src, "</".$tag.">")-1;
			$return = substr($src, $start, $end-$start+1);
		}

		if (count($return)) {
			return $return;
		}

		return false;
	}

	public function loadImages($config = array())
	{
		$objJSNPicasa = JSNISFactory::getObj('sourcepicasa.classes.jsn_is_picasa', null, null, 'jsnplugin');
		$picasaParams	= json_decode($objJSNPicasa->getSourceParameters());
		if(isset($config['sync']))
		{
			$num	= 1000;
			$offset = 1;
		}
		else
		{
			$num = (isset($picasaParams->number_of_images_on_loading) && (is_int($picasaParams->number_of_images_on_loading)||ctype_digit($picasaParams->number_of_images_on_loading)))? $picasaParams->number_of_images_on_loading: '50';
			$offset = $config['offset'] +1;
		}
		$rss 	= 'https://picasaweb.google.com/data/feed/api/user/'.$this->_source['sourceTable']->picasa_username.'/albumid/'.$config['album'].'?kind=photo&alt=rss&access=public&start-index='.$offset.'&max-results='.$num;

		if ($this->_source['sourceTable']->picasa_thumbnail_size) {
			$rss .= '&thumbsize='. $this->_source['sourceTable']->picasa_thumbnail_size.'u';
		}

		if ($this->_source['sourceTable']->picasa_image_size) {
			$rss .= '&imgmax='. $this->_source['sourceTable']->picasa_image_size;
		}

		$file 	= $this->feedContentFile($rss);

		$photos = array();
		$start 	= @strpos($file, "<item>");
		$end 	= @strrpos($file, "</item>");
		$substr = substr($file, $start, $end-$start+1);
		$items 	= explode("<item>", $substr);

		if (is_array($items) && count($items)>0)
		{
			foreach ($items as $tmp)
			{
				if (trim($tmp) != "")
				{
					$title 				= $this->getTagContent($tmp, "title");
					$photoid 			= $this->getTagContent($tmp, "gphoto:id");
					$mediagroup 		= $this->getTagContent($tmp, "media:group");
					$image				= $this->getTagContent($mediagroup, "media:content");
					$thumbnail 			= $this->getTagContent($mediagroup, "media:thumbnail");
					$link				= $this->getTagContent($tmp, "link");
					$description    	= $this->getTagContent($tmp, "description");
					if ($image == false || $thumbnail == false) {
						break;
					}

					$photo['image_title'] 		= $title;
					$photo['image_alt_text'] 		= $title;
					$photo['image_extid'] 		= $photoid;
					$photo['image_small']   	= $thumbnail['url'];
					$photo['image_medium']   	= $thumbnail['url'];
					$photo['image_big']			= $image['url'];
					$photo['album_extid']		= $config['album'];
					$photo['image_link']		= (string) $link;
					$photo['image_description'] = (string) $description ;
					$photo['exif_data']			= $this->getExifInfo($tmp);
					array_push($photos, $photo);
				}
			}
		}

		$data = new stdClass();
		$data->images = $photos;

		return $data;
	}
	public function countImages($albumId){
		$rss	= 'https://picasaweb.google.com/data/feed/api/user/'.$this->_source['sourceTable']->picasa_username.'/albumid/'.$albumId.'?kind=photo&alt=rss&access=public&start-index=1&max-results=0';
		$file	= $this->feedContentFile($rss);
		$start	= @strpos($file, "<gphoto:numphotos>");
		$end	= @strrpos($file, "</gphoto:numphotos>");
		$countImages	= substr($file, $start+18, $end-$start-18);
		return $countImages;
	}
	public function getOriginalInfoImages($config = array())
	{
		$arrayImageInfo = array();

		if (isset($config['image_extid']) && is_array($config['image_extid']))
		{
			foreach ($config['image_extid'] as $imgExtID)
			{
				$photoInfoOriginal 		= $this->getInfoPhoto($config['album_extid'], $imgExtID);

				$imageObj 				= new stdClass();
				$imageObj->album_extid	= (string)$config['album_extid'];
				$imageObj->image_extid 	= (string)$imgExtID;
				$imageObj->title 		= ($photoInfoOriginal['title']) ? ($photoInfoOriginal['title']) : '';
				$imageObj->description 	= ($photoInfoOriginal['description']) ? $photoInfoOriginal['description'] : '';
				$imageObj->link			= ($photoInfoOriginal['url']) ? $photoInfoOriginal['url'] : '';
				$arrayImageInfo[] 		= $imageObj;
			}
		}

		return $arrayImageInfo;
	}

	protected function getInfoPhoto($albumID, $photoID)
	{
		$rss 	= 'https://picasaweb.google.com/data/feed/api/user/'.$this->_source['sourceTable']->picasa_username.'/albumid/'.$albumID.'/photoid/'.$photoID.'?alt=rss&thumbsize=288';
		$file 	= $this->feedContentFile($rss);

		$photo 					= array();
		$photo['title'] 		= $this->getTagContent($file, "title");
		$photo['description'] 	= $this->getTagContent($file, "description");
		$photo['url'] 			= $this->getTagContent($file, "link");

		return $photo;
	}

	public function saveImages($config = array())
	{
		parent::saveImages($config);

		$config 	= $this->_data['saveImages'];
		$imgExtID 	= $config['imgExtID'];

		if (count($imgExtID))
		{
			$objJSNImages 	= JSNISFactory::getObj('classes.jsn_is_images');
			$ordering 		= $objJSNImages->getMaxOrderingByShowlistID($config['showlistID']);

			if (count($ordering) < 0 || is_null($ordering)) {
				$ordering = 1;
			} else {
				$ordering = $ordering[0] + 1;
			}

			$imagesTable = JTable::getInstance('images', 'Table');

			for ($i = 0 ; $i < count($imgExtID); $i++)
			{
				$imagesTable->showlist_id 		= $config['showlistID'];
				$imagesTable->image_extid 		= $imgExtID[$i];
				$imagesTable->album_extid 		= $config['albumID'][$imgExtID[$i]];
				$imagesTable->image_small 		= $config['imgSmall'][$imgExtID[$i]];
				$imagesTable->image_medium 		= $config['imgMedium'][$imgExtID[$i]];
				$imagesTable->image_big			= $config['imgBig'][$imgExtID[$i]];
				$imagesTable->image_title   	= $config['imgTitle'][$imgExtID[$i]];
				if (isset($config['imgAltText'][$imgExtID[$i]]))
				{
					$imagesTable->image_alt_text   	= $config['imgAltText'][$imgExtID[$i]];
				}				
				$imagesTable->ordering			= $ordering;
				$imagesTable->image_description = $config['imgDescription'][$imgExtID[$i]];
				$imagesTable->image_link 		= $config['imgLink'][$imgExtID[$i]];
				$imagesTable->custom_data 		= $config['customData'][$imgExtID[$i]];
				$imagesTable->exif_data 		= $this->getExifInfoPhoto($config['albumID'][$imgExtID[$i]], $imgExtID[$i]);
				$result = $imagesTable->store(array('replcaceSpace' => false));
				$imagesTable->image_id = null;
				$ordering ++;
			}

			if ($result) {
				return true;
			}

			return false;
		}

		return false;
	}

	public function addOriginalInfo()
	{
		$data = array();

		if (is_array($this->_data['images']) && is_array($this->_data['images']))
		{
			foreach ($this->_data['images'] as $img)
			{
				if ($img->custom_data == 1)
				{
					$info 	= $this->getInfoPhoto($img->album_extid, $img->image_extid);
					$img->original_title 		= (is_array($info['title'])) ? '' : trim($info['title']);
					$img->original_description 	= (is_array($info['description'])) ? '' : trim($info['description']);
					$img->original_link 		= (is_array($info['url'])) ? '' : trim($info['url']);
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

		return $data;
	}

	public function getImages2JSON($config = array())
	{
		parent::getImages2JSON($config);

		$arrayImage = array();

		if (count($this->_data['images']))
		{
			foreach ($this->_data['images'] as $image)
			{
				$imageDetailObj 						= new stdClass();
				$image									= (array) $image;
				$imageDetailObj->{'thumbnail'} 		= $image['image_small'];
				$imageDetailObj->{'image'} 			= $image['image_big'];
				$imageDetailObj->{'title'} 			= $image['image_title'];
				if (isset($image['image_alt_text']))
				{
					$imageDetailObj->{'alt_text'} = $image['image_alt_text'];
				}
				else
				{
					$imageDetailObj->{'alt_text'} = $image['image_title'];
				}				
				$imageDetailObj->{'description'} 	= (!is_null($image['image_description'])) ? $image['image_description'] : '';
				$imageDetailObj->{'link'} 			= $image['image_link'];
				$imageDetailObj->exif_data			= $image['exif_data'];
				$arrayImage[]		 				= $imageDetailObj;
			}
		}
		return $arrayImage;
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

				$data 		  = $this->loadImages(array('album' => $album->album_extid,'sync' => true));
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

	public function getImagesLocalInfo($config = array())
	{
		if (count( $config['imageIDs'] ))
		{
			$imageTable 	= JTable::getInstance('images','Table');
			$imageRevert  	= array();

			foreach ($config['imageIDs'] as $ID)
			{
				if ($imageTable->load((int)$ID))
				{
					$info 	= $this->getInfoPhoto($imageTable->album_extid, $imageTable->image_extid);
					$imgObj = new stdClass();
					$imgObj->image_id			= $imageTable->image_id;
					$imgObj->image_extid 		= $imageTable->image_extid;
					$imgObj->album_extid 		= $imageTable->album_extid;
					$imgObj->image_title 		= (is_array($info['title'])) ? '' : trim($info['title']);
					$imgObj->image_description 	= (is_array($info['description'])) ? '' : trim($info['description']);
					$imgObj->image_link 		= (is_array($info['url'])) ? '' : trim($info['url']);
					$imgObj->custom_data 		= 0;
					$imageRevert[] = $imgObj;
				}
			}
			return $imageRevert;
		}
		return false;
	}

	public function getImageSrc($config = array('image_big' => '', 'URL' => '')) {
		return $config['image_big'];
	}

	protected function getExifInfoPhoto($albumID, $photoID)
	{
		$rss 	= 'https://picasaweb.google.com/data/feed/api/user/'.$this->_source['sourceTable']->picasa_username.'/albumid/'.$albumID.'/photoid/'.$photoID.'?alt=rss&thumbsize=288';
		$file 	= $this->feedContentFile($rss);
		return $this->getExifInfo($file);
	}
	protected function getExifInfo($file){
		$exifInfo 	= array();
		$make		= $this->getTagContent($file, "exif:make");
		if ($make != false)
		{
			$exifInfo['make']= $make;
		}
		$model		= $this->getTagContent($file, "exif:model");
		if ($model != false)
		{
			$exifInfo['model']= $model;
		}
		$exposureTime		= $this->getTagContent($file, "exif:exposure");
		if ($exposureTime != false)
		{
			$exifInfo['exposure']= $exposureTime;
		}
		$flash				= $this->getTagContent($file, "exif:flash");
		if ($flash != false)
		{
			$exifInfo['flash']= $flash;
		}
		$focalLength				= $this->getTagContent($file, "exif:focallength");
		if ($focalLength != false)
		{
			$exifInfo['focallength']= $focalLength;
		}
		$iso				= $this->getTagContent($file, "exif:iso");
		if ($iso != false)
		{
			$exifInfo['iso']= $iso;
		}
		$fstop				= $this->getTagContent($file, "exif:fstop");
		if ($fstop != false)
		{
			$exifInfo['fstop']= $fstop;
		}

		$objExif = new JSNExternalSourcePicasaExifInternalSource();
		return $objExif->renderData($exifInfo);
	}
}