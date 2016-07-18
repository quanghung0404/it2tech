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

class SocialSmileys extends EasySocial
{
	/**
	 * This class uses the factory pattern.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The image driver to use.
	 * @return	SocialImage		Returns itself for chaining.
	 */
	public static function factory()
	{
		$obj = new self();

		return $obj;
	}

	public function getEmojis()
	{
		static $icons = array();


		if (!$icons) {
			$library = SOCIAL_LIB . '/bbcode/adapters/decoda/library/config/emoticons.json';

			$jsonFileOverride = JPATH_ROOT . '/templates/' . JFactory::getApplication()->getTemplate() . '/html/com_easysocial/emoticons/emoticons.json';
			if (JFile::exists($jsonFileOverride)) {
				$library = $jsonFileOverride;
			}

			$contents = JFile::read($library);
			$items = json_decode($contents);
			$icons = array();

			foreach ($items as $key => $item) {

				$icons[$key] = new stdClass();

				$icons[$key]->key = $key;

				// Test if override exists
				$override =  JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easysocial/emoticons/' . $key . '.png';

				if (JFile::exists($override)) {
					$icons[$key]->image = JURI::root() . 'templates/' . $this->app->getTemplate() . '/html/com_easysocial/emoticons/' . $key . '.png';
				} else {
					$icons[$key]->image = JURI::root() . 'media/com_easysocial/images/icons/emoji/' . $key . '.png';
				}

				$icons[$key]->command = $item[0];
				$icons[$key]->commands = $item;
			}
		}

		return $icons;
	}

	/**
	 * Generates a list of smileys
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html()
	{
		$theme = ES::themes();

		$icons = $this->getEmojis();

		$theme->set('icons', $icons);
		$output = $theme->output('site/smileys/default');

		return $output;
	}
}
