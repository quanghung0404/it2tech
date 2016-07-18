<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main view here.
FD::import('admin:/includes/views');

class EasySocialSiteView extends EasySocialView
{
	public $rssLink = '';

	public function __construct($config = array())
	{
		// We want to allow child classes to easily access theme configurations on the view
		$this->themeConfig = FD::themes()->getConfig();

		parent::__construct($config);

		// Check if there is a method isFeatureEnabled exists. If it does, we should do a check all the time.
		if (method_exists($this, 'isFeatureEnabled')) {
			$this->isFeatureEnabled();
		}

		// check if user is required to reset password or not.
		if ($this->my->require_reset) {
			$controller = $this->input->get('controller', '', 'cmd');
			$view = $this->input->get('view', '', 'cmd');

			if ($view != 'account' && $view != 'registration' && $view != 'oauth' && $controller != 'account') {
				$url 	= FRoute::account( array( 'layout' => 'requirePasswordReset' ) , false );
				$this->redirect($url);
				return;
			}
		}

		// // When the user doesn't have community access, ensure that they can only view selected views.
		if (!$this->my->hasCommunityAccess()) {

			// Get the current view
			$view = $this->getName();
			$layout = $this->input->get('layout', '', 'cmd');

			// If this is an ajax call, we need to allow some ajax calls to go through
			$allowedAjaxNamespaces = array('site/views/profile/showFormError');

			if ($this->doc->getType() == 'ajax') {
				$namespace = $this->input->get('namespace', '', 'default');

				// If this is an ajax call, and the namespace is valid, skip checking below
				if (in_array($namespace, $allowedAjaxNamespaces)) {
					return;
				}
			}

			// Define allowed views and layout
			$allowedViews = array('profile');
			$allowedLayouts = array('edit');

			// views that we should redirect the user to profile edit page.
			$redirectView = array('dashboard', 'profile', 'login');


			// User should be allowed to logout from the site
			$isLogout = (($this->input->get('controller', '', 'cmd') == 'account') && ($this->input->get('task', '', 'cmd') == 'logout')) || (($this->input->get('view', '', 'cmd') == 'login') && ($this->input->get('layout', '', 'cmd') == 'logout'));

			// user should be allowed to save their profile details on the site.
			$isProfileSaving = ($this->input->get('controller', '', 'cmd') == 'profile') && ( $this->input->get('task', '', 'cmd') == 'save');

			if (in_array($view, $redirectView) && !$layout && !$isLogout && !$isProfileSaving) {
				// we need to redirect the user to profile edit page.
				$this->redirect(FRoute::profile(array('layout' => 'edit'), false));
				return;
			}

			// Ensure that the restricted user is not able to view other views
			if (!in_array($view, $allowedViews) && !$isLogout && !$isProfileSaving) {
				return JError::raiseError(500, JText::_('COM_EASYSOCIAL_NOT_ALLOWED_TO_VIEW_SECTION'));
			}

			// Ensure that the user is only viewing the allowed layouts
			if (!in_array($layout, $allowedLayouts) && !$isLogout && !$isProfileSaving) {
				return JError::raiseError(500, JText::_('COM_EASYSOCIAL_NOT_ALLOWED_TO_VIEW_SECTION'));
			}
		}
	}

	/**
	 * Adds the RSS into the headers
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addRss($link)
	{
		$glue = $this->jconfig->getValue('sef') ? '?' : '&';
		$rss = $link . $glue . 'format=feed&type=rss';
		$atom = $link . $glue . '&format=feed&type=atom';

		if ($this->doc->getType() == 'html') {
			// Add RSS specs
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->doc->addHeadLink($rss, 'alternate', 'rel', $attribs);

			// Add Atom specs
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->doc->addHeadLink($atom, 'alternate', 'rel', $attribs);
		}

		// Set the rss link to the class scope so child can easily get the url
		$this->rssLink = $rss;
	}

	/**
	 * determine author email in rss
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRssEmail($author)
	{
        if ($this->jconfig->getValue('feed_email') == 'none') {
            return '';
        }

        if ($this->jconfig->getValue('feed_email') == 'author') {
            return $author->email;
        }

        return $this->jconfig->getValue('mailfrom');
	}

	/**
	 * Determines if the current view should be locked down.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function lockdown()
	{
		// Default, all views are locked down.
		$state 	= true;

		if( method_exists( $this , 'isLockDown' ) )
		{
			$state 	= $this->isLockDown();
		}

		return $state;
	}

	/**
	 * Responsible to render the views / layouts from the front end.
	 * This is a single point of entry function.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$type = $this->doc->getType();
		$show = $this->input->get('show', '', 'string');

		if ($type != 'html') {
			return parent::display($tpl);
		}

		// Include main structure here.
		$theme = FD::themes();

		// Capture output.
		ob_start();
		parent::display($tpl);
		$contents 	= ob_get_contents();
		ob_end_clean();

		// Get the menu's suffix
		$suffix = $this->getMenuSuffix();

		// Get the current view.
		$view 	= $this->input->get('view', '', 'cmd');
		$view 	= !empty( $view ) ? ' view-' . $view : '';

		// Get the current task
		$task 	= $this->input->get('task', '', 'cmd');
		$task 	= !empty( $task ) ? ' task-' . $task : '';

		// Get any "id" or "cid" from the request.
		$object = $this->input->get('id', $this->input->get('cid', 0, 'int'), 'int');
		$object = !empty( $object ) ? ' object-' . $object : '';

		// Get any layout
		$layout = $this->input->get('layout', '', 'cmd');
		$layout = !empty( $layout ) ? ' layout-' . $layout : '';

		$theme->set('suffix', $suffix);
		$theme->set('layout', $layout);
		$theme->set('object', $object);
		$theme->set('task', $task);
		$theme->set('view', $view);
		$theme->set('show', $show);
		$theme->set('contents', $contents);
		$theme->set('toolbar', $this->getToolbar());

		// Component template scripts
		$page       = FD::page();
		$scripts    = '<script>' . implode('</script><script>', $page->inlineScripts) . '</script>';
		$theme->set( 'scripts'  , $scripts );

		// Ensure component template scripts don't get added to the head.
		$page->inlineScripts = array();

		echo $theme->output('site/structure/default');
	}

	/**
	 * Retrieve the menu suffix for a page
	 *
	 * @since	1.2.8
	 * @access	public
	 * @return	string	The suffix class names
	 */
	public function getMenuSuffix()
	{
		$menu 	= $this->app->getMenu()->getActive();
		$suffix	= '';

		if ($menu) {
			$suffix = $menu->params->get('pageclass_sfx', '');
		}

		return $suffix;
	}

	/**
	 * Generic 404 error page
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function error()
	{
		return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_IS_NOT_AVAILABLE'));
	}

	/**
	 * Allows child library to validate an authentication code
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateAuth()
	{
		// Get user's authentication code
		$auth = $this->input->get('auth', '', 'string');

        // Get the current logged in user's information
        $model  = FD::model('Users');
        $id     = $model->getUserIdFromAuth($auth);

        if (!$id) {
            $this->set('code', 403);
            $this->set('message', JText::_('Invalid user id provided.'));

            return self::display();
        }

        return $id;
	}

	/**
	 * Helper method to retrieve the toolbar's HTML code.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getToolbar()
	{
		// The current logged in user.
		$toolbar = ES::toolbar();

		return $toolbar->render();
	}
}
