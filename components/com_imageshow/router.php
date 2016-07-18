<?php
/**
 * @version    $Id$
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

function ImageShowBuildRoute(&$query)
{
	$segments 	= array ();

	// get a menu item based on Itemid or currently active
	$app = JFactory::getApplication();
	$db	 = JFactory::getDBO();

	if (isset($query['view']))
	{
		$segments [] = $query['view'];
		unset($query['view']);
	}

	if (isset($query['showlist_id']))
	{
		$segments [] = 'slid:' . $query['showlist_id'];
		unset($query['showlist_id']);
	}

	if (isset($query['showcase_id']))
	{
		$segments [] = 'scid:' . $query['showcase_id'];
		unset($query['showcase_id']);
	}

	if (isset($query['show_breadcrumbs']))
	{
		$segments [] = 'sbcrumbs:' . $query['show_breadcrumbs'];
		unset($query['show_breadcrumbs']);
	}

	return $segments;
}

function ImageshowParseRoute($segments)
{
	$vars 		= array();
	$defined 	= array('slid'=>'showlist_id', 'scid'=>'showcase_id', 'sbcrumbs'=>'show_breadcrumbs');

	if (isset($segments[0]))
	{
		$vars['view'] = $segments[0];
		unset($segments[0]);
	}

	foreach($segments as $segment)
	{
		$value = explode(":", $segment);
		if (isset($defined[$value[0]]))
		{
			$vars[$defined[$value[0]]] = $value[1];
		}
	}
	return $vars;
}
?>