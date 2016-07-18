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


jimport( 'joomla.filesystem.file' );

require_once( SOCIAL_LIB . '/migrators/helpers/info.php' );

/**
 * DB layer for EasySocial.
 *
 * @since	1.4
 * @author	Sam <sam@stackideas.com>
 */
class SocialMigratorHelperJomsocialVideo
{
	// component name, e.g. com_community
	var $name  			= null;

	// migtration steps
	var $steps 			= null;

	var $info  			= null;

	var $limit 		 	= null;

	//video state mapping
	var $stateMapping   = null;

	public function __construct()
	{
		$this->info     = new SocialMigratorHelperInfo();
		$this->name  	= 'com_community';

		$this->limit 	= 10; //10 items per cycle

		// do not change the steps sequence !
		$this->steps[] 	= 'videocategory';
		$this->steps[] 	= 'videocategorydefault';
		$this->steps[] 	= 'vidoes';
		$this->steps[] 	= 'vidoecomments';
	}

	public function getVersion()
	{
		$exists 	= $this->isComponentExist();

		if( !$exists->isvalid )
		{
			return false;
		}

		// check JomSocial version.
		$xml		= JPATH_ROOT . '/administrator/components/com_community/community.xml';

		$parser = FD::get( 'Parser' );
		$parser->load( $xml );

		$version	= $parser->xpath( 'version' );
		$version 	= (float) $version[0];

		return $version;
	}

	public function isInstalled()
	{
		$file	= JPATH_ROOT . '/components/com_community/libraries/core.php';

		if(! JFile::exists( $file ) )
		{
			return false;
		}

		return true;
	}

	public function setUserMapping( $maps )
	{
		// do nothing.
	}

	/*
	 * return object with :
	 *     isvalid  : true or false
	 *     messsage : string.
	 *     count    : integer. item count to be processed.
	 */
	public function isComponentExist()
	{
		$obj = new stdClass();
		$obj->isvalid = false;
		$obj->count   = 0;
		$obj->message = '';

		$jsCoreFile	= JPATH_ROOT . '/components/com_community/libraries/core.php';

		if(! JFile::exists( $jsCoreFile ) )
		{
			$obj->message = 'JomSocial not found in your site. Process aborted.';
			return $obj;
		}

		// @todo check if the db tables exists or not.


		// all pass. return object

		$obj->isvalid = true;
		$obj->count   = $this->getItemCount();

		return $obj;
	}

	public function getItemCount()
	{
		$db = FD::db();
		$sql = $db->sql();

		$total = count( $this->steps );

		// --------------- video category
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_videos_category` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'videocategory' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;

		// --------------- videos
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_videos` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'videos' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`storage` = ' . $db->Quote('file');

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;


		// ------------  video's comments
		$query = 'select count(1) as `total`';
		$query .= ' 	from `#__community_wall` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'videocomments' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`type` IN (' . $db->Quote('videos') . ',' . $db->Quote('videos.linking') . ')';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;


		return $total;
	}

	public function process( $item )
	{
		// @debug
		$obj = new stdClass();

		if( empty( $item ) )
		{
			$item = $this->steps[0];
		}

		$result = '';

		switch( $item )
		{
			case 'videocategory':
				$result = $this->processVideoCategory();
				break;

			case 'videocategorydefault':
				$result = $this->processVideoDefaultCategory();
				break;

			case 'vidoes':
				$result = $this->processVideos();
				break;

			case 'vidoecomments':
				$result = $this->processComments();
				break;

			default:
				break;
		}

		// this is the ending part to determine if the process is already ended or not.
		if( is_null( $result ) )
		{
			$keys 		= array_keys( $this->steps, $item);
			$curSteps 	= $keys[0];

			if( isset( $this->steps[ $curSteps + 1] ) )
			{
				$item = $this->steps[ $curSteps + 1];
			}
			else
			{
				$item = null;
			}

			$obj->continue = ( is_null( $item ) ) ? false : true ;
			$obj->item 	   = $item;
			$obj->message  = ( $obj->continue ) ? 'Checking for next item to migrate....' : 'No more item found.';

			return $obj;
		}


		$obj->continue = true;
		$obj->item 	   = $item;
		$obj->message  = implode( '<br />', $result->message );

		return $obj;
	}


	private function processComments()
	{

		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.`uid` as `esvideoid`, v.`creator_type` as `jsvideotype`';
		$query .= ' 	from `#__community_wall` as a';
		$query .= ' 	inner join `#__community_videos` as v on a.`contentid` = v.`id`';
		$query .= '		inner join `#__social_migrators` as c on a.`contentid` = c.`oid` and c.`element` = ' . $db->Quote( 'videos' ) . ' and c.`component` = ' . $db->Quote( $this->name );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'videocomments' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`type` IN (' . $db->Quote('videos') . ',' . $db->Quote('videos.linking') . ')';
		$query .= ' ORDER BY a.`contentid` ASC';
		$query .= ' LIMIT ' . $this->limit;


		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsVideoComments = $db->loadObjectList();

		if( count( $jsVideoComments ) <= 0 )
		{
			return null;
		}

		// // TODO: get superadmin id
		// $userModel 		= FD::model( 'Users' );
		// $superadmins 	= $userModel->getSiteAdmins();
		// $adminId 		= ( $superadmins ) ? $superadmins[0]->id : '42';

		foreach( $jsVideoComments as $jsVideoComment )
		{
			if(! $jsVideoComment->esvideoid )
			{
				// there is no es video id associated. do not process this anymore.

				// log into mgirator
				$this->log( 'videocomments', $jsVideoComment->id, -1 );

				$this->info->setInfo( 'Video comment with id \'' . $jsVideoComment->id . '\' is not associate with video in EasySocial. Video commment migration process aborted.' );
				continue;
			}

			$element = 'videos.user.create';
			if ($jsVideoComment->jsvideotype == 'group') {
				$element = 'videos.group.create';
			}

			// video link
			$esVideoTbl = FD::table('Video');
			$esVideoTbl->load( $jsVideoComment->esvideoid );

			$obj = new stdClass();
			//$obj->url = FRoute::photos( array( 'layout' => 'item', 'id' => $jsVideoComment->esvideoid ) );
			$obj->url = $esVideoTbl->getPermalink();
			$obj->url = $this->removeAdminSegment( $obj->url );

			$esComment = FD::table( 'Comments' );
			$esComment->element 	= $element;
			$esComment->uid 		= $jsVideoComment->esvideoid;
			$esComment->comment 	= $jsVideoComment->comment;
			$esComment->created_by 	= $jsVideoComment->post_by;
			$esComment->created 	= $jsVideoComment->date;
			$esComment->params 		= FD::json()->encode( $obj );
			$esComment->stream_id 	= $this->getVideoStreamId($jsVideoComment->esvideoid);

			//off the trigger for migrated commetns.
			$esComment->offTrigger();
			$esComment->store();

			// log into mgirator
			$this->log( 'videocomments', $jsVideoComment->id, $esComment->id );
			$this->info->setInfo( 'Photo comment with id \'' . $jsVideoComment->id . '\' is now migrated into EasySocial the new comment id: ' . $esComment->id . '.' );

		}// end foreach

		return $this->info;


	}


	private function processVideoCategory()
	{

		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*';
		$query .= ' from `#__community_videos_category` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'videocategory' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsVideoCats = $db->loadObjectList();

		if( count( $jsVideoCats ) <= 0 )
		{
			return null;
		}

		// TODO: get superadmin id
		$userModel 		= FD::model( 'Users' );
		$superadmins 	= $userModel->getSiteAdmins();
		$adminId 		= ( $superadmins ) ? $superadmins[0]->id : '42';

		foreach( $jsVideoCats as $jsVideoCat )
		{
			$esVideoCat = FD::table( 'VideoCategory' );


			$esVideoCat->title 	= $jsVideoCat->name;
			$esVideoCat->alias 	= JFilterOutput::stringURLSafe($jsVideoCat->name);
			$esVideoCat->description = $jsVideoCat->description;
			$esVideoCat->state 	= SOCIAL_STATE_PUBLISHED;
			$esVideoCat->default = 0;
			$esVideoCat->user_id = $adminId; // default to superadmin id
			$esVideoCat->created = FD::date()->toMySQL();

			$esVideoCat->store();


			$this->log( 'videocategory', $jsVideoCat->id, $esVideoCat->id );
			$this->info->setInfo( 'Video category \'' . $jsVideoCat->name . '\' is now migrated into EasySocial with id \'' . $esVideoCat->id . '\'.' );

		}// end foreach

		return $this->info;


	}

	private function processVideoDefaultCategory()
	{

		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = "select id from `#__social_videos_categories` where `default` = 1";
		$sql->raw($query);

		$db->setQuery($sql);
		$result = $db->loadResult();

		if (!$result) {
			// set the 1st one as default.
			$query = "update `#__social_videos_categories` set `default` = 1 limit 1";
			$sql->clear();

			$sql->raw($query);
			$db->setQuery($sql);
			$db->query();
		}

		return null;
	}

	private function processVideos()
	{
		$config = FD::config();
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.uid as `escatid`, d.uid as `esgroupid`';
		$query .= ' from `#__community_videos` as a';
		$query .= ' inner join `#__social_migrators` as c on a.`category_id` = c.`oid` and c.`element` = ' . $db->Quote( 'videocategory' ) . ' and c.`component` = ' . $db->Quote($this->name);
		$query .= ' left join `#__social_migrators` as d on a.`groupid` = d.`oid` and d.`element` = ' . $db->Quote( 'groups' ) . ' and d.`component` = ' . $db->Quote($this->name);
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'videos' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.storage = ' . $db->Quote('file');
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsVideos = $db->loadObjectList();

		if( count( $jsVideos ) <= 0 )
		{
			return null;
		}

		$json = FD::json();

		foreach( $jsVideos as $jsVideo )
		{
			$state = SOCIAL_VIDEO_PUBLISHED;
			if ($jsVideo->status == 'pending') {
				$state = SOCIAL_VIDEO_PENDING;
			} else if (!$jsVideo->published) {
				$state = SOCIAL_VIDEO_UNPUBLISHED;
			}

			$esVideo = FD::table('Video');

			$esVideo->title = $jsVideo->title;
			$esVideo->description = $jsVideo->description;
			$esVideo->user_id = $jsVideo->creator;

			$esVideo->uid = ($jsVideo->creator_type == 'user') ? $jsVideo->creator : $jsVideo->esgroupid;
			$esVideo->type = $jsVideo->creator_type;

			// if this is a group video but there is no group migrated into es, we will migrate as user video
			if ($jsVideo->creator_type != 'user' && !$jsVideo->esgroupid) {
				$esVideo->uid = $jsVideo->creator;
				$esVideo->type = 'user';
			}

			$esVideo->created = $jsVideo->created;
			$esVideo->state = $state;
			$esVideo->featured = $jsVideo->featured;
			$esVideo->category_id = $jsVideo->escatid;
			$esVideo->hits = $jsVideo->hits;
			$esVideo->duration = $jsVideo->duration;
			$esVideo->size = $jsVideo->filesize;
			$esVideo->params = $jsVideo->params;
			$esVideo->storage = 'joomla';
			$esVideo->source = ($jsVideo->type == 'file') ? 'upload' : 'link';

			$state = $esVideo->store();

			if ($state) {

				// now we need to copy the media files.
				$esVideo = $this->copyMediaFiles($jsVideo, $esVideo);
				$state = $esVideo->store();

				// add privacy
				$this->addItemPrivacy( 'videos.view', $esVideo->id, SOCIAL_TYPE_VIDEOS, $jsVideo->creator);

				if ($esVideo->state != SOCIAL_VIDEO_PENDING) {
					// TODO: Add video creation stream.
					$stream				= FD::stream();
					$streamTemplate		= $stream->getTemplate();

					// Set the actor
					$streamTemplate->setActor( $jsVideo->creator , SOCIAL_TYPE_USER );

					// Set the context
					$streamTemplate->setContext( $esVideo->id , SOCIAL_TYPE_VIDEOS );

					$streamTemplate->setVerb( 'create' );

					$streamTemplate->setDate( $jsVideo->created );

					$streamTemplate->setAccess('videos.view');

					if ($jsVideo->creator_type != 'user' && $jsVideo->esgroupid) {
						$streamTemplate->setCluster($jsVideo->esgroupid, $jsVideo->creator_type);
					}

					// Add stream template.
					$stream->add( $streamTemplate );
				}

				// end add stream
				$this->log( 'videos', $jsVideo->id, $esVideo->id );

				$this->info->setInfo( 'Video \'' . $jsVideo->title . '\' has migrated succefully into EasySocial.' );
			}

		}//end foreach

		return $this->info;
	}

	private function copyMediaFiles($jsVideo, $esVideo)
	{
		$config = ES::config();
		$esContainer = ltrim($config->get('video.storage.container'), '/');
		$esPath = JPATH_ROOT . '/' . $esContainer;

		if (!JFolder::exists($esPath)) {
			JFolder::create($esPath);
		}

		// create neccessary folder for this video
		$esMainVideoPath = $esPath . '/' . $esVideo->id;
		if (!JFolder::exists($esMainVideoPath)) {
			JFolder::create($esMainVideoPath);
		}


		$esVideo->file_title = JFilterOutput::stringURLSafe($jsVideo->title);

		if ($jsVideo->type != 'file') {
			// link
			$esVideo->path = $jsVideo->path;
			$esVideo->original = '';

		} else {

			$jsVideoPath = JPATH_ROOT . '/' . $jsVideo->path;

			// images/videos/20/oZXTXe3G76C.mp4
			if (JFile::exists($jsVideoPath)) {
				// lets copy the video into ES video folder.
				$esVideoPath = $esPath . '/' . $esVideo->id;
				$jsFilename = JFile::getName($jsVideo->path);
				$esVideoPath .= '/' . $jsFilename;

				JFile::copy($jsVideoPath, $esVideoPath);

				// we only have relative path in db
				$esVideoPath = $esContainer . '/' . $esVideo->id . '/' . $jsFilename;
				$esVideo->path = $esVideoPath;
			}

			// lets try to get original video files.
			$jsOriVideoPath = str_replace('/videos/', '/originalvideos/', $jsVideoPath);

			if (JFile::exists($jsOriVideoPath)) {

				$jsOriFilename = JFile::getName($jsOriVideoPath);

				// Get the storage path for this video
				$storagePath = $esPath . '/' . $esVideo->id ;

				// We need to rename the original file name.
				$storagePath .= '/' . md5($jsOriFilename);

				// Copy the original video file into the storage path
				JFile::copy($jsOriVideoPath, $storagePath);

				$esVideo->original = $storagePath;
			} else {

				$esVideo->original = JPATH_ROOT . '/' . $esVideo->path;
			}

		}


 		$jsVideoThumb = JPATH_ROOT . '/' . $jsVideo->thumb;

		// media/com_easysocial/videos/19/001bf01c1c8a2bc38add2a7e44185938.jpg (for upload)
		if (JFile::exists($jsVideoThumb)) {
			$jsThumbFilename = JFile::getName($jsVideoThumb);

			// Get the storage path for this video
			$storageThumbPath = $esPath . '/' . $esVideo->id ;

			// We need to rename the original file name.
			// $tmpFilename = md5($jsThumbFilename);
			$storageThumbPath .= '/' . $jsThumbFilename;

			// Copy the original video file into the storage path
			JFile::copy($jsVideoThumb, $storageThumbPath);


			// for thumb, we only have relative path in db
			$esVideoThumbPath = $esContainer . '/' . $esVideo->id . '/' . $jsThumbFilename;
			$esVideo->thumbnail = $esVideoThumbPath;
		}

		if ($jsVideo->type != 'file') {
			// Grab the video data
			$crawler = ES::get('crawler');
			$crawler->crawl($esVideo->path);

			$scrape = (object) $crawler->getData();

			// Set the video params with the scraped data
			$esVideo->params = json_encode($scrape);
		}


// select * from jos_community_videos;

// select * from jos_social_videos order by id desc;
// -- 35


// select * from jos_social_migrators order by id desc;
// -- 730



// delete from jos_social_migrators where id > 730;

// delete from jos_social_videos where id > 35;

// delete from jos_social_privacy_items where `type` = 'videos' and uid > 35;


		// return to caller
		return $esVideo;
	}

	private function addItemPrivacy( $command, $esUid, $esUType, $jsUserId)
	{
		static $defaultESPrivacy = array();

		$db 	= FD::db();
		$sql 	= $db->sql();


		if(! isset( $defaultESPrivacy[ $command ] ) )
		{
			$db 	= FD::db();
			$sql 	= $db->sql();

			$commands = explode( '.', $command );

			$element = $commands[0];
			$rule  	 = $commands[1];

			$query = 'select `id`, `value` from `#__social_privacy`';
			$query .= ' where `type` = ' . $db->Quote( $element );
			$query .= ' and `rule` = ' . $db->Quote( $rule );

			$sql->raw( $query );
			$db->setQuery( $sql );

			$defaultESPrivacy[ $command ] = $db->loadObject();
		}

		$defaultPrivacy = $defaultESPrivacy[ $command ];

		$privacyValue = $defaultPrivacy->value;


		$esPrivacyItem = FD::table( 'PrivacyItems' );

		$esPrivacyItem->privacy_id 	= $defaultPrivacy->id;
		$esPrivacyItem->user_id 	= $jsUserId;
		$esPrivacyItem->uid 		= $esUid;
		$esPrivacyItem->type 		= $esUType;
		$esPrivacyItem->value 		= $privacyValue;

		$esPrivacyItem->store();
	}

	private function getFileMimeType( $file )
	{
		if ( function_exists("finfo_file") ) {
			$finfo 	= finfo_open( FILEINFO_MIME_TYPE ); // return mime type ala mimetype extension
			$mime 	= finfo_file( $finfo, $file );
			finfo_close( $finfo );
			return $mime;
		} else if ( function_exists("mime_content_type") ) {
			return mime_content_type( $file );
		} else {
			return JFile::getExt( $file );
		}
	}

	private function removeAdminSegment( $url = '' )
	{
		if( $url )
		{
			$url 	= '/' . ltrim( $url , '/' );
			$url 	= str_replace('/administrator/', '/', $url );
		}

		return $url;
	}

	private function getVideoStreamId( $esVideoId )
	{
		static $_cache = array();

		$db 	= FD::db();
		$sql 	= $db->sql();

		if (! isset($_cache[$esVideoId])) {

			$sql->select('#__social_stream_item', 'a');
			$sql->column('a.uid');
			$sql->where('a.context_type', SOCIAL_TYPE_VIDEOS);
			$sql->where('a.context_id', $esVideoId);

			$db->setQuery($sql);

			$uid 	= (int) $db->loadResult();
			$_cache[$esVideoId] = $uid;
		}

		return $_cache[$esVideoId];
	}

	public function log( $element, $oriId, $newId )
	{
		$tbl = FD::table( 'Migrators' );

		$tbl->oid 		= $oriId;
		$tbl->element 	= $element;
		$tbl->component = $this->name;
		$tbl->uid 		= $newId;
		$tbl->created 	= FD::date()->toMySQL();

		$tbl->store();
	}

}
