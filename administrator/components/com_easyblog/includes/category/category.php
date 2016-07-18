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

class EasyBlogCategory extends EasyBlog
{

	/**
	 * generate category access sql that used with blogs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function genAccessSQL($columnId, $options = array(), $acl = CATEGORY_ACL_ACTION_VIEW)
	{
		$gid = array();

		if ($this->my->guest) {
			$gid = JAccess::getGroupsByUser(0, false);
		} else {
			$gid = JAccess::getGroupsByUser($this->my->id, false);
		}

		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
			}
		}

		$excludeCatSQL = '';
		$includeCatSQL = '';
		$typeCatSQL = '';
		$statCatSQL = '';

		if ($options) {
			if (isset($options['exclude']) && $options['exclude']) {

				if (is_array($options['exclude'])) {
					$options['exclude'] = array_unique($options['exclude']);
				}

				if (is_array($options['exclude']) && count($options['exclude']) > 1) {
					$excludeCatSQL = " AND cat.`id` NOT IN (" . implode(',', $options['exclude']) . ")";
				} else {
					$excludeCatSQL = (is_array($options['exclude'])) ? " AND cat.`id` != " . $options['exclude'][0] : " AND cat.`id` != " . $options['exclude'];
				}
			}

			if (isset($options['include']) && $options['include']) {

				if (is_array($options['include'])) {
					$options['include'] = array_unique($options['include']);
				}

				if (is_array($options['include']) && count($options['include']) > 1) {
					$includeCatSQL = " AND cat.`id` IN (" . implode(',', $options['include']) . ")";
				} else {
					$includeCatSQL = (is_array($options['include'])) ? " AND cat.`id` = " . $options['include'][0] : " AND cat.`id` = " . $options['include'];
				}
			}

			if (isset($options['type']) && $options['type']) {

				if (is_array($options['type'])) {
					$options['type'] = array_unique($options['type']);
				}

				if (is_array($options['type']) && count($options['type']) > 1) {
					$typeCatSQL = " AND cat.`id` IN (" . implode(',', $options['type']) . ")";
				} else {
					$typeCatSQL = (is_array($options['type'])) ? " AND cat.`id` = " . $options['type'][0] : " AND cat.`id` = " . $options['type'];
				}
			}

			if (isset($options['statType']) && $options['statType']) {
				$statCatSQL = " AND cat.`id` = " . $options['statType'];
			}
		}

		//starting bracket
		$sql = "1 <= (";

		$sql .= "select count(1) from `#__easyblog_post_category` AS acp";
		$sql .= " INNER JOIN `#__easyblog_category` as cat on acp.`category_id` = cat.`id`";
		$sql .=	" where acp.`post_id` = $columnId";
		$sql .= $typeCatSQL;
		$sql .= $statCatSQL;
		$sql .= $includeCatSQL;
		$sql .= $excludeCatSQL;
		$sql .= " and (";
		$sql .= " 	( cat.`private` = 0 ) OR";
		$sql .= " 	( (cat.`private` = 1) and (" . $this->my->id . " > 0) ) OR";
		$sql .= " 	( (cat.`private` = 2) and ( (select count(1) from `#__easyblog_category_acl` as cacl where cacl.`category_id` = cat.id and cacl.`acl_id` = $acl and cacl.`content_id` in ($gids)) > 0 ) )";
		$sql .= " )";
		//ending bracket
		$sql .= " )";

		return $sql;
	}


	/**
	 * generate category access for sql used only in categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function genCatAccessSQL($column, $columnId, $acl = CATEGORY_ACL_ACTION_VIEW)
	{
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

		$sql = "(";
		$sql .= " ( $column = 0) OR";
		$sql .= " ( $column = 1 and 1 > 0) OR";
		$sql .= " ( $column = 2 and (select count(1) from `#__easyblog_category_acl` where `category_id` = $columnId and `acl_id` = $acl and `content_id` in ($gids)) > 0)";
		$sql .= ")";

		return $sql;
	}

	public static function addChilds(&$parent, $items)
	{
		// preform safety checking here.
		if (! $items) {
			return false;
		}

		foreach($items as $cItem) {
			if ($cItem->parent_id == $parent->id) {

				$tmpParent = $cItem;
				$tmpParent->childs = array();

				self::addChilds($tmpParent, $items);

				$parent->childs[] = $tmpParent;
			}
		}

		return false;
	}

}
