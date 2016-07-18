<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/controllers/controller');

class EasySocialControllerAccess extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('unpublish', 'publish');
	}

	public function remove()
	{
		FD::checkToken();

		$ids = FD::makeArray(JRequest::getVar('cid'));

		$view = $this->getCurrentView();

		if (empty($ids))
		{
			$view->setMessage(JText::_('COM_EASYSOCIAL_ACCESS_INVALID_ID_PROVIDED') , SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		foreach ($ids as $id)
		{
			$acc = FD::table('accessrules');
			$acc->load($id);

			$acc->delete();
		}

		$view->setMessage(JText::_('COM_EASYSOCIAL_ACCESS_DELETED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);
		return $view->call(__FUNCTION__);
	}

	public function publish()
	{
		FD::checkToken();

		$ids = FD::makeArray(JRequest::getVar('cid'));

		$view = $this->getCurrentView();

		$task 	= $this->getTask();

		if (empty($ids))
		{
			$view->setMessage(JText::_('COM_EASYSOCIAL_ACCESS_INVALID_ID_PROVIDED') , SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		foreach ($ids as $id)
		{
			$acc = FD::table('accessrules');
			$acc->load($id);

			$acc->$task();
		}

		$message = $task === 'publish' ? 'COM_EASYSOCIAL_ACCESS_PUBLISHED_SUCCESSFULLY' : 'COM_EASYSOCIAL_ACCESS_UNPUBLISHED_SUCCESSFULLY';

		$view->setMessage(JText::_($message), SOCIAL_MSG_SUCCESS);
		return $view->call(__FUNCTION__);
	}

	/**
	 * Scan for access files on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function scanFiles()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get available paths
		$paths = $this->config->get('access.paths');

		$model = FD::model('accessrules');

		$files = array();

		foreach ($paths as $path) {
			$result = $model->scan($path);

			$files = array_merge($files, $result);
		}

		return $this->view->call(__FUNCTION__, $files);
	}

	/**
	 * Install access rule files
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function installFile()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the file from request
		$file = $this->input->get('file', '', 'default');

		if (!$file) {
			return JError::raiseError(500, JText::_('Invalid file path given to scan for access files.'));
		}

		// Load up the model so that we can install the new rule
		$model = ES::model('AccessRules');

		// Try to install the rule now.
		$rules = $model->install($file);
		
		$obj = (object) array(
			'file' => str_ireplace(JPATH_ROOT, '', $file),
			'rules' => $rules
		);

		return $this->view->call(__FUNCTION__, $obj);
	}

	public function upload()
	{
		$file = JRequest::getVar('package', '', 'FILES');

		$state = $this->installPackage( $file, 'accessrules', array('zip', 'access' ), true );

		$view = $this->getCurrentView();

		return $view->call(__FUNCTION__);
	}
}
