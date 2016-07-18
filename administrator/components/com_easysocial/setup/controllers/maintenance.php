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

// Include parent library
require_once( dirname( __FILE__ ) . '/controller.php' );

class EasySocialControllerMaintenance extends EasySocialSetupController
{
	public function __construct()
	{
		// Include foundry's library, since we know that foundry is already available here.
		$this->foundry();
	}

	/**
	 * Synchronize Users on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncUsers()
	{
		// Hardcoded to sync 50 users at a time.
		$limit 		= 100;

		// Fetch first $limit items to be processed.
		$db 		= FD::db();
		$query 		= array();

		$query[]	= 'SELECT a.' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' ) . ' AS a';
		$query[]	= 'WHERE a.' . $db->nameQuote( 'id' ) . ' NOT IN( SELECT b.' . $db->nameQuote( 'user_id' ) . ' FROM ' . $db->nameQuote( '#__social_users' ) . ' AS b )';
		$query[]	= 'LIMIT 0,' . $limit;

		$db->setQuery( $query );
		$items 		= $db->loadObjectList();

		$totalItems = count( $items );

		if( !$items )
		{
			// Nothing to process here.
			$result 		= new stdClass();
			$result->state 	= 1;

			$result 	= $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_USERS_NO_UPDATES' ) , 1 , JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP_SUCCESS' ) );
			return $this->output( $result );
		}

		// Initialize all these users.
		$users 		= FD::user( $items );

		// we need to sync the user into indexer
		foreach( $users as $user )
		{
			$indexer = FD::get( 'Indexer' );

			$contentSnapshot	= array();
			$contentSnapshot[] 	= $user->getName( 'realname' );
			// $contentSnapshot[] 	= $user->email;

			$idxTemplate = $indexer->getTemplate();

			$content = implode( ' ', $contentSnapshot );
			$idxTemplate->setContent( $user->getName( 'realname' ), $content );

			$url = ''; //FRoute::_( 'index.php?option=com_easysocial&view=profile&id=' . $user->id );
			$idxTemplate->setSource($user->id, SOCIAL_INDEXER_TYPE_USERS, $user->id, $url);

			$date = FD::date();
			$idxTemplate->setLastUpdate( $date->toMySQL() );

			$indexer->index( $idxTemplate );
		}

		// Detect if there are any more records.
		$query 		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__users' ) . ' AS a';
		$query[]	= 'WHERE a.' . $db->nameQuote( 'id' ) . ' NOT IN( SELECT b.' . $db->nameQuote( 'user_id' ) . ' FROM ' . $db->nameQuote( '#__social_users' ) . ' AS b )';

		$db->setQuery( $query );
		$total 		= $db->loadResult();

		$result 	= $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_USERS_SYNCED' , $totalItems ) , 2 , JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP_SUCCESS' ) );

		return $this->output( $result );
	}

	/**
	 * Synchronize users with the default profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncProfiles()
	{
		// Hardcoded to sync 100101327 users at a time.
		$limit 		= 100;

		// Fetch first $limit items to be processed.
		$db 		= FD::db();
		$sql 		= $db->sql();

		$query 		= array();
		$query[]	= 'SELECT a.' . $db->nameQuote( 'id' ) . ', a.' . $db->nameQuote('name');
		$query[] 	= 'FROM ' . $db->nameQuote( '#__users' ) . ' AS a';
		$query[]	= 'WHERE a.' . $db->nameQuote( 'id' ) . ' NOT IN( SELECT b.' . $db->nameQuote( 'user_id' ) . ' FROM ' . $db->nameQuote( '#__social_profiles_maps' ) . ' AS b )';
		$query[]	= 'LIMIT 0,' . $limit;

		$db->setQuery( $query );
		$items 		= $db->loadObjectList();

		if( !$items )
		{
			// Nothing to process here.
			$result 		= new stdClass();
			$result->state 	= 1;

			$result 	= $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_PROFILES_NO_UPDATES' ) , 1 , JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP_SUCCESS' ) );
			$this->output( $result );
		}

		// Get the default profile id that we should use.
		$model 		= FD::model( 'Profiles' );
		$profile 	= $model->getDefaultProfile();
		$fnField 	= $model->getProfileField($profile->id, 'JOOMLA_FULLNAME');

		// Get the total users that needs to be fixed.
		$totalItems = count( $items );

		foreach( $items as $item )
		{
			// Insert a new record
			$profileMap 				= FD::table( 'ProfileMap' );

			// Get the de
			$profileMap->profile_id		= $profile->id;
			$profileMap->user_id 		= $item->id;
			$profileMap->state 			= SOCIAL_STATE_PUBLISHED;

			$profileMap->store();

			// lets atleast migrate the user name into profile field;
			// store the data in multirow format
			$names = explode(' ', $item->name);

			$fname = '';
			$lname = '';

			if (is_array($names)) {
				$fname = array_shift($names);
				// if there is still elements in array, lets implode it and set it as last name
				if ($names) {
					$lname = implode(' ', $names);
				}
			}

			$arrNames = array('first' => $fname,
							'middle' => '',
							'last' => $lname,
							'name' => $item->name
						);

			foreach ($arrNames as $key => $val) {

				$fData 				= FD::table( 'FieldData' );
				$fData->field_id	= $fnField->id;
				$fData->uid			= $item->id;
				$fData->type		= 'user';
				$fData->data		= $val;
				$fData->datakey 	= $key;
				$fData->raw			= $val;
				$fData->store();
			}

		}

		// Detect if there are any more records.
		$query 		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__users' ) . ' AS a';
		$query[]	= 'WHERE a.' . $db->nameQuote( 'id' ) . ' NOT IN( SELECT b.' . $db->nameQuote( 'user_id' ) . ' FROM ' . $db->nameQuote( '#__social_profiles_maps' ) . ' AS b )';

		$db->setQuery( $query );
		$total 		= $db->loadResult();

		$result 	= $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_PROFILES_SYNCHRONIZED_USERS' , $totalItems ) , 2 , JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP_SUCCESS' ) );

		return $this->output( $result );

	}


	public function getScripts()
	{
		$maintenance = FD::maintenance();

		// Get previous version installed
		$previous = $this->getPreviousVersion('scriptversion');

		$files = array();

		// 1.3 UPDATE: No previous version means this is a fresh installation, then we only run the installed version script.
		if (empty($previous)) {
			$files = $maintenance->getScriptFiles($this->getInstalledVersion(), '==');
		} else {
			$files = $maintenance->getScriptFiles($previous);
		}

		if (empty($files)) {
			$msg = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_NO_SCRIPTS_TO_EXECUTE');
		} else {
			$msg = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_TOTAL_FILES_TO_EXECUTE', count($files));
		}

		$result = array(
			'message' => $msg,
			'scripts' => $files
		);

		return $this->output($result);
	}

	public function runScript()
	{
		$script = JRequest::getVar('script');

		$maintenance = FD::maintenance();

		$state = $maintenance->runScript($script);

		if (!$state)
		{
			$message = $maintenance->getError();

			$result = $this->getResultObj($message, 0);
		}
		else
		{
			$title = $maintenance->getScriptTitle($script);

			$message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_EXECUTED_SCRIPT', $title);

			$result = $this->getResultObj($message, 1);
		}

		return $this->output($result);
	}

	public function updateScriptVersion()
	{
		$version	= $this->getInstalledVersion();

		// Update the version in the database to the latest now
		$config 	= FD::table( 'Config' );
		$exists		= $config->load( array( 'type' => 'scriptversion' ) );
		$config->type	= 'scriptversion';
		$config->value	= $version;

		$config->store();

		$result = $this->getResultObj(JText::_('COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_UPDATED_MAINTENANCE_VERSION'), 1, JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP_SUCCESS' ));

		// Purge all old version files
		ES::purgeOldVersionScripts();

		// Purge javascript resources
		FD::purgeJavascriptResources();

		// Remove installation temporary file
		JFile::delete(JPATH_ROOT . '/tmp/easysocial.installation');

		return $this->output($result);
	}
}
