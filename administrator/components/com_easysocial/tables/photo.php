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

FD::import( 'admin:/tables/table' );
FD::import( 'admin:/includes/stream/dependencies' );
FD::import( 'admin:/includes/indexer/indexer' );

class SocialTablePhoto extends SocialTable implements ISocialIndexerTable, ISocialStreamItemTable
{
    /**
     * The unique id for this record.
     * @var int
     */
    public $id = null;

    /**
     * The unique type id for this record.
     * @var int
     */
    public $uid = null;

    /**
     * The unique type string for this record.
     * @var string
     */
    public $type = null;

    /**
     * The unique type string for this record.
     * @var string
     */
    public $user_id = null;

    /**
     * The album id for this photo
     * @var int
     */
    public $album_id = null;

    /**
     * The title for this photo
     * @var string
     */
    public $title = null;

    /**
     * The caption for this photo
     * @var string
     */
    public $caption = null;

    /**
     * The creation date of this photos
     * @var string
     */
    public $created = null;

    /**
     * The creation date alias of this photo.
     * @var string
     */
    public $assigned_date = null;

    /**
     * The unique type string for this record.
     * @var string
     */
    public $ordering = null;

    /**
     * Sets the total size used for this photo
     * @var int
     */
    public $total_size = null;

    /**
     * The unique type string for this record.
     * @var string
     */
    public $featured = null;

    /**
     * This determines the storage location for this photo
     * @var string
     */
    public $storage = 'joomla';


    /**
     * The state of the photo. default is true
     * @var string
     */
    public $state = null;

    public $_uuid = null;

    static $_photos = array();

    static $_cache = null;

    /**
     * Class Constructor
     *
     * @since   1.0
     * @param   JDatabase
     */
    public function __construct( $db )
    {
        // Create a unique id only for each table instance
        // This is to help controller implement the right element.
        $this->_uuid = uniqid();

        //determide if load method should get from cache variable or not.
        if( is_null( self::$_cache ) )
        {
            self::$_cache = false;
        }

        parent::__construct('#__social_photos', 'id', $db);
    }

    /**
     * Overrides parent's load implementation
     *
     * @since   1.0
     * @access  public
     */
    public function load( $keys = null, $reset = true )
    {
        if( self::$_cache )
        {
            if( is_array( $keys ) )
            {
                return parent::load( $keys, $reset );
            }

            if(! isset( self::$_photos[ $keys ] ) )
            {
                $state = parent::load( $keys );
                self::$_photos[ $keys ] = $this;
                return $state;
            }

            if( is_bool( self::$_photos[ $keys ] ) )
            {
                return false;
            }

            return parent::bind( self::$_photos[ $keys ] );
        }
        else
        {
            return parent::load( $keys, $reset );
        }
    }

    public function setCacheable( $cache = false )
    {
        self::$_cache  = $cache;
    }

    public function loadByBatch( $ids )
    {
        $db = FD::db();
        $sql = $db->sql();

        $photoIds = array();
        $albumIds = array();

        foreach( $ids as $pid )
        {
            if(! isset( self::$_photos[$pid] ) )
            {
                $photoIds[] = $pid;
            }
        }

        if( $photoIds )
        {
            foreach( $photoIds as $pid )
            {
                self::$_photos[$pid] = false;
            }

            $query = '';
            $idSegments = array_chunk( $photoIds, 5 );
            //$idSegments = array_chunk( $photoIds, count( $photoIds ) );


            for( $i = 0; $i < count( $idSegments ); $i++ )
            {
                $segment    = $idSegments[$i];
                $ids        = implode( ',', $segment );

                $query .= 'select * from `#__social_photos` where `id` IN ( ' . $ids . ')';
                if( ($i + 1)  < count( $idSegments ) )
                {
                    $query .= ' UNION ';
                }
            }

            $sql->raw( $query );
            $db->setQuery( $sql );

            $results = $db->loadObjectList();

            if( $results )
            {
                foreach( $results as $row )
                {
                    $albumIds[] = $row->album_id;
                    self::$_photos[$row->id] = $row;
                }
            }
        }

        return $albumIds;

    }

    /**
     * Rotates a photo
     *
     * @since   1.0
     * @access  public
     * @param   int     The angle to rotate the photo
     * @return
     */
    public function rotate( $angle )
    {
        // Try to rotate the image
        $image  = FD::image();
        $image->load( $this->getPath( 'stock' ) );
        $image->rotate( $angle );

        return $image;
    }

    /**
     * Rotates tags in a photo
     *
     * @since   1.0
     * @access  public
     * @param   int     The angle to rotate the photo
     * @return
     */
    public function rotateTags( $angle )
    {
        $model  = FD::model( 'Photos' );
        $tags   = $model->getTags( $this->id );

        foreach( $tags as $tag )
        {
            $oldTag     = clone( $tag );

            if( $angle == 90 )
            {
                $tag->width     = $oldTag->height;
                $tag->height    = $oldTag->width;

                $tag->left      = ( 1 - $oldTag->top ) - $tag->width;
                $tag->top       = $oldTag->left;
            }

            if( $angle == -90 )
            {
                $tag->width     = $oldTag->height;
                $tag->height    = $oldTag->width;
                $tag->top       = $oldTag->left;
                $tag->left      = $oldTag->top;
            }

            $tag->store();
        }
    }



    /**
     * Toggle's a photo featured state
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function toggleFeatured()
    {
        $this->featured     = $this->featured ? false : true;

        return $this->store();
    }

    /**
     * Determines if the photo is featured
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return  bool
     */
    public function isFeatured()
    {
        return (bool) $this->featured;
    }

    /**
     * Determines if this image is stored externally
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function isStoredRemotely()
    {
        if ($this->storage == SOCIAL_STORAGE_JOOMLA) {
            return false;
        }

        return true;
    }

    /**
     * Cleanup photo title
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function cleanupTitle()
    {
        $knownExtensions    = array( '.jpg' , '.jpeg' , '.gif' , '.png' );

        $this->title    = str_ireplace( $knownExtensions , '' , $this->title );
    }

    /**
     * Determines if the photo is owned by the provided user.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function isMine( $id = null )
    {
        $user   = FD::user( $id );

        $isOwner    = $user->id == $this->uid;

        return $isOwner;
    }

    /**
     * Normalizes all meta value by ensuring that it's using the latest format
     *
     */
    public function normalizeMetaValue(&$meta)
    {
        // Fixed legacy value when the data contains JPATH_ROOT values
        $meta->value = str_ireplace(JPATH_ROOT, '', $meta->value);

        // Get the filename from the path
        $fileName = basename($meta->value);

        // Here we need to test if there is more than 3 segments of /path/album_id/photo_id/filename
        $parts = explode('/', $meta->value);

        if (count($parts) > 3) {
            $meta->value = '/' . $this->album_id . '/' . $this->id . '/' . $fileName;

            // If there is more than 3 segments in the path, we need to update the record
            if (count($parts) > 3) {

                $table = FD::table('PhotoMeta');
                $table->bind($meta);
                $table->value = $meta->value;
                $table->store();
            }
        }
    }

    /**
     * Deletes the photo folder
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function deletePhotoFolder()
    {
        $config = ES::config();
        $container = ES::cleanPath($config->get('photos.storage.container'));

        $path = JPATH_ROOT . '/' . $container . '/' . $this->album_id . '/' . $this->id;

        $exists = JFolder::exists($path);

        if (!$exists) {
            return false;
        }

        $state = JFolder::delete($path);

        return $state;
    }

    /**
     * Deletes the photos generated for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function deletePhotos($types)
    {
        $files = array();

        $config = FD::config();

        // Get the photo storage container
        $container = FD::cleanPath($config->get('photos.storage.container'));

        foreach ($types as $type) {
            $meta = FD::table('PhotoMeta');
            $meta->load(array('photo_id' => $this->id, 'group' => SOCIAL_PHOTOS_META_PATH, 'property' => $type));

            $this->normalizeMetaValue($meta);

            if ($type != 'stock') {

                $files[] = $container . $meta->value;
            }

            $meta->delete();
        }

        // Since remote storages doesn't store the "stock" photo, we need to manually delete it
        $storage = FD::storage($this->storage);
        $storage->delete($files);

        return true;
    }

    /**
     * Assign points to a user
     *
     * @since   1.0
     * @access  public
     * @param   int         The actor's id
     * @return
     */
    public function assignPoints( $rule , $actorId )
    {
        $points = FD::points();
        $points->assign( $rule , 'com_easysocial' , $actorId );
    }

    /**
     * Creates a badge record
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function assignBadge( $rule , $actorId )
    {
        if( $rule == 'photos.create' )
        {
            // @badge: photos.create
            $badge  = FD::badges();
            $badge->log( 'com_easysocial' , 'photos.create' , $actorId , JText::_( 'COM_EASYSOCIAL_PHOTOS_BADGE_UPLOADED' ) );
        }

        if( $rule == 'photos.browse' )
        {
            // @badge: photos.browse
            $badge  = FD::badges();
            $badge->log( 'com_easysocial' , 'photos.browse' , $actorId , JText::_( 'COM_EASYSOCIAL_PHOTOS_BADGE_BROWSE' ) );
        }

        if( $rule == 'photos.tag' )
        {
            // @badge: photos.tag
            $badge  = FD::badges();
            $badge->log( 'com_easysocial' , 'photos.tag' , $actorId , JText::_( 'COM_EASYSOCIAL_PHOTOS_BADGE_TAG' ) );
        }

        if( $rule == 'photos.superstar' )
        {
            // @badge: photos.tag
            $badge  = FD::badges();
            $badge->log( 'com_easysocial' , 'photos.tag' , $actorId , JText::_( 'COM_EASYSOCIAL_PHOTOS_BADGE_TAG' ) );
        }
    }

    /**
     * Updates the metadata of the photo
     *
     * @since   1.2.11
     * @access  public
     * @param   string
     * @return
     */
    public function updateMeta($group, $key, $value)
    {
        $meta       = FD::table('PhotoMeta');
        $options    = array('photo_id' => $this->id, 'group' => $group, 'property' => $key);
        $exists     = $meta->load($options);

        if (!$exists) {

            // If the meta does not exist, create it
            $meta               = FD::table('PhotoMeta');
            $meta->photo_id     = $this->id;
            $meta->group        = $group;
            $meta->property     = $key;
            $meta->value        = $value;
            $state  = $meta->store();

            return $state;
        }

        // If the meta exists, just reset it
        $meta->value    = $value;

        // Store the new angle value
        $state  = $meta->store();

        if (!$state) {
            $this->setError($meta->getError());
        }

        return $state;
    }


    /**
     * Updates the angle of the photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function updateAngle($angle)
    {
        $meta       = FD::table('PhotoMeta');
        $options    = array('photo_id' => $this->id, 'group' => SOCIAL_PHOTOS_META_TRANSFORM, 'property' => 'rotation');
        $exists     = $meta->load($options);

        if (!$exists) {
            $meta->photo_id = $this->id;
            $meta->group    = SOCIAL_PHOTOS_META_TRANSFORM;
            $meta->property = 'rotation';
        }

        // Angle should not be more than 360.
        $angle          = $angle >= 360 ? 0 : $angle;

        $meta->value    = $angle;

        // Store the new angle value
        $state          = $meta->store();

        if (!$state) {
            $this->setError($meta->getError());
        }

        return $state;
    }

    /**
     * Get's the current
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAngle()
    {
        $meta       = FD::table('PhotoMeta');
        $options    = array('photo_id' => $this->id, 'group' => SOCIAL_PHOTOS_META_TRANSFORM, 'property' => 'rotation');

        $meta->load($options);

        $currentAngle   = $meta->value ? $meta->value : 0;

        return $currentAngle;
    }

    /**
     * Override store method
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function store( $updateNulls = false )
    {
        $isNew  = $this->id ? false : true;

        $state  = parent::store();

        if ($isNew) {
            // @points: photos.upload
            // Add points for the author
            $points = FD::points();
            $points->assign('photos.upload', 'com_easysocial', $this->user_id);

            // @badge: photos.upload
            // Assign a badge for the user
            $this->assignBadge( 'photos.upload' , $this->uid );

            $exif = FD::get('Exif');

            // Detect the photo caption and title if exif is available.
            if($exif->isAvailable())
            {
                // Store the meta for the angle of the photo to be 0 by default.
                $meta = FD::table('PhotoMeta');

                $meta->photo_id = $this->id;
                $meta->group = SOCIAL_PHOTOS_META_EXIF;
                $meta->property = 'angle';
                $meta->value = 0;

                $meta->store();
            }
        }

        JPluginHelper::importPlugin('finder');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onFinderAfterSave', array( 'easysocial.photos', &$this, $isNew ) );

        return $state;
    }

    /**
     * Some desc
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function addPhotosStream( $verb, $mysqldatestring = '' )
    {
        // Load up the photo library
        $lib    = FD::photo( $this->uid , $this->type , $this );

        $lib->createStream( $verb , $mysqldatestring );
    }

    public function addStream( $verb )
    {
        // do nothing. do not remove this function!
        // this method is needed to fullfil the interface implmentation.
    }



    public function removeStream()
    {
        $stream = FD::stream();
        $stream->delete( $this->id, SOCIAL_TYPE_PHOTO );
    }


    /**
     * Override parent's delete method
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function delete( $pk = null )
    {
        // Delete the record from the database first.
        $state = parent::delete();

        // Now, try to delete the folder that houses this photo.
        $config = ES::config();

        // Check if this photo is used as a profile cover
        if ($this->isProfileCover()) {
            $cover = FD::table( 'Cover' );
            $cover->load(array('photo_id' => $this->id));

            $cover->delete();
        }

        // Needs to create an instance of image to create
        // and instnance of photos
        $image      = FD::image();
        $photoLib   = FD::get('Photos', $image);
        $basePath   = $photoLib->getStoragePath($this->album_id, $this->id);

        // Construct the base path to the photos folder
        $container  = FD::cleanPath($config->get('photos.storage.container'));
        $basePath   = '/' . $container . '/' . $this->album_id . '/' . $this->id;

        // @legacy
        // @since 1.2.7
        // To fix older version issues where the full path is stored for the photo
        $relative   = str_ireplace( JPATH_ROOT , '' , $basePath );

        // below checking is to make sure the computed relative path is not pointing to photos root folder.
        $photoContainer = FD::cleanPath($config->get('photos.storage.container'));
        $photoContainer = '/' . ltrim($photoContainer, '/');
        $photoContainer = rtrim( $photoContainer, '/');
        $photoContainer = JPath::clean($photoContainer);

        $photoRelPath = '/' . ltrim($relative, '/');
        $photoRelPath = rtrim( $photoRelPath, '/');
        $photoRelPath = JPath::clean($photoRelPath);

        if ( $photoContainer != $photoRelPath) {
            $storage = FD::storage($this->storage);
            $storage->delete($relative, true);
        }

        $model  = FD::model('Photos');

        // Delete the meta's related to this photo
        $model->deleteMeta($this->id);

        // Delete all tags associated with this photo
        $model->deleteTags($this->id);

        // Delete all comments associated with this photo
        $comments   = FD::comments( $this->id, SOCIAL_TYPE_PHOTO, 'upload', SOCIAL_APPS_GROUP_USER );
        $comments->delete();

        // Delete all likes associated with this photo
        $likes      = FD::get( 'Likes' );
        $likes->delete( $this->id , SOCIAL_TYPE_PHOTO, 'upload' );

        // @points: photos.remove
        // Deduct points for the author
        $points = FD::points();
        $points->assign( 'photos.remove' , 'com_easysocial' , $this->uid );

        // Push the ordering of other photos
        $model->pushPhotosOrdering( $this->album_id, 0, $this->ordering, '-' );

        // Need to set cover to another photo
        if( $this->isCover() )
        {
            $album = $this->getAlbum();

            if( $album->hasPhotos() )
            {
                $result = $album->getPhotos( array( 'limit' => 1 ) );

                $album->cover_id = $result['photos'][0]->id;
            }
            else
            {
                $album->cover_id = 0;
            }

            $album->store();
        }

        if ($state) {
            // Delete from smart search index
            JPluginHelper::importPlugin('finder');
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger( 'onFinderAfterDelete', array( 'easysocial.photos' , $this ) );
        }

        return $state;
    }

    /**
     * Tests if the user is allowed to download the photo
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function downloadable( $id = null )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        return $lib->downloadable();
    }

    /**
     * Determiens if the user can view an album
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function viewable( $id = null )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        return $lib->viewable();
    }

    /**
     * Tests if the user is allowed to use this photo as their avatar.
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function canSetProfilePicture( $id = null )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        return $lib->canSetProfilePicture();
    }

    /**
     * Tests if the user is allowed to use this photo as cover
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function canSetProfileCover( $id = null )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        return $lib->canSetProfileCover();
    }

    /**
     * Tests if the user is allowed to feature this photo
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function featureable( $id = null )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        return $lib->featureable();
    }

    /**
     * Tests if the user is allowed to share this photo
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function shareable( $id = null )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        return $lib->shareable();
    }

    /**
     * Tests if the user is allowed to tag on this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function taggable( $id = null )
    {
        $user   = FD::user( $id );

        if( $this->uid == $user->id || $user->isSiteAdmin() )
        {
            return true;
        }

        // @TODO: Test if this photo privacy allow friends to tag on the photo
        $privacyLib = FD::privacy( $this->uid );
        if( !$privacyLib->validate( 'photo.tag' , $user->id , SOCIAL_TYPE_USER ) )
        {
            return false;
        }

        return true;
    }

    /**
     * Tests if the album is delete able by the provided user id.
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   int     User id.
     * @return
     */
    public function deleteable( $id = null , $type = SOCIAL_TYPE_USER )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        return $lib->deleteable();
    }

    /**
     * Some desc
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function move( $newAlbumId  , $where = '' )
    {

        jimport( 'joomla.filesystem.folder' );

        // If the current photo is the cover of the photo we need to update the cover
        // since the photo is already moved away
        if ($this->isCover()) {
            $this->setNewAlbumCover();
        }

        // Get the old album id as we need to move the old photo folder over
        $oldAlbumId         = $this->album_id;

        // Get the path to the photos folder.
        $oldFolder          = $this->getFolder();

        // Set the new album id.
        $this->album_id     = $newAlbumId;

        // Get the new photo folder
        $newAlbumFolder     = $this->getFolder(false);
        $newFolder          = $this->getFolder();


        if(! JFolder::exists( $newAlbumFolder ) )
        {
            JFolder::create( $newAlbumFolder );
        }

        // Save the photo with the new album
        $state              = parent::store();

        if (!$state) {
            return $state;
        }

        JFolder::move( $oldFolder , $newFolder );

        // Once the folder is moved, we also need to update all the metas.
        $model  = FD::model( 'Photos' );
        $metas  = $model->getMeta( $this->id , SOCIAL_PHOTOS_META_PATH );

        foreach( $metas as $meta )
        {
            $table  = FD::table( 'PhotoMeta' );
            $table->bind( $meta );

            $fileName           = basename( $table->value );

            // Rebuild the new path
            $table->value       = $newFolder . '/' . $fileName;

            $table->store();
        }

			// now we need to remove this photo from stream table.
			$model = FD::model( 'Photos' );
			$model->delPhotoStream($this->id, $this->user_id, $oldAlbumId);
			return $state;
    }

    /**
     *
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function setNewAlbumCover()
    {
        $album      = $this->getAlbum();

        if (!$album->hasPhotos()) {
            $album->cover_id    = 0;
        } else {

            $result     = $album->getPhotos(array('limit' => 1));
            $photo      = $result['photos'][0];

            $album->cover_id   = $photo->id;
        }

        $state  = $album->store();

        return $state;
    }

    /**
     * Determines if the user is allowed to move the photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function moveable( $id = null , $type = SOCIAL_TYPE_USER )
    {
        $album = $this->getAlbum();

        // If this is a system album like cover photos, profile pictures, they will not be able to move photos within this album.
        $disallowed = array( SOCIAL_ALBUM_STORY_ALBUM , SOCIAL_ALBUM_PROFILE_COVERS , SOCIAL_ALBUM_PROFILE_PHOTOS );

        if( in_array( $album->core , $disallowed ) )
        {
            return false;
        }

        if( $type == SOCIAL_TYPE_USER )
        {
            $user   = FD::user( $id );

            // @TODO: Allow users with moderation / super admins to delete
            if( $this->uid == $user->id )
            {
                return true;
            }

            return false;
        }

        return false;
    }

    public function getCreator()
    {
        // @legacy
        // There could be instances where the user_id was empty
        if (!$this->user_id && $this->type == SOCIAL_TYPE_USER) {
            return FD::user($this->uid);
        }

        return FD::user($this->user_id);
    }

    /**
     * Retrieves the creation date of the photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getCreationDate()
    {
        return $this->created;
    }

    /**
     * Determines if this album has an assigned date.
     *
     * @since   1.2.8
     * @access  public
     * @param   string
     * @return
     */
    public function hasAssignedDate()
    {
        if( $this->assigned_date == '0000-00-00 00:00:00' )
        {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the assigned date of the album
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAssignedDate()
    {
        // if assigned date is empty, we use creation date.
        if( $this->assigned_date == '0000-00-00 00:00:00' )
        {
            return $this->getCreationDate();
        }

        return $this->assigned_date;
    }

    /**
     * Get a list of tags for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTags($peopleOnly = false)
    {
        $model  = FD::model( 'Photos' );

        // Retrieve list of tags for this photo.
        $tags   = $model->getTags( $this->id , $peopleOnly );

        return $tags;
    }

    /**
     * Retrieves the storage path for this album
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getStoragePath(SocialTableAlbum $album , $relative = false )
    {
        // Rename temporary folder to the destination.
        jimport( 'joomla.filesystem.folder' );

        // Get destination folder path.
        $storage = $album->getStoragePath($relative);

        // Build the storage path now with the album id
        $storage = $storage . '/' . $this->id;

        // Ensure that the final storage path exists.
        if (!$relative) {
            ES::makeFolder($storage);
        }

        return $storage;
    }

    /**
     * Retrieves the likes count for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getLikesCount()
    {
        static $likes   = array();

        if (!isset($likes[$this->id])) {

            // Default context
            $type   = SOCIAL_TYPE_PHOTO;
            $verb   = 'add';
            $group  = $this->type;
            $id     = $this->id;

            // We need to determine if the album is a core one
            $album  = $this->getAlbum();

            if ($album->core == SOCIAL_ALBUM_PROFILE_PHOTOS) {
                $verb   = 'uploadAvatar';
            }

            if ($album->core == SOCIAL_ALBUM_PROFILE_COVERS) {
                $verb   = 'updateCover';
            }

            if ($album->core == SOCIAL_ALBUM_STORY_ALBUM) {

                $layout   = JRequest::getVar('layout', '');

                $type   = SOCIAL_TYPE_PHOTO;
                $verb   = 'upload';

                // We need to test if this photo contains single stream item or aggregated
                $model      = FD::model('Stream');
                $aggregated = $model->isAggregated($this->id, 'photos');

                if ($aggregated && $layout != 'item') {
                    $verb   = 'upload';
                    $type   = SOCIAL_TYPE_STREAM;
                }
            }


            $likes[ $this->id ] = FD::get( 'Likes' )->getCount($id , $type, $verb, $group);
        }

        return $likes[ $this->id ];
    }

    /**
     * Retrieves the comments count for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getCommentsCount()
    {
        static $comments    = array();

        if( !isset( $comments[ $this->id ] ) )
        {
            // Default context
            $context    = SOCIAL_TYPE_PHOTO;
            $verb       = 'add';
            $group      = $this->type;
            $id         = $this->id;

            // We need to determine if the album is a core one
            $album  = $this->getAlbum();

            if ($album->core == SOCIAL_ALBUM_PROFILE_PHOTOS) {
                $verb   = 'uploadAvatar';
            }

            if ($album->core == SOCIAL_ALBUM_PROFILE_COVERS) {
                $verb   = 'updateCover';
            }

            if ($album->core == SOCIAL_ALBUM_STORY_ALBUM) {

                $layout   = JRequest::getVar('layout', '');

                $type   = SOCIAL_TYPE_PHOTO;
                $verb   = 'upload';

                // We need to test if this photo contains single stream item or aggregated
                $model      = FD::model('Stream');
                $aggregated = $model->isAggregated($this->id, 'photos');

                if ($aggregated && $layout != 'item') {
                    $verb   = 'upload';
                    $type   = SOCIAL_TYPE_STREAM;
                }
            }

            $count  = FD::comments($id, $context, $verb, $group)->getCount();

            $comments[ $this->id ]  = $count;
        }

        return $comments[ $this->id ];
    }

    /**
     * Get's the absolute path of the photo given the type.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getFolder($includePhotoId = true, $absolutePath = true)
    {
        $config = ES::config();
        $storage = ES::cleanPath($config->get('photos.storage.container'));
        $path = '';

        if ($absolutePath) {
            $path = JPATH_ROOT;
        }

        $path .= '/' . $storage . '/' . $this->album_id;

        if ($includePhotoId) {
            $path .= '/' . $this->id;
        }

        return $path;
    }

    /**
     * Get's the absolute path of the photo given the type.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPath($type, $relative = false)
    {
        static $paths = array();

        $config = ES::config();

        // Get the photo storage container
        $container = $config->get('photos.storage.container');
        $container = ES::cleanPath($container);

        $key = $this->id . $type . (int) $relative;

        if (!isset($paths[$key])) {
            $model = FD::model('Photos');

            // Retrieve information about the photo
            $metas = $model->getMeta($this->id, SOCIAL_PHOTOS_META_PATH);
            $result = new stdClass();

            if (!$metas) {
                $paths[$key] = false;

                return $paths[$key];
            }

            foreach ($metas as $meta) {

                $this->normalizeMetaValue($meta);

                // Default to use absolute
                $result->{$meta->property} = JPATH_ROOT . '/' . $container . $meta->value;

                if ($relative) {
                    $result->{$meta->property} = '/' . $container . $meta->value;
                }
            }

            $paths[$key] = $result;
        }

        return $paths[$key]->{$type};
    }

    /**
     * Allows caller to download a file.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function download()
    {
        // Get the original file path.
        $file   = $this->getPath( 'original' );

        // Make the path relative
        if ($this->storage != 'joomla') {
            $file       = str_ireplace( JPATH_ROOT , '' , $file );

            $storage    = FD::storage( $this->storage );

            return JFactory::getApplication()->redirect( $storage->getPermalink( $file ) );
        }

        // Get the mime of the image
        $mime   = $this->getMime( 'original' );

        // Construct the file name
        $name   = $this->title . $this->getExtension();

        // Set the headers for the file transfer
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime );
        header("Content-Disposition: attachment; filename=\"". $name ."\";" );
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize( $file ) );

        ob_clean();
        flush();
        readfile( $file );
        exit;
    }

    /**
     * Retrieves the photo extension .jpg | .png
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getExtension($size = 'stock')
    {
        // Use the stock photo to retrieve the extension
        $mime = $this->getMime($size);
        $extension = '.jpg';

        if ($mime == 'image/png') {
            $extension = '.png';
        }

        return $extension;
    }

    /**
     * Retrieves the width of the photo
     *
     * @since   1.2.11
     * @access  public
     * @param   string
     * @return
     */
    public function getWidth($variation = 'original')
    {
        $model  = FD::model('Photos');
        $meta   = $model->getMeta($this->id, SOCIAL_PHOTOS_META_WIDTH, $variation);

        if (!$meta) {
            return '';
        }

        return (int) $meta[0]->value;
    }

    /**
     * Retrieves the height of the photo
     *
     * @since   1.2.11
     * @access  public
     * @param   string
     * @return
     */
    public function getHeight($variation = 'original')
    {
        $model  = FD::model('Photos');
        $meta   = $model->getMeta($this->id, SOCIAL_PHOTOS_META_HEIGHT, $variation);

        if (!$meta) {
            return '';
        }

        return (int) $meta[0]->value;
    }

    /**
     * Returns the mime type for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getMime($type)
    {
        $path = $this->getPath($type);
        $info = getimagesize($path);

        return $info['mime'];
    }

    /**
     * Gets the image object given the size of the image
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getImageObject($size)
    {
        $path = $this->getPath($size);

        // If this image is stored remotely, we need to download the files first.
        if ($this->isStoredRemotely()) {

            $album = $this->getAlbum();
            $relativePath = $this->getPath($size, true);

            // Ensure that the photo folder exists
            $parentRelativePath = JPATH_ROOT . dirname($relativePath);

            if (!JFolder::exists($parentRelativePath)) {
                JFolder::create($parentRelativePath);
            }

            // Download the image from remote storage
            $storage = ES::storage($this->storage);
            $storage->pull($relativePath);

        } else {
            if (!JFile::exists($path)) {
                return false;
            }
        }

        $image = ES::image();
        $image->load($path);

        return $image;
    }

    public function getUri($type)
    {
        $model = ES::model('Photos');
        $metas = $model->getMeta($this->id, SOCIAL_PHOTOS_META_PATH);
    }

    /**
     * Retrieves the permalink to the image given the size
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getSource($type = 'thumbnail')
    {
        static $paths = array();

        $config = FD::config();

        // Load the paths for this photo
        $model = FD::model( 'Photos' );
        $metas = $model->getMeta( $this->id , SOCIAL_PHOTOS_META_PATH );

        $obj = new stdClass();

        $path = FD::cleanPath( $config->get( 'photos.storage.container' ) );
        $allowed    = array( 'thumbnail' , 'large' , 'original', 'square' , 'featured' , 'medium' );

        foreach( $metas as $meta )
        {
            $relative   = $path . '/' . $this->album_id . '/' . $this->id . '/' . basename( $meta->value );

            if( $this->storage != SOCIAL_STORAGE_JOOMLA && in_array( $meta->property , $allowed ) )
            {
                $storage    = FD::storage( $this->storage );
                $url        = $storage->getPermalink( $relative );
            }
            else
            {
                $url    = rtrim( JURI::root() , '/' ) . '/' . $relative;
            }

            $obj->{$meta->property} = $url;
        }

        $paths[ $this->id ] = $obj;

        if( !isset( $paths[ $this->id ]->$type ) )
        {
            $paths[ $this->id ]->$type  = false;
        }


        return $paths[ $this->id ]->$type;
    }


    public function syncIndex()
    {
        $indexer = FD::get( 'Indexer' );

        $item   = $indexer->getTemplate();

        $url    = $this->getPermalink();
        $url    = '/' . ltrim( $url , '/' );
        $url    = str_replace('/administrator/', '/', $url );

        $item->setSource( $this->id , SOCIAL_INDEXER_TYPE_PHOTOS , $this->uid , $url );

        $content = ( $this->caption ) ? $this->caption : $this->title;
        $item->setContent( $this->title, $content );

        $date = FD::date();
        $item->setLastUpdate( $date->toMySQL() );

        $state = $indexer->index( $item );
        return $state;
    }

    public function deleteIndex()
    {
        $indexer = FD::get( 'Indexer' );
        $indexer->delete( $this->id, SOCIAL_INDEXER_TYPE_PHOTOS);
    }

    /**
     * Retrieves the location for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getLocation()
    {
        static $locations = array();

        if( !isset( $locations[ $this->id ] ) )
        {
            $location   = FD::table( 'Location' );
            $state      = $location->loadByType( $this->id , SOCIAL_TYPE_PHOTO , $this->uid );

            if( !$state )
            {
                $locations[ $this->id ] = $state;
            }
            else
            {
                $locations[ $this->id ] = $location;
            }
        }

        return $locations[ $this->id ];
    }

    /**
     * Retrieves the album for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAlbum()
    {
        static $albums = array();

        if (!isset($albums[$this->album_id])) {

            $album = ES::table('Album');
            $album->load($this->album_id);

            $albums[$this->album_id] = $album;
        }

        return $albums[$this->album_id];
    }

    /**
     * Determines if this photo is used as a profile cover
     *
     * @since   1.0
     * @access  public
     */
    public function isProfileCover()
    {
        $model          = FD::model( 'Photos' );
        $isProfileCover = $model->isProfileCover( $this->id , $this->uid , $this->type );

        return $isProfileCover;
    }

    public function isCover()
    {
        return ( $this->getAlbum()->cover_id == $this->id );
    }

    /**
     * Constructs the alias for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAlias()
    {
        $title  = $this->title;

        $title  = JFilterOutput::stringURLSafe( $title );

        $alias  = $this->id . ':' . $title;

        return $alias;
    }

    /**
     * Returns the permalink to the photo
     *
     * @since   1.0
     * @access  public
     * @return  string
     */
    public function getPermalink( $xhtml = true , $external = false , $layout = 'item', $sef = true )
    {
        $options    = array( 'layout' => $layout , 'id' => $this->getAlias() , 'type' => $this->type, 'sef' => $sef );

        if ($this->type == SOCIAL_TYPE_GROUP) {
            $options['uid'] = FD::group($this->uid)->getAlias();
        }

        if ($this->type == SOCIAL_TYPE_EVENT) {
            $options['uid'] = FD::event($this->uid)->getAlias();
        }

        if ($this->type == SOCIAL_TYPE_USER) {
            $options['uid'] = FD::user($this->uid)->getAlias();
        }

        if ($external) {
            $options['external'] = true;
        }

        return FRoute::photos($options, $xhtml);
    }

    /**
     * Returns the permalink to edit the photo
     *
     * @since   1.0
     * @access  public
     * @return  string
     */
    public function getEditPermalink( $xhtml = true , $external = false )
    {
        $url    = $this->getPermalink( $xhtml , $external , 'form' );

        return $url;
    }

    public function export()
    {
        $properties = get_object_vars( $this );

        $photo = array();

        foreach( $properties as $key => $value )
        {
            if( $key[0] != '_' )
            {
                $photo[$key] = $value;
            }
        }

        $photo['sizes'] = array();

        foreach( array( 'large', 'square', 'thumbnail', 'featured', 'original', 'stock' ) as $size )
        {
            $photo['sizes'][$size] = array();

            $photo['sizes'][$size]['url'] = $this->getSource( $size );
        }

        $photo['permalink'] = $this->getPermalink();

        return $photo;
    }

    public function uuid()
    {
        return $this->_uuid;
    }

    /**
     * Determines if the user is allowed to edit this photo
     *
     * @deprecated  1.2
     * @since   1.0
     * @access  public
     * @param   int     The user to check against (optional)
     * @return  bool    True if success, false otherwise
     */
    public function editable( $id = null )
    {
        $lib    = FD::photo( $this->uid , $this->type , $this );

        // Why shareable instead of editable?
        // Changing to editable for now
        // return $lib->shareable();
        return $lib->editable();
    }

    /**
     * Maps the exif data to this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function mapExif( $file )
    {
        $exif = ES::get('Exif');

        // Detect the photo caption and title if exif is available.
        if (!$exif->isAvailable()) {
            return;
        }

        $exif->load($file['tmp_name']);

        $title = $exif->getTitle();
        $caption = $exif->getCaption();

        if ($title) {
            $this->title = $title;
        }

        if ($caption) {

            // Ensure that it is a valid utf8 string
            if (function_exists('utf8_encode')) {
                $caption = utf8_encode($caption);
            }

            $this->caption = $caption;
        }

        // Store the photo again since the title or caption might change
        if ($title || $caption) {
            $this->store();
        }

        // Get the photo model
        $model      = FD::model( 'Photos' );

        // Get the location
        $locationCoordinates    = $exif->getLocation();

        // Once we have the coordinates, we need to reverse geocode it to get the address.
        if( $locationCoordinates )
        {
            $geocode    = FD::get( 'GeoCode' );
            $address    = $geocode->reverse( $locationCoordinates->latitude , $locationCoordinates->longitude );

            $location               = FD::table( 'Location' );
            $location->loadByType( $this->id , SOCIAL_TYPE_PHOTO , $this->uid );

            $location->address      = $address;
            $location->latitude     = $locationCoordinates->latitude;
            $location->longitude    = $locationCoordinates->longitude;
            $location->user_id      = $this->uid;
            $location->type         = SOCIAL_TYPE_PHOTO;
            $location->uid          = $this->id;

            $state  = $location->store();
        }

        // Store custom meta data for the photo
        $model->storeCustomMeta( $this , $exif );
    }

    /**
     * Allows caller to bind a location for the photo
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function bindLocation( $address , $latitude , $longitude )
    {
        if( empty( $address ) )
        {
            $this->setError( JText::_( 'COM_EASYSOCIAL_PHOTOS_ADDRESS_CANNOT_BE_EMPTY' ) );
            return false;
        }

        if( empty( $latitude ) )
        {
            $this->setError( JText::_( 'COM_EASYSOCIAL_PHOTOS_LATITUDE_CANNOT_BE_EMPTY' ) );
            return false;
        }

        if( empty( $longitude ) )
        {
            $this->setError( JText::_( 'COM_EASYSOCIAL_PHOTOS_LONGITUDE_CANNOT_BE_EMPTY' ) );
            return false;
        }

        // Bind the location for the photo if necessary
        $location               = FD::table( 'Location' );
        $location->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_PHOTO , 'user_id' => $this->user_id ) );

        $location->address      = $address;
        $location->latitude     = $latitude;
        $location->longitude    = $longitude;
        $location->user_id      = $this->user_id;
        $location->type         = SOCIAL_TYPE_PHOTO;
        $location->uid          = $this->id;

        // Try to store the location now.
        $state  = $location->store();

        return $state;
    }

    /**
     * Processes rules before storing an image
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function beforeStore( $file , SocialImage $image )
    {
        // Test if exif library exists on the server
        $exif = FD::get('Exif');

        // Detect the photo caption and title if exif is available.
        if ($exif->isAvailable() && $image->hasExifSupport()) {

            // Load the image
            $exif->load($file['tmp_name']);

            // Get the title from the exif library
            $title = $exif->getTitle();

            // Get the caption from the exif library
            $caption = $exif->getCaption();

            // Get the creation date from the exif library
            $createdAlias = $exif->getCreationDate();

            if ($createdAlias) {
                $this->assigned_date = $createdAlias;
            }

            if ($title) {
                $this->title = $title;
            }

            if ($caption) {
                $this->caption = $caption;
            }
        }
    }

    /**
     * Processes rules after storing an image
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function afterStore( $file , SocialImage $image )
    {
        // Load up exif library
        $exif = FD::get( 'Exif' );

        // Push all the ordering of the photo down
        $model      = FD::model( 'Photos' );
        $model->pushPhotosOrdering( $this->album_id , $this->id );

        // Detect location for the photo
        if ($exif->isAvailable() && $image->hasExifSupport()) {

            // Load the file
            $exif->load($file['tmp_name']);

            // Get the location
            $locationCoordinates = $exif->getLocation();

            // Once we have the coordinates, we need to reverse geocode it to get the address.
            if ($locationCoordinates) {

                $my = FD::user();
                $geocode = FD::get( 'GeoCode' );
                $address = $geocode->reverse( $locationCoordinates->latitude , $locationCoordinates->longitude );

                $location               = FD::table( 'Location' );
                $location->loadByType( $this->id , SOCIAL_TYPE_PHOTO , $my->id );

                $location->address      = $address;
                $location->latitude     = $locationCoordinates->latitude;
                $location->longitude    = $locationCoordinates->longitude;
                $location->user_id      = $my->id;
                $location->type         = SOCIAL_TYPE_PHOTO;
                $location->uid          = $this->id;

                $state  = $location->store();
            }

            // Store custom meta data for the photo
            $model->storeCustomMeta( $this , $exif );
        }

        // Synchronize with our search index
        $indexer    = FD::get( 'Indexer' );
        $template   = $indexer->getTemplate();
        $template->setContent( $this->title , $this->caption );

        // Get the url for the photo
        // $url     = FRoute::photos( array( 'layout' => 'item', 'id' => $this->getAlias() ) );

        $url = $this->getPermalink();
        $template->setSource( $this->id , SOCIAL_INDEXER_TYPE_PHOTOS , $this->uid , $url );
        $template->setThumbnail( $this->getSource( 'thumbnail' ) );

        $indexer->index( $template );
    }
}
