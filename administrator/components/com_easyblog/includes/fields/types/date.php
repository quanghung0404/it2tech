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

class EasyBlogFieldsTypeDate extends EasyBlogFieldsAbstract
{
    public $title      = null;
    public $element    = 'date';

	public function __construct()
	{
        // Set the title of this field
        $this->title    = JText::_('COM_EASYBLOG_FIELDS_TYPE_DATE');

        parent::__construct();
	}

    /**
     * Renders the form options for the date field in the admin area
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function admin(EasyBlogTableField &$field)
    {
        $theme  = EB::template();

        // Get options
        $options = json_decode($field->options);

        // If there's no value, define a standard value
        if (empty($options)) {
            $option = new stdClass();
            $option->title = '';
            $option->value = '';

            $options    = array($option);
        }

        $theme->set('params', $field->getParams());
        $theme->set('options', $options);
        $theme->set('field', $field);

        $output = $theme->output('admin/fields/types/admin/date');

        return $output;
    }

    /**
     * Renders the front end form
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

        $theme  = EB::template();

        // Get options
        $options = json_decode($field->options);

        // If there's no value, define a standard value
        if (empty($options)) {
            $option = new stdClass();
            $option->title = '';
            $option->value = '';

            $options    = array($option);
        }

        $params = $field->getParams();

        $value = $params->get('default');

        if ($data && $data->value) {
            $value = $data->value;
        }

        $theme->set('value', $value);
        $theme->set('params', $params);
        $theme->set('formElement', $this->formElement);
        $theme->set('options', $options);
        $theme->set('field', $field);

        $output = $theme->output('admin/fields/types/form/date');

        return $output;
    }

    /**
     * Displays the date on the form
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function display(EasyBlogTableField &$field, EasyBlogPost &$post)
    {
       static $result = array();

       $config = EB::config();

       $dateFormat = $config->get('custom_field_date_format', JText::_('DATE_FORMAT_LC1'));

        $idx = $field->id . $post->id;

        if (!isset($result[$idx])) {
            $data = $this->getValue($field, $post);

            if (!$data->value) {
                $result = '';

                return $result;
            }

            $theme = EB::template();
            $theme->set('data', $data);
            $theme->set('dateFormat', $dateFormat);

            $result[$idx] = $theme->output('admin/fields/types/display/date');
        }

        return $result[$idx];
    }

    /**
     * return date values in plain text.
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
            $data = $this->getValue($field, $blog);
            $result[$idx] = strip_tags($data->value);
        }

        return $result[$idx];
    }
}
