<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import main controller
FD::import('site:/controllers/controller');

class EasySocialControllerProfile extends EasySocialController
{
	public function saveClose()
	{
		return $this->save();
	}

	/**
	 * Save user's information.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries.
		ES::checkToken();

		// Ensure that the user is registered
		ES::requireLogin();

		// Clear previous session
		$session = JFactory::getSession();
		$session->clear('easysocial.profile.errors', SOCIAL_SESSION_NAMESPACE);

		// Get post data.
		$post = JRequest::get('POST');

		// Only fetch relevant fields for this user.
		$options = array('profile_id' => $this->my->getProfile()->id, 'data' => true, 'dataId' => $this->my->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => SOCIAL_PROFILES_VIEW_EDIT, 'group' => SOCIAL_FIELDS_GROUP_USER);

		// Get all published fields apps that are available in the current form to perform validations
		$fieldsModel = ES::model('Fields');
		$fields = $fieldsModel->getCustomFields($options);

		// Initialize default registry
		$registry = ES::registry();

		// Get disallowed keys so we wont get wrong values.
		$disallowed = array(ES::token(), 'option' , 'task' , 'controller');

		// Process $_POST vars
		foreach ($post as $key => $value) {

			if (!in_array($key, $disallowed)) {
				if (is_array($value)) {
					$value = json_encode($value);
				}

				$registry->set($key, $value);
			}
		}

		// Convert the values into an array.
		$data = $registry->toArray();

		// Perform field validations here. Validation should only trigger apps that are loaded on the form
		// @trigger onRegisterValidate
		$fieldsLib = ES::fields();

		// Get the general field trigger handler
		$handler = $fieldsLib->getHandler();

		// Build arguments to be passed to the field apps.
		$args = array(&$data, &$this->my);

		// Ensure that there is no errors.
		// @trigger onEditValidate
		$errors = $fieldsLib->trigger('onEditValidate', SOCIAL_FIELDS_GROUP_USER, $fields, $args, array($handler, 'validate'));

		// If there are errors, we should be exiting here.
		if (is_array($errors) && count($errors) > 0) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_SAVE_ERRORS'), SOCIAL_MSG_ERROR);

			// We need to set the proper vars here so that the es-wrapper contains appropriate class
			JRequest::setVar('view', 'profile', 'POST');
			JRequest::setVar('layout', 'edit', 'POST');

			// We need to set the data into the post again because onEditValidate might have changed the data structure
			JRequest::set($data, 'post');

			return $this->view->call('edit', $errors, $data);
		}

		// @trigger onEditBeforeSave
		$errors = $fieldsLib->trigger('onEditBeforeSave', SOCIAL_FIELDS_GROUP_USER, $fields, $args, array($handler, 'beforeSave'));

		if (is_array($errors) && count($errors) > 0) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_ERRORS_IN_FORM'), SOCIAL_MSG_ERROR);

			// We need to set the proper vars here so that the es-wrapper contains appropriate class
			JRequest::setVar('view', 'profile');
			JRequest::setVar('layout', 'edit');

			// We need to set the data into the post again because onEditValidate might have changed the data structure
			JRequest::set($data, 'post');

			return $this->view->call('edit', $errors);
		}

		// Bind the my object with appropriate data.
		$this->my->bind($data);

		// Save the user object.
		$this->my->save();

		// Reconstruct args
		$args = array(&$data, &$this->my);

		// @trigger onEditAfterSave
		$fieldsLib->trigger('onEditAfterSave', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		// Bind custom fields for the user.
		$this->my->bindCustomFields($data);

		// Reconstruct args
		$args = array(&$data, &$this->my);

		// @trigger onEditAfterSaveFields
		$fieldsLib->trigger('onEditAfterSaveFields', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		// Now we update the Facebook details if it is available
		$associatedFacebook = $this->input->get('associatedFacebook', 0, 'int');

		if (!empty($associatedFacebook)) {
			$facebookPull = $this->input->get('oauth_facebook_pull', null, 'default');
			$facebookPush = $this->input->get('oauth_facebook_push', null, 'default');
			$facebookTable = $this->my->getOAuth(SOCIAL_TYPE_FACEBOOK);

			if ($facebookTable) {
				$facebookTable->pull = $facebookPull;
				$facebookTable->push = $facebookPush;

				$facebookTable->store();
			}
		}

		// Add stream item to notify the world that this user updated their profile.
		$this->my->addStream('updateProfile');

		// Update indexer
		$this->my->syncIndex();


		// @points: profile.update
		// Assign points to the user when their profile is updated
		ES::points()->assign('profile.update', 'com_easysocial', $this->my->id);

		// Prepare the dispatcher
		ES::apps()->load(SOCIAL_TYPE_USER);

		$dispatcher = FD::dispatcher();
		$args = array(&$my, &$fields, &$data);

		// @trigger: onUserProfileUpdate
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onUserProfileUpdate', $args);

		// @trigger onProfileCompleteCheck
		// This should return an array of booleans to state which field is filled in.
		// We count the returned result since it will be an array of trues that marks the field that have data for profile completeness checking.
		// We do this after all the data has been saved, and we reget the fields from the model again.
		// We also need to reset the cached field data
		SocialTableField::$_fielddata = array();
		$options = array('profile_id' => $this->my->getProfile()->id, 'data' => true, 'dataId' => $this->my->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => SOCIAL_PROFILES_VIEW_EDIT, 'group' => SOCIAL_FIELDS_GROUP_USER);
		$fields = $fieldsModel->getCustomFields($options);

		$args = array(&$this->my);
		$completedFields = $fieldsLib->trigger('onProfileCompleteCheck', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		$table = ES::table('Users');
		$table->load(array('user_id' => $this->my->id));
		$table->completed_fields = count($completedFields);
		$table->store();

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_ACCOUNT_UPDATED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $this->my);
	}

	/**
	 * Save user's privacy.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function savePrivacy()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is registered
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// current logged in user
		$my = FD::user();

		$privacyLib = FD::privacy();
		$resetMap = $privacyLib->getResetMap();



		$post 	 	= JRequest::get('POST');
		$privacy 	= $post['privacy'];
		$ids     	= $post['privacyID'];
		$curValues  = $post['privacyOld'];
		$customIds  = $post['privacyCustom'];

		$requireReset = isset( $post['privacyReset'] ) ? true : false;

		$data = array();

		if( count( $privacy ) )
		{
			foreach( $privacy as $group => $items )
			{
				foreach( $items as $rule => $val )
				{
					$id 		 = $ids[ $group ][ $rule ];
					$custom 	 = $customIds[ $group ][ $rule ];
					$curVal 	 = $curValues[ $group ][ $rule ];

					$customUsers = array();


					if( !empty( $custom ) )
					{
						$tmp = explode( ',', $custom );
						foreach( $tmp as $tid )
						{
							if( !empty( $tid ) )
							{
								$customUsers[] = $tid;
							}
						}
					}

					$id = explode('_', $id);

					$obj = new stdClass();

					$obj->id 	 = $id[0];
					$obj->mapid  = $id[1];
					$obj->value  = $val;
					$obj->custom = $customUsers;
					$obj->reset  = false;

					//check if require to reset or not.
					$gr = strtolower( $group . '.' . $rule );
					if( $requireReset && in_array( $gr,  $resetMap ) )
					{
						$obj->reset = true;
					}

					$data[] = $obj;
				}

			}

		}

		// Set the privacy for this user
		if( count( $data ) > 0 )
		{
			$privacyModel 	= FD::model( 'Privacy' );
			$state 			= $privacyModel->updatePrivacy( $my->id , $data, SOCIAL_PRIVACY_TYPE_USER );

			if( $state !== true )
			{
				$view->setMessage( $state , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}
		}

		// @points: privacy.update
		// Assign points when user updates their privacy
		$points = FD::points();
		$points->assign( 'privacy.update' , 'com_easysocial' , $my->id );


		//index user access in finder
		$my->syncIndex();


		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PRIVACY_UPDATED_SUCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows user to remove his avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeAvatar()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		$my 	= FD::user();
		$my->removeAvatar();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_AVATAR_REMOVED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Save user's notification.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function saveNotification()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user is registered
		FD::requireLogin();

		// current logged in user
		$my = FD::user();

		// Get post data.
		$post 	= JRequest::get( 'POST' );

		// Get the current view.
		$view 	= $this->getCurrentView();

		$systemNotifications 	= $post[ 'system' ];
		$emailNotifications 	= $post[ 'email' ];

		$model 	= FD::model( 'Notifications' );
		$state	= $model->saveNotifications( $systemNotifications , $emailNotifications , $my );

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_NOTIFICATION_UPDATED_SUCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Retrieves the timeline for the current user that is being viewed.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getStream()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get the view.
		$view = $this->getCurrentView();

		// set jrequest view
		JRequest::set( array('view'=>'profile') );

		// Get the type of the stream to load.
		$type = $this->input->get('type', '', 'word');

		// Get the current user that is being viewed.
		$id = JRequest::getInt('id', null);

		$user = FD::user($id);

		// @TODO: Check if the viewer can access the user's timeline or not.

		// Retrieve user's stream
		$stream = FD::get('Stream');

		$stickies = $stream->getStickies(array('userId' => $user->id, 'limit' => 0));
		if ($stickies) {
			$stream->stickies = $stickies;
		}

		$appType = '';

		$options = array('userId' => $user->id, 'nosticky' => true);

		if ($type == 'appFilter') {

			$stream->filter	= 'custom';

			// we need to use string and not 'word' due to some app name has number. e.g k2
			$appType = $this->input->get('filterId', '', 'string');
			$options['context'] = $appType;

			$options['actorId'] = $user->id;
		}

		$stream->get($options);

		// Retrieve user's status
		$story = FD::get('Story', SOCIAL_TYPE_USER);
		$story->target = $user->id;

		$stream->story = $story;

		return $view->call( __FUNCTION__, $stream, $story );
	}

	/**
	 * Allows a user to follow another user.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function follow()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user needs to be logged in.
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Get the object identifier.
		$id 	= JRequest::getInt( 'id' );

		// Get the user that is being followed
		$user 	= FD::user( $id );

		$type 	= JRequest::getVar('type');
		$group 	= JRequest::getVar('group', SOCIAL_APPS_GROUP_USER);

		// Get the current logged in user.
		$my		= FD::user();

		// Load subscription table.
		$subscription 	= FD::table('Subscription');

		// Get subscription library
		$subscriptionLib 	= FD::get('Subscriptions');

		// User should never be allowed to follow themselves.
		if ($my->id == $id) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FOLLOWERS_NOT_ALLOWED_TO_FOLLOW_SELF' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $subscription );
		}

		// Determine if the current user is already a follower
		$isFollowing 	= $subscriptionLib->isFollowing( $id , $type , $group , $my->id );

		// If it's already following, throw proper message
		if ($isFollowing) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_SUBSCRIPTIONS_ERROR_ALREADY_FOLLOWING_USER' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $subscription );
		}

		// If the user isn't alreayd following, create a new subscription record.
		$subscription->uid 		= $id;
		$subscription->type 	= $type . '.' . $group;
		$subscription->user_id	= $my->id;

		$state 	= $subscription->store();

		if (!$state) {
			$view->setMessage( $subscription->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $subscription );
		}

		// @badge: followers.follow
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'followers.follow' , $my->id , JText::_( 'COM_EASYSOCIAL_FOLLOWERS_BADGE_FOLLOWING_USER' ) );

		// @badge: followers.followed
		$badge->log( 'com_easysocial' , 'followers.followed' , $user->id , JText::_( 'COM_EASYSOCIAL_FOLLOWERS_BADGE_FOLLOWED' ) );

		// @points: profile.follow
		// Assign points when user follows another person
		$points = FD::points();
		$points->assign( 'profile.follow' , 'com_easysocial' , $my->id );

		// @points: profile.followed
		// Assign points when user is being followed by another person
		$points->assign( 'profile.followed' , 'com_easysocial' , $user->id );

		// check if admin want to add stream on following a user or not.
		$config = FD::config();
		if ($config->get( 'users.stream.following')) {
			// Share this on the stream.
			$stream 			= FD::stream();
			$streamTemplate		= $stream->getTemplate();

			// Set the actor.
			$streamTemplate->setActor( $my->id , SOCIAL_TYPE_USER );

			// Set the context.
			$streamTemplate->setContext( $subscription->id , SOCIAL_TYPE_FOLLOWERS );

			// Set the verb.
			$streamTemplate->setVerb( 'follow' );

			$streamTemplate->setAccess( 'followers.view' );

			// Create the stream data.
			$stream->add( $streamTemplate );
		}

        // Set the email options
        $emailOptions   = array(
            'title'     	=> 'COM_EASYSOCIAL_EMAILS_NEW_FOLLOWER_SUBJECT',
            'template'		=> 'site/followers/new.followers',
            'actor'     	=> $my->getName(),
            'actorAvatar'   => $my->getAvatar(SOCIAL_AVATAR_SQUARE),
            'actorLink'     => $my->getPermalink(true, true),
            'target'		=> $user->getName(),
            'targetLink'	=> $user->getPermalink(true, true),
            'totalFriends'		=> $my->getTotalFriends(),
            'totalFollowing'	=> $my->getTotalFollowing(),
            'totalFollowers'	=> $my->getTotalFollowers()
        );


		$state 	= FD::notify('profile.followed' , array($user->id), $emailOptions, array( 'url' => $my->getPermalink(false, false, false) ,  'actor_id' => $my->id , 'uid' => $id ));

		return $view->call( __FUNCTION__ , $subscription );
	}

	/**
	 * Allows a user to unfollow an object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfollow()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user needs to be logged in.
		FD::requireLogin();

		// Get current logged in user.
		$my		= FD::user();

		// Get the current view.
		$view 	= $this->getCurrentView();

		// Get the object identifier.
		$id 		= JRequest::getInt( 'id' );

		// Get the target that is being unfollowed
		$user 		= FD::user( $id );

		$type 		= JRequest::getVar( 'type' );
		$group 		= JRequest::getVar( 'group', SOCIAL_APPS_GROUP_USER );

		$subscribe  = FD::get( 'Subscriptions');
		$state		= $subscribe->unfollow( $id, $type, $group, $my->id );

		if( !$state )
		{
			$view->setMessage( 'COM_EASYSOCIAL_UNFOLLOW_ERROR', SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// @points: profile.unfollow
		// Assign points when user starts new conversation
		$points = FD::points();
		$points->assign( 'profile.unfollow' , 'com_easysocial' , $my->id );

		// @points: profile.unfollowed
		// Assign points when user starts new conversation
		$points->assign( 'profile.unfollowed' , 'com_easysocial' , $user->id );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Retrieves the dashboard contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getAppContents()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get the app id.
		$appId 		= JRequest::getInt( 'appId' );

		// Get the user's id.
		$userId 	= JRequest::getInt( 'id' );

		// Load application.
		$app 	= FD::table( 'App' );
		$state 	= $app->load( $appId );

		// Get the view.
		$view	 = $this->getCurrentView();

		// If application id is not valid, throw an error.
		if( !$appId || !$state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_APP_ID_INVALID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $app , $userId );
		}

		// @TODO: Check if the user has access to this app or not.

		return $view->call( __FUNCTION__ , $app , $userId );
	}

	/**
	 * Allows user to delete their own account
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

		// Determine if the user is really allowed
		if (!$this->my->deleteable()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_DELETE'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Determine if we should immediately delete the user
		if ($this->config->get('users.deleteLogic') == 'delete') {
			$mailTemplate = 'deleted.removed';

			// Delete the user.
			$this->my->delete();
		}

		if ($this->config->get( 'users.deleteLogic' ) == 'unpublish') {
			$mailTemplate = 'deleted.blocked';

			// Block the user
			$this->my->block();
		}

		// Send notification to admin
		// Push arguments to template variables so users can use these arguments
		$date = FD::date()->format(JText::_('COM_EASYSOCIAL_DATE_DMY'));

		$params = array(
						'name' => $this->my->getName(),
						'avatar' => $this->my->getAvatar(SOCIAL_AVATAR_MEDIUM),
						'profileLink' => JURI::root() . 'administrator/index.php?option=com_easysocial&view=users&layout=form&id=' . $this->my->id,
						'date' => $date,
						'totalFriends' => $this->my->getTotalFriends(),
						'totalFollowers' => $this->my->getTotalFollowers()
						);


		$title = JText::sprintf('COM_EASYSOCIAL_EMAILS_USER_DELETED_ACCOUNT_TITLE', $this->my->getName());

		// Get a list of super admins on the site.
		$usersModel = FD::model('Users');
		$admins = $usersModel->getSiteAdmins();

		if ($admins) {
			foreach ($admins as $admin) {

				// Respect admin's email settings
				if (!$admin->sendEmail) {
					continue;
				}

				$params['adminName'] = $admin->getName();

				$mailer = FD::mailer();
				$template = $mailer->getTemplate();

				$template->setRecipient($admin->getName(), $admin->email);
				$template->setTitle($title);
				$template->setTemplate('site/profile/' . $mailTemplate, $params);
				$template->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

				// Try to send out email to the admin now.
				$state = $mailer->create($template);
			}
		}

		// Log the user out from the system
		$this->my->logout();

		return $this->view->call(__FUNCTION__);
	}


	/**
	 * Allows admin to delete user account
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteUser()
	{
		// Check for request forgeries
		FD::checkToken();

		// Determine current logged in user is an admin or not.
		if (!$this->my->isSiteAdmin()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_DELETE_USER'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		$userId = $this->input->get('id', 0, 'int');

		$user = FD::user($userId);

		if (! $this->my->canDeleteUser($user)) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_DELETE_USER'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Log the user out from the system
		$app = JFactory::getApplication();
		$app->logout($user->id, array('clientid' => 0));

		// Delete the user.
		$user->delete();

		// Send notification to admin
		// Push arguments to template variables so users can use these arguments
		$params = array(
						'name'				=> $user->getName(),
						'avatar'			=> $user->getAvatar( SOCIAL_AVATAR_MEDIUM ),
						'profileLink'		=> JURI::root() . 'administrator/index.php?option=com_easysocial&view=users&layout=form&id=' . $user->id,
						'date'				=> FD::date()->format( JText::_('COM_EASYSOCIAL_DATE_DMY') ),
						'totalFriends'		=> $user->getTotalFriends(),
						'totalFollowers'	=> $user->getTotalFollowers()
						);


		$title = JText::sprintf('COM_EASYSOCIAL_EMAILS_ADMIN_USER_DELETED_TITLE', $user->getName());

		// Get a list of super admins on the site.
		$usersModel = FD::model('Users');
		$admins = $usersModel->getSiteAdmins();

		if ($admins) {
			foreach ($admins as $admin) {

				// Respect admin's email settings
				if (!$admin->sendEmail) {
					continue;
				}

				$params['adminName'] = $admin->getName();

				$mailer = FD::mailer();
				$template = $mailer->getTemplate();

				$template->setRecipient($admin->getName(), $admin->email);
				$template->setTitle($title);
				$template->setTemplate('site/profile/deleted.removed', $params);
				$template->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

				// Try to send out email to the admin now.
				$state = $mailer->create($template);
			}
		}

		$this->view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_ADMINTOOL_DELETE_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Allows site admin to unban a user
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unbanUser()
	{
		// Check for request forgeries
		ES::checkToken();

		// Determine current logged in user is an admin or not.
		if (!$this->my->isSiteAdmin()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_BAN_USER'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		$userId = $this->input->get('id', 0, 'int');
		$user = ES::user($userId);

		if (! $this->my->canBanUser($user)) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_BAN_USER'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Unblock the user
		$user->unblock();

		return $this->view->call(__FUNCTION__, $user);
	}

	/**
	 * Allows admin to ban / block user account
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function banUser()
	{
		// Check for request forgeries
		ES::checkToken();

		// Determine current logged in user is an admin or not.
		if (!$this->my->isSiteAdmin()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_BAN_USER'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		$userId = $this->input->get('id', 0, 'int');
		$period = $this->input->get('period', 0, 'int');

		$user = ES::user($userId);

		if (! $this->my->canBanUser($user)) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_BAN_USER'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}


		// Block the user
		$state = $user->block();

		// we need to update our own block_period column.
		if ($state && $period) {
			$userModel = ES::model('Users');
			$userModel->updateBlockInterval(array($userId), $period);
		}

		// Send notification to admin
		// Push arguments to template variables so users can use these arguments
		$params 	= array(
							'name'				=> $user->getName(),
							'avatar'			=> $user->getAvatar( SOCIAL_AVATAR_MEDIUM ),
							'profileLink'		=> JURI::root() . 'administrator/index.php?option=com_easysocial&view=users&layout=form&id=' . $user->id,
							'date'				=> FD::date()->format( JText::_('COM_EASYSOCIAL_DATE_DMY') ),
							'totalFriends'		=> $user->getTotalFriends(),
							'totalFollowers'	=> $user->getTotalFollowers()
						);


		$title = JText::sprintf('COM_EASYSOCIAL_EMAILS_ADMIN_USER_BANNED_TITLE', $user->getName());

		if ($period) {
			$title  = JText::sprintf('COM_EASYSOCIAL_EMAILS_ADMIN_USER_BANNED_FOR_X_PERIOD_TITLE', $user->getName(), $period);
		}

		// Get a list of super admins on the site.
		$usersModel = ES::model('Users');
		$admins = $usersModel->getSiteAdmins();

		if ($admins) {
			foreach ($admins as $admin) {

				// Respect admin's email settings
				if (!$admin->sendEmail) {
					continue;
				}

				$params['adminName'] = $admin->getName();

				$mailer = FD::mailer();
				$template = $mailer->getTemplate();

				$template->setRecipient($admin->getName(), $admin->email);
				$template->setTitle($title);
				$template->setTemplate('site/profile/account.blocked', $params);
				$template->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

				// Try to send out email to the admin now.
				$state = $mailer->create($template);
			}
		}

		$message = JText::_('COM_EASYSOCIAL_PROFILE_ADMINTOOL_BAN_SUCCESSFULLY');

		if ($period) {
			$message = JText::sprintf('COM_EASYSOCIAL_PROFILE_ADMINTOOL_BAN_SUCCESSFULLY_X_TIME', $period);
		}

		$this->view->setMessage($message, SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $user);
	}


	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown($task)
	{
		return true;
	}

	/**
	 * Renders the info about a user
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initInfo()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the user being viewed
		$id = $this->input->get('id', 0, 'int');
		$user = FD::user($id);

		if (empty($user) || empty($user->id) || $user->isBlock()) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_USERS_NO_SUCH_USER'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// Get the current logged in user's privacy
		$privacy = $this->my->getPrivacy();

		// @privacy: Let's test if the current viewer is allowed to view this profile.
		if ($this->my->id != $user->id && !$privacy->validate('profiles.view', $user->id, SOCIAL_TYPE_USER)) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_PRIVACY_NOT_ALLOWED'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// Get the users model
		$model = FD::model('Users');
		$steps = $model->getAbout($user);

		return $this->view->call(__FUNCTION__, $steps);
	}

	/**
	 * Retrieve additional information for a specific user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getInfo()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the user object
		$id = $this->input->get('id', 0, 'int');
		$user = FD::user($id);

		if (empty($user) || empty($user->id) || $user->isBlock()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_USERS_NO_SUCH_USER'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Get the current user's privacy object
		$privacy = $this->my->getPrivacy();

		// @privacy: Let's test if the current viewer is allowed to view this profile.
		if ($this->my->id != $user->id && !$privacy->validate('profiles.view', $user->id, SOCIAL_TYPE_USER)) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_PROFILE_PRIVACY_NOT_ALLOWED'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Load admin's languge file
		FD::language()->loadAdmin();

		// Get the step index
		$index = $this->input->get('index', 0, 'int');

		// Get the user's profile
		$profile = $user->getProfile();
		$sequence = $profile->getSequenceFromIndex($index, SOCIAL_PROFILES_VIEW_DISPLAY);

		$step = FD::table('FieldStep');
		$state = $step->load(array('uid' => $profile->id, 'type' => SOCIAL_TYPE_PROFILES, 'sequence' => $sequence, 'visible_display' => 1));

		if (!$state) {
			$this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_PROFILE_USER_NOT_EXIST', $user->getName()), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		$fields = FD::model('Fields')->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $user->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => SOCIAL_PROFILES_VIEW_DISPLAY));

		$fieldsLib = FD::fields();

		if (!empty($fields)) {
			$args = array($user);

			$fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_USER, $fields, $args);
		}

		return $this->view->call(__FUNCTION__, $fields);
	}
}
