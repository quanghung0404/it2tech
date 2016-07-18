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

class ImageShowControllerImage extends JControllerLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('editimage', 'display');
		$this->registerTask('showlinkpopup', 'display');
	}

	public function display($cachable = false, $urlparams = false)
	{
		//JRequest::setVar('hidemainmenu', 1);
		$document = JFactory::getDocument();

		$task = $this->getTask();
		switch ($task) {
			case 'editimage':
				//JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('layout', 'editimage');
				JRequest::setVar('view', 'image');
				JRequest::setVar('model', 'image');
				break;
			case 'linkpopup':
				//JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('layout', 'linkpopup');
				JRequest::setVar('view', 'image');
				JRequest::setVar('model', 'image');
			default:
				# code...
				break;
		}
		//JRequest::setVar('edit', false );

		$imageID     = JRequest::getVar('imageID', '');
		$showListID  = JRequest::getVar('showListID', '');
		$sourceName  = JRequest::getVar('sourceName', '');
		$sourceType  = JRequest::getVar('sourceType', '');
		$app 		 = JFactory::getApplication();

		$app->setUserState('com_imageshow.images.imageID', $imageID);
		$app->setUserState('com_imageshow.images.showlistID', $showListID);
		$app->setUserState('com_imageshow.images.sourceName', $sourceName);
		$app->setUserState('com_imageshow.images.sourceType', $sourceType);
		parent::display();
	}

	/**
	 *
	 * Save image details
	 */

	public function apply()
	{
		$model = $this->getModel('image');
		$model->saveImages(JRequest::get());
		$tmpl	= JRequest::getVar('tmpl','');
		$ajax	= JRequest::getInt('ajax', 0);
		$tmpl	= ($tmpl!='')?'&tmpl='.$tmpl:'';
		// end of process update
		$app 			= JFactory::getApplication();
		$showListID		= JRequest::getVar('showlistID');
		if ($ajax)
		{
			echo json_encode(array('result' => 'success', 'message' => ''));
			exit();
		}
		else
		{
			$app->redirect('index.php?option=com_imageshow&controller=showlist&task=edit&cid[]=' . $showListID . $tmpl);
		}
	}

	public function PurgeAbsoleteImages()
	{

		$imageid 	= JRequest::getVar('ImageID');
		$showListID = JRequest::getVar('showListID');
		$model  = $this->getModel('image');
		$articleCate = $model->PurgeAbsoleteImages($showListID,$imageid);
		jexit();
	}

	public function resetImageDetails()
	{
		$images = JRequest::getVar('img_detail', '');
		if (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true)
		{
			$images = stripslashes($images);
		}
		$images	= json_decode($images);
		if (!is_null($images))
		{
			$model  					= $this->getModel('image');
			$images->image_title 		= urldecode($images->original_title);
			$images->image_description 	= $images->original_description;
			$images->image_link 		= urldecode($images->original_link);
			$images->custom_data 		= '0';
			unset($images->original_title);
			unset($images->original_description);
			unset($images->original_link);
			$model->updateImageInformation($images);
		}
		jexit();
	}
}
