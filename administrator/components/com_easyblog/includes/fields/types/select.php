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

class EasyBlogFieldsTypeSelect extends EasyBlogFieldsAbstract
{
    public $title      = null;
    public $element    = 'select';

	public function __construct()
	{
        // Set the title of this field
        $this->title    = JText::_('COM_EASYBLOG_FIELDS_TYPE_SELECT');

        parent::__construct();
	}

    public function admin(EasyBlogTableField &$field)
    {
        // Get multiple select options
        $theme  = EB::template();

        // Get the field params
        $params = $field->getParams();

        $theme->set('params', $params);
        $output = $theme->output('admin/fields/types/admin/select');


        $theme  = EB::template();

        // Get the options
        $options = $this->getOptions($field);

        $theme->set('formElement', $this->formElement);
        $theme->set('element', $this->element);
        $theme->set('options', $options);
        $theme->set('field', $field);

        $output .= $theme->output('admin/fields/types/admin/options');

        return $output;
    }

    /**
     * Renders the select form in the composer
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function form(EasyBlogPost $post, EasyBlogTableField &$field)
    {
        // Retrieve the data for this pot
        $data = $this->getValue($field, $post);
        $selected = array();

        if ($data) {
            if (!is_array($data)) {
                $selected[] = $data->value;
            } else {
                foreach ($data as $row) {
                    $selected[] = $row->value;
                }
            }
        }

        // Get multiple select options
        $theme  = EB::template();

        // Get the options
        $options = $this->getOptions($field);

        // Get the params
        $params = $field->getParams();

        $theme->set('selected', $selected);
        $theme->set('formElement', $this->formElement);
        $theme->set('params', $params);
        $theme->set('element', $this->element);
        $theme->set('options', $options);
        $theme->set('field', $field);

        $output = $theme->output('admin/fields/types/form/select');

        return $output;
    }

    /**
     * Renders the output of the selected values
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function display(EasyBlogTableField &$field, EasyBlogPost &$blog)
    {
        static $result = array();

        $idx = $field->id . $blog->id;

        if (!isset($result[$idx])) {
            $items = $this->getValue($field, $blog);

            if (!$items) {
                $result[$idx] = '';
                return $result[$idx];
            }

            if (!is_array($items)) {
                $items = array($items);
            }

            // now we need to get the title for the selected value.
            $options = json_decode($field->options);

            if ($options) {
                for ($i = 0; $i < count($items); $i++) {
                    $item =& $items[$i];

                    foreach ($options as $option) {
                        if ($option->value == $item->value) {
                            $item->title = $option->title;
                            break;
                        }
                    }
                }
            }


            $params = $field->getParams();

            $theme = EB::template();
            $theme->set('items', $items);
            $theme->set('params', $params);

            $result[$idx] = $theme->output('admin/fields/types/display/select');
        }

        return $result[$idx];
    }

    /**
     * return dropdown values in plain text.
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
            $items = $this->getValue($field, $blog);

            if (!$items) {
                $result[$idx] = '';
                return;
            }

            if (!is_array($items)) {
                $items = array($items);
            }

            $tmp = array();

            // now we need to get the title for the selected value.
            $options = json_decode($field->options);

            if ($options) {
                foreach($items as $item) {
                    foreach ($options as $option) {
                        if ($option->value == $item->value) {
                            $tmp[] = strip_tags($option->title);
                            break;
                        }
                    }
                }
            } else {
                foreach($items as $item) {
                    $tmp[] = strip_tags($item[$i]->value);
                }
            }

            $result[$idx] = implode(' ', $tmp);
        }

        return $result[$idx];
    }


}
