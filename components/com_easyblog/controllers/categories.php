<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerCategories extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Saves a category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false);

		// Ensure that the user has access to create category
		if (!$this->acl->get('create_category') && !EB::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_CATEGORY', 'error');
			return $this->app->redirect($return);
		}

		// Possibility is that this category is being edited.
		$id = $this->input->get('id', 0, 'int');

		// Get the title of the category
		$title = $this->input->get('title', '', 'default');

		if (!$title) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY_CATEGORY_TITLE_ERROR', 'error');
			return $this->app->redirect($return);
		}

		$category = EB::table('Category');
		$category->load($id);

		// Default success message
		$message = 'COM_EASYBLOG_DASHBOARD_CATEGORIES_ADDED_SUCCESSFULLY';

		if ($category->id && $id) {
			$message = 'COM_EASYBLOG_DASHBOARD_CATEGORY_UPDATED_SUCCESSFULLY';
		}

		// Check whether the same category already exists on the site.
		$model = EB::model('Category');
		$exists = $model->isExist($title, $category->id);

		if ($exists) {
			$this->info->set(JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_ALREADY_EXISTS_ERROR', $title), 'error');
			return $this->app->redirect($return);
		}

		$post = $this->input->getArray('post');
		$post['title'] = $title;


		// if this is a edit category, we do not update the author
		if (! $category->id) {
			$post['created_by'] = $this->my->id;
		}

		$post['parent_id'] = $this->input->get('parent_id', 0, 'int');
		$post['private'] = $this->input->get('private', 0, 'int');
		$post['description'] = $this->input->get('description', '', 'raw');

		$category->bind($post);

		// Set the category as published by default.
		$category->published = true;

		// Assign default category params
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/category.json';
		$content = JFile::read($file);

		if ($content) {
			$params = array();

			$rawParams = json_decode($content);

			foreach($rawParams as $raw) {
				$params[$raw->name] = $raw->default;
			}

			$category->params = json_encode($params);
		}

		// Save the cat 1st so that the id get updated
		$category->store();

		// Delete all acl related to this category
		$category->deleteACL();


		if ($category->private == CATEGORY_PRIVACY_ACL) {
			$category->saveACL($post);
		}

		// Set a category avatar if required
		$file = $this->input->files->get('Filedata', '', 'array');

		if (isset($file['name']) && !empty($file['name'])) {
			$category->avatar = EB::uploadCategoryAvatar($category);
			$category->store();
		}

		$this->info->set(JText::sprintf($message, $category->getTitle()), 'success');
		return $this->app->redirect($return);
	}


	/**
	 * Deletes category from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		EB::checkToken();

		// Default redirection url
		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false);

		// Get the id
		$id = $this->input->get('id', 0, 'int');

		// Ensure that the user has access to delete this category
		if (!$this->acl->get('delete_category') && !EB::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_CATEGORY', 'error');
			return $this->app->redirect($redirect);
		}

		if (!$id) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_ID_IS_EMPTY_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		// Load up the category
		$category = EB::table('Category');
		$category->load($id);

		// Ensure that the category that is being deleted is owned by the user
		if ($category->created_by != $this->my->id && !EB::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');
			return $this->app->redirect($redirect);
		}

		// Ensure that there is no posts in this category
		if ($category->getPostCount() > 0) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_POST_NOT_EMPTY_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		// Ensure that this category is not a parent of another category
		if ($category->getChildCount() > 0) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_HAS_CHILD_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		// Try to delete the category now
		$state = $category->delete();

		if (!$state) {
			$this->info->set($category->getError(), 'error');
			return $this->app->redirect($redirect);
		}

		$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_DELETED_SUCCESSFULLY', 'success');
		return $this->app->redirect($redirect);
	}
}
