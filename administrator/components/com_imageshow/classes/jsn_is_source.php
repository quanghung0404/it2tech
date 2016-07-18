<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_source.php 15763 2012-09-01 06:41:07Z hiennh $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
class JSNISSource
{
	var $_db = null;
	var $_listInternalSourceInstalled = array();
	var $_listInternalSource = array();
	var $_listExternalSource = array();
	var $_listSource = array();
	var $_listActiveSource = array();

	function JSNISSource()
	{
		if ($this->_db == null) {
			$this->_db = JFactory::getDBO();
		}

		$this->_setListSource();
	}

	public static function getInstance()
	{
		static $instanceSource;
		if ($instanceSource == null) {
			$instanceSource = new JSNISSource();
		}
		return $instanceSource;
	}

	/**
	 *
	 * Get source by type
	 * @param $type = '' get all source
	 * @param $type = 'internal' get all defined internal sources
	 * @param $type = 'internalSourceInstalled' get all internal sources that installed
	 * @param $type = 'external' get all external sources
	 * @param $type = 'active' get all active sources
	 */
	function getListSources($type = '')
	{
		if ($type == '') {
			return $this->_listSource;
		} else if ($type == 'internal') {
			return $this->_listInternalSource;
		} else if ($type == 'external') {
			return $this->_listExternalSource;
		} else if ($type == 'internalSourceInstalled') {
			return $this->_listInternalSourceInstalled;
		} else if ($type == 'active') {
			return $this->_listActiveSource;
		}
	}

	function _setListSource()
	{
		$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
		$query = 'UPDATE #__extensions SET enabled = 1 WHERE element LIKE \'source%\' AND folder = \'jsnimageshow\' AND enabled = 0';
		$this->_db->setQuery($query);
		$this->_db->query();

		$query = 'SELECT * FROM #__extensions WHERE element LIKE \'source%\' AND folder = \'jsnimageshow\' AND enabled = 1';
		$this->_db->setQuery($query);
		$pluginSource = $this->_db->loadObjectList();

		// add plugin source into lists
		if ($pluginSource)
		{
			foreach ($pluginSource as $plugin)
			{
				$define = JPATH_PLUGINS.DS.'jsnimageshow'.DS.$plugin->element.DS.'define.php';

				if (JFile::exists($define))
				{
					require_once $define;

					$defineObj = json_decode(constant('JSN_IS_'.strtoupper($plugin->element)));

					$objSource 					= new stdClass();
					$objSource->title 			= $defineObj->name;
					$objSource->type  			= $defineObj->type;
					$objSource->pluginInfo 		= $plugin;
					$objSource->define 			= $defineObj;
					$objSource->needInstall 	= false;
					$objSource->needUpdate 		= false;
					$objSource->name 			= $defineObj->name;
					$objSource->identified_name = $defineObj->identified_name;
					$objSource->thumbnail 		= $defineObj->thumb;
					$objSource->sync			= (isset($defineObj->sync))?$defineObj->sync:true;
					$objSource->pagination		= (isset($defineObj->pagination))?$defineObj->pagination:false;

					// add to list source
					//$listSourceType = '_list'.ucfirst($objSource->type).'Source';

					if ($objSource->type == 'internal'){
						$this->_listInternalSource[] = $objSource;
					}else{
						$this->_listExternalSource[] = $objSource;
						$this->_listActiveSource[] 	 = $objSource;
					}

					// add to list internal source installed
					if ($defineObj->type == 'internal')
					{
						if (isset($defineObj->component))
						{
							$comInstall = $objJSNUtils->checkComInstalled($defineObj->component);

							if ($comInstall) {
								$objSource->componentInstall = true;
								$this->_listInternalSourceInstalled[] 	= $objSource;
								$this->_listActiveSource[] 				= $objSource;
							} else {
								$objSource->componentInstall = false;
							}
						}
					}

					$this->_listSource[] = $objSource;
				}
			}
		}

		// add local folder into lists
		$folderSource = $this->getFolderSource(true);

		$this->_listActiveSource[] 	= $folderSource;
		$this->_listSource[] 		= $folderSource;
	}

	function getFolderSource($fakeServer = false)
	{
		// add local folder into lists
		$objSource = new stdClass();
		$objSource->title = JText::_('Local image folder');
		$objSource->identified_name = 'folder';
		$objSource->type  = 'folder';

		$define = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_imageshow'.DS.'imagesources'.DS.'source_folder'.DS.'define.php';

		if (JFile::exists($define))
		{
			require_once $define;
			$objSource->define = json_decode(constant('JSN_IS_SOURCEFOLDER'));
		}

		if ($fakeServer)
		{
			$objSource->identified_name = 'folder';
			$objSource->name = JText::_('Local image folder');
			$objSource->description = JText::_('Local image folder description');
			$objSource->version = '1.0.0';
			$objSource->thumbnail = $objSource->define->thumb;
			$objSource->needUpdate = false;
			$objSource->needInstall = false;
			$objSource->type		= 'folder';
			$objSource->sync		= (isset($objSource->define->sync))?$objSource->define->sync:true;
			$objSource->pagination	= (isset($objSource->define->pagination))?$objSource->define->pagination:false;
		}

		return $objSource;
	}

	/**
	 * check internal sources , need to update database if source component uninstall
	 * @param unknown_type $config
	 */
	function checkInternalSourceInstalled()
	{
		$listsource  = $this->_listInternalSource;
		$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');

		foreach ($listsource as $source)
		{
			if ( isset($source->define->component) && $source->define->component != '')
			{
				$comInstall = $objJSNUtils->checkComInstalled($source->define->component);

				if (!$comInstall)
				{
					$config = array('image_source_name' => $source->identified_name, 'image_source_type' => $source->type);
					$this->updateShowlistBySource($config);
				}
			}
		}
	}

	/**
	 * reset showlist source and remove all images of showlist
	 * @param unknown_type $config
	 */
	function updateShowlistBySource($config = array('image_source_name' => '', 'image_source_type' => ''))
	{
		$showlists = $this->_getShowlistBySource($config);

		if (count($showlists))
		{

			foreach ($showlists as $value) {
				$showlistIDs[] = $value[0];
			}

			$showlistIDString = implode(',', $showlistIDs);

			$queryUpdateShowlist = 'UPDATE #__imageshow_showlist
								    SET
										image_source_name = \'\',
									   	image_source_type = \'\',
									   	image_source_profile_id = 0
								    WHERE showlist_id IN ( '.$showlistIDString.' )';

			$this->_db->setQuery($queryUpdateShowlist);
			$this->_db->query();

			$queryDeleteImages = 'DELETE imgs
								  FROM #__imageshow_showlist sl
								  INNER JOIN #__imageshow_images imgs ON sl.showlist_id = imgs.showlist_id
								  WHERE sl.showlist_id IN ('.$showlistIDString.')';

			$this->_db->setQuery($queryDeleteImages);
			$this->_db->query();

		}
	}

	function _getShowlistBySource($config = array('image_source_name' => '', 'image_source_type' => ''))
	{
		$query = 'SELECT showlist_id
				  FROM #__imageshow_showlist
				  WHERE
				  	image_source_name = '.$this->_db->quote((string) $config['image_source_name']) .'
				  AND
				  	image_source_type = '.$this->_db->quote((string)$config['image_source_type']);
		$this->_db->setQuery($query);
		return $this->_db->loadRowList();
	}

	function callSourcePlugin($config = array('pluginName' => ''))
	{
		if ($config['pluginName'] != '')
		{
			JPluginHelper::importPlugin('jsnimageshow', $config['pluginName']);
			$dispatcher = JDispatcher::getInstance();
			$arg 		= array($config['pluginName']);
			$dispatcher->trigger('onLoadJSNImageSource', $arg);
			return true;
		}

		return false;
	}

	function uninstallImageSource($sourceInfo)
	{
		if (!isset($sourceInfo->pluginInfo)) {
			return false;
		}

		$pluginTable = JTable::getInstance('extension', 'JTable');

		if ($pluginTable->load((int) $sourceInfo->pluginInfo->extension_id))
		{
			$tableName = str_replace('source', '', $pluginTable->element);

			$query = 'SELECT *
						FROM #__imageshow_showlist
						WHERE image_source_name = '.$this->_db->quote($sourceInfo->identified_name) . '
						AND image_source_type = '.$this->_db->quote($sourceInfo->type);
			$this->_db->setQuery($query);
			$showlists = $this->_db->loadObjectList();

			foreach ($showlists as $showlist)
			{
				$imageSource = JSNISFactory::getSource($showlist->image_source_name, $showlist->image_source_type, $showlist->showlist_id);
				// remove all images
				$imageSource->removeAllImages(array('showlist_id' => $showlist->showlist_id));
				// remove profile if have profile
				if (isset($imageSource->_source['profileTable'])) {
					$imageSource->_source['profileTable']->delete();
				}
				// update showlist
				$imageSource->_showlistTable->image_source_type = '';
				$imageSource->_showlistTable->image_source_name = '';
				$imageSource->_showlistTable->image_source_profile_id = 0;
				$imageSource->_showlistTable->store();
			}
		}

		return true;
	}

	function getSourceInfoByPluginID($pluginID = 0)
	{
		foreach ($this->_listSource as $source)
		{
			if (isset($source->pluginInfo) && $source->pluginInfo->extension_id == $pluginID) {
				return $source;
			}
		}

		return false;
	}

	function loadSource($sourceName)
	{
		JPluginHelper::importPlugin('jsnimageshow', $sourceName);
	}

	function triggerSourceEvent($eventName, $arg = array())
	{
		$dispatcher 	= JDispatcher::getInstance();
		$plugins 		= $dispatcher->trigger($eventName, $arg);
		return $plugins;
	}

	function getSourcesFromServer()
	{
		$objJSNUtils    = JSNISFactory::getObj('classes.jsn_is_utils');
		$result 		= $objJSNUtils->getVersionInfoFromServer();
		$return     	= '[]';
		if ($result && $result != null)
		{
			$result  = $objJSNUtils->paserVersionInfoFromServer($result);
			$return  = $objJSNUtils->getItemsFromVersionInfoFromServer($result, JSN_IMAGESHOW_CATEGORY_IMAGESOURCES);
			$return  = @$return->items;
		}
		return $this->_listSourcesOnServer = $return;
	}

	/**
	 * compare remote source with local source
	 * @return list remote source
	 **/
	function compareSources($ignoreFolderSource = false)
	{
		$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
		$baseURL 		= $objJSNUtils->overrideURL();
		$remoteSources 	= $this->getSourcesFromServer();
		$joomlaObject	= new JVersion();
		$sources 		= array();

		if ($ignoreFolderSource == false) {
			$sources[] = $this->getFolderSource(true);
		}

		if (count($remoteSources) && is_array($remoteSources))
		{
			foreach (@$remoteSources as $remoteSource)
			{
				$tags = explode(';', trim(@$remoteSource->tags));

				if (in_array($joomlaObject->RELEASE, $tags))
				{
					$needInstall = true;
					$remoteSource->needUpdate = false;

					foreach ($this->_listSource as $localSource)
					{
						if ($localSource->type != 'folder')
						{
							if ($localSource->identified_name == $remoteSource->identified_name)
							{
								$needInstall = false;
								// compare version
								$localPluginInfo = json_decode($localSource->pluginInfo->manifest_cache);

								if (version_compare($localPluginInfo->version, $remoteSource->version) >= 0)
								{
									$remoteSource->needUpdate = false;
								}
								else
								{
									$remoteSource->needUpdate = true;
								}
								$remoteSource->oldVersion	= $localPluginInfo->version;
								$remoteSource->newVersion	= $remoteSource->version;
								$remoteSource->type   	 	= $localSource->type;
								$remoteSource->localInfo 	= $localSource;
								$remoteSource->thumbnail 	= dirname($baseURL).'/'.$localSource->define->thumb;
							}
						}
					}

					$remoteSource->needInstall = $needInstall;

					$sources[] = $remoteSource;
				}
			}
		}

		return $sources;
	}

	/**
	 * Compare local source with remote source
	 * @param $ingnoreFolderSource true/false add folder source to result return
	 * @return list local source
	 */
	function compareLocalSources()
	{
		$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
		$baseURL 		= $objJSNUtils->overrideURL();
		$remoteSources 	= $this->getSourcesFromServer();
		$joomlaObject	= new JVersion();
		$sources 		= array();
		$sources[] 		= $this->getFolderSource(true);

		foreach (@$this->_listSource as $localSource)
		{
			if ($localSource->type != 'folder')
			{
				$localSource->authentication = false;
				$localSource->needInstall = false;
				$localSource->related_products = array();
				$localSource->needUpdate = false;
				if(count($remoteSources) && is_array($remoteSources))
				{
					foreach (@$remoteSources as $remoteSource)
					{
						$tags = explode(';', trim(@$remoteSource->tags));
						if (in_array($joomlaObject->RELEASE, $tags))
						{
							if ($localSource->identified_name == $remoteSource->identified_name)
							{
								// compare version
								$localPluginInfo = json_decode($localSource->pluginInfo->manifest_cache);

								if (version_compare($localPluginInfo->version, $remoteSource->version) >= 0) {
									$localSource->needUpdate = false;
								} else {
									$localSource->needUpdate = true;
								}
								$localSource->authentication = (boolean) $remoteSource->authentication;
								$localSource->related_products = @$remoteSource->related_products;
							}
						}
					}
				}
				$localSource->thumbnail = dirname($baseURL).'/'.$localSource->thumbnail;
				$localSource->localInfo = $localSource;
				$sources[] = $localSource;
			}
		}
		return $sources;
	}

	function getListSouceDefineToInstall()
	{
		$pluginDefine = json_decode(PluginInstalledList);
		return (is_array($pluginDefine->imageSource)) ? $pluginDefine->imageSource : array();
	}

	function checkLocalSourceExist($souceName)
	{
		foreach ($this->_listSource as $source)
		{
			if ($source->identified_name == strtolower($souceName)) {
				return true;
			}
		}
		return false;
	}

	function getNeedInstallList($sources)
	{
		$results	= array();
		if (count($sources))
		{
			for ($i = 0, $counti = count($sources); $i < $counti; $i++)
			{
				$row = $sources[$i];
				if ($row->needInstall)
				{
					$results [] = $row;
				}
			}
		}
		return $results;
	}

	function getNeedUpdateList($sources)
	{
		$results	= array();
		if (count($sources))
		{
			for ($i = 0, $counti = count($sources); $i < $counti; $i++)
			{
				$row = $sources[$i];
				if (!$row->needInstall)
				{
					$results [] = $row;
				}
			}
		}

		return $results;
	}

	/**
	 * get list needful imagesource when install imageshow
	 * @return array
	 */
	function getListSourcesForInstall()
	{
		$session 			= JFactory::getSession();
		$preVersion 		= (float) str_replace('.', '', $session->get('preversion', null, 'jsnimageshow'));
		$version400 		= (float) str_replace('.', '', '4.0.0');
		$defineSources 		= $this->getListSouceDefineToInstall();
		$task 				= '';
		$remoteSources		= $this->compareSources(true);
		$tmpRemoteSources	= array();

		foreach ($remoteSources as $remoteSource) {
			$tmpRemoteSources[$remoteSource->identified_name] = $remoteSource;
		}

		$listImageSources 	= $defineSources;

		if ($preVersion && $preVersion < $version400)
		{
			$oldCoreSources = $session->get('JSNISImageSourceRequired3xxVersion', array());
			$oldCoreSources = array_diff($oldCoreSources, $defineSources);
			$listImageSources = array_merge($defineSources, $oldCoreSources);
		}

		$list 		= array();
		$objVersion = new JVersion();
		$listRequired = $session->get('jsn-list-required-install', array(), 'jsn-install-manual');

		foreach ($listImageSources as $source)
		{
			$sourceExists = $this->checkLocalSourceExist($source);

			if (@$tmpRemoteSources[$source]->needUpdate){
				$task = 'new';
			}

			if (!count($tmpRemoteSources) && !isset($listRequired[$source])) {
				$task = 'new';
			}

			if (!count($tmpRemoteSources) && isset($listRequired[$source]) && $listRequired[$source] == false) {
				$task = 'new';
			}

			$info					= new stdClass();
			$info->identify_name 	= $source;
			$info->full_name 		= (count($tmpRemoteSources) > 0 && isset($tmpRemoteSources[$source])) ? $tmpRemoteSources[$source]->name : $source;
			$info->edition 			= '';
			$info->joomla_version 	= $objVersion->RELEASE;
			$info->task 			= ($sourceExists == false) ? 'new' : $task;
			$info->commercial 		= false;
			$info->default_install  = (in_array($source, $defineSources)) ? true : false;
			$list[] 				= $info;
		}

		return $list;
	}
}