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

class ArtistsModelArtist extends JModelLegacy
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
			$query = ' SELECT * FROM #__muscol_artists '.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			if($this->_data){
				$this->_data->tags = explode(",",$this->_data->tags);
			}
			
			if(isset($this->_data->related))$this->_data->related = explode(",",$this->_data->related);
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->artist_name = "";
			$this->_data->class_name = "";
			$this->_data->picture = "";
			$this->_data->image = "";
			$this->_data->keywords = "";
			$this->_data->metakeywords = "";
			$this->_data->metadescription = "";
			$this->_data->genre_id = "";
			$this->_data->city = "";
			$this->_data->country = "";
			$this->_data->url = "";
			$this->_data->years_active = "";
			$this->_data->review = "";

			$this->_data->tags = array();
		}
		return $this->_data;
	}
	
	function getArtistsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_artists_data )){
				$query = ' SELECT id,artist_name FROM #__muscol_artists '.
						' WHERE id != '.$this->_id.
						 ' ORDER BY letter,class_name';
				$this->_db->setQuery( $query );
				$this->_artists_data = $this->_db->loadObjectList();
			}
			
		return $this->_artists_data;
	
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

	function getTagsData()
		{
			// Lets load the data if it doesn't already exist
			if (empty( $this->_tags_data )){
				$query = ' SELECT * FROM #__muscol_tags '.
						 ' ORDER BY id';
				$this->_db->setQuery( $query );
				$this->_tags_data = $this->_db->loadObjectList();
				
				$empty_element = new stdClass();
				
				$empty_element->id = "";
				$empty_element->tag_name = "";
				$empty_element->icon = "";
				array_unshift( $this->_tags_data, $empty_element );
			}
			
		return $this->_tags_data;
	
	}

	function store()
	{	
		$row = $this->getTable();

		$data = JRequest::get( 'post' );
		$data['review'] = JRequest::getVar('review', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$datafiles = JRequest::get( 'files' );

		// new tag system in MC 2.4.0
		$data['tags'] = MusColHelper::getTagIDArrayFromString($data['hidden-tags']);
		
		// Bind the form fields to the album table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->bind($datafiles)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the hello record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}

		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onSaveArtist', array ($row->id));

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