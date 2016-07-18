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

FD::import( 'site:/views/views' );

class EasySocialViewLogin extends EasySocialSiteView
{
	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown()
	{
		$config 	= FD::config();

		if( $config->get( 'site.general.lockdown.registration' ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Responsible to display the generic login form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display( $tpl = null )
	{
		// If user is already logged in, they should not see this page.
		if (!$this->my->guest) {
			$url = FRoute::dashboard(array(), false);

			return $this->redirect($url);
		}

		// Add page title
		$this->page->title(JText::_('COM_EASYSOCIAL_LOGIN_PAGE_TITLE'));

		// Add breadcrumb
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_LOGIN_PAGE_BREADCRUMB'));

		// Facebook codes.
		$facebook = ES::oauth('Facebook');

		$loginMenu = $this->config->get('general.site.login');

		// Get any callback urls.
		$return = ES::getCallback();

		// If return value is empty, always redirect back to the dashboard
		if (!$return) {
			// Determine if there's a login redirection
			$urlFromCaller = $this->input->getVar('return', '');
			$return = $urlFromCaller;
	
			if (!$urlFromCaller) {
				if ($loginMenu != 'null') {
					$return = FRoute::getMenuLink($loginMenu);
				} else {
					$return	= FRoute::dashboard(array(), false);
				}

				$return = base64_encode($return);
			}
		} else {
			$return = base64_encode($return);
		}

		if ($this->config->get('registrations.enabled')) {
			
			$profileId = $this->config->get('registrations.mini.profile', 'default');

			if ($profileId === 'default') {
				$model = ES::model('Profiles');
				$profileId = $model->getDefaultProfile()->id;
			}


			$options = array('visible' => SOCIAL_PROFILES_VIEW_MINI_REGISTRATION, 'profile_id' => $profileId);

			$fieldsModel = ES::model('fields');
			$fields = $fieldsModel->getCustomFields($options);

			if (!empty($fields)) {
				FD::language()->loadAdmin();

				$fieldsLib = FD::fields();

				$session = JFactory::getSession();
				$registration = ES::table('Registration');
				$registration->load($session->getId());

				$data = $registration->getValues();

				$args = array(&$data, &$registration);

				$fieldsLib->trigger('onRegisterMini', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

				$this->set('fields', $fields);
			}
		}

		$this->set('return', $return);
		$this->set('facebook', $facebook);

		return parent::display('site/login/default');
	}

	/**
	 * Responsible to log the user out from the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function logout()
	{
		$logoutMenu = $this->config->get('general.site.logout');

		// Default redirection url
		$redirect = FRoute::login(array(), false);

		if ($loginMenu != 'null') {
			$redirect = FRoute::getMenuLink($logoutMenu);
		}

		// If the user is not logged in on the first place
		if ($this->my->guest) {
			return $this->redirect($redirect);
		}

		// Perform the log out.
		$error = $this->app->logout();

		// Get the return URL
		return $this->redirect($redirect);
	}
}
