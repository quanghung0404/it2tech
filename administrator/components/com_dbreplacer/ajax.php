<?php
/**
 * Ajax load page
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

if (JFactory::getApplication()->isSite())
{
	die();
}

$class = new DBReplacer;
echo $class->render();
die;

class DBReplacer
{
	function render()
	{
		$this->db = JFactory::getDbo();

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters   = NNParameters::getInstance();
		$this->config = $parameters->getComponentParams('com_dbreplacer');

		$field  = JFactory::getApplication()->input->get('field', 'table');
		$params = JFactory::getApplication()->input->getBase64('params');

		$params = str_replace(
			array('[-CHAR-LT-]', '[-CHAR-GT-]'),
			array('<', '>'),
			urldecode(base64_decode($params))
		);

		$params = json_decode($params);
		if (is_null($params))
		{
			$params = new stdClass;
		}

		if (empty($params->columns) && $params->table && $params->table == trim(str_replace('#__', $this->db->getPrefix(), $this->config->default_table)))
		{
			$params->columns = explode(',', $this->config->default_columns);
		}

		$this->params = $params;

		switch ($field)
		{
			case 'rows':
				return $this->renderRows();

			case 'columns':
			default:
				return $this->renderColumns();
		}
	}

	function renderColumns()
	{
		$table    = $this->params->table;
		$selected = $this->implodeParams($this->params->columns);

		$options = array();
		if ($table)
		{
			$cols = $this->getColumns();
			foreach ($cols as $col)
			{
				$options[] = JHtml::_('select.option', $col, $col, 'value', 'text', 0);
			}
		}

		$html = '<strong>' . $this->params->table . '</strong><br />';
		$html .= JHtml::_('select.genericlist', $options, 'columns[]', 'multiple="multiple" size="20" class="dbr_element"', 'value', 'text', $selected, 'paramscolumns');

		return $html;
	}

	function getColumns()
	{
		if (preg_match('#[^a-z0-9-_\#]#i', $this->params->table))
		{
			die('Invalid data found in URL!');
		}

		$query = 'SHOW COLUMNS FROM `' . trim($this->params->table) . '`';
		$this->db->setQuery($query);
		$columns = $this->db->loadColumn();

		return $columns;
	}

	function renderRows()
	{
		// Load plugin language
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('com_dbreplacer');

		$max = (int) $this->config->max_rows;

		if (!$this->params->table)
		{
			return '';
		}

		$html = array();

		$html[] = '<p><a class="btn btn-default" onclick="nnDBReplacer.toggleInactiveColumns();">' . JText::_('DBR_TOGGLE_INACTIVE_COLUMNS') . '</a></p>';

		$columns = $this->implodeParams($this->params->columns);

		$cols = $this->getColumns();
		$rows = $this->getRows($max);
		if (count($rows) > $max - 1)
		{
			$html[] = '<p>' . JText::sprintf('DBR_MAXIMUM_ROW_COUNT_REACHED', $max) . '</p>';
		}
		else
		{
			$html[] = '<p>' . JText::sprintf('DBR_ROW_COUNT', count($rows)) . '</p>';
		}

		$html[] = '<table class="table table-striped" id="dbr_results">';
		$html[] = '<thead><tr>';
		foreach ($cols as $col)
		{
			$class = '';
			if (!in_array($col, $columns))
			{
				$class = 'ghosted';
			}
			$html[] = '<th class="' . $class . '">' . $col . '</th>';
		}
		$html[] = '</tr></thead>';
		if ($rows && !empty($rows))
		{
			$html[] = '<tbody>';
			$html[] = $this->getTableRow($rows, $cols);
			$html[] = '</tbody>';
		}
		$html[] = '</table>';

		return implode("\n", $html);
	}

	function getTableRow($rows, $cols)
	{
		$columns = $this->implodeParams($this->params->columns);
		$search  = str_replace('||space||', ' ', $this->params->search);
		$replace = str_replace('||space||', ' ', $this->params->replace);
		$s1      = '|' . md5('<SEARCH TAG>') . '|';
		$s2      = '|' . md5('</SEARCH TAG>') . '|';
		$r1      = '|' . md5('<REPLACE TAG>') . '|';
		$r2      = '|' . md5('</REPLACE TAG>') . '|';

		foreach ($rows as $row)
		{
			$html[] = '<tr>';
			foreach ($cols as $col)
			{
				$class = '';
				$val   = $row->$col;
				if (!in_array($col, $columns))
				{
					$class = 'ghosted';
					if ($val == '' || $val === null || $val == '0000-00-00')
					{
						if ($val === null)
						{
							$val = 'NULL';
						}
						$val = '<span class="null">' . $val . '</span>';
					}
					else
					{
						$val = preg_replace('#^((.*?\n){4}).*?$#si', '\1...', $val);
						if (strlen($val) > 300)
						{
							$val = substr($val, 0, 300) . '...';
						}
						$val = htmlentities($val, ENT_COMPAT, 'utf-8');
					}
				}
				else
				{
					if ($search == 'NULL')
					{
						if ($val == '' || $val === null || $val == '0000-00-00')
						{
							if ($val === null)
							{
								$val = 'NULL';
							}
							if ($val === '')
							{
								$val = '&nbsp;';
							}
							$val = '<span class="search_string"><span class="null">' . $val . '</span></span><span class="replace_string">' . $replace . '</span>';
						}
						else
						{
							$val = preg_replace('#^((.*?\n){4}).*?$#si', '\1...', $val);
							if (strlen($val) > 300)
							{
								$val = substr($val, 0, 300) . '...';
							}
							$val = htmlentities($val, ENT_COMPAT, 'utf-8');
						}
					}
					else if ($search == '*')
					{
						$class = 'search_string';
						if (strlen($val) > 50)
						{
							$val = '*';
							$class .= ' no-strikethrough';
						}

						$val = '<span class="' . $class . '"><span class="null">' . $val . '</span></span><span class="replace_string">' . $replace . '</span>';
					}
					else
					{
						if ($val === null)
						{
							$val = '<span class="null">NULL</span>';
						}
						else
						{
							$match = 0;
							if ($search != '')
							{
								$s = $search;
								if (!$this->params->regex)
								{
									$s = preg_quote($s, '#');
									// replace multiple whitespace (with at least one enter) with regex whitespace match
									$s = preg_replace('#\s*\n\s*#s', '\s*', $s);
								}
								$s = '#' . $s . '#s';
								if (!$this->params->case)
								{
									$s .= 'i';
								}
								if ($this->params->regex && $this->params->utf8)
								{
									$s .= 'u';
								}
								$r = $s1 . '\0' . $s2 . $r1 . $replace . $r2;

								$match = @preg_match($s, $val);
							}

							if ($match)
							{
								$class = 'has_search';
								$val   = preg_replace($s, $r, $val);
								$val   = htmlentities($val, ENT_COMPAT, 'utf-8');
								$val   = str_replace(' ', '&nbsp;', $val);
								$val   = str_replace($s1, '<span class="search_string">', str_replace($s2, '</span>', $val));
								$val   = str_replace($r1, '<span class="replace_string">', str_replace($r2, '</span>', $val));
							}
							else
							{
								$val = preg_replace('#^((.*?\n){4}).*?$#si', '\1...', $val);
								if (strlen($val) > 300)
								{
									$val = substr($val, 0, 300) . '...';
								}
								$val = htmlentities($val, ENT_COMPAT, 'utf-8');
							}

							if ($val == '0000-00-00')
							{
								$val = '<span class="null">' . $val . '</span>';
							}
						}
					}
				}
				$val    = nl2br($val);
				$html[] = '<td class="db_value ' . $class . '">' . $val . '</td>';
			}
			$html[] = '</tr>';
		}

		return implode('', $html);
	}

	function getRows($max = 100)
	{
		if (preg_match('#[^a-z0-9-_\#]#i', $this->params->table))
		{
			die('Invalid data found in URL!');
		}

		$table   = $this->params->table;
		$columns = $this->params->columns;

		$s = str_replace('||space||', ' ', $this->params->search);

		$where = '';
		if ($columns)
		{
			$likes = array();
			if ($s != '')
			{
				if ($s == 'NULL')
				{
					$likes[] = 'IS NULL';
					$likes[] = '= ""';
				}
				else if ($s == '*')
				{
					$likes[] = ' != \'something it would never be!!!\'';
				}
				else
				{
					$dbs = $s;

					if (!$this->params->regex)
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

					if ($this->params->case)
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
				$columns = $this->implodeParams($columns);
				$where   = array();
				foreach ($columns as $column)
				{
					foreach ($likes as $like)
					{
						$where[] = '`' . trim($column) . '` ' . $like;
					}
				}
				$where = ' WHERE ( ' . implode(' OR ', $where) . ' )';
			}
		}
		$pwhere = trim($this->params->where);
		$pwhere = trim(str_replace('WHERE ', '', $pwhere));
		if ($pwhere)
		{
			if ($where)
			{
				$where .= ' AND ( ' . $pwhere . ' )';
			}
			else
			{
				$where = ' WHERE ' . $pwhere;
			}
		}

		$query = 'SELECT * FROM `' . trim($table) . '`'
			. $where
			. ' LIMIT ' . (int) $max;
		$this->db->setQuery($query);

		return $this->db->loadObjectList();
	}

	function implodeParams($params)
	{
		if (is_array($params))
		{
			return $params;
		}
		$params = explode(',', $params);
		$p      = array();
		foreach ($params as $param)
		{
			if (trim($param) != '')
			{
				$p[] = trim($param);
			}
		}

		return array_unique($p);
	}
}
