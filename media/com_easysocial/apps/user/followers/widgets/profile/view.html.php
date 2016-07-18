<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class FollowersWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($user)
	{
		// Get application params
		$appParams = $this->getParams();

		// Get the user params
		$params = $this->getUserParams($user->id);

		if ($appParams->get('widget_followers', true)) {
			echo $this->getFollowers($user, $params);
		}

		if ($appParams->get('widget_following', true)) {
			echo $this->getFollowing($user, $params);
		}
	}

	/**
	 * Display a list of followers for the user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFollowers( $user , &$params )
	{
		$appParams = $this->app->getParams();

		if( !$params->get( 'show_profile_followers' , $appParams->get( 'show_profile_followers' , true ) ) )
		{
			return;
		}

		$my	= FD::user();
		if( $my->id != $user->id )
		{
			$privacy = $my->getPrivacy();

			if(! $privacy->validate( 'followers.view' , $user->id ) )
			{
				return;
			}
		}

		$model = FD::model( 'Followers' );

		$users = $model->getFollowers( $user->id );
		$total = $model->getTotalFollowers( $user->id );
		$limit = $params->get('limit', $appParams->get('follower_widget_profile_total', 20));

		$theme 		= FD::themes();

		$theme->set('activeUser', $user);
		$theme->set('total', $total);
		$theme->set('users', $users);
		$theme->set('limit', $limit);

		return $theme->output('themes:/apps/user/followers/widgets/profile/followers');
	}

	/**
	 * Display a list of users this user is following
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFollowing( $user , &$params )
	{
		$appParams = $this->app->getParams();

		if( !$params->get( 'show_profile_following' , $appParams->get( 'show_profile_following' , true ) ) )
		{
			return;
		}
		$my	= FD::user();

		if( $my->id != $user->id )
		{
			$privacy = $my->getPrivacy();

			if(! $privacy->validate( 'followers.view' , $user->id ) )
			{
				return;
			}
		}

		$model = FD::model( 'Followers' );

		$users = $model->getFollowing($user->id);
		$total = $model->getTotalFollowing($user->id);
		$limit = $params->get('limit', $appParams->get('following_widget_profile_total', 20));

		$theme = FD::themes();

		$theme->set('activeUser', $user);
		$theme->set('total', $total);
		$theme->set('users', $users);
		$theme->set('limit', $limit);

		return $theme->output( 'themes:/apps/user/followers/widgets/profile/following' );
	}


}
