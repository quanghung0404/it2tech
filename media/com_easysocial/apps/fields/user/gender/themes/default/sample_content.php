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
	<?php if(false) { ?>
	<select class="form-control input-sm">
		<option value="0" selected="selected"><?php echo JText::_( 'PLG_FIELDS_GENDER_SELECT_A_GENDER' ); ?></option>
		<option value="1"><?php echo JText::_( 'PLG_FIELDS_GENDER_SELECT_MALE' );?></option>
		<option value="2"><?php echo JText::_( 'PLG_FIELDS_GENDER_SELECT_FEMALE' ); ?></option>
	</select>
	<?php } ?>

	<label class="radio-inline">
		<input type="radio" value="1" name="<?php echo $inputName; ?>" /> <?php echo JText::_('PLG_FIELDS_GENDER_SELECT_MALE'); ?>
	</label>

	<label class="radio-inline">
		<input type="radio" value="2" name="<?php echo $inputName; ?>" /> <?php echo JText::_('PLG_FIELDS_GENDER_SELECT_FEMALE'); ?>
	</label>

	<label class="radio-inline" <?php if(!$params->get('custom')) { ?>style="display: none;"<?php } ?> data-field-gender-others>
		<input type="radio" value="3" name="<?php echo $inputName; ?>" /> <?php echo JText::_('PLG_FIELDS_GENDER_SELECT_CUSTOM'); ?>
	</label>
</div>
