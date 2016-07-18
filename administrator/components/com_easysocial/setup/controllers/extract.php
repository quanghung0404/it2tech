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

class EasySocialControllerExtract
{
	/**
	 * Validates an API key
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function extract()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$file = $input->get('file', '', 'default');

		$state 	= true;
		$result = json_encode($state);

		header('Content-type: text/x-json; UTF-8');
		echo $result;
		exit;
	}

	/**
	 * Downloads the file from the server
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function download()
	{
		// Check the api key from the request
		$apiKey 	= JRequest::getVar( 'apikey' , '' );

		// @TODO: Request the server to download the file
		$ch 		= curl_init( ES_SERVER . '/com_easysocial_v1.0.zip' );
		curl_setopt( $ch , CURLOPT_RETURNTRANSFER , true );

		// Get the response of the server
		$data 	= curl_exec( $ch );

		// Close the connection
		curl_close( $ch );

		// Check if the packages folder exists. If it doesn't create it.
		if(!JFolder::exists( ES_PACKAGES ) )
		{
			JFolder::create( ES_PACKAGES );
		}

		$state 	= JFile::write( ES_PACKAGES . '/com_easysocial_v1.0.zip' , $data );

		$json 	= new Services_JSON();

		$result = $json->encode( $state );

		header('Content-type: text/x-json; UTF-8');
		echo $result;
		exit;
	}
}
