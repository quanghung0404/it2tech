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

$version	= FD::getInstance( 'Version' );

if ($version->getVersion() >= '3.0') {
	class EasySocialParentController extends JControllerLegacy { }
} else {
	
	jimport('joomla.application.component.controller');
	
	class EasySocialParentController extends JController
	{
		public function __construct()
		{
			$this->input = JFactory::getApplication()->input;
			parent::__construct();
		}
	}
}


class EasySocialControllerMain extends EasySocialParentController
{
	protected $view = null;

	public function __construct()
	{
		// Set the current view automatically for the sub controllers
		$this->view = $this->getCurrentView();

		parent::__construct();
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
		$className 	= str_ireplace('EasySocialController', '' , $className );

		$backend 	= $this->location == 'backend' ? true : false;

		// Get the view
		$view = FD::view($className, $backend);

		return $view;
	}

	/**
	 * Allows caller to verify that the user is logged in
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function requireLogin()
	{
		return ES::requireLogin();
	}

	/**
	 * Checks for token existance
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function checkToken()
	{
		return ES::checkToken();
	}
}
