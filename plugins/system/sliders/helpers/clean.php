<?php
/**
 * @package         Sliders
 * @version         5.1.11PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemSlidersHelperClean
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemSlidersHelpers::getInstance();
		$this->params  = $this->helpers->getParams();
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	public function cleanLeftoverJunk(&$string)
	{
		$this->helpers->get('protect')->unprotectTags($string);

		NNProtect::removeFromHtmlTagContent($string, $this->params->protected_tags);
		NNProtect::removeInlineComments($string, 'Sliders');
	}
}
