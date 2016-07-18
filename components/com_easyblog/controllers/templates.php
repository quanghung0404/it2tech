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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerTemplates extends EasyBlogController
{
	/**
	 * Deletes a list of post templates
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

		$ids = $this->input->get('ids', array(), 'array');

		$redirect = EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates', false);

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATES_INVALID_ID'), 'error');
			return $this->app->redirect($redirect);
		}

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load((int) $id);

			// Ensure that the user has access to delete this
			if ($template->user_id == $this->my->id || EB::isSiteAdmin()) {
				$template->delete();
			}

		}

		if ($this->doc->getType() != 'ajax') {
			$this->info->set('COM_EASYBLOG_DASHBOARD_TEMPLATES_DELETED_SUCCESS', 'success');

			return $this->app->redirect($redirect);
		}


		// For ajax calls, we shouldn't do anything
		return $this->ajax->resolve();
	}

	/**
	 * save post templates
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

		$id = $this->input->get('id', '', 'int');

		$template = EB::table('PostTemplate');
		$template->load($id);


		$title = $this->input->get('title', '', 'default');
		$content = $this->input->get('template_content', '', 'raw');

		$data['content'] = $content;

		$template->title = $title;
		$template->data = json_encode($data);
		$template->user_id = $this->my->id;
		$template->created = EB::date()->toSql();

		$template->store();

		$this->info->set('COM_EASYBLOG_DASHBOARD_TEMPLATES_SAVED_SUCCESS', 'success');

		$redirect = EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates', false);
		return $this->app->redirect($redirect);
	}
}
