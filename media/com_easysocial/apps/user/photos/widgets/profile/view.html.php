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
 * Profile view for Notes app.
 *
 * @since	1.0
 * @access	public
 */
class PhotosWidgetsProfile extends SocialAppsWidgets
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
		// Get the user params
		$params = $this->getParams();

		$config = FD::config();

		if (!$config->get('photos.enabled')) {
			return;
		}

		// User might not want to show this app in their profile.
		if (!$params->get('showphotos')) {
			return;
		}

		echo $this->getPhotos($user, $params);
	}


	/**
	 * Display the list of photos a user has uploaded
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPhotos($user, $params)
	{
		// Get photos model
		$model = FD::model('Photos');

		// Get the photo limit from the app setting
		$limit = $params->get('photo_widget_listing_total', 20);

		// limit <- get from the getPhotos function 
		$options = array('uid' => $user->id, 'type' => SOCIAL_TYPE_USER, 'limit' => $limit);
		$photos = $model->getPhotos($options);

		$total = $model->getTotalPhotos($options);

		$this->set('params', $params);
		$this->set('total', $total);
		$this->set('limit', $limit);
		$this->set('user', $user);
		$this->set('photos', $photos);

		return parent::display('widgets/profile/photos');
	}
}
