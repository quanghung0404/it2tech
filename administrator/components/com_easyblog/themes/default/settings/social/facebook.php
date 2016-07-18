<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING_INFO'); ?></div>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_facebook_like', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES'); ?>
				<?php echo $this->html('settings.toggle', 'main_facebook_like_frontpage', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_FRONTPAGE'); ?>

				<?php echo $this->html('settings.toggle', 'main_facebook_ogauthor', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_USE_AUTHOR_URL'); ?>

				<div class="form-group">
					<label class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="input-group input-group-link">
							<input type="text" name="main_facebook_like_admin" class="form-control" value="<?php echo $this->config->get('main_facebook_like_admin');?>" />
							<span class="input-group-btn">
								<a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-facebook-autoposting" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="input-group input-group-link">
							<input type="text" name="main_facebook_like_appid" class="form-control" value="<?php echo $this->config->get('main_facebook_like_appid');?>" />

							<span class="input-group-btn">
								<a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-facebook-autoposting" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_ADVANCED_SETTINGS');?></b>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_facebook_scripts', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS'); ?>

				<?php echo $this->html('settings.toggle', 'main_facebook_opengraph', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APPEND_OPENGRAPH_HEADERS'); ?>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_DESC'); ?></div>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_facebook_analytics', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_ENABLE'); ?>

				<div>
					<span class="label label-danger"><?php echo JText::_('COM_EASYBLOG_NOTE');?></span><br />
					<?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_NOTE');?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_BUTTON_STYLING'); ?></b>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_facebook_like_faces', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES'); ?>

				<?php echo $this->html('settings.toggle', 'main_facebook_like_send', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND'); ?>

				<div class="form-group">
					<label class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select id="main_facebook_like_verb" name="main_facebook_like_verb" class="form-control" onchange="switchFBPosition();">
							<option<?php echo $this->config->get( 'main_facebook_like_verb' ) == 'like' ? ' selected="selected"' : ''; ?> value="like"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES');?></option>
							<option<?php echo $this->config->get( 'main_facebook_like_verb' ) == 'recommend' ? ' selected="selected"' : ''; ?> value="recommend"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS');?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select name="main_facebook_like_theme" class="form-control">
							<option<?php echo $this->config->get('main_facebook_like_theme') == 'light' ? ' selected="selected"' : ''; ?> value="light"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT');?></option>
							<option<?php echo $this->config->get('main_facebook_like_theme') == 'dark' ? ' selected="selected"' : ''; ?> value="dark"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
