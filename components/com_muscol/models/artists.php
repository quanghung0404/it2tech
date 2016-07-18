<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class ArtistsModelArtists extends JModelLegacy
{

	var $_data;

	var $_letter;
	
	var $_total = null;
	
	var $_pagination = null;


	function __construct(){
		parent::__construct();
	
		$mainframe = JFactory::getApplication();
		
		$this->params =JComponentHelper::getParams( 'com_muscol' );
		
		$this->_letter = JRequest::getCmd("letter","","");
		
		// Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('muscol.artists.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		//$limitstart = $mainframe->getUserStateFromRequest('muscol.artists.limitstart', 'limitstart', 0, 'int');
 		$limitstart = JRequest::getVar('limitstart',0);
 		//echo $limitstart;$mainframe->close();
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
		
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
			if($this->params->get('showartistshome') && $this->getLetter() == ''){
				$this->query = 	' SELECT * '
								. ' FROM #__muscol_artists ' 
								. ' ORDER BY letter+0<>0 ASC, letter+0, letter, class_name '
				;
			}
			else{
				$this->query = 	' SELECT * '
								. ' FROM #__muscol_artists '
								. ' WHERE letter = "'.$this->getLetter().'" '
								. ' ORDER BY letter+0<>0 ASC, letter+0, letter,class_name '
				;
			}
		}
		return $this->query;
	}
	
	function getLetter()	{
		return $this->_letter;
	}
	
	
	function getArtistsFastnav(){
			if (empty( $this->artists_fastnav )){
				$query = 	' SELECT id,artist_name FROM #__muscol_artists '.
							' WHERE letter = "'.$this->getLetter().'" ' .
							' ORDER BY letter,class_name ' ;
				$this->_db->setQuery( $query );
				$this->artists_fastnav = $this->_db->loadObjectList();
			}
			
		return $this->artists_fastnav;
	
	}
	
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			
			if($this->params->get('usepaginationartists'))			
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
			else $this->_data = $this->_getList( $query ); 

			for($i = 0; $i < count($this->_data); $i++){
				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_albums '.
							' WHERE artist_id = '.$this->_data[$i]->id
							;
				$this->_db->setQuery( $query );
				$this->_data[$i]->num_albums = $this->_db->loadResult();	
				
				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_songs as s '.
							' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' .
							' WHERE s.artist_id = '.$this->_data[$i]->id
							;
				$this->_db->setQuery( $query );
				$this->_data[$i]->num_songs = $this->_db->loadResult();
				
				$query = 	' SELECT al.id,al.name,al.image '.
							' FROM #__muscol_albums as al '.
							' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
							' WHERE al.artist_id = '.$this->_data[$i]->id .
							' ORDER BY f.order_num, al.year, al.month'
							;
				$this->_db->setQuery( $query );
				$this->_data[$i]->albums = $this->_db->loadObjectList();
			}

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
	
	function getPDFData()	{

		if (empty( $this->pdf_data )){
			
			$query = 	' SELECT * FROM #__muscol_format '.
						' WHERE display_group = 0 '.
						' ORDER BY order_num ';
			$this->_db->setQuery( $query );
			$this->pdf_data = $this->_db->loadObjectList();
			
			//nomes els artistes daquesta lletra
			if($this->_letter) $and_letter = ' AND ar.letter = "'.$this->_letter.'" ';
			else  $and_letter = "";
			
			for($i=0, $n = count($this->pdf_data); $i < $n; $i++){
				$format_id = $this->pdf_data[$i]->id;
				$query = 	' SELECT f.format_name,al.name,al.year,ar.artist_name,ar.letter '.
							' FROM #__muscol_albums as al '.
							' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
							' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
							' WHERE (al.format_id = '.$format_id.' OR f.display_group = '.$format_id.')'.
							$and_letter .
							' ORDER BY ar.letter,ar.class_name,al.year,al.month ';
				
				$this->_db->setQuery( $query );
				$this->pdf_data[$i]->albums = $this->_db->loadObjectList();
				
			}

		}

		return $this->pdf_data;
	}
}
