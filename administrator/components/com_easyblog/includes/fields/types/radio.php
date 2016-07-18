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

class EasyBlogFieldsTypeRadio extends EasyBlogFieldsAbstract
{
    public $title      = null;
    public $element    = 'radio';

	public function __construct()
	{
        // Set the title of this field
        $this->title    = JText::_('COM_EASYBLOG_FIELDS_TYPE_RADIO');

        parent::__construct();
	}

    public function admin(EasyBlogTableField &$field)
    {
        $theme  = EB::template();

        // Get options
        $options = json_decode($field->options);

        // If there's no value, define a standard value
        if (empty($options)) {
            $option     = new stdClass();
            $option->title = '';
            $option->value = '';

            $options    = array($option);
        }

        $theme->set('element', $this->element);
        $theme->set('options', $options);
        $theme->set('field', $field);

        $output = $theme->output('admin/fields/types/admin/options');

        return $output;
    }

    /**
     * Renders the radio form in the composer
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function form(EasyBlogPost $post, EasyBlogTableField &$field)
    {
        // Retrieve the data for this pot
        $item = $this->getValue($field, $post);

        $value = '';
        if ($item) {
            $value = $item->value;
        }
        // Get multiple select options
        $theme  = EB::template();

        // Get the options
        $options = $this->getOptions($field);

        // Get the params
        $params = $field->getParams();

        $theme->set('value', $value);
        $theme->set('formElement', $this->formElement);
        $theme->set('params', $params);
        $theme->set('element', $this->element);
        $theme->set('options', $options);
        $theme->set('field', $field);

        $output = $theme->output('admin/fields/types/form/radio');

        return $output;
    }

    /**
     * Renders the selected value in the post
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function display(EasyBlogTableField &$field, EasyBlogPost &$blog)
    {
        $item = $this->getValue($field, $blog);

        if (!$item) {
            return;
        }

        // now we need to get the title for the selected value.
        $options = json_decode($field->options);

        if ($options) {
            foreach ($options as $option) {
                if ($option->value == $item->value) {
                    $item->title = $option->title;
                    break;
                }
            }
        }

        return isset($item->title) ? strip_tags($item->title) : strip_tags($item->value);
    }

    /**
     * return radio values in plain text.
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function text(EasyBlogTableField &$field, EasyBlogPost &$blog)
    {
        static $result = array();

        $idx = $field->id . $blog->id;

        if (! isset($result[$idx])) {
            $item = $this->getValue($field, $blog);

            if (!$item) {
                $result[$idx] = '';
                return;
            }

            // now we need to get the title for the selected value.
            $options = json_decode($field->options);

            if ($options) {
                foreach ($options as $option) {
                    if ($option->value == $item->value) {
                         $result[$idx] = strip_tags($option->title);
                        break;
                    }
                }
            } else {
                 $result[$idx] = strip_tags($item->value);

            }
        }

        return $result[$idx];
    }

}
