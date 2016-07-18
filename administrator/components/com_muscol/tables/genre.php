<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableGenre extends JTable
{

	var $id = null;
	var $genre_name = null;
	var $parents = null;

	function TableGenre(& $db) {
		parent::__construct('#__muscol_genres', 'id', $db);
	}
	
	function check(){
		
		if(empty($this->parents)) $this->parents = 0;
		else {
			$this->parents = array_unique($this->parents);
			$this->parents = implode(",",$this->parents);
		}
	
		return true;
	}
		
}