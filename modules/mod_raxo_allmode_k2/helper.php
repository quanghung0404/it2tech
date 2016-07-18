<?php
/**
 * =============================================================
 * RAXO All-mode K2 J3.x
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2014 RAXO Group
 * @license		GNU General Public License v2.0
 * 				http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.raxo.org
 * =============================================================
 */


defined('_JEXEC') or die;

require_once JPATH_SITE .'/components/com_k2/helpers/route.php';
require_once JPATH_SITE .'/components/com_k2/helpers/utilities.php';

abstract class ModRAXO_Allmode_K2
{
	public static function getList($params)
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);


		// SOURCE
		$source				= $params->def('source_selection', 'cat');
		$source_cat			= $params->get('source_cat', array());
		$source_itm			= trim(preg_replace(array('/\s*/', '/,+/'), array('', ','), $params->get('source_itm')), ',');
		$exclude_itm		= trim(preg_replace(array('/\s*/', '/,+/'), array('', ','), $params->get('exclude_itm')), ',');

		if ($source == 'cat' && $source_cat) {
			if ($source_cat[0] != '') {
				$query->where('(i.catid = '. implode(' OR i.catid = ', $source_cat). ')');
			}
			$exclude_itm ? $query->where('i.id NOT IN ('. $exclude_itm .')') : '';
		} else if ($source == 'itm' && $source_itm) {
			$query->where('i.id IN ('. $source_itm .')');
		} else {
			echo JText::_('MOD_RAXO_ALLMODE_K2_ERROR_SOURCE');
			return;
		}


		// FILTERS
		$count_top			= (int) $params->get('count_top', 2);
		$count_reg			= (int) $params->get('count_regular', 4);
		$count_skip			= (int) $params->get('count_skip', 0);

		$tags				= $params->get('tags', array('0'=>''));
		$tags				= ($tags[0] != '') ? implode(' OR t.tagID = ', $tags) : NULL;

		$featured			= $params->def('show_featured', 'show');
		$ordering			= $params->def('ordering', 'created_dsc');

		$user				= JFactory::getUser();
		$userID				= (int) $user->get('id');
		$userLV				= implode(',', $user->getAuthorisedViewLevels());
		$access				= $params->def('not_public', 0) ? 0 : 1;


		// TEXT
		$show_title			= (array) $params->def('show_title');
		$show_title_top		= in_array('top', $show_title) ? 1 : '';
		$show_title_reg		= in_array('reg', $show_title) ? 1 : '';
		$limit_title		= $params->get('limit_title', array());
		$limit_title_top	= $limit_title[0];
		$limit_title_reg	= $limit_title[1];

		$show_text			= (array) $params->def('show_text');
		$show_text_top		= in_array('top', $show_text) && $count_top ? 1 : '';
		$show_text_reg		= in_array('reg', $show_text) && $count_reg ? 1 : '';
		$limit_text			= $params->get('limit_text', array());
		$limit_text_top		= $limit_text[0];
		$limit_text_reg		= $limit_text[1];

		$read_more			= $params->get('read_more');
		$read_more_top		= $read_more[0];
		$read_more_reg		= $read_more[1];

		$intro_clean		= $params->def('intro_clean', 1);
		$allowable_tags		= str_replace(' ', '', $params->get('allowable_tags'));
		$allowable_tags		= "<". str_replace(',', '><', $allowable_tags) .">";
		$plugins_support	= $params->def('plugins_support', 0);


		// INFO
		$show_date			= (array) $params->def('show_date');
		$show_date_top		= in_array('top', $show_date) ? 1 : '';
		$show_date_reg		= in_array('reg', $show_date) ? 1 : '';
		$date_type			= $params->def('date_type', 'created');
		$date_format		= $params->get('date_format');
		$date_format_top	= $date_format[0] ? $date_format[0] : 'F d, Y';
		$date_format_reg	= $date_format[1] ? $date_format[1] : 'M d, Y';

		$show_category		= (array) $params->def('show_category');
		$show_category_top	= in_array('top', $show_category) && $count_top ? 1 : '';
		$show_category_reg	= in_array('reg', $show_category) && $count_reg ? 1 : '';
		$category_link		= $params->def('category_link', 0);

		$show_author		= (array) $params->def('show_author');
		$show_author_top	= in_array('top', $show_author) && $count_top ? 1 : '';
		$show_author_reg	= in_array('reg', $show_author) && $count_reg ? 1 : '';
		$author_link		= $params->def('author_link', 0);		

		$show_rating		= (array) $params->def('show_rating');
		$show_rating_top	= in_array('top', $show_rating) && $count_top ? 1 : '';
		$show_rating_reg	= in_array('reg', $show_rating) && $count_reg ? 1 : '';

		$show_hits			= (array) $params->def('show_hits');
		$show_hits_top		= in_array('top', $show_hits) && $count_top ? 1 : '';
		$show_hits_reg		= in_array('reg', $show_hits) && $count_reg ? 1 : '';

		$show_comments		= (array) $params->def('show_comments');
		$show_comments_top	= in_array('top', $show_comments) && $count_top ? 1 : '';
		$show_comments_reg	= in_array('reg', $show_comments) && $count_reg ? 1 : '';
		$comment_system		= $params->def('comment_system', 'k2comments');


		// IMAGES
		$show_image			= (array) $params->def('show_image');
		$show_image_top		= in_array('top', $show_image) && $count_top ? 1 : '';
		$show_image_reg		= in_array('reg', $show_image) && $count_reg ? 1 : '';

		$image_width		= $params->get('image_width', array());
		$image_width_top	= $image_width[0];
		$image_width_reg	= $image_width[1];
		$image_height		= $params->get('image_height', array());
		$image_height_top	= $image_height[0];
		$image_height_reg	= $image_height[1];

		$image_source		= $params->def('image_source', 'automatic');
		$image_link			= $params->def('image_link', 1);
		$image_title		= $params->def('image_title', 1);
		$image_crop			= $params->def('image_crop', 1);
		$image_default		= $params->get('image_default') !== '-1' ? 'modules/mod_raxo_allmode_k2/tools/'. $params->get('image_default') : '';


		// ORDERING
		switch ($ordering) {
			case 'created_asc':
				$orderBy = 'date ASC';
			break;
			case 'title_az':
				$orderBy = 'i.title ASC';
			break;
			case 'title_za':
				$orderBy = 'i.title DESC';
			break;
			case 'popular_first':
				$orderBy = 'i.hits DESC';
			break;
			case 'popular_last':
				$orderBy = 'i.hits ASC';
			break;
			case 'rated_most':
				$orderBy = 'rating_value DESC, r.rating_count DESC';
			break;
			case 'rated_least':
				$orderBy = 'rating_value ASC, r.rating_count ASC';
			break;
			case 'commented_most':
				$orderBy = 'comments_count DESC, comments_date DESC';
			break;
			case 'commented_latest':
				$orderBy = 'comments_date DESC, date DESC';
			break;
			case 'ordering_fwd':
				$orderBy = $featured == 'only' ? 'i.featured_ordering ASC' : 'i.ordering ASC';
			break;
			case 'ordering_rev':
				$orderBy = $featured == 'only' ? 'i.featured_ordering DESC' : 'i.ordering DESC';
			break;
			case 'id_asc':
				$orderBy = 'i.id ASC';
			break;
			case 'id_dsc':
				$orderBy = 'i.id DESC';
			break;
			case 'exact':
				$orderBy = ($source == 'itm' && $source_itm) ? 'FIELD(i.id, '. $source_itm .')' : 'i.id ASC';
			break;
			case 'random':
				$orderBy = 'RAND()';
			break;
			case 'created_dsc':
			default:
				$orderBy = 'date DESC';
			break;
		}


		// QUERY
		$query->select('i.id, i.title, i.alias, i.catid AS category_id, c.alias AS category_alias, i.access');

		// Select: Date
		if ($date_type !== 'created') {
			$query->select(' CASE WHEN i.'. $date_type .' = \'0000-00-00 00:00:00\' THEN i.created ELSE i.'. $date_type .' END AS date');
		} else {
			$query->select(' i.created AS date');
		}

		if ($show_text_top || $show_text_reg || (($show_image_top || $show_image_reg) && ($image_source == 'text' || $image_source == 'automatic'))) {
			$query->select(' i.introtext, i.fulltext');
		}
		($show_image_top || $show_image_reg) && $image_source != 'text' ? $query->select(' i.image_caption') : '';

		$show_hits_top || $show_hits_reg ? $query->select(' i.hits') : '';

		$show_category_top || $show_category_reg ? $query->select(' c.name AS category_name') : '';

		$query->from('#__k2_items AS i');

		// Join: Categories
		$query->join('LEFT', '#__k2_categories AS c ON c.id = i.catid');

		// Join: Users
		if ($show_author_top || $show_author_reg) {
			$query->select(' u.name AS author_name, i.created_by_alias AS author_alias, i.created_by AS author_id');
			$query->join('LEFT', '#__users AS u ON u.id = i.created_by');
		}

		// Join: Rating
		if ($show_rating_top || $show_rating_reg || $ordering == 'rated_most' || $ordering == 'rated_least') {
			$query->select(' ROUND(r.rating_sum / r.rating_count, 2) AS rating_value, r.rating_count');
			$query->join('LEFT', '#__k2_rating AS r ON r.itemID = i.id');
		}

		// Join: Tags
		$tags ? $query->join('INNER', '#__k2_tags_xref AS t ON t.itemID = i.id') : '';

		// Join: Comments
		if ($show_comments_top || $show_comments_reg || $ordering == 'commented_most' || $ordering == 'commented_latest') {
			switch ($comment_system) {
				case 'jcomments':
					$query->select(' COUNT(jc.id) AS comments_count, MAX(jc.date) AS comments_date');
					$query->join('LEFT', '#__jcomments AS jc ON jc.object_id = i.id AND jc.object_group = \'com_k2\' AND jc.published = 1');
					$comments_link	= "#comments";
					break;
				case 'jacomment':
					$query->select(' COUNT(jc.id) AS comments_count, MAX(jc.date) AS comments_date');
					$query->join('LEFT', '#__jacomment_items AS jc ON jc.contentid = i.id AND jc.option = \'com_k2\' AND jc.type = 1');
					$comments_link	= "#jac-wrapper";
					break;
				case 'komento':
					$query->select(' COUNT(jc.id) AS comments_count, MAX(jc.created) AS comments_date');
					$query->join('LEFT', '#__komento_comments AS jc ON jc.cid = i.id AND jc.component = \'com_k2\' AND jc.published = 1');
					$comments_link  = "#section-kmt";
					break;
				case 'compojoom':
					$query->select(' COUNT(jc.id) AS comments_count, MAX(jc.date) AS comments_date');
					$query->join('LEFT', '#__comment AS jc ON jc.contentid = i.id AND jc.component = \'com_k2\' AND jc.published = 1');
					$comments_link	= "#ccomment-content-";
					break;
				case 'k2comments':
				default:
					$query->select(' COUNT(jc.id) AS comments_count, MAX(jc.commentDate) AS comments_date');
					$query->join('LEFT', '#__k2_comments AS jc ON jc.itemID = i.id AND jc.published = 1');
					$comments_link	= "#itemCommentsAnchor";
					break;
			}
		}

		// Filter: Published & Trashed
		$query->where('c.published = 1 AND c.trash = 0 AND i.published = 1 AND i.trash = 0');

		// Filter: Access
		$access ? $query->where('c.access IN ('. $userLV .') AND i.access IN ('. $userLV .')') : '';

		// Filter: Tags
		$tags ? $query->where('(t.tagID = '. $tags .')') : '';

		// Filter: Featured
		$featured == 'hide' ? $query->where('i.featured = 0') : ($featured == 'only' ? $query->where('i.featured = 1') : '');

		// Filter: Language
		if ($app->getLanguageFilter()) {
			$languageTag = $db->quote(JFactory::getLanguage()->getTag());
			$query->where('c.language IN ('. $languageTag .',\'*\') AND i.language IN ('. $languageTag .',\'*\')');
		}

		// Filter: Date
		$date	= JFactory::getDate();
		$min	= $date->format('i');
		$min	= sprintf('%02d', ($min - ($min % 5))); // MySQL Cache 5 minutes optimisation
		$now	= $db->quote($date->format('Y-m-d H:'. $min));

		$query->where('i.publish_up <= '. $now);
		$query->where('(i.publish_down = \'0000-00-00 00:00:00\' OR i.publish_down >= '. $now .')');

		// Filter: Date Range
		switch ($params->def('date_filtering', 'disabled'))
		{
			case 'today':
				$extraquery = ($date_type !== 'created') ? ' OR (i.'. $date_type .' = \'0000-00-00 00:00:00\' AND DATE(i.created) = '. $db->quote($date->format('Y-m-d')) .')' : '';
				$query->where('(DATE(i.'. $date_type .') = '. $db->quote($date->format('Y-m-d')). $extraquery .')');
				break;

			case 'this_week':
				$extraquery = ($date_type !== 'created') ? ' OR (i.'. $date_type .' = \'0000-00-00 00:00:00\' AND YEARWEEK(i.created) = YEARWEEK('. $now .'))' : '';
				$query->where('(YEARWEEK(i.'. $date_type .') = YEARWEEK('. $now .')'. $extraquery .')');
				break;

			case 'this_month':
				$extraquery = ($date_type !== 'created') ? ' OR (i.'. $date_type .' = \'0000-00-00 00:00:00\' AND YEAR(i.created) = YEAR('. $now .') AND MONTH(i.created) = MONTH('. $now .'))' : '';
				$query->where('((YEAR(i.'. $date_type .') = YEAR('. $now .') AND MONTH(i.'. $date_type .') = MONTH('. $now .'))'. $extraquery .')');
				break;

			case 'this_year':
				$extraquery = ($date_type !== 'created') ? ' OR (i.'. $date_type .' = \'0000-00-00 00:00:00\' AND YEAR(i.created) = '. $db->quote($date->format('Y')) .')' : '';
				$query->where('(YEAR(i.'. $date_type .') = '. $db->quote($date->format('Y')) . $extraquery .')');
				break;

			case 'range':
				$date_range_start	= $db->quote($params->get('date_range_start', '1000-01-01 00:00:00'));
				$date_range_end		= $db->quote($params->get('date_range_end', '9999-12-31 23:59:59'));

				$extraquery = ($date_type !== 'created') ? ' OR (i.'. $date_type .' = \'0000-00-00 00:00:00\' AND i.created BETWEEN '. $date_range_start .' AND '. $date_range_end .')' : '';
				$query->where('(i.'. $date_type .' BETWEEN '. $date_range_start .' AND '. $date_range_end . $extraquery .')');
				break;

			case 'relative':
				$date_range_from	= $params->get('date_range_from');
				$date_range_from	= $date_range_from[0] >= '0' ? $db->quote(date('Y-m-d', strtotime($date .' - '. $date_range_from[0] .' day'))) : $db->quote('1000-01-01 00:00:00');
				$date_range_to		= $params->get('date_range_to');
				$date_range_to		= $date_range_to[0] >= '0' ? $db->quote(date('Y-m-d', strtotime($date .' - '. $date_range_to[0] .' day'))) : $db->quote('9999-12-31 23:59:59');

				$extraquery = ($date_type !== 'created') ? ' OR (i.'. $date_type .' = \'0000-00-00 00:00:00\' AND i.created BETWEEN '. $date_range_from .' AND '. $date_range_to .')' : '';
				$query->where('(i.'. $date_type .' BETWEEN '. $date_range_from .' AND '. $date_range_to . $extraquery .')');
				break;

			case 'disabled':
			default:
				break;
		}

		// Filter: Author
		switch ($params->get('authors')) {
			case 'by_me':
				if ($userID) {
					$query->where('(i.created_by = '. $userID .' OR i.modified_by = '. $userID .')');
				} else {
					return;
				}
				break;
			case 'not_me':
				if ($userID) {
					$query->where('(i.created_by <> '. $userID .' AND i.modified_by <> '. $userID .')');
				}
				break;
			case 'all':
			default:
				break;
		}

		$tags || @$comments_link ? $query->group('i.id, i.created') : '';

		$query->order($orderBy);
		$db->setQuery($query, $count_skip, $count_top + $count_reg);
		// echo $db->explain();
		// echo $db->getAffectedRows();
		// echo $query->dump();


		// Retrieve Content
		$items = $db->loadObjectList();

		$lists = array();
		foreach ($items as $i => &$item) {
			$lists[$i] = new stdClass;
			$lists[$i]->id			= '';
			$lists[$i]->title		= $lists[$i]->title_full = '';
			$lists[$i]->link		= '';
			$lists[$i]->date		= '';
			$lists[$i]->image		= $lists[$i]->image_src = $lists[$i]->image_alt = $lists[$i]->image_title = '';
			$lists[$i]->text		= '';
			$lists[$i]->category	= $lists[$i]->category_name = $lists[$i]->category_link = $lists[$i]->category_id = '';
			$lists[$i]->author		= $lists[$i]->author_name = $lists[$i]->author_link = '';
			$lists[$i]->rating		= $lists[$i]->rating_value = $lists[$i]->rating_count = '';
			$lists[$i]->hits		= '';
			$lists[$i]->comments	= $lists[$i]->comments_count = $lists[$i]->comments_link = '';
			$lists[$i]->readmore	= '';

			$lists[$i]->id			= $item->id;
			$lists[$i]->category_id	= $item->category_id ? $item->category_id : '';

			// TOP Items & Regular Items
			if ($i < $count_top) {
				$show_title		= $show_title_top ? 1 : '';
				$limit_title	= $limit_title_top;
				$show_text		= $show_text_top ? 1 : '';
				$limit_text		= $limit_text_top;
				$read_more		= $read_more_top;

				$show_date		= $show_date_top ? 1 : '';
				$date_format	= $date_format_top;
				$show_category	= $show_category_top ? 1 : '';
				$show_author	= $show_author_top ? 1 : '';
				$show_rating	= $show_rating_top ? 1 : '';
				$show_hits		= $show_hits_top ? 1 : '';
				$show_comments	= $show_comments_top ? 1 : '';

				$show_image		= $show_image_top ? 1 : '';
				$image_width	= $image_width_top;
				$image_height	= $image_height_top;
			} else {
				$show_title		= $show_title_reg ? 1 : '';
				$limit_title	= $limit_title_reg;
				$show_text		= $show_text_reg ? 1 : '';
				$limit_text		= $limit_text_reg;
				$read_more		= $read_more_reg;

				$show_date		= $show_date_reg ? 1 : '';
				$date_format	= $date_format_reg;
				$show_category	= $show_category_reg ? 1 : '';
				$show_author	= $show_author_reg ? 1 : '';
				$show_rating	= $show_rating_reg ? 1 : '';
				$show_hits		= $show_hits_reg ? 1 : '';
				$show_comments	= $show_comments_reg ? 1 : '';

				$show_image		= $show_image_reg ? 1 : '';
				$image_width	= $image_width_reg;
				$image_height	= $image_height_reg;
			}


			// Item Link
			if ($access || strpos($userLV, $item->access) !== false) {
				$lists[$i]->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id .':'. urlencode($item->alias), $item->category_id.':'. urlencode($item->category_alias))));
			} else {
				$link	= 'index.php?option=com_users&view=login';
				$menu	= $app->getMenu()->getItems('link', $link);
				$lists[$i]->link = isset($menu[0]) ? JRoute::_($link .'&Itemid='. $menu[0]->id) : JRoute::_($link);
			}

			// Show Title
			if ($show_title) {
				$lists[$i]->title = $limit_title ? self::truncateHTML($item->title, $limit_title, '&hellip;', false, false) : $item->title;
				$lists[$i]->title_full = $item->title;
			}

			// Plugins Support
			$item->introtext = $plugins_support ? JHtml::_('content.prepare', @$item->introtext) : preg_replace('/{[^{]+?{\/.+?}|{.+?}/', '', @$item->introtext);

			// Show Images
			if ($show_image) {
				$img		= array();
				$img_source	= '';
				$image		= md5("Image".$item->id);
				if (JFile::exists(JPATH_SITE .'/media/k2/items/src/'. $image .'.jpg')) {
					$image	= 'media/k2/items/src/'. $image .'.jpg';
				} elseif (JFile::exists(JPATH_SITE .'/media/k2/items/cache/'. $image .'_L.jpg')) {
					$image	= 'media/k2/items/cache/'. $image .'_L.jpg';
				} else {
					$image	= '';
				}

				if ($image_source == 'automatic') {
					if (@$image) {
						$img_source = 'image';
					} else {
						$img_source = 'text';
					}
				} else {
					$img_source = $image_source;
				}

				switch ($img_source) {
					case 'image':
						if (@$image) {
							$img['src'] = $image;
							$img['alt'] = @$item->image_caption;
							$img['ttl'] = $item->title;
						}
					break;
					default:
						$pattern = '/<img[^>]+>/i';
						preg_match($pattern, $item->introtext, $img_tag);
						if (!count($img_tag)) {
							preg_match($pattern, $item->fulltext, $img_tag);
						}
						if (count($img_tag)) {
							preg_match_all('/(alt|title|src)\s*=\s*(["\'])(.*?)\2/i', $img_tag[0], $img_atr);
							$img_atr = array_combine($img_atr[1], $img_atr[3]);
							if (@$img_atr['src']) {
								$img['src'] = trim($img_atr['src']);
								$img['alt'] = trim(@$img_atr['alt']);
								$img['ttl'] = trim(@$img_atr['title']);
								$item->introtext = preg_replace($pattern, '', $item->introtext, 1);
							}
						}
					break;
				}

				if (!@$img['src']) {
					$img['src'] = @$image_default;
					$img['alt'] = JText::_('MOD_RAXO_ALLMODE_K2_NOIMAGE');
				}

				if ($img['src']) {
					// Image Parameters
					$img_src1 = $img_src2 = $img_prm = '';
					if ($image_width || $image_height) {
						$img_src1	= JURI::base(true) .'/modules/mod_raxo_allmode_k2/tools/tb.php?src=';
						$img_src2	.= ($image_width) ? '&amp;w='. $image_width : '';
						$img_src2	.= ($image_height) ? '&amp;h='. $image_height : '';
					}
					if ($image_crop && $image_width && $image_height) {
						$img_src2	.= '&amp;zc=1';
						$img_prm	= ' width="'. $image_width .'" height="'. $image_height .'"';
					}
					$img_src1		= ($img_src1 && strncasecmp($img['src'], "http", 4) !== 0) ? $img_src1 . JURI::base(true) .'/' : $img_src1;
					$img['ttl']		= ($image_title) ? trim(htmlspecialchars($item->title)) : @$img['ttl'];
					$img_prm		.= ($img['ttl']) ? ' title="'. $img['ttl'] .'"' : '';

					// Create Thumbnail
					$lists[$i]->image	= '<img src="'. @$img_src1 . $img['src'] . @$img_src2 .'"'. @$img_prm .' alt="'. @$img['alt'] .'" />';
					$lists[$i]->image	= ($image_link) ? '<a href="'. $lists[$i]->link .'">'. $lists[$i]->image .'</a>' : $lists[$i]->image;
					$lists[$i]->image_src	= @$img['src'];
					$lists[$i]->image_alt	= @$img['alt'];
					$lists[$i]->image_title	= @$img['ttl'];
				}
			}

			// Show Text
			if ($show_text) {
				// Clean XHTML
				if ($intro_clean) {
					$item->introtext = strip_tags($item->introtext, $allowable_tags);
					$item->introtext = str_replace('&nbsp;', ' ', $item->introtext);
					$item->introtext = preg_replace('/\s{2,}/u', ' ', trim($item->introtext));
				}
				// Limit Text
				$lists[$i]->text = $limit_text ? self::truncateHtml($item->introtext, $limit_text, '&hellip;', false, true) : $item->introtext;
			}

			// Show Category
			if ($show_category) {
				$lists[$i]->category_name	= $item->category_name;
				$lists[$i]->category_link	= urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->category_id .':'. urlencode($item->category_alias))));
				$lists[$i]->category		= $category_link ? '<a href="'. $lists[$i]->category_link .'">'. $lists[$i]->category_name .'</a>' : $lists[$i]->category_name;
			}

			// Show Author
			if ($show_author) {
				if ($author_link && !$item->author_alias) {
					$lists[$i]->author_name = $item->author_name;
					$lists[$i]->author_link = JRoute::_(K2HelperRoute::getUserRoute($item->author_id));
					$lists[$i]->author = '<a href="'. $lists[$i]->author_link .'">'. $lists[$i]->author_name .'</a>';
				} else {
					$lists[$i]->author_name = $item->author_alias ? $item->author_alias : $item->author_name;
					$lists[$i]->author = $lists[$i]->author_name;
				}
			}

			// Show Date
			$lists[$i]->date = $show_date ? JHTML::_('date', $item->date, $date_format) : '';

			// Show Readmore
			$lists[$i]->readmore = $read_more ? '<a href="'. $lists[$i]->link .'">'. $read_more .'</a>' : '';

			// Show Hits
			$lists[$i]->hits = $show_hits ? $item->hits : '';

			// Show Rating
			if ($show_rating) {
				$rating_stars = $rating_proc = 0;
				if ($item->rating_count > 0) {
					$rating_stars = floor($item->rating_value);
					$rating_proc = ($item->rating_value - $rating_stars)*100;
				}

				for ($star=0; $star++<5;) {
					$lists[$i]->rating .= '<span class="allmode-star">';
					if ($star <= $rating_stars) {
						$lists[$i]->rating .= '<span></span>';
					} else if ($rating_proc && $star == ceil($item->rating_value)) {
						$lists[$i]->rating .= '<span style="width:'. $rating_proc .'%"></span>';
					}
					$lists[$i]->rating .= '</span>';
				}

				$lists[$i]->rating_value = $item->rating_value;
				$lists[$i]->rating_count = $item->rating_count;
			}

			// Show Comments
			if ($show_comments) {
				$lists[$i]->comments_count	= $item->comments_count;
				$lists[$i]->comments_link	= $comment_system == 'compojoom' ? $lists[$i]->link . $comments_link . $item->id : $lists[$i]->link . $comments_link;
				$lists[$i]->comments		= '<a href="'. $lists[$i]->comments_link .'">'. $item->comments_count .'</a>';
			}

		}

		return $lists;
	}



	/*
	 * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
	 *
	 * @param string $text String to truncate.
	 * @param integer $length Length of returned string, including ellipsis.
	 * @param string $ending Ending to be appended to the trimmed string.
	 * @param boolean $exact If false, $text will not be cut mid-word
	 * @param boolean $considerHtml If true, HTML tags would be handled correctly
	 *
	 * @return string Trimmed string.
	 */
	public static function truncateHtml($text, $length = 320, $ending = '&hellip;', $exact = false, $considerHtml = true)
	{
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (JString::strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$open_tags = array();
			$total_length = $truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
						unset($open_tags[$pos]);
						}
					// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = JString::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length + $content_length > $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entities_length <= $left) {
								$left--;
								$entities_length += JString::strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= JString::substr($line_matchings[2], 0, $left + $entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length >= $length) {
					break;
				}
			}
		} else {
			if (JString::strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = JString::substr($text, 0, $length);
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact && $length > 10) {
			$spacepos = JString::strrpos($truncate, ' ');
			if (isset($spacepos)) {
				$truncate = JString::substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		// close all unclosed html-tags
		if($considerHtml) {
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}
}