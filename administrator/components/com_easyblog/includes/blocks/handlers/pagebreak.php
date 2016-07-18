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

class EasyBlogBlockHandlerPagebreak extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fa fa-files-o';
    public $nestable = false;

    public function meta()
    {
        static $meta;

        if (isset($meta)) {
            return $meta;
        }

        $meta = parent::meta();

        // We do not want to display the font attributes and font styles
        $meta->properties['fonts'] = false;
        $meta->properties['textpanel'] = false;

        return $meta;
    }

    /**
     * Validates if the block contains any contents
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function validate($block)
    {
        //pagebreak is not consider a content. always return false.
        return false;
    }

    /**
     * Renders the output of the block in viewing mode
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getHtml($block, $textOnly = false)
    {
        return $block->html;
    }

    public function data()
    {

        $data = new stdClass();

        // The url to the video
        $data->title = '';
        $data->alt = '';

        return $data;
    }
}
