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

include_once JPATH_ROOT . '/administrator/components/com_easyslider/classes/jsn.easyslider.sliders.php';

class JFormFieldJsnsliderlist extends JFormField
{
    protected function getInput()
    {
        $definePath = JPATH_ROOT . '/administrator/components/com_easyslider/easyslider.defines.php';

        if (is_file($definePath))
        {
            include_once $definePath;
        }

        $_app = JFactory::getApplication('admin');
        $_input = $_app->input;

        $pathOnly = JURI::root(true);
        $app = JFactory::getApplication();

        $script  = '<script>var jQuery_bak = jQuery;</script>';
//        !class_exists('JSNBaseHelper') OR JSNBaseHelper::loadAssets();

        if (JVERSION * 100 >= 300)
        {
            JSNHtmlAsset::addStyle( JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');

            if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']))
            {
                JSNHtmlAsset::addStyle( JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery.ui.1.9.0.ie.css');
            }
        }
        else
        {
            JSNHtmlAsset::addStyle( JSN_URL_ASSETS . '/3rd-party/bootstrap/css/jsn.bootstrap.css');
            JSNHtmlAsset::addStyle( JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.8.16.custom.css');

            if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']))
            {
                JSNHtmlAsset::addStyle( JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.8.16.ie.css');
            }
        }

        JSNHtmlAsset::addStyle( JSN_URL_ASSETS . '/joomlashine/css/jsn-gui.css');


        JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'css/modal.css');
        JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'css/modules.css');
        $script .= '<script type="text/javascript" src="' . JUri::root(true) . '/media/jui/js/jquery.min.js"></script>';
        $script .= '<script type="text/javascript" src="' . JSNES_PLG_JSNFRAMEWORK_SYSTEM_ASSETS_URL . '3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js"></script>';
        $script .= '<script type="text/javascript" src="' . JSNES_ASSETS_URL . 'js/libs/modal.js"></script>';
        $script .= '<script type="text/javascript" src="' . JSNES_ASSETS_URL . 'js/modules.js"></script>';
        $script .= '<script type="text/javascript" src="' . $pathOnly . '/media/jui/js/jquery-noconflict.js"></script>';

        $script .=  '<script>jQuery =  jQuery_bak;</script>';
        $objJSNEasySliderSliders = new JSNEasySliderSliders();
        $sliderID = 0;
        if (!is_null($id = $_input->get->get('id')))
        {
            $data = $objJSNEasySliderSliders->getSliderModule($id);
            if ($data)
            {
                $data = json_decode($data);
                $sliderID = $data->slider_id;
            }
        }
        else
        {
            $sliderID = $app->getUserState('com_easyslider.add.slider_id');
        }

        //build the list of slider
        $html = $script.'<div id="jsn-slider-icon-warning">';

        $sliders = $objJSNEasySliderSliders->getSliders();
        $totalSliders = $objJSNEasySliderSliders->countSilderItems();

        $default = new stdClass();

        $default->slider_id     = 0;
        $default->id            = 0;
        $default->published     = 0;
        $default->ordering      = 0;
        $default->access        = 1;
        $default->slider_title  = '-- select slider --';
        $default->text          = '-- select slider --';
        array_unshift( $sliders, $default);
        $html .= JHTML::_('select.genericList', $sliders, $this->name, 'class="inputbox jsn-select-value"', 'id', 'text', $sliderID, $this->id);

        if (count($sliders))
        {
            $html .= '<a href="javascript:void(0);" id="edit-slider" class="disabled"><i class="icon-pencil"></i></a>';
        }
        else
        {
            $html .= '<a href="javascript:void(0);" class="disabled"><i class="icon-pencil"></i></a>';
        }




        /*Check if it is FREE edition then show warning message to alert that FREE edition only allows create maximum of 3 sliders*/
        $edition = defined('JSN_EASYSLIDER_EDITION') ? JSN_EASYSLIDER_EDITION : "free";
        if (strtolower($edition) == 'free' && $totalSliders !== false && $totalSliders >= 3)
        {

            $html .= '<a href="javascript:void(0);" onclick="alert(\'' . JText::_('JSN_EASYSLIDER_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SLIDERS_IN_FREE_EDITION', true) . '\')" id="new-slider"><i class="icon-plus"></i></a>';
        }
        else
        {
            $html .= '<a href="index.php?option=com_easyslider&view=slider&layout=edit" id="new-slider"><i class="icon-plus"></i></a>';
        }
        $html .= '</div>';
        return $html;
    }
}

?>