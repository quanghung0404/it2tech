<?php
/**
 * @version    $Id: jsn_is_utils.php 16563 2012-10-01 07:56:09Z giangnd $
 * @package    JSN ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class JSNISUtils
{
	var $_db = null;

	function JSNISUtils()
	{
		if ($this->_db == null)
		{
			$this->_db = JFactory::getDBO();
		}
	}

	public static function getInstance()
	{
		static $instanceUtils;
		if ($instanceUtils == null)
		{
			$instanceUtils = new JSNISUtils();
		}
		return $instanceUtils;
	}

	function getParametersConfig()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('name') . ', ' . $db->quoteName('value'));
		$query->from($db->quoteName('#__jsn_imageshow_config'));
		$query->where($db->quoteName('name') . ' IN (' . $db->quote('show_quick_icons') . ', ' . $db->quote('enable_update_checking') . ', ' . $db->quote('number_of_images_on_loading') . ')');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$params = new stdClass;
		if (count($result))
		{
			foreach ($result as $item)
			{
				switch($item->name)
				{
					case 'show_quick_icons':
						$params->show_quick_icons = $item->value;
						break;
					case 'enable_update_checking':
						$params->enable_update_checking = $item->value;
						break;
					case 'number_of_images_on_loading':
						$params->number_of_images_on_loading = $item->value;
						break;
				}
			}
		}
		else
		{
			$params->show_quick_icons = '1';
			$params->enable_update_checking = '1';
			$params->number_of_images_on_loading = '30';
		}
		return $params;
	}

	function overrideURL()
	{
		$pathURL 			= array();
		$uri				= JURI::getInstance();
		$pathURL['prefix'] 	= $uri->toString( array('scheme', 'host', 'port'));

		if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
		{
			$pathURL['path'] =  rtrim(dirname(str_replace(array('"', '<', '>', "'"), '', $_SERVER["PHP_SELF"])), '/\\');
		}
		else
		{
			$pathURL['path'] =  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
		}

		return $pathURL['prefix'].$pathURL['path'].'/';
	}

	function checkSupportLang()
	{
		$objLanguage 			= JFactory::getLanguage();
		$language           	= $objLanguage->getTag();
		$supportLang 			= json_decode(JSN_IMAGESHOW_LIST_LANGUAGE_SUPPORTED);

		foreach ($supportLang as $lang)
		{
			if ($lang->code == $language) return true;
		}

		return false;
	}

	function getAlterContent()
	{
		$script = "\n<script type='text/javascript'>\n";
		$script .= "window.addEvent('domready', function(){
						JSNISImageShow.alternativeContent();
					});";
		$script .= "\n</script>\n";
		return $script;
	}




	/*
	 *  encode url with special character
	 *
	 */
	function encodeUrl($url, $replaceSpace = false)
	{
		$encodeStatus = $this->encodeStatus($url);

		if ($encodeStatus == false)
		{
			$url = rawurlencode($url);
		}

		$url = str_replace('%3B', ";", $url);
		$url = str_replace('%2F', "/", $url);
		$url = str_replace('%3F', "?", $url);
		$url = str_replace('%3A', ":", $url);
		$url = str_replace('%40', "@", $url);
		$url = str_replace('%26', "&", $url);
		$url = str_replace('%3D', "=", $url);
		$url = str_replace('%2B', '+', $url);
		$url = str_replace('%24', "$", $url);
		$url = str_replace('%2C', ",", $url);
		$url = str_replace('%23', "#", $url);
		$url = str_replace('%2D', "-", $url);
		$url = str_replace('%5F', "_", $url);
		$url = str_replace('%2E', ".", $url);
		$url = str_replace('%21', "!", $url);
		$url = str_replace('%7E', "~", $url);
		$url = str_replace('%2A', "*", $url);
		$url = str_replace('%27', "'", $url);
		$url = str_replace('%22', "\"", $url);
		$url = str_replace('%28', "(", $url);
		$url = str_replace('%29', ")", $url);
		$url = str_replace('%5D', "]", $url);
		$url = str_replace('%5B', "[", $url);

		if ($replaceSpace == true)
		{
			$url = str_replace('%20', " ", $url);
		}
		return $url;
	}

	/*
	 * encode array url
	 *
	 */
	function encodeArrayUrl($urls, $replaceSpace = false)
	{
		$arrayUrl =  array();
		foreach ($urls as $key => $value )
		{
			$url = $this->encodeUrl($value, $replaceSpace);
			$arrayUrl[$key] = $url;
		}

		return $arrayUrl;
	}

	//decode url that was encoded by encodeUrl()
	function decodeUrl($url)
	{
		$url = rawurldecode($url);
		return $url;
	}

	// check string was encoded
	function encodeStatus($string)
	{
		$regexp  = "/%+[A-F0-9]{2}/";
		if (preg_match($regexp,$string))
		{
			return true;
		}
		return false;
	}

	function getIDComponent()
	{
		$query = $this->_db->getQuery(true);
		$query->select('id');
		$query->from('#__extensions');
		$query->where('element=\'com_imageshow\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadAssoc();
		return $result;

	}

	function insertMenuSample($menuType)
	{
		$comID 	= $this->getIDComponent();
		$query 	= "INSERT INTO
						#__menu
						(menutype, name, alias, link, type, published, parent, componentid, sublevel, ordering, checked_out, checked_out_time, pollid, browserNav, access, utaccess, params, lft, rgt, home)
				   VALUES
				  		('".$menuType."', 'JSN ImageShow', 'imageshow', 'index.php?option=com_imageshow&view=show', 'component', '1', '0', '".$comID['id']."', '0', '0', '0', '0000-00-00 00:00:00', '0', '0', '0', '0', 'showlist_id=1\nshowcase_id=1', '0', '0', '0')";
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	function checkComInstalled($comName)
	{
		$query = $this->_db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__extensions');
		$query->where('element=\''.$comName.'\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadRow();
		if ($result[0] > 0) {
			return true;
		}
		return false;

	}

	function checkIntallModule()
	{
		$query = $this->_db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__extensions');
		$query->where('element=\'mod_imageshow\' AND type=\'module\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadRow();
		if ($result[0] > 0) {
			return true;
		}
		return false;
	}

	function checkIntallPluginContent()
	{
		$query = $this->_db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__extensions');
		$query->where('element=\'imageshow\' AND type=\'plugin\' AND folder=\'content\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadRow();
		if ($result[0] > 0) {
			return true;
		}
		return false;
	}

	function checkIntallPluginSystem()
	{
		$query = $this->_db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__extensions');
		$query->where('element=\'imageshow\' AND type=\'plugin\' AND folder=\'system\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadRow();
		if ($result[0] > 0) {
			return true;
		}
		return false;

	}

	function getPluginContentInfo()
	{
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('element=\'imageshow\' AND type=\'plugin\' AND folder=\'content\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		return $result;

	}

	function getModuleInfo()
	{
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('element=\'mod_imageshow\' AND type=\'module\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		return $result;
	}

	function getComponentInfo()
	{
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('element=\'com_imageshow\' AND type=\'component\'');
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		return $result;

	}

	function clearData()
	{
		$queries [] = 'TRUNCATE TABLE #__imageshow_configuration';
		$queries [] = 'TRUNCATE TABLE #__imageshow_showlist';
		$queries [] = 'TRUNCATE TABLE #__imageshow_showcase';
		$queries [] = 'TRUNCATE TABLE #__imageshow_images';

		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '') {
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		return true;
	}



	//	function getTotalProfile()
	//	{
	//		$query 	= 'SELECT COUNT(*) FROM #__imageshow_configuration WHERE source_type <> 1';
	//		$this->_db->setQuery($query);
	//		return $this->_db->loadRow();
	//	}

	function getImageInPath($path = null)
	{
		jimport( 'joomla.filesystem.file' );

		if ($path == null ) return false;

		$arrayImage = array();

		if (!JFolder::exists($path))
		{
			return false;
		}

		$dir = @opendir($path);


		$data 				= new stdClass();
		$arrayImage 		= array();

		while (false !== ($file = @readdir($dir)))
		{
			if (JFile::exists($path.DS.$file))
			{
				$fileInfo = pathinfo($path.DS.$file);

				if (preg_match('(png|jpg|jpeg|gif)',strtolower($fileInfo['extension']))) {
					$arrayImage[] = str_replace(DS, '/', $path.DS.$file);
				}
			}
		}

		$data->images		    = $arrayImage;
		natcasesort($arrayImage);
		$data->images		    = $arrayImage;
		return $data;
	}

	function checkValueArray($arrayList, $index)
	{
		if (!array_key_exists($index, $arrayList)) {
			return false;
		}

		if ($arrayList[$index] != '') {
			return $arrayList[$index];
		} else {
			$index = $index - 1;
			return $this->checkValueArray($arrayList,$index);
		}
	}

	function wordLimiter($str, $limit = 100, $end_char = '&#8230;')
	{
		if (trim($str) == '')
		{
			return $str;
		}

		$str = strip_tags($str);
		preg_match('/\s*(?:\S*\s*){'. (int) $limit .'}/', $str, $matches);
		if (strlen($matches[0]) == strlen($str))
		{
			$end_char = '';
		}
		return rtrim($matches[0]).$end_char;
	}

	function checkTmpFolderWritable()
	{
		$foldername = 'tmp';
		$folderpath = JPATH_ROOT.DS.$foldername;

		if (is_writable($folderpath) == false)
		{
			JError::raiseWarning(100, JText::sprintf('Folder "%s" is Unwritable. Please set Writable permission (CHMOD 777) for it before performing maintenance operations', DS.$foldername));
		}
		return true;
	}

	function renderMenuComboBox($ID, $elementText, $elementName, $parameters = '')
	{
		$query = $this->_db->getQuery(true);
		$query->select('menutype AS value, title AS text');
		$query->from('#__menu_types');
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		array_unshift($data, JHTML::_('select.option', '', '- '.JText::_($elementText).' -', 'value', 'text'));
		return JHTML::_('select.genericlist', $data, $elementName, $parameters, 'value', 'text', $ID);
	}

	function randSTR($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		$charsLength 	= (strlen($chars) - 1);
		$string 		= $chars{rand(0, $charsLength)};

		for ($i = 1; $i < $length; $i = strlen($string))
		{
			$r = $chars{rand(0, $charsLength)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}

		return $string;
	}

	function getEdition()
	{
		return trim(strtolower(JSN_IMAGESHOW_EDITION));
	}

	function getShortEdition()
	{
		$arrayStr = explode(' ', $this->getEdition());

		if (count($arrayStr) > 0)
		{
			return $arrayStr[0];
		}

		return null;
	}

	function callJSNButtonMenu()
	{
		jimport('joomla.html.toolbar');
		$path = JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers';
		$toolbar = JToolBar::getInstance('toolbar');
		$toolbar->addButtonPath($path);
		$toolbar->appendButton('JSNMenuButton');
	}

	function checkLimit()
	{
		$edition = $this->getShortEdition();

		if ($edition == 'pro')
		{
			return false;
		}

		return true;
	}

	function getModuleInformation($moduleName)
	{
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__modules');
		$query->where('module='. $this->_db->Quote($moduleName, false));
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		return $result;
	}

	function approveModule($moduleName, $publish = 1)
	{
		$query = $this->_db->getQuery(true);
		$query->update('#__modules');
		$query->set('published = '.(int) $publish);
		$query->where('module = '.$this->_db->Quote($moduleName, false));
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			return false;
		}

		return true;
	}


	function getJoomlaLevelName()
	{
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__viewlevels');
		$this->_db->setQuery($query);
		$items = $this->_db->loadObjectlist();

		$count  = count($items);
		$result = array();
		if($count)
		{
			for($i = 0; $i < $count; $i++)
			{
				$item = $items[$i];
				$result[$item->id] = strtolower($item->title);
			}
		}
		return $result;
	}

	function convertJoomlaLevelFromIDToName($data, $id)
	{
		$count = count($data);
		if($count)
		{
			if(!$id) $id = 1;
			return $data[$id];
		}
		return $id;
	}

	function convertJoomlaLevelFromNameToID($data, $name)
	{
		$count   = count($data);
		$default = '';
		$index   = 0;
		if ($count)
		{
			foreach ($data as $key => $value)
			{
				if (!$index)
				{
					$default = $key;
					$index   = 1;
				}
				if ($name == $value)
				{
					return $key;
				}
			}
			return $default;
		}
		return '';
	}

	function displayShowcaseMissingMessage()
	{
		$string = '<div class="jsn-missing-data-alert-box">';
		$string .= '<div class="header"><span class="icon-warning"></span><span class="message">'.JText::_('SITE_SHOWCASE_DATA_IS_NOT_CONFIGURED').'</span></div>';
		$string .= '<div class="footer"><span class="link-to-more">'.JText::_('SITE_SHOWCASE_CLICK_TO_LEARN_MORE').'</span></div></div>';
		return $string;
	}

	function displayShowlistMissingMessage()
	{
		$string = '<div class="jsn-missing-data-alert-box">';
		$string .= '<div class="header"><span class="icon-warning"></span><span class="message">'.JText::_('SITE_SHOWLIST_DATA_IS_NOT_CONFIGURED').'</span></div>';
		$string .= '<div class="footer"><span class="link-to-more">'.JText::_('SITE_SHOWLIST_CLICK_TO_LEARN_MORE').'</span></div></div>';
		return $string;
	}

	function displayShowlistNoImages()
	{
		$string = '<div class="jsn-missing-data-alert-box no-image">';
		$string .= '<div class="header"><span class="icon-warning"></span><span class="message">'.JText::_('SITE_SHOWLIST_NO_IMAGE').'</span></div>';
		$string .= '</div>';
		return $string;
	}

	function displayThemeMissingMessage()
	{
		$string = '<div class="jsn-missing-data-alert-box">';
		$string .= '<div class="header"><span class="icon-warning"></span><span class="message">'.JText::_('SITE_THEME_DATA_IS_NOT_CONFIGURED').'</span></div>';
		$string .= '<div class="footer"><span class="link-to-more">'.JText::_('SITE_THEME_CLICK_TO_LEARN_MORE').'</span></div></div>';
		return $string;
	}

	function renderListItems($arrayItems, $type = "showlist")
	{
		$itemID 		 = $type.'_id';
		$itemTitle 		 = $type.'_title';
		$showlistAddText = JText::_('JSN_MENU_CREATE_NEW_SHOWLIST');
		$showcaseAddText = JText::_('JSN_MENU_CREATE_NEW_SHOWCASE');

		$html 		= '';
		$html = '<ul class="jsn-submenu jsn-box-shadow-mini jsn-rounded-mini">';

		if (count($arrayItems) > 0)
		{
			foreach ($arrayItems as $item):
			$html .= '<li><a href="index.php?option=com_imageshow&controller='.$type.'&task=edit&cid[]='.$item->$itemID.'">
						  	'.htmlspecialchars($item->$itemTitle).'
						  </a></li>';
			endforeach;
			$html .= '<li class="separator"></li>';
		}

		$html .= '<li class="primary"><a href="index.php?option=com_imageshow&controller='.$type.'&task=add" title="'.htmlspecialchars(${$type.'AddText'}).'"><span class="jsn-icon16 jsn-icon-plus"></span>'.${$type.'AddText'}.'</a></li>';
		$html .= '</ul>';

		return $html;
	}

	function getExtensionInfoByID($id)
	{
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('extension_id='.(int) $id);
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		return $result;
	}

	function getCoreInfo()
	{
		$remoteInfo		     = array();
		$data				= new stdClass();
		$objJoolaVersion    = new JVersion();
		$coreData 	  		= $this->getComponentInfo();
		$coreInfo			= json_decode($coreData->manifest_cache);
		$description		= $coreInfo->description;
		$tmpDescription		= explode(' ', $description);
		$edition			= @$tmpDescription[2].' '.@$tmpDescription[3];
		$data->version 	 	= trim($coreInfo->version);
		$data->edition		= strtolower(trim($edition));
		$data->name 		= $coreInfo->name;
		$data->id 			= JSN_IMAGESHOW_IDENTIFIED_NAME;
		$data->needUpdate   = false;

		$link 				= JSN_IMAGESHOW_INFO_URL;
		$objJSNHTTP 		= JSNISFactory::getObj('classes.jsn_is_httprequest', null, $link);
		$result    			= $objJSNHTTP->DownloadToString();

		$versionData		= $this->getVersionInfoFromServer();

		if (!$versionData || $versionData == null)
		{
			$remoteInfo = array('connection' => false, 'version' => '', 'commercial'=>'', 'description'=>'', 'url'=>'');
		}
		else
		{
			$versionData   	= $this->paserVersionInfoFromServer($versionData);
			$item   		= $this->getItemsFromVersionInfoFromServer($versionData, $data->id);
			$remoteInfo 	= array('connection' => true, 'version' => '', 'commercial'=>false, 'description'=>'', 'url'=>'');

			if ($item->identified_name == $data->id && in_array($objJoolaVersion->RELEASE, explode(';', $item->tags)))
			{
				if (version_compare($data->version, $item->version) == -1)
				{
					$data->needUpdate = true;
				}

				foreach ($item->editions as $edition)
				{
					if (strtolower(trim($edition->edition)) == $data->edition)
					{
						$remoteInfo ['commercial'] = (boolean) $edition->authentication;
						break;
					}
				}
				$remoteInfo ['version'] 	= $item->version;
				$remoteInfo ['description'] = $item->description;
				$remoteInfo ['url'] 		= $item->url;
			}
		}

		$data->commercial = $remoteInfo['commercial'];
		$data->newVersion = @$remoteInfo['version'];
		return $data;
	}

	function parseVersionString ($str)
	{
		return explode('.', $str);
	}

	function compareVersion($runningVersionParam, $latestVersionParam)
	{
		$check	= false;
		$runningVersion 		= $this->parseVersionString($runningVersionParam);
		$countRunningVersion 	= count($runningVersion);
		$latestVersion 			= $this->parseVersionString($latestVersionParam);
		$countLatestVersion 	= count($latestVersion);
		$count 					= 0;
		if	($countRunningVersion > $countLatestVersion)
		{
			$count = $latestVersion;
		}
		else
		{
			$count = $countRunningVersion;
		}

		$minIndex = $count - 1;

		for($i = 0; $i < $count; $i++)
		{
			if ($runningVersion[$i] < $latestVersion[$i])
			{
				$check = true;
				break;
			}
			elseif($runningVersion[$i] == $latestVersion[$i] && $i == $minIndex && $countRunningVersion < $countLatestVersion)
			{
				$check = true;
				break;
			}
			elseif($runningVersion[$i] == $latestVersion[$i])
			{
				continue;
			}
			else
			{
				break;
			}
		}

		return $check;
	}

	function runSQLFile($file)
	{
		jimport('joomla.filesystem.file');

		if (JFile::exists($file))
		{
			$buffer = $this->readFileToString($file);

			if ($buffer === false) {
				return false;
			}

			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			if (count($queries) == 0)
			{
				JError::raiseWarning(100, $sqlFile . JText::_(' not exits'));
				return 0;
			}

			foreach ($queries as $query)
			{
				$query = trim($query);
				if ($query != '' && $query{0} != '#')
				{
					$this->_db->setQuery($query);

					if (!$this->_db->query())
					{
						JError::raiseWarning(100, 'JInstaller::install: '.JText::_('SQL Error')." ".$this->_db->stderr(true));
						return false;
					}
				}
			}

			return true;
		}
		else
		{
			JError::raiseWarning(100, $file . JText::_(' not exits'));
			return false;
		}
	}

	function checkSupportedFlashPlayer()
	{
		$userAgent	= $_SERVER['HTTP_USER_AGENT'];
		$deviceName = '';
		switch(true)
		{
			case (preg_match('/ipod/i', $userAgent)):
				$deviceName = 'ipod';
				break;
			case (preg_match('/iphone/i', $userAgent)):
				$deviceName = 'iphone';
				break;
			case (preg_match('/ipad/i', $userAgent)):
				$deviceName = 'ipad';
				break;
			case (preg_match('/android/i', $userAgent)):
				$deviceName = 'android';
				break;
			case (preg_match('/windows phone/i', $userAgent)):
				$deviceName = 'windows';
				break;
		}
		return $deviceName;
	}

	function downloadArchiveFile($fileName, $type = 'zip', $basePath = '')
	{
		jimport('joomla.filesystem.file');
		if ($basePath == '') {
			$basePath = JPATH_ROOT.DS.'tmp';
		}
		$filePath 	= $basePath.DS.$fileName;
		$fileSize 	= filesize($filePath);

		switch ($type)
		{
			case "zip":
				header("Content-Type: application/zip");
				break;
			case "bzip":
				header("Content-Type: application/x-bzip2");
				break;
			case "gzip":
				header("Content-Type: application/x-gzip");
				break;
			case "tar":
				header("Content-Type: application/x-tar");
		}
		$header = "Content-Disposition: attachment; filename=\"";
		$header .= $fileName;
		$header .= "\"";
		header($header);
		header('Content-Description: File Transfer');
		header("Content-Length: " . $fileSize);
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: no-cache, must-revalidate, max-age=60");
		header("Expires: Sat, 01 Jan 2000 12:00:00 GMT");
		ob_clean();
		flush();
		@readfile($filePath);
	}

	/**
	 * check support CURL
	 * @return true/false
	 */
	function checkCURL()
	{
		if (!function_exists("curl_init") &&
		!function_exists("curl_setopt") &&
		!function_exists("curl_exec") &&
		!function_exists("curl_close")) {
			return false;
		};

		return true;
	}

	/**
	 * check accessing URL when use fopen
	 * @return true/false
	 */
	function checkFOPEN() {
		return (boolean) ini_get('allow_url_fopen');
	}

	/**
	 * convert a file to read string
	 * @param file to read
	 * @return return read string or false on failure
	 */
	function readFileToString($file)
	{
		if (!JFile::exists($file)) return false;

		$file = @fopen($file, 'r');

		$contents = '';

		while (!feof($file))
		{
			$contents .= fread($file, 8192);

			if ($contents === false) {
				return false;
			}
		}

		fclose($file);

		return $contents;
	}

	/**
	 * check necessary ext for download
	 * @return true/false
	 */
	function checkEnvironmentDownload()
	{
		if ($this->checkCURL() || $this->checkFOPEN() || function_exists('fsockopen')) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * check necessary ext for install package
	 * @return true/false
	 */
	function checkEnvironmentInstall()
	{
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLFILE'));
			return false;
		}

		if (!extension_loaded('zlib')) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLZLIB'));
			return false;
		}
	}

	function folderIsWritable($foldername, $type='error')
	{
		if ($foldername == '') $foldername = 'tmp';

		$folderpath = JPATH_ROOT.DS.$foldername;

		if (!is_writable($folderpath))
		{
			if ($type == 'error')
			{
				JError::raiseWarning(100, JText::sprintf('Folder "%s" is Unwritable. Please set Writable permission (CHMOD 777) for it before performing any operations', DS.$foldername));
			}
			return false;
		}
		return true;
	}

	function getVersionInfoFromServer()
	{
		$link = JSN_IMAGESHOW_INFO_URL;
		$objJSNHTTP = JSNISFactory::getObj('classes.jsn_is_httprequest', null, $link);
		$result =  $objJSNHTTP->DownloadToString();
		if (!$result)
		{
			return false;
		}
		return $result;
	}

	function paserVersionInfoFromServer($data)
	{
		if (!$data && $data == null) return false;

		$result				= array();
		$tmpresult			= array();
		$data 				= json_decode($data);
		if (is_null($data)) return false;

		if (isset($data->items))
		{
			$tmp_category_codename = trim($data->category_codename);
			foreach ($data->items as $item)
			{
				if (!isset($item->category_codename) && @$item->category_codename == '')
				{
					$tmpresult[$tmp_category_codename][trim($item->identified_name)] = $item;
				}
				else
				{
					$tmpresult[$tmp_category_codename][trim($item->category_codename)] = $item;
				}
			}
		}

		if (!count($tmpresult)) return $result;

		if (isset($tmpresult[JSN_IMAGESHOW_CATEGORY_EXTENSION][JSN_IMAGESHOW_CATEGORY]))
		{
			$returnedData = $tmpresult[JSN_IMAGESHOW_CATEGORY_EXTENSION][JSN_IMAGESHOW_CATEGORY];
			if (isset($returnedData->items))
			{
				$category_codename = trim($returnedData->category_codename);
				foreach ($returnedData->items as $item)
				{
					if (!isset($item->category_codename) && @$item->category_codename == '')
					{
						$result[$category_codename][trim($item->identified_name)] = $item;
					}
					else
					{
						$result[$category_codename][trim($item->category_codename)] = $item;
					}
				}
			}
			return $result;
		}
		else
		{
			return array();
		}
	}

	function getItemsFromVersionInfoFromServer($data, $key)
	{
		$data = @$data[JSN_IMAGESHOW_CATEGORY];
		if ($data == null) return array();
		if(count($data))
		{
			foreach ($data as $subkey => $item)
			{
				$pos = strpos($subkey, $key);
				if($pos !== false) return $item;
			}
		}
		return array();
	}

	// check if url have http or www
	function isDomain($url){
		$pattern = '(^http(s)?:\/\/|^www.)';
		if(preg_match($pattern, $url)){
			return true;
		}else{
			return false;
		}
	}
	function convertSmartQuotes($string)
	{
		$escapeCharacters = array("\r\n", "\n", "\r");
		$string 	  	= str_replace($escapeCharacters, '', $string);
		$string      	= $this->cp1252ToUTF8($string);

		$find[] = 'â€œ';  // left side double smart quote
		$find[] = 'â€';  // right side double smart quote
		$find[] = 'â€˜';  // left side single smart quote
		$find[] = 'â€™';  // right side single smart quote

		$replace[] = '"';
		$replace[] = "'";

		$string = str_replace($find, $replace, $string);
		$string = str_replace("\"", "'", $string);
		return $string;
	}
	function cp1252ToUTF8($str) {
		$cp1252_map = array (
		    "\xc2\x91" => "\xe2\x80\x98",
		    "\xc2\x92" => "\xe2\x80\x99",
		    "\xc2\x93" => "\xe2\x80\x9c",
		    "\xc2\x94" => "\xe2\x80\x9d",
		);
		return strtr ($str , $cp1252_map );
	}

	function checkFolderUnwritableOnUpdateAndUpgrade($type='core')
	{
		$folders = array();
		if(!is_writable(JPATH_ROOT.DS.'tmp')){
			array_push($folders,'/tmp');
		}
		switch ($type)
		{
			case 'core':
				if(!is_writable(JPATH_ROOT.DS.'administrator'.DS.'components')){
					array_push($folders,'/administrator/components');
				}
				if(!is_writable(JPATH_ROOT.DS.'components')){
					array_push($folders,'/components');
				}
				if(!is_writable(JPATH_ROOT.DS.'plugins')){
					array_push($folders,'/plugins');
				}
				if(!is_writable(JPATH_ROOT.DS.'modules')){
					array_push($folders,'/modules');
				}
				if(!is_writable(JPATH_ROOT.DS.'plugins'.DS.'content')){
					array_push($folders,'/plugins/content');
				}
				if(!is_writable(JPATH_ROOT.DS.'plugins'.DS.'system')){
					array_push($folders,'/plugins/system');
				}
				if(!is_writable(JPATH_ROOT.DS.'plugins'.DS.'editors-xtd')){
					array_push($folders,'/plugins/editors-xtd');
				}
				break;
			case 'source':
			case 'theme':
				if(!is_writable(JPATH_ROOT.DS.'plugins'.DS.'jsnimageshow')){
					array_push($folders,'/plugins/jsnimageshow');
				}
				break;
			default:
				break;
		}
		if(!is_writable(JPATH_ROOT.DS.'language')){
			array_push($folders,'/language');
		}
		if(!is_writable(JPATH_ROOT.DS.'administrator'.DS.'language')){
			array_push($folders,'/administrator/language');
		}

		return $folders;
	}

	function getVersion()
	{
		$version = JSN_IMAGESHOW_VERSION;
		return 	$version;
	}

	function getCurrentElementsOfImageShow()
	{
		$indentifiedNames	= array();
		$indentifiedNames[JSNUtilsText::getConstant('IDENTIFIED_NAME', 'framework')] = JSNUtilsText::getConstant('VERSION', 'framework');
		$indentifiedNames[JSN_IMAGESHOW_IDENTIFIED_NAME] = $this->getVersion();
		// Gets all themes
		$modelThemePlugin	= JModelLegacy::getInstance('plugins', 'imageshowmodel');
		$themeItems			= $modelThemePlugin->getFullData();
		if (count($themeItems))
		{
			for($i = 0, $count = count($themeItems); $i < $count; $i++)
			{
				$themeItem 					= $themeItems[$i];
				$manifestCachce 			= json_decode($themeItem->manifest_cache);

				$indentifiedNames [strtolower($themeItem->element)] = strtolower($manifestCachce->version);
			}
		}

		// Gets all sources
		$objJSNSource = JSNISFactory::getObj('classes.jsn_is_source');
		$listSource = $objJSNSource->getListSources();

		if (count($listSource))
		{
			foreach ($listSource as $source)
			{
				if ($source->identified_name != 'folder')
				{
					$manifestCachce 				= json_decode($source->pluginInfo->manifest_cache);
					$indentifiedNames [strtolower($source->identified_name)] = trim($manifestCachce->version);
				}
			}
		}

		return $indentifiedNames;
	}

	function escapeSpecialString($string)
	{
		return htmlspecialchars(addslashes(str_replace("'", '&rsquo;', $string)), ENT_COMPAT, 'UTF-8');
	}

	function loadJquery($url = '')
	{
		$document = JFactory::getDocument();
		if ($url == '')
		{
			$document->addScript(JURI::root(true) . '/components/com_imageshow/assets/js/jquery.min.js');
		}
		else
		{
			$document->addScript($url);
		}
	}
}