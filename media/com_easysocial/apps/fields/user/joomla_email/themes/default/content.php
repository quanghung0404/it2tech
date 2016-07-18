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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div data-field-joomla_email>
    <ul class="input-vertical list-unstyled">
        <li>
            <input type="text" size="30" class="form-control input-sm" id="email" name="email" value="<?php echo $email; ?>"
            data-check-required
            data-field-email-input
            autocompleted="off"
            placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_SAMPLE_EMAIL_ADDRESS' ); ?>" />
        </li>

        <?php if( $params->get( 'reconfirm_email', true ) ) { ?>
        <li data-field-email-reconfirm-frame <?php if( !$showConfirmation ) { ?>style="display: none;"<?php } ?>>
            <?php echo JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_RECONFIRM_EMAIL' ); ?>
        </li>
        <li data-field-email-reconfirm-frame <?php if( !$showConfirmation ) { ?>style="display: none;"<?php } ?>>
            <input type="text" size="30" class="form-control input-sm" id="email-reconfirm" name="email-reconfirm" value=""
            data-field-email-reconfirm-input
            autocompleted="off"
            placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_SAMPLE_EMAIL_ADDRESS' ); ?>" />
        </li>
        <?php } ?>
    </ul>
</div>
