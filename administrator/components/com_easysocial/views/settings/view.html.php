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

class EasySocialViewSettings extends EasySocialAdminView
{
	/**
	 * Default user listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display($tpl = null)
	{
		// Disallow access
		if (!$this->authorise('easysocial.access.settings')) {
			return $this->app->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		// Set page heading
		$this->setHeading(JText::_('COM_EASYSOCIAL_TITLE_HEADING_SETTINGS'));

		// Set page icon.
		$this->setIcon('fa-cog');

		// Set page description
		$this->setDescription(JText::_('COM_EASYSOCIAL_DESCRIPTION_ACCESS'));


		$this->redirect( FRoute::_( 'index.php?option=com_easysocial&view=settings&layout=form&page=general' ) );

		$active		= JRequest::getVar( 'active' , 'general' );
		$this->set( 'active' , $active );

		return parent::display( 'admin/settings/default' );
	}

	/**
	 * Displays the settings form for the respective page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function form()
	{
		// Get the current page.
		$page = $this->input->get('page', '', 'word');

		// Ensure that the page is valid
		if (!$page) {
			return $this->redirect('index.php?option=com_easysocial');
		}

		// Add Joomla toolbar buttons
		JToolbarHelper::apply();
		JToolbarHelper::custom('export', 'export' , '' , JText::_( 'COM_EASYSOCIAL_SETTINGS_EXPORT_SETTINGS' ) , false );
		JToolbarHelper::custom('import', 'import' , '' , JText::_( 'COM_EASYSOCIAL_SETTINGS_IMPORT_SETTINGS' ) , false );
		JToolbarHelper::custom('reset', 'default' , '' , JText::_( 'COM_EASYSOCIAL_RESET_TO_FACTORY' ) , false );

		// Set the heading
		$languageString = strtoupper($page);

		$this->setHeading(JText::_('COM_EASYSOCIAL_' . $languageString . '_SETTINGS_HEADER'));
		$this->setDescription(JText::_('COM_EASYSOCIAL_' . $languageString . '_SETTINGS_HEADER_DESC'));

		// Ensure that page is in proper string format.
		$page = strtolower($page);

		// Set the page to the class for other method to access
		$this->section = $page;

		// Set the page variable.
		$this->set('page', $page);
		$this->set('settings', $this);

		echo parent::display('admin/settings/form.container');
	}

	/**
	 * Post process after settings is reset
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The page that the user is on
	 */
	public function reset( $page )
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=settings&layout=form&page=' . $page );
	}

	/**
	 * Post process after settings is imported
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The page that the user is on
	 */
	public function import( $page )
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( 'index.php?option=com_easysocial&view=settings&layout=form&page=' . $page );
	}


	/**
	 * Responsible to redirect to the appropriate page when a user clicks on the apply button.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The page name.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function apply( $page = '' )
	{
		$redirect 	= 'index.php?option=com_easysocial&view=settings&layout=form&page=' . $page;

		$info 		= FD::info();
		$info->set( $this->getMessage() );

		return $this->redirect( $redirect );
	}

	/**
	 * Enables the help button on the page.
	 *
	 * @since	1.0
	 * @param	null
	 * @return	mixed	false if help should not appear. String identifier to appear.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function help()
	{
		$layout 	= $this->getLayout();

		switch( $layout )
		{
			case 'form':
				$page 	= JRequest::getVar( 'page' );
				return 'admin/settings/help.form.' . $page;
			break;
		}

		return false;
	}

	/**
	 * Responsible to update the socialize settings for oauth authentications
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function closeOauthDialog()
	{
		echo parent::display( 'admin/settings/forms/socialize.dialog.close' );
	}

	public function getRenderTheme()
	{
		static $theme;

		if( empty( $theme ) )
		{
			$theme = FD::themes();

			$theme->set( 'settings', $this );
		}

		return $theme;
	}

	public function getSettingTheme()
	{
		static $theme;

		if( empty( $theme ) )
		{
			$theme = FD::themes();
		}

		return $theme;
	}

	public function renderTabs( $syntax )
	{
		$tabs = array();

		foreach( $syntax as $key => $content )
		{
			$tabs[$key] = array();

			$tabs[$key]['title'] = $this->renderSettingText( $key );

			$tabs[$key]['content'] = $content;

			if( is_array( $content ) )
			{
				$tabs[$key]['content'] = $this->renderPage( $content );
			}
		}

		$theme = $this->getRenderTheme();
		$theme->set( 'tabs'	, $tabs );

		return $theme->output( 'admin/settings/form.tabs' );
	}

	public function renderPage()
	{
		$args = func_get_args();
		$nums = func_num_args();

		$theme = $this->getRenderTheme();

		if( $nums > 2 )
		{
			$theme->set( 'columnSize', 12 / $nums );
		}

		$theme->set( 'columns', $args );

		return $theme->output( 'admin/settings/form.page' );
	}

	public function renderColumn()
	{
		$args = func_get_args();

		$theme = $this->getRenderTheme();

		$theme->set('column', $args);
		return $theme->output('admin/settings/form.column');
	}

	public function renderSection()
	{
		$args = func_get_args();
		$nums = func_num_args();

		$theme = $this->getRenderTheme();
		$section = $args;

		// We need to define the header
		$header = array_shift($section);

		$theme->set('header', $header);
		$theme->set('section', $section);

		return $theme->output('admin/settings/form.section');
	}

	public function renderSettingText( $text, $suffix = '' , $translate = true )
	{
		if( !$translate )
		{
			return $text . $suffix;
		}

		$text = 'COM_EASYSOCIAL_' . strtoupper( str_replace( ' ', '_', $this->section ) ) . '_SETTINGS_' . strtoupper( str_replace( ' ', '_', $text ) );

		if( !empty( $suffix ) )
		{
			$text .= '_' . strtoupper( str_replace( ' ', '_', $suffix ) );
		}

		return JText::_( $text );
	}

	/**
	 * Renders a panel heading
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function renderHeader($text = '', $description = '')
	{
		// If it is already parsed, then return as it is
		if (JString::substr($text, 0, 4) === '<h3>') {
			dump('this should not be allowed any longer');
			return $text;
		}

		$output = '<div class="panel-head">';
		$output .= '<b>' . $this->renderSettingText($text) . '</b>';

		if ($description) {
			$output .= '<p>' . $this->renderSettingText($description) . '</p>';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders a settings
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function renderSetting($text, $name, $type = 'boolean', $options = array())
	{
		$theme = $this->getRenderTheme();
		$label = $this->renderSettingText($text);

		$help 			= '';
		$unit 			= '';
		$info 			= '';
		$field			= '';
		$custom 		= '';
		$rowAttributes 	= '';

		if( is_array( $options ) )
		{
			// Check for row attributes
			if (isset($options['rowAttributes'])) {
				// Ensure that it's an array.
				$options['rowAttributes'] = ES::makeArray($options['rowAttributes']);
				$rowAttributes = implode(' ', $options['rowAttributes']);
			}

			if (isset($options['custom'])) {
				$custom = $options['custom'];
			}

			// Check for attributes
			if( !isset( $options['attributes'] ) )
			{
				$options['attributes'] = array();
			}

			// Check for class
			if( isset( $options['class'] ) )
			{
				$class = 'class="';

				if( is_array( $options['class'] ) )
				{
					$class .= implode( ' ', $options['class'] );
				}

				if( is_string( $options['class'] ) )
				{
					$class .= $options['class'];
				}

				$class .= '"';

				$options['attributes'][] = $class;

				unset($options['class']);
			}

			// Check for help in options
			if( isset( $options['help'] ) )
			{
				// If help is passed in as string, then we use it as it is
				if( is_string( $options['help'] ) )
				{
					$help = $options['help'];
				}
				// If help is passed in as boolean (true), then we parse it
				else
				{
					$popoverTitle = $this->renderSettingText( $text );
					$popoverDescription = $this->renderSettingText( $text, 'help' );

					$help = $theme->html( 'bootstrap.popover', $popoverTitle, $popoverDescription, 'top' , '' , true );
				}

				unset( $options['help'] );
			}

			if( isset( $options['unit'] ) )
			{
				// If unit is passed in as string, then we use it as it is
				if( is_string( $options['unit'] ) )
				{
					$unit = $options['unit'];
				}
				// If unit is passed in as boolean (true), then we parse it
				else
				{
					$unit = $this->renderSettingText( $text, 'unit' );
				}

				unset( $options['unit'] );
			}

			// Check for info in options
			if( isset( $options['info'] ) )
			{
				// If info is passed in as string, then we use it as it is
				if( is_string( $options['info'] ) )
				{
					$info = $options['info'];
				}
				// If info is passed in as boolean (true), then we parse it
				else
				{
					$info = $this->renderSettingText( $text, 'info' );
				}

				unset( $options['info'] );
			}

			// Translate placeholder in options
			if( isset( $options['placeholder'] ) )
			{
				if( is_bool( $options['placeholder'] ) )
				{
					$options['placeholder'] = $this->renderSettingText( $text, 'placeholder' );
				}
			}
		}

		// Check for custom type
		if( empty( $type ) || $type === 'custom' )
		{
			// If options is passed in as string, we assume that is the custom html to use
			if( is_string( $options ) )
			{
				$field = $options;
			}

			// If options is passed in as array, then we check if field exist
			if( is_array( $options ) && isset( $options['field'] ) )
			{
				$field = $options['field'];

				unset( $options['field'] );
			}
		}
		else
		{
			$renderType	= 'render' . ucfirst( $type );
			$field	= $this->$renderType($name, $options);
		}

		$theme->set('custom', $custom);
		$theme->set('rowAttributes', $rowAttributes);
		$theme->set('label', $label);
		$theme->set('help', $help);
		$theme->set('unit', $unit);
		$theme->set('info', $info);
		$theme->set('field', $field);

		return $theme->output('admin/settings/form.setting');
	}

	public function renderText($name, $options)
	{
		$text = $this->renderSettingText($name);
		
		return $options['text'];
	}

	public function renderBoolean( $name, $options = array() )
	{
		$attributes = isset( $options['attributes'] ) ? $options['attributes'] : '';

		$state	= FD::config()->get( $name );
		$theme	= $this->getSettingTheme();

		return $theme->html( 'grid.boolean', $name, $state, $name, $attributes );
	}

	public function makeOption( $text, $value = '' , $translate = true )
	{
		// If $value is empty, then use $text as the value;

		if (!empty($value) || $value === 0) {
			return array( 'text' => $this->renderSettingText( $text , '' , $translate ), 'value' => $value );
		}

		return array( 'text' => $text, 'value' => $text );
	}

	public function renderEditors( $name, $options = array() )
	{
		$theme	= $this->getSettingTheme();

		return $theme->html( 'form.editors' , $name , $options );
	}

	public function renderList( $name, $options = array() )
	{
		$selected	= FD::config()->get( $name );
		$theme		= $this->getSettingTheme();

		$values		= $options;

		// If $options['options'] exist, then there are other parameters
		// If $options['options'] does not exist, then $options is the values
		if( array_key_exists( 'options', $options ) )
		{
			$values = $options['options'];
			unset( $options['options'] );

			if( array_key_exists( 'emptyoption', $options ) && $options['emptyoption'] )
			{
				array_unshift( $values, $this->makeOption( '', JText::_( 'COM_EASYSOCIAL_SETTINGS_SELECT_AN_OPTION' ) ) );

				unset( $options['emptyoption'] );
			}

			$attributes = isset( $options['attributes'] ) ? $options['attributes'] : '';
		}

		return $theme->html( 'grid.selectlist', $name, $selected, $values, $name, $options['attributes'] );
	}

	/**
	 * Renders a text input
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function renderInput($name, $options = array())
	{
		$default = isset($options['default']) ? $options['default'] : '';
		$attributes = isset( $options['attributes'] ) ? $options['attributes'] : '';

		$value = $this->config->get($name, $default);
		$theme = $this->getSettingTheme();

		// Set the placeholder in the attributes
		if (isset($options['placeholder'])) {

			if (is_array($attributes)) {
				$attributes[] = 'placeholder="' . $options['placeholder'] . '"';
			}

			if (is_string($attributes)) {
				$attributes .= ' placeholder="' . $options['placeholder'] . '"';
			}

			unset($options['placeholder']);
		}

		return $theme->html('grid.inputbox', $name, $value , $name, $attributes);
	}

	public function renderTextarea( $name, $options = array() )
	{
		$value	= FD::config()->get( $name );
		$theme	= $this->getSettingTheme();

		if( isset($options[ 'translate' ] )  && $options[ 'translate' ] )
		{
			$value 	= JText::_( $value );
		}

		$attributes = isset( $options['attributes'] ) ? $options['attributes'] : '';

		return $theme->html( 'grid.textarea', $name, $value, $name, $attributes );
	}

	public function renderHidden( $name, $options = array() )
	{
		$value	= FD::config()->get( $name );
		$theme	= $this->getSettingTheme();

		$attributes = isset( $options['attributes'] ) ? $options['attributes'] : '';

		return $theme->html( 'grid.hidden', $name, $value, $name, $attributes );
	}

	/**
	 * Post process after the api key has been stored
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function savekey( $return = '' )
	{
		if( !empty( $return ) )
		{
			return $this->redirect( $return );
		}
	}

}
