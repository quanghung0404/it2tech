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

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

// Include the main table.
FD::import( 'admin:/tables/table' );

/**
 * Default covers table mapping.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialTableDefaultCover extends SocialTable
{
	/**
	 * The unique id of the default avatar.
	 * @var	int
	 */
	public $id			= null;

	/**
	 * The unique item id.
	 * @var	int
	 */
	public $uid 		= null;

	/**
	 * The unique item type. E.g: @SOCIAL_TYPE_USER
	 * @var string
	 */
	public $type 		= null;

	/**
	 * The title of this default avatar.
	 * @var string
	 */
	public $title       = null;

	/**
	 * The creation date of the default avatar.
	 * @var datetime
	 */
	public $created     = null;

	/**
	 * State of the avatar. 0 - unpublished , 1 -published.
	 * @var	int
	 */
	public $state			= null;

	/**
	 * The storage path to the avatar for large size.
	 * @var string
	 */
	public $large			= null;

	/**
	 * The storage path to the avatar for medium size.
	 * @var string
	 */
	public $medium			= null;

	/**
	 * The storage path to the avatar for small size.
	 * @var string
	 */
	public $small			= null;

	/**
	 * Determines if this avatar is created by the system / core avatars.
	 * @var bool
	 */
	public $default          = false;

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function __construct( $db )
	{
		parent::__construct('#__social_default_covers', 'id', $db);
	}

	/**
	 * Get's the absolute url for the image source.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The size to get. (SOCIAL_AVATAR_SMALL , SOCIAL_AVATAR_MEDIUM , SOCIAL_AVATAR_LARGE)
	 * @param	bool	True to use absolute path. (Optional, default is true)
	 * @return	string	The absolute url to the image.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getSource( $size = SOCIAL_COVER_SMALL , $absolute = true )
	{
		// Get configuration object.
		$config 	= FD::config();

		// Get the avatars storage path.
		$avatarsPath 	= FD::cleanPath( $config->get( 'covers.storage.container' ) );

		// Get the defaults storage path.
		$defaultsPath	= FD::cleanPath( $config->get( 'covers.storage.default' ) );

		// Get the types storage path.
		$typesPath		= FD::cleanPath( $config->get( 'covers.storage.defaults.' . $this->type ) );

		// Get the id storage path
		$idPath			= FD::cleanPath( $this->uid );

		// Let's construct the final path.
		$storagePath	= JPATH_ROOT . '/' . $avatarsPath . '/' . $defaultsPath . '/' . $typesPath . '/' . $idPath . '/' . $this->$size;

		// Let's test if the file exists.
		$exists 		= JFile::exists( $storagePath );

		if (!$exists) {
			$this->setError( JText::_( 'Cover file cannot be found' ) );
			return false;
		}

		// Construct the final uri;
		$uri 		= $avatarsPath . '/' . $defaultsPath . '/' . $typesPath . '/' . $idPath . '/' . $this->$size;

		// If caller wants absolute url, give them the site url.
		if( $absolute )
		{
			return rtrim( JURI::root() , '/' ) . '/' . $uri;
		}

		return $uri;
	}

	/*
	 * Loads an avatar object based on a specific node.
	 */
	public function loadByNode( $id )
	{
		$db 	= FD::db();
		$query  = 'SELECT * FROM ' . $db->nameQuote( $this->_tbl );
		$query  .= ' WHERE ' . $db->nameQuote( 'node_id' ) . '=' . $db->Quote( $id ) . ' '
				. 'AND ' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_DEFAULT );
		$db->setQuery( $query );

		return parent::bind( $db->loadAssoc() );
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
	public function upload( $files )
	{
		// Get config object.
		$config 	= FD::config();

		// Do not proceed if image doesn't exist.
		if( empty( $files ) || !isset( $files[ 'file' ] ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_COVERS_FILE_UNAVAILABLE' ) );
			return false;
		}

		// Get the single file input since the $files is an array.
		$file 		= $files[ 'file' ];

		// Get the default avatars storage location.
		$coversPath = JPATH_ROOT . '/' . FD::cleanPath( $config->get( 'covers.storage.container' ) );

		// Test if the avatars path folder exists. If it doesn't we need to create it.
		if (!FD::makeFolder($coversPath)) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_COVERS_UNABLE_TO_CREATE_CONTAINER_FOLDER' ) );
			return false;
		}

		// Get the defaults avatar path.
		$defaultsPath 	= $coversPath . '/' . FD::cleanPath( $config->get( 'covers.storage.default' ) );

		// Ensure that the defaults path exist
		if (!FD::makeFolder($defaultsPath)) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_COVERS_UNABLE_TO_CREATE_DEFAULT_FOLDER' ) );
			return false;
		}

		// Get the default avatars storage location for this type.
		$typePath 		= $config->get( 'covers.storage.defaults.' . $this->type );
		$storagePath 	= $defaultsPath . '/' . FD::cleanPath( $typePath );


		// Ensure storage path exists.
		if (!FD::makeFolder($storagePath)) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_COVERS_UNABLE_TO_CREATE_DEFAULT_FOLDER' ) );
			return false;
		}

		// Get the profile id and construct the final path.
		$idPath 		= FD::cleanPath( $this->uid );
		$storagePath 	= $storagePath . '/' . $idPath;

		// Ensure storage path exists.
		if( !FD::makeFolder( $storagePath ) ) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_COVERS_UNABLE_TO_CREATE_DEFAULT_FOLDER' ) );
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

		// Process covers storage.
		$cover 	= FD::get( 'Cover' , $image );

		// Try to create the covers.
		$sizes	= $cover->create( $storagePath );

		// Test if the server returned an error.
		if ($sizes === false) {
			$this->setError( JText::_( 'Sorry, there was some errors when creating the covers.' ) );
			return false;
		}

		// Assign the values back.
		foreach( $sizes as $size => $url )
		{
			$this->$size	= $url;
		}

		return true;
	}

	private function deleteImage( $folder , $size )
	{
		if( !empty( $this->$size ) )
		{
			$path   = $folder . DS . $this->$size;
			if( FD::get( 'Files' )->exists( $path ) )
			{
				return FD::get( 'Files' )->delete( $path );
			}
		}
	}

	/*
	 * Resets all avatars for this profile type.
	 */
	public function resetDefault()
	{
		$db 	= FD::db();
		$query  = 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' SET '
				. $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED ) . ' '
				. 'WHERE ' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_DEFAULT ) . ' '
				. 'AND ' . $db->nameQuote( 'node_id' ) . ' = ' . $db->Quote( $this->get( 'node_id' ) );
		$db->setQuery( $query );
		$db->Query();
	}

	/*
	 * Sets an avatar as the default avatar
	 */
	public function setDefault()
	{
		// @rule: Remove existing default items
		$this->resetDefault();

		$this->state    = SOCIAL_STATE_DEFAULT;
		$this->store();
	}
}
