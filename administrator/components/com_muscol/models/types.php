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

class TypesModelTypes extends JModelLegacy
{

	var $_data;

	function _buildQuery()
	{
		$query = 	' SELECT * '.
			 		' FROM #__muscol_type '
		;

		return $query;
	}

	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			$items = $this->_getList( $query );
			$this->_data = $items;
			
			for($i=0, $n = count($this->_data); $i < $n ; $i++){
			
				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_albums '.
							' WHERE '.
							' ( types LIKE "%,'.$this->_data[$i]->id.',%"'.
									' OR types LIKE "'.$this->_data[$i]->id.',%" '.
									' OR types LIKE "%,'.$this->_data[$i]->id.'" '.
									' OR types LIKE "'.$this->_data[$i]->id.'" ) '
							;
							
				$this->_db->setQuery( $query );
				$this->_data[$i]->num_albums = $this->_db->loadResult();
				
			}
		}

		return $this->_data;
	}
}