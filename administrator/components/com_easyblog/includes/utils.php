<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.filesystem.file' );
jimport('joomla.filesystem.folder' );
jimport('joomla.html.parameter' );
jimport('joomla.application.component.model');
jimport('joomla.access.access');

if (!function_exists('dump')) {

	function dump()
	{
		$args 	= func_get_args();

		echo '<pre>';
		foreach( $args as $arg )
		{
			var_dump( $arg );
		}
		echo '</pre>';

		exit;
	}

}

class EasyBlog
{
	public $config = null;
	public $doc = null;
	public $app = null;
	public $input = null;
	public $my = null;
	public $string = null;
	public $lang = null;

	public function __construct()
	{
		// EasyBlog's configuration
		$this->config = EB::config();
		$this->jconfig = EB::jconfig();

		// Joomla's document object
		$this->doc = JFactory::getDocument();

		// Joomla's application object
		$this->app = JFactory::getApplication();

		// Request input object
		$this->input = EB::request();

		// Current logged in user.
		$this->my = JFactory::getUser();

		// String library
		$this->string = EB::string();

		$this->lang = JFactory::getLanguage();
	}

	/**
	 * Helper method to load language
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadLanguage($admin = false)
	{
		if ($admin) {
			return $this->lang->load('com_easyblog', JPATH_ADMINISTRATOR);
		}

		return $this->lang->load('com_easyblog', JPATH_ROOT);
	}
}

class EasyBlogDbJoomla
{
	public $db = null;

	public function __construct()
	{
		$this->db = JFactory::getDBO();
	}

    /**
     * Override parent's setquery method
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function setQuery($query)
    {
        if (is_array($query)) {
            $query = implode(' ', $query);
        }

        return $this->db->setQuery($query);
    }

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function getEscaped( $str , $extra = false )
	{
		return $this->db->escape( $str , $extra );
	}

	public function nameQuote( $str )
	{
		return $this->db->quoteName( $str );
	}

    /**
     * Override the quote to check if array is passed in, then quote all the items accordingly.
     * This is actually already supported from J3.3 but for older versions, we need this compatibility layer
     */
    public function quote($item, $escape = true)
    {
        if (!is_array($item)) {
            return $this->db->quote($item, $escape);
        }

        $result = array();

        foreach ($item as $i) {
            $result[] = $this->db->quote($i, $escape);
        }

        return $result;
    }

    /**
     * Override the quoteName to check if array is passed in, then quoteName all the items accordingly.
     * This is actually already supported from J3.3 but for older versions, we need this compatibility layer
     */
    public function quoteName($name, $as = null)
    {
        if (!is_array($name)) {
            return $this->db->quoteName($name, $as);
        }

        $result = array();

        foreach ($name as $i) {
            $result[] = $this->db->quoteName($i, $as);
        }

        return $result;
    }

    /**
     * Retrieve table columns
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTableColumns($tableName)
    {
        $db = JFactory::getDBO();

        $query  = 'SHOW FIELDS FROM ' . $db->quoteName($tableName);

        $db->setQuery($query);

        $rows = $db->loadObjectList();
        $fields = array();

        foreach ($rows as $row) {
            $fields[] = $row->Field;
        }

        return $fields;
    }

    /**
     * Retrieves table indexes from a specific table.
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public static function getTableIndexes($tableName)
    {
        $db = JFactory::getDBO();

        $query = 'SHOW INDEX FROM ' . $db->quoteName($tableName);

        $db->setQuery($query);

        $result = $db->loadObjectList();

        $indexes = array();

        foreach ($result as $row) {
            $indexes[] = $row->Key_name;
        }

        return $indexes;
    }


	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}

	/**
     * Alias for quote.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @access public
     */
    public function q($item, $escape = true)
    {
        return $this->quote($item, $escape);
    }

    /**
     * Alias for quotename.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @access public
     */
    public function qn($name, $as = null)
    {
        return $this->quoteName($name, $as);
    }

    /**
     * Synchronizes database versions
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public static function sync($from = '')
    {
        $db = EB::db();

        // List down files within the updates folder
        $path = EBLOG_ADMIN_ROOT . '/updates';

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $scripts= array();

        if ($from) {
            $folders = JFolder::folders($path);

            if ($folders) {

                foreach ($folders as $folder) {

                    // Because versions always increments, we don't need to worry about smaller than (<) versions.
                    // As long as the folder is greater than the installed version, we run updates on the folder.
                    // We cannot do $folder > $from because '1.2.8' > '1.2.15' is TRUE
                    // We want > $from, NOT >= $from

                    if (version_compare($folder, $from) === 1) {
                        $fullPath = $path . '/' . $folder;

                        // Get a list of sql files to execute
                        $files = JFolder::files( $fullPath , '.json$' , false , true );

                        foreach ($files as $file) {
                            $data = json_decode(JFile::read($file));
                            $scripts    = array_merge($scripts, $data);
                        }
                    }
                }
            }
        } else {

            $files = JFolder::files($path, '.json$', true, true);

            // If there is nothing to process, skip this
            if (!$files) {
                return false;
            }

            foreach ($files as $file) {
                $data = json_decode(JFile::read($file));
                $scripts = array_merge($scripts, $data);
            }
        }

        if (!$scripts) {
            return false;
        }

        $tables = array();
        $indexes = array();
        $affected = 0;


        foreach ($scripts as $script) {

            $columnExist = true;
            $indexExist = true;

            if (isset($script->column)) {

                // Store the list of tables that needs to be queried
                if (!isset($tables[$script->table])) {
                    $tables[$script->table] = $db->getTableColumns($script->table);
                }

                // Check if the column is in the fields or not
                $columnExist = in_array($script->column, $tables[$script->table]);
            }

            if (isset($script->index)) {

                // Get the list of indexes on a table
                if (!isset($indexes[$script->table])) {
                    $indexes[$script->table] = $db->getTableIndexes($script->table);
                }

                $indexExist = in_array($script->index, $indexes[$script->table]);
            }

            if (!$columnExist || !$indexExist) {
                $db->setQuery($script->query);
                $db->Query();

                $affected   += 1;
            }
        }

        return $affected;
    }
}
