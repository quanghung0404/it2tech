<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Forms
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of Forms.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNMobilizeViewMenus extends JSNBaseView
{

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	function display($tpl = null)
	{
		JSession::checkToken('get') or jexit('Invalid Token');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		// Get config
		$config = JSNConfigHelper::get();

		// Display the view
		parent::display($tpl);

		// Load assets
		JSNMobilizeHelper::loadAssets();
//		$this->addAssets();

	}

	/**
	 * Load assets.
	 *
	 * @return void
	 */
	protected function addAssets()
	{

	}
}
