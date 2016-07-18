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
<?php if ($yearRange === false) { ?>
    <input
        data-field-datetime-year
        class="form-control input-sm"
        type="text"
        value="<?php echo $year; ?>"
        placeholder="<?php echo JText::_('PLG_FIELDS_DATETIME_YEAR'); ?>"
        style="width: 30%;"
    />
<?php } else { ?>

    <div class="es-field-datetime-group" style="width:28%;">
        <?php echo JText::_('PLG_FIELDS_DATETIME_YEAR'); ?>
    <select
        data-field-datetime-year
        class="form-control input-sm"

    >
        <option value=""><?php echo JText::_('PLG_FIELDS_DATETIME_YEAR'); ?></option>

        <?php for ($i = $yearRange->min; $i <= $yearRange->max; $i++) { ?>
            <option value="<?php echo $i; ?>" <?php if ($year == $i) { ?>selected="selected"<?php } ?>><?php echo $i; ?></option>
        <?php } ?>
    </select>
    </div>
<?php }
