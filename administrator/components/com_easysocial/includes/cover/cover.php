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
 * Generic cover library.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialCover
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
									'small'		=> '_small'
							);

	/**
	 * Stores the metadata for large covers.
	 * @var Array
	 */
	static $large 		= array(
									'width'		=> SOCIAL_COVER_LARGE_WIDTH,
									'height'	=> SOCIAL_COVER_LARGE_HEIGHT,
									'mode'		=> 'fill'
							);

	/**
	 * Stores the metadata for small covers
	 * @var Array
	 */
	static $small		= array(
									'width' 	=> SOCIAL_COVER_SMALL_WIDTH ,
									'height' 	=> SOCIAL_COVER_SMALL_HEIGHT,
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
	public function __construct( &$image )
	{
		// Set the current image object.
		$this->image 	= $image;
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
		$avatar 	= new self( $image );

		return $avatar;
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
			$names[ $size ]		= $this->image->getName() . $postfix . $this->image->getExtension();
		}

		return $names;
	}

	/**
	 * Creates the necessary images to be used as an avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The target location to store the avatars
	 * @return
	 */
	public function create( $targetLocation )
	{
		// Get a list of files to build.
		$names		= $this->generateFileNames();

		foreach( $names as $size => $name )
		{
			$info 	= self::$$size;
			$mode 	= $info[ 'mode' ];

			$this->image->$mode( $info[ 'width' ] , $info[ 'height' ] );

			$this->image->save( $targetLocation . '/' . $name );
		}

		return $names;
	}

	/**
	 * Determines if the folder exists. If it doesn't, create it.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool	True on success, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function createFolder()
	{
		$path   = $this->path . DS . $this->uid;

		if( JFolder::exists( $path ) )
		{
			return true;
		}

		// Create the folders
		return JFolder::create( $path );
	}

	public function getFolder()
	{
		return $this->path . DS . $this->uid;
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
	public function getUniqueName()
	{
		$name	= md5( $this->image->getName() . uniqid() );

		return $name;
	}

}
