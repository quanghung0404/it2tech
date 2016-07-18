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

class ArtistsModelArtist extends JModelLegacy
{
	  
	function __construct()
	{
		parent::__construct();
		
		$mainframe = JFactory::getApplication();

		$id = JRequest::getVar('id');
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$default_layout = $params->get( 'albums_view' );
		
		$form = JRequest::getVar('layout') ;
		
		if($form != 'form'){
			$layout = $mainframe->getUserStateFromRequest('artist.layout','layout', $default_layout ,'layout');
			$this->setState('layout', $layout);
		}
		else{
			$this->setState('layout', 'form');
		}
		
		$this->setId((int)$id);
		
	}
	
	function getLayout(){
		if (empty($this->_layout)) {
			$this->_layout = $this->getState('layout')	;
		}
		return $this->_layout;
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
		$this->_albums_data	= null;
		$this->_layout	= $this->getState('layout');

	}
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 	' SELECT ar.*, ge.genre_name FROM #__muscol_artists as ar '.
						' LEFT JOIN #__muscol_genres as ge ON ge.id = ar.genre_id '.
						' WHERE ar.id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();

			if($this->_data){

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
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->artist_name = "";
			$this->_data->class_name = "";
			$this->_data->picture = "";
			$this->_data->image = "";
			$this->_data->keywords = "";
			$this->_data->metakeywords = "";
			$this->_data->metadescription = "";
			$this->_data->genre_id = "";
			$this->_data->city = "";
			$this->_data->country = "";
			$this->_data->url = "";
			$this->_data->years_active = "";
			$this->_data->review = "";
			$this->_data->tags_original = array();
			$this->_data->related = "";
			
		}

		return $this->_data;
	}

	function getRelated(){
		
		if (empty( $this->_related )) {
			//busquem primer els related creuats
			$query = 	' SELECT id '.
						' FROM #__muscol_artists '.
						' WHERE related LIKE "%,'.$this->_data->id.',%"'.
							' OR related LIKE "'.$this->_data->id.',%"'.
							' OR related LIKE "%,'.$this->_data->id.'"'.
							' OR related LIKE "'.$this->_data->id.'"'
						;
			$this->_db->setQuery( $query );
			$cross_related = $this->_db->loadResultArray();

			$related = explode(",",$this->_data->related);
			if($related[0] == "") $related = array();
			
			for($i = 0; $i < count($cross_related); $i++){
				$related[] = $cross_related[$i];
			}
			$related = array_unique($related);
			//print_r($related);die();
			$array_related = array();
			foreach($related as $index => $valor){
				$query = 	' SELECT id,artist_name '.
							' FROM #__muscol_artists '.
							' WHERE id = '.$valor.
							' LIMIT 1 ';
				$this->_db->setQuery( $query );
				$array_related[] = $this->_db->loadObject();		
			}
			$this->_related = $array_related;
			$this->_related_ids = $related;
		}
		return $this->_related;
	}
	function getAlsoRelated(){
		
		if (empty( $this->_also_related )) {
			
			$cross_related = array();
			$related = array();
			for($k = 0; $k < count($this->_related_ids) ; $k++){
				$query = 	' SELECT related '.
							' FROM #__muscol_artists '.
							' WHERE id = '.$this->_related_ids[$k]
							;
				$this->_db->setQuery( $query );
				$new_related = $this->_db->loadResult();
				if(!empty( $new_related ) ) $related[] = $new_related;
				//print_r($query);die();
				//if(!empty($related_prev)) $related[] = implode(",",$related_prev);
			}
			$related = implode(",",$related);
			$related = explode(",",$related);
			
			
			for($k = 0; $k < count($this->_related) ; $k++){
				//busquem primer els related creuats
				$query = 	' SELECT id '.
							' FROM #__muscol_artists '.
							' WHERE related LIKE "%,'.$this->_related[$k]->id.',%"'.
								' OR related LIKE "'.$this->_related[$k]->id.',%"'.
								' OR related LIKE "%,'.$this->_related[$k]->id.'"'.
								' OR related LIKE "'.$this->_related[$k]->id.'"'
							;
				$this->_db->setQuery( $query );
				$cross_related_prev = $this->_db->loadResultArray();
				if(!$cross_related_prev) $cross_related_prev = array();
				$cross_related[] = implode(",",$cross_related_prev);
			}
			$cross_related = implode(",",$cross_related);
			$cross_related = explode(",",$cross_related);

			if($related[0] == "") $related = array();
			
			for($i = 0; $i < count($cross_related); $i++){
				if(!empty( $cross_related[$i]) )	$related[] = $cross_related[$i];
			}
			$related = array_unique($related);
			
			$id_artista_actual = array($this->_id);
			
			$related = array_diff($related,$this->_related_ids,$id_artista_actual);
			//print_r($this->_related_ids);
			//print_r($related);die();
			
			$array_related = array();
			foreach($related as $index => $valor){
				$query = 	' SELECT id,artist_name '.
							' FROM #__muscol_artists '.
							' WHERE id = '.$valor.
							' LIMIT 1 ';
				$this->_db->setQuery( $query );
				$array_related[] = $this->_db->loadObject();		
			}
			$this->_also_related = $array_related;
			//print_r($this->_also_related);die();
		}
		return $this->_also_related;
	}
	
	function getTypesArray(){
			if (empty( $this->_types_array )){
				$query = ' SELECT id,type_name FROM #__muscol_type ';
				$this->_db->setQuery( $query );
				$this->_types_array = $this->_db->loadAssocList('id');
			}
			
		return $this->_types_array;
	
	}
	
	function getAlbumsData()
		{

			if (empty( $this->_albums_data )){
				$query = 	' SELECT * FROM #__muscol_format '.
							' WHERE display_group = 0'.
							' ORDER BY order_num ';
				$this->_db->setQuery( $query );
				$this->_albums_data = $this->_db->loadObjectList();
				
				$types_array = $this->getTypesArray();
				
				for($i=0;$i < count($this->_albums_data); $i++){
					$format_id = $this->_albums_data[$i]->id;
					$query = 	' SELECT f.*,al.*,ar.artist_name,ar.letter,ge.genre_name '.
								' FROM #__muscol_albums as al '.
								' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
								' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
								' LEFT JOIN #__muscol_genres as ge ON ge.id = al.genre_id '.
								' WHERE artist_id = '.$this->_id.
								' AND (format_id = '.$format_id.' OR display_group = '.$format_id.')'.
								' AND (part_of_set = 0 OR ( part_of_set != 0 AND show_separately = "Y" ) ) ' .
								' ORDER BY year,month ';
					
					$this->_db->setQuery( $query );
					$this->_albums_data[$i]->albums = $this->_db->loadObjectList();
					
					for($j = 0; $j < count($this->_albums_data[$i]->albums); $j++){
						// busquem si hi ha albums que pertanyin a aquest item
						
						$query = 	' SELECT id,name,image '.
									' FROM #__muscol_albums '.
									' WHERE part_of_set = ' . $this->_albums_data[$i]->albums[$j]->id
									;
						$this->_db->setQuery( $query );
						$this->_albums_data[$i]->albums[$j]->subalbums = $this->_db->loadObjectList();		
												
						// i ara les etiquetes
						$tags = explode(",",$this->_albums_data[$i]->albums[$j]->tags);
						for($k = 0; $k < count($tags); $k++){
							if($tags[$k] != ""){
								$query = 	' SELECT tag_name,icon '.
											' FROM #__muscol_tags '.
											' WHERE id = '.$tags[$k];
								$this->_db->setQuery( $query );
								$tags[$k] = $this->_db->loadObject();
							}
						}
						$this->_albums_data[$i]->albums[$j]->tags = $tags;
						
						$text = "";
						// traduim els numeros dels types a paraules
						$this->_albums_data[$i]->albums[$j]->types = explode(",",$this->_albums_data[$i]->albums[$j]->types);
						if(!empty($this->_albums_data[$i]->albums[$j]->types)){
							for($k = 0; $k < count($this->_albums_data[$i]->albums[$j]->types) ; $k++){
								if(isset($types_array[$this->_albums_data[$i]->albums[$j]->types[$k]]["type_name"])) $text = $types_array[$this->_albums_data[$i]->albums[$j]->types[$k]]["type_name"] ;
								$this->_albums_data[$i]->albums[$j]->types[$k] = JText::_( $text );
							}
							
						//mirem la puntuacio Average
						$query = 	' SELECT AVG(points) '.
									' FROM #__muscol_ratings '.
									' WHERE album_id = ' . $this->_albums_data[$i]->albums[$j]->id .
									' AND ( type = "album" OR type = "" ) ' ;
						
						$this->_db->setQuery( $query );
						$this->_albums_data[$i]->albums[$j]->average_rating = $this->_db->loadResult();
						
						
						//mirem quantes cançons hi ha
						$query = 	' SELECT COUNT(*) '.
									' FROM #__muscol_songs '.
									' WHERE album_id = ' . $this->_albums_data[$i]->albums[$j]->id ;
						
						$this->_db->setQuery( $query );
						$this->_albums_data[$i]->albums[$j]->num_songs = $this->_db->loadResult();
						
						
						//mirem quants comentaris hi ha
						
						$params =JComponentHelper::getParams( 'com_muscol' );
						switch($params->get('commentsystem')){ 
							
							case 'jomcomment':
								 $query = 	' SELECT COUNT(*) '.
											' FROM #__jomcomment '.
											' WHERE contentid = ' . ( 100000000 + $this->_albums_data[$i]->albums[$j]->id ) . ' AND `option` = "com_muscol" ' ;
								
								$this->_db->setQuery( $query );
								$this->_albums_data[$i]->albums[$j]->num_comments = $this->_db->loadResult();
								
								 break;
							default:
								$query = 	' SELECT COUNT(*) '.
											' FROM #__muscol_comments '.
											' WHERE album_id = ' . $this->_albums_data[$i]->albums[$j]->id .
											' AND (comment_type = "album" OR comment_type = "") ';
								
								$this->_db->setQuery( $query );
								$this->_albums_data[$i]->albums[$j]->num_comments = $this->_db->loadResult();
							break;
						}
						
						}
						//print_r($this->_albums_data[$i]->albums[$j]);					
					}
				}
			}
			
		return $this->_albums_data;
	
	}
	
	function getAlbumsFastnav()
	{

		if (empty( $this->_albums_fastnav )){
			$query = 	' SELECT * FROM #__muscol_format '.
						' WHERE display_group = 0'.
						' ORDER BY order_num ';
			$this->_db->setQuery( $query );
			$this->_albums_fastnav = $this->_db->loadObjectList();
			
			for($i=0, $n = count($this->_albums_fastnav); $i < $n; $i++){
				
				$format_id = $this->_albums_fastnav[$i]->id;
				$query = 	' SELECT f.*,al.*,ar.artist_name,ar.letter,ge.genre_name '.
							' FROM #__muscol_albums as al '.
							' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
							' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
							' LEFT JOIN #__muscol_genres as ge ON ge.id = al.genre_id '.
							' WHERE artist_id = '.$this->_id.
							' AND (format_id = '.$format_id.' OR display_group = '.$format_id.')'.
							' AND (part_of_set = 0 OR ( part_of_set != 0 AND show_separately = "Y" ) ) ' .
							' ORDER BY year,month ';
				
				$this->_db->setQuery( $query );
				$this->_albums_fastnav[$i]->albums = $this->_db->loadObjectList();

			}
		}
		
	return $this->_albums_fastnav;
	
	}
	
	function getComments(){
		if (empty( $this->comments )) {
			$query =	' SELECT c.*,u.name as username FROM #__muscol_comments as c ' .
						' LEFT JOIN #__users as u ON u.id = c.user_id ' .
						' WHERE c.album_id = ' . $this->_id . ' AND c.comment_type = "artist" ' .
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
	
	function getArtistsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_artists_data )){
				
				$params =JComponentHelper::getParams( 'com_muscol' );
				$user = JFactory::getUser();
				
				if($params->get('add_albums_own_artists')) $where = ' WHERE id != '.$this->_id.' AND user_id = ' . $user->id ;
				else $where = ' WHERE id != '.$this->_id ;
				
				$query = ' SELECT id,artist_name FROM #__muscol_artists '.
						 $where .
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
				
				$this->_tags_data = MusColHelper::getTagList();
				
				$empty_element = new stdClass();
				
				$empty_element->id = "";
				$empty_element->tag_name = "";
				$empty_element->icon = "";
				array_unshift( $this->_tags_data, $empty_element );
			}
			
		return $this->_tags_data;
	
	}
	
	function store($data = false)
	{	
		$row = $this->getTable();

		if(!$data){
			$data = JRequest::get( 'post' );
			$data['review'] = JRequest::getVar('review', '', 'post', 'string', JREQUEST_ALLOWRAW);
			
		}
		
		$datafiles = JRequest::get( 'files' );

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
			
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onSaveArtist', array ($row->id));

		return $row->id;
	}
	
	function delete()
	{
		$id = JRequest::getVar( 'id' );

		$row = $this->getTable();

		if ( $id ) {
			
				$query = ' SELECT s.id FROM #__muscol_songs as s LEFT JOIN #__muscol_albums as al ON al.id = s.album_id WHERE al.artist_id = ' . $id ;
				$this->_db->setQuery($query) ;
				$result = $this->_db->loadResultArray();
				
				if(!empty($result)){ 
				
					$result = implode(",",$result);
					
					$query = ' DELETE FROM #__muscol_songs WHERE id IN (' . $result . ')' ;
					$this->_db->setQuery($query) ;
					$this->_db->query();
				
				}
			
				$query = ' DELETE FROM #__muscol_albums WHERE artist_id = ' . $id ;
				$this->_db->setQuery($query) ;
				$this->_db->query();
				
				$query = ' DELETE FROM #__muscol_songs WHERE artist_id = ' . $id ;
				$this->_db->setQuery($query) ;
				$this->_db->query();
				
				if (!$row->delete( $id )) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}
			
		}
		return true;
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
	
	function getRegisterHit($id = false){
		if(!$id) $id = $this->_id ;
		
		if($id){
		
			$row = $this->getTable('statistic');
	
			$data['reference_id'] = $id ;
			$data['type'] = 2 ;
			
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