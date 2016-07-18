<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

function EasyBlogBuildRoute(&$query)
{
	$segments = array();
	$config = EB::config();

	// index.php?option=com_easyblog&view=latest
	if (isset($query['view']) && $query['view'] == 'latest') {
		unset($query['view']);
	}

	// index.php?option=com_easyblog&view=entry
	if (isset($query['view']) && $query['view'] == 'entry' && isset($query['id'])) {

		if ($config->get('main_sef') != 'simple' && $config->get('main_sef') != 'simplecategory') {
			$segments[] = EBR::translate($query['view']);
		}

		// Get the post from the cache
		$postId = (int) $query['id'];

		$post = EB::post();
		$post->load($postId);

		if ($config->get('main_sef') == 'simplecategory') {
			$segments[]= $post->getPrimaryCategory()->getAlias();
		}

		// Since the cache library is already using the post library to re-render the post table data, just use the permalink.
		$segments[] = $post->getAlias();

		unset($query['id']);
		unset($query['view']);
	}

	// Single category view
	// index.php?option=com_easyblog&view=categories&layout=listings&id=xxxx
	if (isset($query['view']) && $query['view'] == 'categories') {

		// Try to get rid of duplicated view vs menu alias
		$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

		$menu = JFactory::getApplication()->getMenu();
		$activeMenu = $menu->getActive();
		$includeAlias = true;

		// If there is an item id in the url, we should get the menu
		if ($itemId) {
			$menu = JFactory::getApplication()->getMenu()->getItem($itemId);

			// Translate the view first
			if ($menu->query['view'] != EBR::translate($query['view'])) {
				$segments[] = EBR::translate($query['view']);
			}
		}

		// if ($itemId && isset($query['layout']) && $query['layout'] == 'listings') {
		// 	$includeAlias = false;
		// }

		// We need to know if the id should be appended to the menu
		if ($activeMenu && isset($activeMenu->query['id']) && isset($query['id']) && $activeMenu->query['view'] == 'categories' && $activeMenu->query['layout'] == 'listings' && $activeMenu->query['id'] == $query['id']) {
			$includeAlias = false;
		}

		// the active menu might be diferent from the current catgory we looking. let check from menu item instead
		if (isset($query['id']) && $includeAlias) {
			$itemMenu = EBR::getMenus('categories', 'listings', $query['id']);

			if ($itemMenu && $itemMenu->segments->view == 'categories' && $itemMenu->segments->layout == 'listings' && $itemMenu->segments->id == $query['id']) {
				$includeAlias = false;
			}

		}

		// Translate the category permalink now
		if (isset($query['id']) && $includeAlias) {
			$category = EB::cache()->get( (int) $query['id'], 'category');

			if ($category) {
				$segments[] = $category->getAlias();
			}
		}

		unset($query['id']);
		unset($query['view']);
		unset($query['layout']);
	}

	// Single tag view
	// index.php?option=com_easyblog&view=tags&layout=listings&id=xxxx
	if (isset($query['view']) && $query['view'] == 'tags') {

		$segments[] = EBR::translate($query['view']);
		if (isset($query['id']) && isset($query['layout'])) {

			$tag = EB::table('Tag');
			$tag->load((int) $query['id']);

			if (! $tag->id) {
				$tag->load($query['id'], true);
			}

			$segments[] = $tag->getAlias();
		}

		unset($query['id']);
		unset($query['view']);
		unset($query['layout']);
	}

	// index.php?option=com_easyblog&view=teamblog&layout=listings&id=xxx
	if(isset($query['view']) && $query['view'] == 'teamblog') {

		$segments[] = EBR::translate($query['view']);

		if (isset($query['layout'])) {
			$segments[] = EBR::translate($query['layout']);
		}

		if (isset($query['id'])) {
			$team = EB::cache()->get((int) $query['id'], 'team');
			$segments[] = $team->getAlias();
		}

		unset($query['id']);
		unset($query['stat']);
		unset($query['layout']);
		unset($query['view']);
	}

	// view=blogger&layout=listings&id=xxx
	if (isset($query['view']) && $query['view'] == 'blogger') {

		// Add view=blogger
		$segments[] = EBR::translate($query['view']);

		// Add bloggers permalink
		if (isset($query['id'])) {
			$author = EB::cache()->get( (int) $query['id'], 'author');
			$segments[] = $author->getAlias();
		}

		if (isset($query['sort'])) {
			$segments[]	= EBR::translate('sort');
			$segments[]	= EBR::translate($query['sort']);

			unset($query['sort']);
		}

		unset($query['view']);
		unset($query['id']);
		unset($query['layout']);
	}

	// index.php?option=com_easyblog&view=dashboard&layout=xxx
	if (isset($query['view']) && $query['view'] == 'dashboard') {

		$segments[] = EBR::translate($query['view']);

		if (isset($query['layout'])) {
			$segments[] = EBR::translate($query['layout']);
		}

		if (isset($query['filter'])) {
			$segments[] = $query['filter'];

			unset($query['filter']);
		}

		if (isset($query['blogid'])) {
			$segments[] = $query['blogid'];
			unset($query['blogid']);
		}

		if (isset($query['postType'])) {
			$segments[] = $query['postType'];
			unset($query['postType']);
		}

		unset($query['view']);
		unset($query['layout']);
	}

	// index.php?option=com_easyblog&view=archive
	if (isset($query['view']) && $query['view'] == 'archive') {

		$segments[] = EBR::translate($query['view']);
		unset($query['view']);

		if (isset($query['layout'])) {
			$segments[] = $query['layout'];
			unset($query['layout']);
		}

		if (isset($query['archiveyear'])) {
			$segments[] = $query['archiveyear'];
			unset($query['archiveyear']);
		}

		if (isset($query['archivemonth'])) {
			$segments[] = $query['archivemonth'];
			unset($query['archivemonth']);
		}

		if (isset($query['archiveday'])) {
			$segments[] = $query['archiveday'];
			unset($query['archiveday']);
		}
	}

	// index.php?option=com_easyblog&view=calendar
	if (isset($query['view']) && $query['view'] == 'calendar') {
		$segments[] = EBR::translate($query['view']);
		unset($query['view']);

		if (isset($query['year'])) {
			$segments[] = $query['year'];
			unset($query['year']);
		}

		if (isset($query['month'])) {
			$segments[] = $query['month'];
			unset($query['month']);
		}

		if (isset($query['day'])) {
			$segments[] = $query['day'];
			unset($query['day']);
		}
	}

	// index.php?option=com_easyblog&view=search
	if (isset($query['view']) && $query['view'] == 'search') {
		$segments[] = EBR::translate($query['view']);
		unset($query['view']);

		if (isset($query['layout'])) {
			$segments[] = $query['layout'];
			unset($query['layout']);
		}

		if (isset($query['query'])) {
			$segments[] = $query['query'];
			unset($query['query']);
		}
	}


	// // index.php?option=com_easyblog&view=composer
	// if (isset($query['view']) && $query['view'] == 'composer') {
	// 	dump('here');
	// }

	// index.php?option=com_easyblog&view=login
	if (isset($query['view']) && $query['view'] == 'login') {
		$segments[] = EBR::translate($query['view']);
		unset($query['view']);
	}


	if (isset($query['type'])) {
		if (!isset($query['format']) && !isset($query['controller'])) {
			$segments[] = $query['type'];
			unset($query['type']);
		}
	}

	if (!isset($query['Itemid'])) {
		$query['Itemid'] = EBR::getItemId();
	}

	if (isset($query['lang'])) {
		unset($query['lang']);
	}

	return $segments;
}

function EasyBlogParseRoute(&$segments)
{
	// Load site's language file
	EB::loadLanguages();

	$vars = array();
	$active = JFactory::getApplication()->getMenu()->getActive();
	$config = EB::config();

	// We know that the view=categories&layout=listings&id=xxx because there's only 1 segment
	// and the active menu is view=categories
	if (isset($active) && $active->query['view'] == 'categories' && !isset($active->query['layout']) && count($segments) == 1) {

		$category = EB::table('Category');
		$category->load(array('alias' => $segments[0]));

		// if still can't get the correct category id try this
		if (!$category->id) {
			$categoryAlias = $segments[0];
			$categoryAlias = str_replace(':', '-', $categoryAlias);

			// Check if Unicode alias is enabled or not
			if ($config->get('main_sef_unicode')) {

				// If enabled, we need to get the id from the alias
				$categoryId = explode('-', $categoryAlias);
				$category->id = $categoryId[0];
			} else {
				$category->load(array('alias' => $categoryAlias));
			}
		}

		// Only force this when we can find a category id.
		if ($category->id) {
			$vars['view'] = 'categories';
			$vars['layout'] = 'listings';
			$vars['id'] = $category->id;

			return $vars;
		}
	}

	// RSD View
	if (isset($segments[0]) && $segments[0] == 'rsd') {
		$vars['view'] = 'rsd';

		return $vars;
	}

	// Feed view
	if (isset($segments[1])) {
		if ($segments[1] == 'rss' || $segments[1] == 'atom') {
			$vars['view']	= $segments[0];
			unset( $segments );
			return $vars;
		}
	}

	// If user chooses to use the simple sef setup, we need to add the proper view
	if (($config->get('main_sef') == 'simple' && count($segments) == 1) ||
		($config->get('main_sef') == 'simplecategory' && count($segments) == 2)) {
		$files = JFolder::folders(JPATH_ROOT . '/components/com_easyblog/views');
		$views = array();

		foreach ($files as $file) {
			$views[] = EBR::translate($file);
		}

		if (!in_array($segments[0], $views)) {
			if (count($segments) == 2) {
				// if the 1st element is not a view, most likely this is simplecategory type. Lets replace the 1st element with entry view.
				$segments[0] = EBR::translate('entry');
			} else {
				array_unshift($segments, EBR::translate('entry'));
			}
		}
	}

	// Composer view
	if (isset($segments[0]) && $segments[0] == EBR::translate('composer')) {
		$vars['view'] = 'composer';
	}

	// Entry view
	if (isset($segments[0]) && $segments[0] == EBR::translate('entry')) {
		$count	= count($segments);
		$entryId    = '';

		// perform manual split on the string.
		if ($config->get('main_sef_unicode')) {
			$permalinkSegment = $segments[($count - 1)];
			$permalinkArr = explode(':', $permalinkSegment);
			$entryId = $permalinkArr[0];
		} else {
			$index = ($count - 1);
			$alias = $segments[$index];

			// There could be instances where it has &highlight=xxx in the query string
			$tmp = explode('&highlight', $alias);
			$alias = $tmp[0];

			$post = EB::post();
			$post->loadByPermalink($alias);

			if ($post) {
				$entryId = $post->id;
			}
		}

		if ($entryId) {
			$vars[ 'id' ]	= $entryId;
		}
		$vars['view']	= 'entry';
	}

	// Calendar view
	if (isset($segments[0]) && $segments[0] == EBR::translate('calendar')) {

		$vars['view'] = 'calendar';

		$count = count($segments);
		$totalSegments	= $count - 1;

		if ($totalSegments >= 1) {

			// First segment is always the year
			if (isset($segments[1])) {
				$vars['year'] = $segments[1];
			}

			// Second segment is always the month
			if (isset($segments[2])) {
				$vars['month'] = $segments[2];
			}

			// Third segment is always the day
			if (isset($segments[3])) {
				$vars['day'] = $segments[3];
			}
		}
	}

	if( isset( $segments[ 0 ] ) && $segments[ 0 ] == EBR::translate('archive' ) )
	{
		$vars[ 'view' ]	= 'archive';

		$count			= count($segments);
		$totalSegments	= $count - 1;

		if( $totalSegments >= 1 )
		{
			$indexSegment	= 1;

			if( $segments[ 1 ] == 'calendar' )
			{
				$vars[ 'layout' ]	= 'calendar';
				$indexSegment		= 2;
			}

			// First segment is always the year
			if( isset( $segments[ $indexSegment ] ) )
			{
				$vars[ 'archiveyear' ]	= $segments[ $indexSegment ];
			}

			// Second segment is always the month
			if( isset( $segments[ $indexSegment + 1 ] ) )
			{
				$vars[ 'archivemonth' ]	= $segments[ $indexSegment + 1 ];
			}

			// Third segment is always the day
			if( isset( $segments[ $indexSegment + 2 ] ) )
			{
				$vars[ 'archiveday' ]	= $segments[ $indexSegment + 2 ];
			}
		}

	}

	// Process categories sef links
	// index.php?option=com_easyblog&view=categories
	if (isset($segments[0]) && $segments[0] == EBR::translate('categories')) {

		// Set the view
		$vars['view'] = 'categories';

		// Get the total number of segments
		$count = count($segments);

		// Ensure that the first index is not a system layout
		$layouts = array('listings', 'simple');

		if ($count == 2 && !in_array($segments[1], $layouts)) {

			$id = null;

			// If unicode alias is enabled, just explode the data
			if ($config->get('main_sef_unicode')) {
				$tmp = explode(':', $segments[1]);
				$id = $tmp[0];
			}

			// Encode segments
			$segments = EBR::encodeSegments($segments);

			if (!$id) {
				$category = EB::table('Category');
				$category->load(array('alias' => $segments[1]));

				$id = $category->id;
			}

			$vars['id'] = $id;
			$vars['layout']	= 'listings';
		}

		// index.php?option=com_easyblog&view=categories&layout=simple
		if ($count == 2 && in_array($segments[1], $layouts)) {
			$vars['layout']	= $segments[1];
		}
	}

	if( isset($segments[0]) && $segments[0] == EBR::translate( 'tags' ) )
	{
		$count	= count($segments);
		if( $count > 1 )
		{
			$tagId = '';
			if( $config->get( 'main_sef_unicode' ) )
			{
				// perform manual split on the string.
				$permalinkSegment   = $segments[ ( $count - 1 ) ];
				$permalinkArr    	= explode( ':', $permalinkSegment);
				$tagId         = $permalinkArr[0];
			}

			$segments = EBR::encodeSegments($segments);
			if( empty( $tagId ) )
			{
				$table	= EB::table('Tag');
				$table->load( $segments[ ( $count - 1 ) ] , true);
				$tagId  = $table->id;
			}

			$vars[ 'id' ]	= $tagId;
			$vars['layout']	= 'tag';
		}
		$vars[ 'view' ]	= 'tags';
	}

	// view=blogger&layout=listings&id=xxx
	if (isset($segments[0]) && $segments[0] == EBR::translate('blogger')) {

		$vars[ 'view' ]	= 'blogger';

		$count	= count($segments);

		if ($count > 1) {

			if ($count == 3) {
				// this is bloggers sorting page
				$vars['sort'] = $segments[2];

			} else {

				// Default user id
				$id = 0;

				// Parse the segments
				$segments = EBR::encodeSegments($segments);

				// For unicode urls we definitely know that the author's id would be in the form of ID-title
				if ($config->get('main_sef_unicode')) {
					$permalink = explode(':', $segments[1]);
					$id = $permalink[0];
				}

				if (!$id) {

					// Try to get the user id
					$permalink = $segments[1];

					$id   = EB::getUserId($permalink);

					if (!$id) {
						$id = EB::getUserId(JString::str_ireplace('-', ' ', $permalink));
					}

					if (!$id) {
						$id = EB::getUserId(JString::str_ireplace('-', '_', $permalink));
					}
				}

				if ($id) {
					$vars['layout'] = 'listings';
					$vars['id']	= $id;
				}

			}// if count > 3
		}
	}

	if( isset($segments[0]) && $segments[0] == EBR::translate( 'dashboard' ) )
	{
		$count	= count($segments);

		if ($count > 1) {

			switch (EBR::translate($segments[1])) {
				case EBR::translate( 'write' ):
					$vars['layout']	= 'write';
				break;
				case EBR::translate( 'profile' ):
					$vars['layout']	= 'profile';
				break;
				case EBR::translate( 'drafts' ):
					$vars['layout']	= 'drafts';
				break;
				case EBR::translate( 'entries' ):
					$vars['layout']	= 'entries';
				break;
				case EBR::translate( 'comments' ):
					$vars['layout']	= 'comments';
				break;
				case EBR::translate( 'categories' ):
					$vars['layout']	= 'categories';
				break;
				case EBR::translate('requests');
					$vars['layout'] = 'requests';
				break;
				case EBR::translate( 'listCategories' ):
					$vars['layout']	= 'listCategories';
				break;
				case EBR::translate( 'category' ):
					$vars['layout']	= 'category';
				break;
				case EBR::translate( 'tags' ):
					$vars['layout']	= 'tags';
				break;
				case EBR::translate( 'review' ):
					$vars['layout']	= 'review';
				break;
				case EBR::translate( 'pending' ):
					$vars['layout']	= 'pending';
				break;
				case EBR::translate('revisions'):
					$vars['layout'] = 'revisions';
				break;
				case EBR::translate( 'teamblogs' ):
					$vars['layout']	= 'teamblogs';
				break;
				case EBR::translate('quickpost'):
					$vars['layout']	= 'quickpost';
				break;
				case EBR::translate('moderate'):
					$vars['layout']	= 'moderate';
				break;
				case EBR::translate('templates'):
					$vars['layout'] = 'templates';
				break;
				case EBR::translate('templateform'):
					$vars['layout'] = 'templateform';
				break;
				case EBR::translate('compare'):
					$vars['layout'] = 'compare';
				break;
			}

			// Check if there's any default type
			if (isset($vars['layout']) && $vars['layout'] == 'quickpost' && isset($segments[2])) {
				$vars['type'] = $segments[2];
			}

			if (isset($vars['layout']) && $vars['layout'] == 'compare' && isset($segments[2])) {
				$vars['blogid'] = $segments[2];
			}

			if( isset( $vars['layout'] ) && $vars['layout'] == 'entries' )
			{
				if( count( $segments ) == 3 )
				{
					if( isset($segments[2]) )
					{
						$vars['postType']	= $segments[2];
					}
				}

				if( count( $segments ) == 4 )
				{
					if( isset($segments[2]) )
					{
						$vars['filter']	= $segments[2];
					}

					if( isset($segments[3]) )
					{
						$vars['postType'] = $segments[3];
					}
				}
			}
			else
			{
				if( isset($segments[2]) )
				{
					$vars['filter']	= $segments[2];
				}
			}
		}
		$vars[ 'view' ]	= 'dashboard';
	}

	if( isset($segments[0]) && $segments[0] == EBR::translate( 'teamblog' ) ) {
		$count	= count($segments);

		if( $count > 1 ) {
			$rawSegments = $segments;
			$segments = EBR::encodeSegments($segments);

			if( $config->get( 'main_sef_unicode' ) )
			{
				// perform manual split on the string.

				if( isset($segments[2]) && $segments[2] == EBR::translate( 'statistic' ) )
				{
					$permalinkSegment   = $rawSegments[1];
				}
				else
				{
					$permalinkSegment   = $rawSegments[ ( $count - 1 ) ];
				}

				$permalinkArr    	= explode( ':', $permalinkSegment);
				$teamId         	= $permalinkArr[0];
			}
			else
			{
				if( isset($segments[2]) && $segments[2] == EBR::translate( 'statistic' ) )
				{
					$permalink = $segments[1];
				}
				else
				{
					$permalink = $segments[ ( $count - 1 ) ];
				}

				$table	= EB::table('TeamBlog');
				$loaded = $table->load( $permalink , true);

				if( !$loaded )
				{
					$name = $segments[ ($count - 1 ) ];
					$name = JString::str_ireplace( ':' , ' ' , $name );
					$name = JString::str_ireplace( '-', ' ' , $name );
					$table->load( $name , true );
				}

				$teamId = $table->id;
			}
			$vars['id']		= $teamId;

			if(isset($segments[2]) && $segments[2] == EBR::translate( 'statistic' ) )
			{
				$vars['layout']	= EBR::translate( $segments[2] );

				if($count == 5)
				{
					if(isset($segments[3]))
					{
						$vars['stat'] = EBR::translate( $segments[3] );

						switch( EBR::translate( $segments[3] ) )
						{
							case EBR::translate( 'category' ):
								if( $config->get( 'main_sef_unicode' ) )
								{
									// perform manual split on the string.
									$permalinkSegment   = $rawSegments[4];
									$permalinkArr    	= explode( ':', $permalinkSegment);
									$categoryId         = $permalinkArr[0];
								}
								else
								{
									$table = EB::table('Category');
									$table->load( $segments[4] , true );
									$categoryId = $table->id;
								}
								$vars['catid'] = $categoryId;
								break;
							case EBR::translate( 'tag' ):
								if( $config->get( 'main_sef_unicode' ) )
								{
									// perform manual split on the string.
									$permalinkSegment   = $segments[4];
									$permalinkArr    	= explode( ':', $permalinkSegment);
									$tagId         		= $permalinkArr[0];
								}
								else
								{
									$table	= EB::table('Tag');
									$table->load( $segments[4] , true);
									$tagId  = $table->id;
								}
								$vars['tagid'] = $tagId;
								break;
							default:
								// do nothing.
						}
					}
				}
			}
			else
			{
				$vars['layout']	= 'listings';
			}

		}

		$vars[ 'view' ]	= 'teamblog';
	}

	if( isset($segments[0]) && $segments[0] == EBR::translate( 'search' ) )
	{
		$count	= count($segments);
		if( $count == 2 )
		{
			if($segments[1] == "parsequery")
			{
				$vars[ 'layout' ] = EBR::translate( $segments[1] );
			}
			else
			{
				$vars[ 'query' ] = $segments[1];
			}

		}
		$vars['view']	= 'search';
	}

	$count	= count($segments);
	if( $count == 1 )
	{
		switch( EBR::translate( $segments[0] ) )
		{
			case EBR::translate( 'latest' ):
				$vars['view']	= 'latest';
				break;
			case EBR::translate( 'featured' ):
				$vars['view']	= 'featured';
				break;
			case EBR::translate( 'images' ):
				$vars['view']	= 'images';
				break;
			case EBR::translate( 'login' ):
				$vars['view']	= 'login';
				break;
			case EBR::translate( 'myblog' ):
				$vars['view']	= 'myblog';
				break;
			case EBR::translate( 'ratings' ):
				$vars['view']	= 'ratings';
				break;
			case EBR::translate( 'subscription' ):
				$vars['view']	= 'subscription';
				break;
		}
	}

	if (! $vars) {
		// someting is not right here.
		return JError::raiseError(404, JText::_('COM_EASYBLOG_PAGE_IS_NOT_AVAILABLE'));
	}

	return $vars;
}
