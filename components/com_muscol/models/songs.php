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

class ArtistsModelSongs extends JModelLegacy{
	
	var $_total = null;
	var $_pagination = null;
 
	function __construct(){
		parent::__construct();
		$id = JRequest::getVar('id');
		$this->setId((int)$id);
		
		$mainframe = JFactory::getApplication();
		
		$limit = $mainframe->getUserStateFromRequest('muscol.songs.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		//$limitstart = $mainframe->getUserStateFromRequest('muscol.songs.limitstart', 'limitstart', 0, 'int');
        $limitstart = JRequest::getVar('limitstart',0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;

	}
	
	function getTotal(){
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
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

	
	function _buildQuery(){
		if(empty($this->query)){
			$this->query = 	' SELECT s.*,al.name as album_name, al.image FROM #__muscol_songs as s '.
							' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' .
							' WHERE s.artist_id = '.$this->_id .
							' ORDER BY al.year, al.month, al.id, s.disc_num,s.num '
						;
		}
		return $this->query;
	}

	function &getData()
	{
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
	
	function getSongsData(){
		if (empty( $this->songs_data )) {
			$query = $this->_buildQuery();
			//$this->songs_data = $this->_db->loadObjectList();
			$this->songs_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
			//print_r($this->songs_data);die();
		}
		return $this->songs_data;	
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