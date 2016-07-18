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

FD::import('admin:/includes/model');

class EasySocialModelMaintenance extends EasySocialModel
{
    /**
     * Caches the scripts path and filename.
     * @var array
     */
    private static $scripts = array();

    /**
     * Caches the available versions.
     * @var array
     */
    private static $versions = array();

    function __construct()
    {
        parent::__construct('maintenance');
    }

    public function initStates()
    {
        $ordering = $this->getUserStateFromRequest('ordering', 'version');
        $direction = $this->getUserStateFromRequest('direction', 'asc');
        $version = $this->getUserStateFromRequest('version', 'all');
        $search = $this->getUserStateFromRequest('search', '');

        $this->setState('ordering', $ordering);
        $this->setState('direction', $direction);
        $this->setState('version', $version);
        $this->setState('search', $search);

        parent::initStates();
    }

    private function initScripts()
    {
        if (empty(self::$scripts)) {
            $lib = FD::maintenance();

            $scripts = $lib->getScriptFiles('all');

            foreach ($scripts as $script) {
                $item = new EasySocialModelMaintenanceScriptItem;

                if ($item->load($script)) {
                    self::$scripts[$item->key] = $item;

                    self::$versions[] = $item->version;
                }
            }

            self::$versions = array_unique(self::$versions);
        }

        return true;
    }

    /**
     * Cache all scripts into self::$scripts first.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     */
    private function getScripts()
    {
        $this->initScripts();

        return self::$scripts;
    }

    public function getVersions()
    {
        $this->initScripts();

        return self::$versions;
    }

    public function getItems()
    {
        $scripts = $this->getScripts();

        $total = 0;

        $results = array();

        // Allowed filter
        // version
        // search
        foreach ($scripts as $script) {
            $version = $this->getState('version');

            if (!empty($version) && $version !== 'all' && $script->version != $version) {
                continue;
            }

            $search = $this->getState('search');

            if (!empty($search) && JString::strpos($script->title, $search === false)) {
                continue;
            }

            $results[] = $script;

            $total++;
        }

        $this->total = $total;
        $this->setState('total', $total);

        // Ordering
        usort($results, array($this, 'sortItems'));

        // var_dump($this->getState('ordering'), $this->getState('direction')); exit;

        $limit  = (int) $this->getState('limit');

        if ($limit > 0)
        {
            $this->setState('limit', $limit);

            $limitstart = $this->getUserStateFromRequest('limitstart', 0);
            $limitstart = (int) ($limit > 0 ? (floor($limitstart / $limit) * $limit ) : 0 );

            $this->setState('limitstart', $limitstart);

            $results = array_slice($results, $limitstart, $limit);
        }

        return $results;
    }

    private function sortItems($a, $b)
    {
        $ordering = $this->getState('ordering');
        $direction = $this->getState('direction');

        if (empty($ordering) || !isset($a->$ordering) || !isset($b->$ordering) || $a->$ordering == $b->$ordering) {
            return 0;
        }

        $marker = $direction === 'desc' ? -1 : 1;

        $result = $a->$ordering < $b->$ordering ? -$marker : $marker;

        return $result;
    }

    public function getItemByKeys($keys)
    {
        $scripts = $this->getScripts();

        $results = array();

        foreach ($keys as $key) {
            if (isset($scripts[$key])) {
                $results[] = $scripts[$key];
            }
        }

        return $results;
    }

    public function getItemByKey($key)
    {
        // If we are getting by a single key, then we see if cache is loaded
        // If cache is not loaded, we don't initiate it because it is unnecessary for cases of ajax loading 1 single script
        if (!empty(self::$scripts)) {
            $scripts = $this->getItemByKeys(array($key));

            if (count($scripts) < 1) {
                return false;
            }

            return $scripts[0];
        }

        $file = SOCIAL_ADMIN_UPDATES . '/' . $key;

        if (!JFile::exists($file)) {
            return false;
        }

        $script = new EasySocialModelMaintenanceScriptItem($file);

        return $script;
    }
}

class EasySocialModelMaintenanceScriptItem
{
    public $file;

    public $key;
    public $filename;
    public $version;
    public $classname;
    public $title;
    public $description;

    CONST PREFIX = 'SocialMaintenanceScript';
    CONST BASE = SOCIAL_ADMIN_UPDATES;

    public function __construct($file = null)
    {
        if (!empty($file)) {
            $this->load($file);
        }
    }

    public function load($file)
    {
        $this->file = $file;

        if (!JFile::exists($file)) {
            return false;
        }

        require_once($file);

        $this->key = str_ireplace(self::BASE . '/', '', $file);

        list($this->version, $this->filename) = explode('/', $this->key);

        $classname = self::PREFIX . str_ireplace('.php', '', $this->filename);

        if (!class_exists($classname)) {
            return false;
        }

        $this->classname = $classname;

        // PHP 5.2 compatibility
        $vars = get_class_vars($classname);

        $this->title = $vars['title'];

        $this->description = $vars['description'];

        return true;
    }

    public function toString()
    {
        return $this->file;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
