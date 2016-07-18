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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewBlocks extends EasySocialSiteView
{
	/**
	 * Dialog to confirm a report.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmBlock()
	{
		// Determines if this feature is allowed
		if (!$this->config->get('users.blocking.enabled')) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_BLOCKING_FEATURE_IS_NOT_ENABLED'));
		}

		// Get the target id
		$targetId = $this->input->get('target', 0, 'int');

		if (!$targetId) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_INVALID_USER_ID_PROVIDED'));
		}

		// Get the target user
		$target = FD::user($targetId);
		$theme  = FD::themes();

		$theme->set('target', $target);

		$output = $theme->output('site/blocks/dialog.form');

		return $this->ajax->resolve($output);
	}

	/**
	 * Dialog to confirm a report.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmUnblock()
	{
		// Determines if this feature is allowed
		if (!$this->config->get('users.blocking.enabled')) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_BLOCKING_FEATURE_IS_NOT_ENABLED'));
		}

		// Get the target id
		$targetId = $this->input->get('target', 0, 'int');

		if (!$targetId) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_INVALID_USER_ID_PROVIDED'));
		}

		// Get the target user
		$target = FD::user($targetId);
		$theme  = FD::themes();

		$theme->set('target', $target);

		$output = $theme->output('site/blocks/dialog.unblock');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after an unblock occurs
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unblock($target = null)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$targetUser = FD::user($target);

		$theme = FD::themes();
		$theme->set('user', $targetUser);
		$output = $theme->output('site/blocks/dialog.unblocked');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after a block occurs
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($target = null)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$targetUser = FD::user($target);

		$theme = FD::themes();
		$theme->set('user', $targetUser);
		$output = $theme->output('site/blocks/dialog.blocked');

		return $this->ajax->resolve($output);
	}
}
