<?php
/**
 * @version     $Id: layout.raw.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'libraries/joomlashine/layout.php';

/**
 * Layout controllers of JControllerAdmin
 * 
 * @package     Controllers
 * @subpackage  Layout
 * @since       1.6
 */
class JSNUniformControllerLayout extends JControllerAdmin
{
	protected $option = JSN_UNIFORM;

	/**
	 *  load layout form
	 * 
	 * @return html layout
	 */
	public function load()
	{
		$name = JFactory::getApplication()->input->getVar('name', null);
		if (empty($name))
		{
			JError::raiseError(500, 'Invalid layout name');
			return;
		}

		$layout = new JSNUniformLayout(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets/layouts/');
		echo $layout->load($name);
	}
}
