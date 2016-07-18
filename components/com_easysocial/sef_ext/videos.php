<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Add the view to the list of titles
if (isset($view)) {
	addView($title, $view);
}

// Determine which type does the video belong to
if (isset($type)) {

	$title[] = JString::ucwords(JText::_('COM_EASYSOCIAL_SH404_TYPE_' . strtoupper($type)));
	
	if (isset($uid)) {

		if ($type == SOCIAL_TYPE_USER) {
			$alias = getUserAlias($uid);
		}

		if ($type == SOCIAL_TYPE_GROUP) {
			$alias = getGroupAlias($uid);
		} 

		if ($type == SOCIAL_TYPE_EVENT) {
			$alias = getEventAlias($uid);
		}

		$title[] = $alias;

		shRemoveFromGETVarsList('uid');
	}

	shRemoveFromGETVarsList('type');
}

// Sorting
if (isset($sort)) {
	$title[] = JString::ucwords($sort);
	shRemoveFromGETVarsList('sort');
}

// Filters
if (isset($filter)) {
	$title[] = JString::ucwords($filter);
	shRemoveFromGETVarsList('filter');
}

// Category alias
if (isset($categoryId)) {
	$title[] = getVideoCategoryAlias($categoryId);
	shRemoveFromGETVarsList('categoryId');
}

// For videos, we need to get the beautiful title
if (isset($id)) {

	// Get the video alias
	$alias = getVideoAlias($id);

	// Layouts
	if (isset($layout)) {
		addLayout($title, $view, $layout);
	}

	// Set the video alias
	$title[] = $alias;

	shRemoveFromGETVarsList('id'); 
}

// Layouts
if (isset($layout)) {
	addLayout($title, $view, $layout);
}