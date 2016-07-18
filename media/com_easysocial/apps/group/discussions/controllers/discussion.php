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

class DiscussionsControllerDiscussion extends SocialAppsController
{
	/**
	 * Displays the lock confirmation dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function confirmLock()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();
		$output	= $theme->output( 'apps/group/discussions/canvas/dialog.lock' );

		return $ajax->resolve( $output );
	}

	/**
	 * Deletes a discussion
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();


		$id 		= JRequest::getInt( 'id' );
		$groupId	= JRequest::getInt( 'groupId' );

		$discussion	= FD::table( 'Discussion' );
		$discussion->load( $id );

		$my 		= FD::user();
		$group 		= FD::group( $groupId );

		if( !$group->isAdmin() && $discussion->created_by != $my->id && !$my->isSiteAdmin() )
		{
			return $this->redirect( $group->getPermalink() );
		}

		// Delete the discussion
		$discussion->delete();

		// @points: groups.discussion.delete
		// Deduct points from the discussion creator when the discussion is deleted
		$points = FD::points();
		$points->assign( 'groups.discussion.delete' , 'com_easysocial' , $discussion->created_by );

		FD::info()->set( JText::_( 'APP_GROUP_DISCUSSIONS_DISCUSSION_DELETED_SUCCESS' ) );

		// After deleting, we want to redirect to the discussions listing
		$url 	= FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias()  , 'appId' => $this->getApp()->id ) , false );

		// Perform a redirection
		$this->redirect( $url );
	}

	/**
	 * Displays the delete confirmation dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function confirmDelete()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		$id 		= JRequest::getInt( 'id' );
		$groupId	= JRequest::getInt( 'groupId' );

		$discussion	= FD::table( 'Discussion' );
		$discussion->load( $id );

		$group 		= FD::group( $groupId );

		$theme 	= FD::themes();

		$theme->set( 'appId'	, $this->getApp()->id );
		$theme->set( 'discussion' , $discussion );
		$theme->set( 'group' , $group );
		$output	= $theme->output( 'apps/group/discussions/canvas/dialog.delete.discussion' );

		return $ajax->resolve( $output );
	}

	/**
	 * Executes the locking of a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function lock()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Load the discussion
		$id 		= JRequest::getInt( 'id' );
		$discussion = FD::table( 'Discussion' );
		$discussion->load( $id );

		// Get the group
		$group		= FD::group( $discussion->uid );

		// Get the current logged in user.
		$my			= FD::user();

		// Check if the viewer can really lock the discussion.
		if( !$group->isAdmin() && !$my->isSiteAdmin() )
		{
			return $this->redirect( $group->getPermalink( false ) );
		}

		// Lock the discussion
		$discussion->lock();

		// Create a new stream item for this discussion
		$stream = FD::stream();

		// Get the stream template
		$tpl		= $stream->getTemplate();

		// Someone just joined the group
		$tpl->setActor( $my->id , SOCIAL_TYPE_USER );

		// Set the context
		$tpl->setContext( $discussion->id , 'discussions' );

		// Set the cluster
		$tpl->setCluster( $group->id , SOCIAL_TYPE_GROUP, $group->type );

		// Set the verb
		$tpl->setVerb( 'lock' );

		// Set the params to cache the group data
		$registry 	= FD::registry();
		$registry->set( 'group' , $group );
		$registry->set( 'discussion' , $discussion );

		$tpl->setParams( $registry );

		$tpl->setAccess( 'core.view' );

		// Add the stream
		$stream->add( $tpl );

		return $ajax->resolve( $discussion );
	}

	/**
	 * Creates a new discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Load the discussion
		$id 		= JRequest::getInt( 'id' );
		$discussion = FD::table( 'Discussion' );
		$discussion->load( $id );

		// Get the current logged in user.
		$my			= FD::user();

		// Get the group
		$groupId 	= JRequest::getInt( 'cluster_id' , 0 );
		$group		= FD::group( $groupId );

		// Only allow owner and admin to modify the
		if( $discussion->id )
		{
			if( $discussion->created_by != $my->id && !$group->isAdmin() && !$my->isSiteAdmin() )
			{
				return $this->redirect( $group->getPermalink( false ) );
			}
		}

		// Check if the user is allowed to create a discussion
		if( !$group->isMember() )
		{
			FD::info()->set( JText::_( 'APP_GROUP_DISCUSSIONS_NOT_ALLOWED_CREATE' ) , SOCIAL_MSG_ERROR );

			// Perform a redirection
			return JFactory::getApplication()->redirect( FRoute::dashboard() );
		}

		// Assign discussion properties
		$discussion->uid 		= $group->id;
		$discussion->type 		= SOCIAL_TYPE_GROUP;
		$discussion->title 		= JRequest::getVar( 'title' , '' );
		$discussion->content 	= JRequest::getVar( 'content' , '' , 'POST' , 'none' , JREQUEST_ALLOWRAW );

		// If discussion is edited, we don't want to modify the following items
		if( !$discussion->id )
		{
			$discussion->created_by = $my->id;
			$discussion->parent_id 	= 0;
			$discussion->hits 		= 0;
			$discussion->state 		= SOCIAL_STATE_PUBLISHED;
			$discussion->votes 		= 0;
			$discussion->lock 		= false;
		}

		$app = $this->getApp();

		// Ensure that the title is valid
		if (!$discussion->title) {
			Foundry::info()->set(JText::_('APP_GROUP_DISCUSSIONS_INVALID_TITLE'), SOCIAL_MSG_ERROR);

			// Get the redirection url
			$url 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'create' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() ) , false );

			return $this->redirect($url);
		}

		// Lock the discussion
		$state 	= $discussion->store();

		if( !$state )
		{
			FD::info()->set( JText::_( 'APP_GROUP_DISCUSSIONS_DISCUSSION_CREATED_FAILED' ) );

			// Get the redirection url
			$url 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'form' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() ) , false );

			return $this->redirect( $url );
		}

		// Process any files that needs to be created.
		$discussion->mapFiles();

		// Get the app
		$app 	= $this->getApp();

		// If it is a new discussion, we want to run some other stuffs here.
		if( !$id )
		{
			// @points: groups.discussion.create
			// Add points to the user that updated the group
			$points = FD::points();
			$points->assign( 'groups.discussion.create' , 'com_easysocial' , $my->id );

			// Create a new stream item for this discussion
			$stream = FD::stream();

			// Get the stream template
			$tpl		= $stream->getTemplate();

			// Someone just joined the group
			$tpl->setActor( $my->id , SOCIAL_TYPE_USER );

			// Set the context
			$tpl->setContext( $discussion->id , 'discussions' );

			// Set the cluster
			$tpl->setCluster( $group->id , SOCIAL_TYPE_GROUP, $group->type );

			// Set the verb
			$tpl->setVerb( 'create' );

			// Set the params to cache the group data
			$registry 	= FD::registry();
			$registry->set( 'group' 	, $group );
			$registry->set( 'discussion', $discussion );

			$tpl->setParams( $registry );

			$tpl->setAccess('core.view');

			// Add the stream
			$stream->add( $tpl );

			// Set info message
			FD::info()->set(false, JText::_( 'APP_GROUP_DISCUSSIONS_DISCUSSION_CREATED_SUCCESS' ), SOCIAL_MSG_SUCCESS );

			// Send notification to group members only if it is new discussion
			$options 	= array();
			$options[ 'permalink' ]	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'discussionId' => $discussion->id , 'external' => true ) , false );
			$options['discussionId']		= $discussion->id;
			$options[ 'discussionTitle' ]	= $discussion->title;
			$options[ 'discussionContent']	= $discussion->getContent();
			$options[ 'userId' ]			= $discussion->created_by;

			$group->notifyMembers( 'discussion.create' , $options );
		}

		// Get the redirection url
		$url 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'discussionId' => $discussion->id ) , false );

		// Perform a redirection
		$this->redirect( $url );
	}

	/**
	 * Retrieves the list of discussions
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getDiscussions()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Load the discussion
		$id 		= JRequest::getInt( 'id' );
		$group 		= FD::group( $id );

		// Get the current logged in user.
		$user 	= FD::user();


		// Check if the viewer can really browse discussions from this group.
		if( !$group->isMember() && ($group->isClosed() || $group->isInviteOnly() ) )
		{
			FD::info()->set( JText::_( 'APP_GROUP_DISCUSSIONS_NOT_ALLOWED_VIEWING' ) , SOCIAL_MSG_ERROR );

			// Perform a redirection
			return $this->redirect( FRoute::dashboard() );
		}

		// Get the current filter type
		$filter 	= JRequest::getWord( 'filter' , 'all' );
		$options 	= array();

		if( $filter == 'unanswered' )
		{
			$options[ 'unanswered' ]	= true;
		}

		if( $filter == 'locked' )
		{
			$options[ 'locked' ]	= true;
		}

		if( $filter == 'resolved' )
		{
			$options[ 'resolved' ]	= true;
		}

		// Get the current group app
		$app 			= $this->getApp();
		$params 		= $app->getParams();

		// Get total number of discussions to display
		$options[ 'limit' ]	= $params->get( 'total' , 10 );

		$model 			= FD::model( 'Discussions' );
		$discussions	= $model->getDiscussions( $group->id , SOCIAL_TYPE_GROUP , $options );
		$pagination 	= $model->getPagination();

		$pagination->setVar( 'view' , 'groups' );
		$pagination->setVar( 'layout' , 'item' );
		$pagination->setVar( 'id' , $group->getAlias() );
		$pagination->setVar( 'appId' , $this->getApp()->id );
		$pagination->setVar( 'filter' , $filter );


		$theme 			= FD::themes();

		$theme->set( 'params'		, $params );
		$theme->set( 'pagination'	, $pagination );
		$theme->set( 'app'			, $app );
		$theme->set( 'group'		, $group );
		$theme->set( 'discussions'	, $discussions );

		$contents 	= $theme->output( 'apps/group/discussions/groups/default.list' );

		$empty 		= empty( $discussions );
		return $ajax->resolve( $contents , $empty );
	}

	/**
	 * Executes the locking of a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function unlock()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Load the discussion
		$id 		= JRequest::getInt( 'id' );
		$discussion = FD::table( 'Discussion' );
		$discussion->load( $id );

		// Get the group
		$group		= FD::group( $discussion->uid );

		// Get the current logged in user.
		$my 		= FD::user();

		// Check if the viewer can really lock the discussion.
		if( !$group->isAdmin() && !$my->isSiteAdmin() )
		{
			return $this->redirect( $group->getPermalink( false ) );
		}

		// Lock the discussion
		$discussion->unlock();

		return $ajax->resolve( $discussion );
	}
}
