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

class EasySocialViewApps extends EasySocialAdminView
{
	/**
	 * Default application listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display($tpl = null)
	{
		// Set the page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_APPS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_APPS');

		// Add Joomla buttons here.
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList('', 'uninstall', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_UNINSTALL'));

		// Get the applications model.
		$model = ES::model('Apps', array('initState' => true));

		// Get the current ordering.
		$search = $this->input->get('search', $model->getState('search'));
		$state = $this->input->get('state', $model->getState('state'));
		$group = $this->input->get('group', $model->getState('group'));

		$ordering = $model->getState('ordering');
		$direction = $model->getState('direction');
		$limit = $model->getState('limit');
		$search = $model->getState('search');
		$group = $model->getState('group');

		// Load the applications.
		$options = array('filter' => 'apps');
		$apps = $model->getItemsWithState($options);

		// Get the pagination.
		$pagination	= $model->getPagination();

		$this->set('group', $group);
		$this->set('search', $search);
		$this->set('limit', $limit);
		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('state', $state);
		$this->set('apps', $apps);
		$this->set('pagination', $pagination);

		parent::display('admin/apps/default');
	}

	/**
	 * Default fields applications listing
	 *
	 * @since	1.4
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function fields($tpl = null)
	{
		// Set the page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_APPS_FIELDS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_APPS_FIELDS');

		// Add Joomla buttons here.
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList('', 'uninstall', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_UNINSTALL'));

		// Get the applications model.
		$model = ES::model('Apps', array('initState' => true));

		// Get the current ordering.
		$search = $this->input->get('search', $model->getState('search'));
		$state = $this->input->get('state', $model->getState('state'));
		$group = $this->input->get('group', $model->getState('group'));

		$ordering = $model->getState('ordering');
		$direction = $model->getState('direction');
		$limit = $model->getState('limit');
		$search = $model->getState('search');
		$group = $model->getState('group');

		// Load the applications.
		$options = array('filter' => 'fields');
		$apps = $model->getItemsWithState($options);

		// Get the pagination.
		$pagination	= $model->getPagination();

		$this->set('group', $group);
		$this->set('search', $search);
		$this->set('limit', $limit);
		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('state', $state);
		$this->set('apps', $apps);
		$this->set('pagination', $pagination);

		parent::display('admin/apps/fields');
	}

	/**
	 * Displays the installation page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function install()
	{
		$info	= FD::info();

		$info->set( $this->getMessage() );

		// Set the page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_APPS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_APPS_INSTALLER');

		// Set the default temporary path.
		$jConfig 		= JFactory::getConfig();
		$temporaryPath	= $jConfig->get( 'tmp_path' );

		// Retrieve folders.
		$appsModel		= FD::model( 'Apps' );
		$directories	= $appsModel->getDirectoryPermissions();

		$this->set( 'directories'	, $directories );
		$this->set( 'temporaryPath' , $temporaryPath );

		parent::display( 'admin/apps/install.form' );
	}

	/**
	 * Post process after discovered items are purged
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function purgeDiscovered()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=apps&layout=discover' );
	}

	/**
	 * Displays the installation page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function discover()
	{
		FD::info()->set( $this->getMessage() );

		// Set the page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_DISCOVER_APPS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_DISCOVER_APPS');

		// Add Joomla buttons here.
		JToolbarHelper::custom( 'installDiscovered' , 'upload' , '' , JText::_( 'COM_EASYSOCIAL_INSTALL_SELECTED_BUTTON' ) , false );
		JToolbarHelper::divider();
		JToolbarHelper::custom( 'discover' , 'refresh' , '' , JText::_( 'COM_EASYSOCIAL_DISCOVER_BUTTON' ) , false );
		JToolbarHelper::custom( 'purgeDiscovered' , 'trash' , '' , JText::_( 'COM_EASYSOCIAL_PURGE_CACHE_BUTTON' ) , false );

		// Get the applications model.
		$model 		= FD::model( 'Apps' , array( 'initState' => true ) );

		// Get the current ordering.
		$search 	= JRequest::getVar( 'search' , $model->getState( 'search' ) );
		$filter		= JRequest::getCmd( 'filter' , $model->getState( 'filter' ) );
		$ordering 	= $model->getState( 'ordering' );
		$direction	= $model->getState( 'direction' );
		$limit 		= $model->getState( 'limit' );
		$search 	= $model->getState( 'search' );

		// Load the applications.
		$apps 		= $model->getItemsWithState( array( 'discover' => true ));

		// Get the pagination.
		$pagination	= $model->getPagination();

		$this->set( 'search' 	, $search );
		$this->set( 'limit'		, $limit );
		$this->set( 'ordering'	, $ordering );
		$this->set( 'direction'	, $direction );
		$this->set( 'filter', $filter );
		$this->set( 'apps'	, $apps );
		$this->set( 'pagination'	, $pagination );

		parent::display( 'admin/apps/discover' );
	}

	/**
	 * Post process after installing discovered apps
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installDiscovered()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=apps&layout=discover' );
	}

	/**
	 * Displays installation completed page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	stdclass	A stdclass containing `output` which is from the callback method and `desc` which is the application description.
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function installCompleted( $app )
	{
		// Set the page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_APPS_INSTALL_SUCCESS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_APPS_INSTALL_SUCCESS');

		$session = JFactory::getSession();
		$session->set('application.queue', null);

		// Get the apps meta.
		$meta = $app->getMeta();

		$this->set('meta', $meta);
		$this->set('app', $app);
		$this->set('output', $app->result->output);
		$this->set('desc', $meta->desc);

		echo parent::display('admin/apps/install.completed');
	}

	/**
	 * Post process after app is published
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function publish($type)
	{
		$this->info->set($this->getMessage());

		$url = 'index.php?option=com_easysocial&view=apps';

		if ($type == 'fields') {
			$url .= '&layout=fields';
		}

		// Reinitialize previous states
		$limitstart = $this->input->get('limitstart', '');
		$group = $this->input->get('group', '');
		$state = $this->input->get('state', '');

		if ($limitstart) {
			$url .= '&limitstart=' . $limitstart;
		}

		if ($group) {
			$url .= '&group=' . $group;
		}

		if ($state) {
			$url .= '&state=' . $state;
		}
		
		$this->redirect($url);
	}

	/**
	 * Post process after app is unpublished
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unpublish($type)
	{
		$this->info->set($this->getMessage());

		$url = 'index.php?option=com_easysocial&view=apps';

		if ($type == 'fields') {
			$url .= '&layout=fields';
		}

		// Reinitialize previous states
		$limitstart = $this->input->get('limitstart', '');
		$group = $this->input->get('group', '');
		$state = $this->input->get('state', '');

		if ($limitstart) {
			$url .= '&limitstart=' . $limitstart;
		}

		if ($group) {
			$url .= '&group=' . $group;
		}

		if ($state) {
			$url .= '&state=' . $state;
		}
		
		$this->redirect($url);
	}

	/**
	 * Post process after apps has been uninstalled
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function uninstall()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=apps' );
		$this->close();
	}

	/**
	 * Post process after an app is saved
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store( $app = null , $task = '' )
	{
		FD::info()->set( $this->getMessage() );

		if( $task == 'apply' )
		{
			$this->redirect( 'index.php?option=com_easysocial&view=apps&layout=form&id=' . $app->id );
			$this->close();
		}

		if( $task == 'save' )
		{
			$this->redirect( 'index.php?option=com_easysocial&view=apps' );
			$this->close();
		}
	}

	/**
	 * Displays the application form page.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function form()
	{
		// Get the application id from request.
		$id 		= JRequest::getInt( 'id' );

		// Load the application.
		$app 		= FD::table( 'App' );
		$app->load( $id );

		if( !$id || !$app->id )
		{
			// App has to have a valid id.
			FD::info()->set( false , JText::_( 'COM_EASYSOCIAL_APP_INVALID_ID' ) , SOCIAL_MSG_ERROR );
			$this->redirect( 'index.php?option=com_easysocial&view=apps' );
			$this->close();
		}

		FD::language()->loadSite();

		// Set the page heading
		$this->setHeading($app->get('title'));
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_APPS_CONFIGURATION');

		JToolbarHelper::cancel();
		JToolbarHelper::divider();
		JToolbarHelper::apply();
		JToolbarHelper::save();

		$this->set('app', $app);

		parent::display( 'admin/apps/form' );
	}

	/**
	 * Displays when the installation is completed
	 *
	 * @access	public
	 */
	public function completed( $app )
	{
		$this->set( 'app'		, $app );

		// Display the success messages.
		parent::display( 'admin.installer.completed' );

		// Display the form again so that the user can continue with the installation if needed.
		$this->display();
	}

	public function errors( $response )
	{
		$this->set( 'response' , $response );

		parent::display( 'admin.installer.errors' );
	}
}
