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
//jomcomment integration
if(file_exists(JPATH_PLUGINS . DS . 'content' . DS . 'jom_comment_bot.php')) include_once( JPATH_PLUGINS . DS . 'content' . DS . 'jom_comment_bot.php' );

class ArtistsModelGenre extends JModelLegacy
{
	  
	function __construct()
	{
		parent::__construct();
		
		$mainframe = JFactory::getApplication();

		$id = JRequest::getVar('id');
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$default_layout = $params->get( 'albums_view' );

		$layout = $mainframe->getUserStateFromRequest('layout','layout', $default_layout ,'layout');
		
		//nomes jms
		//if(JRequest::getVar('layout')) $layout = JRequest::getVar('layout');
		//else $layout = $default_layout ;
		
		$this->setState('layout', $layout);
		
		// Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
 
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//we won't use limitstart for now
		//$limitstart = 0;
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
		
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
			$query = ' SELECT * FROM #__muscol_artists '.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		return $this->_data;
	}
	
	function getTypesArray(){
			if (empty( $this->_types_array )){
				$query = ' SELECT id,type_name FROM #__muscol_type ';
				$this->_db->setQuery( $query );
				$this->_types_array = $this->_db->loadAssocList('id');
			}
			
		return $this->_types_array;
	
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
			$this->query = 	' SELECT f.*,al.*,ar.artist_name,ar.letter,ge.genre_name '.
								' FROM #__muscol_albums as al '.
								' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id '.
								' LEFT JOIN #__muscol_format as f ON f.id = al.format_id '.
								' LEFT JOIN #__muscol_genres as ge ON ge.id = al.genre_id '.
								' WHERE genre_id = '.$this->_id.
								' ORDER BY al.display_group,al.year,al.month ';
			
		}
		return $this->query;
	}
	
	function getAlbumsData()
		{

			if (empty( $this->_albums_data )){
				
				$types_array = $this->getTypesArray();
				
					$query = $this->_buildQuery();
			
					$this->_albums_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
					
					for($i = 0; $i < count($this->_albums_data); $i++){
						// busquem si hi ha albums que pertanyin a aquest item
						
						$query = 	' SELECT id,name,image '.
									' FROM #__muscol_albums '.
									' WHERE part_of_set = ' . $this->_albums_data[$i]id
									;
						$this->_db->setQuery( $query );
						$this->_albums_data[$i]->subalbums = $this->_db->loadObjectList();		
												
						// i ara les etiquetes
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
						
						// traduim els numeros dels types a paraules
						$this->_albums_data[$i]->types = explode(",",$this->_albums_data[$i]->types);
						if(!empty($this->_albums_data[$i]->types)){
							for($k = 0; $k < count($this->_albums_data[$i]->types) ; $k++){
								$this->_albums_data[$i]->types[$k] = JText::_( $types_array[$this->_albums_data[$i]->types[$k]]["type_name"] );
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
						
						}
						//print_r($this->_albums_data[$i]->albums[$j]);					
					
				}
			}
			
		return $this->_albums_data;
	
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
	

}