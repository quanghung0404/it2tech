<?php
/**
 * @version    $Id: view.html.php 16077 2012-09-17 02:30:25Z giangnd $
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
jimport( 'joomla.application.component.view');

class ImageShowViewImage extends JViewLegacy
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
		$app 	= JFactory::getApplication();
		$model  = $this->getModel();
		$this->_document = JFactory::getDocument();

		$showListID  	= $app->getUserState('com_imageshow.images.showlistID');
		$sourceName  	= $app->getUserState('com_imageshow.images.sourceName');
		$sourceType		= $app->getUserState('com_imageshow.images.sourceType');
		$imageID     	= $app->getUserState('com_imageshow.images.imageID');
		$image 	 		= $model->getItems($imageID,$showListID);
		$this->assign('image', $image);
		$this->_addAssets();
		parent::display($tpl);
	}

	/**
	 * Add nesscessary JS & CSS files
	 *
	 * @return void
	 */

	private function _addAssets()
	{
		$objJSNMedia = JSNISFactory::getObj('classes.jsn_is_media');

		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/conflict.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ck/jquery.ck.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-jwysiwyg/jquery.wysiwyg.js');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-jwysiwyg/jquery.wysiwyg.css');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');

		! class_exists('JSNBaseHelper') OR JSNBaseHelper::loadAssets();

		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/imageshow.css');
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/image_selector.css');

		JSNHtmlAsset::loadScript('imageshow/joomlashine/showlist', array(
			'pathRoot' => JURI::root(),
			'language' => JSNUtilsLanguage::getTranslated(array(
					'JSN_IMAGESHOW_OK',
					'JSN_IMAGESHOW_CLOSE'
					))
		));
	}
}
