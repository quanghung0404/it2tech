<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

ED::import('admin:/tables/table');

class DiscussRanksUsers extends EasyDiscussTable
{
	public $id = null;
	public $rank_id	= null;
	public $user_id	= null;
	public $created	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__discuss_ranks_users', 'id', $db);
	}

	public function load( $id = null , $byUserId = false)
	{
		static $users = null;

		if (!isset($users[$id])) {
			
			if ($byUserId) {
				$db = DiscussHelper::getDBO();
				$query = 'SELECT * FROM `#__discuss_ranks_users` WHERE `user_id` = ' . $db->Quote($byUserId);
				$query .= ' ORDER BY `created` DESC LIMIT 1';

				$db->setQuery($query);
				$result = $db->loadObject();

				if ($result) {
					$this->bind( $result );
				}
			} else {
				parent::load($id);
			}
			$users[$id] = $this;
		} else {
			$this->bind($users[$id]);
		}

		return $users[$id];
	}

	/**
	 * Override parent's bind implementation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind($data, $ignore = array())
	{
		parent::bind($data);

		if (!$this->created) {
			$this->created = ED::date()->toSql();
		}

		return true;
	}
}
