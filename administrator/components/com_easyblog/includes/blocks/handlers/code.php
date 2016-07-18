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

class EasyBlogBlockHandlerCode extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fa fa-terminal';

    public function meta()
    {
        static $meta;

        if (isset($meta)) {
            return $meta;
        }

        $meta = parent::meta();

        // We do not want to display the font attributes and font styles
        $meta->properties['fonts'] = false;

        return $meta;
    }

    /**
     * Supplies the default data to the js part
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function data()
    {
        $data = new stdClass();

        $data->mode = 'html';
        $data->theme = 'ace/theme/github';
        $data->show_gutter = true;
        $data->fontsize = 12;

        return $data;
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
        // if no url specified, return false.
        if (!isset($block->data->code) || !$block->data->code) {
            return false;
        }

        return true;
    }

    public function normalizeData(&$data)
    {
        $data->code = str_ireplace('<html>', '&lt;html&gt;', $data->code);
        
        return $data;
    }

    /**
     * Standard method to format the output for displaying purposes
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getHtml($block, $textOnly = false)
    {
        // If configured to display text only, nothing should appear at all for this block.
        if ($textOnly) {
            return;
        }

        $uid = uniqid();

        // If there's no codes, skip this
        if (!isset($block->data->code) || !$block->data->code) {
            return;
        }

        // Initialize default attributes
        if (!isset($block->data->show_gutter)) {
            $block->data->show_gutter = true;
        }

        if (!isset($block->data->read_only)) {
            $block->data->read_only = false;
        }

        if (!isset($block->data->fontsize)) {
            $block->data->fontsize = 12;
        }

        $theme = EB::template();
        $theme->set('data', $block->data);
        $theme->set('uid', $uid);
        $theme->set('html', $block->html);

        $contents = $theme->output('site/blogs/blocks/code');

        return $contents;
    }

}
