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

class EasySocialModelProfiles extends EasySocialModel
{
	public function __construct( $config = array() )
	{
		parent::__construct( 'profiles' , $config );
	}

	/**
	 * Initializes all the generic states from the form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initStates()
	{
		$filter 	= $this->getUserStateFromRequest( 'state' , 'all' );
		$ordering 	= $this->getUserStateFromRequest( 'ordering' , 'ordering' );
		$direction	= $this->getUserStateFromRequest( 'direction' , 'ASC' );

		$this->setState( 'state' , $filter );


		parent::initStates();

		// Override the ordering behavior
		$this->setState( 'ordering' , $ordering );
		$this->setState( 'direction' , $direction );
	}

	/**
	 * Saves the ordering of profiles
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveOrder( $ids , $ordering )
	{
		$table 	= FD::table( 'Profile' );
		$table->reorder();
	}

	public function updateOrdering($id, $order)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "update `#__social_profiles` set ordering = " . $db->Quote($order);
		$query .= " where id = " . $db->Quote($id);

		$sql->raw($query);

		$db->setQuery($sql);
		$state = $db->query();

		return $state;
	}

	/**
	 * Gets the default profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultProfile()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_profiles' );
		$sql->where( 'default' , 1 );

		$db->setQuery( $sql );

		$row 	= $db->loadObject();

		$noDefaultSet = false;

		// If no default profile found then fetch the first one from the database
		if( !$row )
		{
			$sql->clear();
			$sql->select( '#__social_profiles' );
			$sql->limit( 1 );

			$db->setQuery( $sql );

			$row = $db->loadObject();

			$noDefaultSet = true;
		}

		$profile	= FD::table( 'Profile' );
		$profile->bind( $row );

		if( $noDefaultSet )
		{
			$profile->makeDefault();
		}

		return $profile;
	}

	/**
	 * Gets the profile field
	 *
	 * @since	1.2.1
	 * @access	public
	 * @param	int - profile id
	 *          string - field_unique_code
	 * @return  object
	 */
	public function getProfileField( $profileId, $fieldCode )
	{
		$db = FD::db();
		$sql = $db->sql();

		// select * from jos_social_fields as f
		// 	inner join jos_social_fields_steps as s on f.step_id = s.id
		// where f.unique_key = 'JOOMLA_FULLNAME'
		// and s.uid = 186
		// and s.type = 'profiles';

		$sql->column('f.*');
		$sql->select('#__social_fields', 'f');
		$sql->innerjoin('#__social_fields_steps', 's');
		$sql->on('f.step_id', 's.id');
		$sql->where('f.unique_key', $fieldCode);
		$sql->where('s.uid', $profileId);
		$sql->where('s.type', 'profiles');

		$db->setQuery($sql);
		$data = $db->loadObject();

		return $data;
	}

	/**
	 * Retrieves the total number of profiles in the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int		The total number of profiles.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTotalProfiles($options = array())
	{
		$db 	= FD::db();

		$query	= array();

		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__social_profiles' );
		$query[]	= 'WHERE ' . $db->nameQuote('state') . '=' . $db->Quote( SOCIAL_STATE_PUBLISHED );

		if (isset($options['registration'])) {
			$query[] = 'AND ' . $db->nameQuote('registration') . '=' . $db->Quote(1);
		}

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );
		$count 		= (int) $db->loadResult();

		return $count;
	}

	/**
	 * Retrieves a list of custom profiles from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array	An array list of SocialTableProfile
	 *
	 */
	public function getItems( $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_profiles' );

		// Check for search
		$search 	= $this->getState( 'search' );

		if( $search )
		{
			$sql->where( 'title' , '%' . $search . '%' , 'LIKE' );
		}

		// Check for ordering
		$ordering 	= $this->getState( 'ordering' );

		if( $ordering )
		{
			$direction	 = $this->getState( 'direction' ) ? $this->getState( 'direction' ) : 'DESC';

			$sql->order( $ordering , $direction );
		}

		// Check for state
		$state 		= $this->getState( 'state' );

		if( $state != 'all' )
		{
			$sql->where( 'state' , $state );
		}

		// Set the total records for pagination.
		$this->setTotal( $sql->getTotalSql() );

		$db->setQuery( $sql );

		$result		= $this->getData( $sql->getSql() );

		if( !$result )
		{
			return false;
		}

		$profiles	= array();
		$total      = count( $result );

		for( $i = 0; $i < $total; $i++ )
		{
			$profile       = FD::table( 'Profile' );
			$profile->bind( $result[ $i ] );

			$profiles[]    = $profile;
		}

		return $profiles;
	}

	/**
	 * Retrieves a list of users not in any profiles.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Profiles' );
	 * $count 	= $model->getOrphanMembersCount( false );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param   int 	The unique profile id.
	 * @param	boolean	true / false
	 * @return  int 	count of users who doesnt assigned with profile
	 *
	 * @author	Sam <sam@stackideas.com>
	 */
	public function getOrphanMembersCount( $publishedOnly = true )
	{
		$db 		= FD::db();
		$query 		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__users' ) . ' AS a';
		$query[]	= 'WHERE NOT EXISTS ( select user_id from ' . $db->nameQuote( '#__social_profiles_maps' ) . ' AS b';
		$query[]	= 'where a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'user_id' ) . ')';

		if( $publishedOnly )
			$query[]	= 'AND a.' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function deleteOrphanItems()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'delete from `#__social_profiles_maps` where not exists ( select `id` from `#__social_profiles` where `profile_id` = `id` )';
		$sql->raw( $query );

		$db->setQuery( $sql );
		$db->query();

		return true;
	}



	/**
	 * Retrieves a list of users in this profile type.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Profiles' );
	 *
	 * //Displays 10 members from the profile.
	 * $model->getMembers( JRequest::getInt( 'id' ) , array( 'limit' => 10 ) );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param   int 	The unique profile id.
	 * @param	Array	An array of options. (randomize=>bool,limit => int)
	 * @return  Array   An array of SocialUser object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getMembers($profileId, $options = array())
	{
		$config = FD::config();
		$db = ES::db();

		// Determine if we should randomize the result.
		$randomize 	= isset( $options[ 'randomize' ] ) ? true : false;
		$limit 		= isset( $options[ 'limit' ] ) ? (int) $options[ 'limit' ] : false;

		$query		= array();
		$query[]	= 'SELECT b.' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__social_profiles_maps' ) . ' AS a';

		// Joins
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'id' ) . ' = a.' . $db->nameQuote( 'user_id' );


		$excludeBlocked 	= isset($options[ 'excludeblocked' ] ) ? $options[ 'excludeblocked' ] : 0;
		if ($config->get('users.blocking.enabled') && $excludeBlocked && !JFactory::getUser()->guest) {
		    // user block
		    $query[] = ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query[] = ' ON b.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query[] = ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}


		// Where
		$query[] = 'WHERE a.' . $db->nameQuote('profile_id' ) . '=' . $db->Quote( $profileId );
		$query[] = 'AND b.' . $db->nameQuote('block') . ' = ' . $db->Quote(0);

		// user block continue here
		if ($config->get('users.blocking.enabled') && $excludeBlocked && !JFactory::getUser()->guest) {
		    $query[] = ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		// Randomize the result if necessary
		if( $randomize )
		{
			$query[]	= 'ORDER BY RAND()';
		}

		// If limit is set, we need to define the limit here.
		if( $limit )
		{
			$query[]	= 'LIMIT 0,' . $limit;
		}


		// Merge queries back.
		$query 	= implode( ' ' , $query );

		// Debug
		// echo str_ireplace( '#__' , 'jos_' , $query ) . '<br />';
		// exit;

		$db->setQuery( $query );

		// Load by column
		$result = $db->loadColumn();

		if( !$result )
		{
			return $result;
		}

		// Pre-load these users.
		$users	= FD::user( $result );

		// Ensure that $users is an array.
		$users	= FD::makeArray( $users );

		// Randomize the result if necessary.
		if( $randomize )
		{
			shuffle( $users );
		}

		return $users;
	}


	/**
	 * Retrieves random albums
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRandomAlbums($options = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_albums', 'a');
		$sql->column('a.*');

		// Filter by profile id
		$profileId = isset($options['profileId']) ? $options['profileId'] : '';

		if ($profileId) {
			$sql->join('#__social_profiles_maps', 'b');
			$sql->on('a.user_id', 'b.user_id');

			$sql->where('b.profile_id', (int) $profileId);
		}

		// Determine if we should include the core albums
		$coreAlbums 	= isset($options[ 'core' ]) ? $options[ 'core' ] : true;

		if (!$coreAlbums) {
			$sql->where('a.core', 0);
		}

		$coreAlbumsOnly	= isset($options[ 'coreAlbumsOnly' ]) ? $options[ 'coreAlbumsOnly' ] : '';

		if ($coreAlbumsOnly) {
			$sql->where('a.core', 0, '>');
		}

		$withCoversOnly	= isset($options[ 'withCovers' ]) ? $options[ 'withCovers' ] : '';

		if ($withCoversOnly) {
			$sql->join('#__social_photos', 'b', 'INNER');
			$sql->on('a.cover_id', 'b.id');
		}

		$ordering 		= isset($options[ 'order' ]) ? $options[ 'order' ] : '';

		if ($ordering) {
			$direction 	= isset($options[ 'direction' ]) ? $options[ 'direction' ] : 'desc';

			$sql->order($ordering, $direction);
		}


		$pagination 	= isset($options[ 'pagination' ]) ? $options[ 'pagination' ] : false;


		$result = array();

		if ($pagination) {
			// Set the total number of items.
			$totalSql 		= $sql->getSql();
			$this->setTotal($totalSql, true);

			$result			= $this->getData($sql->getSql());
		}
		else
		{
			$limit 		= isset($options[ 'limit' ]) ? $options[ 'limit' ] : '';
			if ($limit) {
				$sql->limit($limit);
			}

			$db->setQuery($sql);
			$result 	= $db->loadObjectList();
		}



		if (!$result) {
			return $result;
		}

		$albums 	= array();

		foreach ($result as $row) {
			$album 	= FD::table('Album');
			$album->bind($row);

			$albums[]	= $album;
		}

		return $albums;
	}

	/**
	 * Removes user from existing profiles
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @return
	 */
	public function removeUserFromProfiles( $id )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_profiles_maps' );
		$sql->where( 'user_id' , $id );

		$db->setQuery( $sql );

		$db->Query();
	}

	/**
	 * Updates the user groups assigned
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateJoomlaGroup($userId, $profileId)
	{
		$profile = ES::table('Profile');
		$profile->load($profileId);

		// Get the list of groups
		$gid = $profile->getJoomlaGroups();
		$options = array('gid' => $gid);

		// Get the current user object and assign it
		$user = ES::user($userId);
		$user->bind($options);

		// Save the user object
		$state = $user->save();

		return $state;
	}

	/*
	 * Update the fields that are associated to certain profile type.
	 *
	 * @param   int     $profileId  The profile type id.
	 */
	public function updateFields( $profileId , $fields )
	{
		$db 	= FD::db();

		// First in first out.
		$query  = 'DELETE FROM ' . $db->nameQuote( '#__social_profile_types_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'profile_id' ) . '=' . $db->Quote( $profileId );

		$db->setQuery( $query );
		$db->Query();

		$query  = 'INSERT INTO ' . $db->nameQuote( '#__social_profile_types_fields' ) . ' VALUES ';

		if( is_array( $fields ) )
		{
			$total  = count( $fields );
			for( $i = 0; $i < $total; $i++ )
			{
				$query  .= '(' . $db->Quote( $profileId ) . ',' . $db->Quote( $fields[ $i ] ) . ')';

				if( ( $i + 1 ) != $total )
				{
					$query  .= ',';
				}
			}
		}
		$db->setQuery( $query );
		$db->Query();
	}

	/**
	 * Updates a user profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateUserProfile($uid, $profileId)
	{
		$map = ES::table('ProfileMap');
		$exists = $map->load(array('user_id' => $uid));

		if (!$exists) {
			$map->user_id = $uid;
			$map->state = SOCIAL_STATE_PUBLISHED;
		}

		$map->profile_id = $profileId;

		$state = $map->store();

		if (!$state) {
			$this->setError($map->getError());
			return $state;
		}

		$db = ES::db();
		$sql = $db->sql();

		$sql->update( '#__social_fields_data', 'a');
		$sql->leftjoin( '#__social_fields', 'b' );
		$sql->on( 'a.field_id', 'b.id' );
		$sql->leftjoin('#__social_fields', 'c' );
		$sql->on( 'b.unique_key', 'c.unique_key' );
		$sql->leftjoin( '#__social_fields_steps', 'd' );
		$sql->on( 'c.step_id', 'd.id' );
		$sql->set( 'a.field_id', 'c.id', false );
		$sql->where( 'a.uid', $uid );
		$sql->where( 'a.type', 'user' );
		$sql->where( 'd.type', 'profiles' );
		$sql->where( 'd.uid', $profileId ) ;

		$db->setQuery($sql);

		$state = $db->query();

		if ($state) {

			// Update fields privacy according to the new profile
			$sql 		= $db->sql();

			// update `jos_social_privacy_items as a
			//  left join jos_social_fields as b
			//  	on a.uid = bid and a.type = 'field'
			//  left join jos_social_fields as c
			//  	on b.unique_key = c.unique_key
			//  left join jos_social_fields_steps as d
			//  	on c.step_id = d.id
			//  set a.uid = c.id
			//  where a.user_id = $uid
			//  and a.type = 'field'
			//  and d.type = 'profiles'
			//  and d.uid = $profileId;

			$sql->update( '#__social_privacy_items', 'a');
			$sql->leftjoin( '#__social_fields', 'b' );
			$sql->on( 'a.uid', 'b.id' );
			$sql->leftjoin('#__social_fields', 'c' );
			$sql->on( 'b.unique_key', 'c.unique_key' );
			$sql->leftjoin( '#__social_fields_steps', 'd' );
			$sql->on( 'c.step_id', 'd.id' );

			$sql->set( 'a.uid', 'c.id', false );

			$sql->where( 'a.user_id', $uid );
			$sql->where('(');
			$sql->where( 'a.type', 'field' );
			$sql->where( 'a.type', 'year' ,'=', 'OR' );
			$sql->where(')');
			$sql->where( 'd.type', 'profiles' );
			$sql->where( 'd.uid', $profileId ) ;

			$db->setQuery( $sql );

			$state = $db->query();

		}

		return $state;

	}

	/**
	 * Retrieves a list of profile types throughout the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of options. (state - Determine the state of the profiles, ordering - The ordering type)
	 * @return
	 */
	public function getProfiles( $config = array() )
	{
		$db = FD::db();
		$query = array();
		$my = FD::user();

		// Ensure that the user's are published
		$validUsers 	= isset( $config[ 'validUser' ] ) ? $config[ 'validUser' ] : null;

		// Determines if we should display admin's on this list.
		$includeAdmin 	= isset( $config[ 'includeAdmin' ] ) ? $config[ 'includeAdmin' ] : null;

		$excludeIds = array();

		if ($my->id) {
			$excludeIds[] = $my->id;
		}

		// If caller doesn't want to include admin, we need to set the ignore list.
		if ($includeAdmin === false) {
			// Get a list of site administrators from the site.
			$userModel = FD::model('Users');
			$admins 	= $userModel->getSiteAdmins();

			if ($admins) {
				foreach ($admins as $admin) {
					$excludeIds[] 	= $admin->id;
				}
			}
		}


		$query[]	= 'SELECT a.* , COUNT(b.' . $db->nameQuote( 'id' ) . ') AS ' . $db->nameQuote( 'count' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_profiles' ) . ' AS a';
		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_profiles_maps' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'profile_id' );

		if ($excludeIds) {
			$query[] = ' AND b.' . $db->nameQuote('user_id') . ' NOT IN (' . implode(',', $excludeIds) . ')';
		}

		if( $validUsers )
		{
			$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__users' ) . ' AS c';
			$query[]	= 'ON c.' . $db->nameQuote( 'id' ) . '= b.' . $db->nameQuote( 'user_id' );
			$query[]	= 'AND c.' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );
		}

		$query[]	= 'WHERE 1';

		// Need to filter by state.
		if( isset( $config[ 'state' ] ) )
		{
			$state 	= (int) $config[ 'state' ];

			$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( $state );
		}


		// Need to filter by registration flag.
		if( isset( $config[ 'registration' ] ) )
		{
			$registration 	= (int) $config[ 'registration' ];

			$query[]	= 'AND a.' . $db->nameQuote( 'registration' ) . '=' . $db->Quote( $registration );
		}


		// Only show which profile have the community access permission
		// if enable this allow admin view ESAD profile user and that user is superadmin then only can view.
		if (!(FD::config()->get('users.listings.esadadmin') && $my->isSiteAdmin()) && isset($config['excludeESAD']) && $config['excludeESAD']) {
			$query[] = ' AND a.' . $db->nameQuote('community_access') . ' = 1';
		}

		// Group results up since we joined with profile maps
		$query[]	= 'GROUP BY a.' . $db->nameQuote( 'id' );

		// Specify the ordering.
		if( isset( $config[ 'ordering' ] ) )
		{
			$ordering	= $config[ 'ordering' ];
			$query[]	= 'ORDER BY a.' . $db->nameQuote( $ordering ) . ' ASC';
		}
		else
		{
			$query[]	= 'ORDER BY a.' . $db->nameQuote( 'ordering' ) . ' ASC';
		}


		// Glue the query up.
		$query 		= implode( ' ' , $query );

		// Debug
		// echo str_ireplace( '#__' , 'jos_' , $query );
		// exit;

		// Determine wheter or not to use pagination
		$paginate 	= isset( $config[ 'limit' ] ) ? $config[ 'limit' ] : SOCIAL_PAGINATION_ENABLE;
		$paginate	= $paginate == SOCIAL_PAGINATION_NO_LIMIT ? false : SOCIAL_PAGINATION_ENABLE;

		$result		= $this->getData( $query , $paginate );
		$profiles   = array();

		foreach( $result as $row )
		{
			$profile    = FD::table( 'Profile' );
			$profile->bind( $row );

			// Assign temporary data.
			$profile->totalUsers 	= $row->count;

			// Set the profile object back.
			$profiles[]	= $profile;
		}

		return $profiles;
	}

	/**
	 * Retrieve the total number of users in this profile type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	int		The total.
	 */
	public function getMembersCount( $profileId, $publishedOnly = true, $excludeBlocked = false )
	{
		$config = FD::config();
		$db = FD::db();
		$query = array();

		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__social_profiles_maps' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'id' ) . ' = a.' . $db->nameQuote( 'user_id' );

		if ($config->get('users.blocking.enabled') && $excludeBlocked && !JFactory::getUser()->guest) {
		    // user block
		    $query[] = ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query[] = ' ON b.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query[] = ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query[]	= 'WHERE a.' . $db->nameQuote( 'profile_id' ) . '=' . $db->Quote( $profileId );

		if ($publishedOnly) {
			$query[]	= 'AND b.' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );
		}

		// Determines if we should display admin's on this list.
		$includeAdmin 	= $config->get( 'users.listings.admin' ) ? true : false;

		$mainframe 	= JFactory::getApplication();
		if ( $mainframe->isAdmin() ) {
			$includeAdmin = true;
		}

		// If caller doesn't want to include admin, we need to set the ignore list.
		if( $includeAdmin === false )
		{
			// Get a list of site administrators from the site.
			$userModel = FD::model('Users');
			$admins 	= $userModel->getSiteAdmins();

			if( $admins )
			{
				$ids	= array();

				foreach( $admins as $admin )
				{
					$ids[] 	= $admin->id;
				}

				$query[] = ' AND b.' . $db->nameQuote('id') . ' NOT IN (' . implode(',', $ids) . ')';
			}
		}

		if ($config->get('users.blocking.enabled') && $excludeBlocked && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query[] = ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	/*
	 * Retreive custom field groups based on a specific step.
	 *
	 * @param   int     $stepId     The step id.
	 */
	public function getFieldsGroups( $stepId , $type = 'profiletype' )
	{
		$db		= FD::db();

		$query  = 'SELECT a.* '
				. 'FROM ' . $db->nameQuote( '#__social_fields_groups' ) . ' AS a '
				. 'WHERE a.' . $db->nameQuote( 'steps_id' ) . ' = ' . $db->Quote( $stepId ) . ' '
				. 'AND a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $query );

		$result		= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$groups = array();

		foreach( $result as $row )
		{
			$group  = FD::table( 'FieldGroup' );
			$group->bind( $row );
			$groups[]   = $group;
		}
		return $groups;
	}

	/**
	 * Determines if a profile has a specific custom field type
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasCustomFieldType( $profileId , $type )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_fields' , 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->join( '#__social_fields_steps' , 'b' );
		$sql->on( 'b.id' , 'a.step_id' );
		$sql->join( '#__social_apps' , 'c' );
		$sql->on( 'c.id' , 'a.app_id' );
		$sql->where( 'b.uid' , $profileId );
		$sql->where( 'b.type' , 'profiles' );
		$sql->where( 'c.element' , $type );
		$sql->where( 'c.state' , SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $sql );

		$exists 	= $db->loadResult() > 0;

		return $exists;
	}

	public function getFields( &$groups , $filters = array() )
	{
		$db     = FD::db();

		foreach( $groups as $group )
		{
			$query  = 'SELECT a.*,b.title AS addon_title , b.element AS addon_element FROM ' . $db->nameQuote( '#__social_fields' ) . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote( '#__social_apps' ) . ' AS b '
					. 'ON b.id=a.field_id '
					. 'WHERE a.`group_id`=' . $db->Quote( $group->id );

			if( $filters )
			{
				$subquery     = array();

				foreach( $filters as $key => $value )
				{
					$subquery[]		= 'a.' . $db->nameQuote( $key ) . '=' . $db->Quote( $value );
				}

				$query .= ' ' . count( $subquery ) == 1 ? ' AND ' . $subquery[ 0 ] : implode( ' AND ' , $subquery );
			}
			$db->setQuery( $query );

			$fields	= $db->loadObjectList();
			$group->childs  = array();

			foreach( $fields as $field )
			{
				$table      = FD::table( 'Field' );
				$table->bind( $field );
				$table->addon_title = $field->addon_title;

				$group->childs[]    = $table;
			}
		}

		return $groups;
	}

	/**
	 * Creates the necessary core fields required in order for the system to work.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique profile id.
	 * @return	bool	True if success and false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function createDefaultFields( $stepId )
	{
		// Load apps model
		$model 		= FD::model( 'Apps' );

		// Get a list of core and default apps
		$apps 		= $model->getDefaultApps( array( 'type' => SOCIAL_APPS_TYPE_FIELDS ) );

		// Get default data from the manifest files.
		$lib 		= FD::fields();
		$fields 	= $lib->getCoreManifest( SOCIAL_FIELDS_GROUP_USER , $apps );

		// Only get fields that doesn't exist for the profile type.
		if( !$fields )
		{
			return false;
		}

		foreach( $fields as $row )
		{
			$field		= FD::table( 'Field' );

			// Set the current profile's id.
			$field->bind( $row );

			// If there is a params set in the defaults.json, we need to decode it back to a string.
			if( $row->params && is_object( $row->params ) )
			{
				$field->params 	= FD::json()->encode( $row->params );
			}

			// Set the core identifier
			$field->core 	= SOCIAL_STATE_PUBLISHED;

			// Set the step id this field belongs to.
			$field->step_id = $stepId;

			// Let's try to store the custom field now.
			$field->store();
		}

		return true;
	}

	/**
	 * Retrieves a list of core fields from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The profile id
	 * @return	Array	An array of SocialTableField
	 */
	public function getCoreFields( $profileId )
	{
		$db     = FD::db();

		$query  = 'SELECT a.*, b.title AS addon_title '
				. 'FROM ' . $db->nameQuote( '#__social_fields' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__social_apps' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'app_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE b.' . $db->nameQuote( 'core' ) . ' = ' . $db->Quote( 1 );

		// @rule: We already know before hand which elements are the core fields for the profile types.
		$elements   = array( $db->Quote( 'joomla_username' ) , $db->Quote( 'joomla_fullname' ) , $db->Quote( 'joomla_email' ) ,
							$db->Quote( 'joomla_password' ), $db->Quote( 'joomla_timezone' ) , $db->Quote('joomla_user_editor' ) , $db->Quote( 'joomla_password2' ) );

		$query  .= ' AND b.' . $db->nameQuote( 'element' ) . ' IN(' . implode( ',' , $elements ) . ')';

		$db->setQuery( $query );

		$result		= $db->loadObjectList();
		$fields     = array();

		foreach( $result as $row )
		{
			$field      = FD::table( 'Field' );
			$field->bind( $row );
			$field->set( 'addon_title' , $row->addon_title );

			// Manually push in profile_id
			$field->profile_id = $profileId;
			$fields[]   = $field;
		}
		return $fields;
	}


	/**
	 * Retrieves the past 7 days statistics for new sign ups.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	Array
	 */
	public function getRegistrationStats($profileId = null)
	{
		$db			= FD::db();
		$dates 		= array();

		// Get the past 7 days
		$curDate 	= FD::date();
		for( $i = 0 ; $i < 7; $i++ )
		{
			$obj = new stdClass();

			if( $i == 0 )
			{
				$dates[]		= $curDate->toMySQL();
			}
			else
			{
				$unixdate 		= $curDate->toUnix();
				$new_unixdate 	= $unixdate - ( $i * 86400);
				$newdate  		= FD::date( $new_unixdate );

				$dates[] 	= $newdate->toMySQL();
			}
		}

		// Reverse the dates
		$dates 			= array_reverse( $dates );

		$result 		= new stdClass();
		$result->dates	= $dates;

		$profiles 	= array();


		foreach( $dates as $date )
		{
			// Registration date should be Y, n, j
			$date	= FD::date( $date )->format( 'Y-m-d' );

			$query = 'select a.' . $db->nameQuote( 'id' ) . ', a.' . $db->nameQuote( 'title' ) . ', count( b.' . $db->nameQuote( 'id' ) . ' ) as cnt';
			$query .= ' from ' . $db->nameQuote( '#__social_profiles' ) . ' as a';
			$query .= '	left join ' . $db->nameQuote( '#__social_profiles_maps' ) . ' as b';
			$query .= '		on a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'profile_id' );
			$query .= '		and date_format( b.' . $db->nameQuote( 'created' ) . ', GET_FORMAT( DATE,' . $db->Quote( 'ISO' ) . ') ) = ' . $db->Quote( $date );

			if ($profileId) {
				$query .= ' WHERE a.' . $db->quoteName('id') . '=' . $db->Quote($profileId);
			}

			$query .= ' group by a.' . $db->nameQuote( 'id' );


			$db->setQuery( $query );

			$items				= $db->loadObjectList();

			foreach( $items as $item )
			{
				if( !isset( $profiles[ $item->id ] ) )
				{
					$profiles[ $item->id ]	= new stdClass();
					$profiles[ $item->id ]->title 	= $item->title;

					$profiles[ $item->id ]->items 	= array();
				}

				if( $item->cnt )
				{
					// $item->cnt	+= 10;
				}
				$profiles[ $item->id ]->items[]	= $item->cnt;
			}
		}

		// Reset the index.
		$profiles 	= array_values( $profiles );

		$result->profiles 	= $profiles;

		return $result;
	}

	/**
	 * Check if the profile alias exists
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $alias   Alias to check
	 * @param  Int       $exclude The id of the profile to exclude from the checking
	 * @return Boolean            State of existance
	 */
	public function aliasExists($alias, $exclude = null)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_profiles');
		$sql->where('alias', $alias);

		if (!empty($exclude))
		{
			$sql->where('id', $exclude, '!=');
		}

		$db->setQuery($sql->getTotalSql());

		$result = $db->loadResult();

		return !empty($result);
	}

	/**
	 * Gets all the profile row without state
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  array     $filters Column and value pairs of what to filter from the table.
	 * @return array              Array of SocialTableProfile objects.
	 */
	public function getAllProfiles($filters = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_profiles');

		foreach ($filters as $key => $val) {
			$sql->where($key, $val);
		}

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		$profiles = array();

		foreach ($result as $row) {
			$table = FD::table('profile');
			$table->bind($row);

			$profiles[] = $table;
		}

		return $profiles;
	}
}
