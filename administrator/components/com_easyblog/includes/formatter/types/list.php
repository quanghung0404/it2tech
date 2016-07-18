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

class EasyBlogFormatterList extends EasyBlogFormatterStandard
{
	/**
	 * Default method to format normal posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute()
	{
		// Result
		$result = array();

		// Cache data and preload posts
		if ($this->cache) {
			EB::cache()->insert($this->items);
		}

		// Preload all featured posts for posts
		$featuredItems = array();

		// Do a simple test to see if preload featured items is require or not
		if (!isset($this->items[0]->featured)) {
			$featuredItems = $this->preloadFeaturedItems();
		}

		$i = 0;

		foreach ($this->items as $item) {

			// Load up the post library
			$post = EB::post();
			$post->load($item->id);

			// Get the list of categories for this particular blog post
			$post->category = $post->getPrimaryCategory();
			$post->categories = $post->getCategories();

			// @Assign dynamic properties that must exist everytime formatBlog is called
			// We can't rely on ->author because CB plugins would mess things up.
			$post->author = $post->getAuthor();

			// Determines if the blog post is featured
			if (isset($item->featured)) {
				$post->isFeatured = $item->featured;
			} else if (isset($featuredItems[$post->id])) {
				$post->isFeatured = $featuredItems[$post->id];
			} else {
				$post->isFeatured = 0;
			}

			// Password verifications
			$this->password($post);

			// Get custom fields
			$post->fields = $post->getCustomFields();

			// Format microblog postings
			if ($post->posttype) {
				$this->formatMicroblog($post);
			} else {
				$post->posttype = 'standard';
			}

			// Assign tags to the custom properties.
			$post->tags = $post->getTags();

			// Prepare nice date for the list
			$post->date	= EB::date($post->created)->format(JText::_('DATE_FORMAT_LC'));

			$result[] = $post;

			$i++;
		}

		return $result;
	}

}
