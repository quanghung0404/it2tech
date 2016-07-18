<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'abstract.php' );

class KomentoComJEvents extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'id',
		'title'			=> 'summary',
		'hits'			=> 'hits',
		'created_by'	=> 'created_by',
		'catid'			=> 'catid'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jevents' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'helper.php' );

		parent::__construct( $component );
	}

	/**
	 * Method to load a plugin object by content id number
	 *
	 * @access	public
	 *
	 * @return	object	Instance of this class
	 */
	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			// Since we are using _rp_id as the cid key instead of _ev_id due to jevents repetition issue
			// #__jevents_repetition eventid join to #__jevents_vevent ev_id
			// #__jevents_repetition eventdetail_id join to #__jevents_vevdetail evdet_id

			$db		= Komento::getDBO();

			$query	= 'SELECT ';
			$query .= $db->nameQuote( 'repeat' )	. '.' . $db->nameQuote( 'eventid' )			. ',';
			$query .= $db->nameQuote( 'repeat' )	. '.' . $db->nameQuote( 'eventdetail_id' )	. ',';
			$query .= $db->nameQuote( 'repeat' )	. '.' . $db->nameQuote( 'rp_id' )			. ' AS ' . $db->nameQuote( 'id' )			. ',';
			$query .= $db->nameQuote( 'event' )		. '.' . $db->nameQuote( 'ev_id' )			. ' AS ' . $db->nameQuote( 'ev_id' )		. ',';
			$query .= $db->nameQuote( 'event' )		. '.' . $db->nameQuote( 'catid' )			. ' AS ' . $db->nameQuote( 'catid' )		. ',';
			$query .= $db->nameQuote( 'event' )		. '.' . $db->nameQuote( 'uid' )				. ' AS ' . $db->nameQuote( 'uid' )			. ',';
			$query .= $db->nameQuote( 'event' )		. '.' . $db->nameQuote( 'created_by' )		. ' AS ' . $db->nameQuote( 'created_by' )	. ',';
			$query .= $db->nameQuote( 'detail' )	. '.' . $db->nameQuote( 'dtstart' )			. ' AS ' . $db->nameQuote( 'dtstart' )		. ',';
			$query .= $db->nameQuote( 'detail' )	. '.' . $db->nameQuote( 'summary' )			. ' AS ' . $db->nameQuote( 'summary' )		. ',';
			$query .= $db->nameQuote( 'detail' )	. '.' . $db->nameQuote( 'hits' )			. ' AS ' . $db->nameQuote( 'hits' );

			$query .= ' FROM ' . $db->nameQuote( '#__jevents_repetition' ) . ' AS ' . $db->nameQuote( 'repeat' );

			$query .= ' LEFT JOIN ' . $db->nameQuote( '#__jevents_vevent' ) . ' AS ' . $db->nameQuote( 'event' );
			$query .= ' ON ' . $db->nameQuote( 'repeat' ) . '.' . $db->nameQuote( 'eventid' ) . ' = ' . $db->nameQuote( 'event' ) . '.' . $db->nameQuote( 'ev_id' );

			$query .= ' LEFT JOIN ' . $db->nameQuote( '#__jevents_vevdetail' ) . 'AS ' . $db->nameQuote( 'detail' );
			$query .= ' ON ' . $db->nameQuote( 'repeat' ) . '.' . $db->nameQuote( 'eventdetail_id' ) . ' = ' . $db->nameQuote( 'detail' ) . '.' . $db->nameQuote( 'evdet_id' );

			$query .= ' WHERE ' . $db->nameQuote( 'repeat' ) . '.' . $db->nameQuote( 'rp_id' ) . ' = ' . $db->quote( $cid );

			$db->setQuery( $query );
			$this->_item = $db->loadObject();

			if( empty( $this->_item ) )
			{
				return $this->onLoadArticleError( $cid );
			}

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds( $categories = '' )
	{
		$db		= Komento::getDBO();
		$query	= '';

		if( empty( $categories ) )
		{
			$query = 'SELECT `ev_id` FROM ' . $db->nameQuote( '#__jevents_vevent' ) . ' ORDER BY `ev_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `ev_id` FROM ' . $db->nameQuote( '#__jevents_vevent' ) . ' WHERE `catid` IN (' . $categories . ') ORDER BY `ev_id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT a.id, a.title, a.level, a.parent_id, a.title AS name, a.parent_id AS parent'
				. ' FROM `#__categories` AS a'
				. ' WHERE a.extension = ' . $db->quote( 'com_jevents' )
				. ' AND a.parent_id > 0'
				. ' ORDER BY a.lft';

		if( Komento::joomlaVersion() == '1.5' )
		{
			$query	= 'SELECT a.id, a.title'
				. ' FROM `#__categories` AS a'
				. ' ORDER BY a.ordering';
		}

		$db->setQuery( $query );
		$categories = $db->loadObjectList();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			foreach( $categories as &$row )
			{
				$repeat = ( $row->level - 1 >= 0 ) ? $row->level - 1 : 0;
				$row->treename = str_repeat( '.&#160;&#160;&#160;', $repeat ) . ( $row->level - 1 > 0 ? '|_&#160;' : '' ) . $row->title;
			}
		}

		return $categories;
	}

	public function isListingView()
	{
		// We don't want to load anything on the listing view.
		return false;
	}

	public function isEntryView()
	{
		$task 	= JRequest::getCmd( 'task' );

		return stristr( $task , '.detail' ) !== false;
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		$task 		= JRequest::getCmd( 'task' );
		$listing 	= array();

		// @task: JEvents does not output the appended text, but it only outputs the response.

		if( stristr( $task , '.detail' ) !== false )
		{
			return $html;
		}
	}

	public function getEventTrigger()
	{
		return 'onAfterDisplayContent';
	}

	public function getContentPermalink()
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jevents' . DIRECTORY_SEPARATOR . 'jevents.defines.php' );
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jevents' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'helper.php' );

		$Itemid		= JEVHelper::getItemid();

		// TODO: Need a method to determine this
		$task 		= 'icalrepeat.detail';

		$title 		= JFilterOutput::stringURLSafe( $this->_item->summary );

		$date 		= Komento::getDate( $this->_item->dtstart );
		$link 		= 'index.php?option=com_jevents&task=' . $task . '&evid=' . $this->_item->id . '&Itemid=' . $Itemid
					. '&year=' . $date->toFormat( '%Y' ) . '&month=' . $date->toFormat( '%m' ) . '&day=' . $date->toFormat( '%d' )
					. '&title=' . $title . '&uid=' . $this->_item->uid;

		$link = $this->prepareLink( $link );

		return $link;
	}

	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		if( empty( $article->_ev_id ) && empty( $article->_rp_id ) )
		{
			return false;
		}

		// JEvents has an entry of repeat even if the event is not repeated.
		// Repeated entry has the same _ev_id but the link to the event is based on _rp_id instead.
		// _rp_id should be the cid key instead of _ev_id
		$article->id = $article->_ev_id;

		if( !empty( $article->_rp_id ) )
		{
			$article->id = $article->_rp_id;
		}

		return true;
	}
}
