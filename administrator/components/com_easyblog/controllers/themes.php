<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerThemes extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'apply' , 'save' );
		$this->registerTask( 'addTheme' , 'addTheme' );
		$this->registerTask( 'removeSetting' , 'removeSetting' );
	}

	/**
	 * Installs a new theme on the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function upload()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the file from the server.
		// Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
		$file 	= $this->input->files->get('package', '', 'raw');

		// Get themes model
		$model	= EB::model('Themes');
		$state 	= $model->install($file);

		$link = 'index.php?option=com_easyblog&view=themes';

		if (!$state) {
			EB::info()->set($model->getError(), 'error');
			$link = 'index.php?option=com_easyblog&view=themes&layout=install';
		} else {
			EB::info()->set(JText::_('COM_EASYBLOG_THEME_INSTALLED_SUCCESS'), 'success');
		}

		$this->app->redirect($link);
	}

	/**
	 * Allows caller to recompile the template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function recompile()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the theme to recompile
		$theme = $this->input->get('cid', '', 'array');
		$theme = isset($theme[0]) ? $theme[0] : '';


		// Recompile the theme
        // @since 4.0
        // Attach the theme's css
        $stylesheet = EB::stylesheet('site', $theme);
        $result = $stylesheet->build();

        $this->info->set(JText::sprintf('COM_EASYBLOG_THEME_COMPILED_SUCCESS', $theme), 'success');

        $this->app->redirect('index.php?option=com_easyblog&view=themes');
	}

	/**
	 * Make the provided theme a default theme for EasyBlog
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setDefault()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('theme');

		$element = $this->input->get('cid', '', 'array');
		$element = $element[0];

		if (!$element || !isset($element[0])) {

			EB::info()->set(JText::_('COM_EASYBLOG_THEME_INVALID_THEME_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=themes');
		}

		// Legacy codes and should be removed soon
		$this->config->set('layout_theme', $element);

		// Get the configuration object
		$this->config->set('theme_site', $element);

		$table 	= EB::table('Configs');
		$table->load('config');

		$table->params 	= $this->config->toString('INI');
		$table->store();

		// Clear the component's cache
		$cache = JFactory::getCache('com_easyblog');
		$cache->clean();

		EB::info()->set(JText::sprintf('COM_EASYBLOG_THEME_SET_AS_DEFAULT', $element), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=themes');
	}


	public function updateBlogImage( $element, $exclude = null )
	{
		// Get the table values
		$themePosition 	= JRequest::getVar( 'themePosition' );
		$themeWidth 	= JRequest::getVar( 'themeWidth' );
		$themeHeight	= JRequest::getVar( 'themeHeight' );
		$themeMethod 	= JRequest::getVar( 'themeMethod' );

		$themes = array();

		for( $i=0; $i<count($themePosition); $i++ )
		{
			$obj = new stdClass();
			$obj->name = $themePosition[$i];
			$obj->width = $themeWidth[$i];
			$obj->height = $themeHeight[$i];
			$obj->resize = $themeMethod[$i];

			$themes[] = $obj;
		}

		// Let's update the blog image.
		$blogImageFile	=	EBLOG_THEMES . DIRECTORY_SEPARATOR . $element . DIRECTORY_SEPARATOR . 'image.ini';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $blogImageFile ) )
		{
			return false;
		}

		$contents 	= JFile::read( $blogImageFile );

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

		$json 		= new Services_JSON();
		$types		= $json->decode( $contents );
		$modified	= false;

		foreach( $types as $i => $type )
		{
			$type->name = $themes[$i]->name;
			$type->width = $themes[$i]->width;
			$type->height = $themes[$i]->height;
			$type->resize = $themes[$i]->resize;
			$type->visible = (empty($type->visible) ? '' : $type->visible);

			$modified	= true;
		}

		if( $modified )
		{
			// Empty name field will be removed
			$myFinalResults = array();

			if( !empty($exclude) )
			{
				foreach ($types as $type)
				{
					// if( !empty($type->name) )
					// {
					// 	// If the pending item have value, replace the existing record
					// 	$myFinalResults[] = $type;
					// }

					if( !in_array($type->name, $exclude) )
					{
						$myFinalResults[] = $type;
					}
				}

				$types = $myFinalResults;
			}

			// Now let's save this
			$contents	= $json->encode( $types );
			$state 		= JFile::write( $blogImageFile , $contents );

			if( !empty($exclude) )
			{
				// User perfrom deletion
				$total 		= count( $exclude );
				$message	= JText::sprintf( 'COM_EASYBLOG_THEME_REMOVED' , $total );
				$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element , $message );
				return;
			}

			if( $state )
			{
				$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_SUCCESS_NEW_THEME_SETTING'), 'info' );
			}
			else
			{
				$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_FAIL_NEW_THEME_SETTING'), 'error' );
			}
		}
	}

	public function addTheme()
	{
		// Get the new added values
		$newThemePosition 	= JRequest::getVar( 'newThemePosition' );
		$newThemeWidth 		= JRequest::getVar( 'newThemeWidth' );
		$newThemeHeight		= JRequest::getVar( 'newThemeHeight' );
		$newThemeMethod 	= JRequest::getVar( 'newThemeMethod' );
		$element 			= JRequest::getVar( 'element' );

		// Let's update the blog image.
		$blogImageFile	=	EBLOG_THEMES . DIRECTORY_SEPARATOR . $element . DIRECTORY_SEPARATOR . 'image.ini';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $blogImageFile ) )
		{
			return false;
		}

		$contents 	= JFile::read( $blogImageFile );

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

		$json 		= new Services_JSON();
		$types		= $json->decode( $contents );
		$modified	= false;

		$obj = new stdClass();
		$obj->name = $newThemePosition;
		$obj->width = $newThemeWidth;
		$obj->height = $newThemeHeight;
		$obj->resize = $newThemeMethod;

		// Append below the list
		$types[] = $obj;

		// Now let's save this
		$contents	= $json->encode( $types );
		$state 		= JFile::write( $blogImageFile , $contents );

		if( $state )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_SUCCESS_NEW_THEME_SETTING'), 'info' );
		}
		else
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_FAIL_NEW_THEME_SETTING'), 'error' );
		}
	}


	public function getAjaxTemplate()
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$files	= JRequest::getVar( 'names' , '' );

		if (empty($files)) {
			return false;
		}

		// Ensure the integrity of each items submitted to be an array.
		if (!is_array($files)) {
			$files	= array( $files );
		}

		$result		= array();

		$template 	= EB::template();

		foreach ($files as $file) {

			$dashboard = explode( '/' , $file );

			if ($dashboard[0]=="dashboard") {
				$out		= $template->output('site/dashboard/'.$dashboard[1] . '.ejs' );

			} elseif ($dashboard[0]=="media") {
				$out		= $template->output( 'site/media/' . $dashboard[1] . '.ejs' );

			} else {
				$out		= $template->output( 'site/' . $file . '.ejs' );

			}

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $out;

			$result[]		= $obj;
		}

		header('Content-type: text/x-json; UTF-8');
		$json	 		= new Services_JSON();
		echo $json->encode( $result );
		exit;
	}
}
