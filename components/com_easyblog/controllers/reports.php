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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerReports extends EasyBlogController
{
	/**
	 * Allows caller to submit a report
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function submit()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the composite keys
		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', '', 'cmd');

		// Initialize redirection link
		$redirect = EB::_( 'index.php?option=com_easyblog&view=entry&id=' . $id, false);

		// Check if guest is allowed to report or not.
		if ($this->my->guest && !$this->config->get('main_reporting_guests')) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_FOR_REGISTERED_USERS_ONLY', 'error');

			return $this->app->redirect($redirect);
		}

		// Ensure that the report reason is not empty.
		$reason = $this->input->get('reason', '', 'default');

		if (!$reason) {
			EB::info()->set(JText::_('COM_EASYBLOG_REPORT_PLEASE_SPECIFY_REASON'), 'error');

			return $this->app->redirect($redirect);
		}

		$report = EB::table('Report');
		$report->obj_id = $id;
		$report->obj_type = $type;
		$report->reason = $reason;
		$report->created = EB::date()->toSql();
		$report->created_by = $this->my->id;
		$report->ip = @$_SERVER['REMOTE_ADDR'];

		$state = $report->store();

		if (!$state) {
			$this->info->set($report->getError());

			return $this->app->redirect($redirect);
		}

		// Notify the site admin when there's a new report made
		$post = EB::post($id);

		$report->notify($post);
		
		$message = JText::_('COM_EASYBLOG_THANKS_FOR_REPORTING');
		
		$this->info->set($message, 'success');
		return $this->app->redirect($redirect);
	}

}