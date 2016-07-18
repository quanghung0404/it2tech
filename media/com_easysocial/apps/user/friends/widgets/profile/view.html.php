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
class FriendsWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom( $user )
	{
		// Get the user params
		$params 	= $this->getUserParams( $user->id );

		echo $this->getFriends( $user , $params );

		echo $this->getMutualFriends( $user , $params );
	}

	/**
	 * Retrieves friends for profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser	The user that is being viewed
	 * @param	SocialRegistry	The params for this widget
	 * @return
	 */
	public function getFriends( $user , &$params )
	{
		$appParams 	= $this->app->getParams();

		if( !$params->get( 'profile_show_friends' , $appParams->get( 'profile_show_friends' , true ) ) )
		{
			return;
		}

		$my = FD::user();

		if( $my->id != $user->id )
		{
			$privacy = $my->getPrivacy();

			if(! $privacy->validate( 'friends.view' , $user->id ) )
			{
				return;
			}
		}

		// Load a list of the user's friends.
		$model 		= FD::model( 'Friends' );

		$limit      = $params->get( 'profile_show_limit', $appParams->get( 'profile_show_limit' , 10 ));
		$options	= array('limit' => $limit, 'idonly' => true);

		$ids 		= $model->getFriends($user->id, $options);

		// If there's nothing here, skip this altogether
		$friends 	= array();
		$total 		= 0;
		if ($ids) {
			$friends 	= FD::user($ids);
			$total 		= $model->getTotalFriends( $user->id );
		}

		$theme 		= FD::themes();
		$theme->set( 'friends' 		, $friends );
		$theme->set( 'activeUser' 	, $user );
		$theme->set( 'total'		, $total );

		return $theme->output( 'themes:/apps/user/friends/widgets/profile/friends' );
	}

	/**
	 * Displays a list of mutual friends for the user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser	The user that is being viewed
	 * @param	SocialRegistry	The params for this widget
	 * @return
	 */
	public function getMutualFriends( $user , &$params )
	{
		$appParams 	= $this->app->getParams();

		if (!$params->get('profile_show_mutual', $appParams->get('profile_show_mutual', true))) {
			return;
		}

		$my = FD::user();

		// If viewer is viewing his own profile, don't show mutual friends
		if ($my->id == $user->id) {
			return;
		}

		if ($my->id != $user->id) {
			$privacy = $my->getPrivacy();

			if(! $privacy->validate( 'friends.view' , $user->id ) )
			{
				return;
			}
		}

		$model = FD::model( 'Friends' );
		$limit = $params->get( 'profile_show_limit', $appParams->get( 'profile_show_limit' , 10 ) );

		$friends = $model->getMutualFriends( $my->id , $user->id , $limit );
		$total = $model->getMutualFriendCount( $my->id , $user->id );

		$theme = FD::themes();
		$theme->set('friends', $friends);
		$theme->set('total', $total);
		$theme->set('user', $user);

		return $theme->output('themes:/apps/user/friends/widgets/profile/mutual');
	}
}
