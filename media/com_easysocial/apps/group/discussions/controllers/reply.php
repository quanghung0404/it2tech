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

class DiscussionsControllerReply extends SocialAppsController
{
	/**
	 * Accepts a reply as an answer
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function accept()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();


		// Get the reply object
		$id 	= JRequest::getInt( 'id' );
		$reply	= FD::table( 'Discussion' );
		$reply->load( $id );

		// Load the discussion
		$discussion = FD::table( 'Discussion' );
		$discussion->load( $reply->parent_id );

		// Get the group
		$group	= FD::group( $reply->uid );

		// Get the current logged in user.
		$my 	= FD::user();

		// Check if the viewer can accept this reply as an answer.
		if( !$group->isAdmin() && $my->id != $discussion->created_by && !$my->isSiteAdmin() )
		{
			return $ajax->reject();
		}

		// Set this discussion as answered
		$discussion->setAnswered( $reply );

		// @points: groups.discussion.answer
		// Earn points when a reply is marked as an answer
		$points = FD::points();
		$points->assign( 'groups.discussion.answer' , 'com_easysocial' , $reply->created_by );

		// Synchronize the items
		$discussion->sync();

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
		$tpl->setVerb( 'answered' );

		// Set the params to cache the group data
		$registry 	= FD::registry();
		$registry->set( 'group' , $group );
		$registry->set( 'reply' , $reply );
		$registry->set( 'discussion' , $discussion );

		$tpl->setParams( $registry );

		$tpl->setAccess('core.view');

		// Add the stream
		$stream->add( $tpl );

		return $ajax->resolve( $discussion );
	}

	/**
	 * Displays the delete confirmation dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Get the reply object
		$id 	= JRequest::getInt( 'id' );
		$reply	= FD::table( 'Discussion' );
		$reply->load( $id );

		// Load the discussion
		$discussion = FD::table( 'Discussion' );
		$discussion->load( $reply->parent_id );

		// Get the group
		$group		= FD::group( $reply->uid );

		// Get the current logged in user.
		$my 	= FD::user();

		// Check if the viewer can really delete the reply.
		if( !$group->isAdmin() && !$my->isSiteAdmin() )
		{
			return $ajax->reject();
		}

		$state 	= $reply->delete();

		// Synchronize the items
		$discussion->sync();

		return $ajax->resolve( $discussion );
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

		// Get the discussion
		$id 	= JRequest::getInt( 'id' );
		$reply	= FD::table( 'Discussion' );
		$reply->load( $id );

		// Get the group
		$group		= FD::group( $reply->uid );

		// Get the current logged in user.
		$user 	= FD::user();

		$theme 	= FD::themes();
		$output	= $theme->output( 'apps/group/discussions/canvas/dialog.delete' );

		return $ajax->resolve( $output );
	}

	/**
	 * Submits a reply
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function submit()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Get the group
		$groupId 	= JRequest::getInt( 'groupId' );
		$group		= FD::group( $groupId );

		// Get the discussion
		$id 		= JRequest::getInt( 'id' );
		$discussion	= FD::table( 'Discussion' );
		$discussion->load( $id );

		// Get the current user
		$my 		= FD::user();

		// Check whether the viewer can really reply to the discussion
		if( !$group->isMember() )
		{
			return $this->reject();
		}

		// Test for locked discussion.
		if ($discussion->lock && !$group->isAdmin()) {
			$obj 			= new stdClass();
			$obj->message 	= JText::_( 'APP_GROUP_DISCUSSIONS_DISCUSSION_IS_LOCKED' );
			$obj->type 		= SOCIAL_MSG_ERROR;

			return $ajax->reject( $obj );
		}


		// Get the content
		// $content 	= JRequest::getVar( 'content' , '' );
		$content 	= JRequest::getVar( 'content' , '' , 'post' , 'none' , JREQUEST_ALLOWRAW );

		if( empty( $content ) )
		{
			$obj 			= new stdClass();
			$obj->message 	= JText::_( 'APP_GROUP_DISCUSSIONS_EMPTY_REPLY_ERROR' );
			$obj->type 		= SOCIAL_MSG_ERROR;

			return $ajax->reject( $obj );
		}

		$reply 				= FD::table( 'Discussion' );
		$reply->uid 		= $discussion->uid;
		$reply->type 		= $discussion->type;
		$reply->content 	= $content;
		$reply->created_by 	= $my->id;
		$reply->parent_id 	= $discussion->id;
		$reply->state 		= SOCIAL_STATE_PUBLISHED;

		// Save the reply.
		$reply->store();

		if( !$id )
		{
			// @points: groups.discussion.reply
			// Earn points when posting a reply
			$points = FD::points();
			$points->assign( 'groups.discussion.reply' , 'com_easysocial' , $reply->created_by );
		}

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
		$tpl->setVerb( 'reply' );

		// Set the params to cache the group data
		$registry 	= FD::registry();
		$registry->set( 'group' , $group );
		$registry->set( 'reply' , $reply );
		$registry->set( 'discussion' , $discussion );

		$tpl->setParams( $registry );

		$tpl->setAccess('core.view');

		// Add the stream
		$stream->add( $tpl );

		// Update the parent's reply counter.
		$discussion->sync( $reply );

		// Before we populate the output, we need to format it according to the theme's specs.
		$reply->author 		= $my;

		// Load the contents
		$theme 		= FD::themes();

		// Since this reply is new, we don't have an answer for this item.
		$answer 	= false;

		$theme->set('question', $discussion);
		$theme->set('group', $group);
		$theme->set('answer', $answer);
		$theme->set('reply', $reply);

		$contents	= $theme->output( 'apps/group/discussions/canvas/item.reply' );

		// Send notification to group members
		$options 	= array();
		$options[ 'permalink' ]	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $this->getApp()->getAlias() , 'discussionId' => $discussion->id , 'external' => true ) , false );
		$options[ 'title' ]		= $discussion->title;
		$options[ 'content']	= $reply->getContent();
		$options['discussionId']		= $reply->id;
		$options[ 'userId' ]	= $reply->created_by;
		$options[ 'targets' ]	= $discussion->getParticipants( array( $reply->created_by ) );

		$group->notifyMembers( 'discussion.reply' , $options );

		return $ajax->resolve( $contents );
	}

	/**
	 * Allows caller to update a reply
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function update()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Get the discussion
		$id 	= JRequest::getInt( 'id' );
		$reply	= FD::table( 'Discussion' );
		$reply->load( $id );

		// Get the group
		$groupId 	= JRequest::getInt( 'groupId' );
		$group		= FD::group( $reply->uid );

		// Get the discussion
		$discussion	= FD::table( 'Discussion' );
		$discussion->load( $reply->parent_id );

		// Get the current user
		$my 		= FD::user();

		// Check whether the viewer can really reply to the discussion
		if( !$group->isMember() )
		{
			return $this->reject();
		}

		// Get the content
		// $content 	= JRequest::getVar( 'content' , '' );
		$content 	= JRequest::getVar( 'content' , '' , 'post' , 'none' , JREQUEST_ALLOWRAW );

		// var_dump($content);exit;

		if( empty( $content ) )
		{
			$obj 			= new stdClass();
			$obj->message 	= JText::_( 'APP_GROUP_DISCUSSIONS_EMPTY_REPLY_ERROR' );
			$obj->type 		= SOCIAL_MSG_ERROR;

			return $ajax->reject( $obj );
		}

		// Update the content
		$reply->content 	= $content;

		// Save the reply.
		$reply->store();

		// Update the parent's reply counter.
		$discussion->sync( $reply );

		return $ajax->resolve( $reply->getContent() );
	}

}
