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

class ArtistsModelFile extends JModelLegacy
{
	  
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id');
		$this->setId((int)$id);
				
	}


	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;

	}

	function &getAlbumSongs()
	{
		// Load the data
		if (empty( $this->_files )) {
			$query = 	' SELECT * '.
						' FROM #__muscol_songs '.
						' WHERE album_id = '.$this->_id
						;
			$this->_db->setQuery( $query );
			$this->_files = $this->_db->loadObjectList();
				
		}
		
		return $this->_files;
	}
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 	' SELECT * '.
						' FROM #__muscol_songs '.
						' WHERE id = '.$this->_id
						;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
				
		}
		
		$query = ' UPDATE #__muscol_songs SET downloaded = '. ( $this->_data->downloaded + 1 ) ;
		$this->_db->setQuery($query);
		$this->_db->query();
		

		return $this->_data;
	}
	
	function getRegisterHit($id = false){
		if(!$id) $id = $this->_id ;
		
		if($id){
			$row = $this->getTable('statistic');
	
			$data['reference_id'] = $id ;
			$data['type'] = 6 ;
			
			// Bind the form fields to the statistics table
			if (!$row->bind($data)) {
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