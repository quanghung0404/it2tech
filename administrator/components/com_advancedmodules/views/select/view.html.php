<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO-revPRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Modules component
 */
class AdvancedModulesViewSelect extends JViewLegacy
{
	protected $state;

	protected $items;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$state = $this->get('State');
		$items = $this->get('Items');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->state = &$state;
		$this->items = &$items;

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		// Add page title
		JToolbarHelper::title(JText::_('COM_MODULES_MANAGER_MODULES'), 'advancedmodulemanager icon-nonumber');

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.cancelselect');

		$bar->appendButton('Custom', $layout->render(array()), 'new');
	}
}
