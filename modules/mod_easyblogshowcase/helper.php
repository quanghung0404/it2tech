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

class modEasyBlogShowcaseHelper
{
	/**
	 * Retrieves a list of items for the module
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function getItems($params)
	{
		$model = EB::model('Blog');

		// Determines if we should display featured or latest entries
		$type = $params->get('showposttype', 'featured');

		// Determines if we should filter by category
		$categoryId = $params->get('catid');

		$result = array();

		if ($categoryId) {
			$categoryId = (int) $categoryId;
		}

		$excludeIds = array();

		// If type equal to latest only, we need to exclude featured post as well
		if ($type == 'latestOnly') {
			// Retrieve a list of featured blog posts on the site.
			$featured = $model->getFeaturedBlog();

			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		$inclusion = '';

		// Get a list of category inclusions
		$inclusion	= EB::getCategoryInclusion($categoryId);

		$subCat = $params->get('subcat', 1);

		// Include child category in the inclusions
		if ($subCat && !empty($inclusion)) {

			$tmpInclusion = array();

			foreach ($inclusion as $includeCatId) {

				// Retrieve nested categories
				$category = new stdClass();
				$category->id = $includeCatId;
				$category->childs = null;

				EB::buildNestedCategories($category->id, $category);

				$linkage = '';
				EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

				$catIds = array();
				$catIds[] = $category->id;
				EB::accessNestedCategoriesId($category, $catIds);

				$tmpInclusion = array_merge($tmpInclusion, $catIds);
			}

			$inclusion = $tmpInclusion;
		}

		// Let's get the post now
		if (($type == 'all' || $type == 'latestOnly')) {
			$result = $model->getBlogsBy('', '', 'latest' , $params->get( 'count' ) , EBLOG_FILTER_PUBLISHED, null, null, $excludeIds, false, false, false, array() , $inclusion);
		}

		// If not latest posttype, show featured post.	
		if ($type == 'featured') {
			$result = $model->getFeaturedBlog($inclusion, $params->get('count'));
		}
	
		// If there's nothing to show at all, don't display anything
		if (!$result) {
			return $result;
		}

		$results = EB::formatter('list', $result);

		// Randomize items
		if ($params->get('autoshuffle')) {
			shuffle($results);
		}

		$contentKey	= $params->get('contentfrom', 'content');
		$textcount = $params->get('textlimit', '200');

		$posts = array();

		$layout = self::getPhotoLayout($params);

		foreach ($results as $post) {

			// $content = $post->getContent();
			$content = $post->getContentWithoutIntro('entry');


			// Get the content from the selected source
			if ($contentKey == 'intro') {
				$content = $post->getIntro(true);
			}

			// Truncate the content 
			if (JString::strlen(strip_tags($content)) > $textcount) {
				$content = JString::substr(strip_tags($content), 0, $textcount) . '...';
			} 

			$post->content = $content;

			$post->postCover = '';
	
			$post->photoLayout = '';

			if ($post->hasImage()) {
				$post->postCover = $post->getImage($layout->size);
			}

			if (!$post->hasImage() && $params->get('photo_legacy', true)) {
				$post->postCover = $post->getContentImage();
			}

			$post->postCoverLayout = $layout;

			$posts[] = $post;
		
		}

		return $posts;
	}

	public static function getPhotoLayout($params)
	{
		$layout = new stdClass();
		$layout->layout = $params->get('photo_layout');
		$layout->size = $params->get('photo_size', 'medium');
		$layout->alignment = $params->get('alignment', 'left');
		$layout->alignment = ($layout->alignment == 'default') ? 'left' : $layout->alignment;

		if (!$layout->layout) {
			$layout->layout = new stdClass();
			$layout->layout->width = 260;
			$layout->layout->height = 200;
			$layout->layout->crop = true;
		}

		return $layout;
	}
}
