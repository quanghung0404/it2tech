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

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerUsers extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		// Map the alias methods here.
		$this->registerTask( 'save'		, 'store' );
		$this->registerTask( 'savenew' 	, 'store' );
		$this->registerTask( 'apply'    , 'store' );

		$this->registerTask( 'publish'	, 'togglePublish' );
		$this->registerTask( 'unpublish', 'togglePublish' );

		$this->registerTask( 'activate'		, 'toggleActivation' );
		$this->registerTask( 'deactivate'	, 'toggleActivation' );
	}

	/**
	 * Resends an activation email
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function resendActivate()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get a list of user ids
		$ids = $this->input->get('cid', array(), 'Array');
		$view = $this->getCurrentView();

		$model = FD::model('Registration');
		$total = 0;

		foreach ($ids as $id) {

			$id = (int) $id;
			$user = FD::user($id);

			// If the user is not blocked and doesn't have an activation, we shouldn't be doing anything.
			if (!$user->block || !$user->activation) {
				continue;
			}

			$model->resendActivation($user);
			$total++;
		}

		if (!$total) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_USERS_ACTIVATION_EMAIL_NO_VALID_USERS'), SOCIAL_MSG_SUCCESS);
		} else {
			$view->setMessage(JText::_('COM_EASYSOCIAL_USERS_ACTIVATION_EMAIL_RESENT'), SOCIAL_MSG_SUCCESS);
		}

		return $view->call(__FUNCTION__);
	}

	/**
	 * Toggle's user publishing state
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current task
		$task = $this->getTask();

		// Get the user's id.
		$ids = $this->input->get('cid', array(), 'array');

		// Get the state
		$method = $task == 'unpublish' ? 'block' : 'unblock';

		foreach ($ids as $id) {

			$user = FD::user($id);
			$my = JFactory::getUser();

			// Do not allow the person to block themselves.
			if ($user->id == $this->my->id) {
				$this->view->setMessage(JText::_('COM_EASYSOCIAL_USERS_NOT_ALLOWED_TO_BLOCK_SELF'), SOCIAL_MSG_ERROR);
				return $this->view->call(__FUNCTION__, $task);
			}

			$state = $user->$method();
		}

		$message = $task == 'unpublish' ? 'COM_EASYSOCIAL_USERS_UNPUBLISHED_SUCCESSFULLY' : 'COM_EASYSOCIAL_USERS_PUBLISHED_SUCCESSFULLY';

		$this->view->setMessage($message, SOCIAL_MSG_SUCCESS);
		return $this->view->call(__FUNCTION__, $task);
	}

	/**
	 * Toggles activation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function activate()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current task
		$view	= $this->getCurrentView();

		// Get the user id that we want to modify now
		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that it's an array
		$ids 	= FD::makeArray( $ids );

		foreach( $ids as $id )
		{
			$user 	= FD::user( $id );

			$user->activate();
		}

		$view->setMessage( JText::_( 'User account activated successfully' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Exports users
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function export()
	{
		// Check for request forgeries
		FD::checkToken();

		$output = fopen('php://output', 'w');

		// Determines if this export is on specific profile
		$id = $this->input->get('profileId', '', 'int');

		if (!$id) {
			$profileTitle = 'all';
		} else {
			$profile = FD::table('Profile');
			$profile->load($id);

			$profileTitle = str_ireplace(' ', '_', strtolower($profile->get('title')));
		}

		// Get a list of users and their custom fields
		$model = FD::model('Users');
		$data  = $model->export($id);

		// Output each row now
		foreach ($data as $row) {
			fputcsv($output, $row);
		}

		// Generate the date of export
		$date = FD::date();
		$fileName = 'users_export_' . $profileTitle . '_' . $date->format('m_d_Y') . '.csv';

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $fileName);

		fclose($output);
		exit;

	}

	/**
	 * Switches a user's profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function switchProfile()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get affected users
		$ids = $this->input->get('cid', array(), 'array');

		// Get the profile to switch to
		$profileId = $this->input->get('profile', 0, 'int');

		// Get the model
		$model = ES::model('Profiles');

		// For invalid ids
		if (!$ids) {
			// @TODO: Add error checking here
		}

		// Should we be updating the user group in Joomla
		$updateGroups = $this->input->get('switch_groups', false, 'bool');

		foreach ($ids as $id) {

			// Switch the user's profile
			$model->updateUserProfile($id, $profileId);


			// Determines if we should also update the user's usergroups
			if ($updateGroups) {
				$model->updateJoomlaGroup($id, $profileId);
			}

			//lets update the user records in com_finder.
			$user = FD::user($id);
			$user->syncIndex();
		}

		$this->view->setMessage('COM_EASYSOCIAL_USERS_USER_PROFILE_UPDATED');
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Allows caller to reset points for specific user
	 *
	 * @since	1.4.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function resetPoints()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the current view
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$points = ES::points();
			$points->reset($id);
		}

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_USERS_POINTS_RESET_SUCCESS'), SOCIAL_MSG_SUCCESS);
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Inserts points for a list of users
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function insertPoints()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 		= $this->getCurrentView();

		// Total points to insert for the user
		$points 	= JRequest::getInt( 'points' );

		// Get the custom message to insert
		$message 	= JRequest::getVar( 'message' );

		// Get list of users to assign points to
		$uids 		= JRequest::getVar( 'uid' );
		$uids 		= FD::makeArray( $uids );

		// If user is not provided, break this
		if (empty($uids)) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_USERS_UNABLE_TO_FIND_USER' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Load up our own points library.
		$lib 	= FD::points();

		foreach ($uids as $userId) {
			$user = FD::user($userId);

			$lib->assignCustom( $user->id , $points , $message );
		}

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_USERS_POINTS_ASSIGNED_TO_USERS' , $points ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Inserts a badge for a list of users
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function insertBadge()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 		= $this->getCurrentView();

		// Get the badge to insert
		$id 		= JRequest::getInt( 'id' );
		$badge 		= FD::table( 'Badge' );
		$badge->load( $id );

		if( !$id || !$badge->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_UNABLE_TO_FIND_BADGE' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$uids 		= JRequest::getVar( 'uid' );
		$uids 		= FD::makeArray( $uids );

		if( empty( $uids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_UNABLE_TO_FIND_USER' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$model 	= FD::model( 'Badges' );

		// Get custom message
		$message	= JRequest::getVar( 'message' );

		// Get custom achieved date
		$achieved 	= JRequest::getVar( 'achieved' );

		foreach( $uids as $userId )
		{
			$user 	= FD::user( $userId );

			// Only create a new record if user hasn't achieved the badge yet.
			if( !$model->hasAchieved( $badge->id , $user->id ) )
			{
				// Insert the badge
				$lib 	= FD::badges();

				$state 		= $lib->create( $badge , $user , $message , $achieved );

				if( $state )
				{
					$lib->addStream( $badge , $user->id );
					$lib->sendNotification( $badge , $user->id );
				}
			}
		}

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_USERS_BADGE_ASSIGNED_TO_USERS' , $badge->get( 'title' ) ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Retrieves the total number of pending users on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPending()
	{
		// Check for request forgeries
		FD::checkToken();

		$view 	= $this->getCurrentView();

		$model 	= FD::model( 'Users' );

		$total 	= $model->getTotalPending();

		return $view->call( __FUNCTION__ , $total );
	}

	/**
	 * Allows caller to remove a badge
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeBadge()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the badge id.
		$id 	= JRequest::getInt( 'id' );
		$userId	= JRequest::getInt( 'userid' );

		// Load up the badge library
		$badge	= FD::badges();

		$badge->remove( $id , $userId );

		$view->setMessage( JText::_( 'Achievement removed from user successfully.' ) );
		$view->call( __FUNCTION__ );
	}

	/**
	 * Deletes a user from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the list of user that needs to be deleted.
		$ids = $this->input->get('id', array(), 'array');
		$ids = FD::makeArray($ids);

		// Get the current view.
		$view = $this->getCurrentView();

		// Let's loop through all of the users now
		foreach ($ids as $id) {
			$user = FD::user($id);

			if ($user) {
				$user->delete();
			}
		}

		return $view->call(__FUNCTION__);
	}

	/**
	 * Approves a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get current view.
		$view 	= $this->getCurrentView();

		// Get the user's id
		$ids 	= JRequest::getVar( 'id' );

		if( !$ids )
		{
			$ids 	= JRequest::getVar( 'cid' );
		}

		// Ensure that they are in an array
		$ids 	= FD::makeArray( $ids );

		if( !$ids )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Determine if we should send a confirmation email to the user.
		$sendEmail	 = JRequest::getVar( 'sendConfirmationEmail' ) ? true : false;

		foreach( $ids as $id )
		{
			// Get the user.
			$user 	= FD::user( $id );

			$user->approve( $sendEmail );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_APPROVED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $user );
	}


	/**
	 * Approves a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get the user's id
		$ids 	= JRequest::getVar( 'id' );

		$ids 	= FD::makeArray( $ids );

		// Get current view.
		$view 	= $this->getCurrentView();

		// Determine if we should send a confirmation email to the user.
		$sendEmail	 = JRequest::getVar( 'sendRejectEmail' ) ? true : false;

		// Determine if we should delete the user.
		$deleteUser = JRequest::getVar( 'deleteUser' ) ? true : false;

		// Get the rejection message
		$reason 	= JRequest::getVar( 'reason' );

		foreach( $ids as $id )
		{
			// Get the user.
			$user 	= FD::user( $id );

			// Try to approve the user.
			$user->reject( $reason , $sendEmail , $deleteUser );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_REJECTED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Assigns user to a specific group
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function assign()
	{
		// Check for request forgeries
		FD::checkToken();

		$view 	= $this->getCurrentView();

		$ids 	= JRequest::getVar( 'cid' );

		// Ensure that id's are in an array
		$ids 	= FD::makeArray( $ids );

		// Get the group id
		$gid 	= JRequest::getInt( 'gid' );

		if( !$ids )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_UNABLE_TO_FIND_USER' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		if( !$gid )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_UNABLE_TO_FIND_GROUP' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$user 	= FD::user( $id );

			$user->assign( $gid );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_USERS_ASSIGNED_TO_GROUP' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Stores the user object
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store()
	{
		// Check for request forgeries
		FD::checkToken();

		// Load front end's language file
		FD::language()->loadSite();

		// Get the current task
		$task = $this->getTask();

		// Determine if this is an edited user.
		$id = $this->input->get('id', 0, 'int');
		$id = !$id ? null : $id;

		// Get the posted data
		$post = $this->input->getArray('post');

		// check if user is required to reset password or not.
		// $requiredPasswordReset = isset( $post['require_reset'] ) ? $post['require_reset'] : 0;

		// this should come from backend user management page only.
		$autoApproval = isset( $post['autoapproval'] ) ? $post['autoapproval'] : 0;

		// Create an options array for custom fields
		$options = array();

		if (!$id) {
			$user = new SocialUser();

			// Get the profile id
			$profileId = $this->input->get('profileId');
		} else {
			// Here we assume that the user record already exists.
			$user = FD::user( $id );

			// Get the profile id from the user
			$profileId = $user->getProfile()->id;

			$options['data'] = true;
			$options['dataId'] = $id;
			$options['dataType'] = SOCIAL_TYPE_USER;
		}

		// Set the profile id
		$options['profile_id'] = $profileId;

		// Set the group
		$options['group'] = SOCIAL_FIELDS_GROUP_USER;

		// Load the profile
		$profile = FD::table('Profile');
		$profile->load( $profileId );

		// Set the visibility
		// since this is at backend so we assume admin is editing someone else.
		if (! $id) {
			$options['visible'] = SOCIAL_PROFILES_VIEW_REGISTRATION;
		}

		// Get fields model
		$fieldsModel = ES::model('Fields');

		// Get the custom fields
		$fields = $fieldsModel->getCustomFields($options);

		// Initialize default registry
		$registry = ES::registry();

		// Get disallowed keys so we wont get wrong values.
		$disallowed = array(ES::token(), 'option' , 'task' , 'controller', 'autoapproval');

		// Process $_POST vars
		foreach ($post as $key => $value) {

			if (!in_array($key, $disallowed)) {

				if (is_array($value)) {
					$value = json_encode($value);
				}

				$registry->set($key, $value);
			}
		}

		// Test to see if the points has changed.
		$points = $this->input->get('points', 0, 'int');

		// Lets get the difference of the points
		$userPoints = $user->getPoints();

		// If there is a difference, the admin may have altered the user points
		if ($userPoints != $points) {

			// Insert a new points record for this new adjustments.
			if ($points > $userPoints) {

				// If the result points is larger, we always need to subtract and get the balance.
				$totalPoints 	= $points - $userPoints;
			} else {

				// If the result points is smaller, we always need to subtract.
				$totalPoints 	= -($userPoints - $points);
			}

			$pointsLib = FD::points();
			$pointsLib->assignCustom($user->id, $totalPoints, JText::_('COM_EASYSOCIAL_POINTS_ADJUSTMENTS'));

			$user->points = $points;
		}

		// Convert the values into an array.
		$data = $registry->toArray();

		// Get the fields lib
		$fieldsLib = FD::fields();

		// Build arguments to be passed to the field apps.
		$args = array(&$data, &$user);

		// @trigger onAdminEditValidate
		$errors = $fieldsLib->trigger( 'onAdminEditValidate', SOCIAL_FIELDS_GROUP_USER, $fields, $args );

		// If there are errors, we should be exiting here.
		if (is_array($errors) && count($errors) > 0) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_SAVE_ERRORS'), SOCIAL_MSG_ERROR);

			// We need to set the data into the post again because onEditValidate might have changed the data structure
			JRequest::set($data, 'post');

			return $this->view->call('form', $errors);
		}

		// @trigger onAdminEditBeforeSave
		$errors = $fieldsLib->trigger('onAdminEditBeforeSave', SOCIAL_FIELDS_GROUP_USER, $fields, $args );

		if (is_array($errors) && count($errors) > 0) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_ERRORS_IN_FORM'), SOCIAL_MSG_ERROR);

			// We need to set the data into the post again because onEditValidate might have changed the data structure
			JRequest::set($data, 'post');

			return $this->view->call('form', $errors);
		}

		// Update the user's gid
		$gid = $this->input->get('gid', array(), 'array');
		$data['gid'] = $gid;

		// Bind the user object with the form data.
		$user->bind($data);


		// Create a new user record if the id don't exist yet.
		if (!$id) {
			$model = ES::model('Users');
			$user = $model->create($data, $user, $profile);

			if (!$user) {
				$this->view->setMessage($model->getError(), SOCIAL_MSG_ERROR);

				// We need to set the data into the post again because onEditValidate might have changed the data structure
				JRequest::set($data, 'post');

				return $this->view->call( 'form' );
			}

			// If admin selected auto approval, automatically approve this user.
			if ($autoApproval) {
				$user = ES::user($user->id);
				$user->approve(false);
			}

			$message = ($autoApproval) ? JText::_('COM_EASYSOCIAL_USERS_CREATED_SUCCESSFULLY_AND_APPROVED') : JText::_('COM_EASYSOCIAL_USERS_CREATED_SUCCESSFULLY');
		} else {
			// If this was an edited user, save the user object.
			$user->save();

			$message = JText::_('COM_EASYSOCIAL_USERS_USER_UPDATED_SUCCESSFULLY');
		}

		// Reconstruct args
		$args = array(&$data, &$user);

		// @trigger onEditAfterSave
		$fieldsLib->trigger( 'onAdminEditAfterSave', SOCIAL_FIELDS_GROUP_USER, $fields, $args );

		// Bind the custom fields for the user.
		$user->bindCustomFields($data);

		// Reconstruct args
		$args = array(&$data, &$user);

		// @trigger onEditAfterSaveFields
		$fieldsLib->trigger('onAdminEditAfterSaveFields', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		// Prepare the dispatcher
		FD::apps()->load(SOCIAL_TYPE_USER);
		$dispatcher = FD::dispatcher();
		$args = array(&$user, &$fields, &$data);

		// @trigger: onUserProfileUpdate
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onUserProfileUpdate', $args);

		// Process notifications
		if (isset($post['notifications']) && !empty($post['notifications'])) {
			$systemNotifications = $post['notifications']['system'];
			$emailNotifications = $post['notifications']['email'];

			// Store the notification settings for this user.
			$model = ES::model('Notifications');

			$model->saveNotifications($systemNotifications, $emailNotifications, $user);
		}

		// Process privacy items
		if (isset($post['privacy']) && !empty($post['privacy'])) {
			$resetPrivacy = isset($post['privacyReset']) ? true : false;

			$user->bindPrivacy($post['privacy'], $post['privacyID'], $post['privacyCustom'], $post['privacyOld'], $resetPrivacy);
		}

		$this->view->setMessage($message, SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $task, $user);
	}
}
