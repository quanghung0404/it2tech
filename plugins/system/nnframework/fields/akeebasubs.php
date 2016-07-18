<?php
/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/groupfield.php';

class JFormFieldNN_AkeebaSubs extends NNFormGroupField
{
	public $type = 'AkeebaSubs';
	public $default_group = 'Levels';

	protected function getInput()
	{
		if ($error = $this->missingFilesOrTables(array('levels')))
		{
			return $error;
		}

		return $this->getSelectList();
	}

	function getLevels()
	{
		$query = $this->db->getQuery(true)
			->select('l.akeebasubs_level_id as id, l.title AS name, l.enabled as published')
			->from('#__akeebasubs_levels AS l')
			->where('l.enabled > -1')
			->order('l.title, l.akeebasubs_level_id');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('id'));
	}
}
