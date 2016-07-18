<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class SocialPhoto
{
	/**
	 * The current unique owner of the item
	 * @var	int
	 */
	public $uid = null;

	/**
	 * The current unique string of the item
	 * @var	string
	 */
	public $type = null;

	/**
	 * The adapter for photo
	 * @var	string
	 */
	public $adapter = null;

	/**
	 * The album's library
	 * @var	SocialAlbums
	 */
	public $albumLib = null;

	/**
	 * The table mapping for the photo.
	 * @var	SocialTablePhoto
	 */
	public $data = null;

	/**
	 * The error message
	 * @var	string
	 */
	public $error = null;

	public function __construct($uid, $type, $id = null)
	{
		if ($id instanceof SocialTablePhoto) {
			$this->data = $id;
		} else {
			$table = FD::table('Photo');
			$table->load($id);

			$this->data = $table;
		}

		$this->uid = $uid;
		$this->type = $type;
		$this->albumLib = $this->album();
		$this->adapter = $this->getAdapter($type);

		// Update renderItemOptions to reflect configurable options
		$config = FD::config();
		$this->renderItemOptions['resizeThreshold'] = $config->get('photos.layout.threshold');
	}

	public static function factory( $id = null )
	{
		return new self( $id );
	}

	private $renderItemOptions = array(
		'viewer'          => null,
		'layout'          => 'item',
		'size'            => 'thumbnail',
		'template'        => 'site/photos/item',
		'showNavigation'  => false,
		'showToolbar'     => true,
		'showInfo'        => true,
		'showStats'       => true,
		'showResponse'    => true,
		'showTags'        => true,
		'showForm'        => true,
		'resizeMode'      => 'contain',
		'resizeThreshold' => 128,
		'resizeUsingCss'  => true,
		'openInPopup'     => false
	);

	/**
	 * Wraps the provided album
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function renderItem($options=array())
	{
		// Flag to determine if resize mode was enforced
		$resizeModeEnforced = !empty($options['resizeMode']);

		// Normalize render options
		$options = array_merge($this->renderItemOptions, $options);

		// If resize mode was not enforced
		if (!$resizeModeEnforced) {

			// Automatically determine if we should use cover or contain based on item layout.
			$options['resizeMode'] = $options['layout']=='item' ? 'cover' : 'contain';
		}

		// Build user alias
		$creator = $this->creator();
		$viewer = FD::user($options['viewer']);

		// Generate photo item template
		$theme = FD::themes();

		// Get the album library
		$albumLib = $this->album();

		// check the photos is it got cluster type e.g. event or group
		$photoGroupId = ($this->type != SOCIAL_APPS_GROUP_USER) ? $this->uid : '0';
		$photoGroupType = ($this->type != SOCIAL_APPS_GROUP_USER) ? $this->type : '';

		$theme->set( 'lib', $this);
		$theme->set( 'tags', $this->data->getTags());
		$theme->set( 'comments', $this->comments());
		$theme->set( 'likes', $this->likes());
		$theme->set( 'shares', $this->reposts($photoGroupId, $photoGroupType));
		$theme->set( 'albumLib', $albumLib);
		$theme->set( 'album', $albumLib->data);
		$theme->set( 'photo', $this->data);
		$theme->set( 'creator', $creator);
		$theme->set( 'privacy', $this->privacy());
		$theme->set( 'options', $options);

		return $theme->output($options['template']);
	}

	/**
	 * Retrieves the album's library
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialAlbums
	 */
	public function album()
	{
		return FD::albums( $this->data->uid , $this->data->type , $this->data->album_id );
	}

	public function creator()
	{
		return FD::user($this->data->user_id);
	}

	public function privacy()
	{
		// @TODO: Get proper photo privacy
		return FD::privacy();
	}

	/**
	 * Prepare the likes object for the photo item
	 *
	 * @since	1.2
	 * @access	public
	 * @return	SocialLikes	The likes library
	 */
	public function likes()
	{
		$verb		= $this->getPhotoVerb();
		$context	= SOCIAL_TYPE_PHOTO;
        $id      	= $this->data->id;

        $streamId = $this->getPhotoStreamId($id, $verb, false);
        if ($verb == 'upload') {
        	// we now this photo is uploaded via stream's story form.
            $model      = FD::model('Stream');
            $aggregated = $model->isAggregated($this->data->id, 'photos');

            if ($aggregated) {
				$streamId = '0';
            }
        }

        // NOTE:
        // We do not need to do any checking or fix any relations here since liking a photo should always be liking a photo
        // Nothing needs to be done here.

		return FD::likes($id, $context , $verb, $this->type, $streamId);
	}

	/**
	 * Prepare the comments object for the photo item
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function comments()
	{
		// Get the verb to use.
		$verb		= $this->getPhotoVerb();

		// The context should always be photos
		$context	= SOCIAL_TYPE_PHOTO;

		// The object id should always be the photo id
        $id     	= $this->data->id;

        $streamId = $this->getPhotoStreamId($id, $verb, false);
        if ($verb == 'upload') {
        	// we now this photo is uploaded via stream's story form.
            $model      = FD::model('Stream');
            $aggregated = $model->isAggregated($this->data->id, 'photos');

            if ($aggregated) {
				$streamId = '0';
            }
        }

        // Get the permalink to the photo
        $permalink 	= $this->data->getPermalink(false, true);

		return FD::comments($id , $context , $verb, $this->type , array('url' => $permalink), $streamId);
	}

	public function reposts($clusterId = 0, $clusterType = '')
	{
		return FD::get('Repost', $this->data->id, SOCIAL_TYPE_PHOTO, $this->type, $clusterId, $clusterType);
	}

    public function getPhotoStreamId($photoId, $verb, $validate = true)
    {
        $model = FD::model('Photos');
        return $model->getPhotoStreamId($photoId, $verb, $validate);
    }

	public function getPhotoVerb()
	{
		static $cache = array();

		if (!isset($cache[$this->data->id])) {

			$album	= FD::table('Album');
			$album->load($this->data->album_id);

			// uploadAvatar
			// updateCover
			// share
			// create

			$core	= $album->core;
			$verb	= 'add';

			if ($core == SOCIAL_ALBUM_PROFILE_PHOTOS) {
				$verb 	= 'uploadAvatar';
			}

			if ($core == SOCIAL_ALBUM_PROFILE_COVERS) {
				$verb 	= 'updateCover';
			}

			// When user upload photos in the story, it gets pushed to the story album
			if ($core == SOCIAL_ALBUM_STORY_ALBUM) {
				$verb	= 'upload';
				// $verb 	= 'share';
			}

			$cache[$this->data->id] = $verb;
		}

		return $cache[$this->data->id];
	}


	/**
	 * Retrieves a list of photos from an album
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getAlbumPhotos( $options = array() )
	{
		$photos 	= $this->albumLib->data->getPhotos( $options );

		return $photos[ 'photos' ];
	}

	/**
	 * Sets error messages
	 *
	 * @since	1.2.11
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setError($message)
	{
		$this->error	= $message;
	}

	/**
	 * Retrieves error messages
	 *
	 * @since	1.2.11
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getError()
	{
		return $this->error;
	}


	/**
	 * Retrieves error messages
	 *
	 * @deprecated 1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getErrorMessage()
	{
		return $this->getError();
	}

	/**
	 * Maps back the call method functions to the adapter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string	Method's name.
	 * @param	mixed	Arguments
	 * @return
	 */
	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->adapter , $method ) , $refArray );
	}

	/**
	 * Retrieves the album's adapter
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdapter( $type )
	{
		$type = empty( $type ) ? 'user' : $type;

		$file 	= dirname( __FILE__ ) . '/adapters/' . strtolower( $type ) . '.php';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );

		$className 	= 'SocialPhotoAdapter' . ucfirst( $type );
		$adapter 	= new $className( $this , $this->albumLib );

		return $adapter;
	}

}
