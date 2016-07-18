<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * General controller.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
class JSNEasySliderController extends JSNBaseController
{
	/**
	 * Method for display page.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Set default view if not set
		$input->set('view', $input->getCmd('view', 'sliders'));

		// Call parent method
		parent::display($cachable, $urlparams);
	}

	/**
	 * Method for hiding a message
	 *
	 * @return	void
	 */
	function hideMsg()
	{
		jexit(JSNUtilsMessage::hideMessage(JFactory::getApplication()->input->getInt('msgId')));
	}

	/**
	 * launch Adapter
	 *
	 * @return boolean
	 */
	function launchAdapter()
	{
		$app	= JFactory::getApplication();
		$input	= $app->input;
		$type	= $input->getCmd('type');
		$sliderId	= $input->getInt('slider_id');

		$app->setUserState('com_easyslider.add.slider_id', $sliderId);

		switch ($type)
		{
			case 'module':
				$moduleInfo	= JSNEasySliderHelper::getModuleInfo();
				$link		= 'index.php?option=com_modules&task=module.add&eid=' . $moduleInfo->extension_id;
				$this->setRedirect($link);
			break;
			case 'menu':
				$componetInfo 				= JSNEasySliderHelper::getComponentInfo();
				$data ['type'] 				= 'component';
				$data ['title'] 			= '';
				$data ['alias'] 			= '';
				$data ['note'] 				= '';
				$data ['link'] 				= 'index.php?option=com_easyslider&view=slider';
				$data ['published'] 		= '1';
				$data ['access'] 			= '1';
				$data ['menutype'] 			= $input->getCmd('menutype');
				$data ['parent_id'] 		= '1';
				$data ['browserNav'] 		= '0';
				$data ['home'] 				= '0';
				$data ['language'] 			= '*';
				$data ['template_style_id'] = '0';
				$data ['slider_id'] 		= '0';
				$data ['component_id'] 		= $componetInfo->extension_id;
				$app->setUserState('com_menus.edit.slider.data', $data);
				$app->setUserState('com_menus.edit.slider.type', 'component');
				$app->setUserState('com_menus.edit.slider.link', 'index.php?option=com_easyslider&view=slider');
				$link = 'index.php?option=com_menus&view=slider&layout=edit';

				$this->setRedirect($link);
				break;
			default:
			break;
		}
		return true;
	}
}
