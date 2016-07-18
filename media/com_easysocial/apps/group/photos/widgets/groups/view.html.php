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

class PhotosWidgetsGroups extends SocialAppsWidgets
{
	public function groupAdminStart($group)
	{
		$category = $group->getCategory();
		$config = FD::config();

        if (!$config->get('photos.enabled', true) || !$category->getAcl()->get('photos.enabled', true) || !$group->getParams()->get('photo.albums', true)) {
            return;
        }

		$this->set( 'group' , $group );
		$this->set( 'app' , $this->app );

		echo parent::display( 'widgets/widget.menu' );
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
		$output = $this->getAlbums($group);

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
	public function getAlbums(&$group)
	{
		$params = $this->getParams();

		// If the app is disabled, do not continue
		if (!$params->get('widgets_album', true) || !$group->getCategory()->getAcl()->get('photos.enabled', true) || !$group->getParams()->get('photo.albums', true)) {
			return;
		}

		$model = FD::model('Albums');

		// Determines the total number of albums to retrieve
		$limit = $params->get('limit', 10);

		$options = array(
			'order' => 'assigned_date',
			'direction' => 'desc',
			'limit' => $limit
		);		

		// Get the list of albums from this group
		$albums = $model->getAlbums($group->id, SOCIAL_TYPE_GROUP, $options);
		$options = array('uid' => $group->id, 'type' => SOCIAL_TYPE_GROUP);

		if (!$albums) {
			return;
		}
		
		// Get the total number of albums
		$total = $model->getTotalAlbums($options);

		$this->set('total', $total);
		$this->set('albums', $albums);
		$this->set('group', $group);

		return parent::display('widgets/widget.albums');
	}
}
