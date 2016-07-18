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

FD::import( 'admin:/includes/model' );

/**
 * Model for registrations.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class EasySocialModelRegistration extends EasySocialModel
{
	/**
	 * Class construct happens here.
	 *
	 * @since	1.0
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct( 'registration' );
	}

	/**
	 * Rejects a user from the whole registration process
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	User's id.
	 * @return	bool	True if success, false otherwise.
	 */
	public function reject( $id )
	{
		// Load user's object.
		$user 	= FD::user( $id );

		// Try to delete the user.
		$user->delete();

		// @rule: Delete node from profile maps
		$member	= FD::table( 'ProfileMap' );
		$member->loadByUser( $user->id );
		$member->delete();

		return $state;
	}

	/*
	 * Retrieve a list of related field id's.
	 *
	 * @param	int		$fieldId	The field id.
	 * @return	Array	An array of field id's.
	 */
	public function getRelatedFieldIds( $uid , $match , $fieldId )
	{
		$db		= FD::db();

		$query	= 'SELECT c.' . $db->nameQuote( 'field_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__social_fields' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__social_fields_groups' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'group_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__social_fields_rules' ) . ' AS c '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = c.' . $db->nameQuote( 'parent_id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $fieldId ) . ' '
				. 'AND b.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $uid ) . ' '
				. 'AND a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED ) . ' '
				. 'AND c.' . $db->nameQuote( 'match_text' ) . ' = ' . $db->Quote( $match );
		$db->setQuery( $query );
		$ids	= $db->loadColumn();

		return $ids;
	}

	/**
	 * Retrives a list of custom field groups given the work flow id.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Registration' );
	 * $model->getFieldGroups( $workflowId );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param   int     The unique workflow id.
	 * @return	Array	An array of SocialTableFieldGroup table.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getFieldGroups( $workflowId )
	{
		$db		= FD::db();

		$query		= array();
		$query[]	= 'SELECT a.* FROM ' . $db->nameQuote( '#__social_fields_groups' ) . ' AS a';
		$query[]	= 'WHERE a.' . $db->nameQuote( 'workflow_id' ) . '=' . $db->Quote( $workflowId );
		$query[]	= 'AND a.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_STATE_PUBLISHED );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );

		$result		= $db->loadObjectList();

		// If there's nothing, just return false.
		if( !$result )
		{
			return $result;
		}

		$groups = array();

		foreach( $result as $row )
		{
			$group  = FD::table( 'FieldGroup' );
			$group->bind( $row );

			$groups[]   = $group;
		}
		return $groups;
	}

	/**
	 * Deprecated.
	 * Retrieves a list of fields which should be displayed during the registration process.
	 * This should not be called elsewhere apart from the registration since it uses different steps, for processes.
	 *
	 * @since	1.0
	 * @deprecated Deprecated since 1.2. Used SocialModelFields::getCustomFields instead.
	 * @access	public
	 * @param	Array	Existing values that are previously posted from $_POST.
	 * @return	Mixed	An array of group and field items as it's child items.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCustomFieldsForProfiles( $profileId )
	{
		$db     	= FD::db();
		$fields 	= array();

		$query 		= array();
		$query[]	= 'SELECT b.*, c.' . $db->nameQuote( 'element' ) . ' AS element,d.' . $db->nameQuote( 'field_id' ) . ' as smartfield';

		$query[]	= 'FROM ' . $db->nameQuote( '#__social_fields_steps' ) . ' AS a';

		// Only want fields from the steps associated to the profile.
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_fields' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'step_id' );

		// Join with apps table to obtain the element
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_apps' ) . ' AS c';
		$query[]	= 'ON c.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'app_id' );

		// Join with rules table.
		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_fields_rules' ) . ' AS d';
		$query[]	= 'ON d.' . $db->nameQuote( 'parent_id' ) . ' = b.' . $db->nameQuote( 'id' );

		// Core fields should not be dependent on the state because it can never be unpublished.
		$query[]	= 'WHERE(';
		$query[]	= 'b.' . $db->nameQuote( 'core' ) . '=' . $db->Quote( 1 );
		$query[]	= 'OR';
		$query[]	= 'b.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_STATE_PUBLISHED );
		$query[]	= ')';

		// Registration field should not select dependant fields by default unless it is selected.
		$query[]	= 'AND b.' . $db->nameQuote( 'id' ) . ' NOT IN (';
		$query[]	= 'SELECT ' . $db->nameQuote( 'field_id' ) . ' FROM ' . $db->nameQuote( '#__social_fields_rules' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'field_id' ) . ' = b.' . $db->nameQuote( 'id' );
		$query[]	= ')';

		// Make sure that the field is set to be visible during registrations.
		$query[]	= 'AND b.' . $db->nameQuote( 'visible_registration' ) . '=' . $db->Quote( 1 );
		// $query[]	= 'AND b.' . $db->nameQuote( 'core' ) . '=' . $db->Quote( 1 );

		// Make sure that only visible_registration is enabled only.


		// Make sure to load fields that are in the current step only.
		$query[]	= 'AND a.' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $profileId );
		$query[]	= 'AND a.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( SOCIAL_TYPE_PROFILES );

		// Join back the queries.
		$query 		= implode( ' ' , $query );

		// echo str_ireplace( '#__' , 'jos_' , $query );
		// exit;

		$db->setQuery( $query );

		$rows	= $db->loadObjectList();

		// If there's no fields at all, just skip this whole block.
		if( !$rows )
		{
			return false;
		}

		$fields 	= array();

		// We need to bind the fields with SocialTableField
		foreach( $rows as $row )
		{
			$field 	= FD::table( 'Field' );
			$field->bind( $row );

			// Manually push profile_id into the field
			$field->profile_id = $profileId;

			$fields[]	= $field;
		}

		return $fields;
	}


	/**
	 * Retrieves a list of core custom fields.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Registration' );
	 * $model->getCoreFields( JRequest::getInt( 'step_id' ) );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The step id.
	 * @param	array 	Some additional data.
	 * @return
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCoreFields( $stepId , $post = array() )
	{
		$db     	= FD::db();

		$query 		= array();

		$query[]	= 'SELECT a.*, b.' . $db->nameQuote( 'element' ) . ' AS ' . $db->nameQuote( 'element' ) . ', c.uid AS ' . $db->nameQuote( 'profile_id' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_fields' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_apps' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'id' ) . ' = a.' . $db->nameQuote( 'app_id' );
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_fields_steps' ) . ' AS c';
		$query[]	= 'ON c.' . $db->nameQuote( 'id' ) . ' = a.' . $db->nameQuote( 'step_id' );
		$query[]	= 'WHERE b.' . $db->nameQuote( 'core' ) . '=' . $db->Quote( 1 );


		// @rule: We already know before hand which elements are the core fields for the profile types.
		$elements   = array( $db->Quote( 'joomla_username' ) , $db->Quote( 'joomla_fullname' ) , $db->Quote( 'joomla_email' ) ,
							$db->Quote( 'joomla_password' ), $db->Quote( 'joomla_timezone' ) , $db->Quote('joomla_user_editor' ) );

		$query[]	= 'AND b.' . $db->nameQuote( 'element' ) . ' IN(' . implode( ',' , $elements ) . ')';

		// Only select from specific steps.
		$query[]	= 'AND a.' . $db->nameQuote( 'step_id' ) . '=' . $db->Quote( $stepId );

		// The fields should be ordered correctly.
		$query[]	= 'ORDER BY a.' . $db->nameQuote( 'ordering' ) . ' ASC';

		// Let's merge the queries.
		$query 		= implode( ' ' , $query );

		// @TODO: There should be some checking here to check for fields that are not added into any steps.

		$db->setQuery( $query );

		$result		= $db->loadObjectList();

		// If all the core fields have already been mapped, just ignore this.
		if( !$result )
		{
			return $result;
		}

		$fields     = array();

		foreach( $result as $row )
		{
			$field      = FD::table( 'Field' );
			$field->bind( $row );

			// Manually push in profile id
			$field->profileId = $row->profile_id;

			$fields[]   = $field;
		}

		return $fields;
	}

	/**
	 * Allows purging of expired registration data.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Registration' );
	 *
	 * // Returns boolean value.
	 * $model->purgeExpired();
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool	True or false state.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function purgeExpired()
	{
		$db 	= FD::db();

		$date 		= FD::get( 'Date' );

		$query[]	= 'DELETE FROM ' . $db->nameQuote( '#__social_registrations' );

		// @TODO: Configurable interval period
		$query[]	= 'WHERE ' . $db->nameQuote( 'created' ) . ' <= DATE_SUB( ' . $db->Quote( $date->toMySQL() ) . ' , INTERVAL 12 HOUR)';

		$db->setQuery( implode( ' ' , $query ) );
		$state 		= $db->Query();

		return $state;
	}

	/**
	 * Links a user account with an oauth client.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function linkOAuthUser( SocialOAuth $client , SocialUser $user , $pull = true , $push = true )
	{
		$accessToken	 = $client->getAccess();

		$oauth 				= FD::table( 'OAuth' );
		$oauth->uid 		= $user->id;
		$oauth->type 		= SOCIAL_TYPE_USER;
		$oauth->client		= $client->getType();
		$oauth->oauth_id	= $client->getUser();
		$oauth->token 		= $accessToken->token;
		$oauth->secret 		= $accessToken->secret;
		$oauth->expires 	= $accessToken->expires;
		$oauth->pull 		= $pull;
		$oauth->push 		= $push;

		// Store the user's meta here.
		try {
			$meta = $client->getUserMeta();
		} catch (Exception $e) {
			$app = JFactory::getApplication();

			// Use dashboard here instead of login because api error calls might come from after user have successfully logged in
			$url = FRoute::dashboard( array(), false );

			$message = (object) array(
				'message' => JText::sprintf( 'COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage() ),
				'type' => SOCIAL_MSG_ERROR
			);

			FD::info()->set( $message );

			$app->redirect( $url );
			$app->close();
		}

		$params 	= FD::registry();
		$params->bind($meta);

		// Store the permissions
		$oauth->permissions	= FD::makeJSON( $client->getPermissions() );

		// Set the params
		$oauth->params 		= $params->toString();

		// Store oauth record
		$state 	= $oauth->store();

		if( !$state )
		{
			$this->setError( $oauth->getError() );

			return false;
		}

		// Trigger fields to do necessary linking
		// Load profile type.

		// Get all published fields apps.
		$fieldsModel = FD::model('Fields');
		$fields = $fieldsModel->getCustomFields(array('profile_id' => $user->profile_id, 'state' => SOCIAL_STATE_PUBLISHED));

		// Prepare the arguments
		$args = array(&$meta, &$client, &$user);

		// Get the fields library
		$lib 		= FD::fields();

		// Get the trigger handler
		$handler	= $lib->getHandler();

		// Trigger onRegisterOAuthBeforeSave
		$errors 	= $lib->trigger('onLinkOAuthAfterSave' , SOCIAL_FIELDS_GROUP_USER, $fields, $args);


		// @TODO: Send email notification to admin that a user linked their social account with an existing account

		// @TODO: Send email notification to the account owner that they have successfully associated their social account.

		return $state;
	}

	/**
	 * Creates a user in the system for users who logged in via oauth
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Registration' );
	 * $model->createUser( $registrationTable );
	 *
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableRegistration		The registration object.
	 * @return	int		The last sequence for the profile type.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function createOAuthUser( $accessToken , $data , $client , $import = true , $sync = true )
	{
		$config 	= FD::config();

		// Registrations needs to be enabled.
		if (!$config->get('registrations.enabled')) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_REGISTRATIONS_DISABLED' ) );
			return false;
		}

		// Load profile type.
		$profile 		= FD::table( 'Profile' );
		$profile->load($data['profileId']);

		// Get all published fields apps.
		$fieldsModel 	= FD::model( 'Fields' );
		$fields 		= $fieldsModel->getCustomFields(array('profile_id' => $profile->id, 'state' => SOCIAL_STATE_PUBLISHED));
		$args       	= array(&$data, &$client);

		// Perform field validations here. Validation should only trigger apps that are loaded on the form

		// Get the fields library
		$lib 		= FD::fields();

		// Get the trigger handler
		$handler	= $lib->getHandler();

		// Trigger onRegisterOAuthBeforeSave
		$errors 	= $lib->trigger( 'onRegisterOAuthBeforeSave' , SOCIAL_FIELDS_GROUP_USER , $fields , $args, array( $handler, 'beforeSave' ) );

		// Get a list of user groups this profile is assigned to
		$json 		= FD::json();
		$groups 	= $json->decode( $profile->gid );

		// Need to bind the groups under the `gid` column from Joomla.
		$data[ 'gid' ]  = $groups;

		// Bind the posted data for the user.
		$user 	= FD::user();
		$user->bind( $data , SOCIAL_POSTED_DATA );

		// Detect the profile type's registration type.
		$type 	= $profile->getRegistrationType(false, true);

		// We need to generate an activation code for the user.
		if ($type == 'verify') {
			$user->activation 	= FD::getHash( JUserHelper::genRandomPassword() );
		}

		// If the registration type requires approval or requires verification, the user account need to be blocked first.
		if( $type == 'approvals' || $type == 'verify')
		{
			$user->block 	= 1;
		}

		// Get registration type and set the user's state accordingly.
		$user->set( 'state' , constant( 'SOCIAL_REGISTER_' . strtoupper( $type ) ) );

		// Set the account type.
		$user->set( 'type'	, $client->getType() );

		// Let's try to save the user now.
		$state 		= $user->save();

		// If there's a problem saving the user object, set error message.
		if( !$state )
		{
			$this->setError( $user->getError() );
			return false;
		}

		// Set the user with proper `profile_id`
		$user->profile_id 	= $profile->id;

		// Once the user is saved successfully, add them into the profile mapping.
		$profile->addUser( $user->id );

		// Assign user object back into the data.
		$data[ 'user' ]   = $user;

		// Bind custom fields for this user.
		if ($import) {
			$user->bindCustomFields($data);
		}

		// Allow field applications to manipulate custom fields data
		$args       = array( &$data , &$client , &$user );

		// Assign users into the EasySocial groups
		$defaultGroups = $profile->getDefaultGroups();

		if ($defaultGroups) {
			foreach ($defaultGroups as $group) {
				$group->createMember($user->id, true);
			}
		}		

		// Allow fields app to make necessary changes if necessary. At this point, we wouldn't want to allow
		// the field to stop the registration process already.
		// @trigger onRegisterAfterSave
		$lib->trigger( 'onRegisterOAuthAfterSave' , SOCIAL_FIELDS_GROUP_USER , $fields , $args );

		// Create a new oauth record on the `#__social_oauth` table so we can simulate the user.
		$oauth 				= FD::table( 'OAuth' );
		$oauth->uid 		= $user->id;
		$oauth->type 		= SOCIAL_TYPE_USER;
		$oauth->client		= $client->getType();
		$oauth->oauth_id	= $data[ 'oauth_id' ];
		$oauth->token 		= $accessToken->token;
		$oauth->secret 		= $accessToken->secret;
		$oauth->expires 	= $accessToken->expires;
		$oauth->pull 		= $sync;
		$oauth->push 		= $sync;

		// Store oauth record
		$oauth->store();

		// @TODO: Send notification email to admin

		// @OTOD: Send registration confirmation email to user.

		return $user;
	}

	/**
	 * Generates a username until it no longer exists on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function generateUsername( $username , $min = 1 , $max = 500 )
	{
		$postfix	= rand( $min , $max );
		$original 	= $username;

		while( $this->isUsernameExists( $username ) )
		{
			$username 	= $original . '_' . $postfix;
		}

		return $username;
	}

	/**
	 * Determines if a username exists on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isUsernameExists( $username )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__users' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'username' , $username );

		$db->setQuery( $sql );

		$exists 	= $db->loadResult() > 0;

		return $exists;
	}

	/**
	 * Determines if an email exists on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isEmailExists( $email )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__users' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'email' , $email );

		$db->setQuery( $sql );

		$exists 	= $db->loadResult() > 0;

		return $exists;
	}

	/**
	 * Creates a user in the system given it's registration data.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Registration' );
	 * $model->createUser( $registrationTable );
	 *
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableRegistration		The registration object.
	 * @return	int		The last sequence for the profile type.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function createUser( SocialTableRegistration &$registration )
	{
		$config = FD::config();

		// Registrations needs to be enabled.
		if (!$config->get('registrations.enabled')) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_REGISTRATIONS_DISABLED' ) );
			return false;
		}

		// Create a user object first
		$user = FD::user();

		// Load up the values which the user inputs
		$param = FD::get('Registry');

		// Bind the JSON values.
		$param->bind($registration->values);

		// Convert the data into an array of result.
		$data = $param->toArray();

		$fieldsModel = FD::model('fields');

		// Get all published fields
		$fields = $fieldsModel->getCustomFields(array('profile_id' => $registration->profile_id, 'visible' => 'registration'));

		// Pass in data and new user object by reference for fields to manipulate
		$args = array(&$data, &$user);

		// Perform field validations here. Validation should only trigger apps that are loaded on the form
		// @trigger onRegisterBeforeSave
		$lib = FD::getInstance('Fields');

		// Set the user's profile id
		$user->profile_id = $registration->profile_id;

		// Get the trigger handler
		$handler = $lib->getHandler();

		// Trigger onRegisterBeforeSave
		$errors = $lib->trigger('onRegisterBeforeSave', SOCIAL_FIELDS_GROUP_USER, $fields, $args, array($handler, 'beforeSave'));

		// We need to know the password of the user because they might need to login after registrations.
		$data['password_clear'] = $data['password'];

		// If there are any errors, throw them on screen.
		if (is_array($errors) && in_array(false, $errors, true)) {
			$this->setError($errors);
			return $user;
		}

		// Load profile type.
		$profile = FD::table('Profile');
		$profile->load($registration->profile_id);

		// Get a list of user groups this profile is assigned to
		$groups = json_decode($profile->gid);

		// Need to bind the groups under the `gid` column from Joomla.
		$data['gid'] = $groups;

		// Bind the posted data for the user.
		$user->bind($data, SOCIAL_POSTED_DATA);

		// Detect the profile type's registration type.
		$type = $profile->getRegistrationType();

		// We need to generate an activation code for the user.
		if ($type == 'verify') {
			$user->activation = FD::getHash(JUserHelper::genRandomPassword());
		}

		// If the registration type requires approval or requires verification, the user account need to be blocked first.
		if ($type == 'approvals' || $type == 'verify') {
			$user->block = 1;
		}

		// Get registration type and set the user's state accordingly.
		$user->set('state', constant('SOCIAL_REGISTER_' . strtoupper($type)));

		// Let's try to save the user now.
		$state = $user->save();

		// If there's a problem saving the user object, set error message.
		// Added another check because $user->save() triggers Joomla's user plugin that although sometimes throws an error, the user actually got created anyway
		if (!$state && empty($user->id)) {
			$this->setError($user->getError());
			return $user;
		}

		// Set the user with proper `profile_id`
		$user->profile_id 	= $profile->id;

		// Once the user is saved successfully, add them into the profile mapping.
		$profile->addUser($user->id);

		// Allow field applications to manipulate custom fields data
		$args = array(&$data, &$user);

		// Assign users into the EasySocial groups
		$defaultGroups = $profile->getDefaultGroups();

		if ($defaultGroups) {
			foreach ($defaultGroups as $group) {
				$group->createMember($user->id, true, $type);
			}
		}

		// Allow fields app to make necessary changes if necessary. At this point, we wouldn't want to allow
		// the field to stop the registration process already.
		// @trigger onRegisterAfterSave
		$lib->trigger('onRegisterAfterSave', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		// Bind custom fields for this user.
		$user->bindCustomFields($data);

		// Reform the args with the binded custom field data in the user object
		$args = array(&$data, &$user);

		// @trigger onRegisterAfterSaveFields
		$lib->trigger('onRegisterAfterSaveFields' , SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		// We need to set the "data" back to the registration table
		$newData = FD::json()->encode($data);
		$registration->values = $newData;

		// Need to create all the privacy records for each field
		$privacyModel = FD::model('Privacy');
		$privacyModel->createFieldPrivacyItemsForUser($user->id);
		$privacyModel->createFieldPrivacyMapsForUser($user->id);

		return $user;
	}

	/**
	 * Notify users and administrator when they create an account on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser			The user object.
	 * @param	SocialTableProfile	The profile type.
	 * @return	bool				True if success, false otherwise.
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function notifyAdmins($data, SocialUser $user, SocialTableProfile $profile, $oauth = false)
	{
		// Get the application data.
		$jConfig = FD::jConfig();

		// Generate a key for the admin's actions.
		$key = md5($user->password . $user->email . $user->name . $user->username);

		$config = FD::config();

		if ($config->get('registrations.emailasusername')) {
			$data['username']	= $user->email;
		}

		// Push arguments to template variables so users can use these arguments
		$params 	= array(
								'site'			=> $jConfig->getValue( 'sitename' ),
								'username'		=> $data['username'],
								'password'		=> $data['password'],
								'firstName'		=> !empty( $data[ 'first_name' ] ) ? $data[ 'first_name' ] : '',
								'middleName'	=> !empty( $data[ 'middle_name' ] ) ? $data[ 'middle_name' ] : '',
								'lastName'		=> !empty( $data[ 'last_name' ] ) ? $data[ 'last_name' ] : '',
								'name'			=> $user->getName(),
								'avatar'		=> $user->getAvatar(SOCIAL_AVATAR_LARGE),
								'profileLink'	=> $user->getPermalink( true, true ),
								'email'			=> $user->email,
								'activation'	=> FRoute::controller( 'registration' , array( 'external' => true , 'task' => 'activate' , 'activation' => $user->activation ) ),
								'reject'		=> FRoute::controller( 'registration' , array( 'external' => true , 'task' => 'rejectUser' , 'id' => $user->id , 'key' => $key ) ),
								'approve'		=> FRoute::controller( 'registration' , array( 'external' => true , 'task' => 'approveUser' , 'id' => $user->id , 'key' => $key ) ),
								'manageAlerts'	=> false,
								'profileType'	=> $profile->get( 'title' )
						);


		// Get the email title.
		$title = $profile->getModeratorEmailTitle($user->username);

		// Get the email format.
		$format = $profile->getEmailFormat();

		// Get a list of super admins on the site.
		$usersModel = FD::model('Users');

		$admins = $usersModel->getSiteAdmins();

		foreach ($admins as $admin) {

			if (!$admin->sendEmail) {
				continue;
			}

			// Immediately send out emails
			$mailer = FD::mailer();

			// Set the admin's name.
			$params['adminName'] = $admin->getName();

			// Get the email template.
			$mailTemplate = $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient($admin->getName(), $admin->email);

			// Set title
			$mailTemplate->setTitle($title);

			// Set the template
			$template = $profile->getModeratorEmailTemplate('', $oauth);

			$mailTemplate->setTemplate($template, $params, $format);

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

			// Try to send out email to the admin now.
			$state = $mailer->create($mailTemplate);
		}

		return true;
	}

	/**
	 * Notify users and administrator when they create an account on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser			The user object.
	 * @param	SocialTableProfile	The profile type.
	 * @return	bool				True if success, false otherwise.
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function notify($data, SocialUser $user, SocialTableProfile $profile, $oauth = false)
	{
		// Get the application data.
		$jConfig = FD::jConfig();

		$config = FD::config();

		if ($config->get('registrations.emailasusername')) {
			$data['username']	= $user->email;
		}

		// Push arguments to template variables so users can use these arguments
		$params 	= array(
								'site' => $jConfig->getValue( 'sitename' ),
								'username' => $data[ 'username' ],
								'password' => $user->password_clear,
								'firstName' => !empty( $data[ 'first_name' ] ) ? $data[ 'first_name' ] : '',
								'middleName' => !empty( $data[ 'middle_name' ] ) ? $data[ 'middle_name' ] : '',
								'lastName' => !empty( $data[ 'last_name' ] ) ? $data[ 'last_name' ] : '',
								'name' => $user->getName(),
								'id' => $user->id,
								'avatar' => $user->getAvatar( SOCIAL_AVATAR_LARGE ),
								'profileLink' => $user->getPermalink( true, true ),
								'email' => $user->email,
								'activation' => FRoute::registration( array( 'external' => true , 'task' => 'activate' , 'controller' => 'registration' , 'token' => $user->activation ) ),
								'token' => $user->activation,
								'manageAlerts' => false,
								'profileType' => $profile->get( 'title' )
						);

		// Get the user preferred language
		$language = $user->getParam('language', '');

		// Get the email title.
		$title = $profile->getEmailTitle('', $language);

		// Get the email format.
		$format = $profile->getEmailFormat();

		// Immediately send out emails
		$mailer = FD::mailer();

		// Get the email template.
		$mailTemplate = $mailer->getTemplate();

		// Set recipient
		$mailTemplate->setRecipient($user->name , $user->email);

		// Set title
		$mailTemplate->setTitle($title);

		// Set the contents
		$mailTemplate->setTemplate($profile->getEmailTemplate('', $oauth) , $params , $format );

		// Set the priority. We need it to be sent out immediately since this is user registrations.
		$mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

		// Set the language. We need the email to be sent out with the correct language.
		$mailTemplate->setLanguage($language);

		// Try to send out email now.
		$state = $mailer->create($mailTemplate);

		return $state;
	}

	/**
	 * Resends activation emails to the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser			The user object.
	 * @param	SocialTableProfile	The profile type.
	 * @return	bool				True if success, false otherwise.
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function resendActivation(SocialUser $user)
	{
		// Get the application data.
		$jConfig = FD::jConfig();

		$config = FD::config();


		// Push arguments to template variables so users can use these arguments
		$params 	= array(
								'site' => $jConfig->getValue('sitename'),
								'username' => $user->username,
								'password' => $user->password_clear,
								'name' => $user->getName(),
								'id' => $user->id,
								'avatar' => $user->getAvatar(SOCIAL_AVATAR_LARGE),
								'profileLink' => $user->getPermalink(true, true),
								'email' => $user->email,
								'activation' => FRoute::registration( array( 'external' => true , 'task' => 'activate' , 'controller' => 'registration' , 'token' => $user->activation ) ),
								'token' => $user->activation,
								'manageAlerts' => false
						);

		// Get the email title.
		$title = JText::_('COM_EASYSOCIAL_REGISTRATION_ACTIVATION_REMINDER');

		// Immediately send out emails
		$mailer = FD::mailer();

		// Get the email template.
		$mailTemplate = $mailer->getTemplate();

		// Set recipient
		$mailTemplate->setRecipient($user->name , $user->email);

		// Set title
		$mailTemplate->setTitle($title);

		// Set the contents
		$mailTemplate->setTemplate('site/registration/reactivate', $params);

		// Set the priority. We need it to be sent out immediately since this is user registrations.
		$mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

		// Try to send out email now.
		$state = $mailer->create($mailTemplate);

		return $state;
	}

	/**
	 * Activates user account
	 *
	 * @param   string  The activation token.
	 * @return  mixed  	False on failure, user object on success.
	 * @since   1.6
	 */
	public function activate($token)
	{
		$db		= FD::db();

		$sql	= $db->sql();

		$sql->select( '#__users' );
		$sql->column( 'id' );
		$sql->where( 'activation', $token );
		$sql->where( 'block', '1' );
		$sql->where( 'lastvisitDate', $db->getNullDate() );

		$db->setQuery( $sql );

		$id 		= (int) $db->loadResult();

		// If user id cannot be located, throw an error.
		if( !$id )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_REGISTRATION_ACTIVATION_TOKEN_NOT_FOUND' ) );
			return false;
		}

		// Activate the user.
		$user	= FD::user( $id );
		$state 	= $user->activate( $token );

		if( !$state )
		{
			$this->setError( $user->getError() );
			return false;
		}

		return $user;
	}

}
