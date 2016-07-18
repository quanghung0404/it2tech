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

class GenresViewGenres extends JViewLegacy
{

	var $k = 0;
	var $i = 0;
	var $total_items = 0;
	
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Genre Manager' ), 'genre' );
		JToolBarHelper::deleteList(JText::_( 'Are you sure you want to delete the selected genres' ));
		JToolBarHelper::editList();
		JToolBarHelper::addNew();
		
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');

		// Get data from the model
		$items		= $this->get( 'Data');

		$this->assignRef('items',		$items);

		parent::display($tpl);
	}
	
	function show_genre_tree($genres,$level,$path){
		
		$return = "";
		
		for($i = 0; $i < count($genres); $i++){
			$path[] = $genres[$i]->genre_name ;
			$return .= $this->render_option($genres[$i]->id,$genres[$i]->genre_name,$level,$path,$genres[$i]->parents);
			$this->k = 1 - $this->k;
			$this->total_items ++;
			$this->i ++;
			
			$level ++;
			if(!empty($genres[$i]->sons)){
				$return .= 	$this->show_genre_tree($genres[$i]->sons,$level,$path);
			}
			$level --;
			array_splice($path,count($path)-1);
		}
		//echo $return;
		return $return;
		
	}
	
	function render_option($id, $name, $level, $path, $parents){
		$indent = "";
		
		if($parents) $parents = implode(", ",$parents);
		else $parents = "";
		
		for($i = 0; $i < $level; $i++){
			$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;";	
		}
		
		$checked 	= JHTML::_('grid.id',   $this->i, $id );
		$link 		= JRoute::_( 'index.php?option=com_muscol&controller=genre&task=edit&cid[]='. $id );
		
		$path = implode(" &raquo; ",$path);
		
		return '
		<tr class="row'.$this->k.'">
			<td class="hidden-phone">
				'.$id .'
			</td>
			<td class="hidden-phone">
				'. $checked .'
			</td>
			<td >
				<a href="'.$link.'">'.$indent. $name. '</a>
			</td>
			<td >
				'. $parents . '
			</td>
			<td class="hidden-phone" >
				'. $path. '
			</td>

		</tr>';
            
	}
}