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

class EasyBlogControllerModerate extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Approves a blog post that is currently in moderation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function approve()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that user is logged in
		EB::requireLogin();

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Check if the user is privileged enough
		if (!$this->acl->get('add_entry') && !$this->acl->get('manage_pending') ) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));
		}

		// Load the draft
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$post = EB::post($id);
			$post->approve();
		}

		// Set the success message / notice.
		$message = JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_SAVED');

		EB::info()->set(JText::_('COM_EASYBLOG_MODERATE_BLOG_POSTS_APPROVED_SUCCESSFULLY'), 'success');

		return $this->app->redirect($return);
	}

	/*
	 * Responsible to reject a blog post
	 *
	 * @param	null
	 */
	public function reject()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that user is logged in
		EB::requireLogin();

		// Get any return url
		$return 	= EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');

		if ($this->getReturnURL()) {
			$return 	= $this->getReturnURL();
		}

		// Check if the user is privileged enough
		if (!$this->acl->get('add_entry') && !$this->acl->get('manage_pending') ) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));
		}

		// Get a list of ids
		$ids = $this->input->get('ids', array(), 'array');
		$message = $this->input->get('message', '', 'default');

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->reject($message);
		}

		$message = JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_REJECTED');

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}
}
