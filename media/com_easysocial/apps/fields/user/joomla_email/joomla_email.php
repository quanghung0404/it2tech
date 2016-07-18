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
FD::import( 'fields:/user/joomla_email/helper' );

/**
 * Field application for Joomla email
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserJoomla_email extends SocialFieldItem
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
	public function onRegister( &$post , &$registration )
	{
		// Try to check to see if user has already set the username.
		$email	= !empty( $post[ 'email' ] ) ? $post[ 'email' ] : '';

		// Set the username property for the theme.
		$this->set( 'email'	, $this->escape( $email ) );

		// Detect if there's any errors.
		$error 	= $registration->getErrors( $this->inputName );

		$this->set( 'error'	, $error );

		$this->set( 'userid', null );

		$this->set( 'showConfirmation', true );

		// Output the registration template.
		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the normal form.
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
		$post['email'] = !empty( $post['email'] ) ? trim( $post['email'] ) : '';

		return $this->validateEmail( $post );
	}

	public function onRegisterMiniValidate(&$post)
	{
		// if (empty($post['email-reconfirm']) && !empty($post['email'])) {
		// 	$post['email-reconfirm'] = $post['email'];
		// }

		$post['email'] = !empty( $post['email'] ) ? trim( $post['email'] ) : '';

		// Check if reconfirm passwords is disabled.
		if (!$this->params->get('mini_reconfirm_email') && isset($post['email'])) {
			$post['email-reconfirm'] = $post['email'];
		}

		return $this->validateEmail($post);
	}

	/**
	 * Save trigger before user object is saved
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialUser	The user object.
	 * @return	bool	State of the trigger
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterBeforeSave( &$post, &$user )
	{
		// Set the email address into the user object
		$user->set( 'email', $post['email'] );

		$config = FD::config();

		// If settings is set to use email as username, then we parse the email through to username
		if ($config->get('registrations.emailasusername')) {
			$post['username'] = $post['email'];
		}

		// Unset the email address from the post data
		unset($post['email']);

		return true;
	}

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser	The user object that is being edited.
	 * @param	Array		The posted data.
	 * @param	Array		An array consisting of errors.
	 */
	public function onEdit( &$post, &$user, $errors )
	{
		$email 	= isset( $post[ 'email' ] ) ? $post[ 'email' ] : $user->email;

		$this->set( 'email', $this->escape( $email ) );

		// Determine if the error state should appear.
		$error = $this->getError( $errors );
		$this->set( 'error' , $error );

		$this->set( 'userid', $user->id );

		$this->set( 'showConfirmation', false );

		return $this->display();
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
	public function onEditValidate( &$post, &$user )
	{
		$post['email'] = !empty( $post['email'] ) ? trim( $post['email'] ) : '';

		return $this->validateEmail( $post , $user->email );
	}

	/**
	 * Save trigger before user object is saved
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialUser	The user object.
	 * @return	bool	State of the trigger
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditBeforeSave( &$post, &$user )
	{
		// Set the email address into the user object
		$user->set( 'email', $post['email'] );

		// Don't mess with the username here regardless of the settings registrations.emailasusername
		// For adminedit on new user, see onAdminEditBeforeSave instead

		// Unset the email address from the post data
		unset( $post['email'] );

		return true;
	}

	public function onAdminEditBeforeSave(&$post, &$user)
	{
		// Set the email address into the user object
		$user->set( 'email', $post['email'] );

		// We don't change the user name is the user is an existing user
		// It is possible that admin is creating a new user
		// In that case we check if the user is a new user, and if the username is empty because it is also possible that username is assigned in backend regardless of the settings
		if(empty($user->id) && empty($post['username']) && FD::config()->get( 'registrations.emailasusername' ))
		{
			$post['username'] = $post['email'];
		}

		// Unset the email address from the post data
		unset( $post['email'] );

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
		return $this->display();
	}

	/**
	 * Validates the posted email
	 *
	 * @since	1.0
	 * @access	private
	 * @param	string
	 * @return	bool		True if valid, false otherwise.
	 */
	private function validateEmail( &$post , $currentEmail = '' )
	{
		$email  = !empty( $post['email'] ) ? trim( $post['email'] ) : '';

		$confirm = !empty( $post['email-reconfirm'] ) ? trim( $post['email-reconfirm'] ) : '';

		if ($this->isRequired() && empty($email)) {
			$this->setError(JText::_('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_REQUIRED'));
			return false;
		}

		// Check against regex
		if (!empty($email) && $this->params->get('regex_validate')) {
			$format = $this->params->get('regex_format');
			$modifier = $this->params->get('regex_modifier');

			$pattern = '/' . $format . '/' . $modifier;

			$result = preg_match($pattern, $email);

			if (empty($result)) {
				$this->setError(JText::_('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_INVALID_FORMAT'));
				return false;
			}
		}

		// Check for email validity
		if( !SocialFieldsUserJoomlaEmailHelper::isValid( $email ) )
		{
			$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_INVALID_EMAIL' ) );
			return false;
		}

		// Check for allowed domains
		if( !SocialFieldsUserJoomlaEmailHelper::isAllowed( $email, $this->params ) )
		{
			$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_DOMAIN_IS_NOT_ALLOWED' ) );
			return false;
		}

		// Check for disallowed domains
		if( SocialFieldsUserJoomlaEmailHelper::isDisallowed( $email , $this->params ) )
		{
			$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_DOMAIN_IS_DISALLOWED' ) );
			return false;
		}

		// Check for forbidden words
		if( SocialFieldsUserJoomlaEmailHelper::isForbidden( $email , $this->params ) )
		{
			$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_CONTAINS_FORBIDDEN' ) );
			return false;
		}

		// Check if current email exist
		if( SocialFieldsUserJoomlaEmailHelper::exists( $email , $currentEmail ) )
		{
			$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_ALREADY_USED' ) );
			return false;
		}

		// Check reconfirm
		if ($this->params->get('reconfirm_email')) {
			if( $email !== $currentEmail && empty( $confirm ) )
			{
				$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_RECONFIRM_REQUIRED' ) );
				return false;
			}

			if( !empty( $confirm ) && $email !== $confirm )
			{
				$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_NOT_MATCHING' ) );
				return false;
			}
		}

		return true;
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
	 * return formated string from the fields value
	 *
	 * @since	1.0
	 * @access	public
	 * @param	userfielddata
	 * @return	array array of objects with two attribute, ffriend_id, score
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onIndexerSearch( $itemCreatorId, $keywords, $userFieldData )
	{
		if(! $this->field->searchable )
			return false;

		$data 		= trim( $userFieldData );

		$content 			= '';
		if( JString::stristr( $data, $keywords ) !== false )
		{
			$content = $data;
		}

		if( $content )
		{
			$my = FD::user();
			$privacyLib = FD::privacy( $my->id );

			if( ! $privacyLib->validate( 'core.view', $this->field->id, SOCIAL_TYPE_FIELD, $itemCreatorId ) )
			{
				return -1;
			}
			else
			{
				// okay this mean the user can view this fields. let hightlight the content.

				// building the pattern for regex replace
				$searchworda	= preg_replace('#\xE3\x80\x80#s', ' ', $keywords);
				$searchwords	= preg_split("/\s+/u", $searchworda);
				$needle			= $searchwords[0];
				$searchwords	= array_unique($searchwords);

				$pattern	= '#(';
				$x 			= 0;

				foreach ($searchwords as $k => $hlword)
				{
					$pattern 	.= $x == 0 ? '' : '|';
					$pattern	.= preg_quote( $hlword , '#' );
					$x++;
				}
				$pattern 		.= ')#iu';

				$content 	= preg_replace( $pattern , '<span class="search-highlight">\0</span>' , $content );
				$content 	= JText::sprintf( 'PLG_FIELDS_JOOMLA_EMAIL_SEARCH_RESULT', $content );
			}
		}

		if( $content )
			return $content;
		else
			return false;
	}

	public function onDisplay( $user )
	{
		if( !$this->allowedPrivacy( $user ) )
		{
			return;
		}

		$this->set( 'email', $this->escape( $user->email ) );

		return $this->display();
	}

	public function onOAuthGetUserPermission( &$permissions )
	{
		$permissions[] = 'email';
	}

	public function onOAuthGetMetaFields( &$fields )
	{
		$fields[] = 'email';
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
		if (empty($user->email)) {
			$this->setError(JText::_('PLG_FIELDS_JOOMLA_EMAIL_VALIDATION_REQUIRED'));
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
	 * @param  array		$data	The post data.
	 * @param  SocialUser	$user	The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		return !empty($user->email);
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

		$email = $user->email;

		$container->raw = $email;
		$container->data = $email;
		$container->value = $email;

		return $container;
	}
}
