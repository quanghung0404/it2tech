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

require_once( dirname(__FILE__) . '/facebook.php' );

/**
 * Facebook OAuth client
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialConsumerFacebook extends SocialFacebook implements ISocialOAuth
{
	private $oauth_appId		= '';
	private $oauth_appSecret 	= '';

	/**
	 * Determines the type of oauth client.
	 * @var string
	 */
	private $type 				= 'facebook';

	/**
	 * Stores the permissions mapping
	 * Permissions should not be premapped, it should be retrieved based on fields/apps
	 * See getAuthorizeURL and getUserMeta
	 * @var Array
	 */
	// private $permissions 		= array(
	// 	'registration' => array(
	// 		'user_about_me',
	// 		'email',
	// 		'publish_actions',
	// 		'publish_stream'
	// 		'create_note',
	// 		'photo_upload',
	// 		'read_stream',
	// 		'share_item',
	// 		'status_update',
	// 		'user_activities',
	// 		'user_birthday',
	// 		'user_friends',
	// 		'user_hometown',
	// 		'user_interests',
	// 		'user_location',
	// 		'user_photos',
	// 		'user_status',
	// 		'user_website',
	// 		'user_work_history',
	// 		'video_upload'
	// 	)
	// );

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The oauth application key
	 * @param	string		The oauth secret key
	 * @param	string		The callback URL
	 */
	public function __construct( $key , $secret , $callback )
	{
		// Initialize the parent object with appropriate data.
		parent::__construct( array( 'appId' => $key , 'secret' => $secret , 'cookie' => true ) );
	}

	/**
	 * Pulls user's stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function pull( $limit = null )
	{
		$options 	= array( 'limit'	=> 25 );

		$result 	= $this->api( '/me/feed' , $options );
		$items 		= $result[ 'data' ];

		// We need to format the items to be our own format.
		$items 		= $this->format( $items );

		return $items;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addPermission( $permission )
	{
		// Get the user
		$oauthId 	= $this->getUserId();

		// Load the table as we need to update with the new permissions
		$oauthTable 	= FD::table( 'OAuth' );
		$oauthTable->load( array( 'oauth_id' => $oauthId ) );

		$permissions	= FD::makeArray( $oauthTable->permissions );
		$permissions[]	= $permission;

		if( $permission == 'publish_actions' )
		{
			$oauthTable->push 		= true;
		}

		$oauthTable->permissions	= FD::makeJSON( $permissions );

		// Store the permission here.
		$oauthTable->store();
	}

	/**
	 * Removes a permission
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removePermissions( $scope )
	{
		$state 	= $this->api( '/me/permissions/' . $scope , 'delete' );

		// Update the permissions list.
		$oauthId	= $this->getUserId();

		// Load the table as we need to update with the new permissions
		$oauthTable 	= FD::table( 'OAuth' );
		$oauthTable->load( array( 'oauth_id' => $oauthId ) );

		$permissions 	= FD::json()->decode( $oauthTable->permissions );
		$index 			= array_search( $scope , $permissions , false );

		if( $index !== false )
		{
			unset( $permissions[ $index ] );
		}

		$permissions 	= array_values( $permissions );

		if( $scope == 'publish_actions' )
		{
			$oauthTable->push 		= false;
		}

		$oauthTable->permissions	= FD::makeJSON( $permissions );

		// Store the permission here.
		$oauthTable->store();
	}

	/**
	 * Retrieves permissions the user has
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermissions()
	{
		$result 		= $this->api( '/me/permissions' );
		$permissions	= array_keys( $result[ 'data' ][ 0 ] );

		return $permissions;
	}

	/**
	 * Push an item to Facebook
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function push( $message , $placeId = null , $photo = null , $link = null)
	{
		$options 	= array('message' => $message);

		if ($placeId) {
			$options['place']	= $placeId;
		}

		if (is_object($link)) {
			$options['link']    = $link->get('link');
			$options['title']	= $link->get('title');
			$options['content']	= $message;

			if ($photo) {
				$options['picture']	= $photo->getSource('thumbnail');
			}
		}


		$result 	= $this->api('/me/feed', 'POST', $options);

		if ($result) {
			return $result[ 'id' ];
		}

		return false;
	}


	/**
	 * Format Facebook stream items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function format( $items )
	{
		$result	= array();

		$model 	= FD::model( 'OAuth' );

		// echo '<pre>';
		// print_r( $items );
		// echo '</pre>';
		// exit;

		foreach( $items as $item )
		{
			// Get the type
			$type 	= $item[ 'type' ];

			$obj 	= FD::registry();

			$obj->set( 'id'	, $item[ 'id' ] );
			$obj->set( 'with' , null );
			$obj->set( 'type' , $type );
			$obj->set( 'content' , '' );
			$obj->set( 'created' , $item[ 'created_time' ] );

			$file 	= dirname( __FILE__ ) . '/opengraph/' . $type . '.php';

			// Skip this item if the file doesn't exist.
			if( !JFile::exists( $file ) )
			{
				continue;
			}

			require_once( $file );

			$graphClass = 'SocialFacebook' . ucfirst( $type );
			$graphObj	= new $graphClass();

			// Process the graph item
			$state 		= $graphObj->process( $obj , $item , $this->getUserId() );

			if( $state === false )
			{
				continue;
			}

			// Replace names in the content if there are any story_tags
			if( isset( $item[ 'story_tags' ] ) )
			{
				$storyTags 	= $item[ 'story_tags' ];

				// Reverse the ordering
				$storyTags	= array_reverse( $storyTags );

				// Store data in temporary array
				$userStoryTags	= array();
				foreach( $storyTags as $tags => $users )
				{
					foreach( $users as $user )
					{
						$userName 	= $user[ 'name' ];
						$userId 	= $user[ 'id' ];
						$info 		= $this->api( '/' . $userId , array( 'fields' => 'id,name,link') );

						// Get the offset and length for the object.
						$offset 	= $user[ 'offset' ];
						$length 	= $user[ 'length' ];

						$userStoryTags[]	= array( 'link' => $info[ 'link' ] , 'name' => $info[ 'name' ] , 'offset' => $offset , 'length' => $length );
					}
				}

				$obj->set( 'story_tags' , $userStoryTags );
			}

			// If user specified that they are with certain users, we should update the data here.
			if( isset( $item[ 'with_tags' ] ) )
			{
				$withData 		= $item[ 'with_tags' ][ 'data' ];
				$userWithData	= array();

				foreach( $withData as $user )
				{
					// Find if there's an oauth user that is linked to an account.
					$oauthName	= $user[ 'name' ];
					$oauthId	= $user[ 'id' ];

					$info 			= $this->api( '/' . $oauthId , array( 'fields' => 'id,name,gender,email,username,picture,cover,timezone,education,location,website,work,link') );

					$userWithData[] = array( 'link' => $info[ 'link' ] , 'name' => $info[ 'name' ] );
				}
				$obj->set( 'with_data' , $userWithData );
			}

			$result[]	= $obj;

		}

		// echo '<pre>';
		// print_r( $result );
		// echo '</pre>';
		// exit;

		return $result;
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
		return $this->type;
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
		// Try to check if external user id exists on the site.
		$oauthTable 	= FD::table( 'OAuth' );

		// Get external user id.
		$userId			= $this->getUserId();

		$state 			= $oauthTable->load( array( 'oauth_id' => $userId , 'client' => $this->getType() ) );

		return $state;
	}

	/**
	 * Retrieves the user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserId()
	{
		$id 	= parent::getUser();

		return $id;
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
		$state 	= $table->load( array( 'oauth_id' => $this->getUser() , 'client' => $this->getType() ) );

		if( !$state )
		{
			return false;
		}

		// Get the user object.
		$user 			= FD::user( $table->uid );
		$credentials 	= array( 'username' => $user->username , 'password' => $user->password );

		return $credentials;
	}

	/**
	 * Revokes the user's access
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revoke()
	{
		$result     = parent::api( '/me/permissions' , 'delete' );

		return $result;
	}

	/**
	 * Updates the access token
	 *
	 * @since	1.1
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateToken()
	{
		// We need to update with the new access token here.
		$access	 	= $this->getAccess();

		$table 	= FD::table( 'OAuth' );
		$state 	= $table->load( array( 'oauth_id' => $this->getUser() , 'client' => $this->getType() ) );

		if( !$state )
		{
			return false;
		}

		// Try to update with the new token
		$table->token 	= $access->token;

		$state 	= $table->store();

		return $state;
	}

	/**
	 * Retrieves the access token from Facebook
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccess()
	{
		// Get the access token for the user.
		$token 			= $this->getUserAccessToken();

		$obj			= new stdClass();
		$obj->token 	= $token;
		$obj->expires 	= '';
		$obj->secret	= '';

		return $obj;
	}

	/**
	 * Given the access token and secret, set the access token to the parent.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The access token.
	 * @param	string	The secret token.
	 * @return
	 */
	public function setAccess( $access , $secret = '' )
	{
		return $this->setAccessToken( $access );
	}

	/* SINCE 1.3.5: The redirection should be handled by callees who called ->getUserMeta. In which, callees who calls ->getUserMeta, needs to do a try-catch and do a proper fail action instead. */
	/**
	 * Serves as an extra layer in between the parent::api call in order to redirect error properly
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	// public function api()
	// {
	// 	try {
	// 		$args = func_get_args();

	// 		$result = call_user_func_array( array( 'parent', 'api' ), $args );

	// 		return $result;
	// 	} catch( Exception $e ) {
	// 		$app = JFactory::getApplication();

	// 		// Use dashboard here instead of login because api error calls might come from after user have successfully logged in
	// 		$url = FRoute::dashboard( array(), false );

	// 		$message = (object) array(
	// 			'message' => JText::sprintf( 'COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage() ),
	// 			'type' => SOCIAL_MSG_ERROR
	// 		);

	// 		FD::info()->set( $message );

	// 		$app->redirect( $url );
	// 		$app->close();
	// 	}
	// }

	/**
	 * Retrieves user details
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserMeta()
	{
		// Empty user meta data
		$data = array();

		// Load internal configuration
		$config 	= FD::config();

		// Get the default profile
		$profile = $this->getDefaultProfile();

		// Assign the profileId first
		$data[ 'profileId' ] = $profile->id;

		// We need the basic id from Facebook
		$fbFields = array( 'id' );

		// We let field decide which fields they want from facebook
		$fields = $profile->getCustomFields();
		$args = array( &$fbFields, &$this );
		$fieldsLib = FD::fields();
		$fieldsLib->trigger( 'onOAuthGetMetaFields', SOCIAL_FIELDS_GROUP_USER, $fields, $args );

		// Unique it to prevent multiple same fields request
		$fbFields = array_unique( (array) $fbFields );

		// Implode it into a string for request
		$fbFields = implode( ',', $fbFields );

		// Let's try to query facebook for more details.
		$details = $this->api( '/me' , array( 'fields' => $fbFields ) );

		// Give fields the ability to decorate user meta as well
		// This way fields can do extended api calls if the fields need it
		$args = array( &$details, &$this );
		$fieldsLib->trigger( 'onOAuthGetUserMeta', SOCIAL_FIELDS_GROUP_USER, $fields, $args );

		// We remap the id to oauth_id key
		$details['oauth_id'] = $details['id'];
		unset( $details['id'] );

		// Merge Facebook details into data array
		$data = array_merge( $data, $details );

		// Generate a random password for the user.
		$data['password']	= JUserHelper::genRandomPassword();

		return $data;
	}

	/**
	 * Retrieves the authorization url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array
	 * @return
	 */
	public function getAuthorizeURL( $params )
	{
		// Check if there are custom scope passed in
		$scopes 		= isset( $params[ 'scope' ] ) ? $params[ 'scope' ] : array();
		$permissions	= array();

		if( $scopes )
		{
			// Ensure that it's in an array
			$scopes	= FD::makeArray( $scopes );

			$permissions = array_merge( $permissions, $scopes );
		}

		// Add in permissions based on settings
		$config = FD::config();
		if( $config->get( 'oauth.facebook.push' ) )
		{
			$permissions[] = 'publish_actions';
		}

		// We let fields add in permissions
		$args = array( &$permissions );
		$profile = $this->getDefaultProfile();
		$fields = $profile->getCustomFields();
		$fieldsLib = FD::fields();
		$fieldsLib->trigger( 'onOAuthGetUserPermission', SOCIAL_FIELDS_GROUP_USER, $fields, $args );

		// Reset the scope
		$params[ 'scope' ]	= array_unique( (array) $permissions );

		if( !isset( $params[ 'display' ] ) )
		{
			$params[ 'display' ]	= 'popup';
		}

		// Encode the return_to if exists
		if( isset( $params[ 'return_to' ] ) )
		{
			$params[ 'return_to' ]	= base64_encode( $params[ 'return_to' ] );
		}

		// Determine and fix the redirect uri if necessary.
		if( isset( $params[ 'redirect_uri' ] ) )
		{
			$uri 	= $params[ 'redirect_uri' ];

			// Check if there is http:// or https:// in the url.
			if( stristr( $uri , 'http://' ) === false && stristr( $uri , 'https://') === false )
			{
				// If it doesn't exist, always pull from the site.
				$uri 	= rtrim( JURI::root() , '/' ) . $uri;

				$params[ 'redirect_uri' ]	= $uri;
			}
		}

		$url 	=  $this->getLoginUrl( $params );

		return $url;
	}

	/**
	 * Renders a logout button
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRevokeButton( $callback , $uid = null , $type = SOCIAL_TYPE_USER )
	{
		// Check if the user has already authenticated.
		$table 	= FD::table( 'OAuth' );

		$uid 	= is_null( $uid ) ? FD::user()->id : $uid;

		$exists = $table->load( array( 'uid' => $uid , 'type' => $type ) );

		if( !$exists )
		{
			return false;
		}
		$theme 		= FD::themes();

		$theme->set( 'callback'	, $callback );
		$output 	= $theme->output( 'site/facebook/authenticated' );

		return $output;
	}

	private function jfbconnectExists()
	{
		$file 	= JPATH_ADMINISTRATOR . '/components/com_jfbconnect/jfbconnect.php';

		if (JFile::exists($file) && class_exists('JFBCFactory')) {
			return true;
		}

		return false;
	}

	/**
	 * Renders a login button.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getLoginButton( $callback , $permissions = array() , $display = 'popup' , $text = '' )
	{
		// Test if the config has been created correctly.
		$config = FD::config();

		// Check if user wants to integrate with JFBConnect
		if ($config->get('oauth.facebook.jfbconnect.enabled') && $this->jfbconnectExists()) {

			$doc 		= JFactory::getDocument();
			$buttons	= '';
			
			if ($doc->getType() == 'ajax') {
                $params = array('image'=> 'icon.png');
                $buttons = JFBCFactory::getLoginButtons($params);
			}

			$theme 	= FD::themes();
			$theme->set('buttons', $buttons);
			$output = $theme->output('site/facebook/button.jfbconnect');

			return $output;
		}

		if (!$config->get( 'oauth.facebook.registration.enabled')) {
			return;
		}

		if (!$config->get( 'oauth.facebook.app' ) || !$config->get('oauth.facebook.secret')) {
			return;
		}

		$theme 	= FD::themes();

		// Load front end language file.
		FD::language()->loadSite();

		if( empty( $text ) )
		{
			$text 	= JText::_( 'COM_EASYSOCIAL_OAUTH_SIGN_IN_WITH_FACEBOOK' );
		}

		$authorizeURL	= $this->getAuthorizeURL( array( 'scope' => $permissions , 'redirect_uri' => $callback , 'display' => $display ) );

		$theme->set( 'text'			, $text );
		$theme->set( 'authorizeURL'	, $authorizeURL );
		$theme->set( 'appId' 		, $this->appId );
		$theme->set( 'appSecret' 	, $this->appSecret );
		$theme->set( 'callback'		, $callback );
		$theme->set( 'permissions'	, $permissions );

		$output = $theme->output( 'site/facebook/button.login' );

		return $output;
	}

	/**
	 * Get the default assigned Facebook profile
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getDefaultProfile()
	{
		// Load internal configuration
		$config 	= FD::config();

		// Get the profile id the mapping is configured to
		$profileId 	= $config->get( 'oauth.facebook.profile' );

		$profile = FD::table( 'profile' );
		$state = $profile->load( $profileId );

		// Test if profile id is set
		if( !$state )
		{
			// Try to get the default profile on the site.
			$profile 	= FD::table( 'Profile' );
			$state = $profile->load( array( 'default' => 1 ) );

			// If the profile id still cannot be found, just fetch the first item from the database
			if( !$state )
			{
				$model		= FD::model( 'Profiles' );
				$profile	= $model->setLimit( 1 )->getProfiles();
			}
		}

		return $profile;
	}
}
