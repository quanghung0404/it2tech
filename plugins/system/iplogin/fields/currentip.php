<?php
/**
 * Element: Current IP
 * Displays the users current IP address
 *
 * @package         IP Login
 * @version         2.1.1PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class JFormFieldIPLogin_CurrentIP extends JFormField
{
	protected $type = 'Current IP';

	protected function getInput()
	{
		return '<code>' . $_SERVER['REMOTE_ADDR'] . '</code>';
	}
}
