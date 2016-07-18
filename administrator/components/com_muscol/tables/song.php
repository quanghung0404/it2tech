<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );


class TableSong extends JTable
{

	var $id = null;

	var $name = null;
	var $album_id = null;
	var $artist_id = null;
	var $num = null;
	var $disc_num = null;
	var $position = null;
	var $lyrics = null;
	var $filename = null;
	var $extension = null;
	var $length = null;
	var $added = null;
	var $review = null;
	var $songwriters = null;
	var $chords = null;
	var $genre_id = null;
	var $video = null;
	var $hits = null;
	var $buy_link = null;
	var $downloaded = null;
	var $user_id = null;
	
	//this fields do not exist on the database
	var $song_file = null;
	var $hours = null;
	var $minuts = null;
	var $seconds = null;

	var $tags = null;
	
	function TableSong(& $db) {
		parent::__construct('#__muscol_songs', 'id', $db);
	}
	
	function check(){

		$mainframe = JFactory::getApplication();
		
		if($this->id == 0){
			$this->id = null ;
			$this->added =  date('Y-m-d H:i:s') ;
			
			$user = JFactory::getUser();
			$this->user_id = $user->id;
		}
		
		$allowed_types = array("audio/mp3", "audio/x-mp3", "application/ogg", "audio/mpeg", "audio/x-wav","audio/wav","audio/wave", "audio/ogg", "audio/ogg", "video/mp4", "video/webm", "audio/webm", "audio/aac", "audio/x-m4a", "audio/m4a");
		
		if($this->song_file["name"] != "" && in_array($this->song_file["type"], $allowed_types)){
			$success_file = false;
			$success_file = $this->guardarSong($this->song_file);
			if($success_file) $this->filename = $success_file;
			
		}
		elseif($this->song_file["name"] != "" && !in_array($this->song_file["type"], $allowed_types)){
			$mainframe->enqueueMessage(JText::sprintf('FILE_NOT_UPLOADED_INVALID_FILETYPE', $this->song_file["name"], $this->song_file["type"]), 'warning');
		}
		
		if(!$this->name) $this->name = $this->filename ;
		if(!$this->name) $this->name = JText::_('SONG') ;
		
		if(!$this->position) $this->position = $this->num ;
		
		$this->song_file = null;

		$this->tags = implode(",",$this->tags);
		
		// the time in seconds
		
		if(!$this->length) $this->length = $this->hours * 3600 + $this->minuts * 60 + $this->seconds ;
		$this->hours = null;
		$this->minuts = null;
		$this->seconds = null;
			
		return true;
	}	
	
	function guardarSong($file){

		$mainframe = JFactory::getApplication();

		$filename = $file["name"];

		if ($file["error"] > 0){
			$mainframe->enqueueMessage(JText::sprintf('FILE_NOT_UPLOADED_CAUSE_'.$file["error"], $file["name"], $file["error"]), 'warning');
		    return false;
		}
	  
	  //mirem l'extensio darxiu
	  $ext = substr($filename, strrpos($filename, '.') + 1);
	  
	  $this->extension = $ext ;
	  
	  $params =JComponentHelper::getParams( 'com_muscol' );
	  
	  $folder = trim($params->get('songspath'));
	  
	  if($folder == "") $path = "/songs";
	  else $path = $folder;
	  
	  if(substr($folder,0,1) != "/") $path = "/" . $folder;
	  
	  
	  if(substr($path, -1) == "/") $path = substr($path, 0, -1);
	  
	  //$path = ".." . $path . "/" ;
	  
	  $path = JPATH_SITE . $path . "/" ;
	  
	  //echo $path; die();

		if (file_exists($path . $filename)){
		
			  $filename = time()."_".$filename;
			  move_uploaded_file($file["tmp_name"], $path . $filename);
			
			  return $filename;
		  }
		else{
		  move_uploaded_file($file["tmp_name"], $path . $filename);
		  return $filename;
		  }
	
	}
	
}