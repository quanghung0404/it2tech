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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_GENERAL_TITLE');?></b>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_jomsocial_privacy', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_PRIVACY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_toolbar', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_TOOLBAR'); ?>

				<?php echo $this->html('settings.toggle', 'main_jomsocial_friends', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FRIEND'); ?>

				<?php echo $this->html('settings.toggle', 'main_jomsocial_messaging', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MESSAGING'); ?>

				<?php echo $this->html('settings.toggle', 'main_jomsocial_userpoint', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_USERPOINT'); ?>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MEDIA_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MEDIA_INFO'); ?></div>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_album', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ENABLE_MEDIA'); ?>
			</div>
		</div>
		
	</div>

	<div class="col-lg-6">


		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_STREAM_TITLE');?></b>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_blog_new_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_POST_ACTIVITY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_rss_import_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_RSS_IMPORT_NEW_POST_ACTIVITY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_blog_update_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UPDATE_POST_ACTIVITY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_unpublish_remove_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UNPUBLISH_POST_REMOVE_ACTIVITY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_comment_new_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_COMMENT_ACTIVITY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_feature_blog_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FEATURED_BLOG_ACTIVITY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_submit_content', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_SUBMIT_CONTENT'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_show_category', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_DISPLAY_CATEGORY'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_activity_likes', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_LIKES'); ?>

				<?php echo $this->html('settings.toggle', 'integrations_jomsocial_activity_comments', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT'); ?>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-lg-6">
							<div class="input-group">
								<input type="text" name="integrations_jomsocial_blogs_length" class="form-control text-center" value="<?php echo $this->config->get('integrations_jomsocial_blogs_length');?>" style="text-align:center;" size="5" />
								<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' ); ?></span>
							</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT_LENGTH'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT_LENGTH'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT_LENGTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-lg-6">
							<div class="input-group">
								<input type="text" name="integrations_jomsocial_comments_length" class="form-control text-center" value="<?php echo $this->config->get('integrations_jomsocial_comments_length');?>" style="text-align:center;" size="5" />
								<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' ); ?></span>
							 </div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_TITLE_LENGTH'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_TITLE_LENGTH'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_TITLE_LENGTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-lg-6">
							<div class="input-group">
								<input type="text" name="jomsocial_blog_title_length" class="form-control text-center" value="<?php echo $this->config->get('jomsocial_blog_title_length');?>" style="text-align:center;" size="5" />
								<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' ); ?></span>
							</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
