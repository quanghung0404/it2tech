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

class EasySocialViewSubscriptions extends EasySocialSiteView
{

	public function toggle( $verb )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		// update the new label
		$label	= $verb == 'follow' ? JText::_( 'COM_EASYSOCIAL_SUBSCRIPTION_UNFOLLOW' ) : JText::_( 'COM_EASYSOCIAL_SUBSCRIPTION_FOLLOW' );
		$html 	= $verb == 'follow' ? JText::_( 'COM_EASYSOCIAL_SUBSCRIPTION_YOU_NOW_FOLLOW' ) : JText::_( 'COM_EASYSOCIAL_SUBSCRIPTION_YOU_NO_LONGER_FOLLOW' );

		return $ajax->resolve( $html , $label );

	}



	/**
	 * Allows a user to follow an object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function follow( $subscription )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	/**
	 * Allows a user to follow an object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfollow()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}


	public function form( $state = null )
	{
		$contentId		= JRequest::getVar( 'contentId' );
		$contentType	= JRequest::getVar( 'contentType' );

		$return  = new stdClass();
		$return->message    = '';

		if( $state )
		{
			$return->message   = JText::_('COM_EASYSOCIAL_SUBSCRIPTION_YOU_ALREADY_SUBSCRIBED');
			FD::getInstance( 'AJAX' )->success( $return );
			return;
		}


        $html	= FD::get( 'Themes' )
        			->set( 'contentId', $contentId )
        			->set( 'contentType', $contentType )
					->output( 'site/subscriptions/form' );

		$return->htmlform   = $html;

		FD::getInstance( 'AJAX' )->success( $return );
	}


	public function add( $state )
	{

		$return  = new stdClass();
		$return->message    = JText::_('COM_EASYSOCIAL_SUBSCRIPTION_SUCCESSFULLY_SUBSCRIBED');

		FD::getInstance( 'AJAX' )->success( $return );
	}


	public function remove()
	{

		$return  = new stdClass();
		$return->message    = JText::_('COM_EASYSOCIAL_SUBSCRIPTION_SUCCESSFULLY_UNSUBSCRIBED');

		$errors	= $this->getErrors();

		if( count( $errors ) > 0 )
		{
			$return->message = $errors[0];
		}

		FD::getInstance( 'AJAX' )->success( $return );

	}

}
