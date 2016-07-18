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
 * Modules manager master display controller.
 */
class AdvancedModulesController extends JControllerLegacy
{
	/**
	 * @var      string    The default view.
	 */
	protected $default_view = 'modules';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean       $cachable  If true, the view output will be cached
	 * @param   array|boolean $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}
	 *
	 * @return  JController    This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view   = $this->input->get('view', 'modules');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		$document = JFactory::getDocument();

		// For JSON requests
		if ($document->getType() == 'json')
		{

			$view = new ModulesViewModule;

			// Get/Create the model
			if ($model = new ModulesModelModule)
			{
				// Checkin table entry
				if (!$model->checkout($id))
				{
					JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), 'error');

					return false;
				}

				// Push the model into the view (as default)
				$view->setModel($model, true);
			}

			$view->document = $document;

			return $view->display();
		}

		require_once JPATH_COMPONENT . '/helpers/modules.php';

		// Load the submenu.
		ModulesHelper::addSubmenu($this->input->get('view', 'modules'));

		return parent::display();
	}
}
