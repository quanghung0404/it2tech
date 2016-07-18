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

class MembersWidgetsGroups extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function afterCategory($group)
	{
		$app = $this->getApp();
		$permalink = FRoute::groups( array('layout'=> 'item', 'id' => $group->getAlias(), 'appId' => $app->getAlias() ));

		$theme = FD::themes();
		$theme->set('permalink', $permalink);
		$theme->set('group', $group);

		echo $theme->output('themes:/apps/group/members/widgets/header');
	}

	/**
	 * Renders the sidebar widget for group members
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function sidebarBottom($groupId)
	{
		if (!$this->app->getParams()->get('show_members', true)) {
			return;
		}

		$theme = FD::themes();

		$params = $this->app->getParams();
		$limit = (int) $params->get('limit', 10);

		// Load up the group
		$group = FD::group($groupId);

		$options = array('state' => SOCIAL_STATE_PUBLISHED, 'limit' => $limit, 'ordering' => 'created', 'direction' => 'desc');

		$model = FD::model('Groups');
		$members = $model->getMembers($group->id, $options);

		$link = FRoute::groups(array('id' => $group->getAlias(),'appId' => $this->app->getAlias(),'layout' => 'item'));

		$theme->set('group', $group);
		$theme->set('members', $members);
		$theme->set('link', $link);

		echo $theme->output('themes:/apps/group/members/widgets/widget.members');
	}
}
