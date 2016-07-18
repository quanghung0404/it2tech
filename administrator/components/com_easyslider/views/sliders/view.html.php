<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * View class for a list of sliders.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
include_once JPATH_COMPONENT_ADMINISTRATOR . '/classes/jsn.easyslider.sliders.php';

class JSNEasySliderViewSliders extends JSNBaseView
{
    protected $sliders;

    protected $pagination;

    protected $state;

    /**
     * Execute and display a template script.
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Exception object.
     */
    function display($tpl = null)
    {
        // Get config parameters
        $config = JSNConfigHelper::get();
        $session   	= JFactory::getSession();
        $objJSNEasySliderSliders = new JSNEasySliderSliders();
        $totalSliders = $objJSNEasySliderSliders->countSilderItems();
        $layout = $this->getLayout();
        $this->totalSliders = $totalSliders;
        /*Check if it is FREE edition then show warning message to alert that FREE edition only allows create maximum of 3 sliders*/
        $this->edition = defined('JSN_EASYSLIDER_EDITION') ? JSN_EASYSLIDER_EDITION : "free";
        if (strtolower($this->edition) == 'free')
        {
            if ($totalSliders !== false && $totalSliders >= 3)
            {
                if ($layout == 'default')
                {
                    JFactory::getApplication()->enqueueMessage(JText::_('JSN_EASYSLIDER_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SLIDERS_IN_FREE_EDITION'), 'warning');
                }
            }
        }

        if ($session->has('JSN_EASYSLIDER_BACKUP_OLD_DATA_PATH') && (string) $session->get('JSN_EASYSLIDER_BACKUP_OLD_DATA_PATH') != '')
        {
        	JFactory::getApplication()->enqueueMessage('The data backup file of version 1.x.x has been created from the update process. You can download it <a href="' . JUri::root() . str_replace(JPATH_ROOT . '/', '', (string) $session->get('JSN_EASYSLIDER_BACKUP_OLD_DATA_PATH')) . '"><strong>here<strong></a>', 'notice');
        }
        
	    // Set the toolbar
        JToolbarHelper::title(JText::_('JSN_EASYSLIDER_SLIDERS'));

        // Add toolbar menu
        JToolbarHelper::addNew('slider.add', 'JSN_EASYSLIDER_CREATE_NEW');
        JToolbarHelper::editList('slider.edit', 'JTOOLBAR_EDIT');
        JToolbarHelper::custom('sliders.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        JToolbarHelper::divider();
        JToolbarHelper::publish('sliders.publish');
        JToolbarHelper::unpublish('sliders.unpublish');
        JToolbarHelper::divider();
        JToolbarHelper::deleteList('JSN_EASYSLIDER_CONFIRM_DELETE', 'sliders.delete', 'JTOOLBAR_DELETE');
//        JToolbarHelper::preferences();
//        JToolbarHelper::help();
        JToolbarHelper::custom('', ' jsnes-import fa fa-download', ' jsnes-import fa fa-download', 'JSN_EASYSLIDER_IMPORT');
        JToolbarHelper::custom('', ' jsnes-export fa fa-upload', ' jsnes-export fa fa-upload', 'JSN_EASYSLIDER_EXPORT');
        JToolbarHelper::divider();
        JSNEasySliderHelper::addToolbarMenu();

        // Set the submenu
//        JSNEasySliderHelper::addSubmenu('sliders');

        // Get messages
        $msgs = '';

        if (!$config->get('disable_all_messages'))
        {
            $msgs = JSNUtilsMessage::getList('SLIDERS');
            $msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
        }
        
        $this->sliders = $objJSNEasySliderSliders->getSlidersWithoutState();

        // Assign variables for rendering
        $this->msgs = $msgs;
        $this->objJSNEasySliderSliders = $objJSNEasySliderSliders;

        // Add assets
        JSNEasySliderHelper::addAssets();
        $this->_addAssets();
        // Display the template
        parent::display($tpl);
    }

    public function renderBtnAddToModule($item)
    {
        return '<a class="btn btn-default" target="_blank" href="index.php?option=com_easyslider&task=launchAdapter&type=module&slider_id=' . $item->slider_id . '">' . JText::_('JSN_EASYSLIDER_BTN_ASSIGN_TO_MODULE') . ' </a>';
    }

    /**
     * Reder demo custom
     *
     * @param   type $slider sliderlist
     *
     * @return html code
     */
    public function renderCustom($slider)
    {
        return $slider->slider_title;
    }

    protected function _addAssets()
    {
        JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'css/sliders.css');
        
		JSNHtmlAsset::addScript(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/jquery/jquery.min.js');
		JSNHtmlAsset::addScript(JSNES_ASSETS_URL . 'js/sliders.js');
		JSNHtmlAsset::addScript(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/underscore/underscore-min.js');
		JSNHtmlAsset::addScript(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/backbone/backbone-min.js');
        JSNHtmlAsset::addScript(JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/backbone-model.js');
        JSNHtmlAsset::addScript(JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/backbone-view.js');
		JSNHtmlAsset::addScript(JSNES_PLG_SYSTEM_ASSETS_URL . 'js/conflict.js');


    }

}
