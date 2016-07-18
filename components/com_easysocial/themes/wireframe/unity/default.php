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
<div class="row">
	<div class="col-md-12">
		<div class="es-unity">
			<?php echo $this->render( 'module' , 'es-unity-top' ); ?>

			<?php if( !$this->my->id ){ ?>
			<div class="es-login-box">
				<div class="row">
					<div class="col-md-6 login-column">
						<div class="es-login-wrap">
							<form name="loginbox" id="loginbox" method="post" action="<?php echo JRoute::_( 'index.php' );?>">
								<legend class="mt-20"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_TO_ACCOUNT_TITLE' );?></legend>
								<fieldset class="mt-20">
									<div class="form-group">
										<input type="text" class="form-control input-sm" name="username" placeholder="<?php echo $this->config->get( 'registrations.emailasusername' ) ? JText::_( 'COM_EASYSOCIAL_LOGIN_EMAIL_PLACEHOLDER', true ) : JText::_( 'COM_EASYSOCIAL_LOGIN_USERNAME_PLACEHOLDER' , true );?>" />
									</div>

									<div class="form-group">
										<input type="password" class="form-control input-sm" name="password" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER' , true );?>" />
									</div>

									<label class="checkbox fd-small mt-10">
										<input type="checkbox" name="remember"> <span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REMEMBER_YOU' );?></span>
									</label>

									<button type="submit" class="btn btn-es-success btn-block mt-20">
										<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_TO_ACCOUNT_BUTTON' );?>
									</button>
								</fieldset>

								<?php if( $this->config->get( 'oauth.facebook.registration.enabled' ) && $this->config->get( 'registrations.enabled' ) ){ ?>
								<div class="center es-signin-social">
									<p class="line">
										<strong><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_SIGNIN_SOCIAL' );?></strong>
									</p>

									<?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ) ); ?>
								</div>
								<?php } ?>

								<hr />

								<div class="center">
									<?php if ($this->config->get('registrations.emailasusername')) { ?>
									<a class="text-error" href="<?php echo FRoute::account(array('layout' => 'forgetPassword')); ?>"> <?php echo JText::_('COM_EASYSOCIAL_LOGIN_FORGOT_PASSWORD_FULL'); ?></a>
									<?php } else { ?>
									<a class="text-error" href="<?php echo FRoute::account( array( 'layout' => 'forgetUsername' ) );?>"> <?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_FORGOT_USERNAME' );?></a> /
									<a class="text-error" href="<?php echo FRoute::account( array( 'layout' => 'forgetPassword' ) );?>"> <?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_FORGOT_PASSWORD' );?></a>
									<?php } ?>
								</div>

								<input type="hidden" name="option" value="com_easysocial" />
								<input type="hidden" name="controller" value="account" />
								<input type="hidden" name="task" value="login" />
								<input type="hidden" name="return" value="<?php echo $return; ?>" />
								<input type="hidden" name="returnFailed" value="<?php echo base64_encode( JRequest::getURI() ); ?>" />
								<?php echo $this->html( 'form.token' );?>
							</form>
						</div>
					</div>

					<?php if( $this->config->get( 'registrations.enabled' ) ){ ?>
					<div class="col-md-6 register-column">
						<form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
							<div class="register-wrap <?php echo empty( $fields ) ? ' is-empty' : '';?>">
								<h3><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_NO_ACCOUNT' );?></h3>
								<p class="center mb-20">
									<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW' );?>
								</p>

								<?php if( !empty( $fields ) ) { ?>
									<?php foreach( $fields as $field ) { ?>
										<div class="register-field"><?php echo $field->output; ?></div>
									<?php } ?>
								<?php } ?>

								<button class="btn btn-es-success btn-lg btn-register"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW_BUTTON' );?></button>

							</div>

							<input type="hidden" name="option" value="com_easysocial" />
							<input type="hidden" name="controller" value="registration" />
							<input type="hidden" name="task" value="miniRegister" />
						</form>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<div class="es-unity-happen">
				<div class="es-unity-title">
					<span><?php echo JText::_( 'COM_EASYSOCIAL_UNITY_HEADING_WHATS_GOING_ON' );?></span>
				</div>

				<div class="row mt-20">

					<div class="col-md-4">
						<?php echo $this->render( 'module' , 'es-unity-sidebar-top' , 'site/unity/sidebar.module.wrapper' , array( 'style' => 'es-widget' ) ); ?>

						<?php echo $this->render( 'module' , 'es-unity-sidebar-bottom' , 'site/unity/sidebar.module.wrapper' , array( 'style' => 'es-widget' ) ); ?>
					</div>

					<div class="col-md-8">

						<?php echo $this->render( 'module' , 'es-unity-content-top' , null , array( 'style' => 'es-widget' ) ); ?>

						<div class="es-content">

							<div data-unity-real-content>
								<?php echo $stream->html( false, $empty ); ?>

								<?php if( FD::user()->id == 0 ) { ?>
									<div class="pull-right">
										<a href="<?php echo $readmoreURL; ?>"><?php echo $readmoreText; ?></a>
									</div>
								<?php } ?>
							</div>
						</div>

						<?php echo $this->render( 'module' , 'es-unity-content-bottom' ); ?>

					</div>
				</div>
			</div>


		</div>
	</div>
</div>
