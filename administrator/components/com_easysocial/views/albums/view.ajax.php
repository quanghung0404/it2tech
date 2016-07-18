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

class EasySocialViewAlbums extends EasySocialAdminView
{
	/**
	 * Assign users into group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function browse()
	{
		$ajax 		= FD::ajax();

		$callback	= JRequest::getWord( 'jscallback' );

		$theme 	= FD::themes();
		$theme->set( 'callback' , $callback );

		$output = $theme->output( 'admin/albums/dialog.browse' );

		return $ajax->resolve( $output );
	}

	/**
	 * Displays confirmation before deleting an album
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$ajax	= FD::ajax();

		$theme	= FD::themes();


		$output	= $theme->output( 'admin/albums/dialog.delete' );

		return $ajax->resolve( $output );
	}
}
