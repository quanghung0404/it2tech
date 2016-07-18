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

// Import parent view
FD::import('site:/views/views');


class EasySocialViewGroups extends EasySocialSiteView
{
	/**
	 * Checks if this feature should be enabled or not.
	 *
	 * @since	1.2
	 * @access	private
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	private function checkFeature()
	{
		// Do not allow user to access groups if it's not enabled
		if (!$this->config->get('groups.enabled')) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_DISABLED'), SOCIAL_MSG_ERROR);

			FD::info()->set($this->getMessage());
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}
	}

	/**
	 * Default method to display the all groups page.
	 *
	 * @since	1.2
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display($tpl = null)
	{
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Set the page title
		$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS'));

		// Set the page breadcrumb
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS'));

		$id = $this->input->get('userid', 0, 'int');
		$id = !$id ? null : $id;
		$user = FD::user($id);
		$my = FD::user();

		// Get active filter
		$filter = $this->input->get('filter', 'all', 'cmd');

		$allowedFilter = array('all', 'invited', 'mine', 'featured');

		// Only allow filters that we know.
		if (!empty($filter) && !in_array($filter, $allowedFilter)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'));
		}

		// Get a list of group categories
		$catModel = FD::model('GroupCategories');
		$categories = $catModel->getCategories(array('state' => SOCIAL_STATE_PUBLISHED, 'ordering' => 'ordering'));

		$model = FD::model('Groups');
		$options = array('state' => SOCIAL_STATE_PUBLISHED, 'featured' => false);

		// If user is site admin, they should be able to see everything.
		$options['types'] = $my->isSiteAdmin() ? 'all' : 'user';

		// Determine the pagination limit
		$limit = FD::themes()->getConfig()->get('groups_limit', 20);
		$options['limit'] = $limit;

		// Determine if this is filtering groups by category
		$categoryId = $this->input->get('categoryid', 0, 'int');

		// Default the active category to false
		$this->set('activeCategory', false);

		if ($categoryId) {
			$category 	= FD::table('GroupCategory');
			$state = $category->load($categoryId);

			if (!$state) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_INVALID_CATEGORY_ID'));
			}

			$this->set('activeCategory'	, $category);

			$options['category'] = $category->id;
			$filter = 'all';

			$this->page->title($category->get('title'));
		}

		// Since not logged in users cannot filter by 'invited' or 'mine', they shouldn't be able to access these filters at all
		if ($this->my->guest && ($filter == 'invited' || $filter == 'mine')) {
			return $this->app->redirect(FRoute::dashboard(array(), false));
		}

		// If the default filter is invited, we only want to fetch groups that the user has been
		// invited to.
		if ($filter == 'invited') {
			$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_INVITED'));

			$options['invited'] = $my->id;
			$options['types'] = 'all';
			$options['featured'] = '';
		}

		// Filter by own groups
		if ($filter == 'mine') {
			$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_MY_GROUPS'));

			$options['uid'] = $my->id;
			$options['types'] = 'all';
		}

		// Get ordering option if any
		$ordering = $this->input->get('ordering', 'latest', 'cmd');
		$options['ordering'] = $ordering;

		// Get a list of groups
		if ($filter == 'featured') {
			$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_FEATURED'));

			$groups = array();
		} else {
			$groups = $model->getGroups($options);
		}

		// Load up the pagination for the groups here.
		$pagination = $model->getPagination();

		// Get total number of groups on the site
		$totalGroups = $model->getTotalGroups(array('types' => $my->isSiteAdmin() ? 'all' : 'user'));

		// Get total number of featured groups on the site
		$totalFeaturedGroups = $model->getTotalGroups(array('featured' => true));

		// Get the total number of groups the user created
		$totalCreatedGroups = $my->getTotalGroups();

		// Get a list of featured groups
		$featuredGroups = array();

		if ($filter != 'invited') {
			$options['featured'] = true;
			$featuredGroups	= $model->getGroups($options);
		}

		// Get total number of invitations
		$totalInvites	= $model->getTotalInvites($my->id);

		$hrefs = array();

		$hrefs['latest'] = FRoute::groups((array('ordering' => 'latest')));
		$hrefs['name'] = FRoute::groups((array('ordering' => 'name')));
		$hrefs['popular'] = FRoute::groups((array('ordering' => 'popular')));

		$this->set('totalCreatedGroups'	, $totalCreatedGroups);
		$this->set('totalFeaturedGroups', $totalFeaturedGroups);
		$this->set('totalGroups', $totalGroups);
		$this->set('pagination', $pagination);
		$this->set('totalInvites', $totalInvites);
		$this->set('featuredGroups', $featuredGroups);
		$this->set('groups', $groups);
		$this->set('filter', $filter);
		$this->set('categories', $categories);
		$this->set('user', $user);
		$this->set('ordering', $ordering);
		$this->set('hrefs', $hrefs);

		parent::display('site/groups/default');
	}

	/**
	 * Default method to display the group creation page.
	 * This is the first page that displays the category selection.
	 *
	 * @since	1.2
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function create($tpl = null)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Only users with valid account is allowed to create
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		if (!$this->my->getAccess()->get('groups.create') && !$this->my->isSiteAdmin()) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NOT_ALLOWED_TO_CREATE_GROUP'), SOCIAL_MSG_ERROR);

			$this->info->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Ensure that the user did not exceed their group creation limit
		if ($this->my->getAccess()->intervalExceeded('groups.limit', $this->my->id) && !$this->my->isSiteAdmin()) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_EXCEEDED_LIMIT'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(FRoute::groups(array(), false));
		}

		// Detect for an existing create group session.
		$session = JFactory::getSession();

		$stepSession = FD::table('StepSession');

		// If user doesn't have a record in stepSession yet, we need to create this.
		if (!$stepSession->load($session->getId())) {
			$stepSession->set('session_id', $session->getId());
			$stepSession->set('created', FD::get('Date')->toMySQL());
            $stepSession->set('type', SOCIAL_TYPE_GROUP);

			if (!$stepSession->store()) {
				$this->setError($stepSession->getError());
				return false;
			}
		}

		$model = ES::model('Groups');
		$categories	= $model->getCreatableCategories($this->my->getProfile()->id);

		// If there's only 1 category, we should just ignore this step and load the steps page.
		if (count($categories) == 1) {

			$category = $categories[0];

			// Store the category id into the session.
			$session->set('category_id', $category->id, SOCIAL_SESSION_NAMESPACE);

			// Set the current category id.
			$stepSession->uid 	= $category->id;

			// When user accesses this page, the following will be the first page
			$stepSession->step 	= 1;

			// Add the first step into the accessible list.
			$stepSession->addStepAccess(1);

			// Let's save this into a temporary table to avoid missing data.
			$stepSession->store();

			$this->steps();
			return;
		}

		// Set the page title
		FD::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_GROUP_CATEGORY'));

		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS'), FRoute::groups());
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_GROUP_CATEGORY'));

		$this->set('categories', $categories);

		parent::display('site/groups/create');
	}

	/**
	 * Post process after user withdraws application to join the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function withdraw($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		FD::info()->set($this->getMessage());

		return $this->redirect(FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * Post process after a user leaves a group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function leaveGroup($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		FD::info()->set($this->getMessage());

		return $this->redirect(FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * The workflow for creating a new group.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function steps()
	{
		// Only users with a valid account is allowed here.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Check if the user is allowed to create group or not.
		$my			= FD::user();

		if(!$this->my->getAccess()->get('groups.create'))
		{
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NOT_ALLOWED_TO_CREATE_GROUP'), SOCIAL_MSG_ERROR);
			FD::info()->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Ensure that the user did not exceed their group creation limit
		if ($my->getAccess()->intervalExceeded('groups.limit', $my->id) && !$my->isSiteAdmin()) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_EXCEEDED_LIMIT'), SOCIAL_MSG_ERROR);
			FD::info()->set($this->getMessage());

			return $this->redirect(FRoute::groups(array(), false));
		}

		// Get configuration data
		$config 	= FD::config();
		$info 		= FD::info();

		// Retrieve the user's session.
		$session    	= JFactory::getSession();
		$stepSession	= FD::table('StepSession');
		$stepSession->load($session->getId());

		// If there's no registration info stored, the user must be a lost user.
		if(is_null($stepSession->step))
		{
			$info->set(JText::_('Unable to detect active step access'), SOCIAL_MSG_ERROR);
			return $this->redirect(FRoute::groups(array(), false));
		}

		// Get the category that is being selected
		$categoryId 	= $stepSession->uid;

		// Load up the category
		$category		= FD::table('GroupCategory');
		$category->load($categoryId);

		// Check if user really has access to create groups from this category
		if (!$category->hasAccess('create', $my->getProfile()->id)) {

			$info->set(false, JText::sprintf('COM_EASYSOCIAL_GROUPS_NOT_ALLOWED_TO_CREATE_GROUP_IN_CATEGORY', $category->get('title')), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::groups(array(), false));
		}

		// Get the current step index
		$stepIndex		= JRequest::getInt('step', 1);

		// Determine the sequence from the step
		$sequence		= $category->getSequenceFromIndex($stepIndex, SOCIAL_PROFILES_VIEW_REGISTRATION);

		// Users should not be allowed to proceed to a future step if they didn't traverse their sibling steps.
		if(empty($stepSession->session_id) || ($stepIndex > 1 && !$stepSession->hasStepAccess($stepIndex)))
		{
			$info->set(false, JText::sprintf('COM_EASYSOCIAL_GROUPS_PLEASE_COMPLETE_PREVIOUS_STEP_FIRST', $sequence), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::groups(array('layout' => 'steps', 'step' => 1), false));
		}

		// Check if this is a valid step in the profile
		if(!$category->isValidStep($sequence, SOCIAL_GROUPS_VIEW_REGISTRATION))
		{
			$info->set(false, JText::sprintf('COM_EASYSOCIAL_GROUPS_NO_ACCESS_TO_THE_STEP', $sequence), SOCIAL_MSG_ERROR);

			return $this->redirect(FRoute::groups(array('layout' => 'steps', 'step' => 1), false));
		}

		// Remember current state of registration step
		$stepSession->set('step', $stepIndex);
		$stepSession->store();

		// Load the current workflow / step.
		$step 		= FD::table('FieldStep');
		$step->loadBySequence($category->id, SOCIAL_TYPE_CLUSTERS, $sequence);

		// Determine the total steps for this profile.
		$totalSteps	= $category->getTotalSteps();

		// Try to retrieve any available errors from the current registration object.
		$errors			= $stepSession->getErrors();

		// Try to remember the state of the user data that they have entered.
		$data           = $stepSession->getValues();

		// Since they are bound to the respective groups, assign the fields into the appropriate groups.
		$args 			= array(&$data, &$stepSession);

		// Get fields library as we need to format them.
		$fields 		= FD::fields();

		// Enforce privacy to be false for groups
		$fields->init(array('privacy' => false));

		// Retrieve custom fields for the current step
		$fieldsModel 	= FD::model('Fields');
		$customFields	= $fieldsModel->getCustomFields(array('step_id' => $step->id, 'visible' => SOCIAL_GROUPS_VIEW_REGISTRATION));

		// Set the breadcrumb
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS'), FRoute::groups());
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_GROUPS_START_YOUR_GROUP'), FRoute::groups(array('layout' => 'create')));
		FD::page()->breadcrumb($step->get('title'));

		// Set the page title
		FD::page()->title($step->get('title'));

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		// Trigger onRegister for custom fields.
		if(!empty($customFields))
		{
			$fields->trigger('onRegister', SOCIAL_FIELDS_GROUP_GROUP, $customFields, $args, $callback);
		}

		// Pass in the steps for this profile type.
		$steps 			= $category->getSteps(SOCIAL_GROUPS_VIEW_REGISTRATION);

		// Get the total steps
		$totalSteps		= $category->getTotalSteps(SOCIAL_PROFILES_VIEW_REGISTRATION);

		$this->set('stepSession'	, $stepSession);
		$this->set('steps'			, $steps);
		$this->set('currentStep'	, $sequence);
		$this->set('currentIndex'	, $stepIndex);
		$this->set('totalSteps'	, $totalSteps);
		$this->set('step'			, $step);
		$this->set('fields' 		, $customFields);
		$this->set('errors' 		, $errors);
		$this->set('category'		, $category);

		parent::display('site/groups/create.steps');
	}

	/**
	 * Editing a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function edit($errors = false)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Only users with a valid account is allowed here.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get configuration data
		$config 	= FD::config();
		$info 		= FD::info();

		// Load the language file from the back end.
		JFactory::getLanguage()->load('com_easysocial', JPATH_ADMINISTRATOR);

		// If have errors, then we set it
		if (!empty($errors)) {
			$info->set($this->getMessage());
		}

		// Check if the user is allowed to create group or not.
		$my			= FD::user();

		// Get the group id
		$id 		= JRequest::getInt('id');

		// Load the group
		$group		= FD::group($id);

		if(!$id || !$group)
		{
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'), SOCIAL_MSG_ERROR);
			$info->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Check if the user is allowed to edit this group
		if(!$group->isOwner() && !$group->isAdmin() && !$my->isSiteAdmin())
		{
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);
			$info->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Set the breadcrumb
		FD::page()->breadcrumb(JText::_('Groups'), FRoute::groups());
		FD::page()->breadcrumb($group->getName(), $group->getPermalink());
		FD::page()->breadcrumb(JText::_('Edit'));

		// Set the page title
		FD::page()->title(JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_EDIT', $group->getName()));

		// Load up the category
		$category		= FD::table('GroupCategory');
		$category->load($group->category_id);

		// Get the steps model
		$stepsModel 	= FD::model('Steps');
		$steps 			= $stepsModel->getSteps($category->id, SOCIAL_TYPE_CLUSTERS, SOCIAL_PROFILES_VIEW_EDIT);
		$fieldsModel 	= FD::model('Fields');

		// Get custom fields library.
		$fields 		= FD::fields();

		// Enforce privacy to be false for groups
		$fields->init(array('privacy' => false));

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		// Get the custom fields for each of the steps.
		foreach($steps as &$step)
		{
			$step->fields 	= $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP, 'visible' => 'edit'));

			// Trigger onEdit for custom fields.
			if(!empty($step->fields))
			{
				$post	= JRequest::get('post');
				$args 	= array(&$post, &$group, $errors);
				$fields->trigger('onEdit', SOCIAL_TYPE_GROUP, $step->fields, $args, $callback);
			}
		}

		$this->set('group'	, $group);
		$this->set('steps'	, $steps);

		echo parent::display('site/groups/edit');
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
	public function saveStep($session, $currentIndex, $completed = false)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		$info 		= FD::info();
		$config 	= FD::config();

		// Set any message that was passed from the controller.
		$info->set($this->getMessage());

		// If there's an error, redirect back user to the correct step and show the error.
		if($this->hasErrors())
		{
			return $this->redirect(FRoute::groups(array('layout' => 'steps', 'step' => $session->step), false));
		}

		// Registration is not completed yet, redirect user to the appropriate step.
		return $this->redirect(FRoute::groups(array('layout' => 'steps', 'step' => $session->step), false));
	}

	/**
	 * Default method to display the group entry page.
	 *
	 * @since	1.2
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function item($tpl = null)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		$id = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		// Check if the group is valid
		if (!$id || !$group->id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
		}

		// Ensure that the group is published
		if (!$group->isPublished()) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_UNAVAILABLE'), SOCIAL_MSG_ERROR);
			FD::info()->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Check if the group is accessible
		if ($group->isInviteOnly() && !$group->isMember() && !$group->isInvited() && !$this->my->isSiteAdmin()) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NOT_ALLOWED'), SOCIAL_MSG_ERROR);
			FD::info()->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// If the user is not the owner and the user has been blocked by the group creator
		if ($this->my->id != $group->creator_uid && $this->my->isBlockedBy($group->creator_uid)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
		}

		// Set the page title.
		$this->page->title($group->getName());

		// Set the breadcrumbs for the group
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_GROUPS_PAGE_TITLE'), FRoute::groups());
		$this->page->breadcrumb($group->getName());

		$this->set('group', $group);

		if (($group->isInviteOnly() || $group->isClosed()) && !$group->isMember() && !$this->my->isSiteAdmin()) {
			// Display private group contents;
			return parent::display('site/groups/restricted');
		}

		// Update the hit counter
		$group->hit();

		// Get the start limit
		$startlimit = $this->input->get('limitstart', 0, 'int');

		// Get the context
		$context = $this->input->get('app', '', 'cmd');

		// Get group's filter for this logged in user.
		$filters = array();
		if (! $this->my->guest) {
			$filters = $group->getFilters($this->my->id);
		}

		// Get a list of application filters
		$streamModel = FD::model('Stream');
		$appFilters = $streamModel->getAppFilters(SOCIAL_TYPE_GROUP);

		$this->set('context', $context);
		$this->set('filters', $filters);
		$this->set('appFilters', $appFilters);

		// Load list of apps for this group
		$model = FD::model('Apps');

		// Retrieve apps
		$apps = $model->getGroupApps($group->id);

		// We need to load the app's own css file.
		foreach ($apps as $app) {
			$app->loadCss();
		}

		$this->set('apps', $apps);

		$appId = $this->input->get('appId', 0, 'int');
		$contents = '';
		$isAppView = false;

		if ($appId) {

			// Load the application.
			$app = FD::table('App');
			$app->load($appId);
			$app->loadCss();

			FD::page()->title($group->getName() . ' - ' . $app->get('title'));
			FD::page()->breadcrumb($app->get('title'));

			// Load the library.
			$lib = FD::apps();
			$contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'groups', $app, array('groupId' => $group->id));

			$isAppView 	= true;
		}

		$this->set('appId', $appId);

		// Determine if the current request is for "tags"
		$hashtag = $this->input->get('tag', '');

		// hashtagalias used for the header hashtag stream
		$hashtagAlias = $this->input->get('tag', '', 'default');

		// Determines if we should display the custom content based on type.
		$type = $this->input->get('type', '', 'cmd');

		// @since 1.4.6
        // If it is a hashtag view, let the timeline be the default display
		if (!$isAppView && empty($type) && empty($hashtag)) {
			$type = FD::config()->get('groups.item.display', 'timeline');
		}

		$filterId = $this->input->get('filterId', 0, 'int');

		if ($type == 'filterForm') {
			$theme = FD::themes();
			$streamFilter = FD::table('StreamFilter');

			if ($filterId) {
				$streamFilter->load($filterId);
			}

			$theme->set('controller', 'groups');
			$theme->set('filter', $streamFilter);
			$theme->set('uid', $group->id);

			$contents = $theme->output('site/stream/form.edit');
		}

		if ($type == 'info') {
			FD::language()->loadAdmin();

			$currentStep = JRequest::getInt('step', 1);

			$steps = FD::model('Steps')->getSteps($group->category_id, SOCIAL_TYPE_CLUSTERS, SOCIAL_GROUPS_VIEW_DISPLAY);

			$fieldsLib = FD::fields();

			$fieldsLib->init(array('privacy' => false));

			$fieldsModel = FD::model('Fields');

			$index = 1;

			foreach ($steps as $step) {
				$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP, 'visible' => SOCIAL_GROUPS_VIEW_DISPLAY));

				if (!empty($step->fields)) {
					$args = array($group);

					$fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_GROUP, $step->fields, $args);
				}

				$step->hide = true;

				foreach ($step->fields as $field) {
					// As long as one of the field in the step has an output, then this step shouldn't be hidden
					// If step has been marked false, then no point marking it as false again
					// We don't break from the loop here because there is other checking going on
					if (!empty($field->output) && $step->hide === true ) {
						$step->hide = false;
					}
				}

				if ($index === 1) {
					$step->url = FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'info'), false);
				} else {
					$step->url = FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'info', 'infostep' => $index), false);
				}

				$step->title = $step->get('title');

				$step->active = !$step->hide && $currentStep == $index;

				if ($step->active) {
					$theme = FD::themes();

					$theme->set('fields', $step->fields);

					$contents = $theme->output('site/groups/item.info');
				}

				$step->index = $index;

				$index++;
			}

			$this->set('infoSteps', $steps);
		}


		$streamMod = $group->getParams()->get('stream_moderation', null);
		$showPendingPostFilter = false;

		if ($streamMod !== null) {
			if (! $streamMod) {
				// lets check if this group has items under pending moderation or not.
				$showPendingPostFilter = false;
			} else {
				$showPendingPostFilter = true;
			}
			// lets check if this
		}

		$this->set('showPendingPostFilter', $showPendingPostFilter);
		$this->set('type', $type);
		$this->set('filterId', $filterId);
		$this->set('contents', $contents);

		if (!empty($contents)) {
			return parent::display('site/groups/item');
		}

		// If no content, only we get the stream. No point getting stream and contents at the same time.

		// Retrieve group's stream
		$stream = FD::stream();

		// If there's a hash tag, try to get the actual title to display on the site
		if ($hashtag) {
			$tag = $stream->getHashTag($hashtag);
			$hashtag = $tag->title;
		}

		// Retrieve story form for group
		$story = FD::get('Story', SOCIAL_TYPE_GROUP);
		$story->setCluster($group->id, SOCIAL_TYPE_GROUP);
		$story->showPrivacy(false);

		if ($hashtag) {
			$story->setHashtags(array($hashtag));
		}

		// Only group members allowed to post story updates on group page.
		if ($group->isMember() || $this->my->isSiteAdmin()) {

			// Set the story data on the stream
			$stream->story = $story;

			// Get the group params
			$params = $group->getParams();

			// Ensure that the user has permissions to see the story form
			$permissions = $params->get('stream_permissions', null);

			// If permissions has been configured before.
			if (!is_null($permissions)) {

				// If the user is not an admin, ensure that permissions has member
				if (!$group->isAdmin() && !in_array('member', $permissions)) {
					unset($stream->story);
				}

				// If the user is an admin, ensure that permissions has admin
				if ($group->isAdmin() && !in_array('admin', $permissions) && !$group->isOwner()) {
					unset($stream->story);
				}
			}

		}

		//lets get the sticky posts 1st
		$stickies = $stream->getStickies(array('clusterId' => $group->id, 'clusterType' 	=> SOCIAL_TYPE_GROUP, 'limit' => 0));
		if ($stickies) {
			$stream->stickies = $stickies;
		}

		// lets get stream items for this group
		$options = array('clusterId' => $group->id, 'clusterType' 	=> SOCIAL_TYPE_GROUP, 'nosticky' => true);

		// stream filter id
		$filterId = $this->input->get('filterId', 0, 'int');

		if ($filterId) {
			$sfilter = FD::table('StreamFilter');
			$sfilter->load( $filterId );

			$hashtags = $sfilter->getHashTag();
			$tags = explode(',', $hashtags);

			if ($tags) {
				$options['tag'] = $tags;
			}
		}

		// we only wan streams thats has this hashtag associated.
		if ($hashtag) {
			$options['tag'] = array($hashtag);
		}

		$options['startlimit'] = $startlimit;

		// Filter stream item by specific context type
		if ($context) {
			$options['context'] = $context;
		}

		if ($type == 'moderation') {
			$options['onlyModerated'] = true;

			unset($stream->story);
		}

		$stream->get($options);

		// RSS
		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(FRoute::groups(array('id' => $group->getAlias(), 'layout' => 'item'), false));
		}

		// Apply opengraph tags for the group.
		FD::opengraph()->addGroup($group);

		$this->set('rssLink', $this->rssLink);
		$this->set('context', $context);
		$this->set('stream', $stream);
		$this->set('hashtag', $hashtag);
		$this->set('hashtagAlias', $hashtagAlias);

		parent::display('site/groups/item');
	}

	/**
	 * Post process after a group is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function complete($group)
	{
		FD::info()->set($this->getMessage());

		$url 	= FRoute::groups(array(), false);

		if($group->state == SOCIAL_STATE_PUBLISHED)
		{
			$url 	= FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false);
		}

		$this->redirect($url);
	}

	/**
	 * Displays information from groups within a particular category
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function category()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the category id from the query
		$id = $this->input->get('id', 0, 'int');

		$category = FD::table('GroupCategory');
		$category->load($id);

		// Check if the category is valid
		if (!$id || !$category->id) {
			return JError::raise(E_ERROR, 404, JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'));
		}

		// Load backend language file
		FD::language()->loadAdmin();

		// Set the page title to this category
		FD::page()->title($category->get('title'));

		// Set the breadcrumbs
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS'), FRoute::groups());
		FD::page()->breadcrumb($category->get('title'));

		// Get recent 10 groups from this category
		$options = array('sort' => 'random', 'category' => $category->id, 'state' => SOCIAL_STATE_PUBLISHED);

		$model = FD::model('Groups');
		$groups = $model->getGroups($options);

		// Get random members from this category
		$randomMembers 	= $model->getRandomCategoryMembers($category->id, SOCIAL_CLUSTER_CATEGORY_MEMBERS_LIMIT);

		// Get group creation stats for this category
		$stats 			= $model->getCreationStats($category->id);

		// Get total groups within a category
		$totalGroups 	= $model->getTotalGroups(array('category_id' => $category->id));

		// Get total albums within a category
		$totalAlbums 	= $model->getTotalAlbums(array('category_id' => $category->id));

		// Get the stream for this group
		$stream 	= FD::stream();
		$stream->get(array('clusterCategory' => $category->id, 'clusterType' => SOCIAL_TYPE_GROUP));

		// Get random albums for groups in this category
		$randomAlbums 	= $model->getRandomAlbums(array('category_id' => $category->id, 'core' => false));

		$this->set('randomAlbums'	, $randomAlbums);
		$this->set('stream'		, $stream);
		$this->set('totalGroups'	, $totalGroups);
		$this->set('stats' 		, $stats);
		$this->set('randomMembers', $randomMembers);
		$this->set('groups'		, $groups);
		$this->set('category'		, $category);
		$this->set('totalAlbums'	, $totalAlbums);

		parent::display('site/groups/category.item');
	}

	/**
	 * Deprecated in 1.3. Use layout=item&type=info instead.
	 * Displays the information about a group.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function info()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		$id = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		// Check if the group is valid
		if (!$id || !$group->id) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'), SOCIAL_MSG_ERROR);
			FD::info()->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Deprecated and just redirect to the item page with type=info
		return $this->redirect(FRoute::groups(array('layout' => 'item', 'type' => 'info', 'id' => $group->getAlias())));

		// Check if the group is accessible
		if($group->type == SOCIAL_GROUPS_INVITE_TYPE && !$group->isMember())
		{
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NOT_ALLOWED'), SOCIAL_MSG_ERROR);
			FD::info()->set($this->getMessage());

			return $this->redirect(FRoute::dashboard(array(), false));
		}

		// Set the page title.
		FD::page()->title($group->getName());

		// Set the breadcrumbs
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_GROUPS_PAGE_TITLE'), FRoute::groups());
		FD::page()->breadcrumb($group->getName());

		// Determine if the user shouldn't be able to view the group's content
		if($group->type == SOCIAL_GROUPS_PRIVATE_TYPE && !$group->isMember())
		{
			// Display private group contents;
			return;
		}

		// Load language file from back end.
		FD::language()->loadAdmin();

		// Get the custom fields steps.
		// Get the steps model
		$stepsModel		= FD::model('Steps');
		$steps			= $stepsModel->getSteps($group->category_id, SOCIAL_TYPE_CLUSTERS, SOCIAL_PROFILES_VIEW_DISPLAY);

		// Get the fields library
		$fields			= FD::fields();

		// Enforce privacy to be false for groups
		$fields->init(array('privacy' => false));

		$fieldsModel	= FD::model('Fields');
		$incomplete 	= false;

		// Get the custom fields for each of the steps.
		foreach($steps as &$step)
		{
			$step->fields 	= $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP, 'visible' => SOCIAL_PROFILES_VIEW_DISPLAY));

			// Trigger onDisplay for custom fields.
			if(!empty($step->fields))
			{
				$args 	= array($group);

				$fields->trigger('onDisplay', SOCIAL_FIELDS_GROUP_GROUP, $step->fields, $args);
			}

			$step->hide = true;

			foreach($step->fields as $field)
			{
				// If the key output is set but is empty, means that the field does have an output, but there is no data to show
				// In this case, we mark this profile as incomplete
				// Incomplete profile will have a info displayed above saying "complete your profile now"
				// If incomplete has been marked true, then no point marking it as true again
				// We don't break from the loop here because there is other checking going on
				if(isset($field->output) && empty($field->output) && $incomplete === false)
				{
					$incomplete = true;
				}

				// As long as one of the field in the step has an output, then this step shouldn't be hidden
				// If step has been marked false, then no point marking it as false again
				// We don't break from the loop here because there is other checking going on
				if(!empty($field->output) && $step->hide === true)
				{
					$step->hide = false;
				}
			}
		}


		// Set template variables
		$this->set('incomplete', $incomplete);
		$this->set('steps'		, $steps);
		$this->set('group' 	, $group);

		parent::display('site/groups/info');
	}

	/**
	 * Post process after a user is rejected to join the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function reject($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * Post process after group admin cancel the user invitation
	 *
	 * @since	1.3
	 * @access	public
	 * @param	SocialGroup
	 */
	public function cancelInvitation($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * Post process after the group avatar is removed
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function removeAvatar(SocialGroup $group)
	{
		FD::info()->set($this->getMessage());

		$permalink 	= $group->getPermalink(false);

		$this->redirect($permalink);
	}

	/**
	 * Post process after a user is deleted from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function removeMember($group)
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * Post process after a user is approved to join the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function approve($group = null)
	{
		$this->info->set($this->getMessage());

		if ($this->hasErrors()) {
			return $this->redirect(FRoute::groups(array(), false));
		}

		// Default redirect
		$redirect = ESR::groups(array('layout' => 'item', 'id' => $group->getAlias()), false);

		// If caller provided a "return" value, we should respect it
		$return = $this->input->get('return', '', 'default');
		$return = !empty($return) ? base64_decode($return) : $return;

		$redirect = $return ? $return : $redirect;

		$this->redirect($redirect);
	}

	/**
	 * Post process after a user is invited to join the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function invite($group = null)
	{
		FD::info()->set($this->getMessage());

		// There are times where the group id is not provided
		if (!$group) {
			return $this->redirect(FRoute::dashboard(array(), false));
		}

		$this->redirect($group->getPermalink(false));
	}

	/**
	 * Post process after a group is published
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function approveGroup()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array(), false));
	}

	/**
	 * Post process after a group is rejected
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function rejectGroup()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array(), false));
	}


	/**
	 * Post process after a group is set as featured
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function setFeatured($group)
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array(), false));
	}

	/**
	 * Post process after a group is removed from being featured
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialGroup
	 */
	public function removeFeatured($group)
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array(), false));
	}

	/**
	 * Post process after category has been selected
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function selectCategory()
	{
		// Set message data.
		FD::info()->set($this->getMessage());

		// @task: Check for errors.
		if($this->hasErrors())
		{
			return $this->redirect(FRoute::groups(array(), false));
		}

		// @task: We always know that after selecting the profile type, the next step would always be the first step.
		$url 	= FRoute::groups(array('layout' => 'steps', 'step' => 1), false);

		return $this->redirect(FRoute::groups(array('layout' => 'steps', 'step' => 1), false));
	}

	/**
	 * Post process when a group is deleted
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array(), false));
	}

	/**
	 * Post process when a group is unpublished
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(FRoute::groups(array(), false));
	}

	/**
	 * Post process after saving group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function update($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		FD::info()->set($this->getMessage());

		return $this->redirect($group->getPermalink(false));
	}

	/**
	 * Post process after a user response to the invitation.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 */
	public function respondInvitation($group, $action)
	{
		$this->info->set($this->getMessage());

		if ($action == 'reject') {
			$redirect = FRoute::groups(array('filter' => 'invited'), false);
			return $this->redirect($redirect);
		}

		$redirect = FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false);
		return $this->redirect($redirect);
	}


	/**
	 * Post process after saving group filter
	 *
	 * @since	1.2
	 * @access	public
	 * @param	StreamFilter object
	 * @return
	 */
	public function saveFilter($filter, $groupId)
	{
		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		FD::info()->set($this->getMessage());

		$group = FD::group($groupId);

		$this->redirect(FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * Allows viewer to download a file from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function download()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Currently only registered users are allowed to view a file.
		FD::requireLogin();

		// Get the file id from the request
		$fileId 	= JRequest::getInt('fileid', null);

		$file 	= FD::table('File');
		$file->load($fileId);

		if(!$file->id || !$fileId)
		{
			// Throw error message here.
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		// Load up the group
		$group		= FD::group($file->uid);

		// Ensure that the user can really view this group
		if(!$group->canViewItem())
		{
			// Throw error message here.
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		$file->download();
		exit;
	}

	/**
	 * Allows viewer to download a conversation file
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Currently only registered users are allowed to view a file.
		FD::requireLogin();

		// Get the file id from the request
		$fileId 	= JRequest::getInt('fileid', null);

		$file 	= FD::table('File');
		$file->load($fileId);

		if(!$file->id || !$fileId)
		{
			// Throw error message here.
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		// Load up the group
		$group		= FD::group($file->uid);

		// Ensure that the user can really view this group
		if(!$group->canViewItem())
		{
			// Throw error message here.
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		$file->preview();
		exit;
	}

}
