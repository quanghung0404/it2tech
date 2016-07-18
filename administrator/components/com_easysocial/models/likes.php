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

FD::import( 'admin:/includes/model' );

/**
 * Model for likes.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class EasySocialModelLikes extends EasySocialModel
{

	static $_likes = array();


	/**
	 * Class construct happens here.
	 *
	 * @since	1.0
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct( 'likes' );
	}

	private function _getLikesCount( $uid, $type )
	{
		static $counts 	= array();

		$key 	= $uid . $type;

		if( !isset( $counts[ $key ] ) )
		{
			$db		= FD::db();
			$sql	= $db->sql();

			$sql->select( '#__social_likes' )
				->column( '1', '', 'count', true )
				->where( 'type', $type )
				->where( 'uid', $uid );

			$db->setQuery( $sql );
			$cnt   = $db->loadResult();

			$counts[ $key ]	= $cnt;
		}

		return $counts[ $key ];
	}


	public function setCommentLikesBatch( $data )
	{
		$config = FD::config();
		$db = FD::db();
		$sql = $db->sql();


		$dataset = array();
		// Go through each of the items
		foreach( $data as $item )
		{
			// Get related items
			$uid = $item->id;

			// pre-fill the array 1st;
			$key = $uid . '.' . 'comments.user.like';
			self::$_likes[ $key ] = array();

			$dataset[] = $uid;
		}

		// lets build the sql now.
		if( $dataset )
		{

			$query 	= "SELECT a.* FROM `#__social_likes` AS a";
			$query	.= " INNER JOIN `#__users` AS b";
			$query 	.= " ON a.`created_by` = b.`id`";

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    // user block
			    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
			    $query .= ' ON b.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
			    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
			}

			$query 	.= " WHERE a.uid IN (" . implode(',' , $dataset). ")";
			$query 	.= " AND a.`type` = 'comments.user.like'";
			$query 	.= " AND b.`block` = " . $db->Quote(0);

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    // user block continue here
			    $query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
			}

			// echo $query;exit;

			if( $query )
			{
				$sql->raw( $query );
				$db->setQuery( $sql );

				$result = $db->loadObjectList();

				if( $result )
				{
					foreach( $result as $rItem )
					{
						$key = $rItem->uid . '.' . $rItem->type;

						$like 		= FD::table( 'Likes' );
						$like->bind( $rItem );

						self::$_likes[ $key ][] = $like;
					}
				}
			}

		}
	}


	public function setStreamLikesBatch( $data )
	{
		$config = FD::config();
		$db = FD::db();
		$sql = $db->sql();

		//var_dump( $data );

		$streamModel = FD::model( 'Stream' );

		$dataset = array();
		// Go through each of the items
		foreach( $data as $item )
		{
			// Get related items - stream id.
			$uid = $item->id;

			// If there's no context_id, skip this.
			if( !$uid )
			{
				continue;
			}

			// pre-fill the array 1st;
			// $group = ( $item->cluster_id ) ? $item->cluster_type : SOCIAL_APPS_GROUP_USER;

			// $key = $item->id . '.' . $item->context_type . '.' . $group . '.' . $item->verb;

			// if($item->context_type == 'badges' || $item->context_type == 'apps') {
			// 	$key .= '.' . $item->actor_id;
			// }

			$key = $item->id . '.' . 'stream';

			self::$_likes[ $key ] = array();

			$dataset[] = $uid;
		}
		// lets build the sql now.
		if( $dataset )
		{

			$mainSQL = '';

			$query 	= "SELECT a.* FROM `#__social_likes` AS a";
			$query	.= " INNER JOIN `#__users` AS b";
			$query 	.= " ON a.`created_by` = b.`id`";

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    // user block
			    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
			    $query .= ' ON b.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
			    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
			}

			$query 	.= " WHERE a.stream_id IN (" . implode(',' , $dataset). ")";
			$query 	.= " AND b.`block` = " . $db->Quote(0);

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    // user block continue here
			    $query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
			}

			// echo $query;exit;

			if( $query )
			{
				$sql->raw( $query );
				$db->setQuery( $sql );

				$result = $db->loadObjectList();

				if( $result )
				{
					foreach( $result as $rItem )
					{
						// $key = $rItem->type;
						$key = $rItem->stream_id . '.'. 'stream';

						$like 		= FD::table( 'Likes' );
						$like->bind( $rItem );

						self::$_likes[ $key ][] = $like;
					}
				}
			}

		}

	// var_dump( self::$_likes );
	}

	public function setLikeItem( $key, $likeObj )
	{
		// update likes static variable
		$array 		= ( isset( self::$_likes[ $key ] ) ) ? self::$_likes[ $key ] : array() ;
		$array[] 	= $likeObj;
		self::$_likes[ $key ] = $array;
	}

	/**
	 * Removes a like data from the cache
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeLikeItem( $key, $userId )
	{
		$array 		= self::$_likes[ $key ];

		$new        = array();

		foreach( $array as $arr )
		{
			if( $arr->created_by != $userId )
			{
				$new[] = $arr;
			}
		}

		self::$_likes[ $key ] = $new;
	}



	/**
	 * Delete likes related to an object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @param   string type.groups
	 * @return
	 */
	public function delete( $uid , $type )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_likes' );
		$sql->where( 'uid' , $uid );
		$sql->where( 'type' , $type );

		$db->setQuery( $sql );

		$db->Query();

		return true;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLikeStats( $dates , $userId )
	{
		$db 	= FD::db();
		$likes	= array();

		foreach( $dates as $date )
		{
			// Registration date should be Y, n, j
			$date	= FD::date( $date )->format( 'Y-m-d' );

			$query 		= array();
			$query[] 	= 'SELECT `a`.`id`, COUNT( `a`.`id`) AS `cnt` FROM `#__social_likes` AS a';
			$query[]	= 'WHERE `a`.`created_by`=' . $db->Quote( $userId );
			$query[]	= 'AND DATE_FORMAT( `a`.`created`, GET_FORMAT( DATE , "ISO") ) = ' . $db->Quote( $date );
			$query[]    = 'group by a.`created_by`';


			$query 		= implode( ' ' , $query );
			$sql		= $db->sql();
			$sql->raw( $query );

			$db->setQuery( $sql );

			$items				= $db->loadObjectList();

			// There is nothing on this date.
			if( !$items )
			{
				$likes[]	= 0;
				continue;
			}

			foreach( $items as $item )
			{
				$likes[]	= $item->cnt;
			}
		}

		// Reset the index.
		$likes 	= array_values( $likes );

		return $likes;
	}

	/**
	 * $uuid - the unique id of the liked item
	 * $uType - the item type being liked - stream type (status, groups, photos ), comment etc.
	 *
	 * return - int
	 */

	public function getLikesCount( $uuid, $uType )
	{
		//$likeCount = $this->_getLikesCount($uuid, $uType);

		$likes 		= $this->getLikeData( $uuid, $uType );
		$likeCount  = count( $likes );

		return ( empty( $likeCount ) ) ? 0 : $likeCount;
	}

	private function getLikeData( $id , $type )
	{
		// Build the index for the like
		$key	= $id . '.' . $type;

		if( !isset( self::$_likes[ $key ] ) )
		{
			// var_dump( debug_backtrace(2) );
			//exit;
			//

			// var_dump('getLikeData::' . $key);
			// exit;

			$db			= FD::db();
			$sql 		= $db->sql();

			$sql->select('#__social_likes', 'a');
			$sql->join('#__users', 'b', 'INNER');
			$sql->on('a.created_by', 'b.id', '=');

			if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    $sql->leftjoin( '#__social_block_users' , 'bus');

		    	$sql->on( 'b.id' , 'bus.target_id' );
		    	$sql->on( 'bus.user_id', JFactory::getUser()->id );

			    $sql->isnull('bus.id');
			}

			if ($type == 'stream') {
				$sql->where('a.stream_id' , $id );
			} else {
				$sql->where('a.uid' , $id );
				$sql->where('a.type' , $type );
			}
			$sql->where('b.block', 0);

			$db->setQuery( $sql );

			$result 	= $db->loadObjectList();

			// Initialize the items at index
			self::$_likes[ $key ] = array();

			if( $result )
			{
				// Pre-load the users for the liked items
				foreach( $result as $row )
				{
					$like 		= FD::table( 'Likes' );
					$like->bind( $row );

					self::$_likes[ $key ][] = $like;
				}
			}
		}

		$result = self::$_likes[ $key ];
		$result = is_array( $result ) ? $result : array( $result );

		return $result;
	}



	/**
	 * Retrieves likes for a particular item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id.
	 * @param	string	The unique type.group
	 * @return
	 */
	public function getLikes( $id , $type )
	{
		$likes = $this->getLikeData( $id, $type );
		return $likes;
	}

	/**
	 * Retrieves user ids who liked the item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id.
	 * @param	string	The unique type.group
	 * @return  array   userid
	 */
	public function getLikerIds( $id , $type, $exclude = array() )
	{
		$likes = $this->getLikeData( $id, $type );

		$likers = array();
		if( $likes )
		{
			foreach( $likes as $like )
			{
				if( $exclude && !in_array( $like->created_by, $exclude ) )
				{
					$likers[] = $like->created_by;
				}
				else
				{
					$likers[] = $like->created_by;
				}
			}
		}

		return $likers;
	}

	/**
	 * Determines if a user has already liked an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id.
	 * @param	string	The unique type.
	 * @param	int		The user's id.
	 * @return
	 */
	public function hasLiked($id , $type , $userId, $useStreamId = false)
	{
		$likes = null;

		if ($useStreamId) {
			// echo $useStreamId;exit;
			$likes = $this->getLikeData( $useStreamId, 'stream' );
		} else {
			$likes = $this->getLikeData( $id, $type );
		}

		if( $likes )
		{
			foreach( $likes as $like )
			{
				if( $like->created_by == $userId )
				{
					return true;
				}
			}
		}

		return false;

	}

	/**
	 * Adds the necessary data when a user likes an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id.
	 * @param	string	The unique type.
	 * @param	int		The user's id.
	 * @return	boolean	True if success, false otherwise.
	 */
	public function like( $id , $type , $userId, $streamId = null )
	{
		$likes 	= FD::table( 'Likes' );

		$likes->uid 		= $id;
		$likes->type 		= $type;
		$likes->created_by	= $userId;
		$likes->stream_id 	= $streamId;

		$state 		= $likes->store();

		// If there's an error storing, log this down.
		if (!$state) {
			// Set the error to the model.
			$this->setError( $table->getError() );
		}

		//update like static variable
		$key 		= $id . '.' . $type;
		$this->setLikeItem( $key, $likes );

		return $state;
	}

	/**
	 * Removes the necessary data when a user unlike an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id.
	 * @param	string	The unique type.
	 * @param	int		The user's id.
	 * @return	boolean	True if success, false otherwise.
	 */
	public function unlike( $id , $type , $userId, $streamId = null )
	{
		$likes 	= FD::table( 'Likes' );

		// Test if this even exists
		$options = array( 'uid' => $id , 'type' => $type , 'created_by' => $userId );
		if( $streamId )
		{
			$options['stream_id'] = $streamId;
		}
		$state 	= $likes->load( $options );

		if( !$state )
		{
			return false;
		}

		$state 	= $likes->delete();

		if (!$state) {
			// Set the error to the model.
			$this->setError( $table->getError() );
		}

		//update like static variable
		$key 		= $id . '.' . $type;
		$this->removeLikeItem( $key, $userId );

		return $state;
	}
}
