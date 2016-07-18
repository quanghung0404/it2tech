<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussModules extends EasyDiscuss
{
	/**
     * Format the discussion posts data
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function format($posts)
	{
		if (!$posts) {
			return;
		}

		$results = array();

		foreach ($posts as $post) {
			$result = ED::post($post->id);

			// Assign author info
			$result->user = $result->getOwner();

			$results[] = $result;
		}

		return $results;
	}

    /**
     * Method to get the data from modules
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function getData($options = array())
	{
		$params = $options['params'];
		$sort = isset($options['sort']) ? $options['sort'] : 'latest';

		$count = (INT)trim($params->get('count', 0));
		$categoryIds = trim($params->get('category_id', 0));

		if ($categoryIds) {
			// Remove white space
			$categoryIds = preg_replace('/\s+/', '', $categoryIds);
			$categoryIds = explode( ',', $categoryIds );
		}

		$model = ED::model('Posts');

		// If category id is exists, let just load the post by categories.
		if ($categoryIds) {
			$posts = $model->getPostsBy('category', $categoryIds, $sort, null, DISCUSS_FILTER_PUBLISHED, '', $count);
			$posts = $this->format($posts);

			return $posts;
		}

		$posts = $model->getPostsBy('', '', $sort, null, DISCUSS_FILTER_PUBLISHED, '', $count);
		$posts = $this->format($posts);

		return $posts;
	}

	/**
     * Retrieve return URL
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function getReturnURL($params, $isLogged=false)
	{
		$type = empty($isLogged) ? 'login' : 'logout';

		if ($itemid = $params->get($type)) {
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getItem($itemid);

			if ($item) {
				$url = $item->link . '&Itemid=' . $itemid;
			} else {
                $url = JUri::getInstance()->toString();
			}
		} else {
			$url = 'index.php?option=com_easydiscuss';
		}

		return base64_encode($url);
	}

	/**
     * Get login status
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function getLoginStatus()
	{
		$user = JFactory::getUser();
		return (!empty($user->id)) ? true : false;
	}
}

