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

require_once( dirname(__FILE__) . '/linkedin.php' );

class SocialConsumerLinkedIn extends LinkedIn
{
	var $callback		= '';
	var $_access_token	= '';
	private $uid        = '';

	public function __construct( $key , $secret , $callback )
	{

		$config 	= array( 'appKey' => $key , 'appSecret' => $secret , 'callbackUrl' => $callback );
		parent::__construct( $config );

		$this->callback	= $callback;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @return string	$verifier	Any string representation that we can verify it isn't empty.
	 **/
	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'oauth_verifier' , '' );
		return $verifier;
	}

	public function getAccess()
	{
		// Get the verifier code from query.
		$verifier 		= JRequest::getVar( 'oauth_verifier' );
		$token 			= JRequest::getVar( 'oauth_token' );
		$secret 		= $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'];

// dump( $token , $secret , $verifier );
		// Retrieve the access token.
		$accessToken	= $this->retrieveTokenAccess( $token , $secret , $verifier );

		$obj			= new stdClass();
		$obj->token 	= $accessToken[ 'linkedin' ][ 'oauth_token' ];
		$obj->secret	= $accessToken[ 'linkedin' ][ 'oauth_token_secret' ];

		// Set the expiration
		$obj->expires 	= $accessToken[ 'linkedin' ][ 'oauth_expires_in' ];

		return $obj;
	}

	public function getUser()
	{
	    return parent::get( 'account/verify_credentials' )->id;
	}

	public function setAccess( $access , $secret)
	{
	    $this->token    = new OAuthConsumer( $access , $secret );
	}

	public function getUserName()
	{
		$result     = parent::api( '/me' );
		$data       = array( 'first_name' => $result[ 'first_name' ] , 'last_name' => $result[ 'last_name' ] );
		return $data;
	}

	/**
	 * Renders a login button.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getLoginButton( $uid , $type , $callback = '' , $permissions = array() , $display = 'popup' )
	{
		$theme 	= FD::themes();

		// Check if the user has already authenticated.
		$table 	= FD::table( 'OAuth' );
		$exists	= $table->load( array( 'uid' => $uid , 'type' => $type ) );

		if( $exists )
		{
			$output 	= $theme->output( 'site/login/linkedin.authenticated' );
		}
		else
		{
			$callback 		= urlencode( $callback );

			// Ensure that the callback is urlencoded
			$callback 	= trim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easysocial&controller=oauth&task=grant&client=linkedin&uid=' . $uid . '&type=' . $type . '&callback=' . $callback;

			$this->setCallbackUrl( $callback );

			$requestToken	= $this->retrieveTokenRequest();
			$_SESSION['oauth']['linkedin']['request'] = $requestToken['linkedin'];

			$url 			= LINKEDIN::_URL_AUTH . $requestToken['linkedin']['oauth_token'];


			$theme->set( 'url'			, $url );
			$theme->set( 'appId' 		, $this->appId );
			$theme->set( 'appSecret' 	, $this->appSecret );
			$theme->set( 'callback'		, $callback );
			$theme->set( 'permissions'	, $permissions );

			$output = $theme->output( 'site/login/linkedin' );
		}

		return $output;
	}
}
