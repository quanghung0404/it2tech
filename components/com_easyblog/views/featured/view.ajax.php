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

class EasyBlogViewFeatured extends EasyBlogView
{
	/**
	 * Displays a confirmation dialog to confirm featuring an author.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirm()
	{
		$ajax 	= EB::ajax();

		// Ensure that the user has privileges
		if (!EB::isSiteAdmin() && !$this->acl->get('feature_entry') ) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$type 	= $this->input->get('type', '', 'word');
		$id 	= $this->input->get('id', '', 'int');

		if ($type == 'blogger') {
			$title 	 = JText::_('COM_EASYBLOG_FEATURE_AUTHOR_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_FEATURE_AUTHOR_DIALOG_CONTENT');
		}

		if ($type == 'teamblog') {
			$title 	 = JText::_('COM_EASYBLOG_FEATURE_TEAMBLOG_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_FEATURE_TEAMBLOG_DIALOG_CONTENT');
		}

		$theme 	= EB::template();
		$theme->set('title', $title);
		$theme->set('content', $content);

		$output = $theme->output('site/featured/dialog.confirm');

		return $ajax->resolve($output);
	}

	/**
	 * Mark an item as featured item
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function makeFeatured()
	{
		$ajax 	= EB::ajax();

		// Ensure that the user has privileges
		if (!EB::isSiteAdmin() && !$this->acl->get('feature_entry') ) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Get the type
		$type = $this->input->get('type', '', 'word');
		$id   = $this->input->get('id', '', 'int');

		// Make the item featured
		$model = EB::model('Featured');
		$model->makeFeatured($type, $id);

		$ajax->resolve(true);
	}

	/**
	 * Removes featured status from an object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFeatured()
	{
		$ajax 	= EB::ajax();

		// Ensure that the user has privileges
		if (!EB::isSiteAdmin() && !$this->acl->get('feature_entry') ) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$type 	= $this->input->get('type', '', 'word');
		$id 	= $this->input->get('id', '', 'int');

		if ($type == 'blogger') {
			$title 	 = JText::_('COM_EASYBLOG_UNFEATURE_AUTHOR_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_UNFEATURE_AUTHOR_DIALOG_CONTENT');
		}

		if ($type == 'teamblog') {
			$title 	 = JText::_('COM_EASYBLOG_UNFEATURE_TEAMBLOG_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_UNFEATURE_TEAMBLOG_DIALOG_CONTENT');
		}

		$model 	= EB::model('Featured');
		$model->removeFeatured($type, $id);

		$theme 	= EB::template();

		$theme->set('title', $title);
		$theme->set('content', $content);

		$output = $theme->output('site/featured/dialog.unfeature');

		return $ajax->resolve($output);
	}


	/**
	 * Remove an item as featured
	 *
	 * @param	string	$type	The type of this item
	 * @param	int		$postId	The unique id of the item
	 *
	 * @return	string	Json string
	 **/
	function removeFeaturedx($type, $postId)
	{
	    $ajax	= new Ejax();
	    $acl 	= EB::acl();
	    EasyBlogHelper::removeFeatured($type, $postId);

	    $idName 	= '';
	    $message    = '';
	    switch($type)
	    {
	        case 'blogger':
	            $idName 	= '#blogger_title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_BLOGGER_UNFEATURED');
	            break;
	        case 'teamblog':
	            $idName 	= '#teamblog_title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_TEAMBLOG_UNFEATURED');
	            break;
	        case 'post':
	        default:
	            $idName 	= '#title_' . $postId;
	            $message    = JText::_('COM_EASYBLOG_BLOG_UNFEATURED');
	            break;
	    }

		$ajax->script('$("' . $idName .'").removeClass("featured-item");');
	    $ajax->alert( $message, JText::_('COM_EASYBLOG_INFO') , '450', 'auto');
	    $ajax->send();
	    return;
	}
}
