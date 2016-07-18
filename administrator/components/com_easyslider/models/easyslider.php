<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');


/**
 * sliders model.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
class JSNEasySliderModelSliders extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array('il.slider_id', 'il.slider_title', 'il.published', 'il.ordering');
        }
        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return    string    An SQL query
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields
        $query->select('il.*');
        $query->from('#__jsn_easyslider_sliders AS il');
        $query->order($db->escape($this->getState('list.ordering') . ' ' . $db->escape($this->getState('list.direction'))));

        // Join over the asset groups
        $query->select('ag.title AS access');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = access');

        // Check for a search filter
        if ($this->getState('filter.search'))
        {
            $query->where('( ' . $db->quoteName('il.slider_title') . ' LIKE \'%' . $db->escape($this->getState('filter.search')) . '%\' )');
        }

        // If the model is set to check slider state, add to the query
        $state = $this->getState('filter.state');
        $access = $this->getState('filter.access');

        if (is_numeric($state))
        {
            $query->where('il.published = ' . (int)$state, 'AND');
        }

        if (is_numeric($access))
        {
            $query->where('il.access = ' . (int)$access, 'AND');
        }

        return $query;
    }

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     *
     * to be called on the first call to the getState() method unless the model
     *
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string $ordering An optional ordering field.
     * @param   string $direction An optional direction (asc|desc).
     *
     * @return  void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $state);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '', 'string');
        $this->setState('filter.access', $access);

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // List state information.
        parent::populateState('il.slider_id', 'asc');
    }
}
