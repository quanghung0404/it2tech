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
<div data-field-gender>
	<?php if (false) { ?>
	<select id="<?php echo $inputName;?>" name="<?php echo $inputName;?>" data-field-gender-input class="form-control input-sm">
		<option value=""<?php echo $value == 0 ? ' selected="selected"' : '';?>><?php echo JText::_( 'PLG_FIELDS_GENDER_SELECT_A_GENDER' , true ); ?></option>
		<option value="1"<?php echo $value == 1 ? ' selected="selected"' : '';?>><?php echo JText::_( 'PLG_FIELDS_GENDER_SELECT_MALE' , true );?></option>
		<option value="2"<?php echo $value == 2 ? ' selected="selected"' : '';?>><?php echo JText::_( 'PLG_FIELDS_GENDER_SELECT_FEMALE' , true ); ?></option>
	</select>
	<?php } ?>

	<label class="radio-inline">
		<input type="radio" value="1" name="<?php echo $inputName; ?>" <?php if($value == 1) { ?>checked="checked"<?php } ?> data-field-gender-select /> <?php echo JText::_('PLG_FIELDS_GENDER_SELECT_MALE'); ?>
	</label>
	<label class="radio-inline">
		<input type="radio" value="2" name="<?php echo $inputName; ?>" <?php if($value == 2) { ?>checked="checked"<?php } ?> data-field-gender-select /> <?php echo JText::_('PLG_FIELDS_GENDER_SELECT_FEMALE'); ?>
	</label>

	<?php if ($params->get('custom')) { ?>
	<label class="radio-inline">
		<input type="radio" value="3" name="<?php echo $inputName; ?>" <?php if($value == 3) { ?>checked="checked"<?php } ?> data-field-gender-select data-field-gender-select-custom /> <?php echo JText::_('PLG_FIELDS_GENDER_SELECT_CUSTOM'); ?>
	</label>

	<?php // TODO: allow custom input for gender ?>
	<?php if (false) { ?>
	<div data-field-gender-custom <?php if($value != 3) { ?>style="display: none;"<?php } ?>>
		<?php echo JText::_('PLG_FIELDS_GENDER_SELECT_CUSTOM_SPECIFY'); ?>: <input type="text" name="<?php echo $inputName; ?>-custom" value="<?php echo !empty($customValue) ? $customValue: ''; ?>" data-field-gender-custom-input />
	</div>
	<?php } ?>
	<?php } ?>

</div>
