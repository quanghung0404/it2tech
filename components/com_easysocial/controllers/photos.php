<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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

class EasySocialControllerPhotos extends EasySocialController
{
    /**
     * Retrieves a particular photo object
     *
     * @since   1.0
     * @access  public
     */
    public function getPhoto()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the current view
        $view   = $this->getCurrentView();

        // Get the photo object
        $id     = JRequest::getInt( 'id' );
        $photo  = FD::table( 'Photo' );
        $photo->load( $id );

        if( !$id || !$photo->id )
        {
            $this->view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_PHOTO_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $this->view->call( __FUNCTION__ );
        }

        // Get other attributes
        $attributes = JRequest::getVar( 'attr' );

        // Load up photo library
        $lib        = FD::photo( $photo->uid , $photo->type , $photo );

        // Determine if the person can really obtain information about this photo
        if( !$lib->viewable() )
        {
            return $view->call( 'restricted' , $lib );
        }

        return $view->call( __FUNCTION__ , $photo , $attributes );
    }

    /**
     * Allows caller to upload photos
     *
     * @since   1.0
     * @access  public
     */
    public function upload( $isAvatar = false )
    {
        // Check for request forgeries
        FD::checkToken();

        // Only registered users should be allowed to upload photos
        FD::requireLogin();

        // Get the current view
        $view   = $this->getCurrentView();

        // Get current user.
        $my     = FD::user();

        // Load up the configuration
        $config     = FD::config();

        // Check if the photos is enabled
        if (!$config->get('photos.enabled')) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_ALBUMS_PHOTOS_DISABLED'), SOCIAL_MSG_ERROR);
            return $view->call(__FUNCTION__);
        }

        // Load the album table
        $albumId    = JRequest::getInt( 'albumId' );
        $album      = FD::table( 'Album' );
        $album->load( $albumId );

        // Check if the album id provided is valid
        if (!$albumId || !$album->id) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_PHOTOS_INVALID_ALBUM_ID_PROVIDED'), SOCIAL_MSG_ERROR);
            return $view->call( __FUNCTION__ );
        }

        // Get the uid and the type
        $uid = $album->uid;
        $type = $album->type;

        // Load the photo library
        $lib = FD::photo( $uid , $type );

        // Check if the upload is for profile pictures
        if (!$isAvatar) {

            // Check if the person exceeded the upload limit
            if ($lib->exceededUploadLimit()) {
                $view->setMessage( $lib->getErrorMessage( 'upload.exceeded' ) , SOCIAL_MSG_ERROR );
                return $view->call( __FUNCTION__ );
            }

            // Check if the person exceeded the upload limit
            if ($lib->exceededDiskStorage()) {
                $view->setMessage($lib->getErrorMessage(), SOCIAL_MSG_ERROR);
                return $view->call(__FUNCTION__);
            }

            // Check if the person exceeded their daily upload limit
            if ($lib->exceededDailyUploadLimit()) {
                $view->setMessage( $lib->getErrorMessage( 'upload.daily.exceeded' ) , SOCIAL_MSG_ERROR );
                return $view->call( __FUNCTION__ );
            }
        }

        // Set uploader options
        $options = array( 'name' => 'file', 'maxsize' => $lib->getUploadFileSizeLimit() );

        // Get uploaded file
        $uploader = ES::uploader($options);
        $file = $uploader->getFile(null, 'image');

        // If there was an error getting uploaded file, stop.
        if ($file instanceof SocialException) {
            $view->setMessage( $file );
            return $view->call(__FUNCTION__);
        }

        // Load the image object
        $image = FD::image();
        $image->load($file['tmp_name'], $file['name']);

        // Detect if this is a really valid image file.
        if (!$image->isValid()) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_FILE_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Bind the photo data now
        $photo              = FD::table( 'Photo' );
        $photo->uid         = $uid;
        $photo->type        = $type;
        $photo->user_id     = $my->id;
        $photo->album_id    = $album->id;
        $photo->title       = $file[ 'name' ];
        $photo->caption     = '';
        $photo->ordering    = 0;
        $photo->state       = SOCIAL_STATE_PUBLISHED;

        // Set the creation date alias
        $photo->assigned_date   = FD::date()->toMySQL();

        // Cleanup photo title.
        $photo->cleanupTitle();

        // Trigger rules that should occur before a photo is stored
        $photo->beforeStore($file , $image);

        // Try to store the photo.
        $state      = $photo->store();

        if (!$state) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_UPLOAD_ERROR_STORING_DB' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // If album doesn't have a cover, set the current photo as the cover.
        if (!$album->hasCover()) {
            $album->cover_id    = $photo->id;

            // Store the album
            $album->store();
        }

        // Get the photos library
        $photoLib   = FD::get('Photos', $image);

        // Get the storage path for this photo
        $storageContainer = FD::cleanPath($config->get('photos.storage.container'));
        $storage    = $photoLib->getStoragePath($album->id, $photo->id);
        $paths      = $photoLib->create($storage);

        // We need to calculate the total size used in each photo (including all the variants)
        $totalSize  = 0;

        // Create metadata about the photos
        if($paths) {

            foreach ($paths as $type => $fileName) {
                $meta               = FD::table( 'PhotoMeta' );
                $meta->photo_id     = $photo->id;
                $meta->group        = SOCIAL_PHOTOS_META_PATH;
                $meta->property     = $type;
                // do not store the container path as this path might changed from time to time
                $tmpStorage = str_replace('/' . $storageContainer . '/', '/', $storage);
                $meta->value = $tmpStorage . '/' . $fileName;
                $meta->store();

                // We need to store the photos dimension here
                list($width, $height, $imageType, $attr) = getimagesize(JPATH_ROOT . $storage . '/' . $fileName);

                // Set the photo size
                $totalSize += filesize(JPATH_ROOT . $storage . '/' . $fileName);

                // Set the photo dimensions
                $meta               = FD::table('PhotoMeta');
                $meta->photo_id     = $photo->id;
                $meta->group        = SOCIAL_PHOTOS_META_WIDTH;
                $meta->property     = $type;
                $meta->value        = $width;
                $meta->store();

                $meta               = FD::table('PhotoMeta');
                $meta->photo_id     = $photo->id;
                $meta->group        = SOCIAL_PHOTOS_META_HEIGHT;
                $meta->property     = $type;
                $meta->value        = $height;
                $meta->store();
            }
        }

        // Set the total photo size
        $photo->total_size = $totalSize;
        $photo->store();

        // After storing the photo, trigger rules that should occur after a photo is stored
        $photo->afterStore($file, $image);

        // Determine if we should create a stream item for this upload
        $createStream   = JRequest::getBool( 'createStream' );

        // Add Stream when a new photo is uploaded
        if ($createStream) {
            $photo->addPhotosStream( 'create' );
        }

        // Assign badge to user
        $photo->assignBadge('photos.create', $my->id);

        if ($isAvatar) {
            return $photo;
        }

        return $view->call( __FUNCTION__ , $photo , $paths );
    }


    /**
     * Posting photos via story
     *
     * @since   1.0
     * @access  public
     */
    public function uploadStory()
    {
        // Check for request forgeries
        ES::checkToken();

        // Only registered users should be allowed to upload photos
        ES::requireLogin();

        // Get user access
        $access = $this->my->getAccess();

        // Get the uid and type
        $uid  = $this->input->get('uid', 0, 'int');
        $type = $this->input->get('type', '', 'cmd');

        // Load up the photo library
        $lib = FD::photo($uid, $type);

        // Determines if the person exceeded their upload limit
        if ($lib->exceededUploadLimit()) {
            $this->view->setMessage($lib->getError(), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Determines if the person exceeded their daily upload limit
        if ($lib->exceededDailyUploadLimit()) {
            $this->view->setMessage($lib->getError(), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Define uploader options
        $options = array('name' => 'file', 'maxsize' => $lib->getUploadFileSizeLimit());

        // Get uploaded file
        $uploader = ES::uploader($options);
        $file = $uploader->getFile(null, 'image');

        // If there was an error getting uploaded file, stop.
        if ($file instanceof SocialException) {
            $this->view->setMessage($file, SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Load the iamge object
        $image = ES::image();
        $image->load($file['tmp_name'], $file['name']);

        // Detect if this is a really valid image file.
        if (!$image->isValid()) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_PHOTOS_INVALID_FILE_PROVIDED'), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Load up the album's model.
        $albumsModel = ES::model('Albums');

        // Create the default album if necessary
        $album = $albumsModel->getDefaultAlbum($uid, $type, SOCIAL_ALBUM_STORY_ALBUM);

        // Bind photo data
        $photo = ES::table('Photo');
        $photo->uid = $uid;
        $photo->type = $type;
        $photo->user_id = $this->my->id;
        $photo->album_id = $album->id;
        $photo->title = $file['name'];
        $photo->caption = '';
        $photo->ordering = 0;

        // Set the creation date alias
        $photo->assigned_date = FD::date()->toMySQL();

        // Trigger rules that should occur before a photo is stored
        $photo->beforeStore($file, $image);

        // Try to store the photo.
        $state = $photo->store();

        if (!$state) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_PHOTOS_UPLOAD_ERROR_STORING_DB'), SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Load the photos model
        $photosModel = ES::model('Photos');

        // Get the storage path for this photo
        $storage = ES::call('Photos', 'getStoragePath', array($album->id, $photo->id));

        // Get the photos library
        $photoLib = ES::get('Photos', $image);
        $paths = $photoLib->create($storage);

        // Create metadata about the photos
        if ($paths) {

            foreach ($paths as $type => $fileName) {

                $meta = ES::table('PhotoMeta');
                $meta->photo_id = $photo->id;
                $meta->group = SOCIAL_PHOTOS_META_PATH;
                $meta->property = $type;
                $meta->value = $storage . '/' . $fileName;
                $meta->store();

                // We need to store the photos dimension here
                list($width, $height, $imageType, $attr) = getimagesize(JPATH_ROOT . $storage . '/' . $fileName);

                // Set the photo dimensions
                $meta = ES::table('PhotoMeta');
                $meta->photo_id = $photo->id;
                $meta->group = SOCIAL_PHOTOS_META_WIDTH;
                $meta->property = $type;
                $meta->value = $width;
                $meta->store();

                // Set the photo height
                $meta = ES::table('PhotoMeta');
                $meta->photo_id = $photo->id;
                $meta->group = SOCIAL_PHOTOS_META_HEIGHT;
                $meta->property = $type;
                $meta->value = $height;
                $meta->store();
            }
        }

        // Assign badge to user
        $photo->assignBadge('photos.create', $this->my->id);

        // After storing the photo, trigger rules that should occur after a photo is stored
        $photo->afterStore($file, $image);

        return $this->view->call(__FUNCTION__, $photo, $paths, $width, $height);
    }


    /**
     * Allows caller to update a photo
     *
     * @since   1.0
     * @access  public
     */
    public function update()
    {
        // Check for request forgeries
        FD::checkToken();

        // User needs to be logged in
        FD::requireLogin();

        // Get the photo id
        $id     = JRequest::getInt( 'id' );

        // Get the current view
        $view   = $this->getCurrentView();

        // Loads up the photo table
        $photo  = FD::table( 'Photo' );
        $photo->load( $id );

        // Test if the id provided is valid.
        if( !$id || !$photo->id )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NOT_FOUND' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Load up the photos library
        $lib    = FD::photo( $photo->uid , $photo->type , $photo );

        // Test if the user is really allowed to edit the photo
        if( !$lib->editable() )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NOT_ALLOWED_TO_EDIT_PHOTO' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Get the posted data
        $post   = JRequest::get( 'post' );

        // Should we allow the change of the album?
        // $photo->album_id     = $album->id;
        $photo->title       = JRequest::getVar( 'title' );
        $photo->caption     = JRequest::getVar( 'caption' );

        // Set the assigned_date if necessary
        $photoDate  = JRequest::getVar( 'date' , '' );

        if( $photoDate )
        {
            $date   = FD::date( $photoDate );

            $photo->assigned_date   = $date->toMySQL();
        }

        // Try to store the photo now
        $state  = $photo->store();

        if( !$state )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_ERROR_SAVING_PHOTO' ) , SOCIAL_MSG_ERROR );

            return $view->call( __FUNCTION__ );
        }

        // Bind the location for the photo if necessary
        $address    = JRequest::getVar( 'address' );
        $latitude   = JRequest::getVar( 'latitude' );
        $longitude  = JRequest::getVar( 'longitude' );


        if( !empty( $address ) && !empty( $latitude) && !empty( $longitude) )
        {
            $state  = $photo->bindLocation( $address , $latitude , $longitude );
        }

        return $view->call( __FUNCTION__ , $photo );
    }

    /**
     * Allows caller to delete an album
     *
     * @since   1.0
     * @access  public
     */
    public function delete()
    {
        // Check for request forgeries
        FD::checkToken();

        // Only logged in user is allowed to proceed here.
        FD::requireLogin();

        // Get id from request
        $id     = JRequest::getInt( 'id' );

        // Get the view
        $view   = $this->getCurrentView();

        // Load the photo table
        $photo  = FD::table( 'Photo' );
        $photo->load( $id );

        if( !$id && !$photo->id )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Get the current logged in user
        $my     = FD::user();

        // Load the photo library
        $lib    = FD::photo( $photo->uid , $photo->type , $photo );

        // Test if the user is allowed to delete the photo
        if( !$lib->deleteable() )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NO_PERMISSION_TO_DELETE_PHOTO' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Try to delete the photo
        $state      = $photo->delete();

        if( !$state )
        {
            $view->setMessage( $photo->getError() , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Get the new cover
        $newCover   = $photo->getAlbum()->getCoverObject();

        return $view->call( __FUNCTION__, $newCover );
    }

    /**
     * Allows caller to rotate a photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function rotate()
    {
        // Check for request forgeries
        FD::checkToken();

        // Only registered users should be allowed to rotate photos
        FD::requireLogin();

        // Get the current view
        $view = $this->getCurrentView();

        // Get photo id
        $id = $this->input->get('id', 0, 'int');

        // Get photo
        $photo = FD::table('Photo');
        $photo->load($id);

        if (!$id || !$photo->id) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_PHOTO_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call(__FUNCTION__);
        }

        // Determine if the user has access to rotate the photo
        $lib = FD::photo($photo->uid, $photo->type, $photo);

        if (!$lib->canRotatePhoto()) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NOT_ALLOWED_TO_ROTATE_THIS_PHOTO' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Rotate photo
        $tmpAngle = $this->input->get('angle', 0, 'int');

        // Get the real angle now.
        $angle = $photo->getAngle() + $tmpAngle;

        // Update the angle
        $photo->updateAngle($angle);

        // Delete the previous images that are generated except the stock version
        $photo->deletePhotos(array('thumbnail', 'large', 'original', 'featured', 'square'));

        // Rotate the photo
        $image = FD::image();
        $image->load($photo->getPath('stock'));

        // Rotate the new image
        $image->rotate($angle);

        // Save photo
        $photoLib   = FD::get('Photos', $image);

        // Get the storage path
        $storage    = $photoLib->getStoragePath($photo->album_id, $photo->id);

        $exclude    = array('stock');
        $paths      = $photoLib->create($storage, $exclude, $photo->title . '_rotated_' . $angle);

        // When a photo is rotated, we would also need to rotate the tags as well
        $photo->rotateTags($tmpAngle);

        // Create metadata about the photos
        foreach ($paths as $type => $fileName) {

            $meta               = FD::table( 'PhotoMeta' );
            $meta->photo_id     = $photo->id;
            $meta->group        = SOCIAL_PHOTOS_META_PATH;
            $meta->property     = $type;
            $meta->value        = '/' . $photo->album_id . '/' . $photo->id . '/' . $fileName;
            $meta->store();

            // We need to store the photos dimension here
            list($width, $height, $imageType, $attr) = getimagesize(JPATH_ROOT . $storage . '/' . $fileName);

            // Delete previous meta data first
            $photo->updateMeta(SOCIAL_PHOTOS_META_WIDTH, $type, $width);
            $photo->updateMeta(SOCIAL_PHOTOS_META_HEIGHT, $type, $height);
        }

        // Reload photo
        $newPhoto  = FD::table('Photo');
        $newPhoto->load($id);

        // Once image is rotated, we'll need to update the photo source back to "joomla" because
        // we will need to re-upload the image again when synchroinization happens.
        $newPhoto->storage     = SOCIAL_STORAGE_JOOMLA;
        $newPhoto->store();

        return $view->call(__FUNCTION__, $newPhoto, $paths);
    }

    /**
     * Allows caller to feature a photo
     *
     * @since   1.0
     * @access  public
     */
    public function feature()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        $id     = JRequest::getInt( 'id' );

        // Get current view
        $view   = $this->getCurrentView();

        $photo  = FD::table( 'Photo' );
        $photo->load( $id );

        if( !$id || !$photo->id )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Load up photo library
        $lib    = FD::photo( $photo->uid , $photo->type , $photo );

        // Test if the person is allowed to feature the photo
        if( !$lib->featureable() )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NOT_ALLOWED_TO_FEATURE_PHOTO' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // If photo is previously not featured, it is being featured now.
        $isFeatured     = !$photo->featured ? true : false;

        // Toggle the featured state
        $photo->toggleFeatured();

        return $view->call( __FUNCTION__ , $isFeatured );
    }

    /**
     * Allows caller to move a photo over to album
     *
     * @since   1.0
     * @access  public
     * @return
     */
    public function move()
    {
        // Check for request forgeries
        FD::checkToken();

        // Only allow logged in user
        FD::requireLogin();

        // Get the view
        $view       = $this->getCurrentView();

        // Get the current photo id.
        $id         = JRequest::getInt( 'id' );
        $photo  = FD::table( 'Photo' );
        $photo->load( $id );

        // Only allow valid photos
        if (!$id || !$photo->id) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Get the target album id to move this photo to.
        $albumId    = JRequest::getInt( 'albumId' );
        $album      = FD::table( 'Album' );
        $album->load( $albumId );

        if (!$albumId || !$album->id) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ALBUM_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Load the library
        $lib    = FD::photo( $photo->uid , $photo->type , $photo );

        // Check if the user can actually manage this photo
        if (!$lib->canMovePhoto()) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NO_PERMISSION_TO_MOVE_PHOTO' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Load up the target album
        $albumLib   = FD::albums( $album->uid , $album->type , $album );

        // Check if the target album is owned by the user
        if( !$albumLib->isOwner() )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NO_PERMISSION_TO_MOVE_PHOTO' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Try to move the photo to the new album now
        if (!$photo->move($albumId)) {
            $view->setMessage( $photo->getError() , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_PHOTO_MOVED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
        return $view->call( __FUNCTION__ );
    }

    /**
     * Deletes a tag
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteTag()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require the user to be logged in
        FD::requireLogin();

        // Load the tag object
        $id     = JRequest::getInt( 'tag_id' );
        $tag    = FD::table( 'PhotoTag' );
        $tag->load( $id );

        // Get the current view
        $view   = $this->getCurrentView();

        // Get posted data from request
        $post   = JRequest::get( 'POST' );

        // Get the person that created the tag
        $creator    = FD::user( $tag->created_by );

        // Determines if the tag can be deleted
        if( !$tag->deleteable() )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NOT_ALLOWED_TO_DELETE_TAG' ) , SOCIAL_MSG_ERROR );
            $view->call( __FUNCTION__ );
        }

        // Try to delete the tag
        if( !$tag->delete() )
        {
            $view->setMessage( $tag->getError() , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // @points: photos.untag
        // Deduct points from the user that created the tag since the tag has been deleted.
        $photo->assignPoints( 'photos.untag' , $creator->id );

        return $view->call( __FUNCTION__ );
    }

    /**
     * Creates a new tag
     *
     * @since   1.0
     * @access  public
     */
    public function createTag()
    {
        // Check for request forgeries
        FD::checkToken();

        // Require only logged in user to perform this action
        FD::requireLogin();

        // Get the photo id from the request.
        $id     = JRequest::getInt('photo_id');

        // Get the current logged in user
        $my     = FD::user();

        // Get the current view
        $view   = $this->getCurrentView();

        // Load up the photo table
        $photo  = FD::table( 'Photo' );
        $photo->load( $id );

        // Check if the photo id is valid
        if( !$id || !$photo->id )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_PHOTO_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__, null, $photo );
        }

        // Load up the photo library
        $lib    = FD::photo( $photo->uid , $photo->type , $photo );

        // Test if the user is really allowed to tag this photo
        if( !$lib->taggable() )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NOT_ALLOWED_TO_TAG_PHOTO' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__, null, $photo );
        }

        // Get posted data from request
        $post   = JRequest::get( 'POST' );

        // Bind the new data on the post
        $tag    = FD::table( 'PhotoTag' );
        $tag->bind( $post );

        // If there's empty label and the uid is not supplied, we need to throw an error
        if( empty( $tag->label ) && !$tag->uid )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_EMPTY_TAG_NOT_ALLOWED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__, null, $photo );
        }

        // Reset the id of the tag since this is a new tag, it should never contain an id
        $tag->id            = null;
        $tag->photo_id      = $photo->id;
        $tag->created_by    = $my->id;

        // Try to save the tag now
        $state  = $tag->store();

        // Try to store the new tag.
        if( !$state )
        {
            $view->setMessage( $tag->getError() , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__, null, $photo );
        }

        // @points: photos.tag
        // Assign points to the current user for tagging items
        $photo->assignPoints( 'photos.tag' , $my->id );

        // Only notify persons if the photo is tagging a person
        if ($tag->uid && $tag->type == 'person' && $tag->uid != $my->id) {

            // Set the email options
            $emailOptions   = array(
                'title'         => 'COM_EASYSOCIAL_EMAILS_TAGGED_IN_PHOTO_SUBJECT',
                'template'      => 'site/photos/tagged',
                'photoTitle'        => $photo->get( 'title' ),
                'photoPermalink'    => $photo->getPermalink(true, true),
                'photoThumbnail'    => $photo->getSource( 'thumbnail' ),
                'actor'             => $my->getName(),
                'actorAvatar'       => $my->getAvatar(SOCIAL_AVATAR_SQUARE),
                'actorLink'         => $my->getPermalink(true, true)
            );

            $systemOptions  = array(
                'context_type'  => 'tagging',
                'context_ids'   => $photo->id,
                'uid'           => $tag->id,
                'url'           => $photo->getPermalink(false, false, 'item', false),
                'actor_id'      => $my->id,
                'target_id'     => $tag->uid,
                'aggregate'     => false
            );

            // Notify user
            FD::notify( 'photos.tagged' , array($tag->uid), $emailOptions , $systemOptions );

            // Assign a badge to the user
            $photo->assignBadge( 'photos.tag' , $my->id );

            // Assign a badge to the user that is being tagged
            if( $my->id != $tag->uid )
            {
                $photo->assignBadge( 'photos.superstar' , $tag->uid );
            }
        }

        return $view->call( __FUNCTION__ , $tag , $photo );
    }

    /**
     * Allows caller to retrieve a list of tags
     *
     * @since   1.0
     * @access  public
     */
    public function getTags()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the current view
        $view   = $this->getCurrentView();

        // Get the photo object.
        $id     = JRequest::getInt( 'photo_id' );
        $photo  = FD::table( 'Photo' );
        $photo->load( $id );

        if( !$id || !$photo->id )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_PHOTO_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Retrieve the list of tags for this photo
        $tags   = $photo->getTags();

        return $view->call( __FUNCTION__ , $tags );
    }

    /**
     * Allows caller to remove a tag
     *
     * @since   1.0
     * @access  public
     */
    public function removeTag()
    {
        // Check for request forgeries
        FD::checkToken();

        // Allow only logged in users
        FD::requireLogin();

        // Get the tag object
        $id     = JRequest::getInt( 'id' );
        $tag    = FD::table( 'PhotoTag' );
        $tag->load( $id );

        if( !$id || !$tag->id )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_TAG_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Get the current view
        $view   = $this->getCurrentView();

        // Get the current logged in user
        $my     = FD::user();

        // If user is not allowed to delete the tag, throw an error
        if( !$tag->deleteable() )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_NOT_ALLOWED_TO_DELETE_TAG' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Try to delete the tag.
        $state  = $tag->delete();

        if( !$state )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_ERROR_REMOVING_TAG' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        return $view->call( __FUNCTION__ );
    }

    /**
     * Allows caller to set profile photo based on the photo that they have.
     *
     * @since   1.0
     * @access  public
     * @return
     */
    public function createAvatar()
    {
        // Check for request forgeries
        $this->checkToken();

        // Only registered users should be allowed to upload photos
        $this->requireLogin();

        // Get the photo id
        $id = $this->input->get('id', 0, 'int');

        // Try to load the photo.
        $photo = ES::table('Photo');
        $photo->load($id);

        // Try to load the photo with the provided id.
        if (!$id || !$photo->id) {
            $this->view->setMessage('COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED', SOCIAL_MSG_ERROR);

            return $this->view->call(__FUNCTION__);
        }

        // Get the photos lib
        $lib = ES::photo($photo->uid, $photo->type, $photo);

        if (!$lib->canUseAvatar()) {
            $this->view->setMessage('COM_EASYSOCIAL_PHOTOS_NO_PERMISSION_TO_USE_PHOTO_AS_AVATAR', SOCIAL_MSG_ERROR);
            return $this->view->call(__FUNCTION__);
        }

        // Get the image object for the photo
        // Use "original" not "stock" because it might be rotated before this.
        $image = $photo->getImageObject('stock');

        if ($image === false) {
            $image = $photo->getImageObject('original');
        }

        // Need to rotate as necessary here because we're loading up using the stock photo and the stock photo
        // is as is when the user initially uploaded.
        $image->rotate($photo->getAngle());

        $jConfig = ES::jConfig();

        // Store the image temporarily
        $tmp = $jConfig->getValue('tmp_path');
        $tmpPath = $tmp . '/' . md5($photo->id) . $image->getExtension();

        // If the temporary file exists, we need to delete it first
        if (JFile::exists($tmpPath)) {
            JFile::delete($tmpPath);
        }
        $image->save($tmpPath);
        unset($image);

        // If photo was stored remotely, we need to delete the downloaded files
        if ($photo->isStoredRemotely()) {
            $photo->deletePhotoFolder();
        }
        
        $image = ES::image();
        $image->load($tmpPath);

        // Load up the avatar library
        $avatar = ES::avatar($image, $photo->uid, $photo->type);

        // Crop the image to follow the avatar format. Get the dimensions from the request.
        $width = JRequest::getVar('width');
        $height = JRequest::getVar('height');
        $top = JRequest::getVar('top');
        $left = JRequest::getVar('left');

        // We need to get the temporary path so that we can delete it later once everything is done.
        $avatar->crop($top, $left, $width, $height);

        // Create the avatars now
        $avatar->store($photo);

        // Delete the temporary file.
        JFile::delete($tmpPath);

        return $this->view->call(__FUNCTION__, $photo);
    }

    /**
     * Allows caller to create an avatar by posted the $_FILE data
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function createAvatarFromFile()
    {
        // Check for request forgeries
        FD::checkToken();

        // Only registered users should be allowed to upload photos
        FD::requireLogin();

        // Get the current view
        $view       = $this->getCurrentView();
        $config     = FD::config();

        // Get the unique item id
        $uid    = JRequest::getInt( 'uid' );
        $type   = JRequest::getCmd( 'type' );

        // Get current user.
        $my     = FD::user();

        if( !$uid && !$type )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
            return $view->call( 'createAvatar' );
        }

        // Load up the photo library
        $lib    = FD::photo( $uid , $type );

        // Set uploader options
        $options = array( 'name' => 'avatar_file' , 'maxsize' => $lib->getUploadFileSizeLimit() );

        // Get uploaded file
        $uploader = ES::uploader($options);
        $file = $uploader->getFile(null, 'image');

        // If there was an error getting uploaded file, stop.
        if ($file instanceof SocialException) {
            $view->setMessage($file);
            return $view->call('createAvatar');
        }

        // Load the image
        $image = FD::image();
        $image->load($file['tmp_name'], $file['name']);

        // Check if there's a profile photos album that already exists.
        $albumModel = FD::model('Albums');

        // Retrieve the default album for this node.
        $album = $lib->getDefaultAlbum();

        $photo = FD::table('Photo');
        $photo->uid = $uid;
        $photo->type = $type;
        $photo->user_id = $my->id;
        $photo->album_id = $album->id;
        $photo->title = $file['name'];
        $photo->caption = '';
        $photo->ordering = 0;

        // Set the creation date alias
        $photo->assigned_date = FD::date()->toMySQL();

        // We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
        $photo->state = SOCIAL_PHOTOS_STATE_TMP;

        // Try to store the photo first
        $state = $photo->store();

        // Bind any exif data if there are any.
        // Only bind exif data for jpg files (if want to add tiff, then do add it here)
        if ($image->hasExifSupport()) {
            $photo->mapExif($file);
        }

        if( !$state )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_PHOTOS_ERROR_CREATING_IMAGE_FILES' ) , SOCIAL_MSG_ERROR );
            return $view->call( 'createAvatar' );
        }

        // Push all the ordering of the photo down
        $photosModel = FD::model( 'photos' );
        $photosModel->pushPhotosOrdering( $album->id , $photo->id );

        // Render photos library
        $photoLib   = FD::get( 'Photos' , $image );
        $storage    = $photoLib->getStoragePath( $album->id , $photo->id );
        $paths      = $photoLib->create( $storage );

        // Create metadata about the photos
        foreach( $paths as $type => $fileName )
        {
            $meta               = FD::table( 'PhotoMeta' );
            $meta->photo_id     = $photo->id;
            $meta->group        = SOCIAL_PHOTOS_META_PATH;
            $meta->property     = $type;
            $meta->value        = $storage . '/' . $fileName;

            $meta->store();
        }

        // Retrieve the original photo again.
        $image      = $photo->getImageObject( 'original' );

        return $view->call( 'createAvatar' , $photo );
    }

    /**
     * Allows caller to create an avatar by posted the $_FILE data
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function createAvatarFromWebcam()
    {
        // Check for request forgeries
        ES::checkToken();

        // Only registered users should be allowed to upload photos
        ES::requireLogin();

        // Get the unique item id
        $uid = $this->input->get('uid', 0, 'int');
        $type = $this->input->get('type', '', 'cmd');

        $filename = JRequest::getVar('file');

        if (!$uid && !$type) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_PHOTOS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
            return $this->view->call('createAvatar');
        }
        
        // Get Joomla's temporary path
        $jConfig = ES::jConfig();

        $tmp = $jConfig->getValue('tmp_path');
        $filePath = $tmp . '/' . $filename;

        // Load the image
        $image = ES::image();
        $image->load($filePath);

        $avatar = ES::avatar($image, $uid, SOCIAL_TYPE_USER);

        // Check if there's a profile photos album that already exists.
        $albumModel = ES::model('Albums');

        // Retrieve the default album for this node.
        $album = $albumModel->getDefaultAlbum($uid, SOCIAL_TYPE_USER, SOCIAL_ALBUM_PROFILE_PHOTOS);

        // we need to update the album user_id to this current user.
        $album->user_id = $uid;
        $album->store();

        $photo = ES::table('Photo');
        $photo->uid = $uid;
        $photo->type = SOCIAL_TYPE_USER;
        $photo->user_id = $uid;
        $photo->album_id = $album->id;
        $photo->title = $filename;
        $photo->caption = '';
        $photo->ordering = 0;

        // Set the creation date alias
        $photo->assigned_date = ES::date()->toMySQL();

        // We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
        $photo->state = SOCIAL_PHOTOS_STATE_TMP;

        // Try to store the photo first
        $state = $photo->store();

        if (!$state) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_PHOTOS_ERROR_CREATING_IMAGE_FILES'), SOCIAL_MSG_ERROR);
            return $this->view->call('createAvatar');
        }

        // Push all the ordering of the photo down
        $photosModel = FD::model('photos');
        $photosModel->pushPhotosOrdering($album->id, $photo->id);

        // Render photos library
        $photoLib = FD::get('Photos', $image);
        $storage = $photoLib->getStoragePath($album->id, $photo->id);
        $paths = $photoLib->create($storage);

        // Create metadata about the photos
        foreach ($paths as $type => $fileName) {
            $meta = FD::table( 'PhotoMeta' );
            $meta->photo_id = $photo->id;
            $meta->group = SOCIAL_PHOTOS_META_PATH;
            $meta->property = $type;
            $meta->value = $storage . '/' . $fileName;

            $meta->store();
        }

        // Save as avatar
        $options = array('addstream' => false);
        $avatar->store($photo, $options);

        // Add stream item
        $photo->addPhotosStream('uploadAvatar', $photo->assigned_date);

        // Retrieve the original photo again.
        $image = $photo->getImageObject('original');

        return $this->view->call('createAvatar', $photo);
    }
    
}
