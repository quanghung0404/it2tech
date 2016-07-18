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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewDashboard extends EasySocialSiteView
{
	/**
	 * Responsible to output the dashboard layout for the current logged in user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The name of the template file to parse; automatically searches through the template paths.
	 * @return	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display($tpl = null)
	{
		// If the user is not logged in, display the dashboard's unity layout.
		if ($this->my->guest) {
			return $this->displayGuest();
		}

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Default page title
		$title = $this->my->getName() . ' - ' . JText::_('COM_EASYSOCIAL_PAGE_TITLE_DASHBOARD');

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_DASHBOARD'));

		$db = ES::db();
		// $db->hasCreateTempPrivilege();

		// Get list of apps
		$model = FD::model('Apps');
		$options = array('view' => 'dashboard', 'uid' => $this->my->id, 'key' => SOCIAL_TYPE_USER);
		$apps = $model->getApps($options);

		// Load css for apps
		$model->loadAppCss($options);

		// Check if there is an app id in the current request as we need to show the app's content.
		$appId = $this->input->get('appId', 0, 'default');
		$contents = '';
		$isAppView = false;

		// If the user is viewing output from a particular app
		if ($appId) {
			$appId = (int) $appId;

			if (!$appId) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_IS_NOT_AVAILABLE'));
			}

			// Load the application.
			$app = FD::table('App');
			$app->load($appId);

			if (!$app->id) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_APP_NOT_FOUND'));
			}

			// Check if the user has access to this app
			if (!$app->accessible($this->my->id)) {
				FD::info()->set( null , JText::_( 'COM_EASYSOCIAL_DASHBOARD_APP_IS_NOT_INSTALLED' ) , SOCIAL_MSG_ERROR );
				return $this->redirect( FRoute::dashboard( array() , false ) );
			}

			$app->loadCss();

			// Generate the page title
			$title = $this->my->getName() . ' - ' . $app->get('title');

			// Load the library.
			$lib = FD::apps();
			$contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'dashboard', $app, array('userId' => $this->my->id));

			$isAppView 	= true;
		}

		// Get the limit start
		$startlimit = $this->input->get('limitstart', 0, 'int');

		// Determine the start page for the user.
		$start = $this->config->get('users.dashboard.start');

		// Check if there is any stream filtering or not.
		$filter	= $this->input->get('type', $start, 'word');

		if(!$filter) {
			$filter = $start;
		}

		// The filter 'all' is taken from the menu item the setting. all == user & friend, which mean in this case, is the 'me' filter.
		if ($filter == 'all') {

			$filter = 'me';
		}

		$listId = $this->input->get('listId', 0, 'int');
		$fid = '';

		// Used in conjunction with type=appFilter
		$filterId = '';

		// Determine if the current request is for "tags"
		$hashtag = $this->input->get('tag', '', 'default');
		$hashtagAlias = $hashtag;

		if (!empty($hashtag)) {
			$filter = 'hashtag';
		}

		// Retrieve user's groups
		$groupModel = FD::model('Groups');
		$groups = $groupModel->getUserGroups($this->my->id);

		// Retrieve user's events
		$eventModel = FD::model('Events');
		$events = $eventModel->getEvents(array('creator_uid' => $this->my->id, 'creator_type' => SOCIAL_TYPE_USER, 'ongoing' => true, 'upcoming' => true, 'ordering' => 'start', 'limit' => 5));

		// Retrieve user's status
		$story = FD::get('Story', SOCIAL_TYPE_USER);
		$story->setTarget($this->my->id);

		// Retrieve user's stream
		$stream = FD::stream();
		$stream->story  = $story;

		// Determines if we should be rendering the group streams
		$groupId = false;
		$eventId = false;

		$tags = array();

		// Filter by specific list item
		if ($filter == 'list' && !empty($listId)) {

			$list = FD::table('List');
			$list->load($listId);

			$title = $this->my->getName() . ' - ' . $list->get( 'title' );

			// Get list of users from this list.
			$friends = $list->getMembers();

			if ($friends) {
				$stream->get(array('listId' => $listId, 'startlimit' => $startlimit));
			} else {
				$stream->filter = 'list';
			}
		}

		// Filter by specific #hashtag
		if ($filter == 'hashtag') {
			$tag = $this->input->get('tag', '', 'default');

			$hashtag = $tag;

			$title 	= $this->my->getName() . ' - #' . $tag;

			$stream->get(array('tag' => $tag, 'startlimit' => $startlimit));
			$tags = array($tag);
		}


		// Filter by everyone
		if ($filter == 'everyone') {
			$stream->get(array('guest' => true, 'ignoreUser' => true, 'startlimit' => $startlimit));
		}

		// Filter by following
		if ($filter == 'following') {

			// Set the page title
			$title 	= $this->my->getName() . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_FOLLLOW' );

			$stream->get(array('context' => SOCIAL_STREAM_CONTEXT_TYPE_ALL, 'type' => 'follow', 'startlimit' => $startlimit));
		}

		// Filter by bookmarks
		if ($filter == 'bookmarks') {

			// Set the page title
			$title 	= $this->my->getName() . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_DASHBOARD_BOOKMARKS' );

			$stream->get(array('guest' => true, 'type' => 'bookmarks', 'startlimit' => $startlimit));
		}

		// Filter by sticky
		if ($filter == 'sticky') {

			// Set the page title
			$title 	= $this->my->getName() . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_DASHBOARD_STICKY' );

			$stream->get(array('userId' => $this->my->id, 'type' => 'sticky', 'startlimit' => $startlimit));
		}

		// Filter by apps
		if ($filter == 'appFilter') {

			$appType  = $this->input->get('filterid', '', 'string');
			$filterId = $appType;

			$stream->get(array('context' => $appType, 'startlimit' => $startlimit));

			$stream->filter	= 'custom';
		}

		// Filter by custom filters
		if ($filter == 'filter') {

			$fid 	= $this->input->get('filterid', 0, 'int');
			$sfilter = FD::table('StreamFilter');
			$sfilter->load($fid);

			// Set the page title
			$title 		= $this->my->getName() . ' - ' . $sfilter->title;

			if( $sfilter->id )
			{
				$hashtags	= $sfilter->getHashTag();
				$tags 		= explode( ',', $hashtags );

				if( $tags )
				{
					$stream->get( array( 'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL, 'tag' => $tags, 'startlimit' => $startlimit ) );
				}
			}

			$stream->filter = 'custom';
		}

		// Stream filter form
		if ($filter == 'filterForm') {
			// Set the page title
			$title 	= $this->my->getName() . ' - ' . JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FILTER_FORM');
			$id = $this->input->get('id', 0, 'int');

			// Load up the theme lib so we can output the contents
			$theme = FD::themes();

			$filter = FD::table( 'StreamFilter' );
			$filter->load($id);

			$theme->set('filter', $filter);

			$contents = $theme->output('site/stream/form.edit');
		}

		// Filter by groups
		if ($filter == 'group') {
			$id = $this->input->get('groupId', 0, 'int');
			$group   = FD::group($id);
			$groupId = $group->id;

			// Check if the user is a member of the group
			if (!$group->isMember()) {

				$this->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_GROUPS_NO_PERMISSIONS' ) , SOCIAL_MSG_ERROR );
				FD::info()->set( $this->getMessage() );
				return $this->redirect( FRoute::dashboard( array() , false ) );
			}

			// When posting stories into the stream, it should be made to the group
			$story 			= FD::get( 'Story' , SOCIAL_TYPE_GROUP );
			$story->setCluster( $group->id, SOCIAL_TYPE_GROUP );
			$story->showPrivacy( false );
			$stream->story 	= $story;

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('clusterId' => $group->id, 'clusterType' 	=> SOCIAL_TYPE_GROUP, 'limit' => 0));
			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$stream->get( array( 'clusterId' => $group->id , 'clusterType' => SOCIAL_TYPE_GROUP, 'nosticky' => true, 'startlimit' => $startlimit ) );
		}

		if ($filter == 'event') {
			$id = $this->input->get('eventId', 0, 'int');
			$event   = FD::event($id);
			$eventId = $event->id;

			// Check if the user is a member of the group
			if (!$event->getGuest()->isGuest()) {
				$this->setMessage(JText::_('COM_EASYSOCIAL_STREAM_GROUPS_NO_PERMISSIONS'), SOCIAL_MSG_ERROR);
				$this->info->set($this->getMessage());
				return $this->redirect(FRoute::dashboard(array(), false));
			}

			// When posting stories into the stream, it should be made to the group
			$story = FD::get('Story', SOCIAL_TYPE_EVENT);
			$story->setCluster($event->id, SOCIAL_TYPE_EVENT);
			$story->showPrivacy(false);
			$stream->story 	= $story;

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('clusterId' => $event->id, 'clusterType' 	=> SOCIAL_TYPE_EVENT, 'limit' => 0));
			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$stream->get(array('clusterId' => $event->id , 'clusterType' => SOCIAL_TYPE_EVENT, 'nosticky' => true, 'startlimit' => $startlimit));
		}

		if ($filter == 'me') {
			$stream->get( array('startlimit' => $startlimit) );
		}

		// Set the page title.
		FD::page()->title($title);

		// Set hashtags
		$story->setHashtags($tags);

		// Retrieve lists model
		$listsModel	 = FD::model('Lists');

		// Only fetch x amount of list to be shown by default.
		$limit = $this->config->get('lists.display.limit');

		// Get the friend's list.
		$lists = $listsModel->setLimit($limit)->getLists(array('user_id' => $this->my->id, 'showEmpty' => $this->config->get('friends.list.showEmpty')));

		// Get stream filter list
		$model = FD::model('Stream');
		$filterList = $model->getFilters($this->my->id);

		// Add RSS feed for dashboard
		$feedOptions = array('filter' => $filter);

		if ($hashtag) {
			$feedOptions['tag'] = $hashtag;
		} else {
			if ($filter == 'filter') {
				$feedOptions['filterid'] = $fid . ':' . $sfilter->alias;
			} else if ($filter == 'list') {
				$feedOptions['listId'] = $listId;
			} else {
				$id = $this->input->get('id', 0, 'int');

				if ($id) {
					$feedOptions['id'] = $id;
				}
			}
		}

		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(FRoute::dashboard($feedOptions, false));
		}

		// Get a list of application filters
		$appFilters = $model->getAppFilters(SOCIAL_TYPE_USER);

		$this->set('rssLink', $this->rssLink);
		$this->set('title'	, $title);
		$this->set('eventId', $eventId);
		$this->set('events', $events);
		$this->set('filterId', $filterId );
		$this->set('appFilters', $appFilters );
		$this->set('groupId', $groupId );
		$this->set('groups', $groups );
		$this->set('hashtag', $hashtag );
		$this->set('hashtagAlias', $hashtagAlias );
		$this->set('listId', $listId );
		$this->set('filter', $filter );
		$this->set('isAppView', $isAppView );
		$this->set('apps', $apps );
		$this->set('lists', $lists );
		$this->set('appId', $appId );
		$this->set('contents', $contents );
		$this->set('user', $this->my);
		$this->set('stream', $stream);
		$this->set('filterList', $filterList);
		$this->set('fid', $fid);

		echo parent::display('site/dashboard/default');
	}

	/**
	 * Displays the guest view for the dashboard
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function displayGuest()
	{
		// Determine if the current request is for "tags"
		$hashtag = $this->input->get('tag', '');
		$hashtagAlias = $hashtag;

		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(FRoute::dashboard(array(), false));
		}

		// Default stream filter
		$filter = 'everyone';

		if (!empty($hashtag)) {
			$filter = 'hashtag';
		}

		// Get the layout to use.
		$stream = FD::stream();
		$stream->getPublicStream($this->config->get('stream.pagination.pagelimit', 10), 0, $hashtag);

		// Get any callback urls.
		$return = FD::getCallback();

		// Try to get the login return url
		if (!$return) {
			$return = FRoute::getMenuLink($this->config->get('general.site.login'));
		}

		// If return value is empty, always redirect back to the dashboard
		if (!$return) {
			$return = FRoute::dashboard(array(), false);
		}

		// In guests view, there shouldn't be an app id
		$appId = $this->input->get('appId', '', 'default');

		if ($appId) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_IS_NOT_AVAILABLE'));
		}

		// Ensure that the return url is always encoded correctly.
		$return = base64_encode($return);
		$facebook = FD::oauth('Facebook');
		$fields = false;

		if ($this->config->get('registrations.enabled')) {

			$fieldsModel = FD::model('Fields');
			$profileId = $this->config->get('registrations.mini.profile', 'default');

			if ($profileId === 'default') {
				$profileId = FD::model('Profiles')->getDefaultProfile()->id;
			}

			$options = array('visible' => SOCIAL_PROFILES_VIEW_MINI_REGISTRATION, 'profile_id' => $profileId);

			// Get a list of custom fields
			$fields = $fieldsModel->getCustomFields($options);

			if (!empty($fields)) {
				FD::language()->loadAdmin();

				$fieldsLib = FD::fields();

				$session = JFactory::getSession();
				$registration = FD::table('Registration');
				$registration->load($session->getId());

				$data = $registration->getValues();
				$args = array(&$data, &$registration);

				$fieldsLib->trigger('onRegisterMini', SOCIAL_FIELDS_GROUP_USER, $fields, $args);
			}
		}

		$this->set('rssLink', $this->rssLink);
		$this->set('fields', $fields);
		$this->set('filter', $filter);
		$this->set('facebook', $facebook);
		$this->set('hashtag', $hashtag);
		$this->set('hashtagAlias', $hashtagAlias);
		$this->set('stream', $stream);
		$this->set('return', $return);

		echo parent::display('site/dashboard/default.guests');
	}
}
