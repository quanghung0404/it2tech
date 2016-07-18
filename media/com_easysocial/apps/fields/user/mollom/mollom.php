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
 * Field application for Mollom
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserMollom extends SocialFieldItem
{
	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Determines if the user has already validated with recaptcha
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasValidated( &$post )
	{
		$validated = isset($post[$this->inputName]) ? $post[$this->inputName] : false;

		return $validated;
	}

	/**
	 * Validates the captcha data
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateCaptcha(&$post)
	{
		$validated = $this->hasValidated($post);

		if (!$this->field->isRequired() || $this->hasValidated($post)) {
			return true;
		}

		// Get mollom lib
		$captcha = $this->getMollom();

		if (!$captcha) {
			return;
		}

		// Get the necessary data from mollom
		$sessionId = isset($post['mollom_session_id']) ? $post['mollom_session_id'] : '';
		$response = isset($post['mollom_' . $this->inputName]) ? $post['mollom_' . $this->inputName] : '';

		if (empty($response)) {
			return $this->setError(JText::_('PLG_FIELDS_MOLLOM_VALIDATION_PLEASE_ENTER_CAPTCHA_RESPONSE'));
		}

		// Let's try to validate the response.
		$state = $captcha->checkAnswer($sessionId, $response);

		if (!$state) {
			return $this->setError(JText::_('PLG_FIELDS_MOLLOM_VALIDATION_INVALID_RESPONSE'));
		}

		// Set a valid response to the registration object.
		$post[$this->inputName] = true;

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
	 */
	public function onEditValidate(&$post)
	{
		return $this->validateCaptcha($post);
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
		return $this->validateCaptcha($post);
	}

	/**
	 * Determines if mollom has been configured
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	private function isCaptchaConfigured()
	{
		$params 	= $this->field->getApp()->getParams();
		$private 	= $params->get( 'private' );
		$public 	= $params->get( 'public' );

		if( !empty( $private ) && !empty( $public ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the mollom lib
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMollom()
	{
		$app		= $this->field->getApp();
		$params 	= $app->getParams();

		$options 	= array(
							'public'	=> $params->get( 'public' ),
							'private'	=> $params->get( 'private' )
						);


		if( empty( $options[ 'public' ] ) || empty( $options[ 'private' ] ) )
		{
			return false;
		}

		$captcha	= FD::get( 'Captcha' , 'Mollom' , $options );

		return $captcha;
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
		// Check if recaptcha has been configured
		if( !$this->isCaptchaConfigured() )
		{
			return;
		}

		if( $this->hasValidated( $post ) )
		{
			return;
		}

		// Check for errors
		$error		= $registration->getErrors( $this->inputName );
		$captcha	= $this->getMollom();

		if( !$captcha )
		{
			return;
		}

		$this->set( 'error'	, $error );
		$this->set( 'captcha' , $captcha );

		// Display the output.
		return $this->display();
	}

	/**
	 * Displays the field input for user when they edit their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEdit( &$post, &$user, $errors )
	{
		// Check if recaptcha has been configured
		if( !$this->isCaptchaConfigured() )
		{
			return;
		}

		if( $this->hasValidated( $post ) )
		{
			return;
		}

		// Check for errors
		$error = $this->getError( $errors );

		// Get the captcha library.
		$captcha 	= $this->getMollom();

		if( !$captcha )
		{
			return;
		}

		// Output to the template
		$this->set( 'captcha'	, $captcha );
		$this->set( 'error'		, $error );

		// Display the output.
		return $this->display();
	}

	/**
	 * Displays sample mollom code
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onSample()
	{
		$captcha 	= $this->getMollom();

		if( $captcha )
		{
			$this->set( 'captcha'	, $captcha );
		}

		return $this->display();
	}
}
