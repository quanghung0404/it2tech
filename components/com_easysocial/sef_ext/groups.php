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


if( isset( $view ) )
{
	addView( $title , $view );
}

if( isset( $layout ) )
{
	addLayout( $title , $view , $layout );
}

// Determine how is the user's current id being set.
if( isset( $id ) && isset($view) && $view == 'groups' && isset($layout) && $layout == 'item' )
{
	$title[]	= getGroupAlias( $id );

	shRemoveFromGETVarsList( 'id' );
}

if (isset($categoryid)) {
	$title[]	= getGroupCategoryAlias($categoryid);

	shRemoveFromGETVarsList( 'categoryid' );
}

// Get the category alias
if( isset( $id ) && isset($view) && $view == 'groups' && isset($layout) && $layout == 'category')
{
	$category 	= FD::table('GroupCategory');
	$category->load($id);

	$alias		= $category->getAlias();
	$alias		= str_ireplace(':', '-', $alias);

	$title[]	= $alias;

	shRemoveFromGETVarsList( 'id' );
}

if (isset($appId) && $appId && $view == 'groups' && $layout == 'item') {

	$title[]	= getAppAlias($appId);

	shRemoveFromGETVarsList('appId');
}
