<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Module controller class.
 */
class AdvancedModulesControllerModule extends JControllerForm
{
	/**
	 * Override parent add method.
	 *
	 * @return  mixed  True if the record can be added, a JError object if not.
	 */
	public function add()
	{
		$app = JFactory::getApplication();

		// Get the result of the parent method. If an error, just return it.
		$result = parent::add();

		if ($result instanceof Exception)
		{
			return $result;
		}

		// Look for the Extension ID.
		$extensionId = $app->input->get('eid', 0, 'int');

		if (empty($extensionId))
		{
			$redirectUrl = 'index.php?option=' . $this->option . '&view=' . $this->view_item . '&layout=edit';

			$this->setRedirect(JRoute::_($redirectUrl, false));

			return JError::raiseWarning(500, JText::_('COM_MODULES_ERROR_INVALID_EXTENSION'));
		}

		$app->setUserState('com_advancedmodules.add.module.extension_id', $extensionId);
		$app->setUserState('com_advancedmodules.add.module.params', null);

		// Parameters could be coming in for a new item, so let's set them.
		$params = $app->input->get('params', array(), 'array');
		$app->setUserState('com_advancedmodules.add.module.params', $params);
	}

	/**
	 * Override parent cancel method to reset the add module state.
	 *
	 * @param   string $key The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancel($key = null)
	{
		$app = JFactory::getApplication();

		$result = parent::cancel();

		$app->setUserState('com_advancedmodules.add.module.extension_id', null);
		$app->setUserState('com_advancedmodules.add.module.params', null);

		if (!JFactory::getApplication()->isAdmin())
		{
			$returnUri = $this->input->post->get('return', null, 'base64');
			$returnUri = !empty($returnUri) ? base64_decode(urldecode($returnUri)) : JUri::base();
			$this->setRedirect($returnUri);
			$this->redirect();
		}

		return $result;
	}

	/**
	 * Override parent allowAdd method.
	 *
	 * @param   array $data An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();

		return ($user->authorise('core.create', 'com_modules') || count($user->getAuthorisedCategories('com_modules', 'core.create')));
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array  $data An array of input data.
	 * @param   string $key  The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user     = JFactory::getUser();

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_modules.module.' . $recordId))
		{
			return true;
		}

		// Since there is no asset tracking, revert to the component permissions.
		return JFactory::getUser()->authorise('core.edit', 'com_modules');
	}

	/**
	 * Override parent allowSave method.
	 *
	 * @param   array  $data An array of input data.
	 * @param   string $key  The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowSave($data, $key = 'id')
	{
		// Use custom position if selected
		if (isset($data['custom_position']))
		{
			if (empty($data['position']))
			{
				$data['position'] = $data['custom_position'];
			}

			unset($data['custom_position']);
		}

		return parent::allowSave($data, $key);
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   string $model The model
	 *
	 * @return  boolean  True on success.
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Module', '', array());

		// Preset the redirect
		$redirectUrl = 'index.php?option=com_advancedmodules&view=modules' . $this->getRedirectToListAppend();

		$this->setRedirect(JRoute::_($redirectUrl, false));

		return parent::batch($model);
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy $model     The data model object.
	 * @param   array        $validData The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$app  = JFactory::getApplication();
		$task = $this->getTask();

		switch ($task)
		{
			case 'save2new':
				$app->setUserState('com_advancedmodules.add.module.extension_id', $model->getState('module.extension_id'));
				break;

			default:
				$app->setUserState('com_advancedmodules.add.module.extension_id', null);
				break;
		}

		$app->setUserState('com_advancedmodules.add.module.params', null);
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string $key    The name of the primary key of the URL variable.
	 * @param   string $urlVar The name of the URL variable if different from the primary key
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = null)
	{
		if (!JSession::checkToken())
		{
			JFactory::getApplication()->redirect('index.php', JText::_('JINVALID_TOKEN'));
		}

		if (JFactory::getDocument()->getType() == 'json')
		{
			$model      = $this->getModel();
			$data       = $this->input->post->get('jform', array(), 'array');
			$item       = $model->getItem($this->input->get('id'));
			$properties = $item->getProperties();

			// Replace changed properties
			$data = array_replace_recursive($properties, $data);

			if (!empty($data['assigned']))
			{
				$data['assigned'] = array_map('abs', $data['assigned']);
			}

			// Add new data to input before process by parent save()
			$this->input->post->set('jform', $data);

			// Add path of forms directory
			JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/forms');
		}

		if (!JFactory::getApplication()->isAdmin())
		{
			$this->saveFrontend($key, $urlVar);
		}

		parent::save($key, $urlVar);
	}

	public function saveFrontend($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app     = JFactory::getApplication();
		$lang    = JFactory::getLanguage();
		$model   = $this->getModel();
		$table   = $model->getTable();
		$data    = $this->input->post->get('jform', array(), 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";
		$task    = $this->getTask();

		$returnUri = $this->input->post->get('current', null, 'base64');
		if (empty($returnUri))
		{
			$returnUri = $this->input->post->get('return', null, 'base64');
		}
		$returnUri = !empty($returnUri) ? base64_decode(urldecode($returnUri)) : JUri::base();

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = $this->input->getInt($urlVar);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect($returnUri);

			return false;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect($returnUri);

			return false;
		}

		if (!isset($validData['tags']))
		{
			$validData['tags'] = null;
		}

		// Redirect back to the edit screen.
		$this->setRedirect($returnUri);

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			return false;
		}

		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			return false;
		}

		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
					? $this->text_prefix
					: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				$app->setUserState($context . '.data', null);
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				$returnUri = $this->input->post->get('return', null, 'base64');
				$returnUri = !empty($returnUri) ? base64_decode(urldecode($returnUri)) : JUri::base();

				// Redirect to the previous url
				$this->setRedirect($returnUri);
				break;
		}

		$this->redirect();
	}
}
