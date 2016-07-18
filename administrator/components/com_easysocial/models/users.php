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

class EasySocialModelUsers extends EasySocialModel
{
	private $data = null;
	private $displayOptions = null;
	public static $loadedUsers = array();

	public function __construct( $config = array() )
	{
		$this->displayOptions = array();
		parent::__construct('users', $config);
	}

	/**
	 * Retrieves a list of countries user's are from
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUniqueCountries()
	{
		$db = FD::db();
		$sql = $db->sql();

		// $query = "select count(1) as total, x.`raw` as `country` from (";
		// $query .= " select a.`raw` from `#__social_fields_data` as a";
		// $query .= "		inner join `#__social_fields` as b on a.`field_id` = b.`id`";
		// $query .= "		inner join `#__social_apps` as c on b.`app_id` = c.`id`";
		// $query .= "		inner join `#__social_fields_steps` as d on b.`step_id` = d.`id`";
		// $query .= " where c.`element` = 'address'";
		// $query .= " and c.`group` = 'user'";
		// $query .= " and a.`datakey` = 'country'";
		// $query .= " and a.`raw` is not null";
		// $query .= " and a.`raw` != ''";
		// $query .= " union all ";
		// $query .= " select a.`raw` from `#__social_fields_data` as a";
		// $query .= "		inner join `#__social_fields` as b on a.`field_id` = b.`id`";
		// $query .= "		inner join `#__social_apps` as c on b.`app_id` = c.`id`";
		// $query .= "		inner join `#__social_fields_steps` as d on b.`step_id` = d.`id`";
		// $query .= " where c.`element` = 'country'";
		// $query .= " and c.`group` = 'user'";
		// $query .= " and a.`raw` is not null";
		// $query .= " and a.`raw` != ''";
		// $query .= ") as x";
		// $query .= " group by x.`raw`";
		// $query .= " order by count(1) desc";


		$query = "select count(1) as total, x.`raw` as `country` from (";
		$query .= " select a.`raw` from `#__social_fields_data` as a";
		$query .= "		inner join `#__social_fields` as b on a.`field_id` = b.`id`";
		$query .= "		inner join `#__social_apps` as c on b.`app_id` = c.`id`";
		$query .= "		inner join `#__social_fields_steps` as d on b.`step_id` = d.`id`";
		$query .= " where c.`element` = 'address'";
		$query .= " and c.`group` = 'user'";
		$query .= " and a.`datakey` = 'country'";
		$query .= " and a.`raw` is not null";
		$query .= " and a.`raw` != ''";
		$query .= ") as x";
		$query .= " group by x.`raw`";
		$query .= " order by count(1) desc";

		$sql->raw($query);

		$db->setQuery( $sql );

		$countries 	= $db->loadObjectList();

		return $countries;
	}

	/**
	 * Populates the state
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initStates()
	{
		$profile 	= $this->getUserStateFromRequest( 'profile' );
		$group 		= $this->getUserStateFromRequest( 'group' );
		$published	= $this->getUserStateFromRequest( 'published' , 'all' );

		$this->setState( 'published' , $published );
		$this->setState( 'group'	, $group );
		$this->setState( 'profile'	, $profile );

		parent::initStates();
	}

	/**
	 * Exports users from EasySocial with their custom fields data
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function export($profileId)
	{
		$db  = FD::db();
		$sql = $db->sql();

		$fieldsToExlude = array('header',
								'avatar',
								'cover',
								'file',
								'joomla_email',
								'joomla_username',
								'joomla_twofactor',
								'joomla_password',
								'separator');

		$header = array();
		$body = array();

		// first we need to get the the fields for a profile type.
		$fields = array();

		$query = " select a.*, c.`element`";
		$query .= " from `#__social_fields` as a";
		$query .= "	inner join `#__social_fields_steps` as b on a.`step_id` = b.`id`";
		$query .= "	inner join `#__social_apps` as c on a.`app_id` = c.`id`";
		$query .= " where b.`type` = 'profiles'";
		$query .= " and b.`uid` = " . $db->Quote($profileId);
		$query .= " and c.`type` = 'fields'";
		$query .= " and c.`group` = 'user'";
		if ($fieldsToExlude) {
			$tmp = implode( '\',\'', $fieldsToExlude);
			$query .= " and c.`element` not in ('" . $tmp . "')";
		}

		$sql->raw($query);
		$db->setQuery($sql);

		$results = $db->loadObjectList();

		if ($results) {
			foreach ($results as $item) {
				$field = FD::table('Field');
				$field->bind($item);

				$field->data = '';
				$field->profile_id = $profileId;
				$field->element = $item->element;

				$fields[$field->id] = $field;
			}
		}

		// get user data
		$data = array();
		$query = "select a.`id` as `userid`, a.`username`, a.`name`, a.`email`, b.`field_id`, b.`datakey`, b.`raw`";
		$query .= " from `#__users` as a";
		$query .= " inner join `#__social_fields_data` as b on a.`id` = b.`uid` and b.`type` = " . $db->Quote(SOCIAL_TYPE_USER);
		$query .= " inner join `#__social_profiles_maps` as c on a.`id` = c.`user_id`";
		$query .= " where c.`profile_id` = " . $db->Quote($profileId);

		$sql->clear();
		$sql->raw($query);

		$db->setQuery($sql);
		$results = $db->loadObjectList();

		if ($results) {
			foreach ($results as $row) {

				if (!isset($data[$row->userid])) {
					$data[$row->userid] = array();

					$data[$row->userid]['0']['userid'] = $row->userid;
					$data[$row->userid]['0']['username'] = $row->username;
					$data[$row->userid]['0']['email'] = $row->email;
				}

				if (array_key_exists($row->field_id, $fields)) {
					$datakey = $row->datakey ? $row->datakey : 'default';
					$data[$row->userid][$row->field_id][$datakey] = $row->raw;
				}
			}

			// lets format the data by triggering the onExport event.
			$fieldLib = FD::fields();

			foreach ($data as $userid => $fieldData) {
				$formatted = array();
				$args 	= array($fieldData, $userid);

				$formatted = $fieldLib->trigger('onExport', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

				foreach($fields as $fid => $value) {
					$data[$userid][$fid] = $formatted[$fid];
				}
			}

			// real work start here.
			foreach ($data as $userid => $fieldData) {

				if (! $header) {

					$header = array('id','username','email');

					foreach($fields as $fid => $field) {

						$headerdata = $fieldData[$fid];

						$keys = array_keys($headerdata);

						if(count($keys) == 1 && $keys[0] == 'default') {
							$title = JText::_($field->title);
							$header[] = $title;
						} else {
							foreach($keys as $key) {
								$title = JText::_($field->title) . '::' . $key;
								$header[] = $title;
							}
						}
					}
				}

				foreach($fields as $fid => $field) {

					if (! isset($body[$userid])) {
						$body[$userid][] = $fieldData['0']['userid'];
						$body[$userid][] = $fieldData['0']['username'];
						$body[$userid][] = $fieldData['0']['email'];
					}

					$itemdata = $fieldData[$fid];

					foreach($itemdata as $key => $value) {
						$body[$userid][] = $value;
					}

				}
			}

			// now we need to check if there is any users that do not have any data in fields_data.
			// if yes, we need to process these users too.

			// lets borrow the data keys from the last data elements
			$lastData = array_pop($data);

			$query = "select a.`id` as `userid`, a.`username`, a.`name`, a.`email`";
			$query .= " from `#__users` as a";
			$query .= " inner join `#__social_profiles_maps` as c on a.`id` = c.`user_id`";
			$query .= " where c.`profile_id` = " . $db->Quote($profileId);
			$query .= " and not exists (select b.`uid` from `#__social_fields_data` as b where b.`type` = 'user' and b.`uid` = a.`id`)";

			$sql->clear();
			$sql->raw($query);

			$db->setQuery($sql);
			$results = $db->loadObjectList();

			if ($results) {

				foreach ($results as $user) {

					$userid = $user->userid;

					foreach($fields as $fid => $field) {

						if (! isset($body[$userid])) {
							$body[$userid][] = $user->userid;
							$body[$userid][] = $user->username;
							$body[$userid][] = $user->email;
						}

						$data = $lastData[$fid];

						foreach($data as $key => $value) {
							$body[$userid][] = "";
						}

					}

				}
			}

			// lets add the header into the body
			array_unshift($body, $header);
		}

		return $body;
	}


	/**
	 * Retrieves the last login
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLastLogin( $userId )
	{
		$db 			= FD::db();
		$sql 			= $db->sql();

		// getting joomla session lifetime config.
		$jConfig 		= JFactory::getConfig();
		$sessionLimit 	= $jConfig->get( 'lifetime', '0' );
		$curDateTime	= FD::date()->toMySQL();

		$query = 'select `time`, UNIX_TIMESTAMP( date_add( ' . $db->Quote( $curDateTime ) . ' , INTERVAL -' . $sessionLimit . ' MINUTE ) ) as `limit`';
		$query .= ', count(1) as `count`';
		$query .= ' from `#__session` where `userid` = ' . $db->Quote( $userId );
		$query .= ' group by `userid`, `time`';
		$query .= ' order by `time` desc limit 1';

		// echo $query;exit;

		$sql->raw( $query );

		$db->setQuery( $sql );
		$lastLogin = $db->loadObject();

		return $lastLogin;
	}

	public function preloadUsers($ids)
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$query = "select * from `#__social_users`";
		$query .= " where `user_id` IN (" . implode(",", $ids) . ")";

		$sql->raw($query);
		$db->setQuery($sql);

		$results = $db->loadObjectList();

		return $results;
	}

	public function preloadIsOnline($ids)
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$query = "select `session_id`, `userid` from `#__session`";
		$query .= " where `userid` IN (" . implode(",", $ids) . ")";

		$sql->raw($query);
		$db->setQuery($sql);

		$results = $db->loadObjectList();

		$onlineUsers = array();
		if ($results) {
			foreach($results as $item) {
				$onlineUsers[$item->userid] = 1;
			}
		}

		return $onlineUsers;
	}

	/**
	 * Determines if the user exists in #__social_users
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function metaExists( $id )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		if (FD::cache()->exists('user.meta.'.$id)) {
			$value = FD::cache()->get('user.meta.'.$id);
			$exists = ($value) ? true : false;
		} else {
			$sql->select( '#__social_users' );
			$sql->column( 'COUNT(1)' , 'count' );
			$sql->where( 'user_id' , $id );

			$db->setQuery( $sql );

			$exists	= $db->loadResult() > 0 ? true : false;
		}

		return $exists;
	}

	/**
	 * Creates a new user meta
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createMeta( $id )
	{
		$db 			= FD::db();
		$obj 			= new stdClass();
		$obj->user_id 	= $id;

		// If user is created on the site but doesn't have a record, we should treat it as published.
		$obj->state  	= SOCIAL_STATE_PUBLISHED;

		return $db->insertObject( '#__social_users' , $obj );
	}

	/**
	 * Search a username given the email
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The email address
	 * @return
	 */
	public function getUsernameByEmail( $email )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__users' , 'username' );
		$sql->column( 'username' );
		$sql->where( 'email' , $email );

		$db->setQuery( $sql );

		$username 	= $db->loadResult();

		return $username;
	}

	/**
	 * Assigns user to a particular user group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id
	 * @param	int		The group's id
	 * @return
	 */
	public function assignToGroup( $id , $gid )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		// Check if the user is already assigned to this group
		$sql->select( '#__user_usergroup_map' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'group_id' , $gid );
		$sql->where( 'user_id'	, $id );

		$db->setQuery( $sql );

		$exists 	= $db->loadResult();

		if( !$exists )
		{
			$sql->clear();
			$sql->insert( '#__user_usergroup_map' );
			$sql->values( 'user_id' , $id );
			$sql->values( 'group_id' , $gid );

			$db->setQuery( $sql );
			$db->Query();
		}

	}

	/**
	 * Retrieve a user group from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getUserGroup( $id )
	{
		$db 		= FD::db();

		$sql 		= $db->sql();

		$sql->select( '#__usergroups' );
		$sql->where( 'id' , $id );

		$db->setQuery( $sql );

		$result 	= $db->loadObject();

		if( !$result )
		{
			return $result;
		}

		$sql->clear();

		$sql->select( '#__user_usergroup_map' );
		$sql->where( 'group_id' , $id );

		$db->setQuery( $sql->getTotalSql() );

		$result->total 	= $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve a list of user groups from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getUserGroups()
	{
		$db		= FD::db();

		$sql	= $db->sql();

		$sql->select( '#__usergroups', 'a' );
		$sql->column( 'a.*' );
		$sql->column( 'b.id', 'level', 'count distinct' );
		$sql->join( '#__usergroups' , 'b' );
		$sql->on( 'a.lft', 'b.lft', '>' );
		$sql->on( 'a.rgt', 'b.rgt', '<' );
		$sql->group( 'a.id' , 'a.title' , 'a.lft' , 'a.rgt' , 'a.parent_id' );
		$sql->order( 'a.lft' , 'ASC' );

		$db->setQuery( $sql );

		$result 	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as &$row )
		{
			$sql->clear();

			$sql->select( '#__user_usergroup_map' );
			$sql->where( 'group_id' , $row->id );

			$db->setQuery( $sql->getTotalSql() );

			$row->total 	= $db->loadResult();
		}

		return $result;
	}

	/**
	 * Retrieves the "about" information of a user.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAbout($user, $activeStep = 0)
	{
		// Load admin language files
		FD::language()->loadAdmin();

		// Get a list of steps
		$model = FD::model('Steps');
		$steps = $model->getSteps($user->profile_id, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_DISPLAY);

		// Load up the fields library
		$fieldsLib = FD::fields();
		$fieldsModel = FD::model('Fields');

		// Initial step
		$index = 1;
		$hasActive = false;

		foreach ($steps as $step) {

			// Get a list of fields from the current tab
			$options = array('step_id' => $step->id, 'data' => true, 'dataId' => $user->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => SOCIAL_PROFILES_VIEW_DISPLAY);
			$step->fields = $fieldsModel->getCustomFields($options);

			// Trigger each fields available on the step
			if (!empty($step->fields)) {
				$args = array($user);

				$fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_USER, $step->fields, $args);
			}

			// By default hide the step
			$step->hide = true;

			// As long as one of the field in the step has an output, then this step shouldn't be hidden
			// If step has been marked false, then no point marking it as false again
			// We don't break from the loop here because there is other checking going on
			foreach ($step->fields as $field) {

				// We do not want to consider "header" field as a valid output
				if ($field->element == 'header') {
					continue;
				}

				// Ensure that the field has an output
				if (!empty($field->output) && $step->hide === true) {
					$step->hide = false;
				}
			}

			// Default step url
			$step->url = FRoute::profile(array('id' => $user->getAlias(), 'layout' => 'about'), false);

			if ($index !== 1) {
				$step->url = FRoute::profile(array('id' => $user->getAlias(), 'layout' => 'about', 'step' => $index), false);
			}

			$step->title = $step->get('title');
			$step->active = !$step->hide && $index == 1 && !$activeStep;

			// If there is an activeStep set, we should respect that
			if ($activeStep && $activeStep == $step->sequence) {
				$step->active = true;
				$hasActive = true;
			}

			// If the step is not hidden and there isn't any active set previously
			// Also, it should be the first item on the list.
			if (!$activeStep && !$step->hide && !$hasActive && $index == 1) {
				$step->active = true;
				$hasActive = true;
			}

			// If this is not the first step, and there is no active step previously
			if ($index != 1 && !$hasActive && !$step->hide && $step->fields && !$activeStep) {
				$step->active = true;
				$hasActive = true;
			}

			// If this is active, and there is no fields, we should skip it
			if ($step->active && !$step->fields) {
				$step->active = false;
				$hasActive = false;
			}

			if ($step->active) {
				$theme = FD::themes();
				$theme->set('fields', $step->fields);

				$step->html = $theme->output('site/profile/default.info');
			}

			$step->index = $index;

			$index++;
		}

		return $steps;
	}

	/**
	 * Retrieves a list of apps for the user's dashboard.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique user id.
	 * @return
	 */
	public function getDashboardApps( $userId )
	{
		$model 		= FD::model( 'Apps' );
		$options	= array( 'uid' => $userId , 'key' => SOCIAL_TYPE_USER );
		$apps 		= $model->getApps( $options );

		// If there's nothing to process, just exit block.
		if( !$apps )
		{
			return $apps;
		}

		// Format the result as we only want to
		// return the caller apps that should appear on dashboard.
		$result 	= array();

		foreach( $apps as $app )
		{
			if( $app->hasDashboard() )
			{
				$result[]	= $app;
			}
		}

		return $result;
	}

	/**
	 * Retrieves a list of data for a type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique item id.
	 * @param	string	The unique item type.
	 */
	public function initUserData( $id )
	{
		$fieldsModel 	= FD::model( 'Fields' );
		$data 			= $fieldsModel->getFieldsData(array('uid' => $id, 'type' => SOCIAL_TYPE_USER));

		// We need to attach all positions for this field
		$fields	= array();

		if( !$data )
		{
			return false;
		}

		foreach( $data as &$row )
		{
			// Manually assign the uid and type
			$row->uid = $id;
			$row->type = SOCIAL_TYPE_USER;

			$fields[ $row->unique_key ]	= $row;
		}

		return $fields;
	}

	/**
	 * Retrieves a list of user data based on the given ids.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	$ids	An array of ids.
	 * @return
	 */
	public function getUsersMeta($ids = array())
	{
		$loaded = array();
		$new = array();

		if (!empty($ids)) {

			foreach ($ids as $id) {

				if (is_numeric($id)) {

					if (isset(self::$loadedUsers[$id])) {
						$loaded[] = self::$loadedUsers[$id];
					} else {
						$new[] = $id;
					}
				}
			}
		}

		// Only fetch for new items that isn't stored on the cache
		if ($new) {

			foreach ($new as $id) {
				self::$loadedUsers[$id] = false;
			}

			$db = FD::db();
			$sql = $db->sql();

			// set the SQL_BIG_SELECTS here to avoid possible MAX_JOIN_SIZE error.
			$query = "SET SQL_BIG_SELECTS=1";
			$sql->raw($query);
			$db->setQuery($sql);
			$db->query();

			$sql->clear();
			$sql->select( '#__users' , 'a' );
			$sql->column( 'a.*' );
			$sql->column( 'b.small' );
			$sql->column( 'b.medium' );
			$sql->column( 'b.large' );
			$sql->column( 'b.square' );
			$sql->column( 'b.avatar_id' );
			$sql->column( 'b.photo_id' );
			$sql->column( 'b.storage' , 'avatarStorage' );
			$sql->column( 'd.profile_id' );
			$sql->column( 'e.state' );
			$sql->column( 'e.type' );
			$sql->column( 'e.alias' );
			$sql->column( 'e.completed_fields' );
			$sql->column( 'e.permalink' );
			$sql->column( 'e.reminder_sent' );
			$sql->column( 'e.require_reset' );
			$sql->column( 'e.block_period' );
			$sql->column( 'e.block_date' );
			$sql->column( 'f.id' , 'cover_id' );
			$sql->column( 'f.uid' , 'cover_uid' );
			$sql->column( 'f.type' , 'cover_type' );
			$sql->column( 'f.photo_id' , 'cover_photo_id' );
			$sql->column( 'f.cover_id'	, 'cover_cover_id' );
			$sql->column( 'f.x' , 'cover_x' );
			$sql->column( 'f.y' , 'cover_y' );
			$sql->column( 'f.modified' , 'cover_modified' );
			$sql->column( 'g.points' , 'points' , 'sum' );
			$sql->join( '#__social_avatars' , 'b' );
			$sql->on( 'b.uid' , 'a.id' );
			$sql->on( 'b.type' , SOCIAL_TYPE_USER );
			$sql->join( '#__social_profiles_maps' , 'd' );
			$sql->on( 'd.user_id' , 'a.id' );
			$sql->join( '#__social_users' , 'e' );
			$sql->on( 'e.user_id' , 'a.id' );
			$sql->join( '#__social_covers' , 'f' );
			$sql->on( 'f.uid' , 'a.id' );
			$sql->on( 'f.type' , SOCIAL_TYPE_USER );

			$sql->join( '#__social_points_history' , 'g' );
			$sql->on( 'g.user_id' , 'a.id' );

			if (count($new) > 1) {
				$sql->where( 'a.id' , $new , 'IN' );
			} else {
				$sql->where( 'a.id' , $new[0]);
			}

			// to compatible with aggregation function the 'ONLY_FULL_GROUP_BY' standard.
			$sql->group( 'a.id' );


			$db->setQuery($sql);

			$users = $db->loadObjectList();

			if ($users) {

				// cache user metas
				FD::cache()->cacheUsersMeta($users);

				foreach ($users as $user) {
					$loaded[] = $user;
					self::$loadedUsers[$user->id] = $user;
				}
			}
		}

		$return = array();

		if ($loaded) {

			foreach ($loaded as $user) {
				if (isset($user->id)) {
					$return[] = $user;
				}
			}
		}

		return $return;
	}

	/**
	 * Retrieves a list of super administrator's on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	Array
	 */
	public function getSiteAdmins()
	{
		static $cache = null;

		if (is_null($cache)) {
			$db			= FD::db();
			$sql		= $db->sql();

			$sql->select('#__usergroups', 'a')
				->column('a.id')
				->column('a.title')
				->leftjoin('#__usergroups', 'b')
				->on('a.lft', 'b.lft', '>')
				->on('a.rgt', 'b.rgt', '<')
				->group('a.id')
				->order('a.lft', 'asc');

			$db->setQuery($sql);

			// $query		= array();

			// $query[]	= 'SELECT a.' . $db->nameQuote( 'id' ) . ', a.' . $db->nameQuote( 'title' );
			// $query[]	= 'FROM ' . $db->nameQuote( '#__usergroups' ) . ' AS a';
			// $query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__usergroups' ) . ' AS b';
			// $query[]	= 'ON a.' . $db->nameQuote( 'lft' ) . ' > b.' . $db->nameQuote( 'lft' );
			// $query[]	= 'AND a.' . $db->nameQuote( 'rgt' ) . ' < b.' . $db->nameQuote( 'rgt' );
			// $query[]	= 'GROUP BY a.' . $db->nameQuote( 'id' );
			// $query[]	= 'ORDER BY a.' . $db->nameQuote( 'lft' ) . ' ASC';

			// $db->setQuery( $query );
			$result = $db->loadObjectList();

			// Get list of super admin groups.
			$superAdminGroups	= array();

			foreach ($result as $group) {
				if (JAccess::checkGroup($group->id, 'core.admin')) {
					$superAdminGroups[]	= $group;
				}
			}

			$superAdmins = array();

			foreach ($superAdminGroups as $superAdminGroup) {
				$users = JAccess::getUsersByGroup( $superAdminGroup->id );

				foreach ($users as $id) {
					$user  = FD::user( $id );

					// We do not want blocked admin
					if ($user->id && !$user->isBlock()) {
						$superAdmins[]	= $user;
					}
				}
			}

			$cache = $superAdmins;
		}

		return $cache;
	}

	/**
	 * Approves a user's registration application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve( $id )
	{
		$user 	= FD::user( $id );

		return $user->approve();
	}

	/**
	 * Retrieves a list of online users from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOnlineUsers()
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		// Get the session life time so we can know who is really online.
		$jConfig 	= FD::jConfig();
		$lifespan 	= $jConfig->getValue( 'lifetime' );
		$online 	= time() - ( $lifespan * 60 );

		$sql->select( '#__session' , 'a' );
		$sql->column( 'b.id' );
		$sql->join( '#__users' , 'b' , 'INNER' );
		$sql->on( 'a.userid' , 'b.id' );

		// exclude esad users
		$sql->innerjoin('#__social_profiles_maps', 'upm');
		$sql->on('b.id', 'upm.user_id');

		$sql->innerjoin('#__social_profiles', 'up');
		$sql->on('upm.profile_id', 'up.id');
		$sql->on('up.community_access', '1');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'b.id' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where( 'a.time' , $online , '>=' );
		$sql->where( 'b.block' , 0 );
		$sql->group( 'a.userid' );

		$db->setQuery( $sql );

		$result 	= $db->loadColumn();

		if( !$result )
		{
			return array();
		}

		$users	= FD::user( $result );

		return $users;
	}

	/**
	 * Retrieves the total number of users on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalUsers()
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__users' );

		$db->setQuery( $sql->getTotalSql() );

		$total 		= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total number of pending users on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	int		Total number of users
	 */
	public function getTotalPending()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_users' , 'a' );
		$sql->column( 'COUNT(1)' , 'count' );
		$sql->join( '#__users' , 'b' );
		$sql->on( 'b.id' , 'a.user_id' );
		$sql->where( 'a.state' , SOCIAL_REGISTER_APPROVALS );

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total number of pending users form the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPendingUsersCount()
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__users' , 'a' );
		$sql->column( 'COUNT(1)' , 'count' );
		$sql->join( '#__social_users' , 'b' , 'INNER' );
		$sql->on( 'a.id' , 'b.user_id' );
		$sql->where( 'b.state' , SOCIAL_REGISTER_APPROVALS );
		$db->setQuery( $sql );

		$total 		= (int) $db->loadResult();

		return $total;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPendingUsers()
	{
		$db 		= FD::db();
		$query 		= array();
		$query[]	= 'SELECT a.* FROM ' . $db->nameQuote( '#__users' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_users' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'user_id' );
		$query[]	= 'WHERE b.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_REGISTER_APPROVALS );
		$query[]	= 'ORDER BY a.' . $db->nameQuote( 'registerDate' );

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$result 		= $db->loadObjectList();

		// Prepare the user object.
		$users 	= array();

		foreach( $result as $row )
		{
			$user 	= FD::user( $row->id );

			$users[]	= $user;
		}

		return $users;
	}


	/**
	 * Retrieves the total online users on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalOnlineUsers()
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		// Get total backend users
		$sql->select('#__session');
		$sql->column('COUNT(session_id)');
		$sql->where('guest', 0);
		$sql->where('client_id', 1);

		$db->setQuery($sql);

		$totalBackend 	= $db->loadResult();

		// Get total online users on the front end
		$sql->clear();
		$sql->select('#__session');
		$sql->column('COUNT(session_id)');
		$sql->where('guest', 0);
		$sql->where('client_id', 0);

		$db->setQuery($sql);

		$totalSite 	= $db->loadResult();

		$total 	= $totalSite + $totalBackend;

		return $total;
	}

	/**
	 * Retrieves a list of user data based on the given ids.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	$ids	An array of ids.
	 * @return
	 */
	public function getUsersWithState( $options = array() )
	{
		$db		= FD::db();

		$sql 	= $db->sql();

		$sql->select( '#__users' , 'a' );
		$sql->column( 'a.*' );
		$sql->column( 'b.type' );
		$sql->column( 'p.points' , 'points' , 'sum' );

		// Join with points table.
		$sql->join('#__social_points_history', 'p');
		$sql->on('p.user_id', 'a.id');
		$sql->group('a.id');

		// $sql->join( '#__social_users' , 'b' , 'INNER' );
		$sql->join( '#__social_users' , 'b' );
		$sql->on( 'a.id' , 'b.user_id' );

		// Determines if there's a group filter.
		$group	= $this->getState( 'group' );

		if( $group && $group != -1 )
		{
			$sql->join( '#__user_usergroup_map' , 'c' );
			$sql->on( 'a.id' , 'c.user_id' );

			$sql->where( 'c.group_id' , $group );
		}

		// Join with the social profiles table
		$sql->join( '#__social_profiles_maps' , 'e' );
		$sql->on( 'e.user_id' , 'a.id' );

		// Determines if there's a search filter.
		$search = $this->getState( 'search' );

		if( $search )
		{
			$sql->where( '(' );
			$sql->where( 'name' , '%' . $search . '%' , 'LIKE' , 'OR' );
			$sql->where( 'username' , '%' . $search . '%' , 'LIKE' , 'OR' );
			$sql->where( 'email' , '%' . $search . '%' , 'LIKE' , 'OR');
			$sql->where( ')' );
		}

		// Determines if registration state
		$registrationState 	= isset($options[ 'state' ] ) ? $options[ 'state' ] : '';

		if( $registrationState )
		{
			$sql->where( 'b.state' , $registrationState );
		}

		// Determines if state filter is provided
		$state	= $this->getState( 'published' );

		if( $state != 'all' && !is_null( $state ) )
		{
			$state	= $state == 1 ? SOCIAL_JOOMLA_USER_UNBLOCKED : SOCIAL_JOOMLA_USER_BLOCKED;

			$sql->where( 'a.block' , $state );
		}

		// Determines if we want to filter by logged in users.
		$login 	= isset( $options[ 'login' ] ) ? $options[ 'login' ] : '';

		if( $login )
		{
			$tmp	 = 'EXISTS( SELECT ' . $db->nameQuote( 'userid' ) . ' FROM ' . $db->nameQuote( '#__session' ) . ' AS f WHERE ' . $db->nameQuote( 'userid' ) . ' = a.' . $db->nameQuote( 'id' ) . ')';

			$sql->exists( $tmp );
		}

		$picture 	= isset( $options[ 'picture' ] ) ? $options[ 'picture' ] : '';

		// Determines if we should only pick users with picture
		if( $picture )
		{
			$sql->join( '#__social_avatars' , 'g' );
			$sql->on( 'a.id' , 'g.uid' );

			$sql->where( 'g.small' , '' , '!=' );
		}


		// Determines if there's filter by profile id.
		$profile 		= $this->getState( 'profile' );

		if( $profile && $profile != -1 && $profile != -2 )
		{
			$sql->where( 'e.profile_id' , $profile );
		}
		else if( $profile == -2 )
		{
			$sql->isnull( 'e.profile_id');
		}

		// Determines if we have an exclusion list.
		$exclusions 	= isset( $options[ 'exclusion' ] ) ? $options[ 'exclusion' ] : '';

		if( $exclusions )
		{
			// Ensure that it's in an array
			$exclusions 	= FD::makeArray( $exclusions );
			$sql->where( 'a.id' , implode( ',' , $exclusions ) , 'NOT IN' );
		}

		// Determines if we need to order the items by column.
		$ordering 	= isset($options[ 'ordering' ] ) ? $options[ 'ordering' ] : '';

		// Ordering based on caller
		if( $ordering )
		{
			$direction 	= isset( $options[ 'direction' ] ) ? $options[ 'direction' ] : '';

			$sql->order( $ordering , $direction );
		}

		// Column ordering
		$ordering 	= $this->getState( 'ordering' , $ordering );

		if( $ordering )
		{
			$direction 	= $this->getState( 'direction' );

			$sql->order( $ordering , $direction );
		}

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		$limitState  = $this->getState( 'limit' );


		if( $limit != 0 || $limitState )
		{
			if( $limit )
			{
				$sql->limit( 0 , $limit );
			}

			// Set the total number of items.
			$this->setTotal( $sql->getSql() , true );

			// Get the list of users
			$users 	= $this->getData( $sql->getSql() );
		}
		else
		{
			$db->setQuery( $sql );
			$users 	= $db->loadObjectList();
		}


		return $users;
	}

	/**
	 * Determines if the alias exists
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function aliasExists( $alias , $exceptUserId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_users' );
		$sql->column( 'COUNT(1)' , 'total' );
		$sql->where( 'alias' , $alias );
		$sql->where( 'user_id' , $exceptUserId , '!=' );

		$db->setQuery( $sql );
		$exists	= $db->loadResult() >= 1 ? true : false;

		return $exists;
	}

	/**
	 * Retrieve's user id based on the alias
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserIdFromAlias($permalink)
	{
		static $loaded 	= array();

		if (!isset($loaded[$permalink])) {
			$config = FD::config();

			// Get the user form permalink field
			$id = $this->getUserFromPermalink($permalink);

			// If the user set's the permalink, we should respect that.
			if ($id) {
				$loaded[$permalink]	= $id;

				return $loaded[$permalink];
			}

			// Try to get the user id from the alias column
			$id = $this->getUserFromAlias($permalink);

			if ($id) {
				$loaded[$permalink] = $id;

				return $loaded[$permalink];
			}

			// We need to know which column should we be checking against.
			if ($config->get('users.aliasName' ) == 'realname') {

				if (strpos($permalink , ':') !== false) {
					$parts = explode(':', $permalink , 2);

					$id = $parts[0];
				}

				$loaded[$permalink]	= $id;

				return $loaded[$permalink];
			}

			// If it reaches here, we know then that the alias is using username
			// First we need to replace : with -
			$tmp = str_replace( ':', '-', $permalink );
			$id = $this->getUserIdWithUsernamePermalink( $tmp );

			// If we still can't find '-' try '_' now.
			if( !$id )
			{
				$tmp 	= str_replace( ':' , '_' , $permalink );
				$id 	= $this->getUserIdWithUsernamePermalink( $tmp );
			}

			// If we still can't find '_' , we replace it with spaces
			if( !$id )
			{
				$tmp 	= str_replace( ':' , ' ' , $permalink );
				$id 	= $this->getUserIdWithUsernamePermalink( $tmp );
			}

			$loaded[ $permalink ] 	= $id;
		}

		return $loaded[ $permalink ];
	}

	/**
	 * Determines if the permalink is a valid permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isValidUserPermalink( $permalink )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_users' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'permalink' , $permalink );

		$db->setQuery( $sql );

		$exists	= $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Retrieve user's id given the username permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The username permalink
	 * @return
	 */
	public function getUserIdWithUsernamePermalink( $permalink )
	{
		$db 	= FD::db();

		$sql 	= $db->sql();

		$sql->select( '#__users' );
		$sql->column( 'id' );
		$sql->where( 'LOWER( `username` )' , $permalink );

		$db->setQuery( $sql );

		$id 	= $db->loadResult();

		return $id;
	}

	/**
	 * Retrieve a user with the given permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserFromAlias( $alias )
	{
		$db 	= FD::db();

		$sql 	= $db->sql();

		$sql->select( '#__social_users' );
		$sql->column( 'user_id' );
		$sql->where( 'alias' , $alias , '=' );

		$db->setQuery( $sql );

		$id 	= (int) $db->loadResult();

		return $id;
	}

	/**
	 * Retrieve a user with the given permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserFromPermalink( $permalink )
	{
		$db 	= FD::db();

		$sql 	= $db->sql();

		$variant 	= str_ireplace( ':', '-' , $permalink );
		$underscore = str_ireplace( ':' , '_' , $permalink );

		$sql->select( '#__social_users' );
		$sql->column( 'user_id' );
		$sql->where( 'permalink' , $permalink , '=' , 'OR' );
		$sql->where( 'permalink' , $variant , '=' , 'OR' );
		$sql->where( 'permalink' , $underscore , '=' , 'OR' );
		$sql->where( 'LOWER(`permalink`)' , $permalink , '=' , 'OR' );
		$sql->where( 'LOWER(`permalink`)' , $variant , '=' , 'OR' );
		$sql->where( 'LOWER(`permalink`)' , $underscore , '=' , 'OR' );

		// There are instances where the _ is converted into -
		$sql->where('LOWER(`permalink`)', str_ireplace('-', '_', $permalink), '=', 'OR');

		$db->setQuery( $sql );

		$id 	= (int) $db->loadResult();

		return $id;
	}

	public function getDisplayOptions()
	{
		return $this->displayOptions;
	}

	public function getUsersByFilter($fid, $settings = array(), $options = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		if ($fid) {
			// we need to load the data from db and do the search based on the saved filter.
			$filter = FD::table( 'SearchFilter' );
			$filter->load( $fid );

			if (!$filter->id) {
				return array();
			}

			// data saved as json format. so we need to decode it.
			$dataFilter = FD::json()->decode( $filter->filter );

			// override with the one from db.
			$options['criterias'] 		= $dataFilter->{'criterias[]'};
			$options['datakeys'] 		= $dataFilter->{'datakeys[]'};
			$options['operators'] 		= $dataFilter->{'operators[]'};
			$options['conditions'] 		= $dataFilter->{'conditions[]'};
		}

		// we need check if the item passed in is array or not. if not, make it an array.
		if( ! is_array( $options['criterias'] ) )
		{
			$options['criterias'] = array( $options['criterias'] );
		}

		if( ! is_array( $options['datakeys'] ) )
		{
			$options['datakeys'] = array( $options['datakeys'] );
		}

		if( ! is_array( $options['operators'] ) )
		{
			$options['operators'] = array( $options['operators'] );
		}

		if( ! is_array( $options['conditions'] ) )
		{
			$options['conditions'] = array( $options['conditions'] );
		}

		$options['match'] 			= isset( $dataFilter->matchType ) ? $dataFilter->matchType : 'all';

		$avatarOnly = isset($options['avatarOnly']) ? $options['avatarOnly'] : false;
		$options['avatarOnly'] = isset( $dataFilter->avatarOnly ) ? true : $avatarOnly;

		$onlineOnly = isset($options['onlineOnly']) ? $options['onlineOnly'] : false;
		$options['onlineOnly'] = isset( $dataFilter->onlineOnly ) ? true : $onlineOnly;

		//setup displayOptions
		$library 	= FD::get( 'AdvancedSearch' );
		$library->setDisplayOptions($options);

		$this->displayOptions = $library->getDisplayOptions();

		$sModel = FD::model('search');

		$query = $sModel->buildAdvSearch($options['match'], $options);

	    if (! $query) {
	    	return array();
	    }

	    $sql->raw( $query );

		$limit 	= isset( $settings[ 'limit' ] ) ? $settings[ 'limit' ] : '';

		if( $limit != 0 )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getSql() , true );

			// Get the list of users
			$users 	= $this->getData( $sql->getSql() );

		}
		else
		{
			$db->setQuery( $sql );
			$users 	= $db->loadObjectList();
		}

		return $users;
	}


	/**
	 * Retrieves a list of user data based on the given ids.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	$ids	An array of ids.
	 * @return
	 */
	public function getUsers($options = array())
	{
		$db		= FD::db();

		$sql 	= $db->sql();

		// Get current user logged in user
		$my = FD::user();

		// Determines if there's filter by profile id.
		$profile 	= isset( $options[ 'profile' ] ) ? $options[ 'profile' ] : '';

		$ignoreESAD = isset($options['ignoreESAD']) ? $options['ignoreESAD'] : false;


		$sql->select( '#__users' , 'a' );
		$sql->column( 'a.id' );
		$sql->column( 'b.type' );
		$sql->column( 'd.points' , 'points' , 'sum' );
		$sql->join( '#__social_users' , 'b' , 'INNER' );
		$sql->on( 'a.id' , 'b.user_id' );

		// exclude esad users
		if (!$ignoreESAD && !(FD::config()->get('users.listings.esadadmin') && $my->isSiteAdmin())) {
			$sql->innerjoin('#__social_profiles_maps', 'upm');
			$sql->on('a.id', 'upm.user_id');

			$sql->innerjoin('#__social_profiles', 'up');
			$sql->on('upm.profile_id', 'up.id');
			$sql->on('up.community_access', '1');
		}


		// Join with the points table to retrieve user's points
		$sql->join( '#__social_points_history' , 'd' );
		$sql->on( 'd.user_id' , 'a.id' );

		if( $profile && $profile != -1 ) {
			// Join with the social profiles table
			$sql->join( '#__social_profiles_maps' , 'e', 'inner' );
			$sql->on( 'e.user_id' , 'a.id' );
		}

		$excludeBlocked = isset($options[ 'excludeblocked' ] ) ? $options[ 'excludeblocked' ] : 0;

		if (FD::config()->get('users.blocking.enabled') && $excludeBlocked && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.id' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		// Determines if registration state
		$registrationState 	= isset($options[ 'state' ] ) ? $options[ 'state' ] : '';

		if( $registrationState )
		{
			$sql->where( 'b.state' , $registrationState );
		}

		// Determines if we should display admin's on this list.
		$includeAdmin 	= isset( $options[ 'includeAdmin' ] ) ? $options[ 'includeAdmin' ] : null;

		// If caller doesn't want to include admin, we need to set the ignore list.
		if( $includeAdmin === false )
		{
			// Get a list of site administrators from the site.
			$admins 	= $this->getSiteAdmins();

			if( $admins )
			{
				$ids	= array();

				foreach( $admins as $admin )
				{
					$ids[] 	= $admin->id;
				}

				$sql->where( 'a.id' , $ids , 'NOT IN' );
			}
		}

		// Determines if state filter is provided
		$state 	= isset( $options[ 'published' ] ) ? $options[ 'published' ] : '';

		if( $state !== '' )
		{
			$state	= $state == 1 ? SOCIAL_JOOMLA_USER_UNBLOCKED : SOCIAL_JOOMLA_USER_BLOCKED;

			$sql->where( 'a.block' , $state );
		}

		// Determines if we want to filter by logged in users.
		$login 	= isset( $options[ 'login' ] ) ? $options[ 'login' ] : '';

		if( $login )
		{
			// Determine if only to fetch front end
			$frontend	= isset( $options[ 'frontend' ] ) ? $options[ 'frontend' ] : '';

			$tmp	 	= 'EXISTS( ';
			$tmp	 	.= 'SELECT ' . $db->nameQuote( 'userid' ) . ' FROM ' . $db->nameQuote( '#__session' ) . ' AS f WHERE ' . $db->nameQuote( 'userid' ) . ' = a.' . $db->nameQuote( 'id' );

			if( $frontend )
			{
				$tmp 	.= ' AND `client_id` = ' . $db->Quote( 0 );
			}


			$tmp 		.= ')';

			$sql->exists( $tmp );
		}

		$picture 	= isset( $options[ 'picture' ] ) ? $options[ 'picture' ] : '';

		// Determines if we should only pick users with picture
		if( $picture )
		{
			$sql->innerjoin( '#__social_avatars' , 'g' );
			$sql->on( 'a.id' , 'g.uid' );
			$sql->on( 'g.small' , '' , '!=' );

			$sql->innerjoin( '#__social_photos_meta' , 'pm' );
			$sql->on( 'g.photo_id' , 'pm.photo_id' );

			$sql->on( 'pm.group' , 'path');
			$sql->on( 'pm.property' , 'stock');
		}

		if( $profile && $profile != -1 )
		{
			$sql->where( 'e.profile_id' , $profile );
		}

		// Determines if we have an exclusion list.
		$exclusions 	= isset( $options[ 'exclusion' ] ) ? $options[ 'exclusion' ] : '';

		if( $exclusions )
		{
			// Ensure that it's in an array
			$exclusions 	= FD::makeArray( $exclusions );
			$sql->where( 'a.id' , implode( ',' , $exclusions ) , 'NOT IN' );
		}

		// Determines if we have an inclusion list.
		$inclusion = isset($options['inclusion'])? $options['inclusion'] : '';

		if ($inclusion) {

			// Ensure that it's in an array
			$inclusion = FD::makeArray($inclusion);
			$sql->where('a.id', $inclusion, 'IN');
		}

		// Determines if we need to order the items by column.
		$ordering 	= isset($options[ 'ordering' ] ) ? $options[ 'ordering' ] : '';

		// Ordering based on caller
		if( $ordering )
		{
			$direction 	= isset( $options[ 'direction' ] ) ? $options[ 'direction' ] : '';

			$sql->order( $ordering , $direction );
		}

		// Group items by id since the points history may generate duplicate records.
		$sql->group( 'a.id' );
		$sql->group( 'b.type' );

		// echo $sql;exit;

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';


		if( $limit != 0 )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getSql() , true );

			// Get the list of users
			$users 	= $this->getData( $sql->getSql() );

		}
		else
		{

			$db->setQuery( $sql );
			$users 	= $db->loadObjectList();
		}


		return $users;
	}

	/**
	 * Determines whether the current user is active or not.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	boolean		True if online, false otherwise.
	 */
	public function isOnline( $id )
	{
		$db		= FD::db();

		$onlineKey = 'user.online.' . $id;

		if (FD::cache()->exists($onlineKey)) {
			return FD::cache()->get($onlineKey);
		}

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__session' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );

		$db->setQuery( $query );

		$online	= $db->loadResult() > 0;

		return $online;
	}

	/**
	 * Perform necessary logics when a user is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete($id)
	{
		// Delete profile mapping
		$this->deleteProfile( $id );

		// Delete form #__social_oauth
		$this->deleteOAuth( $id );

		// Delete user stream item
		$this->deleteStream( $id );

		// Delete user photos
		$this->deletePhotos( $id );

		// Delete user relations within a cluster
		$this->deleteClusterNodes($id);

		// Delete user followers
		$this->deleteFollowers($id);

		// Delete user notifications
		$this->deleteNotifications($id);

		// Delete user comments from the site
		$this->deleteComments($id);

		// Delete user friends from the site
		$this->deleteFriends($id);

		// Conversations should also be deleted from the site.
		$this->deleteConversations($id);

		return true;
	}

	/**
	 * Remove all followers and following from this user
	 *
	 * @since	1.2.8
	 * @access	public
	 * @param	int		The user's id
	 * @return
	 */
	public function deleteFollowers($id)
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		// Delete notifications generated for this user.
		$sql->delete('#__social_subscriptions');
		$sql->where('uid', $id);
		$sql->where('type', 'user.user');
		$db->setQuery($sql);
		$db->Query();

		// Delete notifications generated by this user.
		$sql->clear();
		$sql->delete('#__social_subscriptions');
		$sql->where('user_id', $id);
		$sql->where('type', 'user.user');
		$db->setQuery($sql);
		$db->Query();
	}

	/**
	 * Remove all notifications from a user.
	 *
	 * @since	1.2.8
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteNotifications($id)
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		// Delete notifications generated for this user.
		$sql->delete('#__social_notifications');
		$sql->where('target_id', $id);
		$db->setQuery($sql);
		$db->Query();

		// Delete notifications generated by this user.
		$sql->clear();
		$sql->delete('#__social_notifications');
		$sql->where('actor_id', $id);
		$db->setQuery($sql);
		$db->Query();

		// Delete any pending emails for this user.
		$user 	= FD::user($id);

		$sql->clear();
		$sql->delete('#__social_mailer');
		$sql->where('recipient_email', $user->email);
		$db->setQuery($sql);
		$db->Query();
	}

	/**
	 * Remove a user from all cluster nodes
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteClusterNodes($id)
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->delete('#__social_clusters_nodes');
		$sql->where('uid', $id);
		$sql->where('type', 'user');

		$db->setQuery($sql);

		$db->Query();
	}

	/**
	 * Deletes the user profile data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteProfile( $userId )
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		// Delete profile mapping of the user
		$sql->delete( '#__social_profiles_maps' );
		$sql->where( 'user_id' , $userId );
		$db->setQuery( $sql );
		$db->Query();

		// Delete user custom fields.
		$sql->clear();
		$sql->delete( '#__social_fields_data' );
		$sql->where( 'uid' , $userId );
		$sql->where( 'type' , SOCIAL_TYPE_USER );
		$db->setQuery( $sql );
		$db->Query();

		// Delete #__social_users
		$sql->clear();
		$sql->delete( '#__social_users' );
		$sql->where( 'user_id' , $userId );

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}

	/**
	 * Delete user photos
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deletePhotos( $userId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		// Delete user albums
		$sql->clear();
		$sql->select( '#__social_albums' );
		$sql->where( 'uid' , $userId );
		$sql->where( 'type' , SOCIAL_TYPE_USER );
		$db->setQuery( $sql );

		$albums	= $db->loadObjectList();

		if( $albums )
		{
			foreach( $albums as $row )
			{
				$album	= FD::table( 'Album' );
				$album->load( $row->id );

				$album->delete();
			}
		}

		return true;
	}

	/**
	 * Delete user's cover
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteCover( $userId )
	{
		$cover 	= FD::table( 'Cover' );
		$cover->load( $userId , SOCIAL_TYPE_USER );

		return $cover->delete();
	}

	/**
	 * Delete user's avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteAvatar( $userId )
	{
		$avatar 	= FD::table( 'Avatar' );
		$avatar->load( $userId , SOCIAL_TYPE_USER );

		return $avatar->delete();
	}

	/**
	 * Deletes the conversations
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteConversations($userId)
	{
		// Get a list of conversations the user is participating in
		$model = FD::model('Conversations');

		return $model->deleteConversationsInvolvingUser($userId);
	}

	/**
	 * Deletes user likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteLikes( $userId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_likes' );
		$sql->where( 'created_by' , $userId );

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}

	/**
	 * Deletes user comments
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteComments($userId)
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_comments' );
		$sql->where( 'created_by' , $userId );

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}

	/**
	 * Deletes the user point relations
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deletePoints( $userId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_points_history' );
		$sql->where( 'user_id' , $userId );

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}

	/**
	 * Deletes the user friend relations
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFriends( $userId )
	{
		// Delete friend list
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_lists' );
		$sql->where( 'user_id' , $userId );

		$db->setQuery( $sql );
		$db->Query();

		$sql->clear();

		// Delete friends
		$sql->delete( '#__social_friends' );
		$sql->where( 'actor_id' , $userId );
		$sql->where( 'target_id' , $userId, '=', 'or' );

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}

	/**
	 * Deletes the user point relations
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteLabels( $userId )
	{
		// Delete labels
		$db		= FD::db();
		$sql	= $db->sql();

		$query->delete( '#__social_labels' );
		$sql->where( 'created_by' , $userId );

		$db->setQuery( $sql );
		$db->Query();

		return true;
	}


	/**
	 * Deletes stream of a user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteStream( $userId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_stream' );
		$sql->where( 'actor_id' , $userId );
		$sql->where( 'actor_type' , SOCIAL_TYPE_USER );

		$db->setQuery( $sql );

		$streams 	= $db->loadObjectList();

		// Delete all stream relations
		foreach( $streams as $stream )
		{
			$sql->clear();

			$sql->delete( '#__social_stream_item' );
			$sql->where( 'uid' , $stream->id );

			$db->setQuery( $sql );
			$db->Query();

			// Delete the stream item.
			$sql->clear();
			$sql->delete( '#__social_stream' );
			$sql->where( 'id' , $stream->id );

			$db->setQuery( $sql );
			$db->query();
		}

		// Delete any hidden stream by the user.
		$sql->clear();

		$sql->delete( '#__social_stream_hide' );
		$sql->where( 'user_id' , $userId );
		$db->setQuery( $sql );

		$db->Query();

		return true;
	}

	/**
	 * Retrieve the user's id given the authentication code for REST api
	 *
	 * @since	1.2.8
	 * @access	public
	 * @param	string		The authentication code used in REST API
	 * @return	int 		The user's id.
	 */
	public function getUserIdFromAuth($code)
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select('#__social_users');
		$sql->column('user_id');
		$sql->where('auth', $code);

		$db->setQuery($sql);

		$id		= (int) $db->loadResult();

		return $id;
	}

	/**
	 * Retrieves the user's id
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The key to lookup for
	 * @param	string	The value for the key
	 * @return
	 */
	public function getUserId( $key , $value )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__users' );
		$sql->column( 'id' );
		$sql->where( $key , $value );

		$db->setQuery( $sql );

		$id 	= $db->loadResult();
		return $id;
	}

	/**
	 * Method to check if user reset limit has been exceeded within the allowed time period.
	 *
	 * @param   JUser  the user doing the password reset
	 *
	 * @return  boolean true if user can do the reset, false if limit exceeded
	 *
	 * @since    2.5
	 */
	public function checkResetLimit($user)
	{
		$params = JFactory::getApplication()->getParams();
		$maxCount = (int) $params->get('reset_count');
		$resetHours = (int) $params->get('reset_time');
		$result = true;

		$lastResetTime = strtotime($user->lastResetTime) ? strtotime($user->lastResetTime) : 0;
		$hoursSinceLastReset = (strtotime(JFactory::getDate()->toSql()) - $lastResetTime) / 3600;

		// If it's been long enough, start a new reset count
		if ($hoursSinceLastReset > $resetHours)
		{
			$user->lastResetTime = JFactory::getDate()->toSql();
			$user->resetCount = 1;
		}

		// If we are under the max count, just increment the counter
		elseif ($user->resetCount < $maxCount)
		{
			$user->resetCount;
		}

		// At this point, we know we have exceeded the maximum resets for the time period
		else
		{
			$result = false;
		}
		return $result;
	}

	/**
	 * Reset password confirmation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The user's username
	 * @param	string	The verification code
	 * @return
	 */
	public function verifyResetPassword( $username , $code )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__users' );
		$sql->column( 'activation' );
		$sql->column( 'id' );
		$sql->column( 'block' );

		if (FD::config()->get('registrations.emailasusername')) {
			$sql->where( 'email' , $username );
		} else {
			$sql->where( 'username' , $username );
		}


		$db->setQuery( $sql );

		$obj 	= $db->loadObject();

		if( !$obj )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_USERS_NO_SUCH_USER_WITH_EMAIL' ) );
			return false;
		}

		// Split the crypt and salt
		$parts 	= explode( ':' , $obj->activation );
		$crypt	= $parts[ 0 ];

		if( !isset( $parts[ 1 ] ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_USERS_NO_SUCH_USER_WITH_EMAIL' ) );
			return false;
		}

		$salt 	= $parts[ 1 ];
		// Manually pass in crypt type as md5-hex because when we generate the activation token, it is crypted with crypt-md5, and due to Joomla 3.2 using bcrypt by default, this part fails. We revert back to Joomla 3.0's default crypt format, which is md5-hex.
		$test	= JUserHelper::getCryptedPassword( $code , $salt, 'md5-hex' );

		if( $crypt != $test )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_INVALID_CODE' ) );
			return false;
		}

		// Ensure that the user account is not blocked
		if( $obj->block )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_USER_BLOCKED' ) );
			return false;
		}

		// Push the user data into the session.
		$app = JFactory::getApplication();
		$app->setUserState( 'com_users.reset.token'	, $crypt . ':' . $salt);
		$app->setUserState( 'com_users.reset.user'	, $obj->id );

		return true;
	}

	/**
	 * Resets the user's password
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The password
	 * @param	string	The reconfirm password
	 * @return
	 */
	public function resetPassword( $password , $password2 )
	{
		// Get the token and user id from the confirmation process.
		$app		= JFactory::getApplication();
		$token		= $app->getUserState( 'com_users.reset.token' , null );
		$userId		= $app->getUserState( 'com_users.reset.user' , null );

		// Check for the token and the user's id.
		if( !$token || !$userId )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_TOKENS_MISSING' ) );
			return false;
		}

		// Retrieve the user object
		$user = JUser::getInstance( $userId );

		// Check for a user and that the tokens match.
		if( empty($user) || $user->activation !== $token )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_USERS_NO_SUCH_USER' ) );
			return false;
		}

		// Ensure that the user account is not blocked
		if ($user->block) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_USER_BLOCKED' ) );
			return false;
		}

		// Generates the new password hash
		$salt 		= JUserHelper::genRandomPassword( 32 );
		$crypted	= JUserHelper::getCryptedPassword( $password , $salt );
		$password	= $crypted . ':' . $salt;

		// Update user's object
		$user->password 	= $password;

		// Reset the activation
		$user->activation	= '';

		// Set the clear password
		$user->password_clear	= $password2;

		if (isset($user->requireReset)) {
			$user->requireReset	= 0;
		}

		// Save the user to the database.
		if( !$user->save( true ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_SAVE_ERROR' ) );
			return false;
		}

		// we need to reset require_reset from social_users table.
		$userModel = FD::model('Users');
		$userModel->updateUserPasswordResetFlag($user->id, '0');

		// Flush the user data from the session.
		$app->setUserState('com_users.reset.token', null);
		$app->setUserState('com_users.reset.user', null);

		return true;
	}

	/**
	 * Resets the user's password
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The password
	 * @param	string	The reconfirm password
	 * @return
	 */
	public function resetRequirePassword( $password , $password2 )
	{
		// Get the token and user id from the confirmation process.
		$app		= JFactory::getApplication();

		// Retrieve the user object
		$user = JFactory::getUser();

		// Ensure that the user account is not blocked
		if ($user->block) {
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_USER_BLOCKED' ) );
			return false;
		}

		// Generates the new password hash
		$salt 		= JUserHelper::genRandomPassword( 32 );
		$crypted	= JUserHelper::getCryptedPassword( $password , $salt );
		$password	= $crypted . ':' . $salt;

		// Update user's object
		$user->password 	= $password;

		// Set the clear password
		$user->password_clear	= $password2;

		// if (JUserHelper::verifyPassword($user->password_clear, $user->password)) {
		// 	$this->setError(JText::_('JLIB_USER_ERROR_CANNOT_REUSE_PASSWORD'));
		// 	return false;
		// }

		if (isset($user->requireReset)) {
			$user->requireReset	= 0;
		}

		// Save the user to the database.
		if( !$user->save( true ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_SAVE_ERROR' ) );
			return false;
		}

		// we need to reset require_reset from social_users table.
		$userModel = FD::model('Users');
		$userModel->updateUserPasswordResetFlag($user->id, '0');

		return true;
	}


	/**
	 * Remind password
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The email address of the user.
	 * @return
	 */
	public function remindPassword( $email )
	{
		// Load backend language file.
		FD::language()->loadAdmin();

		$id 	= $this->getUserId( 'email' , $email );

		if( !$id )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_USERS_NO_SUCH_USER_WITH_EMAIL' ) );
			return false;
		}

		$user	= FD::user( $id );

		// Ensure that the user is not blocked
		if( $user->block )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_USERS_USER_BLOCKED' ) );
			return false;
		}

		// Super administrator is not allowed to reset passwords.
		if( $user->authorise( 'core.admin' ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_SUPER_ADMIN' ) );
			return false;
		}

		// Make sure the user has not exceeded the reset limit
		if (!$this->checkResetLimit($user))
		{
			$resetLimit 	= (int) JFactory::getApplication()->getParams()->get( 'reset_time' );
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_EXCEEDED' , $resetLimit ) );
			return false;
		}

		// Set the confirmation token.
		$token			= JApplication::getHash(JUserHelper::genRandomPassword());
		$salt			= JUserHelper::getSalt('crypt-md5');
		$hashedToken	= md5($token . $salt) . ':' . $salt;

		// Set the new activation
		$user->activation	= $hashedToken;

		// Save the user to the database.
		if( !$user->save(true) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_SAVE_ERROR' ) );
			return false;
		}

		// Get the application data.
		$jConfig 	= FD::jConfig();

		// Push arguments to template variables so users can use these arguments
		$params 	= array(
								'site'			=> $jConfig->getValue( 'sitename' ),
								'username'		=> $user->username,
								'name'			=> $user->getName(),
								'id'			=> $user->id,
								'avatar'		=> $user->getAvatar( SOCIAL_AVATAR_LARGE ),
								'profileLink'	=> $user->getPermalink( true, true ),
								'email'			=> $email,
								'token'			=> $token
						);

		// Get the email title.
		$title 			= JText::_( 'COM_EASYSOCIAL_EMAILS_REMIND_PASSWORD_TITLE' );

		// Immediately send out emails
		$mailer 		= FD::mailer();

		// Get the email template.
		$mailTemplate	= $mailer->getTemplate();

		// Set recipient
		$mailTemplate->setRecipient( $user->name , $user->email );

		// Set title
		$mailTemplate->setTitle( $title );

		// Set the contents
		$mailTemplate->setTemplate( 'site/user/remind.password' , $params );

		// Set the priority. We need it to be sent out immediately since this is user registrations.
		$mailTemplate->setPriority( SOCIAL_MAILER_PRIORITY_IMMEDIATE );

		// Try to send out email now.
		$state 		= $mailer->create( $mailTemplate );

		return $state;
	}

	/**
	 * Remind username
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remindUsername( $email )
	{
		// Load backend language file.
		FD::language()->loadAdmin();

		$db 	= FD::db();
		$sql 	= $db->sql();

		// Check if such email exists
		$sql->select( '#__users' );
		$sql->where( 'email' , $email );

		$db->setQuery( $sql );

		$row	= $db->loadObject();

		if( !$row )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_USERS_NO_SUCH_USER_WITH_EMAIL' ) );
			return false;
		}

		// Ensure that the user is not blocked
		if( $row->block )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_USERS_USER_BLOCKED' ) );
			return false;
		}

		$user 		= FD::user( $row->id );

		// Get the application data.
		$jConfig 	= FD::jConfig();

		// Push arguments to template variables so users can use these arguments
		$params 	= array(
								'site'			=> $jConfig->getValue( 'sitename' ),
								'username'		=> $row->username,
								'name'			=> $user->getName(),
								'id'			=> $user->id,
								'avatar'		=> $user->getAvatar( SOCIAL_AVATAR_LARGE ),
								'profileLink'	=> $user->getPermalink( true, true ),
								'email'			=> $email
						);

		// Get the email title.
		$title 		= JText::sprintf( 'COM_EASYSOCIAL_EMAILS_REMIND_USERNAME_TITLE' , $jConfig->getValue( 'sitename' ) );

		// Immediately send out emails
		$mailer 	= FD::mailer();

		// Get the email template.
		$mailTemplate	= $mailer->getTemplate();

		// Set recipient
		$mailTemplate->setRecipient( $user->name , $user->email );

		// Set title
		$mailTemplate->setTitle( $title );

		// Set the contents
		$mailTemplate->setTemplate( 'site/user/remind.username' , $params );

		// Set the priority. We need it to be sent out immediately since this is user registrations.
		$mailTemplate->setPriority( SOCIAL_MAILER_PRIORITY_IMMEDIATE );

		// Try to send out email now.
		$state 		= $mailer->create( $mailTemplate );

		return $state;
	}


	/**
	 * Delete any oauth related data here
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteOAuth( $userId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		// Get the correct oauth id first.
		$sql->select( '#__social_oauth' );
		$sql->where( 'uid' , $userId );
		$sql->where( 'type' , SOCIAL_TYPE_USER );

		$db->setQuery( $sql );

		$oauthId	= $db->loadResult();

		if( $oauthId )
		{
			$sql->delete( '#__social_oauth' );
			$sql->where( 'uid' , $userId );
			$sql->where( 'type' , SOCIAL_TYPE_USER );
			$db->setQuery( $sql );
			$db->Query();

			$sql->clear();

			// Delete oauth histories as well
			$sql->delete( '#__social_oauth_history' );
			$sql->where( 'oauth_id' , $oauthId );
		}

		return true;
	}

	/**
	 * Creates a user in the system
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Users' );
	 * $model->create( $username , $email , $password );
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
	public function create( $data , SocialUser $user , SocialTableProfile $profile )
	{
		// Get a list of user groups this profile is assigned to
		$json = FD::json();
		$groups = $json->decode( $profile->gid );

		// Need to bind the groups under the `gid` column from Joomla.
		$data['gid'] = $groups;

		// Bind the posted data
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
		$user->set('state' , constant( 'SOCIAL_REGISTER_' . strtoupper( $type ) ) );

		// Save the user object
		$state = $user->save();

		// If there's a problem saving the user object, set error message.
		if (!$state) {
			$this->setError($user->getError());
			return false;
		}

		// Set the user with proper `profile_id`
		$user->profile_id = $profile->id;

		// Once the user is saved successfully, add them into the profile mapping.
		$profile->addUser($user->id);

		return $user;
	}

	public function setUserFieldsData($ids)
	{

		$db = FD::db();
		$sql = $db->sql();


		// if ids is empty, do not process at all.
		if (! $ids) {
			return;
		}

		// lets get the fields first.
		$query = "select a.*, b.`uid` as `profile_id`, c.`element`";
		$query .= " from `#__social_fields` as a";
		$query .= " inner join `#__social_fields_steps` as b on a.`step_id` = b.`id`";
		$query .= " inner join `#__social_apps` as c on a.`app_id` = c.`id`";
		$query .= " where b.`type` = " . $db->Quote(SOCIAL_TYPE_PROFILES);

		$sql->raw($query);
		$db->setQuery($sql);

		$results = $db->loadObjectList();

		// next we get the field_datas for the users.
		$query = "select a.* from `#__social_fields_data` as a";
		$query .= "	inner join `#__social_fields` as b on a.`field_id` = b.`id`";
		$query .= " where a.`type` = 'user'";
		$query .= " and a.`uid` IN (" . implode(',', $ids) . ')';

		$sql->clear();
		$sql->raw($query);
		$db->setQuery($sql);

		$dresults = $db->loadObjectList();

		$fields = array();
		$data = array();

		// binding data into field jtable object
		if ($results) {

			// We need to bind the fields with SocialTableField
			$fieldIds = array();

			foreach($results as $row) {
				$field 	= FD::table( 'Field' );
				$field->bind( $row );

				$fieldIds[] = $field->id;

				$field->data = '';
				$field->profile_id = isset( $row->profile_id ) ? $row->profile_id : '';
				$fields[$field->id]	= $field;
			}

			// // set the field options in batch.
			FD::table( 'Field' )->setBatchFieldOptions( $fieldIds );
		}

		//groupping fields data for later processing.
		if ($dresults) {
			foreach($dresults as $item) {
				$data[$item->uid][$item->field_id][] = $item;
			}
		}


		$final = array();
		//now let combine the data with fields for each users
		if ($data) {
			foreach ($data as $uid => $items) {
				// foreach field data

				$xfield = null;

				foreach ($items as $fid => $fielddata) {

					if (!$fields[$fid]) {
						continue;
					}

					$xfield = clone $fields[$fid];

					$xfield->bindData($uid, SOCIAL_TYPE_USER, $fielddata);
					$xfield->data = $xfield->getData($uid, SOCIAL_TYPE_USER);
					$xfield->uid = $uid;
					$xfield->type = SOCIAL_TYPE_USER;

					$user = FD::user($uid);
					$user->bindCustomField($xfield);
				}

			}//foreach
		}

	}


	public function setUserGroupsBatch( $ids )
	{
		// Get the path to the helper file.
		$file 			= SOCIAL_LIB . '/user/helpers/joomla.php';
		require_once($file);

		SocialUserHelperJoomla::setUserGroupsBatch($ids);
	}

	public function verifyUserPassword( $userid, $password )
	{
		$db = Jfactory::getDbo();

		$query = $db->getQuery(true)
			->select('password')
			->from('#__users')
			->where('id=' . $db->quote($userid));

		$db->setQuery($query);
		$result = $db->loadResult();

		$match = false;

		if (!empty($result))
		{
			if (strpos($result, '$P$') === 0)
			{
				$phpass = new PasswordHash(10, true);

				$match = $phpass->CheckPassword($password, $result);
			}
			elseif (substr($result, 0, 4) == '$2y$')
			{
				$password60 = substr($result, 0, 60);

				if (JCrypt::hasStrongPasswordSupport())
				{
					$match = password_verify($password, $password60);
				}
			}
			elseif (substr($result, 0, 8) == '{SHA256}')
			{
				$parts = explode(':', $result);
				$crypt = $parts[0];
				$salt = @$parts[1];
				$testcrypt = JUserHelper::getCryptedPassword($password, $salt, 'sha256', false);

				$match = $result == $testcrypt;
			}
			else
			{
				$parts = explode(':', $result);
				$crypt = $parts[0];
				$salt = @$parts[1];

				$testcrypt = JUserHelper::getCryptedPassword($password, $salt, 'md5-hex', false);

				$match = $crypt == $testcrypt;
			}
		}

		return $match;
	}

	/**
	 * Reset user's completed fields count in #__social_users based on profile id.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3
	 * @access public
	 * @param  integer    $profileId The profile id to search.
	 * @return boolean               True if successful.
	 */
	public function resetCompletedFieldsByProfileId($profileId)
	{
		$query = "UPDATE `#__social_users` AS `a` LEFT JOIN `#__social_profiles_maps` AS `b` ON `a`.`user_id` = `b`.`user_id` SET `a`.`completed_fields` = 0 WHERE `b`.`profile_id` = '" . $profileId . "'";

		$db = FD::db();
		$sql = $db->sql();

		$sql->raw($query);

		$db->setQuery($sql);

		return $db->query();
	}

	/**
	 * get inactive users based on specify duration.
	 *
	 * @author Sam
	 * @since  1.4
	 * @access public
	 * @param  integer	duration (in days)
	 * @return array 	array of user ids
	 */
	public function getInactiveUsers($duration, $limit = 20)
	{
		$db = FD::db();
		$sql = $db->sql();

		$now    = FD::date();

		$query = "select a.`id`, a.`name`, a.`email` from `#__users` as a";
		$query .= " inner join `#__social_users` as b on a.`id` = b.`user_id`";
		$query .= " where a.`block` = 0";
		$query .= " and a.`lastvisitDate` != " . $db->Quote('00-00-00 00:00:00');
		$query .= " and date_add( a.`lastvisitDate`, INTERVAL $duration DAY ) <= " . $db->Quote($now->toMySQL());
		$query .= " and b.`reminder_sent` = 0";
		$query .= " limit $limit";

		$sql->raw($query);
		$db->setQuery($sql);

		$results = $db->loadObjectList();

		return $results;
	}

	public function updateReminderSentFlag($userId, $flag)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'update `#__social_users` set `reminder_sent` = ' . $db->Quote($flag);
		$query .= ' where user_id = ' . $db->Quote($userId);

		$sql->raw($query);
		$db->setQuery($sql);
		$db->query();

		return true;
	}


	public function updateJoomlaUserPasswordResetFlag($userId, $jFlag, $esFlag)
	{
		$db = FD::db();
		$sql = $db->sql();

		// Joomla user
		$query = 'update `#__users` set `requireReset` = ' . $db->Quote($jFlag);
		$query .= ' where `id` = ' . $db->Quote($userId);

		$sql->raw($query);
		$db->setQuery($sql);
		$db->query();

		// EasySocial User
		$this->updateUserPasswordResetFlag($userId, $esFlag);

		return true;
	}

	public function updateUserPasswordResetFlag($userId, $esFlag)
	{
		$db = FD::db();
		$sql = $db->sql();

		// EasySocial user
		$query = 'update `#__social_users` set `require_reset` = ' . $db->Quote($esFlag);
		$query .= ' where `user_id` = ' . $db->Quote($userId);

		$sql->raw($query);
		$db->setQuery($sql);
		$db->query();

		return true;
	}

	/**
	 * send reminder to inactive user.
	 *
	 * @author Sam
	 * @since  1.4
	 * @access public
	 * @param  array	array of users
	 * @return int number of users processed
	 */
	public function updateBlockInterval($users, $period)
	{
		$db = ES::db();
		$sql = $db->sql();

		if (! $users) {
			return false;
		}

		if (! is_array($users)) {
			$users = array($users);
		}

		$date = ES::date();

		$query = "update `#__social_users` set `block_period` = " . $db->Quote($period);
		if ($period == '0') {
			// clear the date
			$query .= ", `block_date` = " . $db->Quote('00-00-00 00:00:00');
		} else {
			$query .= ", `block_date` = " . $db->Quote($date->toMySQL());
		}

		if (count($users) > 1) {
			$query .= " where `user_id` IN (" . implode(',', $users) . ")";
		} else {
			$query .= " where `user_id` = " . $db->Quote($users[0]);
		}

		$sql->raw($query);
		$db->setQuery($sql);

		$state = $db->query();

		return $state;
	}

	public function getExpiredBannedUsers($userId = '')
	{
		$db = ES::db();
		$sql = $db->sql();

		$now = ES::date();

		$query = "select `user_id` from `#__social_users`";
		$query .= " where `state` = " . $db->Quote(SOCIAL_USER_STATE_DISABLED);
		$query .= " and `block_period` > 0";
		$query .= " and DATE_ADD(`block_date`, INTERVAL `block_period` MINUTE) <= " . $db->Quote($now->toMySQL());
		if ($userId) {
			$query .= " and `user_id` = " . $db->Quote($userId);
		}

		// echo $query;exit;

		$sql->raw($query);
		$db->setQuery($sql);

		$users = $db->loadColumn();

		return $users;
	}


	/**
	 * send reminder to inactive user.
	 *
	 * @author Sam
	 * @since  1.4
	 * @access public
	 * @param  array	array of users
	 * @return int number of users processed
	 */
	public function sendReminder($users)
	{
		$count = 0;
		$jConfig 	= FD::jConfig();
		$config = FD::config();

		if ($users) {

			// Push arguments to template variables so users can use these arguments
			$params 	= array(
								'loginLink'	=> FRoute::login(array() , false),
								'duration'	=> $config->get('users.reminder.duration', '30'),
								'siteName'	=> $jConfig->getValue('sitename')
							);

			foreach ($users as $user) {

				// Immediately send out emails
				$mailer 	= FD::mailer();

				// Set the user's name.
				$params[ 'recipientName' ]	= $user->name;

				// Get the email template.
				$mailTemplate	= $mailer->getTemplate();

				// Set recipient
				$mailTemplate->setRecipient($user->name, $user->email);

				// Set title
				$title = JText::sprintf('COM_EASYSOCIAL_EMAILS_INACTIVE_REMINDER_SUBJECT', $user->name);
				$mailTemplate->setTitle($title);

				// Set the template
				$mailTemplate->setTemplate('site/user/remind.inactive', $params);

				// Try to send out email to the admin now.
				$state 		= $mailer->create($mailTemplate);

				if ($state) {
					// need to update the reminder_sent flag
					$this->updateReminderSentFlag($user->id, '1');

					$count++;
				}

			}
		}

		return $count;
	}

}
