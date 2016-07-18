<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAcl extends EasyBlog
{
	/**
	 * Retrieves the ACL ruleset
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getRuleSet($cid = '')
	{
		static $data = null;

		$my = JFactory::getUser($cid);

		// If the data is already stored, we just load back the data.
		if (isset($data[$my->id])) {
			return $data[$my->id];
		}

		$rulesets = new EasyBlogAclRuleset();

		// Retrieve rules
		$rules = self::getRules('id');

		$db = EB::db();

		if (!empty($my->id)) {

			$rulesets->id = $my->id;
			$rulesets->name = $my->name;
			$rulesets->group = isset($my->usertype) ? $my->usertype : '';

			// @Task: Load default values.
			foreach ($rules as $rule) {
				$rulesets->rules->{$rule->action} = (int) $rule->default;
			}

			$result = array();

			// Get user's joomla usergroups ids.
			$groupIds = '';
			$query = 'SELECT `group_id` FROM `#__user_usergroup_map` WHERE `user_id` = ' . $db->Quote($my->id);
			$db->setQuery($query);

			$groupIds = $db->loadColumn();

			if(! $groupIds) {
				// something went wrong. this user did not have any Joomla group ;(
				$groupIds = array('1'); // default to Public group.
			}


			$groups = array();

			$grpIds = implode(', ', $groupIds);
			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_group' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'content_id' ) . ' IN (' . $grpIds . ') '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );

			$db->setQuery($query);
			$results = $db->loadObjectList();

			foreach($results as $item) {
				$groups[$item->content_id][] = $item;
			}

			// Allow explicit overrides in the groups
			// If user A is in group A (allow) and group B (not allowed) , user A should be allowed
			$result = array();

			foreach ($groups as $group) {
				foreach ($group as $rule) {
					if (!isset($result[0][$rule->acl_id])) {
						$result[0][ $rule->acl_id ]	= new stdClass();
					}

					if (isset( $result[0][ $rule->acl_id]->acl_id ) && $result[0][$rule->acl_id]->status != '1' || !isset($result[0][$rule->acl_id]->acl_id)) {
						$result[0][$rule->acl_id]->acl_id	= $rule->acl_id;
						$result[0][$rule->acl_id]->status	= $rule->status;
					}
				}
			}

			$rulesets = self::mapRules($result, $rules, $rulesets);

		} else {
			// Cannot rely on JComponentHelper::getParams( 'com_users' )->get( 'guest_usergroup' ) because Joomla 3.2.0 onwards always returns 13.
			$user = JFactory::getUser();
			$tmp  = $user->getAuthorisedGroups();
			$gid  = $tmp[0];

			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_group' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $gid ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );

			$db->setQuery($query);
			$groups[] = $db->loadObjectList();

			// Allow explicit overrides in the groups
			// If user A is in group A (allow) and group B (not allowed) , user A should be allowed
			$result		= array();

			foreach ($groups as $group) {
				foreach ($group as $rule) {

					if( !isset( $result[0][ $rule->acl_id ] ) ) {
						$result[0][ $rule->acl_id ]	= new stdClass();
					}

					if( isset( $result[0][ $rule->acl_id]->acl_id ) && $result[0][ $rule->acl_id ]->status != '1' || !isset( $result[0][ $rule->acl_id]->acl_id)) {
						$result[0][ $rule->acl_id ]->acl_id	= $rule->acl_id;
						$result[0][ $rule->acl_id ]->status	= $rule->status;
					}
				}
			}

			$rulesets = self::mapRules($result, $rules, $rulesets);
		}

		$rulesData[$my->id]	= $rulesets;

		return $rulesData[$my->id];
	}

	/**
	 * Retrieves a list of filtered html tags
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getFilterTags()
	{
		$my = JFactory::getUser();
		$gids = EB::getUserGids($my->id);

		if (!$gids) {
			return false;
		}

		$filter = '';

		foreach ($gids as $gid) {
			$result = self::getFilterRule($gid);

			if ($result !== false) {
				$filter = $result;
			}
		}

		$tags = array();

		if ($filter) {
			$tags = $filter->get('disallow_tags');
			$tags = strtolower($tags);
			$tags = explode(',', $tags);
		}

		return $tags;
	}

	/**
	 * Retrieves a list of filter attributes
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getFilterAttributes()
	{
		$my = JFactory::getUser();
		$gids = EB::getUserGids($my->id);

		$filter = '';

		foreach ($gids as $gid) {
			$result = self::getFilterRule($gid);

			if ($result !== false) {
				$filter = $result;
			}
		}

		$atts = array();

		if ($filter) {
			$atts = $filter->get('disallow_attributes');
			$atts = strtolower($atts);
			$atts = explode(',', $atts);
		}

		return $atts;
	}

	/**
	 * Retrieve the rule
	 *
	 */
	private static function getFilterRule($contentId)
	{
		static $_cache = array();

		if (! isset($_cache[$contentId])) {

			$db 	= EasyBlogHelper::db();
			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_filters' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $contentId ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );
			$db->setQuery( $query );

			$result	= $db->loadObject();

			if (!$result) {
				$_cache[$contentId] = false;
			} else {
				$filter 	= EB::table('AclFilter');
				$filter->bind( $result );

				$_cache[$contentId] = $filter;
			}
		}

		return $_cache[$contentId];
	}


	private static function mapRules( $result , $rules , $rulesets )
	{
		foreach( $result as $items )
		{
			foreach( $items as $rule )
			{
				if( isset( $rules[ $rule->acl_id ] ) )
				{
					$action	= $rules[ $rule->acl_id ]->action;

					if(isset($rulesets->rules->{$action}))
					{
						// 'No' explicitly win
						if($rulesets->rules->{$action} == '0')
							continue;
						else
							$rulesets->rules->{$action}	= $rule->status;
					}
					else
					{
						$rulesets->rules->{$action}	= $rule->status;
					}
				}
			}
		}

		return $rulesets;
	}

	/**
	 * Retrieves a list of rules
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private static function getRules()
	{
		static $data = null;

		$db = JFactory::getDbo();

		if (!$data) {
			$query = array();
			$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_acl');
			$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
			$query[] = 'ORDER BY ' . $db->qn('id') . ' ASC';

			$query = implode(' ', $query);

			$db->setQuery($query);

			$result = $db->loadObjectList();

			if ($result) {
				foreach ($result as $row) {
					$data[$row->id]	 = $row;
				}
			}
		}

		return $data;
	}
}

class EasyBlogAclRuleset
{
	public $rules = null;

	public function __construct()
	{
		$this->rules = new stdClass();
	}

	public function get($rule)
	{
		if (isset($this->rules->$rule)) {
			return $this->rules->$rule;
		}

		// By default return false
		return false;
	}
}
