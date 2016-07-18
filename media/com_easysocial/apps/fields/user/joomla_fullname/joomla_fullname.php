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
 * Field application for Joomla full name.
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserJoomla_fullname extends SocialFieldItem
{

    /**
     * format the value used in data export
     *
     * @since   1.3
     * @access  public
     * @param   array,
     * @param   userid
     */
    public function onExport($data, $user)
    {
        $field = $this->field;

        $formatted = array('name' => '',
                            'first' => '',
                            'middle' => '',
                            'last' => ''
                        );

        if (isset($data[$field->id])) {
            $formatted['name'] = isset($data[$field->id]['name']) ? $data[$field->id]['name'] : '';
            $formatted['first'] = isset($data[$field->id]['first']) ? $data[$field->id]['first'] : '';
            $formatted['middle'] = isset($data[$field->id]['middle']) ? $data[$field->id]['middle'] : '';
            $formatted['last'] = isset($data[$field->id]['last']) ? $data[$field->id]['last'] : '';
        }

        // lets further check if user has atleast the 'name' segment or not. if not, then let return user 'fullname' to caller.
        if (!$formatted['name'] && !$formatted['first'] && !$formatted['middle'] && !$formatted['last']) {
        	// if all empty, let load up user data and return fullname atlease.
        	$jUser = JFactory::getUser($user);
        	$formatted['name'] = $jUser->name;
        }

        return $formatted;
    }

	/**
	 * We need to ensure that the fields data are stored so that we don't need to mess up with the name column in Joomla.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterBeforeSave(&$post)
	{
		if (empty($post['first_name']) && !empty($post['username']) && $this->params->get('username_fallback')) {
			$post['first_name'] = $post['username'];
		}

		return $this->save($post);
	}
	/**
	 * We need to ensure that the fields data are stored so that we don't need to mess up with the name column in Joomla.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditBeforeSave(&$post, &$user)
	{
		if (empty($post['first_name']) && !empty($post['username']) && $this->params->get('username_fallback')) {
			$post['first_name'] = $post['username'];
		}

		// Detect if the name is changed.
		$post['nameChanged'] = $this->nameChanged($post, $user);

		return $this->save($post);
	}

	/**
	 * Triggers before a user is saved
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAdminEditBeforeSave(&$post, &$user)
	{
		if (empty($post['first_name']) && !empty($post['username']) && $this->params->get('username_fallback')) {
			$post['first_name'] = $post['username'];
		}

		// Detect if the name is changed.
		$post['nameChanged']	= $this->nameChanged($post, $user);

		return $this->save($post);
	}

	/**
	 * Processes after a user registers on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegisterAfterSave(&$data, &$user)
	{
		$config 	= FD::config();

		if ($config->get('users.aliasName') != 'realname') {
			return;
		}

		// Only proceed when the name has been changed.
		if (isset($data['nameChanged']) && !$data['nameChanged']) {
			return;
		}

		$this->saveAlias($data, $user);
	}

	/**
	 * Triggers after a user is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditAfterSave(&$data, &$user)
	{
		$config = FD::config();

		if ($config->get('users.aliasName') != 'realname') {
			return;
		}

		// Only proceed when the name has been changed.
		if (isset($data['nameChanged']) && !$data['nameChanged']) {
			return;
		}

		$this->saveAlias($data, $user);
	}

	/**
	 * Triggers after a user is saved by the admin
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAdminEditAfterSave(&$data, &$user)
	{
		$config 	= FD::config();

		if ($config->get('users.aliasName') != 'realname') {
			return;
		}

		// Only proceed when the name has been changed.
		if (isset($data['nameChanged']) && !$data['nameChanged']) {
			return;
		}

		$this->saveAlias($data, $user);
	}

	/**
	 * Determines if the name has changed
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function nameChanged($post, $user)
	{
		// Detect if the name has changed
		$firstName 		= isset($post['first_name']) ? $post['first_name'] : '';
		$middleName 	= isset($post['middle_name']) ? $post['middle_name'] : '';
		$lastName 		= isset($post['last_name']) ? $post['last_name'] : '';

		// Build the real name.
		$name = $this->buildFullname($firstName, $middleName, $lastName);

		if ($name != $user->name) {
			return true;
		}

		return false;
	}

	/**
	 * Responsible to save the alias of the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveAlias(&$data, &$user)
	{
		// Get the first name.
		$firstName 		= isset($data['first_name']) ? $data['first_name'] : '';
		$middleName 	= isset($data['middle_name']) ? $data['middle_name'] : '';
		$lastName 		= isset($data['last_name']) ? $data['last_name'] : '';

		// Build the real name.
		$name = $this->buildFullname($firstName, $middleName, $lastName);

		$alias = JFilterOutput::stringURLSafe($name);

		// Check if the alias exists.
		$model = FD::model('Users');

		// Keep the original state of the alias
		$tmp 	= $alias;

		while ($model->aliasExists($alias, $user->id)) {
			// Generate a new alias for the user.
			$alias = $tmp . '-' . rand(1, 150);
		}

		$user->alias = $alias;

		$user->save();
	}

	private function save(&$data)
	{
		// Get the first name.
		$firstName 		= isset($data['first_name']) ? $data['first_name'] : '';
		$middleName 	= isset($data['middle_name']) ? $data['middle_name'] : '';
		$lastName 		= isset($data['last_name']) ? $data['last_name'] : '';

		$name = $this->buildFullname($firstName, $middleName, $lastName);

		// Assign a "name" index so that `#__users`.`name` can have proper values.
		$data['name'] 	= $name;

		// Assign the data to be stored in our own table.
		$nameObj 			= new stdClass;
		$nameObj->first		= $firstName;
		$nameObj->middle	= $middleName;
		$nameObj->last		= $lastName;
		$nameObj->name 		= $name;

		$data[$this->inputName] = $nameObj;


		return true;
	}

	/**
	 * Build fullname based on the store format specified.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3.7
	 * @access public
	 * @param  string    $first  First name.
	 * @param  string    $middle Middle name.
	 * @param  string    $last   Last name.
	 * @return string            Full name built based on the store format.
	 */
	public function buildFullname($first, $middle, $last)
	{
		// 1: first, middle, last
		// 2: last, middle, first

		$format = $this->params->get('store_format');

		$name = '';

		if ($format == 1 && !empty($first)) {
			$name .= $first;
		}

		if ($format == 2 && !empty($last)) {
			$name .= $last;
		}

		if (!empty($middle)) {
			$name .= ' ' . $middle;
		}

		if ($format == 1 && !empty($last)) {
			$name .= ' ' . $last;
		}

		if ($format == 2 && !empty($first)) {
			$name .= ' ' . $first;
		}

		return $name;
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterValidate(&$post)
	{
		// Get the first name from the query.
		$firstName	= isset($post['first_name']) ? trim($post['first_name']) : '';

		// Get the middle name
		$middleName	= isset($post['middle_name']) ? trim($post['middle_name']) : '';

		// Get the last name
		$lastName	= isset($post['last_name']) ? trim($post['last_name']) : '';

		return $this->validateName($firstName, $middleName, $lastName);
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditValidate(&$post)
	{
		// Get the first name from the query.
		$firstName	= isset($post['first_name']) ? trim($post['first_name']) : '';

		// Get the middle name
		$middleName	= isset($post['middle_name']) ? trim($post['middle_name']) : '';

		// Get the last name
		$lastName	= isset($post['last_name']) ? trim($post['last_name']) : '';

		return $this->validateName($firstName, $middleName, $lastName);
	}

	/**
	 * Validates the field
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function validateName($firstName, $middleName, $lastName)
	{
		// Test if this field is required and firstName is the basic minumum
		if ($this->isRequired() && empty($firstName)) {
			$this->setError(JText::_('PLG_FIELDS_JOOMLA_FULLNAME_VALIDATION_EMPTY_NAME'));

			return false;
		}

		if ($this->params->get('max') > 0) {
			$totalLength = JString::strlen($firstName) + JString::strlen($middleName) + JString::strlen($lastName);

			if ($totalLength > $this->params->get('max')) {
				$this->setError(JText::_('PLG_FIELDS_JOOMLA_FULLNAME_VALIDATION_NAME_TOO_LONG'));
				return false;
			}
		}

		if ($this->params->get('regex_validate')) {
			$name = $firstName;

			if (!empty($middleName)) {
				$name = ' ' . $middleName;
			}

			if (!empty($lastName)) {
				$name = ' ' . $lastName;
			}

			$format = $this->params->get('regex_format');

			$modifier = $this->params->get('regex_modifier');

			$pattern = '/' . $format . '/' . $modifier;

			$result = preg_match($pattern, $name);

			if (empty($result)) {
				$this->setError(JText::_('PLG_FIELDS_JOOMLA_FULLNAME_VALIDATION_INVALID_NAME'));

				return false;
			}
		}

		return true;
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
	public function onRegister(&$post,  &$registration)
	{
		// Detect if there's any errors.
		$error 	= $registration->getErrors($this->inputName);

		$this->displayForm(empty($error) ? $post : array());

		$this->set('error'	, $error);

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
	public function onEdit(&$post, &$user, $errors)
	{
		$default = $this->getName($user, $this->value);

		$this->displayForm($post, $default);

		$error = $this->getError($errors);


		$this->set('error', $error);

		return $this->display();
	}

	public function displayForm($post, $default = null)
	{
		$firstName	= isset($post['first_name']) ? trim($post['first_name']) : (isset($default->first) ? $default->first : '');
		$middleName	= isset($post['middle_name']) ? trim($post['middle_name']) : (isset($default->middle) ? $default->middle : '');
		$lastName	= isset($post['last_name']) ? trim($post['last_name']) : (isset($default->last) ? $default->last : '');

		$this->set('firstName', $this->escape($firstName));
		$this->set('middleName', $this->escape($middleName));
		$this->set('lastName', $this->escape($lastName));

		// Build the real name.
		$name = $this->buildFullname($firstName, $middleName, $lastName);

		$this->set('name', $this->escape($name));

		return true;
	}

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when their profile is viewed.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onDisplay($user)
	{
		$name 			= new stdClass();

		if (empty($this->value)) {
			$name->first		= $this->escape($user->name);
			$name->middle 		= '';
			$name->last 		= '';
			$name->name 		= $this->escape($user->name);
		}
		else
		{
			$obj = FD::makeObject($this->value);

			$name->first		= !empty($obj->first) ? $this->escape($obj->first) : '';
			$name->middle		= !empty($obj->middle) ? $this->escape($obj->middle) : '';
			$name->last			= !empty($obj->last) ? $this->escape($obj->last) : '';
			$name->name			= !empty($obj->name) ? $this->escape($obj->name) : '';
		}

		// $this->set('user'	, $user);
		$this->set('name'	, $name);

		// linkage to advanced search page.
		$field = $this->field;
		if ($field->type == SOCIAL_FIELDS_GROUP_USER &&  $field->searchable) {
			$params = array( 'layout' => 'advanced' );
			$params['criterias[]'] = $field->unique_key . '|' . $field->element;
            $params['datakeys[]'] = 'first';
			$params['operators[]'] = 'contain';
			$params['conditions[]'] = $name->first;

			$advsearchLink = FRoute::search($params);
			$this->set( 'advancedsearchlink'	, $advsearchLink );
		}

		return $this->display();
	}

	public function getValue()
	{
		$user = FD::user($this->field->uid);

		return $this->getName($user, $this->value);
	}

	/**
	 * Retrieves the user's name.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser		The user that is being edited.
	 * @return	object
	 */
	protected function getName(SocialUser $user, $value = '')
	{
		// if (!$value) {
		// 	$nameObj 			= new stdClass();
		// 	$nameObj->first		= $user->name;;
		// 	$nameObj->middle 	= '';
		// 	$nameObj->last 		= '';
		// 	$nameObj->name 		= $user->name;

		// 	return $nameObj;
		// }

		// return FD::json()->decode($value);

		$obj = new SocialFieldUserJoomla_FullnameObject($user, $value);

		$obj->setFormat($this->params->get('store_format'));

		return $obj;
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
		return $this->display();
	}

	public function onOAuthGetMetaFields(&$fields)
	{
		$fields = array_merge($fields, array('name', 'first_name', 'middle_name', 'last_name'));
	}

	public function onRegisterOAuthBeforeSave(&$post, &$client)
	{
		$this->save($post);
	}

	public function onRegisterOAuthAfterSave(&$post, &$client, &$user)
	{
		$config 	= FD::config();

		if ($config->get('users.aliasName') != 'realname') {
			return;
		}

		// Only proceed when the name has been changed.
		if (isset($post['nameChanged']) && !$post['nameChanged']) {
			return;
		}

		$this->saveAlias($post, $user);
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
		$firstName = '';

		if (!empty($this->value)) {
			$obj = FD::makeObject($this->value);

			$firstName = !empty($obj->first) ? $obj->first : '';
		}

		if ($this->isRequired() && empty($firstName) && empty($user->name)) {
			$this->setError(JText::_('PLG_FIELDS_JOOMLA_FULLNAME_VALIDATION_EMPTY_NAME'));

			return false;
		}

		return true;
	}

	/**
	 * Checks if this field is filled in.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3
	 * @access public
	 * @param  array        $data   The post data.
	 * @param  SocialUser   $user   The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		if (empty($this->value)) {
			return false;
		}

		$obj = FD::makeObject($this->value);

		if (empty($obj->first) && empty($user->name)) {
			return false;
		}

		return true;
	}

	/**
	 * Trigger to get this field's value for various purposes.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 * @return Mixed               The value data.
	 */
	public function onGetValue($user)
	{
		$container = $this->getValueContainer();

		if (FD::json()->isJsonString($container->raw)) {
			$container->data = FD::json()->decode($container->raw);
		}

		$container->value = $this->getName($user, $container->raw);

		return $container;
	}
}

class SocialFieldUserJoomla_FullnameObject
{
	public $first = '';
	public $middle = '';
	public $last = '';
	public $name = '';

	public $format = 1;

	public function __construct($user, $data = null)
	{
		$this->load($user, $data);
	}

	public function load($user, $data = null)
	{
		$this->first = $user->name;
		$this->name = $user->name;

		$json = FD::json();

		if ($json->isJsonString($data)) {
			$data = $json->decode($data);
		} else {
			$data = (object) $data;
		}

		if (!empty($data->name)) {
			$this->name = $data->name;
		}

		if (!empty($data->first)) {
			$this->first = $data->first;
		}

		if (!empty($data->middle)) {
			$this->middle = $data->middle;
		}

		if (!empty($data->last)) {
			$this->last = $data->last;
		}
	}

	public function toJson()
	{
		return FD::json()->encode(array(
			'first' => $this->first,
			'middle' => $this->middle,
			'last' => $this->last,
			'name' => $this->name
		));
	}

	public function toString()
	{
		if (!empty($this->name) && empty($this->first) && empty($this->middle) && empty($this->last)) {
			return $this->name;
		}

		$name = '';

		if ($this->format == 1 && !empty($this->first)) {
			$name .= $this->first;
		}

		if ($this->format == 2 && !empty($this->last)) {
			$name .= $this->last;
		}

		if (!empty($this->middle)) {
			$name .= $this->middle;
		}

		if ($this->format == 1 && !empty($this->last)) {
			$name .= $this->last;
		}

		if ($this->format == 2 && !empty($this->first)) {
			$name .= $this->first;
		}

		return $name;
	}

	public function __toString()
	{
		return $this->toString();
	}

	public function setFormat($format = 1)
	{
		$this->format = $format;
	}
}
