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

FD::import('site:/views/views');

class EasySocialViewEvents extends EasySocialSiteView
{
    /**
     * Renders the RSS feed for event page
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function display()
    {
        // Get the event id
        $id = $this->input->get('id', 0, 'int');

        // Load up the event
        $event = FD::event($id);

        // Ensure that the event really exists
        if (empty($event) || empty($event->id)) {
            return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
        }

        // Ensure that the event is published
        if (!$event->isPublished()) {
            return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'));
        }

        // Determines if the current user is a guest of this event
        $guest = $event->getGuest($this->my->id);

        // Support for group event
        // If user is not a group member, then redirect to group page
        if ($event->isGroupEvent()) {

            $group = ES::group($event->getMeta('group_id'));

            if (!$this->my->isSiteAdmin() && !$event->isOpen() && !$group->isMember()) {
                return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_EVENTS_NO_PERMISSION_TO_VIEW_EVENT'));
            }
        } else {

            if (!$this->my->isSiteAdmin() && $event->isInviteOnly() && !$guest->isParticipant()) {
                return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_NO_ACCESS_TO_EVENT'));
            }
        }

        // Check if the current logged in user blocked by the event creator or not.
        if ($this->my->id != $event->creator_uid && $this->my->isBlockedBy($event->creator_uid)) {
            return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'));
        }

        // Set the title of the feed
        $this->page->title($event->getName());

        // Get the stream library
        $stream = ES::stream();
        $options = array('clusterId' => $event->id, 'clusterType' => $event->cluster_type, 'nosticky' => true);
        $stream->get($options);

        $items = $stream->data;

        if (!$items) {
            return;
        }

        foreach ($items as $item) {
            $feed = new JFeedItem();

            // Cleanse the title
            $feed->title = strip_tags($item->title);

            $content = $item->content . $item->preview;
            $feed->description = $content;

            // Permalink should only be generated for items with a full content
            $feed->link = $item->getPermalink(true);
            $feed->date = $item->created->toSql();
            $feed->category = $item->context;

            // author details
            $author = $item->getActor();
            $feed->author = $author->getName();
            $feed->authorEmail = $this->getRssEmail($author);

            $this->doc->addItem($feed);
        }
    }
}
