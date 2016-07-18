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

class FormatsModelFormats extends JModelLegacy
{

	var $_data;

	function _buildQuery()
	{
		$query = 	' SELECT * '.
			 		' FROM #__muscol_format '.
					' WHERE display_group = 0 '.
					' ORDER BY order_num '
		;

		return $query;
	}

	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			$items = $this->_getList( $query );
			for($i = 0; $i < count($items) ; $i++){

				$query = 	' SELECT COUNT(*) '.
							' FROM #__muscol_albums '.
							' WHERE format_id = ' . $items[$i]->id ;
				$this->_db->setQuery( $query );
				$items[$i]->num_albums = $this->_db->loadResult();
				
				$query = 	' SELECT format_name '.
							' FROM #__muscol_format '.
							' WHERE id = ' . $items[$i]->display_group ;
				$this->_db->setQuery( $query );
				$items[$i]->display_group_name = $this->_db->loadResult();
				
				$this->_data[] = $items[$i];
				
				$query = 	' SELECT * '.
							' FROM #__muscol_format '.
							' WHERE display_group = '. $items[$i]->id .
							' ORDER BY order_num '
				;
				$items_group = $this->_getList( $query );
				for($j=0 ; $j < count($items_group); $j++){
					$query = 	' SELECT COUNT(*) '.
								' FROM #__muscol_albums '.
								' WHERE format_id = ' . $items_group[$j]->id ;
					$this->_db->setQuery( $query );
					$items_group[$j]->num_albums = $this->_db->loadResult();
					
					$query = 	' SELECT format_name '.
								' FROM #__muscol_format '.
								' WHERE id = ' . $items_group[$j]->display_group ;
					$this->_db->setQuery( $query );
					$items_group[$j]->display_group_name = $this->_db->loadResult();
					
					$this->_data[] = $items_group[$j];
				}
				
			}	
		}

		return $this->_data;
	}
}