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
<div class="es-remind es-remind-username mt-20">
	<div class="es-title">
		<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_ENTER_VERIFICATION' ); ?>
	</div>
	<div class="es-desp small">
		<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_ENTER_VERIFICATION_DESC' );?>
	</div>

	<div class="es-remind-form-wrap">
		<form name="remindUsername" method="post" action="<?php echo JRoute::_( 'index.php' );?>">
			<div class="input-group input-group-sm">
				<?php if ($this->config->get('registrations.emailasusername')) { ?>
				<span class="input-group-addon"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_EMAIL' );?></span>
				<input type="text" class="form-control" name="es-username" value="" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_EMAIL_PLACEHOLDER' , true );?>" />
				<?php } else { ?>
				<span class="input-group-addon"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_USERNAME' );?></span>
				<input type="text" class="form-control" name="es-username" value="" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_USERNAME_PLACEHOLDER' , true );?>" />
				<?php } ?>
			</div>
			<div class="input-group input-group-sm mt-10">
				<span class="input-group-addon"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_VERIFICATION' );?></span>
				<input type="text" class="form-control" name="es-code" value="" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_VERIFICATION_PLACEHOLDER' , true );?>" />
			</div>
			<hr />
			<button class="btn btn-es-primary btn-submit"><?php echo JText::_( 'COM_EASYSOCIAL_RESET_PASSWORD_BUTTON' ); ?></button>
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="controller" value="account" />
			<input type="hidden" name="task" value="confirmResetPassword" />
			<?php echo $this->html( 'form.token' ); ?>
		</form>
	</div>
</div>
