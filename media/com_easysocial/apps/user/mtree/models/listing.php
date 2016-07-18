<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/includes/model');

class ListingModel extends EasySocialModel
{
	public function getConfig()
	{
		require(JPATH_ROOT.'/administrator/components/com_mtree/config.mtree.class.php');
		
		$mtconf	= mtFactory::getConfig();

		return $mtconf;
	}

	public function getItemid()
	{
		$menu = JFactory::getApplication()->getMenu();

		$items = $menu->getItems('link', 'index.php?option=com_mtree&view=home');

		if (!$items) {
			$items = $menu->getItems('link', 'index.php?option=com_mtree&view=listcats&cat_id=0');
		}

		return isset($items[0]) ? '&Itemid='.$items[0]->id : '';
	}

	/**
	 * Retrieves a list of favorites by a specific user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getFavorites($userId = null, $limit = 0)
	{
		$mtconf = $this->getConfig();

		$db = FD::db();
		$nullDate = $db->getNullDate();
		$date = JFactory::getDate();
		$now = $date->toSql();

		# Retrieve Links
		$sql = "SELECT DISTINCT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.*, img.filename AS link_image "
			. "FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat, #__mt_favourites AS f)"
			. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			. "\n WHERE link_published='1' AND link_approved='1' AND f.user_id='".$userId."' AND f.link_id = l.link_id "
			. "\n AND l.link_id = cl.link_id AND cl.main = '1'"
			. "\n AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= '$now' ) "
			. "\n AND cl.cat_id = cat.cat_id ";

		if ($limit != 0) {
			$sql .= ' LIMIT 0,' . $limit;
		}

		$db->setQuery($sql);

		$favorites = $db->loadObjectList();

		if (!$favorites) {
			return $favorites;
		}

		$this->decorate($favorites);

		return $favorites;
	}

	/**
	 * Retrieves a list of reviews created by a specific user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getReviews($userId = null, $limit = 0)
	{
		$mtconf = $this->getConfig();

		$db = FD::db();

		$sql = "SELECT r.*, l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, cat.*, u.username, log.value AS rating, img.filename AS link_image FROM `#__mt_reviews` AS r"
			.	"\nLEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			.	"\nLEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1"
			.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id AND cl.main = 1"
			.	"\n LEFT JOIN #__mt_cats AS cat ON cl.cat_id = cat.cat_id "
			.	"\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id "
			.	"\nWHERE r.user_id = '".$userId."' AND r.rev_approved = 1 AND l.link_published='1' AND link_approved='1'"
			.	"\nORDER BY r.rev_date DESC";

		if ($limit != 0) {
			$sql .= ' LIMIT 0,' . $limit;
		}

		# Retrieve reviews
		$db->setQuery($sql);

		$reviews = $db->loadObjectList();



		if (!$reviews) {
			return $reviews;
		}

		$this->decorate($reviews);

		return $reviews;
	}

	/**
	 * Retrieves a list of listings created by a specific user.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getListings($userId = null, $limit = 0)
	{
		$mtconf = $this->getConfig();
		$db = FD::db();

		// Retrieve the null date
		$date = JFactory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();

		$query = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.*, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat)"
			. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
			. "\n WHERE link_published='1' AND link_approved='1' AND " 
			. "\n user_id='".$userId."' "
			. "\n AND l.link_id = cl.link_id AND cl.main = '1'"
			. "\n AND ( publish_up = " . $db->Quote($nullDate) . " OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = " . $db->Quote($nullDate) . " OR publish_down >= '$now' ) "
			. "\n AND cl.cat_id = cat.cat_id "
			. "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';

		if ($limit != 0) {
			$query .= ' LIMIT 0,' . $limit;
		}

		$db->setQuery($query);

		$items = $db->loadObjectList();

		if (!$items) {
			return $items;
		}

		$this->decorate($items);

		return $items;
	}

	/**
	 * Decorates a result set
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function decorate(&$items)
	{
		$Itemid = $this->getItemid();

		foreach ($items as &$item) {

			// Decorate the object
			$item->link_avatar = $this->getAvatar($item->link_image);

			// Get the pathway
			$pathway = $this->getPathway($item->cat_id);
				
			$item->category = $pathway->getCatName($item->cat_id);

			$item->ratings = $this->getRatings($item->link_rating);

			$item->hyperlink = JRoute::_('index.php?option=com_mtree&task=viewlink&link_id=' . $item->link_id . '&Itemid=' . $Itemid);

			$item->categoryLink = JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$item->cat_id . '&Itemid=' . $Itemid);
		}
	}

	/**
	 * Retrieves the avatar of a link
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAvatar($image)
	{
		$mtconf = $this->getConfig();

		if (is_null($image)) {
			$image = $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_images') . '/noimage_thb.png';

			return $image;
		}

		$avatar = $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_listing_small_image') . $image;

		return $avatar;
	}

	/**
	 * Retrieves the ratings
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getRatings($rating)
	{
		$mtconf = $this->getConfig();
		$stars = floor($rating);
		$html = '';

		// Print stars
		for ($i = 0; $i < $stars; $i++) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="14" height="14" hspace="1" class="star" alt="★" />';
		}

		if (($rating - $stars) >= 0.5 && $stars > 0) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_05.png" width="14" height="14" hspace="1" class="star" alt="½" />';
			$stars += 1;
		}

		// Blank stars
		for ($i = $stars; $i < 5; $i++) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="14" height="14" hspace="1" class="star" alt="" />';
		}

		return $html;
	}

	/**
	 * Retrieves the mosets pathway object
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPathway($categoryId)
	{
		require_once(JPATH_ROOT.'/administrator/components/com_mtree/admin.mtree.class.php');
		
		$pathway = new mtPathWay($categoryId);

		return $pathway;
	}
}
