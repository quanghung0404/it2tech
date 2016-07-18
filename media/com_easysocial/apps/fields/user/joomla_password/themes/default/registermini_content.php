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
<div data-field-joomla_password>
	<input type="password"
		class="form-control input-sm"
		autocomplete="off"
		id="<?php echo $inputName; ?>-input"
		name="<?php echo $inputName; ?>-input"
		placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_ENTER_PASSWORD', true );?>"
		data-password
		/>

        <?php if( $params->get( 'mini_reconfirm_password' , true ) ){ ?>
        <br />
        <input type="password" size="50" class="form-control input-sm"
                style="margin-top:5px;"
                autocomplete="off"
                id="<?php echo $inputName; ?>-reconfirm"
                value="<?php echo !empty( $input ) ? $this->html( 'string.escape', $input ) : ''; ?>"
                name="<?php echo $inputName; ?>-reconfirm"
                data-field-password-confirm
                placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_RECONFIRM_PASSWORD' );?>" />
        <?php } ?>
</div>
