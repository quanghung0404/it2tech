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
?>


<?php echo JText::sprintf('COM_VIRTUEMART_WELCOME_VENDOR', $this->vendor->vendor_store_name) . $li. $li ?>
<?php echo JText::_('COM_VIRTUEMART_VENDOR_REGISTRATION_DATA') . " " . $li; ?>
<?php echo JText::_('COM_VIRTUEMART_LOGINAME')   . $this->user->username . $li; ?>
<?php echo JText::_('COM_VIRTUEMART_DISPLAYED_NAME')   . $this->user->name . $li. $li; ?>
<?php echo JText::_('COM_VIRTUEMART_ENTERED_ADDRESS')   . $li ?>


<?php

foreach ($this->userFields['fields'] as $userField) {
    if (!empty($userField['value']) && $userField['type'] != 'delimiter' && $userField['type'] != 'BT') {
	echo $userField['title'] . ' ' . $userField['value'] . $li;
    }
}

echo $li;

echo JURI::root() . 'index.php?option=com_virtuemart&view=user' . $li;

echo $li;
//echo JURI::root() . 'index.php?option=com_virtuemart&view=user&virtuemart_user_id=' . $this->_models['user']->_id . ' ' . $li;
//echo JURI::root() . 'index.php?option=com_virtuemart&view=vendor&virtuemart_vendor_id=' . $this->vendor->virtuemart_vendor_id . ' ' . $li;
?>
