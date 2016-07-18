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

// Include the fields library
FD::import('admin:/includes/fields/dependencies');

// Include helper file.
FD::import('fields:/event/permalink/helper');

class SocialFieldsEventPermalink extends SocialFieldItem
{
    /**
     * Validates the permalink.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @return  JSON    A jsong encoded string.
     */
    public function isValid()
    {
        // Render the ajax lib.
        $ajax = FD::ajax();

        // Get the cluster id.
        $clusterId = JRequest::getInt('clusterid' , 0);

        // Init the current alias.
        $current = '';

        if (!empty($clusterId))
        {
            $event = FD::event($clusterId);
            $current = $event->alias;
        }

        // Get the provided permalink
        $permalink = JRequest::getVar('permalink' , '');

        // Check if the field is required
        if (!$this->field->isRequired() && empty($permalink)) {
            return true;
        }

        // Check if the permalink provided is valid
        if (!SocialFieldsEventPermalinkHelper::valid($permalink , $this->params))
        {
            return $ajax->reject(JText::_('FIELDS_EVENT_PERMALINK_INVALID_PERMALINK'));
        }

        // Test if permalink exists
        if (SocialFieldsEventPermalinkHelper::exists($permalink) && $permalink != $current)
        {
            return $ajax->reject(JText::_('FIELDS_EVENT_PERMALINK_NOT_AVAILABLE'));
        }

        $text       = JText::_('FIELDS_EVENT_PERMALINK_AVAILABLE');

        return $ajax->resolve($text);
    }
}
