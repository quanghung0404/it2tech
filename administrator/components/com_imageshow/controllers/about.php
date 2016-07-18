<?php
/**
 * @version    $Id: about.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * Controller About Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class ImageShowControllerAbout extends JControllerLegacy
{

	/**
	 * Contructor
	 *
	 * @param   array  $config  a array of config items
	 */

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  A JController object to support chaining
	 */

	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'about');
		parent::display();
	}
}
