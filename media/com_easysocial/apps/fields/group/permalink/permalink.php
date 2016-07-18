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

// Include the fields library
FD::import('admin:/includes/fields/dependencies');

// Include helper file.
FD::import('fields:/group/permalink/helper');

/**
 * Permalink field for group
 *
 * @since	1.2
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsGroupPermalink extends SocialFieldItem
{
	/**
	 * Saves the permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save(&$post, &$group)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		// There could be possibility that the user removes their permalink so
		// we should not check for empty value here.

		if (empty($value) && !empty($group->title)) {
			$value = JFilterOutput::stringURLSafe($group->title);
		}

		$model = FD::model('groups');

		$table	= FD::table('Group');
		$table->load($group->id);

		$table->alias = $model->getUniqueAlias($value, $group->id);
		$table->store();

		// Update the alias value
		$group->alias = $table->alias;

		$post[$this->inputName] = $table->alias;
	}

	/**
	 * Once the registration is stored, we need to update the user's `permalink` column
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterAfterSave(&$post, &$group)
	{
		return $this->save($post, $group);
	}

	/**
	 * Saves the permalink after their profile is edited.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditAfterSave(&$post, &$group)
	{
		return $this->save($post, $group);
	}

	/**
	 * Performs validation for the gender field.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate($post, $group = null, $isCopy = false)
	{
		$key 	= $this->inputName;

		// Get the current value
		$value 	= isset($post[$key]) ? $post[$key] : '';

		if (!$this->isRequired() && empty($value)) {
			return true;
		}

		// Catch for errors if this is a required field.
		if ($this->isRequired() && empty($value)) {
			$this->setError(JText::_('PLG_FIELDS_GROUP_PERMALINK_REQUIRED'));

			return false;
		}

		if ($this->params->get('max') > 0 && JString::strlen($value) > $this->params->get('max')) {
			$this->setError(JText::_('PLG_FIELDS_GROUP_PERMALINK_EXCEEDED_MAX_LENGTH'));
			return false;
		}

		// Determine the current user that is being edited
		$current 	= '';

		if ($group) {
			$current 	= $group->id;
		}

		if ($current) {
			$group 	= FD::group($current);

			// If the permalink is the same, just return true.
			if ($group->alias == $value) {
				return true;
			}
		}

		if ($isCopy) {
			// lets auto append the alias so that there will not be any conflict.
			$i = 0;
			$iterate = true;
			do {
				if (SocialFieldsGroupPermalinkHelper::exists($value)) {
					$value = $value . '-' . ++$i;
				} else {
					$iterate = false;
				}
			} while ($iterate);

			// var_dump($value);
		}

		if (SocialFieldsGroupPermalinkHelper::exists($value)) {
			$this->setError(JText::_('PLG_FIELDS_GROUP_PERMALINK_NOT_AVAILABLE'));

			return false;
		}

		if (!SocialFieldsGroupPermalinkHelper::valid($value, $this->params)) {
			$this->setError(JText::_('PLG_FIELDS_GROUP_PERMALINK_INVALID_PERMALINK'));

			return false;
		}

		// now lets reset the value is this is a copy operation.
		if ($isCopy) {
			$post[$key] = $value;
		}

		return true;
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterValidate(&$post, &$session)
	{
		$state 	= $this->validate($post);

		return $state;
	}

	/**
	 * Performs validation when a user updates their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditValidate(&$post, &$group, $isCopy = false)
	{
		$state 	= $this->validate($post, $group, $isCopy);

		return $state;
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegister(&$post, &$session)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		// Detect if there's any errors.
		$error 	= $session->getErrors($this->inputName);

		$this->set('error'		, $error);
		$this->set('value'		, $this->escape($value));
		$this->set('groupid'	, null);

		return $this->display();
	}

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser		The user that is being edited.
	 * @return
	 */
	public function onEdit(&$post, &$group, $errors)
	{
		$value	= $group->alias;

		$error	= $this->getError($errors);

		$this->set('value', $this->escape($value));
		$this->set('error', $error);

		$this->set('groupid', $group->id);

		return $this->display();
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onSample()
	{
		return $this->display();
	}
}
