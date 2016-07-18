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

// Import the required file and folder classes.
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

class SocialAvatar
{
	private $path	= null;
	private $uid	= null;
	private $type	= null;

	/**
	 * Stores the size map of avatars.
	 * @var	Array
	 */
	private $maps		= array(
									'large'		=> '_large',
									'square'	=> '_square',
									'medium'	=> '_medium',
									'small'		=> '_small'
							);

	/**
	 * Stores the large avatar size. Since large avatars are proportionate, there's no fixed height.
	 * @var Array
	 */
	static $large 		= array(
									'width'		=> SOCIAL_AVATAR_LARGE_WIDTH,
									'mode'		=> 'proportionate'
							);

	/**
	 * Stores the square avatar size.
	 * @var Array
	 */
	static $square		= array(
									'width'		=> SOCIAL_AVATAR_SQUARE_LARGE_WIDTH ,
									'height'	=> SOCIAL_AVATAR_SQUARE_LARGE_HEIGHT,
									'mode'		=> 'fill'
							);

	/**
	 * Stores the medium avatar size.
	 * @var Array
	 */
	static $medium 		= array(
									'width'		=> SOCIAL_AVATAR_MEDIUM_WIDTH,
									'height'	=> SOCIAL_AVATAR_MEDIUM_HEIGHT,
									'mode'		=> 'fill'
							);

	/**
	 * Stores the small avatar size.
	 * @var Array
	 */
	static $small		= array(
									'width' 	=> SOCIAL_AVATAR_SMALL_WIDTH ,
									'height' 	=> SOCIAL_AVATAR_SMALL_HEIGHT,
									'mode'		=> 'fill'
							);


	/**
	 * Stores the image object.
	 * @var	SocialImage
	 */
	private $image 		= null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct( SocialImage &$image , $id = null , $type = null )
	{
		// Set the current image object.
		$this->image 	= $image;
		$this->uid 		= $id;
		$this->type 	= $type;

		// Get the target location
		$this->location	= $this->getPath();
	}

	/**
	 * Factory maker for this class.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function factory( $image , $id = null , $type = null )
	{
		$avatar 	= new self( $image , $id , $type );

		return $avatar;
	}

	/**
	 * Crops an image
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	SocialImage
	 */
	public function crop( $top = null , $left = null , $width = null , $height = null )
	{
		// Use the current image that was already loaded
		$image 		= $this->image;

		// Get the width and height of the photo
		$imageWidth  = $image->getWidth();
		$imageHeight = $image->getHeight();

		if( !is_null( $top ) && !is_null( $left ) && !is_null( $width ) && !is_null( $height ) )
		{
			$actualX	  = $imageWidth  * $left;
			$actualY 	  = $imageHeight * $top;
			$actualWidth  = $imageWidth  * $width;
			$actualHeight = $imageHeight * $height;

			// Now we'll need to crop the image
			$image->crop( $actualX , $actualY , $actualWidth , $actualHeight );
		}
		else
		{
			// If caller didn't provide a crop ratio, we crop the avatar to square

			// Get the correct positions
			if( $imageWidth > $imageHeight )
			{
				$x 	= ($imageWidth - $imageHeight ) / 2;
				$y 	= 0;
				$image->crop( $x , $y , $imageHeight , $imageHeight );
			}
			else
			{
				$x 	= 0;
				$y 	= ( $imageHeight - $imageWidth ) / 2;
				$image->crop( $x , $y , $imageWidth , $imageWidth );
			}
		}

		// We want to store the temporary image somewhere so that the image library could manipulate this file.
		$tmpImagePath 	= md5( FD::date()->toMySQL() ) . $image->getExtension();
		$jConfig 		= FD::jconfig();

		// Save the temporary cropped image
		$tmpImagePath 	= $jConfig->getValue( 'tmp_path' ) . '/' . $tmpImagePath;

		// Now, we'll want to save this temporary image.
		$image->save( $tmpImagePath );

		// Unset the image to free up some memory
		unset( $image );

		// Reload the image again to get the correct resource pointing to the cropped image.
		$image 	= FD::image();
		$image->load( $tmpImagePath );

		$this->image 	= $image;

		return $tmpImagePath;
	}

	/**
	 * Get the size names
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function generateFileNames()
	{
		$names		= array();

		foreach( $this->maps as $size => $postfix )
		{
			$names[ $size ]		= $this->image->getName( true ) . $postfix . $this->image->getExtension();
		}

		return $names;
	}

	/**
	 * Cleanup a folder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The target location to store the avatars
	 * @return
	 */
	public function cleanup( $avatarTable )
	{
		// Don't delete if the avatar is from gallery
		if( !empty( $avatarTable->avatar_id ) )
		{
			return true;
		}

		// Delete previous avatars.
		$paths 	= $avatarTable->getPaths( true );

		$storage = FD::storage( $avatarTable->storage );
		$storage->delete( $paths );


		return true;
	}

	/**
	 * Creates the necessary images to be used as an avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The target location to store the avatars
	 * @return
	 */
	public function create( &$avatarTable = null, $options = array() )
	{
		// Get a list of files to build.
		$names				= $this->generateFileNames();

		if( is_string( $avatarTable ) )
		{
			$targetLocation	= $avatarTable;
		}
		else
		{
			$targetLocation		= !empty( $targetLocation ) ? $targetLocation : $this->getPath();
		}


		foreach( $names as $size => $name )
		{
			$info 	= self::$$size;

			$image 	= $this->image->cloneImage();

			if( $info[ 'mode' ] == 'fill' )
			{
				$image->fill( $info[ 'width' ] , $info[ 'height' ] );
			}

			if( $info[ 'mode' ] == 'resize' )
			{
				$image->resize( $info[ 'width' ] , $info[ 'height' ] );
			}

			if( $info[ 'mode' ] == 'proportionate' )
			{
				$image->width( $info[ 'width' ] );
			}

			$path = $targetLocation . '/' . $name;

			if( JFile::exists($path) )
			{
				JFile::delete($path);
			}

			$image->save( $path );

			if( $avatarTable instanceof SocialTableAvatar )
			{
				$avatarTable->$size 	= $name;
			}
		}

		// Delete the tmp path once it's saved
		// Don't delete if options['deleteimage'] is specifically set to false
		if (!isset($options['deleteimage']) || $options['deleteimage'] != false) {
			$tmp = $image->getPath();

			if ($tmp) {
				JFile::delete($tmp);
			}
		}

		return $names;
	}

	/**
	 * Creates the necessary images to be used as an avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTablePhoto	The photo table
	 * @param 	array 				options - createStream
	 * @return
	 */
	public function store( SocialTablePhoto &$photo, $options = array() )
	{
		// setup the options.
		$createStream = isset( $options['addstream'] ) ? $options['addstream'] : true; // default to true.


		// Check if there's a profile photos album that already exists.
		$model		= FD::model( 'Albums' );

		// Create default album if necessary
		$album 		= $model->getDefaultAlbum( $this->uid , $this->type , SOCIAL_ALBUM_PROFILE_PHOTOS );

		// Load avatar table
		$avatarTable 		= FD::table( 'Avatar' );
		$exists 	= $avatarTable->load( array( 'uid' => $this->uid , 'type' => $this->type ) );

		// Cleanup previous avatars only if they exist.
		if( $exists )
		{
			$this->cleanup( $avatarTable );
		}

		// Create the images
		$this->create( $avatarTable, $options );

		// Set the avatar composite indices.
		$avatarTable->uid 		= $this->uid;
		$avatarTable->type 		= $this->type;

		// Link the avatar to the photo
		$avatarTable->photo_id 	= $photo->id;

		// Unlink the avatar from gallery item
		$avatarTable->avatar_id	= 0;

		// Set the last modified time to now.
		$avatarTable->modified 	= FD::date()->toMySQL();

		// We need to always reset the avatar back to "joomla"
		$avatarTable->storage 	= SOCIAL_STORAGE_JOOMLA;

		// Store the avatar now
		$avatarTable->store();

		// @points: profile.avatar.update
		// Assign points to the current user for uploading their avatar
		$photo->assignPoints( 'profile.avatar.update' , $this->uid );

		// @Add stream item when a new profile avatar is uploaded
		if( $createStream )
		{
			$photo->addPhotosStream( 'uploadAvatar' );
		}

		// Once the photo is finalized as the profile picture we need to update the state
		$photo->state 	= SOCIAL_STATE_PUBLISHED;

		// If album doesn't have a cover, set the current photo as the cover.
		if( !$album->hasCover() )
		{
			$album->cover_id 	= $photo->id;

			// Store the album
			$album->store();
		}

		// Prepare the dispatcher
		FD::apps()->load($this->type);

		if ($this->type == SOCIAL_TYPE_USER) {
			$node 	= FD::user($this->uid);


			// lets update user avatar sizes
			$userAvatars = $node->avatars;

			foreach($userAvatars as $size => $value) {
				$node->avatars[$size] = $avatarTable->{$size};
			}

			// we need to update finder index for this user for the updated avatar.
			$node->syncIndex();

		} else {
			$node	= FD::group($this->uid);
		}

		$args 			= array( &$photo , $node );
		$dispatcher		= FD::dispatcher();

		// @trigger: onUserAvatarUpdate
		$dispatcher->trigger( $this->type , 'onAvatarBeforeSave' , $args );

		// Once it is created, store the photo as we need to update
		$state 	= $photo->store();

		// @trigger: onUserAvatarUpdate
		$dispatcher->trigger( $this->type , 'onAvatarAfterSave' , $args );

		return $state;
	}

	/**
	 * Gets the storage path for photos folder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPath( $createFolders = true )
	{
		if( !empty( $this->location ) )
		{
			return $this->location;
		}

		// Construct destination path
		$config 	= FD::config();

		// Get initial storage path
		$storage 	= JPATH_ROOT;

		$container	= FD::cleanPath( $config->get( 'avatars.storage.container' ) );

		// Append it with the container path
		$storage 	= $storage . '/' . $container;

		// Ensure that the folder exists
		if( $createFolders )
		{
			FD::makeFolder( $storage );
		}

		// Append it with the type
		$containerType	= FD::cleanPath( $config->get( 'avatars.storage.' . $this->type ) );
		$storage 		= $storage . '/' . $containerType;

		// Ensure that the folder exists
		if( $createFolders )
		{
			FD::makeFolder( $storage );
		}

		// Construct the last segment which contains the uid.
		$storage 	= $storage . '/' . $this->uid;

		// Ensure that the path exists.
		if( $createFolders )
		{
			FD::makeFolder( $storage );
		}


		return $storage;
	}

	/**
	 * Gets the storage path for photos folder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStoragePath( $uid , $type , $createFolders = true )
	{
		// Construct destination path
		$config 	= FD::config();

		// Get initial storage path
		$storage 	= JPATH_ROOT;

		// Append it with the container path
		$storage 	= $storage . '/' . FD::cleanPath( $config->get( 'avatars.storage.container' ) );

		// Ensure that the folder exists
		if( $createFolders )
		{
			FD::makeFolder( $storage );
		}

		// Append it with the type
		$storage 	= $storage . '/' . FD::cleanPath( $config->get( 'avatars.storage.' . $type ) );

		// Ensure that the folder exists
		if( $createFolders )
		{
			FD::makeFolder( $storage );
		}

		// Construct the last segment which contains the uid.
		$storage 	= $storage . '/' . $uid;

		// Ensure that the path exists.
		if( $createFolders )
		{
			FD::makeFolder( $storage );
		}


		return $storage;
	}
}
