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

class PlaylistsModelPlaylists extends JModelLegacy
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
		$limit = $mainframe->getUserStateFromRequest('muscol.playlists.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('muscol.playlists.limitstart', 'limitstart', 0, 'int');
		$keywords = $mainframe->getUserStateFromRequest('muscol.playlists.playlists','keywords','','keywords');
		$filter_order     = $mainframe->getUserStateFromRequest('muscol.playlists.filter_order', 'filter_order', 'pl.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('muscol.playlists.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
		
		$this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->setState('keywords', $keywords);
	
		
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
		
	
		$keywords = $this->getKeywords();
		
		$where_clause = array();
		
		if ($keywords != "") {
			$where_clause[] = ' pl.title LIKE "%'.$keywords.'%" OR pl.description LIKE "%'.$keywords.'%" ';
		}
		
		
		$orderby = $this->_buildContentOrderBy();
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
		
		$query = ' SELECT pl.*, u.name as username '
			. ' FROM #__muscol_playlists as pl '
			. ' LEFT JOIN #__users as u ON u.id = pl.user_id ' 
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
	
	
}