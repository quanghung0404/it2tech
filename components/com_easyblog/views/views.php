<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');

class EasyBlogView extends JViewLegacy
{
	protected $app = null;
	protected $my = null;
    protected $customTheme = null;
    protected $props = array();

    // This determines the parameters key prefix to use in menu params
    public $paramsPrefix = 'listing';


	public function __construct()
	{
        $this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->config = EB::config();
        $this->info = EB::info();
        $this->jconfig = EB::jconfig();
		$this->acl = EB::acl();

        // If this is a dashboard theme, we need to let the theme object know
        $options = array('paramsPrefix' => $this->paramsPrefix);

        // If this is an ajax document, we should pass the $ajax library to the client
        if ($this->doc->getType() == 'ajax') {

            //we need to load frontend language from here incase it was called from backend.
            JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

            $this->ajax = EB::ajax();
        }

		// Create an instance of the theme so child can start setting variables to it.
		$this->theme = EB::template(null, $options);

        // Set the input object
        $this->input = EB::request();
	}

	/**
	 * Allows child to set variables
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set($key, $value = '')
	{
        if ($this->doc->getType() == 'json') {
            $this->props[$key] = $value;

            return;
        }

		$this->theme->set($key, $value);
	}

    /**
     * Allows children to check for acl
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function checkAcl($rule, $default = null)
    {
        $allowed = $this->acl->get($rule, $default);

        if (!$allowed) {
            JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'));
            return;
        }

        return true;
    }

    /**
     * Responsible to render the css files on the head
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function renderHeaders()
    {
        // Load js stuffs
        $view = $this->input->get('view', '', 'cmd');

        // Determines which js section to initialize
        $section = 'site';

        if ($view == 'dashboard' || $view == 'composer') {
            $section = $view;
        }

        EB::init($section);

        // Get the theme on the site
        $theme = $this->config->get('theme_site');

        if ($this->customTheme) {
            $theme = $this->customTheme;
        }

        // @since 4.0
        // Attach the theme's css
        $stylesheet = EB::stylesheet('site', $theme);
        $stylesheet->attach(null, true, $this->customTheme);
    }

    /**
     * Allows caller to set a custom theme
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function setTheme($theme)
    {
        $this->customTheme = $theme;

        $this->theme->setCategoryTheme($theme);
    }

	/**
	 * Responsible to display the entire component output
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		if ($this->doc->getType() == 'html') {

            // Render headers on the site
            $this->renderHeaders();

			// Get the contents from the view
            $namespace  = 'site/' . $tpl;

			$contents = $this->theme->output($namespace);

			// Get menu suffix
			$suffix = $this->getMenuSuffix();

			// Get the current view.
			$view = $this->getName();

			// Get the current task
			$layout = $this->getLayout();

            // If this is a dashboard theme, we need to let the theme object know
            $options = array();

            if ($this->getName() == 'dashboard') {
                $options['dashboard'] = true;
            }

			// We need to append the contents back into the main structure
            $theme = EB::template(null, $options);
            // $theme = $this->theme;

            $tmpl = $this->input->get('tmpl');

			// Get the toolbar
            $toolbar = '';

            if ($tmpl != 'component' && $view == 'dashboard') {
                $toolbar = $this->getDashboardToolbar();
            }

            if ($tmpl != 'component' && $view != 'dashboard') {
                $toolbar = $this->getToolbar();
            }

            if ($view == 'entry' && $layout != 'preview') {

                $id = $this->input->get('id', 0, 'int');
                $post = EB::post($id);

                if (!$post->isStandardSource()) {
                    $contribution = $post->getBlogContribution();

                    $contributionHeader = $contribution->getHeader();

                    if ($contributionHeader) {
                        $toolbar = '';
                    }
                }
            }


            // Get the theme name
            $themeName = $theme->getName();

            // There is a possibility that the site uses a different font heading
            $headingFont = $this->getHeadingFont();

            // We attach the script tags on the bottom of the page
            $scripts = EB::helper('Scripts')->getScripts();

			// Jomsocial toolbar
            $jsToolbar = EB::jomsocial()->getToolbar();
			$theme->set('jsToolbar', $jsToolbar);

            $lang = JFactory::getLanguage();
            $rtl = $lang->isRTL();

            if ($rtl) {
                $this->doc->addStyleSheet(JURI::root() . 'components/com_easyblog/themes/' . $themeName . '/styles/style-rtl.css');
            }

            $theme->set('rtl', $rtl);
            $theme->set('bootstrap', '');
            $theme->set('themeName', $themeName);
            $theme->set('headingFont', $headingFont);
            $theme->set('jscripts', $scripts);
			$theme->set('toolbar', $toolbar);
			$theme->set('contents', $contents);
			$theme->set('suffix', $suffix);
			$theme->set('layout', $layout);
			$theme->set('view', $view);

			$output = $theme->output('site/structure/default');

            echo $output;
			return;
		}


        if ($this->doc->getType() == 'json') {

            // Determines if the json result should be wrapped with a callback.
            $callback = $this->input->get('callback', '', 'cmd');

            $output = json_encode($this->props);

            if ($callback) {
                $output = $callback . '(' . $output . ')';
            }

            header('Content-type: text/x-json; UTF-8');
            echo $output;
            exit;
        }
		// dump($tpl);
	}

    /**
     * Retrieves the font type for the heading
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getHeadingFont()
    {
        // Get any google fonts
        $font   = $this->config->get('layout_googlefont');

        if ($font != 'site') {

            $font   = explode(' ', $font);
            $font   = strtolower($font[0]);
        }

        return $font;
    }

    /**
     * Sets view in breadcrumbs
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function setViewBreadcrumb($view)
    {
        if (!EasyBlogRouter::isCurrentActiveMenu($view)) {
            $this->setPathway(JText::_('COM_EASYBLOG_BREADCRUMB_' . strtoupper($view)));

            return true;
        }

        return false;
    }

	/**
	 * Retrieves the toolbar for the site.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public function getToolbar()
    {
        // Get the current view
        $view = $this->input->get('view', '', 'cmd');

        // Get a list of available views
        $views = JFolder::folders(JPATH_COMPONENT . '/views');

        // Get the active view name
        $active	= $this->getName();

        // If the current active view doesn't exist on our known views, set the latest to be active by default.
        if (!in_array($active, $views)) {
        	$active = 'latest';
        }

        // Rebuild the views
        $tmp = new stdClass();

        foreach ($views as $key) {
            $tmp->$key  = false;
        }

        // Reset back the views to the tmp variable
        $views = $tmp;

        // Set the active menu
        if (isset($views->$active)) {
            $views->$active = true;
        }

        // Get toolbar stuffs
        $title = $this->config->get('main_title');
        $desc = $this->config->get('main_description');
        $desc = nl2br($desc);
        $authorId = '';

        // Entry view, we want to load the toolbar
        if ($active == 'entry') {
        	$blog = EB::table('blog');
        	$blog->load(JRequest::getInt('id'));

        	$authorId = $blog->created_by;
        }

        // Blogger view, just get the id from the query
        if ($active == 'blogger') {
            $authorId = $this->input->get('id', 0, 'int');
        }

        // If the viewer is viewing a blogger, we'll need to display the header accordingly.
        if (($active == 'blogger' || ($active == 'entry' && $this->config->get('layout_headers_respect_author'))) && $authorId) {

            $author = EB::user($authorId);

            $title = $author->title ? $author->title : $title;
            $desc = $author->getDescription() ? $author->getDescription() : $desc;
        }

        // If the viewer is viewing a team
        if ($active == 'teamblog') {
        	$team = EB::table('Teamblog');
        	$team->load(JRequest::getInt('id'));

        	$title 	= $team->title ? JText::_($team->title) : $title;
        	$desc 	= $team->getDescription() ? $team->getDescription() : $desc;
        }

        // Get the current menu id
        $itemId = $this->input->get('Itemid', 0, 'int');

        // Determines if the heading should be displayed
        $activeMenu = JFactory::getApplication()->getMenu()->getActive();
        $params = new JRegistry();

        if ($activeMenu) {
            $params = $activeMenu->params;
        }

        $heading = $params->get('show_page_heading', '');

        if ($heading) {
            $title = $params->get('page_heading');
        }

        // Get the total subscribers on the site
        $model = EB::model('Subscription');

        // Load up the subscription record for the current user.
        $subscription = EB::table('Subscriptions');

        if (!$this->my->guest) {
            $subscription->load(array('email' => $this->my->email, 'utype' => 'site'));
        }

        // Determines if this should be on blogger mode
        $bloggerMode = EBR::isBloggerMode();

        // Build the return url
        $return = base64_encode(JURI::getInstance()->toString());

        // Load the theme object
        $theme 	= EB::template();

        $theme->set('view', $view);
        $theme->set('subscription', $subscription);
        $theme->set('bloggerMode', $bloggerMode);
        $theme->set('heading', $heading);
        $theme->set('return', $return);
        $theme->set('title', $title);
        $theme->set('desc', $desc);
        $theme->set('views', $views);

        $output	= $theme->output('site/toolbar/default');

        return $output;
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
			$suffix 	= $menu->params->get('pageclass_sfx', '');
		}


		return $suffix;
	}

	/**
	 * Generate a canonical tag on the header of the page
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canonical($url)
	{
		$url = EBR::_($url, false, null, false, true);

		$this->doc->addHeadLink($this->escape($url), 'canonical');
	}

    /**
     * Retrieves the active menu
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getActiveMenu()
    {
        return $this->app->getMenu()->getActive();
    }

	/**
	 * Retrieve any queued messages from the system
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public function getMessages()
    {
    	$messages 	= EB::getMessageQueue();

    	return $messages;
    }

	/**
	 * Adds the breadcrumbs on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setPathway($title, $link ='')
	{
		// Get the pathway
		$pathway	= $this->app->getPathway();

		// set this option to true if the breadcrumb didn't show the EasyBlog root menu.
		$showRootMenuItem   = false;

		// Translate the pathway item
		$title 		= JText::_($title);
		$state		= $pathway->addItem($title, $link);

		return $state;
	}

    /**
     * Use explicitly on dashboard view only.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getDashboardToolbar()
    {
        // Get total pending blog posts
        $model = EB::model('Blogs');
        $total = $model->getTotalPending();

        $totalTeamRequest = 0;

        // Get total team requests to join team.
        if (EB::isTeamAdmin()) {
            $teamModel = EB::model('TeamBlogs');
            $totalTeamRequest   = $teamModel->getTotalRequest();
        }

        // Get the logout link
        $logoutActionLink = 'index.php?option=com_users&task=user.logout';

        // @task: Determine if the current user is a blogger or not.
        $isBlogger = EB::isSiteAdmin() || $this->acl->get('add_entry');

        // Get the logout return url
        $itemId = EB::router()->getItemid('latest');
        $logoutReturn = base64_encode(EB::_('index.php?option=com_easyblog&view=latest&Itemid=' . $itemId, false));

        // Get the current active layout
        $layout = $this->input->get('layout', '', 'cmd');

        // Get the current user
        $user = EB::user(JFactory::getUser()->id);

        // Get the template
        $theme = EB::template();

        $theme->set('current', $this->getLayout());
        $theme->set('isBlogger', $isBlogger);
        $theme->set('totalPending', $total);
        $theme->set('user', $user);
        $theme->set('logoutURL', $logoutReturn);
        $theme->set('logoutActionLink', $logoutActionLink);
        $theme->set('totalTeamRequest', $totalTeamRequest);

        $output = $theme->output('site/dashboard/toolbar/default');

        return $output;
    }

	protected function outputJSON( $output = null )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json 	= new Services_JSON();

		echo '<script type="text/json" id="ajaxResponse">' . $json->encode( $output ) . '</script>';
		exit;
	}

	/**
	 * Responsible to modify the title whenever necessary. Inherited classes should always use this method to set the title
	 */
	public function setPageTitle( $title , $pagination = null , $addSitePrefix = false )
	{
		$doc 	= JFactory::getDocument();
		$page 	= null;

		if( $addSitePrefix )
		{
			$config 	= EasyBlogHelper::getConfig();
			$title  	.= ' - ' . JText::_($config->get('main_title'));
		}

		if( $pagination && is_object( $pagination ) )
		{
			// @task: Get current page index.
			$page 		= $pagination->get( 'pages.current' );

			// @task: Append the current page if necessary.
			$title 		.= $page == 1 ? '' : ' - ' . JText::sprintf( 'COM_EASYBLOG_PAGE_NUMBER', $page );
		}

		// @task: Set the title for the page.
		$doc->setTitle( $title );
	}

    /**
     * Sets the rss author email
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getRssEmail($author)
    {
        if ($this->jconfig->get('feed_email') == 'none') {
            return;
        }

        if ($this->jconfig->get('feed_email') == 'author') {
            return $author->user->email;
        }

        return $this->jconfig->get('mailfrom');
    }
}
