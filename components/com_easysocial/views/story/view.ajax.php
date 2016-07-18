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

class EasySocialViewStory extends EasySocialSiteView
{
	/**
	 * Post processes after a user submits a story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create($streamItemTable = '', $clusterId = '', $clusterType = '')
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$stream = FD::stream();
		$stream->getItem($streamItemTable->uid, $clusterId, $clusterType, true);

		$output = $stream->html();

		// If app explicitly wants to hide the stream item, do not display anything here.
		if (isset($streamItemTable->hidden) && $streamItemTable->hidden) {
			$output = '';
		}

		return $this->ajax->resolve($output, $streamItemTable->uid);
	}

	/**
	 * Post processes after a user submits a simple story.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createFromModule($streamItemTable = '')
	{
		// Default message
		$message = JText::_('COM_EASYSOCIAL_NOTIFICATIONS_NEW_STORY_POSTED');

		// If we know that there is no argument, the process failed because they are not logged in.
		if (!$streamItemTable) {
			$message = JText::_('COM_EASYSOCIAL_NOTIFICATIONS_NEW_STORY_POSTED_FAILED');
			$this->ajax->script('EasySocial.login();');
			
			return $this->ajax->resolve(false, $message);
		}

		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$stream = FD::stream();
		$stream->getItem($streamItemTable->uid, '', '', true);

		$output = $stream->html();

		// If app explicitly wants to hide the stream item, do not display anything here.
		if (isset($streamItemTable->hidden) && $streamItemTable->hidden) {
			$output = '';
		}

		return $this->ajax->resolve(true, $message, $output, $streamItemTable->uid);
	}

	/**
	 * Post processes after a user submits a story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function update($streamTable = '')
	{
		// Only logged in users allowed here
		FD::requireLogin();

		$ajax 		= FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject( $this->getMessage() );
		}

		$stream 	= FD::stream();
		$stream->getItem($streamTable->uid);

		$output 	= $stream->html(false, '', array('contentOnly' => true));

		return $ajax->resolve($output, $streamTable->uid);
	}
}
