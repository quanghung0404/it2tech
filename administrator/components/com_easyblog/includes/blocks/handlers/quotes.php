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

require_once(dirname(__FILE__) . '/abstract.php');

class EasyBlogBlockHandlerQuotes extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fa fa-quote-left';

    public function meta()
    {
        static $meta;

        if (isset($meta)) {
            return $meta;
        }

        $meta = parent::meta();
        // You can further add your own meta here

        return $meta;
    }

    public function data()
    {

        $data = (object) array(
            // It could also come from $config->get();
            // Which means user can configure default data
            // in the backend.
            'style' => 'style-default',
            'citation' => 1
        );

        return $data;
    }
}
