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

class EasyBlogMigratorZoo extends EasyBlogMigratorBase
{
	public function migrate($applicationId)
	{
		// Render Joomla's session
		$session = JFactory::getSession();

		// Statistics
		$migrateStat = $session->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if (empty($migrateStat)) {
			$migrateStat = new stdClass();
			$migrateStat->blog = 0;
			$migrateStat->category = 0;
			$migrateStat->user = array();
		}

		// Get the total number of items
		$total = $this->getTotalItems($applicationId);

		// Get the real item to be migrated
		$items = $this->getItems($applicationId);

		// Determines if there us still items to be migrated
		$balance = $total - count($items);

		// If there is no item to migrate, just skip this.
		if (!$items) {
			return $this->ajax->resolve('noitem');
		}

		foreach ($items as $item) {

			// Create a new blog object
			$blogObj = new stdClass();

			$date = EB::date();

			// Create category if this item's category does not exist on the site
			$categoryId = $this->migrateCategory($item);

			// Assign the new category to the blog object
			$blogObj->category_id = $categoryId;

			// this is needed because post lib actually use this to create the post - category relations.
			$blogObj->categories = array($categoryId);

			//load user profile
			$profile = EB::user($item->created_by);

			$elements = $item->elements;
			$elementObj = json_decode($elements,true);

			$title = $item->name;

			$contentElement = $elementObj['2e3c9e69-1f9e-4647-8d13-4e88094d2790'];

			$content = $contentElement['0']['value'];

			if (isset($contentElement['1'])) {
				$content .= $contentElement['1']['value'];
			}

			// Get item meta data
			$params = $item->params;
			$paramsObj = json_decode($params,true);
			$metakey = $paramsObj['metadata.keywords'];
			$metadesc = $paramsObj['metadata.description'];

			//assigning blog data
			$blogObj->created_by = $profile->id;
			$blogObj->created = !empty($item->created)? $item->created : $date->toMySQL();
			$blogObj->modified = $date->toMySQL();
			$blogObj->title = $title;
			$blogObj->permalink = $item->alias; // post lib will take care of the normalization of permalink
			$blogObj->intro = $content;

			// translating the article state into easyblog publish status.
			$blogState = ($item->state == 2 || $item->state == -2) ? 0 : $item->state;
			$blogObj->published = $blogState;

			$blogObj->publish_up = !empty($item->publish_up)? $item->publish_up : $date->toMySQL();
			$blogObj->publish_down = !empty($item->publish_down)? $item->publish_down : $date->toMySQL();
			$blogObj->hits = $item->hits;
			$blogObj->frontpage = 1;

			$blogObj->keywords = $metakey;
            $blogObj->description = $metadesc;
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



			// //Migrate over the images
			$imagePath = $elementObj['c26feca6-b2d4-47eb-a74d-b067aaae5b90']['file'];
			$this->migrateZooImages($imagePath, $post, $profile);

			$post->save($saveOptions);

			//Get Zoo Tags and map into Easyblog Tags
			$zooTags = $this->getZooTag($item->id);

			if ($zooTags) {
				foreach ( $zooTags as $tag ) {
					$this->mapZooTag($tag, $item, $post);
				}
			}

			//update session value
			$migrateStat->blog++;
			$statUser = $migrateStat->user;
			$statUserObj = null;

			if (!isset($statUser[$profile->id])) {
			    $statUserObj = new stdClass();
			    $statUserObj->name = $profile->nickname;
			    $statUserObj->blogcount = 0;
			}
			else {
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
			$migratorTable->component = 'com_zoo';
			$migratorTable->store();

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_ZOO') . ': ' . $item->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $post->id . '<br />');

		}

		$hasmore = 'success';

		if ($balance) {
			$hasmore = 'next';
		}

		if ($hasmore == 'success') {
			$stat = JText::_('COM_EASYBLOG_MIGRATOR_ZOO_TOTAL_ARTICLE') . ' : ' . $migrateStat->blog . '<br />';
			$stat .= JText::_('COM_EASYBLOG_MIGRATOR_ZOO_TOTAL_CATEGORY') . ' : ' . $migrateStat->category . '<br />';

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$this->ajax->append('[data-progress-stat]', $stat);

			// we need to clear the stat variable that stored in session.
			$jSession = JFactory::getSession();
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		}

		return $this->ajax->resolve($hasmore);
	}

	public function getTotalItems($applicationId = null)
	{
		$query = 'SELECT COUNT(1) FROM `#__zoo_item` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_zoo');
		$query .= ' )';
		$query .= ' AND a.`application_id` = ' . $this->db->Quote($applicationId);
		$query .= ' AND a.`type` = ' . $this->db->Quote('article');

		$this->db->setQuery($query);

		$total = $this->db->loadResult();

		return $total;
	}

	public function getItems($applicationId = null, $limit = 10)
	{
		$query = 'SELECT * FROM `#__zoo_item` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_zoo');
		$query .= ' )';
		$query .= ' AND a.`application_id` = ' . $this->db->Quote($applicationId);
		$query .= ' AND a.`type` = ' . $this->db->Quote('article');


		$query .= ' ORDER BY a.`id`';

		$this->db->setQuery($query.' LIMIT '.$limit);

		$items = $this->db->loadObjectList();

		return $items;
	}

	public function migrateCategory($item)
	{
		$params = $item->params;
		$paramsObj = json_decode($params,true);
		$catId = $paramsObj['config.primary_category'];
		// By default, the category id is 1 because EasyBlog uses the first category as uncategorized
		$default = 1;

		// If there's no category assigned in this item
		if (!$catId) {
			return $default;
		}

		// Get Zoo's category
		$zooCategory = $this->getZooCategory($catId);

		// Determine if this category has already been created in EasyBlog
		$easyblogCategoryId = $this->easyblogCategoryExists($zooCategory);

		return $easyblogCategoryId;
	}

	public function getZooCategory($id)
	{
		$query = 'SELECT * FROM `#__zoo_category` where `id` = ' . $this->db->Quote($id);
		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		// Mimic Joomla's category behavior
		if ($result) {
			$result->title = $result->name;
		}

		return $result;
	}

	public function getZooTag($itemId)
	{
		$query = 'SELECT * FROM #__zoo_tag '
				. 'WHERE `item_id`=' . $this->db->Quote( $itemId );
		$this->db->setQuery($query);
		$result	= $this->db->loadObjectList();

		return $result;
	}

	public function mapZooTag($tag, $item, $blog)
	{
		$now = EB::date();
		$tableTag = EB::table('Tag');

		if ($tableTag->exists($tag->name)) {
			$tableTag->load($tag->name, true);
		} else {
			$tagArr = array();
		    $tagArr['created_by'] = $this->getDefaultSuperUserId();
		    $tagArr['title'] = $tag->name;
		    $tagArr['alias'] = $tag->name;
		    $tagArr['published'] = '1';
		    $tagArr['created'] = $now->toMySQL();

            $tableTag->bind($tagArr);
		    $tableTag->store();
		}

		$postTag = EB::table('PostTag');
		$postTag->tag_id = $tableTag->id;
		$postTag->post_id = $blog->id;
		$postTag->created = $now->toMySQL();
		$postTag->store();
	}

	public function migrateZooImages( $path , &$blog , $author )
	{
		jimport('joomla.filesystem.file');

		$path = JPATH_ROOT . '/' . $path;
		$config = EB::getConfig();
		$configStorage = str_ireplace( '\\' , '/' , $config->get( 'main_image_path' ) );
		$newPath = JPATH_ROOT . '/' . rtrim( $configStorage , '/' ) . '/' . $author->id;

		if (!JFolder::exists($newPath)) {
			JFolder::create($newPath);
		}

		if (JFile::exists($path)) {
			// Copy the full scaled image
			$large = $path;
			$targetURL = rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $configStorage ) . '/' . $author->id;

			$file = getimagesize($large);

			$file['name'] = basename($large);
			$file['tmp_name'] = $large;
			$file['type'] = $file['mime'];

			$result = EB::mediamanager()->upload($file, 'user:' . $author->id);

			if (isset($result->type)) {
				$relativeImagePath = $newPath . '/' . $file['name'];
				$result->path = $relativeImagePath;
			}

			$result = json_encode($result);

			$blog->image = $result;
		}
	}

}
