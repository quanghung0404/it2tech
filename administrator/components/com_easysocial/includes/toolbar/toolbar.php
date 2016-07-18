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

jimport('joomla.filesystem.file');

class SocialToolbar extends EasySocial
{
	public static function factory()
	{
		$toolbar = new self();

		return $toolbar;
	}

	/**
	 * Deprecated. Use FRoute::getRedirectionUrl($menuId)
	 *
	 * @deprecated 1.3.21
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRedirectionUrl($menuId)
	{
		return FRoute::getRedirectionUrl($menuId);
	}

	/**
	 * Renders the HTML block for the notification bar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function render($options = array())
	{
		$theme = FD::themes();

		// Default options
		$newConversations = false;
		$newRequests = false;
		$newNotifications = false;
		$facebook = false;


		// Display counter related stuffs for logged in user and user that has access to the community
		if ($this->my->id && $this->my->hasCommunityAccess()) {

			// Get a list of new conversations
			$newConversations = $this->my->getTotalNewConversations();

			// Get total pending request count
			$newRequests = $this->my->getTotalFriendRequests();

			// Get new system notifications
			$model = FD::model('Notifications');
			$notificationOptions = array(
											'unread' => true ,
											'target' => array('id' => $this->my->id, 'type' => SOCIAL_TYPE_USER)
										);
			$newNotifications = $model->getCount($notificationOptions);
		}

		// Only render facebook codes if user is not logged in
		if ($this->my->guest) {
			$facebook = FD::oauth('Facebook');
		}

		// Get login redirection url
		$loginMenu = $this->config->get('general.site.login');
		$loginReturn = base64_encode(JRequest::getURI());

		if ($loginMenu != 'null') {
			$loginReturn = FRoute::getMenuLink($loginMenu);
			$loginReturn = base64_encode($loginReturn);
		}

		// Get logout redirection url
		$logoutMenu = $this->config->get('general.site.logout');
		$logoutReturn = FRoute::getMenuLink($logoutMenu);
		$logoutReturn = base64_encode($logoutReturn);

		// Determines if there's any force display options passed in arguments
		$forceOption = isset($options['forceoption']) ? $options['forceoption'] : false;


		// Default this two is enabled.
		$friends = isset($options['friends']) ? $options['friends'] : true;
		$notifications = isset($options['notifications']) ? $options['notifications'] : true;

		// Get other options from arguments
		$toolbar = isset($options['toolbar']) ? $options['toolbar'] : false;
		$dashboard = isset($options['dashboard']) ? $options['dashboard'] : false;
		$conversations = isset($options['conversations']) ? $options['conversations'] : false;
		$search = isset($options['search']) ? $options['search'] : false;
		$login = isset($options['login']) ? $options['login'] : false;
		$profile = isset($options['profile']) ? $options['profile'] : false;

		// Allow caller to determine the popbox position
		$defaultPopboxPosition = 'bottom-left';
		$defaultPopboxPosition = $this->doc->getDirection() == 'rtl' ? 'bottom-right' : $defaultPopboxPosition;
		$popboxPosition = isset($options['modulePopboxPosition']) ? $options['modulePopboxPosition'] : $defaultPopboxPosition;

		// Allow caller to determine the popbox collision
		$defaultPopboxCollision = 'none';
		$popboxCollision = isset($options['modulePopboxCollision']) ? $options['modulePopboxCollision'] : $defaultPopboxCollision;

		// Get template settings
		$template = $theme->getConfig();

		// If the user is guests, ensure that the theme is configured to display toolbar to the guest
		if ($this->my->guest && !$template->get('toolbar_guests')) {
			$toolbar = false;
		}

		// Should we enforce the arguments that is passed in?
		if (!$forceOption) {
			$dashboard = $template->get('toolbar_dashboard') || $dashboard;
			$conversations = $this->config->get('conversations.enabled') || $conversations;
			$search = $template->get('toolbar_search') || $search;
			$login = $template->get('toolbar_login') || $login;
			$profile = $template->get('toolbar_account') || $profile;
			$toolbar = $template->get('toolbar') || $toolbar;

			if ($this->my->guest && !$template->get('toolbar_guests')) {
				$toolbar = false;
			}
		}

		// If toolbar has been disabled altogether skip this
		if (!$toolbar) {
			return;
		}

		// If the user doesn't have access to the community we need to enforce specific options here
		if (!$this->my->hasCommunityAccess()) {
			$friends = false;
			$conversations = false;
			$notifications = false;
			$dashboard = false;
			$search = false;
		}

		if ($search) {
			$searchAdapter = FD::get('Search');

			$filterTypes = $searchAdapter->getTaxonomyTypes();
			$theme->set('filterTypes', $filterTypes);
		}


		// Get the current request variables
		$view = $this->input->get('view', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');
		$userId = $this->input->get('id', 0, 'int');

		$theme->set('newConversations', $newConversations);
		$theme->set('newRequests', $newRequests);
		$theme->set('newNotifications', $newNotifications);
		$theme->set('facebook', $facebook);
		$theme->set('userId', $userId);
		$theme->set('view', $view);
		$theme->set('layout', $layout);
		$theme->set('login', $login);
		$theme->set('profile', $profile);
		$theme->set('search', $search);
		$theme->set('dashboard', $dashboard);
		$theme->set('friends', $friends);
		$theme->set('conversations', $conversations);
		$theme->set('notifications', $notifications);
		$theme->set('toolbar', $toolbar);
		$theme->set('loginReturn', $loginReturn);
		$theme->set('logoutReturn', $logoutReturn);
		$theme->set('popboxPosition', $popboxPosition);
		$theme->set('popboxCollision', $popboxCollision);

		$output = $theme->output('site/toolbar/default');

		return $output;
	}
}
