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

class SocialSharesHelperStream extends SocialSharesHelper
{
	public function getContent()
	{
		$source 	= explode( '.', $this->share->element );
		$element 	= $source[0];
		$group 		= $source[1];

		$message 	= $this->formatContent( $this->share->content );
		$preview    = "";
		$content 	= "";
		$title 		= "";

		$stream = FD::stream();
		$data 	= $stream->getItem( $this->share->uid );

		$theme 		= FD::themes();

		if( $data !== true && !empty( $data ) )
		{
			$title 		= $data[0]->title;
			$content 	= $data[0]->content;

			if( isset( $data[0]->preview ) && $data[0]->preview )
			{
				$preview = $data[0]->preview;
			}
		} else {
			$preview = $theme->output('site/stream/restricted.text');
		}


		$theme->set( 'message' 	, $message );
		$theme->set( 'content'	, $content );
		$theme->set( 'preview'	, $preview);
		$theme->set( 'title'	, $title );

		$html	= $theme->output( 'apps/user/shares/streams/stream/content' );

		return $html;
	}

	public function getLink()
	{
		$link = FRoute::stream( array( 'layout' => 'item', 'id' => $this->item->contextId ) );

		return $link;
	}

	public function getTitle()
	{
		$actors = $this->item->actors;

		$names 	= FD::string()->namesToStream( $actors, true , 3 );

		// Get the source id
		$sourceId = $this->share->uid;

		// Load the stream
		$stream		= FD::table( 'Stream' );
		$stream->load( $sourceId );

		if( ! $stream->id )
		{
			return '';
		}

		$link		= FRoute::stream( array( 'layout' => 'item', 'id' => $sourceId ) );

		// Get the target user.
		$target		= FD::user( $stream->actor_id );
		$actor 		= $actors[0];

		$theme 		= FD::get( 'Themes' );

		$theme->set('actor', $actor);
		$theme->set('link', $link);
		$theme->set('target', $target);

		$title		= $theme->output( 'apps/user/shares/streams/stream/title' );

		return $title;
	}


}
