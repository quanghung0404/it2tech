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

class EasySocialViewGroups extends EasySocialAdminView
{
	/**
	 * Displays a list of profiles in the back end.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 */
	public function display($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_GROUPS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_GROUPS');

		// Add buttons for the groups
		JToolbarHelper::addNew('create', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_NEW'), false);
		JToolbarHelper::divider();
		JToolbarHelper::custom('switchOwner', 'vcard', '', JText::_('COM_EASYSOCIAL_CHANGE_OWNER'));
		JToolbarHelper::custom('switchCategory', 'folder', '', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SWITCH_CATEGORY'));
		JToolbarHelper::divider();
		JToolbarHelper::publishList('publish');
		JToolbarHelper::unpublishList('unpublish');
		JToolbarHelper::divider();
		JToolbarHelper::custom('makeFeatured', 'featured', '', JText::_('COM_EASYSOCIAL_MAKE_FEATURED'));
		JToolbarHelper::custom('removeFeatured', 'star', '', JText::_('COM_EASYSOCIAL_REMOVE_FEATURED'));
		JToolbarHelper::deleteList('', 'delete', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_DELETE'));

		// Gets a list of profiles from the system.
		$model = FD::model('Groups', array('initState' => true));

		// Get the search query from post
		$search = JRequest::getVar('search', $model->getState('search'));

		// Get the current ordering.
		$ordering = JRequest::getVar('ordering', $model->getState('ordering'));
		$direction = JRequest::getVar('direction', $model->getState('direction'));
		$state = JRequest::getVar('state', $model->getState('state'));
		$type = JRequest::getInt('type', $model->getState('type'));
		$limit = $model->getState('limit');

		// Load front end language file
		FD::language()->loadSite();

		// Prepare options
		$groups = $model->getItemsWithState();
		$pagination	= $model->getPagination();

		$callback = JRequest::getVar('callback', '');

		// Set properties for the template.
		$this->set('type', $type);
		$this->set('layout', $this->getLayout());
		$this->set('ordering', $ordering);
		$this->set('limit', $limit);
		$this->set('state', $state);
		$this->set('direction', $direction);
		$this->set('callback', $callback);
		$this->set('pagination'	, $pagination);
		$this->set('groups', $groups);
		$this->set('search', $search);

		echo parent::display('admin/groups/default');
	}

	/**
	 * Displays a list of pending groups
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function pending($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_PENDING_GROUPS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_PENDING_GROUPS');

		// Display buttons on this page.
		JToolbarHelper::custom('approve', 'publish', 'social-publish-hover', JText::_('COM_EASYSOCIAL_APPROVE_BUTTON'), true);
		JToolbarHelper::custom('reject', 'unpublish', 'social-unpublish-hover', JText::_('COM_EASYSOCIAL_REJECT_BUTTON'), true);

		// Gets a list of profiles from the system.
		$model = FD::model('Groups', array('initState' => true));

		// Get the search query from post
		$search		= JRequest::getVar('search', $model->getState('search'));

		// Get the current ordering.
		$ordering 	= JRequest::getWord('ordering', $model->getState('ordering'));
		$direction 	= JRequest::getWord('direction', $model->getState('direction'));
		$limit 		= $model->getState('limit');

		// Prepare options
		$groups		= $model->getItems(array('pending' => true));
		$pagination	= $model->getPagination();

		$callback 	= JRequest::getVar('callback', '');

		// Set properties for the template.
		$this->set('layout'		, $this->getLayout());
		$this->set('ordering'		, $ordering);
		$this->set('limit'			, $limit);
		$this->set('direction'		, $direction);
		$this->set('callback'		, $callback);
		$this->set('pagination'	, $pagination);
		$this->set('groups'		, $groups);
		$this->set('search'		, $search);

		echo parent::display('admin/groups/pending');
	}

	/**
	 * Displays the category listings form this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function categories($tpl = null)
	{
		// Set the structure heading here.
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_GROUPS_CATEGORIES');
		$this->setDescription('COM_EASYSOCIAL_TOOLBAR_TITLE_GROUPS_CATEGORIES_DESC');

		// Add buttons for the groups
		JToolbarHelper::addNew('categoryForm', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_NEW'), false);
		JToolbarHelper::divider();
		JToolbarHelper::publishList('publishCategory');
		JToolbarHelper::unpublishList('unpublishCategory');
		JToolbarHelper::divider();
		JToolbarHelper::deleteList('', 'deleteCategory', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_DELETE'));

		// Gets a list of profiles from the system.
		$model = FD::model('GroupCategories', array('initState' => true));

		$search = $model->getState('search');
		$ordering = $model->getState('ordering');
		$direction = $model->getState('direction');
		$state = $model->getState('state');
		$limit = $model->getState('limit');

		// Prepare options
		$categories	= $model->getItems();
		$pagination	= $model->getPagination();

		$callback = $this->input->get('callback', '', 'default');

		// Set properties for the template.
		$this->set('layout', $this->getLayout());
		$this->set('ordering', $ordering);
		$this->set('limit', $limit);
		$this->set('state', $state);
		$this->set('direction', $direction);
		$this->set('callback', $callback);
		$this->set('pagination', $pagination);
		$this->set('categories', $categories);
		$this->set('search', $search);

		echo parent::display('admin/groups/categories');
	}

	/**
	 * Gets triggered when the save & close button is clicked.
	 *
	 * @param	Socialuser	The user objct.
	 */
	public function store($task, $group)
	{
		FD::info()->set($this->getMessage());

		// If there's an error on the storing, we don't need to perform any redirection.
		if ($this->hasErrors()) {
			// Load the form for the user.
			return $this->form($group);
		}

		$activeTab = $this->input->get('activeTab', 'profile', 'word');

		if ($task == 'apply' || $task == 'savecopy') {
			return $this->redirect('index.php?option=com_easysocial&view=groups&layout=form&id=' . $group->id . '&activeTab=' . $activeTab);
		}

		if ($task == 'save') {
			return $this->redirect('index.php?option=com_easysocial&view=groups');
		}

		if ($task == 'savenew') {

			// Get the current group category
			$categoryId 	= $group->category_id;

			return $this->redirect('index.php?option=com_easysocial&view=groups&layout=form&category_id=' . $categoryId);
		}
	}

	/**
	 * Displays the group creation form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form($errors = array())
	{
		// Perhaps this is an edited category
		$id = $this->input->get('id', 0, 'int');


		JToolbarHelper::apply('apply', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE'), false, false);
		JToolbarHelper::save('save', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE'));
		JToolbarHelper::save2new('savenew', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_NEW'));
		if ($id) {
			JToolbarHelper::save2copy('savecopy', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AS_COPY'));
		}
		JToolbarHelper::divider();
		JToolbarHelper::cancel('cancel', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL'));

		$group = FD::table('Group');
		$group->load($id);

		// Load front end's language file
		ES::language()->loadSite();

		// Get the category
		$categoryId = $this->input->get('category_id', 0, 'int');

		// Default heading and description
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_CREATE_GROUP');
		$this->setDescription('COM_EASYSOCIAL_TOOLBAR_TITLE_CREATE_GROUP_CATEGORY_DESC');

		// Set the structure heading here.
		if ($group->id) {
			$this->setHeading($group->get('title'));
			$this->setDescription('COM_EASYSOCIAL_TOOLBAR_TITLE_EDIT_GROUP_DESC');

			$categoryId = $group->category_id;
			$group = FD::group($id);
		} else {
			FD::import('admin:/includes/group/group');
			$group = new SocialGroup();
		}

		$category = FD::table('GroupCategory');
		$category->load($categoryId);

		// Get the steps
		$stepsModel = FD::model('Steps');
		$steps = $stepsModel->getSteps($categoryId, SOCIAL_TYPE_CLUSTERS);

		// Get the fields
		$lib = FD::fields();
		$fieldsModel = FD::model('Fields');

		$post = $this->input->getArray('post');
		$args = array(&$post, &$group, &$errors);

		foreach ($steps as &$step) {
			if ($group->id) {
				$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP));
			}
			else {
				$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id));
			}

			// @trigger onAdminEdit
			if (!empty($step->fields)) {
				$lib->trigger('onAdminEdit', SOCIAL_FIELDS_GROUP_GROUP, $step->fields, $args);
			}
		}

		$this->set('group', $group);
		$this->set('steps', $steps);
		$this->set('category', $category);

		$model = ES::model('GroupMembers', array('initState' => true));
		$members = $model->getItems(array('groupid' => $group->id));

		$pagination = $model->getPagination();

		$this->set('members', $members);
		$this->set('ordering', $model->getState('ordering'));
		$this->set('direction', $model->getState('direction'));
		$this->set('limit', $model->getState('limit'));
		$this->set('pagination', $pagination);

		$activeTab = JRequest::getWord('activeTab', 'profile');

		$this->set('activeTab', $activeTab);
		$this->set('isNew', empty($group->id));

		return parent::display('admin/groups/form.group');
	}

	/**
	 * Displays the category form for groups
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function categoryForm($tpl = null)
	{
		// Perhaps this is an edited category
		$id = $this->input->get('id', 0, 'int');

		$category = ES::table('GroupCategory');

		// By default the published state should be published.
		$category->state = SOCIAL_STATE_PUBLISHED;

		// If there's an id, try to load it
		$category->load($id);

		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_CREATE_GROUP_CATEGORY');
		$this->setDescription('COM_EASYSOCIAL_TOOLBAR_TITLE_CREATE_GROUP_CATEGORY_DESC');

		// Set the structure heading here.
		if ($category->id) {
			$this->setHeading($category->get('title'));
			$this->setDescription('COM_EASYSOCIAL_TOOLBAR_TITLE_EDIT_GROUP_CATEGORY_DESC');
		}

		// Load front end's language file
		ES::language()->loadSite();

		JToolbarHelper::apply('applyCategory', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE'), false, false);
		JToolbarHelper::save('saveCategory', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE'));
		JToolbarHelper::save2new('saveCategoryNew', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_NEW'));
		JToolbarHelper::divider();
		JToolbarHelper::cancel('cancel', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL'));

		$activeTab	= JRequest::getWord('activeTab', 'settings');
		$createAccess = '';

		// Set properties for the template.
		$this->set('activeTab', $activeTab);
		$this->set('category', $category);

		if ($category->id) {
			$options 		= array('type' => SOCIAL_APPS_TYPE_FIELDS, 'group' => SOCIAL_TYPE_GROUP, 'state' => SOCIAL_STATE_PUBLISHED);

			// Get the available custom fields for groups
			$appsModel		= FD::model('Apps');
			$defaultFields	= $appsModel->getApps($options);

			// Get the steps for this id
			$stepsModel		= FD::model('Steps');
			$steps			= $stepsModel->getSteps($category->id, SOCIAL_TYPE_CLUSTERS);

			// Get the fields for this id
			$fieldsModel	= FD::model('Fields');
			$fields 		= $fieldsModel->getCustomFields(array('uid' => $category->id, 'state' => 'all', 'group' => SOCIAL_TYPE_GROUP));

			// Empty array to pass to the trigger.
			$data			= array();

			// Get the fields sample output
			$lib 			= FD::fields();
			$lib->trigger('onSample', SOCIAL_TYPE_GROUP, $fields, $data, array($lib->getHandler(), 'getOutput'));

			// Create a temporary storage
			$tmpFields		= array();

			// Group the fields to each workflow properly
			if ($steps) {
				foreach ($steps as $step) {
					$step->fields = array();

					if (!empty($fields)) {
						foreach ($fields as $field) {
							if ($field->step_id == $step->id) {
								$step->fields[] = $field;
							}

							$tmpFields[ $field->app_id ]	= $field;
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

			// Get the creation access
			$createAccess = $category->getAccess('create');

			// We need to know if there are any core apps remain
			$coreAppsRemain = $usedCoreAppsCount < $coreAppsCount;

			// We need to know if there are any unique apps remain
			$uniqueAppsRemain = $usedUniqueAppsCount < $uniqueAppsCount;

			// Set the profiles allowed to create groups
			$this->set('createAccess', $createAccess);
			$this->set('coreAppsRemain', $coreAppsRemain);
			$this->set('uniqueAppsRemain', $uniqueAppsRemain);
			$this->set('defaultFields', $defaultFields);
			$this->set('steps', $steps);
			$this->set('fields', $fields);
			$this->set('fieldGroup'	, SOCIAL_FIELDS_GROUP_GROUP);

			// Render the access form.
			$accessModel = ES::model('Access');
			$accessForm = $accessModel->getForm($category->id, SOCIAL_TYPE_CLUSTERS, 'access');
			$this->set('accessForm'	, $accessForm);
		}

		// Set the profiles allowed to create groups
		$this->set('createAccess', $createAccess);

		echo parent::display('admin/groups/form.category');
	}

	/**
	 * Post processing after a category is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveCategory($category = null)
	{
		// Set the messages
		FD::info()->set($this->getMessage());

		$task 	= JRequest::getVar('task');

		if ($task == 'applyCategory') {
			return $this->redirect('index.php?option=com_easysocial&view=groups&layout=categoryForm&id=' . $category->id);
		}

		if ($task == 'saveCategoryNew') {
			return $this->redirect('index.php?option=com_easysocial&view=groups&layout=categoryForm');
		}

		return $this->redirect('index.php?option=com_easysocial&view=groups&layout=categories');
	}


	/**
	 * Post process after switching group owners
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function switchOwner()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=groups');
	}

	/**
	 * Post process after groups are rejected
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=groups&layout=pending');
	}

	/**
	 * Post process after groups are approved
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=groups&layout=pending');
	}


	/**
	 * Post process after groups are deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=groups');
	}

	/**
	 * Post process after a category is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteCategory()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=groups&layout=categories');
	}

	/**
	 * Post process after group has been toggled published.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublish()
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=groups');
	}

	/**
	 * Post process after categories has been toggled published.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublishCategory()
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=categories');
	}

	/**
	 * Post process after adding members into the group
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function addMembers()
	{
		FD::info()->set($this->getMessage());

		$groupid = JRequest::getInt('id');

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=form&activeTab=members&id=' . $groupid);
	}

	/**
	 * Post process after removing members from the group
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function removeMembers()
	{
		FD::info()->set($this->getMessage());

		$groupid = JRequest::getInt('id');

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=form&activeTab=members&layout=form&id=' . $groupid);
	}

	/**
	 * Post process after unpublich members from the group
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function unpublishUser()
	{
		FD::info()->set($this->getMessage());

		$groupid = JRequest::getInt('id');

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=form&activeTab=members&layout=form&id=' . $groupid);
	}

	/**
	 * Post process after publish members from the group
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function publishUser()
	{
		FD::info()->set($this->getMessage());

		$groupid = JRequest::getInt('id');

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=form&activeTab=members&layout=form&id=' . $groupid);
	}

	/**
	 * Post process after moving groups order
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function move($layout = null)
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=' . $layout . '&ordering=ordering');
	}

	/**
	 * Post process after promoting members to admin.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function promoteMembers()
	{
		FD::info()->set($this->getMessage());

		$groupid = JRequest::getInt('id');

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=form&activeTab=members&layout=form&id=' . $groupid);
	}

	/**
	 * Post process after demoting members as admin.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 */
	public function demoteMembers()
	{
		FD::info()->set($this->getMessage());

		$groupid = JRequest::getInt('id');

		$this->redirect('index.php?option=com_easysocial&view=groups&layout=form&activeTab=members&layout=form&id=' . $groupid);
	}

	/**
	 * Post process after a group is marked as featured
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toggleDefault()
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=groups');
	}

	public function switchCategory()
	{
		FD::info()->set($this->getMessage());

		$this->redirect('index.php?option=com_easysocial&view=groups');
	}
}
