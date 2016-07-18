<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Image Source Default
 * @version $Id: sourcedefault.php 12585 2012-05-11 08:17:16Z hiennv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );
include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_factory.php');
class plgJSNImageshowSourceDefault extends JPlugin
{
	var $_pluginType 		= 'jsnimageshow';
	var $_prefix 			= 'source';
	var $_imageSourceName 	= 'default';
	var $_pluginPath;

	function __construct(&$subject, $config = array())
	{
		$this->_setPluginPath();
		parent::__construct($subject, $config);
	}

	function onLoadJSNImageSource($name)
	{
		JPlugin::loadLanguage('plg_'.$this->_pluginType.'_'.$this->_prefix.$this->_imageSourceName);
		$this->_loadSources();
	}

	function _setPluginPath() {
		$this->_pluginPath = dirname(__FILE__);
	}

	function _loadSources()
	{
		return true;
	}

	function onGetJSNSourceProfiles()
	{
		$query = "SELECT *, COUNT(sl.showlist_id) AS totalshowlist FROM #__imageshow_external_source".$this->_imageSourceName." s
				 INNER JOIN #__imageshow_source_profile p ON p.external_source_id = s.external_source_id
				 LEFT JOIN #__imageshow_showlist sl ON sl.image_source_profile_id = p.external_source_profile_id
		";

		return $query;
	}
}