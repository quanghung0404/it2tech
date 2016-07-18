<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class ArtistsModelRating extends JModelLegacy
{
	  
	function __construct()
	{
		parent::__construct();
			
	}

	function store_rating(){
		$row = $this->getTable('rating');
		
		$params =JComponentHelper::getParams( 'com_muscol' );

		$data = JRequest::get( 'get' );
		
		switch($data["type"]){
			
			case "song":
			$type = ' AND type = "song" ' ;
			break;
			default: // album
			$type = ' AND ( type = "album" OR type = "" ) ' ;
			break;
		}
		
		$user = JFactory::getUser();
		
		//we check if is the first rate on this album or we are modifying our previous vote
		$query = 	' SELECT id FROM #__muscol_ratings ' .
					' WHERE user_id = ' .$user->id. ' AND album_id = ' . $data["album_id"] . $type ;
		$this->_db->setQuery($query);
		$id_rating = $this->_db->loadResult();
		if($id_rating) $data["id"] = $id_rating;

		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		
		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onSaveRating', array ($row->id));
		
		if($params->get('registerratings')) $this->getRegisterHit();
				
		return true;
	}
	
	function getRegisterHit($id = false){
		if(!$id) $id = JRequest::getInt('album_id') ;
		
		$data = JRequest::get( 'get' );
		
		if($id){
			$row = $this->getTable('statistic');
			
			$data2['reference_id'] = $id ;
			$data2['value'] = JRequest::getInt('points') ;
			
			switch($data["type"]){
				
				case "song":
				$data2['type'] = 8 ;
				break;
				case "album":
				$data2['type'] = 7 ;
				break;
			}
			
			// Bind the form fields to the statistics table
			if (!$row->bind($data2)) {
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