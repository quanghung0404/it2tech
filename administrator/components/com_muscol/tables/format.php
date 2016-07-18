<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableFormat extends JTable
{

	var $id = null;
	var $format_name = null;
	var $display_group = null;
	var $order_num = null;
	var $icon = null;
	
	var $format_image_file = null;

	function TableFormat(& $db) {
		parent::__construct('#__muscol_format', 'id', $db);
	}
	
	function check(){
		if($this->format_image_file["name"] != ""){
			$success_image=false;
			$success_image = $this->guardarImatge($this->format_image_file);
			if($success_image) $this->icon = $success_image;
		}
		
		if($this->display_group == $this->id ) $this->display_group = 0;
	
		return true;
	}
	
	function guardarImatge($file){
		$filename = $file["name"];

	  	if ($file["error"] > 0){
		  return false;
		}
	  	else{
			if (file_exists("../images/formats/" . $filename)){
				  $filename = time()."_".$filename;
				  move_uploaded_file($file["tmp_name"],"../images/formats/" . $filename);
				  return $filename;
			  }
			else{
				  move_uploaded_file($file["tmp_name"],"../images/formats/" . $filename);
				  return $filename;
			}
		}

	}
	
}