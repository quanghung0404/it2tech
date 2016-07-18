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
$useSSL = $params->get('use_secure_url', false) ? 1 : 0;
?>
<?php if( !$my->id ){ ?>
<div id="fd" class="es mod-es-login style-vertical module-social<?php echo $suffix;?>">
	<div class="mod-bd mt-10">
		<div class="es-form-wrap">
			<form class="es-form-login" method="post" action="<?php echo JRoute::_('index.php', true, $useSSL);?>">
				<fieldset>
					<input type="text" placeholder="<?php echo $config->get( 'registrations.emailasusername' ) ? JText::_( 'MOD_EASYSOCIAL_LOGIN_EMAIL_PLACEHOLDER' ) : JText::_( 'MOD_EASYSOCIAL_LOGIN_USERNAME_PLACEHOLDER' );?>" name="username" class="form-control input-xs mb-10" />

					<input type="password" placeholder="<?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER' );?>" name="password" class="form-control input-xs mb-10" />

					<?php if ($config->get('general.site.twofactor')) { ?>
					<input type="text" class="form-control input-xs" name="secretkey" placeholder="<?php echo JText::_('MOD_EASYSOCIAL_LOGIN_TWOFACTOR_SECRET_PLACEHOLDER', true);?>" />
					<?php } ?>

					<?php if ($params->get('show_remember_me' , true)) { ?>
					<div class="remember<?php echo $params->get('remember_me_style') == 'hidden' || $params->get('remember_me_style') == 'hidden_checked' ? ' hide' : '';?>">
						<div class="checkbox">
							<label for="remember-me">
								<input type="checkbox" id="remember-me" name="remember" value="yes"
									<?php echo $params->get('remember_me_style', 'visible_checked') == 'visible_checked' || $params->get('remember_me_style', 'visible_checked') == 'hidden_checked' ? 'checked="checked"' : '';?>/> <?php echo JText::_('MOD_EASYSOCIAL_LOGIN_KEEP_ME_LOGGED_IN');?>
							</label>
						</div>
					</div>
					<?php } ?>	

					<button type="submit" class="btn btn-block btn-es-primary btn-login"><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_SUBMIT' );?></button>

					<?php if( $params->get( 'show_register_link' , true ) ){ ?>
					<div class="help-block mt-10 fd-small">
						<a href="<?php echo FRoute::registration();?>"><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_REGISTER_NOW' );?></a>
					</div>
					<?php } ?>


					<?php if( $params->get( 'show_forget_username' , true ) && !$config->get( 'registrations.emailasusername' ) ){ ?>
					<div class="help-block mt-10 fd-small">
						<a href="<?php echo FRoute::account( array( 'layout' => 'forgetUsername' ) );?>"><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_FORGOT_USERNAME' );?></a>
					</div>
					<?php } ?>

					<?php if( $params->get( 'show_forget_password' , true ) ){ ?>
					<div class="help-block mt-5 fd-small">
						<a href="<?php echo FRoute::account( array( 'layout' => 'forgetPassword' ) );?>"><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_FORGOT_PASSWORD' );?></a>
					</div>
					<?php } ?>

					<?php if( $params->get('show_facebook_login') && $config->get('oauth.facebook.registration.enabled') && $config->get('registrations.enabled')
							&& (
								($config->get('oauth.facebook.secret') && $config->get( 'oauth.facebook.app' ))
								|| ($config->get('oauth.facebook.jfbconnect.enabled'))
							)
						){ ?>
					<div class="center es-signin-social">
						<p class="line">
							<strong><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_SIGN_IN_WITH_SOCIAL_IDENTITY' );?></strong>
						</p>

						<?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ) ); ?>
					</div>
					<?php } ?>

				</fieldset>

				<input type="hidden" name="option" value="com_easysocial" />
				<input type="hidden" name="controller" value="account" />
				<input type="hidden" name="task" value="login" />
				<input type="hidden" name="return" value="<?php echo $return;?>" />
				<?php echo $modules->html( 'form.token' );?>

			</form>
		</div>
	</div>
</div>
<?php } else { ?>
<div id="fd" class="es mod-es-login style-vertical module-social<?php echo $suffix;?>">
	<form action="<?php echo JRoute::_( 'index.php' );?>" id="es-mod-login-signout-form" method="post">
		<div class="text-center">
			<a href="javascript:void(0);" onclick="document.getElementById( 'es-mod-login-signout-form' ).submit();" class="btn btn-primary">
				<?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_SIGN_OUT' );?>
			</a>
		</div>

		<input type="hidden" name="option" value="com_easysocial" />
		<input type="hidden" name="controller" value="account" />
		<input type="hidden" name="task" value="logout" />
		<input type="hidden" name="return" value="<?php echo base64_encode(FRoute::getMenuLink(FD::config()->get('general.site.logout'))); ?>" />
		<?php echo $modules->html( 'form.token' ); ?>
	</form>
</div>
<?php } ?>
