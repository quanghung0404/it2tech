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

// Import main table.
FD::import('admin:/tables/table');

class SocialTableProfile extends SocialTable
{
	/**
	 * The unique id for the profile type.
	 * @var int
	 */
	public $id = null;

	/**
	 * The title of the profile type
	 * @var string
	 */
	public $title = null;

	/**
	 * The alias of the profile type
	 * @var string
	 */
	public $alias = null;

	/**
	 * The description of the profile type
	 * @var string
	 */
	public $description = null;

	/**
	 * The title of the profile type
	 * @var int
	 */
	public $gid = null;

	/**
	 * The title of the profile type
	 * @var int
	 */
	public $default = false;

	/**
	 * The title of the profile type
	 * @var int
	 */
	public $registration = 1;

	/**
	 * The default avatar for this profile type.
	 * @var int
	 */
	public $default_avatar	= false;

	/**
	 * The creation date time of the profile
	 * @var datetime
	 */
	public $created = null;

	public $apps = null;

	/**
	 * The creation date time of the profile
	 * @var datetime
	 */
	public $modified = null;

	/**
	 * The state of the profile
	 * @var int
	 */
	public $state = 1;

	/**
	 * The parameters (JSON)
	 * @var string
	 */
	public $params = null;

	/**
	 * The ordering of the profile
	 * @var int
	 */
	public $ordering = null;

	/**
	 * The multisite id
	 * @var string
	 */
	public $site_id = null;

	/**
	 * The number of users in this profile type.
	 * @var int
	 */
	public $totalUsers 	= null;

	/**
	 * The users from this profile type.
	 * @var int
	 */
	public $users = null;

	/**
	 * Determines if the user has community access
	 * @var int
	 */
	public $community_access = true;

	public function __construct(& $db )
	{
		parent::__construct('#__social_profiles', 'id', $db);
	}

	// /**
	//  * Overrides parent's load implementation
	//  *
	//  * @since	1.0
	//  * @access	public
	//  */
	// public function load( $keys = null, $reset = true )
	// {

	// 	var_dump('here');
	// 	$state = parent::load( $keys, $reset );

	// 	return $state;
	// }
	/**
	 * Retrieves the list of steps for this particular profile type.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $table 	= FD::table( 'Profile' );
	 * $table->load( JRequest::getInt( 'id' ) );
	 *
	 * // Returns the steps for a particular profile type.
	 * $table->getSteps();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  array   An array of SocialTableWorkflow objects.
	 */
	public function getSteps( $type = null )
	{
		// Load language file from the back end as the steps title are most likely being translated.
		JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );

		$model 		= FD::model( 'Steps' );
		$steps		= $model->getSteps( $this->id , SOCIAL_TYPE_PROFILES, $type );

		return $steps;
	}

	/**
	 * Retrieves the total number of steps for this particular profile type.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $table 	= FD::table( 'Profile' );
	 * $table->load( JRequest::getInt( 'id' ) );
	 *
	 * // Returns the count in integer.
	 * $table->getTotalSteps();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  int		The number of steps involved for this profile type.
	 */
	public function getTotalSteps( $mode = null )
	{
		static $total 	= array();

		$totalKey = empty( $mode ) ? 'all' : $mode;

		if( !isset( $total[$totalKey] ) )
		{
			$model = FD::model( 'Fields' );
			$total[$totalKey] = $model->getTotalSteps( $this->id , SOCIAL_TYPE_PROFILES, $mode );
		}

		return $total[$totalKey];
	}

	/**
	 * Returns parameters for the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialRegistry
	 *
	 */
	public function getParams()
	{
		static $registry 	= array();

		if( !isset( $registry[ $this->id ] ) )
		{
			$registry[ $this->id ] 	= FD::get( 'Registry' , $this->params );
		}

		return $registry[ $this->id ];
	}

	/**
	 * Removes all the workflows for a particular profile type.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	True if success, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function resetSteps()
	{
		$db 	= FD::db();
		$query  = 'DELETE FROM ' . $db->nameQuote( '#__social_fields_steps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'profile_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$db->Query();
	}

	/**
	 * Binds the access for this profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindAccess( $post )
	{
		if( !is_array( $post ) || empty( $post ) )
		{
			return false;
		}

		// Load up the access table binding.
		$access	= FD::table( 'Access' );

		// Try to load the access records.
		$access->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_PROFILES ) );

		// Load the registry
		$registry 	= FD::registry( $access->params );

		foreach( $post as $key => $value )
		{
			$key 	= str_ireplace( '_' , '.' , $key );

			if (is_array($value)) {
				$value = FD::makeObject($value);
			}

			$registry->set( $key , $value );
		}

		$access->uid 		= $this->id;
		$access->type 		= SOCIAL_TYPE_PROFILES;
		$access->params 	= $registry->toString();

		// Try to store the access item
		if( !$access->store() )
		{
			$this->setError( $access->getError() );

			return false;
		}

		return true;
	}


	/**
	 * Adds a user into this profile.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $profile 	= FD::table( 'Profile' );
	 *
	 * // Adding logged in user into an existing profile.
	 * $profile->addUser( JFactory::getUser() );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The user's id.
	 */
	public function addUser( $userId )
	{
		$member		= FD::table( 'ProfileMap' );

		// Try to load previous record if it exists.
		$member->loadByUser( $userId );
		$member->profile_id 	= $this->id;
		$member->user_id		= $userId;
		$member->state			= SOCIAL_STATE_PUBLISHED;

		return $member->store();
	}

	/**
	 * Gets number of members from this profile group.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  int The total number of members
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getMembersCount( $publishedOnly = true, $excludeBlocked = false )
	{
		static $count 	= array();

		$key = $this->id . $publishedOnly . $excludeBlocked;

		if( !isset( $count[ $key ] ) )
		{
			$model 	= FD::model( 'Profiles' );
			$count[ $key ]	= $model->getMembersCount( $this->id, $publishedOnly, $excludeBlocked );
		}

		return $count[ $key ];
	}

	/**
	 * API to determine if the profile contains any members.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	boolean		True if contains members, false otherwise.
	 */
	public function hasMembers()
	{
		return $this->getMembersCount() > 0;
	}

	/**
	 * Returns the title of the profile.
	 *
	 * @access	public
	 * @param	null
	 */
	public function getTitle()
	{
		return JText::_( $this->title );
	}

	/**
	 * Retrieves the profile avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvatar( $size = SOCIAL_AVATAR_MEDIUM )
	{
		$avatar 	= FD::Table( 'Avatar' );
		$state 		= $avatar->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_PROFILES ) );

		if( !$state )
		{
			return $this->getDefaultAvatar( $size );
		}

		return $avatar->getSource( $size );
	}

	/**
	 * Retrieves the list of default avatars for this profile type
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultAvatar( $size = SOCIAL_AVATAR_MEDIUM )
	{
		$config = FD::config();
		$path = rtrim( JURI::root() , '/' ) . $config->get( 'avatars.default.profiles.' . $size );

		return $path;
	}

	/**
	 * Retrieves a list of default groups
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultGroups()
	{
		$params = $this->getParams();

		$items = $params->get('default_groups');

		$groups = array();

		if (!$items) {
			return $groups;
		}

		// Ensure that the items are unique
		$items = array_unique($items);

		foreach ($items as $id) {
			$group = ES::group($id);
			$groups[] = $group;
		}

		return $groups;
	}

	/**
	 * Override parent's bind method implementation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of key / value pairs for the table columns
	 * @param	Array	A list of ignored columns. (Optional)
	 * @return	bool	True if binded successfully.
	 *
	 */
	public function bind($data , $ignore = array(), $debug = false)
	{
		// Request the parent to bind the data.
		$state = parent::bind($data, $ignore);

		// Try to see if there's any params being set to the property as an array.
		if (!is_null($this->params) && is_array($this->params)) {

			$registry = ES::registry();

			foreach ($this->params as $key => $value) {
				$registry->set($key, $value);
			}

			// Set the params to a proper string.
			$this->params = $registry->toString();
		}

		// If there's an array of apps, we need to convert it into save-able data
		if ($this->apps && is_array($this->apps)) {
			$this->apps = json_encode($this->apps);
		}

		// If there is no default apps provided, it should be reset back to empty
		if (is_array($data) && !isset($data['apps'])) {
			$this->apps = '';
		}

		return $state;
	}

	/**
	 * Allows caller to pass in an array of gid to be binded to the object property.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of group id's.
	 * @return	boolean	True on success false otherwise.
	 */
	public function bindUserGroups($gids = array())
	{
		$gids = ES::makeArray($gids);

		if (is_array($gids) && !empty($gids)) {

			$this->gid = json_encode($gids);

			return true;
		}

		return false;
	}

	/**
	 * Override parent's store behavior.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Update null values.
	 * @return	bool	True on success, false otherwise.
	 * @author	Mark Lee <mark@stackides.com>
	 */
	public function store( $updateNulls = false )
	{
		// If created date is not provided, we generate it automatically.
		if (is_null($this->created)) {
			$this->created = FD::date()->toMySQL();
		}

		// Update the modified date.
		$this->modified = FD::date()->toMySQL();

		// Check the alias
		$model = FD::model('profiles');
		$alias = JFilterOutput::stringURLSafe( !empty( $this->alias ) ? $this->alias : $this->title );

		$i = 2;

		do
		{
			$aliasExists = $model->aliasExists($alias, $this->id);

			if( $aliasExists )
			{
				$alias .= '-' . $i++;
			}
		} while( $aliasExists );

		$this->alias = $alias;

		// Check if this is a new profile type.
		$isNew			= !$this->id;

		if( $isNew )
		{
			// Update ordering column.
			$this->ordering = $this->getNextOrder();
		}

		// Store the item now so that we can get the incremented profile id.
		$state	= parent::store( $updateNulls );

		// Create the default step and insert the core fields.
		if( $isNew )
		{
			// Create default profile steps and fields.
			FD::model( 'Fields' )->createDefaultItems( $this->id, SOCIAL_TYPE_PROFILES, SOCIAL_TYPE_USER );
		}

		return $state;
	}

	public function createBlank()
	{
		// If created date is not provided, we generate it automatically.
		if( is_null( $this->created ) )
		{
			$this->created 	= FD::date()->toMySQL();
		}

		// Update the modified date.
		$this->modified = FD::date()->toMySQL();

		// Update ordering column.
		$this->ordering = $this->getNextOrder();

		// Store the item now so that we can get the incremented profile id.
		$state	= parent::store();

		return $state;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function insertStream( $verb )
	{
		// Activity logging.
		// Announce to the world when a new profile is created so that the users can view this profile type.
		$config 			= FD::config();

		// If not allowed, we will not want to proceed here.
		if( !$config->get( 'profiles.stream.' . $verb ) )
		{
			return false;
		}

		// Get the stream library.
		$stream				= FD::stream();

		// Get stream template
		$streamTemplate		= $stream->getTemplate();

		// Set the actors.
		$streamTemplate->setActor( FD::user()->id , SOCIAL_TYPE_USER );

		// Set the context for the stream.
		$streamTemplate->setContext( $this->id , SOCIAL_TYPE_PROFILES );

		// Set the verb for this action as this is some sort of identifier.
		$streamTemplate->setVerb( $verb );

		$streamTemplate->setSiteWide();

		$streamTemplate->setAccess( 'core.view' );


		// Add the stream item.
		return $stream->add( $streamTemplate );
	}

	/*
	 * Determines whether this current profile type is associated with
	 * a custom field.
	 *
	 * @param   int $fieldId    The custom field's id.
	 * @return  boolean     True if associated, false otherwise.
	 */
	public function isChild( $fieldId )
	{
		$db 	= FD::db();
		$query  = 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__social_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->Quote( $fieldId ) . ' '
				. 'AND ' . $db->nameQuote( 'profile_id' ) . '=' . $db->Quote( $this->id );

		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	/**
	 * Sets this profile as the default profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function makeDefault($isCopy = false)
	{
		$db = FD::db();

		// Only 1 item can be default at a time, FIFO model
		$query = array();

		// Profile default value
		$this->default = false;

		// Check if this is save as copy, if yes we do not want to change the profile default value.
		if ($isCopy != true) {
			$query[] = 'UPDATE ' . $db->nameQuote($this->_tbl);
			$query[] = 'SET ' . $db->nameQuote('default') . '=' . $db->Quote(0);

			$db->setQuery($query);
			$db->Query();

			// Update the curent profile to default.
			$this->default = true;
		}
		
		$state = $this->store();

		return $state;
	}

	/**
	 * Deletes a profile off the system. Any related profiles stuffs should also be deleted here.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   mixed	An optional primary key value to delete.  If not set the instance property value is used.
	 */
	public function delete( $pk = null )
	{
		// Try to delete this profile item.
		$state	= parent::delete( $pk );

		// Delete custom fields created for this profile type
		if( $state )
		{
			// Delete all field relations to this profile.
			$model 	= FD::model( 'Fields' );
			$model->deleteFields( $this->id , SOCIAL_TYPE_PROFILES );

			// Delete all stream related items.
			$stream 	= FD::stream();
			$stream->delete( $this->id , SOCIAL_TYPE_PROFILES );

			// Delete profile avatar.
			$avatar 	= FD::table( 'Avatar' );

			if( $avatar->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_PROFILES ) ) )
			{
				$avatar->delete();
			}

			// Delete default avatars for this profile.
			$avatarModel 	= FD::model( "Avatars" );
			$avatarModel->deleteDefaultAvatars( $this->id , SOCIAL_TYPE_PROFILES );
		}

		return $state;
	}

	/**
	 * Validates the profile before storing. Basically, check on the necessary fields that is required and see if there's any errors.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	True if validates, false if there's errors.
	 */
	public function validate()
	{
		// Test if the title is provided since it is necessary.
		if( !$this->title )
		{
			$this->setError( JText::_( 'Profile title is invalid.' ) );
			return false;
		}

		// Test if the 'gid' is provided. Otherwise the user wont be in any group at all.

		return true;
	}

	/**
	 * Retrieves a list of default apps for a profile
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultApps()
	{
		if ($this->apps && is_string($this->apps)) {
			return json_decode($this->apps);
		}

		return array();
	}

	public function getEmailTitle( $type = '' )
	{
		// @TODO: Make this configurable.
		$title = FD::jConfig()->getValue('sitename');

		// Load the correct language selected in the registration form
		JFactory::getLanguage()->load('com_easysocial', JPATH_ROOT, $language);

		return JText::sprintf('COM_EASYSOCIAL_EMAILS_REGISTRATION_EMAIL_TITLE', $title);
	}

	/**
	 * Retrieves the email title for moderator.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getModeratorEmailTitle($username)
	{
		// @TODO: Make this configurable.
		$title = FD::jConfig()->getValue('sitename');

		$message = JText::sprintf('COM_EASYSOCIAL_EMAILS_REGISTRATION_MODERATOR_EMAIL_TITLE', $username, $title);

		return $message;
	}

	/**
	 * Retrieve the email template path.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The type of email.
	 *
	 */
	public function getEmailTemplate($type = '', $oauth = false)
	{
		$param	= $this->getParams();
		$type	= !empty($type) ? $type : $this->getRegistrationType(false, $oauth);

		$path 	= 'site/registration/' . $type;

		return $path;
	}

	/**
	 * Retrieve the email template path.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The type of email.
	 *
	 */
	public function getModeratorEmailTemplate($type = '', $oauth = false)
	{
		$param = $this->getParams();

		if (!$type) {
			$type = $this->getRegistrationType(false, $oauth);
		}

		$path = 'site/registration/moderator.' . $type;

		return $path;
	}

	/**
	 * Retrieve the email contents for a particular email type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The type of email.
	 *
	 */
	public function getEmailContents( $type = '' )
	{
		$param	= $this->getParams();
		$type	= !empty( $type ) ? $type : $param->get( 'registration' );

		return FD::themes()->output( 'site/registration.' . $type );
	}

	/**
	 * Retrieve the email contents for a particular email type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The type of email.
	 *
	 */
	public function getModeratorEmailContents( $type = '' )
	{
		$param	= $this->getParams();
		$type	= !empty( $type ) ? $type : $param->get( 'registration' );

		return FD::themes()->output( 'site/registration.' . $type . '.moderator' );
	}

	public function getEmailFormat( $type = '' )
	{
		$param  	= $this->getParams();
		$type   	= !empty( $type ) ? $type : $param->get( 'registration' );
		$html		= $param->get( 'email_html_' . $type );
		$html       = (bool) !empty( $html ) ? $html : true;
		return $html;
	}

	/**
	 * Retrieves the registration type for this profile type.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $table 	= FD::table( 'Profile' );
	 * $table->load( JRequest::getInt( 'id' ) );
	 *
	 * // Returns the registration type.
	 * $table->getRegistrationType();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Translated result if true.
	 * @return	string	The registration type in string.
	 */
	public function getRegistrationType($translate = false, $oauth = false)
	{
		// Load the params if it's not loaded.
		$param = $this->getParams();
		$key = $oauth ? 'oauth.registration' : 'registration';
		$type = $param->get($key, 'approvals');
		$data = $type;

		if ($translate) {
			$data = JText::_('COM_EASYSOCIAL_REGISTRATIONS_TYPE_' . strtoupper($type));
		}

		return $data;
	}

	/**
	 * Logics to store a profile avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function uploadAvatar( $file )
	{
		$avatar 	= FD::table( 'Avatar' );
		$state 		= $avatar->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_PROFILES ) );

		if( !$state )
		{
			$avatar->uid 	= $this->id;
			$avatar->type 	= SOCIAL_TYPE_PROFILES;

			$avatar->store();
		}

		// Determine the state of the upload.
		$state	= $avatar->upload( $file );

		if (!$state) {
			$this->setError( JText::_( 'There was some problems uploading the avatar' ) );
			return false;
		}

		// Store the data.
		$avatar->store();

		return;
	}

	/**
	 * Logics to copy default avatars from a profile.
	 *
	 * @since	1.1
	 * @access	public
	 * @author	Sam Teh <sam@stackideas.com>
	 */
	public function copyAvatar( $targetProfileId )
	{
		$avatarsModel 	= FD::model( 'Avatars' );
		$defaultAvatars = $avatarsModel->getDefaultAvatars( $targetProfileId );

		if( $defaultAvatars )
		{
			foreach( $defaultAvatars as $avatar )
			{
				unset( $avatar->id );

				$tbl = FD::table( 'DefaultAvatar' );
				$tbl->bind( $avatar );

				$tbl->uid 		= $this->id;
				$tbl->type 		= SOCIAL_TYPE_PROFILES;
				$tbl->created 	= FD::date()->toMySQL();

				$tbl->store();
			}

			// lets copy the image files.

			$config 	= FD::config();

			// Get the avatars storage path.
			$avatarsPath 	= FD::cleanPath( $config->get( 'avatars.storage.container' ) );

			// Get the defaults storage path.
			$defaultsPath	= FD::cleanPath( $config->get( 'avatars.storage.default' ) );

			// Get the types storage path.
			$typesPath		= FD::cleanPath( $config->get( 'avatars.storage.defaults.' . SOCIAL_TYPE_PROFILES ) );

			// Get the id storage path
			$sourceId		= FD::cleanPath( $this->uid );

			// Let's construct the final path.
			$sourcePath	= JPATH_ROOT . '/' . $avatarsPath . '/' . $defaultsPath . '/' . $typesPath . '/' . $targetProfileId;
			$targetPath	= JPATH_ROOT . '/' . $avatarsPath . '/' . $defaultsPath . '/' . $typesPath . '/' . $this->id;

			if( JFolder::exists( $targetPath ) )
			{
				return;
			}

			// now we are save to copy.
			if( JFolder::exists( $sourcePath ) )
			{
				JFolder::copy( $sourcePath, $targetPath );
			}
		}
	}

	/*
	 * Override parent's store params method to allow html codes
	 */
	public function storeParams()
	{
		$raw		= JRequest::getVar( 'params' , '' , 'POST' , 'none' , JREQUEST_ALLOWHTML );

		$param      = FD::get( 'Parameter' , '' );
		$param->bind( $raw );

		$this->params   = $param->toJSON();

		$this->store();
	}

	/**
	 * Retrieves the alias of the profile type
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$alias = ($this->alias) ? JFilterOutput::stringURLUnicodeSlug($this->alias) : JFilterOutput::stringURLUnicodeSlug($this->title);

		$alias = $this->id . ':' . $alias;

		return $alias;
	}

	/**
	 * Gets the permalink to the profiles view.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true)
	{
		$permalink = FRoute::profiles(array('layout' => 'item', 'id' => $this->getAlias()), $xhtml);

		return $permalink;
	}

	/**
	 * Checks if this step is valid depending on the mode/event
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Step id
	 * @param	string	Mode/event to check against
	 * @return	bool	True if it is valid
	 */
	public function isValidStep( $step, $mode = null )
	{
		$db = FD::db();

		$sql = $db->sql();

		$sql->select( '#__social_fields_steps' )
			->where( 'uid', $this->id )
			->where( 'type', SOCIAL_TYPE_PROFILES )
			->where( 'state', 1 )
			->where( 'sequence', $step );

		if( !empty( $mode ) )
		{
			$sql->where( 'visible_' . $mode, 1 );
		}

		$db->setQuery( $sql );

		$result = $db->loadResult();

		return !empty( $result );
	}

	/**
	 * Gets the sequence from the current index (sequence does not obey published state while index is reordered from published state)
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Current index
	 * @param	string	Mode/event to check against
	 * @return	int		The reverse mapped sequence
	 */
	public function getSequenceFromIndex($index, $mode = null)
	{
		$steps = $this->getSteps($mode);

		if (!isset($steps[$index - 1])) {
			return 0;
		}

		$sequence = $steps[$index - 1]->sequence;

		return $sequence;
	}

	/**
	 * Gets the index from the current sequence (sequence does not obey published state while index is reordered from published state)
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		Current sequence
	 * @param	string	Mode/event to check against
	 * @return	int		The reverse mapped index
	 */
	public function getIndexFromSequence( $sequence, $mode = null )
	{
		$steps = $this->getSteps( $mode );

		if( !empty( $steps ) && is_array( $steps ) )
		{
			$index = 1;

			foreach( $steps as $step )
			{
				if( $step->sequence == $sequence )
				{
					return $index;
				}

				$index++;
			}
		}

		return 1;
	}

	/**
	 * Retrieves the Joomla groups associated with the profile
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	Array
	 */
	public function getJoomlaGroups()
	{
		$gids = json_decode($this->gid);

		return $gids;
	}

	/**
	 * Check if this profile have avatar uploaded
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	True if this profile have avatar uploaded
	 */
	public function hasAvatar()
	{
		$avatar 	= FD::Table( 'Avatar' );
		$state 		= $avatar->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_PROFILES ) );

		return (bool) $state;
	}

	/**
	 * Removes the profile avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	Returns the state of the action
	 */
	public function removeAvatar()
	{
		$avatar 	= FD::Table( 'Avatar' );
		$state 		= $avatar->load( array( 'uid' => $this->id , 'type' => SOCIAL_TYPE_PROFILES ) );

		if( $state )
		{
			return $avatar->delete();
		}

		return true;
	}

	public function getCustomFields( $mode = null )
	{
		static $profileFields = array();

		if( empty( $profileFields[$this->id] ) )
		{
			$model = FD::model( 'Fields' );
			$fields = $model->getCustomFields( array( 'profile_id' => $this->id, 'state' => SOCIAL_STATE_PUBLISHED ) );

			$profileFields[$this->id] = $fields;
		}

		if( !empty( $mode ) )
		{
			$key = 'visible_' . $mode;
			$result = array();

			foreach( $profileFields[$this->id] as $field )
			{
				if( !empty( $field->$key ) )
				{
					$result[] = $field;
				}
			}

			return $result;
		}

		return $profileFields[$this->id];
	}

	public function isFieldExist( $key )
	{
		$fields = $this->getFields();

		foreach( $fields as $field )
		{
			if( $field->unique_key === strtoupper( $key ) )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if this profile type allows registration
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function allowsRegistration()
	{
		$allowed = (bool) $this->registration;

		return $allowed;
	}

	public function getTotalFields($visible = null)
	{
		static $cache = array();

		if (!isset($cache[$this->id][$visible])) {
			$db = FD::db();
			$sql = $db->sql();

			$sql->select('#__social_fields', 'a');
			$sql->leftjoin('#__social_fields_steps', 'b');
			$sql->on('a.step_id', 'b.id');

			// We actually need to cross this against app table to see if the apps are valid
			$sql->leftjoin('#__social_apps', 'c');
			$sql->on('a.app_id', 'c.id');
			$sql->where('b.uid', $this->id);
			$sql->where('b.type', SOCIAL_TYPE_PROFILES);
			$sql->where('c.state', SOCIAL_STATE_PUBLISHED);

			if (!empty($visible)) {
				$sql->where('a.visible_' . $visible, 1);
				$sql->where('b.visible_' . $visible, 1);
			}

			$db->setQuery($sql->getTotalSql());

			$cache[$this->id][$visible] = $db->loadResult();
		}

		return $cache[$this->id][$visible];
	}


	public function assignUsersApps($apps = array())
	{
		if (! $apps) {
			return false;
		}

		$appModel = ES::model('Apps');

		foreach($apps as $app) {
			$appModel->assignProfileUsersApps($this->id, $app);
		}

		return true;
	}
}
