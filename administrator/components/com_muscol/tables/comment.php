<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

class TableComment extends JTable{

	var $id = null;
	var $album_id = null;
	var $user_id = null;
	var $comment = null;
	var $comment_type = null;

	function TableComment(& $db) {
		parent::__construct('#__muscol_comments', 'id', $db);
	}
	
	function check(){
		
		$user = JFactory::getUser();
		$this->user_id = $user->id;
		$this->comment = str_replace("\n","<br/>",$this->comment);
		
		return true;
	}
	
}