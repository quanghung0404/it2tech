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

// Import parent view
FD::import( 'site:/views/views' );

class EasySocialViewPhotos extends EasySocialSiteView
{
	/**
	 * Displays the photo item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function item()
	{
		// Get the user's id by validation
		$userId = $this->validateAuth();

		// Available type: photo, cover, avatar
		$type = $this->input->getString('type', 'photo');

		// Available group: user, group, event
		$group = $this->input->getString('group', SOCIAL_TYPE_USER);

		// This id varies depending on what $type is
		$id = $this->input->get('id', 0, 'int');
		$photo  = FD::table('Photo');

		if ($type == 'photo') {
			$photo->load($id);
		}

		if ($type == 'avatar') {
			// If there is no id passed in, then we use the logged in userId instead
			if ($group == SOCIAL_TYPE_USER) {
				if (empty($id)) {
					$id = $userId;
				}
			}

			$avatar = FD::table('Avatar');
			$avatar->load(array('uid' => $id, 'type' => $group));

			$photo->load($avatar->photo_id);
		}

		if ($type == 'cover') {
			// If there is no id passed in, then we use the logged in userId instead
			if ($group == SOCIAL_TYPE_USER) {
				if (empty($id)) {
					$id = $userId;
				}
			}

			$cover = FD::table('Cover');
			$cover->load(array('uid' => $id, 'type' => $group));

			$photo->load($cover->photo_id);
		}

		// Determine the size of the photo to render
		$size   = $this->input->get('size', 'thumbnail', 'cmd');

		// Get the photo source
		$src = $photo->getSource($size);

		$result = new stdClass();
		$result->url = $photo->getSource($size);
		$result->title = $photo->get('title');
		$result->created = $photo->created;
		$result->assigned_date = $photo->assigned_date;
		$result->width = $photo->getWidth();
		$result->height = $photo->getHeight();

		$this->set('photo', $result);

		parent::display();
	}


	/**
	 * Post process after a photo is uploaded via the story form
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function uploadStory($photo = null, $paths = array(), $width = '', $height = '')
	{
		$json = FD::json();

		// If there was an error uploading,
		// return error message.
		if ($this->hasErrors()) {
			$json->send($this->getMessage());
		}

		// Photo html
		$theme = ES::themes();
		$theme->set('photo', $photo);
		$theme->set('width', $width);
		$theme->set('height', $height);

		$html = $theme->output('site/photos/story/attachment.item');

		$response = new stdClass();
		$response->data = $photo->export();
		$response->html = $html;

		return $json->send($response);
	}

	/**
	 * Post process after the photo is uploaded on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function upload( $photo = null , $paths=array() )
	{
		$json = FD::json();

		// If there was an error uploading the photo, throw proper error message
		if ($this->hasErrors()) {
			$json->send($this->getMessage());
		}

		// Get the current logged in user
		$my = FD::user();

		// Get the layout to display
		$layout = JRequest::getCmd( 'layout' , 'item' );

		$options = array(
							'viewer' => $my->id,
							'layout' => $layout,
							'showResponse' => false,
							'showTags'     => false
						);

		// Load up the photo library
		$lib 	= FD::photo( $photo->uid , $photo->type , $photo );
		$output = $lib->renderItem( $options );

		$response		= new stdClass();
		$response->data = $photo->export();
		$response->html = $output;

		$json->send($response);
	}
}
