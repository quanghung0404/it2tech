<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_OPTIONS');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_OPTIONS_DESC');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php
							$contentType = array();
							$contentType[] = JHTML::_('select.option', 'html', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_HTML_OPTION' ) );
							$contentType[] = JHTML::_('select.option', 'plain', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_PLAINTEXT_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $contentType, 'main_remotepublishing_mailbox_format', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_format' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_syncuser', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAP_USERS_EMAIL'); ?>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SELECT_USER'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SELECT_USER'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SELECT_USER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div style="margin-right: 10px; float: left;" class="half-width">
							<input type="hidden" name="main_remotepublishing_mailbox_userid" id="main_remotepublishing_mailbox_userid" value="<?php echo $this->config->get('main_remotepublishing_mailbox_userid') ?>" data-author-id />
							
							<?php $user   = JFactory::getUser($this->config->get('main_remotepublishing_mailbox_userid')); ?>
							<span data-author-name><?php echo $user->name;?></span>
							<a class="modal btn btn-default" data-browse-users>
								<i class="fa fa-group"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?>
							</a>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTYPE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTYPE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTYPE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php
							$contentType = array();
							$contentType[] = JHTML::_('select.option', 'intro', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_INTRO' ) );
							$contentType[] = JHTML::_('select.option', 'content', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_CONTENT' ) );

							$showdet = JHTML::_('select.genericlist', $contentType, 'main_remotepublishing_mailbox_type', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_type' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_CATEGORY'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_CATEGORY'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_CATEGORY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo EB::populateCategories('', '', 'select', 'main_remotepublishing_mailbox_categoryid', $this->config->get( 'main_remotepublishing_mailbox_categoryid') , true); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_STATE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_STATE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_STATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php
							$publishFormat = array();
							$publishFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_UNPUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SCHEDULED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_DRAFT_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $publishFormat, 'main_remotepublishing_mailbox_publish', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_publish' , '1' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_PRIVACY'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_PRIVACY'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_PRIVACY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php
							$privacies = array();
							$privacies[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_PRIVACY_ALL_OPTION' ) );
							$privacies[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PRIVACY_REGISTERED_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $privacies, 'main_remotepublishing_mailbox_privacy', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_privacy' , '0' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_frontpage', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FRONTPAGE'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_image_attachment', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_ATTACHMENT'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_blogimage', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_BLOGIMAGE'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_autoposting', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_AUTOPOST'); ?>
			</div>
		</div>
	</div>
</div>
