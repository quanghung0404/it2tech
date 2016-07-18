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

class EasySocialControllerHashTags extends EasySocialController
{
	/**
	 * Suggests a list of hash tags to the user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function suggest()
	{
		// Check for valid tokens.
		FD::checkToken();

		// Only valid registered user has friends.
		FD::requireLogin();

		// Get current logged in user
		$my = FD::user();

		// Load the view.
		$view = $this->getCurrentView();

		// Properties
		$search = $this->input->get('search', '', 'default');

		// Get the stream model
		$model = FD::model('Hashtags');

		// Try to get the search result
		$result = $model->search($search);

		return $view->call(__FUNCTION__, $result);
	}
}
