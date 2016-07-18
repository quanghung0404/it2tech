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
<li class="toolbarItem toolbar-login" data-toolbar-login
    data-popbox
    data-popbox-id="fd"
    data-popbox-component="es"
    data-popbox-type="toolbar"
    data-popbox-toggle="click"
    data-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
    data-popbox-target=".toobar-profile-popbox"
>
	<a href="javascript:void(0);" data-es-provide="tooltip" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_LOGIN' , true );?>" data-placement="top" class="dropdown-toggle_ loginLink">
		<i class="fa fa-lock"></i>
		<span class="visible-phone"><?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_LOGIN' );?></span>
	</a>

	<div style="display:none;" class="toobar-profile-popbox" data-toolbar-login-dropdown>
		<div class="popbox-dropdown-menu dropdown-menu-login loginDropDown" style="display: block;">
			<form action="<?php echo JRoute::_( 'index.php' );?>" method="post">
			<ul class="fd-reset-list">
				<li class="pb-0">
					<?php if( ( $this->config->get( 'registrations.enabled' ) && $this->config->get('general.site.lockdown.enabled') && $this->config->get('general.site.lockdown.registration') )
								|| ( $this->config->get( 'registrations.enabled' ) && !$this->config->get('general.site.lockdown.enabled') )
							){ ?>
					<a href="<?php echo FRoute::registration();?>" class="fd-small pull-right es-dropdownmenu-btn-register" tabindex="106"><?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_REGISTER' );?></a>
					<?php } ?>

					<label for="es-username" class="fd-small">
					<?php if( $this->config->get( 'registrations.emailasusername' ) ) {
						echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_EMAIL' );
					} else {
						echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_LOGIN_NAME' );
					} ?>
					</label>
					<input type="text" autocomplete="off" size="18" class="form-control input-sm" name="username" id="es-username" tabindex="101">
				</li>
				<li class="pt-0 pb-0">
					<label for="es-password" class="fd-small">
						<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PASSWORD' );?>
					</label>

					<input type="password" autocomplete="off" name="password" class="form-control input-sm" id="es-password" tabindex="102">
				</li>
				<?php if ($this->config->get('general.site.twofactor')) { ?>
				<li class="pt-0 pb-0">
					<label for="es-secretkey" class="fd-small">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_SECRET'); ?>
					</label>

					<input type="text" autocomplete="off" name="secretkey" class="form-control input-sm" id="es-secretkey" tabindex="103">
				</li>
				<?php } ?>
				<li>
					<span class="pull-left">
						<span class="checkbox mt-0">
							<input type="checkbox" value="yes" class="pull-left mr-5" name="remember" id="remember" tabindex="104">
							<label class="fd-small pull-left" for="remember">
								<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_REMEMBER_ME' );?>
							</label>
						</span>

					</span>
					<input type="submit" class="btn btn-es-success pull-right btn-sm" name="Submit" value="<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_BUTTON' , true );?>" tabindex="105">
				</li>
				<?php if ($this->config->get('oauth.facebook.registration.enabled') && $facebook){ ?>
				<li class="item-social text-center">
					<?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ) ); ?>
				</li>
				<?php } ?>
			</ul>


			<div class="dropdown-menu-footer">
				<ul class="fd-reset-list">
					<?php if( ( $this->config->get( 'registrations.enabled' ) && $this->config->get('general.site.lockdown.enabled') && $this->config->get('general.site.lockdown.registration') )
						|| ( $this->config->get( 'registrations.enabled' ) && !$this->config->get('general.site.lockdown.enabled') )
					){ ?>
					<li>
						<i class="fa fa-plus"></i>  <a href="<?php echo FRoute::registration();?>" class="pull-" tabindex="107"><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_CREATE_NEW_ACCOUNT' );?></a>
					</li>
					<?php } ?>
					<?php if( !$this->config->get( 'registrations.emailasusername' ) ) { ?>
					<li>
						<i class="fa fa-life-saver"></i>  <a href="<?php echo FRoute::account( array( 'layout' => 'forgetUsername' ) );?>" class="pull-" tabindex="108"><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_FORGOT_USERNAME' );?></a>
					</li>
					<?php } ?>
					<li>
						<i class="fa fa-life-saver"></i>  <a href="<?php echo FRoute::account( array( 'layout' => 'forgetPassword' ) );?>" class="pull-" tabindex="109"><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_FORGOT_PASSWORD' );?></a>
					</li>
				</ul>
			</div>

				<input type="hidden" name="option" value="com_easysocial" />
				<input type="hidden" name="controller" value="account" />
				<input type="hidden" name="task" value="login" />
				<input type="hidden" name="return" value="<?php echo $loginReturn;?>" />
				<?php echo $this->html( 'form.token' );?>
			</form>
		</div>
	</div>
</li>
