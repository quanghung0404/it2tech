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
jimport( 'joomla.application.component.view');

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS . '/elements/html');
class ImageShowViewShowcase extends JViewLegacy
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
		$objISUtils						= JSNISFactory::getObj('classes.jsn_is_utils');
		$objJSNTheme					= JSNISFactory::getObj('classes.jsn_is_themes');
		$this->_document				= JFactory::getDocument();

		$lists							= array();
		$format							= JRequest::getVar('view_format', 'temporary');
		$showlist_id					= JRequest::getInt('showlist_id');
		$showcaseTheme					= JRequest::getVar('theme', 'showcasethemeclassic');
		$model							= $this->getModel();
		$items							= $this->get('data');
		$session						= JFactory::getSession();
		$overallWidthDimensionValue		= '%';
		$showcaseThemeSession			= $session->get('showcaseThemeSession');
		$session->clear('showcaseThemeSession');

		// GENERAL TAB BEGIN
		if ($showcaseThemeSession)
		{
			$publishShowcase = $showcaseThemeSession['published'];
		}
		else if($items->published != '')
		{
			$publishShowcase = $items->published;
		}
		else
		{
			$publishShowcase = 1;
		}
		$lists['published'] = JHTML::_('jsnselect.booleanlist',  'published', '', $publishShowcase);

		$query				= 'SELECT ordering AS value, showcase_title AS text FROM #__imageshow_showcase ORDER BY ordering';
		$lists['ordering']	= JHtmlList::ordering('ordering', $query, '', $items->showcase_id);
		//$lists['ordering']	= JHTML::_('list.specificordering',  $items, $items->showcase_id, $query );

		$generalImagesOrder	= array(
			'0' => array('value' => 'forward',
			'text' => JText::_('SHOWCASE_GENERAL_FORWARD')),
			'1' => array('value' => 'backward',
			'text' => JText::_('SHOWCASE_GENERAL_BACKWARD')),
			'2' => array('value' => 'random',
			'text' => JText::_('SHOWCASE_GENERAL_RANDOM'))
		);

		$dimension = array(
			'0' => array('value' => 'px',
			'text' => JText::_('px')),
			'1' => array('value' => '%',
			'text' => JText::_('%'))
		);

		// GENERAL TAB END

		$generalData = array();

		if(!empty($showcaseThemeSession))
		{
			$generalData['generalTitle']	= $showcaseThemeSession['showcase_title'];
			$generalData['generalWidth']	= $showcaseThemeSession['general_overall_width'] . $showcaseThemeSession['overall_width_dimension'];
			$generalData['generalHeight']	= $showcaseThemeSession['general_overall_height'];
		}
		else if($items->general_overall_width)
		{
			$generalData['generalTitle']	= htmlspecialchars($items->showcase_title);
			$generalData['generalWidth']	= $items->general_overall_width;
			$generalData['generalHeight']	= $items->general_overall_height;
		}
		else
		{
			$generalData['generalTitle']	= '';
			$generalData['generalWidth']	= '100%';
			$generalData['generalHeight']	= '450';
		}

		$overallWith				= $generalData['generalWidth'];
		$posPercentageOverallWidth	= strpos($overallWith, '%');

		if ($posPercentageOverallWidth)
		{
			$overallWith				= substr($overallWith, 0, $posPercentageOverallWidth + 1);
			$overallWidthDimensionValue = "%";
		}
		else
		{
			$overallWith				= (int) $overallWith;
			$overallWidthDimensionValue = "px";
		}

		$lists['overallWidthDimension'] = JHTML::_('select.genericList', $dimension, 'overall_width_dimension', 'class="overall-width-dimension" style="width: 50px;" onchange="checkOverallWidth();" ' . '', 'value', 'text', $overallWidthDimensionValue );


		$remoteTheme 	 = $objJSNTheme->compareSources();
		$needInstallList = $objJSNTheme->getNeedInstallList($remoteTheme);
		$localTheme 	 = $objJSNTheme->compareLocalSources();
		$needUpdateList	 = $objJSNTheme->getNeedUpdateList($localTheme);
		$canAutoDownload = true;
		$objJSNUtils 	 = JSNISFactory::getObj('classes.jsn_is_utils');

		if (!$objJSNUtils->checkEnvironmentDownload())
		{
			$canAutoDownload = false;
		}

		$this->assignRef('canAutoDownload', $canAutoDownload);
		$this->assignRef('needUpdateList', $needUpdateList);
		$this->assignRef('needInstallList', $needInstallList);
		$this->assignRef('generalData', $generalData);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->_addAssets();
		$this->addToolbar();

		if (!$this->_checkAction())
		{
			JFactory::getApplication()->redirect('index.php?option=com_imageshow&controller=showcase', JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');
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
		JToolBarHelper::title(JText::_('JSN_IMAGESHOW') . ': ' . JText::_('SHOWCASE_SHOWCASE_SETTINGS'), 'showcase-settings');

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
		JHTML::_('behavior.modal', 'a.modal');
		JHTML::_('behavior.modal', 'a.jsn-modal');
		JHTML::_('behavior.tooltip');
		$objJSNMedia = JSNISFactory::getObj('classes.jsn_is_media');

		! class_exists('JSNBaseHelper') OR JSNBaseHelper::loadAssets();

		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/imageshow.css');
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/css/view.showcase.css');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/imageshow.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/installshowcasethemes.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/installdefault.js');

		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');

		$objJSNMedia->addScript(JUri::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/conflict.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-ck/jquery.ck.js');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-stickyfloat/stickyFloat.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/jquery.imageshow.js');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/joomlashine/slider.js');

		//$objJSNMedia->addStyleSheet();
		$objJSNMedia->addStyleSheet(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/jquery/colorpicker/css/colorpicker.css');
		$objJSNMedia->addScript(JURI::root(true) . '/administrator/components/com_imageshow/assets/js/jquery/colorpicker/js/colorpicker.js');

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
		$user 		= JFactory::getUser();
		$isNew 		= $this->items->showcase_id == 0;
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