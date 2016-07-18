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

class EasySocialModelNotifications extends EasySocialModel
{
	function __construct()
	{
		parent::__construct( 'notifications' );
	}

	public function setAllState( $state )
	{
		$my 	= FD::user();

		$db 	= FD::db();
		$sql 	= $db->sql();

		$query 	= '';

		if( $state == 'clear' )
		{
			$query = 'delete from `#__social_notifications`';
			$query .= ' where `target_id` = ' . $db->Quote( $my->id );
			$query .= ' and `target_type` = ' . $db->Quote( SOCIAL_TYPE_USER );
		}
		else
		{
			$query = 'update `#__social_notifications` set `state` = ' . $db->Quote( $state );
			$query .= ' where `target_id` = ' . $db->Quote( $my->id );
			$query .= ' and `target_type` = ' . $db->Quote( SOCIAL_TYPE_USER );
		}

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );

		// echo $query;
		// exit;

		$state = $db->query();
		return $state;

	}

	/**
	 * Saves a notification settings
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The type of notification, whether it is an email or system
	 * @return
	 */
	public function saveNotifications( $systemNotifications , $emailNotifications , SocialUser $user )
	{
		// Get the id's of all the notifications
		$keys	= array_keys( $systemNotifications );
		$rules	= array();

		foreach( $keys as $key )
		{
			$obj 			= new stdClass();
			$obj->id 		= $key;
			$obj->email		= isset( $emailNotifications[ $key ] ) ? $emailNotifications[ $key ] : true;
			$obj->system 	= isset( $systemNotifications[ $key ] ) ? $systemNotifications[ $key ] : true;

			$rules[]	= $obj;
		}

		// Now that we have the rules, store them.
		foreach( $rules as $rule )
		{
			$map 	= FD::table( 'AlertMap' );
			$state	= $map->load( array( 'alert_id' => $rule->id , 'user_id' => $user->id ) );

			$map->alert_id 	= $rule->id;
			$map->user_id	= $user->id;

			$map->email 	= $rule->email;
			$map->system 	= $rule->system;

			$map->store();
		}

		return true;
	}

	/**
	 * Retrieve a list of notification items from the database.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of options.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getItems( $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->column('a.*');
		$sql->select( '#__social_notifications', 'a' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.actor_id' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		// If published options is provided, only search for respective notification items.
		if( isset( $options[ 'unread' ] ) )
		{
			$sql->where( 'a.state' , SOCIAL_NOTIFICATION_STATE_UNREAD );
		}

		// Only fetch items from specific target id and type if necessary.
		$target 		= isset( $options[ 'target_id' ] ) ? $options[ 'target_id' ] : null;

		if( $target )
		{
			$targetType	= $options[ 'target_type' ];

			$sql->where( 'a.target_id' , $target );
			$sql->where( 'a.target_type' , $targetType );
		}


		// if badges / achievement system disabled, then we shouldn't retrive badges related notifications.
		$config = FD::config();
		if(! $config->get( 'badges.enabled' ) )
		{
			$sql->where( 'a.type' , SOCIAL_TYPE_BADGES, '!=' );
		}


		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : 0;
		if( $limit )
		{
			$startlimit = isset( $options[ 'startlimit' ] ) ? $options[ 'startlimit' ] : 0;
			$sql->limit( $startlimit, $limit );
		}


		// Always order by latest first
		$ordering 	= isset( $options[ 'ordering' ] ) ? $options[ 'ordering' ] : '';

		if( $ordering )
		{
			$direction 	= isset( $options[ 'direction' ] ) ? $options[ 'direction' ] : 'DESC';
			$sql->order( $ordering , $direction );
		}
		else
		{
			$sql->order( 'a.created' , 'DESC' );
		}

		$db->setQuery( $sql );

		// echo $sql;

		$items	= $db->loadObjectList();

		if( !$items )
		{
			return $items;
		}

		$result 	= array();

		foreach( $items as $item )
		{
			$notification 	= FD::table( 'Notification' );
			$notification->bind( $item );

			$result[]		= $notification;
		}

		return $result;
	}

	/**
	 * Retrieves the count of notification items.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of options. unread - Only count unread items
	 * @return	int		The count.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCount( $options = array() )
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_notifications', 'a' );
		$sql->column('COUNT(1)');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.actor_id' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		// Only fetch items from specific target id and type if necessary.
		$target = isset($options['target']) ? $options['target'] : null;

		if( !is_null( $target ) && is_array( $target ) )
		{
			$targetId 	= $target[ 'id' ];
			$targetType	= $target[ 'type' ];

			$sql->where( 'a.target_id' 	, $targetId );
			$sql->where( 'a.target_type'	, $targetType );
		}

		// Only fetch unread items
		if( isset( $options[ 'unread' ] ) )
		{
			$sql->where( 'a.state' , SOCIAL_NOTIFICATION_STATE_UNREAD );
		}

		// if badges / achievement system disabled, then we shouldn't retrive badges related notifications.
		$config = FD::config();

		if (!$config->get('badges.enabled')) {
			$sql->where( 'a.type' , SOCIAL_TYPE_BADGES, '!=' );
		}

		$db->setQuery($sql);

		$total = $db->loadResult();

		return $total;
	}
}
