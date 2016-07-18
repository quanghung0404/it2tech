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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewQuickpost extends EasyBlogView
{
	/**
	 * Saves a quick post item
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Get the quickpost type
		$type = $this->input->get('type', '', 'cmd');

		// Test if microblogging is allowed
		if (!$this->config->get('main_microblog')) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG'), 'error');
			
			return $this->ajax->reject($exception);
		}

		// Let's test if the user is a valid user.
		if (!$this->acl->get('add_entry') || $this->my->guest) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG'), 'error');

			return $this->ajax->reject($exception);
		}

		// Ensure that the type is provided otherwise we wouldn't know how to process this
		if (!$type) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_SPECIFY_POST_TYPE'), 'error');
			return $this->ajax->reject($exception);
		}

		// Check if category is set
		$category = $this->input->get('category', '', 'int');

		if (!$category) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_SELECT_CATEGORY_FOR_POST'), 'error');
			return $this->ajax->reject($exception);
		}

		// Get the quickpost object
		$quickpost = $this->getQuickpostObject($type);

		if ($quickpost === false) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_INVALID_POST_TYPE'), 'error');
			return $ajax->reject($exception);
		}

		// Type validations are done here
		$state = $quickpost->validate();

		if ($state !== true) {
			return $this->ajax->reject($state);
		}

		// Load up the blog object
		$data = array();
		$arrData = $this->input->getArray('post');

		// need to prepare the data before binding with post lib for quick post item
        // quick post has limited property. we will just manually assign.

        $data['category_id'] = $arrData['category'];
        $data['categories'] = array($arrData['category']);

		$data['created'] = EB::date()->toSql();
		$data['modified'] = EB::date()->toSql();
		$data['publish_up'] = EB::date()->toSql();
		$data['created_by'] = $this->my->id;
		$data['access'] = $this->acl->get('enable_privacy') ? $arrData['privacy'] : 0;
		$data['frontpage'] = $this->acl->get('contribute_frontpage') ? true : false;

		// If user does not have privilege to store, we need to mark as pending review
		if (!$this->acl->get('publish_entry')) {
			$data['published'] = EASYBLOG_POST_PENDING;
		} else {
			$data['published'] = EASYBLOG_POST_PUBLISHED;
		}

		// quick post is always a sitewide item
		$data['source_id'] = 0;
		$data['source_type'] = EASYBLOG_POST_SOURCE_SITEWIDE;

		// we need to set this as legacy post as the post did not go through composer.
		$data['doctype'] = EASYBLOG_POST_DOCTYPE_LEGACY;

		$data['tags'] = $arrData['tags'];

		// we will let the quickpost adapther to handle the title, content, intro text and .
		$data['title'] = isset($arrData['title']) ? $arrData['title'] : '';
		$data['content'] = '';
		$data['intro'] = '';
		$data['posttype'] = '';
		$data['allowcomment'] = 1;

		$saveOptions = array('applyDateOffset' => false, 'skipCustomFields' => true);
		
		$post = EB::post();

		// Create post revision
		$post->create($saveOptions);

		// binding
		$post->bind($data);

		// process the content
		$quickpost->bind($post);

		try {
			$post->save($saveOptions);

		} catch(EasyBlogException $exception) {

			// Reject if there is an error while saving post
			return $this->ajax->reject($exception);
		}

		//save assets * for now only applied to link post
		if ($post->posttype == 'link') {
			$quickpost->saveAssets($post);
		}

		$message = $quickpost->getSuccessMessage();

		if ($post->isPending()) {
			$message = JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_SAVED_REQUIRE_MODERATION');
		}

		return $this->ajax->resolve(EB::exception($message, 'success'));
	}

	/**
	 * Retrieves the quickpost object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getQuickpostObject($type)
	{
		$adapter = EB::quickpost()->getAdapter($type);

		return $adapter;
	}

	/**
	 * Retrieves the video embed content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVideo()
	{
		$link = $this->input->get('link', '', 'default');

		// Get the embedded codes for the video
		$lib = EB::videos();
		$embed = $lib->getProviderEmbedCodes($link);

		return $this->ajax->resolve($embed);
	}

	/**
	 * Handles photo uploads via the microblogging page.
	 *
	 * @access	public
	 * @param	null
	 **/
	public function uploadPhoto()
	{
		$my 		= JFactory::getUser();
		$config 	= EasyBlogHelper::getConfig();

		if( !$my->id )
		{
			return $this->outputJSON(
				array(
						'type'		=> 'error',
						'message'	=> JText::_( 'You need to be logged in first' )
				)
			);
		}

		$file 				= JRequest::getVar( 'photo-source', '', 'files', 'array' );

		if( !isset( $file['tmp_name'] ) )
		{
			return $this->outputJSON(
				array(
						'type'		=> 'error',
						'message'	=> JText::_( 'There is an error when uploading the image to the server. Perhaps the temporary folder <strong>upload_tmp_path</strong> was not configured correctly.' )
				)
			);
		}

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );

		// @rule: Photos should be stored in the user's home folder by default.
		$imagePath			= str_ireplace( array( "/" , "\\" ) , DIRECTORY_SEPARATOR , rtrim( $config->get('main_image_path') , '/') );
		$userUploadPath    	= JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath . DIRECTORY_SEPARATOR . $my->id);
		$storageFolder		= JPath::clean( $userUploadPath );

		// @rule: Get the image URI
		$imageURI			= rtrim( str_ireplace( '\\' , '/' , $config->get( 'main_image_path') ) , '/' ) . '/' . $my->id;
		$imageURI			= rtrim( JURI::root() , '/' ) . '/' . $imageURI;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name']	= JFile::makeSafe($file['name']);

		// After making the filename safe, and the first character begins with . , we need to rename this file. Perhaps it's a unicode character
		$file['name']	= trim( $file['name'] );
		$filename		= strtolower( $file['name'] );

		if(strpos( $filename , '.' ) === false )
		{
		    $filename	= EB::date()->toFormat( "%Y%m%d-%H%M%S" ) . '.' . $filename;
		}
		else if( strpos( $filename , '.' ) == 0 )
		{
			$filename	= EB::date()->toFormat( "%Y%m%d-%H%M%S" ) . $filename;
		}

		// remove the spacing in the filename.
		$filename 		= str_ireplace(' ', '-', $filename);
		$storagePath 	= JPath::clean( $storageFolder . DIRECTORY_SEPARATOR . $filename );

// 		// @task: try to rename the file if another image with the same name exists
// 		if( JFile::exists( $storagePath ) )
// 		{
// 			$i	= 1;
// 			while( JFile::exists( $storagePath ) )
// 			{
// 				$tmpName	= $i . '_' . EB::date()->toFormat( "%Y%m%d-%H%M%S" ) . '_' . $filename;
// 				$storagePath	= JPath::clean( $storageFolder . DIRECTORY_SEPARATOR . $tmpName );
// 				$i++;
// 			}
// 			$filename	= $tmpName;
// 		}

		$allowed		= EasyImageHelper::canUploadFile( $file );

		if( $allowed !== true )
		{
			return $this->outputJSON(
				array(
						'type'		=> 'error',
						'message'	=> $allowed
				)
			);
		}

		// @rule: Pass to EasyBlogImageHelper to upload the image
		// $result		= EasyImageHelper::upload( $storageFolder , $filename , $file , $imageURI , $storagePath );

// 		// @task: Ensure that images goes through the same resizing format when uploading via media manager.
		$result = new stdClass();
		$result->message    = JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR' );
		$result->item       = '';

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );
		$media 				= new EasyBlogMediaManager();
		$uploaded 			= $media->upload($file, 'user:' . $my->id );

		if( $uploaded !== false )
		{
			$result->message    = JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_SUCCESS' );
			$result->item       = $uploaded;
		}
		else
		{
			// failed.
			$result->item->url  = '';
		}


		return $this->outputJSON(
			array(
					'type'		=> 'success',
					'message'	=> $result->message,
					'uri'		=> $result->item->url
			)
		);
	}
}
