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
 * Field application for boolean value
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserBoolean extends SocialFieldItem
{
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
		$value = $this->params->get( 'default' );

		$this->set( 'value', $value );

		return $this->display();
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
		$value 	= $this->value;

		if( $value == '' )
		{
			return;
		}

		if( !$this->allowedPrivacy( $user ) )
		{
			return;
		}

		// linkage to advanced search page.
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
		$this->set( 'value', $value );

		return $this->display();
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
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEdit( &$post, &$user, $errors )
	{
		// Get the value
		$value = !empty( $post[$this->inputName] ) ? $post[$this->inputName] : $this->value;

		// Set the value
		$this->set( 'value', $value );

		// Check for errors
		$error = $this->getError( $errors );

		// Set errors.
		$this->set( 'error', $error );

		// Display the output.
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
		// Check for post value
		$value 	= !empty( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : $this->params->get( 'default' );

		// Set value
		$this->set( 'value', $value );

		// Check for errors
		$error = $registration->getErrors( $this->inputName );

		// Set errors
		$this->set( 'error', $error );

		return $this->display();
	}
}
