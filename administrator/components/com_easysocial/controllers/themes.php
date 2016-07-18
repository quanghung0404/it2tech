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

// Include main controller here.
FD::import( 'admin:/controllers/controller' );

class EasySocialControllerThemes extends EasySocialController
{
	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'toggleDefault' , 'makeDefault' );
		$this->registerTask( 'apply' , 'store' );
		$this->registerTask( 'save' , 'store' );
	}

	/**
	 * Set's the template as the default template
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function makeDefault()
	{
		// Check for request forgeries
		FD::checkToken();

		$element 	= JRequest::getVar( 'cid' );
		$element 	= $element[ 0 ];
		$element 	= strtolower( $element );

		// Get the current view
		$view 		= $this->getCurrentView();

		// Get the configuration object
		$configTable	= FD::table( 'Config' );
		$config 		= FD::registry();

		if( $configTable->load( 'site' ) )
		{
			$config->load( $configTable->value );
		}

		// Convert the config object to a json string.
		$config->set( 'theme.site' , $element );

		// Convert the configuration to string
		$jsonString 		= $config->toString();

		// Store the setting
		$configTable->value	= $jsonString;

		if( !$configTable->store() )
		{
			$view->setMessage( $configTable->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Stores the theme parameter
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store()
	{
		// Check for request forgeries
		FD::checkToken();

		// @TODO: Check if the user has privilege to access this section.

		// Get the element from the query
		$element 	= JRequest::getWord( 'element' , '' );

		// Get the current view
		$view 		= $this->getCurrentView();

		if( !$element )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_THEMES_INVALID_ELEMENT_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $this->getTask() );
		}

		// Load the model
		$model 		= FD::model( 'Themes' );

		// Format through all the properties that we want to save here.
		$data		= JRequest::get( 'post' );

		// Remove unwanted stuffs from the post data.
		unset( $data[ FD::token() ] );
		unset( $data[ 'option' ] );
		unset( $data[ 'controller' ] );
		unset( $data[ 'task' ] );
		unset( $data[ 'element' ] );

		$state 	= $model->update( $element , $data );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $this->getTask() , $element );
		}

		$view->setMessage(JText::sprintf('COM_EASYSOCIAL_THEMES_SETTINGS_SAVED_SUCCESS', $element), SOCIAL_MSG_SUCCESS);

		return $view->call( __FUNCTION__ , $this->getTask() , $element );
	}


	/**
	 * Installs a new theme on the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function upload()
	{
		// Get the file from the server.
		$file = $this->input->files->get('package', '');

		// Allowed extensions for file name.
		$allowedExtension = array('zip'); 

		// There could be possibility the server reject the file upload
		if (empty($file['tmp_name'])) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_INSTALL_UPLOAD_ERROR_INVALID_TYPE'), SOCIAL_MSG_ERROR);
			$this->view->call(__FUNCTION__);
			return false;
		}

		// We just ensure that the mime is a zip file
		if ($file['type'] !== 'application/zip') {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_INSTALL_UPLOAD_ERROR_INVALID_TYPE'), SOCIAL_MSG_ERROR);
			$this->view->call(__FUNCTION__);
			return false;
		}

		// Get information about the file that was uploaded
		$extension = pathinfo($file['name'], PATHINFO_EXTENSION);

		// Double check to ensure that the file name really contains .zip_close(zip)
		if (!in_array($extension, $allowedExtension)) {
			$this->view->setMessage(JText::_('COM_EASYSOCIAL_INSTALL_UPLOAD_ERROR_INVALID_TYPE' ), SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Get the themes model
		$model = FD::model('Themes');
		$model->install($file);

		$this->view->setMessage( JText::_( 'COM_EASYSOCIAL_INSTALL_UPLOAD_SUCCESSFULLY', SOCIAL_MSG_SUCCESS));
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Returns the template file when loading through mvc.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getAjaxTemplate()
	{
		$templateFiles		= JRequest::getVar( 'names' );

		// Ensure the integrity of each items submitted to be an array.
		$templateFiles		= FD::makeArray( $templateFiles );

		$result		= array();

		foreach( $templateFiles as $path )
		{
			$theme = FD::get( 'Themes' );
			$theme->extension = 'ejs';
			$output = $theme->output($path);

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $output;

			$result[]		= $obj;
		}

		if( !$result )
		{
			header('HTTP/1.1 404 Not Found');
			exit;
		}

		header('Content-type: text/x-json; UTF-8');

		$json 	= FD::json();
		echo $json->encode( $result );
		exit;
	}

	public function compile()
	{
		$location = JRequest::getCmd('location');
		$name     = JRequest::getCmd('name');
		$override = JRequest::getBool('override', false);
		$section  = JRequest::getVar('section');
		$force    = JRequest::getBool('force');

		$view = $this->getCurrentView();

		$stylesheet = FD::stylesheet($location, $name, $override);

		$task = $stylesheet->compile($section, array(
			'force' => $force
		));

		return $view->call(__FUNCTION__, $section, $stylesheet, $task);
	}

	public function minify()
	{
		$location = JRequest::getCmd('location');
		$name     = JRequest::getCmd('name');
		$override = JRequest::getBool('override', false);
		$section  = JRequest::getVar('section');

		$view = $this->getCurrentView();

		$stylesheet = FD::stylesheet($location, $name, $override);

		$task = $stylesheet->minify($section);

		return $view->call(__FUNCTION__, $section, $stylesheet, $task);
	}

	public function build()
	{
		$section  = JRequest::getVar('section');
		$location = JRequest::getCmd('location');
		$name     = JRequest::getCmd('name');
		$override = JRequest::getBool('override', false);
		$preset   = JRequest::getCmd('preset');

		$view = $this->getCurrentView();

		$stylesheet = FD::stylesheet($location, $name, $override);

		$task = $stylesheet->build($preset);

		return $view->call(__FUNCTION__, $stylesheet, $task);
	}

	public function purge()
	{
		$location = JRequest::getCmd('location');
		$name     = JRequest::getCmd('name');
		$override = JRequest::getBool('override', false);

		$view = $this->getCurrentView();

		$stylesheet = FD::stylesheet($location, $name, $override);

		$task = $stylesheet->purge();

		return $view->call(__FUNCTION__, $task);
	}
}
