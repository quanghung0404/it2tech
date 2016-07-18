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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewDashboard extends EasyBlogView
{
	/**
	 * Confirmation to autopost to the respective social sites
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmAutopost()
	{
		$type = $this->input->get('type', '' ,'cmd');
		$id = $this->input->get('id', 0, 'int');

		$theme = EB::template();
		$theme->set('id', $id);
		$theme->set('type', $type);

		$output = $theme->output('site/dashboard/entries/dialog.autopost');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to reject a team member request
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRejectTeamMember()
	{
		$id = $this->input->get('id', 0, 'int');

		$request = EB::table('TeamblogRequest');
		$request->load($id);

		// Check if the id provided is valid
		if (!$id || !$request->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		$theme = EB::template();

		$theme->set('request', $request);
		$theme->set('id', $id);
		$output = $theme->output('site/dashboard/requests/dialog.reject');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to approve a team member request
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmApproveTeamMember()
	{
		$id = $this->input->get('id', 0, 'int');

		$request = EB::table('TeamblogRequest');
		$request->load($id);

		// Check if the id provided is valid
		if (!$id || !$request->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		$theme = EB::template();

		$theme->set('request', $request);
		$theme->set('id', $id);
		$theme->set('buttonApproveLabel', 'COM_EASYBLOG_TEAMBLOG_APPROVE_REQUEST');
		$output = $theme->output('site/dashboard/requests/dialog.approve');

		return $this->ajax->resolve($output);
	}

	/**
	 * Retrieves the total number of pending entries
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPendingCount()
	{
		// Requires user to be logged in
		EB::requireLogin();

		$model = EB::model('Pending');
		$total = $model->getTotalPending($this->my->id);

		return $this->ajax->resolve($total);
	}

	/**
	 * Retrieves the total number of pending entries
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getModerationCount()
	{
		// Requires user to be logged in
		EB::requireLogin();

		// User must have access to view pending blog posts
		if (!$this->acl->get('manage_pending') && !$this->acl->get('publish_entry') && !EB::isSiteAdmin()) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));
		}

		$model = EB::model('Blog');
		$total = $model->getTotalModeration();

		return $this->ajax->resolve($total);
	}

	/**
	 * Displays a confirmation dialog to insert contents from a template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmInsertTemplate()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = EB::table('PostTemplate');
		$table->load($id);

		if (!$id || !$table->id) {
			return $this->ajax->reject();
		}

		$theme = EB::template();

		$theme->set('table', $table);

		$output = $theme->output('site/composer/dialogs/insert.template');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to delete comments
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editComment()
	{
		$ajax 	= EB::ajax();
		$ids 	= $this->input->get('ids', '', 'array');
		$id 	= (int) $ids[0];

		if (!$this->acl->get('manage_comment') || $this->my->guest) {
			die(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_COMMENT'));
		}

		// Load the comment
		$comment = EB::table('Comment');
		$comment->load($id);

		if (!$comment->id || !$id) {

			return $ajax->reject(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');
		}

		// Load the blog
		$blog 	= EB::table('Blog');
		$blog->load($comment->post_id);

		// Test if the user really can edit the entry.
		if ($blog->created_by != $this->my->id && !EB::isSiteAdmin() && !$this->acl->get('edit_comment') ) {
			return $ajax->reject(JText::_( 'COM_EASYBLOG_NOT_ALLOWED'), 'error');
		}

		$theme 	= EB::template();
		$theme->set('id', $id);
		$theme->set('comment', $comment);
		$theme->set('blog', $blog);

		$output = $theme->output('site/dashboard/comments/dialog.edit');

		return $ajax->resolve($output);
	}

	/**
	 * Displays confirmation to delete comments
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function categoryForm()
	{
		$ajax 	= EB::ajax();
		$ids 	= $this->input->get('ids', '', 'array');
		$id 	= 0;

		if ($ids) {
			$id 	= (int) $ids[0];
		}

		// Load the category
		$category = EB::table('Category');
		$category->load($id);

		$aclItem 	= EB::table('CategoryAclItem');
		$rules 		= $aclItem->getAllRuleItems();
		$assigned 	= $category->getAssignedACL();

		$parents 	= EB::populateCategories('', '', 'select', 'parent_id', $category->parent_id);
		$editor 	= JFactory::getEditor();


		$theme 	= EB::template();
		$theme->set('id', $id);
		$theme->set('category', $category);
		$theme->set('editor', $editor);
		$theme->set('parents', $parents);
		$theme->set('rules', $rules);
		$theme->set('assigned', $assigned);

		$output = $theme->output('site/dashboard/categories/dialog.edit');

		return $ajax->resolve($output);
	}

	/**
	 * Displays confirmation to toggle publishing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmPublishComment()
	{
		$ajax 	= EB::ajax();
		$ids 	= $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}


		$theme 	= EB::template();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/comments/dialog.publish');

		return $ajax->resolve($output);
	}

	/**
	 * Displays confirmation to toggle publishing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnpublishComment()
	{
		$ajax 	= EB::ajax();
		$ids 	= $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}


		$theme 	= EB::template();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/comments/dialog.unpublish');

		return $ajax->resolve($output);
	}

	/**
	 * Displays confirmation to delete comments
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDeleteComment()
	{
		$ajax 	= EB::ajax();
		$ids 	= $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme 	= EB::template();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/comments/dialog.delete');

		return $ajax->resolve($output);
	}

	/**
	 * Displays confirmation to delete blog posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$ajax 	= EB::ajax();
		$ids 	= $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}


		$theme 	= EB::template(null, array('dashboard' => true));
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/entries/dialog.delete');

		return $ajax->resolve($output);
	}


	/**
	 * Displays confirmation to delete revisions
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRevisionDelete()
	{
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme 	= EB::template(null, array('dashboard' => true));
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/revisions/dialog.delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to toggle publishing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmFeature()
	{
		$id = $this->input->get('id', 0, 'int');

		$theme 	= EB::template(null, array('dashboard' => true));
		$theme->set('id', $id);

		$output = $theme->output('site/dashboard/entries/dialog.feature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to toggle publishing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnfeature()
	{
		$id = $this->input->get('id', '', 'int');

		$theme = EB::template(null, array('dashboard' => true));
		$theme->set('id', $id);
		$output = $theme->output('site/dashboard/entries/dialog.unfeature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to toggle publishing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmPublish()
	{
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::template(null, array('dashboard' => true));
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/entries/dialog.publish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to toggle publishing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnpublish()
	{
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}


		$theme 	= EB::template(null, array('dashboard' => true));
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/entries/dialog.unpublish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Display a dialog confirm the blog entry approvals.
	 *
	 * @param	int		$ids		The specific blog id.
	 * @param	string	$url		The redirect url.
	 * @return	string	JSON encoded strings.
	 */
	public function confirmApproveBlog()
	{
		$ajax = EB::ajax();
		$ids  = $this->input->get('ids', '', 'array');

		if (!$this->my->id || !$this->acl->get('manage_pending') && !EB::isSiteAdmin()) {
			die();
		}

		$theme 	= EB::template();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/moderate/dialog.approve');

		return $ajax->resolve($output);
	}

	/**
	 * Displays confirmation to reject a blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmRejectBlog()
	{
		$ajax = EB::ajax();
		$ids = $this->input->get('ids', '', 'array');

		if (!$this->my->id || !$this->acl->get('manage_pending') && !EB::isSiteAdmin()) {
			die();
		}

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

        $theme = EB::template();
        $theme->set('ids', $ids);
        $output = $theme->output('site/dashboard/moderate/dialog.reject');

        return $this->ajax->resolve($output);

	}

	/**
	 * Responds to the getcategory ajax call by return a list of category items.
	 *
	 * @access	public
	 * @param	null
	 */
	public function getCategory()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );
		$id 	= JRequest::getInt( 'id' );

		$model	= EB::model( 'categories', true );
		$items	= $model->getChildCategories( $id , true, true );

		if( !$items )
		{
			return $ajax->success( array() );
		}

		$categories 	= array();

		for($i = 0; $i < count($items); $i++)
		{
			$item           = $items[$i];

			$category 		= EB::table('Category');
			$category->load( $item->id );

			$item->hasChild = $category->getChildCount();
		}

 		$ajax->success( $items );
	}

	/**
	 * Confirmation to delete a category from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 */
	public function confirmDeleteCategory()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Get the category id
		$ids = $this->input->get('ids', array(), 'array');
		$id = $ids[0];

		// Load the tag
		$category = EB::table('Category');
		$category->load($id);

		if (!$id || !$category->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Ensure that the user has access to delete categories
		if (!$this->acl->get('delete_category') && !EB::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Ensure that the user owns the tag
		if ($category->created_by != $this->my->id && !EB::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$theme = EB::template();
		$theme->set('id', $id);
		$output = $theme->output('site/dashboard/categories/dialog.delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to delete a tag from the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDeleteTag()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		$id = $this->input->get('id', 0, 'int');

		// Load the tag
		$tag = EB::table('Tag');
		$tag->load($id);

		if (!$id || !$tag->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Ensure that the user has access to create tags
		if (!$this->acl->get('create_tag') && !EB::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Ensure that the user owns the tag
		if ($tag->created_by != $this->my->id && !EB::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$theme = EB::template();
		$theme->set('id', $id);
		$output = $theme->output('site/dashboard/tags/dialog.delete');

		return $this->ajax->resolve($output);
	}

	public function buildcategorytier()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );
		$id 	= JRequest::getInt( 'id' );

		if( empty($id) )
		{
			return $ajax->fail();
		}

		$loop           = true;
		$scategory 		= EB::table('Category');
		$scategory->load( $id );

		$model			= EB::model( 'categories', true );
		$tier 			= array();

		$searchId       = $scategory->parent_id;
		while( $loop )
		{
			if( empty( $searchId ) )
			{
				$loop   = false;
			}
			else
			{
				$category 		= EB::table('Category');
				$category->load( $searchId );
				$tier[]   		= $category;

				$searchId = $category->parent_id;
			}
		}

		// get the root tier
		$root   = array_pop( $tier );

		//reverse the array order
		$tier	= array_reverse($tier);

		array_push($tier, $scategory);
// 		echo '<pre>';
// 		print_r($tier);
// 		echo '</pre>';
// 		exit;


		$categories = array();

		foreach( $tier as $cat )
		{

			$pItem  			= new stdClass();

			$pItem->id  		= $cat->id;
			$pItem->parent_id  	= $cat->parent_id;
			$pItem->hasChild    = 1;


			$model	= EB::model( 'categories', true );
			$items	= $model->getChildCategories( $cat->parent_id, true, true );

			if( !$items )
			{
				$pItem->hasChild = 0;
				$categories[]   = $pItem;
				continue;
			}

			for($i = 0; $i < count($items); $i++)
			{
				$item           = $items[$i];

				$category 		= EB::table('Category');
				$category->load( $item->id );

				$item->hasChild = $category->getChildCount();
			}

			$pItem->childs  = $items;
			$categories[]   = $pItem;
		}

		$ajax->success( $categories );
	}

	/**
	 * Load media configuration
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function mediaConfiguration()
	{
		$ajax 	= EB::ajax();

		// Require login
		EB::requireLogin();

		$user = EB::user($this->my->id);

		$tpl = EB::template();
		$blogger_id = $user->id;

		$tpl->set('blogger_id', $blogger_id );

		// @since: 3.6
		// Media manager options
		$tpl->set( 'session'			, JFactory::getSession() );

		$mediamanager	= EB::mediamanager();
		$userFolders	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath('' , 'user') , 'folders' );
		$userFiles		= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath('' , 'user') , 'files' );

		$sharedFolders	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath( '' , 'shared' ) , 'folders' );
		$sharedFiles 	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath( '' , 'shared' ) , 'files' );

		$tpl->set( 'userFolders' , $userFolders );
		$tpl->set( 'userFiles'	 , $userFiles );
		$tpl->set( 'sharedFolders' , $sharedFolders );
		$tpl->set( 'sharedFiles'	, $sharedFiles );

		// @rule: Test if the user is already associated with Flickr
		$oauth 	= EB::table('OAuth');
		$associated	= $oauth->loadByUser( $this->my->id , EBLOG_OAUTH_FLICKR );
		$tpl->set( 'flickrAssociated' , $associated );

		// Retrieve flickr's data
		$flickr = $this->getFlickrData();

		// Retrieve dropbox's data
		$dropbox = $this->getDropboxData();

		$tpl->set('flickr', $flickr);
		$tpl->set('dropbox', $dropbox);

		$html = $tpl->output('site/media/configuration');

		$ajax->resolve($html);
	}
}
