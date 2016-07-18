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

require_once( dirname(__FILE__) . '/twitteroauth.php' );

class SocialConsumerTwitter extends TwitterOAuth
{
	var $callback		= '';
	var $_access_token	= '';

	private $uid        = '';

	private $oauth_appId		= '';
	private $oauth_appSecret 	= '';

	public function __construct( $key , $secret , $callback )
	{
		$this->oauth_appId 		= $key;
		$this->oauth_appSecret	= $secret;

		parent::__construct( $key , $secret );

		$this->callback	= $callback;
	}

	/**
	 * Return client type
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getType()
	{
		return 'twitter';
	}

	/**
	 * Determines if the current logged in user on Facebook is already registered on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isRegistered()
	{
		$table 	= FD::table( 'OAuth' );
		$state 	= $table->load( array( 'oauth_id' => $this->getUserId() , 'client' => $this->getType() ) );

		return $state;
	}

	/**
	 * Gets the login credentials for the Joomla site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLoginCredentials()
	{
		$table 	= FD::table( 'OAuth' );

		// dump( $this->getUserId() );
		$state 	= $table->load( array( 'oauth_id' => $this->getUserId() , 'client' => $this->getType() ) );

		if( !$state )
		{
			return false;
		}

		// Get the user object.
		$user 			= FD::user( $table->uid );
		$credentials 	= array( 'username' => $user->username , 'password' => JUserHelper::genRandomPassword() );

		return $credentials;
	}

	/**
	 * Retrieves user details
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserDetails()
	{
		// Load internal configuration
		$config 	= FD::config();

		// Get the profile id the mapping is configured to
		$profileId 	= $config->get( 'oauth.twitter.profile' );

		// Query Twitter for more details.
		$details 	= parent::get( '/account/verify_credentials' );

		// dump( $details );

		// Load container
		$data 				= array();
		$data['username']	= $details->screen_name;
		$data['name']		= $details->name;

		// Set a custom email address for the user.
		$uri				= JURI::getInstance();
		$domain 			= $uri->toString( array( 'host' ) );
		$data['email']		= $details->screen_name . '@' . $domain;

		$data[ 'oauth_id' ]	= $details->id;


		// @TODO: Map the custom fields from the profile with our own internal mapping
		if( $config->get( 'oauth.twitter.registration.avatar' ) )
		{
			// Set the avatar and cover
			$data[ 'avatar' ]	= $details->profile_image_url;
		}


		// Generate a random password for the user.
		$data[ 'password' ]	= JUserHelper::genRandomPassword();

		$data[ 'profileId' ]	= $profileId;

		// @TODO: Map accordingly.
		return $data;
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

	/**
	 * Returns the authorization url.
	 *
	 * @return string	$url	A link to twitter's login URL.
	 **/
	public function getAuthorizationURL( $token = '' )
	{
		$temporary  = parent::getRequestToken( $this->callback );

		// Temporarily store the temporary request tokens in the session
		$session    = JFactory::getSession();
		$session->set( 'easysocial_oauth_token' , $temporary[ 'oauth_token' ] );
		$session->set( 'easysocial_oauth_token_secret' , $temporary[ 'oauth_token_secret' ] );

		return parent::getAuthorizeURL( $temporary[ 'oauth_token' ] );
	}

	/**
	 * Exchanges the verifier code with the access token.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccess()
	{
		// Get the verifier code from query.
		$verifier 		= JRequest::getVar( 'oauth_verifier' );

		// Set temporary token
		$this->token    = new OAuthConsumer( $_REQUEST[ 'oauth_token'] , $_SESSION[ 'oauth_token'] );

		// Now let's try to get the access token.
		$accessToken	= $this->getAccessToken( $verifier );

		$obj			= new stdClass();
		$obj->token 	= $accessToken[ 'oauth_token' ];
		$obj->secret	= $accessToken[ 'oauth_token_secret' ];

		// Twitter sessions never expires (for now...)
		$obj->expires 	= 0;

		$params 		= FD::registry();
		$params->set( 'user_id' , $accessToken[ 'user_id' ] );
		$params->set( 'screen_name' , $accessToken[ 'screen_name' ] );

		$obj->params 	= $params->toString();

		return $obj;
	}

	/**
	 * Retrieves the unique user id from the external site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserId()
	{
		// Get the access
		$user		= parent::get( '/account/verify_credentials' );

		return $user->id;
	}

	public function setAccess( $access , $secret)
	{
	    $this->token    = new OAuthConsumer( $access , $secret );
	}

	/**
	 * Renders a logout button
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLogoutButton( $callback )
	{
		// Check if the user has already authenticated.
		$table 	= FD::table( 'OAuth' );
		$exists	= $table->load( array( 'uid' => $uid , 'type' => $type ) );

		$theme->set( 'logoutCallback'	, $callback );
		$output 	= $theme->output( 'site/login/facebook.authenticated' );
	}

	/**
	 * Renders a login button.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getLoginButton( $callback , $permissions = array() , $display = 'popup' )
	{
		$theme 	= FD::themes();

		$callback 	= rtrim( JURI::root() , '/' ) . '/' . ltrim( $callback , '/' );


		$requestToken 	= $this->getRequestToken( $callback );

		$_SESSION['oauth_token']		= $requestToken['oauth_token'];
		$_SESSION['oauth_token_secret'] = $requestToken['oauth_token_secret'];

		$url 			= $this->getAuthorizeURL( $requestToken );


		$theme->set( 'url'			, $url );
		$theme->set( 'appId' 		, $this->oauth_appId );
		$theme->set( 'appSecret' 	, $this->oauth_appSecret );
		$theme->set( 'callback'		, $callback );
		$theme->set( 'permissions'	, $permissions );

		$output = $theme->output( 'site/login/twitter' );

		return $output;
	}
}
