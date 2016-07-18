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

class DiscussionsViewGroups extends SocialAppsView
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
		$group = ES::group($groupId);

		// Check if the viewer is allowed here.
		if (!$group->canViewItem()) {
			return $this->redirect($group->getPermalink(false));
		}

		// Get app params
		$params = $this->app->getParams();

		$model = FD::model('Discussions');
		$options = array('limit' => $params->get('total', 10));

		$discussions = $model->getDiscussions($group->id , SOCIAL_TYPE_GROUP , $options);
		$pagination = $model->getPagination();
		$pagination->setVar('option' , 'com_easysocial');
		$pagination->setVar('view' , 'groups');
		$pagination->setVar('layout' , 'item');
		$pagination->setVar('id' , $group->getAlias());
		$pagination->setVar('appId' , $this->app->getAlias());

		$this->set('params', $params);
		$this->set('pagination', $pagination);
		$this->set('group', $group);
		$this->set('discussions', $discussions);

		echo parent::display('groups/default');
	}

}
