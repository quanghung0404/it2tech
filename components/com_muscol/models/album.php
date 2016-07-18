<?php

/** 
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class ArtistsModelAlbum extends JModelLegacy
{
	  
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id');
		$this->setId((int)$id);
				
	}


	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
		$this->_songs	= null;
		$this->_prev_album_data	= null;
		$this->_next_album_data	= null;
		$this->_ids_same_format_group	= null;
		

	}
	
	function getTypesArray(){
			if (empty( $this->_types_array )){
				$query = ' SELECT id,type_name FROM #__muscol_type ';
				$this->_db->setQuery( $query );
				$this->_types_array = $this->_db->loadAssocList('id');
			}
			
		return $this->_types_array;
	
	}
	
	function &getData()
	{
		
		// Load the data
		if (empty( $this->_data )) {
			$query = 	' SELECT f.*,dg.format_name as display_group_name,al.*,ar.artist_name,ar.letter,ge.genre_name '.
						' FROM #__muscol_albums as al '.
						' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
						' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
						' LEFT JOIN #__muscol_format as dg ON dg.id = f.display_group '.
						' LEFT JOIN #__muscol_genres as ge ON ge.id = al.genre_id '.
						' WHERE al.id = ' . $this->_id
						;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			
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
				$this->_data->tags_original = array();
			}
			else{
				
				if($this->_data->display_group_name == "") $this->_data->display_group_name = $this->_data->format_name;
				
				$time = MusColHelper::time_to_array($this->_data->length);
				$this->_data->hours = $time["hours"];
				$this->_data->minuts = $time["minuts"];
				$this->_data->seconds = $time["seconds"];
							
				// types
				$types_array = $this->getTypesArray();
				
				$this->_data->types_original = explode(",",$this->_data->types);
	
				$this->_data->types = explode(",",$this->_data->types);
				$text = "";
				
				if(!empty($this->_data->types)){
					for($j = 0; $j < count($this->_data->types) ; $j++){
						if(isset($types_array[$this->_data->types[$j]]["type_name"])) $text = $types_array[$this->_data->types[$j]]["type_name"] ;
						$this->_data->types[$j] = JText::_( $text );
					}
				}
				
				// i ara les etiquetes
				
				$this->_data->tags_original = explode(",",$this->_data->tags);
				
				$tags = explode(",",$this->_data->tags);
				for($k = 0; $k < count($tags); $k++){
					if($tags[$k] != ""){
						$query = 	' SELECT id,tag_name,icon '.
									' FROM #__muscol_tags '.
									' WHERE id = '.$tags[$k]
									;
						$this->_db->setQuery( $query );
						$tags[$k] = $this->_db->loadObject();	
					}
				}
				$this->_data->tags = $tags;
					
			}
		}

		return $this->_data;
	}
	
	function getAverageRating(){
		if (empty( $this->average_rating )){

				$query = 	' SELECT AVG(points) '.
							' FROM #__muscol_ratings '.
							' WHERE album_id = ' . $this->_id .
							' AND ( type = "album" OR type = "" ) ' ;
				
				$this->_db->setQuery( $query );
				$this->average_rating = $this->_db->loadResult();

		}
			
		return $this->average_rating;	
	}
	
	function getRatedByThisUser(){
		if (empty( $this->is_rated )){
			
			$user = JFactory::getUser();

				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_ratings '.
							' WHERE user_id = ' . $user->id .
							' AND album_id = ' . $this->_id .
							' AND ( type = "" OR type = "album" ) '
							;
				
				$this->_db->setQuery( $query );
				$this->is_rated = $this->_db->loadResult();
				if($this->is_rated){
					$query = 	' SELECT points '.
								' FROM #__muscol_ratings '.
								' WHERE user_id = ' . $user->id .
								' AND album_id = ' . $this->_id .
								' AND ( type = "" OR type = "album" ) '
								;
					
					$this->_db->setQuery( $query );
					$this->is_rated = $this->_db->loadResult();
				}
		}
			
		return $this->is_rated;				
			
	}
	
	function &getCompilationAlbums(){
		if (empty( $this->_albums_data )){

				$query = 	' SELECT f.*,al.*,ar.artist_name,ar.letter,ge.genre_name '.
							' FROM #__muscol_albums as al '.
							' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
							' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
							' LEFT JOIN #__muscol_genres as ge ON ge.id = al.genre_id '.
							' WHERE al.part_of_set = ' . $this->_id . 
							' ORDER BY f.order_num,f.id,year,month ';
				
				$this->_db->setQuery( $query );
				$this->_albums_data = $this->_db->loadObjectList();
				
				$types_array = $this->getTypesArray();
				
				for($i = 0, $n = count($this->_albums_data); $i < $n; $i++){
					
					// i ara les etiquetes
					$tags = explode(",",$this->_albums_data[$i]->tags);
					for($k = 0; $k < count($tags); $k++){
						if($tags[$k] != ""){
							$query = 	' SELECT tag_name,icon '.
										' FROM #__muscol_tags '.
										' WHERE id = '.$tags[$k].
										' LIMIT 1 ';
							$this->_db->setQuery( $query );
							$tags[$k] = $this->_db->loadObject();	
						}
					}
					$this->_albums_data[$i]->tags = $tags;
					
					// traduim els numeros dels types a paraules
					$this->_albums_data[$i]->types = explode(",",$this->_albums_data[$i]->types);
					if(!empty($this->_albums_data[$i]->types)){
						for($k = 0; $k < count($this->_albums_data[$i]->types) ; $k++){
							$this->_albums_data[$i]->types[$k] = JText::_( $types_array[$this->_albums_data[$i]->types[$k]]["type_name"] );
						}
					}
					
					//mirem la puntuacio Average
					$query = 	' SELECT AVG(points) '.
								' FROM #__muscol_ratings '.
								' WHERE album_id = ' . $this->_albums_data[$i]->id ;
					
					$this->_db->setQuery( $query );
					$this->_albums_data[$i]->average_rating = $this->_db->loadResult();
					
					
					//mirem quantes cançons hi ha
					$query = 	' SELECT COUNT(*) '.
								' FROM #__muscol_songs '.
								' WHERE album_id = ' . $this->_albums_data[$i]->id ;
					
					$this->_db->setQuery( $query );
					$this->_albums_data[$i]->num_songs = $this->_db->loadResult();
					
					
					//mirem quants comentaris hi ha
					
					$params =JComponentHelper::getParams( 'com_muscol' );
					switch($params->get('commentsystem')){ 
						
						case 'jomcomment':
							 $query = 	' SELECT COUNT(*) '.
										' FROM #__jomcomment '.
										' WHERE contentid = ' . ( 100000000 + $this->_albums_data[$i]->id ) . ' AND `option` = "com_muscol" ' ;
							
							$this->_db->setQuery( $query );
							$this->_albums_data[$i]->num_comments = $this->_db->loadResult();
							
							 break;
						default:
							$query = 	' SELECT COUNT(*) '.
										' FROM #__muscol_comments '.
										' WHERE album_id = ' . $this->_albums_data[$i]->id ;
							
							$this->_db->setQuery( $query );
							$this->_albums_data[$i]->num_comments = $this->_db->loadResult();
						break;
					}
					//print_r($this->_albums_data[$i]);					
				}
			
		}
			
		return $this->_albums_data;		
		
	}
	
	function get_ids_same_format_group(){
		if (empty( $this->_ids_same_format_group )) {
			$album = $this->getData() ;
			$artist_id = $album->artist_id;
			$format_id = $album->format_id;
			$display_group = $album->display_group;

			$query = "	SELECT al.id FROM #__muscol_albums  as al
						LEFT JOIN #__muscol_format as fo ON fo.id = al.format_id 
						WHERE al.artist_id = $artist_id 
							AND (display_group = '$format_id' OR format_id = '$format_id' OR format_id = '$display_group') 
						 ORDER BY year,month";
			
			$this->_db->setQuery( $query );
			$groups = $this->_db->loadObjectList();
			//print_r( $this->_ids_same_format_group);die;
			foreach($groups as $group){
				$this->_ids_same_format_group[] = $group->id ;
			}
		}
		
		return $this->_ids_same_format_group;
	}
	
	function getPrevAlbumData(){

		if (empty( $this->_prev_album_data )) {

			$ids = $this->get_ids_same_format_group();
			
			$id_ant = false;
			
			if(is_array($ids) && !empty($ids)){

				$position = array_search($this->_id, $ids);
				if($position > 0) $id_ant = $ids[$position - 1];
				
				if($id_ant){
	
					$query = 	' SELECT #__muscol_format.*,#__muscol_albums.*,#__muscol_artists.artist_name,#__muscol_artists.letter '.
								' FROM #__muscol_albums '.
								' LEFT JOIN #__muscol_artists ON #__muscol_artists.id = #__muscol_albums.artist_id '.
								' LEFT JOIN #__muscol_format ON #__muscol_format.id = #__muscol_albums.format_id '.
								' WHERE #__muscol_albums.id = '.$id_ant.
								' LIMIT 1 ';
					$this->_db->setQuery( $query );
					$this->_prev_album_data = $this->_db->loadObject();
				}
			}
		}

		return $this->_prev_album_data;
	}
	
	function getNextAlbumData(){

		if (empty( $this->_next_album_data )) {

			$ids = $this->get_ids_same_format_group();

			$id_pos = false;
			
			if(is_array($ids) && !empty($ids)){

				$position = array_search($this->_id, $ids);
				if($position < (count($ids) - 1)) $id_pos = $ids[$position + 1];
				
				if($id_pos){
					$query = 	' SELECT #__muscol_format.*,#__muscol_albums.*,#__muscol_artists.artist_name,#__muscol_artists.letter '.
								' FROM #__muscol_albums '.
								' LEFT JOIN #__muscol_artists ON #__muscol_artists.id = #__muscol_albums.artist_id '.
								' LEFT JOIN #__muscol_format ON #__muscol_format.id = #__muscol_albums.format_id '.
								' WHERE #__muscol_albums.id = '.$id_pos.
								' LIMIT 1 ';
					$this->_db->setQuery( $query );
					$this->_next_album_data = $this->_db->loadObject();
				}
			}
		}

		return $this->_next_album_data;
	}
	
	
	function getSongs()
	{
		// Load the data
		if (empty( $this->_songs )) {
			$query = 	' SELECT s.*,ar.artist_name '.
						' FROM #__muscol_songs as s '.
						' LEFT JOIN #__muscol_artists as ar ON ar.id = s.artist_id ' .
						' WHERE s.album_id = '.$this->_id.
						' ORDER BY s.disc_num,s.num ';
			$this->_db->setQuery( $query );
			$this->_songs = $this->_db->loadObjectList();
			
			if(!empty($this->_songs) && JRequest::getVar('layout') == 'form'){
				for($i = 0, $n = count($this->_songs); $i < $n; $i++){
					$time = MusColHelper::time_to_array($this->_songs[$i]->length);
					$this->_songs[$i]->hours = $time["hours"];
					$this->_songs[$i]->minuts = $time["minuts"];
					$this->_songs[$i]->seconds = $time["seconds"];	
				}
			}
		}

		return $this->_songs;
	}
	
	function getRandomId(){
		//funcio que utilitza el modul mod_muscol_random_album
		$query = 	' SELECT id '.
					' FROM #__muscol_albums ';
					
		$this->_db->setQuery( $query );
		$ids = $this->_db->loadResultArray();
		
		return $ids[rand(0,count($ids))];

	}
	
	function getComments(){
		if (empty( $this->comments )) {
			$query =	' SELECT c.*,u.name as username FROM #__muscol_comments as c ' .
						' LEFT JOIN #__users as u ON u.id = c.user_id ' .
						' WHERE c.album_id = ' . $this->_id . ' AND (c.comment_type = "album" OR c.comment_type = "") ' .
						' ORDER BY c.date ' ;
			$this->_db->setQuery($query);
			$this->comments = $this->_db->loadObjectList();
		}
		return $this->comments;
		
	}
	
	function store_comment(){
		$row = $this->getTable('comment');

		$data = JRequest::get( 'post' );

		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
				
		return true;
	}
	
	function GetHits()
	{
		$mainframe = JFactory::getApplication();

		if ($this->_id)
		{
			
			$table = $this->getTable();
			$table->hit($this->_id);
			return $this->_data->hits;
		}
		return false;
	}
	
	function getNumRating(){
		if (empty( $this->num_rating )){

				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_ratings '.
							' WHERE album_id = ' . $this->_id .
							' AND ( type = "" OR type = "album" ) ' ;
				
				$this->_db->setQuery( $query );
				$this->num_rating = $this->_db->loadResult();

		}
			
		return $this->num_rating;	
	}
	
	function getArtistsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_artists_data )){
				
				$params =JComponentHelper::getParams( 'com_muscol' );
				$user = JFactory::getUser();
				
				if($params->get('add_albums_own_artists')) $user_id = $user->id ;
				else $user_id = 0;
				
				$this->_artists_data = MusColHelper::getArtistList($user_id);
			}
			
		return $this->_artists_data;
	
	}
	
	function getAlbumsData(){

			if (empty( $this->albums_data )){
				
				$user = JFactory::getUser();
				
				$query = 	' SELECT al.id,al.name,ar.artist_name,f.format_name FROM #__muscol_albums as al '.
							' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id ' .
							' LEFT JOIN #__muscol_format as f ON f.id = al.format_id ' .
							' WHERE al.id != ' . $this->_id .
							' AND al.user_id = '. $user->id.
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
				
				$this->_types_data = MusColHelper::getTypeList();
				
				$empty_element = new stdClass();

				$empty_element->id = "";
				$empty_element->type_name = "";
				array_unshift( $this->_types_data, $empty_element );
			}
			
		return $this->_types_data;
	
	}
	
	function getGenresData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_genres_data )){
				
				$this->_genres_data = MusColHelper::getGenresData();
				
			}
			
		return $this->_genres_data;
	
	}
	
	function getTagsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_tags_data )){
				
				$this->_tags_data = MusColHelper::getTagList();
				
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
				//if($value != "") // nomes guardem si hi ha nom...
				$this->save_song($song_data);
			}
		}

		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onSaveSong', array ($row->id));
				
		return $row->id;
	}
	
	function save_song($data)
	{	
		$row = $this->getTable('song');
		$mainframe = JFactory::getApplication();
		
		//print_r($data);die();
		
		$id3 = MusColHelper::ID3active() ;
		if($id3){
			$data = MusColHelper::getID3data($data, $data['song_file']['tmp_name']) ;	
		}

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
		
		return true;
	}
	
	function store_simple($data, $datafiles = array())
	{	
		$row = $this->getTable();
		
		if($data["show_separately"] == "on") $data["show_separately"] = "N";
		else $data["show_separately"] = "Y";
		
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
			
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
				
		return $row->id;
	}
	
	function delete()
	{
		$id = JRequest::getVar( 'id' );

		$row = $this->getTable();

		if ( $id ) {
			
				$query = ' DELETE FROM #__muscol_songs WHERE album_id = ' . $id ;
				$this->_db->setQuery($query) ;
				$this->_db->query();
				
				if (!$row->delete( $id )) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}
			
		}
		return true;
	}
	
	function getRegisterHit($id = false){
		if(!$id) $id = $this->_id ;
		
		if($id){
			$row = $this->getTable('statistic');
	
			$data['reference_id'] = $id ;
			$data['type'] = 1 ;
			
			// Bind the form fields to the statistics table
			if (!$row->bind($data)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
	
			if (!$row->check()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
	
			if (!$row->store()) {
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
	}

	
}