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

class GroupsWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Display groups as a widget
	 *
	 * @since	1.2
	 * @access	public
	 * @param	Socialuser
	 * @return
	 */
	public function sidebarBottom($user)
	{
		$config = FD::config();
		$params = $this->getParams();

		if ($params->get('widget_profile', true) && $config->get('groups.enabled')) {
			echo $this->getGroups($user, $params);
		}
	}

	/**
	 * Retrieves the list of groups
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups($user, $params)
	{
		$model = FD::model('Groups');
		$isUserAViewer = FD::user()->id == $user->id ? true : false;


		$groupOptions = array('uid' => $user->id, 'state' => SOCIAL_CLUSTER_PUBLISHED);

		// if $user is the current viewer, we will get all the groups
		if ($isUserAViewer) {
			$groupOptions['types'] = 'all';
		}

		$groups = $model->getGroups($groupOptions);

		$limit = $params->get('widget_profile_total', 5);

		// Get the total groups the user owns
		$groupCntOptions = array('types' => 'open');

		// if $user is the current viewer, we will get all the groups
		if ($isUserAViewer) {
			$groupCntOptions = array();
		}

		$total = $user->getTotalGroups($groupCntOptions);

		$theme = FD::themes();
		$theme->set('user', $user);
		$theme->set('limit', $limit);
		$theme->set('groups', $groups);
		$theme->set('total', $total);

		return $theme->output('themes:/apps/user/groups/widgets/profile/groups');
	}
}
