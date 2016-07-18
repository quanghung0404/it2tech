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

// Load parent table
FD::import( 'admin:/tables/table' );

class SocialTableAvatar extends SocialTable
{
	/**
	 * The unique id for this record.
	 * @var int
	 */
	public $id			= null;

	/**
	 * The unique type id for this record.
	 * @var int
	 */
	public $uid 		= null;

	/**
	 * The unique type string for this record.
	 * @var string
	 */
	public $type 		= null;

	/**
	 * The unique type id for this record. (Optional: used only when tied to a pre-defined avatar list)
	 * @var int
	 */
	public $avatar_id = null;

	/**
	 * This is the foreign key for #__social_photos
	 * @var int
	 */
	public $photo_id = null;

	/**
	 * The small version of avatar.
	 * @var string
	 */
	public $small		= null;

	/**
	 * The medium version of avatar.
	 * @var string
	 */
	public $medium		= null;

	/**
	 * The square version of avatar.
	 * @var string
	 */
	public $square      = null;

	/**
	 * The large version of avatar.
	 * @var string
	 */
	public $large       = null;

	/**
	 * The modified date of an avatar.
	 * @var string
	 */
	public $modified      = null;

	/**
	 * The storage path for the avatar
	 * @var string
	 */
	public $storage		= 'joomla';

	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @param	JDatabase
	 */
	public function __construct( $db )
	{
		parent::__construct('#__social_avatars', 'id', $db);
	}

	/**
	 * Responsible to store the uploaded images.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function upload( $file )
	{
		// Get config object.
		$config 	= FD::config();

		// Do not proceed if image doesn't exist.
		if( empty( $file ) || !isset( $file[ 'tmp_name' ] ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_AVATARS_FILE_UNAVAILABLE' ) );
			return false;
		}

		// Get the default avatars storage location.
		$avatarsPath 	= JPATH_ROOT . '/' . FD::cleanPath( $config->get( 'avatars.storage.container' ) );

		// Test if the avatars path folder exists. If it doesn't we need to create it.
		if (!FD::makeFolder($avatarsPath)) {
			$this->setError( JText::_( 'Errors when creating default container for avatar' ) );
			return false;
		}

		// Get the default avatars storage location for this type.
		$typePath 		= $config->get( 'avatars.storage.' . $this->type );
		$storagePath 	= $avatarsPath . '/' . FD::cleanPath( $typePath );

		// Ensure storage path exists.
		if (!FD::makeFolder($storagePath)) {
			$this->setError( JText::_( 'Errors when creating path for avatar' ) );
			return false;
		}

		// Get the profile id and construct the final path.
		$idPath 		= FD::cleanPath( $this->uid );
		$storagePath 	= $storagePath . '/' . $idPath;

		// Ensure storage path exists.
		if (!FD::makeFolder($storagePath)) {
			$this->setError( JText::_( 'Errors when creating default path for avatar' ) );
			return false;
		}

		// Get the image library to perform some checks.
		$image 	= FD::get( 'Image' );
		$image->load( $file[ 'tmp_name' ] );

		// Test if the image is really a valid image.
		if (!$image->isValid()) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_AVATARS_FILE_NOT_IMAGE' ) );
			return false;
		}

		// Process avatar storage.
		$avatar 	= FD::get( 'Avatar' , $image );

		// Let's create the avatar.
		$sizes 		= $avatar->create( $storagePath );

		if ($sizes === false) {
			$this->setError( JText::_( 'Sorry, there was some errors when creating the avatars.' ) );
			return false;
		}

		// Delete previous files.
		$this->deleteFile( $storagePath );

		// Assign the values back.
		foreach( $sizes as $size => $url )
		{
			$this->$size	= $url;
		}

		return true;
	}

	/**
	 * Override parent's behavior of deleting as we also need to delete physical files.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function delete( $pk = null )
	{
		$state 	= parent::delete();

		if( !$state )
		{
			return false;
		}

		// Get config
		$config 		= FD::config();

		// Get the default avatars storage location.
		$avatarsPath 	= JPATH_ROOT . '/' . FD::cleanPath( $config->get( 'avatars.storage.container' ) );

		// Test if the avatars path folder exists. If it doesn't we need to create it.
		if (!FD::makeFolder($avatarsPath)) {
			$this->setError( JText::_( 'Errors when creating default container for avatar' ) );
			return false;
		}

		// Get the default avatars storage location for this type.
		$typePath 		= $config->get( 'avatars.storage.' . $this->type );
		$storagePath 	= $avatarsPath . '/' . FD::cleanPath( $typePath );

		// Set the absolute path based on the uid.
		$storagePath 	= $storagePath . '/' . $this->uid;

		$this->deleteFolder( $storagePath );

		return $state;
	}

	/**
	 * Deletes the current variation of avatars given the absolute path to an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The absolute path to the item.
	 * @return	bool		True if success, false otherwise.
	 */
	public function deleteFolder( $path )
	{
		jimport( 'joomla.filesystem.folder' );

		// Test if the path exists.
		if(!JFolder::exists( $path ) )
		{
			return false;
		}

		$state	= JFolder::delete( $path );

		return $state;
	}

	/**
	 * Deletes the current variation of avatars given the absolute path to an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The absolute path to the item.
	 * @return	bool		True if success, false otherwise.
	 */
	public function deleteFile( $storagePath )
	{
		jimport( 'joomla.filesystem.file' );

		// Delete small variations.
		$small 	= $storagePath . '/' . $this->small;

		if( JFile::exists( $small ) )
		{
			JFile::delete( $small );
		}

		// Delete medium variations.
		$medium 	= $storagePath . '/' . $this->medium;

		if( JFile::exists( $medium ) )
		{
			JFile::delete( $medium );
		}

		// Delete large variations.
		$large 	= $storagePath . '/' . $this->large;

		if( JFile::exists( $large ) )
		{
			JFile::delete( $large );
		}

		// Delete medium variations.
		$square 	= $storagePath . '/' . $this->square;

		if( JFile::exists( $square ) )
		{
			JFile::delete( $square );
		}

		return true;
	}

	/**
	 * Retrieves the path to the avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	True to retrieve absolute path, false otherwise.
	 * @return
	 */
	public function getPaths( $relative = false )
	{
		$sizes 		= array( 'small' , 'medium' , 'large' , 'square' );
		$result 	= array();

		$path 	= '';

		if( !$relative )
		{
			$path 	= JPATH_ROOT;
		}

		// Get the initial storage path.
		$config	= FD::config();
		$path 	= $path . '/' . FD::cleanPath( $config->get( 'avatars.storage.container' ) );

		// Get the container path
		$path 	= $path . '/' . FD::cleanPath( $config->get( 'avatars.storage.' . $this->type ) );

		// Get the unique id path
		$path 	= $path . '/' . $this->uid;

		foreach( $sizes as $size )
		{
			$avatarPath 		= $path . '/' . $this->$size;

			$result[ $size ]	= $avatarPath;
		}


		return $result;
	}

	/**
	 * Retrieves the path to the avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	True to retrieve absolute path, false otherwise.
	 * @return
	 */
	public function getPath( $size = SOCIAL_AVATAR_MEDIUM , $absolute = false )
	{
		$config 	= FD::config();

		$source 	= '';

		if( $absolute )
		{
			$source 	= JPATH_ROOT;
		}

		$location 	= FD::cleanPath( $config->get( 'avatars.storage.container' ) );
		$location 	= $location . '/' . FD::cleanPath( $config->get( 'avatars.storage.' . $this->type ) );

		$location 	= $location . '/' . $this->uid . '/' . $this->$size;

		return $location;
	}

	/**
	 * Get's the uri to an avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determine if the absolute uri should be returned.
	 */
	public function getSource( $size = SOCIAL_AVATAR_MEDIUM , $absolute = true )
	{
		$config = FD::config();

		// If avatar_id is not empty, means this is this from the default avatars
		if (!empty($this->avatar_id)) {
			$default = FD::table('defaultavatar');
			$default->load( $this->avatar_id );

			return $default->getSource( $size, $absolute );
		}

		// If the avatar size that is being requested is invalid, return default avatar.
		if( !isset( $this->$size ) || empty( $this->$size ) )
		{
			return false;
		}

		// @TODO: Configurable storage path.
		$avatarLocation 	= FD::cleanPath( $config->get( 'avatars.storage.container' ) );
		$typesLocation 		= FD::cleanPath( $config->get( 'avatars.storage.' . $this->type ) );

		// Build absolute path to the file.
		$path	= JPATH_ROOT . '/' . $avatarLocation . '/' . $typesLocation . '/' . $this->uid . '/' . $this->$size;

		// Try to get the avatars from remote storage
		if ($this->storage == 'amazon') {

			$remotePath = $avatarLocation . '/' . $typesLocation . '/' . $this->uid . '/' . $this->$size;
			$storage = FD::storage('amazon');
			$uri = $storage->getPermalink($remotePath);

			$connector = ES::connector();
			$connector->addUrl($uri);
			$connector->useHeadersOnly();
			$connector->connect();

			$headers = $connector->getResult($uri, true);

			// If the avatar exist, return this uri.
			$notFound = stristr($headers, 'HTTP/1.1 404 Not Found');
			if ($notFound === false) {
				return $uri;
			}						
		}

		// Detect if avatar exists.
		if (!JFile::exists($path)) {
			$default = rtrim( JURI::root() , '/' ) . $config->get( 'avatars.default.user.' . $size );
			return $default;
		}

		// Build the uri path for the avatar.
		$uri 	= $avatarLocation . '/' . $typesLocation . '/' . $this->uid . '/' . $this->$size;

		if( $absolute )
		{
			$uri 	= rtrim( JURI::root() , '/' ) . '/' . $uri;
		}

	    return $uri;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addStream( $verb )
	{

		if( $verb == 'update' )
		{
			// Add stream item when a new photo is uploaded.
			$stream				= FD::stream();
			$streamTemplate		= $stream->getTemplate();

			// Set the actor.
			$streamTemplate->setActor( $this->uid , SOCIAL_TYPE_USER );

			// Set the context.
			$streamTemplate->setContext( $this->id , SOCIAL_TYPE_AVATAR );

			// Set the verb.
			$streamTemplate->setVerb( 'update' );

			//
			$streamTemplate->setType( 'full' );

			// Create the stream data.
			$stream->add( $streamTemplate );
		}
	}
}
