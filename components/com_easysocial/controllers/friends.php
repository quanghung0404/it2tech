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

// Import parent controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerFriends extends EasySocialController
{
	protected $app	= null;

	/**
	 * Allows caller to invite other users
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sendInvites()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that the user is logged in
		FD::requireLogin();

		// Get the current view
		$view = $this->getCurrentView();

		// Get the list of emails
		$emails = $this->input->get('emails', '', 'html');

		if (!$emails) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_INVITE_PLEASE_ENTER_EMAILS'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		$emails = explode("\n", $emails);

		// Get the message
		$message = $this->input->get('message', '', 'default');

		foreach ($emails as $email) {
			$table = FD::table('FriendInvite');

			// Check if this email has been invited by this user before
			$table->load(array('email' => $email, 'user_id' => $this->my->id));

			// Skip this if the user has already been invited before.
			if ($table->id) {
				continue;
			}

			$table->email = $email;
			$table->user_id = $this->my->id;
			$table->message = $message;

			$table->store();
		}

		$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_INVITE_SENT_INVITATIONS'), SOCIAL_MSG_SUCCESS);
		return $view->call(__FUNCTION__);
	}

	/**
	 * Gets a list of users from a particular list.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getListFriends()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in
		FD::requireLogin();

		// Get current view.
		$view 	= FD::view( 'Friends' , false );

		// Check if friends lists are enabled.
		$config 	= FD::config();
		if( !$config->get( 'friends.list.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DISABLED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get current id.
		$id 	= JRequest::getInt( 'id' , 0 );

		// Try to load the list.
		$list 	= FD::table( 'List' );
		$state 	= $list->load( $id );

		if (!$id || !$state) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_INVALID_LIST_ID' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $list , array() );
		}

		$limit 		= FD::themes()->getConfig()->get( 'friendslimit' , 20 );
		$options 	= array( 'limit' => $limit , 'list_id' => $list->id );

		// Get list members
		$model 		= FD::model( 'Friends' );
		$members 	= $model->getFriends( $list->user_id , $options );

		// Get the pagination
		$pagination	= $model->getPagination();

		// Set additional vars for the pagination
		$pagination->setVar( 'view' 	, 'friends' );
		$pagination->setVar( 'filter' 	, 'list' );
		$pagination->setVar( 'id'		, $list->id );

		return $view->call( __FUNCTION__ , $list , $members , $pagination );
	}

	/**
	 * Allows caller to set a friend list as the default list.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function setDefault()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that user is logged in.
		FD::requireLogin();

		$my 	= FD::user();
		$view 	= $this->getCurrentView();
		$config	= FD::config();

		if( !$config->get( 'friends.list.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DISABLED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the list id.
		$id 	= JRequest::getInt( 'id' );

		$list 	= FD::table( 'List' );
		$list->load( $id );

		if( !$id || !$list->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_LISTS_ERROR_LIST_INVALID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Check if the user owns this list item.
		if( !$list->isOwner() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_LISTS_ERROR_LIST_IS_NOT_OWNED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Set the list as default
		$state 	= $list->setDefault();

		if( !$state )
		{
			$view->setMessage( $list->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Creates a new friend list.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function storeList()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Check if user is valid.
		FD::requireLogin();

		// Check if friends list is enabled
		$config = FD::config();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Get current logged in user.
		$my 	= FD::user();

		// @friends.list.enabled
		// Check if the friend list feature is enabled
		if( !$config->get( 'friends.list.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DISABLED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get post data.
		$data	= JRequest::get( 'POST' );

		// Detect if this is an edited list or a new list
		$id  	= JRequest::getVar( 'id' );

		// Generate a new list.
		$list 	= FD::table( 'List' );

		// Get the access
		$access	= FD::access();

		// @access friends.list.enabled
		// Check if the user is allowed to create friend lists
		if( !$access->allowed( 'friends.list.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LISTS_ACCESS_NOT_ALLOWED' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Check for title
		if (empty($data['title'])) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_LISTS_TITLE_REQUIRED'), SOCIAL_MSG_ERROR);
			return $view->call('listForm');
		}

		if( !empty( $id ) )
		{
			$list->load( $id );
		}
		else
		{
			// This will be a new friend list, check if the user has already reached the limit
			$listModel 			= FD::model( 'Lists' );

			// Get the total friends list a user has
			$totalFriendsList	= $listModel->getTotalLists( $my->id );

			if( $access->exceeded( 'friends.list.limit' , $totalFriendsList ) )
			{
				$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LISTS_ACCESS_LIMIT_EXCEEDED' ) );
				return $view->call( __FUNCTION__ );
			}
		}

		// Bind the list with the posted data.
		$list->bind( $data );

		// Check if the user owns this list item.
		if( !empty( $list->id ) && $my->id != $list->user_id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_LISTS_ERROR_LIST_IS_NOT_OWNED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Set the creator of the list.
		$list->user_id 	= $my->id;

		// Prepare the dispatcher
		FD::apps()->load( SOCIAL_TYPE_USER );
		$dispatcher		= FD::dispatcher();
		$args 			= array( &$list );

		// @trigger: onFriendListBeforeSave
		$dispatcher->trigger( SOCIAL_TYPE_USER , 'onFriendListBeforeSave' , $args );

		// Try to store the list.
		$state 	= $list->store();

		// @trigger: onFriendListBeforeSave
		$dispatcher->trigger( SOCIAL_TYPE_USER , 'onFriendListAfterSave' , $args );

		if (!$state) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_LISTS_ERROR_CREATING_LIST' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $list );
		}

		// Get friends from this list
		$friends 	= JRequest::getVar( 'uid' );

		// Assign these friends into the list.
		$list->addFriends( $friends );

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_LISTS_CREATED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $list );
	}


	/**
	 * Gets a list of friend lists.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getLists()
	{
		// // Check for request forgeries.
		// FD::checkToken();

		// // Ensure that only valid user is allowed.
		// FD::requireLogin();

		// // Get current logged in user.
		// $my 	= FD::user();

		// // Get the current limitstart
		// $limitstart = JRequest::getInt( 'limitstart' , 0 );

		// // Get current view.
		// $view 	= $this->getCurrentView();

		// // Check if friends lists are enabled.
		// $config 	= FD::config();

		// if( !$config->get( 'friends.list.enabled' ) )
		// {
		// 	$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DISABLED' ) , SOCIAL_MSG_ERROR );
		// 	return $view->call( __FUNCTION__ );
		// }

		// // Get lists model.
		// $model 	= FD::model( 'Lists' );

		// // Get lists
		// $model->setState( 'limitstart' , $limitstart );
		// $lists 	= $model->getLists( array( 'user_id' => $my->id ) );

		// // Pass the lists to the view for processing.
		// return $view->call( __FUNCTION__ , $lists );
	}

	/**
	 * Retrieve the counts
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getCounters()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in
		FD::requireLogin();

		// Get current view.
		$view 		= $this->getCurrentView();
		$my 		= FD::user();

		// Get the friends model
		$model 			= FD::model( 'Friends' );

		// Set the total friends.
		$totalFriends 	= $model->getTotalFriends( $my->id , array( 'state' => SOCIAL_FRIENDS_STATE_FRIENDS ) );

		// Get the total pending friends
		$totalPendingFriends 	= $model->getTotalPendingFriends( $my->id );

		// Get the total request made
		$totalRequestSent 		= $model->getTotalRequestSent( $my->id );

		// Get the suggestion count.
		$totalSuggest			= $model->getSuggestedFriends( $my->id, null, true );

		return $view->call( __FUNCTION__ , $totalFriends , $totalPendingFriends , $totalRequestSent , $totalSuggest );
	}

	/**
	 * Gets all the count of the user's friend lists.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getListCounts()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in
		FD::requireLogin();

		// Get current view.
		$view 		= $this->getCurrentView();

		// Check if friends lists are enabled.
		$config 	= FD::config();

		if( !$config->get( 'friends.list.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DISABLED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get current logged in user.
		$my 	= FD::user();

		$model 	= FD::model( 'Lists' );
		$lists	= $model->getLists( array( 'user_id' => $my->id ) );

		return $view->call( __FUNCTION__ , $lists );
	}

	/**
	 * Adds a list of user into a friend list.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function assign()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user needs to be logged in.
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Check if friends lists are enabled.
		$config 	= FD::config();

		if( !$config->get( 'friends.list.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DISABLED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get list of user id's.
		$ids 	= JRequest::getVar( 'uid' );
		$ids 	= FD::makeArray( $ids );

		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_PLEASE_ENTER_NAMES' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get current logged in user.
		$my 	= FD::user();

		// Get the list id.
		$listId = JRequest::getInt( 'listId' );

		$list 	= FD::table( 'List' );
		$list->load( $listId );

		if( !$listId || !$list->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_INVALID_LIST_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// We need to run some tests to see if the user tries to add a user that is not their friend.
		$friendsModel 	= FD::model( 'Friends' );

		foreach( $ids as $id )
		{
			if( !$friendsModel->isFriends( $my->id , $id ) )
			{
				$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_NOT_FRIEND_YET' ) , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}

			if( $list->mapExists( $id ) )
			{
				$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_FRIEND_ALREADY_IN_LIST' ) , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}
		}

		// User needs to own this list.
		if( !$list->isOwner() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_NOT_OWNER' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$users 	= FD::user( $ids );

		// Add the user to the list.
		$list->addFriends( $ids );

		return $view->call( __FUNCTION__ , $users );
	}

	/**
	 * Suggest a list of friend names for a user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 *
	 */
	public function filter()
	{
		// Check for valid tokens.
		FD::checkToken();

		// Check for valid user.
		FD::requireLogin();

		// Load friends model.
		$model = FD::model( 'Friends' );

		// Load the view.
		$view = $this->getCurrentView();

		// Get the filter types.
		$type = $this->input->get('filter', 'all', 'cmd');
		$userId = $this->input->get('userid', null, 'int');

		$user = FD::user($userId);
		$my   = FD::user();

		$friends 	= array();

		$limit 		= FD::themes()->getConfig()->get( 'friendslimit' , 20 );
		$options 	= array( 'limit' => $limit );
		$userAlias	= $user->getAlias();

		if ($type == 'pending') {
			$options[ 'state' ]	= SOCIAL_FRIENDS_STATE_PENDING;
			$friends 	= $model->getFriends( $user->id , $options );
		}

		if ($type == 'all') {
			$options[ 'state' ]	= SOCIAL_FRIENDS_STATE_FRIENDS;
			$friends 	= $model->getFriends($user->id , $options);
		}

		if ($type == 'mutual') {
			$friends 	= $model->getMutualFriends( $my->id, $user->id, $limit );
		}

		if ($type == 'suggest') {
			$friends 	= $model->getSuggestedFriends($my->id, $limit);
			$userAlias	= $my->getAlias();
		}

		if ($type == 'request') {
			$options[ 'state' ]		= SOCIAL_FRIENDS_STATE_PENDING;
			$options[ 'isRequest' ]	= true;

			$friends 	= $model->getFriends($user->id , $options);
		}

		if ($type == 'invites') {
			$friends = $model->getInvitedUsers($user->id);
		}

		// Get the pagination
		$pagination	= $model->getPagination();

		// Set additional vars for the pagination
		$itemId = FRoute::getItemId('friends', array('filter' => $type));

		$pagination->setVar('Itemid', $itemId);
		$pagination->setVar('view', 'friends');

		$addUserAlias = $type != 'suggest';

		if ($type == 'all' && $user->id == $my->id) {
			$addUserAlias = false;
		}

		if ($addUserAlias) {
			$pagination->setVar('userid', $userAlias);
		}

		return $view->call(__FUNCTION__, $type, $friends, $pagination);
	}

	/**
	 * Suggest a list of friend names for a user in photo tagging.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function suggestPhotoTagging()
	{
		$this->suggest( 'photos.tagme' );
	}

	/**
	 * Suggest a list of friend names for a user or a friend list.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function suggestWithList( $privacy = null )
	{
		// Check for valid tokens.
		FD::checkToken();

		// Only valid registered user has friends.
		FD::requireLogin();

		$my 		= FD::user();

		// Load friends model.
		$model 		= FD::model( 'Friends' );

		// Load the view.
		$view 		= $this->getCurrentView();

		// Properties
		$search  = $this->input->get('search', '', 'default');
		$exclude = $this->input->get('exclude', '', 'default');
		$includeme = $this->input->get('includeme', 0, 'default');
		$showNonFriend = $this->input->get('showNonFriend', 0, 'int');


		// Determine what type of string we should search for.
		$config 	= FD::config();
		$type 		= $config->get( 'users.displayName' );

		//check if we need to apply privacy or not.
		$options = array();

		if ($privacy) {
			$options['privacy'] = $privacy;
		}

		if ($exclude) {
			$options[ 'exclude' ] = $exclude;
		}


		if ($includeme) {
			$options[ 'includeme' ] = $includeme;
		}

		if ($showNonFriend) {
			$options[ 'everyone' ] = true;
		}

		// Try to get the search result.
		$friends		= $model->search( $my->id , $search , $type, $options);

		// Try to search a list of friends
		$listModel		= FD::model( 'Lists' );
		$friendsList	= $listModel->search( $my->id , $search );

		return $view->call( __FUNCTION__ , $friends , $friendsList );
	}

	/**
	 * Suggest a list of friend names for a user.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function suggest($privacy = null)
	{
		// Check for valid tokens.
		ES::checkToken();

		// Only valid registered user has friends.
		ES::requireLogin();

		// Load friends model.
		$model = ES::model('Friends');

		// Properties
		$search  = $this->input->get('search', '', 'default');
		$exclude = $this->input->get('exclude', '', 'default');
		$includeme = $this->input->get('includeme', 0, 'default');
		$showNonFriend = $this->input->get('showNonFriend', 0, 'int');

		// Determine what type of string we should search for.
		$type = $this->config->get('users.displayName');

		//check if we need to apply privacy or not.
		$options = array();

		if ($privacy) {
			$options['privacy'] = $privacy;
		}

		if ($exclude) {
			$options['exclude'] = $exclude;
		}

		if ($includeme) {
			$options['includeme'] = $includeme;
		}

		if ($showNonFriend) {
			$options[ 'everyone' ] = true;
		}

		// Determine if we should search all users on the site
		$searchType = $this->input->get('type', '', 'cmd');

		if ($searchType == 'invitegroup' && $this->config->get('groups.invite.nonfriends')) {
			$options['everyone'] = true;
		}

		if ($searchType == 'inviteevent' && $this->config->get('events.invite.nonfriends')) {
			$options['everyone'] = true;
		}

		// Try to get the search result.
		$result = $model->search($this->my->id , $search , $type, $options);

		return $this->view->call(__FUNCTION__, $result);
	}

	/**
	 * Creates a new friend request to a target
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function request()
	{
		// Check for request forgeries.
		FD::checkToken();

		// User needs to be logged in
		FD::requireLogin();

		// Get the target user that is being added.
		$id = $this->input->get('id', 0, 'int');
		$user = FD::user($id);

		// Get the appropriate callback
		$allowedCallbacks = array(__FUNCTION__, 'usersRequest', 'popboxRequest');

		// Get the current callback
		$callback = $this->input->get('viewCallback', __FUNCTION__, 'cmd');

		if (!in_array($callback, $allowedCallbacks)) {
			$callback = __FUNCTION__;
		}

		// @TODO: Check if target user blocks this.

		// If the user doesn't exist;
		if (!$user || !$id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_UNABLE_TO_LOCATE_USER'));

			return $this->view->call($callback);
		}

		// Get the current viewer.
		$my = FD::user();

		// Determine that the user did not exceed their friend usage.
		if ($my->exceededFriendLimit()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_EXCEEDED_LIMIT'), SOCIAL_MSG_ERROR);
			return $this->view->call($callback);
		}

		// Load up the model to check if they are already friends.
		$model = FD::model('Friends');
		$friend = $model->request($my->id, $user->id);

		if ($friend === false) {

			// There is a possibility that it already requested initially
			$friend = FD::table('Friend');
			$friend->loadByUser($my->id, $user->id);

			$this->view->setMessage($model->getError(), SOCIAL_MSG_ERROR);
		}

		return $this->view->call($callback, $friend);
	}

	/**
	 * Approves a friend request
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function approve()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Do not allow non registered user access
		FD::requireLogin();

		// Get the connection id.
		$id		= JRequest::getInt( 'id' );

		// Get the current user.
		$my 	= FD::user();

		// Get the view.
		$view 	= $this->getCurrentView();

		// Try to load up the friend table
		$friend	= FD::table( 'Friend' );

		// Load the connection.
		if (!$friend->load($id)) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_ERROR_INVALID_ID') , SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__ , $friend);
		}

		// Get the person that initiated the friend request.
		$actor 	= FD::user($friend->actor_id);

		// Test if the target is really the current user.
		if ($friend->target_id != $my->id) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_ERROR_NOT_YOUR_REQUEST'), SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__, $friend);
		}

		// Try to approve the request.
		if (!$friend->approve()) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_ERROR_APPROVING_REQUEST'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__, $friend);
		}

		// Prepare the dispatcher
		FD::apps()->load(SOCIAL_TYPE_USER);

		$dispatcher		= FD::dispatcher();
		$args 			= array( &$friend );

		// @trigger: onFriendApproved
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onFriendApproved', $args);

		$message 		= JText::sprintf('COM_EASYSOCIAL_FRIENDS_NOW_FRIENDS_WITH', $actor->getName());
		$view->setMessage($message , SOCIAL_MSG_SUCCESS );

		$callback 			= JRequest::getVar( 'viewCallback' , __FUNCTION__ );
		$allowedCallbacks	= array( __FUNCTION__ , 'notificationsApprove' );

		if( !in_array( $callback , $allowedCallbacks ) )
		{
			$callback 	= __FUNCTION__;
		}

		return $view->call( $callback  , $friend );
	}

	/**
	 * Cancels a friend request.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function cancelRequest()
	{
		// Check for request forgeries
		FD::checkToken();

		// Guests shouldn't be here.
		FD::requireLogin();

		// Get the current logged in user.
		$my 	= FD::user();

		// Get the current view.
		$view 	= FD::view( 'Friends' , false );

		// Get the friend id.
		$id 	= JRequest::getInt( 'id' );

		// Get the model
		$friends	= FD::model( 'Friends' );

		$table 		= FD::table( 'Friend' );
		$table->load( $id );

		if( !$id || !$table->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Check if the user is allowed to cancel the request.
		if( !$table->isInitiator() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_NOT_ALLOWED_TO_CANCEL_REQUEST' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Try to cancel the request.
		$state 		= $friends->cancel( $id );

		if( !$state )
		{
			$view->setMessage( $friends->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Prepare the dispatcher
		FD::apps()->load( SOCIAL_TYPE_USER );
		$dispatcher		= FD::dispatcher();
		$args 			= array( &$table );

		// @trigger: onFriendCancelRequest
		$dispatcher->trigger( SOCIAL_TYPE_USER , 'onFriendCancelRequest' , $args );

		return $view->call( __FUNCTION__ , $id );
	}

	/**
	 * Rejects a friend request
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function reject()
	{
		// Check for request forgeries
		FD::checkToken();

		// Guests shouldn't be able to view this.
		FD::requireLogin();

		// Get current logged in user.
		$my 	= FD::user();

		// Get the friend id.
		$id 	= JRequest::getInt( 'id' );

		// Get the current view
		$view	= $this->getCurrentView();

		// Try to load up the friend table
		$friend	= FD::table( 'Friend' );

		if (!$friend->load($id) || !$id) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_ERROR_INVALID_ID' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Test if the target is really the current user.
		if ($friend->target_id != $my->id) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_ERROR_NOT_YOUR_REQUEST'), SOCIAL_MSG_ERROR);

			return $view->call( __FUNCTION__ );
		}

		// @task: Run approval
		if (!$friend->reject()) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_FRIENDS_ERROR_REJECTING_REQUEST'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// Prepare the dispatcher
		FD::apps()->load( SOCIAL_TYPE_USER );
		$dispatcher		= FD::dispatcher();
		$args 			= array( &$friend );

		// @trigger: onFriendReject
		$dispatcher->trigger( SOCIAL_TYPE_USER , 'onFriendReject' , $args );


		return $view->call( __FUNCTION__ );
	}

	/**
	 * Removes a user from the friend list.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeFromList()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Only logged in users can add users to their list.
		FD::requireLogin();

		// Get current logged in user.
		$my 		= JFactory::getUser();

		// Get current view.
		$view 		= $this->getCurrentView();

		// Get the user that's being removed from the list.
		$userId 	= JRequest::getInt( 'userId' );

		// Get the current list id.
		$listId 	= JRequest::getInt( 'listId' );

		// Try to load the list now.
		$list 		= FD::table( 'List' );
		$state 		= $list->load( $listId );

		if( !$listId || !$state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_INVALID_LIST_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Check if the list is owned by the current user.
		if( !$list->isOwner() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_NOT_OWNER' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Try to delete the item from the list.
		$state 	= $list->deleteItem( $userId );

		if( !$state )
		{
			$view->setMessage( $list->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Removes a friend
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfriend()
	{
		// Check for request forgeries
		FD::checkToken();

		// User needs to be logged in.
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Get the target user that will be removed.
		$id		= JRequest::getInt( 'id' );

		// Get the current user.
		$my 	= FD::user();

		// Try to load up the friend table
		$friend	= FD::table( 'Friend' );
		$state 	= $friend->load( $id );

		if( !$state || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Need to ensure that the target or source of the friend belongs to the current user.
		if( $friend->actor_id != $my->id && $friend->target_id != $my->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_ERROR_NOT_YOUR_FRIEND' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Throw errors when there's a problem removing the friends
		if( !$friend->unfriend( $my->id ) )
		{
			$view->setMessage( $friend->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Prepare the dispatcher
		FD::apps()->load( SOCIAL_TYPE_USER );
		$dispatcher		= FD::dispatcher();
		$args 			= array( &$friend );

		// @trigger: onFriendRemoved
		$dispatcher->trigger( SOCIAL_TYPE_USER , 'onFriendRemoved' , $args );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Deletes a friend list from the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function deleteList()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Only logged in users can try to delete the friend list.
		FD::requireLogin();

		// Get the view.
		$view 	= $this->getCurrentView();

		// Lets get some of the information that we need.
		$my 	= FD::user();

		// Get the list id.
		$id 	= JRequest::getInt( 'id' );

		// Try to load the list.
		$list 	= FD::table( 'List' );
		$list->load( $id );

		// Test if the id provided is valid.
		if( !$list->id || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_LISTS_ERROR_LIST_INVALID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $list );
		}

		// Test if the owner of the list matches.
		if( !$list->isOwner() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_LISTS_ERROR_LIST_IS_NOT_OWNED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $list );
		}

		// Try to delete the list.
		$state 	= $list->delete();

		if( !$state )
		{
			$view->setMessage( $list->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $list );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DELETE_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $list );
	}
}
