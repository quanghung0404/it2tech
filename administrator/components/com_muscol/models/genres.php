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

class GenresModelGenres extends JModelLegacy
{

	var $_data;
	var $_array_genres;

	function _buildQuery()
	{
		$query = 	' SELECT * '.
			 		' FROM #__muscol_genres '.
					' WHERE parents = "0" '
		;

		return $query;
	}
	
	function getData(){
		
		if (empty( $this->_array_genres )){
			$this->_array_genres = array();
			
			$query = 	' SELECT id,genre_name '.
			 			' FROM #__muscol_genres '
						
			;
			$this->_db->setQuery( $query );
			$result = $this->_db->loadObjectList();
			
			for($i = 0; $i < count( $result ) ; $i++){
				$this->_array_genres[$result[$i]->id] = $result[$i]->genre_name ;
			}
		
		}

		if (empty( $this->_data )){
			
			$query = $this->_buildQuery();
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObjectList();
			
			for($i = 0; $i < count( $this->_data ) ; $i++){
				$this->_data[$i]->sons = $this->get_descendants($this->_data[$i]);	
			}

		}

		return $this->_data;
	}
	
	function get_descendants($genre){
		//echo $genre->id." -> ";
		$query = 	' SELECT * FROM #__muscol_genres '.
					' WHERE '.
					' ( parents LIKE "%,'.$genre->id.',%"'.
							' OR parents LIKE "'.$genre->id.',%" '.
							' OR parents LIKE "%,'.$genre->id.'" '.
							' OR parents LIKE "'.$genre->id.'" ) '
					;
		$this->_db->setQuery( $query );
		$return = $this->_db->loadObjectList();
		
		
		//print_r($return);
		//echo "<br/><br/>";
		if(!empty( $return )){
			for($i = 0; $i < count( $return ) ; $i++){
				if($return[$i]->parents) {
					$return[$i]->parents = explode(",",$return[$i]->parents);
					for($j = 0; $j < count( $return[$i]->parents ) ; $j++){
						$return[$i]->parents[$j] = $this->_array_genres[$return[$i]->parents[$j]]; // traduim el nom dels pares
					}
					
				}
				$return[$i]->sons = $this->get_descendants($return[$i]);	
			}
			//print_r($return);
			//echo "<br/><br/>";
		}
		
		return $return;
		
	}
	
}