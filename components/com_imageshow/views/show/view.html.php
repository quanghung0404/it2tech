<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: view.html.php 11746 2012-03-15 04:41:16Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
class ImageShowViewShow extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		$pageclassSFX 			= '';
		$titleWillShow 			= '';
		$app 					= JFactory::getApplication('site');
		$input 					= $app->input;
		$menu_params 			= $app->getParams('com_imageshow');
		$menus					= $app->getMenu();
		$menu 					= $menus->getActive();
		$jsnisID 				= JRequest::getInt('jsnisid', 0);

		$showCaseID = $input->getInt('showcase_id', 0);

		if ($jsnisID != 0)
		{
			$pageclassSFX 	= $menu_params->get('pageclass_sfx');
			$showPageTitle 	= $menu_params->get('show_page_heading');
			$pageTitle 		= $menu_params->get('page_title');

			if (!empty($showPageTitle))
			{
				if (!empty($pageTitle))
				{
					$titleWillShow = $pageTitle;
				}
				else if (!empty($item->name))
				{
					$titleWillShow = $item->name;
				}
			}
		}

		$showListID 			= $input->getInt('showlist_id', 0);
		$showBreadCrumbs		= $input->getInt('show_breadcrumbs', 0);

		$objJSNShow				= JSNISFactory::getObj('classes.jsn_is_show');
		$objUtils				= JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNShowlist         = JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNShowcase         = JSNISFactory::getObj('classes.jsn_is_showcase');
		$objJSNImages			= JSNISFactory::getObj('classes.jsn_is_images');
		$coreData 	  			= $objUtils->getComponentInfo();
		$coreInfo				= json_decode($coreData->manifest_cache);
		$paramsCom				= $mainframe->getParams('com_imageshow');

		$randomNumber 			= $objUtils->randSTR(5);
		$showlistInfo 			= $objJSNShowlist->getShowListByID($showListID);

		if (count($showlistInfo) && $showBreadCrumbs)
		{
			$pathway = $app->getPathway();
			$pathway->addItem($showlistInfo['showlist_title']);
		}

		$articleAuth 			= $objJSNShow->getArticleAuth($showListID);
		$row 					= $objJSNShowcase->getShowCaseByID($showCaseID);

		$imageSource 			= JSNISFactory::getSource($showlistInfo['image_source_name'], $showlistInfo['image_source_type'], $showlistInfo['showlist_id']);
		$imagesData 			= $objJSNImages->getImagesByShowlistID($showlistInfo['showlist_id']);

		$this->assignRef('titleWillShow', $titleWillShow);
		$this->assignRef('showcaseInfo', $row);
		$this->assignRef('randomNumber', $randomNumber);
		$this->assignRef('imagesData', $imagesData);
		$this->assignRef('showlistInfo', $showlistInfo);
		$this->assignRef('articleAuth', $articleAuth);
		$this->assignRef('pageclassSFX', $pageclassSFX);
		$this->assignRef('objUtils', $objUtils);
		$this->assignRef('Itemid', $menu->id);
		$this->assignRef('coreInfo', $coreInfo);
		parent::display($tpl);
	}
}