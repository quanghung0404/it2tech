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


class ElementModelElement extends JModelLegacy
{

	var $_data;
  	var $_total = null;
  	var $_pagination = null;
  	var $_keywords = null;
	var $_artists_list = null;
	var $_artist_id = null;

	function __construct(){
		parent::__construct();
	
		global $mainframe, $option;

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
		
		$keywords = $mainframe->getUserStateFromRequest('articleelement.keywords','keywords','','keywords');
		
		$artist_id = $mainframe->getUserStateFromRequest('articleelement.artist_id','artist_id',0,'artist_id');
	
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->setState('keywords', $keywords);
		$this->setState('artist_id', $artist_id);
		
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
			$cadena.="(#__muscol_albums.keywords LIKE '% ".$keyword[$i]." %') AND ";
		}
		if($i>0){
			$cadena = substr($cadena,0,-5);
			$cadena = "".$cadena."";
		}
		else if ($i==0) $cadena = "";
		return $cadena;
	 }
	 
	 function get_keywords($keywords){
		return $this->subs_characters(" ".$keywords." ");
	}
	
	function subs_characters($cadena){
	
		$specialchars = array("'","\"","-","&","(",")","?","¿","¡","!","/",",",".",":",";","[","]","+");
		$cadena = str_ireplace($specialchars," ",$cadena);
		
		$specialchars = array("á","à","ä","â");
		$cadena = str_ireplace($specialchars,"a",$cadena);
		$specialchars = array("é","è","ë","ê");
		$cadena = str_ireplace($specialchars,"e",$cadena);
		$specialchars = array("í","ì","ï","î");
		$cadena = str_ireplace($specialchars,"i",$cadena);
		$specialchars = array("ó","ò","ö","ô");
		$cadena = str_ireplace($specialchars,"o",$cadena);
		$specialchars = array("ú","ù","ü","û");
		$cadena = str_ireplace($specialchars,"u",$cadena);
		
		return $this->erase_multiple_whitespaces($cadena);
	
	}
	function erase_multiple_whitespaces($cadena){
		$cadena2 = str_ireplace("  "," ",$cadena,$times);
		if($times!=0) $cadena2 = $this->erase_multiple_whitespaces($cadena2);
		return $cadena2;
	}
	 
	function _buildQuery()
	{
		
		$keywords = $this->getKeywords();
		$artist_id = $this->getArtistId();
		
		$where_clause = array();

		if ($keywords != "")
			$where_clause[] = $this->get_where_clause_keywords($keywords);
		if ($artist_id > 0) {
			$where_clause[] = ' #__muscol_albums.artist_id = '.(int) $artist_id;
		}
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

		$query = ' SELECT #__muscol_albums.*,#__muscol_artists.artist_name,#__muscol_format.format_name,#__muscol_type.type_name '
			. ' FROM #__muscol_albums '
			.' LEFT JOIN #__muscol_artists ON #__muscol_artists.id = #__muscol_albums.artist_id ' 
			.' LEFT JOIN #__muscol_format ON #__muscol_format.id = #__muscol_albums.format_id ' 
			.' LEFT JOIN #__muscol_type ON #__muscol_type.id = #__muscol_albums.type_id ' 
			.$where_clause
			.' ORDER BY add_time DESC ' 
		;

		return $query;
	}

	function getData(){
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));	
		}

 	return $this->_data;

	}
	function getArtistsList()
	{
		if (empty( $this->_artists_list )){
			$query = ' SELECT id,artist_name '
			. ' FROM #__muscol_artists '
			.' ORDER BY letter,class_name ' 
			;
			$this->_db->setQuery( $query );
			$this->_artists_list = $this->_db->loadAssocList();
		}

 	return $this->_artists_list;

	}
}