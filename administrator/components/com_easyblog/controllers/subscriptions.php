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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerSubscriptions extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Create new subscribers on the site
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

		$name  = $this->input->get('name', '', 'default');
		$email = $this->input->get('email', '', 'email');

		if (!$name) {
			EB::info()->set(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_PLEASE_ENTER_NAME'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		if (!$email) {
			EB::info()->set(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_PLEASE_ENTER_EMAIL'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		// Get the model from the site
		$model 	= EB::model('Subscription');
		$state  = $model->addSiteSubscription($email, '', $name);


		EB::info()->set(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_ADDED_SUCCESS'), 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
	}

	/**
	 * Removes a subscriber from the list
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess( 'subscription' );

		$ids = $this->input->get('cid', array(), 'array');
		$filter = $this->input->get('filter', '', 'cmd');

		if (!$filter) {
			$this->info->set('COM_EASYBLOG_ERROR_REMOVING_SUBSCRIPTION_MISSING_SUBSCRIPTION_TYPE', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		if (!$filter) {
			$this->info->set('COM_EASYBLOG_INVALID_SUBSCRIPTION_ID', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		foreach ($ids as $id) {

			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$table = EB::table('Subscriptions');
			$table->load((int) $id);
			$table->delete();
		}

		$this->info->set('COM_EASYBLOG_SUBSCRIPTION_DELETED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
	}

	/**
	 * Allow users to import csv files into subscriptions table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function importFile()
	{
		// Check for request forgeries
		EB::checkToken();

		$file = $this->input->files->get('package');
		// $file	= JRequest::getVar( 'package', '', 'files', 'array' );

		$model = EB::model('Subscription');

		// Check if the file exists
		if (!$file || !isset($file['tmp_name']) || empty($file['tmp_name'])) {

			EB::info()->set('COM_EASYBLOG_SUBSCRIPTION_IMPORT_FILE_NOT_EXIST', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		//the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $file['tmp_name'];
		$fileName = $file['name'];

		//always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$uploadPath = JPATH_ROOT . '/tmp/' . $fileName;
		$result = $model->massAssignSubscriber($fileTemp);

		if($result){
			// Redirect user back
			EB::info()->set(JText::sprintf('COM_EASYBLOG_SUBSCRIPTION_IMPORT_ADDED', count($result)), 'success');

			// $this->app 	= JFactory::getApplication();
			$this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}
		else
		{
			EB::info()->set('COM_EASYBLOG_SUBSCRIPTION_IMPORT_NOONE_ADDED', 'success');
			$this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

	}
}
