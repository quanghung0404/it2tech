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

FD::import('site:/views/views');

class EasySocialViewRegistration extends EasySocialSiteView
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
		$config = FD::config();

		if ($config->get('general.site.lockdown.registration')) {
			return false;
		}

		return true;
	}

	/**
	 * Default method to display the registration page.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display($tpl = null)
	{
		$info   = FD::info();

		// Clear cache as soon as the user registers on the site.
		$cache = JFactory::getCache();
		$cache->clean('page');
		$cache->clean('_system');

		// Do not allow users to proceed if registrations are disabled
		if (!$this->config->get('registrations.enabled')) {
			$info->set(JText::_('COM_EASYSOCIAL_ERROR_REGISTRATION_DISABLED'), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::login(array(), false));
		}

		// Do not allow registered users to create account.
		$user	= FD::user();

		// Checks if the user is already registered.
		if ($user->isRegistered()) {
			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Retrieve profile id from the query string
		$profileId = $this->input->get('profile_id', 0, 'int');

		// If there's a profile id, we need to redirect them to the appropriate step
		if ($profileId) {
			$redirectOptions = array('controller' => 'registration', 'task' => 'selectType', 'profile_id' => $profileId);
			$redirection = FRoute::registration($redirectOptions, false);

			return $this->redirect($redirection);
		}

		// Detect for an existing registration session.
		$session = JFactory::getSession();

		// Check if this user was invited by someone else
		$inviteId = $this->input->get('invite', 0, 'int');

		if ($inviteId) {
			$session->set('invite', $inviteId, SOCIAL_SESSION_NAMESPACE);
		}

		// Check if there is a quick parameter or not and set it into the session
		$session->set('quick', JRequest::getBool('quick', false), SOCIAL_SESSION_NAMESPACE);

		// Load up necessary model and tables.
		$registration	= FD::table('Registration');

		// Purge expired session data for registrations.
		$model = FD::model('Registration');
		$model->purgeExpired();

		// If user doesn't have a record in registration yet, we need to create this.
		if (!$registration->load($session->getId())) {
			$registration->set('session_id', $session->getId());
			$registration->set('created', FD::get('Date')->toMySQL());
			$registration->set('profile_id', $profileId);

			if (!$registration->store()) {
				$this->setError($registration->getError());
				return false;
			}
		}

		// If there is only 1 profile type, we don't really need to show the profile type selection
		$profileModel = FD::model('Profiles');
		$options = array('state'	=> SOCIAL_STATE_PUBLISHED,
							'ordering' => 'ordering',
							'limit' => SOCIAL_PAGINATION_NO_LIMIT,
							'totalUsers' => $this->config->get('registrations.profiles.usersCount'),
							'validUser' => true,
							'registration' => true
					);

		$profiles = $profileModel->getProfiles($options);

		// Add the "users" to the profiles
		foreach ($profiles as $profile) {
			$profile->users = $profileModel->getMembers($profile->id, array('limit' => 10, 'randomize' => true));
		}

		// If there's only 1 profile type, we should just ignore this step and load the steps page.
		if (count($profiles) == 1) {

			$profile = $profiles[0];

			// Store the profile type id into the session.
			$session->set('profile_id', $profile->id, SOCIAL_SESSION_NAMESPACE);

			// Set the current profile type id.
			$registration->profile_id 	= $profile->id;

			// When user accesses this page, the following will be the first page
			$registration->step 	= 1;

			// Add the first step into the accessible list.
			$registration->addStepAccess(1);

			// Let's save this into a temporary table to avoid missing data.
			$registration->store();

			$this->steps();
			return;
		}

		// Set the page title
		FD::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_REGISTRATION_SELECT_PROFILE'));

		// Try to retrieve the profile id from the session.
		$profileId  = $session->get('profile_id', $profileId, SOCIAL_SESSION_NAMESPACE);

		// The first profile selection page is always the first in the progress bar.
		$this->set('currentStep', SOCIAL_REGISTER_SELECTPROFILE_STEP);
		$this->set('profileId', $profileId);
		$this->set('profiles', $profiles);

		return parent::display('site/registration/default');
	}

	/**
	 * This is the first entry point when the social site redirects back to this callback.
	 * It is responsible to close the popup and redirect to the appropriate url.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function oauthDialog()
	{
		// Get allowed clients
		$allowedClients	= array_keys((array) $this->config->get('oauth'));

		// Get the current client.
		$oauthClient = $this->input->get('client', '', 'word');

		if (!in_array($oauthClient, $allowedClients)) {
			FD::info()->set(false, JText::sprintf('COM_EASYSOCIAL_OAUTH_INVALID_OAUTH_CLIENT_PROVIDED', $oauthClient), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::login(array(), false));
		}

		// Get the oauth client object.
		$client = FD::oauth($oauthClient);

		// Detect if the user has already registered with the site.
		if ($client->isRegistered()) {
			// We need to update the token
			$client->updateToken();

			$client->login();

			// let get user data again.
			$user = FD::user();

			// @TODO:: here we will redirect user to our password reset page. awesome possum.
			if ($user->require_reset) {
				$url 	= FRoute::account( array( 'layout' => 'requirePasswordReset' ) , false );
			} else {
				// Determine which URL to redirect the user to based on the settings
				$url = FRoute::getMenuLink($this->config->get('general.site.login'));

				if ($url === false) {
				// Default URL redirection
					$url = FRoute::dashboard(array(), false);

					// Determine if there a referer URL
					$callback = FRoute::referer();

					// Check whether the callback url is it equal to facebook url
					if ($callback == 'https://www.facebook.com/') {
						$callback = Froute::referrer($url);
					}

					if ($callback) {
						$url = $callback;
					}
				}
			}

			$redirect = $url;
		} else {
			// Get the access
			$access = $client->getAccess();

			// Set the access token on the session
			$key = $oauthClient . '.token';
			$session = JFactory::getSession();
			$session->set($key, $access->token, SOCIAL_SESSION_NAMESPACE);

			$redirect = FRoute::registration(array('layout' => 'oauth', 'client' => $oauthClient), false);
		}

		$this->set('redirect', $redirect);

		parent::display('site/registration/oauth.popup');
	}

	/**
	 * Displays the first step of user signing up with oauth
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function oauth()
	{
		// If user is already logged in here, they shouldn't be allowed on this page.
		if (!$this->my->guest) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_OAUTH_YOU_ARE_ALREADY_LOGGED_IN'), SOCIAL_MSG_ERROR);

			FD::info()->set($this->getMessage());

			$this->redirect(FRoute::dashboard(array(), false));
		}

		// Get allowed clients
		$allowedClients	= array_keys((array) $this->config->get('oauth'));

		// Get the current client.
		$oauthClient = $this->input->get('client', '', 'word');

		if (!in_array($oauthClient, $allowedClients)) {
			FD::info()->set(false, JText::sprintf('COM_EASYSOCIAL_OAUTH_INVALID_OAUTH_CLIENT_PROVIDED', $oauthClient), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::login(array(), false));
		}

		// Get the oauth client object.
		$client = FD::oauth($oauthClient);

		// Add page title
		$title = JText::sprintf('COM_EASYSOCIAL_OAUTH_PAGE_TITLE', ucfirst($oauthClient));
		$this->page->title($title);

		// Add breadcrumbs
		$this->page->breadcrumb($title);

		// Check configuration if the registration mode is set to simplified or normal.
		$registrationType = $this->config->get('oauth.' . $oauthClient . '.registration.type');

		if ( $registrationType == 'simplified') {
			$createUrl 	= FRoute::raw('index.php?option=com_easysocial&controller=registration&task=oauthSignup&client=' . $oauthClient);
		} else {
			$createUrl	= FRoute::registration(array('layout' => 'oauthSelectProfile', 'client' => $oauthClient));
		}

		// Check if import avatar option is enabled
		$importAvatar = $this->config->get('oauth.' . $oauthClient . '.registration.avatar');

		// Check if import cover option is enabled
		$importCover = $this->config->get('oauth.' . $oauthClient . '.registration.cover');

		// Get user's meta
		try {
			$meta = $client->getUserMeta();
		} catch (Exception $e) {
			$app = JFactory::getApplication();

			// Use dashboard here instead of login because api error calls might come from after user have successfully logged in
			$url = FRoute::dashboard( array(), false );

			$message = (object) array(
				'message' => JText::sprintf( 'COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage() ),
				'type' => SOCIAL_MSG_ERROR
			);

			FD::info()->set($message);

			$app->redirect( $url );
			$app->close();
		}


		$this->set('meta', $meta);
		$this->set('createUrl', $createUrl);
		$this->set('clientType', $oauthClient);
		$this->set('importAvatar', $importAvatar);
		$this->set('importCover', $importCover);

		parent::display('site/registration/oauth');
	}

	/**
	 * Displays the first step of user signing up with oauth
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function oauthSelectProfile()
	{
		$config 	= FD::config();

		// Get allowed clients
		$allowedClients	= array_keys((array) $config->get('oauth'));

		// Get the current client.
		$oauthClient 	= JRequest::getWord('client');

		if (!in_array($oauthClient, $allowedClients)) {
			FD::info()->set(false, JText::sprintf('COM_EASYSOCIAL_OAUTH_INVALID_OAUTH_CLIENT_PROVIDED', $oauthClient), SOCIAL_MSG_ERROR);
			return $this->redirect(FRoute::login(array(), false));
		}

		// Get the oauth client object.
		$client 	= FD::oauth($oauthClient);

		// If there is only 1 profile type, we don't really need to show the profile type selection
		$profileModel	= FD::model('Profiles');
		$options		= array('state'		=> SOCIAL_STATE_PUBLISHED,
								'ordering' 		=> 'ordering',
								'limit'			=> SOCIAL_PAGINATION_NO_LIMIT,
								'totalUsers'	=> $config->get('registrations.profiles.usersCount'),
								'validUser'		=> true,
								'registration'	=> SOCIAL_STATE_PUBLISHED
							);
		$profiles 		= $profileModel->getProfiles($options);

		$this->set('profiles'		, $profiles);
		$this->set('clientType'	, $oauthClient);

		parent::display('site/registration/oauth.profile');
	}

	/**
	 * Post processing after linking account
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function oauthLinkAccount($clientType)
	{
		if ($this->hasErrors()) {
			$url 	= FRoute::registration(array('layout' => 'oauth', 'client' => $clientType), false);

			return $this->redirect($url);
		}

		FD::info()->set($this->getMessage());

		// If it was successfully, we need to redirect to the dashboard area.
		return $this->redirect(FRoute::dashboard(array(), false));
	}

	/**
	 * Displays the first step of user signing up with oauth
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function oauthPreferences($profileId = '', $username = '', $email = '', $oauthClient = '')
	{
		$config 	= FD::config();

		if ($this->hasErrors()) {
			FD::info()->set($this->getMessage());
		}

		// Get allowed clients
		$allowedClients	= array_keys((array) $config->get('oauth'));

		// Get the profile id.
		$profileId 		= JRequest::getInt('profile', $profileId);

		// Get the current client.
		$oauthClient 	= JRequest::getWord('client', $oauthClient);

		if (!in_array($oauthClient, $allowedClients)) {
			FD::info()->set(false, JText::sprintf('COM_EASYSOCIAL_OAUTH_INVALID_OAUTH_CLIENT_PROVIDED', $oauthClient), SOCIAL_MSG_ERROR);
			return $this->redirect(FRoute::login(array(), false));
		}

		// Add page title
		$title 		= JText::sprintf('COM_EASYSOCIAL_OAUTH_PAGE_TITLE_INFO', ucfirst($oauthClient));
		FD::page()->title($title);

		// Add breadcrumbs
		$url 		= FRoute::registration(array('view' => 'registration', 'layout' => 'oauth', 'client' => $oauthClient));
		FD::page()->breadcrumb(JText::sprintf('COM_EASYSOCIAL_OAUTH_PAGE_TITLE', ucfirst($oauthClient)), $url);
		FD::page()->breadcrumb($title);

		// We might reach here from oauth.profile from normal registration, and in that case, username and email might be empty
		if (empty($username) || empty($email)) {
			$client = FD::oauth($oauthClient);

			try {
				$meta = $client->getUserMeta();
			} catch (Exception $e) {
				$app = JFactory::getApplication();

				// Use dashboard here instead of login because api error calls might come from after user have successfully logged in
				$url = FRoute::dashboard( array(), false );

				$message = (object) array(
					'message' => JText::sprintf( 'COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage() ),
					'type' => SOCIAL_MSG_ERROR
				);

				FD::info()->set( $message );

				$app->redirect( $url );
				$app->close();
			}



			if (empty($username)) {
				$username = $meta['username'];
			}

			if (empty($email)) {
				$email = $meta['email'];
			}
		}

		// @TODO: Check if the username has been used, if it does, generate a username for him.
		$model 			= FD::model('Registration');

		$usernameExists	= $model->isUsernameExists($username);

		if ($usernameExists && !FD::config()->get('registrations.emailasusername')) {
			// Generate username
			$username = $model->generateUsername($username);
		}

		$emailExists = $model->isEmailExists($email);

		$this->set('emailExists'		, $emailExists);
		$this->set('usernameExists' 	, $usernameExists);
		$this->set('username'			, $username);
		$this->set('email'				, $email);
		$this->set('profileId'			, $profileId);
		$this->set('clientType'			, $oauthClient);

		parent::display('site/registration/oauth.preferences');
	}

	/**
	 * Post process by redirecting user to the login page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function oauthSignup()
	{
		FD::info()->set($this->getMessage());

		$url	= FRoute::dashboard(array(), false);

		return $this->redirect($url);
	}

	/**
	 * This is where the magic begins where all steps are configurable from the back end.
	 * We load steps based on the configured options.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function steps()
	{
		$config = FD::config();
		$info 	= FD::info();

		// Clear cache as soon as the user registers on the site.
		$cache 		= JFactory::getCache();
		$cache->clean('page');
		$cache->clean('_system');

		// Ensure that registrations is enabled.
		if (!$config->get('registrations.enabled')) {
			$info->set(JText::_('COM_EASYSOCIAL_ERROR_REGISTRATION_DISABLED'), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::login(array(), false));
		}

		// Do not allow registered users to create account.
		$user	= FD::user();

		// Checks if the user is already registered.
		if ($user->isRegistered()) {
			$info->set(JText::_('COM_EASYSOCIAL_ERROR_REGISTRATION_ALREADY_A_REGISTERED_MEMBER'), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Check if this user was invited by someone else
		$inviteId = $this->input->get('invite', 0, 'int');

		$session = JFactory::getSession();

		if ($inviteId) {
			$session->set('invite', $inviteId, SOCIAL_SESSION_NAMESPACE);
		}

		// Retrieve the user's session.
		$registration = FD::table('Registration');
		$registration->load($session->getId());

		// If there's no registration info stored, the user must be a lost user.
		if (is_null($registration->step)) {
			$info->set(false, JText::_('COM_EASYSOCIAL_REGISTRATION_UNABLE_TO_DETECT_ACTIVE_SESSION'), SOCIAL_MSG_ERROR);
			return $this->redirect(FRoute::registration(array(), false));
		}

		// Let's try to load the profile type that the user has already selected.
		$profile		= FD::table('Profile');
		$profile->load($registration->profile_id);

		// Try to retrieve any available errors from the current registration object.
		$errors = $registration->getErrors();

		// Try to remember the state of the user data that they have entered.
		$data = $registration->getValues();

		// Get the current step index
		$stepIndex = JRequest::getInt('step', 1);

		// Determine the sequence from the step
		$sequence = $profile->getSequenceFromIndex($stepIndex, SOCIAL_PROFILES_VIEW_REGISTRATION);

		// If no sequence found, means don't have any page to show
		if (empty($sequence)) {
			$info->set(false, JText::_('COM_EASYSOCIAL_REGISTRATION_NO_VALID_REGISTRATION_STEPS'), SOCIAL_MSG_ERROR);
			return $this->redirect(FRoute::registration(array(), false));
		}

		// Users should not be allowed to proceed to a future step if they didn't traverse their sibling steps.
		if (empty($registration->session_id) || ($stepIndex != 1 && !$registration->hasStepAccess($stepIndex))) {
			$info->set(false, JText::sprintf('COM_EASYSOCIAL_ERROR_REGISTRATION_COMPLETE_PREVIOUS_STEP_FIRST', $stepIndex), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::registration(array('layout' => 'steps', 'step' => $registration->step), false));
		}

		// Check if this is a valid step in the profile
		if (!$profile->isValidStep($sequence, SOCIAL_PROFILES_VIEW_REGISTRATION)) {
			$info->set(false, JText::sprintf('COM_EASYSOCIAL_ERROR_REGISTRATION_NO_ACCESS_TO_STEP', $sequence), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::registration(array('layout' => 'steps', 'step' => 1), false));
		}

		// Remember current state of registration step
		$registration->set('step', $stepIndex);
		$registration->store();

		// Load the current workflow / step.
		$step 		= FD::table('FieldStep');
		$step->loadBySequence($profile->id, SOCIAL_TYPE_PROFILES, $sequence);

		// Determine the total steps for this profile.
		$totalSteps	= $profile->getTotalSteps();

		// Retrieve registration model.
		$registrationModel = FD::model('Registration');

		// Since they are bound to the respective groups, assign the fields into the appropriate groups.
		$args = array(&$data, &$registration);

		// Get fields library as we need to format them.
		$fields = FD::getInstance('Fields');

		// Retrieve custom fields for the current step
		$fieldsModel = FD::model('Fields');
		$customFields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'visible' => SOCIAL_PROFILES_VIEW_REGISTRATION));

		// Set the breadcrumb
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_REGISTRATION_SELECT_PROFILE'), FRoute::registration());
		FD::page()->breadcrumb($step->get('title'));

		// Set the page title
		FD::page()->title($step->get('title'));

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		// Trigger onRegister for custom fields.
		if (!empty($customFields)) {
			$fields->trigger('onRegister', SOCIAL_FIELDS_GROUP_USER, $customFields, $args, $callback);
		}

		// We don't want to show the profile types if there's only 1 profile in the system.
		$profilesModel = FD::model('Profiles');
		$totalProfiles = $profilesModel->getTotalProfiles(array('registration' => true));

		// Pass in the steps for this profile type.
		$steps = $profile->getSteps(SOCIAL_PROFILES_VIEW_REGISTRATION);

		// Get the total steps
		$totalSteps = $profile->getTotalSteps(SOCIAL_PROFILES_VIEW_REGISTRATION);

		$this->set('registration' 	, $registration);
		$this->set('steps'			, $steps);
		$this->set('totalProfiles'	, $totalProfiles);
		$this->set('currentStep'	, $sequence);
		$this->set('currentIndex'	, $stepIndex);
		$this->set('totalSteps'	, $totalSteps);
		$this->set('step'			, $step);
		$this->set('fields' 		, $customFields);
		$this->set('errors' 		, $errors);
		$this->set('profile' 		, $profile);

		parent::display('site/registration/default.steps');
	}

	/**
	 * Method is invoked each time a step is saved. Responsible to redirect or show necessary info about the current step.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableRegistration
	 * @param	int
	 * @param	bool
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function saveStep($registration, $currentIndex, $completed = false)
	{
		$info 		= FD::info();
		$config 	= FD::config();

		// Registrations must be enabled.
		if (!$config->get('registrations.enabled')) {
			$info->setMessage(false, JText::_('COM_EASYSOCIAL_REGISTRATIONS_DISABLED'), SOCIAL_MSG_ERROR);
			return $this->redirect(FRoute::login(array(), false));
		}

		// Set any message that was passed from the controller.
		$info->set($this->getMessage());

		// If there's an error, redirect back user to the correct step and show the error.
		if ($this->hasErrors()) {
			return $this->redirect(FRoute::registration(array('layout' => 'steps', 'step' => $currentIndex), false));
		}

		// Registration is completed. Redirect user to the complete page.
		if ($completed) {
			return $this->redirect(FRoute::registration(array('layout' => 'completed'), false));
		}

		// Registration is not completed yet, redirect user to the appropriate step.
		return $this->redirect(FRoute::registration(array('layout' => 'steps', 'step' => $currentIndex + 1), false));
	}

	/**
	 * Post process after the user selects the type.
	 *
	 * @access	public
	 * @param	null
	 */
	public function selectType()
	{
		// Set message data.
		FD::info()->set($this->getMessage());

		// @task: Check for errors.
		if ($this->hasErrors()) {
			return $this->redirect(FRoute::registration(array(), false));
		}

		// @task: We always know that after selecting the profile type, the next step would always be the first step.
		$url 	= FRoute::registration(array('layout' => 'steps', 'step' => 1), false);

		return $this->redirect(FRoute::registration(array('layout' => 'steps', 'step' => 1), false));
	}

	/**
	 * Displays some information once the user registration is completed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function complete(&$user, &$profile)
	{
		// Check if there is a menu assigned for completed registration.
		$params = $profile->getParams();

		// If the profile is set to verify, we need to redirect users to the activation form instead of respecting the registration redirect.
		if ($profile->getRegistrationType() == 'verify') {

			$url = FRoute::registration(array('layout' => 'completed', 'id' => $profile->id, 'userid' => $user->id), false);

			return $this->redirect($url);
		}

		// If the profile is set to require admin approval, we need to redirect users to the wait for admin approval page instead of respecting the registration redirect.
		if ($profile->getRegistrationType() == 'approvals') {

			$url = FRoute::registration(array('layout' => 'completed', 'id' => $profile->id, 'userid' => $user->id), false);

			return $this->redirect($url);
		}

		// If the profile is set to manually login, we need to redirect users to the login page instead of respecting the registration redirect.
		if ($profile->getRegistrationType() == 'login') {

			$url = FRoute::registration(array('layout' => 'completed', 'id' => $profile->id, 'userid' => $user->id), false);

			return $this->redirect($url);
		}

		// Here we respect the settings that is configured for the registration success settings.
		$link = FRoute::getMenuLink($params->get('registration_success'));

		if ($link) {
			return $this->redirect($link);
		}

		// If profile is configured to be automatically logged in, redirect them to the dashboard page.
		if ($profile->getRegistrationType() == 'auto') {

			// Check if session has a return value or not
			$session = JFactory::getSession();
			$registration = FD::table('Registration');
			$registration->load($session->getId());
			$registry = FD::registry($registration->values);
			$return = $registry->get('return');

			if (!empty($return)) {
				return $this->redirect(base64_decode($return));
			}

			$config = FD::config();
			$loginMenu = $config->get('general.site.login');

			if ($loginMenu == 'null') {
				$url = FRoute::dashboard(array(), false);
			} else {
				$url = FRoute::getMenuLink($loginMenu);
			}

			return $this->redirect($url);
		}

		$url = FRoute::registration(array('layout' => 'completed', 'id' => $profile->id, 'userid' => $user->id), false);


		return $this->redirect($url);
	}

	/**
	 * Displays some information once the user registration is completed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function completed()
	{
		// If user is already logged in, redirect them to their dashboard automatically.
		$user = FD::user();

		if ($user->id) {
			$config = FD::config();
			$loginMenu = $config->get('general.site.login');

			if ($loginMenu == 'null') {
				$url = FRoute::dashboard(array(), false);
			} else {
				$url = FRoute::getMenuLink($loginMenu);
			}

			return $this->redirect($url);
		}

		$userId = $this->input->get('userid', 0, 'int');
		$user = FD::user($userId);

		// Get the profile type.
		$id = $this->input->get('id', 0, 'int');
		$profile = FD::table('Profile');
		$profile->load($id);

		$oauth = $this->input->get('oauth', 0, 'int');

		// Get the registration type
		$type = $profile->getRegistrationType(false, $oauth);

		$this->set('user', $user);
		$this->set('type', $type);
		$this->set('profile', $profile);

		echo parent::display('site/registration/default.complete');
	}

	/**
	 * Responsible to display the activation form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function activation()
	{
		$id 	= JRequest::getInt('userid');

		$user 	= FD::user($id);

		$this->set('user'	, $user);

		echo parent::display('site/registration/default.complete.verify');
	}

	/**
	 * Responsible for displaying the username / password form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function oauthCreateAccountForm($username = '', $email = '', $clientType = '')
	{
		$config 	= FD::config();

		// Get allowed clients
		$allowedClients	= array_keys((array) $config->get('oauth'));

		// Get the current client.
		$oauthClient 	= JRequest::getWord('client', $clientType);

		if (!in_array($oauthClient, $allowedClients)) {
			FD::info()->set(false, JText::sprintf('COM_EASYSOCIAL_OAUTH_INVALID_OAUTH_CLIENT_PROVIDED', $oauthClient), SOCIAL_MSG_ERROR);
			return $this->redirect(FRoute::login(array(), false));
		}

		$this->set('username'	, $username);
		$this->set('email'		, $email);
		$this->set('clientType', $oauthClient);

		echo parent::display('site/registration/oauth.create.account');
	}

	/**
	 * Responsible for post processing after a user signs up with their oauth account
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function oauthCreateAccount($user = null)
	{
		if ($this->hasErrors()) {
			// Throw some errors here.
			FD::info()->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		return $this->redirect(FRoute::registration(array('layout' => 'completed', 'userid' => $user->id, 'id' => $user->profile_id, 'oauth' => 1), false));
	}

	/**
	 * Shows the user that their registration is completed
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function completeOauth()
	{
		// If user is already logged in, redirect them to their dashboard automatically.
		$userId 	= JRequest::getInt('userId');
		$user 		= FD::user($userId);

		// Get the profile type.
		$id 		= $user->profile_id;
		$profile	= FD::table('Profile');
		$profile->load($id);

		$type 		= $profile->getRegistrationType(false, true);

		if ($type == 'verify') {
			return $this->redirect(FRoute::registration(array('layout' => 'activation', 'id' => $user->id), false));
		}

		$this->set('user'		, $user);
		$this->set('type'		, $type);
		$this->set('profile'	, $profile);

		echo parent::display('site/registration/default.complete.oauth');
	}

	/**
	 * Responsible for post-processing of activation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function activate($user = null)
	{
		FD::info()->set($this->getMessage());

		if ($this->hasErrors()) {
			$url	= FRoute::registration(array('layout' => 'activation', 'userid' => $user->id), false);

			return $this->redirect($url);
		}

		return $this->redirect(FRoute::login(array(), false));
	}

	/**
	 * Post process after a user has been approved
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function approveUser()
	{
		FD::info()->set($this->getMessage());

		echo parent::display('site/registration/moderation.approved');
	}


	/**
	 * Post process after a user has been rejected
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function rejectUser()
	{
		FD::info()->set($this->getMessage());

		echo parent::display('site/registration/moderation.rejected');
	}

	/**
	 * Post processing after trying to create user
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function quickRegister()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::registration());
	}

	/**
	 * Proxy function to route the mini registration process to full registration page
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function miniRegister()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::registration());
	}

	/**
	 * Proxy function to route the mini registration process to full registration page
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function fullRegister($profileId = 0)
	{
		FD::info()->set($this->getMessage());

		// If no profile id, then we route user to select a profile
		if (!empty($profileId)) {
			$this->redirect(FRoute::registration(array('layout' => 'steps', 'step' => 1)));
		}

		$this->redirect(FRoute::registration());
	}

	/**
	 * Shorthand for view to pass in quick as parameter to the registration page
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function selectProfile()
	{
		$this->redirect(FRoute::registration(array('quick' => true)));
	}
}
