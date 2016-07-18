<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

// Import main table.
ED::import('admin:/tables/table');

class DiscussAttachments extends EasyDiscussTable
{
	public $id = null;
	public $uid	= null;
	public $title = null;
	public $type = null;
	public $path = null;
	public $created	= null;
	public $published = null;
	public $mime = null;
	public $storage = 'joomla';

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_attachments', 'id', $db);
	}
}
