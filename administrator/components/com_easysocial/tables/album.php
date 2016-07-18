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

FD::import( 'admin:/tables/table' );
FD::import( 'admin:/includes/stream/stream' );
FD::import( 'admin:/includes/stream/dependencies' );
FD::import( 'admin:/includes/indexer/indexer' );

class SocialTableAlbum extends SocialTable
	implements ISocialIndexerTable, ISocialStreamItemTable
{
	/**
	 * The unique id for this record.
	 * @var int
	 */
	public $id			= null;

	/**
	 * The photo id that is used for this album
	 * @var int
	 */
	public $cover_id 		= null;

	/**
	 * The user id for this record.
	 * @var int
	 */
	public $uid 		= null;

	/**
	 * The unique type string for this record.
	 * @var string
	 */
	public $type 		= null;

	/**
	 * The user id for this record.
	 * @var int
	 */
	public $user_id		= null;

	/**
	 * The unique type string for this record.
	 * @var string
	 */
	public $title 		= null;

	/**
	 * The unique type string for this record.
	 * @var string
	 */
	public $caption 		= null;

	/**
	 * The created date of this album.
	 * @var string
	 */
	public $created 		= null;

	/**
	 * The creation date alias of this album.
	 * @var string
	 */
	public $assigned_date 		= null;

	/**
	 * The ordering of this album.
	 * @var string
	 */
	public $ordering 		= null;

	/**
	 * Extended parameters of this album in json format.
	 * @var string
	 */
	public $params 		= null;

	/**
	 * Stores the hits counter for an album.
	 * @param	int
	 */
	public $hits 		= null;

	/**
	 * Determines if this album is used for the system (Which means it cannot be deleted.)
	 * @var string
	 */
	public $core 		= null;

	public $_uuid = null;

	static $_albums = array();

	private $cover 	= null;

	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @param	JDatabase
	 */
	public function __construct( $db )
	{
		// Create a unique id only for each table instance
		// This is to help controller implement the right element.
		$this->_uuid = uniqid();

		parent::__construct('#__social_albums', 'id', $db);
	}

	/**
	 * Overrides parent's load implementation
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function load( $keys = null, $reset = true )
	{
		$state = false;
		$loaded = false;

		if( is_array( $keys ) )
		{
			$state = parent::load( $keys, $reset );
		}
		else
		{
			if (!isset(self::$_albums[$keys])) {
				$state 					= parent::load($keys);
				self::$_albums[$keys]	= $this;
			} else {

				$value 	= self::$_albums[$keys];

				if (is_bool($value)) {
					$state 	= false;
				} else {
					$state = parent::bind($value);
				}
				$loaded = true;
			}
		}

		if( $state && !$loaded)
		{
			// Converts params into an object first
			if( empty( $this->params ) )
			{
				$this->params = new stdClass();
			}
			else
			{
				$this->params = FD::json()->decode( $this->params );
			}
		}

		return $state;
	}


	/**
	 *  load albums by batch
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function loadByBatch( $ids )
	{
		$db = FD::db();
		$sql = $db->sql();

		$albumIds = array();

		foreach( $ids as $pid )
		{
			if(! isset( self::$_albums[$pid] ) )
			{
				$albumIds[] = $pid;
			}
		}

		if( $albumIds )
		{
			foreach( $albumIds as $pid )
			{
				self::$_albums[$pid] = false;
			}

			$query = '';
			$idSegments = array_chunk( $albumIds, 5 );
			//$idSegments = array_chunk( $albumIds, count( $albumIds ) );


			for( $i = 0; $i < count( $idSegments ); $i++ )
			{
				$segment    = $idSegments[$i];
				$ids = implode( ',', $segment );

				$query .= 'select * from `#__social_albums` where `id` IN ( ' . $ids . ')';

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
					$tbl = FD::table( 'Album' );
					$tbl->bind( $row );

					if( empty( $tbl->params ) )
					{
						$tbl->params = new stdClass();
					}
					else
					{
						$tbl->params = FD::json()->decode( $tbl->params );
					}


					self::$_albums[$row->id] = $tbl;
				}
			}
		}

	}


	/**
	 * Overrides parent's store implementation
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store( $updateNulls = false )
	{
		// Detect if this is a new album
		$isNew 	= $this->id ? false : true;

		// Set a default title if the title is not set.
		if( empty( $this->title ) )
		{
			$this->title 	= JText::_( 'COM_EASYSOCIAL_UNTITLED_ALBUM' );
		}

		// Convert params back into json string
		if( !is_string( $this->params ) )
		{
			$this->params = FD::json()->encode( $this->params );
		}

		// Set the date to now if created is empty
		if( empty( $this->created ) )
		{
			$this->created = FD::date()->toSql();
		}

		// Update ordering column.
		$this->ordering = $this->getNextOrder( array( 'uid' => $this->uid , 'type' => $this->type ) );

		// Invoke paren't store method.
		$state 	= parent::store( $updateNulls );

		if( $isNew && !$this->core )
		{
			// @points: photos.albums.create
			// Add points for the author for creating an album
			$points = FD::points();
			$points->assign( 'photos.albums.create' , 'com_easysocial' , $this->uid );
		}


		JPluginHelper::importPlugin('finder');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onFinderAfterSave', array( 'easysocial.albums', &$this, $isNew ) );

		return $state;
	}


	public function syncIndex()
	{
		$indexer = FD::get( 'Indexer' );

		$tmpl 	= $indexer->getTemplate();

		$creator 	= FD::user( $this->uid );
		$userAlias 	= $creator->getAlias();

		// $url 	= FRoute::albums( array( 'id' => $this->getAlias() , 'userid' => $userAlias , 'layout' => 'item' ) );

		$url   	= $this->getPermalink();
		$url 	= '/' . ltrim( $url , '/' );
		$url 	= str_replace('/administrator/', '/', $url );

		$tmpl->setSource( $this->id , SOCIAL_INDEXER_TYPE_ALBUMS , $this->uid , $url );

		$content = ( $this->caption ) ? $this->caption : $this->title;
		$tmpl->setContent( $this->title, $content );

		if( $this->cover_id )
		{
			$photo = FD::table( 'Photo' );
			$photo->load( $this->cover_id );

			$thumbnail 	= $photo->getSource( 'thumbnail' );
			if( $thumbnail )
			{
				$tmpl->setThumbnail( $thumbnail );
			}
		}

		$date = FD::date();
		$tmpl->setLastUpdate( $date->toMySQL() );

		$state = $indexer->index( $tmpl );
		return $state;
	}

	public function deleteIndex()
	{
		$indexer = FD::get( 'Indexer' );
		$indexer->delete( $this->id, SOCIAL_INDEXER_TYPE_ALBUMS);
	}


	public function uuid()
	{
		return $this->_uuid;
	}

	/**
	 * Retrieves the likes count for this album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLikesCount()
	{
		static $likes 	= array();

		if (!$this->id) return 0;

		if( !isset( $likes[ $this->id ] ) )
		{
			$likes[ $this->id ]	= FD::get( 'Likes' )->getCount( $this->id , SOCIAL_TYPE_ALBUM, 'create' );
		}

		return $likes[ $this->id ];
	}

	/**
	 * Retrieves the comments count for this album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentsCount()
	{
		static $comments 	= array();

		if (!$this->id) return 0;

		if( !isset( $comments[ $this->id ] ) )
		{
			$comments[ $this->id ]	= FD::comments( $this->id, SOCIAL_TYPE_ALBUM, 'create', SOCIAL_APPS_GROUP_USER )->getCount();
		}

		return $comments[ $this->id ];
	}

	/**
	 * Get the total number of tags for this album
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int
	 */
	public function getTagsCount()
	{
		$model 	= FD::model( 'Albums' );

		$tags 	= $model->getTotalTags( $this->id );

		return $tags;
	}

	/**
	 * Get the total number of tags for this album
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int
	 */
	public function isFavourite($userId)
	{
		$model = FD::model('Albums');

		$exists = $model->isFavourite($this->id, $userId);

		return $exists;
	}

	/**
	 * Retrieves a list of tags from all albums
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags( $usersOnly = false )
	{
		$model 	= FD::model( 'Albums' );

		$tags 	= $model->getTags( $this->id , $usersOnly );

		return $tags;
	}

	/**
	 * Retrieves the storage path for this album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStoragePath( $relative = false )
	{
		// Rename temporary folder to the destination.
		jimport( 'joomla.filesystem.folder' );

		// Get destination folder path.
		$config 	= FD::config();
		$path 		= '';

		if( !$relative )
		{
			$path 	= JPATH_ROOT;
		}

		$path 		= $path . '/' . FD::cleanPath( $config->get( 'photos.storage.container' ) );

		// Ensure that the storage folder exists.
		if( !$relative )
		{
			FD::makeFolder( $path );
		}

		// Build the storage path now with the album id
		$path 	= $path . '/' . $this->id;

		// Ensure that the final storage path exists.
		if( !$relative )
		{
			FD::makeFolder( $path );
		}

		return $path;
	}

	/**
	 * Gets the total number of photos for an album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPhotos()
	{
		static $total = array();

		if( !isset( $total[ $this->id ] ) )
		{
			$model 				= FD::model( 'Albums' );
			$total[ $this->id ]	= $model->getTotalPhotos( $this->id );
		}

		return $total[ $this->id ];
	}

	/**
	 * Determines if the album is owned by the provided user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isMine( $id = null )
	{
		$user 	= FD::user( $id );

		$isOwner	= $user->id == $this->uid;

		return $isOwner;
	}


	/**
	 * Determines if an album has a cover.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasCover()
	{
		if( $this->cover_id )
		{
			return true;
		}

		return false;
	}

	/**
	 * Build's the album's alias
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$title 	= $this->title;

		if ($this->core) {
			$title 	= JText::_($this->title);
		}

		$alias 	= $this->id . ':' . JFilterOutput::stringURLSafe($title);

		return $alias;
	}

	/**
	 * Retrieves the cover photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCover( $size = 'thumbnail' )
	{
		static $covers 	= array();
		static $photos 	= array();

		$index 		= $this->id . '-' . $size;

		if( !isset( $covers[ $index ] ) )
		{
			// If the album does not have a cover, load an empty photo object
			if( !$this->hasCover() )
			{
				// If the album does not have a cover, use the default album avatar
				$avatar 	= JURI::root() . 'media/com_easysocial/defaults/avatars/albums/large.png';

				// @TODO: Display according to it's own sizes
				return $avatar;
			}

			if( !isset( $photos[ $this->cover_id ] ) )
			{
				$photo 	= FD::table( 'Photo' );
				$photo->load( $this->cover_id );

				$photos[ $this->cover_id ]	= $photo;
			}

			$covers[ $index ]	= $photos[ $this->cover_id ]->getSource( $size );
		}

		return $covers[ $index ];
	}

	/**
	 * Retrieves the cover photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCoverObject()
	{
		$covers 	= array();

		if( !isset( $covers[ $this->id ] ) )
		{
			// If the album does not have a cover, load an empty photo object
			if( !$this->hasCover() )
			{
				// If the album does not have a cover, use the default album avatar
				$photo 		= FD::table( 'Photo' );

				return $photo;
			}

			$photo 	= FD::table( 'Photo' );
			$photo->load( $this->cover_id );

			$covers[ $this->id ]	= $photo;
		}

		return $covers[ $this->id ];
	}

	/**
	 * Retrieves the cover photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCoverUrl( $source = 'thumbnail' )
	{
		if( !is_null( $this->cover ) )
		{
			return $this->cover;
		}

		if( !$this->cover && $this->hasCover() )
		{
			$photo 			= FD::table( 'Photo' );
			$photo->load( $this->cover_id );

			$this->cover	= $photo->getSource( $source );
		}
		else
		{
			// @TODO: Make this configurable
			$this->cover 	= SOCIAL_DEFAULTS_URI . '/albums/cover.png';
		}

		return $this->cover;
	}

	/**
	 * Override parent's delete method
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete( $pk = null )
	{
		/*
		 * we need to delete the photos 1st and then only do ao parent::delete to avoid album getting onfindersave issue.
		 */

		// Delete the photos from the site first.
		$photosModel = FD::model( 'photos' );
		$photosModel->deleteAlbumPhotos( $this->id );

		// @points: photos.albums.remove
		// Deduct points for the author for deleting an album
		$points = FD::points();
		$points->assign( 'photos.albums.remove' , 'com_easysocial' , $this->uid );

		// Now, try to delete the folder that houses this photo.
		$config 	= FD::config();
		$storage 	= JPATH_ROOT . '/' . FD::cleanPath( $config->get( 'photos.storage.container' ) );
		$storage 	= $storage . '/' . $this->id;

		jimport( 'joomla.filesystem.folder' );

		$exists 	= JFolder::exists( $storage );

		// Test if the folder really exists first before deleting it.
		if( $exists )
		{
			$state 	= JFolder::delete( $storage );
		}

		// Delete the record from the database first.
		$state 	= parent::delete();

		// Delete likes related to the album
		$likes 	= FD::get( 'Likes' );
		$likes->delete( $this->id , SOCIAL_TYPE_ALBUM, 'create');

		// Delete comments related to the album
		$comments = FD::comments( $this->id, SOCIAL_TYPE_ALBUM, 'create', SOCIAL_APPS_GROUP_USER );
		$comments->delete();

		// Delete from smart search index
	    JPluginHelper::importPlugin('finder');
	    $dispatcher = JDispatcher::getInstance();
	    $dispatcher->trigger( 'onFinderAfterDelete', array( 'easysocial.albums' , $this ) );

		return $state;
	}

	public function addAlbumStream( $verb )
	{
		// for album, we only want to create stream when is a new album creation and not during update.

		if( $verb == 'create' )
		{
			$stream 	= FD::stream();

			$template 	= $stream->getTemplate();
			$template->setActor( $this->uid , $this->type );
			$template->setContext( $this->id , SOCIAL_STREAM_CONTEXT_ALBUMS );
			$template->setVerb( $verb );

			$template->setAccess( 'albums.view' );

			$template->setDate( $this->created );

			$stream->add( $template );
		}
	}

	/**
	 * Generates a new stream method.
	 *
	 */
	public function addStream( $verb )
	{
		// do nothing. Please do not remove this function!
	}

	/**
	 * Deletes a stream item
	 *
	 */
	public function removeStream()
	{
		$stream 	= FD::stream();

		return $stream->delete( $this->id , SOCIAL_STREAM_CONTEXT_ALBUMS );
	}

	/**
	 * Determines if this is a core album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isCore()
	{
		// If this is a system album like cover photos, profile pictures, they will not be able to delete them.
		$disallowed 	= array( SOCIAL_ALBUM_STORY_ALBUM , SOCIAL_ALBUM_PROFILE_COVERS , SOCIAL_ALBUM_PROFILE_PHOTOS );

		if( in_array( $this->core , $disallowed ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Tests if the album is editable by the provided user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	User id.
	 * @return
	 */
	public function editable($debug = false)
	{
		// Previously there is a isCore check here.
		// Restrictions limited to core albums should be
		// checked with $album->isCore(), not $album->editable().

		$lib 	= FD::albums($this->uid, $this->type, $this);

		return $lib->editable();
	}

	public function viewable( $id = null )
	{
		// TODO: Check if user can view this album.

		// If id not given, use current logged in user.
		if (!$id) {
			$my = FD::user();
			$id = $my->id;
		}

		// Get the privacy object
		$privacy = FD::privacy( $id );
		return $privacy->validate( 'albums.view', $this->id, 'albums', $this->uid );


		return true;
	}

	/**
	 * Determines if the album needs to display the date
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function hasDate()
	{
		if( $this->core )
		{
			return false;
		}

		return true;
	}

	/**
	 * Tests if the album is delete able by the provided user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	User id.
	 * @return
	 */
	public function deleteable( $id = null , $type = SOCIAL_TYPE_USER )
	{
		if( $this->isCore() )
		{
			return false;
		}

		if( $type == SOCIAL_TYPE_USER )
		{
			$user 	= FD::user( $id );

			// @TODO: Allow users with moderation / super admins to delete
			if( $this->uid == $user->id )
			{
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * Assign points to a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 		The actor's id
	 * @return
	 */
	public function assignPoints( $rule , $actorId )
	{
		$points = FD::points();
		$points->assign( $rule , 'com_easysocial' , $actorId );
	}

	public function getPhotos( $options = array() )
	{
		if( !$this->id )
		{
			return array( 'photos' => array() , 'nextStart' => -1 );
		}

		$lib 	= FD::albums( $this->uid , $this->type , $this->id );

		return $lib->getPhotos( $this->id, $options );
	}

	public function hasPhotos()
	{
		return ( $this->getTotalPhotos() > 0 );
	}

	/**
	 * Retrieves the permalink for the album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determines if it should be xhtml encoded
	 * @param	bool	Determines if the URL should be an external url.
	 * @return
	 */
	public function getPermalink( $xhtml = true , $external = false , $layout = 'item', $sef = true )
	{
		// Standard url options
		$options 	= array( 'id' => $this->getAlias() , 'layout' => $layout , 'uid' => $this->uid , 'type' => $this->type, 'sef' => $sef );

		if( $this->type == SOCIAL_TYPE_GROUP )
		{
			$options[ 'uid' ]	= FD::group( $this->uid )->getAlias();
		}

		if( $this->type == SOCIAL_TYPE_USER )
		{
			$options[ 'uid' ]	= FD::user( $this->uid )->getAlias();
		}

		if( $external )
		{
			$options[ 'external' ]	= true;
		}

		return FRoute::albums( $options , $xhtml );
	}

	/**
	 * Retrieves the edit permalink for the album
	 *
	 * @since	1.2
	 * @access	public
	 * @param	bool	Determines if it should be xhtml encoded
	 * @param	bool	Determines if the URL should be an external url.
	 * @return
	 */
	public function getEditPermalink( $xhtml = true , $external = false , $layout = 'form' )
	{
		$url 	= $this->getPermalink( $xhtml , $external , $layout );

		return $url;
	}

	public function getCreator()
	{
		return FD::user( $this->user_id );
	}

	/**
	 * Retrieves the location of the album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLocation()
	{
		static $locations 	= array();

		if( !isset( $locations[ $this->id ] ) )
		{
			$location 	= FD::table( 'Location' );
			$state 		= $location->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_ALBUM ) );

			if( !$state )
			{
				$locations[ $this->id ]	= $state;
			}
			else
			{
				$locations[ $this->id ]	= $location;
			}
		}

		return $locations[ $this->id ];
	}

	/**
	 * Retrieves the creation date of the album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCreationDate()
	{
		return $this->created;
	}

	/**
	 * Determines if this album has an assigned date.
	 *
	 * @since	1.2.8
	 * @access	public
	 * @param	string
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
	 * @since	1.0
	 * @access	public
	 * @param	string
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

	public function export( $flags = array() )
	{
		$properties = get_object_vars( $this );

		$album = array();

		foreach( $properties as $key => $value )
		{
			if( $key[0] != '_' )
			{
				$album[$key] = $value;
			}
		}

		$album['permalink'] = $this->getPermalink(false);

		if( in_array( 'cover', $flags ) )
		{
			if( $this->hasCover() )
			{
				$cover = FD::table( 'photo' );
				$cover->load( $this->cover_id );

				$album['cover'] = $cover->export();
			}
			else
			{
				$album['cover'] = array();
			}
		}

		if( in_array( 'photos', $flags ) )
		{
			$album['photos'] = array();

			$model 		= FD::model( 'Photos' );

			$result 	= $model->getPhotos( array( 'album_id' => $this->id , 'pagination' => false ) );
			$album[ 'photos' ]	= array();

			if( $result )
			{
				foreach( $result as $photo )
				{
					$album['photos'][] = $photo->export();
				}
			}

		}

		return $album;
	}
}
