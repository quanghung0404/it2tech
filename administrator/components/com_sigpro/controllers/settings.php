<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SigProControllerSettings extends SigProController
{

	public function apply()
	{
		$response = $this->saveSettings();
		$this->setRedirect('index.php?option=com_sigpro&view=settings', $response->message, $response->type);
	}

	public function save()
	{
		$response = $this->saveSettings();
		$this->setRedirect('index.php?option=com_sigpro', $response->message, $response->type);
	}

	protected function saveSettings()
	{
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			$this->checkPermissions();
			JRequest::checkToken() or jexit('Invalid Token');
			$data = JRequest::getVar('jform', array(), 'post', 'array');
			$id = JRequest::getInt('id');
			$option = JRequest::getCmd('component');

			// Joomla! 3.2 compatibility
			if (version_compare(JVERSION, '3.2', 'ge'))
			{
				require_once JPATH_SITE.'/components/com_config/model/cms.php';
				require_once JPATH_SITE.'/components/com_config/model/form.php';
			}

			// Validate the form
			JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/'.$option);
			$form = JForm::getInstance('com_sigpro.settings', 'config', array('control' => 'jform', 'load_data' => $loadData), false, '/config');
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_config/models');
			$model = JModelLegacy::getInstance('Component', 'ConfigModel');
			$params = $model->validate($form, $data);
			if ($params === false)
			{
				$errors = $model->getErrors();
				$response = new stdClass;
				$response->message = $errors[0] instanceof Exception ? $errors[0]->getMessage() : $errors[0];
				$response->type = 'warning';
				return $response;
			}

			$data = array('params' => $params, 'id' => $id, 'option' => $option);
		}
		else
		{
			JRequest::checkToken() or jexit('Invalid Token');
			$data = JRequest::get('post');
		}

		$model = SigProModel::getInstance('Settings', 'SigProModel');
		$model->setState('option', 'com_sigpro');
		$model->setState('data', $data);

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$options = array('defaultgroup' => '_system', 'cachebase' => JPATH_ADMINISTRATOR.'/cache');
			$cache = JCache::getInstance('callback', $options);
			$cache->clean();
		}

		$response = new stdClass;
		if ($model->save())
		{
			$response->message = JText::_('COM_SIGPRO_SETTINGS_SAVED');
			$response->type = 'message';
		}
		else
		{
			$response->message = $model->getError();
			$response->type = 'error';
		}
		return $response;
	}

	public function cancel()
	{
		$this->setRedirect('index.php?option=com_sigpro');
	}

	protected function checkPermissions()
	{
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			if (!JFactory::getUser()->authorise('core.admin', 'com_sigpro'))
			{
				JFactory::getApplication()->redirect('index.php?option=com_sigpro', JText::_('JERROR_ALERTNOAUTHOR'));
				return;
			}
		}
	}

}
