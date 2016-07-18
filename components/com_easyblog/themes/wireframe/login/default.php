<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="login" class="eb-login text-center eb-responsive">
	<h3 class="eb-login-title reset-heading mb-15">
		<?php echo JText::_('COM_EASYBLOG_MEMBER_LOGIN');?>
	</h3>
	<p class="eb-desp">
		<?php echo JText::_('COM_EASYBLOG_MEMBER_LOGIN_INFO');?>
	</p>

	<div class="form-group">
		<input type="text" class="form-control" id="username" name="username" placeholder="<?php echo JText::_('COM_EASYBLOG_USERNAME') ?>" />
	</div>

	<div class="form-group">
		<input id="passwd" type="password" name="password" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_PASSWORD') ?>" />
	</div>

	<div class="eb-login-footer row-table">
		<?php if(JPluginHelper::isEnabled('system', 'remember')) { ?>
		<div class="col-cell text-left">
			<div class="eb-checkbox">
				<input id="eb-remember" type="checkbox" name="remember" value="yes" alt="<?php echo JText::_('COM_EASYBLOG_REMEMBER_ME', true) ?>"/>
				<label for="eb-remember">
					<?php echo JText::_('COM_EASYBLOG_REMEMBER_ME') ?>
				</label>
			</div>
		</div>
		<?php } ?>
		
		<div class="col-cell text-right">
			<button type="submit" class="btn btn-primary"><?php echo JText::_('COM_EASYBLOG_LOGIN_BUTTON') ?></button>
		</div>
	</div>

	<hr>

	<div class="eb-login-help row-table">
		<div class="col-cell">
			<a class="btn btn-block btn-default" href="<?php echo EB::getResetPasswordLink();?>"><?php echo JText::_('COM_EASYBLOG_FORGOT_YOUR_PASSWORD'); ?></a>
		</div>
		<div class="col-cell">
			<a class="btn btn-block btn-default" href="<?php echo EB::getRemindUsernameLink(); ?>"><?php echo JText::_('COM_EASYBLOG_FORGOT_YOUR_USERNAME'); ?></a>
		</div>
	</div>

	<?php if (EB::isRegistrationEnabled()) { ?>
	<a href="<?php echo EB::getRegistrationLink();?>" class="btn btn-block btn-success"><?php echo JText::_('COM_EASYBLOG_CREATE_AN_ACCOUNT'); ?> <i class="fa fa-angle-right"></i></a>
	<?php } ?>
	
	<input type="hidden" value="com_users"  name="option">
	<input type="hidden" value="user.login" name="task">
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo $this->html('form.token'); ?>

	<?php if ($this->config->get('integrations_jfbconnect_login')) { ?>
		{JFBCLogin}
	<?php } ?>
</form>
