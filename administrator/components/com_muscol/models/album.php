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

class AlbumsModelAlbum extends JModelLegacy
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
		$this->_formats_data	= null;		
		$this->_genres_data	= null;	
		$this->_tags_data	= null;	
		$this->_types_data	= null;	
		
	}

	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT al.*, ar.artist_name FROM #__muscol_albums as al '.
					' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id ' .
					'  WHERE al.id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			
			if($this->_data){
				//$this->_data->genres = explode(",",$this->_data->genres);
				$this->_data->tags = explode(",",$this->_data->tags);
				$this->_data->types = explode(",",$this->_data->types);
				
				$time = $this->time_to_array($this->_data->length);
				$this->_data->hours = $time["hours"];
				$this->_data->minuts = $time["minuts"];
				$this->_data->seconds = $time["seconds"];
			}
		}
		//print_r( $this->_data);die();
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->name = "";
			$this->_data->subtitle = "";
			$this->_data->artist_id = 0;
			$this->_data->subartist = "";
			$this->_data->format_id = 0;
			$this->_data->ndisc = "";
			$this->_data->year = "";
			$this->_data->month = "";
			$this->_data->hours = "";
			$this->_data->minuts = "";
			$this->_data->seconds = "";
			$this->_data->price = "";
			$this->_data->album_file = "";
			$this->_data->buy_link = "";
			$this->_data->keywords = "";
			$this->_data->edition_year = "";
			$this->_data->edition_country = "";
			$this->_data->edition_month = "";
			$this->_data->label = "";
			$this->_data->catalog_number = "";
			$this->_data->edition_details = "";
			$this->_data->metakeywords = "";
			$this->_data->metadescription = "";
			$this->_data->image = "";
			$this->_data->name2 = "";
			$this->_data->artist2 = "";
			$this->_data->review = "";
			$this->_data->artist_name = "";
			$this->_data->part_of_set = 0;
			$this->_data->show_separately = "";

			$this->_data->tags = array();
			$this->_data->types = array();
		}
		return $this->_data;
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
	
	function getAlbumsData(){

			if (empty( $this->albums_data )){
				$query = 	' SELECT al.id,al.name,ar.artist_name,f.format_name FROM #__muscol_albums as al '.
							' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id ' .
							' LEFT JOIN #__muscol_format as f ON f.id = al.format_id ' .
							' WHERE al.id != ' . $this->_id .
						 	' ORDER BY ar.letter,ar.class_name,f.order_num,al.year,al.month';
				$this->_db->setQuery( $query );
				$this->albums_data = $this->_db->loadObjectList();
			}
			//print_r($this->albums_data);die();
		return $this->albums_data;
	
	}
	
	function getFormatsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_formats_data )){
				$query = ' SELECT id,format_name FROM #__muscol_format '.
						 ' ORDER BY order_num';
				$this->_db->setQuery( $query );
				$this->_formats_data = $this->_db->loadObjectList();
			}
			
		return $this->_formats_data;
	
	}
	function getTypesData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_types_data )){
				$query = ' SELECT id,type_name FROM #__muscol_type '.
						 ' ORDER BY id';
				$this->_db->setQuery( $query );
				$this->_types_data = $this->_db->loadObjectList();
				
				$empty_element = new stdClass();

				$empty_element->id = "";
				$empty_element->type_name = "";
				array_unshift( $this->_types_data, $empty_element );
			}
			
		return $this->_types_data;
	
	}
	function getSongs(){

			if (empty( $this->songs )){
				$query = 	' SELECT * FROM #__muscol_songs '.
							' WHERE album_id = ' . $this->_id .
						 	' ORDER BY disc_num, num ';
				$this->_db->setQuery( $query );
				$this->songs = $this->_db->loadObjectList();
				
				if(!empty($this->songs)){
					for($i = 0, $n = count($this->songs); $i < $n; $i++){
						$time = $this->time_to_array($this->songs[$i]->length);
						$this->songs[$i]->hours = $time["hours"];
						$this->songs[$i]->minuts = $time["minuts"];
						$this->songs[$i]->seconds = $time["seconds"];	
					}
				}
			}
			//print_r($this->songs);die();
		return $this->songs;
	
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
	
	function rate($id,$points){
		
		$query = ' UPDATE #__muscol_albums SET points = '.$points.' WHERE id = '.$id ;
		$this->_db->setQuery( $query );
		$this->_db->query();
		
		$query = ' SELECT name FROM #__muscol_albums WHERE id = '.$id ;
		$this->_db->setQuery( $query );
		$album_name = $this->_db->loadResult();
		//$album_name = $album_name[0];
		//return $query;
		return "<div class='message_green'>" . $album_name . JText::_( ' has been rated with ' ).$points.JText::_( ' out of ' ).'5</div>';
		
	}
	
	function store()
	{	
		$row = $this->getTable();

		$data = JRequest::get( 'post' );
		$data['review'] = JRequest::getVar('review', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$datafiles = JRequest::get( 'files' );
		
		if($data["show_separately"] == "on") $data["show_separately"] = "N";
		else $data["show_separately"] = "Y";
		
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

		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			//print_r($row);die();
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		//print_r($row);die();
		
		if(!$data["id"]){ // it's a new album
			$data["id"] = $row->id;
		}
		
		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onSaveAlbum', array ($row->id));
		
		//les cançons
		foreach($data as $key => $value){
			if(substr($key,0,5) == "song_")	{
				$song_id = (int)substr($key,5);

				if($data["artist_id_" . $song_id]){
					$theartist_id = $data["artist_id_" . $song_id];
				}
				else{
					$theartist_id =  $data["artist_id"];
				}

				$song_data = array(
								   "id" => $song_id ,
								   "album_id" => $data["id"],
								   "artist_id" => $theartist_id,
								   "disc_num" => $data["disc_num_" . $song_id],
								   "num" => $data["num_" . $song_id],	
								   "position" => $data["position_" . $song_id],	
								   "hours" => $data["hours_" . $song_id],	
								   "minuts" => $data["minuts_" . $song_id],	
								   "seconds" => $data["seconds_" . $song_id],	
								   "name" => $value	,
								   "filename" => $data["filename_" . $song_id],	
								   "song_file" => $datafiles["song_file_" . $song_id] 
								   );
				$this->save_song($song_data);
			} //les cançons NOVES
			else if(substr($key,0,7) == "0_song_")	{
				$song_id = (int)substr($key,7);
				$song_data = array(
								   "id" => 0 ,
								   "album_id" => $data["id"],
								   "disc_num" => $data["0_disc_num_" . $song_id],
								   "num" => $data["0_num_" . $song_id],	
								   "position" => $data["0_position_" . $song_id],	
								   "hours" => $data["0_hours_" . $song_id],	
								   "minuts" => $data["0_minuts_" . $song_id],	
								   "seconds" => $data["0_seconds_" . $song_id],	
								   "name" => $value	,
								   "artist_id" => $data["artist_id"],
								   "filename" => $data["0_filename_" . $song_id],	
								   "song_file" => $datafiles["0_song_file_" . $song_id] ,
								   "tags" => $data["tags"],
								   "genre_id" => $data["genre_id"]
								   );
				// nomes guardem si hi ha nom...
				$this->save_song($song_data);
			}
		}
				
		return true;
	}
	
	function save_song($data)
	{	
		$row = $this->getTable('song');
		$mainframe = JFactory::getApplication();
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$id3 = MusColHelper::ID3active() ;
		if($id3){
			if($data['song_file']['tmp_name'] != "") $filename = $data['song_file']['tmp_name'] ;
			elseif($data['filename'] && $data['id'] == 0) {
				$filename = JPATH_SITE  . $params->get('songspath'). DS . str_replace(array("/", "\\"), DS, $data['filename'] ) ;
			}
			
			if($filename) $data = MusColHelper::getID3data($data, $filename) ;	
			
		}
		
		//print_r($data);die();

		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			//print_r($row); $mainframe->close();
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onSaveSong', array ($row->id));
		
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
				
				$results = $dispatcher->trigger('onDeleteAlbum', array ($cid));
				
				$query = ' DELETE FROM #__muscol_songs WHERE album_id = ' . $cid ;
				$this->_db->setQuery($query) ;
				$this->_db->query();
				
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

}