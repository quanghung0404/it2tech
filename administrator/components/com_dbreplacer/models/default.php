<?php
/**
 * DB Replacer Default Model
 *
 * @package         DB Replacer
 * @version         4.0.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * DB Replacer Default Model
 */
class DBReplacerModelDefault extends JModelLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to replace in the database
	 *
	 * @access public
	 * @return integer
	 */
	public function replace(&$params)
	{
		if (empty($params->columns))
		{
			return;
		}

		$where = '';
		$s     = str_replace('||space||', ' ', $params->search);
		$r     = str_replace('||space||', ' ', $params->replace);

		$likes = array();
		if ($s != '')
		{
			if ($s == 'NULL')
			{
				$likes[] = '= ""';
				$likes[] = 'IS NULL';
			}
			else if ($s == '*')
			{
				$likes[] = ' != \'something it would never be!!!\'';
			}
			else
			{
				$dbs = $s;

				if (!$params->regex)
				{
					$dbs = preg_quote($dbs);
					// replace multiple whitespace (with at least one enter) with regex whitespace match
					$dbs = preg_replace('#\s*\n\s*#s', '\s*', $dbs);
				}

				// escape slashes
				$dbs = str_replace('\\', '\\\\', $dbs);
				// escape single quotes
				$dbs = str_replace('\'', '\\\'', $dbs);
				// remove the lazy character: doesn't work in mysql
				$dbs = str_replace(array('*?', '+?'), array('*', '+'), $dbs);
				// change \s to [:space:]
				$dbs = str_replace('\s', '[[:space:]]', $dbs);

				if ($params->case)
				{
					$likes[] = 'RLIKE BINARY \'' . $dbs . '\'';
				}
				else
				{
					$likes[] = 'RLIKE \'' . $dbs . '\'';
				}
			}
		}
		if (!empty($likes))
		{
			$where = array();
			foreach ($params->columns as $column)
			{
				foreach ($likes as $like)
				{
					$where[] = '`' . trim($column) . '` ' . $like;
				}
			}
			$where = ' WHERE ( ' . implode(' OR ', $where) . ' )';
		}

		$params->where = trim(str_replace('WHERE ', '', $params->where));
		if ($params->where)
		{
			if ($where)
			{
				$where .= ' AND ( ' . $params->where . ' )';
			}
			else
			{
				$where = ' WHERE ' . $params->where;
			}
		}

		$query = 'SELECT * FROM `' . trim(preg_replace('#^__#', $this->_db->getPrefix(), $params->table)) . '`'
			. $where
			. ' LIMIT ' . (int) $params->max;
		$this->_db->setQuery($query);

		$rows = $this->_db->loadObjectList();

		$count = 0;
		foreach ($rows as $row)
		{
			$set   = array();
			$where = array();

			foreach ($row as $key => $val)
			{
				if ($val != '' && $val !== null && $val != '0000-00-00')
				{
					$where[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($val);
				}

				if (!in_array($key, $params->columns))
				{
					continue;
				}

				if ($s == 'NULL')
				{
					if ($val == '' || $val === null || $val == '0000-00-00')
					{
						$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($r);
					}
					continue;
				}

				if ($s == '*')
				{
					$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($r);
					continue;
				}

				$dbs = $s;
				if (!$params->regex)
				{
					$dbs = preg_quote($dbs, '#');
					// replace multiple whitespace (with at least one enter) with regex whitespace match
					$dbs = preg_replace('#\s*\n\s*#s', '\s*', $dbs);
					$dbs = str_replace('\[[:space:]]', '\s*', $dbs);
				}
				$dbs = '#' . $dbs . '#s';
				if (!$params->case)
				{
					$dbs .= 'i';
				}
				if ($params->regex && $params->utf8)
				{
					$dbs .= 'u';
				}

				if (!@preg_match($dbs, $val))
				{
					continue;
				}

				$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote(preg_replace($dbs, $r, $val));
			}

			if (!empty($set) && !empty($where))
			{
				$where = ' WHERE ( ' . implode(' AND ', $where) . ' )';
				if ($params->where)
				{
					$where .= ' AND ( ' . $params->where . ' )';
				}

				$query = 'UPDATE `' . trim(preg_replace('#^__#', $this->_db->getPrefix(), $params->table)) . '`'
					. ' SET ' . implode(', ', $set)
					. $where
					. ' LIMIT 1';
				$this->_db->setQuery($query);

				if (!$this->_db->execute())
				{
					JFactory::getApplication()->enqueueMessage(JText::_('???'), 'error');
				}
				else
				{
					$count++;
				}
			}
		}

		if (!$count)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('DBR_NO_ROWS_UPDATED'), 'notice');

			return;
		}

		JFactory::getApplication()->enqueueMessage(JText::sprintf('DBR_ROWS_UPDATED', $count), 'message');
	}
}
