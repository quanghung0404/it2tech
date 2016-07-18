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
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

class modEasyBlogCategoriesHelper
{
	public static function getChildCategories( &$result , $params , &$categories, $level = 1 )
	{
	    $db = EB::db();
	    $my = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		$order = $params->get('order', 'popular');
		$sort = $params->get('sort', 'desc');
		$count = (INT)trim($params->get('count', 0));
		$hideEmptyPost = $params->get('hideemptypost', '0');
		$language = EB::getCurrentLanguage();

	    foreach($result as $row) {
	        if ($row->parent_id == 0) {
	            $categories[$row->id] = $row;
	            $categories[$row->id]->childs = array();
			} else {
			    $categories[ $row->id ]  = $row;
			    $categories[ $row->id ]->childs  = array();
			}

			$query	= 'SELECT ' . $db->qn('a.id') . ', ' . $db->qn('a.title') . ', ' . $db->qn('a.parent_id') . ', ' . $db->qn('a.alias') . ', ' . $db->qn('a.avatar') . ', COUNT(b.`id`) AS `cnt`'
					. ' , ' . $db->quote($level) . ' AS level'
					. ' FROM ' . $db->qn( '#__easyblog_category' ) . ' AS `a`'
					. ' LEFT JOIN '. $db->qn( '#__easyblog_post_category' ) . ' AS pc'
					. ' ON a.`id` = pc.`category_id`'
					. ' LEFT JOIN '. $db->qn( '#__easyblog_post' ) . ' AS b'
					. ' ON b.`id` = pc.`post_id`'
					. ' AND b.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED)
					. ' AND b.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

			$query	.= ' WHERE a.`published` = 1';
			$query  .= ' AND a.`parent_id`=' . $db->Quote( $row->id );

			if ($language) {
				$query .= 'AND(';
				$query .= ' a.' . $db->quoteName('language') . '=' . $db->Quote($language);
				$query .= ' OR';
				$query .= ' a.' . $db->quoteName('language') . '=' . $db->Quote('');
				$query .= ' OR';
				$query .= ' a.' . $db->quoteName('language') . '=' . $db->Quote('*');
				$query .= ')';
			}

			if (!$hideEmptyPost) {
				$query	.= ' GROUP BY a.`id` ';
			} else {
				$query	.= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';
			}

			if ($order == 'ordering') {
				$orderBy = ' ORDER BY `lft` asc';
			}
			if ($order == 'popular') {
				$orderBy = ' ORDER BY `cnt` desc';
			}
			if ($order == 'alphabet') {
				$orderBy = ' ORDER BY a.`title` asc';
			}
			if ($order == 'latest') {
				$orderBy = ' ORDER BY a.`created` desc';
			}

			$query .= $orderBy;

			$db->setQuery( $query );

			$records = $db->loadObjectList();

			if ($records) {
			    modEasyBlogCategoriesHelper::getChildCategories( $records , $params , $categories[ $row->id ]->childs, ++$level );

				// foreach ($records as $childrec) {
				// 	$categories[$row->id]->cnt += $childrec->cnt;
				// }
			}
		}
	}

	public static function getAvatar($category)
	{
		$categorytable = EB::table('Category');
		$categorytable->bind($category);

		return $categorytable->getAvatar();
	}

	public static function accessNestedCategories(&$categories, $selected, $params, $level = null)
	{
		$showCategoryAvatar = $params->get('showcavatar', true);
		$width = $params->get('avatarwidth', '50');
		$height = $params->get('avatarheight', '50');
		$showCount = $params->get('showcount', true);
		
		foreach($categories as $category)
		{
			if (is_null($level)) {
				$level 	= 0;
			}

			$css = '';

			if ($category->id == $selected) {
				$css = 'font-weight: bold;';
			}

			if ($params->get('layouttype') == 'tree') {
				// $category->level	-= 1;
				$padding	= $level * 30;
			}

			require(JModuleHelper::getLayoutPath('mod_easyblogcategories', 'item'));

			if ($params->get('layouttype') == 'tree' || $params->get('layouttype') == 'flat') {
				if (isset($category->childs) && is_array($category->childs)) {

					// Since running the iteration will invert the ordering, we'll need to reverse it back.
					$category->childs = array_reverse($category->childs);
				    modEasyBlogCategoriesHelper::accessNestedCategories( $category->childs , $selected, $params ,  $level + 1 );
				}
			}
		}
	}

	public static function accessNestedToggleCategories(&$category)
	{
		require(JModuleHelper::getLayoutPath('mod_easyblogcategories', 'toggle_item'));
	}

}
