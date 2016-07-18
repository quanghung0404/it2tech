<?php
/**
 * @package         ReReplacer
 * @version         6.2.0PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * List Model
 */
class ReReplacerModelList extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'ordering', 'a.ordering',
				'state', 'a.published',
				'name', 'a.name',
				'description', 'a.description',
				'category', 'a.category',
				'search', 'a.search',
				'replace', 'a.replace',
				'id', 'a.id',
			);
		}

		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$this->parameters = NNParameters::getInstance();

		parent::__construct($config);
	}

	/**
	 * @var        string    The prefix to use with controller messages.
	 */
	protected $text_prefix = 'NN';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.ordering', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param    string    A prefix for the store id.
	 *
	 * @return    string    A store id.
	 */
	protected function getStoreId($id = '', $getall = 0)
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $getall;

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select(
				$this->getState(
					'list.select',
					'a.*'
				)
			)
			->from($db->quoteName('#__rereplacer', 'a'));

		$filter = $this->getState('filter.state');
		if (is_numeric($filter))
		{
			$query->where('a.published = ' . ( int ) $filter);
		}
		else if ($filter === '')
		{
			$query->where('( a.published IN ( 0,1,2 ) )');
		}

		$filter = $this->getState('filter.category');
		if ($filter != '')
		{
			$query->where('a.category = ' . $db->q($filter));
		}

		$filter = $this->getState('filter.casesensitive');
		if ($filter != '')
		{
			$query->where('a.params LIKE ' . $db->q('%"casesensitive":"' . $filter . '"%'));
		}

		$filter = $this->getState('filter.regex');
		if ($filter != '')
		{
			$query->where('a.params LIKE ' . $db->q('%"regex":"' . $filter . '"%'));
		}

		$filter = $this->getState('filter.enable_in_admin');
		if ($filter != '')
		{
			$query->where('a.params LIKE ' . $db->q('%"enable_in_admin":"' . $filter . '"%'));
		}

		$filter = $this->getState('filter.area');
		if ($filter != '')
		{
			$query->where('a.area = ' . $db->q($filter));
		}

		// Filter the list over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . ( int ) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where(
					'( `name` LIKE ' . $search .
					' OR `description` LIKE ' . $search .
					' OR `category` LIKE ' . $search .
					' OR `search` LIKE ' . $search .
					' OR `replace` LIKE ' . $search . ' )'
				);
			}
		}

		// Add the list ordering clause.
		$ordering = $this->getState('list.ordering', 'a.ordering');
		if ($ordering == 'a.ordering')
		{
			$query->order("( `area` != 'articles' )")
				->order("( `area` != 'component' )")
				->order("( `area` != 'body' AND `area` != '' )")
				->order("( `area` != 'everywhere' )");
		}
		$query->order($db->escape($ordering) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}

	public function getItems($getall = 0)
	{
		// Get a storage key.
		$store = $this->getStoreId('', $getall);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();
		if ($getall)
		{
			$this->_db->setQuery($query);
			$items = $this->_db->loadObjectList();
		}
		else
		{
			$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		}

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		foreach ($items as $i => $item)
		{
			$isini  = ((substr($item->params, 0, 1) != '{') && (substr($item->params, -1, 1) != '}'));
			$params = $this->parameters->getParams($item->params, JPATH_ADMINISTRATOR . '/components/com_rereplacer/item_params.xml');
			foreach ($params as $key => $val)
			{
				if (!isset($item->$key) && !is_object($val))
				{
					$items[$i]->$key = $val;
				}
			}
			unset($items[$i]->params);

			if ($isini)
			{
				foreach ($items[$i] as $key => $val)
				{
					if (is_string($val))
					{
						$items[$i]->$key = stripslashes($val);
					}
				}
			}
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $items;
	}

	/**
	 * Import Method
	 * Import the selected items specified by id
	 * and set Redirection to the list of items
	 */
	function import($model)
	{
		$file = JRequest::getVar('file', '', 'files', 'array');

		if (!is_array($file) || !isset($file['name']))
		{
			$msg = JText::_('RR_PLEASE_CHOOSE_A_VALID_FILE');
			JFactory::getApplication()->redirect('index.php?option=com_rereplacer&view=list&layout=import', $msg);
		}

		$ext = explode(".", $file['name']);

		if ($ext[count($ext) - 1] != 'rrbak')
		{
			$msg = JText::_('RR_PLEASE_CHOOSE_A_VALID_FILE');
			JFactory::getApplication()->redirect('index.php?option=com_rereplacer&view=list&layout=import', $msg);
		}

		jimport('joomla.filesystem.file');
		$publish_all = JFactory::getApplication()->input->getInt('publish_all', 0);

		$data = file_get_contents($file['tmp_name']);

		if (empty($data))
		{
			JFactory::getApplication()->redirect('index.php?option=com_snippets&view=list', JText::_('File is empty!'));

			return;
		}

		if ($data['0'] == '<')
		{
			// Old format
			$data = explode('<RR_ITEM_START>', $data);

			$items = array();
			foreach ($data as $data_item)
			{
				$data_item = trim(str_replace('<RR_ITEM_END>', '', $data_item));
				if ($data_item)
				{
					$data_item_keyvals = explode('<RR_KEY>', $data_item);
					$item              = array();
					foreach ($data_item_keyvals as $data_item_keyval)
					{
						$data_item_keyval = trim(str_replace('<RR_END>', '', $data_item_keyval));
						if ($data_item_keyval)
						{
							$data_item_keyval             = explode('<RR_VAL>', $data_item_keyval);
							$item[$data_item_keyval['0']] = (isset($data_item_keyval['1'])) ? $data_item_keyval['1'] : '';
						}
					}
				}
			}
		}
		else
		{
			$items = json_decode($data, true);
			if (is_null($items))
			{
				$items = array();
			}
		}

		$msg = JText::_('Items saved');

		foreach ($items as $item)
		{
			$item['id'] = 0;
			if ($publish_all == 0)
			{
				unset($item['published']);
			}
			else if ($publish_all == 1)
			{
				$item['published'] = 1;
			}
			$items[] = $item;

			$saved = $model->save($item);
			if ($saved != 1)
			{
				$msg = JText::_('Error Saving Item') . ' ( ' . $saved . ' )';
			}
		}

		JFactory::getApplication()->redirect('index.php?option=com_rereplacer&view=list', $msg);
	}

	/**
	 * Export Method
	 * Export the selected items specified by id
	 */
	function export($ids)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('r.name')
			->select('r.description')
			->select('r.category')
			->select('r.search')
			->select('r.replace')
			->select('r.area')
			->select('r.params')
			->select('r.published')
			->select('r.ordering')
			->from('#__rereplacer as r')
			->where('r.id IN ( ' . implode(', ', $ids) . ' )');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$string = json_encode($rows);

		$filename = 'ReReplacer Items';
		if (count($rows) == 1)
		{
			$name = JString::strtolower(html_entity_decode($rows['0']->name));
			$name = preg_replace('#[^a-z0-9_-]#', '_', $name);
			$name = trim(preg_replace('#__+#', '_', $name), '_-');

			$filename = 'ReReplacer Item (' . $name . ')';
		}

		// SET DOCUMENT HEADER
		if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "Opera";
		}
		elseif (preg_match('#MSIE ([0-9].[0-9]{1,2})#', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "IE";
		}
		else
		{
			$UserBrowser = '';
		}
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
		@ob_end_clean();
		ob_start();

		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

		if ($UserBrowser == 'IE')
		{
			header('Content-Disposition: inline; filename="' . $filename . '.rrbak"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $filename . '.rrbak"');
			header('Pragma: no-cache');
		}

		// PRINT STRING
		echo $string;
		die;
	}

	/**
	 * Copy Method
	 * Copy all items specified by array cid
	 * and set Redirection to the list of items
	 */
	function copy($ids, $model)
	{
		foreach ($ids as $id)
		{
			$model->copy($id);
		}

		$msg = JText::sprintf('Items copied', count($ids));
		JFactory::getApplication()->redirect('index.php?option=com_rereplacer&view=list', $msg);
	}

	function getHasCategories()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('count(id)')
			->from($db->quoteName('#__rereplacer'))
			->where($db->quoteName('category') . ' != ' . $db->quote(''));

		$db->setQuery($query);

		return $db->loadResult();
	}
}
