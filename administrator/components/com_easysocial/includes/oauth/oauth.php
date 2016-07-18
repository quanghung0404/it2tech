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

require_once( dirname( __FILE__ ) . '/dependencies.php' );

class SocialOauth
{
	static $clients 	= array();

	/**
	 * The current oauth client.
	 * @var	SocialConsumer
	 */
	private $client 	= null;

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * FD::get( 'OAuth' , 'facebook' );
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The oauth client's name. E.g: facebook , twitter.
	 * @param	string	A valid callback url.
	 */
	public static function getInstance( $client , $callback = '' )
	{
		if( !isset( self::$clients[ $client ] ) )
		{
			self::$clients[ $client ]	= new self( $client , $callback );
		}

		return self::$clients[ $client ];
	}

	public function __construct( $client , $callback = '' )
	{
		// Get the path to the consumer file.
		$file 	= dirname( __FILE__ ) . '/clients/' . strtolower( $client ) . '/consumer.php';

		jimport( 'joomla.filesystem.file' );

		// If file doesn't exist, just quit.
		if( !JFile::exists( $file ) )
		{
			return false;
		}

		if( empty( $callback ) )
		{
			$callback   = rtrim( JURI::root() , '/' ) . JRoute::_( 'index.php?option=com_easysocial&controller=oauth&task=grant&client=' . $client , false);
		}

		require_once( $file );

		// All adapters classes should have the same naming convention
		$consumerClass  = 'SocialConsumer' . ucfirst( $client );

		if( !class_exists( $consumerClass ) )
		{
			return false;
		}

		$config	= FD::config();

		// All oauth clients should have a key and secret.
		$key 	= $config->get( 'oauth.' . strtolower( $client ) . '.app' );
		$secret = $config->get( 'oauth.' . strtolower( $client ) . '.secret' );

		// Let's try to create instance of consumer.
		$this->client 	= new $consumerClass( $key , $secret , $callback );
	}

	/**
	 * Maps back the call method functions to the helper.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	Method's name.
	 * @param	mixed	Arguments
	 * @return
	 */
	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}
		return call_user_func_array( array( $this->client , $method ) , $refArray );
	}

	/**
	 * Logs the user into the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function login()
	{
		// Try to log the user in.
		$app 			= JFactory::getApplication();
		$credentials 	= $this->getLoginCredentials();

		// dump($credentials);
		// Try to log the user in.
		return $app->login( $credentials );
	}
}
