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

// Include main view file.
FD::import( 'site:/views/views' );

class EasySocialViewProfiles extends EasySocialSiteView
{
	/**
	 * Displays a single profile item layout
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function item()
	{
		$id = $this->input->get('id', 0, 'int');

		// Get the profile object
		$profile = FD::table('Profile');
		$profile->load($id);

		if (!$id || !$profile->id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_404_PROFILE_NOT_FOUND'));
		}

		$model 	= FD::model('Profiles');

		$randomMembers = array();

		// If user does not have community access, we should not display the random members
		if ($profile->community_access) {
			$randomMembers = $model->getMembers($profile->id, array('randomize' => true, 'limit' => 20));
		}

		$totalUsers = $profile->getMembersCount();

		// Get statistics of user registration for this profile type
		$stats  = $model->getRegistrationStats($profile->id);
		$stats  = $stats->profiles[0]->items;

		// Get the stream for this profile
		$startlimit = JRequest::getInt( 'limitstart' , 0 );
		$stream 	= FD::stream();

		$options = array('profileId' => $profile->id);

		if ($startlimit) {
			$options['startlimit'] = $startlimit;
		}

		$stream->get($options);

		// Set the page title to this category
		FD::page()->title($profile->get('title'));

		// Set the breadcrumbs
		FD::page()->breadcrumb($profile->get('title'));

		// Get a list of random albums from this profile
		$albums = $model->getRandomAlbums(array('core' => false, 'withCovers' => true));

		$this->set('albums', $albums);
		$this->set('stream', $stream);
		$this->set('stats', $stats);
		$this->set('randomMembers', $randomMembers);
		$this->set('totalUsers', $totalUsers);
		$this->set('profile', $profile);

		echo parent::display('site/profiles/item');
	}
}
