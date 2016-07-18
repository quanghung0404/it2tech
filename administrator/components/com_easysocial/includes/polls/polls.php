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

// Include stream data template here.
require_once( dirname( __FILE__ ) . '/template.php' );

class SocialPolls
{
	/**
	 * Class constructor.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct()
	{
		//do nothing.
	}

	public static function factory()
	{
		return new self();
	}

	/**
	 * get html for polls creation form.
	 *
	 * @since	1.4
	 * @access	public
	 * @param
	 * @return  string
	 */
	public function getForm($element, $uid = 0, $source = '', $cluster_id = '')
	{
		$theme = FD::get('Themes');

		$theme->set('element', $element);
		$theme->set('uid', $uid);
		$theme->set('source', $source);
		$theme->set('cluster_id', $cluster_id);

		$mode = '';

		if ($element && $uid) {
			$poll = FD::table('Polls');
			$state = $poll->load(array('element' => $element, 'uid' => $uid));

			if ($state) {
				$mode = '.edit';

				$pollItems = $poll->getItems();

				$theme->set('poll', $poll);
				$theme->set('items', $pollItems);
			}
		}

		$output = $theme->output('site/polls/form' . $mode);

		return $output;
	}

	/**
	 * get html for polls voting form.
	 *
	 * @since	1.4
	 * @access	public
	 * @param
	 * @return  string
	 */
	public function getDisplay($pollId)
	{

		$poll = FD::table('Polls');
		$state = $poll->load($pollId);

		if (! $state) {
			return false;
		}

		$my = ES::user();
		$privacy = $my->getPrivacy();

		$isVoted = $poll->isVoted($my->id);

		$isExpired = false;
		$showResult = false;
		$canVote = false;
		$canEdit = ($my->id == $poll->created_by || $my->isSiteAdmin()) ? true : false;

		if ($privacy->validate('polls.vote', $poll->created_by, SOCIAL_TYPE_USER) ) {
			$canVote = true;
		}

		// check if user has the access to vote on polls or not.
		if ($canVote) {
			$access = $my->getAccess();
			if (! $access->allowed('polls.vote')) {
				$canVote = false;
			}
		}

		if ($poll->expiry_date && $poll->expiry_date != '0000-00-00 00:00:00') {
			// lets check if this poll already expired or not.
			$curDateTime = ES::date()->toSql();

			if ($curDateTime >= $poll->expiry_date) {
				$canVote = false;
				$isExpired = true;
			}
		}

		if ($isVoted || !$canVote) {
			$showResult = true;
		}

		$pollItems = $poll->getItems();

		$theme = FD::get('Themes');

		$theme->set('poll', $poll);
		$theme->set('items', $pollItems);
		$theme->set('isvoted', $isVoted);
		$theme->set('canvote', $canVote);
		$theme->set('canedit', $canEdit);
		$theme->set('showResult', $showResult);
		$theme->set('isExpired', $isExpired);

		$output = $theme->output('site/polls/form.vote');

		return $output;
	}

	/**
	 * get html for polls voting form.
	 *
	 * @since	1.4
	 * @access	public
	 * @param
	 * @return  array of user objects
	 */
	public function getVoters($pollId, $pollItemId = '')
	{
		$model = FD::model("Polls");

		$ids = $model->getVoterIds($pollId, $pollItemId);
		$voters = array();

		if ($ids) {

			// pre-load users
			FD::user($voters);

			foreach($ids as $id) {
				$user = FD::user($id);
				$voters[] = $user;
			}
		}

		return $voters;
	}


	/**
	 * get poll template for creation.
	 *
	 * @since	1.4
	 * @access	public
	 * @param
	 * @return  SocialPollsTemplate
	 */
	public function getTemplate()
	{
		$template 	= new SocialPollsTemplate();
		return $template;
	}

	/**
	 * create polls.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	SocialPollsTemplate
	 *        	array()
	 * @return  SocialTablePolls
	 */
	public function create(SocialPollsTemplate $template, $options = array()) {

		$my = FD::user();
		$poll = FD::table('Polls');

		// check if user has the access to create polls or not.
		$access = $my->getAccess();
		if (! $access->allowed('polls.create')) {
			return false;
		}

		$poll->element = $template->element;
		$poll->uid = $template->uid;
		$poll->title = $template->title;
		$poll->multiple = $template->multiple;
		$poll->locked = $template->locked;
		$poll->created = $template->created;
		$poll->created_by = empty($template->created_by) ? $my->id : $template->created_by;
		$poll->expiry_date = $template->expiry_date;
		$poll->cluster_id = $template->cluster_id;

		$state = $poll->store();

		if (! $state) {
			return false;
		}

		// now we need to add options for the poll
		if ($template->items) {
			foreach($template->items as $itemOption) {
				$pollItem = FD::table('PollsItems');
				$pollItem->poll_id = $poll->id;
				$pollItem->value = $itemOption->text;
				$pollItem->count = 0;

				$pollItem->store();
			}
		}

		ES::points()->assign('polls.add', 'com_easysocial', $my->id);

		return $poll;
	}

	public function vote($pollId, $optionId, $userId)
	{

		// check if user has the access to vote on polls or not.
		$my = ES::user();
		$access = $my->getAccess();
		if (! $access->allowed('polls.vote')) {
			return false;
		}


		$pollItem = FD::table('PollsItems');
		$state = $pollItem->load($optionId);

		if ($state) {
			$pollItem->count = (int) $pollItem->count + 1;
			$ok = $pollItem->store();

			if ($ok) {
				// add into polls_users table if not exists
				$pollUser = FD::table('PollsUsers');
				$voted = $pollUser->load(array('poll_id' => $pollId, 'poll_itemid' => $optionId, 'user_id' => $userId));

				if (! $voted) {
					$pollUser->poll_id = $pollId;
					$pollUser->poll_itemid = $optionId;
					$pollUser->user_id = $userId;

					$pollUser->store();
				}

				ES::points()->assign('polls.vote', 'com_easysocial', $userId);

			} else {
				return false;
			}
		} else {
			return false;
		}

		return true;
	}

	public function unvote($pollId, $optionId, $userId)
	{
		// check if user has the access to vote on polls or not.
		$my = ES::user();
		$access = $my->getAccess();
		if (! $access->allowed('polls.vote')) {
			return false;
		}



		$pollItem = FD::table('PollsItems');
		$state = $pollItem->load($optionId);

		if ($state) {
			$pollItem->count = (int) $pollItem->count - 1;

			if ($pollItem->count < 0) {
				// we do not allow negative value
				$pollItem->count = 0;
			}

			$ok = $pollItem->store();

			if ($ok) {
				// delete from pollsuser table
				$pollUser = FD::table('PollsUsers');
				$voted = $pollUser->load(array('poll_id' => $pollId, 'poll_itemid' => $optionId, 'user_id' => $userId));

				if ($voted) {
					$pollUser->delete();
				}

				ES::points()->assign('polls.unvote', 'com_easysocial', $userId);

			} else {
				return false;
			}
		} else {
			return false;
		}

		return true;
	}


	// public function updateContext($id, $uid, $element)
	// {
	// 	$poll = FD::table('Polls');
	// 	$poll->load($id);
	// }

}
