<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal" enctype="multipart/form-data" data-eb-dashboard-account>
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-user"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_YOUR_ACCOUNT');?>
		</h2>
	</div>

	<?php echo EB::info()->html();?>

	<?php if ($this->config->get('main_dashboard_editaccount')) { ?>
		<?php echo $this->output('site/dashboard/account/profile'); ?>
	<?php } ?>

	<?php if ($this->acl->get('add_entry') ) { ?>
		<?php echo $this->output('site/dashboard/account/blog'); ?>
	<?php } ?>

	<?php if ($this->config->get('main_facebook_ogauthor') || ($this->acl->get('update_facebook') && $this->config->get('integrations_facebook') && $this->config->get('integrations_facebook_centralized_and_own'))) { ?>
	<?php echo $this->output('site/dashboard/account/facebook'); ?>
	<?php } ?>

	<?php if ($this->acl->get('update_twitter') && $this->config->get('integrations_twitter') && $this->config->get('integrations_twitter_centralized_and_own')) {?>
	<?php echo $this->output('site/dashboard/account/twitter'); ?>
	<?php } ?>

	<?php if ($this->acl->get('update_linkedin') && $this->config->get('integrations_linkedin') && $this->config->get('integrations_linkedin_centralized_and_own')) {?>
	<?php echo $this->output('site/dashboard/account/linkedin'); ?>
	<?php } ?>

	<?php if ($this->acl->get('add_entry') && $this->acl->get('allow_seo') ){ ?>
	<?php echo $this->output('site/dashboard/account/metas'); ?>
	<?php } ?>

	<?php if ($this->config->get('main_google_profiles') && $this->acl->get('add_google_authorship')) { ?>
	<?php echo $this->output('site/dashboard/account/authorship'); ?>
	<?php } ?>

	<?php if ($this->config->get('integrations_google_adsense_blogger') && $this->acl->get('add_adsense') && $this->config->get('integration_google_adsense_enable')) { ?>
	<?php echo $this->output('site/dashboard/account/adsense'); ?>
	<?php } ?>

	<?php if ($this->config->get('main_feedburner') && $this->config->get('main_feedburnerblogger') && $this->acl->get('allow_feedburner') ) { ?>
	<?php echo $this->output('site/dashboard/account/feedburner'); ?>
	<?php } ?>

	<div class="form-actions ">
		<div class="pull-right">
			<button class="btn btn-primary">
				<i class="fa fa-save"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SAVE_PROFILE'); ?>
			</button>
		</div>
	</div>

	<?php echo $this->html('form.action', 'profile.save'); ?>
</form>
