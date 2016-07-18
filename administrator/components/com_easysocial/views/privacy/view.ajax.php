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

// Include parent view
FD::import( 'admin:/views/views' );

class EasySocialViewPrivacy extends EasySocialAdminView
{
	/**
	 * Display dialog for confirming deletion
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		$ajax 	= FD::ajax();

		$theme	= FD::themes();
		$contents 	= $theme->output( 'admin/privacy/dialog.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Sends back the list of files to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function discoverFiles( $files = array() )
	{
		$ajax 	= FD::ajax();

		$ajax->resolve( $files );
	}

	/**
	 * Processes ajax calls to scan rules.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function scan( $obj )
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $obj );
	}
}
