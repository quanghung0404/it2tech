<?php
/**
 * @package		Komento comment module
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

$helper	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

if( !JFile::exists( $helper ) )
{
	return;
}

// load all dependencies
require_once( $helper );
// KomentoDocumentHelper::loadHeaders();
KomentoDocumentHelper::load( 'module', 'css', 'assets' );
JFactory::getLanguage()->load( 'com_komento' , JPATH_ROOT );

// initialise all data
$profile	= Komento::getProfile();
$config		= Komento::getConfig();
$konfig		= Komento::getKonfig();

/* $params
limit
component
sort = latest/likes
filtersticked
showtitle
showcomponent
showavatar
showauthor
lapsedtime
maxcommenttext
maxtitletext */

// todo: filter by category

$model		= Komento::getModel( 'comments' );

$comments	= '';
$options	= array(
	'threaded'	=> 0,
	'sort'		=> $params->get( 'sort' ),
	'limit'		=> $params->get( 'limit' ),
	'sticked'	=> $params->get( 'filtersticked' ) ? 1 : 'all',
	'random'	=> $params->get( 'random' )
);

$component	= $params->get( 'component' );
$cid		= array();
$category	= $params->get( 'category' );

if( $component != 'all' && !empty( $category ) )
{
	$application	= Komento::loadApplication( $component );
	$cid			= $application->getContentIds( $category );

	if( count( $cid ) == 1 )
	{
		$cid = $cid[0];
	}
}
else
{
	$cid = 'all';
}

switch( $params->get( 'sort' ) )
{
	case 'latest':
		$comments = $model->getComments( $component, $cid, $options );
		break;
	case 'likes':
		$comments = $model->getPopularComments( $component, $cid, $options );
		break;
}

require( JModuleHelper::getLayoutPath( 'mod_komento_comments' ) );
