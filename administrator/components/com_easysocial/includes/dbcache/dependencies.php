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

class SocialDbcacheTable
{
    /**
     * Set caching mode of this table.
     * Full mode will cache the full data from the table.
     * Row mode will cache data from the table on each loads.
     * @var string
     */
    protected $mode = 'full';

    /**
     * SocialTable object class key name.
     * @var string
     */
    protected $key;

    /**
     * Derived tablename based on $key.
     * @var string
     */
    protected $tablename;

    /**
     * Derived tablekey based on $key.
     * @var string
     */
    protected $tablekey;

    /**
     * Cached data.
     * @var array
     */
    protected $data = array();

    /**
     * Total data count.
     * @var int
     */
    protected $total;

    /**
     * Cached resultset mapped to $data with $tablekey as key.
     * @var array
     */
    protected $resultset = array();

    /**
     * Custom filter as serialized key mapped to $data with $tablekey as key.
     * @var array
     */
    protected $map = array();

    /**
     * Stores filter used for sql select conditions
     * @var array
     */
    protected $filters = array(
        'where' => array(),
        'order' => array(),
        'limit' => array()
    );

    public function __construct($options = array())
    {
        $this->init($options);
    }

    public function init($options = array())
    {
        $this->db = FD::db();

        if (isset($options['mode'])) {
            $this->mode = $options['mode'];
        }

        if (isset($options['key'])) {
            $this->key = $options['key'];
        }

        if (isset($options['tablename'])) {
            $this->tablename = $options['tablename'];
        }

        if (isset($options['tablekey'])) {
            $this->tablekey = $options['tablekey'];
        }

        $table = $this->getTable();

        if (empty($this->tablename)) {
            $this->tablename = $table->getTableName();
        }

        if (empty($this->tablekey)) {
            $this->tablekey = $table->getKeyName();
        }

        if ($this->mode === 'full') {
            $this->initTable();
        }
    }

    public function initTable()
    {
        $sql = $this->db->sql();

        $sql->select($this->tablename);

        $this->db->setQuery($sql);

        $this->data = $this->db->loadObjectList($this->tablekey);

        $this->total = count($this->data);
    }

    public function initRow($keys)
    {
        // $keys is definitely NOT ARRAY

        $sql = $this->db->sql();
        $sql->select($this->tablename);

        $originalKeys = $keys;

        if (!is_array($keys)) {
            $keys = array($this->tablekey => $keys);
        }

        foreach ($keys as $k => $v) {
            $sql->where($k, $v);
        }

        $sql->limit(1);

        $this->db->setQuery($sql);
        $result = $this->db->loadRow();

        $this->data[$originalKeys] = $result;
    }

    /**
     * Method to convert row of query data into JTable object by calling bind on the JTable object.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access protected
     * @param  array     $result Arrays of row object.
     * @return array             Arrays of JTable object.
     */
    public function bindTable($result)
    {
        $binded = array();

        foreach ($result as $i => $row) {
            $table = $this->getTable();

            $table->bind($row);
            $binded[$i] = $table;
        }

        return $binded;
    }

    /**
     * Method to get table object. Implementor might have a different way of retrieving JTable object.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access protected
     * @return JTable    The JTable object based on the key.
     */
    protected function getTable()
    {
        return FD::table($this->key);
    }

    /**
     * Alias method instead of using JTable load to get the row object from cached data
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     * @param  Mixed     $keys Keys to filter.
     * @return Object          Standard class of the row object.
     */
    public function get($keys = null)
    {
        if (!is_array($keys)) {
            if (!isset($this->data[$keys])) {
                $this->initRow($keys);
            }

            return $this->data[$keys];
        }

        ksort($keys);

        $key = serialize($keys);

        if (!isset($this->map[$key])) {
            $result = $this->filter($keys);

            if (empty($result)) {
                $this->map[$key] = false;
            } else {
                $this->map[$key] = $result[0];
            }
        }

        if (!$this->map[$key]) {
            return false;
        }

        return $this->data[$this->map[$key]];
    }

    /**
     * Similar to get but returns a binded JTable object instead
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     * @param  Mixed     $keys Keys to filter.
     * @return JTable          JTable of the row object.
     */
    public function load($keys = null)
    {
        $data = $this->get($keys);

        if (!$data) {
            return false;
        }

        $table = $this->getTable();
        $table->bind($data);

        return $data;
    }

    /**
     * Returns an array of keys that needs to be mapped to $data based on $tablekey.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access protected
     * @param  array     $keys Keys to filter.
     * @return array           Array of keys based on $tablekey corresponding to $data key.
     */
    protected function filter($keys = array())
    {
        ksort($keys);

        $key = serialize($keys);

        if (!isset($this->resultset[$key])) {
            $result = array();

            foreach ($this->data as $obj) {
                $match = true;

                // As long as one of the filter fails, it is considered as not match
                foreach ($keys as $k => $v) {
                    if ($obj->$k != $v) {
                        $match = false;
                        break;
                    }
                }

                if ($match) {
                    $result[] = $obj->{$this->tablekey};
                }
            }

            $this->resultset[$key] = $result;
        }

        return $this->resultset[$key];
    }

    /**
     * Alias method instead of using database driver to get the result set from cached data
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     * @param  array     $filters Keys to filter.
     * @return array              Array of table objects.
     */
    public function loadObjectList($filters = array(), $order = null, $limit = null)
    {
        $maps = $this->filter($filters);

        $result = array();

        foreach ($maps as $id) {
            $result[$id] = $this->data[$id];
        }

        return $result;
    }
}
