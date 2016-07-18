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

class SocialTag extends EasySocial
{
	private $adapter = null;

	// Determines the type id where the tags should appear in
	public $uid = null;

	// Determines the type where the tags should appear in
	public $type = null;

	public function __construct($uid = null, $type = null)
	{
		parent::__construct();

		$this->uid = $uid;
		$this->type = $type;
	}

	public function factory($uid = null, $type = null)
	{
		$obj = new self($uid, $type);

		return $obj;
	}

	/**
	 * Insert a list of tags
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function insert($tags = array(), $type = SOCIAL_TYPE_USER)
	{
		$handler = 'insert' . ucfirst($type);

		return $this->$handler($tags);
	}

	/**
	 * Inserts a list of tagged users
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function insertUser($tags = array(), $authorId = null, $authorType = SOCIAL_TYPE_USER)
	{
		if (!$tags) {
			return false;
		}

		$result = array();

		$author = ES::user($authorId);

		foreach ($tags as $userId) {
			$userId = (int) $userId;

			$table = ES::table('Tag');
			$table->type = 'entity';
			$table->target_id = $this->uid;
			$table->target_type = $this->type;
			$table->item_id = $userId;
			$table->item_type = SOCIAL_TYPE_USER;
			$table->creator_id = $author->id;
			$table->creator_type = $authorType;

			$table->store();

			$result[] = $table;
		}

		return $result;
	}

	/**
	 * Responsible to add the items on the database
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function insertTag($data)
	{
		$table = ES::table('Tag');
		$table->bind($data);

		return $table->store();
	}
}
