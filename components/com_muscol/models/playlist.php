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

class ArtistsModelPlaylist extends JModelLegacy{
	
 	var $playlist_data = null;

	function __construct(){
		parent::__construct();
		$id = JRequest::getVar('id');
		$this->setId((int)$id);

	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
		$this->playlist_data	= null;

	}
	
	function _buildQuery(){
		if(empty($this->query)){
			
			if(!$this->playlist_data->songs) $this->playlist_data->songs = 0 ;
			
			$this->query = 	' SELECT s.*,al.name as album_name, al.image, ar.artist_name FROM #__muscol_songs as s '.
							' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' .
							' LEFT JOIN #__muscol_artists as ar ON (ar.id = s.artist_id OR ar.id = al.artist_id) ' .
							' WHERE s.id IN ('.$this->playlist_data->songs . ')' 
						;
		}
	
		return $this->query;
	}
	
	function &getData(){
		if (empty( $this->_data )) {
			$query = $this->_buildQuery();

			$object_array = $this->_getList($query);
			
			$array_songs = explode("," , $this->playlist_data->songs );
			$array_songs_types = explode("," , $this->playlist_data->types );
			//print_r($array_songs_types);die();
			
			if(is_array($object_array)){
				// we convert the object array to an associative object array
				foreach($object_array as $song_object){
					$assoc_object_array[$song_object->id] = 	$song_object ;
				}
			}
			
			$this->_data = array();
			if(is_array($array_songs)){
				// we order the songs
	
				foreach($array_songs as $song_id){
	
					$this->_data[] = 	$assoc_object_array[$song_id];
	
				}
				
			}

		}
		
		return $this->_data;	
	}
	
	function getPlaylistData(){
		if($this->_id){
			if (empty( $this->playlist_data )) {
				$query = 'SELECT pl.*, us.name AS username FROM #__muscol_playlists AS pl LEFT JOIN #__users AS us ON us.id = pl.user_id WHERE pl.id = ' . $this->_id ;
				
				$this->_db->setQuery( $query );
				$this->playlist_data = $this->_db->loadObject(); 
	
			}
		}
		else{
			$this->playlist_data = $this->load_playlist_from_session();
	
		}
		return $this->playlist_data;
	}
	
	function load_playlist_from_session(){
	
		if (empty( $this->playlist_data )) {
			$this->playlist_data = new stdClass() ;

			$session =JSession::getInstance('','');
			$playlist = $session->get('muscol_playlist') ; // the playlist is an array
			$playlist_types = $session->get('muscol_playlist_types') ;
			$user = JFactory::getUser();

			if(is_array($playlist)) $this->playlist_data->songs = implode(",", $playlist) ;
			if(is_array($playlist_types)) $this->playlist_data->types = implode(",", $playlist_types) ;
			
			$this->playlist_data->id = 0;
			$this->playlist_data->title = JText::_('On the go');
			$this->playlist_data->username = $user->name;
			$this->playlist_data->user_id = $user->id;
			$this->playlist_data->description = "";
			//print_r($this->playlist_data);die();
		}
		
		return $this->playlist_data;
	}
	
	function getComments(){
		if (empty( $this->comments )) {
			$query =	' SELECT c.*,u.name as username FROM #__muscol_comments as c ' .
						' LEFT JOIN #__users as u ON u.id = c.user_id ' .
						' WHERE c.album_id = ' . $this->_id . ' AND c.comment_type = "playlist" ' .
						' ORDER BY c.date ' ;
			$this->_db->setQuery($query);
			$this->comments = $this->_db->loadObjectList();
		}
		return $this->comments;
		
	}
	
	function add_item($id,$song_id,$type){
		if($id){
	
			$data["id"] = $id;
			
			$user = JFactory::getUser();
			
			$query = 	' SELECT songs,types FROM #__muscol_playlists ' .
						' WHERE id = ' .$id ;
			$this->_db->setQuery($query);
			$object = $this->_db->loadObject();
			
			$songs = $object->songs ;
			$types = $object->types ;
			
			if($songs) $data["songs"] = $songs . "," . $song_id;
			else $data["songs"] = $song_id;
			
			if($types) $data["types"] = $types . "," . $type;
			else $data["types"] = $type;
			
			return $this->store($data);
	
		}
		else{//if it is a provisional playlist stored on the SESSION 
			
			$session =JSession::getInstance('','');
			$playlist = $session->get('muscol_playlist') ; // the playlist is an array
			$playlist_types = $session->get('muscol_playlist_types') ;
			
			// we add the new item
			$playlist[] = $song_id ;
			$playlist_types[] = $type ;
			
			$session->set('muscol_playlist', $playlist) ;
			$session->set('muscol_playlist_types', $playlist_types) ;
			
		}
				
		return true;
	}
	
	function remove_songs()
	{
		$song_positions = JRequest::getVar( 'song_positions', array(0), 'default', 'array' );
		$playlist_id = JRequest::getVar('id') ;
		
		$playlist_data = $this->getPlaylistData();
		$array_songs = explode("," , $playlist_data->songs ) ;
		$array_types = explode("," , $playlist_data->types ) ;
		
		$final_array = array();
		$final_array_types = array();
		
		$n = count( $array_songs ) ;
		if ($n) {
			for($i = 0; $i < $n ; $i++) {
				if(!in_array($i, $song_positions)){
					$final_array[] = $array_songs[$i] ;
					$final_array_types[] = $array_types[$i] ;
				}
			}
		}
		
		if(is_array($final_array)) $final_string = implode(",", $final_array );
		else $final_string = "";
		
		if(is_array($final_array_types)) $final_string_types = implode(",", $final_array_types );
		else $final_string_types = "";
	
		if($playlist_id){
			// we store the modified playlist on the database
			//first, we check that the user making the request is the creator of the playlist
			$user = JFactory::getUser();
			if($playlist_data->user_id != $user->id) return false;
			else{
				
				$data["id"] = $playlist_id;
				$data["songs"] = $final_string;
				$data["types"] = $final_string_types;
				
				return $this->store($data);
		
			}
		}
		else{//if it is a provisional playlist stored on the SESSION 
			
			$session =JSession::getInstance('','');
			
			// we store the modified playlist on the session
			$playlist = $final_array ;
			$playlist_types = $final_array_types ;
			
			$session->set('muscol_playlist', $playlist) ;
			$session->set('muscol_playlist_types', $playlist_types) ;
			
		}
		
		return true;
	}
	
	function save_playlist_order()
	{
		$playlist_order = JRequest::getVar( 'playlist_order', array(0), 'default', 'array' );
		$playlist_id = JRequest::getVar('id') ;
		
		$playlist_data = $this->getPlaylistData();
	
		$array_songs = explode("," , $playlist_data->songs ) ;
		$array_songs_types = explode("," , $playlist_data->types ) ;
		
		$final_array = array();
		$final_array_types = array();
		
		asort($playlist_order);
		
		$n = count( $playlist_order ) ;
		
		if ($n) {
			foreach($playlist_order as $key => $value) {
				
					$final_array[] = $array_songs[$key] ;
					$final_array_types[] = $array_songs_types[$key] ;
					
			}
		}
		
		if(is_array($final_array)) $final_string = implode(",", $final_array );
		else $final_string = "";
		
		if(is_array($final_array_types)) $final_string_types = implode(",", $final_array_types );
		else $final_string_types = "";
	
		if($playlist_id){
			// we store the modified playlist on the database
			//first, we check that the user making the request is the creator of the playlist
			$user = JFactory::getUser();
			if($playlist_data->user_id != $user->id) return false;
			
			else{
				
				$data["id"] = $playlist_id;
				$data["songs"] = $final_string;
				$data["types"] = $final_string_types;
				
				return $this->store($data);
			
			}
		}
		else{//if it is a provisional playlist stored on the SESSION 
			
			$session =JSession::getInstance('','');
			
			// we store the modified playlist on the session
			$playlist = $final_array ;
			$playlist_types = $final_array_types ;
			
			$session->set('muscol_playlist', $playlist) ;
			$session->set('muscol_playlist_types', $playlist_types) ;
			
		}
		
		return true;
	}
	
	function consolidate_playlist(){
		
		$user = JFactory::getUser();
		if(!$user->id) return false;
		else{
			
			// we get the data from the playlist stored on the session
			$session =JSession::getInstance('','');
			$playlist_items = implode(",", $session->get('muscol_playlist') );
			$playlist_item_types = implode(",", $session->get('muscol_playlist_types') );
			
			$data["id"] = 0;
			$data["songs"] = $playlist_items;
			$data["types"] = $playlist_item_types;
			$data["user_id"] = $user->id;
			$data["title"] = JText::_('Playlist') . " " . JHTML::_('date', date('Y-m-d H:i:s'), JText::_('DATE_FORMAT_LC2'));
			
			return $this->store($data);
		
		}
		
		return true;
		
	}
	
	function save_playlist(){
		$data = JRequest::get( 'post' );
		$data['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$query = 'SELECT user_id FROM #__muscol_playlists WHERE id = ' . $data["id"] ;
		$this->_db->setQuery($query);
		$creator_id = $this->_db->loadResult();
		$user = JFactory::getUser();
		if($creator_id != $user->id) return false;
		
		return $this->store($data);
	}
	
	function store($data)
	{	
		$row = $this->getTable();
	
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
	
	function delete_playlist()
	{
		$cids = JRequest::getVar( 'id', array(0), 'default', 'array' );

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
	
	function getRegisterHit($id = false){
		if(!$id) $id = $this->_id ;
		
		if($id){
			$row = $this->getTable('statistic');
	
			$data['reference_id'] = $id ;
			$data['type'] = 5 ;
			
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