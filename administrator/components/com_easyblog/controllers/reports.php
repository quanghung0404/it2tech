<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerReports extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Deletes a reported blog post
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

		// Get the report id
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			return;
		}

		foreach ($ids as $id) {

			$id = (int) $id;

			$report = EB::table('Report');
			$report->load($id);

			$blog = EB::table('Blog');
			$blog->load($report->obj_id);
			$blog->delete();

			// Once the blog post is unpublished, delete the report since action was already performed.
			$report->delete();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=reports');
	}

	/**
	 * Unpublishes a blog post
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

		// Get the report id
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			return;
		}

		// Get the blogs model
		$model = EB::model('Blogs');

		foreach ($ids as $id) {

			$id = (int) $id;

			$report = EB::table('Report');
			$report->load($id);

			$args	= array(&$report->obj_id);
			$model->publish($args, 0);

			// Once the blog post is unpublished, delete the report since action was already performed.
			$report->delete();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_UNPUBLISHED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=reports');
	}

	/**
	 * Allow caller to discard reports
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function discard()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('report');

		// Get a list of report ids.
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$report = EB::table('Report');
			$report->load((int) $id);

			$report->delete();
		}

		$message 	= JText::_('COM_EASYBLOG_REPORTS_DISCARDED_SUCCESSFULLY');
		$this->info->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=reports');
	}
}
