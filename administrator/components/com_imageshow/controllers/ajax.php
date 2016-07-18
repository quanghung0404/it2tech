<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: ajax.php 14386 2012-07-25 09:25:28Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class ImageShowControllerAjax extends JControllerLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'ajax');
		parent::display();
	}

	function authenthicateCustomerInfo()
	{
		$post 			= JRequest::get('post');
		$objVersion 	= new JVersion();
		$link			= JSN_IMAGESHOW_AUTOUPDATE_URL.'&identified_name='.urlencode($post['identify_name']).'&based_identified_name='.urlencode($post['based_identified_name']).'&edition='.urlencode($post['edition']).'&joomla_version='.urlencode($objVersion->RELEASE).'&username='.urlencode($post['username']).'&password='.urlencode($post['password']).'&upgrade=no';
		$objJSNHTTP   	= JSNISFactory::getObj('classes.jsn_is_httprequest', null, $link);
		$result    		= $objJSNHTTP->DownloadToString();

		if ($result)
		{
			$decodeToJSON = json_decode($result);

			if (is_null($decodeToJSON))
			{
				echo json_encode(array('success' => false, 'multiple' => false, 'message'=>(string) $result, 'editions'=>array()));
			}
			else
			{
				echo json_encode(array('success' => true, 'multiple' => true, 'message'=>'', 'editions'=>$decodeToJSON->editions));
			}
		}
		else
		{
			echo json_encode(array('success' => false, 'multiple' => false, 'message'=> '', 'editions'=>array()));
		}
		exit();
	}

	function checkUpdateAllElements()
	{
		$objJSNUtils      	= JSNISFactory::getObj('classes.jsn_is_utils');
		$componentInfo 	  	= $objJSNUtils->getComponentInfo();
		$componentData 	  	= null;
		$objJoolaVersion    = new JVersion();
		$edition		  	= $objJSNUtils->getEdition();
		$componentData   	= json_decode($componentInfo->manifest_cache);
		$currentVersion   	= @$componentData->version;
		$indentifiedNames	= array();
		$exts 				= array();
		$exts[JSN_IMAGESHOW_IDENTIFIED_NAME] 	= $currentVersion;
		$indentifiedNames [] 					= JSN_IMAGESHOW_IDENTIFIED_NAME;

		// Gets all themes
		$modelThemePlugin	= JModelLegacy::getInstance('plugins', 'imageshowmodel');
		$themeItems			= $modelThemePlugin->getFullData();
		if (count($themeItems))
		{
			for($i = 0, $count = count($themeItems); $i < $count; $i++)
			{
				$themeItem 					= $themeItems[$i];
				$exts[$themeItem->element]  = $themeItem->version;
				$indentifiedNames [] 		= strtolower($themeItem->element);
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
					$exts[$source->identified_name] = $manifestCachce->version;
					$indentifiedNames [] 			= strtolower($source->identified_name);
				}
			}
		}

		$count = count($indentifiedNames);
		if ($count)
		{
			$data = $objJSNUtils->getVersionInfoFromServer();
			if (!$data)
			{
				echo json_encode(array('connection' => false, 'update' => false));
				exit();
			}
			else
			{
				if ($data != null)
				{
					$data   	= $objJSNUtils->paserVersionInfoFromServer($data);
					$core   	= $objJSNUtils->getItemsFromVersionInfoFromServer($data, JSN_IMAGESHOW_IDENTIFIED_NAME);
					$sources  	= $objJSNUtils->getItemsFromVersionInfoFromServer($data, JSN_IMAGESHOW_CATEGORY_IMAGESOURCES);
					$themes   	= $objJSNUtils->getItemsFromVersionInfoFromServer($data, JSN_IMAGESHOW_CATEGORY_THEMES);
					//check core version
					if (isset($core->identified_name) && isset($exts[$core->identified_name]) && $exts[$core->identified_name] != null)
					{
						if (version_compare($exts[$core->identified_name], $core->version) == -1 && in_array($objJoolaVersion->RELEASE, explode(';', $core->tags)))
						{
							echo json_encode(array('connection' => true, 'update' => true));
							exit();
						}
					}
					// check source version
					if (count(@$sources->items))
					{
						foreach ($sources->items as $item)
						{
							if (isset($item->identified_name) && isset($exts[$item->identified_name]) && $exts[$item->identified_name] != null)
							{
								if (version_compare($exts[$item->identified_name], $item->version) == -1 && in_array($objJoolaVersion->RELEASE, explode(';', $item->tags)))
								{
									echo json_encode(array('connection' => true, 'update' => true));
									exit();
								}
							}
						}
					}
					// check theme version
					if (count(@$themes->items))
					{
						foreach ($themes->items as $item)
						{
							if (isset($item->identified_name) && isset($exts[$item->identified_name]) && $exts[$item->identified_name] != null)
							{
								if (version_compare($exts[$item->identified_name], $item->version) == -1 && in_array($objJoolaVersion->RELEASE, explode(';', $item->tags)))
								{
									echo json_encode(array('connection' => true, 'update' => true));
									exit();
								}
							}
						}
					}
					echo json_encode(array('connection' => true, 'update' => false));
					exit();
				}
				echo json_encode(array('connection' => true, 'update' => false));
				exit();
			}
		}
		else
		{
			echo json_encode(array('connection' => false, 'update' => false));
			exit();
		}
	}
}
?>