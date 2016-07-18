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

class EasySocialViewThemes extends EasySocialAdminView
{
	/**
	 * Displays a list of themes on the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display( $tpl = null )
	{
		// Set page heading
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_THEMES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_THEMES');

		JToolbarHelper::custom( 'makeDefault' , 'default' , '' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_MAKE_DEFAULT' ) , false );

		// Load themes model
		$model = FD::model('Themes');
		$themes = $model->getThemes();

		$this->set('themes', $themes);

		parent::display('admin/themes/default');
	}

	public function compiler()
	{
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_THEMES_COMPILER');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_THEMES_CUSTOMIZE');

		// Master set of stylesheets
		$stylesheets = array();

		// Site themes
		$model = FD::model('Themes');
		$site_themes = $model->getThemes();

		$stylesheets[] = (object) array(
			'title'    => JText::_('COM_EASYSOCIAL_THEMES_LOCATION_SITE'),
			'location' => 'site',
			'override' => false,
			'themes'   => $site_themes
		);

		// Site overrides
		$elements = FD::stylesheet('site')->overrides();
		$site_overrides = array();

		// Emulate results from $model->getThemes();
		// TODO: Find a proper way to do this.
		foreach ($elements as $element) {
			$site_overrides[] = (object) array(
				'name'    => ucwords($element),
				'element' => $element
			);
		}

		$stylesheets[] = (object) array(
			'title'    => JText::_('COM_EASYSOCIAL_THEMES_LOCATION_SITE_OVERRIDE'),
			'location' => 'site',
			'override' => true,
			'themes'   => $site_overrides
		);

		$super = FD::config()->get('general.super');

		if ($super) {

			// Admin themes
			// TODO: Model to retrieve admin themes
			$stylesheets[] = (object) array(
				'title'    => JText::_('COM_EASYSOCIAL_THEMES_LOCATION_ADMIN'),
				'location' => 'admin',
				'override' => false,

				// Emulate results from $model->getThemes();
				// TODO: Find a proper way to do this.
				'themes'    => array((object) array('name' => 'Default', 'element' => 'default'))
			);

			// Admin overrides
			// TODO: This is strangely retrieving site overrides.
			$elements = FD::stylesheet('admin')->overrides();
			$admin_overrides = array();

			// Emulate results from $model->getThemes();
			// TODO: Find a proper way to do this.
			foreach ($elements as $element) {
				$admin_overrides[] = (object) array(
					'name'    => ucwords($element),
					'element' => $element
				);
			}

			$stylesheets[] = (object) array(
				'title'    => JText::_('COM_EASYSOCIAL_THEMES_LOCATION_ADMIN_OVERRIDE'),
				'location' => 'admin',
				'override' => false,
				'themes'   => $admin_overrides
			);
		}

		// Modules
		$elements = FD::stylesheet('module')->modules();
		$modules = array();

		// Emulate results from $model->getThemes();
		// TODO: Find a proper way to do this.
		foreach ($elements as $element) {
			$modules[] = (object) array(
				// TODO: Get actual module name
				'name'    => ucwords(str_ireplace('_', ' ', str_ireplace('mod_easysocial_', '', $element))),
				'element' => $element
			);
		}

		$stylesheets[] = (object) array(
			'title'    => JText::_('COM_EASYSOCIAL_THEMES_LOCATION_MODULE'),
			'location' => 'module',
			'override' => false,
			'themes'   => $modules
		);

		// Get url params
		// Defaults to site/wireframe.
		$location = JRequest::getCmd('location' , 'site');
		$name     = JRequest::getCmd('name'     , 'wireframe');
		$override = JRequest::getBool('override', false);

		// Get active stylesheet
		$stylesheet = FD::stylesheet($location, $name, $override);
		$uuid = uniqid();

		// For admin/themes/compiler
		$this->set('stylesheets', $stylesheets);

		// For admin/themes/compiler/form
		$this->set('uuid'      , $uuid);
		$this->set('location'  , $location);
		$this->set('name'      , $name);
		// TODO: Find a proper way to do this
		$this->set('element'   , ucwords(str_ireplace('_', ' ', str_ireplace('mod_easysocial_', '', $name))));
		$this->set('override'  , $override);
		$this->set('stylesheet', $stylesheet);
		$this->set('type'      , $stylesheet->type());
		$this->set('manifest'  , $stylesheet->manifest());

		// Also pass in server memory limit.
		$memory_limit = ini_get('memory_limit');
		$memory_limit = FD::math()->convertBytes($memory_limit) / 1024 / 1024;
		$this->set('memory_limit', $memory_limit);

		parent::display('admin/themes/compiler');
	}

	/**
	 * Displays the theme's form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form()
	{
		$element = $this->input->get('element', '', 'default');

		if(!$element) {
			$this->redirect( 'index.php?option=com_easysocial&view=themes' );
			$this->close();
		}

		JToolbarHelper::apply('apply', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE'), false, false);
		JToolbarHelper::save('save', JText::_('COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_SAVE_AND_CLOSE'));
		JToolbarHelper::cancel();
		
		$model = FD::model( 'Themes' );
		$theme = $model->getTheme($element);

		// Set the page heading
		$this->setHeading($theme->name);
		$this->setDescription($theme->desc);

		$this->set('theme', $theme);

		parent::display('admin/themes/form');
	}

	/**
	 * Displays the theme's installation page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function install()
	{
		JToolbarHelper::cancel( 'cancel' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_CANCEL' ) );

		// Set the page heading
		$this->setHeading('COM_EASYSOCIAL_TOOLBAR_TITLE_THEMES_INSTALL');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_THEMES_INSTALL');

		parent::display('admin/themes/install');
	}

	/**
	 * Upload view that sets the messge to redirect back to install page
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.1
	 * @access public
	 */
	public function upload()
	{
		FD::info()->set($this->getMessage());

		return $this->redirect('index.php?option=com_easysocial&view=themes&layout=install');
	}

	/**
	 * Make a theme as a default theme
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function makeDefault()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=themes' );
	}

	/**
	 * Post processing after a theme is stored
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store( $task , $element = null )
	{
		// Disallow access
		if( !$this->authorise( 'easysocial.access.themes' ) )
		{
			$this->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
		}

		$url 	= 'index.php?option=com_easysocial&view=themes';
		$active = JRequest::getVar( 'activeTab' );

		if( $active )
		{
			$active	= '&activeTab=' . $active;
		}

		if( $element && ($task == 'apply' && $task != 'save' ) )
		{
			$url 	= 'index.php?option=com_easysocial&view=themes&layout=form&element=' . $element . $active;
		}

		FD::info()->set( $this->getMessage() );

		$this->redirect( $url );
		$this->close();
	}
}
