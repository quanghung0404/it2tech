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

class ArtistsViewArtists extends JViewLegacy
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Artist Manager' ), 'artist' );
		
		//JToolBarHelper::editList();
		JToolBarHelper::addNew();
		JToolBarHelper::deleteList(JText::_( 'Are you sure you want to delete the selected artists' ));
		
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');
		
		$params =JComponentHelper::getParams( 'com_muscol' );

		// Get data from the model
		$letter = $this->get('Letter');
		$letters = MusColAlphabets::get_combined_array();
		
		if(in_array($params->get('alphabet'),array('arabicltr', 'arabicrtl'))) $letters = array_reverse($letters, true) ;
		
		
		$keywords = $this->get('keywords');
		$items		= $this->get( 'Data');
		$pagination = $this->get('Pagination');

		$lists['order_Dir'] = $this->get('FilterOrderDir') ;
		$lists['order']     = $this->get('FilterOrder') ;

		// get list of sections for dropdown filter
		$javascript = 'onchange="this.form.submit();"';
		$lists['letter'] = "<option value=''>-- ".JText::_( 'Select letter' )." --</option>";
		
		foreach($letters as $key => $value){
			if($key == $letter) $selected = "selected";
			else $selected = "";
			
			$lists['letter'] .= "<option value='".$key."' $selected>".$value."</option>";
		}
		$lists['letter'] = "<select class='chzn-select' name='letter' ".$javascript.">".$lists['letter']."</select>";
		
		$this->assignRef('lists', $lists);
		
		$this->assignRef('keywords', $keywords);
		// push data into the template
		$this->assignRef('letters', $letters);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('items',		$items);
		
		JHtml::_('formbehavior.chosen', '.chzn-select');

		parent::display($tpl);
	}
}