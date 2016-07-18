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
require_once(__DIR__ . '/location.php');

$my = JFactory::getUser();
$config = EB::config();

// Ensure that all script are loaded in the site.
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();
EB::stylesheet('site')->attach();

// Get the posts
$posts = modEasyBlogPostMapHelper::getPosts($params);

// Sort the posts
$posts = modEasyBlogPostMapHelper::sortLocation($posts);

$totalPosts = count($posts);

if ($posts) {

	$locations = array();

	// always store first location
	$locations[] = new modEasyBlogMapLocation($posts[0]);

	// store previous post by reference
	$previousPost = $locations[0];

	// start from second location to check
	for ($i = 1; $i < $totalPosts; $i++) {
		
		$post =& $posts[$i];
		$postObj = new modEasyBlogMapLocation ($post);

		if (modEasyBlogPostMapHelper::sameLocation($post, $previousPost)) {
			$previousPost->content .= $postObj->content;
			$previousPost->ratingid[] = $postObj->id;
		} else {
			$locations[] = $postObj;
			$previousPost = $locations[count($locations) - 1];
		}
	}
}

$language = $params->get('language', 'en');
$zoom = $params->get('zoom', 15);
$fitBounds = $params->get('fitbounds', 1);
$mapUi = $params->get('mapui', 0) == 1? "false" : "true";
$mapWidth = $params->get('mapwidth');
$mapHeight = $params->get('mapheight');

// Load language file of extension
EB::loadLanguages();

// Generate a unique uid for this module
$uid = uniqid();

require(JModuleHelper::getLayoutPath('mod_easyblogpostmap'));
