<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import HTTP client library

class_exists('http_class') OR require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/3rd-party/httpclient/http.php';

// Prevent this file from being included twice
if (class_exists('JSNClientInformation'))
{
	return;
}

/**
 * Class containing compatibility relation between framework and extension.
 *
 * @package  JSN_Framework
 * @since    1.1.0
 */
class JSNClientInformation
{
	/**
	 * Method to post client information
	 *
	 * @return void
	 *
	 */
	public static function postClientInformation()
	{
		$app = JFactory::getApplication();
		if (!$app->isAdmin())
		{
			return false;
		}
		
		$user = JFactory::getUser();
		if (!$user->authorise('core.admin'))
		{
			return false;
		}
		
		$framework = JTable::getInstance('Extension');
		$framework->load(
				array(
						'element' => 'jsnframework',
						'type'  => 'plugin',
						'folder' => 'system'
				)
		);
	
		
		// Check if JoomlaShine extension framework is disabled?
		if (! $framework->extension_id OR ! $framework->enabled)
		{
			return false;
		}
	
		// array informations will be post
		$dataInformations = array();
	
		// system information
		$dataInformations['systemInfo'] = self::getSystemInfo();
	
		// php information
		$dataInformations['phpInfo'] = self::getPhpSettings();
	
		// user information
		$dataInformations['userInfo'] = self::getUserInfo();
	
		// list ext jsn installed
		$dataInformations['installedExtList'] = self::getInstalledExtensionList();

		$secret_key = md5($dataInformations['userInfo']['domain'] . $dataInformations['userInfo']['server_ip']);
		
		$http                       = new http_class;
		$http->timeout              = 0;
		$http->data_timeout         = 0;
		$url                        = JSN_EXT_POST_CLIENT_INFORMATION_URL;
		$error                      = $http->GetRequestArguments($url,$arguments);
	
		$arguments["RequestMethod"] = "POST";
		$arguments["PostValues"] = array(
				'client_information' => json_encode($dataInformations),
				'secret_key' => $secret_key
		);
	
		try
		{
			$error = $http->Open($arguments);
			if ($error == "")
			{
				$error = $http->SendRequest($arguments);
				$http->Close();
				if ($error == "")
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	/**
	 * Method to get the php settings
	 *
	 * @return array some php settings
	 *
	 */
	public static function getPhpSettings()
	{
		$phpSettings = array();
			
		$phpSettings['php_built_on']             = php_uname();
		$phpSettings['php_version']              = phpversion();

		return $phpSettings;
	}
	
	/**
	 * Method to get the system information
	 *
	 * @return  array system information values
	 *
	 */
	public static function getSystemInfo()
	{
		$version                             = new JVersion;
		$platform                            = new JPlatform;
		$db                                  = JFactory::getDbo();
	
		$sysInfo                             = array();
		$sysInfo['database_version']         = $db->getVersion();
		$sysInfo['database_collation']       = $db->getCollation();
		$sysInfo['web_server']               = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : getenv('SERVER_SOFTWARE');
		$sysInfo['server_api']               = php_sapi_name();
		$sysInfo['joomla_version']           = $version->getLongVersion();
		$sysInfo['joomla_platform_version']  = $platform->getLongVersion();
	
		return $sysInfo;
	}
	
	/**
	 * Method to get the user information
	 *
	 * @return  array user information values
	 *
	 */
	public static function getUserInfo()
	{
		$user                  = JFactory::getUser();
		$userInfo              = array();
	
		$userInfo['domain']    = JURI::root();
		$userInfo['server_ip'] = self::getServerAddress();
		
		return $userInfo;
	}
	
	/**
	 * Method to get list extension install
	 *
	 * @return  array list extension install
	 *
	 */
	public static function getInstalledExtensionList()
	{
		// list ext jsn installed
		$productLists 			= '';
		$products 				= JSNVersion::$products;
		$installedExtensionList = array();
		$productLists 			= '"' . implode('","', $products) . '"';
	
		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);
	
		// Build query
		$q->select('*');
		$q->from($db->quoteName('#__extensions'));
		$q->where($db->quoteName('element') . " IN ({$productLists})");
		$q->where($db->quoteName('type') . "=" . $db->quote('component'));

		// Execute query
		$db->setQuery($q);
		try 
		{
			$extensions = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			return $installedExtensionList;
		}

		if (count($extensions))
		{	
			foreach ($extensions as $extension)
			{				
				$manifest = json_decode($extension->manifest_cache);
				
				$oldDefineFile = JPATH_ADMINISTRATOR . '/components/' . $extension->element . '/defines.' . str_replace('com_', '', $extension->element) . '.php';
				
				$defineFile = JPATH_ADMINISTRATOR . '/components/' . $extension->element . '/' . str_replace('com_', '', $extension->element) . '.defines.php';
				
				$installedExtensionList[$extension->element]['edition'] = '';
				
				if (file_exists($defineFile))
				{					
					$constName = 'JSN_' . strtoupper( str_replace('com_', '', $extension->element) ) . '_EDITION';
					$defineFileContent = file_get_contents($defineFile);
					
					if (preg_match('#DEFINE\(\'' . $constName . '\',\s*\'(.*)\'\)\s*;#i', $defineFileContent, $match))
					{
						$installedExtensionList[$extension->element]['edition'] = $match[1];
					}	

				}
				elseif (file_exists($oldDefineFile))
				{
					$constName = 'JSN_' . strtoupper( str_replace('com_', '', $extension->element) ) . '_EDITION';					
					$oldDefineFileContent = file_get_contents($oldDefineFile);

					if (preg_match('#DEFINE\(\'' . $constName . '\',\s*\'(.*)\'\)\s*;#i', $oldDefineFileContent, $match))
					{
						$installedExtensionList[$extension->element]['edition'] = $match[1];
					}
				}
				else
				{
					$installedExtensionList[$extension->element]['edition'] = '';
				}	
				
				$installedExtensionList[$extension->element]['version'] = $manifest->version;
				$installedExtensionList[$extension->element]['name']    = strtoupper( str_replace('_', ' ', $extension->element) );
				
			}
		}
		
		return $installedExtensionList;
	}
	
	/**
	 * Method to get server address
	 *
	 * @return  string
	 *
	 */
	public static function getServerAddress() 
	{
		if (array_key_exists('SERVER_ADDR', $_SERVER))
		{
			if ($_SERVER['SERVER_ADDR'] == '::1')
			{
				if (array_key_exists('SERVER_NAME', $_SERVER))
				{
					return gethostbyname($_SERVER['SERVER_NAME']);
				}
				else
				{
					// Running CLI
					if (stristr(PHP_OS, 'WIN'))
					{
						return gethostbyname(php_uname("n"));
					} else {
						$ifconfig = shell_exec('/sbin/ifconfig eth0');
						preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
						return $match[1];
					}					
				}
			}
			return $_SERVER['SERVER_ADDR'];
		}
		elseif (array_key_exists('LOCAL_ADDR', $_SERVER))
		{
			return $_SERVER['LOCAL_ADDR'];
		}
		elseif (array_key_exists('SERVER_NAME', $_SERVER))
		{
			return gethostbyname($_SERVER['SERVER_NAME']);
		}
		else
		{
			// Running CLI
			if (stristr(PHP_OS, 'WIN'))
			{
				return gethostbyname(php_uname("n"));
			} else {
				$ifconfig = shell_exec('/sbin/ifconfig eth0');
				preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
				return $match[1];
			}
		}
		
		return '';
	}
}
