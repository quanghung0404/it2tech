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

class JFormFieldNN_FlexiContent extends NNFormGroupField
{
	public $type = 'FlexiContent';
	public $default_group = 'Tags';

	protected function getInput()
	{
		if ($error = $this->missingFilesOrTables(array('tags', 'types')))
		{
			return $error;
		}

		return $this->getSelectList();
	}

	function getTags()
	{
		$query = $this->db->getQuery(true)
			->select('t.name as id, t.name')
			->from('#__flexicontent_tags AS t')
			->where('t.published = 1')
			->order('t.name');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list);
	}

	function getTypes()
	{
		$query = $this->db->getQuery(true)
			->select('t.id, t.name')
			->from('#__flexicontent_types AS t')
			->where('t.published = 1')
			->order('t.name, t.id');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list);
	}
}
