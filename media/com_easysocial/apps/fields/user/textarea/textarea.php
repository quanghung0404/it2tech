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
FD::import( 'admin:/includes/fields/dependencies' );

class SocialFieldsUserTextarea extends SocialFieldItem
{
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
	public function onRegister( &$post, &$registration )
	{
		// Get the value from posted data if it's available.
		$value = !empty( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : $this->params->get( 'default' );

		// Set the value
		$this->set( 'value', $this->escape( $value ) );

		// Get any errors for this field.
		$error = $registration->getErrors( $this->inputName );

		// Set the error
		$this->set( 'error', $error );

		// Display the output.
		return $this->display();
	}

	/**
	 * Validates the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterValidate( &$post )
	{
		$value = !empty( $post[$this->inputName] ) ? $post[$this->inputName] : '';

		return $this->validateInput( $value );
	}

	/**
	 * Executes before a user's registration is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterBeforeSave( &$post , &$user )
	{
		// use isset instead of !empty because we do not even wan empty string or false value here
		if( $this->params->get( 'readonly' ) && isset( $post[ $this->inputName ] ) )
		{
			unset( $post[ $this->inputName ] );
		}

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
	public function onEdit( &$post, &$user, $errors )
	{
		// Get the value.
		$value = !empty( $post[$this->inputName] ) ? $post[$this->inputName] : $this->value;

		// Set the value.
		$this->set( 'value', $this->escape( $value ) );

		// Get the error.
		$error = $this->getError( $errors );

		// Set the error.
		$this->set( 'error', $error );

		return $this->display();
	}

	/**
	 * Validates the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditValidate( &$post )
	{
		$value = !empty( $post[$this->inputName] ) ? $post[$this->inputName] : '';

		return $this->validateInput( $value );
	}

	/**
	 * Executes before a user's edit is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialUser	The user object.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditBeforeSave( &$post , &$my )
	{
		// use isset instead of !empty because we do not even wan empty string or false value here
		if( $this->params->get( 'readonly' ) && isset( $post[ $this->inputName ] ) )
		{
			unset( $post[ $this->inputName ] );
		}

		return true;
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onSample()
	{
		$this->set( 'value', $this->params->get( 'default' ) );

		return $this->display();
	}

	/**
	 * General validation function
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	Value of the text
	 * @return	bool	State of the validation
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	private function validateInput( $value )
	{
		// If this is required, check for the value.
		if( $this->isRequired() && empty( $value ) )
		{
			return $this->setError( JText::_( 'PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_REQUIRED' ) );
		}

		if( !empty( $value ) && $this->params->get( 'min' ) > 0 && JString::strlen( $value ) < $this->params->get( 'min' ) )
		{
			return $this->setError( JText::_( 'PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_TOO_SHORT' ) );
		}

		if( $this->params->get( 'max' ) > 0 && JString::strlen( $value ) > $this->params->get( 'max' ) )
		{
			return $this->setError( JText::_( 'PLG_FIELDS_TEXTAREA_VALIDATION_INPUT_TOO_LONG' ) );
		}

		return true;
	}

	public function onDisplay( $user )
	{
		$value 	= $this->value;

		if( !$value )
		{
			return;
		}

		if( !$this->allowedPrivacy( $user ) )
		{
			return;
		}

		$value = $this->escape( $value );

		// Push variables into theme.
		$this->set( 'value' , str_replace( "\n", "<br />", $value ) );

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
		return $this->validateInput($this->value);
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
	 * @param  array		$data	The post data.
	 * @param  SocialUser	$user	The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		return !empty($this->value);
	}
}
