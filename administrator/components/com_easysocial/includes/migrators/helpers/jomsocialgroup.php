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

// FD::import( 'admin:/inclues/migrators/helpers/info' );
require_once( SOCIAL_LIB . '/migrators/helpers/info.php' );

/**
 * DB layer for EasySocial.
 *
 * @since	1.0
 * @author	Sam <sam@stackideas.com>
 */
class SocialMigratorHelperJomsocialGroup
{
	// component name, e.g. com_community
	var $name  			= null;

	// migtration steps
	var $steps 			= null;

	var $info  			= null;

	var $limit 		 	= null;

	public function __construct()
	{
		$this->info     = new SocialMigratorHelperInfo();
		$this->name  	= 'com_community';

		$this->limit 	= 10; //10 items per cycle

		// do not change the steps sequence !
		$this->steps[] 	= 'groupcategory';
		$this->steps[] 	= 'groups';
		$this->steps[] 	= 'groupmembers';
		$this->steps[] 	= 'groupavatar';
		$this->steps[] 	= 'groupcover';
		$this->steps[] 	= 'groupphotos';
		$this->steps[] 	= 'groupdiscussions';
		$this->steps[] 	= 'groupdiscussionsfile';
		$this->steps[] 	= 'groupbulletins';
		$this->steps[] 	= 'groupwalls';

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


		// groups category
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_groups_category` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupcategory' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;



		// ------------  groups
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_groups` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groups' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;



		// ------------  groups members
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_groups_members` as a';
		$query .= ' inner join `#__community_groups` as d on a.`groupid` = d.`id`';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`memberid` = b.`oid` and b.`element` = concat_ws(' . $db->Quote('.') . ',' . $db->Quote( 'groupmembers' ) . ', a.`groupid` ) and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;



		// ------------  groups avatar
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_groups` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupavatar' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;


		// ------------  groups cover
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_groups` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupcover' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;


		// ------------  groups photos
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_photos` as a';
		$query .= ' inner join `#__community_photos_albums` as b on a.`albumid` = b.`id` and b.`type` = ' . $db->Quote( 'group' );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupphotos' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`storage` = ' . $db->Quote( 'file' );

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;



		// ------------ discussion
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_wall` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupdiscussions' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`type` = ' . $db->Quote( 'discussions' );

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;


		// ------------ discussion files
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_files` as a';

		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupdiscussionsfile' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`discussionid` != ' . $db->Quote( '0' );

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;



		// ------------ bulletins
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_groups_bulletins` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupbulletins' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';

		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;



		// ------------ groups walls
		$query = 'select count(1) as `total`';
		$query .= ' from `#__community_activities` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupwalls' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and `app` = ' . $db->Quote( 'groups.wall' );

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
			case 'groupcategory':
				$result = $this->processGroupCategory();
				break;

			case 'groups':
				$result = $this->processGroups();
				break;

			case 'groupmembers':
				$result = $this->processMembers();
				break;

			case 'groupavatar':
				$result = $this->processAvatar();
				break;

			case 'groupcover':
				$result = $this->processCover();
				break;

			case 'groupphotos':
				$result = $this->processPhotos();
				break;

			case 'groupdiscussions':
				$result = $this->processDiscussion();
				break;

			case 'groupdiscussionsfile':
				$result = $this->processDiscussionFiles();
				break;

			case 'groupbulletins':
				$result = $this->processBulletins();
				break;

			case 'groupwalls':
				$result = $this->processWalls();
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

	private function processCover()
	{
		$config = FD::config();
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.uid as `esgroupid`';
		$query .= ' from `#__community_groups` as a';
		$query .= ' inner join `#__social_migrators` as c on a.`id` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupcover' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsGroups = $db->loadObjectList();

		if( count( $jsGroups ) <= 0 )
		{
			return null;
		}

		foreach( $jsGroups as $jsGroup )
		{
			if( !$jsGroup->cover )
			{
				// no need to process further.
				$this->log( 'groupcover', $jsGroup->id , $jsGroup->id );

				$this->info->setInfo( 'Group ' . $jsGroup->id . ' is using default cover. no migration is needed.' );
				continue;
			}

			$imagePath = JPATH_ROOT . '/' . $jsGroup->cover;

			$tmp 		= explode( '/', $imagePath );
			$filename 	= $tmp[ count( $tmp ) - 1 ];

			if( !JFile::exists( $imagePath ) )
			{
				$this->log( 'groupcover', $jsGroup->id , $jsGroup->id );

				$this->info->setInfo( 'Group ' . $jsGroup->id . ' the cover image file is not found from the server. Process aborted.');
				continue;
			}

			// lets copy this file to tmp folder 1st.
			$tmp 			= JFactory::getConfig()->get( 'tmp_path' );
			$tmpImageFile 	= $tmp . '/' . md5( JFactory::getDate()->toSql() );
			JFile::copy( $imagePath , $tmpImageFile );

			$image = FD::image();
			$image->load( $tmpImageFile );

			// Check if there's a profile photos album that already exists.
			$albumModel	= FD::model( 'Albums' );

			// Retrieve the group's default album
			$album 	= $albumModel->getDefaultAlbum( $jsGroup->esgroupid , SOCIAL_TYPE_GROUP , SOCIAL_ALBUM_PROFILE_COVERS );
			$album->user_id = $jsGroup->ownerid;
			$album->store();

			$photo 				= FD::table( 'Photo' );
			$photo->uid 		= $jsGroup->esgroupid ;
			$photo->user_id 	= $jsGroup->ownerid ;
			$photo->type 		= SOCIAL_TYPE_GROUP;
			$photo->album_id 	= $album->id;
			$photo->title 		= $filename;
			$photo->caption 	= '';
			$photo->ordering	= 0;

			// We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
			$photo->state 		= SOCIAL_PHOTOS_STATE_TMP;

			// Try to store the photo first
			$state 		= $photo->store();

			// Push all the ordering of the photo down
			$photosModel = FD::model( 'photos' );
			$photosModel->pushPhotosOrdering( $album->id , $photo->id );

			// Render photos library
			$photoLib 	= FD::get( 'Photos' , $image );
			$storage    = $photoLib->getStoragePath($album->id, $photo->id);
			$paths 		= $photoLib->create( $storage );

			// Create metadata about the photos
			foreach( $paths as $type => $fileName )
			{
				$meta 				= FD::table( 'PhotoMeta' );
				$meta->photo_id		= $photo->id;
				$meta->group 		= SOCIAL_PHOTOS_META_PATH;
				$meta->property 	= $type;
				$meta->value		= $storage . '/' . $fileName;

				$meta->store();
			}


			// Load the cover
			$cover 	= FD::table( 'Cover' );
			$cover->uid 	= $jsGroup->esgroupid;
			$cover->type 	= SOCIAL_TYPE_GROUP;

			$cover->setPhotoAsCover( $photo->id );

			// Save the cover.
			$cover->store();

			// now we need to update back the photo item to have the cover_id and the state to published
			// We need to set the photo state to "SOCIAL_STATE_PUBLISHED"
			$photo->state 		= SOCIAL_STATE_PUBLISHED;
			$photo->store();

			if (! $album->cover_id) {
				$album->cover_id = $photo->id;
				$album->store();
			}

			// @Add stream item when a new event cover is uploaded
			// get the cover update date.
			$uploadDate = $this->getMediaUploadDate('cover.upload', $jsGroup->id);

			if (!$uploadDate) {
				// if empty, then lets just use event creation date.
				$uploadDate = $jsGroup->created;
			}

			$photo->addPhotosStream( 'updateCover', $uploadDate );


			// log into mgirator
			$this->log( 'groupcover', $jsGroup->id , $jsGroup->id );

			$this->info->setInfo( 'Group cover ' . $jsGroup->id . ' is now migrated into EasySocial.' );

		}

		return $this->info;

	}

	private function getMediaUploadDate($context, $jsGroupId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "select `created` from `#__community_activities` where `groupid` = '$jsGroupId' and `app` = '$context' order by `id` desc limit 1";
		$sql->raw($query);

		$db->setQuery($sql);
		$result = $db->loadResult();

		return $result;
	}



	private function processWalls()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.`uid` as `esgroupid`';
		$query .= ' from `#__community_activities` as a';
		$query .= ' 	inner join `#__social_migrators` as c on a.`groupid` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupwalls' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and `app` = ' . $db->Quote( 'groups.wall' );
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsWalls = $db->loadObjectList();

		if( count( $jsWalls ) <= 0 )
		{
			return null;
		}

		foreach( $jsWalls as $jsWall )
		{
			// create story stream for this group.

			$stream 		= FD::stream();

			// Get the stream template
			$template 		= $stream->getTemplate();

			$template->setActor( $jsWall->actor , SOCIAL_TYPE_USER );
			$template->setContext( '0' , SOCIAL_TYPE_STORY );


			$content = ( $jsWall->title ) ? $jsWall->title : $jsWall->content;
			$template->setContent( $content );

			$template->setVerb( 'create' );

			// Set the params to cache the group data

			$group = FD::group( $jsWall->esgroupid );
			$registry	= FD::registry();
			$registry->set( 'group' , $group );

			// Set the params to cache the group data
			$template->setParams( $registry );

			$template->setCluster( $jsWall->esgroupid, SOCIAL_TYPE_GROUP, $group->type );

			// Set this stream to be public
			$template->setAccess( 'story.view' );

			$template->setDate( $jsWall->created );

			$streamItem 	= $stream->add( $template );

			$this->log( 'groupwalls', $jsWall->id, $streamItem->uid );

			$this->info->setInfo( 'Group wall \'' . $jsWall->id . '\' is now migrated into EasySocial as group\'s story update.' );


		}

		return $this->info;

	}

	private function processBulletins()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.`uid` as `esgroupid`';
		$query .= ' from `#__community_groups_bulletins` as a';
		$query .= ' 	inner join `#__social_migrators` as c on a.`groupid` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupbulletins' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsBulletins = $db->loadObjectList();

		if( count( $jsBulletins ) <= 0 )
		{
			return null;
		}


		foreach( $jsBulletins as $jsBullentin )
		{

			$esNews = FD::table( 'GroupNews' );


			$esNews->cluster_id	= $jsBullentin->esgroupid;
			$esNews->title 		= $jsBullentin->title;
			$esNews->content 	= $jsBullentin->message;
			$esNews->created	= $jsBullentin->date;
			$esNews->created_by	= $jsBullentin->created_by;
			$esNews->state		= $jsBullentin->published;
			$esNews->comments	= 1;
			$esNews->hits		= 0;

			// we need to override the stream creation date
			$esNews->setStreamDate( $jsBullentin->date );

			// store function will create the stream for this as well.
			$esNews->store();

			$this->log( 'groupbulletins', $jsBullentin->id, $esNews->id );

			$this->info->setInfo( 'Group bullentin  \'' . $jsBullentin->id . '\' is now migrated into EasySocial as group news.' );

		}

		return $this->info;

	}

	private function processDiscussion()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*';
		$query .= ' from `#__community_wall` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupdiscussions' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`type` = ' . $db->Quote( 'discussions' );
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsDiscussions = $db->loadObjectList();

		if( count( $jsDiscussions ) <= 0 )
		{
			// lets see if tehre is any discussion that do not have any replies. if yes, we will migrate these items.
			$this->processDiscussParent();

			return null;
		}

		foreach( $jsDiscussions as $jsDiscuss )
		{

			$esObj = $this->mapDiscussionParent( $jsDiscuss );

			$esDiscussParentId	= $esObj->esdiscussid;
			$esGroupId			= $esObj->esgroupid;

			// lets add the child posts into easysocial.
			$esDiscussChild = FD::table( 'Discussion' );

			$esDiscussChild->parent_id	= $esDiscussParentId;
			$esDiscussChild->uid		= $esGroupId;
			$esDiscussChild->type 		= SOCIAL_TYPE_GROUP;
			$esDiscussChild->content 	= $jsDiscuss->comment;
			$esDiscussChild->created_by	= $jsDiscuss->post_by;
			$esDiscussChild->state		= $jsDiscuss->published;
			$esDiscussChild->created	= $jsDiscuss->date;

			$esDiscussChild->store();

			// now we need to update the parent post.
			$parentPost = FD::table( 'Discussion' );
			$parentPost->load( $esDiscussParentId );

			$parentPost->last_reply_id = $esDiscussChild->id;
			$parentPost->total_replies = $parentPost->total_replies + 1;
			$parentPost->store();


			//load the group
			$group = FD::group( $esGroupId );

			// Create a new stream item for this discussion reply
			$stream = FD::stream();

			// Get the stream template
			$tpl		= $stream->getTemplate();

			// Someone just joined the group
			$tpl->setActor( $my->id , SOCIAL_TYPE_USER );

			// Set the context
			$tpl->setContext( $discussion->id , 'discussions' );

			// Set the verb
			$tpl->setVerb( 'reply' );

			// Set the params to cache the group data
			$registry 	= FD::registry();
			$registry->set( 'group' , $group );
			$registry->set( 'reply' , $esDiscussChild );
			$registry->set( 'discussion' , $parentPost );

			$tpl->setParams( $registry );
			$tpl->setDate( $jsDiscuss->date );

			// Set the cluster
			$tpl->setCluster( $group->id , SOCIAL_TYPE_GROUP, $group->type );

			$tpl->setAccess('core.view');

			// Add the stream
			$stream->add( $tpl );

			$this->log( 'groupdiscussions', $jsDiscuss->id, $esDiscussChild->id );

			$this->info->setInfo( 'Group discussion\'s reply  \'' . $jsDiscuss->id . '\' is now migrated into EasySocial.' );


		} //end foreach


		return $this->info;
	}

	private function processDiscussionFiles()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, d.`uid` as `esdiscussid`, c.`uid` as `esgroupid`, e.`uid` as `escollectid`';
		$query .= ' from `#__community_files` as a';
		$query .= ' 	inner join `#__social_migrators` as c on a.`groupid` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( $this->name );
		$query .= '		inner join `#__social_migrators` as d on a.discussionid = d.oid and d.`element` = ' . $db->Quote( 'groupdiscussionsparent' ) . ' and d.`component` = ' . $db->Quote( $this->name );
		$query .= '		left join `#__social_migrators` as e on a.groupid = e.oid and e.`element` = ' . $db->Quote( 'groupcollection' ) . ' and e.`component` = ' . $db->Quote( $this->name );

		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupdiscussionsfile' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';


		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsFiles = $db->loadObjectList();

		if( count( $jsFiles ) <= 0 )
		{
			return null;
		}

		foreach( $jsFiles as $jsFile )
		{
			$esCollectionId = $this->getGroupCollectionId( $jsFile );

			$filePath = JPATH_ROOT . '/' . $jsFile->filepath;

			if( JFile::exists( $filePath ) )
			{

				// add the file extension into the filename.
				$filePathArr = explode( '.', $filePath );
				$fileExt = $filePathArr[ count($filePathArr) - 1 ];

				// append file ext into filename.
				$jsFile->name = $jsFile->name . '.' . $fileExt;

				$fileMime = $this->getFileMimeType( $filePath );
				$fileHash = md5( $jsFile->name . $jsFile->filepath );

				$esFile 				= FD::table( 'File' );
				$esFile->name			= $jsFile->name;
				$esFile->collection_id 	= $esCollectionId;
				$esFile->hits 			= $jsFile->hits;
				$esFile->hash 			= $fileHash;
				$esFile->uid 			= $jsFile->esgroupid;
				$esFile->type			= SOCIAL_TYPE_GROUP;
				$esFile->created		= $jsFile->created;
				$esFile->user_id 		= $jsFile->creator;
				$esFile->size 			= $jsFile->filesize;
				$esFile->mime 			= $fileMime;
				$esFile->state 			= SOCIAL_STATE_PUBLISHED;
				$esFile->storage 		= SOCIAL_STORAGE_JOOMLA;
				$esFile->store();

				// attach this file into discussion.
				$esDiscussFile 					= FD::table( 'DiscussionFile' );
				$esDiscussFile->file_id 		= $esFile->id;
				$esDiscussFile->discussion_id 	= $jsFile->esdiscussid;
				$esDiscussFile->store();

				// now we need to append the file tag into discussion content.
				$fileTag = "\r\n";
				$fileTag .= '[file id="'. $esFile->id . '"]' . $jsFile->name . '[/file]';

				$esDiscuss = FD::table( 'Discussion' );
				$esDiscuss->load( $jsFile->esdiscussid );
				$esDiscuss->content = $esDiscuss->content . $fileTag;
				$esDiscuss->store();


				// now we copy the file into es.
				$storage	= $esFile->getStoragePath();

				// Ensure that the storage path exists.
				FD::makeFolder( $storage );

				$state 		= JFile::copy( $filePath , $storage . '/' . $esFile->hash );

				// now we add stream
				$stream		= FD::stream();
				$tpl		= $stream->getTemplate();
				$group 		= FD::group( $jsFile->esgroupid );

				// this is a cluster stream and it should be viewable in both cluster and user page.
				$tpl->setCluster( $jsFile->esgroupid, SOCIAL_TYPE_GROUP, $group->type );

				// Set the actor
				$tpl->setActor( $jsFile->creator , SOCIAL_TYPE_USER );

				// Set the context
				$tpl->setContext( $esFile->id , SOCIAL_TYPE_FILES );

				// Set the verb
				$tpl->setVerb( 'uploaded' );

				// set date
				$tpl->setDate( $jsFile->created );

				// Set the params to cache the group data
				$registry	= FD::registry();
				$registry->set( 'group' , $group );
				$registry->set( 'file'	, $esFile );

				// Set the params to cache the group data
				$tpl->setParams( $registry );

				// since this is a cluster and user stream, we need to call setPublicStream
				// so that this stream will display in unity page as well
				// This stream should be visible to the public
				$tpl->setAccess( 'core.view' );

				$stream->add( $tpl );


				$this->log( 'groupdiscussionsfile', $jsFile->id, $esFile->id );
				$this->info->setInfo( 'File with id ' . $jsFile->id . ' for group discussion ' . $jsFile->discussionid . ' successfully migrated into EasySocial.' );

			}
			else
			{
				$this->log( 'groupdiscussionsfile', $jsFile->id, 0 );
				$this->info->setInfo( 'File with id ' . $jsFile->id . ' for group discussion ' . $jsFile->discussionid . ' not found. Migration of this file aborted.' );
			}

		}

		return $this->info;

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


	private function getGroupCollectionId( $jsFile )
	{
		static $cache = array();

		if(! isset( $cache[ $jsFile->esgroupid ] ) )
		{

			if( $jsFile->escollectid )
			{
				$cache[ $jsFile->esgroupid ] = $jsFile->escollectid;
			}
			else
			{
				$collection 			= FD::table( 'FileCollection' );
				$collection->title		= 'Group file sharing';
				$collection->owner_id 	= $jsFile->esgroupid;
				$collection->owner_type = SOCIAL_TYPE_GROUP;
				$collection->user_id 	= $jsFile->creator;
				$collection->store();

				$this->log( 'groupcollection', $jsFile->groupid, $collection->id );

				$cache[ $jsFile->esgroupid ] = $collection->id;

			}

		}

		return $cache[ $jsFile->esgroupid ];
	}


	private function processDiscussParent()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.`uid` as `esgroupid`';
		$query .= ' from `#__community_groups_discuss` as a';
		$query .= ' 	inner join `#__social_migrators` as c on a.`groupid` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );

		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupdiscussionsparent' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsParents = $db->loadObjectList();

		if( $jsParents )
		{
			foreach( $jsParents as $jsParent )
			{
				$esDiscussion = $this->addParentDiscussion( $jsParent );
			}
		}

	}


	private function mapDiscussionParent( $jsDiscuss )
	{
		static $cache = array();

		$db 	= FD::db();
		$sql 	= $db->sql();

		if( ! isset( $cache[ $jsDiscuss->contentid ] ) )
		{
			$query = 'select a.*, b.`uid` as `esdiscussid`, c.`uid` as `esgroupid`';
			$query .= ' from `#__community_groups_discuss` as a';
			$query .= ' 	inner join `#__social_migrators` as c on a.`groupid` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
			$query .= '		left join `#__social_migrators` as b on a.id = b.oid and b.`element` = ' . $db->Quote( 'groupdiscussionsparent' ) . ' and b.`component` = ' . $db->Quote( $this->name );
			$query .= ' where a.id = ' . $db->Quote( $jsDiscuss->contentid );

			$sql->raw( $query );
			$db->setQuery( $sql );

			$jsParent = $db->loadObject();

			if( $jsParent->esdiscussid )
			{
				$obj = new stdClass();

				$obj->esdiscussid 	= $jsParent->esdiscussid;
				$obj->esgroupid 	= $jsParent->esgroupid;

				$cache[ $jsDiscuss->contentid ] = $obj;
			}
			else
			{
				$esDiscussion = $this->addParentDiscussion( $jsParent );

				$obj = new stdClass();

				$obj->esdiscussid 	= $esDiscussion->id;
				$obj->esgroupid 	= $jsParent->esgroupid;

				$cache[ $jsDiscuss->contentid ] = $obj;
			}


		} //end

		return $cache[ $jsDiscuss->contentid ];
	}

	private function addParentDiscussion( $jsParent )
	{
		// lets add the parent post into easysocial.
		$esDiscussion = FD::table( 'Discussion' );

		$esDiscussion->parent_id	= 0;
		$esDiscussion->uid			= $jsParent->esgroupid;
		$esDiscussion->type 		= SOCIAL_TYPE_GROUP;
		$esDiscussion->answer_id	= 0; // we will update later
		$esDiscussion->last_reply_id= 0; // we will update later
		$esDiscussion->title		= $jsParent->title;
		$esDiscussion->content 		= $jsParent->message;
		$esDiscussion->created_by	= $jsParent->creator;
		$esDiscussion->hits			= 0;
		$esDiscussion->state		= 1;
		$esDiscussion->created		= $jsParent->created;
		$esDiscussion->last_replied	= $jsParent->lastreplied;
		$esDiscussion->votes		= 0;
		$esDiscussion->total_replies= 0;
		$esDiscussion->lock			= $jsParent->lock;
		$esDiscussion->params		= '';

		$esDiscussion->store();

		//TODO: add discusstion creation stream
		$group = FD::group( $jsParent->esgroupid );

		// Create a new stream item for this discussion
		$stream = FD::stream();

		// Get the stream template
		$tpl		= $stream->getTemplate();

		// Someone just joined the group
		$tpl->setActor( $esDiscussion->created_by , SOCIAL_TYPE_USER );

		// Set the context
		$tpl->setContext( $esDiscussion->id , 'discussions' );

		// Set the verb
		$tpl->setVerb( 'create' );

		// Set the params to cache the group data
		$registry 	= FD::registry();
		$registry->set( 'group' 	, $group );
		$registry->set( 'discussion', $esDiscussion );

		// Set the cluster
		$tpl->setCluster( $jsParent->esgroupid , SOCIAL_TYPE_GROUP, $group->type );

		$tpl->setParams( $registry );
		$tpl->setDate( $jsParent->created );

		$tpl->setAccess('core.view');

		// Add the stream
		$stream->add( $tpl );

		$this->log( 'groupdiscussionsparent', $jsParent->id, $esDiscussion->id );

		return $esDiscussion;
	}

	private function processGroupCategory()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*';
		$query .= ' from `#__community_groups_category` as a';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupcategory' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		// echo $query;exit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsGroupCats = $db->loadObjectList();

		if( count( $jsGroupCats ) <= 0 )
		{
			return null;
		}

		// TODO: get superadmin id
		$userModel 		= FD::model( 'Users' );
		$superadmins 	= $userModel->getSiteAdmins();
		$adminId 		= ( $superadmins ) ? $superadmins[0]->id : '42';

		foreach( $jsGroupCats as $jsGroupCat )
		{
			$esClusterCat = FD::table( 'ClusterCategory' );

			$esClusterCat->type 	= SOCIAL_TYPE_GROUP;
			$esClusterCat->title 	= $jsGroupCat->name;
			$esClusterCat->alias 	= $jsGroupCat->name;
			$esClusterCat->description = $jsGroupCat->description;
			$esClusterCat->created 	= FD::date()->toMySQL();
			$esClusterCat->state 	= SOCIAL_STATE_PUBLISHED;
			$esClusterCat->uid 		= $adminId; // default to superadmin id

			$esClusterCat->store();

			// we no longer need to create the default steps items as the store function in cluster catgories will do the job.
			// $this->createDefaultStepItems( $esClusterCat->id );

			$this->log( 'groupcategory', $jsGroupCat->id, $esClusterCat->id );
			$this->info->setInfo( 'Group category \'' . $jsGroupCat->name . '\' is now migrated into EasySocial with id \'' . $esClusterCat->id . '\'.' );

		}// end foreach

		return $this->info;
	}


	private function createDefaultStepItems( $groupId )
	{
		// Read the default profile json file first.
		$path 		= SOCIAL_ADMIN_DEFAULTS . '/fields/group.json';

		$contents	= JFile::read( $path );

		$json 		= FD::json();
		$defaults 	= $json->decode( $contents );

		$newStepIds = array();

		// Let's go through each of the default items.
		foreach( $defaults as $step )
		{
			// Create default step for this profile.
			$stepTable 				= FD::table( 'FieldStep' );
			$stepTable->bind( $step );

			// always set this to yes.
			// $stepTable->visible_display = 1;

			// Map the correct uid and type.
			$stepTable->uid 		= $groupId;
			$stepTable->type 		= SOCIAL_TYPE_CLUSTERS;

			$stepTable->state 					= SOCIAL_STATE_PUBLISHED;
			$stepTable->sequence 				= 1;
			$stepTable->visible_registration 	= SOCIAL_STATE_PUBLISHED;
			$stepTable->visible_edit 			= SOCIAL_STATE_PUBLISHED;
			$stepTable->visible_display 		= SOCIAL_STATE_PUBLISHED;

			// Try to store the default steps.
			$state 			= $stepTable->store();

			$newStepIds[] 	= $stepTable->id;

			// Now we need to create all the fields that are in the current step
			if( $step->fields && $state )
			{

				foreach( $step->fields as $field )
				{
					$appTable 		= FD::table( 'App' );
					$appTable->loadByElement( $field->element , SOCIAL_TYPE_GROUP , SOCIAL_APPS_TYPE_FIELDS );

					$fieldTable		= FD::table( 'Field' );


					$fieldTable->bind( $field );

					// Ensure that the main items are being JText correctly.
					$fieldTable->title 			= $field->title;
					$fieldTable->description	= $field->description;
					$fieldTable->default 		= isset( $field->default ) ? $field->default : '';

					// Set the app id.
					$fieldTable->app_id 	= $appTable->id;

					// Set the step.
					$fieldTable->step_id 	= $stepTable->id;

					// Set this to be published by default.
					$fieldTable->state 		= isset( $field->state ) ? $field->state : SOCIAL_STATE_PUBLISHED;

					// Set this to be searchable by default.
					$fieldTable->searchable = isset( $field->searchable ) ? $field->searchable : SOCIAL_STATE_PUBLISHED;

					// Set this to be searchable by default.
					$fieldTable->required = isset( $field->required ) ? $field->required : SOCIAL_STATE_PUBLISHED;

					// // Set this to be searchable by default.
					// $fieldTable->required = isset( $field->required ) ? $field->required : SOCIAL_STATE_PUBLISHED;

					$fieldTable->display_title 			= 1;
					$fieldTable->display_description 	= 1;
					$fieldTable->visible_registration 	= 1;
					$fieldTable->visible_edit 			= 1;
					$fieldTable->visible_display 		= isset( $field->visible_display ) ? $field->visible_display : SOCIAL_STATE_PUBLISHED;


					// Check if the default items has a params.
					if( isset( $field->params ) )
					{
						$fieldTable->params 	= FD::json()->encode( $field->params );
					}

					// Store the field item.
					$fieldTable->store();

					// set the unique key
					$fieldTable->checkUniqueKey();
					$fieldTable->store();

				}
			}
		}

		return $newStepIds;
	}

	private function processGroups()
	{
		$config = FD::config();
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.uid as `escatid`';
		$query .= ' from `#__community_groups` as a';
		$query .= ' inner join `#__social_migrators` as c on a.`categoryid` = c.`oid` and c.`element` = ' . $db->Quote( 'groupcategory' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groups' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsGroups = $db->loadObjectList();

		if( count( $jsGroups ) <= 0 )
		{
			return null;
		}

		foreach( $jsGroups as $jsGroup )
		{
			$esGroup = FD::table( 'Cluster' );

			$esGroup->category_id		= $jsGroup->escatid;
			$esGroup->cluster_type 		= SOCIAL_TYPE_GROUP;
			$esGroup->creator_type 		= SOCIAL_TYPE_USER;
			$esGroup->creator_uid		= $jsGroup->ownerid;
			$esGroup->title				= $jsGroup->name;
			$esGroup->description		= $jsGroup->description;
			$esGroup->alias 			= JFilterOutput::stringURLSafe( $jsGroup->name );
			$esGroup->state				= $jsGroup->published;
			$esGroup->created			= $jsGroup->created;
			$esGroup->params			= null; // TODO: check what params we need to store.
			$esGroup->hits				= 0;
			$esGroup->type 				= $jsGroup->approvals == 1 ? SOCIAL_GROUPS_PRIVATE_TYPE : SOCIAL_GROUPS_PUBLIC_TYPE;
			$esGroup->key 				= ''; // TODO: check what is this key for

			$state = $esGroup->store();

			if( $state )
			{
				// Add group creation stream.
				if( $config->get( 'groups.stream.create' ) )
				{
					$stream				= FD::stream();
					$streamTemplate		= $stream->getTemplate();

					// Set the actor
					$streamTemplate->setActor( $jsGroup->ownerid , SOCIAL_TYPE_USER );

					// Set the context
					$streamTemplate->setContext( $esGroup->id , SOCIAL_TYPE_GROUPS );

					$streamTemplate->setVerb( 'create' );
					$streamTemplate->setSiteWide();
					// $streamTemplate->setPublicStream( 'core.view' );


					// Set the params to cache the group data
					$registry	= FD::registry();
					$registry->set( 'group' , $esGroup );

					// Set the params to cache the group data
					$streamTemplate->setParams( $registry );

					$streamTemplate->setDate( $jsGroup->created );

					$streamTemplate->setAccess('core.view');

					// Add stream template.
					$stream->add( $streamTemplate );
				}
				//end add stream

				$this->log( 'groups', $jsGroup->id, $esGroup->id );

				$this->info->setInfo( 'Group \'' . $jsGroup->name . '\' has migrated succefully into EasySocial.' );
			}

		}//end foreach

		return $this->info;
	}

	private function processMembers()
	{
		$config = FD::config();
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.`uid` as `esgroupid`, d.`ownerid`, d.`created` as `joindate`';
		$query .= ' from `#__community_groups_members` as a';
		$query .= ' inner join `#__social_migrators` as c on a.`groupid` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
		$query .= ' inner join `#__community_groups` as d on a.`groupid` = d.`id`';
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`memberid` = b.`oid` and b.`element` = concat_ws(' . $db->Quote('.') . ',' . $db->Quote( 'groupmembers' ) . ', a.`groupid` ) and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`groupid` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsMembers = $db->loadObjectList();

		if( count( $jsMembers ) <= 0 )
		{
			return null;
		}

		foreach( $jsMembers as $jsMember )
		{
			$esMember = FD::table( 'ClusterNode' );

			$esMember->cluster_id	= $jsMember->esgroupid;
			$esMember->uid 			= $jsMember->memberid;
			$esMember->type 		= SOCIAL_TYPE_USER;
			$esMember->created		= $jsMember->joindate; // use group creation date as joined date.
			$esMember->state		= $jsMember->approved ? SOCIAL_GROUPS_MEMBER_PUBLISHED : SOCIAL_GROUPS_MEMBER_PENDING;
			$esMember->owner		= ( $jsMember->ownerid == $jsMember->memberid ) ? 1 : 0;
			$esMember->admin		= ( $jsMember->ownerid == $jsMember->memberid ) ? 1 : 0;
			$esMember->invited_by	= 0;

			$esMember->store();

			/* We cant add the member join stream because JomSocial did not store the join date. */

			$this->log( 'groupmembers' . '.' . $jsMember->groupid , $jsMember->memberid, $esMember->id );

			$this->info->setInfo( 'Member id \'' . $jsMember->memberid. '\' from Group \'' . $jsMember->groupid . '\' has migrated succefully into EasySocial.' );

		}

		return $this->info;
	}

	private function processAvatar()
	{
		$config = FD::config();
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, c.uid as `esgroupid`';
		$query .= ' from `#__community_groups` as a';
		$query .= ' inner join `#__social_migrators` as c on a.`id` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupavatar' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsGroups = $db->loadObjectList();

		if( count( $jsGroups ) <= 0 )
		{
			return null;
		}

		foreach( $jsGroups as $jsGroup )
		{
			if( !$jsGroup->avatar )
			{
				// no need to process further.
				$this->log( 'groupavatar', $jsGroup->id , $jsGroup->id );

				$this->info->setInfo( 'Group ' . $jsGroup->id . ' is using default avatar. no migration is needed.' );
				continue;
			}


			$imagePath = JPATH_ROOT . '/' . $jsGroup->avatar;

			$tmp 		= explode( '/', $imagePath );
			$filename 	= $tmp[ count( $tmp ) - 1 ];

			if( !JFile::exists( $imagePath ) )
			{
				$this->log( 'groupavatar', $jsGroup->id , $jsGroup->id );

				$this->info->setInfo( 'Group ' . $jsGroup->id . ' the avatar image file is not found from the server. Process aborted.');
				continue;
			}

			// lets copy this file to tmp folder 1st.
			$tmp 			= JFactory::getConfig()->get( 'tmp_path' );
			$tmpImageFile 	= $tmp . '/' . md5( JFactory::getDate()->toSql() );
			JFile::copy( $imagePath , $tmpImageFile );

			$image = FD::image();
			$image->load( $tmpImageFile );

			$avatar	= FD::avatar( $image, $jsGroup->esgroupid, SOCIAL_TYPE_GROUP );

			// Check if there's a profile photos album that already exists.
			$albumModel	= FD::model( 'Albums' );

			// Retrieve the group's default album
			$album 	= $albumModel->getDefaultAlbum( $jsGroup->esgroupid , SOCIAL_TYPE_GROUP , SOCIAL_ALBUM_PROFILE_PHOTOS );
			$album->user_id = $jsGroup->ownerid;
			$album->store();

			$photo 				= FD::table( 'Photo' );
			$photo->uid 		= $jsGroup->esgroupid ;
			$photo->user_id 	= $jsGroup->ownerid ;
			$photo->type 		= SOCIAL_TYPE_GROUP;
			$photo->album_id 	= $album->id;
			$photo->title 		= $filename;
			$photo->caption 	= '';
			$photo->ordering	= 0;

			// We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
			$photo->state 		= SOCIAL_PHOTOS_STATE_TMP;

			// Try to store the photo first
			$state 		= $photo->store();

			// Push all the ordering of the photo down
			$photosModel = FD::model( 'photos' );
			$photosModel->pushPhotosOrdering( $album->id , $photo->id );

			// Render photos library
			$photoLib 	= FD::get( 'Photos' , $image );
			$storage    = $photoLib->getStoragePath($album->id, $photo->id);
			$paths 		= $photoLib->create( $storage );

			// Create metadata about the photos
			foreach( $paths as $type => $fileName )
			{
				$meta 				= FD::table( 'PhotoMeta' );
				$meta->photo_id		= $photo->id;
				$meta->group 		= SOCIAL_PHOTOS_META_PATH;
				$meta->property 	= $type;
				$meta->value		= $storage . '/' . $fileName;

				$meta->store();
			}

			// Create the avatars now, but we do not want the store function to create stream.
			// so we pass in the option. we will create the stream our own.
			$options = array( 'addstream' => false );
			$avatar->store( $photo, $options );

			// @Add stream item when a new event cover is uploaded
			// get the cover update date.
			$uploadDate = $this->getMediaUploadDate('groups.avatar.upload', $jsGroup->id);

			if (!$uploadDate) {
				// if empty, then lets just use event creation date.
				$uploadDate = $jsGroup->created;
			}

			$photo->addPhotosStream( 'uploadAvatar', $uploadDate );

			// log into mgirator
			$this->log( 'groupavatar', $jsGroup->id , $photo->id );

			$this->info->setInfo( 'Group avatar ' . $jsGroup->id . ' is now migrated into EasySocial.' );

		}

		return $this->info;

	}

	private function processPhotos()
	{

		$config = FD::config();
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select c.`uid` as `esgroupid`, b.`groupid`, a.*';
		$query .= ' from `#__community_photos` as a';
		$query .= ' inner join `#__community_photos_albums` as b on a.`albumid` = b.`id` and b.`type` = ' . $db->Quote( 'group' );
		$query .= ' inner join `#__social_migrators` as c on b.`groupid` = c.`oid` and c.`element` = ' . $db->Quote( 'groups' ) . ' and c.`component` = ' . $db->Quote( 'com_community' );
		$query .= ' where not exists ( ';
		$query .= '		select b.`id` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'groupphotos' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`storage` = ' . $db->Quote( 'file' );
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;


		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsPhotos = $db->loadObjectList();

		if( count( $jsPhotos ) <= 0 )
		{
			return null;
		}

		foreach( $jsPhotos as $jsPhoto )
		{
			if(! $jsPhoto->published )
			{
				// photos not published. do not migrate.

				// log into mgirator
				$this->log( 'groupphotos', $jsPhoto->id, -1 );

				$this->info->setInfo( 'Photo with id \'' . $jsPhoto->id . '\' is currently in unpublished or delete state. Photo migration process aborted.' );
				continue;
			}

			$imagePath = JPATH_ROOT . '/' . $jsPhoto->original;

			if( !JFile::exists( $imagePath ) )
			{
				// files from originalphotos not found. let try to get it from photos folder instead.

				// images/photos/84/1/e03fbd75d6e8f5fe0e542665.jpg
				$imagePath = JPATH_ROOT . '/' . $jsPhoto->image;
			}

			if( !JFile::exists( $imagePath ) )
			{
				// both image from originalphotos and photos folder not found. Lets give up.

				// log into mgirator
				$this->log( 'groupphotos', $jsPhoto->id, -1 );

				$this->info->setInfo( 'Photo with id \'' . $jsPhoto->id . '\' not found in the server. Photo migration process aborted.' );
				continue;
			}


			// lets get this photo album
			$esAlbumId = $this->processJSPhotoAlbum( $jsPhoto );

			// lets copy this file to tmp folder 1st.
			$tmp 			= JFactory::getConfig()->get( 'tmp_path' );
			$tmpImageFile 	= $tmp . '/' . md5( JFactory::getDate()->toSql() );
			JFile::copy( $imagePath , $tmpImageFile );

			$esPhoto = FD::table( 'Photo' );

			$esPhoto->uid 			= $jsPhoto->esgroupid;
			$esPhoto->type 			= SOCIAL_TYPE_GROUP;
			$esPhoto->user_id 		= $jsPhoto->creator;
			$esPhoto->album_id 		= $esAlbumId;

			// we use the filename as the title instead of caption.
			$fileName 				= JFile::getName( $imagePath );
			$esPhoto->title 		= $fileName;
			$esPhoto->caption 		= $jsPhoto->caption;

			$esPhoto->created 		= $jsPhoto->created;
			$esPhoto->assigned_date	= $jsPhoto->created;

			$esPhoto->ordering 		= $this->getPhotoOrdering( $esAlbumId );
			$esPhoto->featured 		= '0';
			$esPhoto->state 		= ( $jsPhoto->published ) ? '1' : '0';

			// Let's test if exif exists
			$exif 				= FD::get( 'Exif' );

			// Load the iamge object
			$image 	= FD::image();
			$image->load( $tmpImageFile );

			// Detect the photo caption and title if exif is available.
			if( $exif->isAvailable() && $image->hasExifSupport() )
			{
				// Load the image
				$exif->load( $tmpImageFile );

				$title 			= $exif->getTitle();
				$caption		= $exif->getCaption();
				$createdAlias	= $exif->getCreationDate();

				if( $createdAlias )
				{
					$esPhoto->assigned_date 	= $createdAlias;
				}

				if( $title )
				{
					$esPhoto->title 	= $title;
				}

				if( $caption )
				{
					$esPhoto->caption	= $caption;
				}
			}

			$esPhoto->store();

			// Get the photos library
			$photoLib 	= FD::get( 'Photos' , $image );
			$storage    = $photoLib->getStoragePath($esAlbumId , $esPhoto->id);
			$paths 		= $photoLib->create( $storage );

			// Create metadata about the photos
			foreach( $paths as $type => $fileName )
			{
				$meta 				= FD::table( 'PhotoMeta' );
				$meta->photo_id		= $esPhoto->id;
				$meta->group 		= SOCIAL_PHOTOS_META_PATH;
				$meta->property 	= $type;
				$meta->value		= $storage . '/' . $fileName;

				$meta->store();
			}

			// add photo stream
			$esPhoto->addPhotosStream( 'create', $jsPhoto->created );

			//lets add cover photo into this photo's album
			$album = FD::table( 'Album' );
			$album->load( $esAlbumId );

			if( ! $album->hasCover() )
			{
				$album->cover_id = $esPhoto->id;
				$album->store();
			}

			// log into mgirator
			$this->log( 'groupphotos', $jsPhoto->id, $esPhoto->id );

			$this->info->setInfo( 'Photo with id \'' . $jsPhoto->id . '\' from group \'' . $jsPhoto->groupid . '\' is now migrated into EasySocial.' );

		}

		return $this->info;

	}

	private function getPhotoOrdering( $albumId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select max( `ordering` ) from `#__social_photos`';
		$query .= ' where `album_id` = ' . $db->Quote( $albumId );

		$sql->raw( $query );
		$db->setQuery( $sql );

		$ordering = $db->loadResult();

		return ( empty( $ordering ) ) ? '1' : $ordering + 1;
	}


	private function processJSPhotoAlbum( $jsPhoto )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.*, b.`uid` as `esalbumid`';
		$query .= ' from `#__community_photos_albums` as a';
		$query .= '		left join `#__social_migrators` as b on a.id = b.oid and b.`element` = ' . $db->Quote( 'groupalbums' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' where a.id = ' . $db->Quote( $jsPhoto->albumid );

		$sql->raw( $query );
		$db->setQuery( $sql );

		$jsAlbum = $db->loadObject();


		if( $jsAlbum->esalbumid )
		{
			// this album already migrated. lets us this es album id.
			return $jsAlbum->esalbumid;
		}


		// this album not yet migrate. lets do it!
		$esAlbum = FD::table( 'Album' );

		// Set the album creation alias
		$esAlbum->assigned_date = $jsAlbum->created;
		$esAlbum->created 		= $jsAlbum->created;

		// Set the uid and type.
		$esAlbum->uid 		= $jsPhoto->esgroupid;
		$esAlbum->type 		= SOCIAL_TYPE_GROUP;
		$esAlbum->user_id 	= $jsAlbum->creator;

		// @todo: get the album cover photo.
		$esAlbum->cover_id 	= '0';

		$esAlbum->title 	= $jsAlbum->name;
		$esAlbum->caption 	= $jsAlbum->name;
		$esAlbum->params 	= null;
		$esAlbum->core 		= '0';

		// Try to store the album
		$esAlbum->store();

		$this->log( 'groupalbums', $jsAlbum->id, $esAlbum->id );

		return $esAlbum->id;
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
