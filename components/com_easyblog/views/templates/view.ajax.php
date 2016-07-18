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

require_once(EBLOG_ROOT . '/views/views.php');

class EasyBlogViewTemplates extends EasyBlogView
{
	/**
	 * Displays a dialog to request user to set the title of the template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveTemplateDialog()
	{
		$theme = EB::template();

		$output = $theme->output('site/dashboard/templates/dialog.save');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays a delete confirmation
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject('COM_EASYBLOG_INVALID_TEMPLATE_ID_PROVIDED');
		}

		$postTemplate = EB::table('PostTemplate');
		$postTemplate->load($id);

		if ($postTemplate->isBlank()) {
			return $this->ajax->reject('COM_EASYBLOG_YOU_ARE_NOT_ALLOWED_TO_DELETE_BLANK_TEMPLATE');
		}

		// This determines if the action should be managed by the template file or the caller will handler the delete button
		$deleteAction = $this->input->get('deleteAction', 'submit', 'word');

		$ids = array($id);

		$theme = EB::template();
		$theme->set('ids', $ids);
		$theme->set('deleteAction', $deleteAction);

		$output = $theme->output('site/dashboard/templates/dialog.delete');


		return $this->ajax->resolve($output);
	}

	/**
	 * Lists down blog templates created by the author.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listTemplates()
	{
		$model = EB::model('Blog');
		$result = $model->getTemplates($this->my->id);
		$templates = array();

		if ($result) {
			foreach ($result as $table) {
				$templates[] = $table->export();
			}
		}

		return $this->ajax->resolve($templates);
	}

	/**
	 * Saves a blog post template
	 *
	 * @since	4.0
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

		// We want to get the document data
		$document = $this->input->get('document', '', 'raw');
		$title = $this->input->get('template_title', '', 'default');

		// If the caller passes us an id, we are assuming that they want to update the template
		$templateId = $this->input->get('template_id', 0, 'int');

		$postTemplate = EB::table('PostTemplate');

		// Default success message
		$message = 'COM_EASYBLOG_BLOG_TEMPLATE_SAVED_SUCCESS';

		if ($templateId) {
			$postTemplate->load($templateId);
			$message = 'COM_EASYBLOG_BLOG_TEMPLATE_UPDATE_SUCCESS';
		} else {
			$postTemplate->title = $title;
			$postTemplate->user_id = $this->my->id;
			$postTemplate->created = EB::date()->toSql();
			$postTemplate->system = $this->input->get('system', 0, 'int');
		}

		$postTemplate->data = $document;
		$postTemplate->store();

		// Create an exportable object
		$export = $postTemplate->export();



		return $this->ajax->resolve(EB::exception($message, EASYBLOG_MSG_SUCCESS), $export);
	}


	/**
	 * Displays a confirmation dialog to delete a template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDeleteTemplate()
	{
		$ids = $this->input->get('ids', array(), 'array');

		if (!$ids) {
			return $this->ajax->reject();
		}

		$theme = EB::template();

		$theme->set('ids', $ids);

		$output = $theme->output('site/dashboard/templates/dialog.delete');

		return $this->ajax->resolve($output);
	}
}
