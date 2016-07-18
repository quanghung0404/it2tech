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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewBlogs extends EasyBlogAdminView
{
    /**
     * Allows caller to re-notify subscribers
     *
     * @since   4.0
     * @access  public
     */
	public function confirmNotify()
	{
        $id = $this->input->get('id', 0, 'int');

        $theme = EB::template();
        $theme->set('id', $id);

        $output = $theme->output('admin/blogs/dialog.renotify');

		return $this->ajax->resolve($output);
	}

    /**
     * Allows caller to move blog posts between categories
     *
     * @since   4.0
     * @access  public
     */
    public function move()
    {
        $filter = array();
        $filter[] = JHTML::_('select.option', '', JText::_('COM_EASYBLOG_SELECT_CATEGORY'));

        $model = EB::model('Category');
        $categories = $model->getAllCategories();

        foreach ($categories as $cat) {
            $filter[] = JHTML::_('select.option', $cat->id, $cat->title);
        }

        $theme = EB::template();
        $theme->set('filter', $filter);

        $output = $theme->output('admin/blogs/dialog.move.category');

        return $this->ajax->resolve($output);
    }

    /**
     * Confirmation to accept a pending blog post
     *
     * @since   4.0
     * @access  public
     */
    public function confirmAccept()
    {
        $id = $this->input->get('id', 0, 'default');

        $theme = EB::template();
        $theme->set('id', $id);

        $output = $theme->output('admin/blogs/dialog.accept.confirm');

        return $this->ajax->resolve($output);
    }

    /**
     * Confirmation to reject a pending blog post
     *
     * @since   4.0
     * @access  public
     */
    public function confirmReject()
    {
        $id = $this->input->get('id', '', 'int');

        $theme = EB::template();
        $theme->set('id', $id);

        $output = $theme->output('admin/blogs/dialog.reject.confirm');

        return $this->ajax->resolve($output);
    }

    /**
     * Displays confirmation to auto post the post
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function confirmAutopost()
    {
        $id = $this->input->get('id', 0, 'int');
        $type = $this->input->get('type', '', 'word');

        $theme = EB::template();
        $theme->set('type', $type);
        $theme->set('id', $id);

        $output = $theme->output('admin/blogs/dialog.autopost.confirm');

        return $this->ajax->resolve($output);
    }


    /**
     * Allows caller to reassign blog posts author
     *
     * @since   4.0
     * @access  public
     */
    public function authors()
    {
        $filter = array();
        $filter[] = JHTML::_('select.option', '', JText::_('COM_EASYBLOG_SELECT_AUTHOR'));

        $model = EB::model('Users');
        $users = $model->getUsers(true, false);

        if ($users) {
            foreach ($users as $user) {
                $filter[] = JHTML::_('select.option', $user->id, $user->name);
            }
        }

        $theme = EB::template();
        $theme->set('filter', $filter);

        $output = $theme->output('admin/blogs/dialog.assign.author');
        return $this->ajax->resolve($output);
    }

}
