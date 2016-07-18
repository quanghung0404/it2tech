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

// Include helper lib
require_once( dirname( __FILE__ ) . '/helper.php' );

/**
 * Field application for Joomla language
 *
 * @since	1.2
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialFieldsUserJoomla_language extends SocialFieldItem
{
	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function onRegister( &$post , &$registration )
	{
		// Get value.
		$value = !empty( $post[$this->inputName] ) ? $post[$this->inputName] : '';

		$showSubname = $this->params->get('show_subname');

		// Get available languages
		$languages 	= SocialLanguageHelper::getLanguages('', $showSubname);

		// Check for errors.
		$error		= $registration->getErrors( $this->inputName );

		$this->set( 'value'		, $value );
		$this->set( 'languages'	, $languages );
		$this->set( 'error' 	, $error );

		// Output the registration template.
		return $this->display();
	}

	/**
	 * Save trigger which is called before really saving the object.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	array
	 * @param	SocialUser	The user object.
	 * @return	bool	The state of the trigger
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function onRegisterBeforeSave( &$post , &$user )
	{
		$value 		= isset( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : '';

		$user->setParam( 'language' , $value );

		// Remove this from the index
		unset( $post[$this->inputName] );

		return true;
	}

	/**
	 * Displays the field input for user when they edit their account.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function onEdit( &$post, &$user, $errors )
	{
		// Get the params
		$value 	= $user->getParam( 'language' , '' );

		if( !empty( $post[ $this->inputName ] ) )
		{
			$value = $post[ $this->inputName ];
		}

		// Check for errors.
		$error		= $this->getError( $errors );

		$showSubname = $this->params->get('show_subname');

		// Get available languages
		$languages 	= SocialLanguageHelper::getLanguages('', $showSubname);

		$this->set( 'value'		, $value );
		$this->set( 'languages'	, $languages );
		$this->set( 'error' 	, $error );

		// Output the edit template.
		return $this->display();
	}

	/**
	 * Save trigger which is called before really saving the object.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	array
	 * @param	SocialUser	The user object.
	 * @return	bool	The state of the trigger
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function onEditBeforeSave( &$post , &$user )
	{
		$value 		= isset( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : '';

		$user->setParam( 'language' , $value );

		// Remove this from the index
		unset( $post[$this->inputName] );

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
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function onSample()
	{
		return $this->display();
	}
}
