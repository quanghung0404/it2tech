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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include the fields library
FD::import('admin:/includes/fields/dependencies');

class SocialFieldsUserCheckbox extends SocialFieldItem
{
	public function getValue()
	{
		$container = $this->getValueContainer();

		$container->data = FD::makeObject($container->raw);

		$container->value = array();

		foreach( $container->data as $v )
		{
			$option = FD::table( 'fieldoptions' );
			$option->load( array( 'parent_id' => $this->field->id, 'key' => 'items', 'value' => $v ) );

			$container->value[$option->value] = $option->title;
		}

		return $container;
	}

	public function getDisplayValue()
	{
		$options = $this->getValue();
		$values = array();

		foreach ($options as $option) {
			$values[$option->value] = $option->title;
		}

		return $values;
	}

	public function getOptions()
	{
		$options = $this->field->getOptions('items');

		if (!$options) {
			$options = array();
		}

		$result = array();

		foreach ($options as $o) {
			$result[$o->value] = $o->title;
		}

		return $result;
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
	 */
	public function onRegister( &$post, &$registration )
	{
		// Selected value
		$selected	= array();

		// Test if the user had tried to submit any values.
		if (!empty($post[$this->inputName])) {
			$selected = json_decode($post[$this->inputName]);
		}

		// Get a list of options for this field.
		$options = $this->field->getOptions('items');

		// If there's no options, we shouldn't even be showing this field.
		if (empty($options)) {
			return;
		}

		// Detect if there's any errors.
		$error = $registration->getErrors($this->inputName);

		$this->set('error', $error);
		$this->set('selected', $selected);
		$this->set('options', $options);

		// Display the output.
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
	 */
	public function onRegisterValidate(&$post, &$registration)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validate($value);
	}

	/**
	 * Displays the field input for user when they edit their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser		The user that is being edited.
	 * @param	Array			The post data.
	 * @param	Array			The error data.
	 * @return	string			The html string of the field
	 */
	public function onEdit( &$post, &$user, $errors )
	{
		$options = $this->field->getOptions('items');
		$selected = array();

		// Get the value
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $this->value;
		$selected = json_decode($value);

		if (is_null($selected) || $selected === "") {
			$selected = array();
		}

		$error = $this->getError( $errors );

		// Set the value.
		$this->set('options', $options);
		$this->set('error', $error);
		$this->set('selected', $selected);

		return $this->display();
	}

	public function onEditBeforeSave(&$post, &$user)
	{
		//If that is empty value, so it will pass to array
		if (empty($post[$this->inputName])) {
			$post[$this->inputName] = array();
		}
	}

	/**
	 * Determines whether there's any errors in the submission in the edit form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onEditValidate( &$post )
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validate($value);
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @return	string	The html output.
	 *
	 */
	public function onSample()
	{
		return $this->display();
	}

	public function onDisplay( $user )
	{
		$value		= $this->value;

		if( !$value )
		{
			return;
		}

		$value = FD::makeObject( $value );

		if( !$this->allowedPrivacy( $user ) )
		{
			return;
		}


		$options = array();

		$field = $this->field;

		$advGroups = array(SOCIAL_FIELDS_GROUP_GROUP, SOCIAL_FIELDS_GROUP_USER);
		$addAdvLink = in_array($field->type, $advGroups) && $field->searchable;

		foreach( $value as $v )
		{
			$option = FD::table( 'fieldoptions' );
			$option->load( array( 'parent_id' => $this->field->id, 'key' => 'items', 'value' => $v ) );

			if ($addAdvLink) {
				$params = array( 'layout' => 'advanced' );

				if ($field->type != SOCIAL_FIELDS_GROUP_USER) {
					$params['type'] = $field->type;
					$params['uid'] = $field->uid;
				}

				$params['criterias[]'] = $field->unique_key . '|' . $field->element;
				$params['operators[]'] = 'contain';
				$params['conditions[]'] = $v;

				$advsearchLink = FRoute::search($params);
				$option->advancedsearchlink = $advsearchLink;
			}

			$options[] = $option;
		}

		// echo '<pre>';print_r( $value );echo '</pre>';


		$this->set( 'options', $options );

		return $this->display();
	}

	public function validate($value)
	{
		if (!empty($value)) {
			$value = FD::json()->decode($value);
		}

		// If this is required, check for the value.
		if( $this->isRequired() && empty( $value ) )
		{
			$this->setError( JText::_( 'PLG_FIELDS_CHECKBOX_CHECK_AT_LEAST_ONE_ITEM' ) );
			return false;
		}

		return true;
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
		return $this->validate($this->value);
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
		return $this->getValue();
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

		$value = FD::makeObject($this->value);

		if (empty($value)) {
			return false;
		}

		return true;
	}
}

class SocialFieldsUserCheckboxValue extends SocialFieldValue
{
	public function toString()
	{
		$values = array_values($this->value);

		return implode(', ', $values);
	}
}
