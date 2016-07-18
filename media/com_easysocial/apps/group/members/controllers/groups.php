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

class MembersControllerGroups extends SocialAppsController
{
	/**
	 * Filters the output of members
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function filterMembers()
	{
		// Check for request forgeriess
		ES::checkToken();

		// Ensure that the user is logged in.
		ES::requireLogin();

		// Get the group
		$id = $this->input->get('id', 0, 'int');
		$group = ES::group($id);

		// Get the current filter
		$filter = $this->input->get('filter', '', 'word');

		// Check whether the viewer can really view the contents of pending
		if ($filter == 'pending' && !$group->isAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_NOT_ALLOWED_TO_VIEW_SECTION'));
		}

		$options = array();

		// Get the pagination settings
		$themes = ES::themes();
		$limit = $themes->getConfig()->get('userslimit');

		// Members to display per page.
		$options['limit'] = $limit;

		// List only group admins
		if ($filter == 'admin') {
			$options['admin'] = true;
		}

		// List only pending users
		if ($filter == 'pending') {
			$options['state']	= SOCIAL_GROUPS_MEMBER_PENDING;
		}

		$model = ES::model('Groups');
		$users = $model->getMembers($group->id, $options);
		$pagination	= $model->getPagination();

		$pagination->setVar('view', 'groups');
		$pagination->setVar('layout', 'item');
		$pagination->setVar('id', $group->getAlias());
		$pagination->setVar('appId', $this->getApp()->getAlias() );
		$pagination->setVar('Itemid', FRoute::getItemId('groups', 'item', $group->id));

		if ($pagination && $filter && $filter != 'all') {
			$pagination->setVar('filter', $filter);
		}

		// Load the contents
		$theme = ES::themes();
		$theme->set('pagination', $pagination);
		$theme->set('group', $group);
		$theme->set('users', $users);

		$contents = $theme->output('apps/group/members/groups/default.list');

		if ($pagination) {
			$contents .= '<div class="es-pagination-footer" data-users-pagination>' . $pagination->getListFooter('site') . '</div>';
		}

		return $this->ajax->resolve($contents);
	}

}
