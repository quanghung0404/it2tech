<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewReports extends EasyBlogAdminView
{
	/**
	 * Dialog for unpublish confirmation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnpublish()
	{
		// Get the report id
		$id = $this->input->get('id', 0, 'int');

		$template = EB::template();

		$template->set('id', $id);

		$output = $template->output('admin/reports/dialog.unpublish');

		$this->ajax->resolve($output);
	}

	/**
	 * Dialog for delete confirmation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		// Get the report id
		$id = $this->input->get('id', 0, 'int');

		$template = EB::template();

		$template->set('id', $id);

		$output = $template->output('admin/reports/dialog.delete');

		$this->ajax->resolve($output);
	}
}
