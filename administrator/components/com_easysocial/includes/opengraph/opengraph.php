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

class SocialOpengraph extends EasySocial
{
	public $properties 	= array();

	/**
	 * This is the factory method to ensure that this class is always created all the time.
	 * Usage: FD::get( 'Template' );
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public static function getInstance()
	{
		static $obj = null;

		if (!$obj) {
			$obj = new self();
		}

		return $obj;
	}

	/**
	 * Inserts an image into the opengraph headers
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addImage($image, $width = null, $height = null)
	{
		// Get the current site http/https append to image URL
		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		$obj = new stdClass();

		// some of the image path pass inside here which got contained http:// and https://
		// have to re-structure it again, first remove http:// or https://
		if (strpos($image, 'https://') !== false) {
			$image = ltrim($image, 'https:');
		}

		if (strpos($image, 'http://') !== false) {
			$image = ltrim($image, 'http:');
		}

		$obj->url = $scheme . $image;
		$obj->width = $width;
		$obj->height = $height;

		if (!isset($this->properties['image'])) {
			$this->properties['image'] = array();
		}

		$this->properties['image'][] = $obj;

		return $this;
	}

	/**
	 * Inserts the video into the opengraph headers
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addVideo(SocialVideo $video)
	{
		$this->addType('video');

		$obj = new stdClass();
		$obj->url = $video->getExternalPermalink();
		$obj->type = 'text/html';
		$obj->width = 1280;
		$obj->height = 720;

		$this->properties['video'][] = $obj;

		return $this;
	}

	/**
	 * Inserts the description of the page
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addDescription($content)
	{
		// Remove html tags from the content
		$content = strip_tags($content);

		// We need to remove newlines from the content
		$content = str_ireplace("\r\n", "", $content);

		// We also need to replace html entity for space with proper spaces
		$content = JString::str_ireplace("&nbsp;", " ", $content);

		// We also need to trim the content to avoid trailing / leading spaces
		$content = trim($content);

		// Decode back the contents if there is any other html entities
		$content = html_entity_decode($content);

		// Remove any double quotes to avoid issues with escaping
		$content = JString::str_ireplace('"', '', $content);

		$this->properties['description'] = $content;

		return $this;
	}

	/**
	 * Adds the url attribute
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addUrl($url)
	{
		$this->properties['url'] = $url;

		return $this;
	}

	public function addType( $type )
	{
		$this->properties[ 'type' ]	= $type;

		return $this;
	}

	public function addTitle( $title )
	{
		$this->properties[ 'title' ]	= $title;

		return $this;
	}

	/**
	 * Adds opengraph data for the user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addProfile( SocialUser $user )
	{
		// Only proceed when opengraph is enabled
		if (!$this->config->get('oauth.facebook.opengraph.enabled')) {
			return;
		}

		$this->properties['type'] = 'profile';
		$this->properties['title'] = JText::sprintf('COM_EASYSOCIAL_OPENGRAPH_PROFILE_TITLE', ucfirst($user->getName()));

		$this->addImage($user->getAvatar(SOCIAL_AVATAR_MEDIUM), SOCIAL_AVATAR_MEDIUM_WIDTH, SOCIAL_AVATAR_MEDIUM_HEIGHT);

		$this->addUrl($user->getPermalink(true, true));

		return $this;
	}

	/**
	 * Adds opengraph data for the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addGroup(SocialGroup $group)
	{
		$config = FD::config();

		// Only proceed when opengraph is enabled
		if (!$config->get('oauth.facebook.opengraph.enabled')) {
			return;
		}

		$this->properties['type']  = 'profile';
		$this->properties['title'] = $group->getName();
		$description = strip_tags($group->getDescription());
		$this->addDescription($description);
		$this->addImage($group->getAvatar(SOCIAL_AVATAR_MEDIUM), SOCIAL_AVATAR_MEDIUM_WIDTH, SOCIAL_AVATAR_MEDIUM_HEIGHT);
		$this->addUrl($group->getPermalink(true, true));

		return $this;
	}

	/**
	 * Adds the open graph tags on a page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function render()
	{
		// Only proceed when opengraph is enabled
		if (!$this->config->get('oauth.facebook.opengraph.enabled')) {
			return;
		}

		require_once(dirname(__FILE__) . '/renderer.php');

		foreach ($this->properties as $property => $data) {
			if (method_exists('OpengraphRenderer', $property)) {
				OpengraphRenderer::$property($data);
			}
		}

		return true;
	}
}
