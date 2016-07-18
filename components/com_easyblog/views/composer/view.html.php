<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewComposer extends EasyBlogView
{
	public function display($tmpl = null)
	{
        // this is to handle the backend
        if (JFactory::getApplication()->isAdmin() && method_exists($this, $this->getLayout())) {
            $layoutName = $this->getLayout();
            return $this->$layoutName();
        }

        JHTML::_('behavior.modal');

        // Ensure that the user is logged in.
        EB::requireLogin();

        // null = new post
        // 63   = post 63 from post table
        // 63.2 = post 63, revision 2 from history table
        $uid = $this->input->getVar('uid', null);

        // If no id given, create a new post.
        $post = EB::post($uid);

        // Do not allow normal user to edit the existing post if there is already another revision that was submitted for approvals.
        if (!EB::isSiteAdmin() && !$this->acl->get('moderate_entry') && $post->hasRevisionWaitingForApproval()) {
            JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED_TO_EDIT_ANOTHER_REVISION_PENDING_APPROVAL'));

            return;
        }

        // Verify access (see function manager())
        if (!$post->canCreate() && !$post->canEdit()) {

            // Do not allow user to access this page if he doesn't have access
            JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'));

            return;
        }

        // If there's no id provided, we will need to create the initial revision for the post.
        if (!$uid) {
            $post->create();
            $uid = $post->uid;
        }

        // Determines if we should show the sidebars by default
        $templateId = $this->input->get('block_template', 0, 'int');

        if (!$post->title) {
            $this->doc->setTitle(JText::_('COM_EASYBLOG_COMPOSER_POST_UNTITLED'));
        }

        $composer = EB::composer();

        // Render default post templates
        $postTemplatesModel = EB::model('Templates');
        $postTemplates = $postTemplatesModel->getPostTemplates($this->my->id);

        $this->set('postTemplates', $postTemplates);
        $this->set('composer', $composer);
        $this->set('post', $post);

        return parent::display('composer/default');
	}

    public function getPosts($tmpl = null)
    {
        // Ensure that the user is logged in.
        EB::requireLogin();

        $lang = $this->input->getVar('code', null);
        $langid = $this->input->getVar('codeid', null);
        $search = $this->input->getVar('query', '');

        // Admin might want to display the featured blogs on all pages.
        $start = $this->input->get('start', 0, 'int');
        $limitstart = $this->input->get('limitstart', 0, 'int');

        // conditions
        $options = array();

        $options['langcode'] = $lang;

        if ($search) {
            $options['search'] = $search;
        }

        if (! EB::isSiteAdmin()) {
            $options['userid'] = $this->my->id;
        }

        $model = EB::model('Blog');

        // get results
        $data = $model->getAssociationPosts($options);

        // Get the pagination
        $pagination = $model->getPagination();
        // $pagination = $pagination->getPagesLinks();

        if ($data) {
            EB::cache()->insert($data);
        }

        $posts = EB::formatter('list', $data, false);

        $this->set('posts', $posts);
        $this->set('langcode', $lang);
        $this->set('langid', $langid);
        $this->set('search', $search);
        $this->set('pagination', $pagination);

        parent::display('composer/posts/listing');
    }

}
