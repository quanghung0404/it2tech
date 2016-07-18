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

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelPrivacy extends EasySocialModel
{
	private $data			= null;

	static $_privacyitems 	= array();

	public function __construct( $config = array() )
	{
		parent::__construct( 'privacy' , $config );
	}

	public function getPrivacyId( $type, $rule, $useDefault = false )
	{
		$db = FD::db();

		$query = 'select ' . $db->nameQuote( 'id' ) . ' from ' . $db->nameQuote( '#__social_privacy' );
		$query .= ' where ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type );
		$query .= ' and ' . $db->nameQuote( 'rule' ) . ' = ' . $db->Quote( $rule );

		$db->setQuery( $query );
		$result = $db->loadResult();

		if( empty( $result ) && $useDefault )
		{
			$query = 'select ' . $db->nameQuote( 'id' ) . ' from ' . $db->nameQuote( '#__social_privacy' );
			$query .= ' where ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'core' );
			$query .= ' and ' . $db->nameQuote( 'rule' ) . ' = ' . $db->Quote( 'view' );

			$db->setQuery( $query );
			$result = $db->loadResult();
		}

		return $result;
	}


	/**
	 * Updates the privacy of an object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updatePrivacy( $uid , $data, $type = SOCIAL_PRIVACY_TYPE_USER )
	{
		$db  = FD::db();
		$sql = FD::sql();

		if( count( $data ) <= 0 )
			return false;

		foreach( $data as $item )
		{
			$tbl = FD::table( 'PrivacyMap' );

			$valueInInt = '';

			if( $item->mapid )
			{
				$tbl->load( $item->mapid );
			}

			$tbl->privacy_id = $item->id;
			$tbl->uid 		 = $uid;
			$tbl->utype 	 = $type;
			$tbl->value 	 = FD::privacy()->toValue( $item->value );
			$valueInInt		 = $tbl->value;
			$state = $tbl->store();

			if( ! $state )
			{
				return $tbl->getError();
			}


			// reset sql object.
			$sql->clear();

			//clear the existing customized privacy data.
			$sql->delete( '#__social_privacy_customize' );
			$sql->where( 'uid', $tbl->id );
			$sql->where( 'utype', SOCIAL_PRIVACY_TYPE_USER );

			$db->setQuery( $sql );
			$db->query();

			// save custom users here.
			if( $tbl->value == SOCIAL_PRIVACY_CUSTOM && count( $item->custom ) > 0 )
			{
				foreach( $item->custom as $customUserId )
				{
					if( empty( $customUserId ) )
					{
						continue;
					}

					$tblCustom = FD::table( 'PrivacyCustom' );

					$tblCustom->uid 	= $tbl->id;
					$tblCustom->utype 	= SOCIAL_PRIVACY_TYPE_USER;
					$tblCustom->user_id = $customUserId;
					$tblCustom->store();

				}
			}

			// lets check if we need to reset the privacy_items or not.
			// we can do either delete or updates. delete seems more clean.
			if( isset( $item->reset ) && $item->reset && $type == SOCIAL_PRIVACY_TYPE_USER )
			{
				// delete user's  non-fields privacy item. e.g. photos, story updates and etc
				$query = 'delete from `#__social_privacy_items`';
				$query .= ' where `privacy_id` = ' . $db->Quote( $item->id );
				$query .= ' and `user_id` = ' . $db->Quote( $uid );
				$query .= ' and `type` != ' . $db->Quote( SOCIAL_TYPE_FIELD );

				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );
				$db->query();


				// now we need to update user's fields privacy.
				$updateQuery = "update `#__social_privacy_items` set `value` = " . $db->Quote($valueInInt);
				$updateQuery .= ' where `privacy_id` = ' . $db->Quote( $item->id );
				$updateQuery .= ' and `user_id` = ' . $db->Quote( $uid );
				$updateQuery .= ' and `type` = ' . $db->Quote( SOCIAL_TYPE_FIELD );
				$sql->clear();
				$sql->raw( $updateQuery );
				$db->setQuery( $sql );
				$db->query();


				// need to update stream for related privacy items.
				$query = 'select `type` from `#__social_privacy` where `id` = ' . $db->Quote( $item->id );
				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );
				$pType = $db->loadResult();


				$isPublic 	= ( $valueInInt == SOCIAL_PRIVACY_PUBLIC ) ? 1 : 0;

				$updateQuery = 'update `#__social_stream` set `ispublic` = ' . $db->Quote( $isPublic );
				$updateQuery .= ' ,`access` = ' . $db->Quote($valueInInt);
				switch( $pType )
				{
					case 'photos':
						$updateQuery .= ' where `actor_id` = ' . $db->Quote( $uid ) . ' and `context_type` = ' . $db->Quote( SOCIAL_TYPE_PHOTO ) ;
						break;
					case 'albums':
						$updateQuery .= ' where `actor_id` = ' . $db->Quote( $uid ) . ' and `context_type` = ' . $db->Quote( SOCIAL_TYPE_ALBUM ) ;
						break;
					case 'story':
						$updateQuery .= ' where `actor_id` = ' . $db->Quote( $uid ) . ' and `context_type` IN (' . $db->Quote( SOCIAL_TYPE_STORY ) . ', ' . $db->Quote( SOCIAL_TYPE_LINKS ) . ')' ;
						break;
					case 'core':
						$updateQuery .= ' where `actor_id` = ' . $db->Quote( $uid ) . ' and `context_type` NOT IN' ;
						$updateQuery .= ' ('. $db->Quote( SOCIAL_TYPE_STORY ) . ', ' . $db->Quote( SOCIAL_TYPE_LINKS ) . ', ' . $db->Quote( SOCIAL_TYPE_PHOTO ) . ', ' . $db->Quote( SOCIAL_TYPE_ALBUM ). ')' ;
						break;

					default:
						$updateQuery .= ' where `actor_id` = ' . $db->Quote( $uid ) . ' and `context_type` = ' . $db->Quote( $pType ) ;
						break;
				}

				$sql->clear();
				$sql->raw( $updateQuery );
				$db->setQuery( $sql );
				$db->query();

			}
			else if( isset( $item->reset ) && $item->reset && $type == SOCIAL_PRIVACY_TYPE_PROFILES )
			{

				$commandSQL = 'select `user_id` from `#__social_profiles_maps` where `profile_id` = ' . $db->Quote( $uid );

				// uid == profile id.
				// we need to update user's privacy setting as well for this profile.
				$updateQuery = 'update `#__social_privacy_map` set `value` = ' . $db->Quote( $valueInInt );
				$updateQuery .= ' where `privacy_id` = ' . $db->Quote( $item->id );
				$updateQuery .= ' and `uid` IN ( '. $commandSQL .' )';
				$updateQuery .= ' and `utype` = ' . $db->Quote( 'user' );

				// echo $updateQuery;
				// echo '<br>';

				$sql->clear();
				$sql->raw( $updateQuery );
				$db->setQuery( $sql );
				$db->query();


				// now lets clear the privacy for items.
				$query = 'delete from `#__social_privacy_items`';
				$query .= ' where `privacy_id` = ' . $db->Quote( $item->id );
				$query .= ' and `user_id` IN ( ' . $commandSQL . ' )';
				$query .= ' and `type` != ' . $db->Quote( SOCIAL_TYPE_FIELD );

				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );
				$db->query();


				// now we need to update user's fields privacy.
				$updateQuery = "update `#__social_privacy_items` set `value` = " . $db->Quote($valueInInt);
				$updateQuery .= ' where `privacy_id` = ' . $db->Quote( $item->id );
				$updateQuery .= ' and `user_id` IN ( ' . $commandSQL . ' )';
				$updateQuery .= ' and `type` = ' . $db->Quote( SOCIAL_TYPE_FIELD );
				$sql->clear();
				$sql->raw( $updateQuery );
				$db->setQuery( $sql );
				$db->query();

				// need to update stream for related privacy items.
				$query = 'select `type` from `#__social_privacy` where `id` = ' . $db->Quote( $item->id );
				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );
				$pType = $db->loadResult();

				$isPublic 	= ( $valueInInt == SOCIAL_PRIVACY_PUBLIC ) ? 1 : 0;

				$updateQuery = 'update `#__social_stream` set `ispublic` = ' . $db->Quote( $isPublic );
				$updateQuery .= ' ,`access` = ' . $db->Quote($valueInInt);
				switch( $pType )
				{
					case 'photos':
						$updateQuery .= ' where `actor_id` IN ( ' . $commandSQL . ' )';
						$updateQuery .= ' and `context_type` = ' . $db->Quote( SOCIAL_TYPE_PHOTO ) ;
						break;
					case 'albums':
						$updateQuery .= ' where `actor_id` IN ( ' . $commandSQL . ' )';
						$updateQuery .= ' and `context_type` = ' . $db->Quote( SOCIAL_TYPE_ALBUM ) ;
						break;
					case 'story':
						$updateQuery .= ' where `actor_id` IN ( ' . $commandSQL . ' )';
						$updateQuery .= ' and `context_type` IN (' . $db->Quote( SOCIAL_TYPE_STORY ) . ', ' . $db->Quote( SOCIAL_TYPE_LINKS ) . ')' ;
						break;
					case 'core':
						$updateQuery .= ' where `actor_id` IN ( ' . $commandSQL . ' )';
						$updateQuery .= ' and `context_type` NOT IN' ;
						$updateQuery .= ' ('. $db->Quote( SOCIAL_TYPE_STORY ) . ', ' . $db->Quote( SOCIAL_TYPE_LINKS ) . ', ' . $db->Quote( SOCIAL_TYPE_PHOTO ) . ', ' . $db->Quote( SOCIAL_TYPE_ALBUM ). ')' ;
						break;

					default:
						$updateQuery .= ' where `actor_id` IN ( ' . $commandSQL . ' )';
						$updateQuery .= ' and `context_type` = ' . $db->Quote( $pType );
						break;
				}

				$sql->clear();
				$sql->raw( $updateQuery );

				$db->setQuery( $sql );
				$db->query();

			}

		}

		// echo 'done';exit;

		return true;
	}

	public function preloadUserPrivacy($userIds)
	{
		$db = FD::db();

		$type = SOCIAL_PRIVACY_TYPE_USER;

		// Render items that are stored in the database.
		$query = 'select a.' . $db->nameQuote('type') . ', a.' . $db->nameQuote('rule') . ', b.' . $db->nameQuote('value') . ',';
		$query .= ' a.' . $db->nameQuote('id') . ', b.' . $db->nameQuote('id') . ' as ' . $db->nameQuote('mapid') . ', b.' . $db->nameQuote('uid');
		$query .= ' from ' . $db->nameQuote('#__social_privacy') . ' as a';
		$query .= '	inner join ' . $db->nameQuote('#__social_privacy_map') . ' as b on a.' . $db->nameQuote('id') . ' = b.' . $db->nameQuote('privacy_id');
		$query .= ' where b.' . $db->nameQuote('uid') . ' IN (' . implode(",", $userIds) . ')';
		$query .= ' and b.' . $db->nameQuote('utype') . ' = ' . $db->Quote($type);
		$query .= ' and a.' . $db->nameQuote('state') . ' = ' . $db->Quote(SOCIAL_STATE_PUBLISHED);
		$query .= ' order by a.' . $db->nameQuote('type');

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$items = array();

		// prefill default array.
		foreach($userIds as $uid) {
			$items[$uid] = array();
		}

		if ($results) {
			foreach($results as $item) {
				$items[$item->uid][] = $item;
			}
		}

		return $items;
	}

	/**
	 * Responsible to retrieve the data for a privacy item.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique id for the item.
	 * @param	string	The unique string for the type of the item.
	 * @param	string	The unique component name.
	 */
	public function getData( $id , $type = SOCIAL_PRIVACY_TYPE_USER )
	{
		static $_cache = array();

		$cacheIdx = $id . $type;

		if (isset($_cache[$cacheIdx])) {
			return $_cache[$cacheIdx];
		}


		$db = FD::db();

		$item = array();

		// Render default acl items from manifest file.
		$defaultItems = $this->getDefaultPrivacy($id, $type);
		$loadDB = true;

		$result = array();

		if ($type == SOCIAL_PRIVACY_TYPE_USER) {
			if (FD::cache()->exists('user.privacy.' . $id)) {
				$loadDB = false;
				$result = FD::cache()->get('user.privacy.' . $id);
			}
		}

		if ($loadDB) {
			// Render items that are stored in the database.
			$query = 'select a.' . $db->nameQuote('type') . ', a.' . $db->nameQuote('rule') . ', b.' . $db->nameQuote('value') . ',';
			$query .= ' a.' . $db->nameQuote('id') . ', b.' . $db->nameQuote('id') . ' as ' . $db->nameQuote('mapid');
			$query .= ' from ' . $db->nameQuote('#__social_privacy') . ' as a';
			$query .= '	inner join ' . $db->nameQuote('#__social_privacy_map') . ' as b on a.' . $db->nameQuote('id') . ' = b.' . $db->nameQuote('privacy_id');
			$query .= ' where b.' . $db->nameQuote('uid') . ' = ' . $db->Quote($id);
			$query .= ' and b.' . $db->nameQuote('utype') . ' = ' . $db->Quote($type);
			$query .= ' and a.' . $db->nameQuote('state') . ' = ' . $db->Quote(SOCIAL_STATE_PUBLISHED);
			$query .= ' order by a.' . $db->nameQuote('type');

			// echo $query;exit;

			$db->setQuery($query);

			$result = $db->loadObjectList();
		}

		// If there's nothing stored into the database, we just return the default values.
		if (!$result) {
			$_cache[$cacheIdx] = $defaultItems;
			return $defaultItems;
		}

		// If there's values stored in the database, map the values back.
		foreach( $result as $row )
		{
			$row->type  = strtolower( $row->type );
			$group 		= $row->type;

			$obj        = new stdClass();

			$obj->type 		= (string) $row->type;
			$obj->rule 		= (string) $row->rule;
			$obj->id 		= $row->id;
			$obj->mapid 	= $row->mapid;
			$obj->default   = $row->value;

			if( isset( $defaultItems[ $group ] ) )
			{
				$defaultGroup = $defaultItems[$group];

				foreach( $defaultGroup as $rule )
				{
					if( $rule->type == $row->type && $rule->rule == $row->rule)
					{
						$optionKeys 	= array_keys( $rule->options );
						$defaultOptions = array_fill_keys( $optionKeys, '0');

						$key 			= constant( 'SOCIAL_PRIVACY_' . $row->value );

						$defaultOptions[$key] = '1';

						$obj->options = $defaultOptions;

						break;
					}

				}
			}

			$obj->custom = '';

			//get the customized user listing if there is any
			if( $row->value == SOCIAL_PRIVACY_CUSTOM )
			{
				$obj->custom = $this->getPrivacyCustom( $row->mapid , SOCIAL_PRIVACY_TYPE_USER );
			}

			$defaultItems[ $group ][ $obj->rule ] = $obj;
		}

		$_cache[$cacheIdx] = $defaultItems;

		return $defaultItems;
	}



	public function getDefaultPrivacy( $id , $type = SOCIAL_PRIVACY_TYPE_USER )
	{
		static $_cache = array();

		$db = FD::db();
		$sql = $db->sql();

		$result = array();

		if( $type == SOCIAL_PRIVACY_TYPE_USER )
		{
			// lets try to get from user profile privacy 1st.
			$user 		= FD::user( $id );
			$profile_id = $user->get( 'profile_id' );

			$result = array();
			if (isset($_cache[$profile_id])) {

				$result =  $_cache[$profile_id];

			} else {

				$query = 'select a.`id`, a.`type`, a.`rule`, a.`options`, b.`value`';
				$query .= ' from ' . $db->nameQuote( '#__social_privacy' ) . ' as a';
				$query .= '	inner join ' . $db->nameQuote( '#__social_privacy_map' ) . ' as b on a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'privacy_id' );
				$query .= ' where b.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $profile_id );
				$query .= ' and b.' . $db->nameQuote( 'utype' ) . ' = ' . $db->Quote( SOCIAL_PRIVACY_TYPE_PROFILES );
				$query .= ' and a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );
				$query .= ' order by a.' . $db->nameQuote( 'type' );

				$sql->raw( $query );

				$db->setQuery( $sql );
				$result = $db->loadObjectList();

				$_cache[$profile_id] = $result;
			}
		}

		if( ! $result )
		{
			$query = 'select * from ' . $db->nameQuote( '#__social_privacy');
			$query .= ' where ' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );
			$query .= ' order by ' . $db->nameQuote( 'type' );
			$db->setQuery( $query );

			$result = $db->loadObjectList();
		}

		$items = array();

		foreach( $result as $item )
		{

			$obj 			= new stdClass();
			$obj->id 		= $item->id;
			$obj->mapid 	= '0';
			$obj->type 		= $item->type;
			$obj->rule 		= $item->rule;
			$obj->default 	= $item->value;
			$obj->options	= array();

			$default = FD::call( 'Privacy' , 'toKey' , $item->value );
			$options = FD::json()->decode( $item->options );

			foreach( $options->options as $key => $option )
			{
				$obj->options[ $option ] = ( $default == $option ) ? '1' : '0';
			}

			$obj->custom = null;

			$items[ $item->type ][ $item->rule ]	= $obj;
		}

		// Sort the items
		krsort($items);

		return $items;
	}

	/**
	 * Renders the default manifest file for privacies.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The component's unique element name
	 */
	public function renderManifest( $component = 'com_easysocial' )
	{
		$file 		= JPATH_ROOT . '/administrator/components/' . strtolower( $component ) . '/privacy.xml';

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		// Try to load the privacy manifest file.
		$parser 	= FD::get( 'Parser' );
		$state 		= $parser->load( $file );

		if( !$state )
		{
			$this->setError( JText::sprintf( 'Manifest file for %1s cannot be found.' , $component ) );
			return false;
		}

		$items		= array();
		$nodes		= $parser->children();

		foreach( $nodes as $node )
		{
			$obj 			= new stdClass();
			$obj->type 		= (string) $node->type;
			$obj->rule 		= (string) $node->rule;
			$obj->options	= array();

			// Item properties.
			$options 	= $node->options->xpath( 'option' );

			foreach( $options as $key => $option )
			{
				$key 	= (string) $option;

				// Determine if the value is selected.
				$obj->options[ $key ]	= is_null( $option->attributes()->selected ) ? 0 : (string) $option->attributes()->selected;
			}

			$group 		= (string) $node->type;
			$items[ $group ][ $obj->rule ]	= $obj;
		}

		// Sort the items
		krsort($items);

		return $items;
	}

	/**
	 * Responsible to add / upate user privacy on an object
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The user id.
	 * @param	int 	The unique id form the object.
	 * @param 	string 	The type of object.
	 * @param	string	The privacy value from user.
	 * @param	string	The custom user id.
	 *
	 */
	public function update($userId, $pid, $uId, $uType, $value, $custom = '' )
	{
		// lets check if this user already has the record or not.
		// if not, we will add it here.
		// if exists, we will update the record.

		$db 	= FD::db();

		// check if user selected custom but there is no userids, then we do not do anything.
		if( $value == 'custom' && empty( $custom ) )
		{
			return false;
		}

		$query = 'select `id` from `#__social_privacy_items`';
		$query .= ' where `user_id` = ' . $db->Quote( $userId );
		$query .= ' and `uid` = ' . $db->Quote( $uId );
		$query .= ' and `type` = ' . $db->Quote( $uType );

		$db->setQuery( $query );

		$result = $db->loadResult();

		$privacy 	= FD::privacy( $userId );
		$valueInInt = $privacy->toValue( $value );

		$tbl = FD::table( 'PrivacyItems' );

		if( $result )
		{
			// record exist. update here.
			$tbl->load( $result );
			$tbl->value = $valueInInt;

		}
		else
		{
			// record not found. add new here.
			$tbl->user_id 		= $userId;
			$tbl->privacy_id 	= $pid;
			$tbl->uid 			= $uId;
			$tbl->type 			= $uType;
			$tbl->value 		= $valueInInt;
		}

		if(! $tbl->store() )
		{
			return false;
		}

		//clear the existing customized privacy data.
		$sql = FD::sql();

		$sql->delete( '#__social_privacy_customize' );
		$sql->where( 'uid', $tbl->id );
		$sql->where( 'utype', SOCIAL_PRIVACY_TYPE_ITEM );

		$db->setQuery( $sql );
		$db->query();

		// if there is custom userids.
		if( $value == 'custom' && !empty( $custom ) )
		{
			$customList = explode( ',', $custom );

			for( $i = 0; $i < count( $customList ); $i++ )
			{
				$customUserId = $customList[ $i ];

				if( empty( $customUserId ) )
				{
					continue;
				}

				$tblCustom = FD::table( 'PrivacyCustom' );

				$tblCustom->uid 	= $tbl->id;
				$tblCustom->utype 	= SOCIAL_PRIVACY_TYPE_ITEM;
				$tblCustom->user_id = $customUserId;
				$tblCustom->store();

			}
		}

		// need to update the stream's ispublic flag.
		if( $uType != SOCIAL_TYPE_FIELD )
		{
			$context 	= $uType;
			$column 	= 'context_id';
			$updateId 	= $uId;
			$isPublic 	= ( $valueInInt == SOCIAL_PRIVACY_PUBLIC ) ? 1 : 0;

			$updateQuery = 'update #__social_stream set ispublic = ' . $db->Quote( $isPublic );


			switch( $context )
			{
				case SOCIAL_TYPE_ACTIVITY:
					$updateQuery .= ' where `id` = ( select `uid` from `#__social_stream_item` where `id` = ' . $db->Quote( $uId ) . ')';
					break;
				case SOCIAL_TYPE_STORY:
				case SOCIAL_TYPE_LINKS:
					$updateQuery .= ' where `id` = ' . $db->Quote( $uId );
					break;

				default:
					$updateQuery .= ' where `id` IN ( select `uid` from `#__social_stream_item` where `context_type` = ' . $db->Quote( $context ) . ' and `context_id` = ' . $db->Quote( $uId ) . ')';
					break;
			}

			$sql->clear();
			$sql->raw( $updateQuery );
			$db->setQuery( $sql );
			$db->query();
		}

		// lets trigger the onPrivacyChange event here so that apps can handle their items accordingly.
		$obj = new stdClass();
		$obj->user_id 		= $userId;
		$obj->privacy_id 	= $pid;
		$obj->uid 			= $uId;
		$obj->utype 		= $uType;
		$obj->value 		= $valueInInt;
		$obj->custom 		= $custom;

		// Get apps library.
		$apps 	= FD::getInstance( 'Apps' );

		// Try to load user apps
		$state 	= $apps->load( SOCIAL_APPS_GROUP_USER );
		if( $state )
		{
			// Only go through dispatcher when there is some apps loaded, otherwise it's pointless.
			$dispatcher		= FD::dispatcher();

			// Pass arguments by reference.
			$args 			= array( $obj );

			// @trigger: onPrepareStream for the specific context
			$result 		= $dispatcher->trigger( SOCIAL_APPS_GROUP_USER , 'onPrivacyChange' , $args , $uType );
		}

		return true;
	}
	public function getPrivacyCustom( $pItemId, $type = SOCIAL_PRIVACY_TYPE_ITEM )
	{
		$db 	= FD::db();
		$sql 	= FD::sql();

		$sql->select( '#__social_privacy_customize' );
		$sql->column( 'user_id' );
		$sql->where( 'uid', $pItemId, '=' );
		$sql->where( 'utype', $type, '=' );

		$db->setQuery( $sql );
		$result = $db->loadObjectList();

		return $result;
	}

	public function getItem($uid , $type)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "select * from `#__social_privacy_items`";
		$query .= " where `uid` = " . $db->Quote($uid);
		$query .= " and `type` = " . $db->Quote($type);
		$sql->raw($query);

		$db->setQuery($sql);
		$result = $db->loadObject();

		return $result;
	}


	/**
	 * Retrieves the privacy object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPrivacyItem( $uid , $type , $ownerId , $command )
	{
		static $cached 	= array();

		// Build the index for cached item
		$index 	= $uid . $type . $ownerId . $command;
		$key 	= $uid . '.' . $type;


		if( isset( $cached[ $index ] ) )
		{
			return $cached[ $index ];
		}

		$db 			= FD::db();
		$result 		= array();
		static $items	= array();

		if( isset( $items[ $index ] ) )
		{
			$result 	= $items[ $index ];
		}
		else
		{
			if( isset( self::$_privacyitems[ $key ] ) )
			{
				if( self::$_privacyitems[ $key ] )
				{
					$result = clone( self::$_privacyitems[ $key ] );
				}
			}
			else
			{
				if( $uid )
				{
					$query = 'select a.' . $db->nameQuote( 'id' ) . ', a.' . $db->nameQuote( 'value' ) . ' as ' . $db->nameQuote( 'default' ) . ', a.' . $db->nameQuote( 'options' ) . ', ';
					$query .= 'b.' . $db->nameQuote( 'user_id' ) . ', b.' . $db->nameQuote( 'uid' ) . ', b.' . $db->nameQuote( 'type' ) . ', b.' . $db->nameQuote( 'value' ) . ',';
					$query .= 'b.' . $db->nameQuote( 'id' ) . ' as ' . $db->nameQuote( 'pid' );
					$query .= ' from ' . $db->nameQuote( '#__social_privacy' ) . ' as a';
					$query .= '		inner join ' . $db->nameQuote( '#__social_privacy_items' ) . ' as b on a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'privacy_id' );
					$query .= ' where b.' . $db->nameQuote( 'uid') . ' = ' . $db->Quote( $uid );
					$query .= ' and b.' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type );
					$query .= ' and a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );
					if( $ownerId )
					{
						$query .= ' and b.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $ownerId );
					}

					// var_dump( $ownerId );
					// echo $query;
					// echo '<br /><br />';


					$db->setQuery( $query );
					$result = $db->loadObject();

					$items[ $index ]	= $result;
				}
			}
		}

		// If we still can't find a result, then we need to load from the default items
		if( !$result || !isset( $result->id ) )
		{
			// Retrieve the core values
			$defaultValue				= $this->getPrivacyDefaultValues( $command, $ownerId );

			$result 			= clone( $defaultValue );
			$result->uid 		= $uid;
			$result->type 		= $type;
			$result->user_id 	= $ownerId;
			$result->value 		= isset( $result->default ) ? $result->default : '';
			$result->pid  		= '0';
		}

		if( !isset( $result->options ) )
		{
			$result->options	= '';
		}

		$default = FD::call( 'Privacy' , 'toKey' , $result->value );
		$options = FD::json()->decode( $result->options );

		$result->option	= array();

		if( $options )
		{
			foreach( $options->options as $key => $option )
			{
				$result->option[ $option ] = ( $default == $option ) ? '1' : '0';
			}
		}

		// get the custom user id.
		$result->custom = array();

		if( $result->value == SOCIAL_PRIVACY_CUSTOM )
		{
			if( $result->pid )
			{
				$result->custom = $this->getPrivacyCustom( $result->pid );
			}
			else if( $result->mapid )
			{
				$result->custom = $this->getPrivacyCustom( $result->mapid, SOCIAL_PRIVACY_TYPE_USER );

			}
		}


		// This is where we define whether the privacy item is editable or not.
		$my = FD::user();

		$result->editable = false;

		if ($result->user_id && $result->user_id == $my->id) {
			$result->editable = true;
		}

		$cached[$index]	= $result;

		return $cached[$index];
	}

	/**
	 * Retrieves the default values for the privacy item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPrivacyDefaultValues( $command = null , $userId = null )
	{
		static $core 	= array();

		$command 	= !$command ? 'core.view' : $command;
		$index		= $command . $userId;

		$data 		= explode( '.' , $command );
		$isFieldCommand = ($data[0] == 'field') ? true : false;

		$element 	= array_shift( $data );
		$rule 		= implode( '.', $data);

		if( isset( $core[ $index ] ) )
		{
			return $core[ $index ];
		}

		$default 	= null;

		// If owner id is provided, try to get the owner's privacy object
		if( $userId )
		{
			$userPrivacy 	= $this->getPrivacyUserDefaultValues( $userId, $isFieldCommand );

			if( $userPrivacy )
			{
				foreach( $userPrivacy as $item )
				{
					if( $item->type == $element && $item->rule == $rule )
					{
						$default = $item;
						break;
					}
				}
			}
		}

		$systemPrivacy	= $this->getPrivacySystemDefaultValues();

		// If the default value is still null, try to search for default values from our own table
		if( !$default )
		{
			foreach( $systemPrivacy as $item )
			{
				if( $item->type == $element && $item->rule == $rule )
				{
					$default 	= $item;

					break;
				}
			}
		}

		// If we still can't find the default, then we just revert to the core.view privacy here.
		if( !$default )
		{
			foreach( $systemPrivacy as $defaultItem )
			{
				if( $defaultItem->type == 'core' && $defaultItem->rule == 'view' )
				{
					$default = $defaultItem;
					break;
				}
			}


		}

		$core[ $index ]	= $default;

		return $core[ $index ];
	}

	public function getPrivacySystemDefaultValues()
	{
		static $system	= null;

		if( $system )
		{

			return $system;
		}

		$db 		= FD::db();
		$sql 		= $db->sql();

		// Try to get the privacy from the master table
		$query 		= array();
		$query[]	= 'SELECT a.' . $db->nameQuote( 'type' ) . ', a.' . $db->nameQuote( 'rule' ) . ', a.' . $db->nameQuote( 'id' ) . ', a.' . $db->nameQuote( 'value' ) . ' AS ' . $db->nameQuote( 'default' ) . ', a.' . $db->nameQuote( 'options' );
		$query[] 	= ', 0 as ' . $db->nameQuote( 'mapid' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_privacy' ) . ' AS a';
		$query[]	= 'where a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );


		$query 		= implode( ' ' , $query );

		$sql->raw( $query );
		$db->setQuery( $sql );

		$system		= $db->loadObjectList();

		return $system;
	}

	public function getPrivacyUserDefaultValues( $userId, $isFieldCommand = false )
	{
		static $users 	= array();

		$indexKey = $userId . '-' . $isFieldCommand;

		if (isset( $users[ $indexKey ] )) {
			return $users[ $indexKey ];
		}

		$db 	= FD::db();

		$query = 'select a.'.$db->nameQuote( 'type' ) . ', a.' . $db->nameQuote( 'rule' ) . ', a.' . $db->nameQuote( 'id' ) . ', b.' . $db->nameQuote( 'value' ) . ' as ' . $db->nameQuote( 'default' ) . ', a.' . $db->nameQuote( 'options' );
		$query .= ', b.' . $db->nameQuote( 'id' ) . ' as ' . $db->nameQuote( 'mapid' );
		$query .= ' from ' . $db->nameQuote( '#__social_privacy' ) . ' as a';
		$query .= ' inner join ' . $db->nameQuote( '#__social_privacy_map' ) . ' as b';
		$query .= ' ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'privacy_id' );
		$query .= ' where b.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $userId );
		$query .= ' and b.' . $db->nameQuote( 'utype' ) . ' = ' . $db->Quote( SOCIAL_PRIVACY_TYPE_USER );
		$query .= ' and a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );
		if ($isFieldCommand) {
			$query .= ' and a.' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'field' );
		} else {
			$query .= ' and a.' . $db->nameQuote( 'type' ) . ' != ' . $db->Quote( 'field' );
		}

		$db->setQuery( $query );

		$result = $db->loadObjectList();

		if( ! $result )
		{
			$currentUser = FD::user( $userId );
			$profile_id = $currentUser->get( 'profile_id' );

			$query = 'select a.'.$db->nameQuote( 'type' ) . ', a.' . $db->nameQuote( 'rule' ) . ', a.' . $db->nameQuote( 'id' ) . ', b.' . $db->nameQuote( 'value' ) . ' as ' . $db->nameQuote( 'default' ) . ', a.' . $db->nameQuote( 'options' );
			$query .= ', 0 as ' . $db->nameQuote( 'mapid' );
			$query .= ' from ' . $db->nameQuote( '#__social_privacy' ) . ' as a';
			$query .= ' inner join ' . $db->nameQuote( '#__social_privacy_map' ) . ' as b';
			$query .= ' ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'privacy_id' );
			$query .= ' where b.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $profile_id );
			$query .= ' and b.' . $db->nameQuote( 'utype' ) . ' = ' . $db->Quote( SOCIAL_PRIVACY_TYPE_PROFILES );
			$query .= ' and a.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );

			$db->setQuery( $query );

			$result = $db->loadObjectList();
		}

		$users[ $indexKey ]	= $result;

		return $users[ $indexKey ];
	}

	/**
	 * string - privacy rule in the form of element.rule. e.g. core.view, photos.view
	 *
	 */
	public function getPrivacyItemCoreOld( $command = null, $ownerId = null , $debug = false )
	{
		static $core 			= array();
		static $userCore 		= array();
		static $defaultCore 	= array();


		$key 	= ( is_null( $command ) || empty( $command ) ) ? 'core.view' : $command;
		$skey 	= $key . $ownerId;

		$data 		= explode( '.' , $key );
		$element 	= array_shift( $data );
		$rule 		= implode( '.', $data);

		if(! isset( $core[ $skey ] ) )
		{
			$db 		 = FD::db();
			$defaultData = null;

			// lets get the default value configured by owner.
			if( $ownerId )
			{
				if( ! isset( $userCore[ $ownerId ] ) )
				{
					$query = 'select a.'.$db->nameQuote( 'type' ) . ', a.' . $db->nameQuote( 'rule' ) . ', a.' . $db->nameQuote( 'id' ) . ', b.' . $db->nameQuote( 'value' ) . ' as ' . $db->nameQuote( 'default' ) . ', a.' . $db->nameQuote( 'options' );
					$query .= ' from ' . $db->nameQuote( '#__social_privacy' ) . ' as a';
					$query .= ' inner join ' . $db->nameQuote( '#__social_privacy_map' ) . ' as b';
					$query .= ' ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'privacy_id' );
					$query .= ' where b.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $ownerId );
					$query .= ' and b.' . $db->nameQuote( 'utype' ) . ' = ' . $db->Quote( SOCIAL_PRIVACY_TYPE_USER );

					$db->setQuery( $query );
					$prules = $db->loadObjectList();

					$userCore[ $ownerId ] = $prules;
				}

				$prules = $userCore[ $ownerId ];

				if( $prules )
				{
					foreach( $prules as $item )
					{
						if( $item->type == $element && $item->rule == $rule )
						{
							$defaultData = $item;
							break;
						}
					}
				}

			}

			if( ! $defaultData )
			{
				if( ! isset( $defaultCore['default'] ) )
				{
					// lets fall back to privacy master then if stil no records found.
					$query = 'select a.'.$db->nameQuote( 'type' ) . ', a.' . $db->nameQuote( 'rule' ) . ', a.' . $db->nameQuote( 'id' ) . ', a.' . $db->nameQuote( 'value' ) . ' as ' . $db->nameQuote( 'default' ) . ', a.' . $db->nameQuote( 'options' );
					$query .= ' from ' . $db->nameQuote( '#__social_privacy' ) . ' as a';

					$db->setQuery( $query );

					$defaultCore['default'] = $db->loadObjectList();

				}

				foreach( $defaultCore['default'] as $defaultItem )
				{
					if( $defaultItem->type == $element && $defaultItem->rule == $rule )
					{
						$defaultData = $defaultItem;
						break;
					}
				}

				if( ! $defaultData )
				{
					foreach( $defaultCore['default'] as $defaultItem )
					{
						if( $defaultItem->type == 'core' && $defaultItem->rule == 'view' )
						{
							$defaultData = $defaultItem;
							break;
						}
					}

				}
			}

			$core[ $skey ] = $defaultData;
		}

		return $core[ $skey ];
	}


	/**
	 * method used in backend to list down all the privacy items.
	 *
	 */
	public function getList()
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_privacy' );

		// Determines if user wants to search for something
		$search 	= $this->getState( 'search' );

		if( $search )
		{
			$sql->where( 'type' , $search , 'LIKE' , 'OR' );
			$sql->where( 'rule' , $search , 'LIKE' , 'OR' );
			$sql->where( 'description' , $search , 'LIKE' , 'OR' );
		}

		$ordering 	= $this->getState( 'ordering' );

		if( $ordering )
		{
			$direction 	= $this->getState( 'direction' );

			$sql->order( $ordering , $direction );
		}


		$this->setTotal( $sql->getTotalSql() );

		$rows 	= parent::getData( $sql->getSql() );

		if( !$rows )
		{
			return false;
		}

		// We want to pass back a list of PointsTable object.
		$data 	= array();

		// Load the admin language file whenever there's points.
		JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );

		foreach( $rows as $row )
		{
			$privacy 	= FD::table( 'Privacy' );
			$privacy->bind( $row );

			$data[]	= $privacy;
		}

		return $data;

	}

	/**
	 * Scans through the given path and see if there are any privacy's rule files.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The paths
	 * @return
	 */
	public function scan( $path )
	{
		jimport( 'joomla.filesystem.folder' );

		$data 	= array();

		$directory 		= JPATH_ROOT . $path;
		$directories 	= JFolder::folders( $directory , '.' , true, true );

		foreach( $directories as $folder )
		{
			// just need to get one level folder.
			$files 		= JFolder::files( $folder , '.privacy$' , false , true );
			$data = array_merge($data, $files);
		}

		return $data;
	}

	/**
	 * Given a path to the file, install the privacy rules.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The path to the privacy .json file.
	 * @return	bool		True if success false otherwise.
	 */
	public function install( $path )
	{
		// Import platform's file library.
		jimport( 'joomla.filesystem.file' );

		// Read the contents
		$contents 	= JFile::read( $path );

		// If contents is empty, throw an error.
		if( empty( $contents ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PRIVACY_UNABLE_TO_READ_PRIVACY_RULE_FILE' ) );
			return false;
		}

		$json 		= FD::json();
		$data 		= $json->decode( $contents );

		if(! is_array( $data ) )
		{
			$data = array( $data );
		}


		// Let's test if there's data.
		if( empty( $data ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PRIVACY_UNABLE_TO_READ_PRIVACY_RULE_FILE' ) );
			return false;
		}

		$privLib 	= FD::privacy();
		$result 	= array();

		foreach( $data as $row )
		{
			$type 	= $row->type;
			$rules 	= $row->rules;

			if( count( $rules ) > 0 )
			{
				foreach( $rules as $rule )
				{
					$command 		= $rule->command;
					$description 	= $rule->description;
					$default 		= $rule->default;
					$options 		= $rule->options;

					$optionsArr = array();
					foreach( $options as $option )
					{
						$optionsArr[] = $option->name;
					}

					$ruleOptions 	= array( 'options' => $optionsArr );
					$optionString 	= $json->encode( $ruleOptions );

					// Load the tables
					$privacy 	= FD::table( 'Privacy' );

					// If this already exists, we need to skip this.
					$state 	= $privacy->load( array( 'type' => $type , 'rule' => $command ) );

					if( $state )
					{
						continue;
					}

					$privacy->core 			= isset( $rule->core ) && $rule->core ? true : false;
					$privacy->state 		= SOCIAL_STATE_PUBLISHED;
					$privacy->type 			= $type;
					$privacy->rule 			= $command;
					$privacy->description 	= $description;
					$privacy->value 		= $privLib->toValue( $default );
					$privacy->options 		= $optionString;

					$addState = $privacy->store();

					if ($addState) {
						// now we need to add this new privacy rule into all the profile types.
						$this->addRuleProfileTypes($privacy->id, $privacy->value);
					}

					$result[] = $type . '.' . $command;

				}
			}
		}

		return $result;
	}

	private function addRuleProfileTypes($privacyId, $default) {
		$db = FD::db();
		$sql = $db->sql();

		$query = "insert into `#__social_privacy_map` (`privacy_id`, `uid`, `utype`, `value`)";
		$query .= " select '$privacyId', `id`, 'profiles', '$default' from `#__social_profiles` where `id` not in (";
		$query .= " 	select distinct `uid` from `#__social_privacy_map` where `utype` = 'profiles' and `privacy_id` = $privacyId)";
		$sql->raw($query);

		$db->setQuery($sql);
		$db->query();

	}

	public function setStreamPrivacyItemBatch( $data )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		// _privacyitems
		$streamModel = FD::model( 'Stream' );

		$dataset = array();
		foreach( $data as $item )
		{
			$relatedData = $streamModel->getBatchRalatedItem( $item->id );

			// If there are no related data, skip this.
			if( !$relatedData )
			{
				continue;
			}

			$element 	= $item->context_type;

			$streamItem = $relatedData[0];
			$uid 		= $streamItem->context_id;

			if( $element == 'photos' && count( $relatedData ) > 1 )
			{
				if( $streamItem->target_id )
				{
					$key = $streamItem->target_id . '.albums';

					if( ! isset( self::$_privacyitems[ $key ] ) )
					{
						$dataset[ 'albums' ][] = $streamItem->target_id;
					}
				}

				foreach( $relatedData as $itemData )
				{
					$key = $itemData->context_id . '.photos';

					if( ! isset( self::$_privacyitems[ $key ] ) )
					{
						$dataset[ 'photos' ][] = $itemData->context_id;
					}
				}

				// go to next item
				continue;
			}

			if( $element == 'story' || $element == 'links' )
			{
				$uid = $streamItem->uid;
			}

			if( $element == 'badges' || $element == 'shares' )
			{
				$uid 	 = $streamItem->id;
				$element = SOCIAL_TYPE_ACTIVITY;
			}

			if( !$uid )
			{
				continue;
			}

			$key = $uid . '.' . $element;

			if( ! isset( self::$_privacyitems[ $key ] ) )
			{
				$dataset[ $element ][] = $uid;
			}
		}

		//var_dump( $dataset );

		// lets build the sql now.
		if( $dataset )
		{

			$mainSQL = '';
			foreach( $dataset as $element => $uids )
			{
				$ids = implode( ',', $uids );

				foreach( $uids as $uid )
				{
					$key = $uid . '.' . $element;
					self::$_privacyitems[ $key ] = array();
				}

				$query = 'select a.`id`, a.`value` as `default`, a.`options`, ';
				$query .= 'b.`user_id`, b.`uid`, b.`type`, b.`value`,';
				$query .= 'b.`id`  as `pid`';
				$query .= ' from `#__social_privacy` as a';
				$query .= '		inner join `#__social_privacy_items` as b on a.`id` = b.`privacy_id`';
				$query .= ' where b.uid IN (' . $ids . ')';
				$query .= ' and b.type = ' . $db->Quote( $element );

				$mainSQL .= ( empty( $mainSQL ) ) ? $query : ' UNION ' . $query;

			}

			$sql->raw( $mainSQL );
			$db->setQuery( $sql );

			$result = $db->loadObjectList();

			if( $result )
			{
				foreach( $result as $rItem )
				{
					$key = $rItem->uid . '.' . $rItem->type;
					self::$_privacyitems[ $key ] = $rItem;
				}
			}

		}

	}

	public function getAllRulesCommand()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select concat( `type`, ' . $db->Quote( '.' ) . ',  `rule` ) as `commands` from `#__social_privacy`';
		$sql->raw( $query );

		$db->setQuery( $sql );

		$result = $db->loadColumn();

		return $result;
	}

	public function getFieldValue($key, $userId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$keys = explode('.', $key);
		$pType = array_shift($keys);
		$pRule = implode('.', $keys);

		$query = "select a.`value` from `#__social_privacy_items` as a";
		$query .= "		inner join `#__social_privacy` as b on a.`privacy_id` = b.`id`";
		$query .= " where a.`user_id` = $userId and a.`type` = 'field'";
		$query .= " and b.`type` = '$pType' and b.`rule` = '$pRule'";

		$query .= " union all";

		$query .= " select a.`value` from `#__social_privacy_map` as a";
		$query .= " 	inner join `#__social_privacy` as b on a.privacy_id = b.id";
		$query .= " where a.`utype` = 'user'";
		$query .= " and a.`uid` = $userId";
		$query .= " and b.`type` = '$pType' and b.`rule` = '$pRule'";

		$query .= " union all";

		$query .= " select a.`value` from `#__social_privacy` as a where a.`type` = '$pType' and a.`rule` = '$pRule'";
		$query .= " limit 1";

		$sql->raw($query);
		$db->setQuery($sql);

		$value = $db->loadResult();

		return $value;
	}

	public function createFieldPrivacyItemsForUser($userId)
	{
		/*

		insert into jos_social_privacy_items (privacy_id, user_id, uid, type, value)
		select d.id as privacy_id, '610' as user_id, a.id as uid, d.type, d.value from jos_social_fields as a
		left join jos_social_fields_steps as b
		on b.id = a.step_id
		left join jos_social_apps as c
		on c.id = a.app_id
		left join jos_social_privacy as d
		on d.rule = c.element
		where d.type = 'field'
		and b.type = 'profiles'
		and b.uid = 1
		and a.id not in (select e.uid from jos_social_privacy_items as e where e.type = 'field' and e.user_id = '620')

		*/

		$profileId = FD::user($userId)->profile_id;

		$db = FD::db();
		$sql = $db->sql();

		$query = array();

		// this $privacyTable is the sql to retrieve the privacy value from profile if exists, and if no, then it will return from privacy master.
		$privacyTable = "select pi.`id`, pi.type, pi.rule, ifnull(pim.`value`, pi.value) as `value`";
		$privacyTable .= " from `#__social_privacy` as pi";
		$privacyTable .= " left join `#__social_privacy_map` as pim on pi.`id` = pim.`privacy_id` and pim.`utype` = 'profiles' and pim.`uid` = '$profileId'";
		$privacyTable .= " where pi.`type` = 'field'";

		$query[] = "INSERT INTO `#__social_privacy_items` (`privacy_id`, `user_id`, `uid`, `type`, `value`)";
		$query[] = "SELECT `d`.`id` AS `privacy_id`, '$userId' AS `user_id`, `a`.`id` AS `uid`, `d`.`type`, `d`.`value` FROM `#__social_fields` AS `a`";
		$query[] = "LEFT JOIN `#__social_fields_steps` AS `b`";
		$query[] = "ON `b`.`id` = `a`.`step_id`";
		$query[] = "LEFT JOIN `#__social_apps` AS `c`";
		$query[] = "ON `c`.`id` = `a`.`app_id`";
		$query[] = "LEFT JOIN (" . $privacyTable . ") AS `d`";
		$query[] = "ON `d`.`rule` = `c`.`element`";
		$query[] = "WHERE `d`.`type` = 'field'";
		$query[] = "AND `b`.`type` = 'profiles'";
		$query[] = "AND `b`.`uid` = '$profileId'";
		$query[] = "AND `a`.`id` NOT IN (SELECT `e`.`uid` FROM `#__social_privacy_items` AS `e` WHERE `e`.`type` = 'field' AND `e`.`user_id` = '$userId')";

		$sql->raw(implode(' ', $query));

		$db->setQuery($sql);

		return $db->query();
	}

	public function createFieldPrivacyMapsForUser($userId)
	{
		/*
		insert into jos_social_privacy_map (privacy_id, uid, utype, value)
		select pi.`id` as `privacy_id`, '10399' AS `uid`, 'user' AS `utype`, ifnull(pim.`value`, pi.value) as `value`
		from jos_social_privacy as pi
				left join jos_social_privacy_map as pim on pi.`id` = pim.`privacy_id` and pim.`utype` = 'profiles' and pim.`uid` = (select profile_id from jos_social_profiles_maps where user_id = 10399)
		where pi.`type` = 'field'
		AND `pi`.`id` NOT IN (SELECT `b`.`privacy_id` FROM `jos_social_privacy_map` AS `b` WHERE `b`.`uid` = '10399' AND `b`.`utype` = 'user');
		*/

		$db = FD::db();
		$sql = $db->sql();

		$query = "INSERT INTO `#__social_privacy_map` (`privacy_id`, `uid`, `utype`, `value`)";
		$query .= " select pi.`id` as `privacy_id`, '$userId' AS `uid`, 'user' AS `utype`, ifnull(pim.`value`, pi.value) as `value`";
		$query .= " from #__social_privacy as pi";
		$query .= "		left join #__social_privacy_map as pim on pi.`id` = pim.`privacy_id`";
		$query .= "						and pim.`utype` = 'profiles' and pim.`uid` = (select prm.`profile_id` from `#__social_profiles_maps` as prm where prm.`user_id` = '$userId')";
		$query .= " where pi.`type` = 'field'";
		$query .= " AND pi.`id` NOT IN (SELECT b.`privacy_id` FROM `#__social_privacy_map` AS `b` WHERE `b`.`uid` = '$userId' AND `b`.`utype` = 'user')";

		$sql->raw($query);

		$db->setQuery($sql);

		return $db->query();
	}
}
