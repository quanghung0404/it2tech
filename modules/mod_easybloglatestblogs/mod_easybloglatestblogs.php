<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
    return;
}
// @task: Include module's helper file.
require_once($engine);
require_once(__DIR__ . '/helper.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/modules/modules.php');

$config = EB::config();

// Either way, we need to load the module's script because we require responsive scripts to load on the page.
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

// @task: Load component's language file.
JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);
JFactory::getLanguage()->load('mod_easybloglatestblogs', JPATH_ROOT);

$app = JFactory::getApplication();
$my = JFactory::getUser();
$textcount = $params->get('textcount', 200);
$filterType = $params->get('type');
$bloggerLayout = false;
$layout = $params->get('alignment');
$columnCount = $params->get('column');
$disabled = $params->get('enableratings') ? false : true;

// Get the photo layout option
$photoLayout = $params->get('photo_layout');
$photoSize = $params->get('photo_size', 'medium');

$photoAlignment = isset($photoLayout->alignment) ? $photoLayout->alignment : 'center';

// If the full width photo is enabled, we strict the alignment to use center
if (isset($photoLayout->full)) {
	$photoAlignment = 'center';
}

// Filtering posts by blog author
if ($filterType == 1 || $filterType == 'author') {

	$authorId = (int) $params->get('bloggerlist', '');

	if (!$authorId) {
		echo JText::_('MOD_LATESTBLOGS_SELECT_BLOGGER');
		return;
	}

	// $posts = modLatestBlogsHelper::getPostByBlogger($params, $authorId);
	$posts = modLatestBlogsHelper::getLatestPost($params, $authorId, 'blogger');
	$bloggerLayout = true;
}

// Filtering posts by category
if ($filterType == 2 || $filterType == 'category') {
	$categoryId = $params->get('cid', '');

	if (!$categoryId) {
		echo JText::_('MOD_LATESTBLOGS_SELECT_CATEGORY');
		return;
	}

	// Load up the category table
	$category = EB::table('Category');
	$category->load($categoryId);

	// Check if the category is a private category. If it is private, ensure that the user has access
	// to view the category.
	if ($category->private != 0 && $my->guest) {
		$privacy = $category->checkPrivacy();

		if (!$privacy->allowed) {
			echo JText::_('MOD_LATESTBLOGS_CATEGORY_IS_CURRENTLY_SET_TO_PRIVATE');
			return;
		}
	}

	// Initialize the default list of categories.
	$catIds = array($category->id);

	// If configured to display subcategory items
	if ($params->get('includesubcategory', 0)) {

		// Why???
		$category->childs = null;

		// Build nested category level
		EB::buildNestedCategories($category->id, $category, false, true);
		EB::accessNestedCategoriesId($category, $catIds);
	}

	// Get the list of blog posts associated with the category
	$posts = modLatestBlogsHelper::getLatestPost($params, $catIds, 'category');

	// Determines if the admin wants to display the counter
	$total = count($posts);

	if ($total > 0 && $params->get('showccount', '')) {
		$model = EB::model('Category');
		$categoryTotalPostCnt = $model->getTotalPostCount($catIds);
	}

	// Let the template know which header we want to use.
	$templateForHeader = 'category';
}

// Filter blog posts by tags
if ($filterType == 3 || $filterType == 'tags') {

	$tagId = $params->get('tagid', '');

	// Ensure that the admin actually selected a tag
	if (empty($tagId)) {
		echo JText::_('MOD_LATESTBLOGS_SELECT_TAG');
		return;
	}

	// Load up the tag table
	$tag = EB::table('Tag');
	$tag->load($tagId);

	// Get the posts that are associated with the tag
	$posts = modLatestBlogsHelper::getLatestPost($params, $tag->id, 'tag');

	// Get the count when necessary
	$total = count($posts);

	if ($total > 0 && $params->get('showtcount', '')) {
		$tagTotalPostCnt = $tag->getPostCount();
	}

	// Let the template know which header we want to use
	$templateForHeader = 'tag';
}

// Filter blog posts by team blogs
if ($filterType == 4 || $filterType == 'team') {

	$teamId = $params->get('tid');

	// Ensure that the admin selected a team from the module settings
	if (!$teamId) {
		echo JText::_('MOD_LATESTBLOGS_SELECT_TEAM');
		return;
	}

	// Load up the team blog table
	$team = EB::table('TeamBlog');
	$team->load($teamId);

	// Determine if the current viewer is a member of the team
	$gids = EB::getUserGids();
	$team->isMember = $team->isMember($my->id, $gids);

	// Default set empty posts unless they can view it
	$posts = array();

	if ($team->access != 1 || $team->isMember) {
		$posts = modLatestBlogsHelper::getLatestPost($params, $teamId, 'team');
	}

	// Let the template know what header we want to use
	$templateForHeader = 'team';
}

// This will only get called when user is viewing the entry view of a blog post.
if ($filterType == 5 || $filterType == 'entry') {

	// Get the current view of the page
	$view = $app->input->get('view', '', 'cmd');

	// We need to have the id
	$id = $app->input->get('id', 0, 'int');

	// If the view is not entry, skip this
	if ($view != 'entry' || !$id) {
		return;
	}

	// Load up the blog table
	$blog = EB::table('Blog');
	$blog->load($id);

	// Ensure that the blog post has a proper author
	if (!$blog->created_by) {
		return;
	}

	// Now we load the posts
	$posts = modLatestBlogsHelper::getPostByBlogger($params, modLatestBlogsHelper::getBloggers($params, $blog->created_by));
}

// Filter blog posts by recent posts.
if ($filterType == 0 || $filterType == 'recent') {
	$posts = modLatestBlogsHelper::getLatestPost($params);
}

modLatestBlogsHelper::formatPost($posts, $params);

require(JModuleHelper::getLayoutPath('mod_easybloglatestblogs'));
