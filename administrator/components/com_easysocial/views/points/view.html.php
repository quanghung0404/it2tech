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

FD::import( 'admin:/views/views' );

class EasySocialViewPoints extends EasySocialAdminView
{
	/**
	 * Main method to display the points view.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display( $tpl = null )
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_POINTS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_POINTS');

		// Add Joomla buttons here
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();

		$model 		= FD::model( 'Points' , array( 'initState' => true ) );
		$state 		= $model->getState( 'published' );
		$extension 	= $model->getState( 'filter' );
		$limit 		= $model->getState( 'limit' );
		$ordering 	= $model->getState( 'ordering' );
		$direction	= $model->getState( 'direction' );
		$search 	= $model->getState( 'search' );

		// Load a list of extensions so that users can filter them.
		$extensions	= $model->getExtensions();

		// Load the language files for each available extension
		$langlib = FD::language();
		foreach( $extensions as $e )
		{
			$langlib->load( $e, JPATH_ROOT );
			$langlib->load( $e, JPATH_ADMINISTRATOR );
		}

		$points		= $model->getItems();

		// Get pagination
		$pagination = $model->getPagination();

		$this->set( 'ordering'	, $ordering );
		$this->set( 'direction'	, $direction );
		$this->set( 'limit'		, $limit );
		$this->set( 'selectedExtension'	, $extension );
		$this->set( 'search'	, $search );
		$this->set( 'pagination', $pagination );
		$this->set( 'extensions', $extensions );
		$this->set( 'extension'	, $extension );
		$this->set( 'points'	, $points );
		$this->set( 'state' 	, $state );

		echo parent::display( 'admin/points/default' );
	}

	/**
	 * Post process points saving
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function save( $task , $point )
	{
		FD::info()->set( $this->getMessage() );

		if( $this->hasErrors() )
		{
			$this->redirect( 'index.php?option=com_easysocial&view=points&layout=form&id=' . $point->id );
			$this->close();
		}

		if( $task == 'apply' )
		{
			$this->redirect( 'index.php?option=com_easysocial&view=points&layout=form&id=' . $point->id );
			$this->close();
		}

		$this->redirect( 'index.php?option=com_easysocial&view=points' );
		$this->close();
	}

	/**
	 * Main method to display the form.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function form( $tpl = null )
	{
		// Get the id from the request.
		$id 	= JRequest::getInt( 'id' , 0 );

		// Get the table object
		$point	= FD::table( 'Points' );
		$state 	= $point->load( $id );

		// If it can't load, this is most likely a new point form.
		if( $state )
		{
			// Add heading here.
			$this->setHeading( JText::_( 'COM_EASYSOCIAL_HEADING_EDIT_POINTS' ) );

			// Add description here.
			$this->setDescription( JText::_( 'COM_EASYSOCIAL_DESCRIPTION_EDIT_POINTS' ) );
		}

		JToolbarHelper::cancel();
		JToolbarHelper::divider();
		JToolbarHelper::apply( 'apply' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE' ) , false , false );
		JToolbarHelper::save( 'save' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE' ) );

		$params 	= $point->getParams();

		if (!$params) {
			$params 	= array();
		} else {
			$params 	= $params->toArray();
		}

		$this->set('params', $params);
		$this->set( 'point'	, $point );

		echo parent::display( 'admin/points/form' );
	}

	/**
	 * Redirects user back to the points listing once it's installed
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function upload()
	{
		// Get info object.
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		return $this->redirect( 'index.php?option=com_easysocial&view=points&layout=install' );
	}

	/**
	 * Displays the CSV upload form
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function csv( $tpl = null )
	{
		// Add heading here.
		$this->setHeading('COM_EASYSOCIAL_HEADING_UPLOAD_CSV_POINTS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_UPLOAD_CSV_POINTS');


		echo parent::display('admin/points/csv');
	}

	/**
	 * Post process after the mass assignment is completed
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function massAssign( $success = array() , $failed = array() )
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=points&layout=csv' );
	}

	/**
	 * Displays the installation layout for points.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function install( $tpl = null )
	{
		// Add heading here.
		$this->setHeading('COM_EASYSOCIAL_HEADING_INSTALL_POINTS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_INSTALL_POINTS');

		echo parent::display('admin/points/install');
	}

	/**
	 * Displays the discover layout for points.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function discover( $tpl = null )
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_DISCOVER_POINTS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_INSTALL_POINTS');

		JToolbarHelper::custom('discover', 'download', '', JText::_('COM_EASYSOCIAL_DISCOVER_BUTTON'), false);

		echo parent::display('admin/points/discover');
	}

	/**
	 * Post processing for publishing and unpublishing an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function publish()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=points' );
		$this->close();
	}

	/**
	 * Post processing for deleting an item
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function remove()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=points' );
		$this->close();
	}
}
