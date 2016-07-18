<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class EasyDiscussControllerPost_types extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.posttypes');
		$this->registerTask('add', 'edit');
		$this->registerTask('publish', 'unpublish');
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('apply', 'save');
		$this->registerTask('savepublishnew', 'save');
	}

	public function edit()
	{
		$this->app->redirect('index.php?option=com_easydiscuss&view=types&layout=form');
	}

	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=types');
	}

	public function unpublish()
	{
		ED::checkToken();
		$postTypes = ED::table('Post_types');
		$ids = $this->input->get('cid', '', 'var');
		$state = $this->input->get('task', '', 'var') == 'publish' ? 1 : 0;

		foreach ($ids as $id) {
			$id	= (int) $id;
			$postTypes->load($id);
			$postTypes->set('published', $state);
			$postTypes->store();
		}

		$message = $state ? JText::_('COM_EASYDISCUSS_POST_TYPES_PUBLISHED') : JText::_('COM_EASYDISCUSS_POST_TYPES_UNPUBLISHED');

		ED::setMessage($message, 'success');

		$this->app->redirect('index.php?option=com_easydiscuss&view=types');
	}

	public function apply()
	{
		$this->save();
	}

	public function save()
	{
		ED::checkToken();

		$post = $this->input->getArray('post');

		$id = $this->input->get('id', 0, 'int');
		$isNew = $id ? false : true;

		$postTypes = ED::table('Post_types');
		$postTypes->load($id);

		$oldTitle = $postTypes->title;

		// Binds the new data.
		$postTypes->bind($post);

		if (!$postTypes->created) {
			$postTypes->created = ED::date()->toSql();
		}

		if ($postTypes->title != $oldTitle || $oldTitle == '') {
			$postTypes->alias = ED::getAlias($postTypes->title, 'posttypes');
		}

		$postTypes->published = 1;

		if ($postTypes->store()) {
			//since we using the alias to join with discuss_posts.post_type, we need to update the value there as well.
			$postTypes->updateTopicPostType($oldTitle);
		}

		// Get the current task
		$task = $this->getTask();

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easydiscuss&view=types&layout=form&id=' . $postTypes->id;
		} else {
			$redirect = 'index.php?option=com_easydiscuss&view=types';
		}

		$message = !empty($postTypes->id) ? JText::_('COM_EASYDISCUSS_POST_TYPES_UPDATED') : JText::_('COM_EASYDISCUSS_POST_TYPES_CREATED');

		ED::setMessage($message, 'success');
		$this->app->redirect($redirect);
		$this->app->close();
	}

	/**
	 * Allows deletion of post types
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the categories
		$ids = $this->input->get('cid', '', 'default');

		$redirect = 'index.php?option=com_easydiscuss&view=types';

		foreach ($ids as $id) {
			$id = (int) $id;

			$types = ED::table('post_types');

			// Try to delete the category
			$state = $types->delete($id);

			if (!$state) {
				ED::setMessage($types->getError(), 'error');
				return $this->app->redirect($redirect);
			}
		}

		ED::setMessage('COM_EASYDISCUSS_CATEGORIES_DELETE_SUCCESS', 'success');

		return $this->app->redirect($redirect);
	}
}
