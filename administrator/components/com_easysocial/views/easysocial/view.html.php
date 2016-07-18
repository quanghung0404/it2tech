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

ES::import('admin:/views/views');

class EasySocialViewEasySocial extends EasySocialAdminView
{
	/**
	 * Main method to display the dashboard view.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display( $tpl = null )
	{
		// Add heading here.
		$this->setHeading('COM_EASYSOCIAL_HEADING_DASHBOARD');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_DASHBOARD');


		// Get total albums
		$photosModel = ES::model('Albums');
		$totalAlbums = $photosModel->getTotalAlbums();

		// Get mailer model
		$mailerModel = ES::model('Mailer');
		$mailStats = $mailerModel->getDeliveryStats();

		// profiles signup data
		$profilesModel = ES::model('Profiles');
		$signupData = $profilesModel->getRegistrationStats();

		// Get reports model
		$reportsModel = ES::model('Reports');
		$totalReports = $reportsModel->getReportCount();

		// Get total videos
		$videosModel = ES::model('Videos');
		$totalVideos = $videosModel->getTotalVideos(array('state' => 'all'));

		// Get total pending users
		$usersModel = ES::model('Users');
		$pendingUsers = $usersModel->getPendingUsers();
		$totalPending = count($pendingUsers);

		// Get total events
		$eventsModel = ES::model('Events');
		$totalEvents = $eventsModel->getTotalEvents();

		// Get total number of groups
		$groupsModel = ES::model('Groups');
		$totalGroups = $groupsModel->getTotalGroups();

		$xAxes = array();

		foreach ($signupData->dates as $date) {
			$xAxes[] = FD::date($date)->format(JText::_('COM_EASYSOCIAL_DATE_DM'));
		}

		// Add translation on the profile title
		foreach ($signupData->profiles as $profile) {
			$profile->title = JText::_($profile->title);
		}

		$this->set('totalEvents', $totalEvents);
		$this->set('totalVideos', $totalVideos);
		$this->set('totalReports', $totalReports);
		$this->set('mailStats', $mailStats);
		$this->set('axes', $xAxes);
		$this->set('signupData', $signupData);
		$this->set('totalPending', $totalPending);
		$this->set('pendingUsers', $pendingUsers);
		$this->set('totalUsers', $usersModel->getTotalUsers());
		$this->set('totalOnline', $usersModel->getTotalOnlineUsers());
		$this->set('totalGroups', $totalGroups);
		$this->set('totalAlbums', $totalAlbums);

		// Add Joomla button
		if ($this->my->authorise('core.admin', 'com_easysocial')) {
			JToolbarHelper::preferences( 'com_easysocial' );
		}

		// Add clear cache button here.
		JToolbarHelper::custom('clearCache', 'trash', '', JText::_('COM_EASYSOCIAL_TOOLBAR_BUTTON_PURGE_CACHE'), false);

		echo parent::display('admin/easysocial/default');
	}

	/**
	 * Post process after clearing cache files
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function clearUrls()
	{
		$this->info->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial');
	}

	/**
	 * Post process after clearing cache files
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function clearCache()
	{
		$this->info->set($this->getMessage());

		$this->redirect( 'index.php?option=com_easysocial' );
	}

	/**
	 * Post process after synchronizing the database columns
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function sync()
	{
		$this->info->set($this->getMessage());

		$this->redirect( 'index.php?option=com_easysocial' );
	}

}
