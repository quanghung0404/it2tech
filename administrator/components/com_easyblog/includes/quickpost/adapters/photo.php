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

require_once(__DIR__ . '/abstract.php');

class EasyBlogQuickPostPhoto extends EasyBlogQuickPostAbstract
{
	/**
	 * Processes the upload of webcam
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind(EasyBlogPost &$blog)
	{
		$dataType = $this->input->get('dataType', '', 'cmd');

		if ($dataType == 'webcam') {
			$this->bindWebcam($blog);
		}

		if ($dataType == 'upload') {
			$this->bindUpload($blog);
		}

		// Construct the content
		$content = $this->input->get('content', '', 'default');
		$title = $this->input->get('title', '', 'default');

		// Replace newlines with <br /> tags since the form is a plain textarea.
		$content = nl2br($content);

		$blog->title = $title;
		$blog->content = $content;
		$blog->posttype = EBLOG_MICROBLOG_PHOTO;
	}

	/**
	 * Binds the uploaded photo to the post object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function bindUpload(EasyBlogPost &$post)
	{
		// Get the title
		$title = $this->input->get('title', '', 'default');

		if (!$title) {
			$post->title = JText::_('COM_EASYBLOG_DEFAULT_UPLOADED_PHOTO');
		}

		$fileName = $this->input->get('fileName', '', 'default');
		$tmpPath = JPATH_ROOT . '/tmp/' . $fileName;
		$tmpPath = JPath::clean($tmpPath);
		$fileInfo = getimagesize($tmpPath);

		// Construct a dummy file object
		$file = array('name' => $fileName, 'tmp_name' => $tmpPath, 'type' => $fileInfo['mime']);

		// Upload this file into the user's images folder.
        // Load up media manager now
        $mm = EB::mediamanager();
        $result = $mm->upload($file, 'user:' . $this->my->id);

        if ($result instanceof Exception) {
        	return;
        }

        $post->image = $result->uri;
	}

	/**
	 * Binds the user's webcam photo
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function bindWebcam(EasyBlogPost &$blog)
	{
		// Get the title
		$title = $this->input->get('title', '', 'default');

		if (!$title) {
			$post->title = JText::_('COM_EASYBLOG_DEFAULT_WEBCAM_PHOTO_TITLE');
		}

		$fileName = $this->input->get('fileName', '', 'default');
		$tmpPath = JPATH_ROOT . '/tmp/' . $fileName;
		$tmpPath = JPath::clean($tmpPath);

		// Construct a dummy file object
		$file = array('name' => $fileName, 'tmp_name' => $tmpPath);

		// Upload this file into the user's images folder.
        // Load up media manager now
        $mm = EB::mediamanager();
        $result = $mm->upload($file, 'user:' . $this->my->id);

        $post->image = $result->uri;
	}

	/**
	 * Validates a quick post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate()
	{
		$dataType = $this->input->get('dataType', '', 'cmd');

		$fileName = $this->input->get('fileName', '', 'default');

		if (empty($fileName)) {
			$exception = EB::exception('COM_EASYBLOG_QUICKPOST_UPLOAD_PICTURE', 'error');

			return $exception;
		}

		return true;
	}

	/**
	 * Since quotes are stored in the title, we don't really need to do anything here
	 */
	public function afterSave( &$blog )
	{
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_( 'COM_EASYBLOG_MICROBLOG_PHOTO_POSTED_SUCCESSFULLY' );
	}

	public function format(EasyBlogPost &$blog)
	{
		// Find and replace all images in intro.
		$obj			= self::getAndRemoveImages($blog->intro);

		if ($obj) {
			$blog->intro 	= $obj->content;
			$blog->images 	= $obj->images;
		}

		// Lets strip out the images from the text / content.
		$obj = self::getAndRemoveImages($blog->content);

		if ($obj) {
			$blog->content 	= $obj->content;
			$blog->images	= array_merge($obj->images, $blog->images);
		}

		return $blog;
	}

	public static function getAndRemoveImages( $content )
	{
		//try to search for the 1st img in the blog
		$img            = '';
		$pattern		= '#<img[^>]*>#i';
		$result 		= array();

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[ 0 ] ) && !empty( $matches[ 0 ] ) )
		{

			$images 	= $matches[ 0 ];

			if( !is_array( $images ) )
			{
				$images 	= array( $images );
			}

			foreach( $images as $image )
			{
				$content 	= str_ireplace( $image , '' , $content );

				// Get the URL to the image
				$pattern = '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
				preg_match($pattern, $image , $matches);

				if($matches)
				{
					$imgPath	= $matches[1];
					$source		= EB::image()->rel2abs($imgPath , JURI::root());

					$result[]	= $source;
				}
			}

		}

		$obj 			= new stdClass();
		$obj->content	= $content;
		$obj->images	= $result;

		return $obj;
	}
}
