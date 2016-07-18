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

include_once JPATH_ROOT . '/administrator/components/com_easyslider/classes/jsn.easyslider.render.php';

class modEasySliderHelper
{
    public static function render(&$params)
    {
        $objJSNEasySliderRender = new JSNEasySliderRender();
        $sliderID = $params->get('slider_id');
        if (is_numeric($sliderID))
        {
             echo $objJSNEasySliderRender->render($sliderID, true);

        }
    }
}