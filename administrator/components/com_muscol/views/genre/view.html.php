<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class GenresViewGenre extends JViewLegacy
{

	var $parents_ids = array();
	
	function display($tpl = null)
	{
		global $mainframe;
		$genre		= $this->get('Data');
		$parents	= $this->get('ParentsData');
		
		$this->parents_ids = $genre->parents;
		
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');
		
		$isNew		= ($genre->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Genre' ).': <small><small>[ ' . $text.' ]</small></small>','genre' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		

		$this->assignRef('genre',		$genre);
		$this->assignRef('parents',		$parents);
		
		JHtml::_('formbehavior.chosen', '.chzn-select');

		parent::display($tpl);
	}
	
	function show_genre_tree($genres,$level){
		
		$return = "";
		
		for($i = 0; $i < count($genres); $i++){
			$return .= $this->render_option($genres[$i]->id,$genres[$i]->genre_name,$level);
			$level ++;
			if(!empty($genres[$i]->sons)){
				$return .= 	$this->show_genre_tree($genres[$i]->sons,$level);
			}
			$level --;
		}
		//echo $return;
		return $return;
		
	}
	
	function render_option($id, $name, $level){
		$indent = "";
		
		for($i = 0; $i < $level; $i++){
			$indent .= "&nbsp;&nbsp;";	
		}
		
		$selected = ""; 
		if( in_array($id,$this->parents_ids) ) $selected = "selected";
            
		return "<option value='".$id."' $selected >".$indent.$name."</option>";	
	}
}