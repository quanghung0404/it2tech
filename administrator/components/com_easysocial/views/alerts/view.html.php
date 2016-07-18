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

class EasySocialViewAlerts extends EasySocialAdminView
{
	/**
	 * Main method to display the badges view.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	null
	 *
	 */
	public function display( $tpl = null )
	{
		// Add heading here.
		$this->setHeading('COM_EASYSOCIAL_HEADING_ALERTS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_ALERTS');

		// Default filters
		$options = array();

		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::divider();
		JToolbarHelper::custom( 'emailPublish' , 'publish' , '' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_PUBLISH_EMAIL' ) );
		JToolbarHelper::custom( 'emailUnpublish' , 'unpublish' , '' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_UNPUBLISH_EMAIL' ) );
		JToolbarHelper::divider();
		JToolbarHelper::custom( 'systemPublish' , 'publish' , '' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_PUBLISH_SYSTEM' ) );
		JToolbarHelper::custom( 'systemUnpublish' , 'unpublish' , '' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_UNPUBLISH_SYSTEM' ) );

		// Load badges model.
		$model		= FD::model( 'Alert' , array( 'initState' => true ) );

		// Get the current ordering.
		$ordering	= $model->getState( 'ordering' );
		$direction	= $model->getState( 'direction' );
		$published	= $model->getState( 'published' );
		$limit		= $model->getState( 'limit' );

		// Get the badges
		$alerts		= $model->getItems();

		// Get pagination
		$pagination 	= $model->getPagination();

		$this->set( 'limit'			, $limit );
		$this->set( 'ordering'		, $ordering );
		$this->set( 'direction'		, $direction );
		$this->set( 'published'		, $published );
		$this->set( 'alerts'		, $alerts );
		$this->set( 'pagination'	, $pagination );

		echo parent::display( 'admin/alerts/default' );
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
		$this->setHeading('COM_EASYSOCIAL_HEADING_DISCOVER_ALERTS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_DISCOVER_ALERTS');

		JToolbarHelper::custom('discover', 'download', '', JText::_('COM_EASYSOCIAL_DISCOVER_BUTTON'), false);

		echo parent::display('admin/alerts/discover');
	}

	/**
	 * Post process after alerts has been published / unpublished
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The current task
	 */
	public function togglePublish( $task = null )
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=alerts' );
	}

	public function install( $tpl = null )
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_INSTALL_ALERTS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_INSTALL_ALERTS');

		echo parent::display('admin/alerts/install');
	}

	public function upload()
	{
		// Get info object.
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		return $this->redirect( 'index.php?option=com_easysocial&view=alerts&layout=install' );
	}

	public function publish()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=alerts');
	}
}
