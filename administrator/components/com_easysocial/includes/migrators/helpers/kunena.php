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

require_once( SOCIAL_LIB . '/migrators/helpers/info.php' );

/**
 * DB layer for EasySocial.
 *
 * @since	1.1
 * @author	Sam <sam@stackideas.com>
 */
class SocialMigratorHelperKunena
{
	// component name, e.g. com_kunena
	var $name  			= null;

	// migtration steps
	var $steps 			= null;

	var $info  			= null;

	var $mapping 		= null;

	var $accessMapping 	= null;

	var $limit 		 	= null;

	var $userMapping  	= null;

	public function __construct()
	{
		$this->info     = new SocialMigratorHelperInfo();
		$this->name  	= 'com_kunena';

		$this->limit 	= 10; //10 items per cycle

		$this->steps[] 	= 'topic';
		$this->steps[] 	= 'replies';

		$this->accessMapping = array(
			'0' 	=> SOCIAL_PRIVACY_PUBLIC,
			'1'		=> SOCIAL_PRIVACY_MEMBER,
			'10'	=> SOCIAL_PRIVACY_MEMBER,
			'20'	=> SOCIAL_PRIVACY_MEMBER,
			'30'	=> SOCIAL_PRIVACY_FRIEND,
			'40'	=> SOCIAL_PRIVACY_ONLY_ME
			);
	}

	public function getVersion()
	{
		if( !$this->isComponentExist() )
		{
			return false;
		}

		// check JomSocial version.
		$xml		= JPATH_ROOT . '/administrator/components/com_kunena/kunena.xml';

		$parser = FD::get( 'Parser' );
		$parser->load( $xml );

		$version	= $parser->xpath( 'version' );
		$version 	= (float) $version[0];

		return $version;
	}

	public function isInstalled()
	{
		$file 	= JPATH_ROOT . '/components/com_kunena/kunena.php';

		if(! JFile::exists( $file ) )
		{
			return false;
		}

		return true;
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

		$file 	= JPATH_ROOT . '/components/com_kunena/kunena.php';

		if(! JFile::exists( $file ) )
		{
			$obj->message = 'Kunena not found in your site. Process aborted.';
			return $obj;
		}

		// @todo check if the db tables exists or not.


		// all pass. return object

		$obj->isvalid = true;
		$obj->count   = $this->getItemCount();

		return $obj;
	}

	public function setUserMapping( $maps )
	{
		// do nothing.
	}

	public function getItemCount()
	{
		$db = FD::db();
		$sql = $db->sql();

		$total = count( $this->steps );

		// kunena topics count
		$query = 'select count(1) as `total`';
		$query .= ' from `#__kunena_topics` as a';
		$query .= ' inner join `#__kunena_messages` as b on a.`id` = b.`thread` and a.`first_post_id` = b.`id`';
		$query .= ' where not exists ( ';
		$query .= '		select b.`oid` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'topic' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and b.`hold` = ' . $db->Quote( '0' );


		$sql->clear();
		$sql->raw( $query );
		$db->setQuery( $sql );
		$numTotal = $db->loadResult();
		$numTotal = ( $numTotal > 0 ) ? ceil( $numTotal / $this->limit ) : 0;
		$total = $total + $numTotal;

		//kunena replies
		$query = 'select count(1) as `total`';
		$query .= ' from `#__kunena_messages` as a';
		$query .= ' inner join `#__kunena_topics` as b on a.thread = b.id and a.id != b.first_post_id';

		$query .= ' where not exists ( ';
		$query .= '		select b.`oid` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'reply' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`hold` = ' . $db->Quote( '0' );

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
			case 'topic':
				$result = $this->processTopic();
				break;

			case 'replies':
				$result = $this->processReplies();
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


	private function processTopic()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query	= 'SELECT a.* from `#__kunena_topics` as a';
		$query .= ' inner join `#__kunena_messages` as b on a.`id` = b.`thread` and a.`first_post_id` = b.`id`';
		$query .= ' where not exists ( ';
		$query .= '		select b.`oid` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'topic' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and b.`hold` = ' . $db->Quote( '0' );
		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;


		$sql->raw( $query );
		$db->setQuery( $sql );


		$kPosts = $db->loadObjectList();

		if( count( $kPosts ) <= 0 )
		{
			return null;
		}

		foreach( $kPosts as $kItem )
		{
			// add stream.
			$this->addTopicStream( $kItem );

			// add log
			$this->log( 'topic', $kItem->id, $kItem->id );

			$this->info->setInfo( 'Kunena topic post with id \'' . $kItem->id . '\' processed succussfully.' );
		}

		return $this->info;
	}



	private function processReplies()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.* from `#__kunena_messages` as a';
		$query .= ' inner join `#__kunena_topics` as b on a.`thread` = b.`id` and a.`id` != b.`first_post_id`';
		$query .= ' where not exists ( ';
		$query .= '		select b.`oid` from `#__social_migrators` as b';
		$query .= ' 			where a.`id` = b.`oid` and b.`element` = ' . $db->Quote( 'reply' ) . ' and b.`component` = ' . $db->Quote( $this->name );
		$query .= ' )';
		$query .= ' and a.`hold` = ' . $db->Quote( '0' );

		// debug code
		// $query .= ' and a.id > 53348';

		$query .= ' ORDER BY a.`id` ASC';
		$query .= ' LIMIT ' . $this->limit;


		$sql->raw( $query );
		$db->setQuery( $sql );


		$kPosts = $db->loadObjectList();

		if( count( $kPosts ) <= 0 )
		{
			return null;
		}

		foreach( $kPosts as $kItem )
		{
			// add stream.
			$this->addRepliesStream( $kItem );

			// add log
			$this->log( 'reply', $kItem->id, $kItem->id );

			$this->info->setInfo( 'Kunena reply post with id \'' . $kItem->id . '\' processed succussfully.' );
		}

		return $this->info;
	}


	private function addRepliesStream( $kItem )
	{
		$stream				= FD::stream();
		$streamTemplate		= $stream->getTemplate();

		// Set the actor.
		$streamTemplate->setActor( $kItem->userid, SOCIAL_TYPE_USER );

		// Set the context.
		$streamTemplate->setContext( $kItem->id , 'kunena' );

		// Set the verb.
		$streamTemplate->setVerb( 'reply' );

		// set stream content
		$streamTemplate->setContent( $kItem->subject );

		$streamTemplate->setAccess( 'core.view', SOCIAL_PRIVACY_PUBLIC );

		// set the stream creation date
		$date = FD::date( $kItem->time );
		$streamTemplate->setDate( $date->toMySQL() );

		// Create the stream data.
		$stream->add( $streamTemplate );
	}

	private function addTopicStream( $kItem )
	{
		$stream				= FD::stream();
		$streamTemplate		= $stream->getTemplate();

		// Set the actor.
		$streamTemplate->setActor( $kItem->first_post_userid, SOCIAL_TYPE_USER );

		// Set the context.
		$streamTemplate->setContext( $kItem->id , 'kunena' );

		// Set the verb.
		$streamTemplate->setVerb( 'create' );

		// set stream content
		$streamTemplate->setContent( $kItem->first_post_message );

		$streamTemplate->setAccess( 'core.view', SOCIAL_PRIVACY_PUBLIC );

		// set the stream creation date
		$date = FD::date( $kItem->first_post_time );
		$streamTemplate->setDate( $date->toMySQL() );

		// Create the stream data.
		$stream->add( $streamTemplate );
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
