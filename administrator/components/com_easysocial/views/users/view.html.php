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
defined('_JEXEC') or die('Unauthorized Access');

// Include main views file.
FD::import('admin:/views/views');

class EasySocialViewUsers extends EasySocialAdminView
{
	/**
	 * Default user listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_USERS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_USERS');

		// Add Joomla buttons
		JToolbarHelper::addNew();
		JToolbarHelper::divider();
		JToolbarHelper::publishList('publish', JText::_('COM_EASYSOCIAL_UNBLOCK'));
		JToolbarHelper::unpublishList('unpublish', JText::_('COM_EASYSOCIAL_BLOCK'));
		JToolbarHelper::divider();
		JToolbarHelper::custom('assign', 'addusers', '', JText::_('COM_EASYSOCIAL_ASSIGN_GROUP'));
		JToolbarHelper::divider();
		JToolbarHelper::custom('switchProfile', 'switchprofile', '', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SWITCH_PROFILE'));
		JToolbarHelper::divider();
		JToolbarHelper::custom('assignBadge', 'assignbadge', '', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_ASSIGN_BADGE'));
		JToolbarHelper::custom('assignPoints', 'assignpoints', '', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_ASSIGN_POINTS'));
		JToolbarHelper::custom('resetPoints', 'delete', '', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_RESET_POINTS'));
		JToolbarHelper::custom('resendActivate', 'envelope', '', JText::_('COM_EASYSOCIAL_TOOLBAR_BUTTON_RESEND_ACTIVATION'));
		JToolbarHelper::deleteList();

		// Get the model
		$profilesModel = FD::model('Profiles');
		$model = FD::model('Users', array('initState' => true));

		// perform some maintenance actions here
		$profilesModel->deleteOrphanItems();

		// Get filter states.
		$ordering = $this->input->get('ordering', $model->getState('ordering'), 'default');
		$direction = $this->input->get('direction', $model->getState('direction'), 'default');

		$limit = $model->getState('limit');
		$published = $model->getState('published');

		$search = $this->input->get('search', $model->getState('search'), 'default');
		$group = $this->input->get('group', $model->getState('group'), 'int');
		$profile = $this->input->get('profile', $model->getState('profile'), 'int');

		// Checks if user listing that is retrieved requires multiple selection or not
		// Multiple is enabled by default, assuming that we are on normal user listing page
		// If tmpl = component, this means that other elements is retrieving user listing through ajax, in that case, we default it to false instead
		$multiple = true;

		if ($this->input->get('tmpl', '', 'string') === 'component') {
			$multiple = $this->input->get('multiple', false, 'bool');
		}

		$this->set('multiple', $multiple);

		// Get users
		$users		= $model->getUsersWithState();

		// Get pagination from model
		$pagination		= $model->getPagination();

		$callback 		= JRequest::getVar('callback', '');

		// Prepare usergroup array separately because the usergroup title is no longer in the user object
		$usergroupsData = FD::model('Users')->getUserGroups();

		// Reformat the usergroup to what we want
		$usergroups = array();
		foreach ($usergroupsData as $row) {
			$usergroups[$row->id] = $row->title;
		}

		$this->set('usergroups', $usergroups);

		$this->set('profile'		, $profile);
		$this->set('ordering'		, $ordering);
		$this->set('limit'			, $limit);
		$this->set('direction'		, $direction);
		$this->set('callback'		, $callback);
		$this->set('search'		, $search);
		$this->set('published'		, $published);
		$this->set('group'			, $group);
		$this->set('pagination'	, $pagination);
		$this->set('users' 		, $users);

		echo parent::display('admin/users/default');
	}

	/**
	 * Displays the export user form
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function export()
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_EXPORT_USERS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_EXPORT_USERS');

		// Get a list of profiles on the site
		$model = FD::model('Profiles');
		$profiles = $model->getProfiles();

		$this->set('profiles', $profiles);

		echo parent::display('admin/users/export');
	}

	/**
	 * Displays a list of pending approval users.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function pending()
	{
		JToolbarHelper::deleteList();
		JToolbarHelper::divider();
		JToolbarHelper::custom('approve', 'publish', 'social-publish-hover', JText::_('COM_EASYSOCIAL_APPROVE_BUTTON'), true);
		JToolbarHelper::custom('reject', 'unpublish', 'social-unpublish-hover', JText::_('COM_EASYSOCIAL_REJECT_BUTTON'), true);


		$this->setHeading('COM_EASYSOCIAL_HEADING_PENDING_APPROVALS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_PENDING_APPROVALS');

		// Get the user's model.
		$model = FD::model('Users', array('initState' => true));

		$ordering 	= JRequest::getVar('ordering', $model->getState('ordering'));
		$direction 	= JRequest::getVar('direction'	, $model->getState('direction'));
		$limit 		= $model->getState('limit');
		$published 	= $model->getState('published');
		$filter		= JRequest::getWord('filter', $model->getState('filter'));
		$profile 	= JRequest::getInt('profile', $model->getState('profile'));

		$result 		= $model->getUsers(array('state' => SOCIAL_REGISTER_APPROVALS, 'ignoreESAD' => true, 'limit' => $limit));
		$pagination 	= $model->getPagination();
		$users 			= array();

		if($result)
		{
			foreach($result as $row)
			{
				$users[]	= FD::user($row->id);
			}
		}

		$profilesModel	= FD::model('Profiles');
		$profiles 		= $profilesModel->getProfiles();

		$this->set('profile'		, $profile);
		$this->set('limit'			, $limit);
		$this->set('ordering'		, $ordering);
		$this->set('direction'		, $direction);
		$this->set('profiles'		, $profiles);
		$this->set('users'			, $users);
		$this->set('pagination'	, $pagination);
		$this->set('filter'		, $filter);
		$this->set('search'		, '');

		parent::display('admin/users/default.pending');
	}

	/**
	 * Post process after account is activated
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function activate()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=users');
	}

	/**
	 * Post processing after a user is blocked or unblocked
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublish()
	{
		// Disallow access
		if(!$this->authorise('easysocial.access.users'))
		{
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users');
		$this->close();
	}

	/**
	 * Post processing after a user profile has changed
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function switchProfile()
	{
		// Disallow access
		if(!$this->authorise('easysocial.access.users'))
		{
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$this->info->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users');
		$this->close();
	}

	/**
	 * Displays user profile layout in the back end.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function form($errors = null)
	{
		// Disallow access
		if (!$this->authorise('easysocial.access.users')) {
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		// Set any errors
		if ($this->hasErrors()) {
			$this->info->set($this->getMessage());
		}

		// Get the user from the request.
		$id = $this->input->get('id', 0, 'int');

		// Add Joomla buttons
		$this->addButtons(__FUNCTION__);

		// Set page heading
		if (!$id) {
			echo $this->newForm($errors);
		} else {
			echo $this->editForm($id, $errors);
		}
	}

	/**
	 * Displays the new user form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function newForm($errors = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_CREATE_USER');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_CREATE_USER');

		// Get the profile id
		$profileId	= JRequest::getInt('profileId');

		$model		= FD::model('Profiles');
		$profiles	= $model->getProfiles();

		$profile	= FD::table('Profile');

		// Load front end's language file
		FD::language()->loadSite();

		// If profile id is already loaded, just display the form
		if ($profileId) {
			$profile->load($profileId);

			// Get the steps model
			$stepsModel = FD::model('Steps');
			$steps		= $stepsModel->getSteps($profileId, SOCIAL_TYPE_PROFILES);

			// Init fields library
			$fields		= FD::fields();

			// New user doesn't need privacy here, hence we manually override ths privacy to be false
			$fields->init(array('privacy' => false));

			// Get custom fields model.
			$fieldsModel	= FD::model('Fields');

			// Build the arguments
			$user			= new SocialUser();
			$post			= JRequest::get('post');
			$args			= array(&$post, &$user, $errors);

			// Get the custom fields for each of the steps.
			foreach ($steps as &$step) {
				$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id));

				// Trigger onEdit for custom fields.
				if (!empty($step->fields)) {
					$fields->trigger('onAdminEdit', SOCIAL_FIELDS_GROUP_USER, $step->fields, $args);
				}
			}

			$this->set('steps', $steps);
		}

		$this->set('profile'	, $profile);
		$this->set('profileId'	, $profileId);
		$this->set('profiles', $profiles);

		return parent::display('admin/users/form.new');
	}

	/**
	 * Displays the edit form of user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The id of the user that is being edited.
	 */
	public function editForm($id, $errors = null)
	{
		// Get the user object
		$user = FD::user($id);

		// Get the user's profile
		$profile = $user->getProfile();

		$this->setHeading($user->getName() . ' (' . $profile->get('title') . ')');
		$this->setDescription(JText::_('COM_EASYSOCIAL_DESCRIPTION_EDIT_USER'));

		// Load up language file from the front end.
		FD::language()->loadSite();

		// Get a list of access rules that are defined for this
		$accessModel = FD::model('Access');

		// Get user's privacy
		$privacyLib = FD::get('Privacy', $user->id);
		$privacyData = $privacyLib->getData();
		$privacy = array();


		// Update the privacy data with proper properties.
		if ($privacyData) {

			foreach ($privacyData as $group => $items) {

				// We do not want to show field privacy rules here because it does not make sense for user to set a default value
				// Most of the fields only have 1 and it is set in Edit Profile page
				if ($group === 'field') {
					continue;
				}

				foreach ($items as &$item) {
					$rule 		= strtoupper(JString::str_ireplace('.', '_', $item->rule));
					$groupKey 	= strtoupper($group);

					$item->groupKey 	= $groupKey;
					$item->label 		= JText::_('COM_EASYSOCIAL_PRIVACY_LABEL_' . $groupKey . '_' . $rule);
					$item->tips 		= JText::_('COM_EASYSOCIAL_PRIVACY_TIPS_' . $groupKey . '_' . $rule);
				}

				$privacy[$group] = $items;
			}
		}


		// Get the steps model
		$stepsModel = FD::model('Steps');
		$steps 		= $stepsModel->getSteps($user->profile_id, SOCIAL_TYPE_PROFILES);

		// Get custom fields model.
		$fieldsModel 	= FD::model('Fields');

		// Get custom fields library.
		$fields = FD::fields();

		// Manually set the user here because admin edit might be editing a different user
		$fields->setUser($user);

		// Get the custom fields for each of the steps.
		foreach ($steps as &$step) {

			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $user->id, 'dataType' => SOCIAL_TYPE_USER));

			// Trigger onEdit for custom fields.
			if (!empty($step->fields)) {
				$post = $this->input->getArray('post');
				$args = array(&$post, &$user, $errors);

				$fields->trigger('onAdminEdit', SOCIAL_FIELDS_GROUP_USER, $step->fields, $args);
			}
		}

		// Get user badges
		$badges = $user->getBadges();

		// Get the user notification settings
		$alertLib = FD::alert();
		$alerts = $alertLib->getUserSettings($user->id);

		// Get stats
		$stats = $this->getStats($user);

		// Get user points history
		$pointsModel = FD::model('Points');
		$pointsHistory = $pointsModel->getHistory($user->id, array('limit' => 20));
		$pointsPagination = $pointsModel->getPagination();

		// Get user's groups
		$userGroups = array_keys($user->groups);

		// We need to hide the guest user group that is defined in com_users options.
		// Public group should also be hidden.
		$userOptions = JComponentHelper::getComponent('com_users')->params;

		$defaultRegistrationGroup 	= $userOptions->get('new_usertype');
		$guestGroup = array(1, $userOptions->get('guest_usergroup'));

		$this->set('userGroups'	, $userGroups);
		$this->set('guestGroup'	, $guestGroup);
		$this->set('stats'			, $stats);
		$this->set('pointsHistory', $pointsHistory);
		$this->set('alerts'		, $alerts);
		$this->set('privacy'		, $privacy);
		$this->set('badges'		, $badges);
		$this->set('steps'			, $steps);
		$this->set('user'			, $user);

		return parent::display('admin/users/form');
	}

	/**
	 * Retrieves user statistics
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStats(SocialUser $user)
	{
		// Build user statistics
		$stats 			= $this->getStatsDates();
		$stats->items	= array();

		// Get list of user activity
		$streamModel		= FD::model('Stream');
		$obj 			= new stdClass();
		$obj->title		= JText::_('COM_EASYSOCIAL_USERS_STATS_STREAM_POSTS');
		$obj->items 	= $streamModel->getPostStats($stats->dates, $user->id);
		$stats->items[]	= $obj;

		// Get list of user conversations
		// $conversationsModel 	= FD::model('Conversations');
		// $conversationsModel->getConversationStats($stats->dates, $user->id);

		// Get stats for user likes
		$likesModel 	= FD::model('Likes');
		$obj 			= new stdClass();
		$obj->title		= JText::_('COM_EASYSOCIAL_USERS_STATS_LIKES');
		$obj->items 	= $likesModel->getLikeStats($stats->dates, $user->id);
		$stats->items[]	= $obj;

		// Get stats for user comments
		$commentsModel 		= FD::model('Comments');
		$obj 			= new stdClass();
		$obj->title		= JText::_('COM_EASYSOCIAL_USERS_STATS_COMMENTS');
		$obj->items 	= $commentsModel->getCommentStats($stats->dates, $user->id);
		$stats->items[]	= $obj;

		return $stats;
	}

	/**
	 * Retrieves the date stats
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStatsDates()
	{
		$dates 		= array();

		// Get the past 7 days
		$curDate 	= FD::date();
		for($i = 0 ; $i < 7; $i++)
		{
			$obj = new stdClass();

			if($i == 0)
			{
				$dates[]			= $curDate->toMySQL();
				$friendlyDates[]	= $curDate->format( JText::_('COM_EASYSOCIAL_DATE_DM') );
			}
			else
			{
				$unixdate 		= $curDate->toUnix();
				$new_unixdate 	= $unixdate - ($i * 86400);
				$newdate  		= FD::date($new_unixdate);

				$dates[] 			= $newdate->toMySQL();
				$friendlyDates[]	= $newdate->format( JText::_('COM_EASYSOCIAL_DATE_DM') );
			}
		}

		// Reverse the dates
		$dates 			= array_reverse($dates);
		$friendlyDates	= array_reverse($friendlyDates);

		$result 		= new stdClass();
		$result->dates			= $dates;
		$result->friendlyDates	= $friendlyDates;

		return $result;
	}

	/**
	 * Gets triggered when the user is approved
	 *
	 * @param	SocialUser	The user objct.
	 */
	public function approve($user)
	{
		// Disallow access
		if(!$this->authorise('easysocial.access.users'))
		{
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users&layout=pending');
	}

	/**
	 * Gets triggered when the apply button is clicked.
	 *
	 * @param	Socialuser	The user objct.
	 */
	public function apply(&$user)
	{
		// Disallow access
		if(!$this->authorise('easysocial.access.users'))
		{
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$errors 	= $this->getErrors();

		if($errors)
		{

		}

		$this->redirect('index.php?option=com_easysocial&view=users&id=' . $user->id . '&layout=form');
	}

	/**
	 * Post process after saving a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The current task
	 * @return
	 */
	public function store($task, $user)
	{
		// Disallow access
		if(!$this->authorise('easysocial.access.users'))
		{
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		// Enqueue the message
		FD::info()->set($this->getMessage());

		// If there's an error on the storing, we don't need to perform any redirection.
		if($this->hasErrors())
		{
			// Load the form for the user.
			return $this->form($user);
		}

		if($task == 'save')
		{
			return $this->redirect('index.php?option=com_easysocial&view=users');
		}

		if($task == 'apply')
		{
			return $this->redirect('index.php?option=com_easysocial&view=users&layout=form&id=' . $user->id);
		}

		if($task == 'savenew')
		{
			return $this->redirect('index.php?option=com_easysocial&view=users&layout=form');
		}
	}

	/**
	 * Post process after a badge has been removed
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeBadge()
	{
		FD::info()->set($this->getMessage());

		$userId 	= JRequest::getInt('userid');

		$this->redirect('index.php?option=com_easysocial&view=users&layout=form&id=' . $userId);
	}

	/**
	 * Reject a user's registration application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject()
	{
		// Disallow access
		if(!$this->authorise('easysocial.access.users'))
		{
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users&layout=pending');
	}

	/**
	 * Post processing after user is assigned into a group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assign()
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users');
	}

	/**
	 * Post processing after user is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Disallow access
		if(!$this->authorise('easysocial.access.users'))
		{
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users');
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
	public function addButtons($layout)
	{
		switch($layout)
		{
			case 'form':

				JToolbarHelper::apply('apply', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE'), false, false);
				JToolbarHelper::save('save', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE'));
				JToolbarHelper::save2new('savenew', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_NEW'));
				JToolbarHelper::cancel('cancel', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL'));
			break;

			case 'display':
			default:


			break;
		}
	}

	/**
	 * Post process after resending activation email
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function resendActivate()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=users');
	}

	/**
	 * Post process after resetting points
	 *
	 * @since	1.4.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function resetPoints()
	{
		$this->info->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=users');
	}

	/**
	 * Post process after points has been inserted for user
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function insertPoints()
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users');
	}

	/**
	 * Post process after badge has been inserted for user
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function insertBadge()
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=users');
	}
}
