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

class modEasyBlogListHelper
{
	public static function getPosts($params)
	{
		// Get the default sorting and ordering
		$sort = $params->get('sorting', 'latest');
		$order = $params->get('ordering', 'desc');

		// Get the total number of posts to display
		$limit = (int) trim($params->get('count', 0));

		// Determines if the user wants to filter items by specific ategories
		$catId = $params->get('catid');
		$categories = array();

		if ($catId) {
			$categories = explode(',', $catId);
		}

		$model = EB::model('Category');
		$result = $model->getPosts($categories, $limit);
		$posts = array();

		if (!$result) {
			return $posts;
		}

		$posts = EB::formatter('list', $result);

		return $posts;
	}
}
