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

require_once(__DIR__ . '/table.php');

class EasyBlogTableComment extends EasyBlogTable
{
	public $id = null;
	public $post_id = null;
	public $comment = null;
	public $name = null;
	public $title = null;
	public $email = null;
	public $url = null;
	public $ip = null;
	public $created_by = null;
	public $created = null;
	public $modified = null;
	public $published = null;
	public $publish_up = null;
	public $publish_down = null;
	public $ordering = null;
	public $vote = null;
	public $hits = null;
	public $sent = null;
	public $lft = null;
	public $rgt = null;
	public $parent_id = null;

	/**
	 * Stores the comment author object. Cached locally and not on static
	 * @var EasyBlogTableProfile
	 */
	public $author = null;

	public function __construct(& $db )
	{
		parent::__construct( '#__easyblog_comment' , 'id' , $db );
	}

	/**
	 * Deletes any stream related to this comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeStream()
	{
		// jomsocial
		EB::jomsocial()->removeCommentStream($this->id);
	}

	/**
	 * Method to update ordering before a comment is saved.
	 **/
	public function updateOrdering()
	{
		$model			= EB::model( 'Comment' );
		$latestComment 	= $model->getLatestComment( $this->post_id , $this->parent_id );

		// @rule: Processing child comments
		if( $this->parent_id != 0 )
		{
			$parentComment 	= EB::table('Comment');
			$parentComment->load( $this->parent_id );

			$left 		= $parentComment->lft + 1;
			$right 		= $parentComment->lft + 2;
			$nodeVal	= $parentComment->lft;

			if( !empty( $latestComment ) )
			{
				$left 		= $latestComment->rgt + 1;
				$right		= $latestComment->rgt + 2;
				$nodeVal	= $latestComment->rgt;
			}

			$model->updateCommentSibling( $this->post_id , $nodeVal );

			$this->lft 		= $left;
			$this->rgt 		= $right;

			return true;
		}


		// @rule: Processing new comments
		$left 	= 1;
		$right 	= 2;

		if( !empty( $latestComment ) )
		{
			$left 	= $latestComment->rgt + 1;
			$right	= $latestComment->rgt + 2;

			$model->updateCommentSibling( $this->post_id , $latestComment->rgt );
		}

		$this->lft 	= $left;
		$this->rgt 	= $right;

		return true;
	}

	/**
	 * Process email notifications
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processEmails($isModerated = false , $blog )
	{
		$config = EB::config();

		// Fix contents of comments.
		$content = $this->comment;

		// Initialize what we need
		$commentAuthor = $this->name;
		$commentAuthorEmail = $this->email;
		$commentAuthorAvatar = JURI::root() . 'components/com_easyblog/assets/images/default_blogger.png';

		// Get the date object
		$date = EB::date();

		// Format the date
		$commentDate = $date->format(JText::_('DATE_FORMAT_LC3'));

		$teamLink = '';

		if (isset($blog->team)) {
			$teamLink = '&team=' . $blog->team;
		}

		// Get the author data
		if ($this->created_by != 0) {
			$user = $this->getAuthor();

			$commentAuthor = $user->getName();
			$commentAuthorEmail = $user->user->email;
			$commentAuthorAvatar = $user->getAvatar();
		}

		$blogAuthor = EB::user($blog->created_by);

		// truncate the content of the comment.
		if ($config->get('notification_commentruncate', false)) {
			$content = JHTML::_('string.truncate', $content, $config->get('notification_commenttruncate_limit', 300));
		}
		
		$data 		= array(
							'blogTitle'			=> $blog->title,
							'blogIntro'			=> $blog->intro,
							'blogContent'		=> $blog->content,
							'blogLink'			=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry'.$teamLink.'&id='. $blog->id, false, true),
							'commentTitle'		=> empty( $comment->title ) ? '-' : $comment->title,
							'commentContent'	=> $content,
							'commentAuthor'		=> $commentAuthor,
							'commentAuthorAvatar' => $commentAuthorAvatar,
							'commentDate'		=> $commentDate,
							'commentLink'		=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry'.$teamLink.'&id='. $blog->id, false, true) . '#comment-' . $this->id
						);


		// Get a list of emails
		$emails = array();

		// Get the notification library
		$notification = EB::notification();

		if ($isModerated) {

			$hashkey		= EB::table('HashKeys');
			$hashkey->uid	= $this->id;
			$hashkey->type	= 'comments';
			$hashkey->store();

			// Generate the approval and reject links
			$data['approveLink'] = EBR::getRoutedURL('index.php?option=com_easyblog&task=comments.approve&key=' . $hashkey->key, false, true);
			$data['rejectLink'] = EBR::getRoutedURL('index.php?option=com_easyblog&task=comments.reject&key=' . $hashkey->key, false, true);

			// Send email notification to admin.
			if ($config->get('custom_email_as_admin')) {
				$notification->getCustomEmails( $emails );
			} else {
				$notification->getAdminEmails( $emails );
			}

			// @rule: Send email notification to blog authors.
			if ($config->get('notification_commentmoderationauthor')) {
				$obj = new stdClass();
				$obj->unsubscribe = false;
				$obj->email = $blogAuthor->user->email;

				$emails[$blogAuthor->user->email] = $obj;
			}

			$notification->send($emails, JText::_('COM_EASYBLOG_NEW_COMMENT_ADDED_MODERATED_TITLE'), 'comment.moderate', $data);

			return true;
		}

		if (!$isModerated) {

			// Get a list of admin emails
            if ($config->get('notification_commentadmin')) {
    			$notification->getAdminNotificationEmails($emails);
            }

			// Send notification to blog authors.
			if ($config->get('notification_commentauthor')) {
				
				$obj = new stdClass();
				$obj->unsubscribe = false;
				$obj->email = $blogAuthor->user->email;

				$emails[$blogAuthor->user->email] = $obj;
			}

			// Send notifications to blog subscribers
			if (($config->get('main_subscription') && $blog->subscription == '1') && $config->get('notification_commentsubscriber')) {
				$notification->getBlogSubscriberEmails($emails, $blog->id);
			}

			// Do not send to the person that commented on the blog post.
			unset($emails[$commentAuthorEmail]);

			if (!$emails) {
				return false;
			}

			// Send the emails now.
			$subject = JText::sprintf('COM_EASYBLOG_NEW_COMMENT_ADDED_IN_POST', $blog->title);

			$notification->send($emails, $subject, 'comment.new', $data);

			return true;
		}
	}

	/**
	 * Retrieve a list of user id's from the following:
	 *
	 * - Users who subscribed to the blog entry
	 */
	public function getSubscribers( $blog , $excludeUsers )
	{
		$result			= $blog->getSubscribers();
		$subscribers	= array();

		foreach( $result as $row )
		{
			if( !in_array( $row->user_id , $excludeUsers ) && $row->user_id )
			{
				$subscribers[]	= $row->user_id;
			}
		}

		return $subscribers;
	}

	/**
	 * Override parent's store behavior
	 *
	 * @since	4.0
	 * @access	public
	 * @param	bool	Determines if this is moderated
	 * @return
	 */
	public function store($isModerated = false)
	{
		// Determines if this is a new comment
		$isNew = !$this->id ? true : false;

		// @rule: Update the ordering as long as this is a new comment. Regardless of the publishing status
		if ($isNew) {
			$this->updateOrdering();
		}

		// Import plugins
		JPluginHelper::importPlugin('easyblog');
        $dispatcher = JDispatcher::getInstance();

		// @onBeforeCommentSave
        $dispatcher->trigger('onBeforeCommentSave', array(&$this));

		// @rule: If this was moderated and it is published, we should notify the other extensions.
		if ($isModerated && $this->published) {
			$isNew 	= true;
		}

		// @onAfterCommentSave
        $dispatcher->trigger('onAfterCommentSave', array(&$this));

		// @rule: Store after the ordering is updated
		$state = parent::store();

		// @rule: Run point integrations here.
		if ($isNew && $this->published == 1 && $state) {

			// Get the current logged in user
			$my = JFactory::getUser();

			// Get the blog object
			$blog = $this->getBlog();

			// Load site's language file
			EB::loadLanguages();

			// Get config
			$config = EB::config();

			$author = $this->getAuthor();

			// Get the external link of the blog post
			$link = $blog->getExternalBlogLink('index.php?option=com_easyblog&view=entry&id='. $blog->id) . '#comment-' . $this->id;

			// @rule: Send notifications to the author of the blog for EasyDiscuss
			if ($blog->created_by != $this->created_by && $this->created_by != 0) {

				EB::easydiscuss()->addNotification($blog, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT_IN_YOUR_BLOG' , $blog->title), EBLOG_NOTIFICATIONS_TYPE_COMMENT, array($blog->created_by), $this->created_by, $link);

				// Add notifications for EasySocial
				EB::easysocial()->notifySubscribers($blog, 'new.comment', $this);
			}

			// Notify subscribers through notification system.
			if ($this->created_by != 0) {

				$targets = $this->getSubscribers($blog, array($this->created_by, $blog->created_by));

				if (!empty($targets) && $config->get('integrations_easydiscuss_notification_comment_follower')) {
					EB::easydiscuss()->addNotification($blog, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT_POSTED', $blog->title), EBLOG_NOTIFICATIONS_TYPE_COMMENT, $targets, $this->created_by, $link);
				}

				// Integrations with EasyDiscuss
				EB::easydiscuss()->log( 'easyblog.new.comment' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_COMMENT' , $blog->title ) );
				EB::easydiscuss()->addPoint('easyblog.new.comment' , $this->created_by );
				EB::easydiscuss()->addBadge('easyblog.new.comment' , $this->created_by );

				// Integrations with EasySocial
				EB::easysocial()->assignPoints('comment.create', $this->created_by);
				EB::easysocial()->assignPoints('comment.create.author', $blog->created_by);
				EB::easysocial()->assignBadge('comment.create', JText::_('COM_EASYBLOG_EASYSOCIAL_BADGE_CREATE_COMMENT'));

				// Alpha user points
				$url = EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $this->post_id);

				EB::aup()->assignPoints('plgaup_easyblog_add_comment', $this->created_by, JText::sprintf('COM_EASYBLOG_AUP_NEW_COMMENT_SUBMITTED', $url, $blog->title));

				// @rule: Add comment for blog author
				if ($blog->created_by != $this->created_by) {
					EB::aup()->assignPoints('plgaup_easyblog_add_comment_blogger', $blog->created_by, JText::sprintf('COM_EASYBLOG_AUP_NEW_COMMENT_SUBMITTED', $url, $blog->title));
				}
			}

			// Determine whether or not to push this as a normal activity
			$external = $blog->getBlogContribution();

			// @rule: Add activity integrations for group integrations.
			if ($external && $external->type != EASYBLOG_POST_SOURCE_TEAM) {

				if ($external->type == EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP) {
					EB::groups()->addCommentStream($blog, $this, $external);

				} else if ($external->type == EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT) {
					EB::event()->addCommentStream($blog, $this, $external);

				}
			} else {

				// Add jomsocial stream
				EB::jomsocial()->insetCommentActivity($this, $blog);

				// Add EasySocial stream
				EB::easysocial()->createCommentStream($this, $blog);

				// Assign EasySocial points
				EB::easysocial()->assignPoints('comments.create');

				// Give points to the comment author
				EB::jomsocial()->assignPoints('com_easyblog.comments.add');

				// @rule: Give points to the blog author
				if ($my->id != $blog->created_by && $this->created_by != 0) {

					// Assign EasySocial points
					EB::easysocial()->assignPoints('comments.create.author', $blog->created_by);

					// Assign Jomsocial points
					EB::jomsocial()->assignPoints('com_easyblog.comments.addblogger', $blog->created_by);
				}
			}

		}

		return $state;
	}

	/**
	 * Retrieves the blog object associated with this comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlog()
	{
		$post = EB::post($this->post_id);

		return $post;
	}

	/**
	 * Overrides the parent's delete method
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete($pk = null)
	{
		// Try to delete the comment item first
		$state = parent::delete($pk);

		// Get the current logged in user
		$my = JFactory::getUser();

		// Remove comment's stream
		$this->removeStream();

		if ($this->created_by != 0 && $this->published == '1') {

			// Get the blog post
			$post = $this->getBlog();

			// Load language
			EB::loadLanguages();

			$config = EB::config();

			// Integrations with EasyDiscuss
			EB::easydiscuss()->log('easyblog.delete.comment' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_COMMENT' , $post->title));
			EB::easydiscuss()->addPoint('easyblog.delete.comment' , $this->created_by );
			EB::easydiscuss()->addBadge('easyblog.delete.comment' , $this->created_by );

			// AlphaUserPoints
			EB::aup()->assign('plgaup_easyblog_delete_comment', $this->created_by, JText::_('COM_EASYBLOG_AUP_COMMENT_DELETED'));

			// Assign EasySocial points
			EB::easysocial()->assignPoints('comments.remove', $this->created_by);

			// Deduct points from the comment author
			EB::jomsocial()->assignPoints('com_easyblog.comments.remove', $this->created_by);

			// Deduct points from the blog post author
			if ($my->id != $post->created_by) {

				// Deduct EasySocial points
				EB::easysocial()->assignPoints('comments.remove.author', $post->created_by);

				// JomSocial
				EB::jomsocial()->assignPoints('com_easyblog.comments.removeblogger', $post->created_by);

				// AUP
				EB::aup()->assignPoints('plgaup_easyblog_delete_comment_blogger', $post->created_by, JText::sprintf('COM_EASYBLOG_AUP_COMMENT_DELETED_BLOGGER', $url, $post->title));
			}
		}

		return $state;
	}

	/**
	 * Responsible to bind values into itself.
	 *
	 * @access	public
	 * @param 	Array 	$post	An array of posted values.
	 */
	public function bindPost($post)
	{
		$config = EB::config();

		// Could be edited
		if (!empty($post['commentId'])) {
			$this->id	= $post['commentId'];
		}

		// Set the post id
		$this->post_id  = $post['post_id'];

		//replace a url to link
		$comment        = $post['comment'];

		$filter 		= JFilterInput::getInstance();
		$comment		= $filter->clean($comment);

		$this->comment	= $comment;

		if( isset( $post['name'] ) )
		{
			$this->name		= $filter->clean($post['name']);
		}

		if( isset( $post['title'] ) )
		{
			$this->title	= $filter->clean($post['title']);
		}

		if( isset( $post['email'] ) )
		{
			$this->email	= $filter->clean($post['email']);
		}

		if( isset( $post['url'] ) )
		{
			$this->url		= $filter->clean($post['url']);
		}
	}

	/**
	 * Retrieves the comment author name
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthorName()
	{
		// If the comments was posted as a guest, we want to display the appropriate name
		if (!$this->created_by) {
			return $this->name;
		}

		// Get the author object
		$author 	= $this->getAuthor();

		return $author->getName();
	}

	/**
	 * Retrieves the comment author avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthorAvatar()
	{
		// Get the author object
		$author 	= $this->getAuthor();

		return $author->getAvatar();
	}

	/**
	 * Retrieves the created date object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCreated()
	{
		$date 	= EB::date($this->created);

		return $date;
	}

	/**
	 * Determines if the comment is published
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPublished()
	{
		return $this->published == 1;
	}

	/**
	 * Determines if the comment is moderated
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isModerated()
	{
		return $this->published === EBLOG_COMMENT_STATUS_MODERATED || $this->published === '2';
	}

	/**
	 * Allows caller to unpublish a comment
	 *
	 * @since	4.0
	 * @access	public
	 * @return	bool
	 */
	public function unpublish()
	{
		// Determines if the comment was previously moderated
		$pending = $this->published == EBLOG_COMMENT_STATUS_MODERATED;

		// Set the publish status
		$this->published = false;

		// Store the item
		$state = $this->store($pending);

		return $state;
	}

	/**
	 * Publishes a comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Determines if the comment was previously moderated
		$pending = $this->published == EBLOG_COMMENT_STATUS_MODERATED;

		// Set the publish status
		$this->published 	= true;

		// Store the item
		$state = $this->store($pending);

		// Send notifications if necessary
		if ($this->published && !$this->sent && $pending) {

			$this->comment = EB::comment()->parseBBCode($this->comment);
			$this->comment = nl2br($this->comment);

			// Process emails
			$this->processEmails();

			// Update the sent flag
			$this->updateSent();
		}

		return $state;
	}


	/**
	 * Determines if the comment is unpublished
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isUnpublished()
	{
		return $this->published != 1;
	}

	/**
	 * Retrieve the permalink to the comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = false)
	{
		$post = EB::post($this->post_id);
		$url = $post->getPermalink();

		// Append the fragments
		$url 	.= '#comment-' . $this->id;

		return $url;
	}

	/**
	 * Retrieves the formatted comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function getContent($truncate = true)
	{
		if ($truncate) {
			$text = JString::strlen($this->comment) > 150 ? JString::substr($this->comment, 0, 150) . JText::_('COM_EASYBLOG_ELLIPSES') : $this->comment;
		} else {
			$text = EB::comment()->parseBBCode($this->comment);
		}

		$text = strip_tags($text, '<img>');

		return $text;
	}

	/**
	 * Retrieves the comment author
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthor()
	{
		if (is_null($this->author)) {
			$this->author = EB::user($this->created_by);
		}

		return $this->author;
	}

	public function updateSent()
	{
		$db = EB::db();

		if(! empty($this->id))
		{
			$query  = 'UPDATE `#__easyblog_comment` SET `sent` = 1 WHERE `id` = ' . $db->Quote($this->id);

			$db->setQuery($query);
			$db->query();
		}

		return true;
	}


	public function isCreator( $id = '' )
	{
		if( empty( $id ) )
		{
			$id	= JFactory::getUser()->id;
		}

		return $this->created_by == $id;
	}

	/**
	 * Validates the comment posted
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateData()
	{
		if (!$this->validate('title')) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY'));
			return false;
		}

		if (!$this->validate('name')) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_NAME_IS_EMPTY'));
			return false;
		}

		if (!$this->validate('email')) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_EMAIL_IS_EMPTY'));
			return false;
		}

		if (!$this->validate('comment')) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_IS_EMPTY'));
			return false;
		}

		return true;
	}

	/**
	 * Validates the comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate($type)
	{
		$config		= EB::getConfig();


		if( $config->get( 'comment_requiretitle' ) && $type == 'title' )
		{
			return JString::strlen( $this->title ) != 0 ;
		}

		if( $type == 'name' )
		{
			return JString::strlen( $this->name ) != 0;
		}

		if( $type == 'email' )
		{
			return JString::strlen( $this->email ) != 0;
		}

		if( $type == 'comment' )
		{
			return JString::strlen( $this->comment ) != 0;
		}

		return true;
	}

	/**
	 * Determines if this comment is a spam
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isSpam()
	{
		$config = EB::config();

		if (!$config->get('comment_akismet')) {
			return false;
		}

		$data = array(
				'author'    => $this->name,
				'email'     => $this->email,
				'website'   => JURI::root(),
				'body'      => $this->comment,
				'permalink' => EBR::_('index.php?option=com_easyblog&view=entry&id=' . $this->post_id)
			);

		if (EB::akismet()->isSpam($data)) {
			return true;
		}

		return false;
	}

	/**
	 * Validates posted data
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validatePost($post)
	{
		$config = EB::config();
		$my     = JFactory::getUser();

		$comment = isset($post['comment']) ? $post['comment'] : '';
		$title   = isset($post['title']) ? $post['title'] : '';
		$register = isset($post['register']) ? $post['register'] : false;
		$username = isset($post['username']) ? $post['username'] : '';
		$subscribe = isset($post['subscribe']) ? $post['subscribe'] : false;
		$name = isset($post['name']) ? $post['name'] : '';
		$email = isset($post['email']) ? $post['email'] : '';
		$website = isset($post['website']) ? $post['website'] : '';
		$terms = isset($post['terms']) ? $post['terms'] : '';

		// If name is empty, it could be that the user is logged in
		if (!$my->guest) {
			$user = EB::user($my->id);

			// Override the name
			$name = $user->getName();

			// Override the email
			$email = $my->email;

			// Override the website
			$website = $user->getWebsite();
		}


		if (!$comment || JString::strlen($comment) == 0) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENTS_PLEASE_ENTER_SOME_COMMENTS'));
			return false;
		}

		if ($config->get('comment_requiretitle') && JString::strlen($title) == 0) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY'));
			return false;
		}

		if ($register && (!$username || JString::strlen($username) == 0)) {
			$this->setError(JText::_('COM_EASYBLOG_SUBSCRIPTION_USERNAME_IS_EMPTY'));
			return false;
		}

		if (!$name || JString::strlen($name) == 0) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_NAME_IS_EMPTY'));
			return false;
		}

		if ($config->get('comment_require_email') && (!$email || JString::strlen($email) == 0)) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_EMAIL_IS_EMPTY'));

			return false;
		}

		if ($my->guest && ($config->get('comment_require_website') && (!$website || JString::strlen($website) == 0))) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_WEBSITE_IS_EMPTY'));

			return false;
		}

		// If user is subscribed to the blog, ensure that email exists
		if ($subscribe && !$email) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_EMAIL_IS_EMPTY'));

			return false;
		}

		// Check for valid email
		$validEmail = EB::string()->isValidEmail($email);

		if ($config->get('comment_require_email') && !$validEmail) {
			$this->setError(JText::_('COM_EASYBLOG_COMMENT_EMAIL_INVALID'));

			return false;
		}

		// Ensure that the user checked the terms and condition box.
		if ($config->get('comment_tnc') && !$terms && (($config->get('comment_tnc_users') == 0 && $my->guest) || ($config->get('comment_tnc_users') == 1 && !$my->guest) || ($config->get('comment_tnc_users') == 2))) {
			$this->setError(JText::_('COM_EASYBLOG_YOU_MUST_ACCEPT_TNC'));

			return false;
		}

		return true;
	}
}
