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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_TITLE');?></b>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_ADMIN'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_ADMIN'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_ADMIN_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'notification_commentadmin', $this->config->get('notification_commentadmin')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_AUTHOR'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_AUTHOR'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'notification_commentauthor', $this->config->get('notification_commentauthor')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_PENDING_MODERATION_AUTHOR'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_PENDING_MODERATION_AUTHOR'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_PENDING_MODERATION_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'notification_commentmoderationauthor', $this->config->get('notification_commentmoderationauthor')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_SUBSCRIBERS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_SUBSCRIBERS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_SUBSCRIBERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'notification_commentsubscriber', $this->config->get('notification_commentsubscriber')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_LIKE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_LIKE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_LIKE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'notification_commentlike', $this->config->get('notification_commentlike')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_SETTINGS');?></b>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'notification_commentruncate', $this->config->get('notification_commentruncate')); ?>
					</div>
				</div>
	            <div class="form-group">
	            	<label for="page_title" class="col-md-5">
	                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE_LIMIT'); ?>

	                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE_LIMIT'); ?>"
	                       data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE_LIMIT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
	                </label>
	                <div class="col-md-7">	                	
	                    <input type="text" class="form-control" name="notification_commenttruncate_limit" value="<?php echo $this->config->get('notification_commenttruncate_limit', 300);?>" />
	                </div>
	            </div>
	        </div>
		</div>
	</div>
</div>
