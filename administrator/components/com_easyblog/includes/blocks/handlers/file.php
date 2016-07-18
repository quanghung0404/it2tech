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

class EasyBlogBlockHandlerFile extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fa fa-file';

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

        // Set the template for the video player
        $theme = EB::template();
        $meta->preview = $theme->output('site/composer/blocks/handlers/file/preview');

        return $meta;
    }

    public function data()
    {
        $data = new stdClass();

        $data->name = '';
        $data->type = '';
        $data->size = '';
        $data->url = '';

        //for fieldset
        $data->showicon = 1;
        $data->showsize = 1;

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
        if (!isset($block->data->url) || !$block->data->url) {
            return false;
        }

        return true;
    }


    // /**
    //  * Displays the html output for a file preview block
    //  *
    //  * @since   5.0
    //  * @access  public
    //  * @param   string
    //  * @return
    //  */
    // public function getHtml($block, $textOnly = false)
    // {
    //     if ($textOnly) {
    //         return;
    //     }

    //     // Ensure that we have the url of the video otherwise we wouldn't know how to display the video
    //     if (!isset($block->data->url) || !$block->data->url) {
    //         return;
    //     }

    //     $template = EB::template();
    //     $template->set('block', $block);
    //     $contents = $template->output('site/blogs/blocks/file');

    //     return $contents;
    // }
}
