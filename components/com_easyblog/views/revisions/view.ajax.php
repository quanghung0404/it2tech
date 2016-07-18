<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ROOT . '/views/views.php');

class EasyBlogViewRevisions extends EasyBlogView
{
	/**
	 * Given the current revision id, and the target id, display a comparison between 2 revisions
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function compare()
	{
		// Current is the current revision that is being edited
		$current = $this->input->get('current', 0, 'int');

		// Target is the comparison target
		$target = $this->input->get('target', 0, 'int');

		// Load the current revision that is being edited
		$currentRevision = EB::table('Revision');
		$currentRevision->load($current);

		// Check if the user has access to the post or not
		$post = $currentRevision->getPost();

		if (!$post->canEdit()) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_COMPARE_REVISION'));
		}

		// Revision being compared to
		$targetRevision = EB::table('Revision');
		$targetRevision->load($target);

		$theme = EB::template();
		$theme->set('current', $currentRevision);
		$theme->set('target', $targetRevision);

		$output = $theme->output('site/composer/revisions/revision');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to delete a revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteRevision()
	{
		$id = $this->input->get('id', 0, 'int');

		// Load the revision
		$revision = EB::table('Revision');
		$revision->load($id);

		if (!$revision->canDelete()) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_DELETE_REVISION'));
		}

		$theme = EB::template();
		$theme->set('revision', $revision);

		$output = $theme->output('site/composer/revisions/dialog.delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to switch post to use specific revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUseRevision()
	{
		$uid = $this->input->get('uid');

		$post = EB::post($uid);

		if (!$post->canEdit()) {
			return $this->ajax->reject(EB::exception('You are not allowed to edit this post'));
		}

		$theme = EB::template();
		$theme->set('post', $post);

		$output = $theme->output('site/composer/revisions/dialog.switchrevision');

		return $this->ajax->resolve($output);
	}

	/**
	 * Retrieves a list of revisions for the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRevisions()
	{
		$uid = $this->input->get('uid');

		// Load up the post
		$post = EB::post($uid);

		// Ensure that the user is allowed to edit and view revisions from this post
		if (!$post->canEdit()) {
			return $this->ajax->reject(EB::exception('You are not allowed to edit this post'));
		}

		$revisions = $post->getRevisions();

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('revisions', $revisions);

		$output = $theme->output('site/composer/revisions/list');

		return $this->ajax->resolve($output);
	}

	public function setAsCurrent()
	{
		$ajax = EB::ajax();

		$revisionId = $this->input->get('revisionId', null, 'int');

		// Stop if no revisionId given.
		if (is_null($revisionId)) {
			return $ajax->reject(EB::Exception('COM_EASYBLOG_REVISION_UNKNOWN_REVISION_ID'));
		}

		$revision = EB::table('Revision');
		$revision->load($revisionId);
		$state = $revision->setAsCurrent();

		if (!$state) {
			return $ajax->reject(EB::Exception('COM_EASYBLOG_REVISION_USE_REVISION_FAILED'));
		}

		return $ajax->reject(EB::Exception('COM_EASYBLOG_REVISION_USE_REVISION_SUCCESS'), EASYBLOG_MSG_SUCCESS);
	}

	public function diff()
	{
		$ajax = EB::ajax();

		$source = $this->input->get('source', null, 'int');
		$target = $this->input->get('target', null, 'int');

		$html = EB::revisions()->renderDiff($source, $target);

		if ($html instanceof EasyBlogException) {
			$ajax->reject($html);
		}

		$ajax->resolve($html);
	}
}
