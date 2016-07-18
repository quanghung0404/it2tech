<?php
/**
 * @version       1.51.0
 * @package       RSform!Pro 1.3.0
 * @copyright (C) 2007-2010 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class TableRSForm_GetResponse extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $form_id = null;
	public $enable_getresponse = 1;
	public $getresponse_update = 1;
	public $getresponse_list = '';
	public $vars = '';

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__rsform_getresponse', 'form_id', $db);
	}

	// Validate data before save
	public function check()
	{

		if (is_array($this->vars))
		{
			$this->vars = serialize($this->vars);
		}

		// Check if we need to add the empty record to the database
		$row = self::getInstance('RSForm_GetResponse', 'Table');
		if (!$row->load($this->form_id))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->insert($db->qn($this->getTableName()))
				->set($db->qn('form_id') . '=' . $db->q($this->form_id));
			$db->setQuery($query)->execute();
		}

		return true;
	}

}