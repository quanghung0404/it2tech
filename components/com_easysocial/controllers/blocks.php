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

// Import parent controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerBlocks extends EasySocialController
{
	/**
	 * Blocks a user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that the current user is logged in
		FD::requireLogin();

		// Get the current view
		$view = $this->getCurrentView();

		// Get the target id
		$target = $this->input->get('target', 0, 'int');
		$reason = $this->input->get('reason', '', 'default');

		if (!$target) {
			$view->setError(JText::_('COM_EASYSOCIAL_INVALID_USER_ID_PROVIDED'));
			return $view->call(__FUNCTION__, $target);
		}

		// Load up the block library
		$lib = FD::blocks();
		$lib->block($target, $reason);


		return $view->call(__FUNCTION__, $target);
	}

	/**
	 * Unblock a user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unblock()
	{
		// Check for request forgeries
		FD::checkToken();

		// Ensure that the current user is logged in
		FD::requireLogin();

		// Get the current view
		$view = $this->getCurrentView();

		// Get the target id
		$target = $this->input->get('target', 0, 'int');

		if (!$target) {
			$view->setError(JText::_('COM_EASYSOCIAL_INVALID_USER_ID_PROVIDED'));
			return $view->call(__FUNCTION__, $target);
		}

		// Load up the block library
		$lib = FD::blocks();
		$lib->unblock($target);

		return $view->call(__FUNCTION__, $target);
	}
}
