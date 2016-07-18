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

/**
 * Field application for timezone
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserJoomla_timezone extends SocialFieldItem
{
	/**
	 * The list of available timezone groups to use.
	 * @var Array
	 */
	private $timezones = array(
		'Africa' 	=> null,
		'America' 	=> null,
		'Antartica' => null,
		'Arctic' 	=> null,
		'Asia' 		=> null,
		'Atlantic'	=> null,
		'Australia' => null,
		'Europe' 	=> null,
		'Indian' 	=> null,
		'Pacific' 	=> null
	);

	/**
	 * Stores the state of the current group when filtering arrays.
	 * @var string
	 */
	static $tmpState = null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 */
	public function __construct()
	{
		// Initialize our timezones.
		$this->initTimeZones();

		parent::__construct();
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
	public function onRegister(&$post, &$registration)
	{
		// Get value.
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $this->params->get('default');

		// Set value.
		$this->set('value', $value);

		// Check for errors.
		$error = $registration->getErrors($this->inputName);

		// Set errors.
		$this->set('error', $error);

		// Set the timezones for the template.
		$this->set('timezones', $this->timezones);

		// Output the registration template.
		return $this->display();
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
	public function onRegisterValidate(&$post, &$registration)
	{
		// Selected value
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : null;

		// If this is required, check for the value.
		if ($this->isRequired() && empty($value)) {
			return $this->setError(JText::_('PLG_FIELDS_JOOMLA_TIMEZONE_VALIDATION_SELECT_TIMEZONE'));
		}

		return true;
	}

	/**
	 * Save trigger which is called before really saving the object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialUser	The user object.
	 * @return	bool	The state of the trigger
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterBeforeSave(&$post, &$user)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : null;

		if (!empty($value)) {
			$user->setParam('timezone', $value);
		}

		unset($post[$this->inputName]);

		return true;
	}

	/**
	 * Displays the field input for user on edit page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser	The user object who is editting
	 * @param	Array		The post data in array
	 * @param	Array		The errors in array
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		// Get error.
		$error = $this->getError($errors);

		// Set error.
		$this->set('error', $error);

		// Get value.
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $user->getParam('timezone', $this->params->get('default'));

		// Set value.
		$this->set('value', $value);

		$this->set('timezones', $this->timezones);

		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the edit form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditValidate(&$post)
	{
		// Selected value
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		// If this is required, check for the value.
		if ($this->isRequired() && empty($value)) {
			$this->setError(JText::_('PLG_FIELDS_JOOMLA_TIMEZONE_VALIDATION_SELECT_TIMEZONE'));
			return false;
		}

		return true;
	}

	/**
	 * Save trigger which is called before really saving the object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialUser	The user object.
	 * @return	bool	The state of the trigger
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditBeforeSave(&$post, &$user)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		if (!empty($value)) {
			$user->setParam('timezone', $value);
		}

		unset($post[$this->inputName]);

		return true;
	}

	/**
	 * Initializes timezones.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initTimeZones()
	{
		// Get available time zones.
		$zones = DateTimeZone::listIdentifiers();

		foreach ($this->timezones as $group => &$val) {
			// Set the temporary state
			self::$tmpState = $group;

			// Perform filtering of the current group
			$match = array_filter($zones, array($this, 'filterByGroup'));

			$val = $match;
		}
	}

	/**
	 * Performs array filtering of the timezone.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	boolean		True if array value matches the searched text.
	 */
	public static function filterByGroup($var)
	{
		if (stristr($var, self::$tmpState) === false) {
			return false;
		}

		return true;
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onSample()
	{
		// Set the timezones for the template.
		$this->set('timezones', $this->timezones);

		// Set an empty value
		$this->set('value', '');

		return $this->display();
	}

	public function onDisplay($user)
	{
		if (!$this->allowedPrivacy($user)) {
			return;
		}

		$timezone = $user->getParam('timezone');

		if (empty($timezone)) {
			return;
		}

		$this->set('value', $timezone);

		return $this->display();
	}

	/**
	 * Checks if this field is complete.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 */
	public function onFieldCheck($user)
	{
		$timezone = $user->getParam('timezone');

		if ($this->isRequired() && empty($timezone)) {
			$this->setError(JText::_('PLG_FIELDS_JOOMLA_TIMEZONE_VALIDATION_SELECT_TIMEZONE'));
			return false;
		}

		return true;
	}
}
