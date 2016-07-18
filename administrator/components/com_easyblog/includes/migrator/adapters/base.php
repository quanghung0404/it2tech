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

class EasyBlogMigratorBase
{
	public function __construct()
	{
		$this->db = EB::db();
		$this->ajax = EB::ajax();
	}

	public function easyblogCategoryExists($category)
	{
		$title = JString::strtolower($category->title);
		$alias = JString::strtolower($category->alias);

		$query = 'select `id` from `#__easyblog_category`';
		$query .= ' where lower(`title`) = ' . $this->db->Quote($title);
		$query .= ' OR lower(`alias`) = ' . $this->db->Quote($alias);
		$query .= ' LIMIT 1';

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		// If easyblog category doesn't exist, create a new category using K2's category data
		if (!$result) {
			$result = $this->createEasyBlogCategory($category);
		}

		return $result;
	}

	public function getEasyblogCategory()
	{
		$query = 'select `id` from `#__easyblog_category`';
		$query .= ' where (`published`) = ' . $this->db->Quote('1');
		$query .= ' LIMIT 1';

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		return $result;
	}

	public function createEasyBlogCategory($categoryObject)
	{
		$session = JFactory::getSession();
		$stats = $session->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if (empty($stats)) {
			$stats			= new stdClass();
			$stats->blog	= 0;
			$stats->category= 0;
			$stats->user	= array();
		}

		$category = EB::table('Category');

		$category->title = $categoryObject->title;
		$category->alias = JString::strtolower($categoryObject->alias);

		// If k2 did not define the category publishing state, default it to enabled.
		$category->published = !isset($categoryObject->published) ? true : $categoryObject->published;

		// Set the creator of the category
		$category->created_by = $this->getDefaultSuperUserId();

		// Now, try to save the category
		$category->store();

		// Update the total count of category that has been migrated
		$stats->category++;
		$session->set('EBLOG_MIGRATOR_JOOMLA_STAT', $stats, 'EASYBLOG');

		return $category->id;
	}


	public function getDefaultSuperUserId()
	{
		$saUserId = '62';
		if (EB::getJoomlaVersion() >= '1.6') {

			$saUsers = EB::getSAUsersIds();

			$saUserId = '42';

			if (count($saUsers) > 0) {
				$saUserId = $saUsers['0'];
			}
		}

		return $saUserId;
	}

	public function migrateContentMeta($metaKey, $metaDesc, $blogId)
	{
		if (empty($metaKey) && empty($metaDesc)) {
			return true;
		}

		$meta = EB::table('Meta');
		$meta->keywords = $metaKey;
		$meta->description = $metaDesc;
		$meta->content_id = $blogId;
		$meta->type = 'post';
		$meta->store();

		return true;
	}

	public function migrateJomcomment( $contentId , $blogId , $option )
	{
		$query = 'SELECT * FROM ' . $this->db->nameQuote( '#__jomcomment' ) . ' '
				. 'WHERE ' . $this->db->nameQuote( 'contentid' ) . ' = ' . $this->db->Quote( $contentId ) . ' '
				. 'AND ' . $this->db->nameQuote( 'option' ) . ' = ' . $this->db->Quote( $option ) . ' '
				. 'ORDER BY `id` ASC';

		$this->db->setQuery($query);
		$comments = $this->db->loadObjectList();


		if (!$comments) {
			return;
		}

		$lft = 1;
		$rgt = 2;

		foreach ($comments as $comment) {
			$post = array();

			$post['id'] = $blogId;
			$post['comment'] = $comment->comment;
			$post['name'] = $comment->name;
			$post['title'] = $comment->title;
			$post['email'] = $comment->email;
			$post['url'] = $comment->website;

			$table = EB::table('Comment');
			$table->bindPost($post);

			//the rest info assign here.
			$table->lft = $lft;
			$table->rgt = $rgt;
			$table->ip = $comment->ip;
			$table->created_by = $comment->user_id;
			$table->created = $comment->date;
			$table->modified = $comment->date;
			$table->published = $comment->published;
			$table->ordering = $comment->ordering;
			$table->vote = $comment->voted;

			$table->store();

			//do not touch this settings!
			$lft	= $rgt + 1;
			$rgt	= $lft + 1;
		}
	}

	public function checkXMLPostData( $fileName, $sourceName = 'post' )
	{
	    $jSession = JFactory::getSession();

	    $sessionId = $jSession->getToken();

	    $query = 'select * from `#__easyblog_xml_wpdata`';
 	    $query .= ' where `session_id` = ' . $this->db->Quote($sessionId);
 	    $query .= ' and `filename` = ' . $this->db->Quote($fileName);
	    $query .= ' and `source` = ' . $this->db->Quote( $sourceName );
	    $query .= ' order by `id`';

	    $this->db->setQuery($query);
	    $results = $this->db->loadObjectList();

	    if (!$results) {
	    	return false;
	    }

	    return true;
	}

	public function getXMLPostData( $fileName, $sourceName = 'post' )
	{
	    $jSession = JFactory::getSession();

	    $sessionId = $jSession->getToken();

	    $query = 'select * from `#__easyblog_xml_wpdata`';
 	    $query .= ' where `session_id` = ' . $this->db->Quote($sessionId);
 	    $query .= ' and `filename` = ' . $this->db->Quote($fileName);
	    $query .= ' and `source` = ' . $this->db->Quote( $sourceName );
	    $query .= ' order by `id` limit 10';

	    $this->db->setQuery($query);
	    $results = $this->db->loadObjectList();

	    $contentId  = '';

	    $posts = array();

		foreach ($results as $post) {
		    if (isset($post->post_id)) {
		        $contentId = $post->post_id;
		        $post->data = unserialize($post->data);

				if (!empty($post->comments)) {
					$post->comments = unserialize($post->comments);
				}
		    }

			$this->clearXMLData($fileName, $contentId);

			$posts[] = $post;

		}

	    return $posts;
	}

	public function clearXMLData( $fileName, $postid = '' )
	{
	    $jSession = JFactory::getSession();

	    $sessionId = $jSession->getToken();

		if ($postid === true) {
		    $query = 'delete from `#__easyblog_xml_wpdata`';
		    $query .= ' where `session_id` = ' . $this->db->Quote( $sessionId );
		    $query .= ' and `filename` = ' . $this->db->Quote($fileName);
			$query .= ' limit 1';
		} else {
		    $query = 'delete from `#__easyblog_xml_wpdata`';
		    $query .= ' where `session_id` = ' . $this->db->Quote( $sessionId );
		    $query .= ' and `filename` = ' . $this->db->Quote($fileName);

		    if (!empty($postid)) {
		    	$query .= ' and `post_id` = ' . $this->db->Quote($postid);
		    }
		}

	    $this->db->setQuery($query);
	    $this->db->query();

		return true;
	}

	public function logXMLData( $fileName,  $postId, $source,  $data, $comments = array() )
	{
	    $jSession 	= JFactory::getSession();

		//log the entry into migrate table.
		$xml = EB::table('Xmldata');

		$xml->post_id = $postId;
		$xml->session_id = $jSession->getToken();
		$xml->source = $source;
		$xml->filename = $fileName;
		$xml->data = serialize($data);
		$xml->comments = (! empty($comments) ) ? serialize($comments) : '';

		$xml->store();
	}

}
