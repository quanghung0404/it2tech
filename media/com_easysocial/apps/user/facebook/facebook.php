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

// Include apps interface.
FD::import( 'admin:/includes/apps/apps' );

/**
 * Facebook application for EasySocial.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialUserAppFacebook extends SocialAppItem
{

	/**
	 * Responsible to process cron items for oauth items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onCronExecute()
	{
		// We'll temporarily disable this.
		return;

		$model 		= FD::model( 'OAuth' );

		// Load up facebook client
		$facebookClient 	= FD::oauth( 'facebook' );

		// Get a list of pullable items
		$oauthUsers			= $model->getPullableClients();

		if( !$oauthUsers )
		{
			return;
		}

		// Go through each of the pullable users
		foreach( $oauthUsers as $oauthUser )
		{
			// Simulate the user now by passing in their valid token.
			$facebookClient->setAccess( $oauthUser->token );

			// Get the stream items from Facebook
			$items 			= $facebookClient->pull();

// echo '<pre>';
// print_r( $items );
// echo '</pre>';
// exit;
			foreach( $items as $item )
			{
				// Store this into the stream now.
				$stream 		= FD::stream();

				// Get the stream template
				$template 		= $stream->getTemplate();
				$template->setActor( $oauthUser->uid , $oauthUser->type );
				$template->setContext( $item->get( 'id' ) , SOCIAL_TYPE_FACEBOOK );
				$template->setContent( $item->get( 'content' ) );
				$template->setVerb( 'update' );

				$template->setAccess('core.view');

				// Create the new stream item.
				$streamTable 		= $stream->add( $template );

				// Store into the stream assets table as the app needs this.
				$assets 			= FD::table( 'StreamAsset' );
				$assets->stream_id 	= $streamTable->id;
				$assets->type		= SOCIAL_TYPE_FACEBOOK;
				$assets->data 		= $item->toString();
				$assets->store();

				// Store into the import history.
				$history				= FD::table( 'OAuthHistory' );
				$history->remote_id		= $item->get( 'id' );
				$history->remote_type	= $item->get( 'type' );
				$history->local_id 		= $streamTable->id;
				$history->local_type 	= SOCIAL_TYPE_STREAM;
				$history->store();
			}

			// Update the last pulled item datetime.
			$oauthTable 	= FD::table( 'OAuth' );
			$oauthTable->bind( $oauthUser );

			$oauthTable->last_pulled	= FD::date()->toMySQL();
			$state 	= $oauthTable->store();

		}
	}
	/**
	 * Automatically pushes to Facebook when user creates a new story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @param	SocialTableStreamItem	The stream item.
	 * @return
	 */
	public function onAfterStorySave( $stream , $streamItem , $template )
	{
		// Get the current user's object.
		$my 	= FD::user();

		// Get the current user's oauth object.
		$oauth	= $my->getOAuth( SOCIAL_TYPE_FACEBOOK );

		if( !$oauth )
		{
			return;
		}

		$config 	= FD::config();

		// If user disabled this, we shouldn't push to the server.
		if( !$oauth->push || !$config->get( 'oauth.facebook.push' ) )
		{
			return;
		}

		$photos = JRequest::getVar( 'photos' );
		$photo	= null;

		if( $photos )
		{
			// Get the first picture
			$photoId	= $photos[0];

			$photo 	= FD::table( 'Photo' );
			$photo->load( $photoId );
		}

		// Get the single stream object
		$streamObj 		= FD::stream()->getItem( $streamItem->uid , true );

		// We don't want to process anything if there's a privacy issue.
		if( $streamObj === true )
		{
			return;
		}

		$streamObj 		= $streamObj[0];

		//check stream access.
		if ($streamObj->access > 30) {
			return;
		}

		$latitude 		= JRequest::getVar('locations_lat', '');
		$longitude 		= JRequest::getVar('locations_lng', '');
		$placeId 		= null;

		$client 	= FD::OAuth('Facebook');
		$client->setAccess($oauth->token);

		if( $latitude && $longitude )
		{
			$options 		= array( 'type' => 'place' , 'center' => $latitude . ',' . $longitude );

			$places 		= $client->api( '/search' , $options );

			// Get the first item
			$place 			= $places['data'][0];
			$placeId 		= $place[ 'id' ];
		}

		$link 	= false;

		// Get stream's content
		$content 	= strip_tags($streamObj->content_raw);

		// Check if the stream type is a link type
		if ($streamObj->type == 'links') {
			$link 	= $streamObj->getAssets();
			$link	= $link[ 0 ];
		} else {
			$link 	= FD::registry();

			$link->set('link', FRoute::stream(array('id' => $streamObj->uid, 'layout' => 'item', 'external' => true)));
			$link->set('title', '');
			$link->set('content', $content);
		}


		$id			= $client->push($content, $placeId, $photo, $link);

		// Once the push was successfull, we need to store this in the history table to prevent infinite loops.
		if (!$id) {
			return false;
		}

		// Store this into our history table to prevent infinite looping
		$history 	= FD::table( 'OAuthHistory' );
		$history->oauth_id		= $oauth->oauth_id;
		$history->remote_id		= $id;
		$history->remote_type	= 'status';
		$history->local_id 		= $streamItem->uid;
		$history->local_type 	= 'story';
		$history->store();
	}


	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	jos_social_stream, boolean
	 * @return  0 or 1
	 */
	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if( $item->context_type != SOCIAL_TYPE_FACEBOOK )
		{
			return false;
		}

		$item->cnt = 1;

		if( $includePrivacy )
		{
			$uid		= $item->id;
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );

			$sModel = FD::model( 'Stream' );
			$aItem 	= $sModel->getActivityItem( $item->id, 'uid' );

			if( $aItem )
			{
				$uid 	= $aItem[0]->id;

				if( !$privacy->validate( 'core.view', $uid , SOCIAL_TYPE_ACTIVITY , $item->actor_id ) )
				{
					$item->cnt = 0;
				}
			}
		}

		return true;
	}


	/**
	 * Trigger for onPrepareStream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// We only want to process related items
		if( $item->context != SOCIAL_TYPE_FACEBOOK )
		{
			return;
		}

		// Get current logged in user.
		$my         = FD::user();

		// Get user's privacy.
		$privacy 	= FD::privacy( $my->id );

		// Load the assets based on the stream id.
		$asset 		= FD::table( 'StreamAsset' );
		$asset->load( array( 'stream_id' => $item->uid ) );
		$params 	= FD::registry( $asset->data );

		// If "type" is not supplied, skip this
		if( !$params->get( 'type' ) )
		{
			return;
		}

		// var_dump( $params->toArray() );
		// Set the content
		$item->content 	= $this->formatMessage( $params );

		$this->set( 'item'	, $item );
		$this->set( 'params', $params );

		$file 		= 'streams/content.import.' . $params->get( 'type' );
		$contents 	= parent::display( $file );

		// Decoratre the stream item
		$item->display	= 'full';
		$item->title 	= parent::display( 'streams/title.import' );
		$item->content 	= $contents;
	}

	/**
	 * Formats the message
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatMessage( $params )
	{
		$withData	= $params->get( 'with_data' );

		if( $withData )
		{
			// dump( $withData );
		}

		$storyTags 	= $params->get( 'story_tags' );

		$message 	= $params->get( 'content' );

		if( $storyTags )
		{
			foreach( $storyTags as $tag )
			{
				$message 		= JString::substr_replace( $message , '<a href="' . $tag->link . '">' . $tag->name . '</a>', $tag->offset , $tag->length );
			}
		}

		return $message;
	}
}
