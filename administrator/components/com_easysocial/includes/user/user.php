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
defined('_JEXEC') or die('Unauthorized Access');

// Dependencies
ES::import('admin:/tables/table');
ES::import('admin:/includes/indexer/indexer');

class SocialUser extends JUser
{
	/**
	 * The user's unique id.
	 * @var int
	 */
	public $id = null;

	/**
	 * The user's name which is stored in `#__users` table.
	 * @var string
	 */
	public $name = null;

	/**
	 * The user's username which is stored in `#__users` table.
	 * @var string
	 */
	public $username = null;

	/**
	 * The user's email which is stored in `#__users` table.
	 * @var string
	 */
	public $email = null;

	/**
	 * The user's password which is a md5 hash which is stored in `#__users` table.
	 * @var string
	 */
	public $password = null;

	/**
	 * The user's type which is stored in `#__users` table. (Only for Joomla 1.5)
	 * @var string
	 */
	public $usertype = null;

	/**
	 * The user's published status which is stored in `#__users` table.
	 * @var int
	 */
	public $block 			= null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $sendEmail 		= null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $registerDate	= null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $otpKey = null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $otep = null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $lastvisitDate	= null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $activation 		= null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $params 			= null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $privacy			= null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $connections		= 0;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $param		= null;

	/**
	 * User's current state. Stored in `#__social_users` table.
	 * @var int
	 */
	public $state       = null;

	/**
	 * User's preferences on receiving emails. Stored in `#__users` table.
	 * @var int
	 */
	public $profile_id   = null;

	/**
	 * User's avatar id (from gallery). Stored in `#__social_avatars` table.
	 * @var int
	 */
	public $avatar_id    = null;

	/**
	 * User's avatar id (from uploaded photos). Stored in `#__social_avatars` table.
	 * @var int
	 */
	public $photo_id    = null;

	/**
	 * User's permalink
	 * @var string
	 */
	public $permalink	= null;

	/**
	 * User's online status. This isn't stored anywhere. It's just loaded
	 * initially, to let other's know of the user's online state.
	 * @var int
	 */
	public $online		= null;

	/**
	 * User's alias.
	 *
	 * @var string
	 */
	public $alias 		= null;

	/**
	 * User's authentication code.
	 *
	 * @var string
	 */
	public $auth 		= null;

	/*
	 * Custom values
	 */
	public $password_clear   = null;

	public $reminder_sent = null;

	public $require_reset = null;

	public $block_period = null;

	public $block_date = null;


	// Default avatar sizes
	public $avatarSizes	= array( 'small' , 'medium' , 'large' , 'square');

	// Avatars
	public $avatars 		= array( 'small' 	=> '',
									 'medium' 	=> '',
									 'large'	=> '',
									 'square'	=> ''
									);

	// Cover Photo
	public $cover 			= null;

	/**
	 * Stores the default avatar property if exists.
	 * @var SocialTableDefaultAvatar
	 */
	public $defaultAvatar	= null;

	/**
	 * The user's points
	 * @var int
	 */
	public $points 		= 0;

	/**
	 * Stores the user type.
	 * @var	string
	 */
	public $type = 'joomla';

	/**
	 * Keeps a list of users that are already loaded so we
	 * don't have to always reload the user again.
	 * @var Array
	 */
	static $userInstances	= array();

	/**
	 * Keeps a list of super admin ids.
	 * @var Array
	 */
	static $admins 		= array();

	/**
	 * Stores user badges
	 * @var Array
	 */
	protected $badges 		= array();

	/**
	 * Helper object for various cms versions.
	 * @var	object
	 */
	protected $helper 		= null;

	/**
	 * Determines the storage type for the avatars
	 * @var string
	 */
	protected $avatarStorage = 'joomla';

	/**
	 * Determines the number of fields completed for this user in this profile.
	 * @var integer
	 */
	public $completed_fields = 0;

	public function __construct($params = array(), $debug = false)
	{
		// Get the path to the helper file.
		$file = __DIR__ . '/helpers/joomla.php';

		require_once($file);

		// Initialize helper object.
		$this->helper = new SocialUserHelperJoomla($this);

		// Create the user parameters object
		$this->_params = new JRegistry;

		// Initialize user's property locally.
		$this->initParams($params);

		if (!$this->id) {
			$this->guest = true;
		} else {
			$this->guest = false;
		}
	}

	/**
	 * Blocks a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function block()
	{
		// Set juser data first
		$this->block = SOCIAL_JOOMLA_USER_BLOCKED;

		// Set our own state data
		$this->state = SOCIAL_USER_STATE_DISABLED;

		// Save the user after updating their blocked state
		$state = $this->save();

		// After blocking a user, synchronize with finder
		$this->syncIndex();

		// Log the user out
		$app = JFactory::getApplication();
		$app->logout($this->id, array('clientid' => 0));

		return $state;
	}

	/**
	 * Blocks a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unblock()
	{
		// Set juser data first
		$this->block = SOCIAL_JOOMLA_USER_UNBLOCKED;

		// Set our own state data
		$this->state = SOCIAL_USER_STATE_ENABLED;

		// When user is unbanned, we also want to remove any block_period from the table
		$this->block_period = 0;
		$this->block_date = '0000-00-00 00:00:00';

		// onBeforeUnblock

		$state = $this->save();

		// After unblocking a user, we need to sync the index again
		$this->syncIndex();

		return $state;
	}

	/**
	 * Determines if this user is blocked
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	True if user is blocked, false otherwise
	 */
	public function isBlock()
	{
		return (bool) $this->block;
	}

	/**
	 * Determines if this user is blocked by another user
	 *
	 * @since	1.3
	 * @access	public
	 * @return	bool	True if user is blocked, false otherwise
	 */
	public function isBlockedBy($id)
	{

		if (!FD::config()->get('users.blocking.enabled')) {
			return false;
		}

		static $cache = array();

		if (!isset($cache[$id])) {
			$model = FD::model('Blocks');

			$cache[$id] = (bool) $model->isBlocked($id, $this->id);
		}

		return $cache[$id];
	}

	/**
	 * Assign this user to a group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The group id
	 * @return	bool	True if success, false otherwise
	 */
	public function assign( $gid )
	{
		$model = FD::model( 'Users' );

		$model->assignToGroup( $this->id , $gid );
	}

	/**
	 * Initializes the provided properties into the existing object. Instead of
	 * trying to query to fetch more info about the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	void
	 */
	public function initParams( &$params )
	{
		// Get all properties of this object
		$properties	= get_object_vars( $this );

		// Bind parameters to the object
		foreach( $properties as $key => $val )
		{
			if( isset( $params->$key ) )
			{
				$this->$key		= $params->$key;
			}
		}

		// Bind params json object here
		$this->_params->loadString( $this->params );

		// Bind user avatars here.
		foreach( $this->avatars as $size => $value )
		{
			if( isset( $params->$size ) )
			{
				$this->avatars[ $size ]	= $params->$size;
			}
		}

		// set the list of user groups
		$this->groups 	= $this->helper->getUserGroups();

	}

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   $id     int/Array     Optional parameter
	 * @return  SocialUser   The person object.
	 */
	public static function factory($ids = null, $debug = false)
	{
		$items = self::loadUsers($ids, $debug);

		return $items;
	}

	/**
	 * Processes user related stream item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addStream( $verb )
	{
		$config = FD::config();

		if( $verb == 'uploadAvatar' )
		{
			// Add stream item when a new photo is uploaded.
			$stream				= FD::stream();
			$streamTemplate		= $stream->getTemplate();

			// Set the actor.
			$streamTemplate->setActor( $this->id , SOCIAL_TYPE_USER );

			// Set the context.
			$streamTemplate->setContext( $this->id , SOCIAL_TYPE_PHOTO );

			// Set the verb.
			$streamTemplate->setVerb( 'add' );

			$streamTemplate->setAccess( 'photos.view' );


			//
			$streamTemplate->setType( 'full' );

			// Create the stream data.
			$stream->add( $streamTemplate );
		}

		if( $verb == 'updateProfile' && $config->get( 'users.stream.profile' ) )
		{
			// Add stream item when a new photo is uploaded.
			$stream				= FD::stream();
			$streamTemplate		= $stream->getTemplate();

			// Set the actor.
			$streamTemplate->setActor( $this->id , SOCIAL_TYPE_USER );

			// Set the context.
			$streamTemplate->setContext( $this->id , SOCIAL_TYPE_PROFILES );

			// Set the verb.
			$streamTemplate->setVerb( 'update' );


			$streamTemplate->setAggregate( true );


			$streamTemplate->setAccess( 'core.view' );


			// Set stream style
			$streamTemplate->setType( 'mini' );

			// Create the stream data.
			$stream->add( $streamTemplate );
		}
	}

	/**
	 * Retrieves a list of apps for a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getApps( $view )
	{
		static $apps 	= array();

		if( !isset( $apps[ $this->id ][ $view ] ) )
		{
			$model 		= FD::model( 'Apps' );
			$options 	= array( 'view' => $view , 'uid' => $this->id , 'key' => SOCIAL_TYPE_USER );
			$userApps 	= $model->getApps( $options );

			$apps[ $this->id ][ $view ]	= $userApps;
		}

		return $apps[ $this->id ][ $view ];
	}

	/**
	 * Creates a guest object and store them into the property as static instance.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function createGuestObject()
	{
		// Set guest property
		if (!isset(SocialUserStorage::$users[0])) {
			$guest	= FD::table( 'Users' );
			$data	= array();

			$obj 	= new self( $guest ,  $data );

			$obj->id 	= 0;
			$obj->name 	= JText::_( 'COM_EASYSOCIAL_GUEST_NAME' );

			SocialUserStorage::$users[0]	= $obj;
		}
	}

	/**
	 * Reloads the cache for custom field values when the user profile changes.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reloadFields()
	{
		$model 	= FD::model( 'Users' );

		SocialUserStorage::$fields[$this->id]	= $model->initUserData($this->id);
	}

	/**
	 * Removes a user item from the cache
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFromCache()
	{
		// Remove from user's storage cache
		unset(SocialUserStorage::$users[$this->id]);

		// Remove it from the model's cache too
		unset(EasySocialModelUsers::$loadedUsers[$this->id]);
	}

	/**
	 * Loads a given user id or an array of id's.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * // Loads current logged in user.
	 * $my 		= FD::get( 'User' );
	 * // Shorthand
	 * $my 		= FD::user();
	 *
	 * // Loads a single user.
	 * $user	= FD::get( 'User' , 42 );
	 * // Shorthand
	 * $user 	= FD::user( 42 );
	 *
	 * // Loads multiple users.
	 * $users 	= FD::get( 'User' , array( 42 , 43 ) );
	 * // Shorthand
	 * $users 	= FD::user( array( 42 , 43 ) );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int|Array	Either an int or an array of id's in integer.
	 * @return	SocialUser	The user object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function loadUsers($ids = null, $debug = false)
	{
		// Determine if the argument is an array.
		$argumentIsArray = is_array($ids);

		// If it is null or 0, the caller wants to retrieve the current logged in user.
		if (is_null($ids) || (is_string($ids) && $ids == '')) {
			$ids = array(JFactory::getUser()->id);
		}

		// Ensure that id's are always an array
		$ids = FD::makeArray($ids);

		// Reset the index of ids so we don't load multiple times from the same user.
		$ids = array_values($ids);

		// Always create the guest objects first.
		self::createGuestObject();

		// Total needs to be computed here before entering iteration as it might be affected by unset.
		$total = count($ids);

		// Placeholder for items that are already loaded.
		$loaded = array();

		// @task: We need to only load user's that aren't loaded yet.
		for ($i = 0; $i < $total; $i++) {

			if (empty($ids)) {
				break;
			}

			if (!isset($ids[$i]) && empty($ids[$i])) {
				continue;
			}

			$id = $ids[$i];

			// If id is null, we know we want the current user.
			if (is_null($id)) {
				$ids[$i] = JFactory::getUser()->id;
			}

			// The parsed id's could be an object from the database query.
			if (is_object($id) && isset($id->id)) {
				$id = $id->id;

				// Replace the current value with the proper value.
				$ids[$i] = $id;
			}

			if (isset(SocialUserStorage::$users[$id])) {
				$loaded[] = $id;
				unset($ids[$i]);
			}

		}

		// Reset the ids after it was previously unset.
		$ids = array_values($ids);

		// Place holder for result items.
		$result	= array();

		foreach ($loaded as $id) {
			$result[] = SocialUserStorage::$users[$id];
		}

		if (!empty($ids)) {

			// Retrieve user's data
			$model = FD::model('Users');
			$users = $model->getUsersMeta($ids);

			// Iterate through the users list and add them into the static property.
			if ($users) {

				foreach ($users as $user) {
					// Get the user's cover photo
					$user->cover = self::getCoverObject($user);

					// Detect if the user has an avatar.
					$user->defaultAvatar = false;

					if ($user->avatar_id) {
						$defaultAvatar = FD::table('DefaultAvatar');
						$defaultAvatar->load($user->avatar_id);
						$user->defaultAvatar = $defaultAvatar;
					}

					// Try to load the user from `#__social_users`
					// If the user record doesn't exists in #__social_users we need to initialize it first.
					if (!$model->metaExists($user->id)) {
						$model->createMeta($user->id);
					}

					// Attach fields for this user.
					// SocialUserStorage::$fields[$user->id]	= $model->initUserData($user->id);

					// Get user's badges
					// SocialUserStorage::$badges[$user->id]	= FD::model('Badges')->getBadges($user->id);

					// Create an object of itself and store in the static object.
					$obj = new SocialUser($user);


					SocialUserStorage::$users[$user->id] = $obj;

					$result[] = SocialUserStorage::$users[$user->id];
				}
			} else {

				foreach ($ids as $id) {
					// Since there are no such users, we just use the guest object.
					SocialUserStorage::$users[$id] = SocialUserStorage::$users[0];

					$result[] = SocialUserStorage::$users[$id];
				}
			}
		}

		// If the argument passed in is not an array, just return the proper value.
		if (!$argumentIsArray && count($result) == 1) {
			return $result[0];
		}

		return $result;
	}

	/**
	 * Bind the cover object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCoverObject( $user )
	{
		$cover = FD::table('Cover');

		if (!empty($user->cover_id)) {
			$coverData = new stdClass();
			$coverData->id = $user->cover_id;
			$coverData->uid = $user->cover_uid;
			$coverData->type = $user->cover_type;
			$coverData->photo_id = $user->cover_photo_id;
			$coverData->cover_id = $user->cover_cover_id;
			$coverData->x = $user->cover_x;
			$coverData->y = $user->cover_y;
			$coverData->modified = $user->cover_modified;

			$cover->bind($coverData);
		} else {
			// Type is always user for this object.
			$cover->type = SOCIAL_TYPE_USER;
		}

		return $cover;
	}

	/**
	 * Determines whether the current user is active or not.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	boolean		True if online, false otherwise.
	 */
	public function isOnline()
	{
		static $states 	= array();

		if( !isset( $states[ $this->id ] ) )
		{
			$model 	= FD::model( 'Users' );

			$online	= $model->isOnline( $this->id );

			$states[ $this->id ]	= $online;
		}

		return $states[ $this->id ];
	}

	/**
	 * Determines if the current logged in user is viewing this current page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	boolean		True if online, false otherwise.
	 */
	public function isViewer()
	{
		$my 	= FD::user();

		$isViewer	= $my->id == $this->id;

		return $isViewer;
	}

	/**
	 * Determines if the user is logged in
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean
	 */
	public function isLoggedIn()
	{
		return $this->id > 0;
	}

	/**
	 * Logs the user out from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function logout()
	{
		$app = JFactory::getApplication();

		// Try to logout the user.
		$error = $app->logout();

		return $error;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addPoints( $point )
	{
		$this->points 	+= $point;

		return $this;
	}

	/**
	 * Determines if the current user is a super administrator of the site or not.
	 *
	 * @access	public
	 * @param	null
	 * @return	boolean	True on success false otherwise.
	 */
	public function isSiteAdmin()
	{
		static $_cache = array();

		$isSiteAdmin	= false;
		if (isset($_cache[$this->id])) {
			$isSiteAdmin = $_cache[$this->id];
		} else {
			$isSiteAdmin        = $this->authorise( 'core.admin' ) || $this->authorise( 'core.manage' );
			$_cache[$this->id] 	= $isSiteAdmin;
		}
		return ( $isSiteAdmin ) ? true : false ;
	}


	/**
	 * determine if the current user can delete a specific user or not
	 *
	 * @since	1.4
	 * @access	public
	 * @param	EasySocialUser $user
	 *
	 * @return	boolean 		True if success, false otherwise.
	 */
	public function canDeleteUser($user)
	{
		if (! $this->isSiteAdmin()) {
			return false;
		}

		$isUserSuper = $this->authorise('core.admin');
		$isTargetSuper = $user->authorise('core.admin');

		$isUserAdmin = !$isUserSuper && $this->authorise('core.manage');
		$isTargetAdmin = !$isTargetSuper && $user->authorise('core.manage');

		// if currrent user is a superadmin and target user also a super admin, we dont allow to delete.
		if ($isUserSuper && $isTargetSuper) {
			return false;
		}

		// if current user is a standard admin and the target is either super or admin, we dont allow to delete target.
		if ($isUserAdmin && ($isTargetSuper || $isTargetAdmin)) {
			return false;
		}

		return true;
	}


	/**
	 * determine if the current user can ban a specific user or not
	 *
	 * @since	1.4
	 * @access	public
	 * @param	EasySocialUser $user
	 *
	 * @return	boolean 		True if success, false otherwise.
	 */
	public function canBanUser($user)
	{
		if (! $this->isSiteAdmin()) {
			return false;
		}

		$isUserSuper = $this->authorise('core.admin');
		$isTargetSuper = $user->authorise('core.admin');

		$isUserAdmin = !$isUserSuper && $this->authorise('core.manage');
		$isTargetAdmin = !$isTargetSuper && $user->authorise('core.manage');

		// if currrent user is a superadmin and target user also a super admin, we dont allow to ban.
		if ($isUserSuper && $isTargetSuper) {
			return false;
		}

		// if current user is a standard admin and the target is a superadmin, we dont allow to ban target.
		if ($isUserAdmin && $isTargetSuper) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the current user is followed by the target id
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 	The target user id.
	 *
	 * @return	boolean 		True if success, false otherwise.
	 */
	public function isFollowed( $id )
	{
		static $followed	= null;

		if( !isset( $followed[ $this->id ][ $id ] ) )
		{
			$subscription 					= FD::get( 'Subscriptions' );
			$followed[ $this->id ][ $id ]	= $subscription->isFollowing( $this->id , SOCIAL_TYPE_USER , SOCIAL_APPS_GROUP_USER , $id );
		}

		return $followed[ $this->id ][ $id ];
	}

	/**
	 * Determines if the current user is friends with the specified user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 	The target user id.
	 *
	 * @return	boolean 		True if success, false otherwise.
	 */
	public function isFriends( $id )
	{
		static $isFriends	= null;

		if( !isset( $isFriends[ $this->id ][ $id ] ) )
		{
			$model 	= FD::model( 'Friends' );

			$isFriends[ $this->id ][ $id ]	= $model->isFriends( $this->id , $id );
		}

		return $isFriends[ $this->id ][ $id ];
	}

	/**
	 * Determines if the current user is friends with the specified user id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$id 	The target user id.
	 *
	 * @return	boolean 		True if success, false otherwise.
	 */
	public function getFriend( $id )
	{
		static $data = array();

		if( !isset( $data[ $this->id ] ) )
		{
			$data[ $this->id ]	= array();
		}

		if( !isset( $data[ $this->id ][ $id ] ) )
		{
			$friend 	= FD::table( 'Friend' );
			$friend->loadByUser( $this->id , $id );
		}

		return $friend;
	}

	/**
	 * Determines if the person is a registered member or not.
	 *
	 * @param	null
	 * @return	boolean		True if registered, false otherwise.
	 */
	public function isRegistered()
	{
		return $this->id > 0;
	}

	/**
	 * Determines if the current user record is a new user or not.
	 *
	 * @access	private
	 * @param	null
	 * @return	boolean	True on success false otherwise.
	 */
	private function isNew()
	{
		return $this->id < 1;
	}

	/**
	 * Determines if the person is pending approval or not.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	boolean		True if still pending, false otherwise.
	 */
	public function isPending()
	{
		if( $this->status == SOCIAL_REGISTER_APPROVAL )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user has access to the community area
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasCommunityAccess()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$profile = $this->getProfile();

			$items[$this->id] = (bool) $profile->community_access;
		}

		return $items[$this->id];
	}

	/**
	 * Determines if the user has an avatar
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasAvatar()
	{
		return !empty($this->avatar_id) || !empty($this->photo_id);
	}


	/**
	 * Get available avatar sizes
	 *
	 * @since	1.4.6
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvatarSizes()
	{
		return $this->avatarSizes;
	}

	/**
	 * Retrieves the user's avatar location
	 *
	 * @access	public
	 * @param   string	$size 	The avatar size to retrieve for.
	 * @return  string  The current user's username.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getAvatar($size = SOCIAL_AVATAR_MEDIUM, $debug = false)
	{
		$config = FD::config();

		// If avatar id is being set, we need to get the avatar source
		if ($this->defaultAvatar) {
			$default = $this->defaultAvatar->getSource($size);

			return $default;
		}

		// If the avatar size that is being requested is invalid, return default avatar.
		$default = $this->getDefaultAvatar($size);

		if (!$this->avatars[$size] || empty($this->avatars[$size])) {
			return $default;
		}

		// Get the path to the avatar storage.
		$avatarLocation = FD::cleanPath($config->get('avatars.storage.container'));
		$usersAvatarLocation = FD::cleanPath($config->get('avatars.storage.user'));

		// Build the path now.
		$path = $avatarLocation . '/' . $usersAvatarLocation . '/' . $this->id . '/' . $this->avatars[ $size ];

		// if ($debug) {
		// 	dump($path);
		// }

		if ($this->avatarStorage == SOCIAL_STORAGE_JOOMLA) {
			// Build final storage path.
			$absolutePath = JPATH_ROOT . '/' . $path;

			// Detect if this file really exists.
			if (!JFile::exists($absolutePath)) {
				return $default;
			}

			$uri = rtrim( JURI::root() , '/' ) . '/' . $path;
		} else {
			$storage = FD::storage($this->avatarStorage);
			$uri = $storage->getPermalink($path);
		}

	    return $uri;
	}

	/**
	 * Retrieves the default cover location as it might have template overrides.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultCover()
	{
		static $default 	= null;

		if( !$default )
		{
			$app		= JFactory::getApplication();
			$config 	= FD::config();
			$overriden	= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/covers/users/default.png';
			$uri 		= rtrim( JURI::root() , '/' ) . '/templates/' . $app->getTemplate() . '/html/com_easysocial/covers/users/default.png';

			if( JFile::exists( $overriden ) )
			{
				$default 	= $uri;
			}
			else
			{
				$default	= rtrim( JURI::root() , '/' ) . $config->get( 'covers.default.user.default' );
			}
		}

		return $default;
	}

	/**
	 * Retrieves the default avatar location as it might have template overrides.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultAvatar( $size )
	{
		static $default 	= null;

		$key = $this->id . '.' . $size;


		if( !isset( $default[ $key ] ) )
		{
			$app		= JFactory::getApplication();
			$config 	= FD::config();
			$overriden	= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/avatars/users/' . $size . '.png';
			$uri 		= rtrim( JURI::root() , '/' ) . '/templates/' . $app->getTemplate() . '/html/com_easysocial/avatars/users/' . $size . '.png';

			if( JFile::exists( $overriden ) )
			{
				$default[ $key ] 	= $uri;
			}
			else
			{
				$default[ $key ]	= rtrim( JURI::root() , '/' ) . $config->get( 'avatars.default.user.' . $size );
			}

			// See if profile is set and see if there is a default avatar in the profile or not

			$model 		= FD::model('Avatars');
			$avatars 	= $model->getDefaultAvatars($this->profile_id);

			foreach($avatars as $avatar) {
				if ($avatar->default) {
					$default[ $key ] = $avatar->getSource($size);
					break;
				}
			}
		}

		return $default[ $key ];
	}

	/**
	 * Retrieves the photo table for the user's avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvatarPhoto()
	{
		static $photos 	= array();

		if( !isset( $photos[ $this->id ] ) )
		{
			$model 	= FD::model( 'Avatars' );
			$photo	= $model->getPhoto( $this->id );

			$photos[ $this->id ]	= $photo;
		}

		return $photos[ $this->id ];
	}

	public function hasCover()
	{
		return !(empty($this->cover) || empty($this->cover->id));
	}

	/**
	 * Retrieves the user's cover data
	 *
	 * @since 	1.2
	 * @access	public
	 * @param   string	$size 	The avatar size to retrieve for.
	 * @return  string  The current user's username.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCoverData()
	{
		return $this->cover;
	}

	/**
	 * Retrieves the user's cover location
	 *
	 * @since 	1.0
	 * @access	public
	 * @param   string	$size 	The avatar size to retrieve for.
	 * @return  string  The current user's username.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCover()
	{
		if( !$this->cover )
		{
			$cover 	= $this->getDefaultCover();

			return $cover;
		}

		return $this->cover->getSource();
	}

	/**
	 * Retrieves the user's cover position
	 *
	 * @since	1.2
	 * @access	public
	 * @param   string	$size 	The avatar size to retrieve for.
	 * @return  string  The current user's username.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCoverPosition()
	{
		if( !$this->cover )
		{
			return 0;
		}

		return $this->cover->getPosition();
	}

	/**
	 * Retrieves the user badges
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBadges()
	{
		if (!isset(SocialUserStorage::$badges[$this->id])) {

			$model 	= FD::model('Badges');

			SocialUserStorage::$badges[$this->id] = $model->getBadges($this->id);
		}

		// Returns a list of badges earned by the user.
		return SocialUserStorage::$badges[$this->id];
	}

	/**
	 * Retrieves the user's username
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  string  The current user's username.
	 */
	public function getUserName()
	{
		return $this->username;
	}

	/**
	 * Retrieves the user's real name dependent on the system configurations.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  string  The current user's screen name. It can be in a form of (name, username or email)
	 */
	public function getName( $useFormat = '' )
	{
		$config 	= FD::config();
		$name 		= $this->username;

		if( $useFormat )
		{
			if( $useFormat == 'realname' )
				$name 	= JString::ucfirst( $this->name );
		}
		else
		{
			if( $config->get( 'users.displayName' ) == 'realname' )
				$name 	= JString::ucfirst( $this->name );
		}

		return $name;
	}

	/**
	 * Get's a user stream name. If the current logged in user is him/her self, use "You" instead.
	 * This can be applied to anyone that is trying to apply stream like-ish contents.
	 *
	 * @access	public
	 * @return	string
	 */
	public function getStreamName( $uppercase = true )
	{
		$my			= FD::user();

		if( $my->id == $this->id )
		{
			$uppercase 	= $uppercase ? '' : '_LOWERCASE';

			return JText::_( 'COM_EASYSOCIAL_YOU' . $uppercase );
		}

		return $this->getName();
	}

	/**
	 * Retrieves the user's connection.
	 *
	 * @param   null
	 * @return  string  The current user's connection.
	 */
	public function getConnections()
	{
	    return $this->connections;
	}

	/**
	 * Retrieves the user's points
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	float	The points that a user has.
	 */
	public function getPoints()
	{
		return $this->points;
	}

	/**
	 * Returns the last visited date from a user.
	 *
	 * Example of usage:
	 * <code>
	 * <?php
	 * $user 	= FD::user();
	 *
	 * // Displays: 5 mins ago
	 * echo $user->getLastVisitDate()->toLapsed();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialDate		The last visited date object.
	 */
	public function getLastVisitDate( $type = '' )
	{
		// If user wants a lapsed type.
		if( $type == 'lapsed' )
		{
			$date 	= FD::date( $this->lastvisitDate );

			return $date->toLapsed();
		}

		return $this->lastvisitDate;
	}

	/**
	 * Returns the user's user group that they belong to.
	 *
	 * Example of usage:
	 * <code>
	 * <?php
	 * $user 	= FD::user();
	 *
	 * // Returns array( 'ID' => 'Super User' , 'ID' => 'Registered' )
	 * $user->getUserGroups();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array	An array of group in string.
	 */
	public function getUserGroups( $gids = false )
	{
		$groups 	= $this->helper->getUserGroups();

		if( $gids )
		{
			return array_keys( $groups );
		}

		return $groups;
	}

	/**
	 * Returns the last visited date from a user.
	 *
	 * Example of usage:
	 * <code>
	 * <?php
	 * $user 	= FD::user();
	 *
	 * // Displays: 5 mins ago
	 * echo $user->getLastVisitDate()->toLapsed();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialDate		The last visited date object.
	 */
	public function getRegistrationDate()
	{
		$date 	= FD::get( 'Date' , $this->registerDate );

		return $date;
	}

	/**
	 * Retrieves the profile type of the current user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	TableProfiles
	 */
	public function getProfile()
	{
		static $profiles 	= array();

		if (!isset($profiles[$this->profile_id])) {

			$profile 	= FD::table('Profile');
			$profile->load($this->profile_id);

			$profiles[$this->profile_id]	= $profile;
		}

		return $profiles[$this->profile_id];
	}

	/**
	 * Retrieves the privacy object of the current user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialPrivacy
	 */
	public function getPrivacy()
	{
		$privacy 	= FD::privacy( $this->id );

		return $privacy;
	}

	/**
	 * Get the alias of the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias($withId = true, $forceId = false)
	{
		$config = ES::config();

		// Default permalink to use.
		$name = $config->get('users.aliasName') == 'realname' ? $this->name : $this->username;

		$withId = ($withId || $forceId) ? true : $withId;

		// If sef is not enabled or running SH404, just return the ID-USERNAME prefix.
		jimport( 'joomla.filesystem.file' );
		$jConfig	= FD::jconfig();
		$sh404		= JFile::exists( JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.php' );
		$mijoSef		= JFile::exists( JPATH_ADMINISTRATOR . '/components/com_mijosef/mijosef.php' );

		if( !$jConfig->getValue( 'sef' ) || $sh404 )
		{
			return ($withId ? $this->id . ':' : '') . JFilterOutput::stringURLSafe( $name );
		}

		$name 		= ($withId ? $this->id . ':' : '') . $name;

		// Check if the permalink is set
		if( $this->permalink && !empty( $this->permalink ) )
		{
			$name 	= ($forceId ? $this->id . ':' : '') . $this->permalink;

			if($mijoSef) {
				return ($withId ? $this->id . ':' : '') . JFilterOutput::stringURLSafe( $name );
			}
		}

		// If alias exists and permalink doesn't we use the alias
		if( $this->alias && !empty( $this->alias ) && !$this->permalink )
		{
			$name 	= ($withId ? $this->id . ':' : '') . JFilterOutput::stringURLUnicodeSlug( $this->alias );
		}

		// If the name is in the form of an e-mail address, fix it here by using the ID:permalink syntax
		if( JMailHelper::isEmailAddress( $name ) )
		{
			return ($withId ? $this->id . ':' : '') . JFilterOutput::stringURLSafe( $name );
		}

		// Ensure that the name is a safe url.
		$name 	= JFilterOutput::stringURLSafe( $name );

		return $name;
	}

	/**
	 * Centralized method to retrieve a person's profile link.
	 * This is where all the magic happens.
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	string	The url for the person
	 */
	public function getPermalink($xhtml = true, $external = false, $sef = true)
	{
		$my = ES::user();

		// If user is blocked, just use a dummy link
		if (!$my->isSiteAdmin() && ($this->isBlock() || !$this->hasCommunityAccess())) {
			return 'javascript:void(0);';
		}

		// When simple urls are enabled, we just hardcode the url
		$config = ES::config();
		$jConfig = ES::jConfig();

		if (!ES::isSh404Installed() && $config->get('users.simpleUrl') && $jConfig->getValue('sef') && $sef && $this->alias) {
			$url = rtrim(JURI::root(), '/') . '/' . $this->getAlias(false);

			return $url;
		}

		$options = array('id' => $this->getAlias());

		if ($external) {
			$options['external'] = true;
		}

		$options['sef'] = $sef;

		$url = FRoute::profile($options , $xhtml);

		return $url;
	}

	/**
	 * Allows caller to set a field value given the unique key
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setFieldValue($key, $value)
	{
		$data 	= $this->getProfile()->getCustomFields();

		if (!$data) {
			return false;
		}

		$fields 	= array();

		foreach ($data as $field) {
			$fields[$field->unique_key]	= $field;
		}

		if (!isset($fields[$key])) {
			return false;
		}

		// Get the field
		$field 	= $fields[$key];

		$model 	= FD::model('Fields');
		$state 	= $model->setValue($this->id, SOCIAL_TYPE_USER, $field, $value);

		return $state;
	}

	/**
	 * Retrieves the custom field formatted value from this user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFieldValue($key)
	{
		static $processed 	= array();

		if (!isset($processed[$this->id])) {
			$processed[ $this->id ]	= array();
		}

		if (!isset($processed[$this->id][$key])) {

			// Get the field
			if (!isset(SocialUserStorage::$fields[$this->id][$key])) {
				// We use getCustomFields instead for multirow data support
				// getFieldsData is deprecated
				// $result = FD::model('Fields')->getFieldsData(array('uid' => $this->id, 'type' => SOCIAL_TYPE_USER, 'key' => $key));
				$result = FD::model('Fields')->getCustomFields(array('group' => SOCIAL_TYPE_USER, 'uid' => $this->profile_id, 'data' => true , 'dataId' => $this->id , 'dataType' => SOCIAL_TYPE_USER, 'key' => $key));

				SocialUserStorage::$fields[$this->id][$key] = isset($result[0]) ? $result[0] : false;
			}

			$field 	= SocialUserStorage::$fields[$this->id][$key];

			// Initialize a default property
			$processed[ $this->id ][ $key ]	= '';

			if ($field) {

				// Trigger the getFieldValue to obtain data from the field.
				$value 	= FD::fields()->getValue( $field );

				$processed[ $this->id ][ $key ] 	= $value;
			}
		}


		return $processed[ $this->id ][ $key ];
	}

	/**
	 * Retrieves the custom field raw data from this user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFieldData( $key , $default = '' )
	{
		static $processed 	= array();

		if( !isset( $processed[ $this->id ] ) )
		{
			$processed[ $this->id ]	= array();
		}

		if( !isset( $processed[ $this->id ][ $key ] ) )
		{
			if (!isset(SocialUserStorage::$fields[$this->id][$key])) {
				$result = FD::model('Fields')->getCustomFields(array('group' => SOCIAL_TYPE_USER, 'uid' => $this->profile_id, 'data' => true , 'dataId' => $this->id , 'dataType' => SOCIAL_TYPE_USER, 'key' => $key));

				SocialUserStorage::$fields[$this->id][$key] = isset($result[0]) ? $result[0] : false;
			}

			$field 	= SocialUserStorage::$fields[$this->id][$key];

			// Initialize a default property
			$processed[ $this->id ][ $key ]	= '';

			if( $field )
			{
				// Trigger the getFieldValue to obtain data from the field.
				$value 	= FD::fields()->getData( $field );

				$processed[ $this->id ][ $key ] 	= $value;
			}
		}

		return $processed[ $this->id ][ $key ];
	}

	/**
	 * Returns the total number of groups the user created.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalCreatedGroups()
	{
		static $total 	= array();

		if(!isset($total[ $this->id ])) {
			$model = FD::model('Groups');

			$total[$this->id] = $model->getTotalCreated($this->id, SOCIAL_TYPE_USER);
		}

		return $total[$this->id];
	}

	/**
	 * Returns the total number of groups the user participated.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalGroups($filter = array())
	{
		static $total 	= array();

		if (!isset($total[$this->id])) {
			$model = FD::model('Groups');

			$total[$this->id] = $model->getTotalParticipatedGroups($this->id, $filter);
		}

		return $total[$this->id];
	}

	/**
	 * Returns the total number of followers the user has
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getTotalFollowers()
	{
		static $total 	= array();

		if( !isset($total[ $this->id ] ) )
		{
			$model	= FD::model( 'Followers' );

			$total[ $this->id ]	= $model->getTotalFollowers( $this->id );
		}

		return $total[ $this->id ];
	}

	/**
	 * Retrieves the total albums the user has
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalAlbums( $excludeCore = false )
	{
		static $total 	= array();

		if( !isset( $total[ $this->id ] ) )
		{
			$model 		= FD::model( 'Albums' );
			$options 	= array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_USER );

			if( $excludeCore )
			{
				$options[ 'excludeCore' ]	= $excludeCore;
			}

			$total[ $this->id ] = $model->getTotalAlbums( $options );
		}

		return $total[ $this->id ];
	}

	/**
	 * Retrieves the total number of videos the user has
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalVideos($daily = false, $includeUnpublished = false)
	{
		static $total 	= array();

		$sid = $this->id . (int) $daily . (int) $includeUnpublished;

		if (!isset($total[$sid])) {

			$model = ES::model('Videos');
			$options = array('userid' => $this->id);

			if ($includeUnpublished) {
				$options['state'] = 'all';
			}

			if ($daily) {
				$today = ES::date()->toMySQL();
				$date = explode(' ', $today);

				$options['day'] = $date[0];
			}

			$total[$sid] = $model->getTotalVideos($options);
		}

		return $total[$sid];
	}

	/**
	 * Retrieves the total photos the user has
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPhotos($daily = false, $includeUnpublished = false)
	{
		static $total 	= array();

		$sid = $this->id . (int) $daily . (int) $includeUnpublished;

		if (!isset($total[$sid])) {

			$model   = FD::model('Photos');
			$options = array('uid' => $this->id, 'type' => SOCIAL_TYPE_USER);

			if ($includeUnpublished) {
				$options['state'] = 'all';
			}

			if ($daily) {
				$today 	= FD::date()->toMySQL();
				$date 	= explode( ' ', $today );

				$options['day'] = $date[0];
			}

			$total[$sid] = $model->getTotalPhotos($options);
		}

		return $total[$sid];
	}


	/**
	 * Returns the total number of badges the user has
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getTotalBadges()
	{
		static $total 	= array();

		if (!isset($total[$this->id])) {
			$model = FD::model('Badges');
			$total[$this->id] = $model->getTotalBadges($this->id);
		}

		return $total[$this->id];
	}

	/**
	 * Returns the total number of users this user follows.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getTotalFollowing()
	{
		static $total 	= array();

		if( !isset($total[ $this->id ] ) )
		{
			$model	= FD::model( 'Followers' );

			$total[ $this->id ]	= $model->getTotalFollowing( $this->id );
		}

		return $total[ $this->id ];
	}

	/**
	 * Retrieves the default friend list for this user.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialTableList
	 */
	public function getDefaultFriendList()
	{
		static $lists 	= array();

		if( !isset( $lists[ $this->id ] ) )
		{
			$list 	= FD::table( 'List' );
			$exists	= $list->load( array( 'default' => 1 , 'user_id' => $this->id ) );

			if( !$exists )
			{
				$lists[ $this->id ]	= false;
			}
			else
			{
				$lists[ $this->id ]	= $list;
			}
		}


		return $lists[ $this->id ];
	}

	/**
	 * Returns the total number of friends list the current user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getTotalFriendsList()
	{
		static $total	= array();

		if( ! isset( $total[ $this->id ] ) )
		{
			$model					= FD::model( 'Lists' );
			$total[ $this->id ] 	= $model->getTotalLists( $this->id );
		}

		return $total[ $this->id ];
	}

	/**
	 * Returns the total number of friends the current user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getTotalFriends()
	{
		static $total	= array();

		if( ! isset( $total[ $this->id ] ) )
		{
			$model	= FD::model( 'Friends' );
			$total[ $this->id ] 	= $model->getTotalFriends( $this->id );
		}

		return $total[ $this->id ];
	}

	/**
	 * Retrieves the oauth token
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOAuth( $client = '' )
	{
		$oauth 	= FD::table( 'OAuth' );

		$state 	= $oauth->load( array( 'client' => $client , 'uid' => $this->id , 'type' => SOCIAL_TYPE_USER ) );

		if( !$state )
		{
			return false;
		}

		return $oauth;
	}

	/**
	 * Retrieves the oauth token
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOAuthToken( $client = '' )
	{
		$oauth 	= FD::table( 'OAuth' );

		$oauth->load( array( 'client' => $client , 'uid' => $this->id , 'type' => SOCIAL_TYPE_USER ) );

		return $oauth->token;
	}

	/**
	 * Gets the oauth library for this user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isAssociated( $clientType = '' )
	{
		$oauth 	= FD::table( 'OAuth' );
		$state 	= $oauth->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_USER ) );

		if( !$state )
		{
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the total number of mutual friends.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalMutualFriends( $targetId )
	{
		static $data 	= array();

		if( !isset( $data[ $this->id ] ) )
		{
			$model 		= FD::model( 'Friends' );

			$total 		= $model->getMutualFriendCount( $this->id , $targetId );

			$data[ $this->id ]	= $total;
		}

		return $data[ $this->id ];
	}

	/**
	 * Gets the @SocialAccess object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialAccess
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getAccess()
	{
		static $data	= null;

		if (!isset($data[$this->id])) {
			$access = FD::access($this->id, SOCIAL_TYPE_USER);

			$data[$this->id] = $access;
		}

		return $data[$this->id];
	}

	/**
	 * Returns the total number of new notifications for this user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int		The total number of unread notifications the user has.
	 */
	public function getTotalNewNotifications()
	{
		static $total 	= null;

		if( is_null( $total ) )
		{
			$model 	= FD::model( 'Notifications' );
			$total	= $model->getCount( array( 'unread' => 1 , 'target' => array( 'id' => $this->id , 'type' => SOCIAL_TYPE_USER ) ) );
		}

		return $total;
	}

	/**
	 * Returns the total number of new conversations this user has not yet read.
	 *
	 * @param	null
	 * @return	int 	The total new conversations
	 */
	public function getTotalNewConversations()
	{
		static $results	= array();

		if( !isset( $results[ $this->id ] ) )
		{
			$model	= FD::model( 'Conversations' );
			$total 	= $model->getConversations( $this->id , array( 'count' => true , 'filter' => 'unread' ) );

			$results[ $this->id ]	= $total;
		}

		return $results[ $this->id ];
	}

	/**
	 * Returns the total number of new friend requests the user has.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int 		The total number of requests.
	 */
	public function getTotalFriendRequests()
	{
		static $results 	= array();

		if( !isset( $results[ $this->id ] ) )
		{
			$model 	= FD::model( 'Friends' );
			$total 	= $model->getTotalRequests( $this->id );

			$results[ $this->id ]	= $total;
		}

		return $results[ $this->id ];
	}

	/**
	 * Returns the total number of friend request a user made.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int 		The total number of requests.
	 */
	public function getTotalFriendRequestsSent()
	{
		static $results 	= array();

		if (!isset($results[$this->id])) {
			$model = FD::model('Friends');
			$total = $model->getTotalRequestSent($this->id);

			$results[$this->id] = $total;
		}

		return $results[$this->id];
	}

	/**
	 * Loads the user's session
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function loadSession()
	{
		$user 	= FD::user();

		$this->helper->loadSession( $this , $user );
	}

	/*
	 * Allows caller to update a specific field item given it's unique id and value.
	 *
	 * @param   int     $fieldId    The field id.
	 * @param   mixed   $value      The value for that field.
	 *
	 * @return  boolean True on success, false otherwise.
	 */
	public function updateField( $fieldId , $value )
	{
		$data   = FD::table( 'FieldData' );
		$data->loadByField( $fieldId , $this->node_id );

		$data->node_id  = $this->node_id;
		$data->field_id = $fieldId;
		$data->data     = $value;
		$data->data_binary  = $value;

		return $data->store();
	}

	/**
	 * Determines if this user account can be deleted.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteable()
	{
		if( $this->isSiteAdmin() )
		{
			return false;
		}

		// Check if this user's profile allows deletion.
		$profile 	= $this->getProfile();
		$params 	= $profile->getParams();

		if( $params->get( 'delete_account' ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Allows caller to delete a cover photo for a user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteCover()
	{
		$state 	= $this->cover->delete();

		// Reset this user's cover
		$this->cover 	= FD::table( 'Cover' );

		// Prepare the dispatcher
		FD::apps()->load( SOCIAL_TYPE_USER );
		$dispatcher		= FD::dispatcher();
		$args 			= array( &$this , &$this->cover );

		// @trigger: onUserCoverRemove
		$dispatcher->trigger( SOCIAL_TYPE_USER , 'onUserCoverRemove' , $args );

		return $state;
	}

	/**
	 * Override parent's delete implementation if necessary.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool	The delete state. True on success, false otherwise.
	 */
	public function delete()
	{
		$state = parent::delete();

		// Once the user is deleted, we also need to delete it from the #__social_users table.
		if ($state) {
			$model = FD::model('Users');
			$model->delete($this->id);

	        JPluginHelper::importPlugin('finder');
	        $dispatcher = JDispatcher::getInstance();
	        $dispatcher->trigger( 'onFinderAfterDelete', array( 'easysocial.users' , $this ) );
		}

		return $state;
	}

	/**
	 * Alternative to store if we just want to save the user's details in #__social_users
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool
	 * @return	bool
	 */
	public function store()
	{
		$user 	= FD::table('Users');
		$user->loadByUser( $this->id );

		$user->user_id 	= $this->id;
		$user->state 	= $this->state;
		$user->type 	= $this->type;
		$user->alias 	= $this->alias;
		$user->auth		= $this->auth;

		$state 	= $user->store();

		return $state;
	}

	/**
	 * Override parent's implementation when save so we could run some pre / post rendering.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool
	 * @return	bool
	 */
	public function save($updateOnly = false)
	{
		// Determine if this record is a new user by identifying the id.
		$isNew		= $this->isNew();

		// Request parent to store data.
		// $state 		= parent::save($updateOnly);

		// Joomla 3.3.0 sets the JUser object into the session when user login, and if we do parent::save, then SocialUser object will get updated into the session, and causes instance mismatch that leads up to user getting logged out.
		// Hence here we recreate a JUser object, bind it, and save it.
		// In order to make sure that getProperties() gets the correct properties (especially password), SocialUser::bind no longer binds to the to parent class.
		// This is partly because, JUser::bind encrypts password, and calling parent::bind will cause SocialUser->password to no longer have the original clear password, and calling getProperties from here will get you the encrypted password to rebind again.
		if ($isNew) {
			$user = new JUser();
		} else {
			$user = JFactory::getUser($this->id);
		}

		// We only want to bind data that JUser needs
		$vars = get_object_vars($user);
		$data = array();

		foreach ($vars as $key => $val) {
			if (isset($this->$key)) {
				$data[$key]	= $this->$key;
			}
		}

		// Need a custom check for password2
		if (isset($this->password2)) {
			$data['password'] = $this->password;
			$data['password2'] = $this->password2;
		} else {

			// This is to prevent Joomla from throwing PHP notice error because bind actions expects both password and password2 to exist.
			if (!$isNew) {
				unset($data['password']);
			}
		}

		// lets re-arrange the 'groups' so that other user plugins can facilidate the user data
		if (isset($data['groups']) && $data['groups']) {

			$newG = array();
			foreach($data['groups'] as $key => $val) {
				if (is_int($val)) {
					$newG[] = $val;
				} else {
					$newG[] = $key;
				}
			}
			// now we reassgn the groups back to the data.
			$data['groups'] = $newG;
		}

		$user->bind($data);
		$state = $user->save($updateOnly);

		$this->setProperties($user->getProperties());

		// Once the #__users table is updated, we need to update ours as well.
		if ($state) {

			$userTable = FD::table('Users');
			$userTable->loadByUser( $this->id );

			$userTable->user_id	= $this->id;
			$userTable->state	= $this->state;
			$userTable->type	= $this->type;
			$userTable->alias	= $this->alias;
			$userTable->auth	= $this->auth;
			$userTable->completed_fields = $this->completed_fields;
			$userTable->require_reset = $this->require_reset;

			$state = $userTable->store();

			// @TODO: Set the default parameters and connections?
			// $this->params = $this->param->toString();
			// $user->set( 'params'		, $params );
			// $user->set( 'connections'	, $connections );
		} else {
			$this->setError($user->getError());
		}

		return $state;
	}

	/**
	 * Activates a user account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function activate( $sendEmail = true )
	{
		// Load Joomla users plugin for triggers.
		JPluginHelper::importPlugin('user');

		// Set joomla parameters
		$this->activation 	= '';
		$this->block 		= 0;

		// Update the current state property.
		$this->state 	= SOCIAL_USER_STATE_ENABLED;

		// Try to save the user.
		$state 			= $this->save();

		// Save the user.
		if( !$state )
		{
			$this->setError( $this->getError() );
			return false;
		}

		//index user into com_finder
		$this->syncIndex();

		// @points: user.register
		// Assign points when user registers on the site.
		$points = Foundry::points();
		$points->assign('user.registration', 'com_easysocial', $this->id);

		return true;
	}

	/**
	 * Approves a user's registration application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve( $sendEmail = true )
	{
		// Check if the user already approved or not.
		if ($this->block == 0 && $this->state == SOCIAL_USER_STATE_ENABLED) {
			//already approved.
			return true;
		}

		// Update the JUser object.
		$this->block = 0;

		// Update the current state property.
		$this->state = SOCIAL_USER_STATE_ENABLED;

		// If set to admin approve, user should be activated regardless of whether user activates or not.
		$this->activation = 0;

		// Store the block status
		$this->save();

		// Check this user profile registration type is it got auto join group action
		// Get the current profile this user has registered on.
		$profile = $this->getProfile();

		// Assign users into the EasySocial groups
		$defaultGroups = $profile->getDefaultGroups();

		if ($defaultGroups) {
			foreach ($defaultGroups as $group) {
				$group->createMemberViaAutoJoinGroups($this->id);
			}
		}

		// Activity logging.
		// Announce to the world when a new user registered on the site.
		$config = FD::config();

		// Get the application params
		$app = FD::table('App');
		$options = array('element' => 'profiles', 'group' => SOCIAL_TYPE_USER);

		$app->load($options);
		$params = $app->getParams();

		// If not allowed, we will not want to proceed here.
		if ($params->get('stream_register', true)) {

			$stream = FD::stream();

			// Get stream template
			$streamTemplate = $stream->getTemplate();

			// Set the actors.
			$streamTemplate->setActor($this->id, SOCIAL_TYPE_USER);

			// Set the context for the stream.
			$streamTemplate->setContext($this->id, SOCIAL_TYPE_PROFILES);

			// Set the verb for this action as this is some sort of identifier.
			$streamTemplate->setVerb('register');

			$streamTemplate->setSiteWide();

			$streamTemplate->setAccess('core.view');

			// Add the stream item.
			$stream->add($streamTemplate);
		}

		// add user into com_finder index
		$this->syncIndex();

		// @points: user.register
		// Assign points when user registers on the site.
		$points = FD::points();
		$points->assign('user.registration', 'com_easysocial', $this->id);

		// @badge: registration.create
		// Assign badge for the person that initiated the friend request.
		$badge = FD::badges();
		$badge->log( 'com_easysocial' , 'registration.create' , $this->id , JText::_( 'COM_EASYSOCIAL_REGISTRATION_BADGE_REGISTERED' ) );

		// If we need to send email to the user, we need to process this here.
		if ($sendEmail) {

			// Get the application data.
			$jConfig = FD::jConfig();

			// Get the current profile this user has registered on.
			$profile = $this->getProfile();

			// Push arguments to template variables so users can use these arguments
			$params = array(
							'site' => $jConfig->getValue( 'sitename' ),
							'username' => $this->username,
							'name' => $this->getName(),
							'avatar' => $this->getAvatar( SOCIAL_AVATAR_LARGE ),
							'email' => $this->email,
							'profileType' => $profile->get('title'),
							'manageAlerts' => false
							);

			JFactory::getLanguage()->load('com_easysocial', JPATH_ROOT);

			// Get the email title.
			$title = JText::_('COM_EASYSOCIAL_EMAILS_REGISTRATION_APPLICATION_APPROVED');

			// Immediately send out emails
			$mailer = FD::mailer();

			$mailTemplate = $mailer->getTemplate();

			$mailTemplate->setTitle($title);
			$mailTemplate->setRecipient($this->getName(), $this->email);
			$mailTemplate->setTemplate('site/registration/approved', $params);
			$mailTemplate->setLanguage($this->getLanguage());

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

			// Try to send out email now.
			$mailer->create($mailTemplate);
		}

		return true;
	}

	/**
	 * Reject's a user's registration application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject( $reason = '' , $sendEmail = true , $deleteUser = false )
	{
		// Announce to the world when a new user registered on the site.
		$config 			= FD::config();

		// If we need to send email to the user, we need to process this here.
		if( $sendEmail )
		{
			// Get the application data.
			$jConfig 	= FD::jConfig();

			// Get the current profile this user has registered on.
			$profile 	= $this->getProfile();

			// Push arguments to template variables so users can use these arguments
			$params 	= array(
									'site'			=> $jConfig->getValue( 'sitename' ),
									'username'		=> $this->username,
									'name'			=> $this->getName(),
									'email'			=> $this->email,
									'reason'		=> $reason,
									'profileType'	=> $profile->get( 'title' ),
									'manageAlerts'	=> false
							);

			JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT );

			// Get the email title.
			$title      = JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_REJECTED_EMAIL_TITLE' );

			// Immediately send out emails
			$mailer 	= FD::mailer();

			// Get the email template.
			$mailTemplate	= $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient( $this->getName() , $this->email );

			// Set title
			$mailTemplate->setTitle( $title );

			// Set the contents
			$mailTemplate->setTemplate( 'site/registration/rejected' , $params );

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority( SOCIAL_MAILER_PRIORITY_IMMEDIATE );

			// Try to send out email now.
			$mailer->create( $mailTemplate );
		}

		// If required, delete the user from the site.
		if($deleteUser)
		{
			$this->delete();
		}else{
			// else we need to 'expire' the activation token
			$this->activation = 1;

			// incase the user already activated, we need to block the user again.
			$this->block = 1;

			// now save juser
			$this->save();
		}

		return true;
	}

	/**
	 * Bind an array of data to the current user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	The object's properties.
	 * @param	bool	Determines whether the data is from $_POST method.
	 *
	 * @return 	bool	True if success false otherwise.
	 */
	public function bind(&$data, $post = false)
	{
		// Request the helper to bind specific additional details
		$this->helper->bind($this, $data);

		$this->setProperties($data);
	}

	/**
	 * Deprecated. Binds a single custom field data based on the given field element
	 *
	 * @since	1.2
	 * @deprecated Deprecated since 1.3.
	 * @access	public
	 * @param	Array	An array of data that is being posted.
	 * @return	bool	True on success, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function bindCustomField( $field )
	{
		SocialUserStorage::$fields[$this->id][$field->unique_key] = $field;
	}

	/**
	 * Binds the user custom fields.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of data that is being posted.
	 * @return	bool	True on success, false otherwise.
	 */
	public function bindCustomFields($data)
	{
		// Get the registration model.
		$model = ES::model('Fields');

		// Get the field id's that this profile is allowed to store data on.
		$fields	= $model->getStorableFields($this->profile_id, SOCIAL_TYPE_PROFILES);

		// If there's nothing to process, just ignore.
		if (!$fields) {
			return false;
		}

		// Let's go through all the storable fields and store them.
		foreach ($fields as $fieldId) {

			$key = SOCIAL_FIELDS_PREFIX . $fieldId;

			if (!isset($data[$key])) {
				continue;
			}

			// Get the value
			$value = isset($data[$key]) ? $data[$key] : '';

			// Test if field really exists to avoid any unwanted input
			$field = ES::table('Field');

			// If field doesn't exist, just skip this.
			if (!$field->load($fieldId)) {
				continue;
			}

			$field->saveData($value, $this->id, SOCIAL_TYPE_USER);
		}
	}

	/**
	 * Binds the privacy object for the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindPrivacy( $privacy , $privacyIds , $customIds, $privacyOld, $resetPrivacy = false )
	{
		$privacyLib = FD::privacy();
		//$resetMap 	= call_user_func_array( array( $privacyLib , 'getResetMap' ) );
		$resetMap 	= $privacyLib->getResetMap();

		$result 	= array();

		if( empty( $privacy ) )
		{
			return false;
		}

		foreach( $privacy as $group => $items )
		{
			foreach( $items as $rule => $value )
			{
				$id		= $privacyIds[ $group ][ $rule ];
				$id 	= explode( '_' , $id );

				$custom			= $customIds[ $group ][ $rule ];
				$customUsers	= array();
				$curVal 	 	= $privacyOld[ $group ][ $rule ];

				// Break down custom user rules
				if( !empty( $custom ) )
				{
					$tmp 	= explode( ',' , $custom );

					foreach( $tmp as $userId )
					{
						if( !empty( $userId ) )
						{
							$customUsers[]	= $userId;
						}
					}
				}

				$obj 			= new stdClass();
				$obj->id		= $id[ 0 ];
				$obj->mapid		= $id[ 1 ];
				$obj->value		= $value;
				$obj->custom	= $customUsers;

				$obj->reset  = false;

				//check if require to reset or not.
				$gr = strtolower( $group . '.' . $rule );
				if( $resetPrivacy && in_array( $gr,  $resetMap ) )
				{
					$obj->reset = true;
				}

				$result[]	= $obj;
			}
		}

		$model 		= FD::model( 'Privacy' );
		$state 		= $model->updatePrivacy( $this->id , $result , SOCIAL_PRIVACY_TYPE_USER );

		if ($state) {
			//index user access in finder
			$this->syncIndex();
		}

		return $state;
	}

	/**
	 * Sync's the user record with Joomla smart search
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncIndex()
	{
		// Determines if this is a new account
		$isNew = $this->isNew();

		// Trigger our own finder plugin
        JPluginHelper::importPlugin('finder');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onFinderAfterSave', array('easysocial.users', &$this, $isNew));
	}

	/**
	 * Determines if the user exceeded their friend request limit
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exceededFriendLimit()
	{
		$access 	= $this->getAccess();
		$limit 		= $access->get('friends.limit');

		//TODO: Should get this in one query only.
		$total = $this->getTotalFriends() + $this->getTotalFriendRequestsSent();

		// Site admin should never be bound to this rule.
		if ($this->isSiteAdmin()) {
			return false;
		}

		if ($limit != 0 && $access->exceeded('friends.limit', $total)) {
			return true;
		}

		return false;
	}

	/**
	 * Allows caller to remove the user's avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeAvatar()
	{
		$avatar 	= FD::table( 'Avatar' );
		$state 		= $avatar->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_USER ) );

		if( $state )
		{
			$state 		= $avatar->delete();

			// Prepare the dispatcher
			FD::apps()->load( SOCIAL_TYPE_USER );
			$dispatcher		= FD::dispatcher();
			$args 			= array( &$this , &$avatar );

			// @trigger: onUserAvatarRemove
			$dispatcher->trigger( SOCIAL_TYPE_USER , 'onUserAvatarRemove' , $args );
		}

		return $state;
	}

	/**
	 * Function to verify user password
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  string    $password Password to verify
	 * @return Boolean             State of the verification
	 */
	public function verifyUserPassword( $password )
	{
		$model = FD::model('Users');

		return $model->verifyUserPassword($this->id, $password);
	}

	/**
	 * Deprecated. Used to support <1.1 legacy language strings.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @return string    The gender term.
	 */
	public function getGenderTerm()
	{
		$gender = $this->getFieldData( 'GENDER' );

		$term = JText::_( 'COM_EASYSOCIAL_THEIR' );

		if( $gender == 1 )
		{
			$term = JText::_( 'COM_EASYSOCIAL_HIS' );
		}

		if( $gender == 2 )
		{
			$term = JText::_( 'COM_EASYSOCIAL_HER' );
		}

		return $term;
	}

	/**
	 * Used to construct a part of language strings to form a gender specific language strings
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @return string    The gender string as part of a full language string key.
	 */
	public function getGenderLang()
	{
		$gender = $this->getFieldData('GENDER');

		$term = '_NOGENDER';

		if ($gender == 1) {
			$term = '_MALE';
		}

		if ($gender == 2) {
			$term = '_FEMALE';
		}

		return $term;
	}

	/**
	 * Retrieves the language that the user is currently using
	 *
	 * @since	1.2
	 * @access	public
	 * @return	string	The locale of the language.
	 */
	public function getLanguage()
	{
		static $params = array();

		if (!isset($params[$this->id])) {

			$obj 	= FD::makeObject($this->params);

			// Get the locale the user is using
			$locale = !empty($obj->language) ? $obj->language : '';

			// If the user configures to use the site language, get the default language of the site.
			if (empty($locale)) {
				$jConfig 	= FD::jConfig();
				$locale 	= $jConfig->getValue('language');
			}

			$params[$this->id]	= $locale;
		}

		return $params[$this->id];
	}

	/**
	 * Function to verify current user the badges is viewable by the userId that passed in.
	 *
	 * @author Sam <sam@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  string    $userId user to check against
	 * @return Boolean   State of the verification
	 */
	public function badgesViewable( $userId )
	{
		if( $this->id != $userId )
		{
			$privacy 	= FD::privacy( $userId );

			if( !$privacy->validate( 'achievements.view' , $this->id , SOCIAL_TYPE_USER ) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns the total number of events that this user is invited to.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3
	 * @access public
	 * @return integer    The number of events invited.
	 */
	public function getTotalEvents()
	{
		return FD::model('Events')->getTotalEvents(array('guestuid' => $this->id, 'types' => 'all', 'gueststate' => SOCIAL_EVENT_GUEST_GOING));
	}

	/**
	 * Returns the total number of events that this user created.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3
	 * @access public
	 * @return integer    The number of events created.
	 */
	public function getTotalCreatedEvents($customOptions = array())
	{
		$baseOptions = array('creator_uid' => $this->id, 'creator_type' => SOCIAL_TYPE_USER, 'types' => 'all');

		$options = array_merge($baseOptions, $customOptions);

		return FD::model('Events')->getTotalEvents($options);
	}

	/**
	 * Sets the OTP settings for the user. This technique is borrowed from totp plugin
	 *
	 * @access public
	 * @since 1.3
	 */
	public function setOtpConfig($otpConfig)
	{
		// Create the encryptor class
		$key = FD::jConfig()->getValue('secret');
		$aes = new FOFEncryptAes($key, 256);

		// Create the encrypted option strings
		if (!empty($otpConfig->method) && ($otpConfig->method != 'none')) {

			$decryptedConfig = json_encode($otpConfig->config);
			$decryptedOtep   = json_encode($otpConfig->otep);

			// Bind the values to this user
			$this->otpKey    = $otpConfig->method . ':' . $aes->encryptString($decryptedConfig);
			$this->otep      = $aes->encryptString($decryptedOtep);
		}

		return $result;
	}

	/**
	 * Retrieves the user's one time password settings
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOtpConfig()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$otpConfig = new stdClass();
			$otpConfig->method = 'none';
			$otpConfig->config = array();
			$otpConfig->otep   = array();

			// Ensure the user has an otp set
			if (!$this->otpKey) {
				$cache[$this->id] = $otpConfig;

				return $cache[$this->id];
			}

			// Get the encrypted data
			list($method, $encryptedConfig) = explode(':', $this->otpKey, 2);
			$encryptedOtep = $this->otep;

			// Create an encryptor class
			$key = FD::jConfig()->getValue('secret');
			$aes = new FOFEncryptAes($key, 256);

			// Decrypt the data
			$decryptedConfig = $aes->decryptString($encryptedConfig);
			$decryptedOtep 	 = $aes->decryptString($encryptedOtep);

			// Remove the null padding added during encryption
			$decryptedConfig = rtrim($decryptedConfig, "\0");
			$decryptedOtep   = rtrim($decryptedOtep, "\0");

			// Update the configuration object
			$otpConfig->method = $method;
			$otpConfig->config = @json_decode($decryptedConfig);
			$otpConfig->otep   = @json_decode($decryptedOtep);

			/*
			 * If the decryption failed for any reason we essentially disable the
			 * two-factor authentication. This prevents impossible to log in sites
			 * if the site admin changes the site secret for any reason.
			 */
			if (is_null($otpConfig->config)) {
				$otpConfig->config = array();
			}

			if (is_object($otpConfig->config)) {
				$otpConfig->config = (array) $otpConfig->config;
			}

			if (is_null($otpConfig->otep)) {
				$otpConfig->otep = array();
			}

			if (is_object($otpConfig->otep)) {
				$otpConfig->otep = (array) $otpConfig->otep;
			}

			$cache[$this->id] = $otpConfig;
		}

		return $cache[$this->id];
	}

}

/**
 * This class would be used to store all user objects
 *
 */
class SocialUserStorage
{
	static $users 	= array();
	static $fields 	= array();
	static $badges 	= array();
}
