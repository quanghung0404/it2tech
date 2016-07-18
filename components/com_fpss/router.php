<?php
/**
 * @version		$Id: router.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

function FPSSBuildRoute(&$query)
{
	$segments = array();
	if (isset($query['task']))
	{
		$task = $query['task'];
		$segments[] = $task;
		unset($query['task']);
	}
	if (isset($query['id']))
	{
		$id = $query['id'];
		$segments[] = $id;
		unset($query['id']);
	}
	if (isset($query['url']))
	{
		$url = $query['url'];
		$segments[] = $url;
		unset($query['url']);
	}
	return $segments;
}

function FPSSParseRoute($segments)
{
	$vars = array();
	$vars['task'] = $segments[0];
	$vars['id'] = $segments[1];
	if (isset($segments[2]))
	{
		$vars['url'] = $segments[2];
	}
	return $vars;
}
