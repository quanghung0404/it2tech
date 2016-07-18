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

class EasySocialViewMigrators extends EasySocialAdminView
{

	/**
	 * Sends back the list of files to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function check( $obj )
	{
		$ajax 	= FD::ajax();

		$ajax->resolve( $obj );
	}

	/**
	 * Sends back the list of files to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process( $obj, $updateConfig = 0 )
	{
		$ajax 	= FD::ajax();

		$ajax->resolve( $obj, $updateConfig );
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

	public function confirmMigration()
	{
		$ajax 	= FD::ajax();

		$theme 		= FD::themes();
		$contents	= $theme->output( 'admin/migrators/dialog.confirm' );

		return $ajax->resolve( $contents );
	}

	public function confirmPurge()
	{
		$ajax 	= FD::ajax();

		$type = $this->input->get('type', '', 'default');

		$theme 		= FD::themes();
		$contents	= $theme->output( 'admin/migrators/dialog.confirm.purge' );

		return $ajax->resolve( $contents );
	}

}
