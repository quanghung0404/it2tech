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
 * Field application for URL
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserUrl extends SocialFieldItem
{
	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when their profile is viewed.
	 *
	 * @since	1.0
	 * @access	public
	 */
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

		// If there's no http:// or https:// , automatically append http://
		if( stristr( $value , 'http://' ) === false && stristr( $value , 'https://' ) === false )
		{
			$value 	= 'http://' . $value;
		}

		// Push vars to the theme
		$this->set( 'value' , $this->escape( $value ) );

		// linkage to advanced search page.
		$field = $this->field;
		// if ($field->type == SOCIAL_FIELDS_GROUP_USER && $field->searchable) {
		// 	$params = array( 'layout' => 'advanced' );
		// 	$params['criterias[]'] = $field->unique_key . '|' . $field->element;
		// 	$params['operators[]'] = 'contain';
		// 	$params['conditions[]'] = $this->escape($this->value);

		// 	$advsearchLink = FRoute::search($params);
		// 	$this->set( 'advancedsearchlink'	, $advsearchLink );
		// }

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
	public function onRegister( &$post , &$registration )
	{
		// Get the value.
		$value = !empty( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : JText::_( $this->params->get( 'default' ), true );

		// Set the value.
		$this->set( 'value'	, $this->escape( $value ) );

		// Get any errors for this field.
		$error		= $registration->getErrors( $this->inputName );

		// Set the error.
		$this->set( 'error'	, $error );

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
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterValidate( &$post )
	{
		// Selected value
		$value 		= !empty($post[ $this->inputName ]) ? $post[$this->inputName] : '';

		return $this->validateInput( $value );
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
	public function onEdit( &$post, &$user, $errors )
	{
		// Get the value.
		$value 	= !empty( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : $this->value;

		// Set the value.
		$this->set( 'value'	, $this->escape( $value ) );

		// Get the error.
		$error = $this->getError( $errors );

		// Set the error.
		$this->set( 'error'	, $error );

		return $this->display();
	}

	/**
	 * Validate input when user edit their profile.
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
		// Selected value
		$value 		= !empty($post[ $this->inputName ]) ? $post[$this->inputName] : '';

		return $this->validateInput( $value );
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
				$content 	= JText::sprintf( 'PLG_FIELDS_URL_SEARCH_RESULT', $content );
			}
		}

		if( $content )
			return $content;
		else
			return false;
	}




	/**
	 * General validation function
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The text value to validate
	 * @return	bool	The state of the validation
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	private function validateInput( $value )
	{
		// If this is required, check for the value.
		if( $this->isRequired() && empty( $value ) )
		{
			return $this->setError( JText::_( 'PLG_FIELDS_URL_VALIDATION_EMPTY_URL' ) );
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
