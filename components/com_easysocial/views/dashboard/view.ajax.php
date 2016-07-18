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

class EasySocialViewDashboard extends EasySocialSiteView
{
	/**
	 * Responsible to output the application contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialAppTable	The application ORM.
	 */
	public function getAppContents( $app )
	{
		// If there's an error throw it back to the caller.
		if ($this->hasErrors()) {
			return $this->ajax->reject( $this->getMessage() );
		}

		// Load the library.
		$lib = FD::apps();
		$contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'dashboard', $app, array('userId' => $this->my->id));

		// Return the contents
		return $this->ajax->resolve($contents);
	}

	/**
	 * Retrieves the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getStream($stream , $type = '', $hashtags = array())
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		// Generate RSS link for this view
		$options = array('filter' => $type);
		$id = $this->input->get('id', 0, 'default');

		if ($id) {

			if ($type == 'custom') {
				$sfilter = FD::table('StreamFilter');
				$sfilter->load($id);

				$options['filter'] = 'filter';
				$options['filterid'] = $sfilter->id . ':' . $sfilter->alias;
			} else if ($type == 'list') {
				$options['listId'] = $id;
			} else {
				$options['id'] = $id;
			}
		}

		$this->addRss(FRoute::dashboard($options, false));

		// Get the stream count
		$count = $stream->getCount();

		// Initialize the default ids
		$groupId = false;
		$eventId = false;

		// Retrieve the story lib
		$story = FD::get('Story', SOCIAL_TYPE_USER);

		// Get the tags
		if ($hashtags) {
			$hashtags = FD::makeArray($hashtags);
			$story->setHashtags($hashtags);
		}

		// If the stream is a group type, we need to set the story
		if ($type == SOCIAL_TYPE_GROUP || $type == SOCIAL_TYPE_EVENT) {
			$story = FD::get('Story', $type);

			$clusterId = $this->input->getInt('id', 0);

			$story->setCluster($clusterId, $type);
			$story->showPrivacy(false);

			// lets set the groupid / eventid accordingly
			if ($type == SOCIAL_TYPE_GROUP) {
				$groupId = $clusterId;
			}

			if ($type == SOCIAL_TYPE_EVENT) {
				$eventId = $clusterId;
			}
		}

		// Set the story to the stream
		$stream->story = $story;

		$theme = FD::themes();
		$theme->set('rssLink', $this->rssLink);
		$theme->set('eventId', $eventId);
		$theme->set('groupId', $groupId);
		$theme->set('hashtag', false);
		$theme->set('stream', $stream);
		$theme->set('story', $story);
		$theme->set('streamcount', $count);

		$contents = $theme->output('site/dashboard/feeds');

		return $this->ajax->resolve($contents, $count);
	}
}
