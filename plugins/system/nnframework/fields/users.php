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

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNN_Users extends NNFormField
{
	public $type = 'Users';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		if (!is_array($this->value))
		{
			$this->value = explode(',', $this->value);
		}

		$options = $this->getUsers();

		$size     = (int) $this->get('size');
		$multiple = $this->get('multiple');

		require_once dirname(__DIR__) . '/helpers/html.php';

		return NNHtml::selectlistsimple($options, $this->name, $this->value, $this->id, $size, $multiple);
	}

	function getUsers()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(u.id)')
			->from('#__users AS u')
			->where('u.block = 0');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$query->clear('select')
			->select('u.name, u.username, u.id')
			->order('name');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('username', 'id'));
	}
}
