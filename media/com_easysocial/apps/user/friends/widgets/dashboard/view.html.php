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

/**
 * Profile view for Notes app.
 *
 * @since	1.0
 * @access	public
 */
class FriendsWidgetsDashboard extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom()
	{
		// Get the current user since the dashboard simply means that the current user is the target
		$my 		= FD::user();

		// Get the user params
		$params 	= $this->getUserParams( $my->id );

		// Determine if we should show online friends
		$onlineFriends 	= true;

		echo $this->getFriends( $params );

		echo $this->getOnlineFriends( $params );

		echo $this->getSuggestedFriends( $params );
	}

	/**
	 * Displays online users widget
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOnlineFriends( &$params )
	{
		$appParams 	= $this->app->getParams();
		$my 		= FD::user();

		if( !$params->get( 'dashboard_show_online' , $appParams->get( 'dashboard_show_online' , true ) ) )
		{
			return;
		}

		// Get the friends model
		$model 		= FD::model( 'Friends' );

		// Get online friends
		$friends 	= $model->getOnlineFriends( $my->id );

		$theme 		= FD::themes();
		$theme->set( 'friends'	, $friends );

		return $theme->output( 'themes:/apps/user/friends/widgets/dashboard/online' );
	}

	/**
	 * Retrieves suggested friends
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSuggestedFriends( &$params )
	{
		$appParams 	= $this->app->getParams();

		if( !$params->get( 'dashboard_show_suggest' , $appParams->get( 'dashboard_show_suggest' , true ) ) )
		{
			return;
		}

		$my 		= FD::user();

		// Get friends model
		$model 		= FD::model( 'Friends' );

		// Get list of friends by the current user.
		$limit      = $params->get( 'dashboard_show_limit', $appParams->get( 'dashboard_show_limit' , 10 ) );
		$friends 	= $model->getSuggestedFriends( $my->id, $limit );

		$theme 		= FD::themes();
		$theme->set( 'friends'	, $friends );

		return $theme->output( 'themes:/apps/user/friends/widgets/dashboard/suggest' );
	}

	/**
	 * Retrieves user's friends
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFriends( &$params )
	{
		$appParams 	= $this->app->getParams();

		if( !$params->get( 'dashboard_show_friends' , $appParams->get( 'dashboard_show_friends' , true ) ) )
		{
			return;
		}

		$my 		= FD::user();


		$limit      = $params->get( 'dashboard_show_limit', $appParams->get( 'dashboard_show_limit' , 10 ) );
		$options	= array( 'limit' => $limit );

		// Get friends model
		$model 		= FD::model( 'Friends' );

		// Get list of friends by the current user.
		$friends 	= $model->getFriends( $my->id, $options );
		$total 		= $model->getTotalFriends( $my->id );

		$theme 		= FD::themes();
		$theme->set( 'friends'	, $friends );
		$theme->set( 'total'	, $total );

		return $theme->output( 'themes:/apps/user/friends/widgets/dashboard/friends' );
	}
}
