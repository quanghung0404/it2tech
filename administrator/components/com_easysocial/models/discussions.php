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

class EasySocialModelDiscussions extends EasySocialModel
{
	public function __construct( $config = array() )
	{
		parent::__construct( 'discussions' , $config );
	}

	/**
	 * Initializes all the generic states from the form
	 *
	 * @since	1.2
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
	 * Retrieves a list of participants from a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The discussion id.
	 * @return
	 */
	public function getParticipants( $id , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->column( 'DISTINCT( a.created_by )' );
		$sql->select( '#__social_discussions' , 'a' );
		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.created_by' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}
		$sql->where( '(' );
		$sql->where( 'a.parent_id' , $id );
		$sql->where( 'a.id' , $id , '=' , 'OR' );
		$sql->where( ')' );
		$sql->where( 'a.state' , SOCIAL_STATE_PUBLISHED );

		$exclude 	= isset( $options[ 'exclude' ] ) ? $options[ 'exclude' ] : '';

		if( $exclude )
		{
			$exclude 	= FD::makeArray( $exclude );
			$sql->where( 'a.created_by' , $exclude , 'NOT IN' );
		}

		$db->setQuery( $sql );

		$rows 	= $db->loadColumn();

		if( !$rows )
		{
			return $rows;
		}

		$users 	= FD::user( $rows );

		return $users;
	}

	/**
	 * Retrieves the last reply item for a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The discussion id
	 * @return	SocialTableDiscussion 	The discussion table object.
	 */
	public function getLastReply( $id )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_discussions' , 'a' );
		$sql->where( 'a.parent_id' , $id );
		$sql->where( 'a.state' , SOCIAL_STATE_PUBLISHED );
		$sql->order( 'a.created' , 'DESC' );
		$sql->limit( 1 );

		$db->setQuery( $sql );

		$obj 	= $db->loadObject();

		if( !$obj )
		{
			return false;
		}

		$reply 	= FD::table( 'Discussion' );
		$reply->bind( $obj );

		return $reply;
	}

	/**
	 * Retrieves the total number of discussions
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The discussion id
	 * @return	int		The total replies
	 */
	public function getTotalDiscussions( $uid , $type )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_discussions' , 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'a.parent_id' , '0' );
		$sql->where( 'a.uid' , $uid );
		$sql->where( 'a.type' , $type );
		$sql->where( 'a.state' , SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $sql );

		$total	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total number of replies for a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The discussion id
	 * @return	int		The total replies
	 */
	public function getTotalReplies( $id )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_discussions' , 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'a.parent_id' , $id );
		$sql->where( 'a.state' , SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $sql );

		$total	= $db->loadResult();

		return $total;
	}

	/**
	 * Delete files from a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFiles( $id )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_discussions_files' );
		$sql->where( 'discussion_id' , $id );

		$db->setQuery( $sql );
		return $db->Query();
	}

	/**
	 * Deletes all replies from a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The unique id it is associated with.
	 * @return	Array	An array of SocialTableDiscussion
	 */
	public function deleteReplies( $id )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		// Get a list of replies so we can delete their respective stream items first
		$replies 	= $this->getReplies( $id );

		if( !$replies )
		{
			return;
		}

		foreach( $replies as $reply )
		{
			// Delete all stream items for the replies
			FD::stream()->delete( $reply->id , 'discussions' );

			// Delete all files related to this reply.
			$this->deleteFiles( $reply->id );
		}

		// Now we delete all the items
		$sql->delete( '#__social_discussions' );
		$sql->where( 'parent_id' , $id );

		$db->setQuery( $sql );

		$state 	= $db->Query();

		return $state;
	}

	/**
	 * Retrieves a list of replies to a discussion given the unique id of the discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The unique id it is associated with.
	 * @return	Array	An array of SocialTableDiscussion
	 */
	public function getReplies( $id , $options = array() )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_discussions' , 'a' );

		$sql->column('a.*');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.created_by' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where( 'a.parent_id' , $id );

		// Determines if we should always order the items
		$ordering 	= isset($options['ordering']) ? $options['ordering'] : '';

		if ($ordering) {

			if ($ordering == 'created') {
				$sql->order('a.created', 'ASC');
			}

		}

		$db->setQuery( $sql );

		$result 	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$replies	= array();

		// Preload the list of users first
		$authors 	= array();

		foreach( $result as $row )
		{
			$reply 	= FD::table( 'Discussion' );
			$reply->bind( $row );

			$authors[]	= $reply->created_by;
			$replies[]	= $reply;
		}

		// Preload list of users
		FD::user( $authors );

		foreach( $replies as &$reply )
		{
			$reply->author 	= FD::user( $reply->created_by );
		}

		return $replies;
	}

	/**
	 * Retrieves a list of discussions given the unique id and the unique type.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The unique id it is associated with.
	 * @param	string	The unique type it is associated with.
	 * @param	Array	An array of options
	 * @return	Array	An array of SocialTableDiscussion
	 */
	public function getDiscussions( $uid , $type , $options = array() )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->column('a.*');
		$sql->select( '#__social_discussions' , 'a' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.created_by' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where( 'a.uid' , $uid );
		$sql->where( 'a.type' , $type );
		$sql->where( 'a.parent_id' , 0 );

		// Determines if we should fetch resolved items only
		$resolved 	= isset( $options[ 'resolved' ] ) ? $options[ 'resolved' ] : '';

		if( $resolved )
		{
			$sql->where( 'a.answer_id' , '' , '!=' );
		}

		// Determines if we should fetch locked items only
		$locked 	= isset( $options[ 'locked' ] ) ? $options[ 'locked' ] : '';

		if( $locked )
		{
			$sql->where( 'a.lock' , '' , '!=' );
		}

		// Determines if we should fetch unanswered items only
		$unanswered 	= isset( $options[ 'unanswered' ] ) ? $options[ 'unanswered' ] : '';

		if( $unanswered )
		{
			$sql->where( 'a.last_reply_id' , '0' , '=' );
		}

		// Default to order by creation date
		$sql->order( 'a.created' , 'DESC' );

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		if( $limit )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Run pagination here.
			$this->setTotal( $sql->getTotalSql() );

			$result		= $this->getData( $sql->getSql() );
		}
		else
		{
			$db->setQuery( $sql );
			$result 	= $db->loadObjectList();
		}

		if( !$result )
		{
			return $result;
		}

		$discussions	= array();

		// Preload the list of users first
		$authors 	= array();

		foreach( $result as $row )
		{
			$discussion 	= FD::table( 'Discussion' );
			$discussion->bind( $row );

			// If this discussion has been replied before, we need to retrieve the item
			$discussion->lastreply	= false;

			if( $discussion->last_reply_id )
			{
				// @TODO: Need to think of a way to optimize this
				$reply 	= FD::table( 'Discussion' );
				$reply->load( $discussion->last_reply_id );
				$reply->author	= FD::user( $reply->created_by );

				$discussion->lastreply	= $reply;
			}

			$authors[]		= $discussion->created_by;
			$discussions[]	= $discussion;
		}

		// Preload list of users
		FD::user( $authors );

		foreach( $discussions as &$discussion )
		{
			$discussion->author 	= FD::user( $discussion->created_by );
		}

		return $discussions;
	}

	/**
	 * Deletes all discussion from a given type
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete( $uid , $type )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_discussions' );
		$sql->where( 'uid' , $uid );
		$sql->where( 'type' , $type );

		$db->setQuery( $sql );

		$state	= $db->query();

		return $state;
	}
}
