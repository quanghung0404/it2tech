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

class RatingsModelRatings extends JModelLegacy
{

	var $_data;
  	var $_total = null;
  	var $_pagination = null;

	function __construct(){
		parent::__construct();
	
		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('muscol.ratings.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('muscol.ratings.limitstart', 'limitstart', 0, 'int');
		
		$filter_order     = $mainframe->getUserStateFromRequest('muscol.ratings.filter_order', 'filter_order', 'r.date', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('muscol.ratings.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
		
		
		$this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

  	}

	function getTotal(){

		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);	
		}
		return $this->_total;
	 }
	  
	 function getPagination(){

		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
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
		
		if(empty($this->query)){
			
			$orderby = $this->_buildContentOrderBy();
			
			$this->query = 	' SELECT r.*,al.name as album_name, s.name as song_name,u.name ' .
							' FROM #__muscol_ratings as r ' .
							' LEFT JOIN #__users as u ON u.id = r.user_id ' . 
							' LEFT JOIN #__muscol_albums as al ON al.id = r.album_id ' . 	
							' LEFT JOIN #__muscol_songs as s ON s.id = r.album_id ' . 	
							$orderby;
		}
		return $this->query;
	}
	
	function getData(){

		if (empty( $this->_data )){
			$query = 	$this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));	
		}

 	return $this->_data;

	}
	
}