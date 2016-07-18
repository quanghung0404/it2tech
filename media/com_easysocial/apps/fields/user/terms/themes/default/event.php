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
<div class="form-group <?php echo !empty( $error ) ? 'has-error' : ''; ?>"
	data-field
	data-field-<?php echo $field->id; ?>
	data-check
>
	<?php if ($params->get('dialog')) { ?>
		<div class="col-lg-8 col-lg-offset-3 terms-checkbox">
			<div class="checkbox">
				<label for="terms-<?php echo $inputName;?>">
					<input type="checkbox" id="terms-<?php echo $inputName;?>" name="<?php echo $inputName;?>" data-field-terms-checkbox <?php if ($value) { ?>checked="checked"<?php } ?> />
					<?php echo JText::sprintf('PLG_FIELDS_TERMS_ACCEPT_TERMS_DIALOG', '<a href="javascript:void(0);" data-field-terms-dialog>' . JText::_('PLG_FIELDS_TERMS_ACCEPT_TERMS_DIALOG_LINK') . '</a>');?>
				</label>
			</div>
			<?php echo $this->includeTemplate( 'site/fields/errormini' ); ?>
		</div>
	<?php } else { ?>
		<div data-field-terms class="fd-cf">
			<div class="col-xs-12 col-sm-12">
			<textarea class="form-control input-sm es-terms-field" readonly="readonly" data-field-terms-textbox><?php echo JText::_($params->get('message', JText::_('PLG_FIELDS_TERMS_CONDITION_MESSAGE_TERMS')));?></textarea>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 mt-5 terms-checkbox">
			<div class="checkbox">
				<label for="terms-<?php echo $inputName;?>">
					<input type="checkbox" id="terms-<?php echo $inputName;?>" name="<?php echo $inputName;?>" data-field-terms-checkbox <?php if ($value) { ?>checked="checked"<?php } ?> /> <?php echo JText::_( 'PLG_FIELDS_TERMS_ACCEPT_TERMS' );?>
				</label>
			</div>
			<?php echo $this->includeTemplate( 'site/fields/errormini' ); ?>
		</div>
	<?php } ?>

</div>
