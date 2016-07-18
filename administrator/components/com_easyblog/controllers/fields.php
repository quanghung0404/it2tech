<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerFields extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Toggle publishing for fields
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		// Toggle required for fields
		$this->registerTask('setRequired', 'toggleRequire');
		$this->registerTask('removeRequired', 'toggleRequire');

		$this->registerTask('apply', 'save');
		$this->registerTask('savenew', 'save');

		$this->registerTask('publishgroup', 'togglePublishGroup');
		$this->registerTask('unpublishgroup', 'togglePublishGroup');

		$this->registerTask('applyGroup', 'saveGroup');
		$this->registerTask('saveNewGroup', 'saveGroup');
	}


	/**
	 * Toggles publishing for custom fields
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toggleRequire()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the list of id's
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_FIELDS_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=fields');
		}

		$task = $this->getTask();

		foreach ($ids as $id) {
			$field = EB::table('Field');
			$field->load((int) $id);

			// Unpublish or publish the field
			$field->$task();
		}

		$message = 'COM_EASYBLOG_FIELDS_SET_REQUIRED_SUCCESSFULLY';

		if ($task == 'removeRequired') {
			$message = 'COM_EASYBLOG_FIELDS_UNSET_REQUIRED_SUCCESSFULLY';
		}

		$this->info->set(JText::_($message), 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=fields');
	}


	/**
	 * Toggles publishing for custom fields
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the list of id's
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_FIELDS_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=fields');
		}

		$task = $this->getTask();

		foreach ($ids as $id) {
			$field = EB::table('Field');
			$field->load((int) $id);

			// Unpublish or publish the field
			$field->$task();
		}

		$message = 'COM_EASYBLOG_FIELDS_PUBLISHED_SUCCESSFULLY';

		if ($task == 'unpublish') {
			$message = 'COM_EASYBLOG_FIELDS_UNPUBLISHED_SUCCESSFULLY';
		}

		$this->info->set(JText::_($message), 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=fields');
	}

	/**
	 * Deletes custom fields from the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the ids
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_FIELDS_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=fields');
		}

		foreach ($ids as $id) {
			$field = EB::table('Field');
			$field->load($id);

			$field->delete();
		}

		$this->info->set(JText::_('COM_EASYBLOG_FIELDS_DELETED_SUCCESSFULLY'), 'success');
		$this->app->redirect('index.php?option=com_easyblog&view=fields');
	}

	/**
	 * Toggles publishing of field group
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublishGroup()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the group ids
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_FIELDS_GROUP_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groups');
		}

		$task = $this->getTask();
		$method = $task == 'publishgroup' ? 'publish' : 'unpublish';

		foreach ($ids as $id) {

			$group = EB::table('FieldGroup');
			$group->load((int) $id);

			$group->$method();
		}

		$message = 'COM_EASYBLOG_FIELDS_GROUP_PUBLISHED_SUCCESSFULLY';

		if ($task == 'unpublishGroup') {
			$message = 'COM_EASYBLOG_FIELDS_GROUP_UNPUBLISHED_SUCCESSFULLY';
		}

		$this->info->set(JText::_($message), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groups');
	}

	/**
	 * Saves a new field group
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveGroup()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the title
		$title 	= $this->input->get('title', '', 'default');
		$read 	= $this->input->get('read', '', 'default');
		$write 	= $this->input->get('write', '', 'default');
		$state 	= $this->input->get('state', '', 'boolean');

		// User could be editing the group
		$id = $this->input->get('id', 0, 'int');

		// validation here
		if (! $title) {
			EB::info()->set(JText::_('COM_EASYBLOG_FIELDS_GROUP_TITLE_EMPTY_WARNING'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groupForm&id=' . $id);
		}

		$group 	= EB::table('FieldGroup');
		$group->load($id);

		$group->title 	= $title;
		$group->read 	= json_encode($read);
		$group->write 	= json_encode($write);
		$group->state 	= $state;

		// Save the group
		$state = $group->store();

		if (!$state) {
			EB::info()->set($group->getError(), 'error');
		} else {
			EB::info()->set(JText::_('COM_EASYBLOG_FIELDS_GROUP_SAVED_SUCCESS'), 'success');
		}

		$task = $this->getTask();

		if ($task == 'applyGroup') {
			return $this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groupForm&id=' . $group->id);
		}

		if ($task == 'saveNewGroup') {
			return $this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groupForm');
		}

		$this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groups');
	}

	/**
	 * Deletes a custom field group
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeGroup()
	{
		// Check for request forgeries
		EB::checkToken();

		$ids = $this->input->get('cid', '', 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_FIELDS_GROUP_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groups');
		}

		foreach ($ids as $id) {

			// Load the table layer
			$group = EB::table('FieldGroup');
			$group->load((int) $id);

			// Delete the field group
			$group->delete();
		}

		$this->info->set(JText::_('COM_EASYBLOG_FIELDS_GROUP_DELETED_SUCCESSFULLY'), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groups');
	}

	/**
	 * Saves a custom field
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get posted data
		// $options = array('group_id' => 'int', 'title' => 'string', 'help' => 'string', 'state' => 'boolean', 'required' => 'boolean', 'type' => 'cmd');
		// $post = $this->input->getArray($options);

		$post = $this->input->getArray('post');

		// Determines if we should load existing field
		$id = $this->input->get('id', '', 'int');

		// Get the field
		$field = EB::table('Field');
		$field->load($id);

		$task = $this->getTask();

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=fields';

		if ($task == 'savenew') {
			$redirect = 'index.php?option=com_easyblog&view=fields&layout=form';
		}

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easyblog&view=fields&layout=form&id=' . $field->id;
		}

		// Bind the posted data
		$field->bind($post);

		// Throw errors if we hit error
		if (!$field->check()) {
			$this->info->set($field->getError(), 'error');

			$redirect = 'index.php?option=com_easyblog&view=fields&layout=form';
			return $this->app->redirect($redirect);
		}

		// If there is no group id, do not allow saving
		if (!$field->group_id) {
			$this->info->set(JText::_('COM_EASYBLOG_FIELDS_SAVE_ERROR_NO_GROUP_SELECTED'), 'error');
			return $this->app->redirect($redirect);
		}

		// Set the creation date
		$field->created = EB::date()->toSql();

		// Get field options
		$optionsTitle = $this->input->get('field_options_title', array(), 'array');
		$optionsValue = $this->input->get('field_options_values', array(), 'array');

		$total = count($optionsTitle);
		$options = array();

		if ($total > 0) {
			for ($i = 0; $i < $total; $i++) {

				$option = new stdClass();
				$option->title = $optionsTitle[$i];
				$option->value = $optionsValue[$i];

				if (empty($option->title)) {
					continue;
				}

				$option->value = trim($option->value);

				if (! $option->value) {
					// if value is empty, let take title as value.
					$option->value = $option->title;
				}

				$options[]	= $option;
			}

			$field->options = json_encode($options);
		}

		// Get field params
		$params = $this->input->get('params', array(), 'array');

		$field->params = json_encode($params);

		$state = $field->store();
		$message = JText::_('COM_EASYBLOG_FIELDS_FIELD_SAVED_SUCCESS');

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easyblog&view=fields&layout=form&id=' . $field->id;
		}


		if (!$state) {
			$message = JText::_('COM_EASYBLOG_FIELDS_FIELD_SAVED_ERROR');
		}

		$this->info->set($message, $state ? 'success' : 'error');

		$this->app->redirect($redirect);
	}
}
