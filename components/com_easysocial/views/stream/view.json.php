<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/views/views' );

class EasySocialViewStream extends EasySocialSiteView
{
	public function display($tpl = null)
	{
        $auth   = $this->input->getString('auth');

        // Get the current logged in user's information
        $model  = FD::model('Users');
        $id     = $model->getUserIdFromAuth($auth);

        $userId = $this->input->getInt('userid');

        $limit = $this->input->getInt('limit');
        $startlimit = $this->input->getInt('startlimit');

        $filter = $this->input->getString('filter', 'me');

        // If user id is not passed in, return logged in user
        if (!$userId) {
            $userId = $id;
        }

        // If we still can't find user's details, throw an error
        if (!$userId || !$auth) {

            $this->set('code', 403);
            $this->set('message', JText::_('Invalid user id provided.'));

            return parent::display();
        }

		// Get the stream library
		$stream 	= FD::stream();

		$options = array('userId' => $userId, 'startlimit' => $startlimit, 'limi' => $limit);

		switch($filter) {
			case 'everyone':
				$options['guest'] = true;
				$options['ignoreUser'] = true;
				break;

			case 'following':
			case 'follow':
				$options['type'] = 'follow';
				break;
			case 'bookmarks':
				$options['guest'] = true;
				$options['type'] = 'bookmarks';
			case 'me':
				// nohting to set
				break;
			case 'hashtag':
				$tag = $this->input->getString('tag', '');
				$options['tag'] = $tag;
				break;
			default:
				$options['context'] = $filter;
				break;
		}

		// $stream->get(array('userId' => $userId, 'startlimit' => $startlimit, 'limit' => $limit, 'type' => $filter));
		$stream->get($options);


		$result 	= $stream->toArray();

		$data 		= new stdClass();
		$data->items 	= array();

		if (!$result) {
			$this->set('items' , array());
		}

		// Set the url to this listing
		$data->url = FRoute::stream();

		// Follows the spec of http://activitystrea.ms/specs/json/1.0/
		foreach ($result as $row) {

			$item = new stdClass();

			// Set the stream title
			$item->title = strip_tags($row->title);

			// Set the stream content
			$item->raw_content = $row->content_raw;
			$item->content = $row->content;

			// Set the publish date
			$item->published = $row->created->toMySQL();

			// Set the generator
			$item->generator = new stdClass();
			$item->generator->url = JURI::root();

			// Set the generator
			$item->provider = new stdClass();
			$item->provider->url = JURI::root();

			// Set the verb
			$item->verb = $row->verb;

			// Set the actor
			$item->actor = new stdClass();
			$item->actor->id = $row->actor->id;
			$item->actor->url = $row->actor->getPermalink();
			$item->actor->objectType = 'person';

			// Set actors image
			$item->actor->image 		= new stdClass();
			$item->actor->image->url	= $row->actor->getAvatar();
			$item->actor->image->width	= SOCIAL_AVATAR_MEDIUM_WIDTH;
			$item->actor->image->height	= SOCIAL_AVATAR_MEDIUM_HEIGHT;

			// Set the actors name
			$item->actor->displayName	= $row->actor->getName();

			// These properties onwards are not activity stream specs
			$item->icon = $row->fonticon;

			// Like id should contain the exact item id
			$item->likes = new stdClass();

			if (!is_bool($row->likes)) {
				$item->likes->uid = $row->likes->uid;
				$item->likes->element = $row->likes->element;
				$item->likes->group = $row->likes->group;
				$item->likes->verb = $row->likes->verb;
				$item->likes->hasLiked = $row->likes->hasLiked();
				$item->likes->stream_id = $row->likes->stream_id;

				// Get the total likes
				$item->likes->total = $row->likes->getCount();
			}

			$item->comments = new stdClass();

			if (!is_bool($row->comments)) {
				$item->comments->uid = $row->comments->uid;
				$item->comments->element = $row->comments->element;
				$item->comments->group = $row->comments->group;
				$item->comments->verb = $row->comments->verb;
				$item->comments->stream_id = $row->comments->stream_id;

				// Get the total likes
				$item->comments->total = $row->comments->getCount();
			}

			// Set the lapsed time
			$item->lapsed = $row->lapsed;

			// set the if this stream is mini mode or not.
			// mini mode should not have any actions such as - likes, comments, share and etc.
			$item->mini = $row->display == SOCIAL_STREAM_DISPLAY_MINI ? true : false;


			$data->items[]	= $item;
		}

		$this->set('data', $data);

		parent::display();
	}
}
