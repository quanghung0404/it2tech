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

// Include the fields library
FD::import( 'admin:/includes/fields/dependencies' );

require_once( dirname( __FILE__ ) . '/helper.php' );

/**
 * Processes ajax calls for the Joomla_Email field.
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserAvatar extends SocialFieldItem
{
	/**
	 * Performs the file uploading here when the user selects their profile picture.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function upload()
	{
		// Get the ajax library
		$ajax 		= FD::ajax();

		// Get the file
		$tmp 		= JRequest::getVar( $this->inputName, '', 'FILES' );

		$file = array();
		foreach( $tmp as $k => $v )
		{
			$file[$k] = $v['file'];
		}

		// Check if it is a valid file
		if( empty( $file[ 'tmp_name' ] ) )
		{
			return $ajax->reject( JText::_( 'PLG_FIELDS_AVATAR_ERROR_INVALID_FILE' ) );
		}

		// Get user access
		$access = FD::access( $this->uid , SOCIAL_TYPE_PROFILES );

        // We need to perform sanity checking here
        $options = array('name' => $this->inputName, 'maxsize' => $access->get('photos.uploader.maxsize') . 'M', 'multiple' => true);

        $uploader = ES::uploader($options);
        $file = $uploader->getFile(null, 'image');

        // If there was an error getting uploaded file, stop.
        if ($file instanceof SocialException) {
            return $ajax->reject($file->message);
        }

		// Copy this to temporary location first
		$tmpPath = SocialFieldsUserAvatarHelper::getStoragePath( $this->inputName );

		// Load up the image library so we can get the appropriate extension
		$image 	= FD::image();
		$image->load($file['tmp_name']);

		// Get the extension
		$tmpName = md5( $file[ 'name' ] . $this->inputName . FD::date()->toMySQL() ) . $image->getExtension();

		$state = JFile::copy( $file['tmp_name'], $tmpPath . '/' . $tmpName );

		if( !$state )
		{
			return $ajax->reject( JText::_( 'PLG_FIELDS_AVATAR_ERROR_UNABLE_TO_MOVE_FILE' ) );
		}

		$tmpUri = SocialFieldsUserAvatarHelper::getStorageURI( $this->inputName );

		return $ajax->resolve( $file, $tmpUri . '/' . $tmpName, $tmpPath . '/' . $tmpName );
	}

	/**
	 * Allows caller to set the avatar based on the default avatars
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadDefault()
	{
		$ajax = FD::ajax();

		// Get the avatar id
		$id = JRequest::getInt('avatarId');

		if (!$id) {
			return $ajax->reject( JText::_( 'PLG_FIELDS_AVATAR_ERROR_RETRIEVING_AVATAR' ) );
		}

		$default = FD::table( 'defaultavatar' );
		$default->load( $id );

		$uri = $default->getSource( SOCIAL_AVATAR_SQUARE, true );

		return $ajax->resolve( $uri );
	}

}
