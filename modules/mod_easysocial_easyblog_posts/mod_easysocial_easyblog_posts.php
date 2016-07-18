<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main engine
$file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
$easyblog = JPATH_ROOT . '/administrator/components/com_easyblog/easyblog.php';

jimport( 'joomla.filesystem.file' );

if (!JFile::exists($file) || !JFile::exists($easyblog)) {
	return;
}

// Include the engine file.
require_once($file);

// Check if Foundry exists
if (!FD::exists()) {
	FD::language()->loadSite();
	echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
	return;
}

// Include EasyBlog's library
require_once(JPATH_ROOT . '/administrator/components/com_easyblog/includes/easyblog.php');

$my = ES::user();

// Load up the module engine
$modules = ES::modules('mod_easysocial_easyblog_posts');

$model = EB::model('Blog');

// Get the module options
$total = (int) $params->get( 'total' , 5 );
$sorting = $params->get( 'sorting' , 'latest' );

// Let's load the list of posts now
$posts = $model->getBlogsBy( 'latest' , '' , $sorting , $total );

// We need to format the blog post accordingly.
$posts = EB::formatter('list', $posts, false);

// Get the author of the blog posts
foreach ($posts as $post) {
	$post->user = ES::user($post->created_by);
}

// We need these packages
$modules->addDependency( 'css' , 'javascript' );

// Get the layout to use.
$layout = $params->get( 'layout' , 'default' );
$suffix = $params->get( 'suffix' , '' );

require(JModuleHelper::getLayoutPath('mod_easysocial_easyblog_posts', $layout));
