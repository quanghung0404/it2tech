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

class JFormFieldNN_Form2Content extends NNFormGroupField
{
	public $type = 'Form2Content';
	public $default_group = 'Projects';

	protected function getInput()
	{
		if ($error = $this->missingFilesOrTables(array('projects' => 'project'), '', 'f2c'))
		{
			return $error;
		}

		return $this->getSelectList();
	}

	function getProjects()
	{
		$query = $this->db->getQuery(true)
			->select('t.id, t.title as name')
			->from('#__f2c_project AS t')
			->where('t.published = 1')
			->order('t.title, t.id');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list);
	}
}
