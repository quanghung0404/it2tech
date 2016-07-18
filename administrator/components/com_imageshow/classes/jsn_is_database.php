<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_database.php 9062 2011-10-21 07:55:26Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_upgradedbutil.php');
class JSNISDatabase
{
	function __construct()
	{
		$this->_db = JFactory::getDBO();
	}

	function JSNISDatabase()
	{
		$this->__construct();
	}

	function executeQueries($queies)
	{
		if (count($queies))
		{
			foreach ($queies as $value)
			{
				$this->_db->setQuery($value);
				if (!$this->_db->query())
				{
					return false;
				}
			}
		}
		return true;
	}

	function checkTableColumExist($table, $column)
	{
		$objUpgradeDBAction = new JSNJSUpgradeDBAction();
		return $objUpgradeDBAction->isExistTableColumn($table, $column);
	}

	function checkTableExist($tableName)
	{
		$objUpgradeDBAction = new JSNJSUpgradeDBAction();
		return $objUpgradeDBAction->isExistTable($tableName);
	}
}
?>