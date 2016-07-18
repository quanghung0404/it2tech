<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableBlock extends EasyBlogTable
{
	public $id = null;
	public $element = null;
	public $group = null;
	public $title = null;
	public $description = null;
	public $published = null;
	public $created = null;
	public $ordering = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_composer_blocks' , 'id' , $db);
	}

	public function getCreated()
	{
		$date = EB::date($this->created);

		return $date;
	}

	/**
	 * Publishes a block
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Set the state
		$this->published = 1;

		// Store the post
		return $this->store();
	}

	/**
	 * Unpublishes a block
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		// Set the state
		$this->published = 0;

		// Store the post
		return $this->store();
	}
}
