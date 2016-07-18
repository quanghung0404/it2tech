<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableRating extends JTable{

	var $id = null;
	var $album_id = null;
	var $user_id = null;
	var $points = null;
	var $type = null;

	function TableRating(& $db) {
		parent::__construct('#__muscol_ratings', 'id', $db);
	}
	
	function check(){
		
		$user = JFactory::getUser();
		$this->user_id = $user->id;
		
		return true;
	}
	
}