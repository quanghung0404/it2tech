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
 * Widgets for group
 *
 * @since	1.0
 * @access	public
 */
class GroupsWidgetsGroups extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom( $groupId )
	{
		// Get the group
		$group = FD::group($groupId);

		$params = $this->app->getParams();


		// Determines if we should display the online group members
		if ($params->get('show_online')) {
			echo $this->getOnlineUsers($group);
		}

		// Determines if we should display friends in this group
		if ($params->get('show_friends')) {
			echo $this->getFriends($group);
		}
	}

	/**
	 * Displays a list of friends in the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFriends($group)
	{
		$theme = ES::themes();

		// Get the current logged in user.
		$my = FD::user();

		$options = array();
		$options['userId'] = $my->id;
		$options['randomize'] = true;
		$options['limit'] = 5;
		$options['published'] = true;

		// Get a list of friends in this group based on the current viewer.
		$model = ES::model('Groups');
		$friends = $model->getFriendsInGroup($group->id, $options);
		$total = $model->getTotalFriendsInGroup($group->id, $options);

		if (!$friends) {
			return;
		}
		
		$theme->set('total', $total);
		$theme->set('friends', $friends);

		return $theme->output('themes:/apps/group/groups/widgets/widget.friends');
	}

	/**
	 * Displays a list of online group members
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getOnlineUsers($group)
	{
		$model = ES::model('Groups');
		$users = $model->getOnlineMembers($group->id);
		$total = count($users);

		$theme = FD::themes();
		$theme->set('total', $total);
		$theme->set('users', $users);

		return $theme->output( 'themes:/apps/group/groups/widgets/widget.online' );
	}
}
