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

class EasyBlogControllerBlocks extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Toggles the publishing state of a block
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

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=blocks';

		// Get the items to be published / unpublished
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_BLOCKS_INVALID_ID_PROVIDED', 'error');
			return $this->app->redirect($redirect);
		}

		// Get the current task
		$task = $this->getTask();

		foreach ($ids as $id) {
			$block = EB::table('Block');
			$block->load((int) $id);

			$block->$task();
		}

		$message = 'COM_EASYBLOG_BLOCKS_PUBLISHED_SUCCESSFULLY';

		if ($task == 'unpublish') {
			$message = 'COM_EASYBLOG_BLOCKS_UNPUBLISHED_SUCCESSFULLY';
		}

		$this->info->set(JText::_($message));
		return $this->app->redirect($redirect);
	}
}
