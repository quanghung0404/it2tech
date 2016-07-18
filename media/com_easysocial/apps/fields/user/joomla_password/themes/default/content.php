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
	<ul class="input-vertical list-unstyled">
		<?php if( $showOriginalPassword ) { ?>
		<li>
			<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_ENTER_ORIGINAL_PASSWORD_TO_CHANGE_PASSWORD' ); ?>
		</li>
		<li>
			<input type="password"
				size="50"
				class="form-control input-sm"
				autocomplete="off"
				id="<?php echo $inputName; ?>-orig"
				name="<?php echo $inputName; ?>-orig"
				data-field-password-orig
				placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_ENTER_PASSWORD' );?>" />
		</li>
		<li>
			<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_ENTER_NEW_PASSWORD' ); ?>
		</li>
		<?php } ?>

		<li>
			<input type="password" size="50" class="form-control input-sm" autocomplete="off"
					id="<?php echo $inputName; ?>-input"
					name="<?php echo $inputName; ?>-input"
					value="<?php echo !empty( $input ) ? $this->html( 'string.escape', $input ) : ''; ?>"
					data-field-password-input
					placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_ENTER_PASSWORD' );?>" />
			<span class="help-inline small" data-field-password-strength></span>
		</li>

		<?php if( $params->get( 'reconfirm_password' , true ) ){ ?>
		<li>
			<input type="password" size="50" class="form-control input-sm"
					autocomplete="off"
					id="<?php echo $inputName; ?>-reconfirm"
					value="<?php echo !empty( $input ) ? $this->html( 'string.escape', $input ) : ''; ?>"
					name="<?php echo $inputName; ?>-reconfirm"
					data-field-password-confirm
					placeholder="<?php echo JText::_( 'PLG_FIELDS_JOOMLA_PASSWORD_RECONFIRM_PASSWORD' );?>" />
		</li>
		<?php } ?>

	</ul>
</div>
