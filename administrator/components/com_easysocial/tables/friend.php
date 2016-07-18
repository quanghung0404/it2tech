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

/**
 * Object Relation Mapping for the friends table.
 *
 * Usage:
 *
 * <code>
 * $id 		= JRequest::getInt( 'id' );
 *
 * // Loads a new friend record given the unique key.
 * $table 	= FD::table( 'Friend' );
 * $table->load( $id );
 * </code>
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class SocialTableFriend extends SocialTable
{
	/**
	 * The unique id which is auto incremented.
	 * @var int
	 */
	public $id			= null;

	/**
	 * The user id that requested the friendship.
	 * @var int
	 */
	public $actor_id	= null;

	/**
	 * The user id which is being targeted.
	 * @var int
	 */
	public $target_id	= null;

	/**
	 * The state of the friendship.
	 * @var bool
	 */
	public $state		= null;

	/**
	 * The datetime value of the request that was initially created.
	 * @var datetime
	 */
	public $created 	= null;

	/**
	 * The datetime value of the request that was initially created.
	 * @var datetime
	 */
	public $modified 	= null;

	/**
	 * The message that was sent to the target user from the source user.
	 * @var datetime
	 */
	public $message		= null;

	public function __construct( $db )
	{
		parent::__construct('#__social_friends', 'id', $db);
	}

	/**
	 * Override parent's behavior of loading
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Source user id.
	 * @param	int		Target user id.
	 * @return
	 */
	public function loadByUser( $actorId , $targetId )
	{
		$db 		= FD::db();

		$query		= array();

		$query[]	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl );
		$query[]	= 'WHERE';
		$query[]	= '(';
		$query[]	= $db->nameQUote( 'actor_id' ) . '=' . $db->Quote( $actorId ) . ' AND ' . $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $targetId );
		$query[]	= ') OR (';
		$query[]	= $db->nameQuote( 'target_id' ) . '=' . $db->Quote( $actorId ) . ' AND ' . $db->nameQuote( 'actor_id' ) . '=' . $db->Quote( $targetId );
		$query[]	= ')';

		$db->setQuery( $query );

		$data 		= $db->loadObject();

		if( !$data )
		{
			return false;
		}

		return parent::bind( $data );
	}

	public function bind( $data , $ignore = array() )
	{
		$state	= parent::bind( $data );

		// @task: If created is not set, we need to set it here.
		if( empty( $this->created ) )
		{
			$this->created	= FD::get( 'Date' )->toMySQL();
		}

		// @task: If created is not set, we need to set it here.
		if( empty( $this->state ) )
		{
			// @TODO: Make this configurable. Default state to be published
			$this->state	= SOCIAL_FRIENDS_LIST_PUBLISHED;
		}

		return $state;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sendNotification( $action = '' )
	{
		// Send notifications when a user approves a friend request.
		if( $action == 'approve' )
		{
			// We want to send a notification to the user who initiated the friend request.
			$recipient 		= array( $this->actor_id );

			// Get the target object.
			$target			= FD::user( $this->target_id );

			// Add notification to the requester that the user accepted his friend request.
			$systemOptions		= array(
											// The unique node id here is the #__social_friend id.
											'uid'		=> $this->id,
											// The actor is always the target because the actor is receiving this notification item.
											'actor_id'	=> $this->target_id,
											'type'		=> SOCIAL_TYPE_FRIEND,
											'permalink'	=> FRoute::profile( array( 'id' => $target->getAlias() ) ),
											'image'		=> $target->getAvatar( SOCIAL_AVATAR_LARGE ),
											'url'		=> $target->getPermalink(false, false, false)
										);

			// Send notification to the original requested when a user approves to be his / her friend.
			$params 	= array(
								'actor'			=> $target->getName(),
								'friendId'		=> $target->id,
								'friendAvatar'	=> $target->getAvatar( SOCIAL_AVATAR_LARGE ),
								'friendName'	=> $target->getName(),
								'friendLink'	=> $target->getPermalink(true, true),
								'friendDate'		=> FD::date()->toMySQL(),
								'totalFriends'		=> $target->getTotalFriends(),
								'totalMutualFriends'=> $target->getTotalMutualFriends( $this->actor_id )
							);

			// Email template
			$emailOptions 		= array(
											'title'		=> 'COM_EASYSOCIAL_EMAILS_FRIENDS_REQUEST_APPROVED_SUBJECT',
											'template'	=> 'site/friends/accepted',
											'params'	=> $params
										);

			// Add the option to the notification.
			FD::notify('friends.approve', $recipient, $emailOptions, $systemOptions);
		}

	}

	/**
	 * Determines if the user is the initiator of the request
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The user's id to check against.
	 * @return	bool	True if the user is the initiator, false otherwise.
	 */
	public function isInitiator( $id = null )
	{
		$id 	= FD::user( $id )->id;

		$state 	= $this->actor_id == $id ? true : false;

		return $state;
	}

	/**
	 * This contains the logic to approve a friend request.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	$message	Any approval message
	 *
	 * @return	boolean				True on success false otherwise.
	 **/
	public function approve()
	{
		// Set the last modified date.
		$this->modified 	= FD::date()->toMySQL();

		// Set the state and ensure that the state is both friends.
		$this->state 		= SOCIAL_FRIENDS_STATE_FRIENDS;

		// Try to save this record.
		$state				= $this->store();

		// Send notification to the requester that the target user has accepted his friend request.
		$this->sendNotification(__FUNCTION__);

		// If the user is approving this request, they will always be the target_id
		$user 			= FD::user( $this->actor_id );
		$target			= FD::user( $this->target_id );

		// Get the default friend list for the user.
		$list 	= $user->getDefaultFriendList();

		// If there's a friend list, assign the target to their friend list
		if( $list )
		{
			$list->addFriends( array( $target->id ) );
		}

		// Now we get the target's friend list
		$list 	= $target->getDefaultFriendList();

		if( $list )
		{
			$list->addFriends( array( $user->id ) );
		}


		// Assign points to the user that approves this request.
		$points 	= FD::points();
		$points->assign('friends.add', 'com_easysocial' , $target->id);

		// Assign points to the user that requested this friend.
		$points->assign('friends.add', 'com_easysocial' , $user->id);

		// Add activity logging when a friend connection has been made.
		// Activity logging.

		$config = FD::config();
		if( $config->get( 'users.stream.friend' ) )
		{
			$stream				= FD::stream();
			$streamTemplate		= $stream->getTemplate();

			// Set the actor.
			$streamTemplate->setActor( $this->actor_id , SOCIAL_TYPE_USER );

			// Set the context.
			$streamTemplate->setContext( $this->id , SOCIAL_TYPE_FRIEND );

			// set the target
			$streamTemplate->setTarget( $this->target_id );

			// Set the verb.
			$streamTemplate->setVerb( 'add' );

			// set to aggreate friend
			$streamTemplate->setAggregate( true );

			$streamTemplate->setAccess( 'core.view' );

			// Create the stream data.
			$stream->add( $streamTemplate );
		}

		return $state;
	}

	/**
	 * This contains the logic to reject a friend request
	 *
	 * @access	public
	 * @param	string	$message	Any reject message
	 *
	 * @return	boolean	True on success false otherwise.
	 **/
	public function reject()
	{
		// When a reject occurs, we need to delete the record.
		$state 	= $this->delete();

		// @TODO: Remove related items

		return $state;
	}

	/**
	 * Override parent's delete method as we have our own logics.
	 *
	 * @access	public
	 * @param	null
	 * @return	boolean		True on success, false otherwise.
	 **/
	public function unfriend( $actorId = null )
	{
		// Get the actor's id
		$actor 		= FD::user( $actorId );

		// Delete the current friend record first.
		$state		= parent::delete();

		// Remove user's from the respective lists.
		$this->removeFromLists();

		// Remove user's conversations since they shouldn't be communicating anymore.
		$this->removeConversations();

		// Remove any stream associations
		$this->removeFromStream();

		// @points: friends.remove
		// Deduct points from the actor
		$points = FD::points();
		$points->assign( 'friends.remove' , 'com_easysocial' , $actor->id );

		// @badge: friends.remove
		// Assign badge for actor.
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'friends.remove' , $actor->id , JText::_( 'COM_EASYSOCIAL_FRIENDS_BADGE_REMOVED_FRIEND' ) );

		return $state;
	}

	/**
	 * Remove any friend associations from the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFromStream()
	{
		$stream 	= FD::stream();

		return $stream->delete( $this->id , SOCIAL_TYPE_FRIEND );
	}

	public function removeConversations()
	{

	}

	/**
	 * Removes user from any associated friend list.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeFromLists()
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_lists_maps' );
		$sql->where( 'target_id', $this->target_id );
		$sql->where( 'target_id', $this->actor_id, '=', 'OR' );

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}

	/**
	 * Override parent's store method.
	 *
	 * @access	public
	 * @param	bool	$updateModified		Update modified time if this is true. Default true.
	 * @return	bool	True on success, false on error.
	 */
	public function store( $updateModified = true )
	{
		$now		= FD::get( 'Date' )->toMySQL();

		// If script needs us to alter the modified date or if it's a new record,
		// ensure that the modified column contains proper values.
		if( $updateModified )
		{
			$this->modified	= $now;
		}

		return parent::store();
	}

	/**
	 * Retrieves the target user
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	SocialTableUser	A person object
	 **/
	public function getTarget()
	{
		return FD::user( $this->target );
	}

	/**
	 * Retrieves the SocialUser object of the person that requested the friendship.
	 *
	 * Example:
	 *
	 * <code>
	 * <?php
	 * $requestId	= JRequest::getInt( 'requestId' );
	 * $table		= FD::table( 'Friend' );
	 * $table->load( $requestId );
	 * $user 		= $table->getRequester();
	 * echo $user->getName();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialUser
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getRequester()
	{
		$user 	= FD::user( $this->actor_id );

		return $user;
	}

	/**
	 * Retrieves the request message by the source
	 *
	 * Example:
	 *
	 * <code>
	 * <?php
	 * $requestId	= JRequest::getInt( 'requestId' );
	 * $table		= FD::table( 'Friends' );
	 * $table->load( $requestId );
	 * echo $table->getRequestMessage();
	 * ?>
	 * </code>
	 *
	 * @access	public
	 * @param	null
	 * @return	string	The request message
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getRequestMessage()
	{
		return $this->message;
	}

	/**
	 * Retrieves the response message by the target
	 *
	 * @access	public
	 * @param	null
	 * @return	string	The request message
	 */
	public function getResponseMessage()
	{
		return $this->modified_message;
	}

	/**
	 * Sets a target id.
	 *
	 * Example:
	 *
	 * <code>
	 * <?php
	 * $table	= FD::table( 'Friend' );
	 * $table->setTargetId( FD::user()->id );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The target id.
	 * @return	SocialTableFriend
	 */
	public function setTargetId( $id )
	{
		$this->target_id	= $id;

		return $this;
	}

	/**
	 * Get's the actor id.
	 *
	 * Example:
	 *
	 * <code>
	 * <?php
	 * $table	= FD::table( 'Friend' );
	 * $table->load( JRequest::getInt( 'id' ) );
	 * echo $table->getActorId();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialTableFriend
	 */
	public function getActorId()
	{
		return $this->target_id;
	}

	/**
	 * Sets the requester id.
	 *
	 * Example:
	 *
	 * <code>
	 * <?php
	 * $table	= FD::table( 'Friend' );
	 * $table->setSourceId( FD::user()->id );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The target id.
	 * @return	SocialTableFriend
	 */
	public function setActorId( $id )
	{
		$this->actor_id	= $id;

		return $this;
	}
}




