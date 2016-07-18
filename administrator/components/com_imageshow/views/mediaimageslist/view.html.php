<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: view.html.php 17386 2012-10-24 09:47:31Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class ImageShowViewMediaImagesList extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
		$document 		= JFactory::getDocument();
		$objJSNMedia 	= JSNISFactory::getObj('classes.jsn_is_media');

		$objJSNMedia->addStyleSheet(JURI::root(true).'/administrator/components/com_imageshow/assets/css/popup-imagemanager.css');
		$document->addScriptDeclaration("var JSNISImageManager = window.parent.JSNISImageManager;");

		$objJSNMediaManager = JSNISFactory::getObj('classes.jsn_is_mediamanager');
		$images 			= $objJSNMediaManager->getImages();
		$folders 			= $objJSNMediaManager->getFolders();

		$this->assign('baseURL', $objJSNMediaManager->comMediaBaseURL);
		$this->assignRef('images', $images);
		$this->assignRef('folders', $folders);
		parent::display($tpl);
	}
}