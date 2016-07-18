<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Image Source Default
 * @version $Id: sourceinternal.php 8543 2011-09-28 04:25:45Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );
include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_imageshow'.DS.'imagesources'.DS.'plugin_helpers'.DS.'sourcedefault.php');
class plgJSNImageshowSourceInternal extends plgJSNImageshowSourceDefault
{
	function __construct(&$subject, $config = array())
	{
		$this->_setPluginPath();
		parent::__construct($subject, $config);
	}

	function onLoadJSNImageSource($name)
	{
		parent::onLoadJSNImageSource($name);
	}

	function _loadSources()
	{
		$classFile 	= 'internal_source_'.$this->_imageSourceName.'.php';
		$file  		= $this->_pluginPath.DS.'sources'.DS.$classFile;

		if (JFile::exists($file)) {
			require_once $file;
		}
	}
}