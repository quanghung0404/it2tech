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
class EasySocialModelFollowers extends EasySocialModel
{
	private $data			= null;

	function __construct()
	{
		parent::__construct( 'followers' );
	}

	/**
	 * Retrieve a list of followers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalFollowers( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER  );
		$sql->where( 'a.uid' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.user_id' , 'uu.id' );

		// exclude esad users
		$sql->innerjoin('#__social_profiles_maps', 'upm');
		$sql->on('uu.id', 'upm.user_id');

		$sql->innerjoin('#__social_profiles', 'up');
		$sql->on('upm.profile_id', 'up.id');
		$sql->on('up.community_access', '1');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve a list of following items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalFollowing( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER );
		$sql->where( 'a.user_id' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.uid' , 'uu.id' );

		// exclude esad users
		$sql->innerjoin('#__social_profiles_maps', 'upm');
		$sql->on('uu.id', 'upm.user_id');

		$sql->innerjoin('#__social_profiles', 'up');
		$sql->on('upm.profile_id', 'up.id');
		$sql->on('up.community_access', '1');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}


	/**
	 * Retrieve a list of followers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFollowers( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column('a.user_id', 'id');
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER );
		$sql->where( 'a.uid' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.user_id' , 'uu.id' );

		// exclude esad users
		$sql->innerjoin('#__social_profiles_maps', 'upm');
		$sql->on('uu.id', 'upm.user_id');

		$sql->innerjoin('#__social_profiles', 'up');
		$sql->on('upm.profile_id', 'up.id');
		$sql->on('up.community_access', '1');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		if( $limit != 0 )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getTotalSql() );

			// Get the list of users
			$rows 	= $this->getData( $sql->getSql() );

		}
		else
		{
			$db->setQuery( $sql );
			$rows 	= $db->loadObjectList();
		}

		if( !$rows )
		{
			return $rows;
		}

		$followers	= array();
		$ids 		= array();

		foreach ($rows as $row) {
			$ids[]	= $row->id;
		}

		$followers 	= FD::user($ids);

		return $followers;
	}

	/**
	 * Retrieve a list of followers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFollowing( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column('a.uid', 'id');
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER );
		$sql->where( 'a.user_id' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.uid' , 'uu.id' );

		// exclude esad users
		$sql->innerjoin('#__social_profiles_maps', 'upm');
		$sql->on('uu.id', 'upm.user_id');

		$sql->innerjoin('#__social_profiles', 'up');
		$sql->on('upm.profile_id', 'up.id');
		$sql->on('up.community_access', '1');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		if( $limit != 0 )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getTotalSql() );

			// Get the list of users
			$rows 	= $this->getData( $sql->getSql() );
		}
		else
		{
			$db->setQuery( $sql );

			$rows 	= $db->loadObjectList();
		}

		if( !$rows )
		{
			return $rows;
		}

		$followers = array();
		$ids 		= array();

		foreach ($rows as $row) {
			$ids[]	= $row->id;
		}

		$followers 	= FD::user($ids);

		return $followers;
	}

	public function getTotalSuggestions($userId)
	{
		$options = array('countOnly' => true);
		$count = $this->getSuggestions($userId, $options);
		return $count;
	}

	/**
	 * Retrieve a list of suggested followers
	 *
	 * @since	1.3.26
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSuggestions( $userId , $options = array() )
	{
		$db = FD::db();
		$rows = array();
		$query = '';

		$countOnly 	= isset( $options[ 'countOnly' ] ) ? $options[ 'countOnly' ] : false;

		if ($countOnly) {
			$query = "select count(distinct uid) from (";
		} else {
			$query = "select SQL_CALC_FOUND_ROWS x.`uid`, count(x.`uid`) from (";
		}

		$query .= " select a.`uid`";
		$query .= "		from `#__social_subscriptions` as a";
		$query .= "		inner join `#__social_subscriptions` as b on a.`user_id` = b.`user_id` and b.`uid` = " . $db->Quote($userId) . " and b.`type` = 'user.user'";
		$query .= "	where a.`uid` != " . $db->Quote($userId);
		$query .= "	and a.`type` = 'user.user'";
		$query .= "	and not exists (select c.`uid` from `#__social_subscriptions` as c where c.`uid` = a.`uid` and c.`type` = 'user.user' and c.`user_id` = " . $db->Quote($userId) . ")";
		$query .= " union all ";
		$query .= " select a.`uid`";
		$query .= "	from `#__social_subscriptions` as a";
		$query .= "		inner join `#__social_subscriptions` as b on a.`user_id` = b.`uid` and b.`user_id` = " . $db->Quote($userId) . " and b.`type` = 'user.user'";
		$query .= "	where a.`uid` != " . $db->Quote($userId);
		$query .= "	and a.`type` = 'user.user'";
		$query .= "	and not exists (select c.`uid` from `#__social_subscriptions` as c where c.`uid` = a.`uid` and c.`type` = 'user.user' and c.`user_id` = " . $db->Quote($userId) . ")";
		$query .= ") as x";
		$query .= " inner join `#__users` as u on x.`uid` = u.`id` and u.`block` = 0";

		// exclude esad users
		$query .= " INNER JOIN `#__social_profiles_maps` as upm on u.`id` = upm.`user_id`";
		$query .= " INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1";

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query .= " LEFT JOIN " . $db->nameQuote( '#__social_block_users' ) . " as bus";
		    $query .= " ON u." . $db->nameQuote( "id" ) . " = bus." . $db->nameQuote( "user_id" ) ;
		    $query .= " AND bus." . $db->nameQuote( "target_id" ) . " = " . $db->Quote( JFactory::getUser()->id ) ;
		}

		// at this point, no more where claus assignment!
		if ($countOnly) {

			$db->setQuery($query);
			$result = $db->loadResult();

			return $result;
		}

		// continue here if this is not a countOnly operation.
		$query .= " group by x.`uid` order by count(x.`uid`) desc";

		$max 	= isset( $options[ 'max' ] ) ? $options[ 'max' ] : '';
		if ($max) {
			$query .= " limit $max";

			// echo $query;exit;

			$db->setQuery($query);

			$rows 	= $db->loadObjectList();

		} else {

			// for pagination
			$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : FD::themes()->getConfig()->get( 'followersLimit' , 20 );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limit' , $limit );
			$this->setState( 'limitstart' , $limitstart );

			$query .= " limit $limitstart, $limit";

			$db->setQuery($query);
			$rows = $db->loadObjectList();

			// now execute found_row() to get the number of records found.
			$cntQuery = 'select FOUND_ROWS()';
			$db->setQuery( $cntQuery );
			$total	= $db->loadResult();

			// Set the total number of items.
			$this->setTotalCount($total);

		}

		if (!$rows) {
			return $rows;
		}

		$suggestions = array();
		$ids 		= array();

		foreach ($rows as $row) {
			$ids[]	= $row->uid;
		}

		$suggestions 	= FD::user($ids);

		return $suggestions;
	}
}
