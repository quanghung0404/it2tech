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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/views/views' );

class EasySocialViewLikes extends EasySocialSiteView
{

    /**
     * Toggle the likes on an object.
     *
     * @since   1.0
     * @access  public
     * @param
     * @return  string
     */
    public function toggle()
    {
        // Validate the current request
        $userId = $this->validateAuth();

        // Get the item properties
        $id = $this->input->get('uid', 0, 'int');
        $type    = $this->input->get('element', '', 'cmd');
        $group   = $this->input->get('group', SOCIAL_APPS_GROUP_USER, 'cmd');
        $itemVerb = $this->input->get('verb', '', 'string');

        // Get the stream id.
        $streamid = $this->input->get('stream_id', 0, 'int');

        // If id is invalid, throw an error.
        if (!$id || !$type) {
            $this->set('status', 0);

            return parent::display();
        }

        // Get current logged in user.
        $my = FD::user();

        // Load likes library.
        $model  = FD::model('Likes');

        // Build the key for likes
        $key = $type . '.' . $group;

        if ($itemVerb) {
            $key = $key . '.' . $itemVerb;
        }

        // Determine if user has liked this item previously.
        $hasLiked   = $model->hasLiked($id, $key, $my->id);

        // If user had already liked this item, we need to unlike it.
        if ($hasLiked) {
            $state  = $model->unlike($id, $key, $my->id, $streamid);
        } else {
            $state  = $model->like($id, $key, $my->id, $streamid);

            //now we need to update the associated stream id from the liked object
            if ($streamid) {
                $stream = FD::stream();
                $stream->updateModified($streamid);
            }
        }

        // The current action
        $verb   = $hasLiked ? 'unlike' : 'like';

        $this->set('status', $state ? 1 : 0);

        parent::display();
    }
}
