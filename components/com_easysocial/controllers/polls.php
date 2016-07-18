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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerPolls extends EasySocialController
{

	public function update()
	{
		// Check for request forgeries.
		FD::checkToken();

		// User needs to be logged in.
		FD::requireLogin();

        $pollId = $this->input->get('id', 0, 'int');
        $uid = $this->input->get('uid', 0, 'int');
        $element = $this->input->get('element', '', 'default');

		$title = $this->input->get('title', '', 'default');
        $multiple = $this->input->get('multiple', '0', 'default');
        $toberemove = $this->input->get('toberemove', '', 'default');
		$expirydate = $this->input->get('expirydate', '', 'default');

        $items  = $this->input->get('items', '', 'array');

        $my = FD::user();

		$poll = FD::table('Polls');
		$state = $poll->load($pollId);

		if (! $state) {
			// error. invalid poll id.
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_POLLS_ERROR_INVALID_POLL_ID'), SOCIAL_MSG_ERROR);
			return $this->view->call( __FUNCTION__ );
		}

		$poll->title = $title;
		$poll->multiple = $multiple;


		if ($expirydate) {
			// since we know the expirey date that pass in already has the timezone. We need to reverse it.
			$offset = ES::date()->getOffSet();
			$newDate = new JDate($expirydate, $offset);
			$expirydate = $newDate->toSql();
		}

		$poll->expiry_date = $expirydate;

		$poll->store();

		// if there are items to delete, lets do it here.
		if ($toberemove) {
			$tobeRemoved = explode(',', $toberemove);

			if ($tobeRemoved) {
				foreach($tobeRemoved as $id) {
					$pollItem = FD::table('PollsItems');
					$pollItem->delete($id);
				}
			}
		}

		// now we need to update / add new items
		if ($items) {
			foreach($items as $item) {

				$item = (object) $item;

				$pollItem = FD::table('PollsItems');
				$pollItem->load($item->id);

				$pollItem->poll_id = $pollId;
				$pollItem->value = $item->text;

				$pollItem->store();
			}
		}

		return $this->view->call( __FUNCTION__);
	}

	/**
	 * poll voting
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */

	public function vote()
	{
		// Check for request forgeries.
		FD::checkToken();

		// User needs to be logged in.
		FD::requireLogin();

        $pollId = $this->input->get('id', 0, 'int');
        $itemId  = $this->input->get('itemId', 0, 'int');
        $action  = $this->input->get('act', '', 'default');

        $my = FD::user();

		$poll = FD::table('Polls');
		$state = $poll->load($pollId);

		if (! $state) {
			// error. invalid poll id.
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_POLLS_ERROR_INVALID_POLL_ID'), SOCIAL_MSG_ERROR);
			return $this->view->call( __FUNCTION__ );
		}

		// make sure the user did not vote before.
		// $isVoted = $poll->isVoted($my->id);

		// if ($isVoted) {
		// 	// error. invalid poll id.
  //           $this->view->setMessage(JText::_('COM_EASYSOCIAL_POLLS_ERROR_VOTED_BEFORE'), SOCIAL_MSG_ERROR);
		// 	return $this->view->call( __FUNCTION__ );
		// }

		$pollLib = FD::get('Polls');

		if ($action == 'vote') {
			$pollLib->vote($pollId, $itemId, $my->id);
		} else if ($action == 'unvote') {
			$pollLib->unvote($pollId, $itemId, $my->id);
		}

		return $this->view->call( __FUNCTION__);
	}

}
