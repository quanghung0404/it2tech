<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/abstract.php');

class EasyBlogBlockHandlerHeading extends EasyBlogBlockHandlerAbstract
{
    public $icon     = 'fa fa-header';

    public function meta()
    {

        static $meta;
        if (isset($meta)) return $meta;

        $meta = parent::meta();

        return $meta;
    }

    public function data() {
        $data = (object) array(
            'level' => 'H2'
        );
        return $data;
    }
}
