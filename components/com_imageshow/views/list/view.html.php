<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
class ImageShowViewList extends JViewLegacy
{
	public function display($tpl = null)
	{
		$doc = JFactory::getDocument();
		$objJSNShowlist		= JSNISFactory::getObj('classes.jsn_is_showlist');
		$objJSNShowcase		= JSNISFactory::getObj('classes.jsn_is_showcase');
		$objJSNImages		= JSNISFactory::getObj('classes.jsn_is_images');
		$objUtils			= JSNISFactory::getObj('classes.jsn_is_utils');

		$app 				= JFactory::getApplication('site');
		$menuParams 		= $app->getParams('com_imageshow');
		$menus				= $app->getMenu();
		$menu 				= $menus->getActive();
		$itemid				= @$menu->id;

		if (count($menuParams->get('showlist_id')))
		{
			$showlists			= $objJSNShowlist->getShowlistIDs(implode(',', $menuParams->get('showlist_id')));
		}
		else
		{
			$showlists = array();
		}
		$showcase			= $objJSNShowcase->getShowCaseByID($menuParams->get('showcase_id'));
		//$menuLayout 		= $menuParams->get('layout', 'thumbnails');

		/*$height 			= $showcase->general_overall_height;
		$width 				= $showcase->general_overall_width;

		if (!strpos($width, '%'))
		{
			$width = (int) $width;
		}

		$this->width 			= $width;
		$this->height 			= $height;*/
		$this->showlists 		= $showlists;
		$this->showcase 		= $showcase;
		$this->itemid 			= $itemid;
		$this->objJSNImages 	= $objJSNImages;
		$this->objJSNShowlist	= $objJSNShowlist;
		$this->menuParams 		= $menuParams;
		$this->document	 		= $doc;
		//$this->menuLayout 		= $menuLayout;
		$this->objUtils			= $objUtils;
		$this->_addAssets();
		parent::display($tpl);
	}

	private function _addAssets()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(true) . '/components/com_imageshow/assets/css/imageshow.css');
		$doc->addStyleSheet(JURI::root(true) . '/components/com_imageshow/assets/css/style.css');
	}
}