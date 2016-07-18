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

class ArtistsModelPlaylists extends JModelLegacy{
	
 
	function __construct(){
		parent::__construct();
		$id = JRequest::getVar('id');
		$this->setId((int)$id);
		
		$mainframe = JFactory::getApplication();
 
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('muscol.playlists.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		//$limitstart = $mainframe->getUserStateFromRequest('muscol.playlists.limitstart', 'limitstart', 0, 'int');
 		$limitstart = JRequest::getVar('limitstart',0);
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);


	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
		$this->playlist_data	= null;
		$this->user = JFactory::getUser();

	}
	
	function _buildQuery(){
		if(empty($this->query)){
			$this->query = 	' SELECT pl.*,us.name as user_name FROM #__muscol_playlists as pl '.
							' LEFT JOIN #__users as us ON us.id = pl.user_id ' .
							' WHERE pl.user_id = '.$this->user->id
						;
		}
	
		return $this->query;
	}
	
	function &getData(){
		if (empty( $this->_data )) {
			$query = $this->_buildQuery();

			$this->_data = $this->_getList($query);

		}
	
		return $this->_data;	
	}
	
	function _buildQuery_others(){
		if(empty($this->query_others)){
			$this->query_others = 	' SELECT pl.*,us.name as user_name FROM #__muscol_playlists as pl '.
							' LEFT JOIN #__users as us ON us.id = pl.user_id ' .
							' WHERE pl.user_id != '.$this->user->id
						;
		}
	
		return $this->query_others;
	}
	
	function &getDataOthers(){
		if (empty( $this->_data_others )) {
			$query = $this->_buildQuery_others();

			$this->_data_others = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		}
	
		return $this->_data_others;	
	}
	
	  function getTotal()
	  {
			// Load the content if it doesn't already exist
			if (empty($this->_total)) {
				$query = $this->_buildQuery_others();
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


	
	function &getPlaylist_from_session(){
	
		if (empty( $this->playlist_session )) {
			$session =JSession::getInstance('','');
			$playlist = $session->get('muscol_playlist') ; // the playlist is an array
			
			$this->playlist_session = new stdClass();
			
			if(is_array($playlist)) $this->playlist_session->songs = implode(",", $playlist) ;
			$this->playlist_session->id = 0;
			$this->playlist_session->title = JText::_('On-the-go');

		}
		
		return $this->playlist_session;
	}
	
}