<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-field-datetime-form with-border mb-5" data-field-datetime-form>
    <div class="es-field-datetime-textbox">
        <i class="fa fa-calendar"></i>
        <input class="datepicker-wrap form-control input-sm" data-field-datetime-select type="text" placeholder="<?php echo JText::_($params->get('placeholder')); ?>" />
    </div>
</div>

<div data-yearprivacy class="data-field-datetime-yearprivacy mt-10" <?php if (!$params->get('year_privacy')) { ?>style="display: none;"<?php } ?>>
    <h4><?php echo JText::_('PLG_FIELDS_BIRTHDAY_YEAR_PRIVACY_TITLE'); ?></h4>
    <div>
        <?php echo JText::_('PLG_FIELDS_BIRTHDAY_YEAR_PRIVACY_INFO'); ?>
    </div>
</div>
