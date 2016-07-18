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

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelAlbums extends EasySocialModel
{
	function __construct( $config = array() )
	{
		parent::__construct( 'albums' , $config );
	}

	/**
	 * Populates the state
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initStates()
	{
		$profile 	= $this->getUserStateFromRequest( 'profile' );
		$group 		= $this->getUserStateFromRequest( 'group' );
		$published	= $this->getUserStateFromRequest( 'published' , 'all' );

		$this->setState( 'published' , $published );
		$this->setState( 'group'	, $group );
		$this->setState( 'profile'	, $profile );

		parent::initStates();
	}

	/**
	 * Retrieves list of albums for admin area
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDataWithState()
	{
		$db 	= FD::db();

		// Get the query object
		$sql 	= $db->sql();

		$sql->select( '#__social_albums' , 'a' );
		$sql->column( 'a.*' );
		$sql->column( 'COUNT(b.id)' , 'totalphotos' );

		$sql->join( '#__social_photos' , 'b' );
		$sql->on( 'a.id' , 'b.album_id' );

		$sql->group( 'a.id' );

		// Determines if we should search for the title
		$search 	= $this->getState( 'search' );

		if( $search )
		{
			$sql->where( 'a.title' , '%' . $search . '%' , 'LIKE' , 'OR' );
			$sql->where( 'a.caption' , '%' . $search . '%' , 'LIKE' , 'OR' );
		}
		// Determine the ordering
		$ordering	= $this->getState( 'ordering' );

		if( $ordering )
		{
			$direction	= $this->getState( 'direction' );

			$sql->order( $ordering , $direction );
		}

		// We should only be picking up photos which are valid
		$sql->where( 'b.state' , SOCIAL_STATE_PUBLISHED );

		// Determine the pagination limit
		$limit 	= $this->getState( 'limit' );

		if( $limit )
		{
			// Set the total number of items.
			$this->setTotal( $sql->getSql() , true );

			// Get the list of users
			$result 	= parent::getData( $sql->getSql() , true );
		}
		else
		{
			$db->setQuery( $sql );
			$result 	= $db->loadObjectList();
		}

		$albums 	= array();

		foreach( $result as $row )
		{
			$album 	= FD::table( 'Album' );
			$album->bind( $row );

			// Set custom attributes
			$album->totalphotos 	= $row->totalphotos;

			$albums[]	= $album;
		}

		return $albums;
	}

	/**
	 * Retrieves list of albums
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlbums($uid = '', $type = '', $options = array())
	{
		$config = FD::config();
		$db = FD::db();

		// Get the query object
		$sql = $db->sql();

		$sql->select( '#__social_albums', 'a' );
		$sql->column( 'a.*' );

		$excludeblocked = isset( $options[ 'excludeblocked' ] ) ? $options[ 'excludeblocked' ] : 0;

		if ($config->get('users.blocking.enabled') && $excludeblocked && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.user_id' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		if ($uid) {
			$sql->where( 'uid', $uid );
		}

		if ($type) {
			$sql->where( 'type', $type );
		}

		$exclusion 	= isset( $options[ 'exclusion' ] ) ? $options[ 'exclusion' ] : '';
		$privacy	= isset( $options[ 'privacy' ] ) ? $options[ 'privacy' ] : false;

		if ($exclusion) {

			$exclusion 	= FD::makeArray( $exclusion );

			foreach ($exclusion as $excludeAlbumId) {
				$sql->where( 'id' , $excludeAlbumId , '!=' );
			}
		}

		// if present, we want to filter albums for this particular user only
		$userId	= isset( $options[ 'userId' ] ) ? $options[ 'userId' ] : false;
		if ($userId) {
			$sql->where('a.user_id', $userId);
		}

		// if present, we want to filter this particular album only
		$albumId	= isset( $options[ 'albumId' ] ) ? $options[ 'albumId' ] : false;
		if ($albumId) {
			$sql->where('a.id', $albumId);
		}


		// Determine if we should include the core albums
		$coreAlbums 	= isset( $options[ 'core' ] ) ? $options[ 'core' ] : true;

		if( !$coreAlbums )
		{
			$sql->where( 'core' , 0 );
		}

		$coreAlbumsOnly	= isset( $options[ 'coreAlbumsOnly' ] ) ? $options[ 'coreAlbumsOnly' ] : '';

		if( $coreAlbumsOnly )
		{
			$sql->where( 'core' , 0 , '>' );
		}

		$favourite = isset( $options[ 'favourite' ] ) ? $options[ 'favourite' ] : '';
		if ($favourite) {
			$sql->join( '#__social_albums_favourite' , 'fa' , 'INNER' );
			$sql->on( 'fa.album_id' , 'a.id' );
			$sql->on( 'fa.user_id' ,  $options['userFavourite']);
		}

		$withCoversOnly	= isset( $options[ 'withCovers' ] ) ? $options[ 'withCovers' ] : '';

		if( $withCoversOnly )
		{
			$sql->join( '#__social_photos' , 'b' , 'INNER' );
			$sql->on( 'cover_id' , 'b.id' );
		}

		$ordering 		= isset( $options[ 'order' ] ) ? $options[ 'order' ] : '';

		if( $ordering )
		{
			$direction 	= isset( $options[ 'direction' ] ) ? $options[ 'direction' ] : 'desc';

			$sql->order( $ordering , $direction );
		}

		$pagination 	= isset( $options[ 'pagination' ] ) ? $options[ 'pagination' ] : false;

		$result = array();

		// echo $sql;

		if( $pagination )
		{
			// Set the total number of items.
			$totalSql 		= $sql->getSql();
			$this->setTotal( $totalSql , true );

			$result			= $this->getData( $sql->getSql() );
		}
		else
		{
			$limit 		= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';
			if( $limit )
			{
				$sql->limit( $limit );
			}

			$db->setQuery( $sql );
			$result 	= $db->loadObjectList();
		}



		if( !$result )
		{
			return $result;
		}

		$albums 	= array();

		$privacyLib = FD::privacy( FD::user()->id );

		foreach( $result as $row )
		{
			$album 	= FD::table( 'Album' );
			$album->bind( $row );

			$add = true;
			if ($privacy) {
				if ($album->type == SOCIAL_TYPE_USER) {
					$add = $privacyLib->validate( 'albums.view' , $album->id, SOCIAL_TYPE_ALBUM , $album->user_id );
				} else if($album->type == SOCIAL_TYPE_GROUP) {
					$group = Foundry::group($album->uid);
					if ($group->isOpen()) {
						$add = true;
					} else {
						$add = $group->isMember() || FD::user()->isSiteAdmin();
					}
				}
			}

			if ($add) {
				$albums[]	= $album;
			}
		}

		return $albums;
	}

	/**
	 * Creates a default album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	SocialTableAlbum
	 */
	public function createDefaultAlbum( $uid , $type , $defaultType )
	{
		$album 			= FD::table( 'Album' );

		if( $defaultType == SOCIAL_ALBUM_PROFILE_PHOTOS )
		{
			$album->title 	= 'COM_EASYSOCIAL_ALBUMS_PROFILE_AVATAR';
			$album->caption	= 'COM_EASYSOCIAL_ALBUMS_PROFILE_AVATAR_DESC';
		}

		if( $defaultType == SOCIAL_ALBUM_PROFILE_COVERS )
		{
			$album->title 	= 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER';
			$album->caption	= 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER_DESC';
		}

		if( $defaultType == SOCIAL_ALBUM_STORY_ALBUM )
		{
			$album->title 	= 'COM_EASYSOCIAL_ALBUMS_STORY_PHOTOS';
			$album->caption	= 'COM_EASYSOCIAL_ALBUMS_STORY_PHOTOS_DESC';
		}

		$album->uid 	= $uid;
		// This might not work if admin creates default album for another user
		$album->user_id = FD::user()->id;
		$album->type 	= $type;
		$album->core 	= $defaultType;

		$album->store();

		return $album;
	}

	/**
	 * Retrieves the default album for a particular node
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The node id.
	 * @param	string	The node type.
	 * @param	string	The album type.
	 * @return	SocialTableAlbum
	 */
	public function getDefaultAlbum( $uid , $type , $albumType )
	{
		$exists 	= $this->hasDefaultAlbum( $uid , $type , $albumType );

		if( !$exists )
		{
			return $this->createDefaultAlbum( $uid , $type , $albumType );
		}

		$album 	= FD::table( 'Album' );
		$album->load( array( 'uid' => $uid , 'type' => $type , 'core' => $albumType ) );

		return $album;
	}

	/**
	 * Determines if there is a default album created for a given user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasDefaultAlbum( $uid , $type , $defaultType )
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__social_albums' );
		$sql->column( 'COUNT(1)' , 'total' );
		$sql->where( 'core' , $defaultType );
		$sql->where( 'uid'	, $uid );
		$sql->where( 'type'	, $type );

		$db->setQuery( $sql );

		$exists 	= $db->loadResult() >= 1;

		return $exists;
	}

	/**
	 * Determines if this album is already favourite
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFavourite($albumId, $userId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_albums_favourite');
		$sql->column('COUNT(1)', 'total');
		$sql->where('album_id', $albumId);
		$sql->where('user_id', $userId);

		$db->setQuery( $sql );

		$exists = $db->loadResult() >= 1;

		return $exists;
	}

	/**
	 * Remove album from favourite
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFavourite($albumId, $userId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->delete('#__social_albums_favourite');
		$sql->where('album_id', $albumId);
		$sql->where('user_id', $userId);

		$db->setQuery($sql);
        return $db->Query();
	}

	/**
	 * Add album as favourite
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addFavourite($albumId, $userId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->insert( '#__social_albums_favourite' );
		$sql->values( 'album_id' , $albumId );
		$sql->values( 'user_id' , $userId );

		$db->setQuery($sql);
		return $db->Query();
	}

	/**
	 * Retrieve the number of tags in this album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalTags( $id , $userOnly = false )
	{
		$db		= FD::db();

		$sql	= $db->sql();

		$sql->select( '#__social_photos_tag' , 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->join( '#__social_photos' , 'b' , 'INNER' );
		$sql->on( 'a.photo_id' , 'b.id' );
		$sql->where( 'b.album_id' , $id );

		// Determines if we need to fetch tags that are associated with real users only.
		if( $userOnly )
		{
			$sql->where( 'a.type' , 'person' );
			$sql->where( 'a.uid' , '0' , '!=' );
		}

		$db->setQuery( $sql );

		$result 	= $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve a list of tags that are used in a particular album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags( $id , $userOnly = false )
	{
		$db		= FD::db();

		$sql	= $db->sql();

		$sql->select( '#__social_photos_tag' , 'a' );
		$sql->column( 'a.*' );
		$sql->join( '#__social_photos' , 'b' , 'INNER' );
		$sql->on( 'a.photo_id' , 'b.id' );
		$sql->where( 'b.album_id' , $id );

		// Determines if we need to fetch tags that are associated with real users only.
		if( $userOnly )
		{
			$sql->where( 'a.type' , 'person' );
			$sql->where( 'a.uid' , '0' , '!=' );

			$sql->group( 'a.type' );
			$sql->group( 'a.uid' );
		}

		$db->setQuery( $sql );
		$result 	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$tags 	= array();

		foreach( $result as $row )
		{
			$tag 	= FD::table( 'PhotoTag' );
			$tag->bind( $row );

			$tags[]	= $tag;
		}

		return $tags;
	}

	/**
	 * Retrieves the total number of photos created within an album
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int
	 */
	public function getTotalPhotos( $albumId )
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__social_photos' );
		$sql->column( 'COUNT(1)' , 'total' );
		$sql->where( 'state' , SOCIAL_STATE_PUBLISHED );
		$sql->where( 'album_id' , $albumId );

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total number of albums created on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	int
	 */
	public function getTotalAlbums( $options = array() )
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__social_albums' );
		$sql->column( 'COUNT(1)' , 'total' );

		if( isset( $options[ 'uid' ] ) && isset( $options[ 'type' ] ) )
		{
			$sql->where( 'uid'	, $options[ 'uid' ] );
			$sql->where( 'type'	, $options[ 'type' ] );
		}

		// Determines if we should exclude core albums
		$excludeCore 	= isset( $options[ 'excludeCore' ] ) ? $options[ 'excludeCore' ] : '';

		if( $excludeCore )
		{
			$sql->where( 'core' , 0 );
		}

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}

	public function getStreamId( $uid )
	{
		$db		= FD::db();

		// Get a list of items from the item table first.
		$sql	= $db->sql();

		$sql->select( '#__social_stream_item' );
		$sql->column( 'uid' );
		$sql->where( 'context_id' , $uid );
		$sql->where( 'context_type', 'albums' );
		$sql->limit( 1 );

		$db->setQuery( $sql );

		$item = $db->loadResult();

		if(! $item )
			return false;
		else
			return $item;
	}

	public function getFavouriteParticipants($albumId)
	{
		$db		= FD::db();

		// Get a list of items from the item table first.
		$sql	= $db->sql();

		$sql->select( '#__social_albums_favourite' );
		$sql->column( 'user_id' );
		$sql->where( 'album_id' , $albumId );

		$db->setQuery( $sql );

		$item = $db->loadColumn();

		return $item;
	}

}
