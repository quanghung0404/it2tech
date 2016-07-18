<?php
/**
 * @package         Tabs
 * @version         5.1.10PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemTabsHelperProtect
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemTabsHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		list($tag_start, $tag_end) = $this->helpers->get('replace')->getTagCharacters();

		$this->params->protected_tags = array(
			$tag_start . $this->params->tag_open,
			$tag_start . '/' . $this->params->tag_close,
			$tag_start . $this->params->tag_link,
		);
	}

	public function protect(&$string)
	{
		NNProtect::protectFields($string);
		NNProtect::protectSourcerer($string);
	}

	public function protectTags(&$string)
	{
		NNProtect::protectTags($string, $this->params->protected_tags);
	}

	public function unprotectTags(&$string)
	{
		NNProtect::unprotectTags($string, $this->params->protected_tags);
	}
}
