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

// Load adapter.
require_once( dirname( __FILE__ ) . '/adapters/asido/class.asido.php' );

/**
 * Class to manipulate images.
 *
 * @since	1.0
 */
class SocialImage
{
	/**
	 * Stores the current image resource.
	 * @var	resource
	 */
	private $image  	= null;

	/**
	 * Stores the original image resource in case we need to revert.
	 * @var	resource
	 */
	private $original  	= null;

	/**
	 * Stores the information about the image.
	 * @var	stdClass
	 */
	private $meta		= null;

	/**
	 * Stores the current adapter.
	 * @var	Asido
	 */
	private $adapter    = null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @param	string	The image driver to use.
	 * @access	public
	 */
	public function __construct( $driver = 'gd_hack' )
	{
		// Load the adapter
		$this->adapter	= new Asido();

		// @TODO: Configurable image driver
		$this->adapter->driver( $driver );
	}

	/**
	 * This class uses the factory pattern.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The image driver to use.
	 * @return	SocialImage		Returns itself for chaining.
	 */
	public static function factory( $driver = 'gd_hack' )
	{
		$image 	= new self( $driver );

		return $image;
	}

	/**
	 * Determines if the image is a valid image type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isValid()
	{
		if(!$this->image) {
			return false;
		}

		// @TODO: Additional checks that we should perform ourselves.

		return $this->adapter->is_format_supported( $this->meta->info[ 'mime' ] );
	}

	/**
	 * Loads an image resource given the path to the image.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The path to the image.
	 * @param	string			The name / title of the image.
	 * @return	SocialImage		Returns itself for chaining.
	 */
	public function load($path , $name = '')
	{
		// Set the meta info about this image.
		$meta 			= new stdClass();

		// Set the path for this image.
		$meta->path 	= $path;

		// Set the meta info
		$meta->info		= getimagesize($path);

		// Set the name for this image.
		if ( !empty($name) )
		{
			$meta->name = $name;
		}
		else
		{
			$meta->name = basename($path);
		}

		// If name is not provided, we'll generate a unique one for it base on the path.
		if( empty( $meta->name ) )
		{
			$meta->name 	= $this->genUniqueName( $path );
		}

		$this->meta 	= $meta;

		// Set the image resource.
		$this->image    = $this->adapter->image( $path );

		// Fix the orientation of the image first.
		$this->fixOrientation();

		// Set the original image resource.
		$this->original	= $this->image;

		return $this;
	}

	public function replaceImage($image)
	{
		$this->image = $image;
	}

	public function cloneImage()
	{
		$image 	= clone( $this );
		$image->replaceImage( clone( $this->image ) );
		// $image->replaceImage( clone($this->image) );
		return $image;

	}
	public function newInstance()
	{
		$image = FD::image();

		$image->load( $this->meta->path , $this->meta->name );

		return $image;
	}

	/**
	 * Retrieves the mime of the current image.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The mime info.
	 */
	public function getMime()
	{
		if( !$this->image )
		{
			return false;
		}

		if( !isset( $this->meta->info[ 'mime' ] ) )
		{
			return false;
		}

		return $this->meta->info[ 'mime' ];
	}

	/**
	 * Resizes an image to a specific width.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The target width.
	 * @return	SocialImage	Returns itself for chaining.
	 */
	public function width( $width )
	{
		// Resize to width.
		$this->adapter->width( $this->image , $width );

		return $this;
	}

	/**
	 * Resizes an image to a specific height.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The target height.
	 * @return	SocialImage	Returns itself for chaining.
	 */
	public function height( $height )
	{
		// Resize to height.
		$this->adapter->height( $this->image , $height );

		return $this;
	}

	/**
	 * Gets the width of the image
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	int
	 */
	public function getWidth()
	{
		$width	= $this->meta->info[0];

		return $width;
	}

	/**
	 * Gets the width of the image
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	int
	 */
	public function getHeight()
	{
		$height 	= $this->meta->info[ 1 ];

		return $height;
	}


	/**
	 * General resize for image
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The target width.
	 * @param	int			The target height.
	 * @param	int			The resize mode.
	 * @return	SocialImage	Returns itself for chaining.
	 */
	public function resize($width = null , $height = null, $mode = ASIDO_RESIZE_PROPORTIONAL)
	{
		// Resize
		$this->adapter->resize($this->image, $width, $height, $mode);

		return $this;
	}

	/**
	 * Rotates the image
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rotate( $degree = 0 )
	{
		$this->adapter->rotate( $this->image, $degree );

		return $this;
	}

	/**
	 * Resize an image to fit the target width and height
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The target width.
	 * @param	int			The target height.
	 * @return	SocialImage	Returns itself for chaining.
	 */
	public function fit( $width = null , $height = null )
	{
		$this->adapter->fit( $this->image , $width , $height );

		return $this;
	}

	/**
	 * Resize an image to fill a frame
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The target width.
	 * @param	int			The target height.
	 * @return	SocialImage	Returns itself for chaining.
	 */
	public function fill( $width = null , $height = null )
	{
		$this->adapter->fill( $this->image , $width , $height );

		return $this;
	}

	public function outerFit( $width = null , $height = null )
	{
		$this->adapter->outerFit( $this->image , $width , $height );

		return $this;
	}

	/**
	 * Resize an image to fit a frame
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The target width.
	 * @param	int			The target height.
	 * @return	SocialImage	Returns itself for chaining.
	 */
	public function frame( $width = null , $height = null )
	{
		$this->adapter->frame( $this->image , $width , $height );

		return $this;
	}


	/**
	 * Crops an image given the coordinates , width and height.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The coordinate position for x
	 * @param	int		The coordinate position for y
	 * @param	int		The width of the cropped image.
	 * @param	int		The height of the cropped image.
	 * @return	SocialImage		Returns itself for chaining.
	 */
	public function crop( $x = 0 , $y = 0 , $width , $height )
	{
		// Try to crop the current image resource.
		$this->adapter->crop( $this->image , $x , $y , $width , $height );

		return $this;
	}

	/**
	 * Save's the image resource in a target location.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The target where the image should be saved into.
	 * @return	bool	True on success, false otherwise
	 * @return
	 */
	public function save($target)
	{
		// Set the image target.
		$this->image->target($target);

		// Try to save the image.
		$state 	= $this->image->save();

		// TODO: Add some logging if failed.
		if( !$state ) {
		}

		return $state;
	}

	/**
	 * Just copy the file to the target
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The path to the target
	 * @return	boolean
	 */
	public function copy( $target )
	{
		$state 	= JFile::copy( $this->meta->path , $target );

		return $state;
	}

	/**
	 * Returns the path of the image.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The name / title of the image.
	 */
	public function getPath()
	{
		return $this->meta->path;
	}

	/**
	 * Returns the name of the image.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The name / title of the image.
	 */
	public function getName($hash = false)
	{
		if ($hash) {
			return $this->genUniqueName();
		}

		return $this->meta->name;
	}

	/**
	 * Returns the extension type for this image.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The extension for the image.
	 */
	public function getExtension()
	{
		$mime 	= false;

		if( isset( $this->meta->info[ 'mime' ] ) )
		{
			$mime 	= $this->meta->info[ 'mime' ];
		}

		switch( $mime )
		{
			case 'image/jpeg':
				$extension  = '.jpg';
			break;

			case 'image/png':
			case 'image/x-png':
			default:
				$extension  = '.png';
			break;
		}

		return $extension;
	}

	/**
	 * Generates a random image name based on the node id.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $avatar 	= FD::get( 'Avatar' );
	 *
	 * // Returns md5 hash.
	 * $output	= $avatar->generateName( 'anyprefix' , 'anysuffix' , '.png' );
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	Prefix for the file name.
	 * @param	string	Suffix for the file name.
	 * @param	string	The extension of the file.
	 */
	public function genUniqueName( $salt = '' )
	{
		if( $salt )
		{
			$hashed 	= md5( $this->meta->name . $salt );
		}
		else
		{
			$hashed	= md5( $this->meta->name . uniqid() );
		}

		return $hashed;
	}

	/**
	 * Determines if the current image has exif data
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasExifSupport()
	{
		$mime 	= $this->getMime();

		if( $mime == 'image/jpg' || $mime == 'image/jpeg' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Fixes image orientation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function fixOrientation()
	{
		$exif 	= FD::get( 'Exif' );

		if( !$exif->isAvailable() )
		{
			return false;
		}

		// Get the mime type for this image
		$mime 	= $this->getMime();

		// Only image with jpeg are supported.
		if( $mime != 'image/jpeg' )
		{
			return false;
		}

		// Load exif data.
		$exif->load( $this->meta->path );

		$orientation 	= $exif->getOrientation();

		switch($orientation)
		{
			case 1:
				// Do nothing here as the image is already correct.
				$this->adapter->rotate( $this->image , 0 );

			break;

			case 2:
				// Flip image horizontally since it's at top right
				$this->adapter->flop( $this->image );
			break;

			case 3:

				// Rotate image 180 degrees left since it's at bottom right
				$this->adapter->rotate( $this->image , 180 );

			break;

			case 4:

				// Flip image vertically because it's at bottom left
				$this->adapter->flip( $this->image );

			break;

			case 5:

				// Flip image vertically
				$this->adapter->flip( $this->image );

				// Rotate image 90 degrees right.
				$this->adapter->rotate( $this->image , -90 );

			break;

			case 6:

				// Rotate image 90 degrees right
				$this->adapter->rotate( $this->image , 90 );

			break;

			case 7:

				// Flip image horizontally
				$this->adapter->flop( $this->image );

				// Rotate 90 degrees right.
				$this->adapter->rotate( $this->image , 90 );

			break;

			case 8:

				// Rotate image 90 degrees left
				$this->adapter->rotate( $this->image , -90 );

			break;
		}
	}
}
