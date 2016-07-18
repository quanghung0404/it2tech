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
defined('_JEXEC') or die('Unauthorized Access');

// Include the fields library
FD::import('admin:/includes/fields/dependencies');

require_once(dirname(__FILE__) . '/helper.php');

/**
 * Processes ajax calls for the cover field.
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserCover extends SocialFieldItem
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
		$ajax = FD::ajax();
		$tmp = JRequest::getVar($this->inputName, '', 'FILES');

		$file = array();
		foreach ($tmp as $k => $v) {
			$file[$k] = $v['file'];
		}

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			return $ajax->reject(JText::_('PLG_FIELDS_COVER_VALIDATION_INVALID_IMAGE'));
		}

		// Get user access
		$access = FD::access($this->uid, SOCIAL_TYPE_PROFILES);

        // We need to perform sanity checking here
        $options = array('name' => $this->inputName, 'maxsize' => $access->get('photos.uploader.maxsize') . 'M', 'multiple' => true);

        $uploader = ES::uploader($options);
        $file = $uploader->getFile(null, 'image');

        // If there was an error getting uploaded file, stop.
        if ($file instanceof SocialException) {
            return $ajax->reject($file->message);
        }

		$result 	= $this->createCover($file, $this->inputName);

		return $ajax->resolve($result);
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createCover($file, $inputName)
	{
		// Load our own image library
		$image = FD::image();

		// Generates a unique name for this image.
		$name = $file['name'];

		// Load up the file.
		$image->load($file['tmp_name'], $name);

		// Ensure that the image is valid.
		if (!$image->isValid()) {
			// @TODO: Add some logging here.
			echo JText::_('PLG_FIELDS_AVATAR_VALIDATION_INVALID_IMAGE');
			exit;
		}

		// Get the storage path
		$storage = SocialFieldsUserCoverHelper::getStoragePath($inputName);

		// Create a new avatar object.
		$photos = FD::get('Photos', $image);

		// Create avatars
		$sizes = $photos->create($storage);

		// We want to format the output to get the full absolute url.
		$base = basename($storage);

		$result = array();

		foreach ($sizes as $size => $value) {
			$row = new stdClass();

			$row->title	= $file['name'];
			$row->file = $value;
			$row->path = JPATH_ROOT . '/media/com_easysocial/tmp/' . $base . '/' . $value;
			$row->uri = rtrim(JURI::root(), '/') . '/media/com_easysocial/tmp/' . $base . '/' . $value;

			$result[$size] = $row;
		}

		return $result;
	}

}
