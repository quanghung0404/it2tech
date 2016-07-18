<?php


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableStatistic extends JTable
{

	var $id = null;
	var $value = null;
	var $valuestring = null;
	var $reference_id = null;
	var $type = null;
	var $date_event = null;
	var $ip = null;
	var $user_id = null;

	function TableStatistic(& $db) {
		parent::__construct('#__muscol_statistics', 'id', $db);
	}
	
	function check(){
		
		$user = JFactory::getUser();
		
		$this->ip = $_SERVER['REMOTE_ADDR'] ;
		$this->user_id = $user->id ;
	
		return true;
	}
	
}