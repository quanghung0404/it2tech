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

abstract class EasyBlogBlockHandlerAbstract
{
    public $type;
    public $icon;
    public $title;
    public $keywords;
    public $table;
    public $nestable = false;
    public $visible = true;

    public function __construct(EasyBlogTableBlock &$block)
    {
        $this->type = $block->element;
        $type = JString::strtoupper($this->type);

        $this->title = JText::_('COM_EASYBLOG_BLOCKS_HANDLER_' . $type . '_TITLE');
        $this->keywords = JText::_('COM_EASYBLOG_BLOCKS_HANDLER_' . $type . '_KEYWORDS');
        $this->table = $block;
    }

    /**
     * Retrieves the icon
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Retrieves the description / help text for the current block
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getDescription()
    {
        return JText::_($this->table->description);
    }

    /**
     * Standard method to format the output for displaying purposes
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function formatDisplay($item, EasyBlogPost &$blog)
    {
        return $item->html;
    }

    /**
     * Standard meta data of a block object
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function meta()
    {
        $meta = new stdClass();

        // Standard descriptors
        $meta->type = $this->type;
        $meta->icon = $this->icon;
        $meta->title = $this->title;
        $meta->keywords = $this->keywords;
        $meta->data = $this->data();

        // Nestable
        $meta->nestable = $this->nestable;

        // Dimensions
        $meta->dimensions = new stdClass();
        $meta->dimensions->enabled = true;
        $meta->dimensions->respectMinContentSize = false;

        // Others
        $meta->properties = array(
            'fonts' => true,
            'textpanel' => true
        );

        $template = EB::template();
        $template->set('block', $this);
        $template->set('data', $meta->data);

        // HTML & Block
        $meta->html = $template->output('site/composer/blocks/handlers/' . $this->type . '/html');
        $meta->block = EB::blocks()->renderBlockContainer(EASYBLOG_BLOCK_MODE_EDITABLE, $this, $meta->html);

        // Fieldset & fieldgroup
        $meta->fieldset = $template->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
        $meta->fieldgroup = $template->output('site/composer/blocks/fieldgroup', array('fieldset' => $meta->fieldset));

        return $meta;
    }

    /**
     * Retrieves the output for the block when it is being displayed
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
        $content = $block->html;

        // strip html tags to precise length count.
        $content = strip_tags($content);

        // convert html entities back to it string. e.g. &nbsp; back to empty space
        $content = html_entity_decode(mb_convert_encoding(stripslashes($content), "HTML-ENTITIES", 'UTF-8'));

        // replace special characters from redactor.
        $content = str_replace('&#8203;', '', $content);

        // replace line feed
        $content = preg_replace('/[\n\r]/', '', $content);

        // remove any blank space.
        $content = trim($content);

        // get content length
        $contentLength = JString::strlen($content);
        if ($contentLength > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * use for acl checking on blocks. By default this method always return true. If a block needed acl checking,
     * the block will need to override this method in their handler.
     *
     * @since   5.0
     * @access  public
     * @param
     * @return boolean
     */
    public function canUse()
    {
        return true;
    }


    /**
     * Retrieves the output for the block when it is being edited
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getEditableHtml($block)
    {
        return isset($block->editableHtml) ? $block->editableHtml : '';
    }

    public function updateBlock($block, $data)
    {
        $block->html = '';
        return $block;
    }

    public abstract function data();
}
