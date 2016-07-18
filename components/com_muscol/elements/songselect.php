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

class JFormFieldSongSelect extends JFormField
{

	protected $type = 'SongSelect';

	protected function getInput()
	{

		$db = JFactory::getDBO();

		$query = 	' SELECT s.id AS value, CONCAT(ar.artist_name , " - " , s.name ) AS text FROM #__muscol_songs as s '.
					' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' .
					' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id ' .
					' ORDER BY ar.letter,ar.class_name,al.year,al.month,s.disc_num,s.num';
		$db->setQuery( $query );
		
		$options = array();
		$options[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select a song' ) .' -' );
		$options = array_merge($options, $db->loadObjectList());
		
		return JHTML::_ ( 'select.genericlist', $options, $this->name, $attribs, 'value', 'text', $this->value );

	}
	
}