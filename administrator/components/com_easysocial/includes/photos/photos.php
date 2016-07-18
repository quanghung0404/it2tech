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

/**
 * Photos library.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialPhotos
{
	private $path	= null;
	private $uid	= null;
	private $type	= null;

	/**
	 * Stores the size map of avatars.
	 * @var	Array
	 */
	static $sizes = array(

		'square' => array(
			'width'  => SOCIAL_PHOTOS_SQUARE_WIDTH,
			'height' => SOCIAL_PHOTOS_SQUARE_HEIGHT,
			'mode'   => 'fill'
		),

		'thumbnail' => array(
			'width'  => SOCIAL_PHOTOS_THUMB_WIDTH,
			'height' => SOCIAL_PHOTOS_THUMB_HEIGHT,
			'mode'   => 'outerFit'
		),

		'featured' => array(
			'width'  => SOCIAL_PHOTOS_FEATURED_WIDTH,
			'height' => SOCIAL_PHOTOS_FEATURED_HEIGHT,
			'mode'   => 'outerFit'
		),

		'large' => array(
			'width'  => SOCIAL_PHOTOS_LARGE_WIDTH,
			'height' => SOCIAL_PHOTOS_LARGE_HEIGHT,
			'mode'   => 'fit'
		)
	);

	/**
	 * Stores the image object.
	 * @var	SocialImage
	 */
	private $image = null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct( SocialImage &$image )
	{
		// Set the current image object.
		$this->image = $image;
	}

	/**
	 * Factory maker for this class.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function factory( $image )
	{
		$photo 	= new self( $image );

		return $photo;
	}

	/**
	 * Returns the image resource
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * Gets the storage path for photos folder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getStoragePath( $albumId , $photoId , $createFolders = true )
	{
		// Get destination folder path.
		$config 	= FD::config();
		$container	= FD::cleanPath($config->get('photos.storage.container'));
		$storage 	= JPATH_ROOT . '/' . $container;

		// Test if the storage folder exists
		if ($createFolders) {
			FD::makeFolder($storage);
		}

		// Set the storage path to the album
		$storage 	= $storage . '/' . $albumId;

		// If it doesn't exist, create it.
		if ($createFolders) {
			FD::makeFolder($storage);
		}

		// Create a new folder for the photo
		$storage 	= $storage . '/' . $photoId;

		if ($createFolders) {
			FD::makeFolder($storage);
		}

		// Re-generate the storage path since we do not want to store the JPATH_ROOT
		$storage 	= '/' . $container . '/' . $albumId . '/' . $photoId;

		return $storage;
	}

	/**
	 * Generates a file name for an image
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function generateFilename($size, $fileName = '')
	{
		if (empty($fileName)) {
			$fileName 	= $this->image->getName(false);
		}

		// Remove any previously _stock from the image name
		$fileName 	= str_ireplace('_stock', '', $fileName);

		$extension	= $this->image->getExtension();

		$fileName 	= str_ireplace($extension, '', $fileName);

		// Ensure that the file name is lowercased
		$fileName 	= strtolower($fileName);

		// Ensure that the file name is valid
		$fileName 	= JFilterOutput::stringURLSafe($fileName);

		// Append the size and extension back to the file name.
		$fileName 	= $fileName . '_' . $size . $extension;

		return $fileName;
	}

	/**
	 * Creates the necessary images to be used as an avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The target location to store the avatars
	 * @param	Array		An array of excluded sizes.
	 * @return
	 */
	public function create($path, $exclusion = array(), $overrideFileName = '')
	{
		// Files array store a list of files
		// created for this photo.
		$files 			= array();

		// Create stock image
		$filename       = $this->generateFilename('stock', $overrideFileName);

		$file           = $path . '/' . $filename;
		$files['stock'] = $filename;

		$this->image->copy( JPATH_ROOT . $path . '/' . $filename );

		// Create original image
		$filename          = $this->generateFilename( 'original' );
		$file              = JPATH_ROOT . $path . '/' . $filename;
		$files['original'] = $filename;
		$this->image->rotate(0); // Fake an operation queue
		$this->image->save( $file );

		// Once the photo successfully uploaded, trigger onAfterPhotoUpload
		$dispatcher = FD::dispatcher();

        // Set the arguments
        $args = array(&$this);

        // @trigger onAfterPhotoUpload
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onAfterPhotoUpload', $args);

		// Use original image as source image
		// for all other image sizes.
		$sourceImage = FD::image()->load( $file );

		// Create the rest of the image sizes
		foreach( self::$sizes as $name => $size )
		{
			if( in_array( $name , $exclusion ) ) continue;

			// Clone an instance of the source image.
			// Otherwise subsequent resizing operations
			// in this loop would end up using the image
			// instance that was resized by the previous loop.
			$image    		= $sourceImage->cloneImage();

			$filename 		= $this->generateFilename($name, $overrideFileName);
			$file			= JPATH_ROOT . $path . '/' . $filename;
			$files[$name] 	= $filename;

			// Resize image
			$method 		= $size['mode'];
			$image->$method($size['width'], $size['height']);

			// Save image
			$image->save($file);

			// Free up memory
			unset($image);
		}

		return $files;
	}
}
