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

class KomentoComeasyblog extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'id',
		'title'			=> 'title',
		'hits'			=> 'hits',
		'created_by'	=> 'created_by',
		'catid'			=> 'category_id',
		'permalink'		=> 'permalink'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'helper.php' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			$this->_item = EasyBlogHelper::getTable( 'Blog', 'Table' );

			if( !$this->_item->load($cid) )
			{
				return $this->onLoadArticleError( $cid );
			}

			$blogger	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blogger->load( $this->_item->created_by );

			$this->_item->blogger = $blogger;

			$link = 'index.php?option=com_easyblog&view=entry&id=' . $this->getContentId();

			// forcefully get item id if request is ajax
			$format = JRequest::getString( 'format', 'html' );
			if( $format === 'ajax' )
			{
				$itemid = JRequest::getInt( 'pageItemId' );
				$link .= '&Itemid=' . $itemid;
			}

			$link	= EasyBlogRouter::_( $link );
			$this->_item->permalink = $this->prepareLink( $link );

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds( $categories = '' )
	{
		$db		= Komento::getDBO();
		$query = '';

		if( empty( $categories ) )
		{
			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' ORDER BY `id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT a.id, a.title, a.parent_id, count(b.id) - 1 AS level'
				. ' FROM `#__easyblog_category` AS a'
				. ' INNER JOIN `#__easyblog_category` AS b ON a.lft BETWEEN b.lft and b.rgt'
				. ' GROUP BY a.id'
				. ' ORDER BY a.lft ASC';

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

	public function getCommentAnchorId()
	{
		return 'comments';
	}

	public function isListingView()
	{
		// integration done in Easyblog
		return false;
	}

	public function isEntryView()
	{
		return JRequest::getCmd('view') == 'entry';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'entry' && $options['trigger']  == 'onDisplayComments' )
		{
			return $html;
		}
	}

	public function getEventTrigger()
	{
		return 'onDisplayComments';
	}

	public function getAuthorName()
	{
		return $this->_item->blogger->getName();
	}

	public function getAuthorPermalink()
	{
		return $this->_item->blogger->getProfileLink();
	}

	public function getAuthorAvatar()
	{
		return $this->_item->blogger->getAvatar();
	}
}
