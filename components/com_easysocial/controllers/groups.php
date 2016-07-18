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

// Import main controller
ES::import('site:/controllers/controller');

jimport('joomla.filesystem.file');

class EasySocialControllerGroups extends EasySocialController
{
	/**
	 * Selects a category
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function selectCategory()
	{
		// Only logged in users are allowed to use this.
		FD::requireLogin();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the logged in user.
		$my 	= FD::user();

		// Check if the user really has access to create groups
		if( !$my->getAccess()->allowed( 'groups.create' ) && !$my->isSiteAdmin() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS_CREATE_GROUP' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Ensure that the user did not exceed their group creation limit
		if ($my->getAccess()->intervalExceeded('groups.limit', $my->id) && !$my->isSiteAdmin()) {

			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_EXCEEDED_LIMIT'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// Get the category id from request
		$id 		= JRequest::getInt( 'category_id' , 0 );

		$category	= FD::table( 'GroupCategory' );
		$category->load( $id );

		// If there's no profile id selected, throw an error.
		if( !$id || !$category->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// @task: Let's set some info about the profile into the session.
		$session		= JFactory::getSession();
		$session->set( 'category_id' , $id , SOCIAL_SESSION_NAMESPACE );

		// @task: Try to load more information about the current registration procedure.
		$stepSession				= FD::table( 'StepSession' );
		$stepSession->load(array('session_id' => $session->getId(), 'type' => SOCIAL_TYPE_GROUP));

		if( !$stepSession->session_id )
		{
			$stepSession->session_id 	= $session->getId();
		}

		$stepSession->uid 			= $category->id;
		$stepSession->type 			= SOCIAL_TYPE_GROUP;

		// When user accesses this page, the following will be the first page
		$stepSession->set( 'step' , 1 );

		// Add the first step into the accessible list.
		$stepSession->addStepAccess( 1 );
		$stepSession->store();

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

		// Get the current group
		$id 	= JRequest::getInt('id');
		$group	= FD::group($id);

		// Only allow group admins to remove avatar
		if (!$group->isAdmin()) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__, $group);
		}

		// Try to remove the avatar from the group now
		$group->removeAvatar();

		$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_AVATAR_REMOVED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

		return $view->call(__FUNCTION__, $group);
	}

	/**
	 * Creates a new group
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function store()
	{
		// Check for request forgeries
		ES::checkToken();

		// Only logged in user is allowed to create groups
		ES::requireLogin();

		// Check if the user really has access to create groups
		$my 	= FD::user();

		// Get the current view
		$view 	= $this->getCurrentView();
		$config	= FD::config();


		if( !$my->getAccess()->allowed( 'groups.create' ) && !$my->isSiteAdmin() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS_CREATE_GROUP' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Ensure that the user did not exceed their group creation limit
		if ($my->getAccess()->intervalExceeded('groups.limit', $my->id) && !$my->isSiteAdmin()) {

			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_EXCEEDED_LIMIT'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// Get current user's info
		$session    = JFactory::getSession();

		// Get necessary info about the current registration process.
		$stepSession		= FD::table( 'StepSession' );
		$stepSession->load( $session->getId() );

		// Load the group category
		$category 	= FD::table( 'GroupCategory' );
		$category->load( $stepSession->uid );

		$sequence = $category->getSequenceFromIndex($stepSession->step, SOCIAL_GROUPS_VIEW_REGISTRATION);

		// Load the current step.
		$step 		= FD::table( 'FieldStep' );
		$step->load(array('uid' => $category->id, 'type' => SOCIAL_TYPE_CLUSTERS, 'sequence' => $sequence));

		// Merge the post values
		$registry 	= FD::get( 'Registry' );
		$registry->load( $stepSession->values );

		// Load up groups model
		$groupsModel		= FD::model( 'Groups' );

		// Get all published fields apps that are available in the current form to perform validations
		$fieldsModel 		= FD::model( 'Fields' );
		$fields				= $fieldsModel->getCustomFields( array( 'step_id' => $step->id, 'visible' => SOCIAL_GROUPS_VIEW_REGISTRATION ) );

		// Load json library.
		$json 	= FD::json();

		// Retrieve all file objects if needed
		$files 		= JRequest::get( 'FILES' );
		$post		= JRequest::get( 'POST' );
		$token      = FD::token();

		$disallow = array($token, 'option', 'cid', 'controller', 'task', 'option', 'currentStep');

		// Process $_POST vars
		foreach( $post as $key => $value )
		{
			if (!in_array($key, $disallow))
			{
				if( is_array( $value ) )
				{
					$value  = FD::json()->encode( $value );
				}
				$registry->set( $key , $value );
			}
		}

		// Convert the values into an array.
		$data		= $registry->toArray();

		$args       = array( &$data , &$stepSession );

		// Perform field validations here. Validation should only trigger apps that are loaded on the form
		// @trigger onRegisterValidate
		$fieldsLib			= FD::fields();

		// Get the trigger handler
		$handler			= $fieldsLib->getHandler();

		// Get error messages
		$errors				= $fieldsLib->trigger( 'onRegisterValidate' , SOCIAL_FIELDS_GROUP_GROUP , $fields , $args, array( $handler, 'validate' ) );

		// The values needs to be stored in a JSON notation.
		$stepSession->values   = $json->encode( $data );

		// Store registration into the temporary table.
		$stepSession->store();

		// Get the current step (before saving)
		$currentStep    = $stepSession->step;

		// Add the current step into the accessible list
		$stepSession->addStepAccess( $currentStep );

		// Bind any errors into the registration object
		$stepSession->setErrors( $errors );

		// Saving was intercepted by one of the field applications.
		if( is_array( $errors ) && count( $errors ) > 0 )
		{
			// @rule: If there are any errors on the current step, remove access to future steps to avoid any bypass
			$stepSession->removeAccess( $currentStep );

			// @rule: Reset steps to the current step
			$stepSession->step = $currentStep;
			$stepSession->store();

			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATION_SOME_ERRORS_IN_THE_REGISTRATION_FORM' ) , SOCIAL_MSG_ERROR );

			return $view->call( 'saveStep' , $stepSession , $currentStep );
		}

		// Determine if this is the last step.
		$completed      = $step->isFinalStep( SOCIAL_GROUPS_VIEW_REGISTRATION );

		// Update creation date
		$stepSession->created 	= FD::date()->toMySQL();

		// Since user has already came through this step, add the step access
		$nextStep		= $step->getNextSequence( SOCIAL_GROUPS_VIEW_REGISTRATION );

		if( $nextStep !== false )
		{
			$nextIndex = $stepSession->step + 1;
			$stepSession->addStepAccess( $nextIndex );
			$stepSession->step = $nextIndex;
		}

		// Save the temporary data.
		$stepSession->store();

		// If this is the last step, we try to save all user's data and create the necessary values.
		if( $completed )
		{
			// Create the group now.
			$group 	= $groupsModel->createGroup( $stepSession );

			// If there's no id, we know that there's some errors.
			if( !$group->id )
			{
				$errors 		= $groupsModel->getError();

				$view->setMessage( $errors , SOCIAL_MSG_ERROR );

				return $view->call( 'saveStep' , $stepSession , $currentStep );
			}

			// @points: groups.create
			// Assign points to the user when a group is created
			$points = FD::points();
			$points->assign( 'groups.create' , 'com_easysocial' , $my->id );

			// add this action into access logs.

			FD::access()->log('groups.limit', $my->id, $group->id, SOCIAL_TYPE_GROUP);

			// Get the registration data
			$sessionData 	= FD::registry( $stepSession->values );

			// Clear existing session objects once the creation is completed.
			$stepSession->delete();

			// Default message
			$message 	= JText::_( 'COM_EASYSOCIAL_GROUPS_CREATED_PENDING_APPROVAL' );

			// If the group is published, we need to perform other activities
			if( $group->state == SOCIAL_STATE_PUBLISHED )
			{
				$message 	= JText::_( 'COM_EASYSOCIAL_GROUPS_CREATED_SUCCESSFULLY' );

				// Add activity logging when a user creates a new group.
				if( $config->get( 'groups.stream.create' ) )
				{
					$stream				= FD::stream();
					$streamTemplate		= $stream->getTemplate();

					// Set the actor
					$streamTemplate->setActor( $my->id , SOCIAL_TYPE_USER );

					// Set the context
					$streamTemplate->setContext( $group->id , SOCIAL_TYPE_GROUPS );

					$streamTemplate->setVerb( 'create' );
					$streamTemplate->setSiteWide();
					$streamTemplate->setAccess( 'core.view' );
					$streamTemplate->setCluster($group->id, SOCIAL_TYPE_GROUP, $group->type );

					// Set the params to cache the group data
					$registry	= FD::registry();
					$registry->set( 'group' , $group );

					// Set the params to cache the group data
					$streamTemplate->setParams( $registry );

					// Add stream template.
					$stream->add( $streamTemplate );
				}
			}

			$view->setMessage( $message , SOCIAL_MSG_SUCCESS );

			// Render the view now
			return $view->call( 'complete' , $group );
		}

		return $view->saveStep( $stepSession , $currentIndex , $completed );
	}

	/**
	 * Allows caller to trigger the delete method
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered members allowed
		FD::requireLogin();

		// Get the group
		$id = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		if (!$group->id || !$id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Only group owner and site admins are allowed to delete the group
		if (!$this->my->isSiteAdmin() && !$group->isOwner()) {

			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Try to delete the group
		$group->delete();

		$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_GROUP_DELETED_SUCCESS'), SOCIAL_MSG_SUCCESS);
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Updates the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function update()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered members allowed
		FD::requireLogin();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the group
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );
		$my 	= FD::user();

		if( !$group->id || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Only allow user to edit if they have access
		if( !$group->isAdmin() && !$my->isSiteAdmin())
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) , SOCIAL_MSG_ERROR );

			return $view->call(__FUNCTION__, $group);
		}

		// Get post data.
		$post 	= JRequest::get( 'POST' );

		// Get all published fields apps that are available in the current form to perform validations
		$fieldsModel 	= FD::model( 'Fields' );

		// Only fetch relevant fields for this user.
		$options		= array( 'group' => SOCIAL_TYPE_GROUP , 'uid' => $group->getCategory()->id , 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP , 'visible' => SOCIAL_PROFILES_VIEW_EDIT);

		$fields			= $fieldsModel->getCustomFields( $options );

		// Load json library.
		$json 		= FD::json();

		// Initialize default registry
		$registry 	= FD::registry();

		// Get disallowed keys so we wont get wrong values.
		$disallowed = array( FD::token() , 'option' , 'task' , 'controller' );

		// Process $_POST vars
		foreach( $post as $key => $value )
		{
			if( !in_array( $key , $disallowed ) )
			{
				if( is_array( $value ) )
				{
					$value  = $json->encode( $value );
				}
				$registry->set( $key , $value );
			}
		}

		// Convert the values into an array.
		$data		= $registry->toArray();

		// Perform field validations here. Validation should only trigger apps that are loaded on the form
		// @trigger onRegisterValidate
		$fieldsLib	= FD::fields();

		// Get the general field trigger handler
		$handler = $fieldsLib->getHandler();

		// Build arguments to be passed to the field apps.
		$args 		= array( &$data , &$group );

		// Ensure that there is no errors.
		// @trigger onEditValidate
		$errors 	= $fieldsLib->trigger( 'onEditValidate' , SOCIAL_FIELDS_GROUP_GROUP , $fields , $args, array( $handler, 'validate' ) );

		// If there are errors, we should be exiting here.
		if( is_array( $errors ) && count( $errors ) > 0 )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_PROFILE_SAVE_ERRORS' ) , SOCIAL_MSG_ERROR );

			// We need to set the proper vars here so that the es-wrapper contains appropriate class
			JRequest::setVar( 'view' 	, 'groups' , 'POST' );
			JRequest::setVar( 'layout'	, 'edit' , 'POST' );

			// We need to set the data into the post again because onEditValidate might have changed the data structure
			JRequest::set( $data , 'post' );

			return $view->call( 'edit', $errors , $data );
		}

		// @trigger onEditBeforeSave
		$errors 	= $fieldsLib->trigger( 'onEditBeforeSave' , SOCIAL_FIELDS_GROUP_GROUP , $fields , $args, array( $handler, 'beforeSave' ) );

		if( is_array( $errors ) && count( $errors ) > 0 )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_ERRORS_IN_FORM' ) , SOCIAL_MSG_ERROR );

			// We need to set the proper vars here so that the es-wrapper contains appropriate class
			JRequest::setVar( 'view' 	, 'groups' );
			JRequest::setVar( 'layout'	, 'edit' );

			// We need to set the data into the post again because onEditValidate might have changed the data structure
			JRequest::set( $data, 'post' );

			return $view->call( 'edit' , $errors );
		}

		// Save the group now
		$group->save();

		// @points: groups.update
		// Add points to the user that updated the group
		$my 	= FD::user();
		$points = FD::points();
		$points->assign( 'groups.update' , 'com_easysocial' , $my->id );

		// Reconstruct args
		$args 		= array( &$data , &$group );

		// @trigger onEditAfterSave
		$fieldsLib->trigger( 'onEditAfterSave' , SOCIAL_FIELDS_GROUP_GROUP , $fields , $args );

		// Bind custom fields for the user.
		$group->bindCustomFields( $data );

		// Reconstruct args
		$args 		= array( &$data , &$group );

		// @trigger onEditAfterSaveFields
		$fieldsLib->trigger( 'onEditAfterSaveFields' , SOCIAL_FIELDS_GROUP_GROUP, $fields , $args );

		// Add stream item to notify the world that this user updated their profile.
		$group->createStream( FD::user()->id , 'update' );

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_PROFILE_UPDATED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $group );
	}

	/**
	 * Approves user to join a group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function approve()
	{
		// Get the user's id
		$userId = $this->input->get('userId', 0, 'int');

		if (!$userId) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Get the group
		$id = $this->input->get('id', 0, 'int');
		$group = ES::group($id);

		if (!$group->id || !$id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// If there's a key provided, match it with the group
		$key = $this->input->get('key', '', 'default');

		// Ensure that the current user is the admin of the group
		if (!$group->isAdmin() && $group->key != $key) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Approve the member
		$user = ES::user($userId);
		$group->approveUser($user->id);

		$this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_MEMBER_APPROVED_SUCCESS', $user->getName()), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $group);
	}

	/**
	 * Approves a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approveGroup()
	{
		// Get the group id from the request
		$id 	= JRequest::getInt('id');

		// Try to load the group object
		$group 	= FD::group($id);

		// Get the current view
		$view 	= $this->getCurrentView();

		if( !$group->id || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$group->approve();

		$view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_GROUP_PUBLISHED_SUCCESSFULLY', $group->getName()), SOCIAL_MSG_SUCCESS);

		return $view->call(__FUNCTION__);
	}

	/**
	 * Rejects a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rejectGroup()
	{
		// Get the group id from the request
		$id 	= JRequest::getInt('id');

		// Try to load the group object
		$group 	= FD::group($id);

		// Get the current view
		$view 	= $this->getCurrentView();

		if( !$group->id || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$group->reject();

		$view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_GROUP_REJECTED_SUCCESSFULLY', $group->getName()), SOCIAL_MSG_SUCCESS);

		return $view->call(__FUNCTION__);
	}

	/**
	 * Rejects user from joining a group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function reject()
	{
		// Get the current view
		$view	= $this->getCurrentView();

		// Get the group id
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );

		if( !$group->id || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Ensure that the current user is the admin of the group
		if (!$group->isAdmin() && !$this->my->isSiteAdmin()) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $group );
		}

		// Get the user id
		$userId = $this->input->get('userId', 0, 'int');
		$user = FD::user($userId);

		// Reject the member
		$group->rejectUser($user->id);
		$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_REJECTED_USER_SUCCESS'), SOCIAL_MSG_SUCCESS);


		return $view->call( __FUNCTION__ , $group );
	}

	/**
	 * Cancel user invitation from group
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function cancelInvitation()
	{
		// Get the current view
		$view = $this->getCurrentView();

		// Get the group id
		$id = JRequest::getInt('id');
		$group = FD::group($id);

		if (!$group->id || !$id) {

			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// Ensure that the current user is the admin of the group
		if (!$group->isAdmin() && !$this->my->isSiteAdmin()) {
			
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__, $group);
		}

		// Get the user id
		$userId = $this->input->get('userId', 0, 'int');
		$user = FD::user($userId);

		// Cancel member invitation
		$group->cancelInvitation($user->id);
		$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_REJECTED_USER_SUCCESS'), SOCIAL_MSG_SUCCESS);

		return $view->call(__FUNCTION__, $group);
	}	

	/**
	 * Allows user to join a group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function joinGroup()
	{
		// Check for request forgeries
		ES::checkToken();

		// Only registered members allowed
		ES::requireLogin();

		// Get the group id
		$id = $this->input->get('id', 0, 'int');
		$group = ES::group($id);

		if (!$group->id || !$id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Get the user's access as we want to limit the number of groups they can join
		$access = $this->my->getAccess();
		$total = $this->my->getTotalGroups();

		if ($access->get('groups.allow.join') && $access->exceeded('groups.join', $total)) {
			return $this->view->call('exceededJoin');
		}

		// Create a member record for the group
		$group->createMember($this->my->id);

		return $this->view->call(__FUNCTION__ , $group);
	}

	/**
	 * Allows user to withdraw application to join a group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function withdraw()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered members allowed
		FD::requireLogin();

		// Get current user
		$my 	= FD::user();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the group id
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );

		if( !$group->id || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Remove the user from the group.
		$group->deleteMember( $my->id );

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_GROUPS_WITHDRAWN_REQUEST_SUCCESS' , $group->getName() ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $group );
	}

	/**
	 * Allows admin of a group to remove member from the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function removeMember()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered members allowed
		FD::requireLogin();

		// Get current user
		$my 	= FD::user();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the group id
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );

		if( !$group->id || !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Check if the user that is deleting is an admin of the group
		if( !$group->isAdmin() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// Get the target user that needs to be removed
		$userId 	= JRequest::getInt( 'userId' );
		$user 		= FD::user( $userId );

		// Remove the user from the group.
		$group->deleteMember( $user->id );

		// Notify group member
		$group->notifyMembers('user.remove', array('userId' => $user->id));

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_GROUPS_REMOVED_USER_SUCCESS' , $user->getName() ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $group );
	}

	/**
	 * Allows user to leave a group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function leaveGroup()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered members allowed
		FD::requireLogin();

		// Get the group id
		$id = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		if (!$group->id || !$id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Ensure that this is not the group owner.
		if ($group->isOwner()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_GROUP_OWNER_NOT_ALLOWED_TO_LEAVE'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Remove the user from the group.
		$group->leave($this->my->id);

		// Notify group members
		$group->notifyMembers('leave', array('userId' => $this->my->id));

		$this->view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_LEAVE_GROUP_SUCCESS', $group->getName()), SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $group);
	}

	/**
	 * Unpublishes a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the id of the group
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::table( 'Group' );
		$group->load( $id );

		if( !$id || !$group->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Only allow super admin's to do this
		$my 	= FD::user();

		if( !$my->isSiteAdmin() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NOT_ALLOWED_TO_UNPUBLISH_GROUP' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Try to unpublish the group now
		$state 	= $group->unpublish();

		if( !$state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_ERROR_UNPUBLISHING_GROUP' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_UNPUBLISHED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Retrieves the group's stream filters.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		// Get the current view
		$view 	= $this->getCurrentView();

		$id 		= JRequest::getInt( 'id', 0 );
		$groupId 	= JRequest::getInt( 'clusterId' );
		$group 		= FD::group( $groupId );

		if( !$id && !$group->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) );
			return $view->call( __FUNCTION__ );
		}

		// Only group members are allowed to use this
		if( !$group->isMember() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) );
			return $view->call( __FUNCTION__ );
		}

		$my 	= FD::user();

		$filter 	= FD::table( 'StreamFilter' );
		$filter->load( $id );

		return $view->call( __FUNCTION__, $filter , $group->id );
	}

	/**
	 * this method is called from the dialog to quickly add new filter based on the viewing hashtag.
	 *
	 * @since	1.2
	 * @access	public
	 * @param
	 * @return
	 */
	public function addFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$my 	= FD::user();

		$view 	= $this->getCurrentView();

		$title   	= JRequest::getVar( 'title' );
		$tag   		= JRequest::getVar( 'tag' );
		$groupId   	= JRequest::getVar( 'id' );

		$filter = FD::table( 'StreamFilter' );

		$filter->title 		= $title;
		$filter->uid   		= $groupId;
		$filter->utype 		= SOCIAL_TYPE_GROUP;
		$filter->user_id 	= $my->id;

		$filter->store();

		// add hashtag into filter
		$filterItem = FD::table( 'StreamFilterItem' );

		$filterItem->filter_id 	= $filter->id;
		$filterItem->type 		= 'hashtag';
		$filterItem->content 	= $tag;

		$filterItem->store();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_SAVED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__, $filter, $groupId );
	}


	/**
	 * Stores the groups's hashtag filter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param
	 * @return
	 */
	public function saveFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$my 	= FD::user();

		$id 		= JRequest::getInt( 'id' , 0 );
		$groupId 	= JRequest::getInt( 'uid', 0 );

		$post   = JRequest::get( 'POST' );


		// Get the current view.
		$view 	= $this->getCurrentView();

		// Load the filter table
		$filter = FD::table( 'StreamFilter' );

		if(! trim( $post['title'] ) )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_GROUP_STREAM_FILTER_WARNING_TITLE_EMPTY' ) );
			return $view->call( __FUNCTION__, $filter );
		}

		if(!trim( $post['hashtag'] ) )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_GROUP_STREAM_FILTER_WARNING_HASHTAG_EMPTY' ) );
			return $view->call( __FUNCTION__, $filter );
		}

		if( $id )
		{
			$filter->load( $id );
		}

		$filter->title = $post[ 'title' ];
		$filter->uid   = $groupId;
		$filter->utype = SOCIAL_TYPE_GROUP;
		$filter->user_id = $my->id;
		$filter->store();

		// now we save the filter type and content.
		if( $post['hashtag'] )
		{
			$hashtag = trim( $post[ 'hashtag' ] );
			$hashtag = str_replace( '#', '', $hashtag);
			$hashtag = str_replace( ' ', '', $hashtag);


			$filterItem = FD::table( 'StreamFilterItem' );
			$filterItem->load( array( 'filter_id' => $filter->id, 'type' => 'hashtag') );

			$filterItem->filter_id 	= $filter->id;
			$filterItem->type 		= 'hashtag';
			$filterItem->content 	= $hashtag;

			$filterItem->store();
		}
		else
		{
			$filter->deleteItem( 'hashtag' );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUP_STREAM_FILTER_SAVED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__, $filter, $groupId );
	}

	/**
	 * Stores the groups's hashtag filter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param
	 * @return
	 */
	public function deleteFilter()
	{
		// Check for request forgeries.
		FD::checkToken();

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		$view 	= $this->getCurrentView();

		$my 	= FD::user();

		$id 		= JRequest::getInt( 'id', 0 );
		$groupId 	= JRequest::getInt( 'uid', 0 );

		if(! $id )
		{
			FD::getInstance( 'Info' )->set( JText::_( 'Invalid filter id - ' . $id ) , 'error' );
			$view->setError( JText::_( 'Invalid filter id.' ) );
			return $view->call( __FUNCTION__ );
		}


		$filter = FD::table( 'StreamFilter' );

		// make sure the user is the filter owner before we delete.
		$filter->load( array( 'id' => $id, 'uid' => $groupId, 'utype' => SOCIAL_TYPE_GROUP ) );

		if(! $filter->id )
		{
			FD::getInstance( 'Info' )->set( JText::_( 'Filter not found - ' . $id ) , 'error' );
			$view->setError( JText::_( 'Filter not found. Action aborted.' ) );
			return $view->call( __FUNCTION__ );
		}

		$filter->deleteItem();
		$filter->delete();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_DELETED' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__, $groupId );
	}


	/**
	 * Retrieves the group's stream items.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStream()
	{
		// Check for request forgeries
		FD::checkToken();

		// Load up the group
		$id = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		// Check if the group can be seen by this user
		if ($group->isClosed() && !$group->isMember() && !$my->isSiteAdmin()) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Retrieve the stream
		$stream = FD::stream();

		$stickies = $stream->getStickies(array('clusterId' => $group->id, 'clusterType' => SOCIAL_TYPE_GROUP, 'limit' => 0));
		if ($stickies) {
			$stream->stickies = $stickies;
		}

		// Determines if the user should see the story form
		if ($group->isMember() || $this->my->isSiteAdmin()) {

			$story = FD::story(SOCIAL_TYPE_GROUP);
			$story->setCluster($group->id, SOCIAL_TYPE_GROUP);
			$story->showPrivacy(false);

			$stream->story = $story;

			// Get the group params
			$params = $group->getParams();

			// Ensure that the user has permissions to see the story form
			$permissions = $params->get('stream_permissions', null);

			// If permissions has been configured before.
			if (!is_null($permissions)) {

				// If the user is not an admin, ensure that permissions has member
				if (!$group->isAdmin() && !in_array('member', $permissions)) {
					unset($stream->story);
				}

				// If the user is an admin, ensure that permissions has admin
				if ($group->isAdmin() && !in_array('admin', $permissions) && !$group->isOwner()) {
					unset($stream->story);
				}
			}
		}

		// lets get stream items for this group
		$options = array('clusterId' => $group->id, 'clusterType' => SOCIAL_TYPE_GROUP, 'nosticky' => true);



		// Determines if we should only display moderated stream items
		$options['onlyModerated'] = $this->input->get('moderation', false, 'bool');

		// Determines if we should filter stream items by specific filters
		$filterId = $this->input->get('filterId', 0, 'int');

		if ($filterId) {

			$streamFilter = FD::table('StreamFilter');
			$streamFilter->load($filterId);

			// Get a list of hashtags
			$hashtags = $streamFilter->getHashTag();
			$tags = explode(',', $hashtags);

			if ($tags) {
				$options['tag'] = $tags;
			}
		}

		// Retrieving stream items by app element
		$appElement = $this->input->get('app', '', 'word');

		if ($appElement) {
			$options['context'] = $appElement;
		}

		$stream->get($options);

		return $this->view->call(__FUNCTION__ , $stream);
	}

	/**
	 * Allows caller to retrieve groups
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups()
	{
		// Check for request forgeries
		$this->checkToken();

		// Get the current view
		$view = $this->getCurrentView();

		// Check if the caller passed us a category id.
		$categoryId = $this->input->get('categoryId', 0, 'int');

		// Load up the model
		$model = FD::model('Groups');

		// Filter
		$filter = $this->input->get('filter', '', 'cmd');

		// Sort
		$sort = $this->input->get('ordering', 'latest', 'cmd');

		// Options
		$options = array('state' => SOCIAL_CLUSTER_PUBLISHED, 'types' => $this->my->isSiteAdmin() ? 'all' : 'user');

		// Default values
		$groups = array();
		$featuredGroups	= array();

		if ($filter == 'featured') {
			// Get a list of featured groups
			$options['featured']	= true;
			$featuredGroups	= $model->getGroups( $options );
		} else {

			// Determine the pagination limit
			$limit = FD::themes()->getConfig()->get( 'groups_limit' , 20 );
			$options['limit'] = $limit;
			$options['featured'] = false;

			if ($filter == 'mine') {
				$options['uid'] = $this->my->id;
				$options['types'] = 'all';
			}

			if ($filter == 'invited') {
				$options['invited'] = FD::user()->id;
				$options['types'] = 'all';
			}

			if ($categoryId) {
				$options['category'] = $categoryId;
			}

			if ($sort) {
				$options['ordering'] = $sort;
			}

			// Get the groups
			$groups = $model->getGroups($options);
		}

		// Get the pagination
		$pagination	= $model->getPagination();

		// Now we need to retrieve featured groups
		$options['featured'] = true;
		$featuredGroups = $model->getGroups($options);

		// Define those query strings here
		$pagination->setVar('Itemid', FRoute::getItemId('groups'));
		$pagination->setVar('view', 'groups');
		$pagination->setVar('filter', $filter);
		$pagination->setVar('ordering', $sort);

		if (isset($options['category'])) {
			$groupCat = FD::table('GroupCategory');
			$groupCat->load($options['category']);
			$pagination->setVar('categoryid', $groupCat->getAlias());
		}

		return $view->call(__FUNCTION__, $groups, $pagination, $featuredGroups, $sort);
	}

	/**
	 * Allows caller to response to invitation
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function respondInvitation()
	{
		// If the user clicks on respond invitation via email, we do not want to check for tokens.
		$email = $this->input->get('email', '', 'default');

		if (!$email) {
			// Check for request forgeries
			FD::checkToken();
		}

		// Only registered users are allowed to do this
		FD::requireLogin();

		// Get the group
		$id = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		if (!$id || !$group) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Load the member
		$member	= FD::table('GroupMember');
		$member->load(array('cluster_id' => $group->id , 'uid' => $this->my->id));

		if (!$member->id) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NOT_INVITED'), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Get the response action
		$action = $this->input->get('action', '', 'word');

		// If user rejected, just delete the invitation record.
		if ($action == 'reject') {
			$member->delete();
			$message = JText::sprintf( 'COM_EASYSOCIAL_GROUPS_REJECT_RESPONSE_SUCCESS' , $group->getName() );
		}

		if ($action == 'accept') {
			$member->state = SOCIAL_GROUPS_MEMBER_PUBLISHED;
			$member->store();

			// Create stream when user accepts the invitation
			$group->createStream($this->my->id, 'join');

			// @points: groups.join
			// Add points when user joins a group
			$points = FD::points();
			$points->assign('groups.join', 'com_easysocial', $this->my->id);

			// Notify members when a new member is added
			$group->notifyMembers('join', array('userId' => $this->my->id));
			$message = JText::sprintf('COM_EASYSOCIAL_GROUPS_ACCEPT_RESPONSE_SUCCESS', $group->getName());
		}

		$this->view->setMessage($message, SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $group, $action);
	}

	/**
	 * Allows caller to invite other users to join the group.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function invite()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only registered users are allowed to do this
		FD::requireLogin();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current user
		$my 	= FD::user();

		// Get the group
		$id    = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		if (!$id || !$group) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call(__FUNCTION__);
		}

		// Determine if the user is a member of the group
		if (!$group->isMember()) {
			$view->setMessage(JText::_( 'COM_EASYSOCIAL_GROUPS_NEED_TO_BE_MEMBER_TO_INVITE' ) , SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		// Get the list of members that are invited
		$ids 	= JRequest::getVar( 'uid' );

		if (!$ids) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_ENTER_FRIENDS_NAME_TO_INVITE' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach ($ids as $id) {
			// Ensure that the user is not a member or has been invited already
			if (!$group->isMember( $id ) && !$group->isInvited($id)) {
				$group->invite($id, $my->id);
			}
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_FRIENDS_INVITED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $group );
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

		// In order to access the dashboard apps, user must be logged in.
		FD::requireLogin();

		// Get the group id
		$groupId	= JRequest::getInt( 'groupId' );

		// Try to load the group
		$group		= FD::group( $groupId );

		if( !$groupId || !$group )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$my 	= FD::user();

		if( !$group->canViewItem() && !$my->isSiteAdmin())
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the app id.
		$appId 		= JRequest::getInt( 'appId' );

		// Load application.
		$app 	= FD::table( 'App' );
		$state 	= $app->load( $appId );

		// Get the view.
		$view 	= $this->getCurrentView();

		// If application id is not valid, throw an error.
		if( !$appId || !$state )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_APPS_INVALID_APP_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $app );
		}



		return $view->call( __FUNCTION__ , $app );
	}

	/**
	 * Allows caller to set a group as a featured group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function removeFeatured()
	{
		// Check for request forgeries
		FD::checkToken();

		// Require the user to be logged in
		FD::requireLogin();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the current user
		$my		= FD::user();

		// Get the group
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );

		if( !$id || !$group->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $group );
		}

		if( !$my->isSiteAdmin() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $group );
		}

		// Set it as featured
		$group->removeFeatured();

		return $view->call( __FUNCTION__ , $group );
	}

	/**
	 * Allows caller to set a group as a featured group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function setFeatured()
	{
		// Check for request forgeries
		FD::checkToken();

		// Require the user to be logged in
		FD::requireLogin();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the current user
		$my		= FD::user();

		// Get the group
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );

		if( !$id || !$group->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $group );
		}

		if( !$my->isSiteAdmin() )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $group );
		}

		// Set it as featured
		$group->setFeatured();

		return $view->call( __FUNCTION__ , $group );
	}

	/**
	 * Make a user an admin of a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revokeAdmin()
	{
		// Check for request forgeries
		FD::checkToken();

		// Require the user to be logged in
		FD::requireLogin();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the current user
		$my		= FD::user();

		// Get the group
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );

		if (!$group->isOwner() && !$my->isSiteAdmin()) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_NO_ACCESS' ) );
			return $view->call( __FUNCTION__ );
		}

		// Get the target user
		$userId	= JRequest::getInt( 'userId' );

		$member	= FD::table( 'GroupMember' );
		$member->load( array( 'uid' => $userId , 'cluster_id' => $group->id ) );

		// Make the user as the admin
		$member->revokeAdmin();

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Make a user an admin of a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function makeAdmin()
	{
		// Check for request forgeries
		FD::checkToken();

		// Require the user to be logged in
		FD::requireLogin();

		// Get the current view
		$view = $this->getCurrentView();

		// Get the group
		$id = $this->input->get('id', 0, 'int');
		$group = FD::group($id);

		if (!$group->isOwner() && !$group->isAdmin() && !$this->my->isSiteAdmin()) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'));
			return $view->call( __FUNCTION__ );
		}

		// Get the target user
		$userId = $this->input->get('userId', 0, 'int');

		$member = FD::table('GroupMember');
		$member->load(array('uid' => $userId, 'cluster_id' => $group->id));

		// Make the user as the admin
		$member->makeAdmin();

		// Create a stream for this
		$group->createStream($userId, 'makeadmin');

		$permalink = $group->getPermalink(false, true);

		// Notify the person that they are now a group admin
		$emailOptions   = array(
			'title'     	=> 'COM_EASYSOCIAL_GROUPS_EMAILS_PROMOTED_AS_GROUP_ADMIN_SUBJECT',
			'template'  	=> 'site/group/promoted',
			'permalink' 	=> $group->getPermalink(true, true),
			'actor'     	=> $this->my->getName(),
			'actorAvatar'   => $this->my->getAvatar(SOCIAL_AVATAR_SQUARE),
			'actorLink'     => $this->my->getPermalink(true, true),
			'group'			=> $group->getName(),
			'groupLink'		=> $group->getPermalink(true, true)
		);

		$systemOptions  = array(
			'context_type' => 'groups.group.promoted',
			'url' => $group->getPermalink(false, false),
			'actor_id' => $this->my->id,
			'uid' => $group->id
		);

		// Notify the owner first
		$state = FD::notify('groups.promoted', array($userId), $emailOptions, $systemOptions);

		return $view->call(__FUNCTION__);
	}

	public function initInfo()
	{
		FD::checkToken();

		$view = $this->getCurrentView();

		$groupId = JRequest::getInt('groupId');

		$group = FD::group($groupId);

		if (empty($group) || empty($group->id)) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		$my = FD::user();

		if (!$my->isSiteAdmin() && !$group->isOpen() && !$group->isMember()) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		FD::language()->loadAdmin();

		$steps = FD::model('Steps')->getSteps($group->category_id, SOCIAL_TYPE_CLUSTERS, SOCIAL_GROUPS_VIEW_DISPLAY);

		$fieldsLib = FD::fields();

		$fieldsLib->init(array('privacy' => false));

		$fieldsModel = FD::model('Fields');

		$index = 1;

		foreach ($steps as $step) {
			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP, 'visible' => SOCIAL_GROUPS_VIEW_DISPLAY));

			if (!empty($step->fields)) {
				$args = array($group);

				$fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_GROUP, $step->fields, $args);
			}

			$step->hide = true;

			foreach ($step->fields as $field) {
				// As long as one of the field in the step has an output, then this step shouldn't be hidden
				// If step has been marked false, then no point marking it as false again
				// We don't break from the loop here because there is other checking going on
				if (!empty($field->output) && $step->hide === true ) {
					$step->hide = false;
				}
			}

			if ($index === 1) {
				$step->url = FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'info'), false);
			} else {
				$step->url = FRoute::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'info', 'infostep' => $index), false);
			}

			$step->title = $step->get('title');

			$step->active = !$step->hide && $index == 1;

			if ($step->active) {
				$theme = FD::themes();

				$theme->set('fields', $step->fields);

				$step->html = $theme->output('site/groups/item.info');
			}

			$step->index = $index;

			$index++;
		}

		return $view->call(__FUNCTION__, $steps);
	}

	public function getInfo()
	{
		FD::checkToken();

		$view = $this->getCurrentView();

		$groupId = JRequest::getInt('groupId');

		$group = FD::group($groupId);

		if (empty($group) || empty($group->id)) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		$my = FD::user();

		if (!$my->isSiteAdmin() && !$group->isOpen() && !$group->isMember()) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		FD::language()->loadAdmin();

		$index = JRequest::getInt('index');

		$category = $group->getCategory();

		$sequence = $category->getSequenceFromIndex($index, SOCIAL_GROUPS_VIEW_DISPLAY);

		$step = FD::table('FieldStep');
		$state = $step->load(array('uid' => $category->id, 'type' => SOCIAL_TYPE_CLUSTERS, 'sequence' => $sequence, 'visible_display' => 1));

		if (!$state) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		$fields = FD::model('Fields')->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP, 'visible' => SOCIAL_GROUPS_VIEW_DISPLAY));

		$fieldsLib = FD::fields();

		$fieldsLib->init(array('privacy' => false));

		if (!empty($fields)) {
			$args = array($group);

			$fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_GROUP, $fields, $args);
		}

		return $view->call(__FUNCTION__, $fields);
	}

	/**
	 * Service Hook for explorer
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function explorer()
	{
		// Check for request forgeries
		Foundry::checkToken();

		// Require the user to be logged in
		Foundry::requireLogin();

		// Get the current view
		$view		= $this->getCurrentView();

		// Get the group object
		$groupId 	= JRequest::getInt( 'uid' );
		$group 		= Foundry::group( $groupId );

		// Determine if the viewer can really view items
		if( !$group->canViewItem() )
		{
			return $view->call( __FUNCTION__ );
		}

		// Load up the explorer library
		$explorer	= Foundry::explorer( $group->id , SOCIAL_TYPE_GROUP );
		$hook		= JRequest::getCmd( 'hook' );

		$result 	= $explorer->hook( $hook );

		$exception	= Foundry::exception( 'Folder retrieval successful' , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $exception , $result );
	}

	/**
	 * Suggests a list of groups for a user.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function suggest()
	{
		// Check for request forgeries
		ES::checkToken();
		ES::requireLogin();

		// Get the search query
		$search = $this->input->get('search', '', 'word');

		// Get exclusion list
		$exclusion = $this->input->get('exclusion', array(), 'array');

		// Determines if the user is an admin
		$options = array('unpublished' => false, 'exclusion' => $exclusion);

		if ($this->my->isSiteAdmin()) {
			$options['unpublished'] = true;
		}

		// Load up the groups model
		$model = ES::model('Groups');
		$groups = $model->search($search, $options);

		return $this->view->call(__FUNCTION__, $groups);
	}


}
