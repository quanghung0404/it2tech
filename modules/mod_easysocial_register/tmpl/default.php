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
<div id="fd" class="es mod-es-register module-register<?php echo $suffix;?> es-responsive" data-guest-register>

	<?php echo FD::info()->toHTML();?>

	<form name="registration" method="post" action="<?php echo JRoute::_( 'index.php' );?>" data-registermini-form>
		<div class="quick-register clearfix<?php echo $params->get( 'splash_image' , true ) ? ' has-splash' : '';?>">

			<?php if( $params->get( 'splash_image' , true ) ){ ?>
			<div class="quick-register-splash">
				<div class="splash-image" style="background-image:url(<?php echo $splashImage;?>);"></div>

				<div class="splash-header">
					<h2><?php echo JText::_( $params->get( 'splash_image_title' , 'MOD_EASYSOCIAL_REGISTER_SPLASH_TITLE_JOIN_US_TODAY' ) );?></h2>
				</div>

				<div class="splash-footer">
					<?php echo JText::_( $params->get( 'splash_footer_content' , 'MOD_EASYSOCIAL_REGISTER_SPLASH_FOOTER_CONTENT' ) ); ?>
				</div>
			</div>
			<?php } ?>

			<div class="quick-register-form">
				<?php if( $params->get( 'show_heading_title' , true ) || $params->get( 'show_heading_desc' , true ) ){ ?>
				<div class="text-center">
					<?php if( $params->get( 'show_heading_title' , true ) ){ ?>
					<h3>
						<?php echo $params->get( 'heading_title' , JText::_( 'MOD_EASYSOCIAL_REGISTER_DONT_HAVE_ACCOUNT' ) ); ?>
					</h3>
					<?php } ?>

					<?php if( $params->get( 'show_heading_desc' , true ) ){ ?>
					<p class="center mb-20">
						<?php echo $params->get( 'heading_desc' , JText::_( 'MOD_EASYSOCIAL_REGISTER_NOW_TO_JOIN' ) );?>
					</p>
					<?php } ?>
				</div>
				<hr />
				<?php } ?>

				<?php if (!empty($fields)) { ?>
					<?php foreach ($fields as $field) { ?>
						<?php if (isset($field->output)) { ?>
						<div class="register-field" data-registermini-fields-item><?php echo $field->output; ?></div>
						<?php } ?>
					<?php } ?>
				<?php } ?>

				<button class="btn btn-es-primary btn-block mb-20" type="button" data-registermini-submit><?php echo JText::_( 'MOD_EASYSOCIAL_REGISTER_REGISTER_NOW_BUTTON' );?> &rarr;</button>

				<?php if( $params->get('social', true) && $config->get('oauth.facebook.registration.enabled') && $config->get('registrations.enabled')
							&& (
								($config->get('oauth.facebook.secret') && $config->get( 'oauth.facebook.app' ))
								|| ($config->get('oauth.facebook.jfbconnect.enabled'))
							)
						){ ?>
					<hr />
					<div class="text-center mb-10">
						<p class="line">
							<strong><?php echo JText::_( 'MOD_EASYSOCIAL_REGISTER_OR_REGISTER_WITH_YOUR_SOCIAL_IDENTITY' );?></strong>
						</p>

						<?php echo FD::oauth( 'Facebook' )->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ), false, 'popup', JText::_('MOD_EASYSOCIAL_REGISTER_REGISTER_WITH_YOUR_FACEBOOK_ACCOUNT') ); ?>
					</div>
				<?php } ?>
			</div>
		</div>

		<?php echo $modules->html( 'form.token' ); ?>
		<input type="hidden" name="redirect" value="<?php echo base64_encode( JRequest::getURI() );?>" />
		<input type="hidden" name="option" value="com_easysocial" />
		<input type="hidden" name="controller" value="registration" />
		<input type="hidden" name="task" value="miniRegister" />
		<input type="hidden" name="modRegisterType" value="<?php echo $registerType; ?>" />
		<input type="hidden" name="modRegisterProfile" value="<?php echo $profileId; ?>" />
	</form>

</div>
