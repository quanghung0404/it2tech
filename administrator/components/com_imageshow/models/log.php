<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: log.php 8411 2011-09-22 04:45:10Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );

class ImageShowModelLog extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
	}

	function store($data)
	{
		$row = $this->getTable();

		if (!$row->bind($data))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}

?>