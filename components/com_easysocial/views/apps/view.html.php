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

FD::import( 'site:/views/views' );

class EasySocialViewApps extends EasySocialSiteView
{
	/**
	 * Displays the apps on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display( $tpl = null )
	{
		// Require user to be logged in
		FD::requireLogin();


		if (! $this->config->get('apps.browser')) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_APPS_BROWSER_DISABLED'));
		}


		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get current logged in user.
		$my 		= FD::user();

		// Get model.
		$model 		= FD::model( 'Apps' );
		$sort 		= JRequest::getVar( 'sort' , 'alphabetical' );
		$order 		= JRequest::getWord( 'order' , 'asc' );
		$options	= array( 'type' => SOCIAL_APPS_TYPE_APPS , 'installable' => true , 'sort' => $sort , 'order' => $order, 'group' => SOCIAL_APPS_GROUP_USER );
		$modelFunc	= 'getApps';

		switch( $sort )
		{
			case 'recent':
				$options['sort'] = 'a.created';
				$options['order'] = 'desc';
				break;

			case 'alphabetical':
				$options['sort'] = 'a.title';
				$options['order'] = 'asc';
				break;

			case 'trending':
				// need a separate logic to get trending based on apps_map
				$modelFunc = 'getTrendingApps';
				break;
		}

		// Get the current filter
		$filter 	= JRequest::getWord( 'filter' , 'browse' );
		$title	 	= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_BROWSE_APPS' );

		if( $filter == 'mine' )
		{
			$options[ 'uid' ]	= $my->id;
			$options[ 'key' ]	= SOCIAL_TYPE_USER;
			$title 				= JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_YOUR_APPS' );
		}

		// Set the page title
		FD::page()->title( $title );

		// Try to fetch the apps now.
		$apps 		= $model->$modelFunc( $options );

		$this->set( 'filter', $filter );
		$this->set( 'sort'	, $sort );
		$this->set( 'apps'	, $apps );

		parent::display( 'site/apps/default' );
	}

	/**
	 * Displays the application in a main canvas layout which is the full width of the component.
	 * Example:
	 * 		index.php?option=com_easysocial&view=apps&layout=canvas&id=[id]&appView=[appView]
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 */
	public function canvas()
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the unique id of the item that is being viewed
		$uid = $this->input->get('uid', null, 'int');
		$type = $this->input->get('type', SOCIAL_TYPE_USER, 'word');

		// Determines if the type is accessible
		if (!$this->allowed($uid, $type)) {
			return;
		}

		// Get the current app id.
		$id = $this->input->get('id', 0, 'int');

		// Get the current app.
		$app = FD::table('App');
		$state = $app->load($id);

		// Default redirection url
		$redirect = FRoute::dashboard(array(), false);

		// Check if the user has access to this app
		if (!$app->accessible($uid, $type) && $type == SOCIAL_TYPE_USER) {
			$this->info->set(null, JText::_('COM_EASYSOCIAL_APPS_CANVAS_APP_IS_NOT_INSTALLED'), SOCIAL_MSG_ERROR);
			return $this->redirect($redirect);
		}

		// If id is not provided, we need to throw some errors here.
		if (!$id || !$state) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_APPS_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect($redirect);
		}

		// Try to load the app's css.
		$app->loadCss();

		// Check if the app provides any custom view
		$appView = $this->input->get('customView', 'canvas', 'default');

		// We need to set the breadcrumb for the cluster type
		if ($type == 'group') {
			$group = FD::group($uid);
			$this->page->breadcrumb($group->getName());
		}

		// Set the breadcrumbs with the app's title
		$this->page->breadcrumb($app->get('title'));

		// Load the library.
		$lib = FD::apps();
		$contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_CANVAS, $appView, $app, array('uid' => $uid));

		$this->set('uid', $uid);
		$this->set('contents', $contents);

		$template = 'site/apps/default.canvas.' . strtolower($type);

		echo parent::display($template);
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function allowed( $uid , $type )
	{
		if( $type == SOCIAL_TYPE_GROUP )
		{
			$group	= FD::group( $uid );

			if (!$group->id) {
				return JError::raiseError(JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'), 404);
			}

			if( $group->isOpen() )
			{
				return true;
			}

			if( $group->isClosed() && !$group->isMember() )
			{
				// Display private info
				$this->set( 'group' , $group );
				parent::display( 'site/groups/restricted' );
				return false;
			}
		}

		// @TODO: Other user checks.

		return true;
	}
}
