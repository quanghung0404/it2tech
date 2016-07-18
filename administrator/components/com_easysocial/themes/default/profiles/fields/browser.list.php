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
<?php if ($defaultFields) { ?>
<ul class="list-unstyled browser-list" data-fields-browser-list>
    <?php foreach ($defaultFields as $defaultField) { ?>
        <?php if ($core == $defaultField->core && (!isset($unique) || $unique == $defaultField->unique)) { ?>
            <?php echo $this->includeTemplate('admin/profiles/fields/browser.item', array('defaultField' => $defaultField)); ?>
        <?php } ?>
    <?php } ?>
</ul>
<?php } else { ?>
    <?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_NO_FIELDS_AVAILABLE'); ?>
<?php } ?>
