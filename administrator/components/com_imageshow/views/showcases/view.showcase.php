<?php
/**
 * @version    $Id: view.showcase.php 16516 2012-09-27 11:38:09Z haonv $
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

// Import Joomla view library
jimport('joomla.application.component.view');

class ImageShowViewShowCases extends JViewLegacy
{
	/**
	 * Display method
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return	void
	 */

	function display($tpl = null)
	{
		$document 	= JFactory::getDocument();
		$document->setMimeEncoding( 'application/json' );

		$showcaseID			= JRequest::getVar('showcase_id');
		$objJSNShowcase		= JSNISFactory::getObj('classes.jsn_is_showcase');
		$objShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$themeProfile 		= $objShowcaseTheme->getThemeProfile($showcaseID);

		if ($showcaseID > 0 && !is_null($themeProfile))
		{
			$showcaseData = $objJSNShowcase->getShowcaseByID($showcaseID);
		}
		elseif ($showcaseID > 0 && is_null($themeProfile))
		{
			$theme						= JRequest::getVar('theme');
			$showcaseTable				= JTable::getInstance('showcase', 'Table');
			$showcaseTable->showcase_id = $showcaseID;
			$showcaseTable->theme_name	= $theme;
			$showcaseData				= $showcaseTable;
		}
		else
		{
			$theme 						= JRequest::getVar('theme');
			$showcaseTable				= JTable::getInstance('showcase', 'Table');
			$showcaseTable->showcase_id	= 0;
			$showcaseTable->theme_name	= $theme;
			$showcaseData				= $showcaseTable;

		}

		$objJSNUtils	= JSNISFactory::getObj('classes.jsn_is_utils');
		$URL   			= dirname($objJSNUtils->overrideURL()) . '/';
		$dataObj 		= $objJSNShowcase->getShowcase2JSON($showcaseData, $URL);

		echo json_encode($dataObj);
		jexit();
	}
}
