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

jimport('joomla.system.file');
jimport('joomla.system.folder');

class modLatestBlogsHelper
{
	/**
	 * Retrieves a list of posts created by a list of specified authors.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function getPostByBlogger(&$params, $authorId)
	{
		$db = EB::db();
		$config = EB::config();

		// Get the profile of the user.
		$author = EB::user($authorId);

		// Default posts to empty.
		$author->posts = array();

		$author->posts = modLatestBlogsHelper::getLatestPost($params, $author->id, 'blogger');
		
		return $author;
	}

	static function getLatestPost(&$params, $id = null, $type = 'latest')
	{
		$db = EB::db();
		$config = EB::config();
		$count = (int) $params->get('count', 0);

		$model = EB::model('Blog');

		$posts = '';

		$sort = $params->get('sortby', 'latest') == 'latest' ? 'latest' : 'modified';

		switch( $type )
		{
		    case 'blogger':
		    	$posts = $model->getBlogsBy('blogger', $id, $sort, $count, EBLOG_FILTER_PUBLISHED, null, false);
		    	break;
		    case 'category':
		    	$posts = $model->getBlogsBy('category', $id, $sort, $count, EBLOG_FILTER_PUBLISHED, null, false);
		    	break;
		    case 'tag':
		    	$posts	= $model->getTaggedBlogs($id, $count);
		    	break;
		    case 'team':
		    	$posts	= $model->getBlogsBy('teamblog', $id, $sort, $count, EBLOG_FILTER_PUBLISHED, null, false);
		    	break;
		    case 'latest':
		    default:
				if ($params->get('usefeatured')) {
					$posts = $model->getFeaturedBlog(array(), $count);
				} else {
					$categories	= EB::getCategoryInclusion($params->get('catid'));
					$catIds = array();

					if (!empty($categories)) {
						if (!is_array($categories)) {
							$categories	= array($categories);
						}

						foreach($categories as $item) {
							$category = new stdClass();
							$category->id = trim( $item );

							$catIds[] = $category->id;

							if ($params->get('includesubcategory', 0)) {
								$category->childs = null;
								EB::buildNestedCategories($category->id, $category , false , true );
								EB::accessNestedCategoriesId($category, $catIds);
							}
						}

						$catIds = array_unique( $catIds );
					}

					$cid = $catIds;

					if (!empty($cid)) {
						$type = 'category';
					}

					$postType = null;

					if ($params->get('postType') != 'all') {
						$postType = $params->get('postType');
					}

					$posts = $model->getBlogsBy($type, $cid, array($sort, 'DESC'), $count, EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, true, array(), $cid, $postType);
				}
				break;
		}

		if (count($posts) > 0) {
            $posts = EB::modules()->processItems($posts, $params);
		}

		return $posts;
	}

	static function getBloggers(&$params , $bloggerList = '')
	{
		$db = EB::db();
		$my = JFactory::getUser();

		if (empty($bloggerList) || !$bloggerList) {
			$bloggerList = $params->get('bloggerlist','');
		}

		$bloggers = explode(',', $bloggerList);
		$arrBloggers = '';

		for ($i = 0; $i < count($bloggers); $i++) {
		    $blogger = $bloggers[$i];
		    $blogger = trim($blogger);

		    if (is_numeric($blogger)) {
		        $arrBloggers[] = $blogger;
		    }
		}

		$bloggerListType = $params->get('bloggerlisttype',''); // include/exclude

		$model = EB::model('Blogger');

		if ($bloggerListType == 'include') {
			return $model->getBloggers('latest', 0, 'showallblogger' , '',  $arrBloggers);
		} else {
			return $model->getBloggers('latest', 0, 'showallblogger' , '', array(), $arrBloggers);
		}
	}

	public static function formatPost(&$posts, &$params)
	{
		// Removing duplicate images for legacy image behavior.
		$images = array();
		foreach ($posts as $post) {
			if ($post->posttype != 'quote' && $params->get('showintro', '-1') != '-1') { 
				if ($params->get('photo_legacy', 0)) {
					if ($post->protect) { 
						$post->content = self::replaceImage($post->content);
					}

					if (!$post->protect) {
						$post->summary = self::replaceImage($post->summary);
					}
				}
	 		}
		}
	}

	public static function replaceImage($content)
	{
		preg_match("/<img[^>]+\>/i", $content, $matches);
		if ($matches) {
			$content = str_replace($matches[0], '', $content);
		}
		return $content;
	}
}
