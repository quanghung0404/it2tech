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

class EasyBlogControllerCategory extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		// Check for acl rules.
		$this->checkAccess('category');

		$this->registerTask('apply', 'save');
		$this->registerTask('save', 'save');
		$this->registerTask('savenew', 'save');

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'publish' , 'publish' );

		// In Joomla 3.0, it seems like we need to explicitly set unpublish
		$this->registerTask( 'unpublish' , 'unpublish' );
		$this->registerTask( 'orderup' , 'orderup' );
		$this->registerTask( 'orderdown' , 'orderdown' );
	}

	public function orderdown()
	{
		// Check for request forgeries
		EB::checkToken();

	    $this->orderCategory(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		EB::checkToken();

		$this->orderCategory(-1);
	}

	public function orderCategory( $direction )
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

		$mainframe  = JFactory::getApplication();

		// Initialize variables
		$db		= EasyBlogHelper::db();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] ))
		{
			$row = EB::table('Category');
			$row->load( (int) $cid[0] );

			$row->move($direction);
		}

		$mainframe->redirect( 'index.php?option=com_easyblog&view=categories');
		exit;
	}

	public function saveOrder()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess( 'category' );

	    $mainframe  = JFactory::getApplication();

		$row = EB::table('Category');
		$row->rebuildOrdering();

		//now we need to update the ordering.
		$row->updateOrdering();

		$message	= JText::_('COM_EASYBLOG_CATEGORIES_ORDERING_SAVED');
		EB::info()->set($message, 'success');

		$mainframe->redirect( 'index.php?option=com_easyblog&view=categories');
		exit;
	}

	/**
	 * Saves a category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get posted data
		$post = $this->input->getArray('post');
		$task = $this->getTask();

		// Get the category id
		$id = $this->input->get('id', '', 'int');
		$category = EB::table('Category');
		$category->load($id);

		// Determines if this is a new category
		$isNew = $category->id ? false : true;

		// Bind the posted data
		$category->bind($post);

		// Construct the redirection url
		$url = 'index.php?option=com_easyblog&view=categories&layout=form';

		if ($category->id) {
			$url .= '&id=' . $id;
		}

		if (!$category->title) {
			EB::info()->set(JText::_('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY'), 'error');
			return $this->app->redirect($url);
		}

		if (!$category->isNotAssigned() && $category->isDefault()) {
			EB::info()->set(JText::_('COM_EASYBLOG_CATEGORIES_SAVE_NOT_PUBLIC'), 'error');
			return $this->app->redirect($url);
		}

		if (!$category->created_by && empty($category->created_by)) {
			$category->created_by = $this->my->id;
		}

		// Get the description for the category
		$category->description = $this->input->get('description', '', 'raw');

		// Process the params
		$raw = $this->input->get('params', '', 'array');

		// var_dump($raw);exit;

		$category->params = json_encode($raw);

		// Try to save the category now
		$state 	= $category->store();

		if (!$state) {
			EB::info()->set($category->getError(), 'error');

			return $this->app->redirect($url);
		}

		// Bind the category with the custom fields
		$fieldGroup = $this->input->get('field_group', '', 'int');

		if ($fieldGroup) {
			$category->bindCustomFieldGroup($fieldGroup);
		} else {
			$category->removeFieldGroup();
		}

		// Once the category is saved, delete existing acls
		$category->deleteACL();

		if ($category->private == CATEGORY_PRIVACY_ACL) {
			$category->saveACL($post);
		}

		// Set the meta for the category
		$category->createMeta();


		// Process category avatars
		$file 	= $this->input->files->get('Filedata', '', 'avatar');

		if (isset($file['tmp_name']) && !empty($file['name'])) {

			$avatar 	= EB::uploadCategoryAvatar($category, true);
			$category->avatar 	= $avatar;

			$category->store();
		}

		$message 	= JText::_('COM_EASYBLOG_CATEGORIES_SAVED_SUCCESS');

		EB::info()->set($message, 'success');

		if ($task == 'savenew') {
			return $this->app->redirect('index.php?option=com_easyblog&view=categories&layout=form');
		}

		if ($task == 'apply') {
			return $this->app->redirect('index.php?option=com_easyblog&view=categories&layout=form&id=' . $category->id);
		}


		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	/**
	 * Removes a category from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('category');

		// Get the list of id's
		$ids = $this->input->get('cid', array(), 'array');

		$return = 'index.php?option=com_easyblog&view=categories';

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$category = EB::table('Category');
			$category->load($id);

			// Try to delete the category now.
			$state = $category->delete();

			if (!$state) {
				$this->info->set($category->getError(), 'error');
				return $this->app->redirect($return);
			}
		}

		$this->info->set('COM_EASYBLOG_CATEGORIES_DELETE_SUCCESS', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Publishes a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('category');

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		// Get the model
		$model = EB::model('Categories');
		$state = $model->publish($ids, 1);

		$this->info->set('COM_EASYBLOG_CATEGORIES_PUBLISHED_SUCCESS', 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	/**
	 * Unpublish category from the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('category');

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		// Get the model
		$model = EB::model('Categories');
		$state = $model->publish($ids, 0);

		$this->info->set('COM_EASYBLOG_CATEGORIES_UNPUBLISHED_SUCCESS', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	/**
	 * Toggles a category as the default category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function makeDefault()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the category id
		$id = $this->input->get('cid', array(), 'array()');

		if (!$id) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		// Check for acl rules.
		$this->checkAccess('category');

		// Since the id is an array, we only want the first item
		$id = (int) $id[0];

		// Set the current category as default
		$category = EB::table('Category');
		$category->load($id);

		// If the category is not public, don't set it as default
		if (!$category->isNotAssigned()) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_NOT_PUBLIC','error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		// If the category is not published, don't set it as default.
		if (!$category->published) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_NOT_PUBLISHED','error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}	

		$category->setDefault();

		$this->info->set('COM_EASYBLOG_CATEGORIES_MARKED_AS_DEFAULT', 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}
}
