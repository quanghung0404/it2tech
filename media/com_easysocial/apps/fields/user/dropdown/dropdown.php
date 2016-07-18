<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include the fields library
ES::import('admin:/includes/fields/dependencies');

class SocialFieldsUserDropdown extends SocialFieldItem
{
	public function getValue()
	{
		$container = $this->getValueContainer();

		$value = $container->data;

		$option = FD::table('fieldoptions');
		$option->load( array( 'parent_id' => $this->field->id, 'key' => 'items', 'value' => $value ) );

		$container->value = $option->title;

		return $container;
	}

	public function getOptions()
	{
		$options = $this->field->getOptions( 'items' );

		if( empty( $options ) )
		{
			return array();
		}

		$result = array();

		foreach( $options as $o )
		{
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
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegister( &$post , &$registration )
	{
		// Current selected value.
		$selected 	= '';

		// If the value exists in the post data, it means that the user had previously set some values.
		if( isset( $post[ $this->inputName ] ) && !empty( $post[ $this->inputName ] ) )
		{
			$selected 	= $post[ $this->inputName ];
		}

		// Get list of child options
		$options = $this->params->get( 'items' );

		if( empty( $options ) )
		{
			return;
		}

		// Detect if there's any errors.
		$errors 	= $registration->getErrors( $this->inputName );

		$this->set( 'error'		, $errors );

		// Set the default value.
		$this->set( 'selected'	, $selected );

		// Set options
		$this->set( 'options'	, $options );

		// Display the output.
		return $this->display();
	}

	public function onRegisterValidate(&$post, &$user)
	{
		$value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validate($value);
	}

	/**
	 * Displays the field input for user when they edit their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		// Get the list items
		$options = $this->params->get('items');
		$selected = '';

		if ($options) {

			foreach ($options as $id => $option) {

				if ($option->default) {
					$selected = $option->value;
				}
			}
		}

		// If this field have value, then we use from value
		if ($this->value !== '' && !is_null($this->value)) {
			$selected = $this->value;
		}

		// If the value exists in the post data, it means that the user had previously set some values.
		// We use isset instead of !empty because the data could be 0
		if (isset($post[$this->inputName])) {
			$selected = $post[$this->inputName];
		}

		// Get any errors
		$error = $this->getError($errors);

		$this->set('error', $error);
		$this->set('selected', $selected);
		$this->set('options', $options);

		return $this->display();
	}

	public function onEditValidate(&$post, &$user)
	{
		// We use isset instead of !empty because the data could be 0
		$value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validate($value);
	}

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when their profile is viewed.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onDisplay( $user )
	{
		$value		= $this->value;

		if( !$value )
		{
			return;
		}

		if( !$this->allowedPrivacy( $user ) )
		{
			return;
		}

		$option = FD::table('fieldoptions');
		$option->load( array( 'parent_id' => $this->field->id, 'key' => 'items', 'value' => $value ) );

		$field = $this->field;

		$advGroups = array(SOCIAL_FIELDS_GROUP_GROUP, SOCIAL_FIELDS_GROUP_USER);

		if (in_array($field->type, $advGroups) && $field->searchable) {
			$params = array( 'layout' => 'advanced' );
			if ($field->type != SOCIAL_FIELDS_GROUP_USER) {
				$params['type'] = $field->type;
				$params['uid'] = $field->uid;
			}
			$params['criterias[]'] = $field->unique_key . '|' . $field->element;
			$params['operators[]'] = 'equal';
			$params['conditions[]'] = $this->value;

			$advsearchLink = FRoute::search($params);
			$this->set( 'advancedsearchlink'	, $advsearchLink );
		}


		// Push variables into theme.
		$this->set( 'option', $option );
		$this->set( 'value' , $value );

		return $this->display();
	}

	/**
	 * return formated string from the fields value
	 *
	 * @since	1.0
	 * @access	public
	 * @param	userfielddata
	 * @return	array array of objects with two attribute, ffriend_id, score
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onIndexer( $userFieldData )
	{
		if(! $this->field->searchable )
			return false;

		$content = trim( $userFieldData );

		if( $content )
			return $content;
		else
			return false;
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
		$options = $this->params->get( 'items' );

		$selected = '';

		if( !empty( $options ) )
		{
			foreach( $options as $id => $option )
			{
				if( !empty( $option->default ) )
				{
					$selected = $option->value;
				}
			}
		}

		$this->set( 'selected', $selected );
		$this->set( 'options', $options );

		return $this->display();
	}

	public function validate($value)
	{
		if ($this->isRequired() && (is_null($value) || $value == '')) {
			$this->setError(JText::_('PLG_FIELDS_DROPDOWN_VALIDATION_PLEASE_SELECT_A_VALUE'));
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

		return $this->validate($this->value);
	}
}
