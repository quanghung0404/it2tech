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

// Include parent formatter
require_once(dirname(__FILE__) . '/types/standard.php');

class EasyBlogFormatter extends EasyBlog
{
	private $type 	= null;
	private $items	= null;
	private $cache	= null;

	public function __construct($type, $items, $cache = true)
	{
		parent::__construct();

		$this->type = $type;
		$this->items = $items;
		$this->cache = $cache;
	}

	public function execute()
	{
		// If there's no items, skip this altogether
		if (empty($this->items)) {
			return $this->items;
		}

		$fileName = $this->type;

		if ($this->doc->getType() == 'json') {
			$fileName = $fileName . '.json';
		}

		require_once(dirname(__FILE__) . '/types/' . $fileName . '.php');

		$class = 'EasyBlogFormatter' . ucfirst($this->type);

		$obj = new $class($this->items, $this->cache);

		return $obj->execute();
	}
}
