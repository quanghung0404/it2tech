<?php

/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Mobilization view.
 *
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JSNMobilizeViewModules extends JSNModulesView
{

	/**
	 * Method for display page.
	 *
	 * @param   boolean  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		JSession::checkToken('get') or jexit('Invalid Token');
		
		$baseUrl = JURI::base(true);
		$document = JFactory::getDocument();
		JSNHtmlAsset::addScriptPath('mobilize', $baseUrl . '/components/com_mobilize/assets/js');
		JSNHtmlAsset::loadScript('mobilize/modules');
		$document->addStyleSheet(JURI::base(true) . '/components/com_mobilize/assets/css/mobilize.css');
		parent::display($tpl);
	}

}
