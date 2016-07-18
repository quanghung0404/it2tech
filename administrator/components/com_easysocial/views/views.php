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
FD::import( 'admin:/includes/views' );

/**
 * Main admin view class.
 *
 * @since	1.0
 * @access	public
 */
abstract class EasySocialAdminView extends EasySocialView
{
	protected $page = null;

	public function __construct($config = array())
	{
		// Initialize page.
		$page = new stdClass();

		// Initialize page values.
		$page->icon = '';
		$page->iconUrl = '';
		$page->heading = '';
		$page->description = '';

		$this->page = $page;
		$this->my = FD::user();

		// Initialize the breadcrumbs
		$this->breadcrumbs	= array();

		$view = $this->getName();

		// Disallow access if user does not have sufficient permissions
		$rule = 'easysocial.access.' . $view;

		// For fields, it uses a different view
		if ($view == 'fields') {
			$rule 	= 'easysocial.access.profiles';
		}

		if (!$this->authorise($rule)) {
			$this->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		parent::__construct($config);
	}

	/**
	 * Checks for user access
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function authorise($command, $extension = 'com_easysocial')
	{
		return $this->my->authorise($command, $extension);
	}

	/**
	 * Allows caller to set the header title in the structure layout.
	 *
	 * @since	1.0
	 * @param	string	The string to appear in the headers.
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function setHeading($title)
	{
		$this->page->heading = JText::_($title);
	}

	/**
	 * Allows caller to set the header title in the structure layout.
	 *
	 * @since	1.0
	 * @param	string	The string to appear in the description area.
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function setDescription($description)
	{
		$this->page->description	= JText::_($description);
	}

	/**
	 * Allows caller to set the header icon in the structure layout.
	 *
	 * @since	1.0
	 * @param	string	The string to appear in the description area.
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function setIconUrl( $url , $rounded = true )
	{
		$this->page->iconUrl 		= $url;
		$this->page->iconRounded	= $rounded;
	}

	/**
	 * Allows caller to set the header icon in the structure layout.
	 *
	 * @since	1.0
	 * @param	string	The string to appear in the description area.
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function setIcon( $iconClass )
	{
		$this->page->icon 	= $iconClass;
	}


	/**
	 * Central method that is called by child items to display the output.
	 * All views that inherit from this class should use display to output the html codes.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The POSIX path for the theme file.
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display( $tpl = null )
	{
		$doc 		= JFactory::getDocument();
		$format		= $doc->getType();
		$tmpl  		= JRequest::getString( 'tmpl' );

		// Joomla page title should always display EasySocial
		JToolbarHelper::title( JText::_( 'COM_EASYSOCIAL_TITLE_EASYSOCIAL' ) , 'easysocial' );

		if( $format == 'html' )
		{
			// Load Joomla's framework.
			JHTML::_( 'behavior.framework' );

			$class  = '';

			if( $tmpl == 'component' )
			{
				$class 	= 'es-window';
			}

			// Main wrapper
			$class  = isset($class) ? $class : '';

			// Add the sidebar to the page obj.
			$sidebar	= $this->getSideBar();

			// Capture contents.
			ob_start();
			parent::display($tpl);
			$html 	= ob_get_contents();
			ob_end_clean();

			$version 	= FD::getLocalVersion();

			$theme		= FD::get( 'Themes' );

			$theme->set( 'version'	, $version );
			$theme->set( 'class'	, $class );
			$theme->set( 'tmpl'		, $tmpl );
			$theme->set( 'html'		, $html );
			$theme->set( 'sidebar'	, $sidebar );
			$theme->set( 'page'		, $this->page );

			$contents	= $theme->output( 'admin/structure/default');

			echo $contents;

			return;
		}

		return parent::display( $tpl );
	}

	/**
	 * Allows overriden objects to redirect the current request only when in html mode.
	 *
	 * @access	public
	 * @param	string	$uri 	The raw uri string.
	 * @param	boolean	$route	Whether or not the uri should be routed
	 */
	public function redirect( $uri , $message = '' , $class = '' )
	{
		$app = JFactory::getApplication();

	    $app->redirect( $uri , $message , $class );
	    $app->close();
	}

	/**
	 * Returns the sidebar html codes.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The html codes for the sidebar.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getSideBar()
	{
		$showSidebar 	= JRequest::getVar('sidebar', 1);
		$showSidebar 	= $showSidebar == 1 ? true : false;

		if (!$showSidebar) {
			return;
		}

		$sidebar	= FD::getInstance( 'Sidebar' );

		$view 		= JRequest::getCmd( 'view' , 'easysocial' );

		$output 	= $sidebar->render( $view );

		return $output;
	}
}
