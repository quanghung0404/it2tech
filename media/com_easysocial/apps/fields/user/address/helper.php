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
 * Helper file for address.
 *
 * @since   1.0
 * @author  Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserAddressHelper
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
    public static function getCountries($source = 'file')
    {
        static $countries = array();

        if (!isset($countries[$source])) {
            $data = new stdClass();

            if ($source === 'file') {
                $file = JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/countries.json';
                $contents = JFile::read($file);

                $json = FD::json();
                $data = (object) $json->decode($contents);
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
     * @param   string
     * @return
     */
    public static function getCountryName($code, $source = 'file')
    {
        $countries = self::getCountries($source);

        $value = $code;

        if (isset($countries->$code)) {
            $value = $countries->$code;
        }

        return $value;
    }

    public static function getStates($countryName, $sort = 'name')
    {
        $country = FD::table('region');

        $country->load(array('type' => SOCIAL_REGION_TYPE_COUNTRY, 'name' => $countryName));

        $states = $country->getChildren(array('ordering' => $sort));

        $data = new stdClass();

        foreach ($states as $state) {
            $data->{$state->name} = $state->name;
        }

        return $data;
    }
}
