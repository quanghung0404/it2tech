<?php
/**
 * @version    $Id: view.showlist.php 16077 2012-09-17 02:30:25Z giangnd $
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

class ImageShowViewShowLists extends JViewLegacy
{
	/**
	 * Display method
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return	void
	 */

	public function display($tpl = null)
	{
		$objJSNShowlist		= JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNShowcase		= JSNISFactory::getObj('classes.jsn_is_showcase');
		$objJSNUtils		= JSNISFactory::getObj('classes.jsn_is_utils');
		$URL				= dirname($objJSNUtils->overrideURL()) . '/';
		$showlistID 		= JRequest::getVar('showlist_id');
		$dataObj 			= $objJSNShowlist->getShowlist2JSON($URL, $showlistID);
		if (count($dataObj->showlist->images->image))
		{
			foreach ($dataObj->showlist->images->image as $image)
			{
				$image->description = strip_tags($image->description);
			}
		}		
		echo json_encode($dataObj);
		jexit();
	}
}

