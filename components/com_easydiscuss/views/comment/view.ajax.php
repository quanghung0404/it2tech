<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/akismet/akismet.php');

class EasyDiscussViewComment extends EasyDiscussView
{
	/**
	 * Responsible to process a comment for saving.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		ED::checkToken();
		
        $id = $this->input->get('id','','int');
        $message = $this->input->get('comments','', 'string');
        $acceptedTerms = $this->input->get('tncCheckbox','');
		
		// Load the post item.
		$post = ED::post($id);

		// check if the user is it get banned or not
		if ($post->isUserBanned()) {
        	return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_BANNED_YOU'));
		}

        if (empty($message)) {
        	return $this->ajax->reject(JText::_('COM_EASYDISCUSS_COMMENT_IS_EMPTY'));
        }

        // Check the terms and condirion if it is enabled
        if ($this->config->get('main_comment_tnc') && $acceptedTerms == 'false') {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT'));
		}
		
		// Test if a valid post id is provided.
		if (!$post->id) {
			return $this->ajax->reject( JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID'));
		}

		// Test if the user is allowed to add comment or not.
		if (!$post->canComment()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_COMMENTS_NOT_ALLOWED'));
		}

		// Load user profile's object.
		$profile = ED::user($this->my->id);

		// Build up comment object.
		$commentData = new stdClass();
		$commentData->user_id = $this->my->id;
		$commentData->name = $profile->getName();
		$commentData->email	= $this->my->email;
		$commentData->comment = $message;
		$commentData->post_id = $post->id;

		// Run through akismet screening if necessary.
		if ($this->config->get('antispam_akismet') && ($this->config->get('antispam_akismet_key'))) {
			$data = array(
					'author' => $this->my->name,
					'email' => $this->my->email,
					'website' => DISCUSS_JURIROOT,
					'body' => $commentData->comment,
					'alias' => ''
				);

			$akismet = new Akismet(DISCUSS_JURIROOT, $this->config->get('antispam_akismet_key'), $data);

			if ($akismet->isSpam()) {
				return $this->ajax->reject(JText::_('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED'));
			}
		}

		$comment = ED::table('Comment');
		$comment->bind($commentData, true);

		if (!$comment->store()) {
			return $this->ajax->reject($comment->getError());
		}

		// Get post duration.
		$durationObj = new stdClass();
		$durationObj->daydiff = 0;
		$durationObj->timediff = '00:00:01';

		$comment->duration = ED::getDurationString($durationObj);

		// Set the comment creator.
		$comment->creator = $profile;

		// Process after save operation
		$comment->postSave();

		// Get the result of the posted comment.
		$theme = ED::themes();
		$theme->set('comment', $comment);
		$contents = $theme->output('site/comments/default.item');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays a confirmation dialog to move a comment to a reply.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique post id.
	 */
	public function confirmConvert()
	{
		$id = $this->input->get('id', 0, 'int'); 
		$postId = $this->input->get('postId', 0, 'int');

		// Test if a valid post id is provided.
		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID'));
		}

		$theme = ED::themes();
		$theme->set('id', $id);
		$theme->set('postId', $postId);
		$contents = $theme->output('site/dialogs/ajax.comment.convert');
		
		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays a confirmation dialog to delete a comment.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique post id.
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');

		// Test if a valid post id is provided.
		if (!$id) {
			$this->ajax->reject( JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID'));
			return $this->ajax->send();
		}

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/comments/dialogs/delete.confirmation');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Responsible to delete a comment.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function delete()
	{
		$id = $this->input->get('id', 0, 'int');

		$comment = ED::table('Comment');
		$comment->load($id);

		$postId = $comment->post_id;

		if (!$comment->canDeleteComment()) {
			echo JText::_('COM_EASYDISCUSS_COMMENTS_NOT_ALLOWED');
			return false;
		}

		if (!$comment->delete()) {
			echo $comment->getError();
			return false;
		}

		// AUP Integrations
		ED::aup()->assign(DISCUSS_POINTS_DELETE_COMMENT, $comment->user_id, '');

		// Check if there any comment left on the post.
		$post = ED::post($postId);
		$content = $post->getComments();

		return $this->ajax->resolve($content);
	}

	/**
	 * Shows the terms and condition dialog window.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function showTnc()
	{
		$theme = ED::themes();
		$contents = $theme->output('site/comments/dialogs/comment.term');

		return $this->ajax->resolve($contents);
	}
}
