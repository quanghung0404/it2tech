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
defined( 'JPATH_BASE' ) or die( 'Unauthorized Access' );

// Import main table
FD::import('admin:/tables/table');

/**
 * Object relation mapping for conversation.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialTableConversation extends SocialTable
{
	public $id = null;
	public $created = null;
	public $created_by = null;
	public $lastreplied = null;
	public $type = null;

	public $isread		= null;
	public $message		= null;

	public function __construct( $db )
	{
		parent::__construct('#__social_conversations', 'id' , $db);
	}

	/**
	 * Override's parent's store behavior
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	bool		True on success false otherwise
	 */
	public function store($updateNulls = false)
	{
		// Determines if this is a new conversation object.
		$isNew = $this->id ? false : true;
		$state = parent::store();

		if ($isNew) {
			// @badge: conversation.create
			$badge 	= FD::badges();
			$badge->log('com_easysocial', 'conversation.create', $this->created_by, JText::_('COM_EASYSOCIAL_CONVERSATIONS_BADGE_STARTED_NEW_CONVERSATION'));

			// @points: conversation.create
			// Assign points when user starts new conversation
			$type 	= $this->type == SOCIAL_CONVERSATION_SINGLE ? '' : '.group';
			$points = FD::points();
			$points->assign( 'conversation.create' . $type , 'com_easysocial' , $this->created_by );
		}

		return $state;
	}

	/*
	 * Loads a conversation record based on the existing conversations.
	 *
	 * @param	int		$creator	The node id of the creator.
	 * @param	int		$recipient	The node id of the recipient.
	 */
	public function loadByRelation( $creator , $recipient , $type )
	{
		$db 	= FD::db();
		$query	= 'SELECT COUNT(1) AS related,b.* FROM ' . $db->nameQuote( '#__social_conversations_participants' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( $this->_tbl ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'id' ) . ' = a.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'WHERE ( '
				. 'a.' . $db->nameQuote( 'user_id') . ' = ' . $db->Quote( $creator ) . ' '
				. 'OR '
				. 'a.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $recipient ) . ' '
				. ') '
				. 'AND b.' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type ) . ' '
				. 'GROUP BY a.' . $db->nameQuote( 'conversation_id' );

		// echo $query;exit;
		$db->setQuery( $query );

		$data	= $db->loadObject();

		if( !isset( $data->related ) )
		{
			return false;
		}

		if( $data->related >= 2 )
		{
			return parent::bind( $data );
		}
		return false;
	}

	/**
	 * Determines if the current user is a participant in the current conversation.
	 *
	 * @param	int			The user's id.
	 *
	 * @return	boolean		True if participant, false otherwise.
	 */
	public function isParticipant( $userId = null )
	{
		$user 	= FD::user( $userId );

		$model 	= FD::model( 'Conversations' );
		return $model->isParticipant( $this->id , $user->id );
	}

	/**
	 * Determines if the current conversation is multiple recipients or not.
	 *
	 * @return	boolean
	 */
	public function isMultiple()
	{
		return $this->type == SOCIAL_CONVERSATION_MULTIPLE;
	}

	/**
	 * Determines if the current conversation is multiple recipients or not.
	 *
	 * @return	boolean
	 */
	public function isSingle()
	{
		return $this->type == SOCIAL_CONVERSATION_SINGLE;
	}

	/**
	 * Determines if the current conversation is archived or not.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$userId
	 * @return	boolean
	 */
	public function isArchived( $userId )
	{
		static $archives = array();

		if( !isset( $archives[ $this->id . $userId ] ) )
		{
			$model 	= FD::model( 'Conversations' );

			$archives[ $this->id . $userId ] = $model->isArchived( $this->id , $userId );
		}

		return $archives[ $this->id . $userId ];
	}

	/**
	 * Determines if the current node really has access to the specific conversation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The current viewer's id.
	 * @return	boolean		True if the user has access to the conversation. False otherwise
	 */
	public function hasAccess( $userId )
	{
		$model = FD::model( 'Conversations' );

		return $model->hasAccess( $this->id , $userId );
	}

	/**
	 * Determines if the current conversation contains any attachments
	 * @param	null
	 * @return	boolean		True if contain attachments, false otherwise
	 */
	public function hasAttachments()
	{
		$model 	= FD::model( 'Conversations' );

		return $model->hasAttachments( $this->id );
	}

	/**
	 * Determines if the current conversation is writable by the given user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int	$userId		The user's id.
	 * @return	boolean			True if it's new, false otherwise.
	 */
	public function isWritable( $userId = null )
	{
		static $writable 	= array();

		if( $userId == null )
		{
			$userId 	= FD::user()->id;
		}

		if( !isset( $writable[ $userId ] ) )
		{
			$participant	= FD::table( 'ConversationParticipant' );
			$participant->load( array( 'conversation_id' => $this->id , 'user_id' => $userId ) );

			// Default value.
			$writable[ $userId ]	= false;

			// Check if the state is still participating.
			if( $participant->state == SOCIAL_CONVERSATION_STATE_PARTICIPATING )
			{
				$writable[ $userId ]	= true;
			}
		}

		return $writable[ $userId ];
	}

	/**
	 * Determines if the current conversation is readable by the given user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int	$userId		The user's id.
	 * @return	boolean			True if it's new, false otherwise.
	 */
	public function isReadable( $userId )
	{
		$participant	= FD::table( 'ConversationParticipant' );
		$state 			= $participant->load( array( 'conversation_id' => $this->id , 'user_id' => $userId ) );

		if( !$state )
		{
			return false;
		}

		// If there's a participant record, it's definitely readable.
		return true;
	}

	/**
	 * Determines if the current conversation has been read.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int	$userId		The user's id.
	 * @return	boolean			True if it's new, false otherwise.
	 */
	public function isNew( $userId = null )
	{
		$user 	= FD::user( $userId );
		$model 	= FD::model( 'Conversations' );

		return $model->isNew( $this->id , $user->id );
	}

	/**
	 * Retrieves a list of participants in this conversation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array (Optional)	$exclusions		An array of user's node id that should be excluded.
	 *
	 * @return	Array	An array of SocialTablePeople objects containing all the participants.
	 */
	public function getParticipants( $exclusions = array(), $use4send = false )
	{
		static $_cache = array();

		$key = $this->id . '_';
		if (is_array($exclusions)) {
			$key .= implode('_', $exclusions);
		} else {
			$key .= $exclusions;
		}

		if (! isset($_cache[$key])) {

			$model 	= FD::model( 'Conversations' );
			$result	= $model->getParticipants( $this->id , $exclusions, false, $use4send );

			if (! $result) {
				$creator = Foundry::user($this->created_by);
				$result = array($creator);
			}

			$_cache[$key] = $result;
		}

		return $_cache[$key];
	}

	/**
	 * Centralized method to retrieve a conversation's link.
	 * This is where all the magic happens.
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	string	The url for the person
	 */
	public function getPermalink( $xhtml = true , $external = false, $sef = true )
	{
		$url 	= FRoute::conversations( array( 'id' => $this->id , 'layout' => 'read', 'external' => $external, 'sef' => $sef ) , $xhtml );

		return $url;
	}

	/**
	 * Get the participant user id's.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParticipantsId( $exclusions = array() )
	{
		$participants 	= $this->getParticipants( $exclusions );
		$ids 	= array();

		foreach( $participants as $participant )
		{
			$ids[]	= $participant->id;
		}

		return $ids;
	}

	/**
	 * Get's the participant's avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array (Optional)	$exclusions		An array of user's id that should be excluded.
	 * @param	string (Optional)	$avatarSize		The size of the avatar that should be used.
	 *
	 * @return	string								The absolute path to the avatar.
	 */
	public function getParticipantAvatar( $exclusions = array() , $avatarSize = SOCIAL_AVATAR_SMALL )
	{
		$model 	= FD::model( 'Conversations' );
		$user 	= $model->getParticipants( $this->id , $exclusions );

		return $user[0]->getAvatar( $avatarSize );
	}

	/**
	 * Gets the last replier from this discussion.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	$exclusions		A list of user id's that should be excluded.
	 * @return 	SocialUser 				A SocialUser object.
	 */
	public function getLastParticipant( $exclusions = array() )
	{
		$model 			= FD::model( 'Conversations' );
		$participants 	= $model->getParticipants( $this->id , $exclusions );

		if( !is_array( $participants ) )
		{
			return $participants;
		}

		if( count( $participants ) <= 0 )
		{
			return false;
		}

		// Only return the first participant
		return $participants[ 0 ];
	}

	/**
	 * Retrieves the last message for this specific conversation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The current viewer's id.
	 * @return	SocialTableConversationMessage
	 */
	public function getLastMessage($userId)
	{
		static $messages = array();

		$key = $this->id . $userId;

		if (!isset($messages[$key])) {
			$model = FD::model('Conversations');
			$messages[$key] = $model->getLastMessage($this->id, $userId);
		}

		return $messages[$key];
	}

	/**
	 * Override the parent's delete method as we don't really delete the conversation
	 * only when there's no one left in the system, we should delete the conversation.
	 *
	 * @param	int		The nodeid that requested this deletion.
	 * @param
	 * @param	boolean	True on success, false otherwise
	 */
	public function delete($userId = null)
	{
		// Delete all message map for this particular node
		$model = FD::model('Conversations');
		$state = $model->delete($this->id, $userId);

		if (!$state) {
			$this->setError($model->getError());
		}

		return $state;
	}

	/**
	 * Archives the entire conversation for specific node.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$userId		The nodeid that requested this deletion.
	 * @param	boolean	True on success, false otherwise
	 */
	public function archive( $userId )
	{
		return FD::model( 'Conversations' )->archive( $this->id , $userId );
	}

	/**
	 * Unarchives the entire conversation for specific node.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$userId		The user's id that requested to unarchive the conversation.
	 *
	 * @param	boolean	True on success, false otherwise
	 */
	public function unarchive( $userId )
	{
		$model	= FD::model( 'Conversations' );

		return $model->unarchive( $this->id , $userId );
	}

	/**
	 * Responsible to make a user leave the conversation.
	 * No deletion should occur unless there's no more participants at all.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id that request to leave the conversation.
	 * @return	bool	True on success, false otherwise.
	 */
	public function leave( $userId )
	{
		$model 	= FD::model( 'Conversations' );
		$state	= $model->leave( $this->id , $userId );

		if( !$state )
		{
			$this->setError( $model->getError() );
		}

		// @badge: conversation.leave
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'conversation.leave' , $userId , JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_BADGE_LEFT_A_CONVERSATION' ) );

		return $state;
	}

	/**
	 * Mark a particular conversation as read for the specific user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @return	bool	True on success false otherwise.
	 */
	public function markAsRead( $userId )
	{
		$model 	= FD::model( 'Conversations' );

		return $model->markAsRead( $this->id , $userId );
	}

	/**
	 * Mark a particular conversation to new.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			$userId		The user's id.
	 * @return	boolean
	 */
	public function markAsUnread( $userId )
	{
		$model 	= FD::model( 'Conversations' );

		return $model->markAsUnread( $this->id , $userId );
	}

	/**
	 * Adds a participant into an existing conversation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The creator's user id.
	 * @param	int			The recipient's user id.
	 * @return	boolean		True on success, false otherwise.
	 */
	public function addParticipant( $created_by , $userId )
	{
		// Create a new participant record.
		$participant 					= FD::table( 'ConversationParticipant' );

		// Try to load and see if the participant has already been added to the system.
		$participant->load( array( 'user_id' => $userId , 'conversation_id' => $this->id ) );

		$participant->conversation_id 	= $this->id;
		$participant->user_id 			= $userId;
		$participant->state				= SOCIAL_STATE_PUBLISHED;

		// Try to save the participant
		$state 	= $participant->store();

		if( !$state )
		{
			$this->setError( $participant->getError() );

			return $state;
		}

		// @badge: conversation.invite
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'conversation.invite' , $created_by , JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_BADGE_INVITED_USER_TO_CONVERSATION' ) );

		// @points: conversation.invite
		// Assign points when user starts new conversation
		$points = FD::points();
		$points->assign( 'conversation.invite' , 'com_easysocial' , $created_by );

		// Once the participant is created, we need to create a
		// a new conversation message with the type of join so that others would know
		// that a new user is added to the conversation.
		$message 	= FD::table( 'ConversationMessage' );
		$message->conversation_id 	= $this->id;
		$message->message 			= $userId;
		$message->type 				= SOCIAL_CONVERSATION_TYPE_JOIN;
		$message->created_by	 	= $created_by;

		// Try to store the new message
		$state 		= $message->store();

		if( !$state )
		{
			$this->setError( $message->getError() );

			return $state;
		}

		// Get conversation model
		$model 	= FD::model( 'Conversations' );

		// Get existing participants.
		$participants 	= $model->getParticipants( $this->id );

		// Finally, we need to add the message maps
		$model->addMessageMaps( $this->id , $message->id , $participants , $created_by );

		return true;
	}
}
