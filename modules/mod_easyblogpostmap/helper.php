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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

jimport('joomla.system.file');
jimport('joomla.system.folder');

class modEasyBlogPostMapHelper
{
	static function getPosts(&$params)
	{
		$db = EB::db();
		$config = EB::config();
		$type = $params->get('type');
		
		$joinQuery = '';

		$headQuery = 'SELECT a.* FROM ' . $db->qn('#__easyblog_post') . ' as a';

		// select valid latitude/longitude or address
		$query = ' WHERE ((TRIM(a.' . $db->qn('latitude') . ') != ' . $db->quote('') . ' AND TRIM(a.' . $db->qn('longitude') . ') != ' . $db->quote('') . ')';
		$query .= ' OR TRIM(a.' . $db->qn('address') . ') != ' . $db->quote('') . ')';
		$query .= ' AND a.' . $db->qn('published') . ' = ' . $db->quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND a.' . $db->qn('state') . ' = ' . $db->quote(EASYBLOG_POST_NORMAL);

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();
		
		if ($filterLanguage) {
			$query .= EBR::getLanguageQuery('AND', 'a.language');
		}

		switch($type)
		{
			case '1' :
				// by blogger
				$bloggers = self::join($params->get('bloggerid'));

				if (!empty($bloggers)) {
					$query .= ' AND a.' . $db->qn('created_by') . ' IN (' . $bloggers . ')';
				}
				break;
			case '2' :
				// by category
				$categories = self::join($params->get('categoryid'));

				if (!empty($categories)) {
					$joinQuery .= ' INNER JOIN ' . $db->qn('#__easyblog_post_category') . ' as pc';
					$joinQuery .= ' ON pc.'. $db->qn('post_id') . ' = a.' . $db->qn('id');
					$query .= ' AND pc.' . $db->qn( 'category_id' ) . ' IN (' . $categories . ')';
				}
				break;
			case '3' :
				// by tag
				$tags = self::join($params->get('tagid'));

				if (!empty($post_ids)) {
					$joinQuery .= ' INNER JOIN ' . $db->qn('#__easyblog_post_tag') . ' as pt';
					$joinQuery .= ' ON pt.'. $db->qn('post_id') . ' = a.' . $db->qn('id');
					$query .= ' AND pt' . $db->qn('tag_id') . ' IN (' . $tags . ')';
				}
				break;
			case '4' :
				// by team
				$teams = self::join($params->get('teamids'));

				if (!empty($post_ids)) {
					$query .= ' AND a.' . $db->qn('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);
					$query .= ' AND a.' . $db->qn('source_id') . ' IN (' . $post_ids . ')';
				}
				break;

			case '0' :
			default:
				// by latest
				$featured = $params->get('usefeatured');

				if ($featured) {
					$joinQuery .= ' INNER JOIN ' . $db->qn('#__easyblog_featured') . ' as f';
					$joinQuery .= ' ON f.'. $db->qn('content_id') . ' = a.' . $db->qn('id');
					$joinQuery .= ' AND f.'. $db->qn('type') . ' = ' . $db->Quote(EBLOG_FEATURED_BLOG);
				}
				break;
		}

		// always sort by latest
		$query .= ' ORDER BY a.' . $db->qn('created') . ' DESC';

		// set limit
		$query .= ' LIMIT ' . (int) $params->get('count', 5);

		// joins the strings.
		$query = $headQuery . $joinQuery . $query;

		$db->setQuery($query);
		$posts = $db->loadObjectList();

		$posts = self::processItems($posts, $params);
		return $posts;
	}

	private static function processItems( $posts, &$params )
	{
		$config = EB::getConfig();
		$results = array();

		$posts = EB::formatter('list', $posts);

		foreach ($posts as $data) {
			$row = EB::post($data->id);
			$row->bind($data);

			$row->author = EB::user($row->created_by);

			$row->commentCount = EB::getCommentCount($row->id);
			$row->featuredImage = '';

			if ($params->get('showimage', 1)) {
				$row->featuredImage = self::getFeaturedImage($row, $params);
			}

			self::prepareTooltipContent($row, $params);
			$results[] = $row;
		}

		return $results;
	}

	private static function prepareTooltipContent( &$post, &$params )
	{
		$infoWidth = $params->get('infowidth');

		$disabled = true;
		if ($params->get('enableratings')) {
			$disabled = false;
		}
		
		$post->html = '<div class="ebpostmap_infoWindow" style="max-width:'. $infoWidth .'px;"><table>'."\n";

		if ($params->get('showimage') && $post->featuredImage) {

			$image = '<td class="ebpostmap_featuredImage"';
			
			if ($params->get('showavatar')) {
				$image .= ' colspan = "2"';
			}

			$image .= '>'.$post->featuredImage.'</td>'."\n";

			$post->html .= '<tr>' . $image . '</tr>'."\n";
		}

		$post->html .= '<tr>';

		if ($params->get('showavatar')) {
			$avatar = '<td class="ebpostmap_avatar" valign="top"><a href="'.$post->author->getProfileLink().'" class="mod-avatar"><img class="avatar" src="'.$post->author->getAvatar().'" /></a></td>'."\n";
			$post->html .= $avatar;
		}

		$post->html .= '<td class="ebpostmap_detail">';
		$post->html .= '<div class="ebpostmap_title"><a href="'. EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $post->id) .'"><b>'.$post->title.'</b></a></div>'."\n";

		if ($params->get('showauthor')) {
			$blogger = '<div class="ebpostmap_blogger">'.JText::sprintf('MOD_EASYBLOGPOSTMAP_POST_BY', $post->author->getName()).'</div>'."\n";
			$post->html .= $blogger;
		}

		if ($params->get('showaddress')) {
			$address = '<div class="ebpostmap_address">'.JText::sprintf('MOD_EASYBLOGPOSTMAP_ADDRESS_AT', $post->address).'</div>'."\n";
			$post->html .= $address;
		}

		if ($params->get('showcommentcount')) {
			$comment = '<div class="ebpostmap_comments">'.JText::sprintf('MOD_EASYBLOGPOSTMAP_TOTAL_COMMENTS', $post->commentCount).'</div>'."\n";
			$post->html .= $comment;
		}

		if ($params->get('showhits')) {
			$hits = '<div class="ebpostmap_hits">'.JText::sprintf('MOD_EASYBLOGPOSTMAP_HITS', $post->hits).'</div>'."\n";
			$post->html .= $hits;
		}

		if ($params->get('showratings')) { 
			$ratings = '<div class="ebpostmap_ratings">'. EB::ratings()->html($post, 'ebpostmap_'.$post->id.'-ratings',JText::_('MOD_EASYBLOGPOSTMAP_RATEBLOG'), $disabled).'</div>'."\n";
			$post->html .= $ratings;
		}

		$post->html .= '</td></tr></div>'."\n";
	}

	private static function getFeaturedImage(&$row, &$params)
	{
		$featuredImgWidth = $params->get('featuredimgwidth');
		$featuredImgHeight = $params->get('featuredimgheight');

		$postCover = '';
		$image = '';
		
		if ($row->hasImage()) {
		    $postCover = $row->getImage('small');
		}

		if (!empty($postCover)) {
			$image = '<img title="'.$row->title.'" src="' . $postCover . '" style="width:'. $featuredImgWidth .'px;height:'. $featuredImgHeight .'px;"/>';
		}

		return $image;
	}

	private static function join($items)
	{
		$db = EB::db();

		if (!is_array($items)) {
			$items = str_replace(' ', '', $items);
			$items = explode(',', $items);
		}

		$temp = array();

		foreach ($items as $item) {
			$temp[] = $db->quote($item);
		}

		$result = implode(',', $temp);

		return $result;
	}

	public static function sortLocation($items)
	{
		usort($items, array('modEasyBlogPostMapSorter', 'latitudesort'));
		usort($items, array('modEasyBlogPostMapSorter', 'longitudesort'));
		return $items;
	}

	public static function sameLocation($a, $b)
	{
		return ($a->latitude == $b->latitude && $a->longitude == $b->longitude);
	}
}

class modEasyBlogPostMapSorter
{
	// sort by location first
	static function customsort($a, $b, $field)
	{
		if ($a->$field == $b->$field) {
			return 0;
		}

		return ($a->$field > $b->$field)? -1 : 1;
	}

	static function latitudesort($a, $b)
	{
		return self::customsort($a, $b, 'latitude');
	}

	static function longitudesort($a, $b)
	{
		return self::customsort($a, $b, 'longitude');
	}
}
