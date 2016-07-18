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
defined('_JEXEC') or die('Unauthorized Access');

class VideosWidgetsGroups extends SocialAppsWidgets
{
	/**
	 * Display admin actions for the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function groupAdminStart(SocialGroup $group)
	{
		$theme = ES::themes();
		$theme->set('group', $group);
		$theme->set('app', $this->app);

		echo $theme->output('themes:/apps/group/videos/widgets/widget.menu');
	}

	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($groupId, $group)
	{
		// Get recent albums
		$output = $this->getVideos($group);

		echo $output;
	}


	/**
	 * Display the list of photo albums
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVideos(SocialGroup &$group)
	{
		$params = $this->getParams();

		// // If the app is disabled, do not continue
		// if (!$params->get('widgets_album', true)) {
		// 	return;
		// }

		$model = ES::model('Videos');

		// Determines the total number of albums to retrieve
		$limit = $params->get('limit', 10);

		$options = array();
		$options['uid'] = $group->id;
		$options['type'] = SOCIAL_TYPE_GROUP;

		// Get the videos for the group
		$videos = $model->getVideos($options);

		$totalVideos = $model->getTotalVideos($options);

		$this->set('totalVideos', $totalVideos);
		$this->set('videos', $videos);
		$this->set('group', $group);

		return parent::display('widgets/widget.videos');
	}
}
