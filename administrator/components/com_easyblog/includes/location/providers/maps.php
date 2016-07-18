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

class EasyBlogLocationProvidersMaps extends EasyBloglocationProviders
{
    protected $queries = array(
        'latlng' => '',
        'address' => '',
        'key' => ''
    );

    public $url = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Determines if the settings is complete
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function isSettingsComplete()
    {
        if (!$this->config->get('location_service_provider') == 'maps') {
            return false;
        }

        return true;
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

        $connector = EB::connector();
        $connector->setMethod('GET');
        $connector->addUrl($this->url . '?' . http_build_query($options));
        $connector->execute();

        $result = $connector->getResult();

        $result = json_decode($result);

        if (!isset($result->status) || $result->status != 'OK') {
            $error = isset($result->error_message) ? $result->error_message : JText::_('COM_EASYBLOG_LOCATION_PROVIDERS_MAPS_UNKNOWN_ERROR');

            $this->setError($error);
            return array();
        }

        $venues = array();

        foreach ($result->results as $row) {
            $obj = new EasyBlogLocationData;
            $obj->latitude = $row->geometry->location->lat;
            $obj->longitude = $row->geometry->location->lng;
            $obj->name = $row->formatted_address;
            $obj->address = '';

            $venues[] = $obj;
        }

        return $venues;
    }
}
