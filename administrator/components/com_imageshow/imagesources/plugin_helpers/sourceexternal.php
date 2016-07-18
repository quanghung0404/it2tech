<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Image Source Default
 * @version $Id: sourceexternal.php 12585 2012-05-11 08:17:16Z hiennv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );
include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'imagesources'.DS.'plugin_helpers'.DS.'sourcedefault.php');
class plgJSNImageshowSourceExternal extends plgJSNImageshowSourceDefault
{
	var $_tablePrefix;
	var $_installFile		= 'install.mysql.sql';
	var $_uninstallFile		= 'uninstall.mysql.sql';

	function onLoadJSNImageSource($name)
	{
		parent::onLoadJSNImageSource($name);
		$this->_db = JFactory::getDBO();
		$this->_tablePrefix = $this->_db->getPrefix().'imageshow_external_source_';
		$this->_loadTable();
		//$this->_checkTable();
	}

	function _loadSources()
	{
		$classFile 	= 'external_source_'.$this->_imageSourceName.'.php';
		$file  		= $this->_pluginPath.DS.'sources'.DS.$classFile;

		if (JFile::exists($file)) {
			require_once $file;
		}
	}

	function _loadTable() {
		JTable::addIncludePath($this->_pluginPath.DS.'tables');
	}

	function _checkTable()
	{
		$query 	= 'SHOW TABLES LIKE \''.$this->_tablePrefix.'\'';
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		if (!empty($result)) {
			return true;
		}

		$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNUtils->runSQLFile($this->_pluginPath.DS.'install'.DS.$this->_installFile);
	}

	function getLanguageJSNPlugin()
	{
		$language = array();
		$language['admin']['files'] = array('plg_'.$this->_pluginType.'_'.$this->_prefix.$this->_imageSourceName.'.ini');
		$language['admin']['path'] 	= array($this->_pluginPath.DS.'languages');
		// echo $language;
		return $language;
	}

	function onGetQueryProfile($options = array('title' => '', 'name' => ''))
	{
		$this->_db = JFactory::getDBO();

		if ($options['name'] != '' && $options['name'] != $this->_imageSourceName) {
			return;
		}

		$where = '';

		if ($options['title'] != ''){
			$where = ' WHERE LOWER(source.external_source_profile_title) LIKE '.$this->_db->quote( '%'.$options['title'].'%');
		}

		$query	= 'SELECT
						source.external_source_profile_title,
						source.external_source_id,
						\''.$this->_imageSourceName.'\' as image_source_name
				   FROM #__imageshow_external_source_'.$this->_imageSourceName.' source';

		return $query.$where;
	}
}