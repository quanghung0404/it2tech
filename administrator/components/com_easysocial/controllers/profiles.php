<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main controller
FD::import( 'admin:/controllers/controller' );

class EasySocialControllerProfiles extends EasySocialController
{
	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		// Map the alias methods here.
		$this->registerTask('unpublish', 'togglePublish' );
		$this->registerTask('publish'	, 'togglePublish' );

		$this->registerTask('form', 'form' );

		$this->registerTask('save', 'store');
		$this->registerTask('savenew', 'store');
		$this->registerTask('apply', 'store');
		$this->registerTask('savecopy', 'store');
	}

	/**
	 * Method to update profile type the ordering
	 *
	 * @param   null    All parameters are from HTTP $_POST
	 * @return  JSON    JSON encoded string.
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		FD::checkToken();

		$cid = $this->input->get('cid', array(), 'array');
		$ordering = $this->input->get('order', array(), 'array');

		$view 	= $this->getCurrentView();

		if (!$cid) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_PROFILES_ORDERING_NO_ITEMS'), SOCIAL_MSG_ERROR );
			return $view->call(__FUNCTION__);
		}

		$model = FD::model('Profiles');

		for($i = 0; $i < count($cid); $i++) {

			$id = $cid[$i];
			$order = $ordering[$i];

			$model->updateOrdering($id, $order);
		}

		$view->setMessage(JText::_('COM_EASYSOCIAL_PROFILES_ORDERING_UPDATED'), SOCIAL_MSG_SUCCESS);

		return $view->call(__FUNCTION__);
	}

	/**
	 * Method to add a member into an existing profile type.
	 *
	 * @param   null    All parameters are from HTTP $_POST
	 * @return  JSON    JSON encoded string.
	 */
	public function insertMember()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get the id from request.
		$id 	= JRequest::getInt( 'id' );

		// Get the profile id.
		$profile_id 	= JRequest::getInt( 'profile_id' );

		// Get the current view.
		$view 	= $this->getCurrentView();

		if( !$id )
		{
			$view->setMessage( JText::_( 'Please enter a valid user id.' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// @TODO: Try to remove user from any other existing profile maps.
		$model 	= FD::model( 'Profiles' );
		$model->removeUserFromProfiles( $id );

		$table 	= FD::table( 'ProfileMap' );

		$table->user_id 	= $id;
		$table->profile_id 	= $profile_id;
		$table->state 		= SOCIAL_STATE_PUBLISHED;


		// @rule: Store user profile bindings
		$table->store();

		$user 	= FD::user( $id );

		return $view->call( __FUNCTION__ , $user );
	}

	/**
	 * Responsible to delete a profile from the system.
	 *
	 * @since   1.0
	 * @access  public
	 * @return  null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the id from the post.
		$ids    = JRequest::getVar( 'cid' );

		// Ensure that the ids is now an array.
		$ids 	= FD::makeArray( $ids );

		// Get the view object.
		$view 	= FD::view( 'Profiles' );

		// Test if there's any id's being passed in.
		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_DELETE_NO_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Let's go through each of the profile and delete it.
		foreach( $ids as $id )
		{
			$profile    = FD::table( 'Profile' );
			$profile->load( $id );

			// If profile has members in it, do not try to delete this.
			if( $profile->hasMembers() )
			{
				$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_ERROR_DELETE_PROFILE_CONTAINS_USERS' , $profile->title ) , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}

			// Now try to delete the profile.
			$profile->delete();
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_DELETED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	public function form()
	{
		// Check for request forgeries!
		FD::checkToken();

		// Get the view.
		$view			= $this->getCurrentView();
		$view->task 	= $this->getTask();

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Saves a new or existing profile.
	 *
	 * @since   1.0
	 * @access  public
	 * @param   null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function store()
	{
		// Check for request forgeries!
		ES::checkToken();

		$pid = $this->input->get('id', 0, 'int');
		$cid = $this->input->get('cid', 0, 'int');
		$post = $this->input->getArray('post');

		// Determines if this is a new profile type
		$isNew = !$pid ? true : false;

		// Get the current task
		$task = $this->getTask();
		$isCopy = $task == 'savecopy' ? true : false;

		// Load the profile type.
		$profile = ES::table('Profile');

		if ($cid && $isCopy) {
			$profile->load($cid);

			//reset the pid
			$post['id'] = $cid;
		} else {
			$profile->load($pid);
		}

		// Bind the posted data.
		$profile->bind($post);

		// Get the current task since we need to know what to do after the storing is successful.
		$this->view->task = $task;

		// Bind the user group's that are associated with the profile.
		$gid = $this->input->get('gid', '', 'default');

		// This is a minimum requirement to create a profile.
		if (!$gid) {
			$this->view->setMessage('COM_EASYSOCIAL_PROFILES_FORM_ERROR_SELECT_GROUP', SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__, $profile);
		}

		// Bind user groups for this profile.
		$profile->bindUserGroups($gid);

		// Validate the profile field.
		$valid = $profile->validate();

		// If there's errors, just show the error.
		if ($valid !== true) {
			$this->view->setMessage($profile->getError() , SOCIAL_MSG_ERROR);
			return $this->view->call( __FUNCTION__ , $profile );
		}

		// Try to store the profile.
		if (!$profile->store()) {
			$this->view->setMessage($profile->getError() , SOCIAL_MSG_ERROR);
			return $this->view->store($profile);
		}

		// Bind the access
		$profile->bindAccess($post['access']);

		// If this profile is default, we need to ensure that the rest of the profiles are not default any longer.
		if ($profile->default) {
			$profile->makeDefault($isCopy);
		}

		// Store the avatar for this profile.
		$file = $this->input->files->get('avatar', '');

		// Try to upload the profile's avatar if required
		if (!empty($file['tmp_name'])) {
			$profile->uploadAvatar($file);
		}

		// Get fields data separately as we need allowraw here
		$postfields = JRequest::getVar('fields', $default = null, $hash = 'POST', $type = 'none', $mask = JREQUEST_ALLOWRAW );

		// Set the fields for this profile type.
		if (!empty($postfields)) {
			$fieldsData = FD::json()->decode($postfields);

			$fieldsLib = FD::fields();
			$fieldsLib->saveFields($profile->id, SOCIAL_TYPE_PROFILES, $fieldsData, array( 'copy' => $task === 'savecopy' ) );

			// After saving fields, we have to reset all the user's completed fields count in this profile
			$usersModel = ES::model('Users');
			$usersModel->resetCompletedFieldsByProfileId($profile->id);
		}

		// Set the privacy for this profile type
		if (isset($post['privacy'])) {

			$privacyLib = FD::privacy();
			$resetMap 	= $privacyLib->getResetMap( 'all' );

			$privacy = $post['privacy'];
			$ids     = $post['privacyID'];

			$requireReset = isset( $post['privacyReset'] ) ? true : false;

			$data = array();

			if( count( $privacy ) )
			{
				foreach( $privacy as $group => $items )
				{
					foreach( $items as $rule => $val )
					{
						$id = $ids[ $group ][ $rule ];

						$id = explode('_', $id);

						$obj = new stdClass();

						$obj->id 	= $id[0];
						$obj->mapid = $id[1];
						$obj->value = $val;
						$obj->reset  = false;

						//check if require to reset or not.
						$gr = strtolower( $group . '.' . $rule );

						if ($gr != 'field.joomla_username'
							&& $gr != 'field.joomla_email'
							&& $gr != 'field.joomla_timezone'
							&& $gr != 'field.joomla_fullname'
							) {
							$gr = str_replace( '_', '.', $gr );
						}

						if( $requireReset && in_array( $gr,  $resetMap ) )
						{
							$obj->reset = true;
						}

						$data[] = $obj;
					}

				}

			}


			$privacyModel 	= FD::model( 'Privacy' );
			$privacyModel->updatePrivacy( $profile->id , $data, SOCIAL_PRIVACY_TYPE_PROFILES );
		}


		// default apps assignment.
		if (!$isNew && isset($post['apps']) && $post['apps'] && is_array($post['apps'])) {
			$profile->assignUsersApps($post['apps']);
		}

		// If this is a save as copy
		if ($isCopy && $pid) {
			$profile->copyAvatar($pid);
		}

		$message = 'COM_EASYSOCIAL_PROFILES_PROFILE_CREATED_SUCCESSFULLY';

		if (!$isNew) {
			$message = 'COM_EASYSOCIAL_PROFILES_PROFILE_UPDATED_SUCCESSFULLY';
		}

		if ($isCopy) {
			$message = 'COM_EASYSOCIAL_PROFILES_PROFILE_COPIED_SUCCESSFULLY';
		}

		// Set message.
		$this->view->setMessage($message, SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $profile);
	}

	/**
	 * Method to process files that is being sent to store default avatars.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function uploadDefaultAvatars()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view	= $this->getCurrentView();

		// Load a table mapping.
		$defaultAvatar 	= FD::table( 'DefaultAvatar' );

		// Set the unique id for this avatar item.
		$defaultAvatar->uid 	= JRequest::getInt( 'uid' );

		// Set the unique type for this avatar item.
		$defaultAvatar->type 	= JRequest::getVar( 'type' , SOCIAL_TYPE_PROFILES );

		// Set the default state of the avatar to be published.
		$defaultAvatar->state 	= SOCIAL_STATE_PUBLISHED;

		// Let's try to upload now.
		$file 	= JRequest::get( 'Files' );
		$state 	= $defaultAvatar->upload( $file );

		// There's an error when saving the images.
		if( !$state )
		{
			$view->setMessage( $defaultAvatar->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $defaultAvatar );
		}

		// Let's try to save the defaultAvatar now.
		$state 	= $defaultAvatar->store();

		// If we hit any errors, we should notify the user.
		if( !$state )
		{
			// Set the error to the view.
			$view->setMessage( $defaultAvatar->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $defaultAvatar );
		}

		return $view->call( __FUNCTION__ , $defaultAvatar );
	}

	/**
	 * Toggles a profile as default.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function toggleDefault()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get current cid to work on.
		$cid        = JRequest::getVar( 'cid' );
		$cid 		= FD::makeArray( $cid );

		// Get the current view object.
		$view 		= FD::view( 'Profiles' );

		// Get the profile object.
		$profile    = FD::table( 'Profile' );

		if( !$cid )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_PROFILE_DOES_NOT_EXIST' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// A single item can only be default at a time.
		$cid		= $cid[ 0 ];

		// Load the profile
		$profile->load( $cid );

		if( !$profile->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_PROFILE_DOES_NOT_EXIST' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Try to publish the profile.
		$state 	= $profile->makeDefault();

		if( !$state )
		{
			$view->setMessage( $profile->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the message
		$message 	= JText::_( 'COM_EASYSOCIAL_PROFILES_PROFILE_PROFILE_IS_NOW_DEFAULT_PROFILE' );

		// Set the message to view.
		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Publishes a profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function togglePublish()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get the current task
		$task 		= $this->getTask();

		// Get current cid to work on.
		$cid        = JRequest::getVar( 'cid' );
		$cid 		= FD::makeArray( $cid );

		// Get the current view object.
		$view 		= FD::view( 'Profiles' );

		foreach( $cid as $id )
		{
			// Get the profile object.
			$profile    = FD::table( 'Profile' );

			// Load the profile
			$profile->load( $id );

			if( !$profile->id )
			{
				$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_PROFILE_DOES_NOT_EXIST' ) , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}

			// Do not allow admin to unpublish a default profile
			if( $profile->default )
			{
				$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_UNABLE_TO_UNPUBLISH_DEFAULT_PROFILE' ) , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}

			// Try to publish the profile.
			if( !$profile->$task() )
			{
				$view->setMessage( $profile->getError() , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}
		}

		// Get the message
		$message 	= $task == 'publish' ? JText::_( 'COM_EASYSOCIAL_PROFILES_PROFILE_PUBLISHED_SUCCESSFULLY' ) : JText::_( 'COM_EASYSOCIAL_PROFILES_PROFILE_UNPUBLISHED_SUCCESSFULLY' );

		// Set the message to view.
		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows a profile to be ordered down
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function moveDown()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the id from the request
		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that they are in the array
		$ids 	= FD::makeArray( $ids );

		if( !$ids )
		{
			$view->setMessage( JText::_( 'Invalid profile id provided' ) , SOCIAL_MSG_ERROR );
			return $view->call( 'move' );
		}

		foreach( $ids as $id )
		{
			$profile 	= FD::table( 'Profile' );
			$profile->load( $id );

			// Move direction up
			$profile->move( 1 );
		}

		$view->setMessage( JText::_( 'Profile re-ordered successfully.' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( 'move' );
	}

	/**
	 * Allows a profile to be ordered up
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function moveUp()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the id from the request
		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that they are in the array
		$ids 	= FD::makeArray( $ids );

		if( !$ids )
		{
			$view->setMessage( JText::_( 'Invalid profile id provided' ) , SOCIAL_MSG_ERROR );
			return $view->call( 'move' );
		}

		foreach( $ids as $id )
		{
			$profile 	= FD::table( 'Profile' );
			$profile->load( $id );

			// Move direction up
			$profile->move( -1 );

			// $profile->store();
		}

		$view->setMessage( JText::_( 'Profile re-ordered successfully.' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( 'move' );
	}

	/**
	 * Updates the ordering of the profiles
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateOrdering()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get all the inputs.
		$ids 	= JRequest::getVar( 'cid' , null , 'post' , 'array' );
		$order	= JRequest::getVar( 'order' , null , 'post' , 'array' );

		// Sanitize the input
		JArrayHelper::toInteger( $ids );
		JArrayHelper::toInteger( $order );

		$model	= FD::model( 'Profiles' );
		$model->saveOrder( $ids , $order );

		$view 	= $this->getCurrentView();

		return $view->call( __FUNCTION__ );
	}

	public function getFieldValues()
	{
		// Check for request forgeries.
		FD::checkToken();

		$fieldid		= JRequest::getInt( 'fieldid', 0 );
		$values	= '';

		if( $fieldid !== 0 )
		{
			$fields	= FD::table( 'field' );
			$fields->load( $fieldid );

			$values 	= FD::json()->decode( $fields->params );

			if( !is_object( $values ) )
			{
				$values = new stdClass();
			}

			$values->core_title = $fields->title;
			$values->core_display_title = (boolean) $fields->display_title;
			$values->core_description = $fields->description;
			$values->core_required = (boolean) $fields->required;
			$values->core_default = $fields->default;
		}

		FD::view( 'Profiles' )->call( __FUNCTION__, $values );
	}

	/**
	 * Save the custom fields.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function createBlankProfile()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Create the new profile
		$newProfile = FD::table( 'Profile' );
		$newProfile->title = 'temp';
		$newProfile->createBlank();
		$id = $newProfile->id;

		FD::view( 'Profiles' )->call( __FUNCTION__, $id );
	}

	/**
	 * Deletes the profile avatar
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function deleteProfileAvatar()
	{
		$view = $this->getCurrentView();

		$id = JRequest::getInt( 'id' );

		$table = FD::table( 'profile' );

		$state = $table->load( $id );

		if( $state )
		{
			$state = $table->removeAvatar();

			if( !$state )
			{
				$view->setMessage( 'PROFILES: Unable to delete the avatar', SOCIAL_MSG_ERROR );
			}
		}

		$view->call( __FUNCTION__ );
	}
}
