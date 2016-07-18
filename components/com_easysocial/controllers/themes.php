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

jimport( 'joomla.application.component.controller' );

class EasySocialControllerThemes extends JController
{
	public function getAjaxTemplate()
	{
		$templateFiles		= JRequest::getVar( 'names' );

		// Ensure the integrity of each items submitted to be an array.
		if( !is_array( $templateFiles ) )
		{
			$templateFiles	= array( $templateFiles );
		}

		$result		= array();
		$theme		= FD::get( 'Themes' );

		foreach( $templateFiles as $file )
		{
			// Remove any trailing .ejs in file name if exist.
			$file		= str_replace('.ejs', '', $file);

			$template	= $theme->getTemplate( $file );

			ob_start();
			include( $template->ejs );
			$output 	= ob_get_contents();
			ob_end_clean();

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $output;

			$result[]		= $obj;
		}

		if( !$result )
		{
			header('HTTP/1.1 404 Not Found');
			exit;
		}

		header('Content-type: text/x-json; UTF-8');
		$json 	= FD::json();
		echo $json->encode( $result );
		exit;
	}


	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown()
	{
		return false;
	}
}
