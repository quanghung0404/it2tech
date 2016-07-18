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
class FilesWidgetsGroups extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($groupId)
	{
		// Get the params of the group
		$params = $this->app->getParams();

		// If the widget has been disabled we shouldn't display anything
		if (!$params->get('widget')) {
			return;
		}

		$group = FD::group($groupId);
		
		$theme = FD::themes();
		$limit = $params->get( 'widget_total' , 5 );

		$model = ES::model('Files');
		$options = array('limit' => $limit);
		$files = $model->getFiles($group->id, SOCIAL_TYPE_GROUP, $options);

		if (!$files) {
			return;
		}
		
		$total = $model->getTotalFiles($group->id, SOCIAL_TYPE_GROUP);

		$theme->set('total', $total);
		$theme->set('files', $files);

		echo $theme->output( 'themes:/apps/group/files/widgets/widget.files' );
	}
}
