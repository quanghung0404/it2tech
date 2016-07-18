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

// Import main controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerDashboard extends EasySocialController
{

	/**
	 * Retrieves the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getStream()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		// set jrequest view
		JRequest::set( array('view'=>'dashboard') );

		$hashtags = array();

		// Get the type of the stream to load.
		$type = $this->input->get('type', '', 'word');

		// Get the stream
		$stream = FD::stream();

		if (!$type) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_INVALID_FEED_TYPE'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__, $stream, $type);
		}

		// Get feeds from user's friend list.
		if ($type == 'list') {

			// The id of the friend list.
			$id = $this->input->get('id', 0, 'int');

			$list = FD::table('List');
			$list->load($id);

			if (!$id || !$list->id) {
				$this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_INVALID_LIST_ID_PROVIDED'), SOCIAL_MSG_ERROR);
				return $this->view->call(__FUNCTION__, $stream, $type);
			}

			// Get list of users from this list.
			$friends = $list->getMembers();

			if ($friends) {
				$stream->get(array('listId' => $id));
			} else {
				$stream->filter = 'list';
			}
		}

		if ($type == 'following') {
			$stream->get(array('context' => SOCIAL_STREAM_CONTEXT_TYPE_ALL, 'type' => 'follow'));
		}

		// Filter by bookmarks
		if ($type == 'bookmarks') {
			$stream->get(array('guest' => true, 'type' => 'bookmarks'));
		}

		// Filter by sticky
		if ($type == 'sticky') {
			$stream->get(array('userId' => $this->my->id, 'type' => 'sticky'));
		}

		// Filter stream items by event
		if ($type == 'event') {
			$id    = $this->input->get('id', 0, 'int');
			$event = FD::event($id);

			// Check if the user is a member of the group
			if (!$event->getGuest()->isGuest() && !$this->my->isSiteAdmin()) {
				$this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_EVENTS_NO_PERMISSIONS'), SOCIAL_MSG_ERROR);
				return $this->view->call(__FUNCTION__, $stream, $type);
			}

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('clusterId' => $id, 'clusterType' 	=> SOCIAL_TYPE_EVENT, 'limit' => 0));
			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$stream->get(array('clusterId' => $id , 'clusterType' => SOCIAL_TYPE_EVENT, 'nosticky' => true));
		}

		if ($type == 'group') {

			$id    = $this->input->get('id', 0, 'int');
			$group = FD::group($id);

			// Check if the user is a member of the group
			if (!$group->isMember() && !$this->my->isSiteAdmin()) {
				$this->view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_GROUPS_NO_PERMISSIONS'), SOCIAL_MSG_ERROR);
				return $this->view->call(__FUNCTION__, $stream, $type);
			}

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('clusterId' => $id, 'clusterType' 	=> SOCIAL_TYPE_GROUP, 'limit' => 0));
			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$stream->get(array('clusterId' => $id , 'clusterType' => SOCIAL_TYPE_GROUP, 'nosticky' => true));
		}

		// Get feeds from everyone
		if( $type == 'everyone' )
		{
			// $stream->getPublicStream( SOCIAL_STREAM_GUEST_LIMIT, 0 );
			$stream->get( array(
								'guest' 	=> true,
								'ignoreUser' => true
								)
						);
		}

		if ($type == 'appFilter') {

			// we need to use string and not 'word' due to some app name has number. e.g k2
			$appType = $this->input->get('id', '', 'string');
			$stream->get(array('context' => $appType));
			$stream->filter	= 'custom';
		}

		// custom filter.
		if ($type == 'custom') {

			// Get the id
			$id = $this->input->get('id', 0, 'int');

			$sfilter = FD::table('StreamFilter');
			$sfilter->load($id);

			if ($sfilter->id) {
				$hashtags = $sfilter->getHashTag();
				$tags = explode( ',', $hashtags );

				if ($tags) {
					$stream->get(array('context' => SOCIAL_STREAM_CONTEXT_TYPE_ALL, 'tag' => $tags));
				}
			}

			$stream->filter = 'custom';
		}

		// Get feeds from the current user and friends only.
		if ($type == 'me') {
			$stream->get();
		}

		// $nextStartDate = $stream->getNextStartDate();
		// echo $stream->html();exit;

		return $this->view->call(__FUNCTION__, $stream, $type, $hashtags);
	}

	/**
	 * Retrieves the dashboard contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getAppContents()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		// Get the app id.
		$appId 		= JRequest::getInt( 'appId' );

		// Load application.
		$app 	= FD::table( 'App' );
		$state 	= $app->load( $appId );

		// Get the view.
		$view 	= $this->getCurrentView();

		// If application id is not valid, throw an error.
		if( !$appId || !$state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_INVALID_APP_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $app );
		}

		$my 	= FD::user();


		// Check if the user has access to this app or not.
		// If application id is not valid, throw an error.
		if( !$app->accessible( $my->id ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_PLEASE_INSTALL_APP_FIRST' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $app );
		}

		return $view->call( __FUNCTION__ , $app );
	}
}
