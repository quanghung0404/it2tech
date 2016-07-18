<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

class TablePlaylist extends JTable{

	var $id = null;
	var $title = null;
	var $description = null;
	var $user_id = null;
	var $songs = null;
	var $types = null;
	var $public = null;
	var $added = null;

	function TablePlaylist(& $db) {
		parent::__construct('#__muscol_playlists', 'id', $db);
	}
	
	function check(){
		
		$user = JFactory::getUser();
		if(!$this->id) $this->user_id = $user->id;
		
		if($this->id == 0){
			$this->added =  date('Y-m-d H:i:s') ;
			$this->user_id = $user->id;
		}
		
		return true;
	}
	
}