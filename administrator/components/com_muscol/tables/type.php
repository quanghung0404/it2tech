<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableType extends JTable
{

	var $id = null;
	var $type_name = null;
		
	function TableType(& $db) {
		parent::__construct('#__muscol_type', 'id', $db);
	}
	
	function check(){
	
		return true;
	}
	
}