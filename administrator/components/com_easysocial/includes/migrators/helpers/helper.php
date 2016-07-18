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

// FD::import( 'admin:/inclues/migrators/helpers/info' );
require_once( SOCIAL_LIB . '/migrators/helpers/info.php' );

/**
 * Main class for migrator helper
 *
 * @since	1.0
 * @author	Sam <sam@stackideas.com>
 */
class SocialMigratorHelper
{
	public $info  			= null;

	/**
	 * The total number of items to process each time.
	 * @var int
	 */
	public $limit 		 	= 10;

	/**
	 * The user's chosen custom mapping
	 * @var Array
	 */
	public $userMapping  	= null;

	public function __construct()
	{
		$this->info     = new SocialMigratorHelperInfo();
	}

	/**
	 * Sets the mapping for the custom field types the user has chosen
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setUserMapping( $maps )
	{
		if( !$maps )
		{
			return false;
		}

		$userMap 	= array();

		foreach( $maps as $map )
		{
			if( strpos( $map['name'], 'field_' ) !== false )
			{
				$fid = str_replace( 'field_', '', $map['name'] );
				if( $fid )
				{
					$userMap[ $fid ] = $map['value'];
				}
			}

			$this->userMapping = $userMap;
		}
	}

	public function getCountryCode( $country )
	{
		static $countries = null;

		$country = strtolower( $country );

		if( !$countries )
		{
			$file 		= JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/countries.json';
			$contents 	= JFile::read( $file );

			$json 		= FD::json();
			$data 	= $json->decode( $contents );

			foreach( $data as $key => $value )
			{
				$countries[ strtolower( $value) ] = $key;
			}
		}


		if( isset( $countries[ $country ] ) )
		{
			// return country code.
			return $countries[ $country ];
		}
		else
		{
			// just return the coountry value
			return $country;
		}

	}


	/**
	 * Retrieves the field mapping from CB -> EasySocial
	 *
	 * @since	1.2
	 * @access	public
	 * @return	Array
	 */
	public function getFieldsMap()
	{
		return $this->mapping;
	}

	/**
	 * Adds a new user -> profile mapping
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function addProfileMapping( $userId , $profileId )
	{
		$db = FD::db();
		$sql = $db->sql();

		// delete from existing profile map
		$query = 'delete from `#__social_profiles_maps` where `user_id` = ' . $db->Quote( $userId );
		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$db->query();



		$mapping 	= FD::table( 'ProfileMap' );
		$mapping->profile_id 	= $profileId;
		$mapping->user_id 		= $userId;
		$mapping->state 		= SOCIAL_STATE_PUBLISHED;

		$mapping->store();

		// Update the log
		$this->info->setInfo( JText::sprintf( 'User ID #%1$s is associated to the Profile ID #%2$s' , $userId , $profileId ) );

		return $mapping;
	}


	/**
	 * Retrieves the default profile
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultProfile()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_profiles' );
		$sql->where( 'default' , 1 );
		$sql->limit( 1 );

		$db->setQuery( $sql );

		$row 	= $db->loadObject();

		if( !$row )
		{
			// If there is no default profile, just select 1 item from the profiles list.
			$sql->clear();
			$sql->select( '#__social_profiles' );
			$sql->limit( 1 );

			$db->setQuery( $sql );
			$row 	= $db->loadObject();

			if( !$row )
			{
				return false;
			}

		}

		$profile 	= FD::table( 'Profile' );
		$profile->bind( $row );

		return $profile;
	}


	public function removeAdminSegment( $url = '' )
	{
		if( $url )
		{
			$url 	= '/' . ltrim( $url , '/' );
			$url 	= str_replace('/administrator/', '/', $url );
		}

		return $url;
	}


	public function createDefaultItems( $profileId, $type = 'default' )
	{
		// Read the default profile json file first.
		$path 		= SOCIAL_ADMIN_DEFAULTS . '/fields/profile_migrator.json';

		if( $type == 'com_comprofiler')
		{
			$path 		= SOCIAL_ADMIN_DEFAULTS . '/fields/cb_profile_migrator.json';
		}

		$contents	= JFile::read( $path );

		$json 		= FD::json();
		$defaults 	= $json->decode( $contents );

		$newStepIds = array();

		// Let's go through each of the default items.
		foreach( $defaults as $step )
		{
			// Create default step for this profile.
			$stepTable 				= FD::table( 'FieldStep' );
			$stepTable->bind( $step );

			// always set this to yes.
			// $stepTable->visible_display = 1;

			// Map the correct uid and type.
			$stepTable->uid 		= $profileId;
			$stepTable->type 		= SOCIAL_TYPE_PROFILES;

			// Try to store the default steps.
			$state 			= $stepTable->store();

			$newStepIds[] 	= $stepTable->id;

			// Now we need to create all the fields that are in the current step
			if( $step->fields && $state )
			{

				foreach( $step->fields as $field )
				{
					$appTable 		= FD::table( 'App' );
					$appTable->loadByElement( $field->element , SOCIAL_TYPE_USER , SOCIAL_APPS_TYPE_FIELDS );

					$fieldTable		= FD::table( 'Field' );


					$fieldTable->bind( $field );

					// Ensure that the main items are being JText correctly.
					$fieldTable->title 			= $field->title;
					$fieldTable->description	= $field->description;
					$fieldTable->default 		= isset( $field->default ) ? $field->default : '';

					// Set the app id.
					$fieldTable->app_id 	= $appTable->id;

					// Set the step.
					$fieldTable->step_id 	= $stepTable->id;

					// Set this to be published by default.
					$fieldTable->state 		= isset( $field->state ) ? $field->state : SOCIAL_STATE_PUBLISHED;

					// Set this to be searchable by default.
					$fieldTable->searchable = isset( $field->searchable ) ? $field->searchable : SOCIAL_STATE_PUBLISHED;

					// Set this to be searchable by default.
					$fieldTable->required = isset( $field->required ) ? $field->required : SOCIAL_STATE_PUBLISHED;

					// // Set this to be searchable by default.
					// $fieldTable->required = isset( $field->required ) ? $field->required : SOCIAL_STATE_PUBLISHED;

					// Check if the default items has a params.
					if( isset( $field->params ) )
					{
						$fieldTable->params 	= FD::json()->encode( $field->params );
					}

					// Store the field item.
					$fieldTable->store();

					// set the unique key
					$fieldTable->checkUniqueKey();
					$fieldTable->store();

				}
			}
		}

		return $newStepIds;
	}

	public function addPrivacyMap( $uid, $utype )
	{
		$db = FD::db();
		$sql = $db->sql();

		// lets add privacy for this newly create profile :)
		$query = 'insert into `#__social_privacy_map` ( `privacy_id`, `uid`, `utype`, `value` )';
		$query .= ' select id, ' . $db->Quote( $uid ) . ', ' . $db->Quote( $utype ) . ', value';
		$query .= ' from `#__social_privacy`';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$state = $db->query();

		return $state;

	}


	public function profileLastOrdering()
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'select max(`ordering`) from `#__social_profiles`';

		$sql->raw( $query );
		$db->setQuery( $sql );

		$result = $db->loadResult();

		return ( empty( $result ) ) ? 0 : $result;
	}

}
