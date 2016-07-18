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
class JSNMobilizeViewPositions extends JSNPositionsView
{

	public function display($tpl = null)
	{
		JSession::checkToken('get') or jexit('Invalid Token');
		
		if(!function_exists('curl_version')){
			$contr = new JControllerLegacy();
			$contr->setRedirect('index.php?option=com_mobilize&task=position.selectPosition');
			$contr->redirect();
		}
		$this->setFilterable(false);
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true) . '/media/jui/js/jquery.js');

		if (isset($this->filterEnabled) AND $this->filterEnabled)
		{
			JSNHtmlAsset::addScript(JSN_MOBILIZE_ASSETS_URL . '/js/jsn.jquery.noconflict.js');
		}
		/**
		 * When position clicked
		 * object returned after this event fired is
		 * clicked position
		 * Use $(this)
		 */
		$onPostionClick = "
			if ( !$(this).hasClass('active-position') ){
				window.parent.jQuery.jSelectPosition($(this).find('p').text());				
			}
		";
		$this->addPositionClickCallBack($onPostionClick);

		parent::display($tpl);
	}

}
