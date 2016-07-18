<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport( 'joomla.application.component.controller' );

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerLang extends EasySocialController
{
	/**
	 * Allows a javascript caller to load a language string via javascript.
	 *
	 * @since 	1.0
	 * @access	public
	 * @param	null
	 * @return	JSON
	 */
	public function getLanguage()
	{
		$languages		= JRequest::getVar( 'languages' );
		$result 		= array();

		// If this is not an array, make it as an array.
		if( !is_array( $languages ) )
		{
			$languages	= array($languages);
		}

		// Load language support for front end and back end.
		JFactory::getLanguage()->load( JPATH_ROOT . '/administrator' );
		JFactory::getLanguage()->load( JPATH_ROOT );

		foreach( $languages as $key )
		{
			$result[ $key ]	= JText::_( strtoupper( $key ) );
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


}
