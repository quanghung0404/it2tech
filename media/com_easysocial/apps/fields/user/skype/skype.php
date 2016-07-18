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

/**
 * Field application for Skype
 *
 * @since	1.3
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialFieldsUserSkype extends SocialFieldItem
{
	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( $options )
	{
		parent::__construct( $options );
	}

	/**
	 * Displays the field input for user when they edit their account
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function onEdit( &$post, &$registration, $errors)
	{
		// Get the value.
		$value 	= !empty($post[ $this->inputName ]) ? $post[ $this->inputName ] : $this->value;

		// Get the error.
		$error = $this->getError($errors);

		// Set the value.
		$this->set('value'	, $this->escape($value));
		$this->set('error'	, $error);

		return $this->display();
	}

	/**
	 *
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onDisplay($user)
	{
		$value 	= $this->value;

		if (!$value) {
			return;
		}

		if (!$this->allowedPrivacy($user)) {
			return;
		}

		// If both is not enabled, we shouldn't do anything but just display text
		$type = '';

		if ($this->params->get('call') && $this->params->get('chat')) {
			$type = 'dropdown';
		}

		// Chat only
		if ($this->params->get('chat') && !$this->params->get('call')) {
			$type = 'chat';
		}

		// Call only
		if ($this->params->get('call') && !$this->params->get('chat')) {
			$type = 'call';
		}

		// Push variables into theme.
		$this->set('type', $type);
		$this->set('user', $user);
		$this->set('value', $this->escape($value));

		return $this->display();
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
	public function onRegister( &$post, &$registration )
	{
		// Get the default value.
		$value 		= '';

		// If the value exists in the post data, it means that the user had previously set some values.
		if( isset( $post[ $this->inputName ] ) && !empty( $post[ $this->inputName ] ) )
		{
			$value 	= $post[ $this->inputName ];
		}

		// Detect if there's any errors.
		$error 	= $registration->getErrors( $this->inputName );

		$this->set( 'error'		, $error );
		$this->set( 'value'		, $value );

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
