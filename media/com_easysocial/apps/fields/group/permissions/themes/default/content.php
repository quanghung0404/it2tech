<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div>
    <div class="checkbox">
        <label for="stream-permissions-admin" class="option">
            <input type="checkbox" id="stream-permissions-admin" value="admin" name="stream_permissions[]" <?php if (!empty($value) && in_array('admin', $value)) { ?>checked="checked"<?php } ?> />
            <?php echo JText::_('COM_EASYSOCIAL_APP_GROUP_PERMISSIONS_ADMINS'); ?>
        </label>
    </div>
    <div class="checkbox">
        <label for="stream-permissions-everyone" class="option">
            <input type="checkbox" id="stream-permissions-everyone" value="member" name="stream_permissions[]" <?php if (!empty($value) && in_array('member', $value)) { ?>checked="checked"<?php } ?> />
            <?php echo JText::_('COM_EASYSOCIAL_APP_GROUP_PERMISSIONS_MEMBERS'); ?>
        </label>
    </div>
</div>
