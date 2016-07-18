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

jimport( 'joomla.application.component.controller' );

$version	= FD::getInstance( 'Version' )->getVersion();

if( $version >= '3.0' )
{
	class EasySocialParentController extends JControllerAdmin
	{
	}
}
else
{
	class EasySocialParentController extends JController
	{
	}
}


class EasySocialControllerMain extends EasySocialParentController
{
	protected $view = null;

	public function __construct()
	{
		$this->my = FD::user();
		$this->app = JFactory::getApplication();
		$this->config = FD::config();
		$this->doc = JFactory::getDocument();

		// Set the current view automatically for the sub controllers
		$this->view = $this->getCurrentView();

		parent::__construct();

		// Input needs to be overridden later because the parent controller is already assigning the input variable
		$this->input = FD::request();
	}

	/**
	 * Allows caller to get the current view.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCurrentView()
	{
		$className 	= get_class( $this );

		// Remove the EasySocialController portion from it.
		$className 	= str_ireplace( 'EasySocialController' , '' , $className );

		$backend 	= $this->location == 'backend' ? true : false;

		$view 		= FD::view( $className , $backend );

		return $view;
	}
}
