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

class EasySocialViewApps extends EasySocialAdminView
{
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

	/**
	 * Displays confirmation to uninstall apps
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmUninstall()
	{
		$ajax 	= FD::ajax();

		$theme 		= FD::themes();
		$contents	= $theme->output( 'admin/apps/dialog.uninstall' );

		$ajax->resolve( $contents );
	}
}
