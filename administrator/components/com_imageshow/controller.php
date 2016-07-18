<?php
/**
 * @version    $Id: controller.php 16139 2012-09-19 03:33:09Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
/**
 * General controller of JSN ImageShow component
 *
 * Controller (Controllers are where you put all the actual code.) Provides basic
 * functionality, such as rendering views (aka displaying templates).
 *
 * @package  JSN.ImageShow
 * @since    2.5
 */

class ImageShowController extends JControllerLegacy
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 */

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('plugin',  'display');
	}

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  A JController object to support chaining.
	 *
	 */

	public function display($cachable = false, $urlparams = false)
	{
		switch ($this->getTask())
		{
			case 'plugin':
				JRequest::setVar('layout', 'plugin');
				JRequest::setVar('view', 'cpanel');
				JRequest::setVar('model', 'cpanel');
				break;
			case 'alltip':
				JRequest::setVar('layout', 'all_tip');
				JRequest::setVar('view', 'cpanel');
				JRequest::setVar('model', 'cpanel');
				break;
			case 'modal':
				JRequest::setVar('layout', 'modal');
				JRequest::setVar('view', 'cpanel');
				JRequest::setVar('model', 'cpanel');
				break;
			default:
				JRequest::setVar('layout', 'default');
				JRequest::setVar('view', 'cpanel');
				JRequest::setVar('model', 'cpanel');
		}

		parent::display();
	}

	/**
	 * The function install sample data for ImageShow
	 *
	 * @return void
	 */

	public function sampledata()
	{
		$sampleData		= JRequest::getInt('sample_data');
		$menuType		= JRequest::getString('menutype');
		$keepAllData	= JRequest::getInt('keep_all_data');
		$installMessage	= JRequest::getInt('install_message');

		$model 	= $this->getModel('cpanel');
		$msg 	= '';

		if ($keepAllData == 1)
		{
			$model->clearData();
		}

		if ($installMessage == 1)
		{
			$objJSNInstMessage 	= JSNISFactory::getObj('classes.jsn_is_installermessage');
			$objJSNInstMessage->installMessage();
		}

		if ($sampleData == 1)
		{
			$model->populateDatabase();
			if ($menuType != '')
			{
				$model->insertMenuSample($menuType);
			}
			$msg  = JText::_('Install sample data successfully');
		}

		$link = 'index.php?option=com_imageshow';

		$this->setRedirect($link, $msg);
	}

	/**
	 * The function redirect user to the menu settings for creating a menu to display ImageShow
	 *
	 * @return void
	 */

	public function launchAdapter()
	{
		$jsnUtils		= JSNISFactory::getObj('classes.jsn_is_utils');
		$app 			= JFactory::getApplication();
		$type			= JRequest::getCmd('type');
		$showcaseID 	= JRequest::getInt('showcase_id');
		$showlistID 	= JRequest::getInt('showlist_id');
		$app->setUserState('com_imageshow.add.showcase_id', $showcaseID);
		$app->setUserState('com_imageshow.add.showlist_id', $showlistID);

		switch ($type)
		{
			case 'module':
				$moduleInfo 	= $jsnUtils->getModuleInfo();
				$link 			= 'index.php?option=com_modules&task=module.add&eid=' . $moduleInfo->extension_id;
				$this->setRedirect($link);
				break;
			case 'menu':
				$componetInfo 				= $jsnUtils->getComponentInfo();
				$data ['type'] 				= 'component';
				$data ['title'] 			= '';
				$data ['alias'] 			= '';
				$data ['note'] 				= '';
				$data ['link'] 				= 'index.php?option=com_imageshow&view=show';
				$data ['published'] 		= '1';
				$data ['access'] 			= '1';
				$data ['menutype'] 			= JRequest::getCmd('menutype');
				$data ['parent_id'] 		= '1';
				$data ['browserNav'] 		= '0';
				$data ['home'] 				= '0';
				$data ['language'] 			= '*';
				$data ['template_style_id'] = '0';
				$data ['id'] 				= '0';
				$data ['component_id'] 		= $componetInfo->extension_id;
				$app->setUserState('com_menus.edit.item.data', $data);
				$app->setUserState('com_menus.edit.item.type', 'component');
				$app->setUserState('com_menus.edit.item.link', 'index.php?option=com_imageshow&view=show');
				$link 						= 'index.php?option=com_menus&view=item&layout=edit';
				$this->setRedirect($link);
				break;
			default:
				break;
		}
		return true;
	}

	/**
	 * Hide a message
	 *
	 * @return void
	 */

	public function hideMsg()
	{
		$msgID = JRequest::getInt('msgId');
		$objJSNMsg 	= JSNISFactory::getObj('classes.jsn_is_message');
		$objJSNMsg->disableMessage($msgID);
		exit();
	}
}
