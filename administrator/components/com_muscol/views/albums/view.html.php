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
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');

class AlbumsViewAlbums extends JViewLegacy
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Album Manager' ), 'album' );
		JToolBarHelper::addNew();
		JToolBarHelper::deleteList(JText::_( 'Are you sure you want to delete the selected albums' ));
		//JToolBarHelper::editList();
		
		JToolBarHelper::preferences( 'com_muscol' , '500', '1100');
		
		$document	= JFactory::getDocument();
		
		// Get data from the model
		
		$pagination = $this->get('Pagination');
		$keywords = $this->get('keywords');
		$artists = $this->get('ArtistsList');
		$artist_id = $this->get('ArtistId');
		$items = $this->get('Data');	
	
		// push data into the template
		$this->assignRef('items', $items);	
		$this->assignRef('pagination', $pagination);
		$this->assignRef('keywords', $keywords);
		
		//cridem els JavaScript
		
		JHtml::_('jquery.framework');
		//JHtmlBehavior::framework();
		
		//DISCOGS integration
		$document->addScript('components/com_muscol/assets/discogs.js');
		
		$document->addScript('components/com_muscol/assets/stars.js');
		
		//cridem el CSS
		$document->addStyleSheet('components/com_muscol/assets/albums.css');
		
		$lists['order_Dir'] = $this->get('FilterOrderDir') ;
		$lists['order']     = $this->get('FilterOrder') ;
			
		// get list of sections for dropdown filter
		$javascript = 'onchange="this.form.submit();"';
		$lists['artist_id'] = "<option value='0'>-- ".JText::_( 'Select artist' )." --</option>";
		//print_r($artists);
		for($i = 0; $i < count($artists); $i++){
			if($artists[$i]["id"] == $artist_id) $selected = "selected";
			else $selected = "";
			$lists['artist_id'] .= "<option value='".$artists[$i]["id"]."' $selected>".$artists[$i]["artist_name"]."</option>";
		}
		$lists['artist_id'] = "<select class='chzn-select' name='artist_id' ".$javascript.">".$lists['artist_id']."</select>";
		
		$this->assignRef('lists', $lists);
		
		JHtml::_('formbehavior.chosen', '.chzn-select');
		
		parent::display($tpl);
	}
	
}