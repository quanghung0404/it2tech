<?php
/**
* @package      EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>

<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.textbox', 'integration_facebook_like_admin', $this->config->get('integration_facebook_like_admin')); ?>
							<a href="http://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-account-id.html" target="_blank" style="margin-left:5px;">
								<?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?>
							</a>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.textbox', 'integration_facebook_like_appid', $this->config->get('integration_facebook_like_appid')); ?>
							<a href="http://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-application-settings.html" target="_blank" style="margin-left:5px;">
								<?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?>
							</a>
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_facebook_scripts', $this->config->get('integration_facebook_scripts')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_facebook_like', $this->config->get('integration_facebook_like')); ?>
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_facebook_like_send', $this->config->get('integration_facebook_like_send')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_facebook_like_faces', $this->config->get('integration_facebook_like_faces')); ?>
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB'); ?>
						</div>
	                    <div class="col-md-6">
	                        <?php echo $this->html('form.dropdown', 'integration_facebook_like_verb',
	                        						array('like' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES', 'recommend' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS'),
	                        						$this->config->get('integration_facebook_like_verb')); ?>
	                    </div>

					</div>

					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES'); ?>
						</div>
	                    <div class="col-md-6">
	                        <?php echo $this->html('form.dropdown', 'integration_facebook_like_theme',
	                        						array('light' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT', 'dark' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK'),
	                        						$this->config->get('integration_facebook_like_theme')); ?>
	                    </div>

					</div>

				</div>
			</div>

		</div>

	</div>
</div>
