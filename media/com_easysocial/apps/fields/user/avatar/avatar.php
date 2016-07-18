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

class SocialFieldsUserAvatar extends SocialFieldItem
{
	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 */
	public function onRegister(&$post, &$registration)
	{
		$avatars		= array();

		// Get the default avatar
		$config 		= FD::config();
		$systemAvatar 	= $config->get('avatars.default.' . $this->group . '.' . SOCIAL_AVATAR_SQUARE);
		$systemAvatar	= rtrim(JURI::root(), '/') . $systemAvatar;
		$defaultAvatarId	= '';


		// Load the default avatars
		if (isset($registration->profile_id)) {
			$model 		= FD::model('Avatars');
			$avatars 	= $model->getDefaultAvatars($registration->profile_id);

			foreach($avatars as $avatar) {
				if ($avatar->default) {
					$systemAvatar 		= $avatar->getSource(SOCIAL_AVATAR_SQUARE);
					$defaultAvatarId	= $avatar->id;
				}
			}
		}

		// Set errors
		$error 			= $registration->getErrors($this->inputName);

		// Set the blank avatar
		$this->set('defaultAvatarId', $defaultAvatarId);
		$this->set('imageSource', $systemAvatar);
		$this->set('avatars', $avatars);
		$this->set('error', $error);
		$this->set('hasAvatar', false);
		$this->set('systemAvatar', $systemAvatar);

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
	 */
	public function onRegisterValidate(&$post, &$registration)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		$state 	= $this->validate($value);

		return $state;
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
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		// If the user did not select an avatar, we should pre-select it for them.
		if ($value) {
			$this->createAvatar($value, $user->id, false);
		}

		$default = false;

		// If the user did not select a default avatar, or did not upload an avatar, check if there's a default avatar selected for them.
		if (isset($user->profile_id)) {
			$default = FD::model('Avatars')->getDefaultAvatars($user->profile_id, SOCIAL_TYPE_PROFILES, true);
		}

		if (!$value && $default) {

			$default 		= $default[0];

			$tmp 			= new stdClass();
			$tmp->type 		= 'gallery';
			$tmp->source	= $default->id;
			$tmp->data 		= '';
			$tmp			= FD::json()->encode($tmp);

			$this->createAvatar($tmp, $user->id, false);
		}

		unset($post[$this->inputName]);
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
		// Let's see if avatarUrl is provided.
		if (!isset($data['avatar']) || empty($data['avatar'])) {
			return;
		}

		$avatarUrl	 		= $data['avatar'];

		// Store the avatar internally.
		$key 				= md5($data['oauth_id'] . $data['username']);
		$tmpAvatarPath 		= SOCIAL_MEDIA . '/tmp/' . $key;
		$tmpAvatarFile 		= $tmpAvatarPath . '/' . $key;

		jimport('joomla.filesystem.folder');

		if (!JFolder::exists($tmpAvatarPath)) {
			$state 	= JFolder::create($tmpAvatarPath);
		}

		$connector 	= FD::get('Connector');
		$connector->addUrl($avatarUrl);
		$connector->connect();

		$contents 	= $connector->getResult($avatarUrl);

		jimport('joomla.filesystem.file');

		if (!JFile::write($tmpAvatarFile, $contents)) {
			return;
		}

		$image = FD::image();
		$image->load($tmpAvatarFile);

		$avatar		= FD::avatar($image, $user->id, SOCIAL_TYPE_USER);

		// Check if there's a profile photos album that already exists.
		$albumModel	= FD::model('Albums');

		// Retrieve the user's default album
		$album		= $albumModel->getDefaultAlbum($user->id, SOCIAL_TYPE_USER, SOCIAL_ALBUM_PROFILE_PHOTOS);

		$photo 				= FD::table('Photo');
		$photo->uid 		= $user->id;
		$photo->user_id 	= $user->id;
		$photo->type 		= SOCIAL_TYPE_USER;
		$photo->album_id 	= $album->id;
		$photo->title 		= $user->getName();
		$photo->caption 	= JText::_('COM_EASYSOCIAL_PHOTO_IMPORTED_FROM_FACEBOOK');
		$photo->ordering	= 0;

		// We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
		$photo->state 		= SOCIAL_PHOTOS_STATE_TMP;

		// Try to store the photo first
		$state 		= $photo->store();

		if (!$state) {
			$this->setError(JText::_('PLG_FIELDS_AVATAR_ERROR_CREATING_PHOTO_OBJECT'));
			return false;
		}

		// Push all the ordering of the photo down
		$photosModel = FD::model('photos');
		$photosModel->pushPhotosOrdering($album->id, $photo->id);

		// If album doesn't have a cover, set the current photo as the cover.
		if (!$album->hasCover()) {
			$album->cover_id 	= $photo->id;

			// Store the album
			$album->store();
		}

		// Get the photos library
		$photoLib 	= FD::get('Photos', $image);
		$storage   = $photoLib->getStoragePath($album->id, $photo->id);
		$paths 		= $photoLib->create($storage);

		// Create metadata about the photos
		foreach ($paths as $type => $fileName) {
			$meta 				= FD::table('PhotoMeta');
			$meta->photo_id		= $photo->id;
			$meta->group 		= SOCIAL_PHOTOS_META_PATH;
			$meta->property 	= $type;
			$meta->value		= $storage . '/' . $fileName;

			$meta->store();
		}

		// Synchronize Indexer
		$indexer 	= FD::get('Indexer');
		$template	= $indexer->getTemplate();
		$template->setContent($photo->title, $photo->caption);

		// $url 	= FRoute::photos(array('layout' => 'item', 'id' => $photo->getAlias()));
		$url 	= $photo->getPermalink();
		$url 	= '/' . ltrim($url, '/');
		$url 	= str_replace('/administrator/', '/', $url);

		$template->setSource($photo->id, SOCIAL_INDEXER_TYPE_PHOTOS, $photo->uid, $url);
		$template->setThumbnail($photo->getSource('thumbnail'));

		$indexer->index($template);

		$options = array();

		if ($user->state == SOCIAL_USER_STATE_PENDING) {
			$options['addstream'] = false;
		}

		// Create the avatars now
		$avatar->store($photo, $options);

		// Once we are done creating the avatar, delete the temporary folder.
		$state		= JFolder::delete($tmpAvatarPath);
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
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		$avatars 			= array();
		$defaultAvatarId	= '';

		$systemAvatar 	= FD::config()->get('avatars.default.' . $this->group . '.' . SOCIAL_AVATAR_SQUARE);
		$systemAvatar	= rtrim(JURI::root(), '/') . $systemAvatar;

		// Load the default avatars
		if (isset($user->profile_id)) {
			$model 		= FD::model('Avatars');
			$avatars 	= $model->getDefaultAvatars($user->profile_id);

			foreach($avatars as $avatar) {
				if ($avatar->default) {
					$systemAvatar		= $avatar->getSource(SOCIAL_AVATAR_SQUARE);
					$defaultAvatarId	= $avatar->id;
				}
			}
		}

		$imageSource = $user->hasAvatar() ? $user->getAvatar(SOCIAL_AVATAR_SQUARE) : '';

		// Set errors
		$error = $this->getError($errors);

		// Set the blank avatar
		$this->set('group', $this->group);
		$this->set('defaultAvatarId', $defaultAvatarId);
		$this->set('avatars'		, $avatars);
		$this->set('imageSource'	, $imageSource);
		$this->set('error', $error);
		$this->set('hasAvatar', $user->hasAvatar());
		$this->set('systemAvatar', $systemAvatar);

		// Display the output.
		return $this->display();
	}

	/**
	 * Performs validation checks when a user edits their profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAdminEditValidate(&$post, &$user)
	{
		return true;
	}

	/**
	 * Performs validation checks when a user edits their profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditValidate(&$post, &$user)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		$state 	= $this->validate($value, $user);

		return $state;
	}

	/**
	 * Once a user edit is completed, the field should automatically
	 * move the temporary avatars into the user's folder if required.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditAfterSave(&$post, &$user)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		if (!empty($value)) {
			$this->createAvatar($value, $user->id, true, empty($post['applyRecurring']));
		}

		unset($post[$this->inputName]);
	}

	/**
	 * Performs validation checks when a user edits their profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate($value, $user = null)
	{
		if ((empty($user) || !$user->hasAvatar()) && $this->isRequired() && empty($value)) {
			$this->setError(JText::_('PLG_FIELDS_AVATAR_VALIDATION_EMPTY_PROFILE_PICTURE'));

			return false;
		}

		if (!empty($value)) {
			$value = FD::json()->decode($value);

			if ((empty($user) || !$user->hasAvatar()) && $this->isRequired() && empty($value->source)) {
				$this->setError(JText::_('PLG_FIELDS_AVATAR_VALIDATION_EMPTY_PROFILE_PICTURE'));

				return false;
			}
		}

		return true;
	}

	public function createAvatar($value, $uid, $createStream = true, $deleteImage = true)
	{
		$value = FD::makeObject($value);

		if (!empty($value->data)) {
			$value->data = FD::makeObject($value->data);
		}

		if ($value->type === 'remove') {
			$table = FD::table('avatar');
			$state = $table->load(array('uid' => $uid, 'type' => $this->group));

			if ($state) {
				$table->delete();

				if ($this->group == SOCIAL_TYPE_USER) {

				    $user = FD::user($uid);

				    // Prepare the dispatcher
				    FD::apps()->load(SOCIAL_TYPE_USER);
				    $dispatcher = FD::dispatcher();
				    $args = array(&$user, &$table);

				    // @trigger: onUserAvatarRemove
				    $dispatcher->trigger(SOCIAL_TYPE_USER, 'onUserAvatarRemove', $args);
				} 				
			}

			return true;
		}

		if ($value->type === 'gallery') {
			$table = FD::table('avatar');
			$state = $table->load(array('uid' => $uid, 'type' => $this->group));

			if (!$state) {
				$table->uid = $uid;
				$table->type = $this->group;
			}

			$table->avatar_id = $value->source;

			$table->store();

			return true;
		}

		if ($value->type === 'upload') {
			$data = new stdClass();

			if (!empty($value->path)) {
				$image = FD::image();
				$image->load($value->path);

				$avatar	= FD::avatar($image, $uid, $this->group);

				// Check if there's a profile photos album that already exists.
				$albumModel	= FD::model('Albums');

				// Retrieve the user's default album
				$album 				= $albumModel->getDefaultAlbum($uid, $this->group, SOCIAL_ALBUM_PROFILE_PHOTOS);

				$photo 				= FD::table('Photo');
				$photo->uid 		= $uid;
				$photo->type 		= $this->group;
				$photo->user_id 	= $this->group == SOCIAL_TYPE_USER ? $uid : FD::user()->id;
				$photo->album_id 	= $album->id;
				$photo->title 		= $value->name;
				$photo->caption 	= '';
				$photo->ordering	= 0;

				// We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
				$photo->state 		= SOCIAL_PHOTOS_STATE_TMP;

				// Try to store the photo first
				$state 		= $photo->store();

				if (!$state) {
					$this->setError(JText::_('PLG_FIELDS_AVATAR_ERROR_CREATING_PHOTO_OBJECT'));
					return false;
				}

				// Push all the ordering of the photo down
				$photosModel = FD::model('photos');
				$photosModel->pushPhotosOrdering($album->id, $photo->id);

				// If album doesn't have a cover, set the current photo as the cover.
				if (!$album->hasCover()) {
					$album->cover_id 	= $photo->id;

					// Store the album
					$album->store();
				}

				// Get the photos library
				$photoLib 	= FD::get('Photos', $image);
				$storage    = $photoLib->getStoragePath($album->id, $photo->id);
				$paths 		= $photoLib->create($storage);

				// Create metadata about the photos
				foreach ($paths as $type => $fileName) {
					$meta 				= FD::table('PhotoMeta');
					$meta->photo_id		= $photo->id;
					$meta->group 		= SOCIAL_PHOTOS_META_PATH;
					$meta->property 	= $type;
					$meta->value		= $storage . '/' . $fileName;

					$meta->store();
				}

				// Synchronize Indexer
				$indexer 	= FD::get('Indexer');
				$template	= $indexer->getTemplate();
				$template->setContent($photo->title, $photo->caption);

				//$url 	= FRoute::photos(array('layout' => 'item', 'id' => $photo->getAlias()));

				$url    = $photo->getPermalink();
				$url 	= '/' . ltrim($url, '/');
				$url 	= str_replace('/administrator/', '/', $url);

				$template->setSource($photo->id, SOCIAL_INDEXER_TYPE_PHOTOS, $photo->uid, $url);
				$template->setThumbnail($photo->getSource('thumbnail'));

				$indexer->index($template);

				// Crop the image to follow the avatar format. Get the dimensions from the request.
				if (!empty($value->data) && is_object($value->data)) {
					$width = $value->data->width;
					$height = $value->data->height;
					$top = $value->data->top;
					$left = $value->data->left;

					$avatar->crop($top, $left, $width, $height);
				}

				$options = array();
				// Create the avatars now
				if (!$createStream) {
					$options = array( 'addstream' => false );
				}

				$options['deleteimage'] = false;

				$avatar->store($photo, $options);
			}

			return true;
		}
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The html output.
	 *
	 */
	public function onSample()
	{
		$id = JRequest::getInt('id');

		$model 		= FD::model('Avatars');
		$avatars 	= $model->getDefaultAvatars($id);

		$this->set('avatars', $avatars);

		$this->set('defaultAvatar', rtrim(JURI::root(), '/') . FD::config()->get('avatars.default.' . $this->group . '.' . SOCIAL_AVATAR_SQUARE));

		return $this->display();
	}

	/**
	 * Gets the meta data for avatar from the OAuth client
	 *
	 * @since  1.2
	 * @access public
	 * @param  Array		$details The metadata
	 * @param  SocialOAuth	$client  The Oauth client class
	 */
	public function onOAuthGetUserMeta(&$details, &$client)
	{
		$config = FD::config();

		if ($config->get('oauth.' . $client->getType() . '.registration.avatar')) {
			$avatar = $client->api('me/picture', array('type' => 'large', 'redirect' => false));
			$avatarUrl = $avatar['data']['url'];

			$details['avatar'] = $avatarUrl;
		}
	}

	/**
	 * Copies the avatar if the user is linking an existing account with an oauth account
	 *
	 * @since  1.2
	 * @access public
	 * @param  Array		$meta   Meta data from OAuth client
	 * @param  SocialOAuth	$client The OAuth client class
	 * @param  SocialUser	$user   The user object
	 */
	public function onLinkOAuthAfterSave(&$meta, &$client, &$user)
	{
		$importAvatar = JRequest::getBool('importAvatar', false);

		if ($importAvatar) {
			return $this->onRegisterOAuthAfterSave($meta, $client, $user);
		}

		return;
	}

	/**
	 * Checks if this field is complete.
	 *
	 * @since  1.2
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 */
	public function onFieldCheck($user)
	{
		return $this->validate($this->value, $user);
	}

	/**
	 * Checks if this field is filled in.
	 *
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

		return $user->hasAvatar();
	}
}
