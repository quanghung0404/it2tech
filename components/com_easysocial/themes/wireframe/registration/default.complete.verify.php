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
<div class="es-complete-wrap mt-20">

	<?php if( $user ){ ?>
	<span class="es-avatar es-avatar-md es-avatar-rounded">
		<img src="<?php echo $user->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" />
	</span>
	<?php } ?>
	<div class="h4">
		<?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_COMPLETED_ACTIVATE_ACCOUNT' );?>
	</div>

	<?php if ($user) { ?>
		<p><?php echo JText::_('COM_EASYSOCIAL_REGISTRATION_COMPLETED_SENT_ACTIVATION_CODE');?></p>
	<?php } else { ?>
		<p><?php echo JText::_('COM_EASYSOCIAL_REGISTRATION_ACTIVATION_PLEASE_PROVIDE_ACTIVATION_CODE'); ?></p>
	<?php } ?>

	<form class="es-login-form es-verify-form" action="<?php echo JRoute::_( 'index.php' );?>" method="post">
		<fieldset class="">
			<input type="text" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_ACTIVATION_CODE_PLACEHOLDER' );?>" name="token" class="full-width" />

			<button class="btn btn-es-primary btn-login btn-large btn-block mt-10" type="submit">
				<?php echo JText::_('COM_EASYSOCIAL_ACTIVATE_ACCOUNT_BUTTON');?>
			</button>
		</fieldset>

		<input type="hidden" name="userid" value="<?php echo $user ? $user->id : '';?>" />
		<input type="hidden" name="option" value="com_easysocial" />
		<input type="hidden" name="controller" value="registration" />
		<input type="hidden" name="task" value="activate" />
	</form>
</div>
