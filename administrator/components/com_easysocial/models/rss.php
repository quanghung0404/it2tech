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

jimport('joomla.application.component.model');

FD::import('admin:/includes/model');

class EasySocialModelRss extends EasySocialModel
{
	public function __construct($config = array())
	{
		parent::__construct('rss', $config);
	}

	/**
	 * Retrieves a list of feeds created by a particular user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$userId		The user's / creator's id.
	 *
	 * @return	Array				A list of notes item.
	 */
	public function getItems($id, $type)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_rss');
		$sql->where('uid', $id);
		$sql->where('type', $type);
		$sql->order('created', 'DESC');
		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}

}
