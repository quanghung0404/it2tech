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
<form name="createOauth" action="<?php echo JRoute::_( 'index.php' );?>" method="post" data-oauth-preferences>
	<div class="row vertical-line vertical-line-50 mt-20">
		<div class="col-md-6">
			<div class="pr-15">
				<div class="form-horizontal">
					<?php if (!$this->config->get('registrations.emailasusername')) { ?>
					<div class="control-group">
						<label for="oauth-username"><?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_USERNAME' );?>:</label>
						<input name="oauth-username" type="text" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_USERNAME_PLACEHOLDER' , true );?>" id="oauth-username" class="form-control input-sm" value="<?php echo $username;?>" />
						<?php if( $usernameExists ){ ?>
						<div class="error small mt-5">
							<?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_USERNAME_ERRORS' ); ?>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<div class="control-group mt-20<?php echo $emailExists ? ' error' : '';?>">
						<label for="oauth-email"><?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_EMAIL' );?>:</label>
						<input name="oauth-email" type="text" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_EMAIL_PLACEHOLDER' , true );?>" id="oauth-email" class="form-control input-sm" value="<?php echo $email;?>" />

						<?php if( $emailExists || ($this->config->get('registrations.emailasusername') && $usernameExists) ){ ?>
						<p class="small text-error">
							<?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_EMAIL_ERRORS' ); ?>
						</p>
						<?php } ?>
					</div>
					<div class="control-group mt-20>">
						<label for="oauth-password"><?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_PASSWORD' );?>:</label>
						<input name="oauth-password" type="password" id="oauth-password" class="form-control input-sm" value="" />
						<div class="small mt-10"><?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_REGISTRATION_PASSWORD_NOTE' );?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-horizontal ml-10">
				<div class="control-group">
					<label class="fd-small mb-5"><?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_IMPORT_AVATAR_' . strtoupper( $clientType ) );?></label>
					<?php echo $this->html( 'grid.boolean' , 'import' , true ); ?>
				</div>
				<div class="control-group mt-20">
					<label class="fd-small mb-5"><?php echo JText::_( 'COM_EASYSOCIAL_OAUTH_SYNC_' . strtoupper( $clientType ) );?></label>
					<?php echo $this->html( 'grid.boolean' , 'stream' , true ); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button class="btn btn-es-primary btn-sm pull-right"><?php echo JText::_( 'COM_EASYSOCIAL_COMPLETE_REGISTRATION_BUTTON' );?></button>
	</div>
	<?php echo $this->html( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="controller" value="registration" />
	<input type="hidden" name="task" value="oauthCreateAccount" />
	<input type="hidden" name="client" value="<?php echo $clientType;?>" />
	<input type="hidden" name="profile" value="<?php echo $profileId;?>" />
	<?php echo $this->html( 'form.itemid' ); ?>
</form>
