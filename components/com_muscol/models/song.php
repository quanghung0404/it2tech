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

class ArtistsModelSong extends JModelLegacy
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
		$this->_prev_song_data	= null;
		$this->_next_song_data	= null;
		$this->_ids_same_album	= null;		

	}
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 	' SELECT s.*, s.artist_id as real_artist_id, al.name as album_name,al.image,al.year,al.subartist,al.artist_id,ar.artist_name,ar.letter, ar2.artist_name as real_artist_name '.
						' FROM #__muscol_songs AS s '.
						' LEFT JOIN #__muscol_albums AS al ON s.album_id = al.id '.						
						' LEFT JOIN #__muscol_artists AS ar ON ar.id = al.artist_id '.
						' LEFT JOIN #__muscol_artists AS ar2 ON ar2.id = s.artist_id '.
						' WHERE s.id = '.$this->_id
						;
			
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			
			if (!$this->_data) {
				$this->_data = new stdClass();
				$this->_data->id = 0;
				
				$this->_data->name = "";
				$this->_data->artist_name = "";
				$this->_data->artist_id = 0;
				$this->_data->real_artist_id = 0;
				$this->_data->real_artist_name = "";
				$this->_data->length = "";
				$this->_data->letter = "";
				$this->_data->album_id = 0;
				$this->_data->album_name = "";
				$this->_data->disc_num = "";
				$this->_data->num = "";
				$this->_data->position = "";
				$this->_data->video = "";
				$this->_data->buy_link = "";
				$this->_data->review = "";
				$this->_data->songwriters = "";
				$this->_data->chords = "";
				$this->_data->lyrics = "";
				$this->_data->filename = "";
				$this->_data->genre_id = "";
				
			}
			else{

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
			
			if(!$this->_data->real_artist_id) $this->_data->real_artist_id = $this->_data->artist_id ;
			if(!$this->_data->real_artist_name) $this->_data->real_artist_name = $this->_data->artist_name ;
			
			$time = MusColHelper::time_to_array($this->_data->length);
			$this->_data->hours = $time["hours"];
			$this->_data->minuts = $time["minuts"];
			$this->_data->seconds = $time["seconds"];	
			
		}

		return $this->_data;
	}
	
	function get_ids_same_album(){
		if (empty( $this->_ids_same_album )) {
			$album = $this->getData();
			$album_id = $album->album_id;
			$query = "	SELECT id FROM #__muscol_songs 
						WHERE album_id = " .$album_id ." 
						ORDER BY disc_num,num ";
			
			$this->_db->setQuery( $query );
			
			$groups = $this->_db->loadObjectList();
			//print_r( $this->_ids_same_format_group);die;
			foreach($groups as $group){
				$this->_ids_same_album[] = $group->id ;
			}
			
		}
		return $this->_ids_same_album;
	}
	function getPrevSongData(){

		if (empty( $this->_prev_song_data )) {

			$ids = $this->get_ids_same_album();
			
			$id_ant = false;
			
			if(is_array($ids) && !empty($ids)){
				$position = array_search($this->_id, $ids);
				if($position > 0) $id_ant = $ids[$position - 1];
				
				if($id_ant){
					$query = 	' SELECT * '.
								' FROM #__muscol_songs '.
								' WHERE id = '.$id_ant.
								' LIMIT 1 ';
					$this->_db->setQuery( $query );
					$this->_prev_song_data = $this->_db->loadObject();
				}
			}
		}
		
		return $this->_prev_song_data;
	}
	
	function getNextSongData(){

		if (empty( $this->_next_song_data )) {

			$ids = $this->get_ids_same_album();

			$id_pos = false;
			
			if(is_array($ids) && !empty($ids)){
				$position = array_search($this->_id, $ids);
				if($position < (count($ids) - 1)) $id_pos = $ids[$position + 1];
				
				if($id_pos){
					$query = 	' SELECT * '.
								' FROM #__muscol_songs '.
								' WHERE id = '.$id_pos.
								' LIMIT 1 ';
					$this->_db->setQuery( $query );
					$this->_next_song_data = $this->_db->loadObject();
				}
			}
		}

		return $this->_next_song_data;
	}
	
	function getComments(){
		if (empty( $this->comments )) {
			$query =	' SELECT c.*,u.name as username FROM #__muscol_comments as c ' .
						' LEFT JOIN #__users as u ON u.id = c.user_id ' .
						' WHERE c.album_id = ' . $this->_id . ' AND c.comment_type = "song" ' .
						' ORDER BY c.date ' ;
			$this->_db->setQuery($query);
			$this->comments = $this->_db->loadObjectList();
		}
		return $this->comments;
		
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
	
	function getRatedByThisUser(){
		if (empty( $this->is_rated )){
			
			$user = JFactory::getUser();

				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_ratings '.
							' WHERE user_id = ' . $user->id .
							' AND album_id = ' . $this->_id .
							' AND type = "song" '
							;
				
				$this->_db->setQuery( $query );
				$this->is_rated = $this->_db->loadResult();
				if($this->is_rated){
					$query = 	' SELECT points '.
								' FROM #__muscol_ratings '.
								' WHERE user_id = ' . $user->id .
								' AND album_id = ' . $this->_id  .
								' AND type = "song" '
								;
					
					$this->_db->setQuery( $query );
					$this->is_rated = $this->_db->loadResult();
				}
		}
			
		return $this->is_rated;				
			
	}
	
	function getAverageRating(){
		if (empty( $this->average_rating )){

				$query = 	' SELECT AVG(points) '.
							' FROM #__muscol_ratings '.
							' WHERE album_id = ' . $this->_id .
							' AND type = "song" ' ;
				
				$this->_db->setQuery( $query );
				$this->average_rating = $this->_db->loadResult();

		}
			
		return $this->average_rating;	
	}
	
	function getNumRating(){
		if (empty( $this->num_rating )){

				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_ratings '.
							' WHERE album_id = ' . $this->_id .
							' AND type = "song" ' ;
				
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
			return $data['album_id'];
		}
		//print_r($row);die();
		return true;
	}
	
	
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}
	
	function getRegisterHit($id = false, $type = 3){
		if(!$id) $id = $this->_id ;
		
		if($id){
			$row = $this->getTable('statistic');
	
			$data['reference_id'] = $id ;
			$data['type'] = $type ;
			
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