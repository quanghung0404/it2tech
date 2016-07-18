<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialSharing extends EasySocial
{
	static $availableVendors = array(
									'email',
									'facebook',
									'twitter',
									'google',
									'live',
									'linkedin',
									'myspace',
									'vk',
									'stumbleupon',
									'digg',
									'tumblr',
									'evernote',
									'reddit',
									'delicious'
								);

	public $vendors = array();
	private $options = array();

	public $display = 'dialog';
	public $title = '';
	public $summary = '';
	public $displayTitle = '';
	public $text = '';
	public $css = '';

	public function __construct($options = array())
	{
		parent::__construct();

		$this->load($options);
	}

	public static function factory($options = array())
	{
		return new self($options);
	}

	/**
	 * Loads the bookmarks
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function load($options = array())
	{
		// Set the options so that we could pass this to the vendors
		$this->options = $options;

		$this->normalizeUrl();
		$this->normalizeDisplay();
		$this->normalizeText();
		$this->normalizeCss();
		$this->normalizeTitle();
		$this->normalizeSummary();

		// Initialize the list of available vendors
		foreach (self::$availableVendors as $vendor) {
			if ($this->config->get('sharing.vendors.' . $vendor)) {
				$this->vendors[] = $vendor;
			}
		}

		// Determines if the caller wants to specifically exclude vendors
		if (isset($options['exclude'])) {
			$this->vendors = array_diff($this->vendors, self::$availableVendors);

			unset($options['exclude']);
		}

		// Force include
		if (isset($options['include'])) {
			$notInList = array_diff($options['include'], $this->vendors);

			$this->vendors = array_merge($this->vendors, $options['include']);

			unset($options['include']);
		}
	}

	public function normalizeUrl()
	{
		if (!isset($this->options['url'])) {
			$this->options['url'] = FRoute::_(JRequest::getURI());
		}

		// Set the url
		$this->url = $this->options['url'];
	}

	public function normalizeDisplay()
	{
		// If display mode is specified, set it accordingly.
		if (isset($this->options['display'])) {
			$this->display = $this->options['display'];
		}
	}

	public function normalizeText()
	{
		// Set the default text to our own text.
		$this->text = JText::_('COM_EASYSOCIAL_SHARING_SHARE_THIS');

		// If text is provided, allow user to override the default text.
		if (isset($this->options['text'])) {
			$this->text = JText::_($this->options['text']);
		}
	}

	public function normalizeCss()
	{
		if (isset($options['css'])) {
			$this->css = $options['css'];
		}
	}

	public function normalizeTitle()
	{
		if (isset($this->options['title'])) {
			$this->title = JText::_($this->options['title']);
		}
	}

	public function normalizeSummary()
	{
		if (isset($this->options['summary'])) {
			$this->summary = $this->options['summary'];
		}
	}

	/**
	 * Retrieve the contents of the sharing script
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getContents()
	{
		$theme = FD::themes();

		// Extract email out
		if (in_array('email', $this->vendors)) {
			$this->vendors = array_diff($this->vendors, array('email'));

			$theme->set('email', $this->getVendor('email'));
		}

		// Get list of vendors
		$vendors = $this->getVendors();

		$theme->set('vendors', $vendors);

		$contents = $theme->output('admin/sharing/base');

		return $contents;
	}

	/**
	 * Displays the sharing code on the page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html($icon = false, $smallText = true)
	{
		// Check for global settings
		if (!$this->config->get('sharing.enabled')) {
			return;
		}

		$theme = FD::themes();

		// Generate a unique id for this element
		$uniqueid = uniqid();

		// Set the text
		$theme->set('text', $this->text);
		$theme->set('css', $this->css);
		$theme->set('uniqueid', $uniqueid);
		$theme->set('icon', $icon);

		if ($this->display !== "dialog") {
			$contents = $this->getContents();
			$theme->set('contents', $contents);
		}

		$theme->set('smallText', $smallText);
		$theme->set('url', $this->url);
		$theme->set('title', $this->title);
		$theme->set('summary', $this->summary);

		$output = $theme->output('admin/sharing/base.' . $this->display);

		return $output;
	}

	/**
	 * Deprecated method. Use $sharing->html
	 *
	 * @deprecated 1.4
	 */
	public function getHtml($icon = false)
	{
		return $this->html($icon);
	}

	/**
	 * Retrieves a list of vendors
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getVendors()
	{
		$vendors = array();

		foreach ($this->vendors as $name) {
			$vendor = $this->getVendor($name);

			if ($vendor !== false) {
				$vendors[$name] = $vendor;
			}
		}

		return $vendors;
	}

	/**
	 * Retrieves a single vendor library
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getVendor($name)
	{
		static $vendors = array();

		if (!isset($vendors[$name])) {

			$file = __DIR__ . '/vendors/' . $name . '.php';
			$exists = JFile::exists($file);

			if (!$exists) {
				return false;
			}

			require_once($file);

			$className = 'SocialSharing' . ucfirst($name);
			$vendor = new $className($name, $this->options);

			$vendors[$name] = $vendor;
		}

		return $vendors[$name];
	}

	public function sendLink($recipients, $token, $content = '')
	{
		$my = FD::user();

		$mailer = FD::mailer();
		$mail = $mailer->getTemplate();

		$subject = JText::sprintf('COM_EASYSOCIAL_SHARING_EMAIL_TITLE', $my->getName());
		$url = base64_decode($token);

		// Set the subject
		$mail->setTitle($subject);

		// Set the mail template
		$options = array(
						'url' => $url,
						'content' => $content,
						'senderName' => $my->getName(),
						'sender' => $my->email
					);

		$mail->setTemplate('site/sharing/link', $options);

		foreach ($recipients as $recipient) {
			$mail->setRecipient('', $recipient);

			$mailer->create($mail);
		}

		return true;
	}
}
