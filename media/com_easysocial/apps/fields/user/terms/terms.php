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
FD::import( 'admin:/includes/fields/dependencies' );

/**
 * Field application for Gender
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserTerms extends SocialFieldItem
{
	/**
	 * Performs validation for the gender field.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate( &$post , $key = null )
	{
		$key 	= is_null( $key ) ? $this->inputName : $key;

		// Get the current value
		$value 	= isset( $post[ $key ] ) ? $post[ $key ] : '';

		// Catch for errors if this is a required field.
		if( $this->isRequired() && empty( $value ) )
		{
			return $this->setError( JText::_( 'PLG_FIELDS_TERMS_VALIDATION_PLEASE_ACCEPT_TERMS' ) );
		}

		$post[ $this->inputName ]	= $value;

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
	public function onRegister(&$post, &$registration)
	{
		// Get the default value.
		$value 		= '';

		// If the value exists in the post data, it means that the user had previously set some values.
		if (isset($post[$this->inputName]) && !empty($post[$this->inputName])) {
			$value 	= $post[ $this->inputName ];
		}

		// Detect if there's any errors.
		$error 	= $registration->getErrors($this->inputName);

		// Get field params
		$params = $this->getParams();

		$this->set('error', $error);
		$this->set('value', $value);

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
	public function onRegisterValidate( &$post,  &$registration )
	{
		$state 	= $this->validate( $post );

		return $state;
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
		// Get the current value
		$value 	= $this->value;

		// Determines if there's any errors should be displayed
		$error	= $this->getError($errors);

		$this->set('value', $value);
		$this->set('error', $error);

		return $this->display();
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
	public function onEditValidate( &$post )
	{
		$state 	= $this->validate( $post );

		return $state;
	}

	public function onAdminEditValidate(&$post)
	{
		// Admin doesn't need terms validation
		return true;
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
		$this->set('value', false);

		return $this->display();
	}
}
