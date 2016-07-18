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

class TasksControllerMilestone extends SocialAppsController
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

		$id 		= JRequest::getInt( 'id' );
		$milestone 	= FD::table( 'Milestone' );
		$milestone->load( $id );

		if( !$id || !$milestone->id || $milestone->uid != $group->id )
		{
			return $ajax->reject();
		}

		$milestone->delete();

		// @points: groups.milestone.delete
		$points = FD::points();
		$points->assign( 'groups.milestone.delete' , 'com_easysocial' , $milestone->user_id );

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

		$user = FD::user();

		// Check if the user is allowed to create a discussion
		if( !$group->isMember() && !$user->isSiteAdmin() )
		{
			return $ajax->reject();
		}

		$theme 	= FD::themes();
		$contents 	= $theme->output( 'apps/group/tasks/views/dialog.delete' );

		$ajax->resolve( $contents );
	}

	/**
	 * Unresolve a milestone
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function unresolve()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		$ajax 		= FD::ajax();

		// Get the current logged in user.
		$my			= FD::user();

		// Get the group
		$groupId 	= JRequest::getInt( 'groupId' , 0 );
		$group		= FD::group( $groupId );

		// Check if the user is allowed to create a discussion
		if( !$group->isMember() && !$my->isSiteAdmin() )
		{
			FD::info()->set( JText::_( 'APP_GROUP_TASKS_NOT_ALLOWED_HERE' ) , SOCIAL_MSG_ERROR );

			// Perform a redirection
			return JFactory::getApplication()->redirect( FRoute::dashboard() );
		}

		// Load up the data
		$id 		= JRequest::getInt( 'id' );
		$milestone 	= FD::table( 'Milestone' );
		$milestone->load( $id );

		if( !$id || !$milestone->id )
		{
			return $ajax->reject();
		}

		$milestone->state 	= 1;

		$milestone->store();

		return $ajax->resolve();
	}

	/**
	 * Resolves a milestone
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function resolve()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		$ajax 		= FD::ajax();

		// Get the current logged in user.
		$my			= FD::user();

		// Get the group
		$groupId 	= JRequest::getInt( 'groupId' , 0 );
		$group		= FD::group( $groupId );

		// Check if the user is allowed to create a discussion
		if( !$group->isMember() && !$my->isSiteAdmin() )
		{
			FD::info()->set( JText::_( 'APP_GROUP_TASKS_NOT_ALLOWED_HERE' ) , SOCIAL_MSG_ERROR );

			// Perform a redirection
			return JFactory::getApplication()->redirect( FRoute::dashboard() );
		}

		// Load up the data
		$id 		= JRequest::getInt( 'id' );
		$milestone 	= FD::table( 'Milestone' );
		$milestone->load( $id );

		if( !$id || !$milestone->id )
		{
			return $ajax->reject();
		}

		$milestone->state 	= 2;

		$milestone->store();

		return $ajax->resolve();
	}

	/**
	 * Creates a new milestone for tasks
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the current logged in user.
		$my			= FD::user();

		// Get the group
		$groupId 	= JRequest::getInt( 'cluster_id' , 0 );
		$group		= FD::group( $groupId );

		// Check if the user is allowed to create a discussion
		if( !$group->isMember() && !$my->isSiteAdmin() )
		{
			FD::info()->set( JText::_( 'Not allowed to create milestone' ) , SOCIAL_MSG_ERROR );

			// Perform a redirection
			return JFactory::getApplication()->redirect( FRoute::dashboard() );
		}

		// Get the posted data
		$post 		= JRequest::get( 'post' );

		// Get the milestone data
		$id 		= JRequest::getInt( 'id' );
		$milestone 	= FD::table( 'Milestone' );
		$milestone->load( $id );

		$milestone->title 			= JRequest::getVar( 'title' );
		$milestone->uid 			= (int) $group->id;
		$milestone->type 			= SOCIAL_TYPE_GROUP;
		$milestone->state 			= SOCIAL_STATE_PUBLISHED;
		if( $group->isMember() )
		{
			$milestone->user_id 		= JRequest::getInt( 'user_id' );
		}
		$milestone->description 	= JRequest::getVar( 'description' );
		$milestone->due 			= JRequest::getVar( 'due' );
		$milestone->owner_id 		= (int) $my->id;
		$milestone->store();

		// Get the app
		$app 	= $this->getApp();

		// Get the application params
		$params = $app->getParams();

		// Get the redirection url
		$url 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'milestoneId' => $milestone->id ) , false );

		// If this is new milestone, perform some tasks
		if( !$id )
		{
			// Generate a new stream
			if( $params->get( 'stream_milestone' , true ) )
			{
				$milestone->createStream( 'createMilestone' );
			}

			if( $params->get( 'notify_milestone' , true ) )
			{
				$group->notifyMembers( 'milestone.create' , array( 'userId' => $my->id , 'id' => $milestone->id , 'title' => $milestone->title , 'content' => $milestone->getContent(), 'permalink' => $url ) );
			}
		}

		// If it is a new item, we want to run some other stuffs here.
		if( !$id )
		{
			// @points: groups.milestone.create
			// Add points to the user that updated the group
			$points = FD::points();
			$points->assign( 'groups.milestone.create' , 'com_easysocial' , $my->id );
		}

		FD::info()->set( JText::_( 'APP_GROUP_TASKS_MILESTONE_CREATED' ) );

		// Perform a redirection
		$this->redirect( $url );
	}
}
