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
	 * Restful api to share a new story
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function share()
	{
		// Validate the user first
		$userId = $this->validateAuth();

		// Get the content for the story
		$content = $this->input->get('content', '', 'default');

		// The target item to comment on
		$target  = $this->input->get('target_user', 0, 'int');

		if (!$content) {
			$this->set('code', '403');
			$this->set('message', JText::_('Please enter some contents.'));

			return parent::display();
		}

		// Load up story library
		$story 	= FD::story(SOCIAL_TYPE_USER);

		// Create the story
		$args = array('content' => $content, 'contextIds' => $target, 'contextType' => SOCIAL_TYPE_STORY, 'actorId' => $userId);

		$result = $story->create($args);

		$this->set('status', 1);

		parent::display();
	}
}
