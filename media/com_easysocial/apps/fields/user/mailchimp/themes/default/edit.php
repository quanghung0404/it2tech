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
<div class="form-group <?php echo !empty( $error ) ? 'has-error' : '';?>"
	data-field
	data-field-<?php echo $field->id; ?>
	data-edit-field
	data-edit-field-<?php echo $field->id; ?>
	<?php if( !isset( $options['check'] ) || $options['check'] !== false ) { ?>data-check<?php } ?>
>
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-xs-12 col-sm-8 data">
		<label class="checkbox" for="<?php echo $inputName;?>">
			<input type="checkbox" name="<?php echo $inputName;?>" id="<?php echo $inputName;?>" value="1" <?php echo $value ? ' checked="checked"' :'';?>/>
			<?php echo JText::_($params->get( 'title', JText::_('PLG_FIELDS_MAILCHIMP_SUBSCRIBE_TO_NEWSLETTER')) ); ?>
		</label>
	</div>

	<?php if( !isset( $options['error'] ) || $options['error'] !== false ) {
		echo $this->includeTemplate( 'site/fields/error' );
	} ?>

	<?php if( $params->get( 'display_description' ) ) {
		echo $this->includeTemplate( 'site/fields/description' );
	} ?>
</div>
