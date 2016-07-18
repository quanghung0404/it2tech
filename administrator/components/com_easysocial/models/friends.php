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

/**
 * Object mapping for lists.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class EasySocialModelFriends extends EasySocialModel
{
	private $data			= null;

	public function __construct()
	{
		parent::__construct( 'friends' );
	}

	public function getSuggestedFriends( $userId = null, $limit = '0', $countOnly = false )
	{
		$config = FD::config();
		$db = FD::db();
		$sql = $db->sql();

		$user 	= FD::user( $userId );
		$result = array();
		$total  = 0;

		// retrieve friends of friends, who isn't your friend yet.

		$query = "select `tfid` as `ffriend_id`, count(`score`) as `score` from (";
		$query .= " select f1.`target_id` as `tfid`, f1.`actor_id` as `score` from (";
		$query .= "	select a.`actor_id` as `mfid` from `#__social_friends` as a where a.`target_id` = $user->id and a.`state` = " . SOCIAL_FRIENDS_STATE_FRIENDS;
		$query .= "	union ";
		$query .= "	select a1.`target_id` as `mfid` from #__social_friends as a1 where a1.`actor_id` = $user->id and a1.`state` = " . SOCIAL_FRIENDS_STATE_FRIENDS;
		$query .= " ) as fm";
		$query .= "	inner join #__social_friends as f1 on fm.mfid = f1.actor_id and f1.state = " . SOCIAL_FRIENDS_STATE_FRIENDS;
		$query .= "	where f1.target_id not in (	select a.actor_id as `mfid` from #__social_friends as a where a.target_id = $user->id and a.state != " . SOCIAL_FRIENDS_STATE_REJECTED;
		$query .= "	union";
		$query .= "	select a1.target_id as `mfid` from #__social_friends as a1 where a1.actor_id = $user->id and a1.state != " . SOCIAL_FRIENDS_STATE_REJECTED . ")";
		$query .= "	and f1.target_id != $user->id";
		$query .= " union";
		$query .= " select f2.actor_id as `tfid`, f2.target_id as score from (";
		$query .= "	select b.actor_id as `mfid` from #__social_friends as b where b.target_id = $user->id and b.state = " . SOCIAL_FRIENDS_STATE_FRIENDS;
		$query .= "	union";
		$query .= "	select b1.target_id as `mfid` from #__social_friends as b1 where b1.actor_id = $user->id and b1.state = " . SOCIAL_FRIENDS_STATE_FRIENDS;
		$query .= " ) as fm2";
		$query .= "	inner join #__social_friends as f2 on fm2.mfid = f2.target_id and f2.state = " . SOCIAL_FRIENDS_STATE_FRIENDS;
		$query .= "	where f2.actor_id not in (	select a.actor_id as `mfid` from #__social_friends as a where a.target_id = $user->id and a.state != " . SOCIAL_FRIENDS_STATE_REJECTED;
		$query .= "	union";
		$query .= "	select a1.target_id as `mfid` from #__social_friends as a1 where a1.actor_id = $user->id and a1.state != " . SOCIAL_FRIENDS_STATE_REJECTED . ")";
		$query .= "	and f2.actor_id != $user->id";
		$query .= ") as x";
		$query .= " INNER JOIN `#__users` AS uu ON uu.`id` = x.`tfid` AND uu.`block` = '0'";

		// exclude esad users
		$query .= " INNER JOIN `#__social_profiles_maps` as upm on uu.`id` = upm.`user_id`";
		$query .= " INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1";


		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			// user block
			$query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
			$query .= ' ON uu.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
			$query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
			$query .= ' WHERE bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		$query .= " group by x.tfid";
		$query .= " order by score desc";

		$sql->raw($query);

		if( !empty( $limit ) && $limit > 0 )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getSQL() , true );
			$total = $this->getTotal();

			// Get the list of users
			$result 	= $this->getData( $sql->getSQL() );
		}
		else
		{
			$db->setQuery( $sql->getSQL() );
			$result 	= $db->loadObjectList();

			$total = count( $result );
		}

		$runTrigger = true;
		if( $limit && $total >= $limit )
		{
			$runTrigger = false;
		}

		// now we trigger custom fields to search users which has the similar
		// data.
		$fieldsLib		= FD::fields();
		$fieldModel  	= FD::model( 'Fields' );
		$fieldsResult 	= array();


		if( $runTrigger )
		{

			$fieldSQL = 'select a.*, b.' . $db->nameQuote( 'type' ) . ', b.' . $db->nameQuote( 'element' ) . ', b.' . $db->nameQuote( 'group' );
			$fieldSQL .= ', c.' . $db->nameQuote( 'uid' ) . ' as ' . $db->nameQuote( 'profile_id' );
			$fieldSQL .= ' FROM ' . $db->nameQuote( '#__social_fields' ) . ' as a';
			$fieldSQL .= ' INNER JOIN ' . $db->nameQuote( '#__social_apps' ) . ' as b';
			$fieldSQL .= ' ON a.app_id = b.id';
			$fieldSQL .= ' LEFT JOIN ' . $db->nameQuote( '#__social_fields_steps' ) . ' as c';
			$fieldSQL .= ' ON a.step_id = c.id';
			$fieldSQL .= ' where a.' . $db->nameQuote( 'friend_suggest' ) . ' = ' . $db->Quote( '1' );
			$db->setQuery( $fieldSQL );

			$fields = $db->loadObjectList();
			if( count( $fields ) > 0 )
			{
				foreach( $fields as $item )
				{

					$field 	= FD::table( 'Field' );
					$field->bind( $item );

					$field->profile_id 	= $item->profile_id;
					$field->data 		= isset( $item->data ) ? $item->data : '';

					$userFieldData = $fieldModel->getCustomFieldsValue( $field->id, $user->id, SOCIAL_FIELDS_GROUP_USER );

					$args 			= array( $user, $userFieldData );
					$f 				= array( &$field );

					$dataResult 	= $fieldsLib->trigger( 'onFriendSuggestSearch' , SOCIAL_FIELDS_GROUP_USER , $f , $args );
					$fieldsResult 	= array_merge( $fieldsResult, $dataResult );
				}
			}

		}

		$tmpResult = array_merge( $result, $fieldsResult );

		//reset $result
		$result = array();

		foreach( $tmpResult as $tmpItem )
		{
			if(! array_key_exists( $tmpItem->ffriend_id , $result ) )
			{
				$result[ $tmpItem->ffriend_id ] = $tmpItem;
			}
		}


		if( $countOnly )
		{
			return count( $result );
		}



		$friends 	= array();

		if( $result )
		{
			//preload users.
			$tmp = array();
			foreach( $result as $item )
			{
				$tmp[] = $item->ffriend_id;
			}

			FD::user( $tmp );
			FD::cache()->cacheUsersPrivacy($tmp);

			// getting the result.
			foreach( $result as $item )
			{
				$obj = new stdClass();

				$obj->friend = FD::user( $item->ffriend_id );
				$obj->count  = $item->score;

				$friends[] = $obj;
			}
		}

		return $friends;
	}

	public function arrayObjectUnique( $array, $keep_key_assoc = false)
	{
	    $duplicate_keys = array();
	    $tmp         = array();

	    foreach ($array as $key=>$val)
	    {
	        // convert objects to arrays, in_array() does not support objects
	        if (is_object($val))
	            $val = (array)$val;

	        if (!in_array($val, $tmp))
	            $tmp[] = $val;
	        else
	            $duplicate_keys[] = $key;
	    }

	    foreach ($duplicate_keys as $key)
	        unset($array[$key]);

	    return $keep_key_assoc ? $array : array_values($array);
	}

	/**
	 * Get mutuals user ids.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The source user id.
	 * @param	int		The target user id.
	 * @return	array   user id.
	 */
	public function getMutualFriends( $source, $target, $limit = 0 )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select `fid` as `afriend` from';
		$query .= $this->buildMutualFriendQueryTableAlias( $source, $target );

		// echo $query;exit;

		$rows = '';
		if( $limit )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $query , true );

			// Get the list of users
			$rows 	= $this->getData( $query );
		}
		else
		{
			$db->setQuery( $query );
			$rows 	= $db->loadObjectList();
		}


		$friends	= array();
		if( $rows )
		{
			$tmpIds = array();
			foreach( $rows as $row )
			{
				$tmpIds[] = $row->afriend;
			}

			// preload user
			FD::user( $tmpIds );

			foreach( $rows as $row )
			{
				$friends[]	= FD::user( $row->afriend );
			}
		}

		return $friends;
	}

	public function getMutualFriendCount( $source, $target )
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'select count(1) from';
		$query .= $this->buildMutualFriendQueryTableAlias( $source, $target );

		$sql->raw( $query );

		$db->setQuery( $sql );

		$result = $db->loadResult();

		return ( empty($result) ) ? '0' : $result;
	}


	private function buildMutualFriendQueryTableAlias( $source, $target )
	{
		$db = FD::db();

		// $query = '	(select if(a.`actor_id` = ' . $db->Quote( $source ) . ', a.`target_id`, a.`actor_id` ) as `afriend`';
		// $query .= '		from `#__social_friends` as a where ( (a.`actor_id` = ' . $db->Quote( $source ) . ' and a.`state` = 1) OR (a.`target_id` = ' . $db->Quote( $source ) . 'and a.`state` = 1) ) ) as z';
		// $query .= ' inner join ';
		// $query .= '	(select if( a.`actor_id` = ' . $db->Quote( $target ) . ', a.`target_id`, a.`actor_id` ) as `afriend`';
		// $query .= ' 	from `#__social_friends` as a where ( (a.`actor_id` = ' . $db->Quote( $target ) . ' and a.`state` = 1) OR ( a.`target_id` = ' . $db->Quote( $target ) . ' and a.`state` = 1 ) ) ) as x';
		// $query .= ' on z.`afriend` = x.`afriend`';
		// $query .= ' inner join `#__users` u on z.`afriend` = u.`id` and u.`block` = ' . $db->Quote( '0' );


		$query = " (select af1.`actor_id` as `fid` from `#__social_friends` as af1 where af1.`target_id` = $source and af1.`state` = 1";
		$query .= "		union ";
		$query .= "	select af2.`target_id` as `fid`  from `#__social_friends` as af2 where af2.`actor_id` = $source and af2.`state` = 1";
		$query .= " ) as x";
		$query .= " inner join `#__users` as u on x.`fid` = u.`id` and u.`block` = 0";

		// exclude esad users
		$query .= " INNER JOIN `#__social_profiles_maps` as upm on u.`id` = upm.`user_id`";
		$query .= " INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1";

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query .= ' ON u.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query .= " where exists (";
		$query .= "		select bf1.`actor_id` from `#__social_friends` as bf1 where bf1.`target_id` = $target and bf1.`actor_id` = x.`fid` and bf1.`state` = 1";
		$query .= " 		union ";
		$query .= "		select bf2.`target_id` from `#__social_friends` as bf2 where bf2.`actor_id` = $target and bf2.`target_id` = x.`fid`  and bf2.`state` = 1";
		$query .= ")";


		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		//debug code;
		//$query = ' (select id as afriend from #__users) as z';

		return $query;
	}



	/**
	 * Determines if the target is a friends of friend with the source.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The target user id.
	 * @param	int		The source user id.
	 * @return	bool	True if is a 2nd level friend.
	 */
	public function isFriendsOfFriends( $target , $source )
	{
		$db 	= FD::db();

		$query		= array();

		$query[]	= 'SELECT b.' . $db->nameQuote( 'target_id' ) . ' AS ' . $db->nameQuote( 'id' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_friends' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_friends' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'target_id' ) . ' = b.' . $db->nameQuote( 'actor_id' );
		$query[]	= 'AND a.' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $target );
		$query[]	= 'AND b.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $source );
		$query[]	= 'UNION';

		$query[]	= 'SELECT b.' . $db->nameQuote( 'actor_id' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_friends' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_friends' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'actor_id' ) . ' = b.' . $db->nameQuote( 'target_id' );
		$query[]	= 'AND a.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $target );
		$query[]	= 'AND b.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $source );

		$query[]	= 'UNION';
		$query[]	= 'SELECT ' . $db->nameQuote( 'id' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_friends' );
		$query[]	= 'WHERE(';
		$query[]	= $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $target );
		$query[]	= 'AND ' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $source );
		$query[]	= 'OR ' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $source );
		$query[]	= 'AND ' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $target );
		$query[]	= ')';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return !empty( $result );
	}

	/**
	 * Determines if the provided id's are friends.
	 *
	 * @since	1.0
	 * @access	public
	 * @param 	int 	$source		The source user id.
	 * @param	int 	$target		The target user id.
	 * @return	boolean				True if they are friends, false otherwise.
	 */
	public function isFriends( $source , $target , $state = SOCIAL_FRIENDS_STATE_FRIENDS )
	{
		$db 		= FD::db();

		$query 		= array();

		$query[]	= 'SELECT COUNT(1)';
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_friends' );
		$query[]	= 'WHERE';
		$query[]	= '( ' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $source ) . ' AND ' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $target ) . ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( $state ) . ' )';
		$query[]	= 'OR';
		$query[]	= '( ' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $target ) . ' AND ' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $source ) . ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( $state ) . ')';

		// Debug
		// $query 	= implode( ' ' , $query );
		// echo str_ireplace( '#__' , 'jos_' , $query );exit;

		// Glue back query.
		$db->setQuery( $query );

		$result 	= (bool) $db->loadResult();

		return $result;
	}

	/**
	 * Determines if the provided id's are friends.
	 *
	 * @since	1.0
	 * @access	public
	 * @param 	int 	$source		The source user id.
	 * @param	int 	$target		The target user id.
	 * @return	boolean				True if they are friends, false otherwise.
	 */
	// public function getState( $source , $target )
	// {
	// 	$db 	= FD::db();

	// 	$query 		= array();

	// 	$query[]	= 'SELECT ' . $db->nameQuote( 'state' );
	// 	$query[]	= 'FROM ' . $db->nameQuote( '#__social_friends' );
	// 	$query[]	= 'WHERE';
	// 	$query[]	= '( ' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $source ) . ' OR ' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( $target ) . ' )';
	// 	$query[]	= 'AND';
	// 	$query[]	= '( ' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $target ) . ' OR ' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $source ) . ')';

	// 	$db->setQuery( $query );
	// 	$state 		= $db->loadResult();

	// 	return $state;
	// }

	/**
	 * Determines if there is an existing request from source user to targeted user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param 	int 	$source		The source user id.
	 * @param	int 	$target		The target user id.
	 * @return	boolean				True if they are friends, false otherwise.
	 */
	public function isPendingFriends( $source , $target )
	{
		$db 	= FD::db();

		$query 	= 'SELECT COUNT(1)';
		$query	.= ' FROM ' . $db->nameQuote( '#__social_friends' );
		$query	.= ' WHERE';
		$query 	.= ' (' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $source ) . ' OR ' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( $target ) . ' )';
		$query 	.= ' AND';
		$query	.= ' (' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $target ) . ' OR ' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $source ) . ' )';
		$query	.= ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_PENDING );

		$db->setQuery( $query );
		$result 	= (bool) $db->loadResult();

		return $result;
	}

	/**
	 * Get a list of online friends
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOnlineFriends( $id )
	{
		$config = FD::config();
		$db 	= FD::db();
		$query 	= array();

		$query[]	= 'SELECT * FROM(';

		$query[]	= 'SELECT a.*, IF(a.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $id ) . ', a.' . $db->nameQuote( 'actor_id' ) . ', a.' . $db->nameQuote( 'target_id' ) . ') as ' . $db->nameQuote( 'friendid' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_friends' ) . ' AS a';

		$query[] 	= 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS uu';
		$query[] 	= 'ON uu.' . $db->nameQuote( 'id' ) . ' = if( a.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $id ) . ', a.' . $db->nameQuote( 'actor_id' ) . ', a.' . $db->nameQuote( 'target_id' ) . ')';
		$query[] 	= 'AND uu.' . $db->nameQuote( 'block' ) . ' = ' . $db->Quote( '0' );

		// exclude esad users
		$query[] = 'INNER JOIN `#__social_profiles_maps` as upm on uu.`id` = upm.`user_id`';
		$query[] = 'INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query[] = ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query[] = ' ON uu.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query[] = ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query[]	= 'WHERE (';
		$query[]	= 'a.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $id ) . ' OR a.' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $id );
		$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS );
		$query[]	= ')';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			// user block continue here
 			$query[] = ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		$query[]	= ') AS ' . $db->nameQuote( 'onlinefriend' );
		$query[]	= 'WHERE EXISTS( SELECT ' . $db->nameQuote( 'userid' ) . ' FROM ' . $db->nameQuote( '#__session' ) . ' AS s WHERE s.' . $db->nameQuote( 'userid' ) . '= onlinefriend.' . $db->nameQuote( 'friendid' ) . ')';

		$query		= implode( ' ' , $query );

		// echo $query;exit;

		$db->setQuery( $query );

		$rows	= $db->loadObjectList();

		if( !$rows )
		{
			return false;
		}

		$friends	= array();

		foreach( $rows as $row )
		{
			if( $row->actor_id != $id )
			{
				$friends[]	= FD::user( $row->actor_id );
			}

			if( $row->target_id != $id )
			{
				$friends[]	= FD::user( $row->target_id );
			}
		}

		return $friends;
	}

	/**
	 * Retrieves a list of friends
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The user's id
	 * @param	Array	An array of options. state - SOCIAL_FRIENDS_STATE_PENDING or SOCIAL_FRIENDS_STATE_FRIENDS
	 *
	 * @return	Array
	 */
	public function getFriends($id , $options = array())
	{
		$config = FD::config();

		$db = FD::db();
		$sql = $db->sql();

		$query[] = 'SELECT a.*, if( a.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $id ) . ', a.' . $db->nameQuote( 'actor_id' ) . ', a.' . $db->nameQuote( 'target_id' ) . ') AS friendid';
		$query[] = 'FROM ' . $db->nameQuote( '#__social_friends' ) . ' AS a';

		$query[] = 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS uu';
		$query[] = 'ON uu.' . $db->nameQuote( 'id' ) . ' = if( a.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $id ) . ', a.' . $db->nameQuote( 'actor_id' ) . ', a.' . $db->nameQuote( 'target_id' ) . ')';

		// exclude esad users
		$query[] = 'INNER JOIN `#__social_profiles_maps` as upm on uu.`id` = upm.`user_id`';
		$query[] = 'INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query[] = ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query[] = ' ON uu.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query[] = ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		// Check if the caller wants to filter friends by list.
		$listId 	= isset( $options[ 'list_id' ] ) ? $options[ 'list_id' ] : null;

		if (!is_null($listId)) {
			$query[] = 'INNER JOIN ' . $db->nameQuote( '#__social_lists_maps' ) . ' AS b';
			$query[] = 'ON (';
			$query[] = 'a.' . $db->nameQuote( 'target_id' ) . '= b.' . $db->nameQuote( 'target_id' );
			$query[] = 'OR';
			$query[] = 'a.' . $db->nameQuote( 'actor_id' ) . '= b.' . $db->nameQuote( 'target_id' );
			$query[] = ')';
			$query[] = 'AND b.' . $db->nameQuote( 'target_type' ) . '=' . $db->Quote( SOCIAL_TYPE_USER );
			$query[] = 'AND b.' . $db->nameQuote( 'list_id' ) . '=' . $db->Quote( $listId );
		}


		$query[] = 'WHERE uu.' . $db->nameQuote( 'block' ) . ' = ' . $db->Quote( '0' );

		// user block continue here
		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {

		    $query[] = 'AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		// Check if state is passed in.
		$state = isset( $options[ 'state' ] ) ? $options[ 'state' ] : SOCIAL_FRIENDS_STATE_FRIENDS;
		$isRequest = isset( $options[ 'isRequest' ] ) ? $options[ 'isRequest' ] : false;
		$limit = isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : false;

		// Add filtering by state.
		if( !is_null( $state ) && $state == SOCIAL_FRIENDS_STATE_PENDING && !$isRequest )
		{
			$query[]	= 'AND a.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $id );
			$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_PENDING ) ;
		}
		else if( !is_null( $state ) && $state == SOCIAL_FRIENDS_STATE_PENDING && $isRequest )
		{
			$query[]	= 'AND a.' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $id );
			$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_PENDING );
		}
		else
		{
			$query[]	= 'AND';
			$query[]	= '(a.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $id );
			$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS ) . ')';
			$query[]	= 'OR';
			$query[]	= '(a.' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $id );
			$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS ) . ')';
		}

		// Glue back query.
		$query 	= implode( ' ' , $query );

		// echo $query;exit;


		if ($limit != 0) {
			$this->setState('limit', $limit);

			// Get the limitstart.
			$limitstart = $this->getUserStateFromRequest('limitstart', 0);
			$limitstart = ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState('limitstart', $limitstart);

			// Set the total number of items.
			$this->setTotal($query, true);

			// Get the list of users
			$rows = $this->getData($query);
		} else {
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}

		if (!$rows) {
			return false;
		}

		$friends	= array();

		$idONLY 	= isset( $options[ 'idonly' ] ) ? true : false;

		foreach( $rows as $row )
		{
			if( $row->actor_id != $id )
			{
				$friends[]	= ( $idONLY ) ? $row->actor_id : FD::user( $row->actor_id );
			}

			if( $row->target_id != $id )
			{
				$friends[]	= ( $idONLY ) ? $row->target_id : FD::user( $row->target_id );
			}
		}

		return $friends;
	}

	/**
	 * Retrieves a list of friends that are in pending approval state.
	 *
	 * Example:
	 *
	 * <code>
	 * <?php
	 * $my 		= FD::user();
	 * $model 	= FD::model( 'Friends' );
	 *
	 * // Returns a list of friends that are pending my approval.
	 * $model->getPendingRequests( $my->id );
	 * ?>
	 * </code>
	 *
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 		The user's id
	 *
	 * @return	Array
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getPendingRequests( $id )
	{
		$db			= FD::db();

		$query 		= array();
		$query[]	= 'SELECT * FROM ' . $db->nameQuote( '#__social_friends' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $id );
		$query[]	= 'AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_PENDING );

		// Glue query back.
		$query 		= implode( ' ' , $query );

		// Get the total number of records before applying any pagination.
		$this->total		= $this->getTotal( $query );

		$db->setQuery( $query );

		$rows		= $db->loadObjectList();

		if( !$rows )
		{
			return false;
		}

		$friends	= array();

		foreach( $rows as $row )
		{
			$friend		= FD::table( 'Friend' );
			$friend->bind( $row );

			$friends[]	= $friend;
		}

		return $friends;
	}

	/**
	 * Retrieves total number of friends a user has.
	 *
	 * @access	public
	 * @param	Array	$options An array of options.
	 *
	 * @return	int		Total friends count.
	 **/
	public function getFriendsCount( $userId )
	{
		$db			= FD::db();

		$query		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__social_friends' );
		$query[]	= 'WHERE';
		$query[]	= '( (';
		$query[]	= $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $userId );
		$query[]	= 'AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS );
		$query[]	= ') OR (';
		$query[]	= $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $userId );
		$query[]	= 'AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS );
		$query[]	= ') )';

		// Glue back the query.
		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );

		$total 		= $db->loadResult();

		return $total;
	}

	/**
	 * Allows caller to make a friend request from source to target
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function request($sourceId, $targetId, $state = SOCIAL_FRIENDS_STATE_PENDING)
	{
		// Do not allow user to create a friend request to himself
		if ($sourceId == $targetId) {
			$this->setError(JText::_('COM_EASYSOCIAL_FRIENDS_UNABLE_TO_ADD_YOURSELF'));

			return false;
		}

		// If they are already friends, ignore this.
		if ($this->isFriends($sourceId, $targetId)) {
			$this->setError(JText::_('COM_EASYSOCIAL_FRIENDS_ERROR_ALREADY_FRIENDS'));
			return false;
		}

		// Check if user has already previously requested this.
		if ($this->isFriends($sourceId, $targetId, SOCIAL_FRIENDS_STATE_PENDING)) {
			$this->setError(JText::_('COM_EASYSOCIAL_FRIENDS_ERROR_ALREADY_REQUESTED'));
			return false;
		}

		// If everything is okay, we proceed to add this request to the friend table.
		$table = FD::table('Friend');
		$table->setActorId($sourceId);
		$table->setTargetId($targetId);
		$table->setState($state);

		// Save the request
		$state 	= $table->store();

		$my   = FD::user($sourceId);
		$user = FD::user($targetId);

		// Prepare the dispatcher
		FD::apps()->load(SOCIAL_TYPE_USER);
		$dispatcher	= FD::dispatcher();
		$args 		= array(&$table, $my, $user);

		// @trigger: onFriendRequest
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onFriendRequest', $args);


		// Send notification to the target when a user requests to be his / her friend.
		$params 	= array(
								'requesterId'		=> $my->id,
								'requesterAvatar'	=> $my->getAvatar( SOCIAL_AVATAR_LARGE ),
								'requesterName'		=> $my->getName(),
								'requesterLink'		=> $my->getPermalink( true, true ),
								'requestDate'		=> FD::date()->toMySQL(),
								'totalFriends'		=> $my->getTotalFriends(),
								'totalMutualFriends'=> $my->getTotalMutualFriends($user->id)
							);
		// Email template
		$emailOptions 		= array(
									'actor'		=> $my->getName(),
									'title'		=> 'COM_EASYSOCIAL_EMAILS_FRIENDS_NEW_REQUEST_SUBJECT',
									'template'	=> 'site/friends/request',
									'params'	=> $params
							);


		FD::notify('friends.request', array($user->id), $emailOptions, false);

		// @badge: friends.create
		// Assign badge for the person that initiated the friend request.
		$badge 	= FD::badges();
		$badge->log('com_easysocial', 'friends.create', $my->id, JText::_('COM_EASYSOCIAL_FRIENDS_BADGE_REQUEST_TO_BE_FRIEND'));

		return $table;
	}

	/**
	 * Returns the total number of friend requests a user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 		The user's id.
	 *
	 * @return	int		The total number of requests.
	 */
	public function getTotalRequests( $id )
	{
		$config = FD::config();
		$db 	= FD::db();

		$query 	= 'SELECT COUNT(1) FROM';
		$query	.= ' ' . $db->nameQuote( '#__social_friends' ) . ' as a ';

		$query	.= ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS uu';
		$query 	.= ' ON uu.' . $db->nameQuote( 'id' ) . ' = a.' . $db->nameQuote( 'actor_id' );

		// exclude esad users
		$query .= ' INNER JOIN `#__social_profiles_maps` as upm on uu.`id` = upm.`user_id`';
		$query .= ' INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query .= ' ON uu.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query 	.= ' WHERE uu.' . $db->nameQuote( 'block' ) . ' = ' . $db->Quote( '0' );

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		$query 	.= ' AND a.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $id );
		$query 	.= ' AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_FRIENDS_STATE_PENDING );

		$db->setQuery( $query );
		$count 	= $db->loadResult();

		return $count;
	}

	/**
	 * Returns the total number of friend a user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 		The user's id.
	 *
	 * @return	int		The total number of requests.
	 */
	public function getTotalFriends( $id )
	{
		$config = FD::config();
		$db 	= FD::db();

		$query 		= array();

		$query[]	= 'SELECT COUNT(1)';
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_friends' ) . ' AS a';

		$query[] 	= 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS uu';
		$query[] 	= 'ON uu.' . $db->nameQuote( 'id' ) . ' = if( a.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $id ) . ', a.' . $db->nameQuote( 'actor_id' ) . ', a.' . $db->nameQuote( 'target_id' ) . ')';

		// exclude esad users
		$query[] = 'INNER JOIN `#__social_profiles_maps` as upm on uu.`id` = upm.`user_id`';
		$query[] = 'INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query[] = ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query[] = ' ON uu.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query[] = ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query[] 	= 'WHERE uu.' . $db->nameQuote( 'block' ) . ' = ' . $db->Quote( '0' );

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query[] = ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		$query[]	= 'AND';
		$query[]	= '( (';
		$query[]	= 'a.' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $id );
		$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS );
		$query[]	= ') OR (';
		$query[]	= 'a.' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $id );
		$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS );
		$query[]	= ') )';

		$query = implode(' ', $query);

		// echo $query;exit;

		$db->setQuery( $query );
		$count 	= (int) $db->loadResult();

		return $count;
	}


	/**
	 * Returns the total number of friend request a user made.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 		The user's id.
	 *
	 * @return	int		The total number of requests.
	 */
	public function getTotalRequestSent( $id )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_friends', 'a' );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'uu.id', 'a.target_id' );

		$sql->where( 'a.actor_id', $id );
		$sql->where( 'a.state', SOCIAL_FRIENDS_STATE_PENDING );

		$sql->where( 'uu.block', '0' );


		$db->setQuery( $sql->getTotalSql() );

		$count 	= (int) $db->loadResult();

		return $count;
	}



	/**
	 * Returns the total number of friend a user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 		The user's id.
	 *
	 * @return	int		The total number of requests.
	 */
	public function getTotalPendingFriends( $id )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_friends', 'a' );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'uu.id', 'a.actor_id' );

		$sql->where( 'a.target_id', $id );
		$sql->where( 'a.state', SOCIAL_FRIENDS_STATE_PENDING );

		$sql->where( 'uu.block', '0' );

		$db->setQuery( $sql->getTotalSql() );

		$count 	= (int) $db->loadResult();

		return $count;
	}

	/**
	 * Cancels a friend request.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The friend's record id.
	 */
	public function cancel( $id )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_friends' );
		$sql->where( 'id' , $id );

		$db->setQuery( $sql );

		$db->Query();

		return true;
	}

	/**
	 * Searches for a user's friend.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The searcher's id.
	 * @param	string	The search term.
	 * @param	string	The search type. Whether to search for username or name.
	 * @param	Array	An array of options (term - the word to search for , exclude - excluded users, privacy rules)
	 * @return 	Array	An array of SocialUser objects.
	 */
	public function search( $id , $term , $type , $options = array() )
	{
		$config = FD::config();
		$db	 = FD::db();

		// Default options
		$includeMe = isset($options['includeme']) ? $options['includeme'] : null;
		$everyone  = isset($options['everyone']) ? $options['everyone'] : null;


		$query   = array();
		$query[] = 'SELECT b.' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' ) . ' AS b';

		// exclude esad users
		$query[] = 'INNER JOIN `#__social_profiles_maps` as upm on b.`id` = upm.`user_id`';
		$query[] = 'INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query[] = ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query[] = ' ON b.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query[] = ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		if (!$everyone) {
			$query[] = 'inner join (';
			$query[] = '	select if( f.' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( $id ) . ', f.' . $db->nameQuote( 'target_id' ) . ', f.' . $db->nameQuote( 'actor_id' ) . ' ) AS ' . $db->nameQuote( 'friend' );
			$query[] = '		from ' . $db->nameQuote( '#__social_friends' ) . ' as f';
			$query[] = ' 			where ( ( f.' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( $id ) . ' and f.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS ) . ')';
			$query[] = '					OR';
			$query[] = ' 				  ( f.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $id ) . ' and f.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_FRIENDS_STATE_FRIENDS ) . ' ) )';
		}

		if ($includeMe && !$everyone) {
			$query[] = ' UNION select ' . $id . ' AS ' . $db->nameQuote( 'friend' );
		}

		if (!$everyone) {
			$query[] = ') as z';
			$query[] = 'ON b.' . $db->nameQuote( 'id' ) . ' = z.' . $db->nameQuote( 'friend' );
		}

			// Searched user must be valid user.
		$query[]	= 'WHERE b.' . $db->nameQuote('block') . '=' . $db->Quote( 0 );

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query[] = 'AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}


		if (!$includeMe) {
			$query[] = 'and b.' . $db->nameQuote( 'id' ) . ' != ' . $db->Quote( $id );
		}

		if ($type == SOCIAL_FRIENDS_SEARCH_NAME || $type == SOCIAL_FRIENDS_SEARCH_REALNAME) {
			$query[]	= 'AND b.' . $db->nameQuote('name') . ' LIKE ' . $db->Quote('%' . $term . '%');
		}

		if ($type == SOCIAL_FRIENDS_SEARCH_USERNAME) {
			$query[]	= 'AND b.' . $db->nameQuote('username') . ' LIKE ' . $db->Quote('%' . $term . '%');
		}

		if (isset($options['exclude'] ) && $options['exclude']) {
			$excludeIds = '';

			if (!is_array($options['exclude'])) {
				$options['exclude'] = explode(',', $options['exclude']);
			}

			foreach ($options['exclude']  as $id) {
				$excludeIds .= ( empty( $excludeIds ) ) ? $db->Quote( $id ) : ', ' . $db->Quote( $id );
			}

			$query[]	= 'AND b.' . $db->nameQuote('id') . ' NOT IN (' . $excludeIds . ')';
		}

		// Glue back query.
		$query 		= implode(' ', $query);

		$db->setQuery($query);

		$result 	= $db->loadColumn();

		if (!$result) {
			return false;
		}

		if (isset($options['privacy'])) {
			$my = FD::user();

			$privacyLib  = $my->getPrivacy();

			$privacyRule = $options['privacy'];

			$finalResult = array();

			foreach ($result as $rs) {
				$addItem = $privacyLib->validate( $privacyRule, $rs );

				if ($addItem) {
					$finalResult[] = $rs;
				}
			}

			$result = $finalResult;

		}

		$friends 	= FD::user($result);

		return $friends;
	}

	/**
	 * Retrieves a list of invited users
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getInvitedUsers($userId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_friends_invitations');
		$sql->where('user_id', $userId);
		$sql->order('created', 'DESC');

		$db->setQuery($sql);

		$invites = $db->loadObjectList();

		return $invites;
	}

	/**
	 * Retrieves the total number of invites user has already sent
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	int
	 */
	public function getTotalInvites($userId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_friends_invitations');
		$sql->column('COUNT(1)');
		$sql->where('user_id', $userId);

		$db->setQuery($sql);
		$total = $db->loadResult();

		return $total;
	}

}
