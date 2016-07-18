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

// Include main views file.
FD::import( 'admin:/views/views' );

class EasySocialViewProfiles extends EasySocialAdminView
{
	/**
	 * Displays a list of profiles in the back end.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display($tpl = null)
	{
		// Add Joomla buttons here.
		$this->addButtons(__FUNCTION__);

		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_PROFILE_TYPES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_PROFILES');

		// Gets a list of profiles from the system.
		$model = FD::model( 'Profiles' , array( 'initState' => true ));

		// perform some maintenance actions here
		$model->deleteOrphanItems();

		// Get the search query from post
		$search = $this->input->get('search', $model->getState('search'), 'default');

		// Get the current ordering.
		$ordering 	= JRequest::getWord( 'ordering' , $model->getState( 'ordering' ) );
		$direction 	= JRequest::getWord( 'direction' , $model->getState( 'direction' ) );
		$state	 	= JRequest::getVar( 'state', $model->getState( 'state' ) );
		$limit 		= $model->getState( 'limit' );

		// Prepare options
		$profiles	= $model->getItems();
		$pagination	= $model->getPagination();

		$callback 	= JRequest::getVar( 'callback' , '' );

		$orphanCount = $model->getOrphanMembersCount( false );

		// Set properties for the template.
		$this->set( 'limit'		, $limit );
		$this->set( 'state'		, $state );
		$this->set( 'ordering'		, $ordering );
		$this->set( 'direction'		, $direction );
		$this->set( 'callback'		, $callback );
		$this->set( 'pagination'	, $pagination );
		$this->set( 'profiles'		, $profiles );
		$this->set( 'search'		, $search );
		$this->set( 'orphanCount'	, $orphanCount );

		echo parent::display( 'admin/profiles/default' );
	}

	/**
	 * Displays a the profile form when someone creates a new profile type or edits an existing profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableProfile	The profile object (Optional)
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function form($profile = '')
	{
		// Get the profile id from the request.
		$id = $this->input->get('id', 0, 'int');

		// Add Joomla buttons here.
		$this->addButtons(__FUNCTION__);

		// Test if id is provided by the query string
		if (!$profile) {
			$profile = FD::table('Profile');

			if ($id) {
				$state = $profile->load($id);

				if (!$state) {
					$this->info->set($this->getMessage());

					return $this->redirect('index.php?option=com_easysocial&view=profiles');
				}
			}
		}

		// Set the structure heading here.
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_NEW_PROFILE_TYPE');

		// If this is an edited profile, display the profile title.
		if (!empty($id)) {
			$this->setHeading($profile->get('title'));
		}

		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_PROFILES_FORM');

		// Default Values
		$defaultAvatars = array();

		// load frontend language so that the custom fields languages display properly.
		FD::language()->loadSite();

		// Only process the rest of the blocks of this is not a new item.
		if ($id) {

			// Get the default avatars for this profile
			$avatarsModel = ES::model('Avatars');
			$defaultAvatars = $avatarsModel->getDefaultAvatars($profile->id);

			// Get a list of available field apps
			$appsModel = ES::model('Apps');
			$defaultFields = $appsModel->getApps(array('type' => SOCIAL_APPS_TYPE_FIELDS, 'group' => SOCIAL_FIELDS_GROUP_USER, 'state' => SOCIAL_STATE_PUBLISHED));

			// Get a list of workflows for this profile type.
			$stepsModel = ES::model('Steps');
			$steps = $stepsModel->getSteps($profile->id, SOCIAL_TYPE_PROFILES);

			// Get a list of fields for this profile
			$fieldsModel = ES::model('Fields');
			$fields = $fieldsModel->getCustomFields(array('profile_id' => $profile->id, 'state' => 'all'));

			// @field.triggers: onSample
			$data = array();
			$lib = FD::fields();
			$lib->trigger('onSample', SOCIAL_FIELDS_GROUP_USER, $fields, $data, array($lib->getHandler(), 'getOutput'));

			// Create a temporary storage
			$tmpFields = array();

			// Group the fields to each workflow properly
			if ($steps) {
				foreach ($steps as $step) {

					$step->fields = array();

					if (!empty($fields)) {

						foreach ($fields as $field) {

							if ($field->step_id == $step->id) {
								$step->fields[] = $field;
							}

							$tmpFields[$field->app_id] = $field;
						}
					}
				}
			}

			// We need to know the amount of core apps and used core apps
			// 1.3 Update, we split out unique apps as well
			$coreAppsCount = 0;
			$usedCoreAppsCount = 0;
			$uniqueAppsCount = 0;
			$usedUniqueAppsCount = 0;

			// hide the apps if it is a core app and it is used in the field
			if ($defaultFields) {
				foreach ($defaultFields as $app) {
					$app->hidden = false;

					// If app is core, increase the coreAppsCount counter
					if ($app->core) {
						$coreAppsCount++;
					}

					// If app is NOT core and unique, increase the coreAppsCount counter
					// This is because core apps are definitely unique, so we do not want to include core apps here
					if (!$app->core && $app->unique) {
						$uniqueAppsCount++;
					}

					// Test if this app has already been assigned to the $tmpFields
					if (isset($tmpFields[$app->id]) && $app->core) {
						$usedCoreAppsCount++;

						$app->hidden = true;
					}

					// Test if this app is NOT core and unique and has already been assigned
	                // This is because core apps are definitely unique, so we do not want to include core apps here
	                if (isset($tmpFields[$app->id]) && !$app->core && $app->unique) {
	                    $usedUniqueAppsCount++;

	                    $app->hidden = true;
	                }
				}
			}

			unset($tmpFields);

			// We need to know if there are any core apps remain
			$coreAppsRemain = $usedCoreAppsCount < $coreAppsCount;

			// We need to know if there are any unique apps remain
			$uniqueAppsRemain = $usedUniqueAppsCount < $uniqueAppsCount;

			// Render the access form.
			$accessModel = FD::model('Access');
			$accessForm = $accessModel->getForm($id, SOCIAL_TYPE_PROFILES, 'access', '', false);

			// Get the total number of members in this profile type
			$membersCount = $profile->getMembersCount();

			// Get a list of user apps installed on the site
			$apps = $appsModel->getApps(array('type' => SOCIAL_APPS_TYPE_APPS, 'group' => SOCIAL_FIELDS_GROUP_USER, 'state' => SOCIAL_STATE_PUBLISHED));

			$this->set('selectedApps', $profile->getDefaultApps());
			$this->set('apps', $apps);
			$this->set('accessForm', $accessForm);
			$this->set('coreAppsRemain', $coreAppsRemain);
			$this->set('uniqueAppsRemain', $uniqueAppsRemain);
			$this->set('defaultFields', $defaultFields);
			$this->set('steps', $steps);
			$this->set('fields', $fields);
			$this->set('fieldGroup', SOCIAL_FIELDS_GROUP_USER);
			$this->set('membersCount', $membersCount);
		}

		// Get a list of themes.
		$themesModel = FD::model('Themes');
		$themes = $themesModel->getThemes();

		// Get profile parameters
		$params = $profile->getParams();

		// Get default privacy
		$privacy = FD::privacy($profile->id, SOCIAL_PRIVACY_TYPE_PROFILES);

		// We need to hide the guest user group that is defined in com_users options.
		// Public group should also be hidden.
		$userOptions = JComponentHelper::getComponent('com_users')->params;
		$defaultRegistrationGroup = $userOptions->get('new_usertype');
		$guestGroup = array(1, $userOptions->get('guest_usergroup'));

		// Set the default registration group for new items
		if (!$id) {
			$profile->gid = $defaultRegistrationGroup;
		}

		// Get the active tab
		$activeTab = $this->input->get('activeTab', 'settings', 'word');

		// Get a list of default groups
		$defaultGroups = $profile->getDefaultGroups();

		// Exclude groups from being suggested
		$excludeGroups = array();

		if ($defaultGroups) {
			foreach ($defaultGroups as $group) {
				$excludeGroups[] = (int) $group->id;
			}
		}


		$this->set('excludeGroups', $excludeGroups);
		$this->set('defaultGroups', $defaultGroups);
		$this->set('activeTab', $activeTab);
		$this->set('defaultAvatars', $defaultAvatars);
		$this->set('guestGroup', $guestGroup);
		$this->set('id', $id);
		$this->set('themes', $themes);
		$this->set('param', $params);
		$this->set('profile', $profile);
		$this->set('privacy', $privacy);

		echo parent::display('admin/profiles/default.form');
	}

	private function groupFields(&$steps)
	{

	}

	/**
	 * This is to return a list of users in an iframe / html.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getUsers()
	{
		$id = $this->input->get('profile_id', 0, 'int');
		$search = $this->input->get('search', '', 'default');

		// @task: Get the profiles model.
		$model = FD::model('Profiles');
		$exclusion	= $model->getMembers($id);

		// @task: Now, we need to get the final result of users.
		$userModel = FD::model('User');
		$users = $userModel->getItems( array( 'exclusion' => array( 'a.id' => $exclusion ) ) );
		$pagination	= $userModel->getPagination();

		// Initialize the user objects.
		$users = FD::user($users);

		$this->set( 'search'	, $search );
		$this->set( 'pagination', $pagination );
		$this->set( 'users'		, $users );

		parent::display( 'admin.profiles.users' );
	}

	/**
	 * Post processing for updating ordering.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function saveorder()
	{
		// Get info object.
		$info 	= FD::info();

		// Set message
		$info->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=profiles' );
	}


	/**
	 * Post processing for storing. What the view should do after a storing is executed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function store( $profile = '' )
	{
		// Get info object.
		$info 	= FD::info();

		// Set message
		$info->set( $this->getMessage() );

		// If there's an error on the storing, we don't need to perform any redirection.
		if( $this->hasErrors() )
		{
			// Load the form for the user.
			return $this->form( $profile );
		}

		$activeTab 	= JRequest::getWord( 'activeTab' , 'settings' );

		switch( $this->task )
		{
			case 'apply':
				$this->redirect( 'index.php?option=com_easysocial&view=profiles&id=' . $profile->id . '&layout=form&activeTab=' . $activeTab );
			break;

			case 'savenew':
				$this->redirect( 'index.php?option=com_easysocial&view=profiles&layout=form' );
			break;

			case 'save':
			default:
				$this->redirect( 'index.php?option=com_easysocial&view=profiles' );
			break;

		}
	}

	/**
	 * Stores the profile and redirect back to the same edit page.
	 */
	public function apply( $profile = '' )
	{
		$this->processMessages();

		return $this->form( $profile );
	}

	/**
	 * Post processing for delete. What the view should do after a delete is executed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function delete()
	{
		FD::info()->set( $this->getMessage() );

		$this->app->redirect( 'index.php?option=com_easysocial&view=profiles' );
	}

	/**
	 * Post processing after items have been reordered
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateOrdering()
	{
		$this->app->redirect( 'index.php?option=com_easysocial&view=profiles' );
	}

	/**
	 * Post processing after an item have been moved up
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function move()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=profiles' );
	}

	/**
	 * Post processing for publish / unpublish. What the view should do after publishing is executed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function togglePublish()
	{
		$info 	= FD::info();

		// Set the message that is passed from the controller.
		$info->set( $this->getMessage() );

		return $this->redirect( 'index.php?option=com_easysocial&view=profiles' );
	}

	/**
	 * Post processing for setting a profile type as default.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function toggleDefault()
	{
		$info 	= FD::info();

		$info->set( $this->getMessage() );

		return $this->redirect( 'index.php?option=com_easysocial&view=profiles' );
	}

	/**
	 * Adds buttons to the page.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	Array	An array of buttons.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function addButtons( $layout )
	{
		$id 		= JRequest::getInt( 'id' );

		switch( $layout )
		{
			case 'form':

				JToolbarHelper::apply( 'apply' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE' ) , false , false );
				JToolbarHelper::save( 'save' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE' ) );

				if (FD::getInstance('Version')->getVersion() >= '1.6') {
					JToolbarHelper::save2new( 'savenew' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_NEW' ) );
				} else {
					JToolbarHelper::save( 'savenew' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_NEW' ) );
				}

				if ($id) {
					JToolbarHelper::save2copy('savecopy', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AS_COPY'));
				}


				JToolbarHelper::divider();
				JToolbarHelper::cancel('cancel', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL'));
			break;

			case 'display':
			default:
				JToolbarHelper::addNew( 'form' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_NEW' ) , false );
				JToolbarHelper::divider();
				JToolbarHelper::publishList( 'publish' );
				JToolbarHelper::unpublishList( 'unpublish' );
				JToolbarHelper::divider();
				JToolbarHelper::deleteList( '' , 'delete' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_DELETE' ) );

			break;
		}
	}

}
