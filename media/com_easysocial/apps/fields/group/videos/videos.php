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

class SocialFieldsGroupVideos extends SocialFieldItem
{
	/**
	 * Displays the form when user tries to create a new group
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function onRegister($post, SocialTableStepSession $session)
	{
		$value = $this->normalize($post, 'videos', $this->params->get('videos', $this->params->get('default', true)));
		$value = (bool) $value;

		// Detect if there's any errors
		$error = $session->getErrors($this->inputName);

		$this->set('error', $error);
		$this->set('value', $value);

		return $this->display();
	}

	/**
	 * Displays the output form when someone tries to edit a group.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	Array 					An array of data that has been submitted
	 * @param	SocialTableStepSession	The session table
	 * @return	string					The html codes for this field
	 *
	 */
	public function onEdit(&$data, &$group, $errors)
	{
		$params	= $group->getParams();
		$value = $group->getParams()->get('videos', $this->params->get('videos', $this->params->get('default', true)));
		$error = $this->getError($errors);

		$this->set('error', $error);
		$this->set('value', $value);

		return $this->display();
	}

	/**
	 * Executes after the group is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onEditBeforeSave(&$data, &$group)
	{
		// Get the posted value
		$value = $this->normalize($data, 'videos', $group->getParams()->get('videos', $this->params->get('default', true)));
		$value = (bool) $value;

		$group->params = $this->setParams($group, $value);
	}

	/**
	 * Executes after the group is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onRegisterBeforeSave(&$data, &$group)
	{
		$value = $this->normalize($data, 'videos', $this->params->get('videos', $this->params->get('default', true)));
		$value = (bool) $value;

		$group->params = $this->setParams($group, $value);
	}

	/**
	 * Given the value, set the params to the group
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setParams($group, $value)
	{
		$params = $group->getParams();
		$params->set('videos', $value);

		return $params->toString();
	}

	/**
	 * Override the parent's onDisplay
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function onDisplay()
	{
		return;
	}

	/**
	 * Displays the sample field in the administration area.
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function onSample()
	{
		$value = $this->params->get('default', true);

		$this->set('value', $value);

		return $this->display();
	}
}
