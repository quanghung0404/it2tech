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

require_once(dirname(__FILE__) . '/base.php');

class EasyBlogMigratorContent extends EasyBlogMigratorBase
{
	public function migrate($authorId, $stateId, $catId, $sectionId, $ebcategory, $myblogSection , $jomcomment = false )
	{
		$catId = $catId == 0 ? null : $catId;

		$session = JFactory::getSession();

		$migrateStat = $session->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if (empty($migrateStat)) {
			$migrateStat = new stdClass();
			$migrateStat->blog = 0;
			$migrateStat->category = 0;
			$migrateStat->user = array();
		}

		// Get the total number of items
		$total = $this->getTotalItems($catId, $stateId, $authorId, $myblogSection, $sectionId);

		// Get the real items
		$items = $this->getItems($catId, $stateId, $authorId, $myblogSection, $sectionId);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		// If there's nothing to load just skip this
		if (!$items) {
			return $this->ajax->resolve('noitem');
		}

		foreach ($items as $item) {

			// Create a new blog object
			$blogObj = new stdClass();

			$date = EB::date();

			$categoryId= $ebcategory;

			if ($ebcategory == 0) {
				// Create category if this item's category does not exist on the site
				$categoryId = $this->migrateCategory($item);
			}

			// Assign the new category to the blog object
			$blogObj->category_id = $categoryId;

			// this is needed because post lib actually use this to create the post - category relations.
			$blogObj->categories = array($categoryId);

			// Create user if the user does not exist in `#__easyblog_users`
			$profile = EB::user($item->created_by);

			//assigning blog data
			$blogObj->created_by = $profile->id;
			$blogObj->created = !empty($item->created)? $item->created : $date->toMySQL();
			$blogObj->modified = $date->toMySQL();
			$blogObj->title = $item->title;
			$blogObj->permalink = $item->alias;

			$blogObj->intro = '';
			$blogObj->content = $item->introtext;

			if (!empty($item->fulltext)) {
				// If $item->fulltext exist, means there is system-readmore.
				$blogObj->intro = $item->introtext;
				$blogObj->content = $item->introtext. '<hr id="system-readmore" />' .$item->fulltext;
			}

			// joomla 3.0 intro imag properties.
			if (!empty($row->images)) {
				$joomlaImages = json_decode($row->images);

				if (!empty($joomlaImages->image_intro)) {

					$imgTag = '<img';

					if ($joomlaImages->image_intro_caption) {
						$imgTag .= ' class="caption" title="' . htmlspecialchars($joomlaImages->image_intro_caption) .'"';
					}

					$imgTag .= ' src="' . htmlspecialchars($joomlaImages->image_intro) . '" alt="' . htmlspecialchars($joomlaImages->image_intro_alt) . '"/>';

					$blogObj->intro = $imgTag . $blogObj->intro;
				}

				if (!empty($joomlaImages->image_fulltext)) {
					$imgTag = '<img';

					if ($joomlaImages->image_fulltext_caption) {
						$imgTag .= ' class="caption" title="' . htmlspecialchars($joomlaImages->image_fulltext_caption) .'"';
					}

					$imgTag .= ' src="' . htmlspecialchars($joomlaImages->image_fulltext) . '" alt="' . htmlspecialchars($joomlaImages->image_fulltext_alt) . '"/>';

					$blogObj->content = $imgTag . $blogObj->content;
				}

			}

			// Need to remap the access.
			$access = ($item->access > 1) ? 1 : 0;
			$blogObj->access = $access;

			// translating the article state into easyblog publish status.
			$blogState = ($item->state == 2 || $item->state == -2) ? 0 : $item->state;
			$blogObj->published = $blogState;
			$blogObj->publish_up = !empty($item->publish_up)? $item->publish_up : $date->toMySQL();
			$blogObj->publish_down = !empty( $item->publish_down )? $item->publish_down : $date->toMySQL();
			$blogObj->ordering = $item->ordering;
			$blogObj->hits = $item->hits;
			$blogObj->frontpage = 1;
            $blogObj->keywords = $item->metakey;
            $blogObj->description = $item->metadesc;
            $blogObj->eb_language = $item->language;
            $blogObj->posttype = '';
            $blogObj->source_id = '0';
            $blogObj->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

			// lets create blank post which are legacy type.
			$post = EB::post();
            $post->create(array('overrideDoctType' => 'legacy'));

            // now let get the uid
            $blogObj->uid = $post->uid;
            $blogObj->revision_id = $post->revision->id;

            // binding
			$post->bind($blogObj, array());

            $saveOptions = array(
                            'applyDateOffset' => false,
                            'validateData' => false,
                            'useAuthorAsRevisionOwner' => true
                            );

			$post->save($saveOptions);

			// Migrate the tags now
			$this->migrateContentTags($post, $item);

			// Run jomcomment migration here.
			if ($jomcomment) {
				$this->migrateJomcomment($item->id, $post->id, 'com_content');
			}

			//Migrate meta description
			// $this->migrateContentMeta($item->metakey, $item->metadesc, $post->id);

			// if article was featured, lets mark this blog post as featured as well.
		    if ($item->featured) {
		      	// just call the model file will do as we do not want to create stream on featured action at this migration.
				$modelFeatured = EB::model('Featured');
				$modelFeatured->makeFeatured('post', $post->id);
		    }

			//update session value
			$migrateStat->blog++;
			$statUser = $migrateStat->user;
			$statUserObj = null;

			if (!isset($statUser[$profile->id])) {
			    $statUserObj = new stdClass();
			    $statUserObj->name = $profile->nickname;
			    $statUserObj->blogcount = 0;
			} else {
			    $statUserObj = $statUser[$profile->id];
			}

			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user = $statUser;

			$session->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');

			//log the entry into migrate table.
			$migratorTable = EB::table('Migrate');

			$migratorTable->content_id = $item->id;
			$migratorTable->post_id = $post->id;
			$migratorTable->session_id = $session->getToken();
			$migratorTable->component = 'com_content';
			$migratorTable->store();

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_JOOMLA_ARTICLE') . ': ' . $item->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $post->id . '<br />');
		}

		//Get balance if any
		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		if (!$hasmore) {
			$stat  = JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_TOTAL_ARTICLE') . ' : ' . $migrateStat->blog . '<br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_TOTAL_CATEGORY') . ' : ' . $migrateStat->category . '<br />';

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$this->ajax->append('[data-progress-stat]', $stat);

			// we need to clear the stat variable that stored in session.
			$jSession = JFactory::getSession();
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		}

		return $this->ajax->resolve($hasmore);
	}

	public function getTotalItems($categoryId = null, $stateId = '*', $authorId = 0, $myblogSection = '', $sectionId = '')
	{
		$query = 'SELECT COUNT(1) FROM `#__content` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_content');
		$query .= ' )';
		if ($authorId != '0') {
			$query .= ' AND a.`created_by` = ' . $this->db->Quote($authorId);
		}

		if ($stateId != '*') {
			switch($stateId)
			{
				case 'P':
					$query .= ' AND a.`state` = ' . $this->db->Quote('1');
					break;
				case 'U':
					$query .= ' AND a.`state` = ' . $this->db->Quote('0');
					break;
				case 'A':
					$query .= ' AND a.`state` = ' . $this->db->Quote('-1');
					break;

				// joomla 1.6 compatibility
				case '1': // publish
					$query .= ' AND a.`state` = ' . $this->db->Quote('1');
					break;
				case '0': //unpublish
					$query .= ' AND a.`state` = ' . $this->db->Quote('0');
					break;
				case '2': // archive
					$query .= ' AND a.`state` = ' . $this->db->Quote('2');
					break;
				case '-2': // trash
					$query .= ' AND a.`state` = ' . $this->db->Quote('-2');
					break;

				default:
					break;
			}
		}

		// we do not want the myblog post process here.
		if ($myblogSection != '') {
			$query .= ' AND a.`sectionid` != ' . $this->db->Quote($myblogSection);
		}

		if ($categoryId != null) {
			$query .= ' AND a.`catid` = ' . $this->db->Quote($categoryId);
		}

		$query .= ' ORDER BY a.`id`';

		$this->db->setQuery($query);

		$total  = $this->db->loadResult();

		return $total;
	}

	public function getItems($categoryId = null, $stateId = '*', $authorId = 0, $myblogSection = '', $sectionId = '', $limit = 10)
	{
		$query = 'SELECT * FROM `#__content` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_content');
		$query .= ' )';
		if ($authorId != '0') {
			$query .= ' AND a.`created_by` = ' . $this->db->Quote($authorId);
		}

		if($stateId != '*') {
			switch($stateId)
			{
				case 'P':
					$query .= ' AND a.`state` = ' . $this->db->Quote('1');
					break;
				case 'U':
					$query .= ' AND a.`state` = ' . $this->db->Quote('0');
					break;
				case 'A':
					$query .= ' AND a.`state` = ' . $this->db->Quote('-1');
					break;

				// joomla 1.6 compatibility
				case '1': // publish
					$query .= ' AND a.`state` = ' . $this->db->Quote('1');
					break;
				case '0': //unpublish
					$query .= ' AND a.`state` = ' . $this->db->Quote('0');
					break;
				case '2': // archive
					$query .= ' AND a.`state` = ' . $this->db->Quote('2');
					break;
				case '-2': // trash
					$query .= ' AND a.`state` = ' . $this->db->Quote('-2');
					break;

				default:
					break;
			}
		}

		// we do not want the myblog post process here.
		if ($myblogSection != '') {
			$query .= ' AND a.`sectionid` != ' . $this->db->Quote($myblogSection);
		}

		if ($categoryId != null) {
			$query .= ' AND a.`catid` = ' . $this->db->Quote($categoryId);
		}

		$query .= ' ORDER BY a.`id`';

		$query .= ' LIMIT ' . $limit;

		$this->db->setQuery($query);

		$items  = $this->db->loadObjectList();

		return $items;
	}

	public function migrateCategory($item)
	{
		// By default, the category id is 1 because EasyBlog uses the first category as uncategorized
		$default = 1;

		// If there's no category assigned in this item
		if (empty($item->catid)) {
			return $default;
		}

		// Get content's category
		$JoomlaCategory = $this->getJoomlaCategory($item->catid);

		// Determine if this category has already been created in EasyBlog
		$easyblogCategoryId = $this->easyblogCategoryExists($JoomlaCategory);

		return $easyblogCategoryId;
	}

	public function getJoomlaCategory($catId)
	{
		$query  = 'select * from `#__categories` where `id` = ' . $this->db->Quote($catId);
	    $this->db->setQuery($query);
	    $result = $this->db->loadObject();

	    return $result;
	}

	/**
	 * Migrates tags from Joomla articles into EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function migrateContentTags($blog, $article)
	{
		if (!EB::isJoomla30()) {
			return false;
		}
		
		$db = EB::db();

		$query = 'SELECT b.* FROM `#__contentitem_tag_map` AS a ';
		$query .= 'INNER JOIN ' . $db->nameQuote('#__tags') . ' AS b ';
		$query .= 'ON a.' . $db->nameQuote('tag_id') . ' = b.' . $db->nameQuote('id') . ' ';
		$query .= 'WHERE a.`content_item_id` = ' . $db->Quote($article->id);
		
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return;
		}

		$date = EB::date();

		foreach ($result as $row) {

			// Create a new tag in EasyBlog if it doesn't exist yet.
			$tag = EB::table('Tag');
			$exists = $tag->load($row->title, true);

			// If the tag does not exist, create it first
			if (!$exists) {

				$tag->title = $row->title;
				$tag->alias = $row->alias;
				$tag->published = $row->published;
				$tag->created_by = $row->created_user_id;

				// Create the tag
				$tag->store();
			}

			// If the tag exists, just associate the tag id with the post id.
			$relation = EB::table('PostTag');
			$relation->tag_id = $tag->id;
			$relation->post_id = $blog->id;
			$relation->created = $date->toMySQL();
			$relation->store();
		}
	}	
}
