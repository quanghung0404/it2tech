<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogImageDataHelper
{
	public static function getFolderObject( $folder , $currentFolder  , $baseURL = '' , $subfolder = '')
	{
		if( !empty( $subfolder ) )
		{
			$subfolder	.= '/';
		}

		$path				= rtrim( $folder , DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $currentFolder;

		$files				= JFolder::files( $path , '.' , false , false , array( '.' , 'index.html') );
		$total				= count( $files );

		$tmp				= new stdClass();

		// @rule: For gallery images, we need to set the gallery items
		$uri				= array();
		if( $files )
		{
			$allowed	= array( 'jpg' , 'png' , 'gif' , 'jpeg' );

			foreach( $files as $file )
			{
				$extension	= JString::strtolower( JFile::getExt( $file ) );
				$thumbFile	= '';

				// Test to see if thumbnail exists, if it does use thumbnail instead.
				if( JFile::exists( $path . DIRECTORY_SEPARATOR . EBLOG_MEDIA_THUMBNAIL_PREFIX . $file ) )
				{
					$thumbFile	= EBLOG_MEDIA_THUMBNAIL_PREFIX . $file;
				}

				// Only fetch image files and do not try to fetch thumbnails since we will try to use them automatically.
				if( in_array( $extension , $allowed ) && stristr( $file , EBLOG_MEDIA_THUMBNAIL_PREFIX ) === false )
				{
					$url	= $baseURL . '/' . $currentFolder . '/';
					$url	.= !empty( $thumbFile ) ? $thumbFile : $file;
					$uri[]	= $url;
				}
			}
		}

		$tmp->type			= EBLOG_MEDIA_FOLDER;
		$tmp->name			= $currentFolder;
		$tmp->path			= $subfolder . $currentFolder;
		$tmp->path_relative	= $subfolder . $currentFolder;
		$tmp->size			= EasyBlogImageDataHelper::getFolderSize( $path );
		$tmp->created		= strftime( JText::_( 'DATE_FORMAT_LC4' ) , filemtime( $path ) );
		$tmp->preview		= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/media/folder.png';
		$tmp->files			= $total;
		$tmp->gallery		= $uri;
		$tmp->link			= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog&view=images&tmpl=component&e_name=content&blogger_id=&current=' . $subfolder;
		return $tmp;
	}

	public static function getFolderSize( $path )
	{

		$handle		= opendir( $path );
		$size		= 0;

		if( !$handle )
		{
			return 0;
		}

		while( $file = readdir( $handle ) )
		{
			if( $file != '.' && $file != '..' && !is_dir( $path . DIRECTORY_SEPARATOR . $file ) )
			{
				$size	+= filesize($path . DIRECTORY_SEPARATOR . $file );
			}
		}
		closedir( $handle );

		return EasyBlogImageDataHelper::formatSize( $size );
	}

	function formatSize($size)
	{
    	$units = array(' B', ' KB', ' MB', ' GB', ' TB');
    	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}

	public function getObject( $folder , $file , $baseURL , $subfolder = '' )
	{
		$config		= EasyBlogHelper::getConfig();
		$extension	= JString::strtolower( JFile::getExt( $file ) );
		$tmp		= new stdClass();

		// Standard properties all files must have.
		$tmp->name			= $file;
		$tmp->path_relative	= $subfolder . '/' . $file;

		switch( $extension )
		{
			// Image
			case 'jpg':
			case 'png':
			case 'gif':
			case 'xcf':
			case 'odg':
			case 'bmp':
			case 'jpeg':

				if( $config->get( 'main_media_lightbox_caption_strip_extension' ) )
				{
					$parts		= explode( '.' , $tmp->name );
					$tmp->name 	= $parts[0];
				}

				// Set the type of file.
				$tmp->type			= EBLOG_MEDIA_IMAGE;

				// The full path to the file
				$path				= $folder . DIRECTORY_SEPARATOR . $file;

				// Relative path should be the relative path to the folder.
				$tmp->size			= filesize( str_ireplace( DIRECTORY_SEPARATOR , '/' , JPath::clean( $folder . DIRECTORY_SEPARATOR . $file ) ) );
				$tmp->url           = $baseURL . '/' . $file;

				// Fix for legacy images uploaded prior to 2.1 since it does not generate a custom thumbnail file
				$tmp->thumbnail		= rtrim( $baseURL , '/' ) . '/' . $file;
				$tmp->fullpath      = rtrim( $baseURL , '/' ) . '/' . $file;

				// Retrieve image information
				$info				= getimagesize( $path );
				$tmp->width			= $info[0];
				$tmp->height		= $info[1];
				$tmp->mime			= $info['mime'];

				// Set the dimensions of the thumbnail
				$tmp->thumbwidth	= $info[0];
				$tmp->thumbheight	= $info[1];

				// Default alignment of the image
				$tmp->defaultAlignment	= $config->get('main_image_alignment');

				// @since 2.1
				// Allow use of thumbnail instead if exists
				if( JFile::exists( $folder . DIRECTORY_SEPARATOR . EBLOG_MEDIA_THUMBNAIL_PREFIX . $file ) )
				{
					$tmp->thumbnail			= rtrim( $baseURL , '/' )  . '/' . EBLOG_MEDIA_THUMBNAIL_PREFIX . $file;
					$tmp->url               = rtrim( $baseURL , '/' )  . '/' . $file;

					// Retrieve the thumb info.
					$thumbPath			= $folder . DIRECTORY_SEPARATOR . EBLOG_MEDIA_THUMBNAIL_PREFIX . $file;
					$info				= getimagesize( $thumbPath );
					$tmp->thumbwidth	= $info[0];
					$tmp->thumbheight	= $info[1];

				}

			break;
			case 'mp4':
			case 'flv':
			case 'mov':
			case 'f4v':
			case '3gp':
			case '3g2':
			case 'aac':
			case 'm4a':
			case 'mp3':
			case 'webm':
			case 'ogv':
			case 'ogg':
				$tmp->type	= EBLOG_VIDEO_FILE;
				$tmp->size	= filesize( str_ireplace( DIRECTORY_SEPARATOR , '/' , JPath::clean( $folder . DIRECTORY_SEPARATOR . $file ) ) );
				$tmp->path			= $subfolder . '/' . $file;
				$tmp->path_relative	= $subfolder . '/' . $file;
				$tmp->width			= $config->get('max_video_width');
				$tmp->height		= $config->get('max_video_height');

				ob_start();
			?>
<object class="video-preview" type="application/x-shockwave-flash" width="300" height="200" data="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easyblog/assets/vendors/jwplayer/player.swf">
	<param name="movie" value="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easyblog/assets/vendors/jwplayer/player.swf" />
	<param name="quality" value="high" />
	<param name="wmode" value="transparent" />
	<param name="autoplay" value="false" />
	<param name="allowfullscreen" value="false" />
	<param name="allowscriptaccess" value="always" />
	<param name="flashvars" value="file=<?php echo rtrim( $baseURL , '/' ) . '/' . $file;?>" />
</object>
			<?php
				$content	= ob_get_contents();
				ob_end_clean();

				$tmp->preview	= $content;
			break;
			default:
				$icon		= EasyBlogImageDataHelper::getIconMap( $file );

				$tmp->type	= EBLOG_MEDIA_FILE;
				$tmp->name	= $file;
				$tmp->path_relative	= $subfolder . '/' . $file;
				$tmp->size	= EasyBlogImageDataHelper::formatSize( filesize( str_ireplace( DIRECTORY_SEPARATOR , '/' , JPath::clean( $folder . DIRECTORY_SEPARATOR . $file ) ) ) );
				$tmp->url	= rtrim( $baseURL , '/' ) . '/' . $file;
				$tmp->thumbUrl	= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/media/' . $icon .'.png';
				$tmp->preview	= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/media/' . $icon .'_preview.png';
				$tmp->created	= strftime( JText::_( 'DATE_FORMAT_LC4' ) , filemtime( str_ireplace( DIRECTORY_SEPARATOR , '/' , JPath::clean( $folder . DIRECTORY_SEPARATOR . $file ) ) ) );

			break;
		}

		return $tmp;
	}

    function getIconMap($filename)
	{
        $mime_types = array(
            'txt' => 'plain',
             'htm' => 'html',
             'html' => 'html',
             'php' => 'html',
             'css' => 'css',
             'js' => 'javascript',
             'json' => 'json',
             'xml' => 'xml',
             'swf' => 'flash',
             'flv' => 'flash',

            // archives
             'zip' => 'zip',
             'rar' => 'zip',
             'exe' => 'exe',
             'msi' => 'exe',
             'cab' => 'exe',

            // audio/video
             'mp3' => 'mpeg',
             'qt' => 'quicktime',
             'mov' => 'quicktime',

            // adobe
             'pdf' => 'pdf',
             'psd' => 'image/vnd.adobe.photoshop',
             'ai' => 'postscript',
             'eps' => 'postscript',
             'ps' => 'postscript',

            // ms office
             'doc' => 'word',
             'rtf' => 'word',
             'xls' => 'excel',
             'ppt' => 'powerpoint',
         );

        $ext = strtolower(array_pop(explode('.',$filename)));
         if (array_key_exists($ext, $mime_types)) {
             return $mime_types[$ext];
         }
         elseif (function_exists('finfo_open')) {
             $finfo = finfo_open(FILEINFO_MIME);
             $mimetype = finfo_file($finfo, $filename);
             finfo_close($finfo);
             return $mimetype;
         }
         else {
             return 'application';
         }
     }

}
