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
$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

jimport( 'joomla.filesystem.file' );

if( !JFile::exists( $file ) )
{
	return;
}

// Include the engine file.
require_once( $file );

// Check if Foundry exists
if( !FD::exists() )
{
	FD::language()->loadSite();
	echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
	return;
}

$config 	= FD::config();

// If photos is not enabled, do not display the albums
if( !$config->get( 'photos.enabled' ) )
{
	return;
}

FD::document()->init();

$my = FD::user();


// Load up the module engine
$modules = FD::modules('mod_easysocial_albums');
$modules->loadComponentStylesheets();

// We need these packages
$modules->addDependency( 'css' , 'javascript' );

// Get the layout to use.
$layout = $params->get( 'layout' , 'default' );
$suffix = $params->get( 'suffix' , '' );

// module setting
$withCover = $params->get( 'withCover' , 0 );
$limit = $params->get( 'total' , 6 );

$userid = (int) $params->get('userid', 0);
$albumid = (int) $params->get('albumid', 0);

$options = array( 'core' => false, 'withCovers' => $withCover, 'limit' => $limit, 'order' => 'created', 'direction' => 'desc', 'excludeblocked' => 1, 'privacy' => true);

if ($userid) {
    $options['userId'] = (int) $userid;
}

if ($albumid) {
    $options['albumId'] = (int) $albumid;
}

// Retrieve recent albums from the site.
$albumsModel	= FD::model( 'Albums' );
$recentAlbums   = $albumsModel->getAlbums('' , '' , $options );

if ($recentAlbums) {
    $photoIds = array();

    foreach($recentAlbums as $album) {
        if ($album->cover_id) {
            $photoIds[] = $album->cover_id;
        }
    }

    if ($photoIds) {
        FD::cache()->cachePhotos($photoIds);
    }
}


require( JModuleHelper::getLayoutPath( 'mod_easysocial_albums' , $layout ) );
