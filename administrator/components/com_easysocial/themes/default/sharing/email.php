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
<div class="clearfix" data-sharing-email <?php if( !empty( $url ) ) { ?>data-token="<?php echo $url; ?>"<?php } ?>>

	<?php if( empty( $url ) ) {

		echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_ERROR' );

	} else { ?>

	<div data-sharing-email-frame data-sharing-email-sending class="alert fade in" style="display: none;">
		<?php echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_SENDING' ); ?>
	</div>

	<div data-sharing-email-frame data-sharing-email-done class="alert alert-success fade in" style="display: none;">
		<?php echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_DONE' ); ?>
	</div>

	<div data-sharing-email-frame data-sharing-email-fail class="alert alert-error fade in" style="display: none;">
		<span data-sharing-email-fail-msg><?php echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_FAIL' ); ?></span>
	</div>

	<div data-sharing-email-frame data-sharing-email-form class="es-sharing-form">
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_RECIPIENTS' ); ?></label>
			<div data-sharing-email-recipients class="clearfix textboxlist">
				<input type="text" data-textboxlist-textField data-sharing-email-input class="form-control input-sm textboxlist-textField"
					   style=""/>
			</div>
			<p class="help-block fd-small muted"><?php echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_INFO' ); ?>
		</div>
		<div class="control-group mt-10">
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_MESSAGE' ); ?></label>
			<div class="controls">
				<textarea data-sharing-email-content class="form-control input-sm full-width"></textarea>
			</div>
		</div>
		<div class="mb-10 mt-20">
			<button type="button" data-sharing-email-send class="btn btn-es-primary pull-right"><?php echo JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_SEND' ); ?></button>
		</div>
	</div>

	<?php } ?>
</div>
