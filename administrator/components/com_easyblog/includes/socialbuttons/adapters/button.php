<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogSocialButton extends EasyBlog
{
	public $post = null;
	public $frontpage = false;
	public $position = null;
	public $team = null;
	public $bottom = null;

	public function __construct(EasyBlogPost $post, $options = array())
	{
		parent::__construct();

		$this->post = $post;
		$this->frontpage = isset($options['frontpage']) ? $options['frontpage'] : false;
		$this->position = isset($options['position']) ? $options['position'] : null;
		$this->bottom = isset($options['bottom']) ? $options['bottom'] : false;
	}

	/**
	 * Retrieves the ordering of the social buttons
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function ordering()
	{
		$key = 'integrations_order_' . $this->type;

		return $this->config->get($key, 0);
	}

	/**
	 * Retrieves the url of the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getUrl()
	{
		$url = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $this->post->id, false, true);

		return $url;
	}

	/**
	 * Generate a temporary place holder uid for the social sharing
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPlaceholderId()
	{
		$placeholder = 'sb-' . rand();

		return $placeholder;
	}

	/**
	 * Retrieves the post title
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getTitle()
	{
		$title = JString::trim(urlencode($this->post->title));

		return $title;
	}

	/**
	 * Retrieves the button size to use
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getButtonSize()
	{
		return $this->config->get('social_button_size');
	}
	
	/**
	 * Retrieves the description that should be used
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getDescription()
	{
		$desc = $this->post->getIntro(true);

		return $this->sanitizeText($desc);
	}

	/**
	 * Retrieves the post image 
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getImage()
	{
		$image = $this->post->getImage('thumbnail');

		return $image;
	}

	/**
	 * Sanitizes the text so that it doesn't display any funny characters on the string.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function sanitizeText($text)
	{
		$text = strip_tags($text);
		$text = str_ireplace("\n", '', $text);
		$text = str_ireplace("\r", '', $text);
		$text = str_ireplace('&nbsp;', ' ', $text);
		$text = EB::string()->escape($text);


		return $text;
	}
}
