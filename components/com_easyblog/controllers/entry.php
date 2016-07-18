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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerEntry extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	function _trim(&$text)
	{
		$text = JString::trim($text);
	}

	/*
	 * @since 2.0.3300
	 * Responsible to update an existing comment
	 *
	 * @param	null
	 * @return	null
	 */
	public function updateComment()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
	    $acl		= EB::acl();
		$id			= JRequest::getInt( 'commentId' );
		$post		= JRequest::get( 'POST' );

		//add here so that other component with the same comment.php jtable file will not get reference.
		JTable::addIncludePath( EBLOG_TABLES );
		$comment	= EB::table('Comment');
		$comment->load( $id );
		$redirect	= EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $comment->post_id , false );

		if (($my->id != $comment->created_by || !$acl->get('delete_comment') ) && !EasyBlogHelper::isSiteAdmin() && !$acl->get('manage_comment') || $my->id == 0) {
			EB::info()->set( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPDATE_COMMENT') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}
		$comment->bindPost( $post );

		if( !$comment->validate( 'title' ) )
		{
			EB::info()->set( JText::_('COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !$comment->validate( 'comment' ) )
		{
			EB::info()->set( JText::_('COM_EASYBLOG_COMMENT_IS_EMPTY') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		$comment->modified	= EB::date()->toMySQL();

		if( !$comment->store() )
		{
			EB::info()->set( JText::_('COM_EASYBLOG_COMMENT_FAILED_TO_SAVE') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		EB::info()->set( JText::_('COM_EASYBLOG_COMMENT_UPDATED_SUCCESS') , 'success' );
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/**
	 * @since 3.0
	 * Unsubscribe a user with email to a blog post
	 *
	 * @param	int		Subscription ID
	 * @param	int		Blog post ID
	 *
	 * @return	bool	True on success
	 */
	public function unsubscribe()
	{
		$subscriptionId	= JRequest::getInt('subscription_id');
		$blogId			= JRequest::getInt('blog_id');
		$my				= JFactory::getUser();
		$mainframe		= JFactory::getApplication();
		$redirect		= EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $blogId , false );

		// Check variables
		if( $my->id == 0 || !$subscriptionId || !$blogId )
		{
			EB::info()->set( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$mainframe->redirect( $redirect );
		}

		// Need to ensure that whatever id passed in is owned by the current browser
		$blogModel	= EB::model('Blog');
		$sid		= $blogModel->isBlogSubscribedUser( $blogId , $my->id , $my->email );

		if($subscriptionId != $sid)
		{
			EB::info()->set( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$mainframe->redirect( $redirect );
		}

		// Proceed to unsubscribe
		$table	= EB::table('Subscriptions');
		$table->load( $subscriptionId );

		if (!$table->delete())
		{
			EB::info()->set( JText::_( 'COM_EASYBLOG_UNSUBSCRIBE_BLOG_FAILED' ) , 'error');
			$mainframe->redirect( $redirect );
		}

		EB::info()->set( JText::_('COM_EASYBLOG_UNSUBSCRIBE_BLOG_SUCCESS') , 'success' );
		$mainframe->redirect( $redirect );
	}
}
