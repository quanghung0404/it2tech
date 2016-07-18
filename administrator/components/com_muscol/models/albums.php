<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class AlbumsModelAlbums extends JModelLegacy
{

	var $_data;
  	var $_total = null;
  	var $_pagination = null;
  	var $_keywords = null;
	var $_artists_list = null;
	var $_artist_id = null;

	function __construct(){
		parent::__construct();
	
		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('muscol.albums.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('muscol.albums.limitstart', 'limitstart', 0, 'int');
		$keywords = $mainframe->getUserStateFromRequest('muscol.albums.keywords','keywords','','keywords');
		$artist_id = $mainframe->getUserStateFromRequest('muscol.albums.artist_id','artist_id',0,'artist_id');
		$filter_order     = $mainframe->getUserStateFromRequest('muscol.albums.filter_order', 'filter_order', 'al.added', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('muscol.albums.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
		
		$this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->setState('keywords', $keywords);
		$this->setState('artist_id', $artist_id);
		
  	}

  	function update_tables(){
		
		$query = " SHOW COLUMNS FROM #__muscol_artists LIKE 'tags' " ;
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		
		if(!$result){
		
			$query = "ALTER TABLE  `#__muscol_artists` ADD  `tags` VARCHAR( 255 ) NOT NULL";
			$this->_db->setQuery($query);
			$this->_db->query();
			
			$query = "ALTER TABLE  `#__muscol_songs` ADD  `tags` VARCHAR( 255 ) NOT NULL , ADD  `external_type` VARCHAR( 255 ) NOT NULL , ADD  `external_id` INT( 11 ) NOT NULL";
			$this->_db->setQuery($query);
			$this->_db->query();
			
			$query = "ALTER TABLE  `#__muscol_albums` ADD  `external_type` VARCHAR( 255 ) NOT NULL , ADD  `external_id` INT( 11 ) NOT NULL";
			$this->_db->setQuery($query);
			$this->_db->query();
				
		}
		
	}


function getTotal()
  {
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


	function getKeywords(){
		if (empty($this->_keywords)) {
			$this->_keywords = $this->getState('keywords')	;
		}
		return $this->_keywords;
	}
	function getArtistId(){
		if (empty($this->_artist_id)) {
			$this->_artist_id = $this->getState('artist_id')	;
		}
		return $this->_artist_id;
	}
 
	 function get_where_clause_keywords($keywords){ //germi
	 	$keywords = utf8_decode(trim($this->get_keywords($keywords)));
		
		$keyword=explode(" ",$keywords);
		$cadena="";
		for($i=0;$i<sizeof($keyword);$i++){
			$cadena.="(al.keywords LIKE \"% ".$keyword[$i]." %\") AND ";
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
	
	function getFilterOrder(){
		return  $this->getState('filter_order') ;
  }
  function getFilterOrderDir(){
		return  $this->getState('filter_order_Dir') ;
  }
  
  function _buildContentOrderBy()
	{
			
			$filter_order     = $this->getState('filter_order' ) ;
			$filter_order_Dir = $this->getState('filter_order_Dir') ;
			
			if($filter_order == "year") $filter_order = 'year ' . $filter_order_Dir . ', month ' .$filter_order_Dir ;
	 
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir . ' ';
	 
			return $orderby;
	}
	 
	function _buildQuery()
	{
		
		$keywords = $this->getKeywords();
		$artist_id = $this->getArtistId();
		
		$where_clause = array();

		if ($keywords != "")
			$where_clause[] = $this->get_where_clause_keywords($keywords);
		if ($artist_id > 0) {
			$where_clause[] = ' al.artist_id = '.(int) $artist_id;
		}
		
		$orderby = $this->_buildContentOrderBy();
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

		$query = ' SELECT al.*,ar.artist_name,f.format_name, u.name as username '
				. ' FROM #__muscol_albums as al '
				.' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id ' 
				.' LEFT JOIN #__muscol_format as f ON f.id = al.format_id ' 
				.' LEFT JOIN #__users as u ON u.id = al.user_id ' 
				.$where_clause
				.$orderby
		;
		
		return $query;
	}
	
	function getTypesArray(){
			if (empty( $this->_types_array )){
				$query = ' SELECT id,type_name FROM #__muscol_type ';
				$this->_db->setQuery( $query );
				$this->_types_array = $this->_db->loadAssocList('id');
			}
			
		return $this->_types_array;
	
	}
	
	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			
			// traduim els numeros dels types a paraules
			if($this->_data){
				$types_array = $this->getTypesArray();
				
				$text = "";
				
				for($i = 0; $i < count($this->_data) ; $i++){
					$this->_data[$i]->types = explode(",",$this->_data[$i]->types);
				
					if(!empty($this->_data[$i]->types)){
						for($j = 0; $j < count($this->_data[$i]->types) ; $j++){
							if(isset($types_array[$this->_data[$i]->types[$j]]["type_name"])) $text = $types_array[$this->_data[$i]->types[$j]]["type_name"] ;
							$this->_data[$i]->types[$j] = JText::_( $text );
						}
					}
				}
			}
		}

 	return $this->_data;

	}
	function getArtistsList()
	{
		if (empty( $this->_artists_list )){
			$query = 	' SELECT id,artist_name '
					. ' FROM #__muscol_artists '
					.' ORDER BY letter,class_name ' 
					;
			$this->_db->setQuery( $query );
			$this->_artists_list = $this->_db->loadAssocList();
		}

 	return $this->_artists_list;

	}
}