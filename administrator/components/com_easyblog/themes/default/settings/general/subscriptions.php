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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_INFO'); ?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SITE_SUBSCRIPTIONS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SITE_SUBSCRIPTIONS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SITE_SUBSCRIPTIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_sitesubscription', $this->config->get('main_sitesubscription'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOG_SUBSCRIPTIONS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOG_SUBSCRIPTIONS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOG_SUBSCRIPTIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_subscription', $this->config->get('main_subscription'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOGGER_SUBSCRIPTIONS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOGGER_SUBSCRIPTIONS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOGGER_SUBSCRIPTIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_bloggersubscription', $this->config->get('main_bloggersubscription'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_CATEGORY_SUBSCRIPTIONS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_CATEGORY_SUBSCRIPTIONS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_CATEGORY_SUBSCRIPTIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_categorysubscription', $this->config->get('main_categorysubscription'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TEAM_SUBSCRIPTIONS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TEAM_SUBSCRIPTIONS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TEAM_SUBSCRIPTIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_teamsubscription', $this->config->get('main_teamsubscription'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_TO_SUBSCRIBE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_TO_SUBSCRIBE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_TO_SUBSCRIBE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_allowguestsubscribe', $this->config->get('main_allowguestsubscribe'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_REGISTRATION_DURING_SUBSCRIBE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_REGISTRATION_DURING_SUBSCRIBE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_REGISTRATION_DURING_SUBSCRIBE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_registeronsubscribe', $this->config->get('main_registeronsubscribe'));?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_FEATURE_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_INFO'); ?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFY_USER_SUBSCRIPTIONS_CONFIRMATION'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFY_USER_SUBSCRIPTIONS_CONFIRMATION'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFY_USER_SUBSCRIPTIONS_CONFIRMATION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_subscription_confirmation', $this->config->get('main_subscription_confirmation'));?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFY_ADMIN_NEW_SUBSCRIPTIONS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFY_ADMIN_NEW_SUBSCRIPTIONS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFY_ADMIN_NEW_SUBSCRIPTIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_subscription_admin_notification', $this->config->get('main_subscription_admin_notification'));?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>