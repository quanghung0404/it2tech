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
defined('_JEXEC') or die('Unauthorized Access');

class SocialAccess
{
	/**
	 * The unique id that is associated with the access rules.
	 * @var	int
	 */
	private $uid = null;

	/**
	 * The unique type that is associated with the access rules.
	 * @var	string
	 */
	private $type = null;

	/**
	 * The Registry that stores the user access.
	 * @var Array
	 */
	public $access = null;

	/**
	 * Cache the default values so that it only load once.
	 * @var string
	 */
	public $default = null;

	public function __construct($id = null, $type = SOCIAL_TYPE_USER)
	{
		$this->loadAccess($id, $type);
	}

	private function loadAccess($id = null, $type = SOCIAL_TYPE_USER)
	{
		// This is to prevent unnecessary multiple loading per user id
		static $loadedAccess = array();

		$uid = null;

		// Perform data standardization

		// If type is profile, then we just directly use it as profile id
		if( $type === SOCIAL_TYPE_PROFILES )
		{
			$uid	= $id;
		}

		// If type is user then we deduce the profile id from the user
		if( $type === SOCIAL_TYPE_USER )
		{
			// Get the user object
			$my 	= FD::user($id);
			$uid	= $my->profile_id;

			$type	= SOCIAL_TYPE_PROFILES;
		}

		// clusters is the profiles equivalent
		// If type is groups category, then use the id directly
		if( $type === SOCIAL_TYPE_CLUSTERS )
		{
			$uid	= $id;
		}

		// If the type is group, then get the group category id from the group
		if( $type === SOCIAL_TYPE_GROUP )
		{
			// @TODO: Get the group category id
			$group	= FD::group( $id );
			$uid 	= $group->category_id;

			$type	= SOCIAL_TYPE_CLUSTERS;
		}

		if ($type === SOCIAL_TYPE_EVENT) {
			$event	= FD::event($id);
			$uid 	= $event->category_id;

			$type	= SOCIAL_TYPE_CLUSTERS;
		}

		$this->getDefaultValues($type);

		if (empty($loadedAccess[$type][$uid])) {

			// Load up the access based on the profile
			$model	 		= FD::model('Access');
			$storedAccess	= $model->getParams($uid, $type);

			// Merge all the group registries first.
			$registry 	= FD::registry($storedAccess);

			$loadedAccess[$type][$uid]	= $registry;
		}

		$this->access = $loadedAccess[$type][$uid];

		return $this;
	}

	/**
	 * Get default values from the files first.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultValues($type)
	{
		static $defaults = array();

		if (empty($defaults[$type])) {

			$model = FD::model('accessrules');

			// Convert the type to group type
			$group = SOCIAL_TYPE_USER;

			if ($type == SOCIAL_TYPE_GROUP || $type == SOCIAL_TYPE_CLUSTERS) {
				$group	= SOCIAL_TYPE_GROUP;
			}

			$options = array('group' => $group, 'state' => SOCIAL_STATE_PUBLISHED);
			$rules = $model->getAllRules($options);

			$registry = FD::registry();

			if (!empty($rules)) {
				foreach ($rules as $rule) {

					if (!isset($rule->default)) {
						$rule->default = true;
					}

					$registry->set($rule->name, $rule->default);
				}
			}

			$defaults[$type] = $registry;
		}

		$this->default = $defaults[$type];

		return $this->default;
	}

	/**
	 * Factory method to create a new access object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The unique id that is tied to the access.
	 * @param	int		The unique type that is tied to the access.
	 */
	public static function factory( $id = null, $type = SOCIAL_TYPE_USER )
	{
		$obj 	= new self( $id , $type );

		return $obj;
	}

	/**
	 * Detect if the user is allowed to perform specific actions.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function get($rule)
	{
		if (!$this->access) {
			return false;
		}

		// Get the default rule
		$default = $this->default->get($rule);

		// If rule is not found in access, then fallback to default
		// If rule is not found in default, then return null
		return $this->access->get($rule, $default);
	}

	/**
	 * Detect if the user is allowed to perform specific actions.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function allowed($rule, $default = true)
	{
		if (!$this->access) {
			return false;
		}

		// If rule is not found in access, then fallback to default
		// If rule is not found in default, then fallback to the provided default value
		return $this->access->get($rule , $this->default->get($rule, $default));
	}

	/**
	 * Determines if a rule item exceeded the usage.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The number of usage allowed
	 * @return	bool	True if allowed, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function exceeded($rule , $usage , $default = true)
	{
		// If rule is not found in access, then fallback to default
		// If rule is not found in default, then fallback to the provided default value
		$limit = (int) $this->access->get($rule, $this->default->get($rule, $default));

		// If limit is 0, we know it should be unlimited
		if ($limit == 0) {
			return false;
		}

		$exceeded	= $usage >= $limit;

		return $exceeded;
	}


	public function intervalExceeded($rule , $userId)
	{
		// If rule is not found in access, then fallback to default
		// If rule is not found in default, then fallback to the provided default value
		$limit = $this->access->get( $rule , $this->default->get($rule) );

		$value = 0;
		$interval = 0;

		if (is_object($limit)) {
			$value = $limit->value;
			$interval = $limit->interval;
		} else {
			// backward compatibility
			$value = $limit;
		}

		// If limit is 0, we know it should be unlimited
		if ($value == 0) {
			return false;
		}

		// we need to get usage here.
		$model = FD::model('AccessLogs');
		$usage = $model->getUsage($rule, $userId, $interval);

		$exceeded	= $usage >= $value;

		return $exceeded;
	}

	public function log($rule, $userId, $uid, $utype) {

		$log = FD::table('AccessLogs');

		$log->rule = $rule;
		$log->user_id = $userId;
		$log->uid = $uid;
		$log->utype = $utype;
		$log->created = FD::date()->toMySQL();

		$state = $log->store();
		return $state;
	}

	public function removeLog($rule, $userId, $uid, $utype) {

		$log = FD::table('AccessLogs');
		$state = $log->load(array('rule'=>$rule, 'user_id' => $userId, 'uid' => $uid, 'utype' => $utype));

		if ($state) {
			$state = $log->delete();
		}

		return $state;
	}

	public function switchLogAuthor($rule, $userId, $uid, $utype, $newUserId) {

		$log = FD::table('AccessLogs');
		$state = $log->load(array('rule'=>$rule, 'user_id' => $userId, 'uid' => $uid, 'utype' => $utype));

		if ($state) {
			$log->user_id = $newUserId;
			$state = $log->store();
		}

		return $state;
	}



}
