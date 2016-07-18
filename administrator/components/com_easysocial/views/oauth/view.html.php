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

FD::import( 'admin:/views/views' );

class EasySocialViewOAuth extends EasySocialAdminView
{
	/**
	 * Post processing after a user revokes the access.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revoke( $callback )
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( $callback );
	}

	/**
	 * Post processing after a user grants access to the app.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function grant( $callback )
	{
		FD::info()->set( $this->getMessage() );

		if( empty( $callback ) )
		{
			// Do our own standard procedure when callbacks are not provided.
		}
		$this->redirect( $callback );
	}
}
