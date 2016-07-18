<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.database.driver');
jimport('joomla.installer.helper');

class EasySocialSetupController
{
	private $result 	= array();

	protected function data( $key , $value )
	{
		$obj 		= new stdClass();
		$obj->$key	= $value;

		$this->result[] 	= $obj;
	}

	public function output( $data = array() )
	{
		header('Content-type: text/x-json; UTF-8');

		if( empty( $data ) )
		{
			$data 	= $this->result;
		}

		echo json_encode( $data );
		exit;
	}

	public function getResultObj( $message , $state , $stateMessage = '' )
	{
		$obj 			= new stdClass();
		$obj->state		= $state;
		$obj->stateMessage	= $stateMessage;
		$obj->message 	= $message;

		return $obj;
	}

	/**
	 * Get's the version of this launcher so we know which to install
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function getVersion()
	{
		static $version = null;

		if( is_null( $version ) )
		{
			// Get the version from the manifest file
			$contents 	= JFile::read( JPATH_ROOT . '/administrator/components/com_easysocial/easysocial.xml' );
			$parser 	= simplexml_load_string( $contents );
			$version 	= $parser->xpath( 'version' );
			$version 	= (string) $version[ 0 ];
		}

		return $version;
	}

	/**
	 * Gets the info about the latest version
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getInfo( $update = false )
	{
		// Get the md5 hash from the server.
		$resource 	= curl_init();

		// If this is an update, we want to tell the server that this is being updated from which version
		if( $update )
		{
			$version	= $this->getVersion();

			// We need to pass the api keys to the server
			curl_setopt( $resource , CURLOPT_POST , true );
			curl_setopt( $resource , CURLOPT_POSTFIELDS , 'from=' . $version );
		}
		else
		{
			$version	= $this->getVersion();

			// We need to pass the api keys to the server
			curl_setopt( $resource , CURLOPT_POST , true );
			curl_setopt( $resource , CURLOPT_POSTFIELDS , 'version=' . $version );
		}

		curl_setopt( $resource , CURLOPT_URL , ES_MANIFEST );
		curl_setopt( $resource , CURLOPT_TIMEOUT , 120 );
		curl_setopt( $resource , CURLOPT_RETURNTRANSFER , true );

		$result 	= curl_exec( $resource );
		curl_close( $resource );

		if( !$result )
		{
			return false;
		}

		$obj 	= json_decode( $result );

		return $obj;
	}

	/**
	 * Loads up the foundry library if it exists
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function foundry()
	{
		$lib 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

		if( !JFile::exists( $lib ) )
		{
			return false;
		}

		// Include foundry framework
		require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
	}

	/**
	 * Loads the previous version that was installed
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The key to save
	 * @param	mixed	The data to save
	 * @return
	 */
	public function getInstalledVersion()
	{
		$this->foundry();

		$path 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/easysocial.xml';

		$parser	= FD::get( 'Parser' );
		$parser->load( $path );

		$version	= $parser->xpath( 'version' );
		$version	= (string) $version[ 0 ];

		return $version;
	}

	/**
	 * get a configuration item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The key of the version type
	 * @return
	 */
	public function getPreviousVersion( $versionType )
	{
		$this->foundry();

		$config	= FD::table( 'Config' );
		$config->load( array( 'type' => $versionType ) );

		return $config->value;
	}

	/**
	 * Determines if we are in development mode
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isDevelopment()
	{
		$session 	= JFactory::getSession();
		$developer	= $session->get( 'easysocial.developer' );

		return $developer;
	}
}

