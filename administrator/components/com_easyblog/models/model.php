<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.model');



if (class_exists('JModelAdmin')) {
	class EasyBlogAdminMainModel extends JModelAdmin
	{
		public function getForm($data = array(), $loadData = true)
		{
		}
	}
} else {

	class EasyBlogAdminMainModel extends JModel
	{
	}
}


class EasyBlogAdminModel extends EasyBlogAdminMainModel
{
	public function __construct()
	{
		parent::__construct();

		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->config = EB::config();
		$this->my = JFactory::getUser();
	}
	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState()
	{
		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}

    protected function implodeValues($data)
    {
        $db  = EB::db();
        $str = '';

        foreach ($data as $value) {
            $str .= $db->Quote($value);

            if (next($data) !== false) {
                $str .= ',';
            }
        }

        return $str;
    }

    protected function bindTable($tableName, $result)
    {
		$binded = array();

		foreach ($result as $row) {
			$table = EB::table($tableName);
			$table->bind($row);
			$binded[] = $table;
		}

		return $binded;
    }
}
