<?php

/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla library
jimport('joomla.application.component.controllerForm');

/**
 * Edit controller.
 *
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JSNMobilizeControllerEdit extends JSNBaseController
{

	/**
	 * Method for display page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Get config parameters
		$config = JSNConfigHelper::get();

		// Check if JSN Mobilize is configured correctly
		if ($config->get('link_mobile') == 'm.domain.tld' OR $config->get('link_tablet') == 'tablet.domain.tld')
		{
			// Redirect to configuration page
			return JFactory::getApplication()->redirect(JRoute::_('index.php?option=' . JFactory::getApplication()->input->getCmd('option')));
		}
		else
		{
			// Call parent method
			parent::display($cachable, $urlparams);
		}
	}

	/**
	 * Save user customization for mobile/tablet UI.
	 *
	 * @return  void
	 */
	public function mobilize()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$post = JRequest::get('post');

		// Get the model.
		$model = $this->getModel('edit');

		// Remove the items.
		if ($model->saveMobilize($post))
		{
			$this->setMessage(JText::plural("JLIB_APPLICATION_SAVE_SUCCESS"));
		}
		else
		{
			$this->setMessage($model->getError());
		}

		$this->setRedirect(JRoute::_('index.php?option=com_mobilize&view=edit', false));
	}

}
