<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/tables/table');

/**
 * Access rules table
 *
 * @author	Jason Rey <jasonrey@stackideas.com>
 * @since	1.2
 */
class SocialTableAccessRules extends SocialTable
{
	/**
	 * The id of the access rule
	 * @var int
	 */
	public $id = null;

	/**
	 * The name of the access rule
	 * @var string
	 */
	public $name = null;

	/**
	 * The title of the access rule
	 * @var string
	 */
	public $title = null;

	/**
	 * The description of the access rule
	 * @var string
	 */
	public $description = null;

	/**
	 * The extension of the access rule
	 * @var string
	 */
	public $extension = null;

	/**
	 * The element of the access rule
	 * @var string
	 */
	public $element = null;

	/**
	 * The group of the access rule
	 * @var string
	 */
	public $group = null;

	/**
	 * The published state of the access rule
	 * @var string
	 */
	public $state = null;

	/**
	 * The created date of the access rule
	 * @var datetime
	 */
	public $created = null;

	/**
	 * The parameters in JSON string.
	 * @var string
	 */
	public $params = null;

	public function __construct(& $db)
	{
		parent::__construct('#__social_access_rules', 'id', $db);
	}

	public function load($keys = null, $reset = true)
	{
		$state = parent::load($keys, $reset);

		if (!$state)
		{
			return false;
		}

		$this->extractParams();

		return true;
	}

	public function bind($src, $ignore = array())
	{
		$state = parent::bind($src, $ignore);

		if (!$state)
		{
			return false;
		}

		$this->extractParams();

		return true;
	}

	public function extractParams()
	{
		$params = FD::makeObject($this->params);

		if (empty($params))
		{
			return;
		}

		foreach ($params as $key => $value)
		{
			$this->$key = $value;
		}
	}
}
