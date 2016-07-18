<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class SongsModelSong extends JModelLegacy
{
	 
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
		
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
		$this->_artists_data	= null;	
		$this->_genres_data	= null;	
		
	}

	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__muscol_songs '.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			
			$time = $this->time_to_array($this->_data->length);
			$this->_data->hours = $time["hours"];
			$this->_data->minuts = $time["minuts"];
			$this->_data->seconds = $time["seconds"];	

			$this->_data->tags = explode(",",$this->_data->tags);		
		}
		//print_r( $this->_data);die();
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;

			$this->_data->tags = array();
		}
		return $this->_data;
	}
	
	function getGenresData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_genres_data )){
				$query = ' SELECT * FROM #__muscol_genres '.
						 ' WHERE parents = "0" '
						 ;
				$this->_db->setQuery( $query );
				$this->_genres_data = $this->_db->loadObjectList();
				
				for($i = 0; $i < count( $this->_genres_data ) ; $i++){
					$this->_genres_data[$i]->sons = $this->get_descendants($this->_genres_data[$i]);	
				}
			}
			
		return $this->_genres_data;
	
	}
	
	function get_descendants($genre){

		$query = 	' SELECT * FROM #__muscol_genres '.
					' WHERE '.
					' ( parents LIKE "%,'.$genre->id.',%"'.
							' OR parents LIKE "'.$genre->id.',%" '.
							' OR parents LIKE "%,'.$genre->id.'" '.
							' OR parents LIKE "'.$genre->id.'" ) '
					;
		$this->_db->setQuery( $query );
		$return = $this->_db->loadObjectList();

		if(!empty( $return )){
			for($i = 0; $i < count( $return ) ; $i++){
				$return[$i]->sons = $this->get_descendants($return[$i]);	
			}

		}
		
		return $return;
		
	}
	
	function time_to_array($total_time){
	 
	  $segons = $total_time % 60;
	  
	  $minuts = ($total_time - $segons)/60;
	  
	  if($minuts >= 60){
	  $minuts_60 = $minuts % 60;
	  $hores = ($minuts - $minuts_60)/60;
	  }
	  else {
		  $hores=0;
		  $minuts_60 = $minuts;
	  }
	  
	  $return["hours"] = $hores;
	  $return["minuts"] = $minuts_60;
	  $return["seconds"] = $segons;	  
	  
	  return $return;
	}
	
	function getArtistFromAlbum(){
		if (empty( $this->artist )) {
			$query = 	' SELECT a.id FROM #__muscol_artists as a '.
						' LEFT JOIN #__muscol_albums as al ON al.artist_id = a.id ' .
						' WHERE al.id = '.$this->_data->album_id;
			$this->_db->setQuery( $query );
			$this->artist = $this->_db->loadResult();

		}
		return $this->artist;
	}

	function getArtistsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_artists_data )){
				$query = ' SELECT id,artist_name FROM #__muscol_artists '.
						 ' ORDER BY letter,class_name';
				$this->_db->setQuery( $query );
				$this->_artists_data = $this->_db->loadObjectList();
			}
			
		return $this->_artists_data;
	
	}

	function getTagsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_tags_data )){
				$query = ' SELECT * FROM #__muscol_tags '.
						 ' ORDER BY id';
				$this->_db->setQuery( $query );
				$this->_tags_data = $this->_db->loadObjectList();
				
				$empty_element = new stdClass();

				$empty_element->id = "";
				$empty_element->tag_name = "";
				$empty_element->icon = "";
				array_unshift( $this->_tags_data, $empty_element );
			}
			
		return $this->_tags_data;
	
	}
	
	function store()
	{	
		$row = $this->getTable();

		$data = JRequest::get( 'post' );
		$data['lyrics'] = JRequest::getVar('lyrics', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$data['review'] = JRequest::getVar('review', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$datafiles = JRequest::get( 'files' );
		
		$id3 = MusColHelper::ID3active() ;
		if($id3){
			$data = MusColHelper::getID3data($data, $datafiles['song_file']['tmp_name']) ;	
		}

		// new tag system in MC 2.4.0
		$data['tags'] = MusColHelper::getTagIDArrayFromString($data['hidden-tags']);
		
		// Bind the form fields to the album table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->bind($datafiles)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the hello record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		else{ //retornem el id de l'album!
			$album_id = $data['album_id'];
		}
		
		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onSaveSong', array ($row->id));
		
		if($album_id){ //retornem el id de l'album!
			return $album_id;
		}
		
		return true;
	}

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row = $this->getTable();
		
		$dispatcher	= JDispatcher::getInstance();
		//new plugin access
		$plugin_ok = JPluginHelper::importPlugin('muscol');

		if (count( $cids )) {
			foreach($cids as $cid) {
				
				$results = $dispatcher->trigger('onDeleteSong', array ($cid));
				
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

}