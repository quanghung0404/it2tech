<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

class EasyBlogImage
{
	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return boolean
	 */
	public static function isImage( $fileName )
	{
		static $imageTypes = 'gif|jpg|jpeg|png';
		return preg_match("/$imageTypes/i",$fileName);
	}

	public static function getFileName( $imageURL )
	{
		if( empty($imageURL) )
			return '';

		$filename = basename($imageURL);
		return $filename;
	}

	/**
	 * Retrieves a file extension given the name
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExtension($path)
	{
		$info = getimagesize($path);

		switch ($info['mime']) {
			case 'image/jpeg':
				$extension  = '.jpg';
			break;

			case 'image/png':
			case 'image/x-png':
			default:
				$extension  = '.png';
			break;
		}


		return $extension;
	}

	public function getFileExtension($filename)
	{
		if( empty( $filename ) )
			return '';

		// it will return with the leading dot e.g .jpg .png
		$file_extension = substr($filename , strrpos($filename , '.') +1);
		return $file_extension;
	}

	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return file type
	 */
	public static function getTypeIcon( $fileName )
	{
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}

	/**
	 * Checks if the file can be uploaded
	 *
	 * @param array File information
	 * @param string An error message to be returned
	 * @return boolean
	 */
	public function canUpload($file, &$err)
	{
		$config = EB::config();

		if(empty($file['name'])) {
			$err = 'COM_EASYBLOG_WARNEMPTYFILE';
			return false;
		}

		jimport('joomla.filesystem.file');
		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$err = 'COM_EASYBLOG_WARNFILENAME';
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		if (!$this->isImage($file['name'])) {
			$err = 'COM_EASYBLOG_WARNINVALIDIMG';
			return false;
		}

		$maxWidth	= 160;
		$maxHeight	= 160;

		// maxsize should get from eblog config
		//$maxSize	= 2000000; //2MB
		//$maxSize	= 200000; //200KB

		// 1 megabyte == 1048576 byte
		$byte   		= 1048576;
		$uploadMaxsize  = (float) $config->get('main_upload_image_size', 0 );
		$maxSize 		= $uploadMaxsize * $byte;

		if ($maxSize > 0 && (float) $file['size'] > $maxSize) {
			$err = 'COM_EASYBLOG_WARNFILETOOLARGE';
			return false;
		}

		$user = JFactory::getUser();
		$imginfo = null;

		if(($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
			$err = 'COM_EASYBLOG_WARNINVALIDIMG';
			return false;
		}

		return true;
	}

	/**
	 * Determines if the file i upload-able
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function canUploadFile($file, &$msgObj = false )
	{
		$config = EB::config();

		if (!isset($file['name']) || empty($file['name'])) {
			return EB::exception('COM_EASYBLOG_IMAGE_UPLOADER_PLEASE_INPUT_A_FILE_FOR_UPLOAD', EASYBLOG_MSG_ERROR);
		}

		// Get the extension
		$extension = strtolower(JFile::getExt($file['name']));

		// Get a list of allowed extensions
		$allowed = EBMM::getAllowedExtensions();

		if (!in_array($extension, $allowed)) {
			return EB::exception('COM_EASYBLOG_FILE_NOT_ALLOWED', EASYBLOG_MSG_ERROR);
		}

		// Ensure that the file that is being uploaded isn't too huge
		$fileSize = (int) $file['size'];
		$maximumAllowed = EBMM::getAllowedFilesize();

		if ($maximumAllowed !== false && $fileSize > $maximumAllowed) {
			return EB::exception('COM_EASYBLOG_WARNFILETOOLARGE', EASYBLOG_MSG_ERROR);
		}

		// Ensure that the user doesn't do any funky stuff to the image
		if (self::containsXSS($file['tmp_name'])) {
			return EB::exception('COM_EASYBLOG_FILE_CONTAIN_XSS', EASYBLOG_MSG_ERROR);
		}

		return true;
	}

	/**
	 * Checks if the file contains any funky html tags
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function containsXSS($path)
	{
		// Sanitize the content of the files
		$contents = JFile::read($path, false, 256);
		$tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');

		// If we can't read the file, just skip this altogether
		if (!$contents) {
			return false;
		}

		foreach ($tags as $tag) {
			// If this tag is matched anywhere in the contents, we can safely assume that this file is dangerous
			if (stristr($contents, '<' . $tag . ' ') || stristr($contents, '<' . $tag . '>') || stristr($contents, '<?php') || stristr($contents, '?\>')) {
				return true;
			}			
		}

		return false;
	}

	public function parseSize($size)
	{
		if ($size < 1024) {
			return $size . ' bytes';
		}
		else
		{
			if ($size >= 1024 && $size < 1024 * 1024) {
				return sprintf('%01.2f', $size / 1024.0) . ' Kb';
			} else {
				return sprintf('%01.2f', $size / (1024.0 * 1024)) . ' Mb';
			}
		}
	}

	public static function imageResize($width, $height, $target)
	{
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}

	public static function countFiles( $dir )
	{
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir)) {
			$d = dir($dir);

			while (false !== ($entry = $d->read())) {
				if (substr($entry, 0, 1) != '.' && is_file($dir . DIRECTORY_SEPARATOR . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
					$total_file++;
				}
				if (substr($entry, 0, 1) != '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array ( $total_file, $total_dir );
	}

	public static function getAvatarDimension($avatar)
	{
		$config			= EasyBlogHelper::getConfig();

		//resize the avatar image
		$avatar	= JPath::clean( JPATH_ROOT . DIRECTORY_SEPARATOR . $avatar );
		$info	= @getimagesize($avatar);
		if(! $info === false)
		{
			$thumb	= EasyImageHelper::imageResize($info[0], $info[1], 60);
		}
		else
		{
			$thumb  = array( EBLOG_AVATAR_THUMB_WIDTH, EBLOG_AVATAR_THUMB_HEIGHT);
		}

		return $thumb;
	}

	/**
	 * Retrieves the relative path to the respective avatar storage
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getAvatarRelativePath($type = 'profile')
	{
		$config = EB::config();

		// Default path
		$path = '';

		if ($type == 'category') {
			$path = $config->get('main_categoryavatarpath');
		} else if($type == 'team') {
			$path = $config->get('main_teamavatarpath');
		} else {
			$path = $config->get('main_avatarpath');
		}

		// Ensure that there are no trailing slashes
		$path = rtrim($path, '/');

		return $path;
	}


	public static function rel2abs($rel, $base)
	{
		return EB::string()->rel2abs( $rel, $base );
	}

	private function getMessageObj( $code = '' , $message = '', $item = false )
	{
		$obj			= new stdClass();
		$obj->code		= $code;
		$obj->message	= $message;

		if( $item )
		{
			$obj->item	= $item;
		}

		return $obj;
	}

	public function upload( $folder , $filename , $file , $baseUri , $storagePath , $subfolder = '' )
	{
		$config	= EB::config();
		$user   = JFactory::getUser();

		if (isset($file['name']))
		{
			if($config->get('main_resize_original_image'))
			{
				$maxWidth	= $config->get( 'main_original_image_width' );
				$maxHeight	= $config->get( 'main_original_image_height' );

				$image	= EB::simpleimage();
				$image->load($file['tmp_name']);
				$image->resizeWithin( $maxWidth , $maxHeight );

				$uploadStatus = $image->save( $storagePath , $image->image_type , $config->get( 'main_original_image_quality' ) );
			}
			else
			{
				$uploadStatus = JFile::upload($file['tmp_name'], $storagePath);
			}

			// @task: thumbnail's file name
			$storagePathThumb	= JPath::clean( $folder . DIRECTORY_SEPARATOR . EBLOG_MEDIA_THUMBNAIL_PREFIX . $filename );

			// Generate a thumbnail for each uploaded images
			$image 	= EB::simpleimage();
			$image->load($storagePath);

			$image->resizeWithin( $config->get( 'main_thumbnail_width' ) , $config->get( 'main_thumbnail_height' ) );
			$image->save( $storagePathThumb , $image->image_type , $config->get( 'main_thumbnail_quality' ) );

			if( !$uploadStatus )
			{
				return $this->getMessageObj( EBLOG_MEDIA_PERMISSION_ERROR , JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR' ) );
			}
			else
			{
			    // file uploaded. Now we test if the index.html was there or not.
			    // if not, copy from easyblog root into this folder
			    if(! JFile::exists( $folder . DIRECTORY_SEPARATOR . 'index.html' ) )
			    {
			        $targetFile = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'index.html';
			        $destFile   = $folder . DIRECTORY_SEPARATOR .'index.html';

			        if( JFile::exists( $targetFile ) )
			        {
			        	JFile::copy( $targetFile, $destFile );
			        }
			    }

				return self::getMessageObj(EBLOG_MEDIA_UPLOAD_SUCCESS , JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_SUCCESS' ), EasyBlogImageDataHelper::getObject( $folder , $filename , $baseUri , $subfolder ));
			}
		}
		else
		{
			return self::getMessageObj( EBLOG_MEDIA_TRANSPORT_ERROR , JText::_( 'COM_EASYBLOG_MEDIA_MANAGER_NO_UPLOAD_FILE' ) );
		}

		return $response;
	}
}
