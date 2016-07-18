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

FD::import('admin:/includes/model');

class EasySocialModelPhotos extends EasySocialModel
{
	static $_photometas = array();
	static $_cache = null;

	function __construct()
	{
		if (is_null(self::$_cache)) {
			self::$_cache = false;
		}

		parent::__construct('photos');
	}

	/**
	 * Retrieves the total amount of storage used by a specific user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDiskUsage($userId, $unit = 'b')
	{
		$db  = FD::db();
		$sql = $db->sql();

		$query = 'SELECT SUM(b.' . $db->quoteName('total_size') . ') FROM '
			    . $db->quoteName('#__social_albums') . ' AS a '
			    . 'INNER JOIN ' . $db->quoteName('#__social_photos') . ' AS b '
			    . 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('album_id') . ' '
			    . 'WHERE a.' . $db->quoteName('user_id') . '=' . $db->Quote($userId);


		$sql->raw($query);
		$db->setQuery($sql);

		$total = $db->loadResult();

		if ($unit == 'b') {
			return $total;
		}

		if ($unit == 'mb') {
			$total = round(($total / 1024) / 1024, 2);
		}

		return $total;
	}

	/**
	 * Retrieves the list of items which stored in Amazon
	 *
	 * @since	1.4.6
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPhotosStoredExternally($storageType = 'amazon')
	{
		// Get the number of files to process at a time
		$config = ES::config();
		$limit = $config->get('storage.amazon.limit', 10);

		$db = FD::db();
		$sql = $db->sql();
		$sql->select('#__social_photos');
		$sql->where('storage', $storageType);
		$sql->limit($limit);

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Stores the exif data for this photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function storeCustomMeta( SocialTablePhoto $photo , SocialExif $exif )
	{
		$config 		= FD::config();
		$storableItems 	= $config->get( 'photos.exif' );

		foreach( $storableItems as $property )
		{
			$method 	= 'get' . ucfirst( $property );

			if( is_callable( array( $exif ,$method ) ) )
			{
				$meta 				= FD::table( 'PhotoMeta' );
				$meta->photo_id 	= $photo->id;

				$meta->group		= "exif";
				$meta->property 	= $property;

				$meta->value 		= $exif->$method();

				$meta->store();
			}
		}

		return true;
	}

	/**
	 * Retrieve a list of tags for a particular photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags( $id , $peopleOnly = false )
	{
		$db		= FD::db();

		$sql	= $db->sql();

		$sql->select( '#__social_photos_tag' );
		$sql->where( 'photo_id' , $id );

		if( $peopleOnly )
		{
			$sql->where( 'uid' , '' , '!=' , 'AND' );
			$sql->where( 'type' , 'person' , '=' , 'AND' );
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
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function tag()
	{
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPhotos( $options = array() )
	{
		$db		= FD::db();

		// Get the query object
		$sql	= $db->sql();

		$state 		= isset($options['state']) ? $options['state'] : SOCIAL_STATE_PUBLISHED;
		$albumId 	= isset($options['album_id']) ? $options['album_id'] : null;
		$storage 	= isset($options['storage']) ? $options['storage'] : '';
		$uid 		= isset($options['uid']) ? $options['uid'] : false;
		$day 		= isset($options['day']) ? $options['day'] : false;

		$query = 'select count(1) from `#__social_photos`';

		if ($state == 'all') {
			$query .= ' WHERE (`state`=' . $db->Quote(SOCIAL_STATE_PUBLISHED) . ' OR `state`=' . $db->Quote(SOCIAL_STATE_UNPUBLISHED) . ')';
		} else {
			$query .= ' where `state` = ' . $db->Quote($state);
		}

		if ($uid) {
			$query .= ' and `uid` = ' . $db->Quote( $uid );
			$query .= ' and `type` = ' . $db->Quote( SOCIAL_TYPE_USER );
		}

		if ($albumId) {
			$query .= ' and `album_id` = ' . $db->Quote( $albumId );
		}


		if ($storage) {
			$query .= ' and `storage` = ' . $db->Quote( $storage );
		}

		if ($day) {
			$start 	= $day . ' 00:00:01';
			$end 	= $day . ' 23:59:59';
			$query .= ' and (`created` >= ' . $db->Quote( $start ) . ' and `created` <= ' . $db->Quote( $end ) . ')';
		}

		$sql->raw($query);
		$db->setQuery($sql);

		$count = $db->loadResult();
		return $count;
	}



	/**
	 * Retrieves list of photos
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlbumPhotos($albumIds, $limit = 5)
	{
		$db = ES::db();
		$sql = $db->sql();

		$accessColumn = "(select pri.value as `access` from `#__social_privacy_items` as pri";
		$accessColumn .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid=a.id and pri.`type` = 'photos'";
		$accessColumn .= " UNION ALL ";
		$accessColumn .= " select prm.value as `access`";
		$accessColumn .= " from `#__social_privacy_map` as prm";
		$accessColumn .= "	inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
		$accessColumn .= "	left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
		$accessColumn .= " where prm.uid = a.user_id and prm.utype = 'user'";
		$accessColumn .= "	and pp.type = 'photos' and pp.rule = 'view'";
		$accessColumn .= " union all ";
		$accessColumn .= " select prm.value as `access`";
		$accessColumn .= " from `#__social_privacy_map` as prm";
		$accessColumn .= "	inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
		$accessColumn .= "	inner join `#__social_profiles_maps` pmp on prm.uid = pmp.profile_id";
		$accessColumn .= " where prm.utype = 'profiles' and pmp.user_id = a.user_id";
		$accessColumn .= "	and pp.type = 'photos' and pp.rule = 'view'";
		$accessColumn .= " limit 1";
		$accessColumn .= ") as access";

		$accessCustomColumn = "(select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access` from `#__social_privacy_items` as pri";
		$accessCustomColumn .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid=a.id and pri.`type` = 'photos'";
		$accessCustomColumn .= " UNION ALL ";
		$accessCustomColumn .= " select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access`";
		$accessCustomColumn .= " from `#__social_privacy_map` as prm";
		$accessCustomColumn .= "	inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
		$accessCustomColumn .= "	left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
		$accessCustomColumn .= " where prm.uid = a.user_id and prm.utype = 'user'";
		$accessCustomColumn .= "	and pp.type = 'photos' and pp.rule = 'view'";
		$accessCustomColumn .= " limit 1";
		$accessCustomColumn .= ") as custom_access";

		$viewer = FD::user()->id;

		$query[] = "select * from (";


		$segments = array();
		foreach($albumIds as $aid) {
			$tmp = " (select a.*,";
			$tmp .= " $accessColumn, $accessCustomColumn";
			$tmp .= " from `#__social_photos` as a";
			$tmp .= " where a.`state` = " . $db->Quote(1);
			$tmp .= " and a.`album_id` = " . $db->Quote($aid);
			$tmp .= " order by a.`ordering` limit 5)";
			$segments[] = $tmp;
		}

		$query[] = implode( ' union all ', $segments);
		$query[] = ") as x";
		// privacy here.
		$query[] = ' WHERE (';

		//public
		$query[] = '(x.`access` = ' . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ') OR';

		//member
		$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ') AND (' . $viewer . ' > 0 ) ) OR ';

		//friends
		$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'x.`user_id`', $viewer ) . ') > 0 ) ) OR ';

		//only me
		$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ') AND ( x.`user_id` = ' . $viewer . ' ) ) OR ';

		// custom
		$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ') AND ( x.`custom_access` LIKE ' . $db->Quote( '%,' . $viewer . ',%' ) . '    ) ) OR ';

		// my own items.
		$query[] = '(x.`user_id` = ' . $viewer . ')';

		// privacy checking end here.
		$query[] = ')';

		// var_dump($query);exit;

		$query = implode(' ', $query);

		$sql->raw($query);

		$db->setQuery($sql);

		$result 	= $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$photos 	= array();
		$photosIds = array();

		foreach( $result as $row ) {
			$photo 	= FD::table( 'Photo' );
			$photo->bind( $row );

			$photos[$photo->album_id][]	= $photo;

			$photosIds[] = $photo->id;
		}


		if ($photosIds) {
			// lets cache photos meta here.
			ES::cache()->cachePhotos($photosIds);
		}


		return $photos;
	}





	/**
	 * Retrieves list of photos
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPhotos($options = array())
	{

		$accessColumn = "(select pri.value as `access` from `#__social_privacy_items` as pri";
		$accessColumn .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid=a.id and pri.`type` = 'photos'";
		$accessColumn .= " UNION ALL ";
		$accessColumn .= " select prm.value as `access`";
		$accessColumn .= " from `#__social_privacy_map` as prm";
		$accessColumn .= "	inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
		$accessColumn .= "	left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
		$accessColumn .= " where prm.uid = a.user_id and prm.utype = 'user'";
		$accessColumn .= "	and pp.type = 'photos' and pp.rule = 'view'";
		$accessColumn .= " union all ";
		$accessColumn .= " select prm.value as `access`";
		$accessColumn .= " from `#__social_privacy_map` as prm";
		$accessColumn .= "	inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
		$accessColumn .= "	inner join `#__social_profiles_maps` pmp on prm.uid = pmp.profile_id";
		$accessColumn .= " where prm.utype = 'profiles' and pmp.user_id = a.user_id";
		$accessColumn .= "	and pp.type = 'photos' and pp.rule = 'view'";
		$accessColumn .= " limit 1";
		$accessColumn .= ") as access";

		$accessCustomColumn = "(select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access` from `#__social_privacy_items` as pri";
		$accessCustomColumn .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid=a.id and pri.`type` = 'photos'";
		$accessCustomColumn .= " UNION ALL ";
		$accessCustomColumn .= " select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access`";
		$accessCustomColumn .= " from `#__social_privacy_map` as prm";
		$accessCustomColumn .= "	inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
		$accessCustomColumn .= "	left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
		$accessCustomColumn .= " where prm.uid = a.user_id and prm.utype = 'user'";
		$accessCustomColumn .= "	and pp.type = 'photos' and pp.rule = 'view'";
		$accessCustomColumn .= " limit 1";
		$accessCustomColumn .= ") as custom_access";


		$db = FD::db();
		$sql = $db->sql();

		$albumId = isset($options['album_id']) ? $options['album_id'] : null;
		$start = isset($options['start']) ? $options['start'] : 0;
		$limit = isset($options['limit']) ? $options['limit'] : 10;

		$state = isset($options['state']) ? $options['state'] : SOCIAL_STATE_PUBLISHED;
		$uid = isset($options['uid']) ? $options['uid'] : false;
		$storage = isset($options['storage']) ? $options['storage'] : '';
		$pagination = isset($options['pagination']) ? $options['pagination'] : true;
		$exclusion 		= isset($options['exclusion']) ? $options['exclusion'] :'';
		$privacy = isset($options['privacy']) ? $options['privacy'] :'';

		$noavatar = isset($options['noavatar']) && $options['noavatar'] ? true : false;
		$nocover = isset($options['nocover']) && $options['nocover'] ? true : false;

		$ordering = isset($options['ordering']) ? $options['ordering'] : '';
		$sort = isset($options['sort']) ? $options['sort'] : 'DESC';

		$query = array();

		if ($privacy) {
			$query[] = "select * from (";
		}


		$query[] = "select a.*";

		if ($privacy) {
			$query[] = ", $accessColumn, $accessCustomColumn";
		}

		$query[] = "from `#__social_photos` as a";

		if ($noavatar || $nocover) {
			$query[] = " inner join `#__social_albums` as b on a.album_id = b.id";
		}

		$query[] = "where a.`state` = " . $db->Quote($state);


		if (!is_null($albumId)) {
			$query[] = "and a.`album_id` = " . $db->Quote($albumId);
		}

		// If user id is specified, we only fetch photos that are created by the user.
		if ($uid) {
			$query[] = "and a.`uid` = " . $db->Quote($uid);
			$query[] = "and a.`type` = " . $db->Quote(SOCIAL_TYPE_USER);
		}

		if ($storage) {
			$query[] = " and a.`storage` = " . $db->Quote($storage);
		}

		// If there's an exclusion list, exclude it
		if (!empty($exclusion)) {

			// Ensure that it's an array
			$exclusion	= FD::makeArray($exclusion);

			$eIds = implode(',', $exclusion);
			$query[] = "and a.`id` NOT IN ($eIds)";
		}

		if ($noavatar && $nocover) {
			$query[] = " and b.`core` not in (1, 2)";
		} else if ($noavatar) {
			$query[] = " and b.`core` != 1";
		} else if ($nocover) {
			$query[] = " and b.`core` != 2";
		}

		if (!empty($ordering)) {

			if ($ordering == 'random') {
				$query[] = "order by RAND()";
			}

			if ($ordering == 'created') {
				$query[] = "order by a.`created` $sort";
			}

			if ($ordering == 'ordering') {
				$query[] = "order by a.`ordering` $sort";
			}

		} else {
			$query[] = "order by a.`ordering` $sort";
		}

		if ($privacy) {

			$viewer = FD::user()->id;


			$query[] = ") as x";

			// privacy here.
			$query[] = ' WHERE (';

			//public
			$query[] = '(x.`access` = ' . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ') OR';

			//member
			$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ') AND (' . $viewer . ' > 0 ) ) OR ';

			//friends
			$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'x.`user_id`', $viewer ) . ') > 0 ) ) OR ';

			//only me
			$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ') AND ( x.`user_id` = ' . $viewer . ' ) ) OR ';

			// custom
			$query[] = '( (x.`access` = ' . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ') AND ( x.`custom_access` LIKE ' . $db->Quote( '%,' . $viewer . ',%' ) . '    ) ) OR ';

			// my own items.
			$query[] = '(x.`user_id` = ' . $viewer . ')';

			// privacy checking end here.
			$query[] = ')';

		}

		// Determine if we should paginate items
		if ($pagination) {
			$query[] = "limit " . $start . "," . $limit;
		}

		$query = implode(' ', $query);

		$sql->raw($query);

		// echo $sql;
		// echo '<br />';

		$db->setQuery( $sql );


		$result 	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$photos 	= array();

		foreach( $result as $row )
		{
			$photo 	= FD::table( 'Photo' );
			$photo->bind( $row );

			$photos[]	= $photo;
		}

		return $photos;
	}

	public function generateIsFriendSQL( $source, $target )
	{
		$query = "select count(1) from `#__social_friends` where ( `actor_id` = $source and `target_id` = $target) OR (`target_id` = $source and `actor_id` = $target) and `state` = 1";

		return $query;
	}


	/**
	 * Retrieves the meta data about a photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMeta($photoId, $group = '', $property = false)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		if( ! self::$_cache )
		{
			$sql->select( '#__social_photos_meta' );
			$sql->where( 'photo_id' , $photoId );

			if ($group) {
				$sql->where('group', $group);
			}

			if ($property) {
				$sql->where('property', $property);
			}

			$db->setQuery($sql);
			$metas 	= $db->loadObjectList();

			return $metas;
		}


		if (!isset(self::$_photometas[$photoId])) {

			self::$_photometas[$photoId]	= array();

			$sql->select('#__social_photos_meta');
			$sql->where('photo_id', $photoId);

			$db->setQuery($sql);
			$metas 	= $db->loadObjectList();

			if ($metas) {
				foreach ($metas as $row) {
					self::$_photometas[$row->photo_id][$row->group][$row->property][] = $row;
				}
			}
		}

		// Default values
		$metas = array();

		if ($group && $property) {

			if (isset(self::$_photometas[$photoId][$group][$property])) {
				$metas = self::$_photometas[$photoId][$group][$property];

				return $metas;
			}

			return $metas;
		}


		if ($group) {

			if (isset(self::$_photometas[$photoId][$group])) {

				foreach (self::$_photometas[$photoId][$group] as $property => $items) {

					if ($items) {

						foreach ($items as $item) {
							$metas[] = $item;
						}
					}
				}

				return $metas;
			}

			return $metas;
		}


		if (isset(self::$_photometas[$photoId])) {

			foreach (self::$_photometas[$photoId] as $group => $items) {

				if ($items) {

					foreach ($items as $item) {
						$metas[] = $item;
					}
				}
			}
		}

		return $metas;
	}

	public function setCacheable( $cache = false )
	{
		self::$_cache  = $cache;
	}

	public function setMetasBatch( $ids )
	{

		$db = FD::db();
		$sql = $db->sql();

		$photoIds = array();

		foreach( $ids as $pid )
		{
			if(! isset( self::$_photometas[$pid] ) )
			{
				$photoIds[] = $pid;
			}
		}

		if( $photoIds )
		{
			foreach( $photoIds as $pid )
			{
				self::$_photometas[$pid] = array();
			}

			$query = '';
			$idSegments = array_chunk( $photoIds, 5 );
			//$idSegments = array_chunk( $photoIds, count( $photoIds ) );

			for( $i = 0; $i < count( $idSegments ); $i++ )
			{
				$segment    = $idSegments[$i];
				$ids = implode( ',', $segment );

				$query .= 'select * from `#__social_photos_meta` where `photo_id` IN ( ' . $ids . ')';

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
					self::$_photometas[$row->photo_id][$row->group][$row->property][] = $row;
				}
			}
		}
	}

	/**
	 * Allows caller to delete all the metadata about a photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteMeta( $photoId , $group = null )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_photos_meta' );
		$sql->where( 'photo_id' , $photoId );

		if( !is_null( $group ) )
		{
			$sql->where( 'group' , $group );
		}

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}

	/**
	 * Deletes all tags associated with a photo
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteTags( $photoId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_photos_tag' );
		$sql->where( 'photo_id' , $photoId );

		$db->setQuery( $sql );

		$db->Query();
	}

	/**
	 * Deletes all photos within the album.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The album id
	 * @return	boolean	True if success, false otherwise.
	 */
	public function deleteAlbumPhotos( $albumId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_photos' );
		$sql->column( 'id' );
		$sql->where( 'album_id', $albumId );

		$db->setQuery( $sql );

		$photoIds 	= $db->loadColumn();

		if( !$photoIds )
		{
			return false;
		}

		foreach( $photoIds as $id )
		{
			$photo 	= FD::table( 'Photo' );
			$photo->load( $id );

			$photo->delete();
		}

		return true;
	}

	/**
	 * Determines if the photo is used as a profile cover
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The photo id
	 * @param	int		The user id
	 * @return
	 */
	public function isProfileCover( $photoId , $uid , $type )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_covers' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'photo_id' , $photoId );
		$sql->where( 'uid' , $uid );
		$sql->where( 'type' , $type );

		$db->setQuery( $sql );

		$exists	= $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	public function pushPhotosOrdering( $albumId, $except = 0, $index = 0, $type = '+' )
	{
		$query = "UPDATE `#__social_photos` SET `ordering` = `ordering` " . $type . " 1 WHERE `album_id` = '" . $albumId . "' AND `ordering` >= '" . $index . "' AND `id` <> '" . $except . "'";

		$db = FD::db();
		$sql = $db->sql();

		$sql->raw( $query );

		$db->setQuery( $sql );

		return $db->query();
	}

	/**
	 * Determines if the photo should be associated with the stream item
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPhotoStreamId($photoId, $verb, $validate = true)
	{
		static $_cache = array();

		$db		= FD::db();
		$sql	= $db->sql();

		$index = $photoId . $verb . $validate;

		if (! isset($_cache[$index])) {

			$sql->select('#__social_stream_item', 'a');
			$sql->column('a.uid');
			$sql->where('a.context_type', SOCIAL_TYPE_PHOTO);
			$sql->where('a.context_id', $photoId);

			if ($verb == 'upload') {
				$sql->where('a.verb', 'share');
				$sql->where('a.verb', 'upload', '=', 'OR');
			} else if($verb == 'add') {
				$sql->where('a.verb', 'create');
			} else {
				$sql->where('a.verb', $verb);
			}

			$db->setQuery($sql);

			$uid 	= (int) $db->loadResult();
			$_cache[$index] = $uid;

			if (!$uid) {
				$_cache[$index] = false;
				return;
			}

			// Check if the uid exists multiple times and if this is a shared record
			if ($validate && $verb == 'share') {

				$sql->clear();

				$sql->select('#__social_stream_item', 'a');
				$sql->column('COUNT(a.`uid`)');
				$sql->where('a.uid', $uid);

				$db->setQuery($sql);

				$total 	= $db->loadResult();

				if ($total == 1) {
					$_cache[$index] = false;
					return false;
				}

				// return $uid;
				$_cache[$index] = $uid;
			}

		}

		return $_cache[$index];
	}

	public function getPhotoStreamIdx( $photoId, $verb )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select('#__social_stream_item', 'a');
		$sql->column('a.uid');
		$sql->where('a.context_type', SOCIAL_TYPE_PHOTO);
		$sql->where('a.context_id', $photoId);
		$sql->where('a.verb', $verb);

		$db->setQuery($sql);

		$uid 	= (int) $db->loadResult();

		if (!$uid){
			return false;
		}

		// If the photo is uploaded in the story form, we need to link to the stream only when there's more than 1 photo
		if ($verb == 'share') {
			$sql->group('a.uid');
			$sql->having('count(a.uid)', '1', '=');
		}


		return $uid;
	}

	public function delPhotoStream( $photoId, $photoOnwerId, $albumId )
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "select a.`id`, a.`uid` from `#__social_stream_item` as a";
		$query .= " where a.`context_type` = '" . SOCIAL_TYPE_PHOTO . "'";
		$query .= " and a.`context_id` = '$photoId'";
		$query .= " and a.`target_id` = '$albumId'";
		$query .= " and a.`actor_id` = '$photoOnwerId'";

		$sql->raw($query);
		$db->setQuery($sql);

		$row = $db->loadObject();

		if( $row )
		{
			$itemId 	= $row->id;
			$streamId 	= $row->uid;

			$query = "delete from `#__social_stream_item` where `id` = '$itemId'";
			$sql->raw($query);

			$db->setQuery($sql);
			$state = $db->query();

			//check if this stream id still have other records or not. if no, then we remove the main stream as well.
			$query = "select count(1) from `#__social_stream_item` where `uid` = '$streamId'";
			$sql->raw($query);
			$db->setQuery($sql);

			$result = $db->loadResult();

			if (empty($result)){
				$query = "delete from `#__social_stream` where `id` = '$streamId'";
				$sql->raw($query);

				$db->setQuery($sql);
				$state = $db->query();
			}


		}

		return true;
	}


}
