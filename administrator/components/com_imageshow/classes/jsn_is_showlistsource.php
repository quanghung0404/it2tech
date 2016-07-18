<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_showlistsource.php 16077 2012-09-17 02:30:25Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );

class JSNISShowlistSource
{
	var $_db 			= null;
	var $_pluginType 	= 'jsnimageshow';
	var $_pluginPrefix 	= 'source';
	var $_installFolder	= 'install';
	var $_installFile	= 'install.mysql.sql';

	function JSNISShowlistSource()
	{
		if ($this->_db == null)
		{
			$this->_db = JFactory::getDBO();
		}
	}

	public static function getInstance()
	{
		static $instanceShowlistSource;
		if ($instanceShowlistSource == null)
		{
			$instanceShowlistSource = new JSNISShowlistSource();
		}
		return $instanceShowlistSource;
	}

	function getSourceInfo($name = null)
	{
		if ($name)
		{
			$query 	= 'SELECT *
			   FROM #__extensions
			   WHERE folder = \''.$this->_pluginType.'\'
			   AND element='.$this->_db->quote($name);

			$this->_db->setQuery($query);
			$result = $this->_db->loadObject();
			if (count($result))
			{
				if ($result->manifest_cache)
				{
					return json_decode($result->manifest_cache);
				}
			}
		}
		return false;
	}
}