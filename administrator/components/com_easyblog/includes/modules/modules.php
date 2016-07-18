<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogModules
{
	const SOURCE_INTRO = "0";
	const SOURCE_CONTENT = "1";
	const SOURCE_HIDDEN = "-1";

	const POST_TRUNCATE = false;
	const POST_STRIP_TAGS = false;
	const POST_SOURCE_INTRO_COLUMN = 'intro';
	const POST_SOURCE_CONTENT_COLUMN = 'content';
	const POST_TRIGGER_PLUGIN = false;

	/**
	 * Formats the result of the module
	 *
	 * @since 5.0
	 */
	public static function processItems($data, &$params)
	{
	    $config = EB::config();
		$app = JFactory::getApplication();
		$appParams = $app->getParams('com_easyblog');
		$limitstart = $app->input->get('limitstart', 0, 'int');
		$result = array();

		$posts = EB::formatter('list', $data, false);

		foreach ($posts as $post) {

			// Default media items
			$post->media = '';
			
			// If the post doesn't have a blog image try to locate for an image
			if ($post->posttype == 'standard' && !$post->hasImage()) {
				
				$photoWSize = $params->get('photo_width', 250);
				$photoHSize = $params->get('photo_height', 250);
 				$size = array('width' => $photoWSize, 'height' => $photoHSize);

				$post->media = EB::modules()->getMedia($post, $params, $size);
			}

			// Get the comment count
			$post->commentCount = $post->getTotalComments();

			// Determines if this post is password protected or not.
			$requireVerification = false;

			if ($config->get('main_password_protect', true) && !empty($r->blogpassword)) {
				$row->title	= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $row->title);
				$requireVerification = true;
			}

			$post->showRating = true;
			$post->protect = false;

			$post->summary = '';

			// Only if verification is necessary, we do not want to show the content
			if ($requireVerification && !EB::verifyBlogPassword($post->blogpassword, $post->id)) {
					
				$return = base64_encode($post->getPermalink());

				$theme = EB::template();
				$theme->set('post', $post);
				$theme->set('id', $post->id);
				$theme->set('return', $return);

				$post->summary = $theme->output('site/blogs/latest/default.protected');

				$post->showRating = false;
				$post->protect = true;
			}

			// Determines the content source
			$contentSource = $params->get('showintro', -1);

			// Determines if we should trigger plugins
			$triggerPlugins = $params->get('trigger_plugins', false);

			// Display only the intro
			if ($contentSource == self::SOURCE_INTRO) {
				$options = array('skipAudio' => true, 'skipImage' => true, 'triggerPlugins' => $triggerPlugins);

				$post->summary = $post->getIntro(self::POST_STRIP_TAGS, self::POST_TRUNCATE, self::POST_SOURCE_INTRO_COLUMN, null, $options);
			}

			// Display the main content without intro
			if ($contentSource == self::SOURCE_CONTENT) {
				$post->summary = $post->getContentWithoutIntro('entry', $triggerPlugins);
			}

			// Checks if this post have a video embedded using legacy video. 
			// If true, properly process the video and get the link.
			$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
			preg_match_all($pattern, $post->summary, $matches, PREG_SET_ORDER);

			if ($matches) {
				$post->summary = EB::videos()->processVideos($post->summary);
			}

			// Truncation settings
			$maxLength = $params->get('textcount', 0);
			$length = JString::strlen($post->summary);
			$autoTruncate = ($maxLength && $length > $maxLength && !$post->protect);

			// Remove any html codes from the content
			$stripTags = $params->get('striptags', true);

			if ($stripTags || $autoTruncate) {
				$post->summary = strip_tags($post->summary);
			}
			
			if ($autoTruncate) {
				$post->summary = JString::substr($post->summary, 0, $maxLength) . JText::_('COM_EASYBLOG_ELLIPSES');
			}

			$result[] = $post;
			
		}

		return $result;
	}
	public static function getMedia( &$row , $params, $size = array() )
	{
		$media  = '';
		$type	= 'image'; //default to image only.

		switch( $type )
		{
			case 'video':
				$row->intro		= EB::videos()->processVideos( $row->intro );
				$row->content	= EB::videos()->processVideos( $row->content );

				break;
			case 'audio':
				$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->intro );
				$row->content	= EasyBlogHelper::getHelper( 'Audio' )->process( $row->content );
				break;
			case 'image':

				$imgSize    = '';
				if( !empty( $size ) )
				{
					if( isset( $size['width'] ) && isset( $size['height'] ) )
					{
						$width	 	= $size[ 'width' ] != 'auto' ? $size['width'] . 'px' : 'auto';
						$height		= $size[ 'height' ] != 'auto' ? $size['height'] . 'px' : 'auto';

						$imgSize    = ' style="width: ' . $width . ' !important; height:' . $height . ' !important;"';
					}
				}

				if( $row->getImage() )
				{
					$media	=	$row->getImage( 'small' );
					if( !empty( $imgSize ) )
					{
						$media  = str_replace('<img', '<img ' . $imgSize . ' ', $media);
					}

				}

				if( empty( $media ) )
				{
					$media = self::getFeaturedImage( $row, $params);
					if( !empty( $imgSize ) )
					{
						$media  = str_replace('<img', '<img ' . $imgSize . ' ', $media);
					}
				}
				else
				{
					$media	= '<img src=" ' . $media . '" class="blog-image" style="margin: 0 5px 5px 0;border: 1px solid #ccc;padding:3px;" ' .$imgSize.'/>';
				}

				break;
			default:
				break;
		}

		if( $type != 'image')
		{
			// remove images.
			$pattern				= '#<img[^>]*>#i';
			preg_match( $pattern , $row->intro . $row->content , $matches );
			if( isset( $matches[0] ) )
			{
				// After extracting the image, remove that image from the post.
				$row->intro		= str_ireplace( $matches[0] , '' , $row->intro );
				$row->content	= str_ireplace( $matches[0] , '' , $row->intro );
			}
		}

		return $media;
	}


	public static function getFeaturedImage( &$row , &$params )
	{
		$pattern	= '#<img class="featured"[^>]*>#i';
		$content	= $row->intro . $row->content;

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return self::getResizedImage($matches[0] , $params );
		}

		// If featured image is not supplied, try to use the first image as the featured post.
		$pattern				= '#<img[^>]*>#i';

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			// After extracting the image, remove that image from the post.
			$row->intro		= str_ireplace( $matches[0] , '' , $row->intro );
			$row->content	= str_ireplace( $matches[0] , '' , $row->intro );

			return self::getResizedImage($matches[0] , $params );
		}

		// If all else fail, try to use the default image
		return false;
	}

	public static function getResizedImage( $img , $params )
	{
		preg_match( '/src= *[\"¦\']{0,1}([^\"\'\>]*)/i' , $img , $matches );

		if( !isset( $matches[ 1 ] ) )
		{
			return $img;
		}

		// We find the thumb and make it a popup
		if( stristr( $matches[1] , 'thumb_' ) === false )
		{
			return $img;
		}

		// Test if the full image exists.
		jimport( 'joomla.filesystem.file' );

		$info	= pathinfo( $matches[ 1 ] );

		$thumb	= JPATH_ROOT . DIRECTORY_SEPARATOR . str_ireplace( '/' , DIRECTORY_SEPARATOR , $matches[ 1 ] );
		$full	= str_ireplace( 'thumb_' , '' , $thumb );

		if( !JFile::exists( $full ) )
		{
			return $img;
		}

		return '<a href="' . str_ireplace( 'thumb_' , '' , $matches[1] ) . '" class="easyblog-thumb-preview">'
			 . $img . '</a>';
	}

	public static function getThumbnailImage($img)
	{
		$srcpattern = '/src=".*?"/';

		preg_match( $srcpattern , $img , $src );

		if(isset($src[0]))
		{
			$imagepath	= trim(str_ireplace('src=', '', $src[0]) , '"');
			$segment 	= explode('/', $imagepath);
			$file 		= end($segment);
			$thumbnailpath = str_ireplace($file, 'thumb_'.$file, implode('/', $segment));

			if(!JFile::exists($thumbnailpath))
			{
				$image 	= EB::simpleimage();
				$image->load($imagepath);
				$image->resize(64, 64);
				$image->save($thumbnailpath);
			}

			$newSrc = 'src="'.$thumbnailpath.'"';
		}
		else
		{
			return false;
		}

		$oldAttributes = array('src'=>$srcpattern, 'width'=>'/width=".*?"/', 'height'=>'/height=".*?"/');
		$newAttributes = array('src'=>$newSrc,'width'=>'', 'height'=>'');

		return preg_replace($oldAttributes, $newAttributes, $img);
	}


}
