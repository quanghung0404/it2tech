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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewNotifications extends EasySocialSiteView
{
	/**
	 * Displays a list of notifications a user has.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the current logged in user.
		$user 		= FD::user();

		// Set the page title
		FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ALL_NOTIFICATIONS' ) );

		// Set breadcrumbs
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ALL_NOTIFICATIONS' ) );

		$config = FD::config();
		$paginationLimit = $config->get('notifications.general.pagination');

		// Get notifications model.
		$options 	= array( 'target_id'	=> $user->id ,
							 'target_type'	=> SOCIAL_TYPE_USER ,
							 'group' 		=> SOCIAL_NOTIFICATION_GROUP_ITEMS,
							 'limit' 		=> $paginationLimit );

		$lib		= FD::notification();
		$items 		= $lib->getItems( $options );

		$this->set( 'items'			, $items );
		$this->set( 'startlimit'	, $paginationLimit );


		parent::display( 'site/notifications/default' );
	}

	/**
	 * Redirects a notification item to the intended location
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function route()
	{
		// The user needs to be logged in to access notifications
		ES::requireLogin();

		// Get the notification id
		$id = $this->input->get('id', 0, 'int');

		// Load up the notification object
		$table = ES::table('Notification');
		$table->load($id);

		// Default redirection URL
		$redirect = FRoute::dashboard(array(), false);

		if (!$id || !$table->id) {
			$this->info->set(JText::_('COM_EASYSOCIAL_NOTIFICATIONS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);

			return $this->redirect($redirect);
		}

		// Ensure that the viewer really owns this item
		if ($table->target_id != $this->my->id) {
			$this->info->set(JText::_('COM_EASYSOCIAL_NOTIFICATIONS_NOT_ALLOWED'), SOCIAL_MSG_ERROR);

			return $this->redirect($redirect);
		}

		// Mark the notification item as read
		$table->markAsRead();

		// Trigger before redirecting to allow apps to modify the url
		$dispatcher = ES::dispatcher();
		$args = array(&$table);

		// Trigger onBeforeStreamDelete
		$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onBeforeNotificationRedirect', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onBeforeNotificationRedirect', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onBeforeNotificationRedirect', $args);

		// Ensure that all &amp; are replaced with &
		$url = str_ireplace('&amp;', '&', $table->url);


		$redirect = FRoute::_($url, false);

		$this->redirect($redirect);
		$this->close();
	}
}
