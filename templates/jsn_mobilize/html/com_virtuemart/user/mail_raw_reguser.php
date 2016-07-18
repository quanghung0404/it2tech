<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('');

$li = "\n";

echo JText::sprintf('COM_VIRTUEMART_WELCOME_USER', $this->user->name) . $li . $li;

if (!empty($this->activationLink)) {
    $activationLink = '<a class="default" href="' . JURI::root() . $this->activationLink . '>' . JText::_('COM_VIRTUEMART_LINK_ACTIVATE_ACCOUNT') . '</a>';
}
echo $activationLink . $li;
echo JText::_('COM_VIRTUEMART_SHOPPER_REGISTRATION_DATA') . $li;

echo JText::_('COM_VIRTUEMART_YOUR_LOGINAME') . ' : ' . $this->user->username . $li;
echo JText::_('COM_VIRTUEMART_YOUR_DISPLAYED_NAME') . ' : ' . $this->user->name . $li;
echo JText::_('COM_VIRTUEMART_YOUR_PASSWORD') . ' : ' . $this->user->password_clear . $li;
echo JText::_('COM_VIRTUEMART_YOUR_ADDRESS') . ' : ' . $li;

echo $li;
echo $activationLink . $li;

foreach ($this->userFields['fields'] as $userField) {
    if (!empty($userField['value']) && $userField['type'] != 'delimiter' && $userField['type'] != 'BT') {
	echo $userField['title'] . ': ' . $this->escape($userField['value']) . $li;
	if ($userField['name'] != 'title' and $userField['name'] != 'first_name' and $userField['name'] != 'middle_name' and $userField['name'] != 'zip') {
	    echo $li;
	}
    }
}
echo $li;

