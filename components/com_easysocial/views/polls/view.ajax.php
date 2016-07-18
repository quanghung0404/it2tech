<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/views/views' );

class EasySocialViewPolls extends EasySocialSiteView
{
	/**
	 * Returns an ajax chain.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The verb that we have performed.
	 */
	public function vote()
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

        $pollId = $this->input->get('id', 0, 'int');
        $itemId  = $this->input->get('itemId', 0, 'int');
        $action  = $this->input->get('act', '', 'default');

		// $poll = FD::table('Polls');
		// $poll->load($pollId);

		// // lets get poll items
		// $items = $poll->getItems();

        $items = array();
		$msg = '';

        if ($action == 'unvote') {
            $msg = JText::_('COM_EASYSOCIAL_POLLS_VOTE_REMOVED_SUCESSFUL');
        } else if ($action == 'vote') {
            $msg = JText::_('COM_EASYSOCIAL_POLLS_VOTED_SUCESSFUL');
        }

		return $ajax->resolve($msg, $items);
	}

	public function update()
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

        $pollId = $this->input->get('id', 0, 'int');

        $pollLib = FD::get('Polls');
        $content = $pollLib->getDisplay($pollId);

		return $ajax->resolve($content);
	}


	public function voters()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax	= FD::ajax();

        $pollId = $this->input->get('id', 0, 'int');
        $pollItemId = $this->input->get('itemid', 0, 'int');

        $poll = FD::get('Polls');
        $voters = $poll->getVoters($pollId, $pollItemId);

		$theme 	= FD::themes();
		$theme->set('voters', $voters);

		$contents 	= $theme->output( 'site/polls/form.voter.list' );

		return $ajax->resolve( $contents );
	}

	public function edit()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax	= FD::ajax();

        $uid = $this->input->get('uid', 0, 'int');
        $element = $this->input->get('element', '', 'default');
        $source = $this->input->get('source', '', 'default');

        $pollLib = FD::get('Polls');
        $content = $pollLib->getForm($element, $uid, $source);

		return $ajax->resolve($content);
	}


}
