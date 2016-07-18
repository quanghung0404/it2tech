<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * DB layer for EasySocial.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialDb
{
	static $instance	= null;

	public $db 			= null;

	public static function getInstance()
	{
		if( is_null( self::$instance ) )
		{
			self::$instance	= new self();
		}

		return self::$instance;
	}

	public function __construct()
	{
		$codeName	= FD::getInstance( 'Version' )->getCodeName();
		$fileName	= strtolower( $codeName );
		$helperFile	= dirname( __FILE__ ) . '/helpers/' . $fileName . '.php';

		require_once( $helperFile );
		$className	= 'SocialDBHelper' . ucfirst( $codeName );

		$this->db	= new $className();
	}

	public function hasCreateTempPrivilege()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$_cache = false;

			$query = "show grants";
			$this->setQuery($query);
			$result = $this->loadResult();

			if (stripos($result, 'GRANT ALL PRIVILEGES') !== false) {
				$_cache = true;
			} else if (stripos($result, 'CREATE TEMPORARY TABLES') !== false) {
				$_cache = true;
			}
		}

		return $_cache;
	}

	/**
	 * Synchronizes the database tables columns with the existing structure
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sync($from = '')
	{
		// List down files within the updates folder
		$path = SOCIAL_ADMIN . '/updates';

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$result	= array();

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
							$result	= array_merge( $result , FD::makeObject( $file ) );
						}
					}
				}
			}
		}
		else
		{
			$files	= JFolder::files( $path , '.json$' , true , true );

			// If there is nothing to process, skip this
			if( !$files )
			{
				return false;
			}

			foreach( $files as $file )
			{
				$result	= array_merge( $result , FD::makeObject( $file ) );
			}
		}

		if( !$result )
		{
			return false;
		}

		$tables = array();
		$indexes = array();
		$changes = array();

		$affected = 0;

		foreach ($result as $row) {

			$columnExist = true;
			$indexExist = true;
			$alterTable = false;

			if (isset($row->column)) {

				// Store the list of tables that needs to be queried
				if (!isset($tables[$row->table])) {
					$tables[$row->table] = $this->getTableColumns($row->table);
				}

				// Check if the column is in the fields or not
				$columnExist = in_array($row->column, $tables[$row->table]);
			}

			if (isset($row->alter)) {
				$alterTable = true;
			}

			if (isset($row->index)) {
				if (!isset($indexes[$row->table])) {
					$indexes[$row->table] = $this->getTableIndexes($row->table);
				}

				$indexExist = in_array($row->index, $indexes[$row->table]);
			}

			if ($alterTable|| !$columnExist || !$indexExist) {
				$sql = $this->sql();
				$sql->raw($row->query);

				$this->setQuery($sql);
				$this->Query();

				$affected += 1;

				if (!$columnExist) {
					$tables[$row->table][] = $row->column;
				}

				if (!$indexExist) {
					$indexes[$row->table][] = $row->index;
				}

				if ($alterTable) {
					$changes[$row->table][] = $row->alter;
				}
			}
		}

		return $affected;
	}

	/**
	 * Retrieve table columns
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTableColumns( $tableName )
	{
		$t_start = microtime(true);

		$query	= 'SHOW FIELDS FROM ' . $this->nameQuote( $tableName );

		$this->setQuery( $query );

		$rows	= $this->loadObjectList();
		$fields	= array();

		foreach( $rows as $row )
		{
			$fields[]	= $row->Field;
		}

		return $fields;
	}

	public function getTableIndexes( $tableName )
	{
		$query = 'SHOW INDEX FROM ' . $this->nameQuote( $tableName );

		$this->setQuery( $query );

		$result = $this->loadObjectList();

		$indexes = array();

		foreach( $result as $row )
		{
			$indexes[] = $row->Key_name;
		}

		return $indexes;
	}

	/**
	 * Helper to load our own sql string helper.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function sql()
	{
		$sql 	= FD::sql();

		return $sql;
	}

	/**
	 * Override JDatabase setQuery behavior.
	 */
	public function setQuery( $query , $offset = 0 , $limit = 0 )
	{
		if( is_array( $query ) )
		{
			$query 	= implode( ' ' , $query );
		}

		return call_user_func_array( array( $this->db , __FUNCTION__ ) , array( $query , $offset , $limit ) );
	}

	public function __call( $method , $args )
	{
		return call_user_func_array( array( $this->db , $method ) , $args );
	}
}
