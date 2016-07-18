<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION' );?></b>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="registration_type" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_TYPE' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_TYPE' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_TYPE_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-7">
						<select name="params[registration]" class="registrationType form-control input-sm">
							<option value="approvals"<?php echo $param->get('registration') == 'approvals' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_REQUIRE_APPROVALS' ); ?></option>
							<option value="verify"<?php echo $param->get('registration') == 'verify' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_REQUIRE_SELF_ACTIVATION' ); ?></option>
							<option value="auto"<?php echo $param->get('registration') == 'auto' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_REQUIRE_AUTO_LOGIN' ); ?></option>
							<option value="login"<?php echo $param->get('registration') == 'login' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_NORMAL' ); ?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="registration_type" class="col-md-5">
						<?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SUCCESS_REDIRECTION');?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SUCCESS_REDIRECTION'), JText::_('COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SUCCESS_REDIRECTION_DESC'), 'bottom'); ?>
						></i>
					</label>
					<div class="col-md-7">
						<?php echo $this->html('form.menus', 'params[registration_success]', $param->get('registration_success'), array(JText::_('COM_EASYSOCIAL_USERS_SETTINGS_MENU_GROUP_CORE') => array(JHtml::_('select.option', 'null', JText::_('COM_EASYSOCIAL_DEFAULT_BEHAVIOR'))))); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_OAUTH_REGISTRATION');?></b>
				<p><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_OAUTH_REGISTRATION_DESC');?></p>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="registration_type" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_TYPE' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_TYPE' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_TYPE_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-7">
						<select name="params[oauth.registration]" class="registrationType form-control input-sm">
							<option value="approvals"<?php echo $param->get('oauth.registration') == 'approvals' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_REQUIRE_APPROVALS' ); ?></option>
							<option value="verify"<?php echo $param->get('oauth.registration') == 'verify' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_REQUIRE_SELF_ACTIVATION' ); ?></option>
							<option value="auto"<?php echo $param->get('oauth.registration') == 'auto' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_REQUIRE_AUTO_LOGIN' ); ?></option>
							<option value="login"<?php echo $param->get('oauth.registration') == 'login' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_NORMAL' ); ?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_EMAILS_TITLE' );?></b>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SEND_EMAILS_USER' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SEND_EMAILS_USER' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SEND_EMAILS_USER_DESC' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-7">
						<?php echo $this->html( 'grid.boolean' , 'params[email.users]' , $param->get( 'email.users' , true ) , '' , array() ); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SEND_EMAILS_ADMIN' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SEND_EMAILS_ADMIN' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_REGISTRATION_SEND_EMAILS_ADMIN_DESC' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-7">
						<?php echo $this->html( 'grid.boolean' , 'params[email.moderators]' , $param->get( 'email.moderators' , true ) , '' , array() ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
