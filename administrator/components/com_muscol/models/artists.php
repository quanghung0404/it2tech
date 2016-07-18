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

class ArtistsModelArtists extends JModelLegacy
{

	var $_data;
  	var $_total = null;
  	var $_pagination = null;
	var $_letters_list = null;
	var $_letter = null;

	function __construct(){
		parent::__construct();
	
		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('muscol.artists.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('muscol.artists.limitstart', 'limitstart', 0, 'int');
		$keywords = $mainframe->getUserStateFromRequest('muscol.artists.keywords','keywords','','keywords');
		$filter_order     = $mainframe->getUserStateFromRequest('muscol.artists.filter_order', 'filter_order', 'ar.class_name', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('muscol.artists.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word' );
		$letter = $mainframe->getUserStateFromRequest('muscol.artists.letter','letter','','letter');
		
		$this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->setState('keywords', $keywords);
		$this->setState('letter', $letter);
		
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
			
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir . ' ';
	 
			return $orderby;
	}

	function _buildQuery(){
		
		$letter = $this->getLetter();
		$keywords = $this->getKeywords();
		
		$where_clause = array();
		
		if ($keywords != "") {
			$where_clause[] = ' ar.artist_name LIKE "%'.$keywords.'%" ';
		}
		if ($letter != "") {
			$where_clause[] = ' letter = "'.$letter.'"';
		}
		
		$orderby = $this->_buildContentOrderBy();
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
		
		$query = ' SELECT ar.*, u.name as username '
			. ' FROM #__muscol_artists as ar '
			. ' LEFT JOIN #__users as u ON u.id = ar.user_id ' 
			.$where_clause
			.$orderby
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
	
	function getLetter(){
		if (empty($this->_letter)) {
			$this->_letter = $this->getState('letter')	;
		}
		return $this->_letter;
	}
	
	function getLettersList()
	{
		if (empty( $this->_letters_list )){
			$query = ' SELECT DISTINCT letter '
					. ' FROM #__muscol_artists '
					.' ORDER BY letter ' 
					;
			$this->_db->setQuery( $query );
			$this->_letters_list = $this->_db->loadAssocList();
		}

 	return $this->_letters_list;

	}
}