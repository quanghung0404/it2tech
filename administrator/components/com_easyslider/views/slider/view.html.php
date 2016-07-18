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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Register include path for loading HTML element renderer class
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/elements/html');
/**
 * Update view.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
include_once JPATH_COMPONENT_ADMINISTRATOR . '/classes/jsn.easyslider.sliders.php';

class JSNEasySliderViewSlider extends JSNUpdateView
{
    protected $state;
    protected $slider;
    protected $form;

    /**
     * Method for display page.
     *
     * @param   boolean $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Exception object.
     */
    function display($tpl = null)
    {

        try
        {
            $this->state = $this->get('State');
            $this->slider = $this->get('Item');
            $this->form = $this->get('Form');
        } catch (Exception $e)
        {
            throw $e;
        }

        $config = JSNConfigHelper::get();
        // Get input object
        $input = JFactory::getApplication()->input;

        if (empty($this->slider->slider_id))
        {
            $objJSNEasySliderSliders = new JSNEasySliderSliders();
            $totalSliders = $objJSNEasySliderSliders->countSilderItems();

            /*Check if it is FREE edition then show warning message to alert that FREE edition only allows create maximum of 3 sliders*/
            $edition = defined('JSN_EASYSLIDER_EDITION') ? JSN_EASYSLIDER_EDITION : "free";
            if (strtolower($edition) == 'free')
            {
                if ($totalSliders !== false && $totalSliders >= 3)
                {
                    JFactory::getApplication()->redirect('index.php?option=com_easyslider&view=sliders');
                    return false;
                }
            }

        }

        // Setup toolbar
        $input->set('hidemainmenu', true);

        // Get messages
        $msgs = '';

        if (!$config->get('disable_all_messages'))
        {
            $msgs = JSNUtilsMessage::getList('SLIDER');
            $msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
        }

        // Assign variables for rendering
        $this->assignRef('msgs', $msgs);

        // Set the toolbar
        JToolBarHelper::title(JText::_('JSN_EASYSLIDER_EDIT_PRODUCT'));

        // Add assets
        JSNEasySliderHelper::addAssets();

        // Display the template
        parent::display($tpl);
    }
}
