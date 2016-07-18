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
defined('_JEXEC') or die('Unauthorized Access');

// Extend from user boolean
FD::import('fields:/user/boolean/boolean');

class SocialFieldsGroupDiscussions extends SocialFieldsUserBoolean
{
	/**
	 *
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegisterBeforeSave( &$post , SocialGroup $group )
	{
		// Get the posted value
		$value 	= isset( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : $this->params->get('default', true);
		$value 	= (bool) $value;

		$registry	= $group->getParams();
		$registry->set( 'discussions' , $value );

		$group->params 	= $registry->toString();
	}

	/**
	 * Override the editing of the title since the value is different
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegister( &$post , &$group )
	{
		$value 	= isset( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : $this->params->get('default', true);

		// Set the value.
		$this->set( 'value'	, $this->escape( $value ) );

		return $this->display();
	}

	/**
	 * Override the editing of the title since the value is different
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEdit( &$post, &$group , $errors )
	{
		// The value will always be the group title
		$params 	= $group->getParams();

		// Get the real value for this item
		$value 		= $params->get('discussions', $this->params->get('default', true));

		// Get the error.
		$error = $this->getError( $errors );

		// Set the value.
		$this->set( 'value'	, $this->escape( $value ) );
		$this->set( 'error'	, $error );

		return $this->display();
	}

	/**
	 * Override the editing of the news value
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditBeforeSave( &$post, &$group )
	{
		// Get the posted value
		$value 	= isset( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : $params->get('discussions', $this->params->get('default', true));
		$value 	= (bool) $value;

		$registry	= $group->getParams();
		$registry->set( 'discussions' , $value );

		$group->params 	= $registry->toString();
	}

	/**
	 * Override the parent's onDisplay
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onDisplay( $group )
	{
		return;
	}
}
