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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerTags extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Allows caller to create a new tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags', false);

		// Ensure that the user has access to create tags
		if (!$this->acl->get('create_tag') && !EB::isSiteAdmin()) {

			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TAG', 'error');
			return $this->app->redirect($return);
		}

		// Get the tags list
		$tags = $this->input->get('tags', '', 'default');

		if (!$tags) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_TAG_INVALID', 'error');

			return $this->app->redirect($return);
		}

		// Since it could be comma separated, we need to match it.
		$tags = explode(',', $tags);

		foreach ($tags as $tag) {

			$tag = JString::trim($tag);

			if (empty($tag)) {
				continue;
			}

			$table = EB::table('Tag');
			$exists = $table->exists($tag);

			if (!$exists) {
				$table->title = $tag;
				$table->created_by = $this->my->id;
				$table->published = EASYBLOG_POST_PUBLISHED;

				$table->store();
			}
		}

		$this->info->set('COM_EASYBLOG_DASHBOARD_TAGS_CREATED_SUCCESSFULLY', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Deletes a tag from the site
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

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags', false);

		// Ensure that the user has access to create tags
		if (!$this->acl->get('create_tag') && !EB::isSiteAdmin()) {

			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TAG', 'error');
			return $this->app->redirect($return);
		}

		// Ensure that the tag exists
		$id = $this->input->get('id', 0, 'int');
		$tag = EB::table('Tag');
		$tag->load($id);

		if (!$id || !$tag->id) {
			$this->info->set('COM_EASYBLOG_TAG_INVALID_ID', 'error');
			return $this->app->redirect($return);
		}

		// Ensure that the user owns this tag
		if ($tag->created_by != $this->my->id && !EB::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_TAG', 'error');
			return $this->app->redirect($return);
		}

		$state = $tag->delete();

		if (!$state) {
			$this->info->set($tag->getError(), 'error');
			return $this->app->redirect($return);
		}

		$this->info->set('COM_EASYBLOG_TAG_DELETED', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Search tags
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function query()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the query
		$search 	= $this->input->get('filter-tags', '', 'string');

		$url 	= EB::_('index.php?option=com_easyblog&view=tags&search=' . $search, false);


		$this->app->redirect($url);
	}

}
