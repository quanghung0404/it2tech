<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogThemesHelperDashboard
{
    public static function id($element, $value)
    {
        $theme  = EB::template();

        $theme->set('element', $element);
        $theme->set('value', $value);
        
        $output = $theme->output('site/dashboard/html/form.id');

        return $output;
    }

    public static function action($title, $action, $type = 'dialog')
    {
        $title  = JText::_($title);
        $theme  = EB::template();

        $theme->set('type', $type);
        $theme->set('title', $title);
        $theme->set('action', $action);
        
        $output = $theme->output('site/dashboard/html/item.action');

        return $output;
    }

    public static function checkall()
    {
        $theme  = EB::template();
        
        $output = $theme->output('site/dashboard/html/table.checkall');

        return $output;        
    }
}
