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

class JFormFieldArtistSelect extends JFormField
{

	protected $type = 'ArtistSelect';

	protected function getInput()
	{

		$db = JFactory::getDBO();
		//,ar.artist_name,f.format_name
		$query = 	' SELECT ar.id AS value, ar.artist_name AS text FROM #__muscol_artists as ar '.
					' ORDER BY ar.letter,ar.class_name ';
		$db->setQuery( $query );
		
		$options = array();
		$options[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select An Artist' ) .' -' );
		$options = array_merge($options, $db->loadObjectList());
		
		return JHTML::_ ( 'select.genericlist', $options, $this->name, $attribs, 'value', 'text', $this->value );

	}
	
}