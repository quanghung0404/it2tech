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

// Include dependencies
ES::import('site:/views/views');

class EasySocialViewProfile extends EasySocialSiteView
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
		return true;
	}

	/**
	 * Displays a user profile to a 3rd person perspective.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 **/
	public function display($tpl = null)
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();
		
		// Get the user's id.
		$id = $this->input->get('id', 0, 'int');

		// Check if there is any stream filtering or not.
		$filter	= $this->input->get('type', '', 'word');

		// The current logged in user might be viewing their own profile.
		if ($id == 0) {
			$id = ES::user()->id;
		}

		// When the user tries to view his own profile but if he isn't logged in, throw a login page.
		if ($id == 0) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the user's object.
		$user = ES::user($id);

		// If the user doesn't exist throw an error
		if (!$user->id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// If the user is blocked or the user doesn't have community access
		if (($this->my->id != $user->id && $this->my->isBlockedBy($user->id)) || !$user->hasCommunityAccess()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// If the user is blocked, they should not be accessible
		if ($user->isBlock() && !$this->my->isSiteAdmin()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// Set the page properties
		$this->page->title($this->string->escape($user->getName()));
		$this->page->breadcrumb($this->string->escape($user->getName()));

		// Get the current user's privacy object
		$privacy = $this->my->getPrivacy();

		// Let's test if the current viewer is allowed to view this profile.
		if ($this->my->id != $user->id && !$privacy->validate('profiles.view', $user->id, SOCIAL_TYPE_USER)) {

			$facebook = ES::oauth('facebook');
			$return = base64_encode($user->getPermalink());

			$this->set('return', $return);
			$this->set('facebook', $facebook);
			$this->set('user', $user);

			return parent::display('site/profile/restricted');
		}

		// Apply opengraph tags.
		$this->opengraph->addProfile($user);

		// Do not assign badge if i view myself.
		if ($user->id != $this->my->id && $this->my->id) {
			// @badge: profile.view
			$badge = ES::badges();
			$badge->log('com_easysocial', 'profile.view', $this->my->id, JText::_('COM_EASYSOCIAL_PROFILE_VIEWED_A_PROFILE'));
		}

		// Get the limit start
		$startLimit = $this->input->get('limitstart', 0, 'int');

		// Determine if the current request is to load an app
		$appId = $this->input->get('appId', 0, 'int');

		// Get the apps library.
		$appsLib = ES::apps();

		// Default contents
		$contents = '';

		// Load the app when necessary
		if ($appId) {

			$app = ES::table('App');
			$app->load($appId);

			// Check if the user has access to this app
			if (!$app->accessible($user->id)) {
				FD::info()->set(false, JText::_('COM_EASYSOCIAL_PROFILE_APP_IS_NOT_INSTALLED_BY_USER'), SOCIAL_MSG_ERROR);

				$redirect = FRoute::profile(array('id' => $user->getAlias()), false);

				return $this->redirect($redirect);
			}

			// Set the page title
			$this->page->title(ES::string()->escape($user->getName()) . ' - ' . $app->get('title'));

			// Render the app contents
			$contents = $appsLib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'profile', $app, array('userId' => $user->id));
		}

		// Get the layout
		$layout = $this->input->get('layout', '', 'cmd');


		// @since 1.3.7
		// If layout is empty, means we want to get the default view
		// Previously timeline is always the default
		if (empty($appId) && empty($layout) && $filter != 'appFilter') {
			$defaultDisplay = $this->config->get('users.profile.display', 'timeline');

			$layout = $defaultDisplay;
		}

		// Default variables
		$timeline = null;
		$newCover = false;

		// Viewing info of a user.
		if ($layout === 'about') {

			$showTimeline = false;
			$usersModel = ES::model('Users');

			// Get the active step
			$activeStep = $this->input->get('step', 0, 'int');

			// Get the list of available steps on the user's profile
			$steps = $usersModel->getAbout($user, $activeStep);

			// We should generate a canonical link if user is viewing the about section and the default page is about
			if ($this->config->get('users.profile.display') == 'about') {
				$this->page->canonical($user->getPermalink(false, true));
			}

			if ($steps) {
				foreach ($steps as $step) {
					if ($step->active) {
						$theme = ES::themes();
						$theme->set('fields', $step->fields);

						$contents = $theme->output('site/profile/default.about.fields');
					}
				}
			}

			$this->set('infoSteps', $steps);
		}

		// Should we filter stream items by specific app types
		$appType = $this->input->get('filterid', '', 'string');

		// If contents is still empty at this point, then we just get the stream items as the content
		if (empty($contents) || $filter == 'appFilter') {

			// Should the timeline be active
			$timeline = true;

			// Retrieve user's stream
			$theme = ES::themes();

			// Get story
			$story = ES::story(SOCIAL_TYPE_USER);
			$story->target = $user->id;

			// Get the stream
			$stream = ES::stream();

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('userId' => $user->id, 'limit' => 0));

			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$streamOptions = array('userId' => $user->id, 'nosticky' => true, 'startlimit' => $startLimit);

			if ($filter == 'appFilter') {
				$timeline = false;

				$streamOptions['actorId'] = $user->id;
			}

			if ($appType) {
				$streamOptions['context'] = $appType;

				// Should this be set now or later
				$stream->filter = 'custom';
			}

			$stream->get($streamOptions);

			// Only registered users can access the story form
			if (!$this->my->guest) {
				$stream->story = $story;
			}

			// Set stream to theme
			$theme->set('stream', $stream);

			$contents = $theme->output('site/profile/default.stream');
		}

		// Add canonical tag for user's profile page
		if (!$filter) {
			$this->page->canonical($user->getPermalink(false, true));
		}

		// Get user's cover object
		$cover = $user->getCoverData();

		// If we're setting a cover
		$coverId = $this->input->get('cover_id', 0, 'int');

		// Load cover photo
		if ($coverId) {
			$coverTable = ES::table('Photo');
			$coverTable->load($coverId);

			// If the cover photo belongs to the user
			if ($coverTable->isMine()) {
				$newCover = $coverTable;
			}
		}

		$streamModel = FD::model('Stream');

		// Get a list of application filters
		$appFilters = $streamModel->getAppFilters(SOCIAL_TYPE_USER);

		// Retrieve list of apps for this user
		$profile = $user->getProfile();
		$appsModel = ES::model('Apps');
		$options = array('view' => 'profile', 'uid' => $user->id, 'key' => SOCIAL_TYPE_USER, 'inclusion' => $profile->getDefaultApps());
		$apps = $appsModel->getApps($options);

		$this->set('appFilters', $appFilters);
		$this->set('filterId', $appType);
		$this->set('timeline', $timeline);
		$this->set('newCover', $newCover);
		$this->set('cover', $cover);
		$this->set('contents' , $contents);
		$this->set('appsLib', $appsLib);
		$this->set('apps', $apps);
		$this->set('activeApp', $appId );
		$this->set('privacy', $privacy );
		$this->set('user', $user);

		// Load the output of the profile.
		return parent::display('site/profile/default');
	}

	/**
	 * Responsible to output the edit profile layout
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The name of the template file to parse; automatically searches through the template paths.
	 * @return	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function edit($errors = null)
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();

		// Set any messages here.
		$this->info->set($this->getMessage());

		// Load the language file from the back end.
		ES::language()->loadAdmin();

		// Get list of steps for this user's profile type.
		$profile = $this->my->getProfile();

		// Get user's installed apps
		$appsModel = ES::model('Apps');
		$userApps = $appsModel->getUserApps($this->my->id);

		// Get the steps model
		$stepsModel = ES::model('Steps');
		$steps = $stepsModel->getSteps($profile->id, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_EDIT);

		// Get custom fields model.
		$fieldsModel = ES::model('Fields');

		// Get custom fields library.
		$fields = ES::fields();

		// Set page title
		ES::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS'));

		// Set the page breadcrumb
		ES::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PROFILE'), FRoute::profile());
		ES::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS'));

		// Check if there are any errors in the session
		// If session contains error, means that this is from the ES::fields()->checkCompleteProfile();
		if (empty($errors)) {
			$session = JFactory::getSession();
			$errors = $session->get('easysocial.profile.errors', '', SOCIAL_SESSION_NAMESPACE);

			if (!empty($errors)) {
				ES::info()->set(false, JText::_('COM_EASYSOCIAL_PROFILE_PLEASE_COMPLETE_YOUR_PROFILE'), SOCIAL_MSG_ERROR);
			}
		}

		// Set the callback for the triggered custom fields
		$callback = array( $fields->getHandler(), 'getOutput' );

		// Get the custom fields for each of the steps.
		foreach ($steps as &$step) {

			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $this->my->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => 'edit'));

			// Trigger onEdit for custom fields.
			if (!empty($step->fields)) {

				$post = JRequest::get('post');
				$args 	= array( &$post, &$this->my, $errors );
				$fields->trigger( 'onEdit' , SOCIAL_FIELDS_GROUP_USER , $step->fields , $args, $callback );
			}
		}

		// Determines if we should show the social tabs on the left.
		$showSocialTabs = false;

		// Determines if the user has associated
		$associatedFacebook = $this->my->isAssociated( 'facebook' );
		$facebookClient = false;
		$facebookMeta = array();
		$fbOAuth = false;
		$fbUserMeta = array();

		if ($associatedFacebook) {
			// We want to show the tabs
			$showSocialTabs = true;

			$facebookToken	= $this->my->getOAuthToken('facebook');
			$facebookClient = ES::oauth('facebook');

			// Set the access for the client.
			$facebookClient->setAccess($facebookToken);

			try {
				$fbUserMeta = $facebookClient->getUserMeta();
			} catch (Exception $e) {
				$message = (object) array(
					'message' => JText::sprintf('COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage()),
					'type' => SOCIAL_MSG_ERROR
				);

				ES::info()->set($message);
			}

			$fbUserMeta = false;

			$fbOAuth = $this->my->getOAuth(SOCIAL_TYPE_FACEBOOK);

			$facebookMeta = ES::registry( $fbOAuth->params );
			$facebookPermissions = ES::makeArray( $fbOAuth->permissions );
		}

		$this->set('fbUserMeta', $fbUserMeta);
		$this->set('fbOAuth', $fbOAuth);
		$this->set('showSocialTabs', $showSocialTabs);
		$this->set('facebookMeta', $facebookMeta);
		$this->set('facebookClient', $facebookClient);
		$this->set('associatedFacebook', $associatedFacebook);
		$this->set('profile', $profile);
		$this->set('steps', $steps);
		$this->set('apps', $userApps);

		return parent::display('site/profile/default.edit.profile');
	}

	/**
	 * Edit privacy form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editNotifications()
	{
		// User needs to be logged in
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the user notification settings
		$lib = ES::alert();
		$alerts = $lib->getUserSettings($this->my->id);

		// Set page title
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_NOTIFICATION_SETTINGS');

		// Set the page breadcrumb
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', FRoute::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_NOTIFICATION_SETTINGS');

		$this->set('alerts', $alerts);

		parent::display('site/profile/default.edit.notifications');
	}

	/**
	 * Edit privacy form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editPrivacy()
	{
		// User needs to be logged in
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get user's privacy
		$privacyLib = ES::privacy($this->my->id);
		$result = $privacyLib->getData();

		// Set page title
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_PRIVACY_SETTINGS');

		// Set the page breadcrumb
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', FRoute::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PRIVACY_SETTINGS');

		$privacy = array();

		// Update the privacy data with proper properties.
		foreach ($result as $group => $items) {

			// We do not want to show field privacy rules here because it does not make sense for user to set a default value
			// Most of the fields only have 1 and it is set in Edit Profile page
			if ($group === 'field') {
				continue;
			}

			// Only display such privacy rules if photos is enabled
			if (($group == 'albums' || $group == 'photos') && !$this->config->get('photos.enabled')) {
				continue;
			}

			// Only display videos privacy if videos is enabled.
			if ($group == 'videos' && !$this->config->get('video.enabled')) {
				continue;
			}

			// Do not display badges / achievements in privacy if badges is disabled
			if ($group == 'achievements' && !$this->config->get('badges.enabled')) {
				continue;
			}

			// Do not display followers privacy item
			if ($group == 'followers' && !$this->config->get('followers.enabled')) {
				continue;
			}

			foreach ($items as &$item) {

				// Conversations rule should only appear if it is enabled.
				if (($group == 'profiles' && $item->rule == 'post.message') && !$this->config->get('conversations.enabled')) {
					unset($item);
				}

				$rule = JString::str_ireplace('.', '_', $item->rule);
				$rule = strtoupper($rule);

				$groupKey = strtoupper($group);

				// Determines if this has custom
				$item->hasCustom = $item->custom ? true : false;

				// If the rule is a custom rule, we need to set the ids
				$item->customIds = array();
				$item->customUsers = array();

				if ($item->hasCustom) {
					foreach ($item->custom as $friend) {
						$item->customIds[] = $friend->user_id;

						$user = ES::user($friend->user_id);
						$item->customUsers[] = $user;
					}
				}

				// Try to find an app element that is related to the privacy type
				$app = ES::table('App');
				$appExists = $app->load(array('element' => $item->type));

				if ($appExists) {
					$app->loadLanguage();
				}

				// Go through each options to get the selected item
				$item->selected = '';

				foreach ($item->options as $option => $value) {
					if ($value) {
						$item->selected = $option;
					}

					// We need to remove "Everyone" if the site lockdown is enabled
					if ($this->config->get('general.site.lockdown.enabled') && $option == SOCIAL_PRIVACY_0) {
						unset($item->options[$option]);
					}
				}

				$item->groupKey = $groupKey;
				$item->label = JText::_('COM_EASYSOCIAL_PRIVACY_LABEL_' . $groupKey . '_' . $rule );
				$item->tips = JText::_('COM_EASYSOCIAL_PRIVACY_TIPS_' . $groupKey . '_' . $rule );
			}

			$privacy[$group] = $items;
		}

		// Get a list of blocked users for this user
		$blockModel = ES::model('Blocks');
		$blocked = $blockModel->getBlockedUsers($this->my->id);

		$this->set('blocked', $blocked);
		$this->set('privacy', $privacy);

		parent::display('site/profile/default.edit.privacy');
	}

	/**
	 * Handle save profiles.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function save()
	{
		ES::info()->set($this->getMessage());

		$task = $this->input->getString('task');

		$options = array();

		if ($task == 'save') {
			$options['layout'] = 'edit';
		}

		$this->redirect(FRoute::profile($options, false));
	}

	/**
	 * Handle save notification.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function saveNotification()
	{
		$info 	= ES::info();
		$info->set( $this->getMessage() );

		$this->redirect( FRoute::profile( array( 'layout' => 'editNotifications' ) , false ) );
	}


	/**
	 * Handle save privacy.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function savePrivacy()
	{
		ES::info()->set( $this->getMessage() );

		$this->redirect( FRoute::profile( array( 'layout' => 'editPrivacy' ) , false ) );
	}


	/**
	 * Allows viewer to download a file from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function downloadFile()
	{
		// Currently only registered users are allowed to view a file.
		ES::requireLogin();

		// Load the file object
		$id = $this->input->get('fileid', 0, 'int');
		$file = ES::table('File');
		$file->load($id);

		if (!$file->id || !$id) {
			// Throw error message here.
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		// Add points for the user when they upload a file.
		ES::points()->assign('files.download', 'com_easysocial', $this->my->id);

		// @TODO: Check for the privacy.

		$file->download();
		exit;
	}

	/**
	 * Post process after removing an avatar
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeAvatar()
	{
		ES::info()->set( $this->getMessage() );

		$my 	= ES::user();

		$this->redirect( FRoute::profile( array( 'id' => $my->getAlias() ) , false ) );
	}


	/**
	 * Post processing after the user wants to delete their account
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		ES::info()->set( $this->getMessage() );


		$this->redirect( FRoute::dashboard( array() , false ) );
	}

}
