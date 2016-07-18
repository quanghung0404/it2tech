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
		<i class="fa fa-cube"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_SETTINGS_TITLE');?>
	</div>
	<div class="eb-box-body">
		<p class="eb-box-lead"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_SETTINGS_DESC');?></p>
		<div class="form-horizontal">
			<?php if ($this->acl->get('allow_user_blog_title')) { ?>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_TITLE');?></label>
				<div class="col-md-7">
					<input type="text" class="form-control" id="title" name="title" value="<?php echo $this->escape( $profile->title );?>" />
				</div>
			</div>
			<?php } ?>
			<?php if ($this->acl->get('allow_user_blog_description')) { ?>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_DESC');?></label>
				<div class="col-md-8">
					<textarea name="description" class="form-control"><?php echo $profile->getDescription();?></textarea>
				</div>
			</div>
			<?php } ?>
			<?php if ($this->acl->get('custom_css')) { ?>
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_CUSTOM_CSS');?></label>
				<div class="col-md-8">
					<textarea name="custom_css" class="hide" data-custom-css></textarea>
					<div class="form-control" id="customcss"><?php echo $profile->custom_css;?></div>
				</div>
			</div>
			<?php } ?>
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BIOGRAPHICAL_INFO');?></label>
				<div class="col-md-8">
        	    	<?php if ($this->config->get('layout_dashboard_biography_editor')){ ?>
        	    		<?php echo $editor->display('biography', $profile->getBiography() , '300', '300', '10', '10', array( 'image' ,'readmore' , 'pagebreak' , 'jcommentsoff' , 'jcommentson') ); ?>
        	    	<?php } else { ?>
                    	<textarea name="biography" class="form-control" placeholder="<?php echo JText::sprintf('COM_EASYBLOG_BIOGRAPHY_NOT_SET', $profile->getName()); ?>"><?php echo $profile->getBiography(true);?></textarea>
                    <?php } ?>
				</div>
			</div>
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_WEBSITE');?></label>
				<div class="col-md-5">
					<input class="form-control" id="url" type="text" name="url" size="50" value="<?php echo $this->escape($profile->url); ?>" />
				</div>
			</div>
			<?php if ($this->acl->get('allow_user_permalink', 1)) { ?>
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_PERMALINK');?></label>
				<div class="col-md-5">
					<?php if( JPluginHelper::isEnabled( 'system' , 'blogurl') ){ ?>
						<span style="line-height: 28px;"><?php echo JURI::root(); ?></span>
					<?php } ?>
					<input type="text" id="user_permalink" name="user_permalink" class="form-control" value="<?php echo $this->escape($profile->permalink); ?>" />
					<div class="small"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_NOTICE_PERMALINK_USAGE')?></div>
				</div>
			</div>
			<?php } ?>
			<?php if ($multithemes->enable) { ?>
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_SELECT_THEME'); ?></label>
				<div class="col-md-5">
					<select name="theme" class="form-control">
						<option value="global"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_THEMES_THEME_GLOBAL');?></option>
						<?php foreach ($multithemes->availableThemes as $theme) { ?>
							<option value="<?php echo $theme;?>"<?php echo $multithemes->selectedTheme == $theme ? ' selected="selected"' : '';?>><?php echo ucfirst($theme);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
