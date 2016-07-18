<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableTag extends JTable
{

	var $id = null;
	var $tag_name = null;
	var $icon = null;
	
	var $tag_image_file = null;

	function TableTag(& $db) {
		parent::__construct('#__muscol_tags', 'id', $db);
	}
	
	function check(){
		if($this->tag_image_file["name"] != ""){
			$success_image=false;
			$success_image = $this->guardarImatge($this->tag_image_file);
			if($success_image) $this->icon = $success_image;
		}
		
		return true;
	}
	
	function guardarImatge($file){
		$filename = $file["name"];

	  	if ($file["error"] > 0){
		  return false;
		}
	  	else{
			if (file_exists("../images/tags/" . $filename)){
				  $filename = time()."_".$filename;
				  move_uploaded_file($file["tmp_name"],"../images/tags/" . $filename);
				  return $filename;
			  }
			else{
				  move_uploaded_file($file["tmp_name"],"../images/tags/" . $filename);
				  return $filename;
			}
		}

	}
	
}