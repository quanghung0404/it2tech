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

FD::import( 'site:/views/views' );

class EasySocialViewComments extends EasySocialSiteView
{
	/**
	 * Restful api to submit a new comment on the site
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function post()
	{
		// Get the user's id by validation
		$userId = $this->validateAuth();

		$uid 	 = $this->input->get('uid', 0, 'int');
		$element = $this->input->get('element', '', 'string');
		$verb    = $this->input->get('verb', '', 'cmd');
		$group   = $this->input->get('group', '', 'cmd');
		$streamId = $this->input->get('stream_id', 0, 'int');

		// Determines the comments parent
		$parent = $this->input->get('parent', 0, 'int');

		// Comment data
		$content = $this->input->get('content', '', 'default');

		// Content cannot be empty
		if (empty($content)) {
			$this->set('code', 403);
			$this->set('message', JText::_('Please enter some contents for the comment.'));

			return parent::display();
		}

		// Format the element
		$element = $element . '.' . $group . '.' . $verb;

		// Get the table object
		$table = FD::table('Comments');

		$table->element = $element;
		$table->uid = $uid;
		$table->comment = $content;
		$table->created_by = $userId;
		$table->created = FD::date()->toSql();
		$table->parent = $parent;
		$table->stream_id = $streamId;

		$state		= $table->store();

		$this->set('status', 1);

		parent::display();
	}

	/**
	 * Restful api to retrieve a list of comments
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getComments()
	{
		// Get the user's id by validating
		$userId = $this->validateAuth();


		$uid 	 = $this->input->get('uid', 0, 'int');
		$element = $this->input->get('element', '', 'string');
		$verb    = $this->input->get('verb', '', 'cmd');
		$group   = $this->input->get('group', '', 'cmd');
		$streamId = $this->input->get('stream_id', 0, 'int');

		$start = $this->input->get('startlimit', 0, 'int');
		$limit = $this->input->get('limit', 0, 'int');

		// Format the element
		$element = $element . '.' . $group . '.' . $verb;

		$options = array('uid' => $uid, 'element' => $element, 'stream_id' => $streamId, 'start' => $start, 'limit' => $limit);

		$model  = FD::model('Comments');
		$result = $model->getComments($options);

		$comments = array();

		$likesModel = FD::model('Likes');

		if ($result) {
			foreach ($result as $row) {

				$comment = new stdClass();
				$comment->id = $row->id;
				$comment->avatar  = $row->getAuthor()->getAvatar();
				$comment->content = $row->getComment();
				$comment->lapsed  = $row->getDate();
				$comment->date = $row->created;

				$author = $row->getAuthor();

				$comment->actor = (object) array(
					'avatar' => $author->getAvatar(),
					'name' => $author->getName(),
					'id' => $author->id
				);

				$comment->likes   = new stdClass();
				$comment->likes->uid     = $comment->id;
				$comment->likes->element = 'comments';
				$comment->likes->group   = $group;
				$comment->likes->verb    = 'likes';
				$comment->likes->stream_id = 0;
				$comment->likes->total   = $likesModel->getLikesCount($comment->likes->uid, 'comments.' . $group . '.like');
				$comment->likes->hasLiked = $likesModel->hasLiked($comment->likes->uid, 'comments.' . $group . '.like', $userId);

				$comments[] = $comment;
			}
		}

		$this->set('comments', $comments);

		parent::display();
	}
}
