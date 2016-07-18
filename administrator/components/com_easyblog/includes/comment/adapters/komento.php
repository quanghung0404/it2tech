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

require_once(__DIR__ . '/base.php');

class EasyBlogCommentKomento extends EasyBlogCommentBase
{
	/**
	 * Determines if Komento exists on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_komento/bootstrap.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Renders the comment form 
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html(EasyBlogPost &$blog)
	{
		if (!$this->exists()) {
			return;
		}

		$output = Komento::commentify('com_easyblog', $blog, array('trigger'=>'onDisplayComments'));

		return $output;
	}

	/**
	 * Renders the comment count for Komento
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCount(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return;
		}

		$model = Komento::getModel('Comments');
		$count = $model->getCount('com_easyblog', $post->id);

		return $count;
	}
}
