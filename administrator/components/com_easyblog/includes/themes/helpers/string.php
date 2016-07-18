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

class EasyBlogThemesHelperString
{
    public static function escape($string)
    {
        return EB::string()->escape($string);
    }

    /**
     * Formats a given date string with a given date format
     *
     * @since   1.0
     * @access  public
     * @param   string      The current timestamp
     * @param   string      The language string format or the format for Date
     * @param   bool        Determine if it should be using the appropriate offset or GMT
     * @return  
     */
    public static function date( $timestamp , $format = '' , $withOffset = true)
    {
        // Get the current date object based on the timestamp provided.
        $date = EB::date($timestamp, $withOffset);

        // If format is not provided, we should use DATE_FORMAT_LC2 by default.
        $format = empty($format) ? 'DATE_FORMAT_LC2' : $format;

        // Get the proper format.
        $format = JText::_($format);
        
        $dateString = $date->format($format);

        return $dateString;
    }

    /**
     * Pluralize the string if necessary.
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return  
     */
    public static function pluralize( $languageKey , $count )
    {
        return Foundry::string()->computeNoun( $languageKey , $count );
    }

    /**
     * Truncates a string at a centrain length and add a more link
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public static function truncater($text, $max = 250)
    {
        $theme = EB::template();
        $length = JString::strlen($text);

        $uid = uniqid();

        $theme->set('uid', $uid);
        $theme->set('length', $length);
        $theme->set('text', $text);
        $theme->set('max', $max);

        $output = $theme->output('admin/html/string.truncater');

        return $output;
    }
}