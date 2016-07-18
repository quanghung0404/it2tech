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
<div id="fd" class="es mod-es-logbox module-logbox<?php echo $suffix;?>">
	<div class="es-login-box es-responsive mt-20">
		<div class="row">
			<div class="col-md-6">
				<div class="pl-20 pr-20">
					<form name="loginbox" id="loginbox" method="post" action="<?php echo JRoute::_('index.php');?>">
						<div class="login-box-title"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_TO_ACCOUNT_TITLE' );?></div>
						<fieldset class="mt-20">
							<div class="form-group">
								<input type="text" class="form-control input-sm" name="username" placeholder="<?php echo $config->get( 'registrations.emailasusername' ) ? JText::_( 'COM_EASYSOCIAL_LOGIN_EMAIL_PLACEHOLDER', true ) : JText::_( 'COM_EASYSOCIAL_LOGIN_USERNAME_PLACEHOLDER' , true );?>" />
							</div>

							<div class="form-group">
								<input type="password" class="form-control input-sm" name="password" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER' , true );?>" />
							</div>

							<?php if ($config->get('general.site.twofactor')) { ?>
							<div class="form-group">
								<input type="text" class="form-control input-sm" name="secretkey" placeholder="<?php echo JText::_('COM_EASYSOCIAL_LOGIN_TWOFACTOR_SECRET', true);?>" />
							</div>
							<?php } ?>


							<?php if( $params->get( 'show_remember_me' , true ) ){ ?>
							<div class="checkbox">
							<label class="fd-small mt-10">
								<input type="checkbox" name="remember"> <span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REMEMBER_YOU' );?></span>
							</label>
							</div>
							<?php } ?>

							<button type="submit" class="btn btn-es-success btn-block mt-20">
								<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_TO_ACCOUNT_BUTTON' );?>
							</button>
						</fieldset>

						<?php if( $params->get('show_facebook_login') && $config->get('oauth.facebook.registration.enabled') && $config->get('registrations.enabled')
								&& (
									($config->get('oauth.facebook.secret') && $config->get( 'oauth.facebook.app' ))
									|| ($config->get('oauth.facebook.jfbconnect.enabled'))
								)
							){ ?>
						<div class="text-center es-signin-social">
							<p class="line">
								<strong><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_SIGNIN_SOCIAL' );?></strong>
							</p>

							<?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ) ); ?>
						</div>
						<?php } ?>

						<hr />

						<div class="text-center">
							<?php if( $params->get( 'show_forget_username' , true ) ){ ?>
							<a class="text-error" href="<?php echo FRoute::account( array( 'layout' => 'forgetUsername' ) );?>"> <?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_FORGOT_USERNAME' );?></a> /
							<?php } ?>

							<?php if( $params->get( 'show_forget_password' , true ) ){ ?>
							<a class="text-error" href="<?php echo FRoute::account( array( 'layout' => 'forgetPassword' ) );?>"> <?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_FORGOT_PASSWORD' );?></a>
							<?php } ?>
						</div>

						<input type="hidden" name="option" value="com_easysocial" />
						<input type="hidden" name="controller" value="account" />
						<input type="hidden" name="task" value="login" />
						<input type="hidden" name="return" value="<?php echo $return; ?>" />
						<?php echo $modules->html( 'form.token' );?>
					</form>
				</div>
			</div>

			<?php if( ( $config->get( 'registrations.enabled' ) && $config->get('general.site.lockdown.enabled') && $config->get('general.site.lockdown.registration') )
						|| ( $config->get( 'registrations.enabled' ) && !$config->get('general.site.lockdown.enabled') )
					){ ?>
			<div class="col-md-6">
				<div class="pl-20 pr-20 modal-es-register" data-logbox-miniform>
					<form method="post" action="<?php echo JRoute::_( 'index.php' );?>" data-registermini-form>
						<div class="register-wrap <?php echo empty( $fields ) ? ' is-empty' : '';?>">
							<div class="login-box-title"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_NO_ACCOUNT' );?></div>
							<p class="text-center mb-20">
								<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW' );?>
							</p>


							<?php if( $params->get('show_quick_registration') && !empty( $fields ) ) { ?>
								<?php foreach( $fields as $field ) { ?>
									<div class="register-field" data-registermini-fields-item><?php echo $field->output; ?></div>
								<?php } ?>
							<?php } ?>

							<div class="clearfix">
								<?php if( $params->get('show_quick_registration') && !empty( $fields ) ) { ?>
									<button class="btn btn-es-primary btn-sm btn-register" type="button" data-registermini-submit><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW_BUTTON' );?></button>
								<?php } else { ?>
								<a href="<?php echo FRoute::registration();?>" class="btn btn-es-primary btn-sm btn-register"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW_BUTTON' );?></a>
								<?php } ?>
							</div>

							<?php if( $config->get( 'oauth.facebook.registration.enabled' ) && $config->get( 'registrations.enabled' )
									&& $config->get( 'oauth.facebook.secret' )
									&& $config->get( 'oauth.facebook.app' )
								){ ?>
								<div class="text-center es-signin-social">
									<p class="line">
										<strong><?php echo JText::_( 'COM_EASYSOCIAL_OR_REGISTER_WITH_YOUR_SOCIAL_IDENTITY' );?></strong>
									</p>

									<?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ), false, 'popup', JText::_('COM_EASYSOCIAL_REGISTER_WITH_YOUR_FACEBOOK_ACCOUNT') ); ?>
								</div>
							<?php } ?>
						</div>

						<input type="hidden" name="option" value="com_easysocial" />
						<input type="hidden" name="controller" value="registration" />
						<input type="hidden" name="task" value="miniRegister" />
						<?php echo $modules->html( 'form.token' );?>
					</form>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
