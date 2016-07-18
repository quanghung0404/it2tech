<?php
/**
 * @version    $Id: view.html.php 16943 2012-10-12 05:00:19Z giangnd $
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
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . '/elements/html');

/**
 * About view of JSN ImageShow component
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class ImageShowViewShowlist extends JViewLegacy
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
		$images 			= array();
		$catid  			= 0;
		$tmpjs 				= '';
		$albumID 			= '';
		$lists 				= array();
		$countImage 		= 0;
		$canAutoDownload 	= true;

		$this->_document	= JFactory::getDocument();
		$objJSNUtils 	 	= JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNImages 		= JSNISFactory::getObj('classes.jsn_is_images');
		$objJSNJSLanguages 	= JSNISFactory::getObj('classes.jsn_is_jslanguages');
		$objImages 	 		= JSNISFactory::getObj('classes.jsn_is_images');
		$objJSNMsg 			= JSNISFactory::getObj('classes.jsn_is_message');

		$model  = $this->getModel();
		$items 	= $this->get('data');

		// Get messages
		$msgs = '';
		$msgs = $objJSNMsg->getList('SHOWLISTS');
		$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';

		if (isset($items->image_source_name) && $items->image_source_name != '')
		{
			$imageSource = JSNISFactory::getSource($items->image_source_name, $items->image_source_type, $items->showlist_id);
			$cat 		 = $objImages->getAllCatShowlist($items->showlist_id);

			if (!empty($cat))
			{
				$catid		 = $cat[0];
				$config		 = array('album'=>$catid);
				$sync 		 = $imageSource->getShowlistMode();

				if ($sync == 'sync')
				{
					$images = $imageSource->loadImages($config);
				}
				else
				{
					$images = $imageSource->loadImages($config);
				}
			}

			if ($imageSource->getShowlistMode() == 'sync')
			{
				$rmcat = 'JSNISImageGrid.removecatSelected();';
			}
			else
			{
				$rmcat = '';
			}

			$totalimage = count($images);

			if ($totalimage)
			{
				$imageInfo 	= (array) @$images->images[0];
				$albumID 	= @$imageInfo['album_extid'];
			}

			$jscode = "
				var JSNISImageGrid;
				var initImageGrid = false;
				var baseUrl = '" . JURI::root() . "';
				var VERSION_EDITION_NOTICE = \"" . JText::_('VERSION_EDITION_NOTICE') . "\";
			(function($){
				function jsnisOpenTree(child)
				{
					var parent = child.parent().parent();

					if (parent.attr('id') == 'jsn-jtree-categories' || parent.attr('id') == undefined) return;

					if (parent.hasClass('secondchild'))
					{
						parent.parent().parent().removeClass('jsn-jtree-close').addClass('jsn-jtree-open');
						parent.removeClass('jsn-jtree-close').addClass('jsn-jtree-open');
						parent.find('>ul').css('display','block');

					}
					else
					{
						parent.removeClass('jsn-jtree-close').addClass('jsn-jtree-open');
						parent.find('>ul').css('display','block');
						jsnisOpenTree(parent);
					}
				}

				function jsnisReshowTree(tree)
				{
					tree.children('li').each(function(){

						if ($(this).children('ul').length)
						{
							if ($(this).hasClass('catselected'))
							{
								jsnisOpenTree($(this));
							}

							var treeChild = $(this).children('ul');
							jsnisReshowTree(treeChild);
						}
						else
						{
							if ($(this).hasClass('catselected'))
							{
								jsnisOpenTree($(this));
							}
						}
					});

				}
				$('#dialogbox:ui-dialog').dialog('destroy');
				$('#dialogbox2:ui-dialog').dialog('destroy');
				$(document).ready(function ()
				{
					$('#jsn_is_showlist_tabs').tabs({
						activate: function(event, ui)
						{
							if(ui.newPanel.attr('id') == 'tab-showlist-images' && !initImageGrid)
							{
								JSNISImageGrid = $.JSNISImageGridGetInstaces({
									showListID   : '" . $items->showlist_id . "',
									sourceName   : '" . $items->image_source_name . "',
									sourceType   : '" . $items->image_source_type . "',
									selectMode   : '" . $imageSource->getShowlistMode() . "',
									pagination	 : '" . $imageSource->_source['sourceDefine']->pagination . "',
									layoutHeight : 500,
									layoutWidth  : '100%'
								});
								" . $rmcat . "
								" . $tmpjs . "
								JSNISImageGrid.initialize();
								if(!$('.media-item').length && !$('.jtree-selected', $('#images')).length)
								{
									JSNISImageGrid.cookie.set('rate_of_west', 58);
									JSNISImageGrid.UILayout.sizePane('west', '58%');
								}

								$('#jsn-jtree-categories').children('ul').each(function(){
									$(this).children('li').each(function(){
										$(this).children('ul').each(function(){
											$(this).children('li').each(function(){
												$(this).find('ul').css('display','none');
											});
										});
									});
								});

								$('#jsn-jtree-categories').children('ul').each(function(){
									jsnisReshowTree($(this));
								});

								initImageGrid = true;
								JSNISImageGrid.overrideSaveEvent();
								JSNISImageShow.getScriptCheckThumb(" . $items->showlist_id . ");
								$(window).trigger('resize');
							}

						}
					});
				});
			})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);";

			$this->_document->addScriptDeclaration($objJSNJSLanguages->loadLang());
			$this->_document->addScriptDeclaration($jscode);
			$showlistMode = $imageSource->getShowlistMode();
			$this->assignRef('selectMode', $showlistMode);
		}
		else
		{
			$jscode = "var baseUrl = '" . JURI::root() . "'";
			$this->_document->addScriptDeclaration($jscode);
		}

		if ($items->showlist_id && $items->showlist_id != '')
		{
			if ($objJSNImages->checkImageLimition($items->showlist_id))
			{
				$msg = JText::_('SHOWLIST_YOU_HAVE_REACHED_THE_LIMITATION_OF_10_IMAGES_IN_FREE_EDITION');
				JError::raiseNotice(100, $msg);
			}

			$countImage = $objJSNImages->countImagesShowList($items->showlist_id);
			$countImage = $countImage[0];
		}

		$authorizationCombo = array(
				'0' => array('value' => '0',
						'text' => JText::_('SHOWLIST_NO_MESSAGE')),
				'1' => array('value' => '1',
						'text' => JText::_('SHOWLIST_JOOMLA_ARTICLE'))
		);

		$imagesLoadingOrder= array(
				'0' => array('value' => 'forward',
						'text' => JText::_('SHOWLIST_GENERAL_FORWARD')),
				'1' => array('value' => 'backward',
						'text' => JText::_('SHOWLIST_GENERAL_BACKWARD')),
				'2' => array('value' => 'random',
						'text' => JText::_('SHOWLIST_GENERAL_RANDOM'))
		);

		$showExifData= array(
				'0' => array('value' => 'no',
						'text' => JText::_('SHOWLIST_SHOW_EXIF_DATA_NO')),
				'1' => array('value' => 'title',
						'text' => JText::_('SHOWLIST_SHOW_EXIF_DATA_TITLE')),
				'2' => array('value' => 'description',
						'text' => JText::_('SHOWLIST_SHOW_EXIF_DATA_DESCRIPTION'))
		);

		$lists['imagesLoadingOrder'] 	= JHTML::_('select.genericList', $imagesLoadingOrder, 'image_loading_order', 'class="inputbox" ' . '', 'value', 'text', $items->image_loading_order);
		$lists['showExifData'] 			= JHTML::_('select.genericList', $showExifData, 'show_exif_data', 'class="inputbox" ' . '', 'value', 'text', $items->show_exif_data);
		$lists['authorizationCombo'] 	= JHTML::_('select.genericList', $authorizationCombo, 'authorization_status', 'class="inputbox" onchange="JSNISImageShow.ShowListCheckAuthorizationContent();"' . '', 'value', 'text', $items->authorization_status);
		$lists['published'] 			= JHTML::_('jsnselect.booleanlist','published', '', ($items->published !='') ? $items->published : 1);
		$lists['overrideTitle'] 		= JHTML::_('jsnselect.booleanlist','override_title', '', $items->override_title);
		$lists['overrideDesc'] 			= JHTML::_('jsnselect.booleanlist','override_description', '', $items->override_description);
		$lists['overrideLink'] 			= JHTML::_('jsnselect.booleanlist','override_link', '', $items->override_link);

		$query = 'SELECT ordering AS value, showlist_title AS text' . ' FROM #__imageshow_showlist'	. ' ORDER BY ordering';
		$lists['ordering'] = JHtmlList::ordering('ordering', $query, '', $items->showlist_id);

		if (!$objJSNUtils->checkEnvironmentDownload())
		{
			$canAutoDownload = false;
		}

		$image_model  		= $this->getModel();
		$categories 		= $model->getTreeMenu();
		$articlesCatgories 	= $model->getTreeArticle();
		$this->assign('categories', $categories);
		$this->assign('articles_catgories', $articlesCatgories);
		$this->assignRef('canAutoDownload', $canAutoDownload);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('imageSource', $imageSource);
		$this->assignRef('countImage', $countImage);
		$this->assignRef('images', $images);
		$this->assignRef('catSelected', $catid);
		$this->assignRef('albumID', $albumID);
		$this->assignRef('totalImage', $totalimage);
		$this->assignRef('msgs', $msgs);
		$this->_addAssets();
		$this->addToolbar();

		if (!$this->_checkAction())
		{
			JFactory::getApplication()->redirect('index.php?option=com_imageshow&controller=showlist', JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');
			return false;
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */

	protected function addToolbar()
	{
		jimport('joomla.html.toolbar');
		$canDo 		= JSNISImageShowHelper::getActions();
		JToolBarHelper::title(JText::_('JSN_IMAGESHOW') . ': ' . JText::_('SHOWLIST_SHOWLIST_SETTINGS'), 'showlist-settings');
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::apply();
		}
		JToolBarHelper::save();
		JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		JToolBarHelper::divider();

		// Add toolbar menu
		JSNISImageShowHelper::addToolbarMenu();
	}

	/**
	 * Add nesscessary JS & CSS files
	 *
	 * @return void
	 */

	private function _addAssets()
	{
		JHTML::_('behavior.tooltip');
		$objJSNMedia = JSNISFactory::getObj('classes.jsn_is_media');

		! class_exists('JSNBaseHelper') OR JSNBaseHelper::loadAssets();

		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/imageshow.css');
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/showlist.css');
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/image_selector.css');

		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/imageshow.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/installimagesources.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/installdefault.js');

		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/conflict.js');

		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/jquery/jquery.contextmenu.r2.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/jquery/jquery.overridden.layout-latest.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/window.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/jquery.imageshow.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/slider.js');

		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ck/jquery.ck.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-topzindex/jquery.topzindex.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');

		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/lang.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/imagegrid.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/tree.js');


		JSNHtmlAsset::loadScript('imageshow/joomlashine/showlist', array(
				'pathRoot' => JURI::root(),
				'language' => JSNUtilsLanguage::getTranslated(array(
						'JSN_IMAGESHOW_OK',
						'JSN_IMAGESHOW_CLOSE',
						'JSN_IMAGESHOW_SAVE',
						'JSN_IMAGESHOW_CANCEL'
						))
		));

	}

	private function _checkAction()
	{
		$user 	= JFactory::getUser();
		$app 	= JFactory::getApplication();
		$input	= $app->input;
		$showlistID = $input->getInt('showlist_id', 0);

		if ($this->items->showlist_id == 0)
		{
			$isNew = true;
		}
		else
		{
			$isNew = false;
		}

		if ($showlistID)
		{
			$isNew = false;
		}

		$canDo 		= JSNISImageShowHelper::getActions();

		if ($isNew)
		{
			if (!$canDo->get('core.create'))
			{
				return false;
			}
		}
		else
		{
			if (!$canDo->get('core.edit'))
			{
				return false;
			}
		}

		return true;
	}
}
