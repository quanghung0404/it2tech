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

// Include apps interface.
FD::import( 'admin:/includes/apps/apps' );

/**
 * Tasks application for Groups in EasySocial.
 *
 * @since	1.2
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialGroupAppTasks extends SocialAppItem
{
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
		$obj = new stdClass();
		$obj->color = '#658ea6';
		$obj->icon = 'fa fa-check-square';
		$obj->label = 'APP_USER_TASKS_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Triggered after a comment is posted in a milestone
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed 	= array('tasks.group.createMilestone');

		if (!in_array($comment->element, $allowed)) {
			return;
		}
		// Get the verb
		$segments 		= explode('.', $comment->element);
		$verb 			= $segments[2];

		if ($comment->element == 'tasks.group.createMilestone') {

			// Get the milestone
			$milestone		= FD::table('Milestone');
			$milestone->load($comment->uid);

			// Get the group
			$group 			= FD::group($milestone->uid);

			// Get a list of recipients
			$recipients 	= $this->getStreamNotificationTargets($comment->uid, 'tasks', 'group', $verb, array(), array($milestone->owner_id, $comment->created_by));

			// okay since comment on group task can be made to 'task.group.createmilestones' and can only be commented via stream item,
			// also, currently milestone page do not display any comments, thus the link have to go to stream item page to see the comment.
			// @2014-07-02, Sam

			$emailOptions 	= array(
				'title'		=> 'APP_GROUP_TASKS_EMAILS_COMMENTED_ON_YOUR_MILESTONE_TITLE',
				'template'	=> 'apps/group/tasks/comment.milestone',
				'permalink'	=> FRoute::stream( array( 'layout' => 'item', 'id' => $comment->stream_id, 'external' => true, 'xhtml' => true ) )
			);

			$systemOptions 	= array(
				'title'			=> '',
				'content'		=> $comment->comment,
				'context_type'	=> $comment->element,
				'url'			=> FRoute::stream( array( 'layout' => 'item', 'id' => $comment->stream_id ) ),
				'actor_id'		=> $comment->created_by,
				'uid'			=> $comment->uid,
				'aggregate'		=> true
			);

			// Notify the owner first
			if ($comment->created_by != $milestone->owner_id) {
				Foundry::notify('comments.item', array($milestone->owner_id), $emailOptions, $systemOptions);
			}

			// Get a list of recipients to be notified for this stream item
			// We exclude the owner of the note and the actor of the like here
			$recipients 	= $this->getStreamNotificationTargets($comment->uid, 'tasks', 'group', $verb, array(), array($milestone->owner_id, $comment->created_by));

			$emailOptions['title'] 		= 'APP_GROUP_TASKS_EMAILS_COMMENTED_ON_USERS_MILESTONE_TITLE';
			$emailOptions['template']	= 'apps/group/tasks/comment.milestone.involved';

			// Notify other participating users
			FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
		}

	}

	/**
	 * Triggered after a group is deleted
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterDelete(SocialGroup &$group)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		// Delete all milestones related to this group
		$sql->delete('#__social_tasks_milestones');
		$sql->where('type', SOCIAL_TYPE_GROUP);
		$sql->where('uid', $group->id);

		$db->setQuery($sql);
		$db->Query();

		// Delete all tasks related to this group
		$sql->clear();
		$sql->delete('#__social_tasks');
		$sql->where('type', SOCIAL_TYPE_GROUP);
		$sql->where('uid', $group->id);

		$db->setQuery($sql);
		$db->Query();
	}

	/**
	 * Processes when someone likes the stream of a milestone
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed 	= array('tasks.group.createMilestone');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		// Get the verb
		$segments 		= explode('.', $likes->type);
		$verb 			= $segments[2];

		if ($likes->type == 'tasks.group.createMilestone') {

			// Get the milestone
			$milestone		= FD::table('Milestone');
			$milestone->load($likes->uid);

			// Get the group
			$group 			= FD::group($milestone->uid);

			// Get a list of recipients
			$recipients 	= $this->getStreamNotificationTargets($likes->uid, 'tasks', 'group', $verb, array(), array($milestone->owner_id, $likes->created_by));

			// okay since likes on group task can be made to 'task.group.createmilestones' and can only be liked via stream item,
			// also, currently milestone page do not display any likes, thus the link have to go to stream item page to see the likes.
			// @2014-07-02, Sam

			$emailOptions 	= array(
				'title'		=> 'APP_GROUP_TASKS_EMAILS_LIKE_YOUR_MILESTONE_TITLE',
				'template'	=> 'apps/group/tasks/like.milestone',
				'permalink'	=> FRoute::stream(array( 'layout' => 'item', 'id' => $likes->stream_id, 'external' => true, 'xhtml' => true))
			);

			$systemOptions 	= array(
				'title' 		=> '',
				'context_type' 	=> $likes->type,
				'url' 			=> FRoute::stream(array( 'layout' => 'item', 'id' => $likes->stream_id)),
				'actor_id' 		=> $likes->created_by,
				'uid'			=> $likes->uid,
				'aggregate'		=> true
			);

			// Notify the owner first
			if ($likes->created_by != $milestone->owner_id) {
				Foundry::notify('likes.item', array($milestone->owner_id), $emailOptions, $systemOptions);
			}

			// Get a list of recipients to be notified for this stream item
			// We exclude the owner of the note and the actor of the like here
			$recipients 	= $this->getStreamNotificationTargets($likes->uid, 'tasks', 'group', $verb, array(), array($milestone->owner_id, $likes->created_by));

			$emailOptions['title'] 		= 'APP_GROUP_TASKS_EMAILS_LIKE_USERS_MILESTONE_TITLE';
			$emailOptions['template']	= 'apps/group/tasks/like.milestone.involved';

			// Notify other participating users
			FD::notify('likes.involved', $recipients, $emailOptions, $systemOptions);
		}


	}

	/**
	 * Processes notification for groups
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad( SocialTableNotification &$item )
	{
		$cmds 	= array( 'group.milestone.create' , 'group.task.create' , 'group.task.completed', 'comments.item', 'likes.item', 'comments.involved', 'likes.involved');

		if( !in_array( $item->cmd , $cmds ) )
		{
			return;
		}

		// Get the actor
		$actor 	= FD::user( $item->actor_id );

		// Get the group id
		$group 		= FD::group( $item->uid );

		if (in_array($item->type, array('likes', 'comments'))) {
			// Check if context_type is correct
			$segments = explode('.', $item->context_type);

			if (count($segments) === 3 && $segments[0] === 'tasks' && $segments[1] === 'group') {
				$hook = $this->getHook('notification', $item->type);
				$hook->execute($item);
				return;
			}
		}

		if( $item->cmd === 'group.task.completed' )
		{
			// Get the milestone data
			$id 		= $item->context_ids;
			$task 		= FD::table( 'Task' );
			$task->load( $id );

			$milestone	= FD::table( 'Milestone' );
			$milestone->load( $task->milestone_id );

			$item->title 		= JText::sprintf( 'APP_GROUP_TASKS_NOTIFICATIONS_USER_COMPLETED_TASK' , $actor->getName() , $milestone->title );
			$item->content		= $task->title;
		}

		if( $item->cmd === 'group.task.create' )
		{
			// Get the milestone data
			$id 		= $item->context_ids;
			$task 		= FD::table( 'Task' );
			$task->load( $id );

			$milestone	= FD::table( 'Milestone' );
			$milestone->load( $task->milestone_id );

			$item->title 		= JText::sprintf( 'APP_GROUP_TASKS_NOTIFICATIONS_USER_CREATED_TASK' , $actor->getName() , $milestone->title );
			$item->content		= $task->title;
		}

		if( $item->cmd === 'group.milestone.create' )
		{
			// Get the milestone data
			$id 		= $item->context_ids;
			$milestone	= FD::table( 'Milestone' );
			$milestone->load( $id );

			$item->title 		= JText::sprintf( 'APP_GROUP_TASKS_NOTIFICATIONS_USER_CREATED_MILESTONE' , $actor->getName() , $group->getName() );
		}
	}

	/**
	 * Processes a saved story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onBeforeStorySave( &$streamTemplate , &$streamItem , &$template )
	{
		// Get the link information from the request
		$items = $this->input->get('tasks_items', array(), 'array');
		$milestoneId = $this->input->get('tasks_milestone', 0, 'int');

		// Load up the milestone object
		$milestone = FD::table('Milestone');
		$milestone->load($milestoneId);

		if (!$items || empty($items) || !$milestone->id) {
			return;
		}

		// Get the group object
		$group = FD::group($streamTemplate->cluster_id);

		// Set the verb of the stream
		$streamTemplate->setVerb('createTask');

		$tasks	= array();

		// We need to store the tasks item now.
		$taskId = '';

		foreach ($items as $item) {

			if (!$item) {
				continue;
			}

			// Store the task now
			$task = FD::table('Task');
			$task->title = $item;
			$task->state = SOCIAL_STATE_PUBLISHED;
			$task->uid = $group->id;
			$task->type = SOCIAL_TYPE_GROUP;
			$task->user_id = FD::user()->id;
			$task->milestone_id = $milestone->id;
			$task->store();

			$taskId = $task->id;

			$tasks[] = $task;
		}

		// Set the context of the task
		if (count($items) == 1 && $taskId) {
			$streamTemplate->setContext($taskId, 'tasks');
		}
			
		$params = FD::registry();
		$params->set('tasks', $tasks);
		$params->set('group', $group);
		$params->set('milestone', $milestone);


		// Set the params on the stream
		$streamTemplate->setParams($params);

		return true;
	}

	/**
	 * Prepares what should appear in the story form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStoryPanel($story)
	{
		$params = $this->getApp()->getParams();

		if (!$params->get('story_form', true)) {
			return;
		}

		// Get the group data
		$group = ES::group($story->cluster);

		$tasks = ES::model('Tasks');
		$milestones	= $tasks->getMilestones( $group->id , SOCIAL_TYPE_GROUP );

		$theme = ES::themes();

		// Create plugin object
		$plugin = $story->createPlugin('tasks', 'panel');

		$button = $theme->output('site/tasks/story/button');

		// If there is no milestone, do not need to display the tasks embed in the story form.
		if (!$milestones) {
			$permalink 	= $this->getApp()->getPermalink('canvas', array('groupId' => $group->id, 'customView' => 'form'));

			// We need to attach the button to the story panel
			$theme->set('permalink', $permalink);
    
	        $form = $theme->output('site/tasks/story/empty');

	        $plugin->setHtml($button, $form);

			return $plugin;
		}

		// We need to attach the button to the story panel
		$theme->set('milestones', $milestones);

		$form = $theme->output('site/tasks/story/form');

		// Attachment script
		$script = ES::get('Script');

		$plugin->setHtml($button, $form);
		$plugin->setScript($script->output('site/tasks/story/plugin'));

		return $plugin;
	}

	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	jos_social_stream, boolean
	 * @return  0 or 1
	 */
	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if( $item->context_type != 'tasks' )
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
	 * Triggered when the prepare stream is rendered
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context != 'tasks') {
			return;
		}

		// Ensure that the group is valid
		$group = FD::group($item->cluster_id);

		if (!$group) {
			return;
		}

		// Determines if the viewer can view the group's items
		if (!$group->canViewItem()) {
			return;
		}

		$item->display	= SOCIAL_STREAM_DISPLAY_FULL;
		$item->color 	= '#658ea6';
		$item->fonticon = 'fa fa-check-square';
		$item->label 	= FD::_( 'APP_GROUPS_TASKS_STREAM_TOOLTIP', true );

		// Do not allow reposting on milestone items
		$item->repost 	= false;

		if ($item->verb == 'createTask') {
			$this->prepareCreatedTaskStream($item, $includePrivacy);
		}

		if ($item->verb == 'createMilestone') {
			$this->prepareCreateMilestoneStream($item, $includePrivacy);
		}
	}

	public function prepareCreatedTaskStream(SocialStreamItem $streamItem, $includePrivacy = true)
	{
		$params = FD::registry($streamItem->params);

		// Get the tasks available from the cached data
		$items = $params->get('tasks');
		$tasks = array();

		$taskId = '';

		foreach ($items as $item) {

			$task = FD::table('Task');
			$task->load($item->id);

			$tasks[] = $task;
			$taskId = $task->id;
		}

		// Get the milestone
		$milestone 	= FD::table('Milestone');
		$milestone->bind($params->get('milestone'));

		// Get the group data
		FD::load('group');
		$group = new SocialGroup();
		$group->bind($params->get('group'));

		$app 		= $this->getApp();
		$permalink	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'milestoneId' => $milestone->id ) );

		$this->set( 'permalink' , $permalink );
		$this->set( 'stream'	, $streamItem );
		$this->set( 'milestone', $milestone );
		$this->set( 'total'	, count( $tasks ) );
		$this->set( 'actor'	, $streamItem->actor );
		$this->set( 'group' , $group );
		$this->set( 'tasks' , $tasks );

		$streamItem->title	= parent::display( 'streams/create.task.title' );
		$streamItem->content	= parent::display( 'streams/create.task.content' );

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
		$app 		= $this->getApp();
		$permalink	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'milestoneId' => $milestone->id ) );

		$this->set( 'permalink'	, $permalink );
		$this->set( 'milestone' , $milestone );
		$this->set( 'actor'		, $actor );
		$this->set( 'group'		, $group );

		$item->title 	= parent::display( 'streams/create.milestone.title' );
		$item->content 	= parent::display( 'streams/create.milestone.content' );
	}
}
