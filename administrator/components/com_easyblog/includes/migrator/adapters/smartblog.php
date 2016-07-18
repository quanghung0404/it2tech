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

class EasyBlogMigratorSmartblog extends EasyBlogMigratorBase
{
	public function migrate($migrateComment, $migrateImage, $imagePath)
	{
		$session = JFactory::getSession();

		//check if com_blog installed.
		if (!JFile::exists(JPATH_ROOT .  '/components/com_blog/blog.php')) {
			return $this->ajax->resolve('notinstalled');
		}

		// statistic
		$migrateStat = $session->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if (empty($migrateStat)) {
			$migrateStat = new stdClass();
			$migrateStat->blog = 0;
			$migrateStat->category = 0;
			$migrateStat->comments = 0;
			$migrateStat->images = 0;
			$migrateStat->user = array();
		}

		// Get total numbers of item
		$total = $this->getTotalItems();

		// Get items to be migrated
		$items = $this->getItems();

		// Determine if there is still items to be migrated
		$balance = $total - count($items);

		// If there is nothing to load just skip this
		if (!$items) {
			return $this->ajax->resolve('0');
		}

		foreach ($items as $item) {

			// here we should process the migration
			// step 1 : create user if not exists in eblog_users - create user through profile jtable load method.
			// step 2 : migrate image files.
			//      step 2.1: create folder if not exist.
			// step 3: migrate comments if needed.

			$date = EB::date();
			$blogObj = new stdClass();

			//load user profile
			$profile = EB::user($item->user_id);

			$categoryId = 1; //assume 1 is the uncategorized id.

			// Assign category to the blog obj
			$blogObj->category_id = $categoryId;

			// this is needed because post lib actually use this to create the post - category relations.
			$blogObj->categories = array($categoryId);

			//assigning blog data
			$blogObj->created_by = $profile->id;
			$blogObj->created = !empty($item->post_date)? $item->post_date:$date->toMySQL();
			$blogObj->modified = !empty($item->post_update)? $item->post_update:$date->toMySQL();
			$blogObj->title = $item->post_title;
			$blogObj->permalink = $item->post_title; // post lib will take care of the normalization of permalink
			$blogObj->intro = '';
			$blogObj->content = $item->post_desc;
			$blogObj->published = $item->published;
			$blogObj->publish_up = !empty($item->post_date )? $item->post_date : $date->toMySQL();
			$blogObj->publish_down = '0000-00-00 00:00:00';
			$blogObj->hits = $item->post_hits;
			$blogObj->frontpage = 1;
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

			//step 2: Migrate image
			$imageMigrated  = false;

			if ($migrateImage) {
				$imageMigrated	= $this->migrateImage($item);
			}

			if ($imageMigrated) {
				$destSafeURL = str_ireplace(DIRECTORY_SEPARATOR, '/',  $imagePath);
				$destSafeURL = 'images/' . $destSafeURL . '/' . $item->post_image;
				$imageContent = '<p><img style="padding:0px 10px 10px 0px;" align="left" src="' . $destSafeURL. '" border="0" /> </p>';
				$post->content = $imageContent . $post->content;
				$migrateStat->images++;
			}

			$post->save($saveOptions);

			// step 3: Migrate comment
			if ($migrateComment) {

				// Get blog comment
				$resultComment = $this->getComment($item);

				if (count($resultComment) > 0) {
					foreach ($resultComment as $itemComment) {
						// Store comment
						$this->storeComment($itemComment, $post);

						//update state
						$migrateStat->comments++;
					}
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
			$migratorTable->component = 'com_blog';
			$migratorTable->store();

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_SMARTBLOG') . ': ' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ' :' . $post->id . '<br />');
		}

		//TODO: get balance
		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		if (!$hasmore) {
			$stat = JText::_('COM_EASYBLOG_MIGRATOR_SMARTBLOG_TOTAL_ARTICLE') . ' : ' . $migrateStat->blog . '<br />';
			$stat .= JText::_('COM_EASYBLOG_MIGRATOR_SMARTBLOG_TOTAL_CATEGORY') . ' : ' . $migrateStat->category . '<br />';

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$this->ajax->append('[data-progress-stat]', $stat);

			// we need to clear the stat variable that stored in session.
			$jSession = JFactory::getSession();
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		}

		return $this->ajax->resolve($hasmore);
	}

	public function getTotalItems()
	{
		$query = 'SELECT COUNT(1) FROM `#__blog_postings` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_blog');
		$query .= ' )';
		$query .= ' ORDER BY a.`id`';

		$this->db->setQuery($query);

		$total = $this->db->loadResult();

		return $total;
	}

	public function getItems($limit = 10)
	{
		$query = 'SELECT * FROM `#__blog_postings` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_blog');
		$query .= ' )';
		$query .= ' ORDER BY a.`id`';

		$this->db->setQuery($query.' LIMIT '.$limit);

		$items = $this->db->loadObjectList();

		return $items;
	}

	public function migrateImage($item)
	{
		$newImagePath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images';
	    if (!empty($imagePath)) {
	        $tmpimagePath = str_ireplace('/', DIRECTORY_SEPARATOR,  $imagePath);
	        $newImagePath .= DIRECTORY_SEPARATOR . $tmpimagePath;
	        $newImagePath = JFolder::makeSafe($newImagePath);
	    }

	    if (!JFolder::exists($newImagePath)) {
	        JFolder::create($newImagePath);
	    }

	    $src = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_blog' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'blogimages' . DIRECTORY_SEPARATOR . 'th'.$item->post_image;
	    $dest = $newImagePath . DIRECTORY_SEPARATOR . $item->post_image;

	    if (JFile::exists($src)) {
	        $imageMigrated	= JFile::copy($src, $dest);
	    }

	    return $imageMigrated;
	}

	public function getComment($item)
	{
		$queryComment = 'SELECT * FROM `#__blog_comment` WHERE `post_id` = ' . $this->db->Quote($item->id);
		$queryComment .= ' ORDER BY `id`';
		$this->db->setQuery($queryComment);
		$resultComment = $this->db->loadObjectList();

		return $resultComment;
	}

	public function storeComment($itemComment, $blog)
	{
		$model = EB::model('Comment');

		//load user profile
		$commentor = EB::user($itemComment->user_id);

		$user = JFactory::getUser($itemComment->user_id );

		$now = EB::date();
		$commt = EB::table('Comment');
		$commt->post_id = $blog->id;
		$commt->comment = $itemComment->comment_desc;
		$commt->title = $itemComment->comment_title;
		$commt->name = $user->name;
		$commt->email = $user->email;
		$commt->url = $commentor->url;
		$commt->created_by = $itemComment->user_id;
		$commt->created = $itemComment->comment_date;
		$commt->published = $itemComment->published;

		//adding new comment
		$latestCmmt	= $model->getLatestComment($blog->id, '0');
		$lft = 1;
		$rgt = 2;

		if (!empty($latestCmmt)) {
		 	$lft = $latestCmmt->rgt + 1;
		 	$rgt = $latestCmmt->rgt + 2;

		 	$model->updateCommentSibling($blog->id, $latestCmmt->rgt);
		}

		$commt->lft = $lft;
		$commt->rgt = $rgt;
		$commt->store();
	}
}
