<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div data-field-joomla_email>
	<input type="text"
		class="form-control input-sm"
		id="email"
		name="email"
		placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_SAMPLE_EMAIL_ADDRESS', true ); ?>" />

    <?php if( $params->get( 'mini_reconfirm_email', false ) ) { ?>
        <br />
        <input type="text" size="30" class="form-control input-sm" id="email-reconfirm" name="email-reconfirm" value=""
        style="margin-top:5px;"
        data-field-email-reconfirm-input
        autocompleted="off"
        placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_EMAIL_SAMPLE_EMAIL_ADDRESS_RECONFIRM' ); ?>" />
    <?php } ?>
</div>
