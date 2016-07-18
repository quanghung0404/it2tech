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

class EasyBlogBlockHandlerVideo extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fa fa-film';

    public function meta()
    {
        static $meta;

        if (isset($meta)) {
            return $meta;
        }

        $meta = parent::meta();

        // We do not want to display the dimensions fieldset, font fieldset and text panel.
        $meta->dimensions->enabled = false;
        $meta->properties['fonts'] = false;
        $meta->properties['textpanel'] = false;

        // Set the template for the video player
        $template = EB::template();
        $meta->player = $template->output('site/composer/blocks/handlers/video/player');

        return $meta;
    }

    public function data()
    {
        $data = new stdClass();
        $data->url = '';
        $data->width = '100%';
        $data->height = '';
        $data->ratio = '16:9';
        $data->autoplay = false;
        $data->loop = false;
        $data->muted = false;

        return $data;
    }

    /**
     * We do not want to display anything
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getEditableHtml($block)
    {
        // If there's no url set on the block, we should just leave this to the parent to output the necessary data.
        if (!$block->data->url) {
            $meta = $this->meta();
            return $meta->html;
        }
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

    /**
     * Displays the html output for a video block
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getHtml($block, $textOnly = false)
    {
        if ($textOnly) {
            return;
        }

        // Ensure that we have the url of the video otherwise we wouldn't know how to display the video
        if (!isset($block->data->url) || !$block->data->url) {
            return;
        }

        $options = (array) $block->data;

        $output = EB::media()->renderVideoPlayer($block->data->url, $options);

        return $output;
    }
}
