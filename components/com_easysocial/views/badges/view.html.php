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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import parent view
FD::import( 'site:/views/views' );

class EasySocialViewBadges extends EasySocialSiteView
{
	/**
	 * Default method to display the registration page.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display($tpl = null)
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Set the page title
		FD::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_BADGES'));

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_BADGES'));
	

		if (!$this->config->get('badges.enabled')) {
			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Get list of badges.
		$model = FD::model('Badges');

		// Get number of badges to display per page.
		$limit = FD::themes()->getConfig()->get('badgeslimit');

		$options = array( 'state' => SOCIAL_STATE_PUBLISHED , 'limit' => $limit );
		$badges = $model->getItems($options);
		$pagination	= $model->getPagination();

		$this->set( 'pagination', $pagination );
		$this->set( 'badges' 	, $badges );

		parent::display( 'site/badges/default' );
	}

	/**
	 * Default method to display the registration page.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	function item( $tpl = null )
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		$config 	= FD::config();

		if( !$config->get( 'badges.enabled' ) )
		{
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		// Get id of badge
		$id 	= JRequest::getInt( 'id' );

		$badge 	= FD::table( 'Badge' );
		$badge->load( $id );

		if( !$id || !$badge->id )
		{
			FD::info()->set( JText::_( 'COM_EASYSOCIAL_BADGES_INVALID_BADGE_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::badges() );

			$this->close();
		}

		// Load the badge language
		$badge->loadLanguage();

		// Set the page title
		FD::page()->title( $badge->get( 'title' ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_BADGES' ) , FRoute::badges() );
		FD::page()->breadcrumb( $badge->get( 'title' ) );

		// Get the badges model
		$options = array(
			'start' => 0,
			'limit' => FD::themes()->getConfig()->get( 'achieverslimit', 50 )
		);
		$achievers 	= $badge->getAchievers( $options );

		$totalAchievers = $badge->getTotalAchievers();

		$this->set( 'totalAchievers', $totalAchievers );
		$this->set( 'achievers'	, $achievers );
		$this->set( 'badge'		, $badge );

		parent::display( 'site/badges/default.item' );
	}

	/**
	 * Displays a list of badges the user has achieved
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function achievements()
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		if (!$this->config->get('badges.enabled')) {
			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Get the current user id that should be displayed
		$userId = $this->input->get('userid', 0, 'int');
		$userId = $userId == 0 ? null : $userId;
		$user = FD::user($userId);

		// If user is not found, we need to redirect back to the dashboard page
		if (!$user->id) {
			return $this->redirect(FRoute::dashboard(array(), false));
		}

		$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_ACHIEVEMENTS');

		if (!$user->isViewer()) {
			$title = JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_ACHIEVEMENTS_USER', $user->getName());

			// Let's test if the current viewer is allowed to view this user's achievements.
			$privacy = $this->my->getPrivacy();
			$allowed = $privacy->validate('achievements.view', $user->id, SOCIAL_TYPE_USER);

			if (!$allowed) {
				$this->set('user', $user);
				parent::display('site/badges/restricted');

				return;
			}
		}

		// Set the page title
		FD::page()->title($title);

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ACHIEVEMENTS' ) , FRoute::badges(array('userid' => $user->id, 'layout' => 'achievements')));

		// Load admin translations
		FD::language()->loadAdmin();

		// @TODO: Check for privacy
		$model = FD::model('Badges');
		$badges = $model->getBadges($user->id);
		$totalBadges = count($badges);

		$this->set('totalBadges', $totalBadges);
		$this->set('badges', $badges);
		$this->set('user', $user);

		parent::display('site/badges/achievements');
	}
}
