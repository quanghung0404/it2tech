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

class ArtistsModelSearch extends JModelLegacy
{
	  
	function __construct()
	{
		parent::__construct();
		
		$mainframe = JFactory::getApplication();
		global $option;

		$id = JRequest::getVar('id');
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		$this->params = $params ;
		
		$default_layout = $params->get( 'albums_view' );

		$layout = $mainframe->getUserStateFromRequest('search.layout','layout', $default_layout ,'layout');
		$this->setState('layout', $layout);
		
		$this->keywords = JRequest::getVar('searchword');
		$this->format_id = JRequest::getVar('format_id');
		$this->genre_id = JRequest::getVar('genre_id');
		$this->artist_id = JRequest::getVar('artist_id');
		$this->type_id = JRequest::getVar('type_id');
		$this->tag_id = JRequest::getVar('tag_id');
		$this->user_id = JRequest::getVar('user_id');
		
		$this->orderby = JRequest::getVar('orderby');
		
		$this->search = JRequest::getVar('search');
		
		if(!$this->search){
			switch(JRequest::getVar('layout')){
				case 'songs':	
					JRequest::setVar('search', 'songs');
					$this->search = 'songs' ;
				break;
				default: 
					JRequest::setVar('search', 'albums');
					$this->search = 'albums' ;
				break;
			}
		}
		
		if($this->search != "songs") $this->search = "albums";
		
		// Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('muscol.search.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		//$limitstart = $mainframe->getUserStateFromRequest('muscol.search.limitstart', 'limitstart', 0, 'int');
		$limitstart = JRequest::getVar('limitstart',0);
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//we won't use limitstart for now
		//$limitstart = 0;
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
		
	}
	
	function getLayout(){
		if (empty($this->_layout)) {
			$this->_layout = $this->getState('layout')	;
		}
		return $this->_layout;
	}

	function setId($id)
	{

		$this->_albums_data	= null;
		$this->_layout	= $this->getState('layout');

	}
	
	function getTotal(){
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
			switch($this->search){
				case "songs":
				$query = $this->_buildQuery();
				break;
				case "albums":default:
				$query = $this->_buildQueryAlbums();
				break;
			}
            $this->_total = $this->_getListCount($query);    
        }
        return $this->_total;
    }
	
	function getPagination()
  	{
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
  	}

	//if we search songs....we use pagination
	function _buildQuery(){
		if(empty($this->query)){
			
			$keywords = $this->keywords;
			$artist_id = $this->artist_id;
			$genre_id = $this->genre_id;
			$user_id = $this->user_id;
			$tag_id = $this->getTagId();
			
			$where_clause = array();
	
			if ($keywords != "") $where_clause[] = ' s.name LIKE "%'.$keywords.'%"';
			
			if ($artist_id > 0) {
				$where_clause[] = ' ( al.artist_id = '. $artist_id . ' OR s.artist_id = '.$artist_id.')';
			}
			if ($genre_id > 0) {
				
				$this->getDescendantsId($genre_id); 
				$descendants = $this->descendantsId;
				$descendants[] = $genre_id ;
				$genre_clause = ' (( s.genre_id = ' . implode(' OR s.genre_id = ',$descendants) . ' ) '.
								' OR ( s.genre_id ="" AND ( al.genre_id = ' . implode(' OR al.genre_id = ',$descendants) . ' ) ) )';
				$where_clause[] = $genre_clause;
			}

			if ($tag_id > 0) {
				$where_clause[] = ' ( s.tags LIKE "'.$tag_id.'"
									 OR s.tags LIKE "'.$tag_id.',%" 
									 OR s.tags LIKE "%,'.$tag_id.'"
									 OR s.tags LIKE "%,'.$tag_id.',%" )';
			}

			if ($user_id > 0) $where_clause[] = ' s.user_id = ' . $user_id ;
			
			// Build the where clause of the content record query
			$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
			
			$this->query = 	' SELECT s.*,al.name as album_name,al.image, ar.artist_name FROM #__muscol_songs as s '.
							' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' .
							' LEFT JOIN #__muscol_artists as ar ON ( ar.id = s.artist_id OR ar.id = al.artist_id ) ' .
							$where_clause .
							' GROUP BY s.id '.
							' ORDER BY s.artist_id, al.year, al.month, al.id, s.disc_num,s.num '
							;
							//echo $this->query; die();
		}
		return $this->query;
	}
	
	function getSongsData(){
		if (empty( $this->songs_data )) {
			$query = $this->_buildQuery();
			
			$this->songs_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
			
		}
		return $this->songs_data;	
	}

	
	function getSearchword(){
			
		return $this->keywords;
	
	}
	
	function getFormatId(){
			
		return $this->format_id;
	
	}
	
	function getArtistId(){
			
		return $this->artist_id;
	
	}
	
	function getGenreId(){
			
		return $this->genre_id;
	
	}
	
	function getTypeId(){
			
		return $this->type_id;
	
	}
	
	function getTagId(){
			
		return $this->tag_id;
	
	}

	function getTypesArray(){
			if (empty( $this->_types_array )){
				$query = ' SELECT id,type_name FROM #__muscol_type ';
				$this->_db->setQuery( $query );
				$this->_types_array = $this->_db->loadAssocList('id');
			}
			
		return $this->_types_array;
	
	}
	
	function getFormatList(){
		
		return MusColHelper::getFormatList();
	
	}
	
	function getArtistList(){
		
		return MusColHelper::getArtistList();
	
	}
	
	function getTypeList(){
	
		return MusColHelper::getTypeList();
	
	}
	
	function getTagList(){
		
		return MusColHelper::getTagList();
	
	}
	
	function get_where_clause_keywords($keywords){ //germi
	 	$keywords = utf8_decode(trim($this->get_keywords($keywords)));
		
		$keyword=explode(" ",$keywords);
		$cadena="";
		for($i=0;$i<sizeof($keyword);$i++){
			$cadena.="(al.keywords LIKE '% ".$keyword[$i]." %') AND ";
		}
		if($i>0){
			$cadena = substr($cadena,0,-5);
			$cadena = "".$cadena."";
		}
		else if ($i==0) $cadena = "";
		return $cadena;
	 }
	 
	 function get_keywords($keywords){
		return $this->erase_multiple_whitespaces(" ".$keywords." ");
	}

	function erase_multiple_whitespaces($cadena){
		$cadena2 = str_ireplace("  "," ",$cadena,$times);
		if($times!=0) $cadena2 = $this->erase_multiple_whitespaces($cadena2);
		return $cadena2;
	}
	
	function getGenresData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_genres_data )){
				$this->_genres_data = MusColHelper::getGenresData();
			}
			
		return $this->_genres_data;
	
	}
	
	var $descendantsId = null;
	
	function getDescendantsId($genre){

		$query = 	' SELECT id FROM #__muscol_genres '.
					' WHERE '.
					' ( parents LIKE "%,'.$genre.',%"'.
							' OR parents LIKE "'.$genre.',%" '.
							' OR parents LIKE "%,'.$genre.'" '.
							' OR parents LIKE "'.$genre.'" ) '
					;
		$this->_db->setQuery( $query );
		$return = $this->_db->loadResultArray();
		
		if(!empty( $return )){
			for($i = 0; $i < count( $return ) ; $i++){
				$this->descendantsId[] = $return[$i];
				$this->getDescendantsId($return[$i]);	
			}

		}
		
	}
	
	function _buildQueryAlbums(){

		if (empty( $this->query )){
			
			$keywords = $this->keywords;
			$format_id = $this->format_id;
			$genre_id = $this->genre_id;
			$user_id = $this->user_id;
			$artist_id = $this->artist_id;
			$type_id = $this->getTypeId();
			$tag_id = $this->getTagId();
			
			$orderby = $this->orderby;
			
			$where_clause = array();
	
			if ($keywords != "") $where_clause[] = $this->get_where_clause_keywords($keywords);
			
			if ($format_id > 0) {
				$where_clause[] = ' (al.format_id = '.(int) $format_id . ' OR display_group = '.(int) $format_id . ')';
			}
			if ($genre_id > 0) {
				$genre->id = $genre_id ;
				$this->getDescendantsId($genre_id); 
				$descendants = $this->descendantsId;
				$descendants[] = $genre_id ;
				$genre_clause = ' ( al.genre_id = ' . implode(' OR al.genre_id = ',$descendants) . ' ) ';
				$where_clause[] = $genre_clause;
			}
			
			if ($type_id > 0) {
				$where_clause[] = ' ( al.types LIKE "'.$type_id.'"
									 OR al.types LIKE "'.$type_id.',%" 
									 OR al.types LIKE "%,'.$type_id.'"
									 OR al.types LIKE "%,'.$type_id.',%" )';
			}
			
			if ($tag_id > 0) {
				$where_clause[] = ' ( al.tags LIKE "'.$tag_id.'"
									 OR al.tags LIKE "'.$tag_id.',%" 
									 OR al.tags LIKE "%,'.$tag_id.'"
									 OR al.tags LIKE "%,'.$tag_id.',%" )';
			}
			
			if ($artist_id > 0) $where_clause[] = ' al.artist_id = ' . $artist_id ;
			
			if ($user_id > 0) $where_clause[] = ' al.user_id = ' . $user_id ;
			
			// Build the where clause of the content record query
			$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
			
			//order by clause
			if (!$orderby) $orderby = $this->params->get('orderby_search', 'date_desc') ;
			
			switch($orderby){
				case 'year_asc':
				$orderby_clause =  ' al.year ASC, al.month ASC ' ;
				break;
				case 'year_desc':
				$orderby_clause =  ' al.year DESC, al.month DESC ' ;
				break;
				case 'name_asc':
				$orderby_clause =  ' al.name ASC ' ;
				break;
				case 'name_desc':
				$orderby_clause =  ' al.name DESC ' ;
				break;
				case 'date_asc':
				$orderby_clause =  ' al.added ASC ' ;
				break;
				case 'date_desc':
				$orderby_clause =  ' al.added DESC ' ;
				break;
				default:
				$orderby_clause =  ' al.year ASC, al.month ASC ' ;
				break;
			}
			
			$this->query = 	' SELECT f.*,al.*,ar.artist_name,ar.letter,ge.genre_name '.
							' FROM #__muscol_albums as al '.
							' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
							' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
							' LEFT JOIN #__muscol_genres as ge ON ge.id = al.genre_id '.
							$where_clause .
							' AND (part_of_set = 0 OR ( part_of_set != 0 AND show_separately = "Y" ) ) ' .
							' ORDER BY ' . $orderby_clause ;
				
			}
			
			
		return $this->query;
	
	}
	
	//this function changes on v2.0: we do not group by formats anymore, and we use pagination
	function getAlbumsData(){
		if (empty( $this->_albums_data )) {
			
			$types_array = $this->getTypesArray();
			
			$query = $this->_buildQueryAlbums();
			
			$this->_albums_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
			
			for($i = 0, $n = count($this->_albums_data); $i < $n; $i++){
				// busquem si hi ha albums que pertanyin a aquest item
				
				$query = 	' SELECT id,name,image '.
							' FROM #__muscol_albums '.
							' WHERE part_of_set = ' . $this->_albums_data[$i]->id
							;
				$this->_db->setQuery( $query );
				$this->_albums_data[$i]->subalbums = $this->_db->loadObjectList();		
										
				// i ara les etiquetes
				if($this->_albums_data[$i]->tags){
					$tags = explode(",",$this->_albums_data[$i]->tags);
					for($k = 0; $k < count($tags); $k++){
						if($tags[$k] != ""){
							$query = 	' SELECT tag_name,icon '.
										' FROM #__muscol_tags '.
										' WHERE id = '.$tags[$k];
							$this->_db->setQuery( $query );
							$tags[$k] = $this->_db->loadObject();	
						}
					}
					$this->_albums_data[$i]->tags = $tags;
				}
				
				$text = "";
				// traduim els numeros dels types a paraules
				$this->_albums_data[$i]->types = explode(",",$this->_albums_data[$i]->types);
				if(!empty($this->_albums_data[$i]->types)){
					for($k = 0; $k < count($this->_albums_data[$i]->types) ; $k++){
						if(isset($types_array[$this->_albums_data[$i]->types[$k]]["type_name"])) $text = $types_array[$this->_albums_data[$i]->types[$k]]["type_name"] ;
						$this->_albums_data[$i]->types[$k] = JText::_( $text );
					}
				}
				
				//mirem la puntuacio Average
				$query = 	' SELECT AVG(points) '.
							' FROM #__muscol_ratings '.
							' WHERE album_id = ' . $this->_albums_data[$i]->id .
							' AND ( type = "album" OR type = "" ) ' ;
				
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
									' WHERE album_id = ' . $this->_albums_data[$i]->id .
									' AND (comment_type = "album" OR comment_type = "") ';
						
						$this->_db->setQuery( $query );
						$this->_albums_data[$i]->num_comments = $this->_db->loadResult();
					break;
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
				
				$keywords = $this->keywords;
				$format_id = $this->format_id;
				$genre_id = $this->genre_id;
				
				$where_clause = array();
		
				if ($keywords != "") $where_clause[] = $this->get_where_clause_keywords($keywords);
				
				if ($format_id > 0) {
					$where_clause[] = ' (al.format_id = '.(int) $format_id . ' OR display_group = '.(int) $format_id . ')';
				}
				if ($genre_id > 0) {
					$genre->id = $genre_id ;
					$this->getDescendantsId($genre_id); 
					$descendants = $this->descendantsId;
					$descendants[] = $genre_id ;
					$genre_clause = ' ( al.genre_id = ' . implode(' OR al.genre_id = ',$descendants) . ' ) ';
					$where_clause[] = $genre_clause;
				}
				
				// Build the where clause of the content record query
				$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
				
				for($i=0, $n = count($this->_albums_fastnav); $i < $n ; $i++){
					
					if ($keywords != ""){
					
						$format_id = $this->_albums_fastnav[$i]->id;
						$query = 	' SELECT f.*,al.*,ar.artist_name,ar.letter,ge.genre_name '.
									' FROM #__muscol_albums as al '.
									' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
									' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
									' LEFT JOIN #__muscol_genres as ge ON ge.id = al.genre_id '.
									$where_clause .
									' AND (format_id = '.$format_id.' OR display_group = '.$format_id.')'.
									' AND (part_of_set = 0 OR ( part_of_set != 0 AND show_separately = "Y" ) ) ' .
									' ORDER BY year,month ';
						//echo $query; die();
						$this->_db->setQuery( $query );
						$this->_albums_fastnav[$i]->albums = $this->_db->loadObjectList();
					
										
					}
					
					
				}
			}
			
		return $this->_albums_fastnav;
	
	}

}