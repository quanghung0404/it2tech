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

class EasySocialViewOauth extends EasySocialSiteView
{
	function display( $tpl = null )
	{
		parent::display( 'site.registration.default' );
	}

	function register( $tpl = null )
	{
	    parent::display( 'site.oauth.registration_facebook' );
	}

	/**
	 * Displays the revoke confirmation dialog.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRevoke()
	{
		// Only required users allowed here.
		FD::requireLogin();

		$ajax 		= FD::ajax();

		// Get client and callback
		$client		= JRequest::getWord( 'client' );
		$callback	= JRequest::getVar( 'callbackUrl' );

		$theme 	= FD::themes();
		$theme->set( 'callback' , $callback );
		$theme->set( 'client'	, $client );

		$contents 	= $theme->output( 'site/' . $client . '/revoke.dialog' );

		return $ajax->resolve( $contents );
	}
}
