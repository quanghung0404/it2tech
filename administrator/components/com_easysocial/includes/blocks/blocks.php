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
defined('_JEXEC') or die('Unauthorized Access');

class SocialBlocks
{
	public function __construct()
	{
		$this->my 		= FD::user();
		$this->config	= FD::config();
	}

	public static function factory()
	{
		return new self();
	}

	/**
	 * Blocks a specific target item
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function block($targetId, $reason = '')
	{
		$table 	= FD::table('BlockUser');
		$table->user_id = $this->my->id;
		$table->target_id = $targetId;
		$table->reason = $reason;
		$table->created = JFactory::getDate()->toSql();

		$table->store();

		return $table;
	}

	/**
	 * Unblocks a specific target item
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unblock($targetId)
	{
		$table 	= FD::table('BlockUser');
		$table->load(array('user_id' => $this->my->id, 'target_id' => $targetId));

		$state = $table->delete();

		return $state;
	}

	/**
	 * Retrieve the form to block the user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getForm($targetId)
	{
		if ($this->my->guest) {
			return;
		}

		// Get the target object
		$user = FD::user($targetId);

		// Default form
		$file = 'form';

		// We need to know if the target was already blocked by the user
		if ($user->isBlockedBy($this->my->id)) {
			$file = 'form.unblock';
		}

		$theme = FD::themes();
		$theme->set('user', $user);
		$output = $theme->output('site/blocks/' . $file);

		return $output;
	}
}
