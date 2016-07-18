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
<div id="fd" class="es mod-es-login es-responsive style-horizontal module-social<?php echo $suffix;?>">
	<div class="mod-bd mt-10">
		<div class="es-form-wrap">
			<form class="es-form-login" method="post" action="<?php echo JRoute::_('index.php', true, $useSSL);?>">
				<fieldset>

					<div class="input-area">
                        <div class="input-area-cell">
                            <div class="form-inline">
                                <i class="fa fa-user"></i>
                                <input type="text" placeholder="<?php echo $config->get( 'registrations.emailasusername' ) ? JText::_( 'MOD_EASYSOCIAL_LOGIN_EMAIL_PLACEHOLDER' ) : JText::_( 'MOD_EASYSOCIAL_LOGIN_USERNAME_PLACEHOLDER' );?>" name="username" class="form-control" />

                                <?php if( $params->get( 'show_forget_username' , true ) ){ ?>
                                <a href="<?php echo FRoute::account( array( 'layout' => 'forgetUsername' ) );?>" class="btn btn-es btn-es-inverse"><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_FORGOT_USERNAME_HORIZONTAL' );?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="input-area-cell">
                            <div class="form-inline">
                                <i class="fa fa-lock"></i>
                                <input type="password" placeholder="<?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER' );?>" name="password" class="form-control" />

                                <?php if( $params->get( 'show_forget_password' , true ) ){ ?>
                                <a href="<?php echo FRoute::account( array( 'layout' => 'forgetPassword' ) );?>" class="btn btn-es btn-es-inverse"><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_FORGOT_PASSWORD_HORIZONTAL' );?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($config->get('general.site.twofactor')) { ?>
                            <div class="input-area-cell">
                                <div class="form-inline es-secret-key">
                                    <i class="fa fa-key"></i>
                                    <input type="input" placeholder="<?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_TWOFACTOR_SECRET_PLACEHOLDER' );?>" name="secretkey" class="form-control" />
                                </div>
                            </div>
                        <?php } ?>
                        <div class="input-area-cell">
                            <button type="submit" class="btn btn-es-primary btn-login"><?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_SUBMIT' );?> &rarr;</button>
                        </div>
                        <?php if( $params->get( 'show_register_link' ) ){ ?>
                            <div class="input-area-cell">
                                <a href="<?php echo FRoute::registration();?>" class="btn btn-es btn-register"><i class="fa fa-list-alt"></i>&nbsp; <?php echo JText::_( 'MOD_EASYSOCIAL_HORIZONTAL_LOGIN_REGISTER' );?></a>
                            </div>

                        <?php } ?>


					</div>

                    <?php if( $params->get('show_facebook_login') && $config->get('oauth.facebook.registration.enabled') && $config->get('registrations.enabled')
                            && (
                                ($config->get('oauth.facebook.secret') && $config->get( 'oauth.facebook.app' ))
                                || ($config->get('oauth.facebook.jfbconnect.enabled'))
                            )
                        ){ ?>
                        <div class="mt-5">
                        <?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ) ); ?>
                        </div>
                    <?php } ?>
					<?php if( $params->get( 'show_remember_me' , true ) ){ ?>
					<div class="remember<?php echo $params->get( 'remember_me_style' ) == 'hidden' || $params->get( 'remember_me_style' ) == 'hidden_checked' ? ' hide' : '';?>">
						<div class="checkbox">
							<label for="remember-me">
								<input type="checkbox" id="remember-me" name="remember" value="yes"
									<?php echo $params->get( 'remember_me_style' , 'visible_checked' ) == 'visible_checked' || $params->get( 'remember_me_style' , 'visible_checked' ) == 'hidden_checked' ? 'checked="checked"' : '';?>/> <?php echo JText::_( 'MOD_EASYSOCIAL_LOGIN_KEEP_ME_LOGGED_IN' );?>
							</label>
						</div>
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
<div id="fd" class="es mod-es-login es-responsive style-horizontal module-social<?php echo $suffix;?>">
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
