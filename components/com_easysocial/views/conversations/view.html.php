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

// Import main view
FD::import( 'site:/views/views' );

class EasySocialViewConversations extends EasySocialSiteView
{
	/**
	 * Checks if the conversation system is enabled.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFeatureEnabled()
	{
		$config 	= FD::config();

		$state 		= $config->get( 'conversations.enabled' );

		if( !$state )
		{
			FD::info()->set( JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_NOT_ENABLED' ) , SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}
	}

	/**
	 * Displays a list of conversations for a particular user. This is the default view of the conversations.
	 *
	 * @access	public
	 * @param	string	The name of the template file to parse; automatically searches through the template paths.
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		// We know for user that the guest cannot access conversations.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		$my				= FD::user();
		$config 		= FD::config();

		$options 		= array(
								'sorting'	=> $this->themeConfig->get( 'conversation_sorting' ),
								'ordering'	=> $this->themeConfig->get( 'conversation_ordering' ),
								'limit'		=> $this->themeConfig->get( 'conversation_limit' )
								);

		$filter 	= JRequest::getWord( 'filter', '' );
		if( $filter == 'all' )
		{
			$filter = '';
		}

		if( $filter )
		{
			$options[ 'filter' ]	= $filter;
		}

		// Load the conversation model.
		$model 			= FD::model( 'Conversations' );

		$conversations	= $model->getConversations( $my->id , $options );
		$pagination		= $model->getPagination();

		// Try to see if there's any new incoming conversation.
		$totalNewInbox 	= $model->getNewCount( $my->id , 'inbox' );

		// Set the page title
		$title 			= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_CONVERSATIONS_INBOX' );

		// If there's new notifications, we would want to show the new count in the browser's title.
		if( $totalNewInbox > 0 )
		{
			$title 		= $title . ' (' . $totalNewInbox . ')';
		}
		FD::page()->title( $title );

		// Set breadcrumbs
		FD::page()->breadcrumb( $title );

		// Check for new items in archives.
		$totalNewArchives	= $model->getNewCount( $my->id , 'archives' );

		$this->set( 'totalNewInbox'		, $totalNewInbox );
		$this->set( 'totalNewArchives'	, $totalNewArchives );
		$this->set( 'active' 			, 'inbox' );
		$this->set( 'conversations' 	, $conversations );
		$this->set( 'pagination'		, $pagination );
		$this->set( 'filter'			, $filter );

		echo parent::display( 'site/conversations/default' );
	}


	/**
	 * Archives layout displays all conversations that are archived.
	 *
	 * @param	null
	 */
	public function archives()
	{
		// We know for user that the guest cannot access conversations.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		$my				= FD::user();
		$config 		= FD::config();

		$options 		= array(
								'sorting'	=> $this->themeConfig->get( 'conversation_sorting' ),
								'ordering'	=> $this->themeConfig->get( 'conversation_ordering' ),
								'limit'		=> $this->themeConfig->get( 'conversation_limit' ),
								'archives'	=> true
								);

		// Load the conversation model.
		$model 			= FD::model( 'Conversations' );
		$conversations	= $model->getConversations( $my->id , $options );
		$pagination		= $model->getPagination();
		$filter 		= JRequest::getWord( 'filter', '' );

		// Push conversations to the theme file
		$this->set( 'conversations' , $conversations );

		// Try to see if there's any new incoming conversation.
		$totalNewInbox 	= $model->getNewCount( $my->id , 'inbox' );

		// Check for new items in archives.
		$totalNewArchives	= $model->getNewCount( $my->id , 'archives' );

		// Set the page title
		$title 			= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_CONVERSATIONS_ARCHIVES' );

		// If there's new notifications, we would want to show the new count in the browser's title.
		if( $totalNewArchives > 0 )
		{
			$title 		= $title . ' (' . $totalNewArchives . ')';
		}

		FD::page()->title( $title );

		// Set breadcrumbs
		FD::page()->breadcrumb( $title );

		$this->set( 'totalNewInbox'	, $totalNewInbox );
		$this->set( 'totalNewArchives'	, $totalNewArchives );

		// Set the current active item.
		$this->set( 'filter'		, $filter );
		$this->set( 'active'		, 'archives' );
		$this->set( 'pagination'	, $pagination );

		echo parent::display( 'site/conversations/default' );
	}

	/**
	 * Displays a list of unread conversations for a particular user.
	 *
	 * @param	null
	 * @return	null
	 */
	public function unread()
	{
		// We know for user that the guest cannot access conversations.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		$my 		= FD::user();
		$config 	= FD::config();
		$options	= array(
								'sorting'	=> $config->get( 'conversations.list.sorting' ) ,
								'ordering'	=> $config->get( 'conversations.list.ordering' ) ,
								'filter'	=> 'unread'
							);

		$model 			= FD::model( 'Conversations' );
		$conversations	= $model->getConversations( $my->get( 'node_id' ) , $options );
		$pagination		= $model->getPagination();

		$this->set( 'conversations' , $conversations );
		$this->set( 'pagination'	, $pagination );

		echo parent::display( 'site/conversations/conversations' );
	}

	/**
	 * Processes after the unread task is called.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function markUnread()
	{
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		$url 	= FRoute::conversations( array() , false );

		return $this->redirect( $url );
	}

	/**
	 * This method is invoked when a conversation is created.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store( $conversation = null )
	{
		$info 	= FD::info();

		// var_dump( $this->getMessage() );exit;
		$info->set( $this->getMessage() );

		if( $this->hasErrors() )
		{
			return $this->compose();
		}

		$this->redirect( FRoute::conversations( array( 'id' => $conversation->id , 'layout' => 'read' ) , false ) );
	}

	/**
	 * Displays the compose conversation view.
	 *
	 * @access	public
	 * @param	$conversation
	 * @return	null
	 */
	public function compose( $conversation = null )
	{
		// @task: Prevent unauthorized access.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Check if user has access to create new conversations
		$access 		= FD::access();

		if( !$access->allowed( 'conversations.create' ) )
		{
			$this->setMessage( JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ERROR_NOT_ALLOWED' ) , SOCIAL_MSG_ERROR );

			FD::info()->set( $this->getMessage() );

			return $this->redirect( FRoute::conversations( array() , false ) );
		}

		// Set the page title
		$title 			= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_CONVERSATIONS_COMPOSE' );
		FD::page()->title( $title );

		// Set breadcrumbs
		FD::page()->breadcrumb( $title );

		// Get current logged in user.
		$my 			= FD::user();

		// There could be errors on the form, we need to reset the message
		$message 		= JRequest::getVar( 'message' , '' );

		// Get a list of friend list from the current user.
		$listModel 		= FD::model( 'Lists' );
		$lists 			= $listModel->getLists( array( 'user_id' => $my->id ) );

		$this->set( 'lists'		, $lists );
		$this->set( 'message' 	, $message );

		echo parent::display( 'site/conversations/compose' );
	}

	/**
	 * When a conversation is marked as read.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function read()
	{
		// Prevent unauthorized access.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the conversation id
		$id = $this->input->get('id', 0, 'int');
		$conversation = FD::table('Conversation');
		$loaded = $conversation->load($id);

		// Check if the conversation id provided is valid.
		if (!$id || !$loaded) {
			$this->info->set(JText::_('COM_EASYSOCIAL_CONVERSATIONS_ERROR_INVALID_ID'), SOCIAL_MSG_ERROR);

			$url = FRoute::conversations(array(), false);

			return $this->redirect($url);
		}

		// Check if the user has access to read this discussion.
		if (!$conversation->isReadable($this->my->id)) {
			$this->info->set(JText::_('COM_EASYSOCIAL_CONVERSATIONS_NOT_ALLOWED_TO_READ'), SOCIAL_MSG_ERROR);

			$url = FRoute::conversations(array(), false);

			return $this->redirect($url);
		}

		// Retrieve conversations model.
		$model = FD::model('Conversations');

		// Always reset the limistart to 0 so that when the page refresh, system will not get the 'previous' saved limitstart.
		$model->setState('limitstart', 0);

		// Get list of files in this conversation
		$filesModel = FD::model('Files');

		// Get a list of all the message ids from this conversation.
		$files = $filesModel->getFiles($model->getMessageIds($conversation->id), SOCIAL_TYPE_CONVERSATIONS);

		// Get a list of participants for this particular conversation except myself.
		$participants = $model->getParticipants($conversation->id);

		// this flag is to indicate if there is only one participant and the participant is a ESAD.
		$isESADuser = false;

		if (count($participants) == 2) {
			foreach($participants as $pUser) {
				if ($pUser->id != $this->my->id && !$pUser->hasCommunityAccess()) {
					$isESADuser = true;
				}
			}
		}

		// Fetch a list of messages for this particular conversation
		$messages = $model->setLimit($this->themeConfig->get('messages_limit'))->getMessages($conversation->id, $this->my->id);

		// Beautify the names
		$participantNames = FD::string()->namesToStream($participants, false, 3, false);

		$title = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_CONVERSATIONS_READ', $participantNames);

		// Set title
		FD::page()->title($title);

		// Set breadcrumbs
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_CONVERSATIONS_INBOX'), FRoute::conversations());
		FD::page()->breadcrumb($title);

		// @trigger: onPrepareConversations
		$dispatcher = FD::dispatcher();
		$args = array(&$messages);

		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onPrepareConversations', $args);

		// Get pagination
		$pagination = $model->getPagination();

		// Determine if load previous messages should appear.
		$loadPrevious = $pagination->total > $pagination->limit;

		// Mark conversation as read because the viewer is already reading the conversation.
		$conversation->markAsRead($this->my->id);

		// Get total number of messages sent today
		$totalSentDaily	= $model->getTotalSentDaily($this->my->id);

		// @points: conversation.read
		// Assign points when user reads a conversation
		$points = FD::points();
		$points->assign('conversation.read', 'com_easysocial', $this->my->id);

		$this->set('files', $files);
		$this->set('totalSentDaily', $totalSentDaily);
		$this->set('loadPrevious', $loadPrevious);
		$this->set('conversation', $conversation);
		$this->set('participants', $participants);
		$this->set('messages', $messages);
		$this->set('pagination', $pagination);
		$this->set('isESADuser', $isESADuser);

		echo parent::display('site/conversations/read');
	}


	/**
	 * Responsible to make the appropriate redirect calls after a conversation is unarchived.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unarchive()
	{
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		$url 	= FRoute::conversations( array() , false );
		return $this->redirect( $url );
	}

	/**
	 * Determines what should be done after the conversation is deleted
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function archive()
	{
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		$url 	= FRoute::conversations( array() , false );
		return $this->redirect( $url );
	}

	/**
	 * Determines what should be done after the conversation is deleted
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete()
	{
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		$url 	= FRoute::conversations( array() , false );
		return $this->redirect( $url );
	}

	/**
	 * Determins what should be done after a participant is added into the conversation.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function addParticipant( SocialTableConversation $conversation )
	{
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		$url 	= FRoute::conversations( array( 'id' => $conversation->id , 'layout' => 'read' ) , false );
		return $this->redirect( $url );
	}

	/**
	 * Allows viewer to download a conversation file
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function download()
	{
		// Currently only registered users are allowed to view a file.
		FD::requireLogin();

		// Get the file id from the request
		$fileId 	= JRequest::getInt( 'fileid' , null );

		$file 	= FD::table( 'File' );
		$file->load( $fileId );

		if( !$file->id || !$fileId )
		{
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}

		// Load up the conversation message
		$message 		= FD::table( 'ConversationMessage' );
		$message->load( $file->uid );

		// Something went wrong with this discussion as it doesn't have participants
		if( !$message->id )
		{
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}

		$conversation 	= FD::table( 'Conversation' );
		$conversation->load( $message->conversation_id );

		// Something went wrong with this discussion as it doesn't have participants
		if( !$conversation->id )
		{
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}

		// Check if viewer is a participant
		if (!$conversation->isParticipant($this->my->id)) {
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}

		$file->download();
		exit;
	}

	/**
	 * Allows viewer to download a conversation file
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		// Currently only registered users are allowed to view a file.
		FD::requireLogin();

		// Get the file id from the request
		$fileId 	= JRequest::getInt( 'fileid' , null );

		$file 	= FD::table( 'File' );
		$file->load( $fileId );

		if( !$file->id || !$fileId )
		{
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}

		// Load up the conversation message
		$message 		= FD::table( 'ConversationMessage' );
		$message->load( $file->uid );

		// Something went wrong with this discussion as it doesn't have participants
		if( !$message->id )
		{
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}

		$conversation 	= FD::table( 'Conversation' );
		$conversation->load( $message->conversation_id );

		// Something went wrong with this discussion as it doesn't have participants
		if( !$conversation->id )
		{
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}

		// Get the current viewer
		$my		= FD::user();

		// Check if viewer is a participant
		if( !$conversation->isParticipant( $my->id ) )
		{
			// Throw error message here.
			$this->redirect( FRoute::dashboard( array() , false ) );
			$this->close();
		}


		$file->preview();
		exit;
	}

	/**
	 * Post processing after leaving a conversation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function leave()
	{
		return $this->redirect( FRoute::conversations( array() , false ) );
	}
}
