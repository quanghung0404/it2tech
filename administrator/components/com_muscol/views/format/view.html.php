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

class FormatsViewFormat extends JViewLegacy
{

	function display($tpl = null)
	{

		$format		= $this->get('Data');
		$formats		= $this->get('FormatsData');
		
		$isNew		= ($format->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Format' ).': <small><small>[ ' . $text.' ]</small></small>','format' );
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
		
		if($format->display_group == 0) $format->display_group = $format->id;

		$this->assignRef('format',		$format);
		$this->assignRef('formats',		$formats);
		
		JHtml::_('formbehavior.chosen', '.chzn-select');

		parent::display($tpl);
	}
}