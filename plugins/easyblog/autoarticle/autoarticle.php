<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.plugin.plugin' );

class plgEasyBlogAutoArticle extends JPlugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Tests if EasyBlog exists
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			$exists = JFile::exists($file);

			if ($exists) {
				require_once($file);
			}
		}

		return $exists;
	}

	/*
	 * Run some cleanup after a blog post is deleted
	 *
	 * @param   $blog   TableBlog   The blog table.
	 * @return  null
	 */
    public function onAfterEasyBlogDelete($blog)
    {
    	if (! $this->exists()) {
    		return;
    	}

    	// Get plugin info
    	$plugin			= JPluginHelper::getPlugin('easyblog', 'autoarticle');
    	$pluginParams 	= EB::registry($plugin->params);

    	if ($pluginParams->get('unpublish') == '1') {

	        $db     = EB::db();
	        $query  = 'SELECT * FROM `#__easyblog_autoarticle_map` WHERE `post_id`=' . $db->Quote( $blog->id );
	        $db->setQuery( $query );
	        $map	= $db->loadObject();

			if( $map )
			{
		        $query  = 'UPDATE `#__content` SET `state`=' . $db->Quote( 0 ) . ' '
		                . 'WHERE `id`=' . $db->Quote( $map->content_id );
				$db->setQuery( $query );
				$db->Query();

		        $query  = 'DELETE FROM `#__content_frontpage` '
		                . 'WHERE `content_id`=' . $db->Quote( $map->content_id );
				$db->setQuery( $query );
				$db->Query();
			}
		}
	}

    public function onAfterEasyBlogSave($post, $isNew)
    {
    	if (! $this->exists()) {
    		return;
    	}

    	if (! $post->isPublished()) {
    		return;
    	}

		$db		= EasyBlogHelper::db();
		$user   = JFactory::getUser();

    	// Get plugin info
    	$plugin = JPluginHelper::getPlugin('easyblog', 'autoarticle');
    	$pluginParams = EB::registry($plugin->params);

        // easyblog blog details
		$data   = array();
		$data['title'] = $post->title;
		$data['alias'] = $post->permalink;


		// if (empty($post->intro)) {
		// 	$data['introtext'] = $post->content;
		// 	$data['fulltext'] = '';
		// } else {
		// 	$data['introtext'] = $post->intro;
		// 	$data['fulltext'] = $post->content;
		// }

		$data['introtext'] = $post->getContent('entry');
		$data['fulltext'] = '';

		$EasyBlogitemId = EBR::getItemId( 'latest' );
		$readmoreURL = EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $post->id . '&Itemid=' . $EasyBlogitemId);
		$readmoreURL = str_replace('/administrator/', '/', $readmoreURL);

		$readmoreLink       = '<a href="' . $readmoreURL . '" class="readon"><span>' . JText::_( 'Read More' ) . '</span></a>';
		$data['introtext']  = $data['introtext'] . '<br />' . $readmoreLink;

		$data['created']		= $post->created;
		$data['created_by']		= $post->created_by;
		$data['modified']		= $post->modified;
		$data['modified_by']	= $user->id;
		$data['publish_up']		= $post->publish_up;
		$data['publish_down']	= $post->publish_down;

		//these four get from plugin params
		$state		= $pluginParams->get('status');
		$access		= 1;


		if ($pluginParams->get('access', '-1') == '-1') {
			$access = ( $post->access ) ? 2 : 1;
		} else {
			$tmpAccess  = $pluginParams->get('access');

			switch ($tmpAccess) {
				case '1':
					$access = '2';
					break;
				case '2':
					$access = '3';
					break;
				case '0':
				default:
					$access = '1';
					break;
			}
		}

		$section = '0';
		$category = $pluginParams->get('sectionCategory', '0');
		$frontpage = ($pluginParams->get('frontpage', '-1') == '-1') ? $post->frontpage : $pluginParams->get('frontpage', '0');
		$autoMapCategory = $pluginParams->get('autocategory', '0');

		if ($autoMapCategory) {
			$autoMapped = self::mapCategory( $post->category_id );

			if (!empty($autoMapped->cid)) {
			    $category	= $autoMapped->cid;
			}
		}

		$data['state'] = $state;
		$data['access'] = $access;
		$data['sectionid'] = $section;
		$data['catid'] = $category;

		$data['metakey'] = JRequest::getVar('keywords', '');;
		$data['metadesc'] = JRequest::getVar('description', '');

		$contentMap = EB::table('AutoArticleMap');
		$joomlaContent = JTable::getInstance('content');

		$aid    = '';

		// try to get the existing content id via the mapping table
		$contentMap->load( $post->id, true);

		if( !empty($contentMap->content_id)) {
		    $aid = $contentMap->content_id;
		}

		if (empty($aid) && !empty($post->permalink)) {
			//try to get if the article already inserted before based on title alias.
			$query  = 'SELECT `id` FROM `#__content` WHERE `alias` = ' . $db->Quote($post->permalink);
			$db->setQuery($query);
			$aid = $db->loadResult();
		}

		if (! empty($aid)) {
		    $joomlaContent->load($aid);
		}

		$joomlaContent->bind($data);

		// Convert the params field to an array.
		$registry = new JRegistry;
		$joomlaContent->attribs = $registry->toArray();

		$joomlaContent->store();
        $articleId  = $joomlaContent->id;

		if (is_null($isNew)) {
		    // something wrong here. test the aid to determine.
		    if (empty($aid)) {
		        $isNew = true;
		    } else {
		        $isNew  = false;
		    }
		}

        if ($isNew && !empty($articleId)) {
			// if saved ok, then insert the mapping into our map table.
			$jdate 	= EB::date();
			$map    = array();
			$map['content_id'] = $articleId;
			$map['post_id'] = $post->id;
			$map['created'] = $jdate->toMySQL();

			$contentMap->bind($map);
			$contentMap->store();
		}

		if ($isNew && $frontpage) {

			JTable::addIncludePath(JPATH_ADMINISTRATOR .'/components/com_content/tables');
			$table = JTable::getInstance('Featured', 'ContentTable');

			// Insert the new entry
			$query = 'INSERT INTO `#__content_frontpage`' .
					' VALUES ( '. (int) $articleId .', 1 )';
			$db->setQuery($query);
			$db->query();

			// we require the table object so that we can reorder the ordering column.
			// reorder featured table ordering
			$table->reorder();
		}

		$cache = JFactory::getCache('com_content');
		$cache->clean();
    }

	public function mapCategory( $eb_catid )
	{
		$db     = EasyBlogHelper::db();

		$data   = new stdClass();
		$data->sid    = '0';
		$data->cid    = '0';

		$category	= EB::table('Category');
		$category->load($eb_catid);

		//get joomla section
		$query	= 'SELECT cc.id as `catid`';
		$query	.= ' FROM #__categories AS cc';
		$query  .= ' WHERE cc.`title` = ' . $db->Quote($category->title);
		$db->setQuery($query);

		$section    = $db->loadObject();
		if (count($section) > 0) {
		    $data->cid	= $section->catid;
		}

		return $data;
	}


}
