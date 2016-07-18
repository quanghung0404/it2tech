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

FD::import( 'admin:/tables/table' );

class SocialTableConfig extends SocialTable
{
	public $type         	= null;
	public $value       	= null;
	public $value_binary    = null;

	public function __construct(& $db )
	{
		parent::__construct( '#__social_config' , 'type' , $db );
	}

	public function store( $updateNulls = false )
	{
		$db 	= FD::db();
		$query  = 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
		        . 'WHERE ' . $db->nameQuote( $this->_tbl_key ) . '=' . $db->Quote( $this->{$this->_tbl_key} );
		$db->setQuery( $query );

		$exist  = (bool) $db->loadResult();

		if( !$exist )
		{
		    return $db->insertObject( $this->_tbl , $this , $this->_tbl_key );
		}
		return $db->updateObject( $this->_tbl , $this , $this->_tbl_key );
	}
}
