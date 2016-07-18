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

jimport('joomla.application.component.modeladmin');

/**
 * Item Model
 */
class ReReplacerModelItem extends JModelAdmin
{
	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        JController
	 */
	public function __construct()
	{
		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$this->parameters = NNParameters::getInstance();

		parent::__construct();
	}

	/**
	 * @var        string    The prefix to use with controller messages.
	 */
	protected $text_prefix = 'NN';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param    object $record A record object.
	 *
	 * @return    boolean    True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
		if ($record->published != -2)
		{
			return false;
		}
		$user = JFactory::getUser();

		return $user->authorise('core.admin', 'com_rereplacer');
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param    object $record A record object.
	 *
	 * @return    boolean    True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check the component since there are no categories or other assets.
		return $user->authorise('core.admin', 'com_rereplacer');
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param    type      The table type to instantiate
	 * @param    string    A prefix for the table class name. Optional.
	 * @param    array     Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 */
	public function getTable($type = 'Item', $prefix = 'ReReplacerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data ( default case ), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_rereplacer.item',
			'item',
			array('control' => 'jform', 'load_data' => $loadData)
		);
		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if ($this->canEditState((object ) $data) != true)
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_rereplacer.edit.item.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null, $getform = 0)
	{
		// Initialise variables.
		$pk    = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item       = JArrayHelper::toObject($properties, 'JObject');

		$isini  = ((substr($item->params, 0, 1) != '{') && (substr($item->params, -1, 1) != '}'));
		$params = $this->parameters->getParams($item->params, JPATH_ADMINISTRATOR . '/components/com_rereplacer/item_params.xml');
		foreach ($params as $key => $val)
		{
			if (!isset($item->$key) && !is_object($val))
			{
				$item->$key = $val;
			}
		}
		unset($item->params);

		if ($isini)
		{
			foreach ($item as $key => $val)
			{
				if (is_string($val))
				{
					$item->$key = stripslashes($val);
				}
			}
		}

		if ($getform)
		{
			$xmlfile = JPATH_ADMINISTRATOR . '/components/com_rereplacer/item_params.xml';
			$params  = new JForm('jform', array('control' => 'jform'));
			$params->loadFile($xmlfile, 1, '//config');
			$params->bind($item);
			$item->form = $params;
		}

		return $item;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

		$params = json_decode($data['params'], true);
		if (is_null($params))
		{
			$params = array();
		}

		// correct the publish date details
		if (isset($params['assignto_date_publish_up']))
		{
			NNText::fixDateOffset($params['assignto_date_publish_up']);
		}

		if (isset($params['assignto_date_publish_down']))
		{
			NNText::fixDateOffset($params['assignto_date_publish_down']);
		}

		$data['params'] = json_encode($params);

		return parent::save($data);
	}

	/**
	 * Method to activate list.
	 *
	 * @param    array     An array of item ids.
	 * @param    string    The new URL to set for the rereplacer.
	 * @param    string    A comment for the rereplacer list.
	 *
	 * @return    boolean    Returns true on success, false on failure.
	 */
	public function activate(&$pks, $name, $search = null, $replace = null)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$db   = $this->getDbo();

		// Sanitize the ids.
		$pks = ( array ) $pks;
		JArrayHelper::toInteger($pks);

		// Access checks.
		if (!$user->authorise('core.admin', 'com_rereplacer'))
		{
			$pks = array();
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));

			return false;
		}

		if (!empty($pks))
		{
			// Update the item rows.
			$db->setQuery(
				'UPDATE `#__rereplacer`' .
				' SET `name` = ' . $db->quote($name) . ', `published` = 1, `search` = ' . $db->quote($search) . ', `replace` = ' . $db->quote($replace) .
				' WHERE `id` IN ( ' . implode(',', $pks) . ' )'
			);
			$db->execute();

			// Check for a database error.
			if ($error = $this->_db->getErrorMsg())
			{
				$this->setError($error);

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to validate form data.
	 */
	public function validate($form, $data, $group = null)
	{
		$newdata = array();

		// Check for valid name
		if (empty($data['name']))
		{
			$this->setError(JText::_('RR_THE_ITEM_MUST_HAVE_A_NAME'));

			return $newdata;
		}

		// Check for valid search
		if ($data['use_xml'])
		{
			if (trim($data['xml']) == '')
			{
				$this->setError(JText::_('RR_THE_ITEM_MUST_HAVE_AN_XML_FILE'));

				return $newdata;
			}
		}
		else
		{
			if (trim($data['search']) == '')
			{
				$this->setError(JText::_('RR_THE_ITEM_MUST_HAVE_SOMETHING_TO_SEARCH_FOR'));

				return $newdata;
			}
			else if (strlen(trim($data['search'])) < 3)
			{
				$this->setError(JText::sprintf('RR_THE_SEARCH_STRING_SHOULD_BE_LONGER', 2));

				return $newdata;
			}
		}

		$params = array();
		$this->_db->setQuery('SHOW COLUMNS FROM #__rereplacer');
		$dbkeys = $this->_db->loadObjectList('Field');
		$dbkeys = array_keys($dbkeys);

		foreach ($data as $key => $val)
		{
			if (in_array($key, $dbkeys))
			{
				$newdata[$key] = $val;
			}
			else
			{
				$params[$key] = $val;
			}
		}

		$newdata['params'] = json_encode($params);

		return $newdata;
	}

	/**
	 * Method to copy an item
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function copy($id)
	{
		$item = $this->getItem($id);

		unset($item->_errors);
		$item->id        = 0;
		$item->published = 0;
		$item->name      = JText::sprintf('NN_COPY_OF', $item->name);

		$item = $this->validate(null, (array) $item);

		return ($this->save($item));
	}
}
