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
<span class=""<?php echo isset( $show ) && $show === false ? ' style="display:none;"' : '';?> data-itemCondition>

	<?php if( $condition->input == 'text' ){ ?>
		<input data-condition type="text" class="form-control input-sm" name="conditions[]" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_ENTER_SOME_VALUE' , true );?>" value="<?php echo $this->html('string.escape', $selected);?>" />
	<?php } ?>

	<?php if( $condition->input == 'distance' ){
			$geo = array();

			if ($selected) {
				$geo = explode( '|', $selected );
			}
	?>

		<input data-distance type="number" class="form-control input-sm"
			   name="frmDistance" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_ENTER_DISTANCE' , true );?>"
			   value="<?php echo isset($geo[0]) ? $geo[0] : '';?>" />
        <input class="form-control input-sm" type="hidden" value="<?php echo isset($geo[1]) ? $geo[1] : '';?>" name="frmLatitude" data-latitude />
        <input class="form-control input-sm" type="hidden" value="<?php echo isset($geo[2]) ? $geo[2] : '';?>" name="frmLongitude" data-longitude />
        <input class="form-control input-sm" type="hidden" value="<?php echo isset($geo[3]) ? $geo[3] : '';?>" name="frmAddress" data-address />

		<input data-condition type="hidden" class="form-control input-sm" name="conditions[]" value="<?php echo $this->html('string.escape', $selected);?>" />
	<?php } ?>

	<?php if( $condition->input == 'age' ){ ?>
		<input data-condition type="number" class="form-control input-sm" name="conditions[]" min="1" max="150" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_ENTER_SOME_VALUE' , true );?>" value="<?php echo $this->html('string.escape', $selected);?>" />
	<?php } ?>

	<?php if( $condition->input == 'ages' ){ ?>
		<input data-condition type="hidden" class="form-control input-sm" name="conditions[]" value="<?php echo $this->html('string.escape', $selected);?>" />
		<?php
			$data[0] = '';
			$data[1] = '';

			if ($selected) {
				$tmp = explode( '|', $selected );
				$data[0] = $tmp[0];
				$data[1] = $tmp[1];
			}
		?>
		<input data-start type="number" class="form-control input-sm" name="frmStart" min="1" max="150" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_ENTER_FROM' , true );?>" value="<?php echo $this->html('string.escape', $data[0]);?>" />
		<input data-end type="number" class="form-control input-sm" name="frmEnd" min="1" max="150" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_ENTER_TO' , true );?>" value="<?php echo $this->html('string.escape', $data[1]);?>" />
	<?php } ?>

	<?php if ($condition->input == 'date'){ ?>
		<?php echo $this->html( 'form.calendar' , 'conditions[]' , $selected , '' , array( 'data-condition' ) ); ?>
	<?php } ?>

	<?php if ($condition->input == 'dates'){ ?>
		<input data-condition type="hidden" class="form-control input-sm" name="conditions[]" value="<?php echo $this->html('string.escape', $selected);?>" />
		<?php
			$data[0] = '';
			$data[1] = '';

			if ($selected) {
				$tmp = explode( '|', $selected );
				$data[0] = $tmp[0];
				$data[1] = $tmp[1];
			}
		?>

		<?php echo $this->html( 'form.calendar' , 'frmStart', $this->html( 'string.escape', $data[0] ), 'frmStart', array( 'data-start' ) ); ?>
		<?php echo $this->html( 'form.calendar' , 'frmEnd', $this->html( 'string.escape', $data[1] ), 'frmEnd', array( 'data-end' ) ); ?>
	<?php } ?>

</span>
