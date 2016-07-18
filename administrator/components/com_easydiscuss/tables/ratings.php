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
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussRatings extends EasyDiscussTable
{
	var $id = null;
	var $uid = null;
	var $type	= null;
	var $created_by	= null;
	var $sessionid = null;
	var $value = null;
	var $ip = null;
	var $published = null;
	var $created = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db)
	{
		parent::__construct('#__discuss_ratings', 'id', $db);
	}
}