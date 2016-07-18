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

require_once( dirname( __FILE__ ) . '/abstract.php' );

class SocialGroupSharesHelperPhotos extends SocialGroupSharesHelper
{
	public function getContent()
	{
		$message 	= $this->formatContent( $this->share->content );

		// Load the photo object
		$photo = FD::table( 'Photo' );
		$photo->load( $this->share->uid );


		// group access checking
		$group	= FD::group( $this->item->cluster_id );

		if (!$group) {
			return;
		}

		// Test if the viewer can really view the item
		if (!$group->canViewItem()) {
			return;
		}

		// Get the photo app params
		$app 		= FD::table('App');
		$app->load(array('element' => 'photo', 'group' => 'group'));

		$params 	= $app->getParams();

		$theme 		= FD::themes();
		$theme->set('params', $params);
		$theme->set( 'photo'		, $photo );
		$theme->set( 'message'		, $message );

		$html	= $theme->output( 'apps/group/shares/streams/photos/content' );

		return $html;
	}

	public function getLink()
	{
		$link = FRoute::photos( array( 'id' => $this->item->contextId ) );

		return $link;
	}

	public function getTitle()
	{
		$actors = $this->item->actors;
		$names  = FD::get( 'String' )->namesToStream( $actors, true, 3 );

		$sourceId = $this->share->uid;

		$photo = FD::table( 'Photo' );
		$photo->load( $sourceId );

		$photoCreator	= FD::user( $photo->user_id );

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'names'	, $names );
		$theme->set( 'photo' 	, $photo );
		$theme->set( 'creator'	, $photoCreator );

		$title	= $theme->output( 'apps/group/shares/streams/photos/title' );

		return $title;
	}
}
