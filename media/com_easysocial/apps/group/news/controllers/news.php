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

class NewsControllerNews extends SocialAppsController
{
	/**
	 * Processes deletion
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

		$ajax 		= FD::ajax();

		$id 		= JRequest::getInt( 'id' );
		$groupId	= JRequest::getInt( 'groupId' );
		$group		= FD::group( $groupId );

		if( !$group->isAdmin() )
		{
			return $this->redirect( $group->getPermalink( false ) );
		}

		// Load the news
		$news 		= FD::table( 'GroupNews' );
		$news->load( $id );

		if( !$group->isAdmin() )
		{
			return $this->redirect( $group->getPermalink( false ) );
		}

		$state 	= $news->delete();

		// @points: groups.news.delete
		// Deduct points from the news creator when the news is deleted.
		$points = FD::points();
		$points->assign( 'groups.news.delete' , 'com_easysocial' , $news->created_by );

		$message = $state ? JText::_( 'APP_GROUP_NEWS_DELETED_SUCCESS' ) : JText::_( 'APP_GROUP_NEWS_DELETED_FAILED' );
		FD::info()->set( $message , SOCIAL_MSG_SUCCESS );

		$this->redirect( $group->getPermalink( false ) );
	}

	/**
	 * Triggers the empty content error
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function emptyContent()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		$theme 	= FD::themes();
		$output = $theme->output( 'apps/group/news/canvas/dialog.empty' );

		return $ajax->resolve( $output );
	}

	/**
	 * Displays confirmation dialog to delete a news
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function confirmDelete()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		$id 		= JRequest::getInt( 'id' );
		$groupId	= JRequest::getInt( 'groupId' );
		$group		= FD::group( $groupId );

		if( !$group->isAdmin() )
		{
			return $ajax->reject();
		}

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );
		$theme->set( 'appId' , $this->getApp()->id );
		$theme->set( 'id' , $id );
		$output	= $theme->output( 'apps/group/news/canvas/dialog.delete' );

		return $ajax->resolve( $output );
	}

	/**
	 * Retrieves the new article form
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function save()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get the posted data
		$post = JRequest::get( 'post' );

		// Determines if this is an edited news.
		$id = JRequest::getInt('newsId');

		// Load up the news obj
		$news 	= FD::table( 'GroupNews' );
		$news->load( $id );

		$message 	= !$news->id ? JText::_( 'APP_GROUP_NEWS_CREATED_SUCCESSFULLY' ) : JText::_( 'APP_GROUP_NEWS_UPDATED_SUCCESSFULLY' );

		// Get the group
		$groupId 	= JRequest::getInt( 'cluster_id' );
		$group 		= FD::group( $groupId );
		$my 		= FD::user();

		// Get the app id
		$app 		= $this->getApp();

		if (!$group->isAdmin() && !FD::user()->isSiteAdmin()) {
			$url = $group->getPermalink(false);
			return $this->redirect($url);
		}

		$options					= array();
		$options[ 'title' ]			= JRequest::getVar( 'title' );
		$options[ 'content' ]		= JRequest::getVar( 'news_content' , '', 'post', 'string', JREQUEST_ALLOWRAW );
		$options[ 'comments' ]		= JRequest::getBool( 'comments' );
		$options[ 'state' ]			= SOCIAL_STATE_PUBLISHED;

		// Only bind this if it's a new item
		if( !$news->id )
		{
			$options[ 'cluster_id' ]	= $groupId;
			$options[ 'created_by' ]	= $my->id;
			$options[ 'hits' ]			= 0;
		}

		// Bind the data
		$news->bind( $options );

		// Check if there are any errors
		if( !$news->check() )
		{
			FD::info()->set( $news->getError() , SOCIAL_MSG_ERROR );

			$url 	= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'form' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() ) , false );

			return $this->redirect( $url );
		}

		// If everything is okay, bind the data.
		$news->store();

		// Redirect to the appropriate page now
		$url 		= FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'newsId' => $news->id ) , false );

		// If it is a new item, we want to run some other stuffs here.
		if (!$id) {
			// @points: groups.news.create
			// Add points to the user that updated the group
			$points = FD::points();
			$points->assign( 'groups.news.create' , 'com_easysocial' , $my->id );

			$app 		= $this->getApp();
			$permalink 	= $app->getPermalink('canvas', array('customView' => 'item', 'groupId' => $group->id, 'newsId' => $news->id));
			// Notify users about the news.
			$options 	= array( 'userId' => $my->id , 'permalink' => $permalink,'newsId' => $news->id, 'newsTitle' => $news->title , 'newsContent' => strip_tags( $news->content ) );

			$group->notifyMembers('news.create', $options);
		}

		FD::info()->set( $message , SOCIAL_MSG_SUCCESS );

		// Perform a redirection
		$this->redirect($url);
	}

}
