<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class TableRSForm_VerticalResponse extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $form_id = null;
	public $enable_verticalresponse = 1;
	public $verticalresponse_update = 1;
	public $verticalresponse_list = '';
	public $vars = '';

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_verticalresponse', 'form_id', $db);
	}

	// Validate data before save
	public function check()
	{

		if (is_array($this->vars))
		{
			$this->vars = serialize($this->vars);
		}

		// Check if we need to add the empty record to the database
		$row = self::getInstance('RSForm_VerticalResponse', 'Table');
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