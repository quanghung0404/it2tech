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

// Include main view file.
FD::import( 'site:/views/views' );

/**
 * Friend's view.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class EasySocialViewFriends extends EasySocialSiteView
{
	/**
	 * Default method to display a list of friends a user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display($tpl = null)
	{
		// User needs to be logged in to access this page.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Check if there's an id.
		// Okay, we need to use getInt to prevent someone inject invalid data from the url. just do another checking later.
		$id = $this->input->get('userid', null, 'int');

		// This is to ensure that the id != 0. if 0, we set to null to get the current user.
		if (empty($id)) {
			$id = null;
		}

		// Get the user.
		$user = FD::user($id);

		// Get the current logged in user.
		$my = FD::user();

		// Get user's privacy
		$privacy = FD::privacy($my->id);

		// Let's test if the current viewer is allowed to view this profile.
		if ($my->id != $user->id && !$privacy->validate('friends.view', $user->id)) {
			FD::showNoAccess( JText::_( 'COM_EASYSOCIAL_FRIENDS_NOT_ALLOWED_TO_VIEW' ) );
			return;
		}

		// lets check if this user is a ESAD user or not
		if (! $user->hasCommunityAccess()) {

			$template 	= 'site/friends/restricted';
			$this->set('user' , $user );

			parent::display($template);
			return;
		}


		// Get the list of friends this user has.
		$model = FD::model('Friends');
		$limit = FD::themes()->getConfig()->get('friendslimit', 20);

		// Initialize default states
		$options = array('state' => SOCIAL_FRIENDS_STATE_FRIENDS , 'limit' => $limit);

		// By default the view is "All Friends"
		$filter = $this->input->get('filter', 'all', 'cmd');

		// If current view is pending, we need to only get pending friends.
		if ($filter == 'pending') {
			$options['state'] 	= SOCIAL_FRIENDS_STATE_PENDING;
		}

		if ($filter == 'request') {
			$options['state'] = SOCIAL_FRIENDS_STATE_PENDING;
			$options['isRequest'] = true;
		}

		// Detect if list id is provided.
		$listId = $this->input->get('listId', 0, 'int');

		// Get the active list
		$activeList = FD::table('List');
		$activeList->load($listId);

		// Check if list id is provided.
		$filter 	= $listId ? 'list' : $filter;

		if ($activeList->id) {
			$options['list_id']	= $activeList->id;
		}

		// Get the list of lists the user has.
		$listModel = FD::model('Lists');

		// Only fetch x amount of list to be shown by default.
		$limit = FD::config()->get('lists.display.limit', 5);

		// Get the list items.
		$lists = $listModel->getLists(array('user_id' => $user->id));

		$totalPendingFriends = $model->getTotalPendingFriends( $user->id );
		$totalRequestSent = $model->getTotalRequestSent( $user->id );

		// Get the total friends list a user has
		$totalFriendsList = $user->getTotalFriendsList();

		// Set the total friends.
		$totalFriends = $model->getTotalFriends($user->id);

		// Get the total number of invitation sent out.
		$totalInvites = $model->getTotalInvites($user->id);

		// set total mutual friends
		if ($my->id != $user->id) {
			$totalMutualFriends 	= $model->getMutualFriendCount($my->id, $user->id);
			$this->set('totalMutualFriends', $totalMutualFriends);
		}

		// Get the suggestion count.
		$friendSuggestedCnt = $model->getSuggestedFriends($my->id, null, true);

		$friends = array();
		$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS');

		if ($activeList->id) {
			$title 	= $activeList->get('title');
		}

		if ($filter == 'mutual') {
			$title 		 = JText::_('COM_EASYSOCIAL_PAGE_TITLE_MUTUAL_FRIENDS');
			$mutuallimit = FD::themes()->getConfig()->get( 'friendslimit' , 20 );
			$friends 	 = $model->getMutualFriends( $my->id, $user->id, $mutuallimit );

			// Set breadcrumbs
			FD::page()->breadcrumb( JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS') , FRoute::friends() );
		}

		if ($filter == 'pending') {
			$title 		= JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_PENDING_APPROVAL');

			// Set breadcrumbs
			FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS' ) , FRoute::friends() );
		}

		if ($filter == 'request') {
			$title 		= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_REQUESTS' );

			$options[ 'state' ]		= SOCIAL_FRIENDS_STATE_PENDING;
			$options[ 'isRequest']	= true;

			$friends 	= $model->getFriends( $user->id , $options );

			// Set breadcrumbs
			FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS' ) , FRoute::friends() );
		}

		if ($filter == 'suggest') {
			$title 		= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_SUGGESTIONS' );
			$friends 	= $model->getSuggestedFriends( $my->id, FD::themes()->getConfig()->get( 'friendslimit' , 20 ) );

			// Set breadcrumbs
			FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS' ) , FRoute::friends() );
		}

		// Ensure that invites are enabled
		if ($filter == 'invites' && !$this->config->get('friends.invites.enabled')) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_FEATURE_NOT_AVAILABLE'));
		}

		if ($filter == 'invites') {
			$title   = JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_INVITES');
			$friends = $model->getInvitedUsers($user->id);
		}

		if ($filter == 'all' || $filter == 'pending' || $filter == 'list') {
			$friends = $model->getFriends($user->id, $options);
		}

		// Get pagination
		$pagination	= $model->getPagination();

		// Set additional params for the pagination links
		$pagination->setVar('view', 'friends');

		if (!$user->isViewer()) {
			$pagination->setVar( 'userid' , $user->getAlias() );
		}


		// Set the page title
		if ($user->isViewer()) {
			FD::page()->title( $title );
		} else {
			FD::page()->title( $user->getName() . ' - ' . $title );
		}


		// Set breadcrumbs
		FD::page()->breadcrumb( $title );

		// Push vars to the theme
		$this->set('totalInvites', $totalInvites);
		$this->set('pagination', $pagination);
		$this->set('privacy', $privacy );
		$this->set('filter', $filter );
		$this->set('activeList', $activeList );
		$this->set('totalFriendsList', $totalFriendsList );
		$this->set('friends', $friends );
		$this->set('totalPendingFriends', $totalPendingFriends );
		$this->set('totalRequestSent', $totalRequestSent );
		$this->set('totalFriendSuggest'	, $friendSuggestedCnt );
		$this->set('user', $user );
		$this->set('lists', $lists );
		$this->set('totalFriends', $totalFriends );

		// Load theme files.
		return parent::display('site/friends/default');
	}


	/**
	 * Displays the list form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function listForm()
	{
		// Ensure that user is logged in.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		FD::info()->set($this->getMessage());

		// Check if friends list is enabled
		$config = FD::config();

		if( !$config->get( 'friends.list.enabled' ) )
		{
			FD::info()->set( false , JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_DISABLED' ) , SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::friends( array() , false ) );
			$this->close();
		}

		// Get current logged in user.
		$my 	= FD::user();

		// Get the list id.
		$id 	= JRequest::getInt( 'id' , 0 );

		$list 	= FD::table( 'List' );
		$list->load( $id );

		// Check if this list is being edited.
		if( $id && !$list->id )
		{
			FD::info()->set( false , JText::_( 'COM_EASYSOCIAL_FRIENDS_INVALID_LIST_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::friends( array() , false ) );
			$this->close();
		}

		// Set the page title
		if( $list->id )
		{
			FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_EDIT_LIST_FORM' ) );
		}
		else
		{
			FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_LIST_FORM' ) );
		}

		// Get list of users from this list.
		$result 	= $list->getMembers();
		$members 	= array();

		if( $result )
		{
			$members	= FD::user( $result );
		}

		$this->set( 'members'	, $members );
		$this->set( 'list' 		, $list );
		$this->set( 'id', $id );

		// Load theme files.
		echo parent::display( 'site/friends/form.list' );
	}

	/**
	 * Displays the invite friends form
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function invite()
	{
		// Requires user to be logged into the site
		FD::requireLogin();

		// Ensure that invites are enabled
		if (!$this->config->get('friends.invites.enabled')) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_FEATURE_NOT_AVAILABLE'));
		}

		$editor = JFactory::getEditor();

		$this->set('editor', $editor);

		parent::display('site/friends/form.invite');
	}

	/**
	 * Post processing after inviting a friend
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sendInvites()
	{
		FD::info()->set($this->getMessage());

		if ($this->hasErrors()) {
			return $this->redirect(FRoute::friends(array('layout' => 'invite'), false));
		}

		return $this->redirect(FRoute::friends(array('filter' => 'invites'), false));
	}

	/**
	 * Perform redirection after the list is created.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 **/
	public function storeList( $list )
	{
		FD::info()->set( $this->getMessage() );

		$url	 = FRoute::friends( array( 'list' => $list->id ) , false );

		if( $this->hasErrors() )
		{
			$this->redirect( FRoute::friends( array() , false ) );
			$this->close();
		}

		$this->redirect( FRoute::friends( array() , false ) );
		$this->close();
	}

	/**
	 * This view is responsible to approve pending friend requests.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function approve()
	{
		// Get the return url.
		$return = JRequest::getVar( 'return' , null );

		$info	= FD::info();

		// Set the message data
		$info->set( $this->getMessage() );

		return $this->redirect( FRoute::friends( array() , false ) );
	}

	/**
	 * Post processing of delete list item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function deleteList()
	{
		$info 	= FD::info();

		$info->set( $this->getMessage() );

		$url 	= FRoute::friends( array() , false );
		$this->redirect( $url );
		$this->close();
	}
}
