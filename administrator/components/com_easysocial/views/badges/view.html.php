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

// Include main view.
FD::import( 'admin:/views/views' );

class EasySocialViewBadges extends EasySocialAdminView
{
	/**
	 * Main method to display the badges view.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display( $tpl = null )
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_BADGES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_BADGES');

		// Add Joomla buttons here
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();

		// Default filters
		$options 		= array();

		// Load badges model.
		$model 	= FD::model( 'Badges' , array( 'initState' => true ) );

		// Get the search query from post
		$search		= JRequest::getVar( 'search' , $model->getState( 'search' ) );

		// Get the current ordering.
		$ordering 	= JRequest::getWord( 'ordering' , $model->getState( 'ordering' ) );
		$direction 	= JRequest::getWord( 'direction' , $model->getState( 'direction' ) );
		$extension 	= JRequest::getWord( 'extension' , $model->getState( 'extension' ) );
		$state	 	= JRequest::getVar( 'state', $model->getState( 'state' ) );
		$limit 		= $model->getState( 'limit' );

		// Get the badges
		$badges			= $model->getItemsWithState();

		// Get a list of unique extensions.
		$extensions 	= $model->getExtensions();

		// Load the language file from each extension
		$langlib = FD::language();
		foreach( $extensions as $e )
		{
			$langlib->load( $e, JPATH_ROOT );
			$langlib->load( $e, JPATH_ADMINISTRATOR );
		}

		// Get pagination
		$pagination 	= $model->getPagination();

		$this->set( 'limit'			, $limit );
		$this->set( 'extension'		, $extension );
		$this->set( 'search'		, $search );
		$this->set( 'ordering'		, $ordering );
		$this->set( 'direction'		, $direction );
		$this->set( 'state'			, $state );
		$this->set( 'badges'		, $badges );
		$this->set( 'pagination'	, $pagination );
		$this->set( 'extensions'	, $extensions );

		echo parent::display( 'admin/badges/default' );
	}

	/**
	 * Post process after badges has been published / unpublished
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The current task
	 */
	public function togglePublish( $task = null )
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=badges' );
	}

	/**
	 * Post process after a badge is stored
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The current task
	 * @return
	 */
	public function store( $task = null , $badge = null )
	{
		FD::info()->set( $this->getMessage() );

		if( $this->hasErrors() )
		{
			$this->redirect( 'index.php?option=com_easysocial&view=badges' );
			$this->close();
		}

		if( $task == 'apply' )
		{
			$this->redirect( 'index.php?option=com_easysocial&view=badges&layout=form&id=' . $badge->id );
			$this->close();
		}

		$this->redirect( 'index.php?option=com_easysocial&view=badges' );
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

		$this->redirect( 'index.php?option=com_easysocial&view=badges&layout=csv' );
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
		$this->setHeading('COM_EASYSOCIAL_HEADING_UPLOAD_CSV_BADGES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_UPLOAD_CSV_BADGES');


		echo parent::display( 'admin/badges/csv' );
	}

	/**
	 * Post process after a badge is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The current task
	 * @return
	 */
	public function remove( $task = null , $badge = null )
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=badges' );
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

		// Id must exist here because user cannot create a badge manually.
		if( !$id )
		{
			FD::info()->set( JText::_( 'COM_EASYSOCIAL_BADGES_INVALID_BADGE_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			$this->redirect( 'index.php?option=com_easysocial&view=badges' );
			$this->close();
		}

		// Get the table object
		$badge	= FD::table( 'Badge' );
		$state 	= $badge->load( $id );

		if( $state && !empty( $badge->extension ) && $badge->extension !== SOCIAL_COMPONENT_NAME )
		{
			FD::language()->load( $badge->extension, JPATH_ROOT );
			FD::language()->load( $badge->extension, JPATH_ADMINISTRATOR );
		}

		// Add heading here.
		$this->setHeading($badge->get('title'));
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_EDIT_BADGE');

		// Add buttons
		JToolbarHelper::cancel( 'cancel' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL' ) );
		JToolbarHelper::divider();
		JToolbarHelper::apply( 'apply' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE' ) , false , false );
		JToolbarHelper::save( 'save' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE' ) );

		// Push the badge to the theme.
		$this->set( 'badge'	, $badge );

		echo parent::display( 'admin/badges/form' );
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
		$this->setHeading('COM_EASYSOCIAL_HEADING_INSTALL_BADGES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_INSTALL_BADGES');

		echo parent::display('admin/badges/install');
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
	public function discover($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_DISCOVER_BADGES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_DISCOVER_BADGES');

		JToolbarHelper::custom('discover', 'download', '', JText::_('COM_EASYSOCIAL_DISCOVER_BUTTON'), false);
		
		echo parent::display( 'admin/badges/discover' );
	}

	public function upload()
	{
		// Get info object.
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		return $this->redirect( 'index.php?option=com_easysocial&view=badges&layout=install' );
	}
}
