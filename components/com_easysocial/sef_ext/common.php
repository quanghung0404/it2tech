<?php
/**
* @package    EasySocial
* @copyright  Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

function addPrefix(&$title, $prefix)
{
	$title[] = $prefix;
}

// Determine how is the user's current id being set.
function addView(&$title, $view)
{
	$title[] = JString::ucwords(JText::_('COM_EASYSOCIAL_ROUTER_' . strtoupper($view)));

	shRemoveFromGETVarsList('view');
}

function addLayout( &$title , $view , $layout )
{
	$title[]	= JString::ucwords( JText::_( 'COM_EASYSOCIAL_ROUTER_' . strtoupper( $view ) . '_LAYOUT_' . strtoupper( $layout ) ) );

	shRemoveFromGETVarsList( 'layout' );
}

function stripExtensions( $title )
{
	// Remove known extensions from title
	$extensions = array( 'jpg' , 'png' , 'gif' );

	$title 	= JString::str_ireplace( $extensions , '' , $title );

	return $title;
}

function getAppAlias( $id )
{
	$app 		= FD::table( 'App' );
	$app->load( (int) $id );

	$alias 		= JFilterOutput::stringURLSafe( $app->alias );
	return $alias;
}

function getListAlias( $id )
{
	$list 		= FD::table( 'List' );
	$list->load( $id );

	$alias 		= JFilterOutput::stringURLSafe( $list->title );
	return $alias;
}

function getBadgeAlias($id)
{
	$badge = FD::table( 'Badge' );
	$badge->load( $id );

	$alias = JFilterOutput::stringURLSafe($badge->alias);
	return $alias;
}

function getVideoCategoryAlias($id)
{
	static $cats = array();

	if (!isset($cats[$id])) {

		$id = (int) $id;

		$category = ES::table('VideoCategory');
		$category->load($id);

		$cats[$id] = JString::ucwords($category->alias);
	}

	return $cats[$id];
}

function getGroupCategoryAlias($id)
{
	static $categories 	= array();

	// Ensure that the id is purely an integer
	if (!isset($categories[$id])) {

		$category 	= FD::table('GroupCategory');
		$category->load($id);

		$alias		= $category->getAlias();
		$alias		= str_ireplace(':', '-', $alias);

		$categories[$id]	= $alias;
	}

	return $categories[$id];
}

function getEventCategoryAlias($id)
{
	static $categories = array();

	// Ensure that the id is purely an integer
	if (!isset($categories[$id])) {

		$category = FD::table('EventCategory');
		$category->load($id);

		$alias = $category->getAlias();
		$alias = str_ireplace(':', '-', $alias);

		$categories[$id]	= $alias;
	}

	return $categories[$id];
}

function getVideoAlias($id)
{
	// Ensure that it's typecasted appropriately.
	$id = (int) $id;

	static $videos = array();

	if (!isset($videos[$id])) {
		$video = ES::table('Video');
		$video->load($id);

		$videos[$id] = JString::ucwords($video->getAlias(false));
	}

	return $videos[$id];
}

function getGroupAlias($id)
{
	static $groups 	= array();

	// Ensure that the id is purely an integer
	if (!isset($groups[$id])) {
		$group 	= FD::group($id);
		// We need to replace : with - since SH404 correctly processes it.
		$alias 	= $group->getAlias();
		$alias 	= str_ireplace(':', '-', $alias);

		$groups[$id]	= $alias;
	}

	return $groups[$id];
}

function getEventAlias($id)
{
	static $events 	= array();

	// Ensure that the id is purely an integer
	if (!isset($events[$id])) {
		$event = FD::event($id);
		// We need to replace : with - since SH404 correctly processes it.
		$alias = $event->getAlias();
		$alias = str_ireplace(':', '-', $alias);

		$events[$id]	= $alias;
	}

	return $events[$id];
}

function getUserAlias( $id )
{
	static $users 	= array();

	$id = (int) $id;

	if( !isset( $users[ $id ] ) )
	{
		$user 		= FD::user( $id );
		$config 	= FD::config();
		$alias 		= $user->username;

		if( $config->get( 'users.aliasName' ) == 'realname' )
		{
			$alias	= $user->id . '-' . $user->name;
		}
		else
		{
			$alias 	= $user->id . '-' . $user->username;
		}

		if( $user->permalink )
		{
			$alias 	= $user->permalink;
		}

		$users[ $id ]	= $alias;
	}

	return $users[ $id ];
}

function uniqueUrl( $title , $fragment )
{
	$i 	= 1;

	$url 	= implode( '/' , $title ) . '/' . $fragment;

	while( urlExists( $url ) )
	{
		$fragment 	= $fragment . '-' . $i;

		$url 	= $url . $fragment;
		$i++;
	}

	return $fragment;
}

function urlExists( $title )
{
	$url 	= $title;

	if( is_array( $title ) )
	{
		$url 	= implode( '/' , $title );
	}

	$db 	= FD::db();
	$sql	= $db->sql();
	$sql->select( '#__sh404sef_urls' );
	$sql->where( 'oldurl' , $url , '=' , 'OR' );
	$sql->where( 'oldurl' , $url . '.html' , '=' , 'OR' );

	$db->setQuery( $sql );

	$exists	= $db->loadResult() > 0 ? true : false;

	return $exists;
}
