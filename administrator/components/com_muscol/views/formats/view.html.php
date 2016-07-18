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

class FormatsViewFormats extends JViewLegacy
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Format Manager' ), 'format' );
		
		//JToolBarHelper::editList();
		JToolBarHelper::addNew();
		JToolBarHelper::deleteList(JText::_( 'Are you sure you want to delete the selected formats' ));
		
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');

		// Get data from the model
		$items		= $this->get( 'Data');

		$this->assignRef('items',		$items);

		parent::display($tpl);
	}
}