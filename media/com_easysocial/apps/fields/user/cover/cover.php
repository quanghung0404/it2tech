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
ES::import('admin:/includes/fields/dependencies');

require_once(dirname(__FILE__) . '/helper.php');

class SocialFieldsUserCover extends SocialFieldItem
{
	/**
	 * Displays the cover form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function onRegister(&$post, &$registration)
	{
		// Get the default cover picture
		$value = ES::getDefaultCover($this->group);
		$defaultCover = $value;

		$this->set('value', $value);
		$this->set('defaultCover', $defaultCover);
		$this->set('hasCover', 0);

		// Get registration error
		$error 	= $registration->getErrors($this->inputName);

		// Set error
		$this->set('error', $error);

		// Display the output.
		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterValidate(&$post, &$registration)
	{
		$cover 	= !empty($post[$this->inputName]) ? $post[$this->inputName] : '';
		$obj 	= FD::makeObject($cover);

		if ($this->isRequired() && (empty($cover) || empty($obj->data))) {
			$this->setError(JText::_('PLG_FIELDS_COVER_VALIDATION_REQUIRED'));

			return false;
		}

		return true;
	}

	/**
	 * Once a user registration is completed, the field should automatically
	 * move the temporary avatars into the user's folder if required.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegisterAfterSave(&$post, &$user)
	{
		return $this->saveCover($post, $user->id, $this->group);
	}

	/**
	 * Processes before the user account is created when user signs in with oauth.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegisterOAuthAfterSave(&$data, &$oauthClient, SocialUser &$user)
	{
		$cover 	= isset($data['cover']) ? $data['cover'] : '';

		// If cover is not provided, skip this.
		if (!$cover) {
			return;
		}

		// Get the cover URL
		$coverUrl 	= $cover->url;

		// Get the session object.
		$uid		= SocialFieldsUserCoverHelper::genUniqueId($this->inputName);

		// Get the user object.
		$user 		= FD::user();

		// Store the cover internally first.
		$tmpPath 	= SOCIAL_TMP . '/' . $uid . '_cover';
		$tmpFile 	= $tmpPath . '/' . $uid;

		// Now we need to get the image data.
		$connector 	= FD::connector();
		$connector->addUrl($coverUrl);
		$connector->connect();

		$contents 	= $connector->getResult($coverUrl);

		jimport('joomla.filesystem.file');

		if (!JFile::write($tmpFile, $contents)) {
			return;
		}

		// Ensure that the image is valid.
		if (!SocialFieldsUserCoverHelper::isValid($tmpFile)) {

			JFile::delete($tmpFile);

			return;
		}

		// Create the default album for this cover.
		$album 	= SocialFieldsUserCoverHelper::getDefaultAlbum($user->id);

		// Once the album is created, create the photo object.
		$photo 	= SocialFieldsUserCoverHelper::createPhotoObject($user->id, SOCIAL_TYPE_USER, $album->id, $data['oauth_id'], true);

		// Set the new album with the photo as the cover.
		$album->cover_id 	= $photo->id;
		$album->store();

		// Generates a unique name for this image.
		$name 	= md5($data['oauth_id'] . $this->inputName . FD::date()->toMySQL());

		// Load our own image library
		$image 	= FD::image();

		// Load up the file.
		$image->load($tmpFile, $name);

		// Load up photos library
		$photos 	= FD::get('Photos', $image);

		$storage = $photos->getStoragePath($album->id, $photo->id);

		// Create avatars
		$sizes 		= $photos->create($storage);

		foreach ($sizes as $size => $path) {
			// Now we will need to store the meta for the photo.
			$meta 	= SocialFieldsUserCoverHelper::createPhotoMeta($photo, $size, $path);
		}

		// Once all is done, we just need to update the cover table so the user
		// will start using this cover now.
		$coverTable 	= FD::table('Cover');
		$state 			= $coverTable->load(array('uid' => $user->id, 'type' => SOCIAL_TYPE_USER));

		// User does not have a cover.
		if (!$state) {
			$coverTable->uid 	= $user->id;
			$coverTable->type 	= SOCIAL_TYPE_USER;
			$coverTable->y 		= $cover->offset_y;
		}

		// Set the cover to pull from photo
		$coverTable->setPhotoAsCover($photo->id);

		// Save the cover.
		$coverTable->store();

		// Once everything is created, delete the temporary file
		JFile::delete($tmpFile);
	}

	/**
	 * Displays the field input for user when they edit their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser		The user that is being edited.
	 * @param	Array			The post data.
	 * @param	Array			The error data.
	 * @return	string			The html string of the field
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEdit(&$post, &$node, $errors)
	{
		$hasCover = $node->hasCover();

		$value = $node->getCover();

		$position = $node->getCoverPosition();

		// If there is a post data, then use the post data
		if (!empty($post[$this->inputName])) {
			$obj = FD::makeObject($post[$this->inputName]);

			if (!empty($obj->data)) {
				$this->set('coverData', $this->escape($obj->data));

				$data = FD::makeObject($obj->data);

				$value = $data->large->uri;

				$hasCover = true;
			}

			if (!empty($obj->position)) {
				$this->set('coverPosition', $this->escape($obj->position));

				$data = FD::makeObject($obj->position);

				if (isset($data->x) && isset($data->y)) {
					$position = $data->x * 100 . '% ' . $data->y * 100 . '%';
				}
			}
		}

		// If the user doesn't have a cover, get the default cover for them
		$defaultCover = ES::getDefaultCover($this->group);

		if (!$hasCover) {
			$value = $defaultCover;
		}

		$error = $this->getError($errors);

		// Set the value
		$this->set('value', $value);
		$this->set('position', $position);
		$this->set('error', $error);
		$this->set('hasCover', $hasCover);
		$this->set('defaultCover', $defaultCover);

		return $this->display();
	}

	public function onAdminEditValidate()
	{
		// Admin shouldn't need to validate
		return true;
	}

	public function onEditValidate(&$post, &$user)
	{
		$cover 	= !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		if ($this->isRequired() && !$user->hasCover()) {
			$obj = FD::makeObject($cover);

			if (empty($obj->data)) {
				$this->setError(JText::_('PLG_FIELDS_COVER_VALIDATION_REQUIRED'));

				return false;
			}
		}

		return true;
	}

	public function onEditAfterSave(&$post, &$user)
	{
		return $this->saveCover($post, $user->id, $this->group);
	}

	public function saveCover(&$post, $uid, $type)
	{
		$coverData = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		unset($post[$this->inputName]);

		if (empty($coverData)) {
			return true;
		}

		$coverData = FD::makeObject($coverData);

		// Get the cover table.
		$cover = FD::table('Cover');

		// Try to load existing cover
		$state = $cover->load(array('uid' => $uid, 'type' => $type));

		// If no existing cover, then we set the uid and type.
		if (!$state) {
			$cover->uid = $uid;
			$cover->type = $type;
		}

		// If both data does not exist, then we don't proceed to store the data.
		if (empty($coverData->data) && empty($coverData->position)) {
			return true;
		}

		if (!empty($coverData->data)) {
			if ($coverData->data === 'delete') {
				$cover->delete();
				return true;
			}

			$coverObj = FD::makeObject($coverData->data);

			// Get the cover album.
			$album = SocialFieldsUserCoverHelper::getDefaultAlbum($uid, $type);

			// Create the photo object.
			$photo = SocialFieldsUserCoverHelper::createPhotoObject($uid, $type, $album->id, $coverObj->stock->title, false);

			// If there are no cover set for this album, set it as cover.
			if (empty($album->cover_id)) {
				$album->cover_id = $photo->id;
				$album->store();
			}

			// Construct the path to where the photo is temporarily uploaded.
			// $tmpPath = SocialFieldsUserCoverHelper::getPath($this->inputName);
			$tmpPath = dirname($coverObj->stock->path);

			// Get the supposed path of where the cover should be
			// Instead of doing SocialPhotos::getStoragePath, I copied the logic from there but only to create the folders up until albumId section.
			// We do not want JPATH_ROOT to be included in the $storage variable
			$storage = '/' . FD::cleanPath(FD::config()->get('photos.storage.container'));
			FD::makeFolder(JPATH_ROOT . $storage);
			$storage .= '/' . $album->id;
			FD::makeFolder(JPATH_ROOT . $storage);
			$storage .= '/' . $photo->id;
			FD::makeFolder(JPATH_ROOT . $storage);

			// Copy the photo from the temporary path to the storage folder.
			$state = JFolder::copy($tmpPath, JPATH_ROOT . $storage, '', true);

			// If cannot copy out the photo, then don't proceed
			if ($state !== true) {
				$this->setError(JText::_('PLG_FIELDS_COVER_ERROR_UNABLE_TO_MOVE_FILE'));
				return false;
			}

			// Create the photo meta for each available sizes.
			foreach ($coverObj as $key => $value) {
				SocialFieldsUserCoverHelper::createPhotoMeta($photo, $key, $storage . '/' . $value->file);
			}

			// Set the uploaded photo as cover for this user.
			$cover->setPhotoAsCover($photo->id);
		}

		// Set the position of the cover if available.
		if (!empty($coverData->position)) {
			$position = FD::makeObject($coverData->position);

			if (isset($position->x)) {
				$cover->x = $position->x;
			}

			if (isset($position->y)) {
				$cover->y = $position->y;
			}
		}

		// Store the cover object
		$cover->store();

		// And we're done.
		return true;
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onSample()
	{
		return $this->display();
	}

	/**
	 * Assigned the appropriate key to retrieve from the OAuth client
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  Array    $fields The fields to request
	 */
	public function onOAuthGetMetaFields(&$fields, &$client)
	{
		if ($client->getType() == 'facebook') {
			$fields[] = 'cover';
		}
	}

	/**
	 * Gets the meta data for avatar from the OAuth client
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  Array		$details The metadata
	 * @param  SocialOAuth	$client  The Oauth client class
	 */
	public function onOAuthGetUserMeta(&$details, &$client)
	{
		$config = FD::config();

		if ($config->get('oauth.' . $client->getType() . '.registration.cover') && isset($details['cover'])) {
			$cover 				= new stdClass();

			$cover->url 		= $details['cover']['source'];
			$cover->offset_y	= $details['cover']['offset_y'];

			$details['cover']	= $cover;
		}
	}

	/**
	 * Copies the cover if the user is linking an existing account with an oauth account
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  Array		$meta   Meta data from OAuth client
	 * @param  SocialOAuth	$client The OAuth client class
	 * @param  SocialUser	$user   The user object
	 */
	public function onLinkOAuthAfterSave(&$meta, &$client, &$user)
	{
		$importCover = JRequest::getBool('importCover', false);

		if ($importCover) {
			return $this->onRegisterOAuthAfterSave($meta, $client, $user);
		}

		return;
	}

	/**
	 * Checks if this field is complete.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 */
	public function onFieldCheck($user)
	{
		if ($this->isRequired() && !$user->hasCover()) {
			$this->setError(JText::_('PLG_FIELDS_COVER_VALIDATION_REQUIRED'));
			return false;
		}

		return true;
	}

	/**
	 * Checks if this field is filled in.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3
	 * @access public
	 * @param  array        $data   The post data.
	 * @param  SocialUser   $user   The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		return $user->hasCover();
	}
}
