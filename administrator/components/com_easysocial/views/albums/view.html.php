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

class EasySocialViewAlbums extends EasySocialAdminView
{
	/**
	 * Default user listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_ALBUMS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_ALBUMS');

		// Get the model
		$model = FD::model('Albums', array('initState' => true));

		// Get filter states.
		$ordering 	= JRequest::getVar( 'ordering' , $model->getState( 'ordering' ) );
		$direction 	= JRequest::getVar( 'direction'	, $model->getState( 'direction' ) );
		$limit 		= $model->getState( 'limit' );
		$published 	= $model->getState( 'published' );
		$search 	= JRequest::getVar( 'search'	, $model->getState( 'search' ) );

		// Add Joomla buttons
		JToolbarHelper::deleteList();

		// Get albums
		$albums 	= $model->getDataWithState();

		// Load frontend language files
		FD::language()->loadSite();

		// Get pagination from model
		$pagination		= $model->getPagination();

		$callback 		= JRequest::getVar( 'callback' , '' );

		$this->set( 'ordering'		, $ordering );
		$this->set( 'limit'			, $limit );
		$this->set( 'direction'		, $direction );
		$this->set( 'callback'		, $callback );
		$this->set( 'search'		, $search );
		$this->set( 'published'		, $published );
		$this->set( 'pagination'	, $pagination );
		$this->set( 'albums' 		, $albums );

		echo parent::display( 'admin/albums/default' );
	}

	/**
	 * Post process after an album is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		FD::info()->set( $this->getMessage() );
		$this->redirect( 'index.php?option=com_easysocial&view=albums' );
	}
}
