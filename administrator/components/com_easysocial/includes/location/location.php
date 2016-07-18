<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialLocation extends EasySocial
{
    static $providers = array();
    protected $provider = null;
    private $baseProviderClassname = '';

    public $table = null;

    public function __construct($id = null, $type = null)
    {
        parent::__construct();

        // Initialize the location provider
        $this->provider = $this->initProvider();

        $this->table = ES::table('Location');

        if (!is_null($id) && !is_null($type)) {
            $this->table->load(array('uid' => $id, 'type' => $type));
        }
    }

    public static function factory($id = null, $type = null)
    {
        return new self($id, $type);
    }

    /**
     *
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function hasAddress()
    {
        return !empty($this->table->address);
    }

    /**
     * Retrieves the longitude
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getLongitude()
    {
        return $this->table->longitude;
    }

    /**
     * Retrieves the latitude
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getLatitude()
    {
        return $this->table->latitude;
    }

    /**
     * Retrieves the address of a location.
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getAddress()
    {
        $address = $this->table->address;

        return $address;
    }

    /**
     * Initialize the location provider
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function initProvider($provider = null)
    {
        // If no provider is given, then we load the default one from the settings
        if (!$provider) {
            $provider = $this->config->get('location.provider', 'fallback');
        }

        if (isset(self::$providers[$provider])) {
            return self::$providers[$provider];
        }

        $file = __DIR__ . '/providers/' . strtolower($provider) . '.php';

        require_once($file);

        $className = 'SocialLocationProviders' . ucfirst($provider);
        $obj = new $className;


        // Now we check if the provider's initialisation generated any errors
        if ($obj->hasErrors()){
            return false;
        }

        self::$providers[$provider] = $obj;

        return self::$providers[$provider];
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->provider, $method), $arguments);
    }
}
