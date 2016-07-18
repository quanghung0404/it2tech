<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldAlbumSelect extends JFormField
{

	protected $type = 'AlbumSelect';

	protected function getInput()
	{

		$db = JFactory::getDBO();
		
		$query = 	' SELECT al.id AS value, CONCAT(ar.artist_name , " - " , al.name , " [" , f.format_name, "]") AS text FROM #__muscol_albums as al '.
					' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id ' .
					' LEFT JOIN #__muscol_format as f ON f.id = al.format_id ' .
					' ORDER BY ar.letter,ar.class_name,f.order_num,al.year,al.month';
		$db->setQuery( $query );
		
		$options = array();
		$options[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select An Album' ) .' -' );
		$options = array_merge($options, $db->loadObjectList());
		
		return JHTML::_ ( 'select.genericlist', $options, $this->name, $attribs, 'value', 'text', $this->value );

	}
	
}