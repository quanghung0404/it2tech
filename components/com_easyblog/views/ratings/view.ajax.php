<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewRatings extends EasyBlogView
{
	/**
	 * Displays a list of voters
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function voters()
	{
		// Get the composite keys
		$uid  = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'cmd');

		// Get maximum number of voters to show up in the dialog.
		$limit = EB::config()->get('main_ratings_display_raters_max');

		// Get the ratings
		$model  = EB::model('Ratings');
		$votes  = $model->getRatingUsers($uid, $type, $limit);

		// Determines the total number of guest votes
		$totalGuests = 0;

		// Format the votes
		if ($votes) {
			foreach ($votes as &$vote) {

				$vote->user = false;

				if ($vote->created_by) {
					$user = EB::user($vote->created_by);
					$vote->user = $user;
				} else {
					$totalGuests += 1;
				}
			}
		}

		$theme = EB::template();
		$theme->set('totalGuests', $totalGuests);
		$theme->set('votes', $votes);
		$output = $theme->output('site/ratings/dialog.voters');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to vote on an imte
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function vote()
	{
		// Get the type of the ratings
		$type = $this->input->get('type', '', 'cmd');

		// Get the blog post object
		$id = $this->input->get('id', 0, 'int');
		$post = EB::post($id);

		if (!$post->id || !$id) {
			return $this->ajax->reject();
		}

		// If the blog post is password protected we shouldn't do anything
		if ($post->isPasswordProtected()) {
			return $this->ajax->reject();
		}

		// Load up the rating table
		$rating	= EB::table('Ratings');

		// Get the user's session
		$session = JFactory::getSession();

		// Do not allow guest to vote, or if the voter already voted.
		$exists = $rating->load(array('created_by' => $this->my->id, 'uid' => $post->id, 'type' => $type, 'sessionid' => $session->getId()));

		if ($exists || ($this->my->guest && !$this->config->get('main_ratings_guests'))) {
			return $this->ajax->reject();
		}

		// Get the rating value
		$value = $this->input->get('value', 0, 'int');

		// Set the ratings property
		$rating->created_by = $this->my->id;
		$rating->type = $type;
		$rating->uid = $post->id;
		$rating->ip = @$_SERVER['REMOTE_ADDR'];
		$rating->value = $value;
		$rating->sessionid = $session->getId();
		$rating->created = EB::date()->toSql();
		$rating->published = true;
		$rating->store();

		// Get the final rating value
		$model = EB::model('Ratings');
		$ratingValue	= $model->getRatingValues($post->id, $type);
		$total  = $ratingValue->total;
		$rating = $ratingValue->ratings;

		$message = JText::_('COM_EASYBLOG_RATINGS_RATED_THANK_YOU');

		return $this->ajax->resolve($total, $message, $rating);
	}
}
