<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );

class EasyBlogTableMediaManager extends EasyBlogTable
{
	var $id 	= null;
	var $path 	= '';
	var $type 	= '';
	var $params	= '';

	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_mediamanager' , 'id' , $db );
	}

	public function bind( $data , $ignore = array() )
	{
		return parent::bind( $data );
	}

	public function load( $path = null , $type = true )
	{
		$db 	= EasyBlogHelper::db();

		$query 	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl );
		$query	.= ' WHERE ' . $db->nameQuote( 'path' ) . '=' . $db->Quote( $path );
		$query	.= ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );

		$db->setQuery( $query );
		$obj	= $db->loadObject();

		return parent::bind( $obj );
	}
}
