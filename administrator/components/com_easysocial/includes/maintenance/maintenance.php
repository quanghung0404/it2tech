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

class SocialMaintenance
{
	/**
	 * Variable to hold error set by scripts
	 * @var String
	 */
	public $error;

	public static function getInstance()
	{
		static $instance = null;

		if (empty($instance))
		{
			$instance = new self();
		}

		return $instance;
	}

	public static function factory()
	{
		return new self();
	}

	public function debug()
	{
		var_dump( $this->session_id );
		exit;
	}


	public function cleanup()
	{
		// Clean up temporary files from uploader
		$this->cleanupUploader();

		// Clean up temporary data
		$this->cleanFromTmp();

		//clearing tmp date from social_registration.
		$this->cleanFromRegistration();

		// Clean up temporary sessions for steps
		$this->cleanSteps();
	}

	/**
	 * Clean up temporary uploader files
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cleanupUploader()
	{
		$db 	= FD::db();
		$sql	= $db->sql();
		$now	= FD::date();

		$query	= 'DELETE FROM `#__social_uploader` WHERE `created` <=' . $db->Quote( $now->toSql() );

		$sql->raw( $query );

		$db->setQuery( $sql );
		$db->Query();
	}

	/**
	 * Cleanup temporary uploaded files
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function cleanFromTmp()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$now    = FD::date();
		$query = 'delete from `#__social_tmp`';
		$query .= ' where `expired` <= ' . $db->Quote( $now->toMySQL() );

		$sql->raw( $query );
		$db->setQuery( $sql );
		$db->query();
	}

	private function cleanSteps()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();
		$now    = FD::date();

		// clean the registration temp data for records that exceeded 1 hour.
		$query 	= 'DELETE FROM ' . $db->nameQuote( '#__social_step_sessions' );
		$query 	.= ' WHERE DATE_ADD(' . $db->nameQuote( 'created' ) . ', INTERVAL 60 MINUTE) <=' . $db->Quote( $now->toSql() );

		$sql->raw( $query );
		$db->setQuery( $sql );
		$db->query();
	}

	private function cleanFromRegistration()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();
		$now    = FD::date();

		// clean the registration temp data for records that exceeded 1 hour.
		$query = 'delete from `#__social_registrations`';
		$query .= ' where date_add( `created` , INTERVAL 60 MINUTE) <= ' . $db->Quote( $now->toMySQL() );

		$sql->raw( $query );
		$db->setQuery( $sql );
		$db->query();
	}

	/**
	 * Maintenance library
	 * @since	1.2
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 *
	 * Usage through Ajax
	 *
	 * To fetch list of scripts:
	 *
	 * $scripts = $maintenance->getScriptFiles();
	 *
	 * To execute the scripts:
	 *
	 * $state = $maintenance->runScript($script);
	 *
	 * If $state if false, to get the error:
	 *
	 * $error = $maintenance->getError();
	 *
	 */

	/**
	 * Get the available scripts and returns the script object in an array
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $from The version to pull from
	 * @return Array           Array of script objects
	 */
	public function getScripts($from = null)
	{
		$files = $this->getScriptFiles($from);

		$result = array();

		foreach ($files as $file)
		{
			$classname = $this->getScriptClassName($file);

			if ($classname === false)
			{
				continue;
			}

			$class = new $classname;

			$result[] = $class;
		}

		return $result;
	}

	/**
	 * Get the available script files and return the file path in an array
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $from The version to pull from
	 * @return Array           Array of script paths
	 */
	public function getScriptFiles($from = null, $operator = '>')
	{
		$files = array();

		// If from is empty, means it is a new installation, and new installation we do not want maintenance to run
		// Explicitly changed backend maintenance to pass in 'all' to get all the scripts instead.
		if (empty($from)) {
			return $files;
		}

		if ($from === 'all') {
			$files = array_merge($files, JFolder::files(SOCIAL_ADMIN_UPDATES, '.php$', true, true));
		} else {
			$folders = JFolder::folders(SOCIAL_ADMIN_UPDATES);

			if (!empty($folders)) {
				foreach ($folders as $folder) {
					// We don't want things from "manual" folder
					if ($folder === 'manual') {
						continue;
					}

					// We cannot do $folder > $from because '1.2.8' > '1.2.15' is TRUE
					// We want > $from by default, NOT >= $from, unless manually specified through $operator
					if (version_compare($folder, $from, $operator)) {
						$fullpath = SOCIAL_ADMIN_UPDATES . '/' . $folder;

						$files = array_merge($files, JFolder::files($fullpath, '.php$', false, true));
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Get the script class name
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $file The path of the script
	 * @return String          The class name of the script
	 */
	public function getScriptClassName($file)
	{
		static $classnames = array();

		if (!isset($classnames[$file]))
		{
			if (!JFile::exists($file))
			{
				$this->setError('Script file not found: ' . $file);
				$classnames[$file] = false;
				return false;
			}

			require_once($file);

			$filename = basename($file, '.php');

			$classname = 'SocialMaintenanceScript' . $filename;

			if (!class_exists($classname))
			{
				$this->setError('Class not found: ' . $classname);
				$classnames[$file] = false;
				return false;
			}

			$classnames[$file] = $classname;
		}

		return $classnames[$file];
	}

	/**
	 * Wraooer function to execute the script
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String/SocialMaintenanceScript    $file The path of the script or the script object
	 * @return Boolean          State of the script execution result
	 */
	public function runScript($file)
	{
		$class = null;

		if (is_string($file))
		{
			$classname = $this->getScriptClassName($file);

			if ($classname === false)
			{
				return false;
			}

			$class = new $classname;
		}

		if (is_object($file))
		{
			$class = $file;
		}

		if (!$class instanceof SocialMaintenanceScript)
		{
			$this->setError('Class ' . $classname . ' is not instance of SocialMaintenanceScript');
			return false;
		}

		$state = true;

		// Clear the error
		$this->error = null;

		try
		{
			$state = $class->main();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		if (!$state)
		{
			if ($class->hasError())
			{
				$this->setError($class->getError());
			}

			return false;
		}

		return true;
	}

	/**
	 * Get the script title
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $file The path of the script
	 * @return String          The title of the script
	 */
	public function getScriptTitle($file)
	{
		$classname = $this->getScriptClassName($file);

		if ($classname === false)
		{
			return false;
		}

		$vars 	= get_class_vars($classname);
		return JText::_($vars['title']);
	}

	/**
	 * Get the script description
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $file The path of the script
	 * @return String          The description of the script
	 */
	public function getScriptDescription($file)
	{
		$classname = $this->getScriptClassName($file);

		if ($classname === false)
		{
			return false;
		}

		$vars 	= get_class_vars($classname);
		return JText::_($vars['description']);
	}

	/**
	 * General set error function for the wrapper execute function
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $msg The error message
	 */
	public function setError($msg)
	{
		$this->error = $msg;
	}

	/**
	 * Checks if there are any error generated by executing the script
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @return boolean   True if there is an error
	 */
	public function hasError()
	{
		return !empty($this->error);
	}

	/**
	 * General get error function that returns error set by executing the script
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @return String    The error message
	 */
	public function getError()
	{
		return $this->error;
	}
}
