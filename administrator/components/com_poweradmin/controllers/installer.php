<?php
/**
 * @version     $Id: installer.php 17617 2012-10-29 08:14:12Z cuongnm $
 * @package     JSN_Poweradmin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import JSN Installer library
require_once JPATH_COMPONENT_ADMINISTRATOR . '/libraries/joomlashine/installer/controller.php';

/**
 * Installer controller
 *
 * @package     JSN_Poweradmin
 * @since       1.1.0
 */
class PoweradminControllerInstaller extends JSNInstallerController
{
	public function installPaExtension()
	{
		$this->model = $this->getModel('installer');
		$canDo	= JHelperContent::getActions('com_installer');
		if($canDo->get('core.manage'))
		{	
			try{
				$rs	= $this->model->download();
				$this->input->set('package', $rs);
				$this->input->set('type', 'plugin');
				$this->input->set('folder', 'jsnpoweradmin');
				$this->input->set('publish', 1);
				$this->input->set('client', 'site');
				$this->input->set('name', str_ireplace(JSN_POWERADMIN_EXT_IDENTIFIED_NAME_PREFIX, '', $this->input->getCmd('identified_name', '')));	
				
				// Set extension parameters
				$_GET['package'] 	= $rs;
				$_GET['type']		= 'plugin';
				$_GET['folder']		= 'jsnpoweradmin';
				$_GET['publish']	= 1;
				$_GET['client']		= 'site';
				$_GET['name']		= str_ireplace(JSN_POWERADMIN_EXT_IDENTIFIED_NAME_PREFIX, '', $this->input->getCmd('identified_name', ''));
	
				if ($this->model->install($rs))
				{
					require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/extensions.php';
					// Enable extension suport
					$_GET['name']		= str_ireplace(JSN_POWERADMIN_EXT_IDENTIFIED_NAME_PREFIX, '', $this->input->getCmd('identified_name', ''));
					try
					{
						JSNPaExtensionsHelper::enableExt($identifiedName);
					}catch (Exception $ex)
					{
						exit ('notenabled');
					}
				}
			}
			catch (Exception $ex)
			{
				exit ($ex->getMessage());
			}
			exit('success');
		}
	}
}
