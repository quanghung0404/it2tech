<?php
/**
 * @version		$Id$
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class SigProModelSettings extends SigProModel
{

	protected $extensionID = null;

	public function getForm()
	{
		$option = $this->getState('option');
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			$component = JComponentHelper::getComponent($option);
			$this->extensionID = $component->id;
			JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/'.$option);
			$form = JForm::getInstance($option.'.settings', 'config', array('control' => 'jform'), false, '/config');
			$form->bind($component->params);
		}
		else
		{
			$component = JTable::getInstance('component');
			$component->loadByOption($option);
			$this->extensionID = $component->id;
			$form = new JParameter($component->params, JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.xml');
		}
		return $form;
	}

	public function save()
	{
		$option = $this->getState('option');
		$data = $this->getState('data');
		if (version_compare(JVERSION, '2.5.0', 'ge'))
		{
			$table = JTable::getInstance('extension');

			// Save the rules.
			if (isset($data['params']) && isset($data['params']['rules']))
			{
				$rules = new JAccessRules($data['params']['rules']);
				$asset = JTable::getInstance('asset');

				if (!$asset->loadByName($data['option']))
				{
					$root = JTable::getInstance('asset');
					$root->loadByName('root.1');
					$asset->name = $data['option'];
					$asset->title = $data['option'];
					$asset->setLocation($root->id, 'last-child');
				}
				$asset->rules = (string)$rules;

				if (!$asset->check() || !$asset->store())
				{
					$this->setError($asset->getError());
					return false;
				}

				// We don't need this anymore
				unset($data['option']);
				unset($data['params']['rules']);
			}

			// Load the previous Data
			if (!$table->load($data['id']))
			{
				$this->setError($table->getError());
				return false;
			}

			unset($data['id']);

			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Clean the component cache.
			$this->cleanCache('_system');

			return true;
		}
		else
		{
			$component = JTable::getInstance('component');
			$component->loadByOption($option);
			$component->bind($data);
			if (!$component->check())
			{
				$this->setError($component->getError());
				return false;
			}
			if (!$component->store())
			{
				$this->setError($component->getError());
				return false;
			}
		}
		return true;
	}

	public function getExtensionID()
	{
		return $this->extensionID;
	}

}
