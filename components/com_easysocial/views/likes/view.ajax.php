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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/views/views' );

class EasySocialViewLikes extends EasySocialSiteView
{
	/**
	 * Returns an ajax chain.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The verb that we have performed.
	 */
	public function toggle( $verb = '' , $id = null , $type = null, $group = SOCIAL_APPS_GROUP_USER, $itemVerb = '' )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		// Set the label
		$label	= $verb == 'like' ? JText::_( 'COM_EASYSOCIAL_LIKES_UNLIKE' ) : JText::_( 'COM_EASYSOCIAL_LIKES_LIKE' );

		// Set the message
		$likes 		= FD::likes( $id , $type, $itemVerb, $group );
		$likes->get( $id , $type, $itemVerb, $group );

		$likeCnt	= count( $likes->data );

		$isHidden	= ( $likeCnt > 0 ) ? false : true;

		$contents 	= $likes->toString();

		return $ajax->resolve( $contents , $label, $isHidden, $verb, $likeCnt );
	}


	/**
	 * Returns an ajax chain.
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 */
	public function showOthers( $users )
	{
		$ajax	= FD::ajax();
		$html 	= '';

		// Get user list
		$theme 		= FD::get( 'Themes' );
		$theme->set( 'users', $users );
		$html 		= $theme->output( 'site/users/simplelist' );

		return $ajax->resolve( $html );
	}
}
