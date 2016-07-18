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

class SocialTableCover extends SocialTable
{
	/**
	 * The unique id for this record.
	 * @var int
	 */
	public $id = null;

	/**
	 * The unique type id for this record.
	 * @var int
	 */
	public $uid = null;

	/**
	 * The unique type string for this record.
	 * @var string
	 */
	public $type = null;

	/**
	 * The unique type id for this record. (Optional: used only when tied to a pre-defined avatar list)
	 * @var int
	 */
	public $photo_id = null;

	/**
	 * The unique type id for this record. (Optional: used only when tied to a pre-defined avatar list)
	 * @var int
	 */
	public $cover_id = null;

	/**
	 * The x coordinate of the cover
	 * @var int
	 */
	public $x = null;

	/**
	 * The y coordinate of the cover
	 * @var int
	 */
	public $y = null;

	/**
	 * The modified date of an avatar.
	 * @var string
	 */
	public $modified = null;

	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @param	JDatabase
	 */
	public function __construct($db)
	{
		parent::__construct('#__social_covers', 'id' , $db );
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
			$this->setError(JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_AVATARS_FILE_UNAVAILABLE'));
			return false;
		}

		// Get the default avatars storage location.
		$avatarsPath 	= JPATH_ROOT . '/' . FD::cleanPath( $config->get( 'avatars.storage.container' ) );

		// Test if the avatars path folder exists. If it doesn't we need to create it.
		if (!FD::makeFolder($avatarsPath)) {
			$this->setError(JText::_('Errors when creating default container for avatar'));
			return false;
		}

		// Get the default avatars storage location for this type.
		$typePath = $config->get( 'avatars.storage.' . $this->type );
		$storagePath = $avatarsPath . '/' . FD::cleanPath($typePath);

		// Ensure storage path exists.
		if( !FD::makeFolder( $storagePath ) )
		{
			$this->setError( JText::_( 'Errors when creating path for avatar' ) );
			return false;
		}

		// Get the profile id and construct the final path.
		$idPath = FD::cleanPath( $this->uid );
		$storagePath = $storagePath . '/' . $idPath;

		// Ensure storage path exists.
		if (!FD::makeFolder($storagePath)) {
			$this->setError( JText::_( 'Errors when creating default path for avatar' ) );
			return false;
		}

		// Get the image library to perform some checks.
		$image = FD::get('Image');
		$image->load($file['tmp_name']);

		// Test if the image is really a valid image.
		if (!$image->isValid()) {
			$this->setError(JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_AVATARS_FILE_NOT_IMAGE'));
			return false;
		}

		// Process avatar storage.
		$avatar = FD::get('Avatar', $image);

		// Let's create the avatar.
		$sizes = $avatar->create($storagePath);

		if ($sizes === false) {
			$this->setError(JText::_('Sorry, there was some errors when creating the avatars.'));
			return false;
		}

		// Delete previous files.
		$this->deleteFile($storagePath);

		// Assign the values back.
		foreach ($sizes as $size => $url) {
			$this->$size = $url;
		}

		return true;
	}

	/**
	 * Allows caller to associate a photo with the cover
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setPhotoAsCover( $photoId , $x = '0.5', $y = '0.5')
	{
		// Reset the cover_id
		$this->cover_id 	= null;

		// Set the photo id
		$this->photo_id 	= $photoId;

		// Set the x position
		$this->x 			= $x;

		// Set the y position
		$this->y 			= $y;

		// Update the modified time
		$this->modified 	= FD::date()->toMySQL();
	}

	/**
	 * Deletes the current variation of avatars given the absolute path to an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The absolute path to the item.
	 * @return	bool		True if success, false otherwise.
	 */
	public function deleteCurrentAvatar( $storagePath )
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
	 * Get the photo object
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPhoto()
	{
		static $items = array();

		if (!isset($items[$this->photo_id])) {
			$photo = FD::table('Photo');
			$photo->load($this->photo_id);

			$items[$this->photo_id] = $photo;
		}

		return $items[$this->photo_id];
	}

	/**
	 * Get's the uri to a cover photo.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determine if the absolute uri should be returned.
	 */
	public function getSource($size = SOCIAL_AVATAR_MEDIUM, $absolute = true)
	{
		// Get config
		$config = FD::config();

		// Set the default cover
		$default = SOCIAL_JOOMLA_URI . $config->get('covers.default.' . $this->type . '.' . SOCIAL_COVER_DEFAULT);

		// If there is a cover override in the template, use it instead.
		$app = JFactory::getApplication();
		$overridePath = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/covers/user/default.jpg';

		if (JFile::exists($overridePath)) {
			$default = rtrim(JURI::root(), '/') . '/templates/' . $app->getTemplate() . '/html/com_easysocial/covers/user/default.jpg';
		}

		// Test if the cover is a photo
		if ($this->photo_id) {

			$photo = $this->getPhoto();

			if (!$photo->id) {
				$uri = $default;
			} else {
				$uri = $photo->getSource('large');
			}

			// The file might not exist, so we need to revert to the default
			if($uri === false) {
				$uri = $default;
			}
		} else {
			$uri = $default;
		}

	    return $uri;
	}

	/**
	 * Returns the position
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPosition()
	{
		$position 	= ( $this->x * 100 ) . '% ' . ( $this->y * 100 ) . '%';

		return $position;
	}
}
