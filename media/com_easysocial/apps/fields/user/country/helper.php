<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

/**
 * Helper file for country.
 *
 * @since   1.0
 * @author  Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserCountryHelper
{
    /**
     * Retrieves a list of countries from the manifest file.
     *
     * @since   1.0
     * @access  public
     * @return  Array    An array of countries.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public static function getCountries($source = 'regions')
    {
        static $countries = array();

        if (!isset($countries[$source])) {
            $data = new stdClass();

            if ($source === 'file') {
                $file = JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/countries.json';
                $contents = JFile::read($file);

                $json = FD::json();
                $data = $json->decode($contents);
                $data = (array) $data;

                // Sort by alphabet
                asort($data);

                $data = (object) $data;
            }

            if ($source === 'regions') {
                $countries = FD::model('Regions')->getRegions(array(
                    'type' => SOCIAL_REGION_TYPE_COUNTRY,
                    'state' => SOCIAL_STATE_PUBLISHED,
                    'ordering' => 'ordering'
               ));

                foreach ($countries as $country) {
                    $data->{$country->code} = $country->name;
                }
            }

            $countries[$source] = $data;
        }

        return $countries[$source];
    }

    /**
     * Gets the country title given the code.
     *
     * @since   1.0
     * @access  public
     * @param   string  Country code
     * @return  string  Country name
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public static function getCountryName($code, $source = 'regions')
    {
        $countries = self::getCountries();

        if (!isset($countries->$code)) {
            return false;
        }

        $value  = $countries->$code;

        return $value;
    }

    public static function getCountryCode($name, $source = 'regions')
    {
        $countries = self::getCountries($source);

        $code = false;

        foreach ($countries as $k => $v) {
            if ($v == $name) {
                $code = $k;
                break;
            }
        }

        return $code;
    }

    public static function getHTMLContentCountries($source = 'regions')
    {
        $countries  = (array) self::getCountries($source);

        $data = array();

        foreach($countries as $key => $value) {
            $row = new stdClass();
            $row->id = $key;
            $row->title = $value;
            $data[] = $row;
        }

        return $data;
    }
}
