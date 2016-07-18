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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerPending extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Removes a pending blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the list of ids to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			// Do something
			exit;
		}

		foreach ($ids as $id) {

			$post = EB::post((int) $id);
			$post->delete();
		}

		$message 	= JText::_('COM_EASYBLOG_PENDING_POSTS_DELETED_SUCCESSFULLY');

		EB::info()->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs&layout=pending');
	}

	/**
	 * Rejects a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function reject()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl
		$this->checkAccess('pending');

		// Get a list of ids
		$ids = $this->input->get('cid', array(), 'array');
		$message = $this->input->get('message', '', 'default');

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->reject($message);
		}

		$message = JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_REJECTED');

		$this->info->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs&layout=pending');
	}

	/**
	 * Approves a blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function approve()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl
		$this->checkAccess('pending');

		// Get a list of id's to approve
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$post = EB::post($id);
			$post->approve();
		}

		$message = JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_APPROVED');

		$this->info->set($message, 'success');
		$this->app->redirect('index.php?option=com_easyblog&view=blogs&layout=pending');
	}

}
