<?php
/**
 * Table class: advancedtemplates
 *
 * @package         Advanced Template Manager
 * @version         1.6.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class AdvancedTemplatesTable extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__advancedtemplates', 'styleid', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_advancedtemplates.style.' . (int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		if (isset($this->_title))
		{
			return $this->_title;
		}

		return $this->_getAssetName();
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable  $table A JTable object for the asset parent
	 * @param   integer $id
	 *
	 * @return  integer
	 */
	protected function getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db      = $this->getDbo();

		$query = $db->getQuery(true)
			->select('id')
			->from('#__assets')
			->where('name = ' . $db->quote('com_advancedtemplates'));

		// Get the asset id from the database.
		$db->setQuery($query);
		if ($result = $db->loadResult())
		{
			$assetId = (int) $result;
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
}

class AdvancedTemplatesTableAdvancedTemplates extends AdvancedTemplatesTable
{
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		return parent::getAssetParentId($table, $id);
	}
}
