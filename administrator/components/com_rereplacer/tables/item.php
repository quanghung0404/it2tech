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

/**
 * Item Table
 */
class ReReplacerTableItem extends JTable
{
	/**
	 * Constructor
	 *
	 * @param    object    Database object
	 *
	 * @return    void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__rereplacer', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 */
	public function check()
	{
		$this->name   = trim($this->name);
		$this->search = trim($this->search);

		// Check for valid name
		if (empty($this->name))
		{
			$this->setError(JText::_('RR_THE_ITEM_MUST_HAVE_A_NAME'));

			return 0;
		}

		// Check for valid search
		if (strpos($this->params, '"use_xml":"1"') !== false)
		{
			if (strpos($this->params, '"xml":""') !== false)
			{
				$this->setError(JText::_('RR_THE_ITEM_MUST_HAVE_AN_XML_FILE'));

				return 0;
			}
		}
		else
		{
			if (trim($this->search) == '')
			{
				$this->setError(JText::_('RR_THE_ITEM_MUST_HAVE_SOMETHING_TO_SEARCH_FOR'));

				return 0;
			}
			else if (strlen(trim($this->search)) < 3)
			{
				$this->setError(JText::sprintf('RR_THE_SEARCH_STRING_SHOULD_BE_LONGER', 2));

				return 0;
			}
		}

		return 1;
	}
}
