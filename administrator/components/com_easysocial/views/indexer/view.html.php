<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main views file.
FD::import( 'admin:/views/views' );

class EasySocialViewIndexer extends EasySocialAdminView
{
	/**
	 * Default application listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		// redirect to main page.
		$mainframe = JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_easysocial' );
		return;


		$info	= FD::info();
		$info->set( $this->getMessage() );

		// Set the page heading
		$this->setHeading( JText::_( 'COM_EASYSOCIAL_HEADING_INDEXER' ) );

		// Set the page icon
		$this->setIcon( 'icon-jar jar-console_command' );

		// Set the page description
		$this->setDescription( JText::_( 'COM_EASYSOCIAL_DESCRIPTION_INDEXER' ) );

		// set joomla toolbar buttons
		$this->toolbar();


		$component 	= JRequest::getVar( 'component' , '' );
		$type		= JRequest::getVar( 'type' , '' );

		// Get the applications model.
		$model 		= FD::model( 'Indexer' );

		$filterItems = $model->getFilters();

		// Load the applications.
		$items 		= $model->getItems(
								array(
										'type'		=> $type,
										'component'	=> $component
								)
						);

		// Get the pagination.
		$pagination	= $model->getPagination();

		$this->set( 'type'	, $type );
		$this->set( 'component'	, $component );
		$this->set( 'filterItem', $filterItems);

		$this->set( 'items'	, $items );
		$this->set( 'pagination'	, $pagination );

		parent::display( 'admin/indexer/default' );
	}

	public function reindex( $tpl = null )
	{
		// redirect to main page.
		$mainframe = JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_easysocial' );
		return;

		// Set page heading
		$this->setHeading( JText::_( 'Index' ) );

		// Set page icon
		$this->setIcon( 'icon-jar jar-store' );

		// Set page description
		$this->setDescription( JText::_( 'Re-indexing your EasySocial items into search indexer.' ) );

		parent::display( 'admin/indexer/reindex' );
	}

	public function toolbar()
	{
		// Add Joomla buttons here.
		JToolbarHelper::addNew( 'reindex', JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_INDEX_ITEMS' ) );
		JToolbarHelper::deleteList( '' , 'remove' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_DELETE' ) );
		JToolbarHelper::divider();
		JToolbarHelper::trash( 'purge' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_PURGE' ) , false );
	}

	/**
	 * Post processing after items are purged
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=indexer' );
	}

	/**
	 * Post processing after items are purged
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=indexer' );
	}
}
