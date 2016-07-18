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

class KomentoComvirtuemart extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'virtuemart_product_id',
		'title'			=> 'product_name',
		'hits'			=> 'hits',
		'created_by'	=> 'created_by',
		'catid'			=> 'catid'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'vmtable.php' );
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'config.php' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			defined('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart');

			JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'tables' );
			$product	= JTable::getInstance( 'Products', 'Table' );

			if( !$product->load($cid) )
			{
				return $this->onLoadArticleError( $cid );
			}

			$instances[$cid] = $product;
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
			$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' ORDER BY `virtuemart_product_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' WHERE `virtuemart_category_id` IN (' . $categories . ') ORDER BY `virtuemart_product_id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT c.`virtuemart_category_id` AS id, l.`category_name` AS title, cx.`category_parent_id` AS parent_id,'
				. ' l.`category_name` AS name, cx.`category_parent_id` AS parent'
				. ' FROM `#__virtuemart_categories_en_gb` as l'
				. ' JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)'
				. ' LEFT JOIN `#__virtuemart_category_categories` AS cx ON l.`virtuemart_category_id` = cx.`category_child_id`'
				. ' ORDER BY c.`ordering`';
		$db->setQuery( $query );
		$categories	= $db->loadObjectList();

		$children = array();

		foreach( $categories as $row )
		{
			$pt		= $row->parent_id;
			$list	= @$children[$pt] ? $children[$pt] : array();
			$list[] = $row;
			$children[$pt] = $list;
		}

		$categories	= JHTML::_( 'menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

		return $categories;
	}

	public function isListingView()
	{
		$views = array('virtuemart', 'category');

		return in_array(JRequest::getCmd('view'), $views);
	}

	public function isEntryView()
	{
		return JRequest::getCmd('view') == 'productdetails';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		// introtext, text, excerpt, intro, content
		if( $view == 'listing' )
		{
			return $html;
		}

		if( $view == 'entry' )
		{
			if( Komento::joomlaVersion() == '1.5' )
			{
				$article->text .= $html;
			}
			return $html;
		}
	}

	public function getEventTrigger()
	{
		$entryTrigger = ( Komento::joomlaVersion() > '1.5' ) ? 'onContentAfterDisplay' : 'onPrepareContent';

		return $entryTrigger;
	}

	public function getAuthorId()
	{
		return $this->_item->created_by ? $this->_item->created_by : $this->_item->modified_by;
	}

	public function getCategoryId()
	{
		$db	= Komento::getDBO();
		$query	= 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id` = ' . $db->quote( $this->getContentId() );
		$db->setQuery( $query );

		$productCategory = $db->loadResult();

		return $productCategory;
	}

	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		if( !is_object($article) && !property_exists($article, 'id') )
		{
			return false;
		}

		return true;
	}

	public function getContentPermalink()
	{
		$productCategory = $this->getCategoryId();

		$productCategory ? $productCategory : JRequest::getInt( 'virtuemart_category_id', 0 );

		$link	= 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$this->_item->virtuemart_product_id.'&virtuemart_category_id='.$productCategory;

		$link = $this->prepareLink( $link );

		return $link;
	}
}
