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

class GenresModelGenre extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	function setId($id)
	{
		$this->_id		= $id;
		$this->_data	= null;
	}

	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__muscol_genres '.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();

		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			
			$this->_data->genre_name = "";
			$this->_data->parents = "";
			
		}
		
		$this->_data->parents = explode(",",$this->_data->parents);
		
		return $this->_data;
	}
	
	function getParentsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_parents_data )){
				$query = ' SELECT * FROM #__muscol_genres '.
						 ' WHERE id != '.$this->_id .' AND parents = "0" '
						 ;
				$this->_db->setQuery( $query );
				$this->_parents_data = $this->_db->loadObjectList();
				
				for($i = 0; $i < count( $this->_parents_data ) ; $i++){
					$this->_parents_data[$i]->sons = $this->get_descendants($this->_parents_data[$i]);	
				}
			}
			
		return $this->_parents_data;
	
	}
	
	function get_descendants($genre){
		//echo $genre->id." -> ";
		$query = 	' SELECT * FROM #__muscol_genres '.
					' WHERE id != '.$this->_id .' AND '.
					' ( parents LIKE "%,'.$genre->id.',%"'.
							' OR parents LIKE "'.$genre->id.',%" '.
							' OR parents LIKE "%,'.$genre->id.'" '.
							' OR parents LIKE "'.$genre->id.'" ) '.
					' AND '.
					' ( parents NOT LIKE "%,'.$this->_id.',%"'.
							' AND parents NOT LIKE "'.$this->_id.',%" '.
							' AND parents NOT LIKE "%,'.$this->_id.'" '.
							' AND parents NOT LIKE "'.$this->_id.'" ) '
					;
		$this->_db->setQuery( $query );
		$return = $this->_db->loadObjectList();
		//print_r($return);
		//echo "<br/><br/>";
		if(!empty( $return )){
			for($i = 0; $i < count( $return ) ; $i++){
				$return[$i]->sons = $this->get_descendants($return[$i]);	
			}
			//print_r($return);
			//echo "<br/><br/>";
		}
		
		return $return;
		
	}
	
	
	function store($data = false)
	{	
		$row = $this->getTable();
		
		if(!$data) $data = JRequest::get( 'post' );
		
		// Bind the form fields to the album table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

}