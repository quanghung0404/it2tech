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

class EasyBlogThemesHelperForm
{
    public static function calendar($name, $value = '', $format = '%Y-%m-%d %H:%M:%S')
    {
        $theme = EB::template();

        $date  = EB::date($value);
        $value = $date->format($format);

        // Generate a uniqid
        $hash = strtolower($name);

        $theme->set('hash', $hash);
        $theme->set('name', $name);
        $theme->set('value', $value);
        $theme->set('format', $format);

        $output = $theme->output('admin/html/form.calendar');

        return $output;
    }

    /**
     * Retrieves the dropdown list for editors on the site
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public static function editors($element, $selected = null, $composer = false)
    {
        $db = EB::db();

        $query = array();
        $query[] = 'SELECT ' . $db->qn('element') . ' AS ' . $db->qn('value') . ',' . $db->qn('name') . ' AS ' . $db->qn('text');
        $query[] = 'FROM ' . $db->qn('#__extensions');
        $query[] = 'WHERE ' . $db->qn('folder') . '=' . $db->Quote('editors');
        $query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('plugin');
        $query[] = 'AND ' . $db->qn('enabled') . '=' . $db->Quote(1);
        $query[] = 'ORDER BY ' . $db->qn('ordering') . ',' . $db->qn('name');

        $query = implode(' ', $query);

        $db->setQuery($query);
        $editors = $db->loadObjectList();

        $lang = JFactory::getLanguage();
        
        for ($i = 0; $i < count($editors); $i++) {
            $editor = $editors[$i];
            $lang->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
            $editor->text = JText::_($editor->text);
        }

        $theme = EB::template();
        $theme->set('composer', $composer);
        $theme->set('element', $element);
        $theme->set('selected', $selected);
        $theme->set('editors', $editors);
        $output = $theme->output('admin/html/form.editors');

        return $output;
    }

    /**
     * Generates a html code for category selection.
     *
     * @access  public
     * @param   int     $parentId   if this option spcified, it will list the parent and all its childs categories.
     * @param   int     $userId     if this option specified, it only return categories created by this userId
     * @param   string  $outType    The output type. Currently supported links and drop down selection
     * @param   string  $eleName    The element name of this populated categeries provided the outType os dropdown selection.
     * @param   string  $default    The default selected value. If given, it used at dropdown selection (auto select)
     * @param   boolean $isWrite    Determine whether the categories list used in write new page or not.
     * @param   boolean $isPublishedOnly    If this option is true, only published categories will fetched.
     * @param   array   $exclusion  A list of excluded categories that it should not be including
     */

    public static function category($element, $id = '', $selected = '', $attributes = '', $parentId = null, $exclusion = array())
    {
        // Get the model
        $model = EB::model('Category');

        // Default to filter all categories
        $filter = 'all';

        if (!is_null($parentId)) {
            $filter = 'category';
        }

        // Get list of parent categories
        $categories = $model->getParentCategories($parentId, $filter, true, true, $exclusion);

        // Perform recursive operation to get the child items
        if (!empty($categories)) {

            foreach ($categories as $category) {

                $category->childs = null;

                self::buildNestedCategories($category);
            }
        }

        // Get the selected category
        $selected = $selected ? $selected : $model->getDefaultCategoryId();

        // Build the nested output for the category items
        $output = '';

        foreach ($categories as $category) {

            $selectedOutput = $selected == $category->id ? ' selected="selected"' : '';
            $output .= '<option value="' . $category->id . '"' . $selectedOutput . '>' . JText::_($category->title) . '</option>';

            self::generateNestedCategoriesOutput($category, $output, $selected);
        }

        // Now we need to build the select output
        $theme = EB::template();
        $theme->set('element', $element);
        $theme->set('attributes', $attributes);
        $theme->set('id', '');
        $theme->set('output', $output);

        $html = $theme->output('admin/html/form.category.select');

        return $html;
    }


    public static function buildNestedCategories($category, $exclusion = array(), $writeOnly = true, $publishedOnly = true)
    {
        $model = EB::model('Category');
        $categories = $model->getChildCategories($category->id, $publishedOnly, $writeOnly, $exclusion);

        // Get accessible categories
        $accessibleCategories = EB::getAccessibleCategories($category->id);

        if (!empty($categories)) {

            foreach ($categories as &$row) {

                $row->childs = null;

                if (!self::buildNestedCategories($row)) {
                    $category->childs[] = $row;
                }
            }
        }

        return false;
    }

    /**
     * Generates a list of category options in a select list
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public static function generateNestedCategoriesOutput($category, &$html, $selected = 0, $depth = 0)
    {
        if (!is_array($category->childs)) {
            return false;
        }

        // Increment the depth
        $depth++;

        $prefix = '';

        for ($i = 0; $i < $depth; $i++) {
            $prefix .= '&nbsp;&nbsp;&nbsp;';
        }

        foreach ($category->childs as $child) {
            $selectedOutput = $selected == $child->id ? ' selected="selected"' : '';
            $html .= '<option value="' . $child->id . '"' . $selectedOutput . '>' . $prefix . '<sup>|_</sup>' . JText::_($child->title) . '</option>';

            // Try to build the nested items
            self::generateNestedCategoriesOutput($child, $html, $selected, $depth);
        }
    }

	public static function action($task = '')
	{
		$theme 	= EB::template();

		$token	= JFactory::getSession()->getFormToken();

		$theme->set('token', $token);
		$theme->set('task', $task);
		$output	= $theme->output('admin/html/form.action');

		return $output;
	}

    public static function hidden($name = '', $value = '')
    {
        $theme  = EB::template();

        $theme->set('name', $name);
        $theme->set('value', $value);
        $output = $theme->output('admin/html/form.hidden');

        return $output;
    }

    public static function token()
    {
        $theme = EB::template();
        $token = JFactory::getSession()->getFormToken();

        $theme->set('token', $token);
        $output  = $theme->output('admin/html/form.token');

        return $output;
    }
}
