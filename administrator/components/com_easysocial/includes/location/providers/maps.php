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

FD::import('admin:/includes/location/provider');

class SocialLocationProvidersMaps extends SociallocationProviders
{
    protected $queries = array(
        'latlng' => '',
        'address' => '',
        'key' => ''
    );

    public $url = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct()
    {
        // key is optional for this case
        $key = FD::config()->get('location.maps.api');

        if (!empty($key)) {
            $this->setQuery('key', $key);
        }
    }

    public function setCoordinates($lat, $lng)
    {
        return $this->setQuery('latlng', $lat . ',' . $lng);
    }

    public function setSearch($search = '')
    {
        return $this->setQuery('address', $search);
    }

    public function getResult($queries = array())
    {
        $this->setQueries($queries);

        // If address is empty, then we only do a latlng search
        // If address is not empty, then we do an address search

        $options = array();

        if (!empty($this->queries['key'])) {
            $options['key'] = $this->queries['key'];
        }

        if (!empty($this->queries['address'])) {
            $options['address'] = $this->queries['address'];
        } else {
            $options['latlng'] = $this->queries['latlng'];
        }

        $connector = FD::connector();
        $connector->setMethod('GET');
        $connector->addUrl($this->url . '?' . http_build_query($options));
        $connector->execute();

        $result = $connector->getResult();

        $result = json_decode($result);

        if (!isset($result->status) || $result->status != 'OK') {
            $error = isset($result->error_message) ? $result->error_message : JText::_('COM_EASYSOCIAL_LOCATION_PROVIDERS_MAPS_UNKNOWN_ERROR');

            $this->setError($error);
            return array();
        }

        $venues = array();

        foreach ($result->results as $row) {
            $obj = new SocialLocationData;
            $obj->latitude = $row->geometry->location->lat;
            $obj->longitude = $row->geometry->location->lng;
            $obj->name = $row->address_components[0]->long_name;
            $obj->address = $row->formatted_address;
            $obj->fulladdress = $row->formatted_address;

            $venues[] = $obj;
        }

        return $venues;
    }
}
