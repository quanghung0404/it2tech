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

// Include main views file.
FD::import( 'admin:/views/views' );

class EasySocialViewIndexer extends EasySocialAdminView
{


	public function indexing( $max, $progress )
	{
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve( $max, $progress );
	}




	/**
	 * List apps from the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getApps( $items = array() )
	{
		$ajax 	= FD::getInstance( 'Ajax' );
		$ajax->resolve( $items );
	}
}
