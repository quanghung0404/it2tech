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

// Include the main views class
FD::import( 'admin:/views/views' );

class EasySocialViewReports extends EasySocialAdminView
{
	/**
	 * Requests confirmation before purging reports
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmPurge()
	{
		$ajax = FD::ajax();

		// Get dialog
		$theme = FD::themes();
		$html = $theme->output( 'admin/reports/dialog.purge' );

		return $ajax->resolve( $html );
	}

	/**
	 * Display confirmation before deleting a report
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();

		$output	= $theme->output( 'admin/reports/dialog.delete' );

		return $ajax->resolve( $output );
	}

	/**
	 * List reporters
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array
	 */
	public function getReporters( $reporters = array() )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme 	= FD::themes();
		$theme->set( 'reporters' , $reporters );

		$contents 	= $theme->output( 'admin/reports/default.reporters' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Post processing after a report has been deleted.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeItem()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $this->getMessage() );
	}
}
