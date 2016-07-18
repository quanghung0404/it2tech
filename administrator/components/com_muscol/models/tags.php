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

class TagsModelTags extends JModelLegacy
{

	var $_data;

	function _buildQuery()
	{
		$query = 	' SELECT * '.
			 		' FROM #__muscol_tags '
		;

		return $query;
	}

	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			$items = $this->_getList( $query );
			$this->_data = $items;
		}

		return $this->_data;
	}
}