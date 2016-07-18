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

// We need the router
require_once( JPATH_ROOT . '/components/com_content/helpers/route.php' );

/**
 * Profile view for article app
 *
 * @since	1.0
 * @access	public
 */
class MembersViewGroups extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($groupId = null, $docType = null)
	{
		$options = array();

		$group = ES::group($groupId);

		// Get the pagination settings
		$themes = ES::themes();
		$limit = $themes->getConfig()->get('userslimit');

		// Members to display per page.
		$options['limit'] = $limit;

		// Get the current filter.
		$filter = $this->input->get('filter', '', 'word');

		// List only group admins
		if ($filter == 'admin') {
			$options['admin'] = true;
		}

		// List only pending users
		if ($filter == 'pending') {
			$options['state']	= SOCIAL_GROUPS_MEMBER_PENDING;
		}

		$model = FD::model('Groups');
		$users = $model->getMembers($group->id, $options);

		// Set pagination properties
		$pagination	= $model->getPagination();
		$pagination->setVar('view', 'groups');
		$pagination->setVar('layout', 'item');
		$pagination->setVar('id', $group->getAlias() );
		$pagination->setVar('appId', $this->app->getAlias());
		$pagination->setVar('Itemid', ESR::getItemId('groups', 'item', $group->id ) );

		if ($pagination && $filter) {
			$pagination->setVar('filter', $filter);
		}

		// Redirection url when an action is performed on a group member
		$redirectOptions = array('layout' => "item", 'id' => $group->getAlias(), 'appId' => $this->app->getAlias());

		if ($filter) {
			$redirectOptions['filter'] = $filter;
		}

		$redirect = ESR::groups($redirectOptions, false);

		$this->set('redirect', $redirect);
		$this->set('active', $filter);
		$this->set('group', $group);
		$this->set('users', $users);
		$this->set('pagination', $pagination);

		echo parent::display('groups/default');
	}

}
