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

class EasySocialViewSettings extends EasySocialAdminView
{
	/**
	 * Displays dialog to confirm reset settings
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmReset()
	{
		$ajax 		= FD::ajax();

		$section	= JRequest::getVar( 'section' );

		$theme		= FD::themes();
		$theme->set( 'section' , $section );
		$contents 	= $theme->output( 'admin/settings/dialog.reset' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Allows user to import a .json file
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function import()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();
		$page 		= JRequest::getVar( 'page' );

		$theme->set( 'page' , $page );
		$contents	= $theme->output( 'admin/settings/dialog.import' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the amazon settings form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function amazon()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();

		$contents	= $theme->output( 'admin/settings/forms/dialog.storage.amazon' );

		$ajax->resolve( $contents );
	}
}
