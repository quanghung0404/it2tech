<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_themes.php 14433 2012-07-27 02:31:11Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
class JSNISThemes
{
	function getThemesFromServer()
	{
		$objJSNUtils    = JSNISFactory::getObj('classes.jsn_is_utils');
		$result 		= $objJSNUtils->getVersionInfoFromServer();
		$return     = '[]';
		if ($result && $result != null)
		{
			$result  = $objJSNUtils->paserVersionInfoFromServer($result);
			$return  = $objJSNUtils->getItemsFromVersionInfoFromServer($result, JSN_IMAGESHOW_CATEGORY_THEMES);
			$return  = @$return->items;
		}
		return $this->_listThemesOnServer = $return;
	}
	/**
	 * get list local theme installed
	 * @return list local theme installed
	 **/
	function getListLocal()
	{
		$objShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$listLocalTheme		= $objShowcaseTheme->listThemes();
		$themes 			= array();

		foreach ($listLocalTheme as $theme)
		{
			$theme['needUpdate'] 		= false;
			$theme['needInstall'] 		= false;
			$theme['authentication'] 	= false;
			$theme['identified_name'] 	= $theme['element'];
			$theme['name'] 				= $theme['name'];
			$themes[] = $theme;
		}

		return $themes;
	}

	/**
	 * compare remote sources with local sources
	 * @return remote sources
	 **/
	function compareSources()
	{
		$joomlaObject		= new JVersion();
		$joomlaVersion 		= $joomlaObject->RELEASE;
		$objShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$listLocalTheme		= $objShowcaseTheme->listThemes();
		$this->getThemesFromServer();
		$sources = array();
		if (count($this->_listThemesOnServer) && is_array($this->_listThemesOnServer))
		{
			foreach (@$this->_listThemesOnServer as $remoteTheme)
			{
				$tags 	= explode(';', trim(@$remoteTheme->tags));
				if (in_array($joomlaVersion, $tags))
				{
					$needInstall = true;
					$remoteTheme->needUpdate = false;
					foreach ($listLocalTheme as $localTheme)
					{
						if ($localTheme['element'] == $remoteTheme->identified_name)
						{
							$needInstall = false;
							// compare version
							$localPluginInfo = json_decode($localTheme['manifest_cache']);

							if (version_compare($localPluginInfo->version, $remoteTheme->version) >= 0)
							{
								$remoteTheme->needUpdate = false;
							}
							else
							{
								$remoteTheme->needUpdate = true;
							}
							$remoteTheme->oldVersion	= $localPluginInfo->version;
							$remoteTheme->newVersion	= $remoteTheme->version;
						}
					}
					$remoteTheme->needInstall = $needInstall;
					$sources[] = $remoteTheme;
				}
			}
		}
		return $sources;
	}

	/**
	 * compare local sources with remote sources
	 * @return local sources
	 **/
	function compareLocalSources()
	{
		$joomlaObject		= new JVersion();
		$joomlaVersion 		= $joomlaObject->RELEASE;
		$listLocalTheme		= $this->getListLocal();
		$this->getThemesFromServer();
		$sources = array();

		foreach ($listLocalTheme as $localTheme)
		{
			$localTheme['authentication'] = false;
			$localTheme['needInstall'] 	  = false;
			$localTheme['related_products'] = array();
			$localTheme['needUpdate'] = false;
			if (count($this->_listThemesOnServer) && is_array($this->_listThemesOnServer))
			{
				foreach (@$this->_listThemesOnServer as $remoteTheme)
				{
					$tags = explode(';', trim(@$remoteTheme->tags));
					if (in_array($joomlaObject->RELEASE, $tags))
					{
						if ($localTheme['identified_name'] == $remoteTheme->identified_name)
						{
							// compare version
							$localPluginInfo = json_decode($localTheme['manifest_cache']);

							if (version_compare($localPluginInfo->version, $remoteTheme->version) >= 0)
							{
								$localTheme['needUpdate'] = false;
							}
							else
							{
								$localTheme['needUpdate'] = true;
							}
							$localTheme['authentication'] = $remoteTheme->authentication;
							$localTheme['related_products'] = @$remoteTheme->related_products;
						}
					}
				}
			}
			$sources[] = (object) $localTheme;
		}

		return $sources;
	}

	function getNeedInstallList($sources)
	{
		$results = array();
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
	function getListThemeForInstall()
	{
		$session 	= JFactory::getSession();
		$preVersion = (float) str_replace('.', '', $session->get('preversion', null, 'jsnimageshow'));
		$version400 = (float) str_replace('.', '', '4.0.0');
		$remoteThemes		= $this->compareSources();
		$tmpRemoteThemes	= array();

		foreach ($remoteThemes as $remoteTheme) {
			$tmpRemoteThemes[$remoteTheme->identified_name] = $remoteTheme;
		}

		$task = '';

		$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$listThemes 		 = $objJSNShowcaseTheme->getListThemeDefineToInstall();
		$themes 			 = array();
		$objVersion 		 = new JVersion();

		$listRequired = $session->get('jsn-list-required-install', array(), 'jsn-install-manual');

		foreach ($listThemes as $theme)
		{
			if ($objJSNShowcaseTheme->checkThemePluginInstallByThemeName($theme) == false) {
				$task = 'new';
			}
			else
			{
				if (@$tmpRemoteThemes[$theme]->needUpdate) {
					$task = 'new';
				}

				if (!count($tmpRemoteThemes) && !isset($listRequired[$theme])) {
					$task = 'new';
				}

				if (!count($tmpRemoteThemes) && isset($listRequired[$theme]) && $listRequired[$theme] == false) {
					$task = 'new';
				}
			}

			$info = new stdClass();
			$info->identify_name 	= $theme;
			$info->full_name 		= (count($tmpRemoteThemes) > 0 && isset($tmpRemoteThemes[$theme])) ? $tmpRemoteThemes[$theme]->name : $theme;
			$info->edition 			= '';
			$info->joomla_version 	= $objVersion->RELEASE;
			$info->task 			= $task;
			$info->commercial 		= false;
			$info->default_install  = true;
			$themes[] = $info;
		}

		return $themes;
	}
}