<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogContributor extends EasyBlog
{
	public $type = null;
	public $uid = null;

	static $items = array();


	public function load($id, $type)
	{
		$this->uid = $id;
		$this->type = $type;

		$index = $this->uid . $this->type;

		if (!isset(self::$items[$index])) {
			self::$items[$index] = $this->getItem($id, $type);
		}

		return self::$items[$index];
	}

	/**
	 * Retrieves the contributor item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItem()
	{
		require_once(__DIR__ . '/adapters/' . $this->type . '.php');

		$class = $this->getClassName();

		$obj = new $class($this->uid, $this->type);

		return $obj;
	}

	/**
	 * Retrieves the class name for the adapter
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getClassName()
	{
		if ($this->type == EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP) {
			return 'EasyBlogContributorEasySocialGroup';
		}

		if ($this->type == EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT) {
			return 'EasyBlogContributorEasySocialEvent';
		}

		if ($this->type == EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP) {
			return 'EasyBlogContributorJomsocialGroup';
		}

		if ($this->type == EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT) {
			return 'EasyBlogContributorJomsocialEvent';
		}

		return 'EasyBlogContributorTeamBlog';
	}

	/**
	 * generate contribution access sql that used with blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function genAccessSQL($contributorType, $columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$gid	= array();

		if ($my->id == 0) {
			$gid	= JAccess::getGroupsByUser(0, false);
		} else {
			$gid	= JAccess::getGroupsByUser($my->id, false);
		}

		$gids = '';
		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $id : ',' . $id;
			}
		}

		$sourceSQL = '';
		if ($contributorType == EASYBLOG_POST_SOURCE_TEAM) {
			$sourceSQL = self::getTeamBlogSQL($columnPrefix, $gids, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP) {
			$sourceSQL = self::getJomSocialGroupSQL($columnPrefix, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT) {
			$sourceSQL = self::getJomSocialEventSQL($columnPrefix, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP) {
			$sourceSQL = self::getEasySocialGroupSQL($columnPrefix, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT) {
			$sourceSQL = self::getEasySocialEventSQL($columnPrefix, $options);

		}

		$concate = isset($options['concateOperator']) ? $options['concateOperator'] : 'OR';

		$sql = '';
		if ($sourceSQL) {
			//starting bracket
			$sql = " $concate (";
			$sql .= $sourceSQL;
			//ending bracket
			$sql .= ")";
		}

		return $sql;
	}

	private static function getEasySocialGroupSQL($columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP);
		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__social_clusters` as srcesgroup";
		$query .= " where srcesgroup.`id` = $columnPrefix.`source_id` and srcesgroup.`cluster_type` = 'group'";
		$query .= " and srcesgroup.`type` = '1'";

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}

	private static function getEasySocialEventSQL($columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT);
		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__social_clusters` as srcesgroup";
		$query .= " where srcesgroup.`id` = $columnPrefix.`source_id` and srcesgroup.`cluster_type` = 'event'";
		$query .= " and srcesgroup.`type` = '1'";

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}


	private static function getJomSocialEventSQL($columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		// $queryEvent	= 'SELECT ' . $db->nameQuote( 'post_id' ) . ' FROM';
		// $queryEvent	.= ' ' . $db->nameQuote( '#__easyblog_external' ) . ' AS ' . $db->nameQuote( 'a' );
		// $queryEvent	.= ' INNER JOIN' . $db->nameQuote( '#__community_events' ) . ' AS ' . $db->nameQuote( 'b' );
		// $queryEvent	.= ' ON ' . $db->nameQuote( 'a' ) . '.uid = ' . $db->nameQuote( 'b' ) . '.id';
		// $queryEvent	.= ' AND ' . $db->nameQuote( 'a' ) . '.' . $db->nameQuote( 'source' ) . '=' . $db->Quote( 'jomsocial.event' );
		// $queryEvent	.= ' WHERE ' . $db->nameQuote( 'b' ) . '.' . $db->nameQuote( 'permission' ) . '=' . $db->Quote( 0 );

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT);
		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__community_events` as srcjsevent";
		$query .= " where srcjsevent.`id` = $columnPrefix.`source_id`";
		$query .= " and srcjsevent.`permission` = 0";

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}


	private static function getJomSocialGroupSQL($columnPrefix, $options = array())
	{
		$db = EB::db();

		// $queryJSGrp = 'select `post_id` from `#__easyblog_external_groups` as exg inner join `#__community_groups` as jsg';
		// $queryJSGrp .= '      on exg.group_id = jsg.id ';
		// $queryJSGrp .= '      where jsg.`approvals` = 0';
		//

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP);
		$mainQuery .= " and 1 <= (";


		$query = "select count(1) from `#__community_groups` as srcjsgroup";
		$query .= " where srcjsgroup.`id` = $columnPrefix.`source_id`";
		$query .= " and srcjsgroup.`approvals` = 0";

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}

	private static function getTeamBlogSQL($columnPrefix, $gids = '', $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$isAdminOnly = (isset($options['isAdminOnly'])) ? $options['isAdminOnly'] : false;


		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		if (isset($options['teamId']) && $options['teamId']) {
			$mainQuery .= " and $columnPrefix.source_id = " . $db->Quote($options['teamId']);
		}

		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__easyblog_team` as srcteam";
		$query .= " where srcteam.`id` = $columnPrefix.`source_id`";
		$query .= " and srcteam.`published` = 1";

		if ($isAdminOnly) {
			$query .= " AND (select count(1) from `#__easyblog_team_users` as srcteamuser where srcteamuser.`team_id` = srcteam.`id` and srcteamuser.`user_id` = " . $my->id ." and srcteamuser.`isadmin` = 1  ) > 0";
		} else {

			$query .= " and ( (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_EVERYONE .")";

			if ($gids) {
				$query .= "       OR (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_REGISTERED ." and (select count(1) from `#__easyblog_team_groups` as srcteamgrp where srcteamgrp.`team_id` = srcteam.`id` and srcteamgrp.`group_id` IN (" . $gids . ") ) > 0)";
			}

			if ($my->id) {
				$query .= "       OR (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_MEMBER ." and (select count(1) from `#__easyblog_team_users` as srcteamuser where srcteamuser.`team_id` = srcteam.`id` and srcteamuser.`user_id` = " . $my->id ."  ) > 0)";

				$query .= "       OR (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_MEMBER ." and (select count(1) from `#__easyblog_team_groups` as srcteamgrp where srcteamgrp.`team_id` = srcteam.`id` and srcteamgrp.`group_id` IN (" . $gids . ") ) > 0)";
			}
			$query .= ")";

		}


		$mainQuery .= $query;
		$mainQuery .= ')';


		return $mainQuery;
	}

}
