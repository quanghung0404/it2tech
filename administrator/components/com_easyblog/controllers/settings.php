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

class EasyBlogControllerSettings extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Saves the settings
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('setting');

		// Get the settings model
		$model = EB::model('Settings');

		// Get the post data from the form
		$post = $this->input->getArray('post');
		$data = array();

		$activeTab = $this->input->get('activeTab', '', 'default');

		// Get the current layout
		$page = $this->input->get('page', '', 'cmd');

		// Clean the input
		unset($post['task']);
		unset($post['option']);
		unset($post['page']);


		foreach ($post as $key => $value) {

			if (is_array($value)) {
				$value 	= implode('|', $value);
			}

			// If this is a google adsense settings, make sure it's formatted correctly.
			if ($key == 'integration_google_adsense_code') {
				$value 	= str_ireplace(';"', '', $value);
			}

			if ($key == 'integration_google_adsense_responsive_code') {
				$value = $this->input->get($key, '', 'raw');
			}

			$data[$key]	= $value;
		}

		if (!isset($post['cover_width_full']) && $page == 'layout') {
			$data['cover_width_full'] = 0;
		}

		if (!isset($post['cover_width_entry_full']) && $page == 'layout') {
			$data['cover_width_entry_full'] = 0;
		}

		// If there's a settings change for EasySocial's privacy, update all the blog post accordingly.
		if (isset($data['integrations_easysocial_privacy']) && $data['integrations_easysocial_privacy']) {
			$model->updateBlogPrivacy(10);
		}

		// If there's a settings change for EasySocial's privacy, update all the blog post accordingly.
		if (isset($data['main_jomsocial_privacy']) && $data['main_jomsocial_privacy']) {
			$model->updateBlogPrivacy(20);
		}

		// Fix the blog description to allow raw html codes
		if (isset($data['main_description'])) {
			$data['main_description']	= JRequest::getVar( 'main_description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		}

		// Updated addthis custom code to allow html codes
		if (isset($data['social_addthis_customcode'])) {
			$data['social_addthis_customcode']	= JRequest::getVar( 'social_addthis_customcode', '', 'post', 'string', JREQUEST_ALLOWRAW);
		}

		// Try to save the settings now
		$state = $model->save($data);

		$message = $state ? JText::_('COM_EASYBLOG_SETTINGS_STORE_SUCCESS') : JText::_('COM_EASYBLOG_SETTINGS_STORE_ERROR');
		$type = $state ? 'success' : 'error';

		// Set info
		$this->info->set($message, $type);

		// Clear the component's cache
		$cache = JFactory::getCache('com_easyblog');
		$cache->clean();

		$url = 'index.php?option=com_easyblog&view=settings&layout=' . $page;

		if ($activeTab) {
			$url .= '&active=' . $activeTab;
		}

		$this->app->redirect($url);
	}

	/**
	 * Allows caller to save their api key
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveApi()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('setting');

		$model 	= EB::model('Settings');
		$key 	= $this->input->get('apikey', '', 'default');
		$from 	= $this->input->get('from', '', 'default');
		$return = $this->input->get('return', '', 'default');

		// Save the apikey
		$model->save(array('main_apikey' => $key));

		EB::info()->set(JText::_('COM_EASYBLOG_API_KEY_SAVED'), 'success');

		// If return is specified, respect that
		if (!empty($return)) {
			$return  = base64_decode($return);
			$this->app->redirect($return);
		}

		if (empty($from)) {
			$this->app->redirect( 'index.php?option=com_easyblog' , JText::_( '' ) );
		} else {
		    $this->app->redirect( 'index.php?option=com_easyblog&view=updater' , JText::_( 'COM_EASYBLOG_API_KEY_SAVED' ) );
		}
	}

	/**
	 * Allows user to import settings file
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function import()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('setting');

		// Get the file data
		$file = $this->input->files->get('file');

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			$this->info->set('COM_EASYBLOG_SETTINGS_IMPORT_ERROR_FILE_INVALID', 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=settings');
		}

		// Get the path to the temporary file
		$path = $file['tmp_name'];
		$contents = JFile::read($path);

		// Load the configuration
		$table = EB::table('Configs');
		$table->load(array('name' => 'config'));

		$table->params 	= $contents;

		$table->store();

		$this->info->set('COM_EASYBLOG_SETTINGS_IMPORT_SUCCESS', 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=settings');
	}

	/**
	* Save the Email Template.
	*/
	public function saveEmailTemplate()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'setting' );

		$mainframe 	= JFactory::getApplication();
		$file 		= JRequest::getVar('file', '', 'POST' );
		$filepath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $file;
		$content	= JRequest::getVar( 'content' , '' , 'POST' , '' , JREQUEST_ALLOWRAW );
		$msg		= '';
		$msgType	= '';

		$status 	= JFile::write($filepath, $content);
		if(!empty($status))
		{
			$msg = JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_SUCCESS');
			$msgType = 'info';
		}
		else
		{
			$msg = JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_FAIL');
			$msgType = 'error';
		}

		$mainframe->enqueueMessage($msg);
		$mainframe->redirect('index.php?option=com_easyblog&view=settings&layout=editEmailTemplate&file='.$file.'&msg='.$msg.'&msgtype='.$msgType.'&tmpl=component&browse=1');
	}
}
