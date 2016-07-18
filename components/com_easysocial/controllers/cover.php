<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import main controller
FD::import( 'site:/controllers/controller' );

jimport( 'joomla.filesystem.file' );

class EasySocialControllerCover extends EasySocialController
{
	/**
	 * Allows caller to create a cover photo
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered member can use this
		FD::requireLogin();

		// Get the coordinates
		$x = $this->input->get('x', 0, 'default');
		$y = $this->input->get('y', 0, 'default');

		// Get photo id from request.
		$id = $this->input->get('id', 0, 'int');

		// Load the photo
		$photo = FD::table('Photo');
		$photo->load($id);

		// Get the unique item id
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'cmd');

		// Check for required variables
		if (!$id || !$photo->id || !$uid || !$type) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PHOTOS_INVALID_PHOTO_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Load up the photo library
		$lib = FD::photo($uid, $type, $photo);

		// Check if the user is allowed to use this photo as a cover.
		if (!$lib->allowUseCover()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PHOTOS_NO_PERMISSION_TO_USE_PHOTO_AS_COVER'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Load the cover
		$cover = FD::table('Cover');
		$state = $cover->load(array('uid' => $uid, 'type' => $type));

		// User does not have a cover.
		if (!$state) {
			$cover->uid = $uid;
			$cover->type = $type;
		}

		// Set the cover to pull from photo
		$cover->setPhotoAsCover($photo->id, $x , $y);

		// Save the cover.
		$cover->store();

		// Check if reposition is true, then don't create stream
		$reposition = $this->input->get('reposition', false, 'bool');

		// @Add stream item when a new profile cover is uploaded
		if (!$reposition) {
			$photo->addPhotosStream('updateCover');
		}

		// Set the photo state to 1 since the user has already confirmed to set it as cover
		$photo->state = SOCIAL_STATE_PUBLISHED;
		$photo->store();

		return $this->view->call(__FUNCTION__, $cover);
	}

	/**
	 * Allows caller to upload a photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function upload()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that the user must be logged in
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Get the unique item stuffs
		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getCmd( 'type' );

		if( !$uid && !$type )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Load the photo library now since we have the unique keys
		$lib 	= FD::photo( $uid , $type );

		// Check if the user is allowed to upload cover photos
		if( !$lib->canUploadCovers() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NO_PERMISSION_TO_UPLOAD_COVER' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Get the current logged in user.
		$my 	= FD::user();

		// Set uploader options
		$options = array( 'name' => 'cover_file' , 'maxsize' => $lib->getUploadFileSizeLimit() );

		// Get uploaded file
		$uploader = ES::uploader($options);
		$file = $uploader->getFile(null, 'image');

		// If there was an error getting uploaded file, stop.
		if ($file instanceof SocialException) {
			$view->setMessage( $file );
			return $view->call( __FUNCTION__ );
		}

		// Load the image
		$image 	= FD::image();
		$image->load( $file[ 'tmp_name' ] , $file[ 'name' ] );

		// Check if there's a profile photos album that already exists.
		$model	= FD::model( 'Albums' );

		// Retrieve the user's default album
		$album 	= $model->getDefaultAlbum( $uid , $type , SOCIAL_ALBUM_PROFILE_COVERS );

		$photo 				= FD::table( 'Photo' );
		$photo->uid 		= $uid;
		$photo->type 		= $type;
		$photo->user_id 	= $my->id;
		$photo->album_id 	= $album->id;
		$photo->title 		= $file[ 'name' ];
		$photo->caption 	= '';
		$photo->ordering	= 0;
		$photo->assigned_date 	= FD::date()->toMySQL();

		// Trigger rules that should occur before a photo is stored
		$photo->beforeStore( $file , $image );

		// Try to store the photo.
		$state 		= $photo->store();

		if( !$state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_ERROR_CREATING_IMAGE_FILES' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Trigger rules that should occur after a photo is stored
		$photo->afterStore( $file , $image );

		// If album doesn't have a cover, set the current photo as the cover.
		if( !$album->hasCover() )
		{
			$album->cover_id 	= $photo->id;

			// Store the album
			$album->store();
		}

		// Render photos library
		$photoLib 	= FD::get( 'Photos' , $image );
		$storage 	= $photoLib->getStoragePath($album->id, $photo->id);
		$paths 		= $photoLib->create($storage);

		// Create metadata about the photos
		foreach( $paths as $type => $fileName )
		{
			$meta 				= FD::table( 'PhotoMeta' );
			$meta->photo_id		= $photo->id;
			$meta->group 		= SOCIAL_PHOTOS_META_PATH;
			$meta->property 	= $type;
			$meta->value		= $storage . '/' . $fileName;

			$meta->store();
		}

		return $view->call( __FUNCTION__ , $photo );
	}

	/**
	 * Allows caller to remove a photo
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only logged in users allowed
		FD::requireLogin();

		// Get the current view
		$view 	= $this->getCurrentView();

		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getCmd( 'type' );

		if( !$uid && !$type )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the cover object
		$cover 	= FD::table( 'Cover' );
		$state 	= $cover->load( array( 'uid' => $uid , 'type' => $type ) );

		if( !$state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Load up the photo library
		$lib 	= FD::photo( $uid , $type , $cover->photo_id );

		if( !$lib->canDeleteCover() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NO_PERMISSION_TO_DELETE_COVER' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Delete the cover.
		$cover->delete();

		return $view->call( __FUNCTION__ );
	}
}
