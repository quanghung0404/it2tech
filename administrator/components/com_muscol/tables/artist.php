<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );


class TableArtist extends JTable
{

	var $id = null;
	var $artist_name = null;
	var $letter = null;
	var $review = null;
	var $image = null;
	var $picture = null;
	var $class_name = null;
	var $keywords = null;
	var $related = null;
	var $added = null;
	var $hits = null;
	var $country = null;
	var $user_id = null;
	
	var $artist_image_file = null;
	var $artist_picture_file = null;
	
	var $metakeywords = null;
	var $metadescription = null;
	
	var $years_active = null;
	var $city = null;
	var $url = null;
	var $genre_id = null;

	var $tags = null;

	function TableArtist(& $db) {
		parent::__construct('#__muscol_artists', 'id', $db);
	}
	
	function check(){
		
		if($this->id == 0){
			$this->added =  date('Y-m-d H:i:s') ;
			
			$user = JFactory::getUser();
			
			$this->user_id = $user->id;
		}
		
		$allowed_types = array("image/jpeg", "image/png", "image/gif", "image/jpg");
		
		if($this->artist_image_file["name"] != "" && in_array($this->artist_image_file["type"], $allowed_types)){
			$success_image=false;
			$success_image = $this->guardarImatge($this->artist_image_file, "/images/artists/");
			if($success_image) $this->image = $success_image;
			//$this->artist_image_file = null;
		}
		
		if($this->artist_picture_file["name"] != "" && in_array($this->artist_picture_file["type"], $allowed_types)){
			$success_image=false;
			$success_image = $this->guardarImatge($this->artist_picture_file, "/images/artists/");
			if($success_image) $this->picture = $success_image;
			//$this->artist_image_file = null;
		}
		
		if(is_array($this->related)) $this->related = implode(",",$this->related);
		else $this->related = "";
		
		$this->prepare_artist();

		$this->tags = implode(",",$this->tags);
		
		return true;
	}
	
	function prepare_artist(){
		
		$art = $this->artist_name;
		$inicial = $this->letter;
		$class_name = $this->class_name;
		//$web_lyrics = $this->web_lyrics;
		$keywords = $this->keywords;
		
		if(!$class_name){
			if(strncasecmp($art,"The ",4)==0) $class_art = substr($art,4);
			else $class_art = $art;
		}
		else{
			$class_art = $class_name;
		}
				
		$string_key = " ".$art." ".$keywords." ";
		
		if($this->id == 0){
			$string_key = $this->get_keywords($string_key);
		}
		else{
			$string_key = $this->get_keywords($keywords);
		}
		
		if(!$inicial){
			$this->letter = substr($class_art,0,1);
			$exep = array("1","2","3","4","5","6","7","8","9","0","!","?","¿","¡","'","$","(",")","\"");
			if(in_array($this->letter,$exep))	$this->letter = "1";
		}
		$this->letter = strtoupper($this->letter);
		$this->class_name = $class_art;
		$this->keywords = $string_key;

	}

	function get_keywords($keywords){

		$return = JFilterOutput::stringURLSafe($keywords);
		$return = str_replace("-"," ",$return);
		$return = $this->erase_multiple_whitespaces(" ".$return." ");

		return $return;
	}

	function erase_multiple_whitespaces($cadena){
		$cadena2 = str_replace("  "," ",$cadena,$times);
		if($times!=0) $cadena2 = $this->erase_multiple_whitespaces($cadena2);
		return $cadena2;
	}
	
	function guardarImatge($file, $folder = "/images/artists/"){
		$filename = $file["name"];

	  	if ($file["error"] > 0){
		  return false;
		}
	  	else{
			if (file_exists(JPATH_SITE . $folder . $filename)){
				  $filename = time()."_".$filename;
				  move_uploaded_file($file["tmp_name"],JPATH_SITE . $folder . $filename);
				  return $filename;
			  }
			else{
				  move_uploaded_file($file["tmp_name"],JPATH_SITE . $folder . $filename);
				  return $filename;
			}
		}

	}
	
}