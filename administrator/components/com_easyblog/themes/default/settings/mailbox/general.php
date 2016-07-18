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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING');?></b>
				<div class="panel-info">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_DESC' ); ?>
					<br /><br />
					<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROMOTE_PUBLISHING_MAILBOX_INSTRUCTION'); ?> <a href="http://stackideas.com/docs/easyblog/administrators/cronjobs" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_HELP_CRON' ); ?></a>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<button type="button" class="btn btn-default" data-test-mailbox><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST_BUTTON');?></button>
						<span data-mailbox-test-result></span>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING'); ?>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PREFIX'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PREFIX'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PREFIX_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<input type="text" name="main_remotepublishing_mailbox_prefix" class="form-control" value="<?php echo $this->config->get('main_remotepublishing_mailbox_prefix');?>" />
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_RUN_INTERVAL'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_RUN_INTERVAL'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_RUN_INTERVAL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="main_remotepublishing_mailbox_run_interval" class="form-control" maxlength="2" value="<?php echo $this->config->get('main_remotepublishing_mailbox_run_interval', '5' );?>" />
									<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_MINUTES' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FETCH_LIMIT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FETCH_LIMIT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FETCH_LIMIT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-3">
								<?php
									$fetchLimit = array();
									$fetchLimit[] = JHTML::_('select.option', '1', JText::_( '1' ) );
									$fetchLimit[] = JHTML::_('select.option', '2', JText::_( '2' ) );
									$fetchLimit[] = JHTML::_('select.option', '3', JText::_( '3' ) );
									$fetchLimit[] = JHTML::_('select.option', '4', JText::_( '4' ) );
									$fetchLimit[] = JHTML::_('select.option', '5', JText::_( '5' ) );
									$fetchLimit[] = JHTML::_('select.option', '10', JText::_( '10' ) );
									$fetchLimit[] = JHTML::_('select.option', '15', JText::_( '15' ) );
									$fetchLimit[] = JHTML::_('select.option', '20', JText::_( '20' ) );
									$fetchLimit[] = JHTML::_('select.option', '50', JText::_( '50' ) );

									$showdet = JHTML::_('select.genericlist', $fetchLimit, 'main_remotepublishing_mailbox_fetch_limit', 'class="text-center form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_fetch_limit' ) );
									echo $showdet;
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="col-lg-6">

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_SERVER_SETTINGS');?></b>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PROVIDER'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PROVIDER'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PROVIDER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select name="main_remotepublishing_mailbox_provider" class="form-control" data-mail-provider>
							<option value=""><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_SELECT_PROVIDER');?></option>
							<option value="gmail"<?php echo ($this->config->get('main_remotepublishing_mailbox_provider') == 'gmail') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_GMAIL');?></option>
							<option value="hotmail"<?php echo ($this->config->get('main_remotepublishing_mailbox_provider') == 'hotmail') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_HOTMAIL');?></option>
							<option value="others"<?php echo ($this->config->get('main_remotepublishing_mailbox_provider') == 'others') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_OTHERS');?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_USERNAME'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_USERNAME'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_USERNAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<input type="text" id="main_remotepublishing_mailbox_username" name="main_remotepublishing_mailbox_username"
							class="form-control"
							value="<?php echo $this->config->get('main_remotepublishing_mailbox_username');?>"
							data-mailbox-username
						/>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PASSWORD'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PASSWORD'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PASSWORD_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<input type="password" id="main_remotepublishing_mailbox_password" autocomplete="off" name="main_remotepublishing_mailbox_password"
							class="form-control"
							value="<?php echo $this->config->get('main_remotepublishing_mailbox_password');?>"
							data-mailbox-password
						/>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SYSTEM_NAME'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SYSTEM_NAME'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SYSTEM_NAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="input-group input-group-link">
							<input type="text" id="main_remotepublishing_mailbox_remotesystemname" name="main_remotepublishing_mailbox_remotesystemname" class="form-control"
								value="<?php echo $this->config->get('main_remotepublishing_mailbox_remotesystemname');?>"
								data-mailbox-address
							/>
							<span class="input-group-btn">
								<a href="http://stackideas.com/docs/easyblog/administrators/remote-publishing/email-publishing" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PORT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PORT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PORT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<input type="text" id="main_remotepublishing_mailbox_port" name="main_remotepublishing_mailbox_port" class="form-control"
							value="<?php echo $this->config->get('main_remotepublishing_mailbox_port');?>" data-mailbox-port
						/>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php
							$services = array();
							$services[] = JHTML::_('select.option', 'imap', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_IMAP' ) );
							$services[] = JHTML::_('select.option', 'pop3', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_POP3' ) );
							echo JHTML::_('select.genericlist', $services, 'main_remotepublishing_mailbox_service', 'class="form-control" data-mailbox-type', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_service') );
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_ssl', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SSL', '', 'data-mailbox-ssl'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_validate_cert', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_VALIDATE_CERT', '', 'data-mailbox-validate-ssl'); ?>


				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAILBOX_NAME'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAILBOX_NAME'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAILBOX_NAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<input type="text" id="main_remotepublishing_mailbox_mailboxname" name="main_remotepublishing_mailbox_mailboxname"
							class="form-control"
							value="<?php echo $this->config->get('main_remotepublishing_mailbox_mailboxname');?>"
							data-mailbox-name
						/>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FROM_WHITE_LIST'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FROM_WHITE_LIST'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FROM_WHITE_LIST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<textarea class="form-control" id="main_remotepublishing_mailbox_from_whitelist" name="main_remotepublishing_mailbox_from_whitelist" data-mailbox-whitelist><?php echo $this->config->get('main_remotepublishing_mailbox_from_whitelist');?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
