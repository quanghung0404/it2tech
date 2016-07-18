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
<div class="eb-box">
	<div class="eb-box-head">
		<i class="fa fa-user"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_SETTINGS_TITLE');?>
	</div>
	<div class="eb-box-body">
		<p class="eb-box-lead"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_SETTINGS_DESC');?></p>
		<div class="form-horizontal clear">

			<?php if ($this->config->get('layout_avatar') && $this->config->get('layout_avatarIntegration') == 'default') { ?>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE'); ?></label>
				<div class="col-md-7">
					<div class="media">
						<div class="media-object pull-left">
                    		<img class="avatar-image" src="<?php echo $profile->getAvatar(); ?>"/>
                    	</div>

	                    <?php  if( $this->acl->get('upload_avatar') ) { ?>
	                    <div id="avatar-upload-form" class="media-body">
	                    	<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE_UPLOAD_CONDITION', (float) $this->config->get('main_upload_image_size', 0) , EBLOG_AVATAR_LARGE_WIDTH, EBLOG_AVATAR_LARGE_HEIGHT); ?>
	                    	<div class="mts"><input id="file-upload" type="file" name="avatar" /></div>
	                    	<div><span id="upload-clear"></span></div>
	                    </div>
	                    <?php } ?>
	                </div>
				</div>
			</div>

			<hr />
			<?php } ?>

			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME'); ?></label>
				<div class="col-md-7">
					<input type="text" id="fullname" name="fullname" class="form-control" value="<?php echo $this->escape($this->my->name); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_WHAT_OTHERS_CALL_YOU'); ?></label>
				<div class="col-md-7">
					<input type="text" id="nickname" name="nickname" class="form-control" value="<?php echo $this->escape( $profile->nickname ); ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_USERNAME'); ?></label>
				<div class="col-md-5">
					<input type="text" class="form-control" disabled="disabled" value="<?php echo $this->my->username; ?>" />
				</div>
			</div>

			<?php if ($this->config->get('main_joomlauserparams')) { ?>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_EMAIL'); ?></label>
				<div class="col-md-7">
					<input class="form-control" type="text" id="email" name="email" value="<?php echo $this->escape($this->my->email);?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_PASSWORD'); ?></label>
				<div class="col-md-7">
					<input class="form-control" type="password" id="password" name="password" value="" class="" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_RECONFIRM_PASSWORD'); ?></label>
				<div class="col-md-7">
					<input class="form-control" type="password" id="password2" name="password2" />
				</div>
			</div>
			<?php } ?>

		</div>
	</div>
</div>
