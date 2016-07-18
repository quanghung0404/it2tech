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

class EasyBlogBlockHandlerImage extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fa fa-camera';

    public function meta() {

        static $meta;
        if (isset($meta)) {
            return $meta;
        }

        $meta = parent::meta();

        // Disable dimensions fieldset
        $meta->dimensions->enabled = false;
        $meta->properties['fonts'] = false;

        // Store image template
        $template = EB::template();
        $meta->imageContainer = $template->output('site/composer/blocks/handlers/image/container');
        $meta->imageCaption = $template->output('site/composer/blocks/handlers/image/caption');
        $meta->imagePopupButton = $template->output('site/composer/blocks/handlers/image/popup_button');
        $meta->imageHint = $template->output('site/composer/blocks/handlers/image/hint');

        return $meta;
    }

    public function data() {

        $data = new stdClass();

        // Source
        $data->isurl = false; // true if via url
        $data->url = '';
        $data->uri = '';
        $data->variation = '';

        // Size
        $data->size_enabled = true;
        $data->mode = 'simple'; // simple, advanced
        $data->mode_lock = false;
        $data->fluid = false;

        // Viewport width/height/ratio
        $data->width = '';
        $data->width_lock = false;
        $data->height = '';
        $data->height_lock = false;
        $data->ratio = '';
        $data->ratio_lock = true;
        $data->alignment = 'center';

        // Element width/height/ratio
        $data->strategy = 'fit'; // fill, fit, custom
        $data->element_width = '';
        $data->element_height = '';
        $data->element_top = '';
        $data->element_left = '';
        $data->element_ratio = '';
        $data->element_ratio_lock = true;

        // Natural width/height/ratio (used for reference)
        $data->natural_width = '';
        $data->natural_height = '';
        $data->natural_ratio = '';

        // Style
        $data->style_enabled = true;
        $data->style = 'clear';

        // Caption
        $data->caption_enabled = true;
        $data->caption_text = '';

        // Link
        $data->link_enabled = true;
        $data->link_url = '';
        $data->link_title = '';
        $data->link_target = '';

        // Popup
        $data->popup_enabled = true;
        $data->popup_url = '';
        $data->popup_uri = '';
        $data->popup_variation = '';

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

    /**
     * determine if current user can use this block or not in composer.
     *
     * @since   5.0
     * @access  public
     * @param
     * @return boolean
     */
    public function canUse()
    {
        $acl = EB::acl();
        return $acl->get('upload_image');
    }


    /**
     * Displays the output
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getHtml($block, $textOnly = false)
    {
        if ($textOnly) {
            return false;
        }

        if (!isset($block->data->url) || !$block->data->url) {
            return false;
        }

        $data = $block->data;

        // Get the default data
        $defaultData = $this->data();

        $data = (object) array_merge((array) $defaultData, (array) $data);

        $isFluid = $data->fluid;
        $isRootBlock = !$block->nested;

        // Classnaems
        $classnames = '';
        if ($data->fluid) {
            $classnames .= ' is-fluid';
        }

        if ($data->style) {
            $classnames .= ' style-' . $data->style;
        }

        // Style attributes
        $imageContainerStyle = array();
        $imageFigureStyle = array();
        $imageElementStyle = array();
        $imageCaptionStyle = array();

        // Root block
        if ($isRootBlock) {

            if ($isFluid) {

                $imageContainerStyle = array(
                    'width' => $data->width
                );

                $imageFigureStyle = array(
                    'width' => '100%',
                    'padding-top' => (1 / $this->ratioDecimal($data->ratio) * 100) . '%'
                );

            } else {
                $imageFigureStyle = array(
                    'width' => $data->width,
                    'height' => $data->height
                );
            }

            if ($data->alignment!=='center') {
                $imageContainerStyle['float'] = $data->alignment;
                $classnames .= ' align-' . $data->alignment;
            }

        // Nested block
        } else {

            if ($isFluid) {

                $imageFigureStyle = array(
                    'padding-top' => (1 / $this->ratioDecimal($data->ratio) * 100) . '%'
                );

            } else {
                $imageFigureStyle = array(
                    'width' => $data->width,
                    'height' => $data->height
                );
            }
        }

        $imageElementStyle = array(
            'width' => $data->element_width,
            'height' => $data->element_height,
            'top' => $data->element_top,
            'left' => $data->element_left
        );

        $imageCaptionStyle = array(
            'width' => $isFluid ? '100%' : $data->width
        );

        // Image link attr
        $imageLinkAttr = '';

        if (!empty($data->link_url)) {

            $imageLinkAttr .= ' href="' . $data->link_url . '"';

            if (!empty($data->link_title)) {
                $imageLinkAttr .= ' title="' . $data->link_title . '"';
            }

            if ($data->link_target) {
                $imageLinkAttr .= ' target="_blank"';
            }
        }

        $imageContainerAttr = $this->getStyle($imageContainerStyle);
        $imageElementAttr = $this->getStyle($imageElementStyle);
        $imageFigureAttr = $this->getStyle($imageFigureStyle);
        $imageCaptionAttr = $this->getStyle($imageCaptionStyle);

        $theme = EB::template();
        $theme->set('block', $block);
        $theme->set('classnames', $classnames);
        $theme->set('imageContainerAttr', $imageContainerAttr);
        $theme->set('imageElementAttr', $imageElementAttr);
        $theme->set('imageFigureAttr', $imageFigureAttr);
        $theme->set('imageCaptionAttr', $imageCaptionAttr);
        $theme->set('imageLinkAttr', $imageLinkAttr);

        $html = $theme->output('site/blogs/blocks/image');

        return $html;
    }

    /**
     * function to convert the image ratio to decimal so that we can get the padding value for display.
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    private function ratioDecimal($ratio) {

        if (strpos($ratio, ':') === false) {
            return $ratio;
        }

        $parts = explode(":", $ratio);
        return $parts[0] / $parts[1];
    }

    private function addCssUnit($num) {
        return (strpos($num, '%')===false) ?
                 (is_numeric($num) ? $num . 'px' : $num)
                 : $num;
    }

    private function getStyle($rules) {

        $css = '';

        foreach ($rules as $rule => $val) {
            if (empty($val)) continue;
            $css .= $rule . ':' . $this->addCssUnit($val) . ';';
        }

        if (empty($css)) return '';

        return ' style="' . $css . '"';
    }
}
