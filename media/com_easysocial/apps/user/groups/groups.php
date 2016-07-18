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

FD::import( 'admin:/includes/group/group' );

/**
 * Groups application for EasySocial
 * @since	1.2
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialUserAppGroups extends SocialAppItem
{
	/**
	 * Notification triggered when generating notification item.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialTableNotification	The notification table object
	 * @return	null
	 */
	public function onNotificationLoad( &$item )
	{
		$allowed = array('group.leave', 'group.joined', 'group.invited', 'group.requested', 'group.approved');
		$contexts = array('groups.user.create');
		$allowed = array_merge($allowed, $contexts);

		if(!in_array( $item->cmd , $allowed ) && !in_array($item->context_type, $allowed)) {
			return;
		}

		// When someon creates a new group
		if ($item->context_type == 'groups.user.create' && $item->type == 'likes') {

			$hook	 = $this->getHook('notification', 'likes');
			$hook->execute($item, 'create');
			return;
		}

		$user 	= FD::user($item->actor_id);
		$group 	= FD::group($item->uid);

		if ($item->cmd == 'group.invited') {
			$item->title 	= JText::sprintf('APP_USER_GROUPS_NOTIFICATIONS_USER_INVITED_YOU_TO_JOIN_GROUP', $user->getName(), $group->getName());
			$item->image 	= $group->getAvatar();
		}

		if( $item->cmd == 'group.joined' )
		{
			$user 			= FD::user( $item->actor_id );
			$group 			= FD::group( $item->uid );
			$item->title	= JText::sprintf( 'APP_USER_GROUPS_NOTIFICATIONS_USER_JOINED_THE_GROUP' , $user->getName() , $group->getName() );
			$item->image 	= $group->getAvatar();
		}

		if( $item->cmd == 'group.leave' )
		{
			$user 			= FD::user( $item->actor_id );
			$group 			= FD::group( $item->uid );
			$item->title	= JText::sprintf( 'APP_USER_GROUPS_NOTIFICATIONS_USER_LEFT_THE_GROUP' , $user->getName() , $group->getName() );
			$item->image 	= $group->getAvatar();
		}

		if( $item->cmd == 'group.requested' )
		{
			$user 			= FD::user( $item->actor_id );
			$group 			= FD::group( $item->uid );
			$item->title	= JText::sprintf('APP_USER_GROUPS_NOTIFICATIONS_USER_ASKED_TO_JOIN_GROUP' , $user->getName() , $group->getName() );
			$item->image 	= $group->getAvatar();
		}

		if( $item->cmd == 'group.approved' )
		{
			$user 			= FD::user( $item->actor_id );
			$group 			= FD::group( $item->uid );
			$item->title	= JText::sprintf('APP_USER_GROUPS_NOTIFICATIONS_USER_APPROVED_TO_JOIN_GROUP' , $user->getName() , $group->getName() );
			$item->image 	= $group->getAvatar();
		}
	}

	/**
	 * Responsible to return the favicon object
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$obj 			= new stdClass();
		$obj->color		= '#303229';
		$obj->icon 		= 'fa fa-users';
		$obj->label 	= 'APP_USER_GROUPS_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Prepares the group activity log
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onBeforeGetStream( array &$options, $view = '' )
	{
		if ($view != 'dashboard') {
			return;
		}

		$allowedContext = array('groups','story','photos', 'tasks', 'discussions');

		if (is_array($options['context']) && in_array('groups', $options['context'])){
			// we need to make sure the stream return only cluster stream.
			$options['clusterType'] = SOCIAL_TYPE_GROUP;
		} else if ($options['context'] === 'groups') {
			$options['context'] 	= $allowedContext;
			$options['clusterType'] = SOCIAL_TYPE_GROUP;
		}
	}



	/**
	 * Prepares the group activity log
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
		if ($item->context != 'groups') {
			return;
		}

		$groupId 	= $item->contextId;
		$group 		= FD::group( $groupId );

		if (!$group) {
			return;
		}

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $item->actor );

		$item->title 	= parent::display( 'logs/' . $item->verb . '.title' );

		return true;
	}

	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if( $item->context_type != 'groups' )
		{
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' ) );

		if( !$group )
		{
			return;
		}

		$item->cnt = 1;

		if( $group->type != SOCIAL_GROUPS_PUBLIC_TYPE )
		{
			if( !$group->isMember( FD::user()->id ) )
			{
				$item->cnt = 0;
			}
		}

		return true;
	}


	/**
	 * event onLiked on shared link
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onAfterLikeSave( &$likes )
	{
		$allowed 	= array('groups.user.create', 'groups.user.join', 'groups.user.leave', 'groups.user.makeadmin', 'groups.user.update');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		$stream = FD::table('Stream');
		$stream->load($likes->uid);

		// Get a list of recipients from the stream
		$recipients 	= $this->getStreamNotificationTargets($likes->uid, 'userprofile', 'user', 'update', array($stream->actor_id), array($likes->created_by));

		// Prepare the command
		$command 		= 'likes.item';

		$systemOptions 	= array(
									'title' 		=> '',
									'context_type' 	=> $likes->type,
									'url' 			=> $stream->getPermalink(false, false, false),
									'actor_id' 		=> $likes->created_by,
									'uid'			=> $likes->uid
								);

		FD::notify($command, $recipients, false, $systemOptions);
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 * @since	1.2
	 * @access	public
	 * @param	array
	 */
	public function onStreamVerbExclude( &$exclude )
	{
		// Get app params
		$params		= $this->getParams();

		$excludeVerb = false;

		if(! $params->get('stream_join', true)) {
			$excludeVerb[] = 'join';
		}

		if (! $params->get('stream_leave', true)) {
			$excludeVerb[] = 'leave';
		}

		if (! $params->get('stream_create', true)) {
			$excludeVerb[] = 'created';
		}

		if (! $params->get('stream_admin', true)) {
			$excludeVerb[] = 'makeadmin';
		}

		if (! $params->get('stream_update', true)) {
			$excludeVerb[] = 'update';
		}

		if ($excludeVerb !== false) {
			$exclude['groups'] = $excludeVerb;
		}
	}


	/**
	 * Trigger for onPrepareStream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// We only want to process related items
		if ($item->cluster_type !== SOCIAL_TYPE_GROUP || empty($item->cluster_id)) {
			return;
		}

		$allowed = array('groups', 'tasks', 'discussions', 'news');

		if (!in_array($item->context, $allowed)) {
			return;
		}

		// Get the group object
		$group = FD::group( $item->cluster_id );

		// If we can't find the group, skip this.
		if (!$group) {
			return;
		}

		// Determines if the user can view this group item.
		if (!$group->canViewItem()) {
			return;
		}

		// Get the app params so that we determine which stream should be appearing
		$app = $this->getApp();
		$params	= $app->getParams();

		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->color = '#303229';
		$item->fonticon	= 'fa-users';
		$item->label = FD::_('APP_USER_GROUPS_STREAM_TOOLTIP', true);

		if ($item->context == 'news') {
			$this->prepareGroupNewsStream($item, $group, $includePrivacy);
		} else if ($item->context == 'tasks') {
			$this->prepareGroupTask($item, $group, $includePrivacy);
		} else if ($item->context == 'discussions') {
			$this->prepareGroupDiscussion( $item, $group, $includePrivacy );
		} else {

			// Prepare the likes
			$likes = FD::likes($item->uid, 'groups', $item->verb, SOCIAL_TYPE_USER, $item->uid);
			$item->likes = $likes;

			$item->display	= SOCIAL_STREAM_DISPLAY_MINI;

			// Display stream item for new member join
			if ($item->verb == 'join' && $params->get('stream_join', true)) {
				$this->prepareJoinStream( $item , $group );
			}

			// Display stream item if member leaves a group
			if ($item->verb == 'leave' && $params->get('stream_leave', true)) {
				$this->prepareLeaveStream( $item , $group );
			}

			// Display stream item for new group creation
			if ($item->verb == 'create' && $params->get('stream_create', true)) {
				$this->prepareCreateStream( $item , $group );
			}

			// Display stream item when a member is promoted to be a group admin
			if ($item->verb == 'makeadmin' && $params->get('stream_admin', true)) {
				$this->prepareMakeAdminStream( $item , $group );
			}

			if ($item->verb == 'update' && $params->get('stream_update', true)) {
				$this->prepareUpdateStream( $item , $group );
			}

		}

		// Hide these items if the user is not a member of the group.
		if (!$group->isMember()) {
			$item->commentLink	= false;
			$item->repost 		= false;
			$item->commentForm 	= false;
		}

		// Only show Social sharing in public group
		if ($group->type != SOCIAL_GROUPS_PUBLIC_TYPE) {
			$item->sharing = false;
		}
	}


	private function prepareGroupDiscussion( SocialStreamItem &$item , $group, $includePrivacy )
	{
		$app = FD::table( 'app' );
		$app->loadByElement( 'discussions', SOCIAL_APPS_GROUP_GROUP, 'apps');

		$params = $app->getParams();

		if( $params->get( 'stream_' . $item->verb , true ) == false )
		{
			return;
		}

		if( $item->verb == 'create' )
		{
			$this->prepareCreateDiscussionStream( $item , $group, $includePrivacy, $app);
		}

		if( $item->verb == 'reply' )
		{
			$this->prepareReplyStream( $item , $group, $includePrivacy, $app);
		}

		if( $item->verb == 'answered' )
		{
			$this->prepareAnsweredStream( $item , $group, $includePrivacy, $app);
		}

		if( $item->verb == 'lock' )
		{
			$this->prepareLockedStream( $item , $group, $includePrivacy, $app);
		}


	}


	/**
	 * Prepares the stream entry for news
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function prepareGroupNewsStream(SocialStreamItem &$item, $group, $includePrivacy)
	{
		$params = FD::registry($item->params);
		$group = FD::group($params->get('news')->cluster_id);

		// Group might already be deleted
		if (!$group) {
			return;
		}

		$news = FD::table('ClusterNews');
		$news->load($params->get('news')->id);

		$this->set('actor', $item->actor);
		$this->set('group', $group);
		$this->set('news', $news);

		$item->title = parent::display('streams/news/create.title');
		$item->content = parent::display('streams/news/create.content');
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareCreateDiscussionStream(&$item, $group, $includePrivacy, $app)
	{
		// Get the context params
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' )->id );

		$discussion	= FD::table( 'Discussion' );
		$discussion->load($item->contextId);

		// Determines if there are files associated with the discussion
		$files 		= $discussion->hasFiles();
		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'discussionId' => $discussion->id ) , false );

		$content 	= $this->formatContent( $discussion );

		$this->set( 'files'		, $files );
		$this->set( 'actor'		, $item->actor );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'discussion', $discussion );
		$this->set( 'content'	, $content );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/discussions/create.title' );
		$item->content 	= parent::display( 'streams/discussions/create.content' );
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareReplyStream( &$item, $group, $includePrivacy, $app)
	{
		// Get the context params
		$params 	= FD::registry($item->params);
		$data = $params->get('group');

		if (!$data) {
			return;
		}

		$group  = FD::group($data->id);

		$discussion = FD::table( 'Discussion' );
		$discussion->load($item->contextId);

		$reply	= FD::table( 'Discussion' );
		$reply->load( $params->get( 'reply' )->id );

		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'discussionId' => $discussion->id ) , false );

		$content 	= $this->formatContent( $reply );

		$this->set( 'actor'		, $item->actor );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'discussion', $discussion );
		$this->set( 'reply'		, $reply );
		$this->set( 'content'	, $content );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/discussions/reply.title' );
		$item->content 	= parent::display( 'streams/discussions/reply.content' );
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareAnsweredStream( &$item, $group, $includePrivacy, $app)
	{
		// Get the context params
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' )->id );

		$discussion = FD::table( 'Discussion' );
		$discussion->bind( $params->get( 'discussion' ) );

		$reply	= FD::table( 'Discussion' );
		$reply->bind( $params->get( 'reply' ) );

		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'discussionId' => $discussion->id ) , false );

		$content 	= $this->formatContent( $reply );

		// Get the reply author
		$reply->author	= FD::user( $reply->created_by );

		$this->set( 'actor'		, $item->actor );
		$this->set( 'permalink'	, $permalink );
		$this->set( 'discussion', $discussion );
		$this->set( 'reply'		, $reply );
		$this->set( 'content'	, $content );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/discussions/answered.title' );
		$item->content 	= parent::display( 'streams/discussions/answered.content' );
	}

	/**
	 * Prepares the stream item for new discussion creation
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialStreamItem	The stream item.
	 * @return
	 */
	private function prepareLockedStream( &$item, $group, $includePrivacy, $app)
	{
		// Get the context params
		$params 	= FD::registry( $item->params );
		$group 		= FD::group( $params->get( 'group' )->id );

		$discussion = FD::table( 'Discussion' );
		$discussion->bind( $params->get( 'discussion' ) );

		$permalink 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'discussionId' => $discussion->id ) , false );

		$item->display 	= SOCIAL_STREAM_DISPLAY_MINI;

		$this->set( 'permalink'	, $permalink );
		$this->set( 'actor'		, $item->actor );
		$this->set( 'discussion', $discussion );

		// Load up the contents now.
		$item->title 	= parent::display( 'streams/discussions/locked.title' );
	}

	/**
	 * Internal method to format the discussions
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function formatContent( $discussion )
	{
		// Get the app params so that we determine which stream should be appearing
		$app = $this->getApp();
		$params	= $app->getParams();

		$content = FD::string()->parseBBCode( $discussion->content , array( 'code' => true , 'escape' => false ) );

		// Remove [file] from contents
		$content = $discussion->removeFiles( $content );

		$maxlength = $params->get('stream_discussion_maxlength', 250);

		if ($maxlength) {
			$content = strip_tags($content);
			$content = JString::strlen($content) > $maxlength ? JString::substr($content, 0, $maxlength ) . JText::_('COM_EASYSOCIAL_ELLIPSES') : $content;
		}

		return $content;
	}

	private function prepareGroupTask( SocialStreamItem &$item , $group, $includePrivacy )
	{

		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 	= '#658ea6';
		$item->fonticon = 'fa fa-check-square';
		$item->label 	= FD::_( 'APP_GROUPS_TASKS_STREAM_TOOLTIP', true );

		// Get the verb
		$verb 	= $item->verb;

		if( $verb == 'createTask' )
		{
			$this->prepareCreatedTaskStream( $item , $includePrivacy );
		}

		if( $verb == 'createMilestone' )
		{
			$this->prepareCreateMilestoneStream( $item , $includePrivacy );
		}

	}

	public function prepareCreatedTaskStream( SocialStreamItem $streamItem , $includePrivacy = true )
	{
		$params 	= FD::registry( $streamItem->params );

		// Get the tasks available from the cached data
		$items 		= $params->get( 'tasks' );
		$tasks 		= array();

		$taskId = '';
		foreach( $items as $item )
		{
			$task 	= FD::table( 'Task' );
			$task->load( $item->id );

			$tasks[]	= $task;
			$taskId = $task->id;
		}

		// Get the milestone
		$milestone 	= FD::table( 'Milestone' );
		$milestone->bind( $params->get( 'milestone' ) );

		// Get the group data
		FD::load( 'group' );
		$group 		= new SocialGroup();
		$group->bind( $params->get( 'group' ) );

		// We need to get the task app for the group
		$app = Foundry::table('App');
		$app->load(array('element' => 'tasks', 'group' => 'group'));

		$permalink	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'milestoneId' => $milestone->id ) );

		$this->set( 'permalink' , $permalink );
		$this->set( 'stream'	, $streamItem );
		$this->set( 'milestone', $milestone );
		$this->set( 'total'	, count( $tasks ) );
		$this->set( 'actor'	, $streamItem->actor );
		$this->set( 'group' , $group );
		$this->set( 'tasks' , $tasks );

		$streamItem->title	= parent::display( 'streams/tasks/create.task.title' );
		$streamItem->content	= parent::display( 'streams/tasks/create.task.content' );

		// Append the likes action on the stream
		if (!$streamItem->contextIds[0]) {

			$likes = Foundry::likes();
			$likes->get($taskId , $streamItem->context, $streamItem->verb, SOCIAL_TYPE_GROUP, $streamItem->uid);
			$streamItem->likes	= $likes;

			// Append the comment action on the stream
			$comments = Foundry::comments($taskId , $streamItem->context, $streamItem->verb, SOCIAL_TYPE_GROUP,  array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $streamItem->uid ) ) ), $streamItem->uid);
			$streamItem->comments 	= $comments;
		}

	}

	public function prepareCreateMilestoneStream( SocialStreamItem $item , $includePrivacy = true )
	{
		$params 	= FD::registry( $item->params );

		$milestone	= FD::table( 'Milestone' );
		$milestone->bind( $params->get( 'milestone' ) );

		// Get the group data
		FD::load( 'group' );
		$group 		= new SocialGroup();
		$group->bind( $params->get( 'group' ) );

		// Get the actor
		$actor 		= $item->actor;
		
		// We need to get the task app for the group
		$app = Foundry::table('App');
		$app->load(array('element' => 'tasks', 'group' => 'group'));
		
		$permalink	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'milestoneId' => $milestone->id ) );

		$this->set( 'permalink'	, $permalink );
		$this->set( 'milestone' , $milestone );
		$this->set( 'actor'		, $actor );
		$this->set( 'group'		, $group );

		$item->title 	= parent::display( 'streams/tasks/create.milestone.title' );
		$item->content 	= parent::display( 'streams/tasks/create.milestone.content' );
	}

	private function prepareLeaveStream( SocialStreamItem &$item , $group )
	{
		// Get the actor
		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/leave.title' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_USER_GROUPS_STREAM_LEAVED_GROUP', $actor->getName(), $group->getName()));
	}

	private function prepareMakeAdminStream( SocialStreamItem &$item , $group )
	{
		// Get the actor
		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/admin.title' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_USER_GROUPS_STREAM_PROMOTED_TO_BE_ADMIN', $actor->getName(), $group->getName()));

	}

	private function prepareJoinStream( SocialStreamItem &$item , SocialGroup $group )
	{
		// Get the actor
		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/join.title' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_USER_GROUPS_STREAM_HAS_JOIN_GROUP', $actor->getName(), $group->getName()));
	}

	private function prepareUpdateStream( SocialStreamItem &$item , $group )
	{
		// Get the actor
		$actor	 = $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/update.title' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_USER_GROUPS_STREAM_UPDATED_GROUP', $actor->getName(), $group->getName()));
	}

	private function prepareCreateStream( SocialStreamItem &$item , SocialGroup $group )
	{
		// We want a full display for group creation.
		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;

		// Get the actor.
		$actor	 		= $item->actor;

		$this->set( 'group'	, $group );
		$this->set( 'actor'	, $actor );

		$item->title 	= parent::display( 'streams/create.title' );
		$item->content	= parent::display( 'streams/content' );

		// Append the opengraph tags
		$item->addOgDescription(JText::sprintf('APP_USER_GROUPS_STREAM_CREATED_GROUP', $actor->getName(), $group->getName()));

	}
}
