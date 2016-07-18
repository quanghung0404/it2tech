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

class TypesViewType extends JViewLegacy
{

	function display($tpl = null)
	{

		$type		= $this->get('Data');
		
		$isNew		= ($type->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Type' ).': <small><small>[ ' . $text.' ]</small></small>' ,'type');
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');
		

		$this->assignRef('type',		$type);

		parent::display($tpl);
	}
}