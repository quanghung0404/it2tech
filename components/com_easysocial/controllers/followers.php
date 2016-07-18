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

// Import parent controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerFollowers extends EasySocialController
{
	protected $app	= null;

	/**
	 * Suggest a list of friend names for a user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 *
	 */
	public function filter()
	{
		// Check for valid tokens.
		FD::checkToken();

		// Check for valid user.
		FD::requireLogin();

		// Load friends model.
		$model 		= FD::model( 'Followers' );

		$limit = FD::themes()->getConfig()->get( 'followersLimit' , 20 );

		// Load the view.
		$view 		= $this->getCurrentView();

		// Get the filter types.
		$type 		= JRequest::getVar( 'type' );

		// Get the user id that we should load for.
		$userId 	= JRequest::getInt( 'id' );

		if( !$userId )
		{
			$userId 	= null;
		}
		// Try to load the target user.
		$user 		= FD::user( $userId );

		$users 		= array();

		if( $type == 'followers' )
		{
			$users 	= $model->getFollowers( $userId, array('limit' => $limit) );
		}

		if( $type == 'following' )
		{
			$users 	= $model->getFollowing( $userId, array('limit' => $limit) );
		}

		if( $type == 'suggest' )
		{
			$users		= $model->getSuggestions($user->id);
		}

		$pagination 	= $model->getPagination();

		// Define those query strings here
		$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'followers' ) );
		$pagination->setVar( 'view'		, 'followers' );
		$pagination->setVar( 'filter' , $type );

		if (FD::user()->id != $userId) {
			$pagination->setVar( 'userid' , $user->getAlias() );
		}

		return $view->call( __FUNCTION__ , $type , $users , $userId, $pagination );
	}

	/**
	 * Unfollows a user
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfollow()
	{
		// Check for valid tokens.
		FD::checkToken();

		// Check for valid user.
		FD::requireLogin();

		// Load friends model.
		$model 		= FD::model( 'Followers' );

		// Load the view.
		$view 		= $this->getCurrentView();

		// Get the user id that we should load for.
		$userId 	= JRequest::getInt( 'id' );

		// Get the current logged in user
		$my 		= FD::user();

		// Loads the followers record
		$follower 	= FD::table( 'Subscription' );
		$follower->load( array( 'uid' => $userId , 'type' => 'user.user' , 'user_id' => $my->id ) );

		if( !$follower->id || !$userId )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FOLLOWERS_INVALID_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Delete the record
		$state 	= $follower->delete();

		$view->call( __FUNCTION__ );
	}


	/**
	 * follows a user
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function follow()
	{
		// Check for valid tokens.
		FD::checkToken();

		// Check for valid user.
		FD::requireLogin();

		// Load friends model.
		$model 		= FD::model( 'Followers' );

		// Load the view.
		$view 		= $this->getCurrentView();

		// Get the user id that we should load for.
		$userId 	= JRequest::getInt( 'id', 0 );

		if (!$userId) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FOLLOWERS_INVALID_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the current logged in user
		$my 		= FD::user();

		// Loads the followers record
		$follower 	= FD::table( 'Subscription' );
		$follower->load( array( 'uid' => $userId , 'type' => 'user.user' , 'user_id' => $my->id ) );

		if ($follower->id){
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FOLLOWERS_ALREADY_FOLLOWED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$follower->uid = $userId;
		$follower->type = 'user.user';
		$follower->user_id = $my->id;

		// Delete the record
		$state 	= $follower->store();

		if (!$state){
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FOLLOWERS_ERROR_FOLLOWING' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$view->call( __FUNCTION__ );
	}

}
