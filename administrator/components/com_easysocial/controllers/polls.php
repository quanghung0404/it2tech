`<?php
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

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerPolls extends EasySocialController
{

	/**
	 * Deletes polls
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view = $this->getCurrentView();

		// Get the id from the request
		$ids = JRequest::getVar('cid');

		// If the user is deleting with the checkbox, find similar reports
		$model = FD::model('Polls');

		foreach ($ids as $id) {
			$tmpPoll = FD::table('Polls');
			$tmpPoll->load($id);

			$userId = $tmpPoll->created_by;

			$state = $tmpPoll->delete();

			if ($state) {
				// Load all related reports
				$pollItems 	= $model->getItems($tmpPoll->id);

				// Delete the poll items for this poll
				foreach ( $pollItems as $pollItem) {
					$tmpPollItem = FD::table('PollsItems');
					$tmpPollItem->load($pollItem->id);

					$tmpPollItem->delete();
				}


				//remove points
				if ($userId) {
					ES::points()->assign('polls.remove', 'com_easysocial', $userId);
				}

			}
		}

		$view->setMessage(JText::_('COM_EASYSOCIAL_POLLS_POLL_ITEM_HAS_BEEN_DELETED'), SOCIAL_MSG_SUCCESS);

		return $view->call( __FUNCTION__ );
	}

}
