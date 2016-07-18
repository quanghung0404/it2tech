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

class TasksControllerTasks extends SocialAppsController
{
	/**
	 * Displays delete confirmation dialog
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		$ajax 		= FD::ajax();

		// Get the group
		$groupId 	= JRequest::getInt( 'groupId' , 0 );
		$group		= FD::group( $groupId );

		$my = FD::user();

		// Check if the user is allowed to create a discussion
		if( !$group->isMember() && !$my->isSiteAdmin() )
		{
			return $ajax->reject();
		}

		$id 	= JRequest::getInt( 'id' );
		$task 	= FD::table( 'Task' );
		$task->load( $id );

		if( !$id || !$task->id || $task->uid != $group->id )
		{
			return $ajax->reject();
		}

		$task->delete();

		// @points: groups.task.delete
		$points = FD::points();
		$points->assign( 'groups.task.delete' , 'com_easysocial' , $my->id );

		return $ajax->resolve();
	}

	/**
	 * Displays delete confirmation dialog
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function confirmDelete()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		$ajax 		= FD::ajax();

		// Get the group
		$groupId 	= JRequest::getInt( 'groupId' , 0 );
		$group		= FD::group( $groupId );

		$my = FD::user();

		// Check if the user is allowed to create a discussion
		if( !$group->isMember() && !$my->isSiteAdmin())
		{
			return $ajax->reject();
		}

		$theme 	= FD::themes();
		$contents 	= $theme->output( 'apps/group/tasks/views/dialog.delete.task' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Allows caller to resolve a task
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function resolve()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allow logged in users
		FD::requireLogin();

		// Load up ajax library
		$ajax 	= FD::ajax();

		// Get the current group and logged in user
		$groupId 	= JRequest::getInt( 'groupId' );
		$group 		= FD::group( $groupId );
		$my 		= FD::user();

		if( !$group || !$groupId )
		{
			return $ajax->reject( 'failed' );
		}

		// Test if the current user is a member of this group.
		if( !$group->isMember() && !$my->isSiteAdmin())
		{
			return $ajax->reject();
		}

		// Determines if this is a new record
		$id 		= JRequest::getInt( 'id' );
		$task 		= FD::table( 'Task' );
		$task->load( $id );

		$task->resolve();

		// @points: groups.task.resolve
		$points = FD::points();
		$points->assign( 'groups.task.resolve' , 'com_easysocial' , $my->id );

		// Get the app
		$app 	= $this->getApp();

		// Get the application params
		$params = $app->getParams();

		if( $params->get( 'notify_complete_task' , true ) )
		{
			$milestone 	= FD::table( 'Milestone' );
			$milestone->load( $task->milestone_id );

			// Get the redirection url
			$url 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'milestoneId' => $milestone->id ) , false );

			$group->notifyMembers( 'task.completed' , array( 'userId' => $my->id , 'id' => $task->id , 'title' => $task->title , 'content' => $milestone->description , 'permalink' => $url , 'milestone' => $milestone->title ) );
		}
		return $ajax->resolve();
	}

	/**
	 * Allows caller to unresolve a task
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function unresolve()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allow logged in users
		FD::requireLogin();

		// Load up ajax library
		$ajax 	= FD::ajax();

		// Get the current group and logged in user
		$groupId 	= JRequest::getInt( 'groupId' );
		$group 		= FD::group( $groupId );
		$my 		= FD::user();

		if( !$group || !$groupId )
		{
			return $ajax->reject();
		}

		// Test if the current user is a member of this group.
		if( !$group->isMember() && !$my->isSiteAdmin())
		{
			return $ajax->reject();
		}

		// Determines if this is a new record
		$id 		= JRequest::getInt( 'id' );
		$task 		= FD::table( 'Task' );
		$task->load( $id );

		$task->state 	= 1;
		$task->store();

		// @points: groups.task.unresolve
		$points = FD::points();
		$points->assign( 'groups.task.unresolve' , 'com_easysocial' , $my->id );

		return $ajax->resolve();
	}


	/**
	 * Allows caller to create a new task given the milestone id and the group id.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only allow logged in users
		FD::requireLogin();

		// Load up ajax library
		$ajax 	= FD::ajax();

		// Get the current group and logged in user
		$groupId 	= JRequest::getInt( 'groupId' );
		$group 		= FD::group( $groupId );
		$my 		= FD::user();

		if( !$group || !$groupId )
		{
			return $ajax->reject();
		}

		// Test if the current user is a member of this group.
		if( !$group->isMember() )
		{
			return $ajax->reject();
		}

		// Determines if this is a new record
		$id 		= JRequest::getInt( 'id' );
		$task 		= FD::table( 'Task' );
		$task->load( $id );

		$milestoneId	= JRequest::getInt( 'milestoneId' );
		$title 			= JRequest::getVar( 'title' );
		$due 			= JRequest::getVar( 'due' );
		$assignee		= JRequest::getVar( 'assignee' );

		// Save task
		$task->milestone_id 	= $milestoneId;
		$task->uid 				= $group->id;
		$task->type				= SOCIAL_TYPE_GROUP;
		$task->title 			= $title;
		$task->due 				= $due;
		$task->user_id 			= !$assignee ? $my->id : $assignee;
		$task->state 			= SOCIAL_STATE_PUBLISHED;

		if( !$task->title )
		{
			return $ajax->reject();
		}

		$task->store();

		if( !$id )
		{
			// @points: groups.task.create
			$points = FD::points();
			$points->assign( 'groups.task.create' , 'com_easysocial' , $my->id );

			// Get the app
			$app 	= $this->getApp();

			// Get the application params
			$params = $app->getParams();

			if( $params->get( 'notify_new_task' , true ) )
			{
				$milestone 	= FD::table( 'Milestone' );
				$milestone->load( $milestoneId );

				// Get the redirection url
				$url 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'milestoneId' => $milestoneId ) , false );

				$group->notifyMembers( 'task.create' , array( 'userId' => $my->id , 'id' => $task->id , 'title' => $task->title , 'content' => $milestone->description , 'permalink' => $url , 'milestone' => $milestone->title ) );
			}
		}

		// Get the contents
		$theme 	= FD::themes();
		$theme->set( 'group', $group);
		$theme->set( 'task' , $task );
		$theme->set( 'user'	, $my );

		$output 	= $theme->output( 'apps/group/tasks/views/item.task' );
		return $ajax->resolve( $output );
	}
}
