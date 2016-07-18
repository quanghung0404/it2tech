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

ES::import('admin:/controllers/controller');

class EasySocialControllerVideoCategories extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		// Register task aliases here.
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');

		$this->registerTask('publish', 'publish');
		$this->registerTask('unpublish', 'unpublish');
	}

	/**
	 * Sets a video category as a default video category
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function toggleDefault()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the list of ids here
		$ids = $this->input->get('cid', array(), 'array');
		$id = $ids[0];

		$table = ES::table('VideoCategory');
		$table->load($id);

		// Set the record as the default video category
		$table->setDefault();

		$this->view->setMessage('COM_EASYSOCIAL_VIDEOS_CATEGORIES_SET_DEFAULT_SUCCESS', SOCIAL_MSG_SUCCESS);
		$this->view->call(__FUNCTION__, $table);
	}

	/**
	 * Deletes a category
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function delete()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the list of ids here
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$category = ES::table('VideoCategory');
			$category->load($id);

			$category->delete();
		}

		$this->view->setMessage('COM_EASYSOCIAL_VIDEOS_CATEGORIES_DELETED_SUCCESS', SOCIAL_MSG_SUCCESS);
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Publishes a category
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function publish()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the list of ids here
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$category = ES::table('VideoCategory');
			$category->load($id);

			$category->publish();
		}

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_VIDEOS_CATEGORIES_PUBLISHED_SUCCESS'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Unpublishes video categories
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function unpublish()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the list of ids here
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$category = ES::table('VideoCategory');
			$category->load($id);

			$category->unpublish();
		}

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_VIDEOS_CATEGORIES_UNPUBLISHED_SUCCESS'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Saves a new video category
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function save()
	{
		// Check for request forgeries
		ES::checkToken();

		// Perhaps the user is editing a video category
		$id = $this->input->get('id', 0, 'int');

		// Get the category
		$category = ES::table('VideoCategory');
		$category->load($id);

		$category->title = $this->input->get('title', '', 'default');
		$category->alias = $this->input->get('alias', '', 'default');
		$category->description = $this->input->get('description', '', 'default');
		$category->state = $this->input->get('state', true, 'bool');
		$category->user_id = $this->my->id;

		$state = $category->store();

		// Bind video category access
		if ($state) {
	        $categoryAccess = $this->input->get('create_access', '', 'default');
	        $category->bindCategoryAccess('create', $categoryAccess);
		}
		
		$task = $this->getTask();

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_VIDEOS_CATEGORY_SAVED_SUCCESS'), SOCIAL_MSG_SUCCESS);
		$this->view->call($task, $category);
	}
}
